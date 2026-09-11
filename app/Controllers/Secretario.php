<?php

namespace App\Controllers;

use App\Models\SecretarioModel;
use App\Models\AdminModel;
use CodeIgniter\Controller;

class Secretario extends Controller
{
    protected $secretarioModel;
    protected $adminModel;
    protected $session;
    protected $request;
    protected $secretarioId;

    public function __construct()
    {
        $this->secretarioModel = new SecretarioModel();
        $this->adminModel = new AdminModel();
        $this->session = \Config\Services::session();
        $this->request = \Config\Services::request();

        helper(['url', 'form']);

        // Verifica se o usuário está logado
        if (!$this->session->get('logged_in')) {
            return redirect()->to('auth/login');
        }

        // Verifica se é secretário
        $tipoUsuario = $this->session->get('Tipo_Usuario');
        if ($tipoUsuario !== 'Secretario') {
            return redirect()->to('agenda');
        }

        // Busca o ID do secretário
        $this->secretarioId = $this->session->get('ID_Referencia');
        if (!$this->secretarioId) {
            $usuarioId = $this->session->get('ID_Usuario');
            if ($usuarioId) {
                $secretario = $this->secretarioModel->getSecretarioByUsuarioId($usuarioId);
                if ($secretario) {
                    $this->secretarioId = $secretario->ID_Secretario;
                    $this->session->set('ID_Referencia', $this->secretarioId);
                }
            }
        }

        if (!$this->secretarioId) {
            $this->session->setFlashdata('error', 'Dados do secretário não encontrados. Faça login novamente.');
            return redirect()->to('auth/login');
        }
    }

    /**
     * Dashboard do secretário
     */
    public function index()
    {
        $data['secretario'] = $this->secretarioModel->getSecretarioById($this->secretarioId);
        $data['active_menu'] = 'dashboard';
        return view('secretario/dashboard', $data);
    }

    /**
     * Página de agendamentos
     */
    public function agendamentos()
    {
        $data['secretario'] = $this->secretarioModel->getSecretarioById($this->secretarioId);
        $data['active_menu'] = 'agendamentos';
        return view('secretario/agendamentos', $data);
    }

    /**
     * Página de pacientes
     */
    public function pacientes()
    {
        $data['secretario'] = $this->secretarioModel->getSecretarioById($this->secretarioId);
        $data['active_menu'] = 'pacientes';
        return view('secretario/pacientes', $data);
    }

    /**
     * Página de médicos
     */
    public function medicos()
    {
        $data['secretario'] = $this->secretarioModel->getSecretarioById($this->secretarioId);
        $data['active_menu'] = 'medicos';
        return view('secretario/medicos', $data);
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
            $data = $this->secretarioModel->getMetrics();
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar métricas: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar métricas']);
        }
    }

    /**
     * AJAX: Buscar agendamentos
     */
    public function getAppointments()
    {
        if (!$this->request->isAJAX()) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['error' => 'Método não permitido']);
        }

        $status = $this->request->getGet('status') ?? '';
        $search = $this->request->getGet('search') ?? '';
        $date = $this->request->getGet('date') ?? '';
        $page = (int) ($this->request->getGet('page') ?? 1);
        $limit = 10;
        $offset = ($page - 1) * $limit;

        try {
            $data = $this->secretarioModel->getAppointments($status, $search, $date, $limit, $offset);
            $total = $this->secretarioModel->countAppointments($status, $search, $date);

            return $this->response->setJSON([
                'data' => $data,
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => ceil($total / $limit)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar agendamentos: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar agendamentos']);
        }
    }

    /**
     * AJAX: Atualizar status do agendamento
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

        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        $motivo = $this->request->getPost('motivo');

        if (!$id || !$status) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['error' => 'Dados incompletos']);
        }

        try {
            $result = $this->secretarioModel->updateAppointmentStatus($id, $status, $motivo);

            if ($result) {
                return $this->response->setJSON(['success' => 'Status atualizado com sucesso!']);
            } else {
                return $this->response
                    ->setStatusCode(500)
                    ->setJSON(['error' => 'Erro ao atualizar status']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Erro ao atualizar status: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro interno do servidor']);
        }
    }

    /**
     * AJAX: Buscar pacientes
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
            $data = $this->secretarioModel->getPatients($search, $limit, $offset);
            $total = $this->secretarioModel->countPatients($search);

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
     * AJAX: Buscar médicos
     */
    public function getDoctors()
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
            $data = $this->secretarioModel->getDoctors($search, $limit, $offset);
            $total = $this->secretarioModel->countDoctors($search);

            return $this->response->setJSON([
                'data' => $data,
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => ceil($total / $limit)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar médicos: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['error' => 'Erro ao carregar médicos']);
        }
    }

    public function aprovarAgendamento()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Requisição inválida']);
        }

        $id = (int) $this->request->getPost('id');
        if (!$id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID inválido']);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('agendamentos');
        $builder->where('ID_Agendamento', $id);
        $appt = $builder->get()->getRow();

        if (!$appt) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Agendamento não encontrado.']);
        }
        if ($appt->Status !== 'Pendente') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Só é possível aprovar agendamentos com status Pendente. Atual: ' . $appt->Status
            ]);
        }

        $builder = $db->table('agendamentos');
        $builder->where('ID_Agendamento', $id);
        $ok = $builder->update(['Status' => 'Confirmado']);

        if ($ok) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Agendamento confirmado com sucesso.',
                'csrf_token' => csrf_hash()
            ]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao aprovar.']);
    }

    public function rejeitarAgendamento()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Requisição inválida']);
        }

        $id = (int) $this->request->getPost('id');
        $motivo = trim((string) $this->request->getPost('motivo'));

        if (!$id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID inválido']);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('agendamentos');
        $builder->where('ID_Agendamento', $id);
        $appt = $builder->get()->getRow();

        if (!$appt) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Agendamento não encontrado.']);
        }
        if ($appt->Status !== 'Pendente') {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Só é possível rejeitar agendamentos Pendentes. Atual: ' . $appt->Status
            ]);
        }

        $update = ['Status' => 'Cancelado'];
        if ($motivo !== '') {
            // Se tiver coluna para guardar o motivo da rejeição, use. Se não, ignora.
            // $update['motivo_cancelamento'] = $motivo;
        }

        $builder = $db->table('agendamentos');
        $builder->where('ID_Agendamento', $id);
        $ok = $builder->update($update);

        if ($ok) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Agendamento rejeitado. O horário foi libertado.',
                'csrf_token' => csrf_hash()
            ]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'Erro ao rejeitar.']);
    }
}
