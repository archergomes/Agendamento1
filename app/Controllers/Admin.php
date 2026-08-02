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
     * Página de disponibilidade de médicos
     */
    public function disponibilidade()
    {
        $data['medicos'] = $this->adminModel->getMedicos();
        $data['active_menu'] = 'disponibilidade';
        return view('admin/disponibilidade', $data);
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
        $name = $this->request->getPost('name');
        $specialty = $this->request->getPost('specialty');
        $phone = $this->request->getPost('phone');
        $email = $this->request->getPost('email');

        if (!$bi || !$name || !$specialty || !$phone) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'Dados incompletos']);
        }

        try {
            $result = $this->adminModel->updateDoctor($bi, $name, $specialty, $phone, $email);

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
}
