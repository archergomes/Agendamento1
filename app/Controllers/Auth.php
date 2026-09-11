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
            'genero' => 'required|in_list[Masculino,Feminino]',
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

        // ============================================
        // "LEMBRAR-ME" — cria cookie de longa duração
        // ============================================
        $remember = $this->request->getPost('remember');

        if ($remember) {
            // Gera token aleatório
            $token = bin2hex(random_bytes(32));
            $expiracao = date('Y-m-d H:i:s', strtotime('+30 days'));

            // Guarda no banco (hash do token)
            $this->authModel->saveRememberToken($usuario->ID_Usuario, $token, $expiracao);

            // Guarda cookie com o token original (não hashed)
            // Formato: usuarioId:token
            $cookieValue = $usuario->ID_Usuario . ':' . $token;

            setcookie(
                'remember_me',
                $cookieValue,
                [
                    'expires'  => time() + (30 * 24 * 60 * 60), // 30 dias
                    'path'     => '/',
                    'httponly' => true,
                    'samesite' => 'Lax',
                    'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'
                ]
            );
        } else {
            // Se não marcou "Lembrar-me", remove qualquer token anterior
            $this->authModel->deleteRememberTokenByUser($usuario->ID_Usuario);

            if (isset($_COOKIE['remember_me'])) {
                setcookie('remember_me', '', time() - 3600, '/');
            }
        }

        // ============================================
        // REDIRECIONAMENTO POR TIPO DE USUÁRIO
        // ============================================

        $redirectUrl = 'agenda'; // URL padrão

        switch ($usuario->Tipo_Usuario) {
            case 'Admin':
                $redirectUrl = 'admin';
                break;
            case 'Medico':
                $redirectUrl = 'medico';
                break;
            case 'Secretario':
                $redirectUrl = 'secretario';
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
        // Remove o remember token do banco
        if (isset($_COOKIE['remember_me'])) {
            $this->authModel->deleteRememberToken($_COOKIE['remember_me']);

            // Apaga o cookie
            setcookie('remember_me', '', time() - 3600, '/');
        }

        // Se houver utilizador logado, remove também o token do usuário
        if ($this->session->get('ID_Usuario')) {
            $this->authModel->deleteRememberTokenByUser($this->session->get('ID_Usuario'));
        }

        $this->session->destroy();
        return redirect()->to('auth/login');
    }

    /**
     * Página de recuperação de senha
     */
    public function recuperar_senha()
    {
        $data['title'] = 'Recuperar Senha';
        return view('auth/recuperar_senha', $data);
    }

    /**
     * AJAX: Enviar link de recuperação
     */
    public function enviar_link_recuperacao()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Requisição inválida'
            ]);
        }

        $email = $this->request->getPost('email');

        // Validar email
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Digite um email válido.'
            ]);
        }

        try {
            // Verificar se o email existe no sistema
            $usuario = $this->authModel->getUserByEmail($email);

            if (!$usuario) {
                // Por segurança, não informamos que o email não existe
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Se o email estiver cadastrado, você receberá um link de recuperação.'
                ]);
            }

            // Gerar token de recuperação
            $token = bin2hex(random_bytes(32));
            $expiracao = date('Y-m-d H:i:s', strtotime('+5 minutes'));

            // Salvar token no banco
            $this->authModel->saveRecoveryToken($usuario->ID_Usuario, $token, $expiracao);

            // Link de recuperação
            $link = base_url("auth/redefinir-senha/$token");

            // ============================================
            // Buscar o nome real do paciente (correção do erro
            // "Undefined property: stdClass::$Nome")
            // ============================================
            $nomeExibicao = 'utilizador';

            if (isset($usuario->Tipo_Usuario) && $usuario->Tipo_Usuario === 'Paciente' && !empty($usuario->ID_Referencia)) {
                $paciente = $this->authModel->getPacienteById($usuario->ID_Referencia);
                if ($paciente) {
                    $nomeCompleto = trim(($paciente->Nome ?? '') . ' ' . ($paciente->Sobrenome ?? ''));
                    if ($nomeCompleto !== '') {
                        $nomeExibicao = $nomeCompleto;
                    }
                }
            }

            // Enviar email
            $emailService = \Config\Services::email();
            $emailService->setFrom('noreply@hospitalmatlhovele.com', 'Centro de Saúde Da Matola II');
            $emailService->setTo($email);
            $emailService->setSubject('Recuperação de Senha - Centro de Saúde Da Matola II');
            $emailService->setMessage("
                <h2>Recuperação de Senha</h2>
                <p>Olá {$nomeExibicao},</p>
                <p>Recebemos uma solicitação para redefinir sua senha no Centro de Saúde Da Matola II.</p>
                <p>Clique no link abaixo para redefinir sua senha:</p>
                <p><a href='{$link}'>Redefinir Senha</a></p>
                <p>Este link é válido por 5 minutos.</p>
                <p>Se você não solicitou esta alteração, ignore este email.</p>
                <br>
                <p>Atenciosamente,</p>
                <p><strong>Centro de Saúde Da Matola II</strong></p>
            ");

            if ($emailService->send()) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Link de recuperação enviado com sucesso! Verifique seu email.'
                ]);
            } else {
                log_message('error', 'Erro ao enviar email: ' . $emailService->printDebugger(['headers']));
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Erro ao enviar email. Tente novamente mais tarde.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao recuperar senha: ' . $e->getMessage() . ' | ' . $e->getFile() . ':' . $e->getLine());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Erro interno do servidor. Tente novamente.'
            ]);
        }
    }

    public function redefinir_senha($token = null)
    {
        if (!$token) {
            return redirect()->to('auth/login');
        }

        $tokenData = $this->authModel->getTokenData($token);

        if (!$tokenData || strtotime($tokenData->Expiracao) < time()) {
            session()->setFlashdata('error', 'Link de recuperação inválido ou expirado.');
            return redirect()->to('auth/login');
        }

        $data['token'] = $token;
        $data['usuario_id'] = $tokenData->ID_Usuario;
        return view('auth/senha', $data);  // <-- esta view é a que criámos
    }

    /**
     * AJAX: Redefinir senha
     */
    public function atualizar_senha()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Requisição inválida'
            ]);
        }

        $usuario_id = $this->request->getPost('usuario_id');
        $token = $this->request->getPost('token');
        $senha = $this->request->getPost('senha');
        $confirmar_senha = $this->request->getPost('confirmar_senha');

        // Validar dados
        if (empty($usuario_id) || empty($token) || empty($senha) || empty($confirmar_senha)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Preencha todos os campos.'
            ]);
        }

        if ($senha !== $confirmar_senha) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'As senhas não coincidem.'
            ]);
        }

        if (strlen($senha) < 6) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'A senha deve ter pelo menos 6 caracteres.'
            ]);
        }

        // Verificar token novamente
        $tokenData = $this->authModel->getTokenData($token);
        if (!$tokenData || $tokenData->ID_Usuario != $usuario_id || strtotime($tokenData->Expiracao) < time()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Token inválido ou expirado.'
            ]);
        }

        try {
            // Atualizar senha
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $result = $this->authModel->updatePassword($usuario_id, $senhaHash);

            if ($result) {
                // Remover token usado
                $this->authModel->deleteToken($token);

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Senha redefinida com sucesso!'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Erro ao redefinir senha.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao redefinir senha: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Erro interno do servidor.'
            ]);
        }
    }

    /**
     * Verifica o cookie "remember_me" e autentica automaticamente
     */
    public function autoLogin()
    {
        // Se já está logado, não faz nada
        if ($this->session->get('logged_in')) {
            return false;
        }

        // Verifica se existe o cookie
        if (!isset($_COOKIE['remember_me'])) {
            return false;
        }

        // Formato: usuarioId:token
        $parts = explode(':', $_COOKIE['remember_me']);
        if (count($parts) !== 2) {
            // Cookie corrompido — apaga
            setcookie('remember_me', '', time() - 3600, '/');
            return false;
        }

        [$usuarioId, $token] = $parts;

        // Busca o token no banco
        $tokenData = $this->authModel->getRememberToken($token);

        if (!$tokenData || $tokenData->ID_Usuario != $usuarioId) {
            // Token inválido — apaga
            setcookie('remember_me', '', time() - 3600, '/');
            return false;
        }

        // Token válido — busca o usuário e cria sessão
        $usuario = $this->authModel->find($usuarioId);
        if (!$usuario) {
            setcookie('remember_me', '', time() - 3600, '/');
            return false;
        }

        // Buscar paciente_id se for paciente
        $pacienteId = null;
        if ($usuario->Tipo_Usuario === 'Paciente' && $usuario->ID_Referencia > 0) {
            $pacienteId = $usuario->ID_Referencia;
        }

        // Recria sessão
        $this->session->set([
            'ID_Usuario'    => $usuario->ID_Usuario,
            'Email'         => $usuario->Email,
            'Tipo_Usuario'  => $usuario->Tipo_Usuario,
            'ID_Referencia' => $usuario->ID_Referencia,
            'paciente_id'   => $pacienteId,
            'logged_in'     => true,
            'user_id'       => $usuario->ID_Usuario
        ]);

        // Renova o token (rotação por segurança)
        $novoToken = bin2hex(random_bytes(32));
        $novaExpiracao = date('Y-m-d H:i:s', strtotime('+30 days'));
        $this->authModel->saveRememberToken($usuario->ID_Usuario, $novoToken, $novaExpiracao);

        setcookie(
            'remember_me',
            $usuario->ID_Usuario . ':' . $novoToken,
            [
                'expires'  => time() + (30 * 24 * 60 * 60),
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Lax',
                'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'
            ]
        );

        return true;
    }
}
