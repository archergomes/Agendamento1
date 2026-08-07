<?php

namespace App\Controllers;

use App\Models\AdminModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

class Admin extends Controller
{
    protected $adminModel;
    protected $session;
    protected $request;

    public function __construct()
    {
        $this->adminModel = new AdminModel();
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();

        helper(['url', 'form']);

        // Verifica se o usuário está logado e é admin
        if (!$this->session->get('logged_in')) {
            return redirect()->to('auth/login');
        }

        // Verifica se o usuário é admin
        $tipoUsuario = $this->session->get('Tipo_Usuario');
        if ($tipoUsuario !== 'Admin') {
            return redirect()->to('agenda');
        }
    }

    /**
     * Página principal do Dashboard
     */
    public function index()
    {
        $data['metrics'] = $this->adminModel->getMetrics();
        $data['active_menu'] = 'dashboard';
        return view('admin/dashboard', $data);
    }

    /**
     * Página de listagem de pacientes
     */
    public function pacientes()
    {
        $data['pacientes'] = $this->adminModel->getPacientes();
        $data['active_menu'] = 'pacientes';
        return view('admin/pacientes', $data);
    }

    /**
     * Página de listagem de médicos
     */
    public function medicos()
    {
        $data['medicos'] = $this->adminModel->getMedicos();
        $data['active_menu'] = 'medicos';
        return view('admin/medicos', $data);
    }

    /**
     * Página de listagem de secretários
     */
    public function secretarios()
    {
        $data['secretarios'] = $this->adminModel->getSecretarios();
        $data['active_menu'] = 'secretarios';
        return view('admin/secretarios', $data);
    }

    /**
     * Página de listagem de agendamentos
     */
    public function agendamentos()
    {
        $data['agendamentos'] = $this->adminModel->getAgendamentos();
        $data['active_menu'] = 'agendamentos';
        return view('admin/agendamentos', $data);
    }

    /**
     * Página de cadastro de paciente
     */
    public function cadPaciente()
    {
        $data['active_menu'] = 'cad_paciente';
        return view('admin/cad_paciente', $data);
    }

    /**
     * Página de cadastro de secretário
     */
    public function cadSecretario()
    {
        $data['active_menu'] = 'cad_secretario';
        return view('admin/cad_secretario', $data);
    }

    /**
     * Página de cadastro de médico
     */
    public function cadMedico()
    {
        $data['active_menu'] = 'cad_medico';
        return view('admin/cad_medico', $data);
    }

    /**
     * Página de relatórios
     */
    public function relatorios()
    {
        $data['active_menu'] = 'relatorios';
        return view('admin/relatorios', $data);
    }

    /**
     * Página de configurações
     */
    public function configuracoes()
    {
        $data['active_menu'] = 'configuracoes';
        return view('admin/config', $data);
    }

    /**
     * AJAX: Cancelar agendamento
     */
    public function cancelAppointment()
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

        $id = (int) $this->request->getPost('id');

        if (!$id) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'ID do agendamento não fornecido']);
        }

        try {
            $result = $this->adminModel->cancelAppointment($id);

            if ($result) {
                return $this->response->setJSON([
                    'success' => 'Agendamento cancelado com sucesso'
                ]);
            } else {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON(['error' => 'Erro ao cancelar agendamento']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao cancelar agendamento: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro interno do servidor']);
        }
    }

    /**
     * AJAX: Excluir agendamento
     */
    public function deleteAppointment()
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

        $id = (int) ($this->request->getPost('id') ?? $this->request->getJSON()->id ?? 0);

        if (!$id) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'ID do agendamento não fornecido']);
        }

        try {
            $result = $this->adminModel->deleteAppointment($id);

            if ($result) {
                return $this->response->setJSON([
                    'success' => 'Agendamento excluído com sucesso'
                ]);
            } else {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON(['error' => 'Erro ao excluir agendamento']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao excluir agendamento: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro interno do servidor']);
        }
    }

    /**
     * AJAX: Buscar métricas para o dashboard
     */
    public function metrics()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        try {
            $metrics = $this->adminModel->getMetrics();
            return $this->response->setJSON($metrics);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar métricas: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar métricas']);
        }
    }

    /**
     * AJAX: Buscar atividades recentes
     */
    public function activity()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        $query = $this->request->getGet('query') ?? '';

        try {
            $activities = $this->adminModel->getRecentActivity($query);
            return $this->response->setJSON($activities);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar atividades: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar atividades']);
        }
    }

    /**
     * AJAX: Buscar dados de um paciente por BI
     */
    public function patient($bi)
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        try {
            $patient = $this->adminModel->getPatientByBi($bi);

            if (!$patient) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON(['error' => 'Paciente não encontrado']);
            }

            return $this->response->setJSON($patient);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar paciente: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao buscar paciente']);
        }
    }

    /**
     * AJAX: Buscar dados de um médico por BI
     */
    public function doctor($bi)
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        try {
            $doctor = $this->adminModel->getDoctorByBi($bi);

            if (!$doctor) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON(['error' => 'Médico não encontrado']);
            }

            return $this->response->setJSON($doctor);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar médico: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao buscar médico']);
        }
    }

    /**
     * AJAX: Atualizar paciente
     */
    public function updatePatient()
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

        $bi = $this->request->getPost('bi');
        $name = $this->request->getPost('name');
        $phone = $this->request->getPost('phone');
        $email = $this->request->getPost('email');

        if (!$bi || !$name || !$phone) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'Dados incompletos']);
        }

        try {
            $result = $this->adminModel->updatePatient($bi, $name, $phone, $email);

            if ($result) {
                return $this->response->setJSON([
                    'success' => 'Paciente atualizado com sucesso'
                ]);
            } else {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON(['error' => 'Erro ao atualizar paciente']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao atualizar paciente: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro interno do servidor']);
        }
    }

    // ============================================================
    // NOVOS MÉTODOS PARA GRÁFICOS
    // ============================================================

    /**
     * AJAX: Dados para gráfico de tendência de agendamentos (últimos 14 dias)
     */
    public function chartAppointmentsTrend()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        try {
            $data = $this->adminModel->getAppointmentsTrend();
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar tendência de agendamentos: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar dados do gráfico']);
        }
    }

    /**
     * AJAX: Dados para gráfico de status dos agendamentos
     */
    public function chartAppointmentsStatus()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        try {
            $data = $this->adminModel->getAppointmentsStatus();
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar status de agendamentos: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar dados do gráfico']);
        }
    }

    /**
     * AJAX: Dados para gráfico de médicos por especialidade
     */
    public function chartDoctorsSpecialty()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        try {
            $data = $this->adminModel->getDoctorsBySpecialty();
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar médicos por especialidade: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar dados do gráfico']);
        }
    }

    /**
     * AJAX: Dados para gráfico de pacientes por mês
     */
    public function chartPatientsMonthly()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        try {
            $data = $this->adminModel->getPatientsMonthly();
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar pacientes por mês: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar dados do gráfico']);
        }
    }

    /**
     * AJAX: Buscar pacientes com filtro
     */
    public function getPatients()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        $query = $this->request->getGet('query') ?? '';

        try {
            $patients = $this->adminModel->getPatients($query);
            return $this->response->setJSON($patients);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar pacientes: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar pacientes']);
        }
    }

    /**
     * AJAX: Excluir paciente
     */
    public function deletePatient()
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

        $bi = $this->request->getPost('bi');

        if (!$bi) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'BI do paciente não fornecido']);
        }

        try {
            $result = $this->adminModel->deletePatient($bi);

            if ($result) {
                return $this->response->setJSON([
                    'success' => 'Paciente excluído com sucesso'
                ]);
            } else {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON(['error' => 'Erro ao excluir paciente']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao excluir paciente: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro interno do servidor']);
        }
    }

    /**
     * AJAX: Buscar detalhes de um paciente por BI
     */
    public function getPatientDetails()
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

        $bi = $this->request->getPost('bi');

        if (!$bi) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'BI do paciente não fornecido']);
        }

        try {
            $patient = $this->adminModel->getPatientDetails($bi);

            if (!$patient) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON(['error' => 'Paciente não encontrado']);
            }

            return $this->response->setJSON($patient);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar detalhes do paciente: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar dados do paciente']);
        }
    }

    /**
     * AJAX: Buscar médicos com filtro
     */
    public function getDoctors()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        $query = $this->request->getGet('query') ?? '';

        try {
            $doctors = $this->adminModel->getDoctorsList($query);
            return $this->response->setJSON($doctors);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar médicos: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar médicos']);
        }
    }

    /**
     * AJAX: Buscar detalhes de um médico por BI
     */
    public function getDoctorDetails()
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

        $bi = $this->request->getPost('bi');

        if (!$bi) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'BI do médico não fornecido']);
        }

        try {
            $doctor = $this->adminModel->getDoctorDetails($bi);

            if (!$doctor) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON(['error' => 'Médico não encontrado']);
            }

            return $this->response->setJSON($doctor);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar detalhes do médico: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar dados do médico']);
        }
    }

    /**
     * AJAX: Excluir médico
     */
    public function deleteDoctor()
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

        $bi = $this->request->getPost('bi');

        if (!$bi) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'BI do médico não fornecido']);
        }

        try {
            $result = $this->adminModel->deleteDoctor($bi);

            if ($result) {
                return $this->response->setJSON([
                    'success' => 'Médico excluído com sucesso'
                ]);
            } else {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON(['error' => 'Erro ao excluir médico']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao excluir médico: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro interno do servidor']);
        }
    }

    /**
     * AJAX: Atualizar médico
     */
    public function updateDoctor()
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

        $bi = $this->request->getPost('bi');
        $nome = $this->request->getPost('nome');
        $telefone = $this->request->getPost('telefone');
        $email = $this->request->getPost('email');
        $especialidade = $this->request->getPost('especialidade');
        $licenca = $this->request->getPost('licenca');

        if (!$bi || !$nome || !$telefone || !$especialidade) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'Dados incompletos']);
        }

        try {
            $result = $this->adminModel->updateDoctorFull($bi, $nome, $telefone, $email, $especialidade, $licenca);

            if ($result) {
                return $this->response->setJSON([
                    'success' => 'Médico atualizado com sucesso'
                ]);
            } else {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON(['error' => 'Erro ao atualizar médico']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao atualizar médico: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro interno do servidor']);
        }
    }

    /**
     * AJAX: Buscar secretários com filtro
     */
    public function getSecretaries()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        $query = $this->request->getGet('query') ?? '';

        try {
            $secretaries = $this->adminModel->getSecretariesList($query);
            return $this->response->setJSON($secretaries);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar secretários: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar secretários']);
        }
    }

    /**
     * AJAX: Buscar detalhes de um secretário por ID
     */
    public function getSecretaryDetails()
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
                ->setJSON(['error' => 'ID do secretário não fornecido']);
        }

        try {
            $secretary = $this->adminModel->getSecretaryDetails($id);

            if (!$secretary) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON(['error' => 'Secretário não encontrado']);
            }

            return $this->response->setJSON($secretary);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar detalhes do secretário: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar dados do secretário']);
        }
    }

    /**
     * AJAX: Excluir secretário
     */
    public function deleteSecretary()
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
                ->setJSON(['error' => 'ID do secretário não fornecido']);
        }

        try {
            $result = $this->adminModel->deleteSecretary($id);

            if ($result) {
                return $this->response->setJSON([
                    'success' => 'Secretário excluído com sucesso'
                ]);
            } else {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON(['error' => 'Erro ao excluir secretário']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao excluir secretário: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro interno do servidor']);
        }
    }

    /**
     * AJAX: Buscar agendamentos com filtro
     */
    public function getAppointments()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        $query = $this->request->getGet('query') ?? '';

        try {
            $appointments = $this->adminModel->getAppointmentsList($query);
            return $this->response->setJSON($appointments);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar agendamentos: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar agendamentos']);
        }
    }

    /**
     * AJAX: Buscar detalhes de um agendamento
     */
    public function getAppointmentDetails()
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
                ->setJSON(['error' => 'ID do agendamento não fornecido']);
        }

        try {
            $appointment = $this->adminModel->getAppointmentDetails($id);

            if (!$appointment) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON(['error' => 'Agendamento não encontrado']);
            }

            return $this->response->setJSON($appointment);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar detalhes do agendamento: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar dados do agendamento']);
        }
    }

    /**
     * AJAX: Atualizar agendamento
     */
    public function updateAppointment()
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
        $data = $this->request->getPost('data');
        $hora = $this->request->getPost('hora');
        $status = $this->request->getPost('status');
        $motivo = $this->request->getPost('motivo');

        if (!$id || !$data || !$hora || !$status) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'Dados incompletos']);
        }

        try {
            $result = $this->adminModel->updateAppointment($id, $data, $hora, $status, $motivo);

            if ($result) {
                return $this->response->setJSON([
                    'success' => 'Agendamento atualizado com sucesso'
                ]);
            } else {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON(['error' => 'Erro ao atualizar agendamento']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao atualizar agendamento: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro interno do servidor']);
        }
    }

    /**
     * AJAX: Criar paciente
     */
    public function createPatient()
    {
        // Verifica se é AJAX
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        // Verifica se é POST
        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        // Obtém os dados do POST
        $bi = $this->request->getPost('bi');
        $nome = $this->request->getPost('nome');
        $telefone = $this->request->getPost('telefone');
        $email = $this->request->getPost('email');
        $dataNascimento = $this->request->getPost('data_nascimento');
        $genero = $this->request->getPost('genero');
        $endereco = $this->request->getPost('endereco');

        // Log para debug
        log_message('debug', 'createPatient - Dados recebidos: ' . print_r([
            'bi' => $bi,
            'nome' => $nome,
            'telefone' => $telefone,
            'email' => $email,
            'data_nascimento' => $dataNascimento,
            'genero' => $genero,
            'endereco' => $endereco
        ], true));

        // Validação
        if (!$bi || !$nome || !$telefone) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'Dados incompletos. Preencha todos os campos obrigatórios.']);
        }

        try {
            // Verificar se BI já existe
            $existing = $this->adminModel->checkPatientExists($bi);
            if ($existing) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON(['error' => 'Já existe um paciente cadastrado com este BI.']);
            }

            // Separar nome e sobrenome
            $nameParts = explode(' ', trim($nome), 2);
            $nomePart = $nameParts[0] ?? '';
            $sobrenomePart = $nameParts[1] ?? '';

            // Preparar dados para inserção
            $data = [
                'Nome' => $nomePart,
                'Sobrenome' => $sobrenomePart,
                'Telefone' => $telefone,
                'BI' => $bi,
                'email' => $email,
                'Endereco' => $endereco
            ];

            if (!empty($dataNascimento)) {
                $data['Data_Nascimento'] = $dataNascimento;
            }

            if (!empty($genero)) {
                $data['Genero'] = $genero;
            }

            log_message('debug', 'createPatient - Dados para inserção: ' . print_r($data, true));

            $result = $this->adminModel->insertPatient($data);

            if ($result) {
                return $this->response->setJSON([
                    'success' => 'Paciente cadastrado com sucesso!'
                ]);
            } else {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON(['error' => 'Erro ao cadastrar paciente no banco de dados.']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao cadastrar paciente: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $e->getTraceAsString());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro interno do servidor: ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX: Criar secretário
     */
    public function createSecretary()
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

        $bi = $this->request->getPost('bi');
        $nome = $this->request->getPost('nome');
        $telefone = $this->request->getPost('telefone');
        $email = $this->request->getPost('email');
        $senha = $this->request->getPost('senha');

        if (!$bi || !$nome || !$telefone || !$email || !$senha) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'Dados incompletos']);
        }

        try {
            $result = $this->adminModel->createSecretary($bi, $nome, $telefone, $email, $senha);

            if ($result) {
                return $this->response->setJSON([
                    'success' => 'Secretário cadastrado com sucesso'
                ]);
            } else {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON(['error' => 'Erro ao cadastrar secretário']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao cadastrar secretário: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro interno do servidor']);
        }
    }

    /**
     * AJAX: Atualizar secretário
     */
    public function updateSecretary()
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

        $bi = $this->request->getPost('bi');
        $nome = $this->request->getPost('nome');
        $telefone = $this->request->getPost('telefone');
        $email = $this->request->getPost('email');

        if (!$bi || !$nome || !$telefone || !$email) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'Dados incompletos']);
        }

        try {
            $result = $this->adminModel->updateSecretary($bi, $nome, $telefone, $email);

            if ($result) {
                return $this->response->setJSON([
                    'success' => 'Secretário atualizado com sucesso'
                ]);
            } else {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON(['error' => 'Erro ao atualizar secretário']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao atualizar secretário: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro interno do servidor']);
        }
    }

    /**
     * AJAX: Criar médico
     */
    public function createDoctor()
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

        $bi = $this->request->getPost('bi');
        $nome = $this->request->getPost('nome');
        $telefone = $this->request->getPost('telefone');
        $email = $this->request->getPost('email');
        $especialidade = $this->request->getPost('especialidade');
        $licenca = $this->request->getPost('licenca');

        if (!$bi || !$nome || !$telefone || !$especialidade || !$licenca) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'Dados incompletos']);
        }

        try {
            $result = $this->adminModel->createDoctor($bi, $nome, $telefone, $email, $especialidade, $licenca);

            if ($result) {
                return $this->response->setJSON([
                    'success' => 'Médico cadastrado com sucesso'
                ]);
            } else {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON(['error' => 'Erro ao cadastrar médico']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao cadastrar médico: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro interno do servidor']);
        }
    }

    /**
     * AJAX: Buscar dados para relatórios
     */
    public function getReportData()
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

        $type = $this->request->getPost('type');
        $dateFrom = $this->request->getPost('date_from');
        $dateTo = $this->request->getPost('date_to');
        $doctorId = $this->request->getPost('doctor_id');
        $status = $this->request->getPost('status');

        try {
            $data = $this->adminModel->getReportData($type, $dateFrom, $dateTo, $doctorId, $status);
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao gerar relatório: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao gerar relatório']);
        }
    }

    /**
     * AJAX: Exportar relatório
     */
    public function exportReport()
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

        $format = $this->request->getPost('format');
        $type = $this->request->getPost('type');
        $dateFrom = $this->request->getPost('date_from');
        $dateTo = $this->request->getPost('date_to');
        $doctorId = $this->request->getPost('doctor_id');
        $status = $this->request->getPost('status');

        try {
            $data = $this->adminModel->getReportData($type, $dateFrom, $dateTo, $doctorId, $status);

            if ($format === 'csv') {
                return $this->exportCSV($data, $type);
            } else {
                return $this->exportPDF($data, $type);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao exportar relatório: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao exportar relatório']);
        }
    }

    private function exportCSV($data, $type)
    {
        $filename = "relatorio_{$type}_" . date('Y-m-d') . ".csv";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Headers based on type
        $headers = [];
        if ($type === 'appointments') {
            $headers = ['Paciente', 'Médico', 'Data', 'Hora', 'Status'];
        } elseif ($type === 'doctors') {
            $headers = ['Médico', 'Especialidade', 'Agendamentos', 'Taxa de Ocupação'];
        } elseif ($type === 'patients') {
            $headers = ['Paciente', 'Visitas', 'Última Visita', 'Status Médio'];
        } else {
            $headers = ['Métrica', 'Valor'];
        }

        fputcsv($output, $headers, ';');

        // Data rows
        foreach ($data as $row) {
            fputcsv($output, (array)$row, ';');
        }

        fclose($output);
        exit();
    }

    private function exportPDF($data, $type)
    {
        // Simple PDF generation - you can use DomPDF or TCPDF
        // For now, return JSON with message
        return $this->response->setJSON([
            'success' => 'PDF exportado com sucesso',
            'data' => $data
        ]);
    }

    /**
     * AJAX: Salvar configurações do hospital
     */
    public function saveHospitalConfig()
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

        $data = [
            'hospital_name' => $this->request->getPost('hospital_name'),
            'hospital_address' => $this->request->getPost('hospital_address'),
            'hospital_phone' => $this->request->getPost('hospital_phone'),
            'hospital_email' => $this->request->getPost('hospital_email'),
            'hospital_description' => $this->request->getPost('hospital_description')
        ];

        try {
            $result = $this->adminModel->saveConfig($data);

            if ($result) {
                return $this->response->setJSON([
                    'success' => 'Configurações do hospital salvas com sucesso!'
                ]);
            } else {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON(['error' => 'Erro ao salvar configurações']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao salvar configurações do hospital: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro interno do servidor']);
        }
    }

    /**
     * AJAX: Salvar configurações do sistema
     */
    public function saveSystemConfig()
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

        $data = [
            'default_duration' => $this->request->getPost('default_duration'),
            'max_appointments' => $this->request->getPost('max_appointments'),
            'email_notifications' => $this->request->getPost('email_notifications'),
            'sms_notifications' => $this->request->getPost('sms_notifications')
        ];

        try {
            $result = $this->adminModel->saveConfig($data);

            if ($result) {
                return $this->response->setJSON([
                    'success' => 'Configurações do sistema salvas com sucesso!'
                ]);
            } else {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON(['error' => 'Erro ao salvar configurações']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao salvar configurações do sistema: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro interno do servidor']);
        }
    }

    /**
     * Página de disponibilidade
     */
    public function disponibilidade()
    {
        $data['medicos'] = $this->adminModel->getMedicos();
        $data['horarios'] = $this->adminModel->getHorarios();
        $data['active_menu'] = 'disponibilidade';
        return view('admin/disponibilidade', $data);
    }

    /**
     * AJAX: Salvar horário
     */
    public function saveSchedule()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['error' => 'Método não permitido']);
        }

        $id = $this->request->getPost('id');
        $doctorId = $this->request->getPost('doctor_id');
        $day = $this->request->getPost('day');
        $start = $this->request->getPost('start');
        $end = $this->request->getPost('end');
        $status = $this->request->getPost('status');

        if (!$doctorId || !$day || !$start || !$end) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Dados incompletos']);
        }

        try {
            if ($id) {
                $result = $this->adminModel->updateSchedule($id, $doctorId, $day, $start, $end, $status);
            } else {
                $result = $this->adminModel->createSchedule($doctorId, $day, $start, $end, $status);
            }

            if ($result) {
                return $this->response->setJSON(['success' => 'Horário salvo com sucesso!']);
            } else {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Erro ao salvar horário']);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Erro interno do servidor']);
        }
    }

    /**
     * AJAX: Excluir horário
     */
    public function deleteSchedule()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['error' => 'Método não permitido']);
        }

        $id = $this->request->getPost('id');

        if (!$id) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'ID não fornecido']);
        }

        try {
            $result = $this->adminModel->deleteSchedule($id);

            if ($result) {
                return $this->response->setJSON(['success' => 'Horário excluído com sucesso!']);
            } else {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Erro ao excluir horário']);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Erro interno do servidor']);
        }
    }

    /**
     * Página de cadastro de agendamento
     */
    public function cadAgendamento()
    {
        $data['medicos'] = $this->adminModel->getMedicos();
        $data['pacientes'] = $this->adminModel->getPacientes();
        $data['active_menu'] = 'cad_agendamento';
        return view('admin/cad_agendamentos', $data);
    }

    /**
     * AJAX: Criar agendamento
     */
    public function createAppointment()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON(['error' => 'Método não permitido']);
        }

        $pacienteId = $this->request->getPost('paciente_id');
        $medicoId = $this->request->getPost('medico_id');
        $data = $this->request->getPost('data');
        $hora = $this->request->getPost('hora');
        $status = $this->request->getPost('status');
        $motivo = $this->request->getPost('motivo');

        if (!$pacienteId || !$medicoId || !$data || !$hora) {
            return $this->response->setStatusCode(400)->setJSON(['error' => 'Dados incompletos']);
        }

        try {
            $result = $this->adminModel->createAppointment($pacienteId, $medicoId, $data, $hora, $status, $motivo);

            if ($result) {
                return $this->response->setJSON(['success' => 'Agendamento criado com sucesso!']);
            } else {
                return $this->response->setStatusCode(500)->setJSON(['error' => 'Erro ao criar agendamento']);
            }
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Erro interno do servidor']);
        }
    }
}
