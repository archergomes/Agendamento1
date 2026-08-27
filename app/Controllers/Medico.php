<?php

namespace App\Controllers;

use App\Models\MedicoModel;
use App\Models\AdminModel;
use CodeIgniter\Controller;

class Medico extends Controller
{
    protected $medicoModel;
    protected $adminModel;
    protected $session;
    protected $request;
    protected $medicoId;

    public function __construct()
    {
        $this->medicoModel = new MedicoModel();
        $this->adminModel = new AdminModel();
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();

        helper(['url', 'form']);

        // Verifica se o usuário está logado
        if (!$this->session->get('logged_in')) {
            return redirect()->to('auth/login');
        }

        // Verifica se é médico
        $tipoUsuario = $this->session->get('Tipo_Usuario');
        if ($tipoUsuario !== 'Medico') {
            return redirect()->to('agenda');
        }

        // Busca o ID do médico
        $this->medicoId = $this->session->get('ID_Referencia');
        if (!$this->medicoId) {
            // Tentar buscar pelo ID_Usuario
            $usuarioId = $this->session->get('ID_Usuario');
            if ($usuarioId) {
                $medico = $this->medicoModel->getMedicoByUsuarioId($usuarioId);
                if ($medico) {
                    $this->medicoId = $medico->ID_Medico;
                    $this->session->set('ID_Referencia', $this->medicoId);
                }
            }
        }

        // Se ainda não tiver ID, redirecionar
        if (!$this->medicoId) {
            $this->session->setFlashdata('error', 'Dados do médico não encontrados. Faça login novamente.');
            return redirect()->to('auth/login');
        }
    }

    /**
     * Dashboard do médico
     */
    public function index()
    {
        $data['medico'] = $this->medicoModel->getMedicoById($this->medicoId);
        $data['active_menu'] = 'dashboard';
        return view('medico/dashboard', $data);
    }

    /**
     * Página da agenda
     */
    public function agenda()
    {
        $data['medico'] = $this->medicoModel->getMedicoById($this->medicoId);
        $data['active_menu'] = 'agenda';
        return view('medico/agenda', $data);
    }

    /**
     * AJAX: Buscar dados da agenda (para a view)
     */
    public function getAgendaData()
    {
        // Verifica se é AJAX
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        $date = $this->request->getGet('date');
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('query');

        // Log para debug
        log_message('debug', 'getAgendaData - date: ' . $date . ', status: ' . $status . ', search: ' . $search);

        try {
            $data = $this->medicoModel->getAgenda($this->medicoId, $date, $status, $search);

            // Garantir que a resposta é um array
            if (!is_array($data)) {
                $data = [];
            }

            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar agenda: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $e->getTraceAsString());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar agenda: ' . $e->getMessage()]);
        }
    }

    /**
     * Página de pacientes
     */
    public function pacientes()
    {
        $data['medico'] = $this->medicoModel->getMedicoById($this->medicoId);
        $data['active_menu'] = 'pacientes';
        return view('medico/pacientes', $data);
    }

    /**
     * Página de disponibilidade
     */
    public function disponibilidade()
    {
        $data['medico'] = $this->medicoModel->getMedicoById($this->medicoId);
        $data['horarios'] = $this->medicoModel->getHorarios($this->medicoId);
        $data['active_menu'] = 'disponibilidade';
        return view('medico/disponibilidade', $data);
    }

    /**
     * Página de histórico
     */
    public function historico()
    {
        $data['medico'] = $this->medicoModel->getMedicoById($this->medicoId);
        $data['active_menu'] = 'historico';
        return view('medico/historico', $data);
    }

    /**
     * Página de perfil
     */
    public function perfil()
    {
        $data['medico'] = $this->medicoModel->getMedicoById($this->medicoId);
        $data['active_menu'] = 'perfil';
        return view('medico/perfil', $data);
    }

    // ==================== MÉTODOS AJAX ====================

    /**
     * AJAX: Buscar métricas do dashboard
     */
    public function metrics()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        try {
            $data = $this->medicoModel->getMetrics($this->medicoId);
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar métricas: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar métricas']);
        }
    }

    /**
     * AJAX: Próxima consulta
     */
    public function proximaConsulta()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        try {
            $data = $this->medicoModel->getNextAppointment($this->medicoId);
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar próxima consulta: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar próxima consulta']);
        }
    }

    /**
     * AJAX: Tendência de consultas (gráfico)
     */
    public function chartConsultasTrend()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        try {
            $data = $this->medicoModel->getAppointmentsTrend($this->medicoId);
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar tendência: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar dados do gráfico']);
        }
    }

    /**
     * AJAX: Distribuição de status (gráfico)
     */
    public function chartStatus()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        try {
            $data = $this->medicoModel->getStatusDistribution($this->medicoId);
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar status: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar dados do gráfico']);
        }
    }

    /**
     * AJAX: Novos vs Retorno (gráfico)
     */
    public function chartNovosRetorno()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        try {
            $data = $this->medicoModel->getNewReturnRatio($this->medicoId);
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar novos vs retorno: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar dados do gráfico']);
        }
    }

    /**
     * AJAX: Faixa etária (gráfico)
     */
    public function chartFaixaEtaria()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        try {
            $data = $this->medicoModel->getAgeDistribution($this->medicoId);
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar faixa etária: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar dados do gráfico']);
        }
    }

    /**
     * AJAX: Agenda do dia
     */
    public function agendaAjax()
    {
        // Verifica se é AJAX
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        $date = $this->request->getGet('date');
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('query');

        // Log para debug
        log_message('debug', 'agendaAjax - date: ' . $date . ', status: ' . $status . ', search: ' . $search);

        try {
            $data = $this->medicoModel->getAgenda($this->medicoId, $date, $status, $search);
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar agenda: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $e->getTraceAsString());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar agenda: ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX: Próximas consultas
     */
    public function proximasConsultas()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        try {
            $data = $this->medicoModel->getUpcomingAppointments($this->medicoId);
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar próximas consultas: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar próximas consultas']);
        }
    }

    /**
     * AJAX: Atualizar status da consulta
     */
    public function updateAppointmentStatus()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        $appointmentId = $this->request->getPost('appointment_id');
        $status = $this->request->getPost('status');

        if (!$appointmentId || !$status) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'Dados incompletos']);
        }

        try {
            $result = $this->medicoModel->updateAppointmentStatus($appointmentId, $status);
            return $this->response->setJSON($result);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao atualizar status: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao atualizar status']);
        }
    }

    /**
     * AJAX: Detalhes do paciente
     */
    public function patient($bi)
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        try {
            $data = $this->medicoModel->getPatientDetails($bi);
            if (!$data) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON(['error' => 'Paciente não encontrado']);
            }
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar paciente: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar dados do paciente']);
        }
    }

    /**
     * AJAX: Lista de pacientes do médico
     */
    public function getPatients()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        $search = $this->request->getGet('search') ?? '';
        $page = (int) ($this->request->getGet('page') ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        try {
            $data = $this->medicoModel->getPatients($this->medicoId, $search, $limit, $offset);
            $total = $this->medicoModel->countPatients($this->medicoId, $search);

            return $this->response->setJSON([
                'data' => $data,
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => ceil($total / $limit)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar pacientes: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar pacientes']);
        }
    }

    /**
     * AJAX: Histórico de consultas
     */
    public function getHistory()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        $period = (int) ($this->request->getGet('period') ?? 30);
        $status = $this->request->getGet('status') ?? '';
        $search = $this->request->getGet('search') ?? '';
        $page = (int) ($this->request->getGet('page') ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        try {
            $data = $this->medicoModel->getHistory($this->medicoId, $period, $status, $search, $limit, $offset);
            $total = $this->medicoModel->countHistory($this->medicoId, $period, $status, $search);

            return $this->response->setJSON([
                'data' => $data,
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => ceil($total / $limit)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar histórico: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar histórico']);
        }
    }

    /**
     * AJAX: Salvar horário de disponibilidade
     */
    public function saveSchedule()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        $id = $this->request->getPost('id');
        $day = $this->request->getPost('day');
        $start = $this->request->getPost('start');
        $end = $this->request->getPost('end');
        $status = $this->request->getPost('status') ?? 'ativo';

        if (!$day || !$start || !$end) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'Dados incompletos']);
        }

        if ($start >= $end) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'Horário de início deve ser menor que o horário de fim']);
        }

        try {
            if ($id) {
                $result = $this->medicoModel->updateSchedule($id, $this->medicoId, $day, $start, $end, $status);
            } else {
                $result = $this->medicoModel->createSchedule($this->medicoId, $day, $start, $end, $status);
            }

            if ($result) {
                return $this->response->setJSON(['success' => 'Horário salvo com sucesso!']);
            } else {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON(['error' => 'Erro ao salvar horário']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao salvar horário: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro interno do servidor']);
        }
    }

    /**
     * AJAX: Excluir horário de disponibilidade
     */
    public function deleteSchedule()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        $id = $this->request->getPost('id');

        if (!$id) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'ID não fornecido']);
        }

        try {
            $result = $this->medicoModel->deleteSchedule($id, $this->medicoId);

            if ($result) {
                return $this->response->setJSON(['success' => 'Horário excluído com sucesso!']);
            } else {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON(['error' => 'Erro ao excluir horário']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao excluir horário: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro interno do servidor']);
        }
    }

    /**
     * AJAX: Atualizar perfil do médico
     */
    public function updateProfile()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        $nome = $this->request->getPost('nome');
        $sobrenome = $this->request->getPost('sobrenome');
        $telefone = $this->request->getPost('telefone');
        $email = $this->request->getPost('email');
        $especialidade = $this->request->getPost('especialidade');

        if (!$nome || !$sobrenome || !$telefone || !$especialidade) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'Dados incompletos']);
        }

        try {
            $result = $this->medicoModel->updateProfile(
                $this->medicoId,
                $nome,
                $sobrenome,
                $telefone,
                $email,
                $especialidade
            );

            if ($result) {
                return $this->response->setJSON(['success' => 'Perfil atualizado com sucesso!']);
            } else {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON(['error' => 'Erro ao atualizar perfil']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao atualizar perfil: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro interno do servidor']);
        }
    }

    /**
     * AJAX: Buscar horários de disponibilidade
     */
    public function getSchedules()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        try {
            $data = $this->medicoModel->getHorarios($this->medicoId);
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar horários: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar horários']);
        }
    }
}
