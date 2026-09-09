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
    /**
     * Atualiza paciente
     */
    public function updatePatient($bi, $name, $phone, $email = '', $endereco = '')
    {
        try {
            // Separar nome e sobrenome
            $nameParts = explode(' ', trim($name), 2);
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

            if (!empty($endereco)) {
                $data['Endereco'] = $endereco;
            }

            log_message('debug', 'updatePatient - Dados para atualização: ' . print_r($data, true));

            $builder = $this->db->table('pacientes');
            $builder->where('BI', $bi);
            $result = $builder->update($data);

            log_message('debug', 'updatePatient - Resultado: ' . ($result ? 'Sucesso' : 'Falha'));

            return $result;
        } catch (\Exception $e) {
            log_message('error', 'updatePatient - Erro: ' . $e->getMessage());
            return false;
        }
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

    /**
     * Busca lista de médicos com filtro
     */
    public function getDoctorsList($query = '')
    {
        $builder = $this->db->table('medicos');

        if (!empty($query)) {
            $builder->groupStart()
                ->like('Nome', $query)
                ->orLike('Sobrenome', $query)
                ->orLike('Especialidade', $query)
                ->orLike('Numero_Licenca', $query)
                ->orLike('Telefone', $query)
                ->groupEnd();
        }

        $builder->orderBy('Nome', 'ASC');
        $results = $builder->get()->getResult();

        $formatted = [];
        foreach ($results as $d) {
            $formatted[] = [
                'bi' => $d->Numero_Licenca ?? '',
                'name' => trim(($d->Nome ?? '') . ' ' . ($d->Sobrenome ?? '')),
                'phone' => $d->Telefone ?? '',
                'email' => $d->Email ?? '',
                'specialty' => $d->Especialidade ?? '',
                'licenseNumber' => $d->Numero_Licenca ?? ''
            ];
        }

        return $formatted;
    }

    /**
     * Busca detalhes completos de um médico por BI (Numero_Licenca)
     */
    public function getDoctorDetails($bi)
    {
        $builder = $this->db->table('medicos');
        $builder->where('Numero_Licenca', $bi);
        $result = $builder->get()->getRow();

        if (!$result) return null;

        return [
            'bi' => $result->Numero_Licenca ?? '',
            'name' => trim(($result->Nome ?? '') . ' ' . ($result->Sobrenome ?? '')),
            'phone' => $result->Telefone ?? '',
            'email' => $result->Email ?? '',
            'specialty' => $result->Especialidade ?? '',
            'licenseNumber' => $result->Numero_Licenca ?? '',
            'id_usuario' => $result->ID_Usuario ?? null,
            'created_at' => isset($result->Criado_Em) ? date('d/m/Y H:i', strtotime($result->Criado_Em)) : ''
        ];
    }

    /**
     * Exclui médico por BI (Numero_Licenca) e seu usuário
     */
    public function deleteDoctor($bi)
    {
        try {
            // Buscar médico para obter ID_Usuario
            $builder = $this->db->table('medicos');
            $medico = $builder->where('Numero_Licenca', $bi)->get()->getRow();

            if (!$medico) {
                return false;
            }

            $idUsuario = $medico->ID_Usuario;
            $idMedico = $medico->ID_Medico;

            // Excluir médico
            $builder = $this->db->table('medicos');
            $result = $builder->delete(['Numero_Licenca' => $bi]);

            // Excluir usuário associado
            if ($result && $idUsuario) {
                $builder = $this->db->table('usuarios');
                $builder->delete(['ID_Usuario' => $idUsuario]);
            }

            return $result;
        } catch (\Exception $e) {
            log_message('error', 'deleteDoctor - Erro: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Atualiza médico com dados completos (incluindo usuário)
     */
    public function updateDoctorFull($bi, $nome, $telefone, $email, $especialidade, $licenca)
    {
        try {
            // Iniciar transação
            $this->db->transBegin();

            // Separar nome e sobrenome
            $nameParts = explode(' ', trim($nome), 2);
            $nomePart = $nameParts[0] ?? '';
            $sobrenomePart = $nameParts[1] ?? '';

            // Buscar médico atual
            $builder = $this->db->table('medicos');
            $medicoAtual = $builder->where('Numero_Licenca', $bi)->get()->getRow();

            if (!$medicoAtual) {
                $this->db->transRollback();
                return false;
            }

            // Dados do médico
            $medicoData = [
                'Nome' => $nomePart,
                'Sobrenome' => $sobrenomePart,
                'Telefone' => $telefone,
                'Email' => $email,
                'Especialidade' => $especialidade
            ];

            if (!empty($licenca) && $licenca !== $bi) {
                $medicoData['Numero_Licenca'] = $licenca;
            }

            // Atualizar médico
            $builder = $this->db->table('medicos');
            $medicoResult = $builder->update($medicoData, ['Numero_Licenca' => $bi]);

            // Atualizar usuário (se tiver ID_Usuario)
            if ($medicoAtual->ID_Usuario) {
                $usuarioBuilder = $this->db->table('usuarios');
                $usuarioBuilder->update(
                    ['Email' => $email],
                    ['ID_Usuario' => $medicoAtual->ID_Usuario]
                );
            }

            $this->db->transCommit();

            log_message('debug', 'updateDoctorFull - Médico e usuário atualizados');
            return true;
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'updateDoctorFull - Erro: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca lista de secretários com filtro
     */
    public function getSecretariesList($query = '')
    {
        $builder = $this->db->table('secretarios');

        if (!empty($query)) {
            $builder->groupStart()
                ->like('Nome', $query)
                ->orLike('Sobrenome', $query)
                ->orLike('Email', $query)
                ->orLike('Telefone', $query)
                ->groupEnd();
        }

        $builder->orderBy('Nome', 'ASC');
        $results = $builder->get()->getResult();

        $formatted = [];
        foreach ($results as $s) {
            $formatted[] = [
                'id' => $s->ID_Secretario ?? '',
                'name' => trim(($s->Nome ?? '') . ' ' . ($s->Sobrenome ?? '')),
                'phone' => $s->Telefone ?? '',
                'email' => $s->Email ?? '',
                'cargo' => $s->Cargo ?? 'Secretário'
            ];
        }

        return $formatted;
    }

    /**
     * Busca detalhes completos de um secretário por ID
     */
    public function getSecretaryDetails($id)
    {
        $builder = $this->db->table('secretarios');
        $builder->where('ID_Secretario', $id);
        $result = $builder->get()->getRow();

        if (!$result) return null;

        return [
            'id' => $result->ID_Secretario ?? '',
            'name' => trim(($result->Nome ?? '') . ' ' . ($result->Sobrenome ?? '')),
            'phone' => $result->Telefone ?? '',
            'email' => $result->Email ?? '',
            'cargo' => $result->Cargo ?? 'Secretário',
            'created_at' => isset($result->Criado_Em) ? date('d/m/Y H:i', strtotime($result->Criado_Em)) : ''
        ];
    }

    /**
     * Exclui secretário por ID
     */
    public function deleteSecretary($id)
    {
        $builder = $this->db->table('secretarios');
        return $builder->delete(['ID_Secretario' => $id]);
    }

    /**
     * Atualiza secretário
     */
    public function updateSecretary($id, $nome, $telefone, $email, $cargo = '')
    {
        try {
            // Verificar se o ID existe
            $builder = $this->db->table('secretarios');
            $builder->where('ID_Secretario', $id);
            $existing = $builder->get()->getRow();

            if (!$existing) {
                log_message('error', 'updateSecretary - Secretário não encontrado ID: ' . $id);
                return false;
            }

            // Separar nome e sobrenome
            $nameParts = explode(' ', trim($nome), 2);
            $nomePart = $nameParts[0] ?? '';
            $sobrenomePart = $nameParts[1] ?? '';

            $data = [
                'Nome' => $nomePart,
                'Sobrenome' => $sobrenomePart,
                'Telefone' => $telefone,
                'Email' => $email,
                'Cargo' => !empty($cargo) ? $cargo : 'Secretário'
            ];

            log_message('debug', 'updateSecretary - Dados para atualização: ' . print_r($data, true));

            // Atualizar secretário
            $builder = $this->db->table('secretarios');
            $builder->where('ID_Secretario', $id);
            $result = $builder->update($data);

            log_message('debug', 'updateSecretary - Resultado da atualização: ' . ($result ? 'true' : 'false'));

            // Se a atualização foi bem sucedida, atualizar o email na tabela usuarios
            if ($result) {
                $builder = $this->db->table('usuarios');
                $builder->where('ID_Referencia', $id);
                $builder->where('Tipo_Usuario', 'Secretario');
                $userResult = $builder->update(['Email' => $email]);

                log_message('debug', 'updateSecretary - Atualização do usuário: ' . ($userResult ? 'true' : 'false'));
            }

            return $result;
        } catch (\Exception $e) {
            log_message('error', 'updateSecretary - Erro: ' . $e->getMessage());
            log_message('error', 'updateSecretary - Trace: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Busca lista de agendamentos com filtro
     */
    public function getAppointmentsList($query = '')
    {
        try {
            $builder = $this->db->table('agendamentos a');
            $builder->select('
            a.ID_Agendamento as id,
            a.Data_Agendamento as data,
            a.Hora_Agendamento as hora,
            a.Status as status,
            a.Motivo as motivo,
            a.Criado_Em as criado_em,
            a.tipo_agendamento,
            a.paciente_nome_agendado,
            a.paciente_relacao,
            CONCAT(p.Nome, " ", p.Sobrenome) as paciente_nome,
            CONCAT(m.Nome, " ", m.Sobrenome) as medico_nome,
            e.Nome as especialidade
        ');
            $builder->join('pacientes p', 'p.ID_Paciente = a.ID_Paciente', 'left');
            $builder->join('medicos m', 'm.ID_Medico = a.ID_Medico', 'left');
            $builder->join('especialidades e', 'e.ID_Especialidade = m.ID_Especialidade', 'left');

            if (!empty($query)) {
                $builder->groupStart()
                    ->like('p.Nome', $query)
                    ->orLike('p.Sobrenome', $query)
                    ->orLike('m.Nome', $query)
                    ->orLike('m.Sobrenome', $query)
                    ->orLike('a.paciente_nome_agendado', $query)
                    ->groupEnd();
            }

            $builder->orderBy('a.Data_Agendamento', 'DESC');
            $builder->orderBy('a.Hora_Agendamento', 'DESC');

            $results = $builder->get()->getResult();

            // Log para depuração
            log_message('debug', 'getAppointmentsList - Query executada, resultados: ' . count($results));

            $formatted = [];
            foreach ($results as $a) {
                // Determinar o nome do paciente a ser exibido
                $pacienteExibido = $a->paciente_nome ?? 'N/A';

                // Verificar se é para outra pessoa
                $isForOther = isset($a->tipo_agendamento) && $a->tipo_agendamento === 'other';

                if ($isForOther && !empty($a->paciente_nome_agendado)) {
                    $pacienteExibido = $a->paciente_nome_agendado . ' (dependente)';
                }

                $formatted[] = [
                    'id' => $a->id,
                    'paciente' => $pacienteExibido,
                    'medico' => $a->medico_nome ?? 'N/A',
                    'especialidade' => $a->especialidade ?? 'N/A',
                    'data' => isset($a->data) ? date('d/m/Y', strtotime($a->data)) : '-',
                    'hora' => isset($a->hora) ? substr($a->hora, 0, 5) : '-',
                    'status' => $a->status ?? 'Pendente',
                    'motivo' => $a->motivo ?? '',
                    'criado_em' => isset($a->criado_em) ? date('d/m/Y H:i', strtotime($a->criado_em)) : '',
                    'tipo_agendamento' => $a->tipo_agendamento ?? 'self',
                    'paciente_nome_agendado' => $a->paciente_nome_agendado ?? '',
                    'paciente_relacao' => $a->paciente_relacao ?? ''
                ];
            }

            return $formatted;
        } catch (\Exception $e) {
            log_message('error', 'Erro em getAppointmentsList: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $e->getTraceAsString());
            return [];
        }
    }

    /**
     * Busca detalhes de um agendamento por ID
     */
    public function getAppointmentDetails($id)
    {
        try {
            $builder = $this->db->table('agendamentos a');
            $builder->select('
            a.*,
            CONCAT(p.Nome, " ", p.Sobrenome) as paciente,
            CONCAT(m.Nome, " ", m.Sobrenome) as medico,
            e.Nome as especialidade,
            p.Data_Nascimento as paciente_data_nasc,
            p.Telefone as paciente_telefone,
            p.BI as paciente_bi,
            p.Endereco as paciente_endereco,
            p.Genero as paciente_genero
        ');
            $builder->join('pacientes p', 'p.ID_Paciente = a.ID_Paciente', 'left');
            $builder->join('medicos m', 'm.ID_Medico = a.ID_Medico', 'left');
            $builder->join('especialidades e', 'e.ID_Especialidade = m.ID_Especialidade', 'left');
            $builder->where('a.ID_Agendamento', $id);
            $result = $builder->get()->getRow();

            if (!$result) return null;

            // Verificar se os campos existem no resultado
            $tipoAgendamento = property_exists($result, 'tipo_agendamento') ? $result->tipo_agendamento : 'self';
            $pacienteNomeAgendado = property_exists($result, 'paciente_nome_agendado') ? $result->paciente_nome_agendado : '';
            $pacienteRelacao = property_exists($result, 'paciente_relacao') ? $result->paciente_relacao : '';
            $pacienteDataNascAgendado = property_exists($result, 'paciente_data_nasc_agendado') ? $result->paciente_data_nasc_agendado : null;
            $pacienteDocTipo = property_exists($result, 'paciente_doc_tipo') ? $result->paciente_doc_tipo : '';
            $pacienteDocNum = property_exists($result, 'paciente_doc_num') ? $result->paciente_doc_num : '';
            $responsavelNome = property_exists($result, 'responsavel_nome') ? $result->responsavel_nome : '';
            $responsavelTelefone = property_exists($result, 'responsavel_telefone') ? $result->responsavel_telefone : '';
            $responsavelBi = property_exists($result, 'responsavel_bi') ? $result->responsavel_bi : '';

            return [
                'id' => $result->ID_Agendamento,
                'paciente' => $result->paciente ?? 'N/A',
                'medico' => $result->medico ?? 'N/A',
                'especialidade' => $result->especialidade ?? 'N/A',
                'data' => $result->Data_Agendamento,
                'data_formatada' => isset($result->Data_Agendamento) ? date('d/m/Y', strtotime($result->Data_Agendamento)) : '',
                'hora' => isset($result->Hora_Agendamento) ? substr($result->Hora_Agendamento, 0, 5) : '',
                'status' => $result->Status ?? 'Pendente',
                'motivo' => $result->Motivo ?? '',
                'criado_em' => isset($result->Criado_Em) ? date('d/m/Y H:i', strtotime($result->Criado_Em)) : '',
                // Campos do paciente
                'paciente_data_nasc' => isset($result->paciente_data_nasc) ? date('d/m/Y', strtotime($result->paciente_data_nasc)) : '',
                'paciente_telefone' => $result->paciente_telefone ?? '',
                'paciente_bi' => $result->paciente_bi ?? '',
                'paciente_endereco' => $result->paciente_endereco ?? '',
                'paciente_genero' => $result->paciente_genero ?? '',
                // Campos para "outra pessoa"
                'tipo_agendamento' => $tipoAgendamento,
                'paciente_nome_agendado' => $pacienteNomeAgendado,
                'paciente_relacao' => $pacienteRelacao,
                'paciente_data_nasc_agendado' => $pacienteDataNascAgendado ? date('d/m/Y', strtotime($pacienteDataNascAgendado)) : '',
                'paciente_doc_tipo' => $pacienteDocTipo,
                'paciente_doc_num' => $pacienteDocNum,
                'responsavel_nome' => $responsavelNome ?: $result->paciente ?? '',
                'responsavel_telefone' => $responsavelTelefone ?: $result->paciente_telefone ?? '',
                'responsavel_bi' => $responsavelBi ?: $result->paciente_bi ?? ''
            ];
        } catch (\Exception $e) {
            log_message('error', 'Erro em getAppointmentDetails: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Atualiza agendamento
     */
    public function updateAppointment($id, $data, $hora, $status, $motivo)
    {
        $updateData = [
            'Data_Agendamento' => $data,
            'Hora_Agendamento' => $hora,
            'Status' => $status,
            'Motivo' => !empty($motivo) ? $motivo : null
        ];

        $builder = $this->db->table('agendamentos');
        return $builder->update($updateData, ['ID_Agendamento' => $id]);
    }

    /**
     * Cria um novo paciente
     */
    public function createPatient($bi, $nome, $telefone, $email = '', $dataNascimento = '', $genero = '', $endereco = '')
    {
        try {
            // Verificar se BI já existe
            $builder = $this->db->table('pacientes');
            $existing = $builder->where('BI', $bi)->get()->getRow();

            if ($existing) {
                return false;
            }

            // Separar nome e sobrenome
            $nameParts = explode(' ', trim($nome), 2);
            $nomePart = $nameParts[0] ?? '';
            $sobrenomePart = $nameParts[1] ?? '';

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

            $builder = $this->db->table('pacientes');
            return $builder->insert($data);
        } catch (\Exception $e) {
            log_message('error', 'Erro em createPatient: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica se um paciente existe pelo BI
     */
    public function checkPatientExists($bi)
    {
        $builder = $this->db->table('pacientes');
        $builder->where('BI', $bi);
        $result = $builder->get()->getRow();
        return $result !== null;
    }

    /**
     * Insere um novo paciente
     */
    public function insertPatient($data)
    {
        try {
            $builder = $this->db->table('pacientes');
            $result = $builder->insert($data);
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Erro ao inserir paciente: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cria um novo secretário
     */
    public function createSecretary($bi, $nome, $telefone, $email, $senha)
    {
        try {
            // Separar nome e sobrenome
            $nameParts = explode(' ', trim($nome), 2);
            $nomePart = $nameParts[0] ?? '';
            $sobrenomePart = $nameParts[1] ?? '';

            // Verificar se BI já existe
            $builder = $this->db->table('secretarios');
            $existing = $builder->where('ID_Secretario', $bi)->get()->getRow();

            if ($existing) {
                return false;
            }

            // Inserir secretário
            $data = [
                'ID_Secretario' => $bi,
                'Nome' => $nomePart,
                'Sobrenome' => $sobrenomePart,
                'Telefone' => $telefone,
                'Email' => $email
            ];

            $builder = $this->db->table('secretarios');
            $result = $builder->insert($data);

            if (!$result) {
                return false;
            }

            // Criar usuário para o secretário
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $usuarioData = [
                'Email' => $email,
                'Senha' => $senhaHash,
                'Tipo_Usuario' => 'Secretario',
                'ID_Referencia' => $bi
            ];

            $builder = $this->db->table('usuarios');
            return $builder->insert($usuarioData);
        } catch (\Exception $e) {
            log_message('error', 'Erro em createSecretary: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cria um novo médico com usuário
     */
    public function createDoctor($bi, $nome, $telefone, $email, $especialidade, $licenca)
    {
        try {
            log_message('debug', 'createDoctor - INICIANDO');

            // Iniciar transação
            $this->db->transBegin();

            // Separar nome e sobrenome
            $nameParts = explode(' ', trim($nome), 2);
            $nomePart = $nameParts[0] ?? '';
            $sobrenomePart = $nameParts[1] ?? '';

            // Verificar se já existe médico com esta licença
            $builder = $this->db->table('medicos');
            $builder->where('Numero_Licenca', $licenca);
            $existingLicenca = $builder->get()->getRow();

            if ($existingLicenca) {
                $this->lastError = 'Já existe um médico com este número de licença.';
                $this->db->transRollback();
                log_message('error', 'createDoctor - Licença já existe: ' . $licenca);
                return false;
            }

            // Verificar se email já existe na tabela medicos
            $builder = $this->db->table('medicos');
            $builder->where('Email', $email);
            $existingEmail = $builder->get()->getRow();

            if ($existingEmail) {
                $this->lastError = 'Este email já está cadastrado como médico.';
                $this->db->transRollback();
                log_message('error', 'createDoctor - Email já existe na tabela medicos: ' . $email);
                return false;
            }

            // Verificar se email já existe na tabela usuarios
            $builder = $this->db->table('usuarios');
            $builder->where('Email', $email);
            $existingUser = $builder->get()->getRow();

            if ($existingUser) {
                $this->lastError = 'Este email já está cadastrado no sistema.';
                $this->db->transRollback();
                log_message('error', 'createDoctor - Email já existe na tabela usuarios: ' . $email);
                return false;
            }

            // Buscar ID da especialidade
            $espBuilder = $this->db->table('especialidades');
            $espBuilder->where('Nome', $especialidade);
            $especialidadeRow = $espBuilder->get()->getRow();
            $idEspecialidade = $especialidadeRow ? $especialidadeRow->ID_Especialidade : 1;
            log_message('debug', 'createDoctor - ID_Especialidade: ' . $idEspecialidade);

            // 1. CRIAR USUÁRIO PRIMEIRO
            $senhaPadrao = '123456';
            $senhaHash = password_hash($senhaPadrao, PASSWORD_DEFAULT);

            // CORREÇÃO: Inserir primeiro para pegar o ID_Usuario
            $usuarioData = [
                'Email' => $email,
                'Senha' => $senhaHash,
                'Tipo_Usuario' => 'Medico',
                'ID_Referencia' => 0, // Valor temporário, será atualizado depois
                'Criado_Em' => date('Y-m-d H:i:s')
            ];

            log_message('debug', 'createDoctor - Dados do usuário: ' . print_r($usuarioData, true));

            $usuarioBuilder = $this->db->table('usuarios');
            $usuarioResult = $usuarioBuilder->insert($usuarioData);

            if (!$usuarioResult) {
                $this->lastError = 'Falha ao criar usuário no sistema.';
                $this->db->transRollback();
                log_message('error', 'createDoctor - Falha ao criar usuário');
                log_message('error', 'createDoctor - Último erro DB: ' . print_r($this->db->error(), true));
                return false;
            }

            $idUsuario = $this->db->insertID();
            log_message('debug', 'createDoctor - ID_Usuario criado: ' . $idUsuario);

            // 2. CRIAR MÉDICO COM O ID_USUARIO
            $medicoData = [
                'Nome' => $nomePart,
                'Sobrenome' => $sobrenomePart,
                'Telefone' => $telefone,
                'Email' => $email,
                'Especialidade' => $especialidade,
                'ID_Especialidade' => $idEspecialidade,
                'Numero_Licenca' => $licenca,
                'ID_Usuario' => $idUsuario,
                'Criado_Em' => date('Y-m-d H:i:s')
            ];

            log_message('debug', 'createDoctor - Dados do médico: ' . print_r($medicoData, true));

            $medicoBuilder = $this->db->table('medicos');
            $medicoResult = $medicoBuilder->insert($medicoData);

            if (!$medicoResult) {
                $this->lastError = 'Falha ao criar médico no sistema.';
                $this->db->transRollback();
                log_message('error', 'createDoctor - Falha ao criar médico');
                log_message('error', 'createDoctor - Último erro DB: ' . print_r($this->db->error(), true));

                // Remover usuário criado
                $this->db->table('usuarios')->delete(['ID_Usuario' => $idUsuario]);
                return false;
            }

            // 3. ATUALIZAR O ID_Referencia do usuário com o ID_Medico criado
            $idMedico = $this->db->insertID();
            $usuarioBuilder = $this->db->table('usuarios');
            $usuarioBuilder->update(
                ['ID_Referencia' => $idMedico],
                ['ID_Usuario' => $idUsuario]
            );

            // Commit da transação
            $this->db->transCommit();

            log_message('debug', 'createDoctor - MÉDICO E USUÁRIO CRIADOS COM SUCESSO! ID_Medico: ' . $idMedico . ', ID_Usuario: ' . $idUsuario);
            return true;
        } catch (\Exception $e) {
            $this->db->transRollback();
            $this->lastError = 'Erro interno: ' . $e->getMessage();
            log_message('error', 'createDoctor - EXCEÇÃO: ' . $e->getMessage());
            log_message('error', 'createDoctor - TRACE: ' . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Busca dados para relatórios
     */
    public function getReportData($type, $dateFrom, $dateTo, $doctorId, $status)
    {
        $db = \Config\Database::connect();
        $data = [];

        if ($type === 'overview') {
            // Total appointments
            $builder = $db->table('agendamentos');
            if ($dateFrom) $builder->where('Data_Agendamento >=', $dateFrom);
            if ($dateTo) $builder->where('Data_Agendamento <=', $dateTo);
            $data['total_appointments'] = $builder->countAllResults();

            // Total patients
            $builder = $db->table('pacientes');
            $data['total_patients'] = $builder->countAll();

            // Total doctors
            $builder = $db->table('medicos');
            $data['total_doctors'] = $builder->countAll();

            // Occupancy rate
            $totalSlots = 0;
            $usedSlots = 0;
            $builder = $db->table('horarios');
            $totalSlots = $builder->countAll();
            $builder = $db->table('agendamentos');
            if ($dateFrom) $builder->where('Data_Agendamento >=', $dateFrom);
            if ($dateTo) $builder->where('Data_Agendamento <=', $dateTo);
            $usedSlots = $builder->countAll();
            $data['occupancy_rate'] = $totalSlots > 0 ? round(($usedSlots / $totalSlots) * 100) : 0;

            // Appointments by doctor
            $builder = $db->table('agendamentos a');
            $builder->select('CONCAT(m.Nome, " ", m.Sobrenome) as medico, COUNT(a.ID_Agendamento) as total');
            $builder->join('medicos m', 'm.ID_Medico = a.ID_Medico', 'left');
            if ($dateFrom) $builder->where('a.Data_Agendamento >=', $dateFrom);
            if ($dateTo) $builder->where('a.Data_Agendamento <=', $dateTo);
            $builder->groupBy('a.ID_Medico');
            $builder->orderBy('total', 'DESC');
            $builder->limit(10);
            $results = $builder->get()->getResult();

            $data['doctor_labels'] = array_column($results, 'medico');
            $data['doctor_data'] = array_column($results, 'total');

            // Appointments by status
            $builder = $db->table('agendamentos');
            if ($dateFrom) $builder->where('Data_Agendamento >=', $dateFrom);
            if ($dateTo) $builder->where('Data_Agendamento <=', $dateTo);
            $statuses = ['Pendente', 'Confirmado', 'Cancelado', 'Concluido'];
            $data['status_labels'] = $statuses;
            $data['status_values'] = [];
            foreach ($statuses as $s) {
                $builder = $db->table('agendamentos');
                if ($dateFrom) $builder->where('Data_Agendamento >=', $dateFrom);
                if ($dateTo) $builder->where('Data_Agendamento <=', $dateTo);
                $builder->where('Status', $s);
                $data['status_values'][] = $builder->countAllResults();
            }
        } elseif ($type === 'appointments') {
            $builder = $db->table('agendamentos a');
            $builder->select('
            CONCAT(p.Nome, " ", p.Sobrenome) as paciente,
            CONCAT(m.Nome, " ", m.Sobrenome) as medico,
            DATE_FORMAT(a.Data_Agendamento, "%d/%m/%Y") as data,
            SUBSTRING(a.Hora_Agendamento, 1, 5) as hora,
            a.Status as status
        ');
            $builder->join('pacientes p', 'p.ID_Paciente = a.ID_Paciente', 'left');
            $builder->join('medicos m', 'm.ID_Medico = a.ID_Medico', 'left');
            if ($dateFrom) $builder->where('a.Data_Agendamento >=', $dateFrom);
            if ($dateTo) $builder->where('a.Data_Agendamento <=', $dateTo);
            if ($doctorId) $builder->where('a.ID_Medico', $doctorId);
            if ($status) $builder->where('a.Status', $status);
            $builder->orderBy('a.Data_Agendamento', 'DESC');
            $results = $builder->get()->getResult();
            $data = $results;
        } elseif ($type === 'doctors') {
            $builder = $db->table('medicos m');
            $builder->select('
            CONCAT(m.Nome, " ", m.Sobrenome) as name,
            m.Especialidade as specialty,
            COUNT(a.ID_Agendamento) as appointments,
            CONCAT(ROUND((COUNT(a.ID_Agendamento) / (SELECT COUNT(*) FROM horarios WHERE ID_Medico = m.ID_Medico)) * 100), "%") as occupancy
        ');
            $builder->join('agendamentos a', 'a.ID_Medico = m.ID_Medico AND a.Status != "Cancelado" AND a.Data_Agendamento BETWEEN "' . $dateFrom . '" AND "' . $dateTo . '"', 'left');
            $builder->groupBy('m.ID_Medico');
            $builder->orderBy('appointments', 'DESC');
            $results = $builder->get()->getResult();
            $data = $results;
        } elseif ($type === 'patients') {
            $builder = $db->table('pacientes p');
            $builder->select('
            CONCAT(p.Nome, " ", p.Sobrenome) as name,
            COUNT(a.ID_Agendamento) as visits,
            MAX(a.Data_Agendamento) as last_visit,
            CASE 
                WHEN COUNT(a.ID_Agendamento) = 0 THEN "Sem consultas"
                WHEN COUNT(a.ID_Agendamento) <= 2 THEN "Baixa frequência"
                WHEN COUNT(a.ID_Agendamento) <= 5 THEN "Média frequência"
                ELSE "Alta frequência"
            END as avg_status
        ');
            $builder->join('agendamentos a', 'a.ID_Paciente = p.ID_Paciente AND a.Status != "Cancelado" AND a.Data_Agendamento BETWEEN "' . $dateFrom . '" AND "' . $dateTo . '"', 'left');
            $builder->groupBy('p.ID_Paciente');
            $builder->orderBy('visits', 'DESC');
            $builder->limit(20);
            $results = $builder->get()->getResult();
            $data = $results;
        }

        return $data;
    }

    /**
     * Salva configurações
     */
    public function saveConfig($data)
    {
        try {
            $builder = $this->db->table('configuracoes');

            foreach ($data as $key => $value) {
                // Verificar se a configuração já existe
                $existing = $builder->where('Chave', $key)->get()->getRow();

                if ($existing) {
                    // Atualizar
                    $builder->where('Chave', $key)->update(['Valor' => $value]);
                } else {
                    // Inserir
                    $builder->insert(['Chave' => $key, 'Valor' => $value]);
                }
            }

            return true;
        } catch (\Exception $e) {
            log_message('error', 'Erro ao salvar configurações: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca horários com dados dos médicos
     */
    public function getHorarios()
    {
        try {
            $builder = $this->db->table('horarios h');
            $builder->select('
            h.*,
            m.Nome as medico_nome,
            m.Sobrenome as medico_sobrenome,
            m.Especialidade,
            e.Nome as especialidade
        ');
            $builder->join('medicos m', 'm.ID_Medico = h.ID_Medico', 'left');
            $builder->join('especialidades e', 'e.ID_Especialidade = m.ID_Especialidade', 'left');
            $builder->orderBy('h.Dia_Semana', 'ASC');
            $builder->orderBy('h.Hora_Inicio', 'ASC');

            $results = $builder->get()->getResult();

            // Log para debug
            log_message('debug', 'getHorarios - Encontrados: ' . count($results));

            return $results;
        } catch (\Exception $e) {
            log_message('error', 'getHorarios - Erro: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Cria um novo horário - CORRIGIDO
     */
    public function createSchedule($data)
    {
        try {
            $builder = $this->db->table('horarios');
            $result = $builder->insert($data);

            if ($result) {
                log_message('debug', 'createSchedule - Horário criado com sucesso');
                return $this->db->insertID();
            }

            log_message('error', 'createSchedule - Falha ao criar horário');
            return false;
        } catch (\Exception $e) {
            log_message('error', 'createSchedule - Erro: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Atualiza um horário
     */
    public function updateSchedule($id, $data)
    {
        try {
            $builder = $this->db->table('horarios');
            $builder->where('ID_Horario', $id);
            $result = $builder->update($data);

            if ($result) {
                log_message('debug', 'updateSchedule - Horário ID ' . $id . ' atualizado');
                return true;
            }

            log_message('error', 'updateSchedule - Falha ao atualizar horário ID ' . $id);
            return false;
        } catch (\Exception $e) {
            log_message('error', 'updateSchedule - Erro: ' . $e->getMessage());
            return false;
        }
    }

    // /**
    //  * Atualiza um horário
    //  */
    // public function updateSchedule($id, $doctorId, $day, $start, $end, $status)
    // {
    //     $data = [
    //         'ID_Medico' => $doctorId,
    //         'Dia_Semana' => $day,
    //         'Hora_Inicio' => $start,
    //         'Hora_Fim' => $end,
    //         'Status' => $status
    //     ];

    //     $builder = $this->db->table('horarios');
    //     return $builder->update($data, ['ID_Horario' => $id]);
    // }

    /**
     * Exclui um horário
     */
    public function deleteSchedule($id)
    {
        $builder = $this->db->table('horarios');
        return $builder->delete(['ID_Horario' => $id]);
    }

    /**
     * Cria um novo agendamento
     */
    public function createAppointment($pacienteId, $medicoId, $data, $hora, $status = 'Pendente', $motivo = null)
    {
        $insertData = [
            'ID_Paciente' => $pacienteId,
            'ID_Medico' => $medicoId,
            'Data_Agendamento' => $data,
            'Hora_Agendamento' => $hora,
            'Status' => $status,
            'Motivo' => $motivo
        ];

        $builder = $this->db->table('agendamentos');
        return $builder->insert($insertData);
    }
}
