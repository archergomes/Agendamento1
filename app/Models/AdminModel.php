<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /**
     * Busca métricas para o dashboard - APENAS UMA VEZ
     */
    public function getMetrics()
    {
        $db = \Config\Database::connect();

        $metrics = [];

        // Total de pacientes
        $builder = $db->table('pacientes');
        $metrics['total_patients'] = $builder->countAll();

        // Total de médicos
        $builder = $db->table('medicos');
        $metrics['total_doctors'] = $builder->countAll();

        // Total de secretários
        $builder = $db->table('secretarios');
        $metrics['total_secretaries'] = $builder->countAll();

        // Agendamentos de hoje
        $today = date('Y-m-d');
        $builder = $db->table('agendamentos');
        $builder->where('Data_Agendamento', $today);
        $builder->where('Status !=', 'Cancelado');
        $metrics['appointments_today'] = $builder->countAllResults();

        // Agendamentos futuros (data > hoje)
        $builder = $db->table('agendamentos');
        $builder->where('Data_Agendamento >', $today);
        $builder->where('Status !=', 'Cancelado');
        $metrics['upcoming_appointments'] = $builder->countAllResults();

        // Agendamentos cancelados
        $builder = $db->table('agendamentos');
        $builder->where('Status', 'Cancelado');
        $metrics['cancelled_appointments'] = $builder->countAllResults();

        return $metrics;
    }

    /**
     * Busca lista de pacientes
     */
    public function getPacientes()
    {
        $builder = $this->db->table('pacientes');
        $builder->orderBy('Nome', 'ASC');
        return $builder->get()->getResult();
    }

    /**
     * Busca lista de médicos
     */
    public function getMedicos()
    {
        $builder = $this->db->table('medicos');
        $builder->orderBy('Nome', 'ASC');
        return $builder->get()->getResult();
    }

    /**
     * Busca lista de secretários
     */
    public function getSecretarios()
    {
        $builder = $this->db->table('secretarios');
        $builder->orderBy('Nome', 'ASC');
        return $builder->get()->getResult();
    }

    /**
     * Busca lista de agendamentos
     */
    public function getAgendamentos()
    {
        $builder = $this->db->table('agendamentos a');
        $builder->select('
            a.*, 
            p.Nome as paciente_nome, 
            p.Sobrenome as paciente_sobrenome,
            m.Nome as medico_nome,
            m.Sobrenome as medico_sobrenome,
            e.Nome as especialidade
        ');
        $builder->join('pacientes p', 'p.ID_Paciente = a.ID_Paciente', 'left');
        $builder->join('medicos m', 'm.ID_Medico = a.ID_Medico', 'left');
        $builder->join('especialidades e', 'e.ID_Especialidade = m.ID_Especialidade', 'left');
        $builder->orderBy('a.Data_Agendamento', 'DESC');
        return $builder->get()->getResult();
    }

    /**
     * Cancela um agendamento
     */
    public function cancelAppointment($id)
    {
        $builder = $this->db->table('agendamentos');
        return $builder->update(['Status' => 'Cancelado'], ['ID_Agendamento' => $id]);
    }

    /**
     * Exclui um agendamento
     */
    public function deleteAppointment($id)
    {
        $builder = $this->db->table('agendamentos');
        return $builder->delete(['ID_Agendamento' => $id]);
    }

    /**
     * Busca atividades recentes
     */
    public function getRecentActivity($search = '')
    {
        $activities = [];

        // Buscar pacientes recentes
        $builder = $this->db->table('pacientes');
        if (!empty($search)) {
            $builder->groupStart()
                ->like('Nome', $search)
                ->orLike('Sobrenome', $search)
                ->orLike('BI', $search)
                ->orLike('Telefone', $search)
                ->groupEnd();
        }
        $builder->orderBy('Criado_Em', 'DESC');
        $builder->limit(5);
        $patients = $builder->get()->getResult();

        foreach ($patients as $p) {
            $activities[] = (object) [
                'type' => 'patient',
                'action_type' => 'patient',
                'details' => "Paciente: {$p->Nome} {$p->Sobrenome} - BI: {$p->BI}",
                'bi' => $p->BI,
                'date' => $p->Criado_Em
            ];
        }

        // Buscar médicos recentes
        $builder = $this->db->table('medicos');
        if (!empty($search)) {
            $builder->groupStart()
                ->like('Nome', $search)
                ->orLike('Sobrenome', $search)
                ->orLike('Especialidade', $search)
                ->orLike('Telefone', $search)
                ->groupEnd();
        }
        $builder->orderBy('Criado_Em', 'DESC');
        $builder->limit(5);
        $doctors = $builder->get()->getResult();

        foreach ($doctors as $d) {
            $activities[] = (object) [
                'type' => 'doctor',
                'action_type' => 'doctor',
                'details' => "Médico: Dr. {$d->Nome} {$d->Sobrenome} - Especialidade: {$d->Especialidade}",
                'bi' => $d->Numero_Licenca ?? 'N/A',
                'date' => $d->Criado_Em
            ];
        }

        // Buscar agendamentos recentes
        $builder = $this->db->table('agendamentos a');
        $builder->select('a.*, p.Nome as paciente_nome, p.Sobrenome as paciente_sobrenome');
        $builder->join('pacientes p', 'p.ID_Paciente = a.ID_Paciente', 'left');
        if (!empty($search)) {
            $builder->groupStart()
                ->like('p.Nome', $search)
                ->orLike('p.Sobrenome', $search)
                ->orLike('a.Status', $search)
                ->groupEnd();
        }
        $builder->orderBy('a.Criado_Em', 'DESC');
        $builder->limit(5);
        $appointments = $builder->get()->getResult();

        foreach ($appointments as $a) {
            $activities[] = (object) [
                'type' => 'appointment',
                'action_type' => 'appointment',
                'details' => "Agendamento: {$a->paciente_nome} {$a->paciente_sobrenome} - Status: {$a->Status}",
                'bi' => 'N/A',
                'date' => $a->Criado_Em
            ];
        }

        // Ordenar por data (mais recente primeiro)
        usort($activities, function ($a, $b) {
            return strtotime($b->date) - strtotime($a->date);
        });

        // Retornar os 10 mais recentes
        return array_slice($activities, 0, 10);
    }

    /**
     * Busca paciente por BI
     */
    public function getPatientByBi($bi)
    {
        $builder = $this->db->table('pacientes');
        $builder->where('BI', $bi);
        $result = $builder->get()->getRow();

        if (!$result) return null;

        return [
            'name' => $result->Nome . ' ' . $result->Sobrenome,
            'phone' => $result->Telefone,
            'email' => $result->email ?? '',
            'bi' => $result->BI
        ];
    }

    /**
     * Busca médico por BI (Número de Licença)
     */
    public function getDoctorByBi($bi)
    {
        $builder = $this->db->table('medicos');
        $builder->where('Numero_Licenca', $bi);
        $result = $builder->get()->getRow();

        if (!$result) return null;

        return [
            'name' => $result->Nome . ' ' . $result->Sobrenome,
            'specialty' => $result->Especialidade,
            'phone' => $result->Telefone,
            'email' => $result->Email,
            'bi' => $result->Numero_Licenca
        ];
    }

    /**
     * Atualiza paciente
     */
    public function updatePatient($bi, $name, $phone, $email = '')
    {
        // Separar nome e sobrenome
        $nameParts = explode(' ', $name, 2);
        $nome = $nameParts[0] ?? '';
        $sobrenome = $nameParts[1] ?? '';

        $data = [
            'Nome' => $nome,
            'Sobrenome' => $sobrenome,
            'Telefone' => $phone
        ];

        if (!empty($email)) {
            $data['email'] = $email;
        }

        $builder = $this->db->table('pacientes');
        return $builder->update($data, ['BI' => $bi]);
    }

    /**
     * Atualiza médico
     */
    public function updateDoctor($bi, $name, $specialty, $phone, $email = '')
    {
        // Separar nome e sobrenome
        $nameParts = explode(' ', $name, 2);
        $nome = $nameParts[0] ?? '';
        $sobrenome = $nameParts[1] ?? '';

        $data = [
            'Nome' => $nome,
            'Sobrenome' => $sobrenome,
            'Especialidade' => $specialty,
            'Telefone' => $phone,
            'Email' => $email
        ];

        $builder = $this->db->table('medicos');
        return $builder->update($data, ['Numero_Licenca' => $bi]);
    }

    // ============================================================
    // MÉTODOS PARA GRÁFICOS
    // ============================================================

    /**
     * Busca tendência de agendamentos dos últimos 14 dias
     */
    public function getAppointmentsTrend()
    {
        $db = \Config\Database::connect();

        $labels = [];
        $data = [];

        for ($i = 13; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $labels[] = date('d/m', strtotime($date));

            $builder = $db->table('agendamentos');
            $builder->where('Data_Agendamento', $date);
            $count = $builder->countAllResults();
            $data[] = $count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Busca distribuição de status dos agendamentos
     */
    public function getAppointmentsStatus()
    {
        $db = \Config\Database::connect();

        $statuses = ['pendente', 'confirmado', 'cancelado'];
        $result = [];

        foreach ($statuses as $status) {
            $builder = $db->table('agendamentos');
            $builder->where('LOWER(Status)', $status);
            $result[$status] = $builder->countAllResults();
        }

        return $result;
    }

    /**
     * Busca médicos agrupados por especialidade
     */
    public function getDoctorsBySpecialty()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('medicos m');
        $builder->select('e.Nome as specialty, COUNT(m.ID_Medico) as total');
        $builder->join('especialidades e', 'e.ID_Especialidade = m.ID_Especialidade', 'left');
        $builder->where('m.ID_Especialidade >', 0);
        $builder->groupBy('m.ID_Especialidade');
        $builder->orderBy('total', 'DESC');
        $builder->limit(10);

        $results = $builder->get()->getResult();

        $labels = [];
        $data = [];

        foreach ($results as $row) {
            $labels[] = $row->specialty ?: 'Outros';
            $data[] = (int) $row->total;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Busca novos pacientes por mês (últimos 6 meses)
     */
    public function getPatientsMonthly()
    {
        $db = \Config\Database::connect();

        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $labels[] = date('M/Y', strtotime($month . '-01'));

            $startDate = $month . '-01';
            $endDate = date('Y-m-t', strtotime($month . '-01'));

            $builder = $db->table('pacientes');
            $builder->where('Criado_Em >=', $startDate);
            $builder->where('Criado_Em <=', $endDate . ' 23:59:59');
            $count = $builder->countAllResults();
            $data[] = $count;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Busca pacientes com filtro
     */
    public function getPatients($query = '')
    {
        $builder = $this->db->table('pacientes');

        if (!empty($query)) {
            $builder->groupStart()
                ->like('Nome', $query)
                ->orLike('Sobrenome', $query)
                ->orLike('BI', $query)
                ->orLike('Telefone', $query)
                ->groupEnd();
        }

        $builder->orderBy('Nome', 'ASC');
        $results = $builder->get()->getResult();

        // Formatar para o frontend
        $formatted = [];
        foreach ($results as $p) {
            $formatted[] = [
                'bi' => $p->BI ?? '',
                'name' => ($p->Nome ?? '') . ' ' . ($p->Sobrenome ?? ''),
                'phone' => $p->Telefone ?? '',
                'email' => $p->email ?? '',
                'birthday' => isset($p->Data_Nascimento) ? date('d/m/Y', strtotime($p->Data_Nascimento)) : '',
                'gender' => $p->Genero ?? '',
                'address' => $p->Endereco ?? ''
            ];
        }

        return $formatted;
    }

    /**
     * Exclui paciente por BI
     */
    public function deletePatient($bi)
    {
        // Buscar paciente pelo BI
        $builder = $this->db->table('pacientes');
        $patient = $builder->where('BI', $bi)->get()->getRow();

        if (!$patient) {
            return false;
        }

        // Excluir paciente
        $builder = $this->db->table('pacientes');
        $result = $builder->delete(['BI' => $bi]);

        if ($result && $patient->ID_Usuario) {
            // Excluir usuário associado (se existir)
            $builder = $this->db->table('usuarios');
            $builder->delete(['ID_Usuario' => $patient->ID_Usuario]);
        }

        return $result;
    }

    /**
     * Busca detalhes completos de um paciente por BI
     */
    public function getPatientDetails($bi)
    {
        $builder = $this->db->table('pacientes');
        $builder->where('BI', $bi);
        $result = $builder->get()->getRow();

        if (!$result) return null;

        // Buscar email na tabela usuarios se disponível
        $email = '';
        if (!empty($result->ID_Usuario)) {
            $userBuilder = $this->db->table('usuarios');
            $userBuilder->where('ID_Usuario', $result->ID_Usuario);
            $user = $userBuilder->get()->getRow();
            if ($user) {
                $email = $user->Email ?? '';
            }
        }

        return [
            'bi' => $result->BI ?? '',
            'name' => trim(($result->Nome ?? '') . ' ' . ($result->Sobrenome ?? '')),
            'phone' => $result->Telefone ?? '',
            'email' => $email ?: ($result->email ?? ''),
            'birthday' => isset($result->Data_Nascimento) ? date('d/m/Y', strtotime($result->Data_Nascimento)) : '',
            'gender' => $result->Genero ?? '',
            'address' => $result->Endereco ?? '',
            'created_at' => isset($result->Criado_Em) ? date('d/m/Y H:i', strtotime($result->Criado_Em)) : ''
        ];
    }
}
