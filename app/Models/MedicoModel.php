<?php

namespace App\Models;

use CodeIgniter\Model;

class MedicoModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /**
     * Busca dados do médico por ID
     */
    public function getMedicoById($id)
    {
        $builder = $this->db->table('medicos');
        $builder->where('ID_Medico', $id);
        return $builder->get()->getRow();
    }

    /**
     * Busca médico por ID_Usuario
     */
    public function getMedicoByUsuarioId($usuarioId)
    {
        $builder = $this->db->table('medicos');
        $builder->where('ID_Usuario', $usuarioId);
        return $builder->get()->getRow();
    }

    /**
     * Métricas do dashboard
     */
    public function getMetrics($medicoId)
    {
        $today = date('Y-m-d');
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $weekEnd = date('Y-m-d', strtotime('sunday this week'));

        // Consultas hoje
        $builder = $this->db->table('agendamentos');
        $builder->where('ID_Medico', $medicoId);
        $builder->where('Data_Agendamento', $today);
        $builder->where('Status !=', 'Cancelado');
        $metrics['today_count'] = $builder->countAllResults();

        // Consultas esta semana
        $builder = $this->db->table('agendamentos');
        $builder->where('ID_Medico', $medicoId);
        $builder->where('Data_Agendamento >=', $weekStart);
        $builder->where('Data_Agendamento <=', $weekEnd);
        $builder->where('Status !=', 'Cancelado');
        $metrics['week_count'] = $builder->countAllResults();

        // Pacientes ativos
        $builder = $this->db->table('agendamentos a');
        $builder->select('COUNT(DISTINCT a.ID_Paciente) as total');
        $builder->where('a.ID_Medico', $medicoId);
        $result = $builder->get()->getRow();
        $metrics['active_patients'] = $result->total ?? 0;

        // Taxa de comparecimento
        $builder = $this->db->table('agendamentos');
        $builder->where('ID_Medico', $medicoId);
        $total = $builder->countAllResults();

        $builder = $this->db->table('agendamentos');
        $builder->where('ID_Medico', $medicoId);
        $builder->where('Status', 'Concluido');
        $concluidos = $builder->countAllResults();

        $metrics['attendance_rate'] = $total > 0 ? round(($concluidos / $total) * 100) : 0;

        return $metrics;
    }

    /**
     * Próxima consulta
     */
    public function getNextAppointment($medicoId)
    {
        $today = date('Y-m-d');

        $builder = $this->db->table('agendamentos a');
        $builder->select('
            a.*, 
            p.Nome as paciente_nome, 
            p.Sobrenome as paciente_sobrenome, 
            p.BI as paciente_bi,
            p.Telefone as paciente_telefone
        ');
        $builder->join('pacientes p', 'p.ID_Paciente = a.ID_Paciente', 'left');
        $builder->where('a.ID_Medico', $medicoId);
        $builder->where('a.Data_Agendamento >=', $today);
        $builder->where('a.Status !=', 'Cancelado');
        $builder->where('a.Status !=', 'Concluido');
        $builder->orderBy('a.Data_Agendamento', 'ASC');
        $builder->orderBy('a.Hora_Agendamento', 'ASC');
        $builder->limit(1);

        $result = $builder->get()->getRow();

        if (!$result) return null;

        return [
            'appointment_id' => $result->ID_Agendamento,
            'time' => substr($result->Hora_Agendamento, 0, 5),
            'patient_name' => trim(($result->paciente_nome ?? '') . ' ' . ($result->paciente_sobrenome ?? '')),
            'patient_bi' => $result->paciente_bi ?? '',
            'patient_phone' => $result->paciente_telefone ?? '',
            'datetime' => $result->Data_Agendamento . ' ' . $result->Hora_Agendamento,
            'room' => 'Sala ' . ($result->Sala ?? '01')
        ];
    }

    /**
     * Tendência de consultas (últimos 14 dias)
     */
    public function getAppointmentsTrend($medicoId)
    {
        $labels = [];
        $data = [];

        for ($i = 13; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $labels[] = date('d/m', strtotime($date));

            $builder = $this->db->table('agendamentos');
            $builder->where('ID_Medico', $medicoId);
            $builder->where('Data_Agendamento', $date);
            $builder->where('Status !=', 'Cancelado');
            $data[] = $builder->countAllResults();
        }

        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * Distribuição de status das consultas
     */
    public function getStatusDistribution($medicoId)
    {
        $statuses = ['Pendente', 'Confirmado', 'Concluido', 'Cancelado'];
        $result = [];

        foreach ($statuses as $status) {
            $builder = $this->db->table('agendamentos');
            $builder->where('ID_Medico', $medicoId);
            $builder->where('Status', $status);
            $result[strtolower($status)] = $builder->countAllResults();
        }

        return $result;
    }

    /**
     * Novos vs Retorno
     */
    public function getNewReturnRatio($medicoId)
    {
        $monthStart = date('Y-m-01');
        $monthEnd = date('Y-m-t');

        // Consultas concluídas no mês
        $builder = $this->db->table('agendamentos a');
        $builder->where('a.ID_Medico', $medicoId);
        $builder->where('a.Data_Agendamento >=', $monthStart);
        $builder->where('a.Data_Agendamento <=', $monthEnd);
        $builder->where('a.Status', 'Concluido');
        $total = $builder->countAllResults();

        // Pacientes que tiveram consulta no mês
        $builder = $this->db->table('agendamentos a');
        $builder->select('a.ID_Paciente, COUNT(a.ID_Agendamento) as total');
        $builder->where('a.ID_Medico', $medicoId);
        $builder->where('a.Data_Agendamento >=', $monthStart);
        $builder->where('a.Data_Agendamento <=', $monthEnd);
        $builder->where('a.Status', 'Concluido');
        $builder->groupBy('a.ID_Paciente');
        $results = $builder->get()->getResult();

        $novos = 0;
        $retorno = 0;
        foreach ($results as $row) {
            if ($row->total == 1) {
                $novos++;
            } else {
                $retorno++;
            }
        }

        return [
            'novos' => $novos,
            'retorno' => $retorno
        ];
    }

    /**
     * Faixa etária dos pacientes
     */
    public function getAgeDistribution($medicoId)
    {
        $builder = $this->db->table('agendamentos a');
        $builder->select('
            p.Data_Nascimento,
            TIMESTAMPDIFF(YEAR, p.Data_Nascimento, CURDATE()) as idade
        ');
        $builder->join('pacientes p', 'p.ID_Paciente = a.ID_Paciente', 'left');
        $builder->where('a.ID_Medico', $medicoId);
        $builder->where('p.Data_Nascimento IS NOT NULL');
        $builder->groupBy('a.ID_Paciente');
        $results = $builder->get()->getResult();

        $faixas = [
            '0-18' => 0,
            '19-30' => 0,
            '31-45' => 0,
            '46-60' => 0,
            '60+' => 0
        ];

        foreach ($results as $row) {
            $idade = (int) $row->idade;
            if ($idade <= 18) $faixas['0-18']++;
            elseif ($idade <= 30) $faixas['19-30']++;
            elseif ($idade <= 45) $faixas['31-45']++;
            elseif ($idade <= 60) $faixas['46-60']++;
            else $faixas['60+']++;
        }

        return [
            'labels' => array_keys($faixas),
            'data' => array_values($faixas)
        ];
    }

    /**
     * Agenda do dia
     */
    public function getAgenda($medicoId, $date = null, $status = null, $search = null)
    {
        try {
            $builder = $this->db->table('agendamentos a');
            $builder->select('
            a.ID_Agendamento as id,
            a.Hora_Agendamento as hora,
            a.Status as status,
            a.Motivo as motivo,
            a.Sala as sala,
            p.Nome as paciente_nome,
            p.Sobrenome as paciente_sobrenome,
            p.BI as paciente_bi,
            p.Telefone as paciente_telefone
        ');
            $builder->join('pacientes p', 'p.ID_Paciente = a.ID_Paciente', 'left');
            $builder->where('a.ID_Medico', $medicoId);

            // Se não tiver data, usa a data atual
            if (empty($date)) {
                $date = date('Y-m-d');
            }

            $builder->where('a.Data_Agendamento', $date);

            // Filtro por status (se fornecido)
            if (!empty($status) && $status !== 'all') {
                $builder->where('LOWER(a.Status)', strtolower($status));
            }

            // Busca por paciente (se fornecido)
            if (!empty($search)) {
                $builder->groupStart()
                    ->like('p.Nome', $search)
                    ->orLike('p.Sobrenome', $search)
                    ->orLike('p.BI', $search)
                    ->groupEnd();
            }

            $builder->orderBy('a.Hora_Agendamento', 'ASC');
            $results = $builder->get()->getResult();

            // Log para debug
            log_message('debug', 'getAgenda - Encontrados: ' . count($results));

            // Formatar resultados
            $formatted = [];
            foreach ($results as $row) {
                $statusLabel = $row->status ?? 'Pendente';
                $statusLower = strtolower($statusLabel);

                $formatted[] = [
                    'id' => $row->id,
                    'time' => !empty($row->hora) ? substr($row->hora, 0, 5) : '--:--',
                    'patient_name' => trim(($row->paciente_nome ?? '') . ' ' . ($row->paciente_sobrenome ?? '')) ?: 'Paciente',
                    'patient_bi' => $row->paciente_bi ?? '',
                    'status' => $statusLower,
                    'status_label' => $statusLabel,
                    'type' => 'primeira',
                    'room' => !empty($row->sala) ? 'Sala ' . $row->sala : 'Sala 01',
                    'reason' => $row->motivo ?? ''
                ];
            }

            return $formatted;
        } catch (\Exception $e) {
            log_message('error', 'Erro em getAgenda: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $e->getTraceAsString());
            return [];
        }
    }

    /**
     * Próximas consultas
     */
    public function getUpcomingAppointments($medicoId)
    {
        $today = date('Y-m-d');

        $builder = $this->db->table('agendamentos a');
        $builder->select('
            a.*,
            p.Nome as paciente_nome,
            p.Sobrenome as paciente_sobrenome,
            p.BI as paciente_bi
        ');
        $builder->join('pacientes p', 'p.ID_Paciente = a.ID_Paciente', 'left');
        $builder->where('a.ID_Medico', $medicoId);
        $builder->where('a.Data_Agendamento >=', $today);
        $builder->where('a.Status !=', 'Cancelado');
        $builder->where('a.Status !=', 'Concluido');
        $builder->orderBy('a.Data_Agendamento', 'ASC');
        $builder->orderBy('a.Hora_Agendamento', 'ASC');
        $builder->limit(20);
        $results = $builder->get()->getResult();

        $formatted = [];
        foreach ($results as $row) {
            $formatted[] = [
                'id' => $row->ID_Agendamento,
                'date' => $row->Data_Agendamento,
                'time' => substr($row->Hora_Agendamento, 0, 5),
                'patient_name' => trim(($row->paciente_nome ?? '') . ' ' . ($row->paciente_sobrenome ?? '')),
                'patient_bi' => $row->paciente_bi ?? '',
                'status' => strtolower($row->Status ?? 'pendente'),
                'status_label' => $row->Status ?? 'Pendente',
                'type' => 'primeira'
            ];
        }

        return $formatted;
    }

    /**
     * Atualiza status da consulta
     */
    public function updateAppointmentStatus($appointmentId, $status)
    {
        try {
            $builder = $this->db->table('agendamentos');
            $builder->where('ID_Agendamento', $appointmentId);
            $result = $builder->update(['Status' => ucfirst($status)]);

            if ($result) {
                return ['success' => 'Status atualizado com sucesso!'];
            } else {
                return ['error' => 'Erro ao atualizar status.'];
            }
        } catch (\Exception $e) {
            return ['error' => 'Erro interno do servidor.'];
        }
    }

    /**
     * Busca detalhes do paciente
     */
    public function getPatientDetails($bi)
    {
        if (!$bi) return null;

        $builder = $this->db->table('pacientes');
        $builder->where('BI', $bi);
        $patient = $builder->get()->getRow();

        if (!$patient) return null;

        // Buscar histórico de consultas
        $builder = $this->db->table('agendamentos a');
        $builder->select('a.*, m.Nome as medico_nome, m.Sobrenome as medico_sobrenome');
        $builder->join('medicos m', 'm.ID_Medico = a.ID_Medico', 'left');
        $builder->where('a.ID_Paciente', $patient->ID_Paciente);
        $builder->orderBy('a.Data_Agendamento', 'DESC');
        $builder->limit(10);
        $history = $builder->get()->getResult();

        $historyFormatted = [];
        foreach ($history as $h) {
            $historyFormatted[] = [
                'date' => $h->Data_Agendamento,
                'reason' => $h->Motivo ?? 'Consulta',
                'status' => strtolower($h->Status ?? ''),
                'status_label' => $h->Status ?? ''
            ];
        }

        $idade = null;
        if ($patient->Data_Nascimento) {
            $idade = date_diff(date_create($patient->Data_Nascimento), date_create('now'))->y;
        }

        return [
            'name' => trim(($patient->Nome ?? '') . ' ' . ($patient->Sobrenome ?? '')),
            'bi' => $patient->BI ?? '',
            'age' => $idade,
            'phone' => $patient->Telefone ?? '',
            'allergies' => 'Nenhuma registada',
            'chronic_conditions' => 'Nenhuma registada',
            'history' => $historyFormatted
        ];
    }

    /**
     * Cria um novo horário
     */
    public function createSchedule($medicoId, $day, $start, $end, $status = 'ativo')
    {
        $data = [
            'ID_Medico' => $medicoId,
            'Dia_Semana' => $day,
            'Hora_Inicio' => $start,
            'Hora_Fim' => $end,
            'Status' => $status
        ];

        $builder = $this->db->table('horarios');
        return $builder->insert($data);
    }

    /**
     * Atualiza um horário
     */
    public function updateSchedule($id, $medicoId, $day, $start, $end, $status)
    {
        $data = [
            'ID_Medico' => $medicoId,
            'Dia_Semana' => $day,
            'Hora_Inicio' => $start,
            'Hora_Fim' => $end,
            'Status' => $status
        ];

        $builder = $this->db->table('horarios');
        return $builder->update($data, ['ID_Horario' => $id]);
    }

    /**
     * Exclui um horário
     */
    public function deleteSchedule($id, $medicoId)
    {
        $builder = $this->db->table('horarios');
        return $builder->delete(['ID_Horario' => $id, 'ID_Medico' => $medicoId]);
    }

    /**
     * Busca pacientes do médico
     */
    public function getPatients($medicoId, $search = '', $limit = 10, $offset = 0)
    {
        $builder = $this->db->table('agendamentos a');
        $builder->select('
            p.ID_Paciente,
            p.Nome,
            p.Sobrenome,
            p.Telefone,
            p.BI,
            COUNT(a.ID_Agendamento) as total_consultas,
            MAX(a.Data_Agendamento) as ultima_consulta
        ');
        $builder->join('pacientes p', 'p.ID_Paciente = a.ID_Paciente', 'left');
        $builder->where('a.ID_Medico', $medicoId);

        if ($search) {
            $builder->groupStart()
                ->like('p.Nome', $search)
                ->orLike('p.Sobrenome', $search)
                ->orLike('p.BI', $search)
                ->orLike('p.Telefone', $search)
                ->groupEnd();
        }

        $builder->groupBy('a.ID_Paciente');
        $builder->orderBy('ultima_consulta', 'DESC');
        $builder->limit($limit, $offset);

        return $builder->get()->getResult();
    }

    /**
     * Conta pacientes do médico
     */
    public function countPatients($medicoId, $search = '')
    {
        $builder = $this->db->table('agendamentos a');
        $builder->select('COUNT(DISTINCT a.ID_Paciente) as total');
        $builder->join('pacientes p', 'p.ID_Paciente = a.ID_Paciente', 'left');
        $builder->where('a.ID_Medico', $medicoId);

        if ($search) {
            $builder->groupStart()
                ->like('p.Nome', $search)
                ->orLike('p.Sobrenome', $search)
                ->orLike('p.BI', $search)
                ->orLike('p.Telefone', $search)
                ->groupEnd();
        }

        $result = $builder->get()->getRow();
        return $result->total ?? 0;
    }

    /**
     * Busca histórico de consultas
     */
    public function getHistory($medicoId, $period = 30, $status = '', $search = '', $limit = 10, $offset = 0)
    {
        $dateLimit = date('Y-m-d', strtotime("-$period days"));

        $builder = $this->db->table('agendamentos a');
        $builder->select('
            a.ID_Agendamento,
            a.Data_Agendamento,
            a.Hora_Agendamento,
            a.Status,
            a.Motivo,
            p.Nome as paciente_nome,
            p.Sobrenome as paciente_sobrenome,
            p.BI as paciente_bi
        ');
        $builder->join('pacientes p', 'p.ID_Paciente = a.ID_Paciente', 'left');
        $builder->where('a.ID_Medico', $medicoId);
        $builder->where('a.Data_Agendamento >=', $dateLimit);

        if ($status) {
            $builder->where('a.Status', ucfirst($status));
        }

        if ($search) {
            $builder->groupStart()
                ->like('p.Nome', $search)
                ->orLike('p.Sobrenome', $search)
                ->orLike('p.BI', $search)
                ->groupEnd();
        }

        $builder->orderBy('a.Data_Agendamento', 'DESC');
        $builder->limit($limit, $offset);

        return $builder->get()->getResult();
    }

    /**
     * Conta histórico de consultas
     */
    public function countHistory($medicoId, $period = 30, $status = '', $search = '')
    {
        $dateLimit = date('Y-m-d', strtotime("-$period days"));

        $builder = $this->db->table('agendamentos a');
        $builder->join('pacientes p', 'p.ID_Paciente = a.ID_Paciente', 'left');
        $builder->where('a.ID_Medico', $medicoId);
        $builder->where('a.Data_Agendamento >=', $dateLimit);

        if ($status) {
            $builder->where('a.Status', ucfirst($status));
        }

        if ($search) {
            $builder->groupStart()
                ->like('p.Nome', $search)
                ->orLike('p.Sobrenome', $search)
                ->orLike('p.BI', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Atualiza perfil do médico
     */
    public function updateProfile($medicoId, $nome, $sobrenome, $telefone, $email, $especialidade)
    {
        $data = [
            'Nome' => $nome,
            'Sobrenome' => $sobrenome,
            'Telefone' => $telefone,
            'Email' => $email,
            'Especialidade' => $especialidade
        ];

        $builder = $this->db->table('medicos');
        return $builder->update($data, ['ID_Medico' => $medicoId]);
    }

    /**
     * Busca horários do médico
     */
    public function getHorarios($medicoId)
    {
        $builder = $this->db->table('horarios');
        $builder->where('ID_Medico', $medicoId);
        $builder->orderBy('Dia_Semana', 'ASC');
        $builder->orderBy('Hora_Inicio', 'ASC');

        $results = $builder->get()->getResult();

        // Ordenar por dia da semana manualmente em PHP
        $order = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];
        usort($results, function ($a, $b) use ($order) {
            $posA = array_search($a->Dia_Semana, $order);
            $posB = array_search($b->Dia_Semana, $order);
            if ($posA === false) $posA = 999;
            if ($posB === false) $posB = 999;
            return $posA - $posB;
        });

        return $results;
    }
}
