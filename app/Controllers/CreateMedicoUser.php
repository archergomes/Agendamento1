<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class CreateMedicoUser extends Controller
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        echo "<h1>👨‍⚕️ Criar Usuário Médico</h1>";
        echo "<hr>";

        // ============================================
        // DADOS DO MÉDICO
        // ============================================
        $medicoData = [
            'Nome' => 'Carlos',
            'Sobrenome' => 'Mendes',
            'Especialidade' => 'Cardiologia',
            'ID_Especialidade' => 1,
            'ID_Departamento' => 1,
            'Telefone' => '+258 84 1234567',
            'Email' => 'dr.carlos.mendes@hospital.com',
            'Data_Inicio' => '2020-01-15',
            'Numero_Licenca' => 'LIC-2020-001',
            'Criado_Em' => date('Y-m-d H:i:s')
        ];

        // ============================================
        // DADOS DO USUÁRIO
        // ============================================
        $email = 'medico@hospital.com';
        $senha = '123456';
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        echo "<h2>📋 Dados do Médico</h2>";
        echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
        echo "<tr><td><strong>Nome</strong></td><td>{$medicoData['Nome']} {$medicoData['Sobrenome']}</td></tr>";
        echo "<tr><td><strong>Especialidade</strong></td><td>{$medicoData['Especialidade']}</td></tr>";
        echo "<tr><td><strong>Telefone</strong></td><td>{$medicoData['Telefone']}</td></tr>";
        echo "<tr><td><strong>Email</strong></td><td>{$medicoData['Email']}</td></tr>";
        echo "<tr><td><strong>Nº Licença</strong></td><td>{$medicoData['Numero_Licenca']}</td></tr>";
        echo "</table>";

        // ============================================
        // 1. VERIFICAR SE O MÉDICO JÁ EXISTE
        // ============================================
        echo "<h3>🔍 Verificando existência...</h3>";
        
        $existingMedico = $db->table('medicos')
            ->where('Email', $medicoData['Email'])
            ->get()
            ->getRow();
        
        if ($existingMedico) {
            echo "<p style='color:orange;'>⚠️ Médico já existe! ID: {$existingMedico->ID_Medico}</p>";
            $medicoId = $existingMedico->ID_Medico;
        } else {
            // ============================================
            // 2. INSERIR MÉDICO
            // ============================================
            echo "<h3>📝 Inserindo médico...</h3>";
            
            // Verificar se a especialidade existe
            $especialidade = $db->table('especialidades')
                ->where('Nome', $medicoData['Especialidade'])
                ->get()
                ->getRow();
            
            if (!$especialidade) {
                // Criar especialidade se não existir
                $db->table('especialidades')->insert(['Nome' => $medicoData['Especialidade']]);
                $medicoData['ID_Especialidade'] = $db->insertID();
                echo "<p style='color:green;'>✅ Especialidade '{$medicoData['Especialidade']}' criada com ID: {$medicoData['ID_Especialidade']}</p>";
            } else {
                $medicoData['ID_Especialidade'] = $especialidade->ID_Especialidade;
                echo "<p style='color:green;'>✅ Especialidade encontrada: ID {$medicoData['ID_Especialidade']}</p>";
            }
            
            // Inserir médico
            $db->table('medicos')->insert($medicoData);
            $medicoId = $db->insertID();
            
            if ($medicoId) {
                echo "<p style='color:green;'>✅ Médico criado com ID: {$medicoId}</p>";
            } else {
                echo "<p style='color:red;'>❌ Erro ao criar médico!</p>";
                return;
            }
        }

        // ============================================
        // 3. VERIFICAR SE O USUÁRIO JÁ EXISTE
        // ============================================
        echo "<h3>🔍 Verificando usuário...</h3>";
        
        $existingUser = $db->table('usuarios')
            ->where('Email', $email)
            ->get()
            ->getRow();
        
        if ($existingUser) {
            echo "<p style='color:orange;'>⚠️ Usuário já existe! ID: {$existingUser->ID_Usuario}</p>";
            $usuarioId = $existingUser->ID_Usuario;
            
            // Atualizar ID_Referencia se necessário
            if ($existingUser->ID_Referencia != $medicoId) {
                $db->table('usuarios')
                    ->where('ID_Usuario', $usuarioId)
                    ->update(['ID_Referencia' => $medicoId]);
                echo "<p style='color:green;'>✅ ID_Referencia atualizado para {$medicoId}</p>";
            }
        } else {
            // ============================================
            // 4. INSERIR USUÁRIO
            // ============================================
            echo "<h3>📝 Inserindo usuário...</h3>";
            
            $usuarioData = [
                'Email' => $email,
                'Senha' => $senhaHash,
                'Tipo_Usuario' => 'Medico',
                'ID_Referencia' => $medicoId,
                'Criado_Em' => date('Y-m-d H:i:s')
            ];
            
            $db->table('usuarios')->insert($usuarioData);
            $usuarioId = $db->insertID();
            
            if ($usuarioId) {
                echo "<p style='color:green;'>✅ Usuário criado com ID: {$usuarioId}</p>";
                
                // Atualizar médico com ID_Usuario
                $db->table('medicos')
                    ->where('ID_Medico', $medicoId)
                    ->update(['ID_Usuario' => $usuarioId]);
                echo "<p style='color:green;'>✅ ID_Usuario {$usuarioId} vinculado ao médico {$medicoId}</p>";
            } else {
                echo "<p style='color:red;'>❌ Erro ao criar usuário!</p>";
                return;
            }
        }

        // ============================================
        // 5. CRIAR HORÁRIOS PARA O MÉDICO
        // ============================================
        echo "<h3>📅 Criando horários para o médico...</h3>";
        
        $horarios = [
            ['Segunda', '08:00', '12:00'],
            ['Segunda', '14:00', '18:00'],
            ['Terça', '08:00', '12:00'],
            ['Terça', '14:00', '18:00'],
            ['Quarta', '08:00', '12:00'],
            ['Quarta', '14:00', '18:00'],
            ['Quinta', '08:00', '12:00'],
            ['Quinta', '14:00', '18:00'],
            ['Sexta', '08:00', '12:00'],
            ['Sexta', '14:00', '18:00']
        ];
        
        $horariosCriados = 0;
        foreach ($horarios as $horario) {
            // Verificar se o horário já existe
            $existing = $db->table('horarios')
                ->where('ID_Medico', $medicoId)
                ->where('Dia_Semana', $horario[0])
                ->where('Hora_Inicio', $horario[1])
                ->where('Hora_Fim', $horario[2])
                ->get()
                ->getRow();
            
            if (!$existing) {
                $db->table('horarios')->insert([
                    'ID_Medico' => $medicoId,
                    'Dia_Semana' => $horario[0],
                    'Hora_Inicio' => $horario[1],
                    'Hora_Fim' => $horario[2]
                ]);
                $horariosCriados++;
            }
        }
        
        echo "<p style='color:green;'>✅ {$horariosCriados} horários criados/verificados</p>";

        // ============================================
        // 6. RESUMO FINAL
        // ============================================
        echo "<hr>";
        echo "<h2>✅ Cadastro Concluído!</h2>";
        
        echo "<div style='background:#f0f9ff;border:2px solid #2563eb;border-radius:8px;padding:20px;max-width:500px;'>";
        echo "<h3 style='margin-top:0;'>🔑 Credenciais de Acesso</h3>";
        echo "<table cellpadding='8'>";
        echo "<tr><td><strong>👨‍⚕️ Médico</strong></td><td>Dr. Carlos Mendes</td></tr>";
        echo "<tr><td><strong>📧 Email</strong></td><td><code>{$email}</code></td></tr>";
        echo "<tr><td><strong>🔐 Senha</strong></td><td><code>{$senha}</code></td></tr>";
        echo "<tr><td><strong>🏷️ Tipo</strong></td><td>Médico</td></tr>";
        echo "<tr><td><strong>🆔 ID</strong></td><td>{$medicoId}</td></tr>";
        echo "</table>";
        echo "</div>";

        // ============================================
        // 7. LISTAR TODOS OS MÉDICOS
        // ============================================
        echo "<hr>";
        echo "<h2>📋 Lista de Médicos Cadastrados</h2>";
        
        $medicos = $db->table('medicos')
            ->select('ID_Medico, Nome, Sobrenome, Especialidade, Email, Numero_Licenca')
            ->orderBy('Nome', 'ASC')
            ->get()
            ->getResult();
        
        echo "<table border='1' cellpadding='8' style='border-collapse:collapse;width:100%;'>";
        echo "<tr style='background:#f0f0f0;'>";
        echo "<th>ID</th><th>Nome</th><th>Especialidade</th><th>Email</th><th>Licença</th>";
        echo "</tr>";
        
        foreach ($medicos as $m) {
            echo "<tr>";
            echo "<td>{$m->ID_Medico}</td>";
            echo "<td>{$m->Nome} {$m->Sobrenome}</td>";
            echo "<td>{$m->Especialidade}</td>";
            echo "<td>{$m->Email}</td>";
            echo "<td>{$m->Numero_Licenca}</td>";
            echo "</tr>";
        }
        echo "</table>";

        // ============================================
        // 8. LISTAR TODOS OS USUÁRIOS
        // ============================================
        echo "<hr>";
        echo "<h2>📋 Lista de Usuários</h2>";
        
        $usuarios = $db->table('usuarios')
            ->select('ID_Usuario, Email, Tipo_Usuario, ID_Referencia')
            ->orderBy('Tipo_Usuario', 'ASC')
            ->get()
            ->getResult();
        
        echo "<table border='1' cellpadding='8' style='border-collapse:collapse;width:100%;'>";
        echo "<tr style='background:#f0f0f0;'>";
        echo "<th>ID</th><th>Email</th><th>Tipo</th><th>ID_Referencia</th>";
        echo "</tr>";
        
        foreach ($usuarios as $u) {
            $color = $u->Tipo_Usuario === 'Admin' ? '#dbeafe' : 
                    ($u->Tipo_Usuario === 'Medico' ? '#d1fae5' : 
                    ($u->Tipo_Usuario === 'Secretario' ? '#fef3c7' : '#f3e8ff'));
            echo "<tr style='background:{$color}'>";
            echo "<td>{$u->ID_Usuario}</td>";
            echo "<td>{$u->Email}</td>";
            echo "<td><strong>{$u->Tipo_Usuario}</strong></td>";
            echo "<td>{$u->ID_Referencia}</td>";
            echo "</tr>";
        }
        echo "</table>";

        // ============================================
        // 9. BOTÕES DE AÇÃO
        // ============================================
        echo "<hr>";
        echo "<div style='display:flex;gap:10px;flex-wrap:wrap;'>";
        echo "<a href='" . site_url('auth/login') . "' style='display:inline-block;padding:10px 20px;background:#2563eb;color:white;text-decoration:none;border-radius:5px;'>🔐 Ir para Login</a>";
        echo "<a href='" . site_url('medico') . "' style='display:inline-block;padding:10px 20px;background:#059669;color:white;text-decoration:none;border-radius:5px;'>👨‍⚕️ Dashboard do Médico</a>";
        echo "<a href='" . site_url('admin') . "' style='display:inline-block;padding:10px 20px;background:#7c3aed;color:white;text-decoration:none;border-radius:5px;'>📊 Admin Dashboard</a>";
        echo "</div>";
    }
}