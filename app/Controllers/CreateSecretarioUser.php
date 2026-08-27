<?php
// Crie o arquivo: app/Controllers/CreateSecretarioUser.php

namespace App\Controllers;

use CodeIgniter\Controller;

class CreateSecretarioUser extends Controller
{
    public function index()
    {
        $db = \Config\Database::connect();

        echo "<h1>👤 Criar Usuário Secretário</h1>";
        echo "<hr>";

        // Dados do secretário
        $email = 'sarcher@hospital.com';
        $senha = '123456';
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $secretarioData = [
            'Nome' => 'Secretario',
            'Sobrenome' => 'Archer',
            'Telefone' => '+258841234567',
            'Email' => $email,
            'Criado_Em' => date('Y-m-d H:i:s')
        ];

        echo "<h2>📋 Dados do Secretário</h2>";
        echo "<table border='1' cellpadding='8'>";
        echo "<tr><td><strong>Nome</strong></td><td>{$secretarioData['Nome']} {$secretarioData['Sobrenome']}</td></tr>";
        echo "<tr><td><strong>Email</strong></td><td>{$secretarioData['Email']}</td></tr>";
        echo "<tr><td><strong>Telefone</strong></td><td>{$secretarioData['Telefone']}</td></tr>";
        echo "</table>";

        // Verificar se o secretário já existe
        $existingSecretario = $db->table('secretarios')
            ->where('Email', $email)
            ->get()
            ->getRow();

        if ($existingSecretario) {
            echo "<p style='color:orange;'>⚠️ Secretário já existe! ID: {$existingSecretario->ID_Secretario}</p>";
            $secretarioId = $existingSecretario->ID_Secretario;
        } else {
            $db->table('secretarios')->insert($secretarioData);
            $secretarioId = $db->insertID();
            echo "<p style='color:green;'>✅ Secretário criado com ID: {$secretarioId}</p>";
        }

        // Verificar se o usuário já existe
        $existingUser = $db->table('usuarios')
            ->where('Email', $email)
            ->get()
            ->getRow();

        if ($existingUser) {
            echo "<p style='color:orange;'>⚠️ Usuário já existe! ID: {$existingUser->ID_Usuario}</p>";
            $usuarioId = $existingUser->ID_Usuario;
        } else {
            $usuarioData = [
                'Email' => $email,
                'Senha' => $senhaHash,
                'Tipo_Usuario' => 'Secretario',
                'ID_Referencia' => $secretarioId,
                'Criado_Em' => date('Y-m-d H:i:s')
            ];

            $db->table('usuarios')->insert($usuarioData);
            $usuarioId = $db->insertID();
            echo "<p style='color:green;'>✅ Usuário criado com ID: {$usuarioId}</p>";

            $db->table('secretarios')
                ->where('ID_Secretario', $secretarioId)
                ->update(['ID_Usuario' => $usuarioId]);
            echo "<p style='color:green;'>✅ ID_Usuario {$usuarioId} vinculado ao secretário</p>";
        }

        echo "<hr>";
        echo "<h2>✅ Cadastro Concluído!</h2>";
        echo "<div style='background:#f0f9ff;border:2px solid #2563eb;border-radius:8px;padding:20px;max-width:500px;'>";
        echo "<h3>🔑 Credenciais de Acesso</h3>";
        echo "<table cellpadding='8'>";
        echo "<tr><td><strong>👤 Secretário</strong></td><td>{$secretarioData['Nome']} {$secretarioData['Sobrenome']}</td></tr>";
        echo "<tr><td><strong>📧 Email</strong></td><td><code>{$email}</code></td></tr>";
        echo "<tr><td><strong>🔐 Senha</strong></td><td><code>{$senha}</code></td></tr>";
        echo "<tr><td><strong>🏷️ Tipo</strong></td><td>Secretário</td></tr>";
        echo "</table>";
        echo "</div>";

        echo "<br>";
        echo "<a href='" . site_url('auth/login') . "' style='display:inline-block;padding:10px 20px;background:#2563eb;color:white;text-decoration:none;border-radius:5px;'>🔐 Ir para Login</a>";
        echo "<a href='" . site_url('secretario') . "' style='display:inline-block;padding:10px 20px;background:#059669;color:white;text-decoration:none;border-radius:5px;margin-left:10px;'>📊 Dashboard do Secretário</a>";
    }
}