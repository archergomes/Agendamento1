<?php

namespace App\Controllers;

use App\Models\AuthModel;
use CodeIgniter\Controller;

class Auth extends Controller
{
    protected $authModel;
    protected $session;
    protected $validation;

    public function __construct()
    {
        $this->authModel = new AuthModel();
        $this->session = \Config\Services::session();
        $this->validation = \Config\Services::validation();

        helper(['url', 'form']);
    }

    public function register()
    {
        // Se GET, carrega a view
        if (strtolower($this->request->getMethod()) !== 'post') {
            return view('auth/register');
        }

        // Regras de validação
        $rules = [
            'nome' => 'required|trim|min_length[2]|max_length[50]',
            'sobrenome' => 'required|trim|min_length[2]|max_length[50]',
            'data_nascimento' => 'required|valid_date',
            'genero' => 'required|in_list[Masculino,Feminino,Outro]',
            'telefone' => 'required|trim|min_length[9]|max_length[20]',
            'bi' => 'required|trim|min_length[5]|max_length[50]|is_unique[pacientes.BI]',
            'email' => 'required|trim|valid_email|is_unique[usuarios.Email]',
            'senha' => 'required|min_length[6]|max_length[255]'
        ];

        $messages = [
            'bi' => ['is_unique' => 'Este número de BI já está cadastrado no sistema.'],
            'email' => ['is_unique' => 'Este e-mail já está cadastrado no sistema.'],
            'data_nascimento' => ['valid_date' => 'Por favor, insira uma data de nascimento válida.']
        ];

        if (!$this->validate($rules, $messages)) {
            return view('auth/register', ['validation' => $this->validator]);
        }

        // Dados do formulário
        $nome = $this->request->getPost('nome');
        $sobrenome = $this->request->getPost('sobrenome');
        $dataNascimento = $this->request->getPost('data_nascimento');
        $genero = $this->request->getPost('genero');
        $telefone = $this->request->getPost('telefone');
        $bi = $this->request->getPost('bi');
        $email = $this->request->getPost('email');
        $senha = $this->request->getPost('senha');

        // 1. Criar o paciente
        $pacienteData = [
            'nome' => $nome,
            'sobrenome' => $sobrenome,
            'data_nascimento' => $dataNascimento,
            'genero' => $genero,
            'telefone' => $telefone,
            'bi' => $bi,
            'endereco' => null,
            'contato_emergencia' => null,
            'id_usuario' => null
        ];

        $pacienteId = $this->authModel->insertPaciente($pacienteData);

        if (!$pacienteId) {
            $this->session->setFlashdata('error', 'Erro ao criar perfil de paciente. Tente novamente.');
            return redirect()->to('auth/register');
        }

        // 2. Criar o usuário
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $usuarioData = [
            'email' => $email,
            'senha' => $senhaHash,
            'tipo_usuario' => 'Paciente',
            'id_referencia' => $pacienteId
        ];

        $usuarioId = $this->authModel->insertUsuario($usuarioData);

        if (!$usuarioId) {
            $this->authModel->deletePaciente($pacienteId);
            $this->session->setFlashdata('error', 'Erro ao criar credenciais. Tente novamente.');
            return redirect()->to('auth/register');
        }

        // 3. Atualizar o paciente com o ID do usuário
        $this->authModel->updatePacienteUsuarioId($pacienteId, $usuarioId);

        $this->session->setFlashdata('success', 'Conta criada com sucesso! Faça login para continuar.');
        return redirect()->to('auth/login');
    }

    public function login()
    {
        // Se GET, carrega a view
        if (strtolower($this->request->getMethod()) !== 'post') {
            return view('auth/login');
        }

        // Validação
        $rules = [
            'email' => 'required|trim|valid_email',
            'senha' => 'required|trim|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            $errorMsg = $this->validator->getErrors() ? implode(' ', $this->validator->getErrors()) : 'Dados inválidos.';

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $errorMsg
                ]);
            }

            $this->session->setFlashdata('error', $errorMsg);
            return view('auth/login', ['validation' => $this->validator]);
        }

        $email = $this->request->getPost('email');
        $senha = $this->request->getPost('senha');

        // Busca o usuário
        $usuario = $this->authModel->getUsuarioByEmail($email);

        if (!$usuario) {
            $errorMsg = 'E-mail não encontrado. Verifique e tente novamente.';

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $errorMsg
                ]);
            }

            $this->session->setFlashdata('error', $errorMsg);
            return redirect()->to('auth/login');
        }

        // Verifica a senha
        if (empty($usuario->Senha)) {
            $errorMsg = 'Conta com problema. Contate o suporte.';

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $errorMsg
                ]);
            }

            $this->session->setFlashdata('error', $errorMsg);
            return redirect()->to('auth/login');
        }

        if (!password_verify($senha, $usuario->Senha)) {
            $errorMsg = 'Senha inválida. Tente novamente.';

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $errorMsg
                ]);
            }

            $this->session->setFlashdata('error', $errorMsg);
            return redirect()->to('auth/login');
        }

        // Buscar o paciente_id se for paciente
        $pacienteId = null;
        if ($usuario->Tipo_Usuario === 'Paciente') {
            if ($usuario->ID_Referencia > 0) {
                $pacienteId = $usuario->ID_Referencia;
            } else {
                $paciente = $this->authModel->getPacienteByUsuarioId($usuario->ID_Usuario);
                if ($paciente) {
                    $pacienteId = $paciente->ID_Paciente;
                }
            }
        }

        // ============================================
        // CORREÇÃO: Redirecionar por tipo de usuário
        // ============================================

        // Dados da sessão
        $sessionData = [
            'ID_Usuario' => $usuario->ID_Usuario,
            'Email' => $usuario->Email,
            'Tipo_Usuario' => $usuario->Tipo_Usuario,
            'ID_Referencia' => $usuario->ID_Referencia,
            'paciente_id' => $pacienteId,
            'logged_in' => true,
            'user_id' => $usuario->ID_Usuario
        ];

        $this->session->set($sessionData);

        // Log para debug
        log_message('debug', 'Login - Tipo de usuário: ' . $usuario->Tipo_Usuario);
        log_message('debug', 'Login - Sessão: ' . print_r($sessionData, true));

        // ============================================
        // REDIRECIONAMENTO POR TIPO DE USUÁRIO
        // ============================================

        $redirectUrl = 'agenda'; // URL padrão

        switch ($usuario->Tipo_Usuario) {
            case 'Admin':
                $redirectUrl = 'admin';
                break;
            case 'Medico':
                $redirectUrl = 'medico/dashboard';
                break;
            case 'Secretario':
                $redirectUrl = 'secretario/dashboard';
                break;
            case 'Paciente':
            default:
                $redirectUrl = 'agenda';
                break;
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Login efetuado com sucesso!',
                'redirect' => site_url($redirectUrl)
            ]);
        }

        $this->session->setFlashdata('success', 'Bem-vindo!');
        return redirect()->to($redirectUrl);
    }

    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('auth/login');
    }
}
