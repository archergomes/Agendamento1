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

    /**
     * Busca usuário por email
     */
    public function getUserByEmail($email)
    {
        $builder = $this->db->table('usuarios');
        $builder->where('Email', $email);
        return $builder->get()->getRow();
    }

    /**
     * Salva token de recuperação
     */
    public function saveRecoveryToken($usuario_id, $token, $expiracao)
    {
        // Remover tokens antigos do mesmo usuário
        $builder = $this->db->table('recuperacao_senha');
        $builder->where('ID_Usuario', $usuario_id);
        $builder->delete();

        // Inserir novo token
        $data = [
            'ID_Usuario' => $usuario_id,
            'Token' => $token,
            'Expiracao' => $expiracao,
            'Criado_Em' => date('Y-m-d H:i:s')
        ];

        $builder = $this->db->table('recuperacao_senha');
        return $builder->insert($data);
    }

    /**
     * Busca dados do token
     */
    public function getTokenData($token)
    {
        $builder = $this->db->table('recuperacao_senha');
        $builder->where('Token', $token);
        return $builder->get()->getRow();
    }

    /**
     * Remove token
     */
    public function deleteToken($token)
    {
        $builder = $this->db->table('recuperacao_senha');
        $builder->where('Token', $token);
        return $builder->delete();
    }

    /**
     * Atualiza senha do usuário
     */
    public function updatePassword($usuario_id, $senha_hash)
    {
        $builder = $this->db->table('usuarios');
        $builder->where('ID_Usuario', $usuario_id);
        return $builder->update(['Senha' => $senha_hash]);
    }

    /**
     * Guarda um remember token para o utilizador
     */
    public function saveRememberToken($usuarioId, $token, $expiracao)
    {
        $builder = $this->db->table('remember_tokens');

        // Remover tokens antigos do mesmo utilizador
        $builder->where('ID_Usuario', $usuarioId)->delete();

        // Inserir novo token
        return $this->db->table('remember_tokens')->insert([
            'ID_Usuario' => $usuarioId,
            'Token'      => hash('sha256', $token),
            'Expira_Em'  => $expiracao,
            'Criado_Em'  => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Busca um remember token válido
     */
    public function getRememberToken($token)
    {
        $builder = $this->db->table('remember_tokens');
        $builder->where('Token', hash('sha256', $token));
        $builder->where('Expira_Em >=', date('Y-m-d H:i:s'));
        return $builder->get()->getRow();
    }

    /**
     * Remove o remember token do utilizador
     */
    public function deleteRememberTokenByUser($usuarioId)
    {
        $builder = $this->db->table('remember_tokens');
        $builder->where('ID_Usuario', $usuarioId);
        return $builder->delete();
    }

    /**
     * Remove o remember token pelo valor do token
     */
    public function deleteRememberToken($token)
    {
        $builder = $this->db->table('remember_tokens');
        $builder->where('Token', hash('sha256', $token));
        return $builder->delete();
    }
}
