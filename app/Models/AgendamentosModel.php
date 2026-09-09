<?php

namespace App\Models;

use CodeIgniter\Model;

class AgendamentosModel extends Model
{
    protected $table = 'agendamentos';
    protected $primaryKey = 'ID_Agendamento';
    protected $allowedFields = ['ID_Paciente', 'ID_Medico', 'Data_Agendamento', 'Hora_Agendamento', 'Motivo', 'Status'];
    protected $useTimestamps = false;
    protected $returnType = 'object';

    /**
     * Busca especialidades
     */
    public function getEspecialidades()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('especialidades');
        $result = $builder->get()->getResult();

        // Log para debug
        log_message('debug', 'Especialidades encontradas: ' . count($result));

        return $result;
    }

    /**
     * Conta médicos por especialidade/query
     */
    public function countMedicos($specialty = '', $query = '')
    {
        $db = \Config\Database::connect();
        $builder = $db->table('medicos');

        // CORREÇÃO: A coluna pode ser 'Especialidade' ou 'ID_Especialidade'
        if (!empty($specialty)) {
            // Tenta buscar pelo nome da especialidade ou pelo ID
            $builder->groupStart()
                ->where('Especialidade', $specialty)
                ->orWhere('ID_Especialidade', $specialty)
                ->groupEnd();
        }

        if (!empty($query)) {
            $builder->groupStart()
                ->like('Nome', $query)
                ->orLike('Sobrenome', $query)
                ->groupEnd();
        }

        $count = $builder->countAllResults();
        log_message('debug', 'countMedicos - specialty: ' . $specialty . ', count: ' . $count);
        return $count;
    }

    /**
     * Busca médicos com filtros
     */
    public function getMedicos($specialty = '', $query = '', $limit = 10, $offset = 0)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('medicos');

        // CORREÇÃO: A coluna pode ser 'Especialidade' ou 'ID_Especialidade'
        if (!empty($specialty)) {
            $builder->groupStart()
                ->where('Especialidade', $specialty)
                ->orWhere('ID_Especialidade', $specialty)
                ->groupEnd();
        }

        if (!empty($query)) {
            $builder->groupStart()
                ->like('Nome', $query)
                ->orLike('Sobrenome', $query)
                ->groupEnd();
        }

        $builder->limit($limit, $offset);
        $result = $builder->get()->getResult();

        log_message('debug', 'getMedicos - encontrados: ' . count($result));
        return $result;
    }


    /**
     * Busca horários disponíveis para um médico em uma data
     */
    public function getAvailableSlots($data, $medicoId)
    {
        // Busca horários do médico
        $db = \Config\Database::connect();
        $builder = $db->table('horarios');
        $builder->where('ID_Medico', $medicoId);
        $horarios = $builder->get()->getResult();

        if (empty($horarios)) {
            return [];
        }

        // Busca agendamentos já marcados para esta data/médico
        $builder = $db->table('agendamentos');
        $builder->where('ID_Medico', $medicoId);
        $builder->where('Data_Agendamento', $data);
        $builder->where('Status !=', 'Cancelado');
        $agendamentos = $builder->get()->getResult();

        // Extrai horários ocupados
        $horariosOcupados = array_column($agendamentos, 'Hora_Agendamento');

        // Gera slots disponíveis
        $slots = [];
        foreach ($horarios as $horario) {
            $horaInicio = new \DateTime($horario->Hora_Inicio);
            $horaFim = new \DateTime($horario->Hora_Fim);
            $intervalo = new \DateInterval('PT30M'); // Intervalo de 30 minutos

            $horaAtual = clone $horaInicio;
            while ($horaAtual < $horaFim) {
                $slot = $horaAtual->format('H:i');
                if (!in_array($slot, $horariosOcupados)) {
                    $slots[] = $slot;
                }
                $horaAtual->add($intervalo);
            }
        }

        return $slots;
    }

    /**
     * Verifica se um horário está disponível
     */
    public function isSlotAvailable($data, $hora, $medicoId)
    {
        try {
            $builder = $this->db->table('agendamentos');
            $builder->where('ID_Medico', $medicoId);
            $builder->where('Data_Agendamento', $data);
            $builder->where('Hora_Agendamento', $hora);
            $builder->where('Status !=', 'Cancelado');
            $existing = $builder->get()->getRow();

            return $existing === null;
        } catch (\Exception $e) {
            log_message('error', 'isSlotAvailable - Erro: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Cria um novo agendamento
     */
    public function createAgendamento($data)
    {
        try {
            // Verificar se os campos obrigatórios existem
            if (
                empty($data['ID_Paciente']) || empty($data['Data_Agendamento']) ||
                empty($data['Hora_Agendamento'])
            ) {
                return ['error' => 'Dados incompletos para criar agendamento.'];
            }

            // Verificar se o ID_Medico está definido
            if (!isset($data['ID_Medico']) || empty($data['ID_Medico'])) {
                return ['error' => 'Médico não selecionado.'];
            }

            // Verificar se o horário está disponível
            $builder = $this->db->table('agendamentos');
            $builder->where('ID_Medico', $data['ID_Medico']);
            $builder->where('Data_Agendamento', $data['Data_Agendamento']);
            $builder->where('Hora_Agendamento', $data['Hora_Agendamento']);
            $builder->where('Status !=', 'Cancelado');
            $existing = $builder->get()->getRow();

            if ($existing) {
                return ['error' => 'Já existe um agendamento para este horário.'];
            }

            // Inserir agendamento - COM TODOS OS CAMPOS
            $insertData = [
                'ID_Paciente' => $data['ID_Paciente'],
                'ID_Medico' => $data['ID_Medico'],
                'Data_Agendamento' => $data['Data_Agendamento'],
                'Hora_Agendamento' => $data['Hora_Agendamento'],
                'Status' => $data['Status'] ?? 'Pendente',
                'Motivo' => $data['Motivo'] ?? null
            ];

            // Adicionar campos extras se existirem
            if (isset($data['tipo_agendamento'])) {
                $insertData['tipo_agendamento'] = $data['tipo_agendamento'];
            }
            if (isset($data['responsavel_nome'])) {
                $insertData['responsavel_nome'] = $data['responsavel_nome'];
            }
            if (isset($data['responsavel_telefone'])) {
                $insertData['responsavel_telefone'] = $data['responsavel_telefone'];
            }
            if (isset($data['responsavel_bi'])) {
                $insertData['responsavel_bi'] = $data['responsavel_bi'];
            }
            if (isset($data['paciente_nome_agendado'])) {
                $insertData['paciente_nome_agendado'] = $data['paciente_nome_agendado'];
            }
            if (isset($data['paciente_relacao'])) {
                $insertData['paciente_relacao'] = $data['paciente_relacao'];
            }
            if (isset($data['paciente_data_nasc_agendado'])) {
                $insertData['paciente_data_nasc_agendado'] = $data['paciente_data_nasc_agendado'];
            }
            if (isset($data['paciente_doc_tipo'])) {
                $insertData['paciente_doc_tipo'] = $data['paciente_doc_tipo'];
            }
            if (isset($data['paciente_doc_num'])) {
                $insertData['paciente_doc_num'] = $data['paciente_doc_num'];
            }

            $builder = $this->db->table('agendamentos');
            $result = $builder->insert($insertData);

            if ($result) {
                $id = $this->db->insertID();
                log_message('debug', 'createAgendamento - Agendamento criado com ID: ' . $id);
                return ['success' => 'Agendamento criado com sucesso!', 'id' => $id];
            } else {
                log_message('error', 'createAgendamento - Falha ao criar agendamento');
                log_message('error', 'createAgendamento - Último erro DB: ' . print_r($this->db->error(), true));
                return ['error' => 'Erro ao criar agendamento no banco de dados.'];
            }
        } catch (\Exception $e) {
            log_message('error', 'createAgendamento - Erro: ' . $e->getMessage());
            return ['error' => 'Erro interno: ' . $e->getMessage()];
        }
    }

    /**
     * Busca agendamentos de um paciente
     */
    public function getAppointmentsByPatient($pacienteId)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('agendamentos a');
        $builder->select('a.*, m.Nome as medico_nome, m.Sobrenome as medico_sobrenome, e.Nome as especialidade');
        $builder->join('medicos m', 'm.ID_Medico = a.ID_Medico');
        $builder->join('especialidades e', 'e.ID_Especialidade = m.ID_Especialidade', 'left');
        $builder->where('a.ID_Paciente', $pacienteId);
        $builder->orderBy('a.Data_Agendamento', 'DESC');

        $results = $builder->get()->getResult();

        // Formata para a view
        return array_map(function ($item) {
            return (object) [
                'id' => $item->ID_Agendamento,
                'especialidade' => $item->especialidade ?? 'N/A',
                'medico' => $item->medico_nome . ' ' . $item->medico_sobrenome,
                'date' => date('d/m/Y', strtotime($item->Data_Agendamento)),
                'time' => $item->Hora_Agendamento,
                'status' => $item->Status,
                'motivo' => $item->Motivo ?? 'N/A'
            ];
        }, $results);
    }

    /**
     * Cancela um agendamento
     */
    public function cancelAppointment($id)
    {
        try {
            $this->update($id, ['Status' => 'Cancelado']);
            return ['success' => 'Agendamento cancelado com sucesso!'];
        } catch (\Exception $e) {
            return ['error' => 'Erro ao cancelar agendamento: ' . $e->getMessage()];
        }
    }
}
