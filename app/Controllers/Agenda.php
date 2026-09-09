<?php

namespace App\Controllers;

use App\Models\AuthModel;
use App\Models\AgendamentosModel;
use CodeIgniter\Controller;

class Agenda extends Controller
{
    protected $authModel;
    protected $agendamentosModel;
    protected $session;
    protected $request;

    public function __construct()
    {
        $this->authModel = new AuthModel();
        $this->agendamentosModel = new AgendamentosModel();
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();

        helper(['url', 'form']);

        // Verifica se o usuário está logado
        if (!$this->session->get('logged_in')) {
            return redirect()->to('auth/login');
        }
    }

    /**
     * Página principal do agendamento
     */
    public function index()
    {
        // Verificar se o usuário está logado
        if (!$this->session->get('logged_in')) {
            return redirect()->to('auth/login');
        }

        $pacienteId = $this->session->get('paciente_id');
        $usuarioId = $this->session->get('ID_Usuario');
        $email = $this->session->get('Email');

        log_message('debug', 'index - Sessão: paciente_id=' . $pacienteId . ', usuario_id=' . $usuarioId . ', email=' . $email);

        // Se não tiver paciente_id na sessão, tenta buscar
        if (!$pacienteId) {
            if ($usuarioId) {
                $paciente = $this->authModel->getPacienteByUsuarioId($usuarioId);
                if ($paciente) {
                    $pacienteId = $paciente->ID_Paciente;
                    $this->session->set('paciente_id', $pacienteId);
                    log_message('debug', 'index - Paciente encontrado por usuario_id: ' . $pacienteId);
                }
            }

            // Se ainda não encontrou, tenta por email
            if (!$pacienteId && $email) {
                $paciente = $this->authModel->getPacienteByEmail($email);
                if ($paciente) {
                    $pacienteId = $paciente->ID_Paciente;
                    $this->session->set('paciente_id', $pacienteId);
                    log_message('debug', 'index - Paciente encontrado por email: ' . $pacienteId);
                }
            }
        }

        $paciente = $this->authModel->getPacienteById($pacienteId);

        // Preparar as variáveis para a view
        $nome_completo = '';
        $telefone = '';
        $bi = '';

        if ($paciente) {
            $nome_completo = isset($paciente->Nome) ? $paciente->Nome : '';
            if (isset($paciente->Sobrenome)) {
                $nome_completo .= ' ' . $paciente->Sobrenome;
            }
            $telefone = isset($paciente->Telefone) ? $paciente->Telefone : '';
            $bi = isset($paciente->BI) ? $paciente->BI : '';
        }

        // Busca especialidades
        $especialidades = $this->agendamentosModel->getEspecialidades();

        // Fallback se não houver especialidades
        if (empty($especialidades)) {
            $especialidades = [
                (object) ['ID_Especialidade' => 1, 'Nome' => 'Medicina Geral'],
                (object) ['ID_Especialidade' => 2, 'Nome' => 'Cardiologia'],
                (object) ['ID_Especialidade' => 3, 'Nome' => 'Pediatria'],
                (object) ['ID_Especialidade' => 4, 'Nome' => 'Ortopedia'],
                (object) ['ID_Especialidade' => 5, 'Nome' => 'Ginecologia'],
                (object) ['ID_Especialidade' => 6, 'Nome' => 'Neurologia'],
                (object) ['ID_Especialidade' => 7, 'Nome' => 'Cirurgia Geral']
            ];
        }

        // Preparar dados para a view
        $data = [
            'paciente' => $paciente,
            'especialidades' => $especialidades,
            'nome_completo' => $nome_completo,
            'telefone' => $telefone,
            'bi' => $bi,
            'active_menu' => 'home'
        ];

        return view('agenda/agenda', $data);
    }

    /**
     * Página de agendamentos do paciente
     */
    public function agendamentos()
    {
        $pacienteId = $this->session->get('paciente_id');

        if (!$pacienteId) {
            $usuarioId = $this->session->get('ID_Usuario');
            if ($usuarioId) {
                $paciente = $this->authModel->getPacienteByUsuarioId($usuarioId);
                if ($paciente) {
                    $pacienteId = $paciente->ID_Paciente;
                    $this->session->set('paciente_id', $pacienteId);
                }
            }
        }

        $data['paciente'] = $this->authModel->getPacienteById($pacienteId);
        $data['agendamentos'] = $this->agendamentosModel->getAppointmentsByPatient($pacienteId);
        $data['active_menu'] = 'agendamentos';
        return view('agenda/agendamentos', $data);
    }

    /**
     * Página de perfil do paciente
     */
    public function perfil()
    {
        $pacienteId = $this->session->get('paciente_id');

        if (!$pacienteId) {
            $usuarioId = $this->session->get('ID_Usuario');
            if ($usuarioId) {
                $paciente = $this->authModel->getPacienteByUsuarioId($usuarioId);
                if ($paciente) {
                    $pacienteId = $paciente->ID_Paciente;
                    $this->session->set('paciente_id', $pacienteId);
                }
            }
        }

        $data['paciente'] = $this->authModel->getPacienteById($pacienteId);
        $data['active_menu'] = 'perfil';
        return view('agenda/perfil', $data);
    }

    /**
     * AJAX: Atualizar perfil
     */
    public function atualizarPerfil()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Requisição inválida'
            ]);
        }

        $pacienteId = $this->session->get('paciente_id');

        if (!$pacienteId) {
            // Tentar buscar pelo ID_Usuario
            $usuarioId = $this->session->get('ID_Usuario');
            if ($usuarioId) {
                $paciente = $this->authModel->getPacienteByUsuarioId($usuarioId);
                if ($paciente) {
                    $pacienteId = $paciente->ID_Paciente;
                    $this->session->set('paciente_id', $pacienteId);
                }
            }
        }

        if (!$pacienteId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Usuário não logado ou paciente não encontrado.'
            ]);
        }

        // Validação
        $rules = [
            'nome' => 'required|trim|min_length[2]|max_length[50]',
            'sobrenome' => 'required|trim|min_length[2]|max_length[50]',
            'telefone' => 'required|trim|min_length[9]|max_length[20]',
            'bi' => 'required|trim|min_length[5]|max_length[50]'
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $errorMsg = implode(' ', $errors);
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $errorMsg,
                'errors' => $errors
            ]);
        }

        // Dados para atualização
        $dadosAtualizacao = [
            'Nome' => $this->request->getPost('nome'),
            'Sobrenome' => $this->request->getPost('sobrenome'),
            'Telefone' => $this->request->getPost('telefone'),
            'BI' => $this->request->getPost('bi')
        ];

        try {
            $result = $this->authModel->updatePaciente($pacienteId, $dadosAtualizacao);

            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Perfil atualizado com sucesso!',
                    'csrf_token' => csrf_hash()
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Erro ao atualizar perfil. Nenhuma alteração foi feita.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao atualizar perfil: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Erro interno do servidor: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * AJAX: Busca médicos
     */
    public function getDoctors()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Requisição inválida'
            ]);
        }

        $query = $this->request->getPost('q') ?? '';
        $specialty = $this->request->getPost('specialty') ?? '';
        $limit = (int) ($this->request->getPost('limit') ?? 10);
        $offset = (int) ($this->request->getPost('offset') ?? 0);

        // Log para debug
        log_message('debug', 'getDoctors - specialty: ' . $specialty . ', query: ' . $query);

        $total = $this->agendamentosModel->countMedicos($specialty, $query);
        $medicos = $this->agendamentosModel->getMedicos($specialty, $query, $limit, $offset);

        if (empty($medicos)) {
            return $this->response->setJSON([
                'status' => 'success',
                'data' => [],
                'total' => 0,
                'csrf_token' => csrf_hash(), // Retorna o novo token
                'csrf_name' => csrf_token()
            ]);
        }

        $formatted = array_map(function ($medico) {
            return [
                'id' => $medico->ID_Medico,
                'name' => $medico->Nome . ' ' . ($medico->Sobrenome ?? ''),
                'specialty' => $medico->Especialidade ?? '',
                'experience' => '5 anos',
                'image' => base_url('assets/img/default-doctor.jpg')
            ];
        }, $medicos);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $formatted,
            'total' => $total,
            'csrf_token' => csrf_hash(), // Retorna o novo token
            'csrf_name' => csrf_token()
        ]);
    }

    /**
     * AJAX: Busca horários disponíveis
     */
    public function getAvailableSlots()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Requisição inválida'
            ]);
        }

        $data = $this->request->getPost('data');
        $medicoId = $this->request->getPost('medico_id');

        if (empty($data) || empty($medicoId)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Dados incompletos'
            ]);
        }

        $slots = $this->agendamentosModel->getAvailableSlots($data, $medicoId);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $slots,
            'csrf_token' => csrf_hash(), // Retorna o novo token
            'csrf_name' => csrf_token()
        ]);
    }

    /**
     * AJAX: Lista agendamentos do paciente
     */
    public function getPatientAppointments()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Requisição inválida'
            ]);
        }

        $pacienteId = $this->session->get('paciente_id');

        if (!$pacienteId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Paciente não encontrado'
            ]);
        }

        $appointments = $this->agendamentosModel->getAppointmentsByPatient($pacienteId);

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $appointments
        ]);
    }

    /**
     * AJAX: Salva agendamento
     */
    public function saveAppointment()
    {
        try {
            // Verificar se é AJAX
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Requisição inválida'
                ]);
            }

            // Log dos dados recebidos
            log_message('debug', 'saveAppointment - POST data: ' . print_r($this->request->getPost(), true));

            // Verificar CSRF Token
            $csrfToken = $this->request->getPost('csrf_test_name');
            if (!$csrfToken) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Token CSRF não fornecido. Recarregue a página.',
                    'csrf_token' => csrf_hash()
                ]);
            }

            // Pega dados do POST
            $especialidade = $this->request->getPost('especialidade');
            $medicoId = (int) $this->request->getPost('medico');
            $dataConsulta = $this->request->getPost('data_consulta');
            $horario = $this->request->getPost('horario');
            $nome = $this->request->getPost('nome');
            $telefone = $this->request->getPost('telefone');
            $bi = $this->request->getPost('bi');
            $motivo = $this->request->getPost('motivo');

            // Novos campos para "outra pessoa"
            $agendadoPara = $this->request->getPost('agendado_para') ?? 'self';
            $pacienteNome = $this->request->getPost('paciente_nome') ?? '';
            $pacienteDataNascimento = $this->request->getPost('paciente_data_nascimento') ?? '';
            $pacienteParentesco = $this->request->getPost('paciente_parentesco') ?? '';
            $pacienteDocumentoTipo = $this->request->getPost('paciente_documento_tipo') ?? '';
            $pacienteDocumentoNumero = $this->request->getPost('paciente_documento_numero') ?? '';

            // Log para debug
            log_message('debug', 'saveAppointment - Dados recebidos: ' . print_r([
                'especialidade' => $especialidade,
                'medicoId' => $medicoId,
                'dataConsulta' => $dataConsulta,
                'horario' => $horario,
                'nome' => $nome,
                'telefone' => $telefone,
                'bi' => $bi,
                'agendado_para' => $agendadoPara,
                'paciente_nome' => $pacienteNome,
                'paciente_parentesco' => $pacienteParentesco
            ], true));

            // Validação básica
            if (
                empty($especialidade) || empty($medicoId) || empty($dataConsulta) || empty($horario) ||
                empty($nome) || empty($telefone) || empty($bi)
            ) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Dados incompletos. Preencha todos os campos obrigatórios.'
                ]);
            }

            // Se for para outra pessoa, validar os campos adicionais
            if ($agendadoPara === 'other') {
                if (empty($pacienteNome) || empty($pacienteParentesco)) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Preencha os dados do paciente (nome e parentesco).'
                    ]);
                }
            }

            // Buscar paciente_id
            $pacienteId = $this->session->get('paciente_id');

            // Se não encontrar, tenta buscar pelo ID_Usuario
            if (!$pacienteId) {
                $usuarioId = $this->session->get('ID_Usuario');
                if ($usuarioId) {
                    $paciente = $this->authModel->getPacienteByUsuarioId($usuarioId);
                    if ($paciente) {
                        $pacienteId = $paciente->ID_Paciente;
                        $this->session->set('paciente_id', $pacienteId);
                    }
                }
            }

            // Se ainda não tem paciente_id, tenta buscar pelo email
            if (!$pacienteId) {
                $email = $this->session->get('Email');
                if ($email) {
                    $paciente = $this->authModel->getPacienteByEmail($email);
                    if ($paciente) {
                        $pacienteId = $paciente->ID_Paciente;
                        $this->session->set('paciente_id', $pacienteId);
                    }
                }
            }

            if (!$pacienteId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Paciente não encontrado. Faça login novamente.'
                ]);
            }

            // Verificar disponibilidade
            if (!$this->agendamentosModel->isSlotAvailable($dataConsulta, $horario, $medicoId)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Horário já ocupado. Escolha outro.'
                ]);
            }

            // Preparar dados para insert - COM TODOS OS CAMPOS
            $dataInsert = [
                'ID_Paciente' => $pacienteId,
                'ID_Medico' => $medicoId,
                'Data_Agendamento' => $dataConsulta,
                'Hora_Agendamento' => $horario,
                'Status' => 'Pendente',
                'Motivo' => !empty($motivo) ? $motivo : null,
                'tipo_agendamento' => $agendadoPara,
                'responsavel_nome' => $nome,
                'responsavel_telefone' => $telefone,
                'responsavel_bi' => $bi
            ];

            // Se for para outra pessoa, adicionar campos do paciente
            if ($agendadoPara === 'other') {
                $dataInsert['paciente_nome_agendado'] = $pacienteNome;
                $dataInsert['paciente_relacao'] = $pacienteParentesco;
                $dataInsert['paciente_data_nasc_agendado'] = !empty($pacienteDataNascimento) ? $pacienteDataNascimento : null;
                $dataInsert['paciente_doc_tipo'] = $pacienteDocumentoTipo;
                $dataInsert['paciente_doc_num'] = !empty($pacienteDocumentoNumero) ? $pacienteDocumentoNumero : null;
            }

            log_message('debug', 'saveAppointment - Dados para insert: ' . print_r($dataInsert, true));

            // Salvar no banco
            $result = $this->agendamentosModel->createAgendamento($dataInsert);

            if (isset($result['success'])) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => $result['success'],
                    'csrf_token' => csrf_hash(),
                    'csrf_name' => 'csrf_test_name'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => $result['error'] ?? 'Erro ao salvar no banco de dados.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'saveAppointment - ERRO: ' . $e->getMessage());
            log_message('error', 'saveAppointment - TRACE: ' . $e->getTraceAsString());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Erro interno do servidor: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * AJAX: Cancelar agendamento
     */
    public function cancelarAgendamento()
    {
        try {
            // Verificar se é AJAX
            if (!$this->request->isAJAX()) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Requisição inválida'
                ]);
            }

            $id = (int) $this->request->getPost('id');

            if (!$id) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ID inválido'
                ]);
            }

            // Verificar se o agendamento pertence ao paciente logado
            $pacienteId = $this->session->get('paciente_id');
            if (!$pacienteId) {
                $usuarioId = $this->session->get('ID_Usuario');
                if ($usuarioId) {
                    $paciente = $this->authModel->getPacienteByUsuarioId($usuarioId);
                    if ($paciente) {
                        $pacienteId = $paciente->ID_Paciente;
                        $this->session->set('paciente_id', $pacienteId);
                    }
                }
            }

            if (!$pacienteId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Usuário não autenticado.'
                ]);
            }

            // Verificar se o agendamento existe e pertence ao paciente
            $db = \Config\Database::connect();
            $builder = $db->table('agendamentos');
            $builder->where('ID_Agendamento', $id);
            $builder->where('ID_Paciente', $pacienteId);
            $appointment = $builder->get()->getRow();

            if (!$appointment) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Agendamento não encontrado ou não pertence a você.'
                ]);
            }

            // Log do status atual para depuração
            log_message('debug', 'Status atual do agendamento ID ' . $id . ': ' . $appointment->Status);

            // Verificar se pode cancelar (apenas se estiver Pendente ou Confirmado)
            $statusPermitidos = ['Pendente', 'Confirmado'];
            if (!in_array($appointment->Status, $statusPermitidos)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Este agendamento não pode ser cancelado. Status atual: ' . $appointment->Status
                ]);
            }

            // Atualizar status para Cancelado
            $builder = $db->table('agendamentos');
            $result = $builder->update(
                ['Status' => 'Cancelado'],
                ['ID_Agendamento' => $id]
            );

            if ($result) {
                // Verificar se realmente foi atualizado
                $builder = $db->table('agendamentos');
                $builder->where('ID_Agendamento', $id);
                $updated = $builder->get()->getRow();

                log_message('debug', 'Agendamento ID ' . $id . ' atualizado para: ' . ($updated ? $updated->Status : 'N/A'));

                // Gerar novo token CSRF
                $newToken = csrf_hash();

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Agendamento cancelado com sucesso!',
                    'csrf_token' => $newToken,
                    'csrf_name' => 'csrf_test_name'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Erro ao cancelar agendamento. Tente novamente.'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'cancelarAgendamento - ERRO: ' . $e->getMessage());
            log_message('error', 'cancelarAgendamento - TRACE: ' . $e->getTraceAsString());

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Erro interno do servidor: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Logout
     */
    public function logout()
    {
        $this->session->destroy();
        return redirect()->to('auth/login');
    }

    /**
     * AJAX: Busca detalhes de um agendamento
     */
    public function getAppointmentDetails()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Requisição inválida'
            ]);
        }

        $id = (int) $this->request->getPost('id');

        if (!$id) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID inválido'
            ]);
        }

        // Verificar se o agendamento pertence ao paciente logado
        $pacienteId = $this->session->get('paciente_id');
        if (!$pacienteId) {
            $usuarioId = $this->session->get('ID_Usuario');
            if ($usuarioId) {
                $paciente = $this->authModel->getPacienteByUsuarioId($usuarioId);
                if ($paciente) {
                    $pacienteId = $paciente->ID_Paciente;
                    $this->session->set('paciente_id', $pacienteId);
                }
            }
        }

        if (!$pacienteId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Sessão inválida.'
            ]);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('agendamentos a');
        $builder->select('
        a.*, 
        m.Nome as medico_nome, 
        m.Sobrenome as medico_sobrenome, 
        e.Nome as especialidade,
        p.Nome as paciente_nome,
        p.Sobrenome as paciente_sobrenome,
        p.Data_Nascimento as paciente_data_nasc,
        p.Telefone as paciente_telefone,
        p.BI as paciente_bi
    ');
        $builder->join('medicos m', 'm.ID_Medico = a.ID_Medico', 'left');
        $builder->join('especialidades e', 'e.ID_Especialidade = m.ID_Especialidade', 'left');
        $builder->join('pacientes p', 'p.ID_Paciente = a.ID_Paciente', 'left');
        $builder->where('a.ID_Agendamento', $id);
        $builder->where('a.ID_Paciente', $pacienteId);
        $result = $builder->get()->getRow();

        if (!$result) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Agendamento não encontrado'
            ]);
        }

        // Verificar se o agendamento pode ser cancelado
        $podeCancelar = in_array($result->Status, ['Pendente', 'Confirmado']);

        // Retornar também o token CSRF
        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'id' => $result->ID_Agendamento,
                'medico' => trim(($result->medico_nome ?? '') . ' ' . ($result->medico_sobrenome ?? '')),
                'especialidade' => $result->especialidade ?? 'N/A',
                'data' => $result->Data_Agendamento,
                'data_formatada' => date('d/m/Y', strtotime($result->Data_Agendamento)),
                'hora' => substr($result->Hora_Agendamento, 0, 5),
                'status' => $result->Status,
                'motivo' => $result->Motivo ?? '',
                'criado_em' => date('d/m/Y H:i', strtotime($result->Criado_Em ?? 'now')),
                'pode_cancelar' => $podeCancelar,
                // Campos para "outra pessoa"
                'tipo_agendamento' => $result->tipo_agendamento ?? 'self',
                'paciente_nome' => trim(($result->paciente_nome ?? '') . ' ' . ($result->paciente_sobrenome ?? '')),
                'paciente_relacao' => $result->paciente_relacao ?? '',
                'paciente_data_nasc' => isset($result->paciente_data_nasc) ? date('d/m/Y', strtotime($result->paciente_data_nasc)) : '',
                'paciente_doc_tipo' => $result->paciente_doc_tipo ?? '',
                'paciente_doc_num' => $result->paciente_doc_num ?? '',
                'responsavel_nome' => trim(($result->Nome ?? '') . ' ' . ($result->Sobrenome ?? '')),
                'telefone' => $result->Telefone ?? '',
                'bi' => $result->BI ?? ''
            ],
            'csrf_token' => csrf_hash(),
            'csrf_name' => 'csrf_test_name'
        ]);
    }

    /**
     * AJAX: Atualiza um agendamento
     */
    public function updateAppointment()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Requisição inválida'
            ]);
        }

        $id = (int) $this->request->getPost('id');
        $data = $this->request->getPost('data');
        $hora = $this->request->getPost('hora');
        $motivo = $this->request->getPost('motivo');

        if (!$id || !$data || !$hora) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Dados incompletos'
            ]);
        }

        // Verificar se o paciente está logado
        $pacienteId = $this->session->get('paciente_id');

        if (!$pacienteId) {
            $usuarioId = $this->session->get('ID_Usuario');
            if ($usuarioId) {
                $paciente = $this->authModel->getPacienteByUsuarioId($usuarioId);
                if ($paciente) {
                    $pacienteId = $paciente->ID_Paciente;
                    $this->session->set('paciente_id', $pacienteId);
                }
            }
        }

        if (!$pacienteId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Sessão inválida. Faça login novamente.'
            ]);
        }

        // Verificar se o agendamento existe, está pendente e pertence ao paciente
        $db = \Config\Database::connect();
        $builder = $db->table('agendamentos');
        $builder->where('ID_Agendamento', $id);
        $builder->where('Status', 'Pendente');
        $builder->where('ID_Paciente', $pacienteId);
        $existing = $builder->get()->getRow();

        if (!$existing) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Agendamento não encontrado ou não pode ser editado'
            ]);
        }

        // Verificar disponibilidade do novo horário
        $builder = $db->table('agendamentos');
        $builder->where('ID_Medico', $existing->ID_Medico);
        $builder->where('Data_Agendamento', $data);
        $builder->where('Hora_Agendamento', $hora);
        $builder->where('Status !=', 'Cancelado');
        $builder->where('ID_Agendamento !=', $id);
        $conflict = $builder->get()->getRow();

        if ($conflict) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Horário já ocupado. Escolha outro.'
            ]);
        }

        // Atualizar
        $updateData = [
            'Data_Agendamento' => $data,
            'Hora_Agendamento' => $hora,
            'Motivo' => !empty($motivo) ? $motivo : null
        ];

        $builder = $db->table('agendamentos');
        $builder->where('ID_Agendamento', $id);
        $result = $builder->update($updateData);

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Agendamento atualizado com sucesso!'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Erro ao atualizar agendamento'
            ]);
        }
    }
}
