<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthModel extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'ID_Usuario';
    protected $allowedFields = ['Email', 'Senha', 'Tipo_Usuario', 'ID_Referencia'];
    protected $useTimestamps = false;
    protected $returnType = 'object';

    public function getUsuarioByEmail($email)
    {
        return $this->where('Email', $email)->first();
    }

    public function emailExists($email)
    {
        return $this->where('Email', $email)->countAllResults() > 0;
    }

    public function insertPaciente($data)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('pacientes');

        $pacienteData = [
            'Nome' => $data['nome'] ?? '',
            'Sobrenome' => $data['sobrenome'] ?? '',
            'Data_Nascimento' => $data['data_nascimento'] ?? null,
            'Genero' => $data['genero'] ?? 'Outro',
            'Telefone' => $data['telefone'] ?? '',
            'BI' => $data['bi'] ?? '',
            'Endereco' => $data['endereco'] ?? null,
            'Contato_Emergencia' => $data['contato_emergencia'] ?? null,
            'ID_Usuario' => $data['id_usuario'] ?? null
        ];

        $builder->insert($pacienteData);
        return $db->insertID();
    }

    public function insertUsuario($data)
    {
        $usuarioData = [
            'Email' => $data['email'] ?? '',
            'Senha' => $data['senha'] ?? '',
            'Tipo_Usuario' => $data['tipo_usuario'] ?? 'Paciente',
            'ID_Referencia' => $data['id_referencia'] ?? 0
        ];

        return $this->insert($usuarioData);
    }

    public function updatePacienteUsuarioId($pacienteId, $usuarioId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('pacientes');
        return $builder->update(
            ['ID_Usuario' => $usuarioId],
            ['ID_Paciente' => $pacienteId]
        );
    }

    public function deletePaciente($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('pacientes');
        return $builder->delete(['ID_Paciente' => $id]);
    }

    public function getPacienteById($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('pacientes');
        return $builder->where('ID_Paciente', $id)->get()->getRow();
    }

    public function getPacienteByUsuarioId($usuarioId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('pacientes');
        return $builder->where('ID_Usuario', $usuarioId)->get()->getRow();
    }

    /**
     * Atualiza paciente
     */
    public function updatePaciente($pacienteId, $data)
    {
        try {
            $db = \Config\Database::connect();
            $builder = $db->table('pacientes');
            $result = $builder->update($data, ['ID_Paciente' => $pacienteId]);
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Erro ao atualizar paciente: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Busca paciente por email
     */
    public function getPacienteByEmail($email)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('pacientes p');
        $builder->join('usuarios u', 'u.ID_Referencia = p.ID_Paciente');
        $builder->where('u.Email', $email);
        return $builder->get()->getRow();
    }
}
