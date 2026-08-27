<?php

namespace App\Models;

use CodeIgniter\Model;

class SecretarioModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /**
     * Busca secretário por ID
     */
    public function getSecretarioById($id)
    {
        $builder = $this->db->table('secretarios');
        $builder->where('ID_Secretario', $id);
        return $builder->get()->getRow();
    }

    /**
     * Busca secretário por ID_Usuario
     */
    public function getSecretarioByUsuarioId($usuarioId)
    {
        $builder = $this->db->table('secretarios');
        $builder->where('ID_Usuario', $usuarioId);
        return $builder->get()->getRow();
    }

    /**
     * Métricas do dashboard
     */
    public function getMetrics()
    {
        $today = date('Y-m-d');

        // Consultas de hoje
        $builder = $this->db->table('agendamentos');
        $builder->where('Data_Agendamento', $today);
        $metrics['today_count'] = $builder->countAllResults();

        // Consultas pendentes
        $builder = $this->db->table('agendamentos');
        $builder->where('Status', 'Pendente');
        $metrics['pending_count'] = $builder->countAllResults();

        // Total de pacientes
        $builder = $this->db->table('pacientes');
        $metrics['total_patients'] = $builder->countAll();

        return $metrics;
    }

    /**
     * Busca agendamentos
     */
    public function getAppointments($status = '', $search = '', $date = '', $limit = 10, $offset = 0)
    {
        $builder = $this->db->table('agendamentos a');
        $builder->select('
            a.*,
            CONCAT(p.Nome, " ", p.Sobrenome) as paciente_nome,
            CONCAT(m.Nome, " ", m.Sobrenome) as medico_nome,
            e.Nome as especialidade
        ');
        $builder->join('pacientes p', 'p.ID_Paciente = a.ID_Paciente', 'left');
        $builder->join('medicos m', 'm.ID_Medico = a.ID_Medico', 'left');
        $builder->join('especialidades e', 'e.ID_Especialidade = m.ID_Especialidade', 'left');

        if ($status) {
            $builder->where('a.Status', $status);
        }

        if ($date) {
            $builder->where('a.Data_Agendamento', $date);
        }

        if ($search) {
            $builder->groupStart()
                    ->like('p.Nome', $search)
                    ->orLike('p.Sobrenome', $search)
                    ->orLike('m.Nome', $search)
                    ->orLike('m.Sobrenome', $search)
                    ->orLike('p.BI', $search)
                    ->groupEnd();
        }

        $builder->orderBy('a.Data_Agendamento', 'DESC');
        $builder->orderBy('a.Hora_Agendamento', 'ASC');
        $builder->limit($limit, $offset);

        return $builder->get()->getResult();
    }

    /**
     * Conta agendamentos
     */
    public function countAppointments($status = '', $search = '', $date = '')
    {
        $builder = $this->db->table('agendamentos a');
        $builder->join('pacientes p', 'p.ID_Paciente = a.ID_Paciente', 'left');
        $builder->join('medicos m', 'm.ID_Medico = a.ID_Medico', 'left');

        if ($status) {
            $builder->where('a.Status', $status);
        }

        if ($date) {
            $builder->where('a.Data_Agendamento', $date);
        }

        if ($search) {
            $builder->groupStart()
                    ->like('p.Nome', $search)
                    ->orLike('p.Sobrenome', $search)
                    ->orLike('m.Nome', $search)
                    ->orLike('m.Sobrenome', $search)
                    ->orLike('p.BI', $search)
                    ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Atualiza status do agendamento
     */
    public function updateAppointmentStatus($id, $status, $motivo = null)
    {
        $data = ['Status' => $status];
        if ($motivo) {
            $data['Motivo'] = $motivo;
        }

        $builder = $this->db->table('agendamentos');
        return $builder->update($data, ['ID_Agendamento' => $id]);
    }

    /**
     * Busca pacientes
     */
    public function getPatients($search = '', $limit = 10, $offset = 0)
    {
        $builder = $this->db->table('pacientes');
        
        if ($search) {
            $builder->groupStart()
                    ->like('Nome', $search)
                    ->orLike('Sobrenome', $search)
                    ->orLike('BI', $search)
                    ->orLike('Telefone', $search)
                    ->groupEnd();
        }

        $builder->orderBy('Nome', 'ASC');
        $builder->limit($limit, $offset);

        return $builder->get()->getResult();
    }

    /**
     * Conta pacientes
     */
    public function countPatients($search = '')
    {
        $builder = $this->db->table('pacientes');
        
        if ($search) {
            $builder->groupStart()
                    ->like('Nome', $search)
                    ->orLike('Sobrenome', $search)
                    ->orLike('BI', $search)
                    ->orLike('Telefone', $search)
                    ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Busca médicos
     */
    public function getDoctors($search = '', $limit = 10, $offset = 0)
    {
        $builder = $this->db->table('medicos');
        
        if ($search) {
            $builder->groupStart()
                    ->like('Nome', $search)
                    ->orLike('Sobrenome', $search)
                    ->orLike('Especialidade', $search)
                    ->orLike('Email', $search)
                    ->groupEnd();
        }

        $builder->orderBy('Nome', 'ASC');
        $builder->limit($limit, $offset);

        return $builder->get()->getResult();
    }

    /**
     * Conta médicos
     */
    public function countDoctors($search = '')
    {
        $builder = $this->db->table('medicos');
        
        if ($search) {
            $builder->groupStart()
                    ->like('Nome', $search)
                    ->orLike('Sobrenome', $search)
                    ->orLike('Especialidade', $search)
                    ->orLike('Email', $search)
                    ->groupEnd();
        }

        return $builder->countAllResults();
    }
}