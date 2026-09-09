<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class CreatePacienteUser extends Controller
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        echo "<h1>👤 Criar Usuário Paciente</h1>";
        echo "<hr>";

        // ============================================
        // DADOS DO PACIENTE
        // ============================================
        $pacienteData = [
            'Nome' => 'Maria',
            'Sobrenome' => 'Silva',
            'Telefone' => '+258 84 7654321',
            'BI' => '123456789MZ',
            'email' => 'maria.silva@email.com',
            'Data_Nascimento' => '1990-05-15',
            'Genero' => 'Feminino',
            'Endereco' => 'Av. 25 de Setembro, 123, Maputo',
            'Criado_Em' => date('Y-m-d H:i:s')
        ];

        // ============================================
        // DADOS DO USUÁRIO
        // ============================================
        $email = 'maria.silva@email.com';
        $senha = '123456';
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        echo "<h2>📋 Dados do Paciente</h2>";
        echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
        echo "<tr><td><strong>Nome</strong></td><td>{$pacienteData['Nome']} {$pacienteData['Sobrenome']}</td></tr>";
        echo "<tr><td><strong>Telefone</strong></td><td>{$pacienteData['Telefone']}</td></tr>";
        echo "<tr><td><strong>BI</strong></td><td>{$pacienteData['BI']}</td></tr>";
        echo "<tr><td><strong>Email</strong></td><td>{$pacienteData['email']}</td></tr>";
        echo "<tr><td><strong>Data Nascimento</strong></td><td>{$pacienteData['Data_Nascimento']}</td></tr>";
        echo "<tr><td><strong>Gênero</strong></td><td>{$pacienteData['Genero']}</td></tr>";
        echo "<tr><td><strong>Endereço</strong></td><td>{$pacienteData['Endereco']}</td></tr>";
        echo "</table>";

        // ============================================
        // 1. VERIFICAR SE O PACIENTE JÁ EXISTE
        // ============================================
        echo "<h3>🔍 Verificando existência...</h3>";
        
        $existingPaciente = $db->table('pacientes')
            ->where('BI', $pacienteData['BI'])
            ->get()
            ->getRow();
        
        if ($existingPaciente) {
            echo "<p style='color:orange;'>⚠️ Paciente já existe! ID: {$existingPaciente->ID_Paciente}</p>";
            $pacienteId = $existingPaciente->ID_Paciente;
        } else {
            // ============================================
            // 2. INSERIR PACIENTE
            // ============================================
            echo "<h3>📝 Inserindo paciente...</h3>";
            
            // Inserir paciente
            $db->table('pacientes')->insert($pacienteData);
            $pacienteId = $db->insertID();
            
            if ($pacienteId) {
                echo "<p style='color:green;'>✅ Paciente criado com ID: {$pacienteId}</p>";
            } else {
                echo "<p style='color:red;'>❌ Erro ao criar paciente!</p>";
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
            if ($existingUser->ID_Referencia != $pacienteId) {
                $db->table('usuarios')
                    ->where('ID_Usuario', $usuarioId)
                    ->update(['ID_Referencia' => $pacienteId]);
                echo "<p style='color:green;'>✅ ID_Referencia atualizado para {$pacienteId}</p>";
            }
        } else {
            // ============================================
            // 4. INSERIR USUÁRIO
            // ============================================
            echo "<h3>📝 Inserindo usuário...</h3>";
            
            $usuarioData = [
                'Email' => $email,
                'Senha' => $senhaHash,
                'Tipo_Usuario' => 'Paciente',
                'ID_Referencia' => $pacienteId,
                'Criado_Em' => date('Y-m-d H:i:s')
            ];
            
            $db->table('usuarios')->insert($usuarioData);
            $usuarioId = $db->insertID();
            
            if ($usuarioId) {
                echo "<p style='color:green;'>✅ Usuário criado com ID: {$usuarioId}</p>";
                
                // Atualizar paciente com ID_Usuario
                $db->table('pacientes')
                    ->where('ID_Paciente', $pacienteId)
                    ->update(['ID_Usuario' => $usuarioId]);
                echo "<p style='color:green;'>✅ ID_Usuario {$usuarioId} vinculado ao paciente {$pacienteId}</p>";
            } else {
                echo "<p style='color:red;'>❌ Erro ao criar usuário!</p>";
                return;
            }
        }

        // ============================================
        // 5. RESUMO FINAL
        // ============================================
        echo "<hr>";
        echo "<h2>✅ Cadastro Concluído!</h2>";
        
        echo "<div style='background:#f0f9ff;border:2px solid #2563eb;border-radius:8px;padding:20px;max-width:500px;'>";
        echo "<h3 style='margin-top:0;'>🔑 Credenciais de Acesso</h3>";
        echo "<table cellpadding='8'>";
        echo "<tr><td><strong>👤 Paciente</strong></td><td>{$pacienteData['Nome']} {$pacienteData['Sobrenome']}</td></tr>";
        echo "<tr><td><strong>📧 Email</strong></td><td><code>{$email}</code></td></tr>";
        echo "<tr><td><strong>🔐 Senha</strong></td><td><code>{$senha}</code></td></tr>";
        echo "<tr><td><strong>🏷️ Tipo</strong></td><td>Paciente</td></tr>";
        echo "<tr><td><strong>🆔 ID</strong></td><td>{$pacienteId}</td></tr>";
        echo "</table>";
        echo "</div>";

        // ============================================
        // 6. LISTAR TODOS OS PACIENTES
        // ============================================
        echo "<hr>";
        echo "<h2>📋 Lista de Pacientes Cadastrados</h2>";
        
        $pacientes = $db->table('pacientes')
            ->select('ID_Paciente, Nome, Sobrenome, Telefone, BI, email')
            ->orderBy('Nome', 'ASC')
            ->get()
            ->getResult();
        
        echo "<table border='1' cellpadding='8' style='border-collapse:collapse;width:100%;'>";
        echo "<tr style='background:#f0f0f0;'>";
        echo "<th>ID</th><th>Nome</th><th>Telefone</th><th>BI</th><th>Email</th>";
        echo "</tr>";
        
        foreach ($pacientes as $p) {
            echo "<tr>";
            echo "<td>{$p->ID_Paciente}</td>";
            echo "<td>{$p->Nome} {$p->Sobrenome}</td>";
            echo "<td>{$p->Telefone}</td>";
            echo "<td>{$p->BI}</td>";
            echo "<td>{$p->email}</td>";
            echo "</tr>";
        }
        echo "</table>";

        // ============================================
        // 7. LISTAR TODOS OS USUÁRIOS
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
        // 8. BOTÕES DE AÇÃO
        // ============================================
        echo "<hr>";
        echo "<div style='display:flex;gap:10px;flex-wrap:wrap;'>";
        echo "<a href='" . site_url('auth/login') . "' style='display:inline-block;padding:10px 20px;background:#2563eb;color:white;text-decoration:none;border-radius:5px;'>🔐 Ir para Login</a>";
        echo "<a href='" . site_url('agenda') . "' style='display:inline-block;padding:10px 20px;background:#059669;color:white;text-decoration:none;border-radius:5px;'>📅 Agendamento</a>";
        echo "<a href='" . site_url('admin') . "' style='display:inline-block;padding:10px 20px;background:#7c3aed;color:white;text-decoration:none;border-radius:5px;'>📊 Admin Dashboard</a>";
        echo "</div>";
    }
}