<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurações - Administrador - Centro de Saúde Da Matola II</title>
    <meta name="description" content="Configurações do sistema no Centro de Saúde Da Matola II">
    <meta name="csrf-token" content="<?= csrf_hash(); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <script>
        var BASE_URL = '<?= rtrim(base_url(), '/'); ?>';
        var SITE_URL = '<?= rtrim(site_url(), '/'); ?>';
        var AJAX_URL = SITE_URL;
    </script>

    <style>
        :root {
            --brand-700: #1d4ed8;
            --brand-600: #2563eb;
            --brand-500: #3b82f6;
            --brand-100: #dbeafe;
            --teal-600: #0d9488;
            --teal-500: #14b8a6;
            --amber-500: #f59e0b;
            --rose-500: #ef4444;
            --ink-900: #111827;
            --ink-700: #374151;
            --ink-500: #6b7280;
            --paper: #f6f8fb;
        }

        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: var(--paper);
            color: var(--ink-900);
        }

        h1, h2, h3, .display-font { font-family: 'Outfit', 'Roboto', sans-serif; }

        /* Notification */
        #notification {
            display: none;
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1000;
            padding: 1rem 1.25rem;
            border-radius: 0.6rem;
            color: white;
            max-width: 350px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        #notification.error { background-color: #ef4444; }
        #notification.success { background-color: #0d9488; }
        #notification.info { background-color: #3b82f6; }
        #notification.warning { background-color: #f59e0b; }
        #notification.show { display: block; animation: slideIn 0.3s ease-out; }
        @keyframes slideIn {
            from { transform: translateX(110%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            width: 80px;
            background: linear-gradient(180deg, #0f2f66 0%, #123a80 100%);
            box-shadow: 2px 0 12px rgba(0,0,0,0.15);
            transition: transform 0.3s ease-in-out, width 0.3s ease-in-out;
            z-index: 900;
            display: flex;
            flex-direction: column;
        }
        .sidebar.show { transform: translateX(0); }
        .sidebar.desktop { transform: translateX(0); }
        .sidebar.desktop.expanded { width: 260px; }
        .sidebar.desktop .sidebar-text { display: none; }
        .sidebar.desktop.expanded .sidebar-text { display: inline; }
        .sidebar.desktop .sidebar-header { justify-content: center; padding: 1rem; }
        .sidebar.desktop.expanded .sidebar-header { justify-content: space-between; padding: 1rem 1.25rem; }
        .sidebar-header { border-bottom: 1px solid rgba(255,255,255,0.12); }
        .sidebar-header h2 { color: white; }
        .sidebar-header button { color: rgba(255,255,255,0.85); }
        .sidebar-header button:hover { color: white; }

        header {
            position: relative;
            z-index: 800;
            background: linear-gradient(90deg, #1d4ed8 0%, #2563eb 55%, #0d9488 130%);
            width: 100%;
            margin-left: 0;
        }

        .page-wrapper {
            margin-left: 80px;
            transition: margin-left 0.3s ease-in-out;
            width: calc(100% - 80px);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .page-wrapper.expanded { margin-left: 260px; width: calc(100% - 260px); }

        .main-content { flex: 1; width: 100%; padding: 1rem; min-height: calc(100vh - 80px); }

        .sidebar-overlay {
            display: none;
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 899;
        }
        .sidebar-overlay.show { display: block; }

        @media (min-width: 768px) {
            #mobile-menu-btn { display: none; }
            .sidebar.desktop { display: flex; }
        }
        @media (max-width: 767px) {
            .sidebar.desktop { display: none; }
            .sidebar { transform: translateX(-100%); width: 260px; }
            .sidebar.show { transform: translateX(0); }
            .page-wrapper { margin-left: 0 !important; width: 100% !important; }
            .page-wrapper.expanded { margin-left: 0 !important; width: 100% !important; }
        }

        .sidebar-nav { display: flex; flex-direction: column; height: calc(100% - 64px); padding: 0.5rem; }
        .main-menu {
            overflow-y: auto; flex-grow: 1;
            scrollbar-width: thin; scrollbar-color: rgba(255,255,255,0.35) transparent;
        }
        .main-menu::-webkit-scrollbar { width: 6px; }
        .main-menu::-webkit-scrollbar-track { background: transparent; }
        .main-menu::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.35); border-radius: 3px; }

        .sidebar-nav a, .sidebar-nav button {
            display: flex; align-items: center; gap: 10px;
            padding: 11px 16px; margin-bottom: 2px;
            border-radius: 0.5rem;
            color: rgba(255,255,255,0.8);
            transition: background-color 0.2s, color 0.2s;
            font-size: 0.92rem;
            width: 100%; text-align: left;
            border: none; background: none; cursor: pointer;
        }
        .sidebar-nav a:hover, .sidebar-nav button:hover { background-color: rgba(255,255,255,0.1); color: white; }
        .sidebar-nav a.active {
            background: rgba(255,255,255,0.16);
            color: white;
            box-shadow: inset 3px 0 0 var(--teal-500);
        }
        .sidebar-nav i { font-size: 1.3rem; width: 26px; text-align: center; }
        .sidebar.desktop .sidebar-nav a, .sidebar.desktop .sidebar-nav button { justify-content: center; padding: 11px; }
        .sidebar.desktop.expanded .sidebar-nav a, .sidebar.desktop.expanded .sidebar-nav button { justify-content: flex-start; padding: 11px 16px; }
        .sidebar-nav .logout { margin-top: 0.5rem; border-top: 1px solid rgba(255,255,255,0.12); padding-top: 0.5rem; }

        /* Config Section */
        .config-section {
            background-color: white;
            border-radius: 0.75rem;
            border: 1px solid #eef1f6;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .config-section h3 {
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            color: var(--ink-900);
            margin-bottom: 1rem;
        }

        .config-form {
            display: grid;
            gap: 1rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
        }
        .form-group label .required {
            color: #ef4444;
        }

        .form-input, .form-select, .form-textarea {
            padding: 0.6rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            transition: border-color 0.2s;
            width: 100%;
            background: white;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--brand-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }
        .form-input.input-success {
            border-color: #10b981 !important;
            background-color: #f0fdf4;
        }
        .form-input.input-error {
            border-color: #ef4444 !important;
            background-color: #fef2f2;
        }
        .form-textarea {
            min-height: 80px;
            resize: vertical;
        }

        .form-hint {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }
        .form-error {
            font-size: 0.75rem;
            color: #ef4444;
            margin-top: 0.25rem;
            display: none;
        }

        .btn {
            padding: 0.65rem 1.25rem;
            border-radius: 0.55rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }
        .btn-primary {
            background-color: var(--brand-500);
            color: white;
        }
        .btn-primary:hover {
            background-color: var(--brand-600);
            transform: translateY(-1px);
        }
        .btn-success {
            background-color: #10b981;
            color: white;
        }
        .btn-success:hover {
            background-color: #059669;
            transform: translateY(-1px);
        }
        .btn-danger {
            background-color: #ef4444;
            color: white;
        }
        .btn-danger:hover {
            background-color: #dc2626;
            transform: translateY(-1px);
        }
        .btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
        }
        .btn-secondary:hover {
            background-color: #d1d5db;
        }
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        .btn-sm {
            padding: 0.25rem 0.6rem;
            font-size: 0.75rem;
            border-radius: 0.375rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        .btn-sm:hover { transform: scale(1.05); }
        .btn-edit { background-color: var(--brand-500); color: white; }
        .btn-edit:hover { background-color: var(--brand-600); }
        .btn-delete { background-color: #ef4444; color: white; }
        .btn-delete:hover { background-color: #dc2626; }

        /* Tabs */
        .tabs {
            display: flex;
            flex-wrap: wrap;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 1.5rem;
            gap: 0.25rem;
        }
        .tab-btn {
            padding: 0.65rem 1.25rem;
            border: none;
            background: none;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s;
            font-weight: 500;
            font-size: 0.875rem;
            border-radius: 0.5rem 0.5rem 0 0;
            position: relative;
        }
        .tab-btn:hover {
            color: var(--ink-900);
            background-color: #f3f4f6;
        }
        .tab-btn.active {
            color: var(--brand-600);
            background-color: #eff6ff;
        }
        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background-color: var(--brand-500);
        }
        .tab-btn i { margin-right: 0.5rem; }

        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease-out;
        }
        .tab-content.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Table */
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        thead th {
            background-color: #f3f5f9;
            padding: 0.75rem 1rem; text-align: left;
            font-weight: 600; color: var(--ink-700);
            font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.03em;
        }
        tbody td { padding: 0.8rem 1rem; border-bottom: 1px solid #eef1f6; vertical-align: middle; }
        tbody tr:hover { background-color: #f9fafc; }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.72rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        .status-badge.ativo { background-color: #d1fae5; color: #065f46; }
        .status-badge.inativo { background-color: #fee2e2; color: #991b1b; }

        .role-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.72rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        .role-badge.admin { background-color: #dbeafe; color: #1e40af; }
        .role-badge.medico { background-color: #ccfbf1; color: #0f766e; }
        .role-badge.secretario { background-color: #fef3c7; color: #92400e; }
        .role-badge.paciente { background-color: #f3e8ff; color: #6d28d9; }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6b7280;
        }
        .empty-state i { font-size: 2.5rem; color: #d1d5db; margin-bottom: 0.5rem; }

        .pulse-line { width: 120px; height: 34px; opacity: 0.9; }
        .pulse-path {
            stroke-dasharray: 300;
            stroke-dashoffset: 300;
            animation: draw-pulse 3.2s ease-in-out infinite;
        }
        @keyframes draw-pulse {
            0% { stroke-dashoffset: 300; }
            55% { stroke-dashoffset: 0; }
            100% { stroke-dashoffset: -300; }
        }
        @media (prefers-reduced-motion: reduce) {
            .pulse-path { animation: none; stroke-dashoffset: 0; }
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        @media (min-width: 640px) {
            .grid-2 {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .main-content { padding: 0.5rem; }
            .config-section { padding: 1rem; }
            .tab-btn { font-size: 0.75rem; padding: 0.5rem 0.75rem; }
            table { font-size: 0.75rem; }
            thead th, tbody td { padding: 0.55rem; }
            .pulse-line { display: none; }
        }
    </style>
</head>
<body>
    <!-- Notification -->
    <div id="notification" role="alert">
        <span id="notification-message"></span>
        <button id="notification-close" class="ml-2 text-white hover:text-gray-200">×</button>
    </div>

    <!-- Sidebar Overlay -->
    <div id="sidebar-overlay" class="sidebar-overlay"></div>

    <!-- Left Sidebar -->
    <div id="sidebar-menu" class="sidebar desktop">
        <div class="sidebar-header flex justify-between items-center">
            <h2 class="text-lg font-semibold sidebar-text">Menu do Administrador</h2>
            <button id="toggle-sidebar-btn" aria-label="Alternar menu">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <button id="close-sidebar-btn" class="md:hidden close-sidebar-btn" aria-label="Fechar menu">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
            <div class="main-menu">
                <a href="<?= site_url('admin') ?>"><i class="fas fa-chart-pie"></i><span class="sidebar-text">Dashboard</span></a>
                <a href="<?= site_url('admin/pacientes') ?>"><i class="fas fa-users"></i><span class="sidebar-text">Pacientes</span></a>
                <a href="<?= site_url('admin/medicos') ?>"><i class="fas fa-user-md"></i><span class="sidebar-text">Médicos</span></a>
                <a href="<?= site_url('admin/secretarios') ?>"><i class="fas fa-user-tie"></i><span class="sidebar-text">Secretários</span></a>
                <a href="<?= site_url('admin/agendamentos') ?>"><i class="fas fa-calendar-check"></i><span class="sidebar-text">Agendamentos</span></a>
                <a href="<?= site_url('admin/disponibilidade') ?>"><i class="fas fa-calendar-alt"></i><span class="sidebar-text">Disponibilidade</span></a>
                <a href="<?= site_url('admin/relatorios') ?>"><i class="fas fa-chart-bar"></i><span class="sidebar-text">Relatórios</span></a>
                <a href="<?= site_url('admin/configuracoes') ?>" class="active"><i class="fas fa-cog"></i><span class="sidebar-text">Configurações</span></a>
            </div>
            <button id="logout-btn" class="logout">
                <i class="fas fa-sign-out-alt"></i>
                <span class="sidebar-text">Sair</span>
            </button>
        </nav>
    </div>

    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <!-- Header -->
        <header class="text-white shadow-lg">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <i class="fas fa-hospital-alt text-2xl" aria-label="Ícone do Centro de Saúde Da Matola II"></i>
                    <div>
                        <h1 class="text-xl font-bold leading-tight">Centro de Saúde Da Matola II</h1>
                        <p class="text-xs text-blue-100 opacity-90">Painel de Administração</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <svg class="pulse-line hidden sm:block" viewBox="0 0 140 40" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path class="pulse-path" d="M0 20 H35 L45 6 L55 34 L65 14 L72 20 H140" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <button id="mobile-menu-btn" class="md:hidden text-white hover:text-blue-200" aria-label="Abrir menu">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content">
            <div class="container mx-auto px-4 py-8">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">Configurações do Sistema</h2>
                        <p class="text-gray-500 text-sm">Gerencie as configurações do hospital e do sistema</p>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="tabs">
                    <button class="tab-btn active" data-tab="hospital">
                        <i class="fas fa-hospital"></i> Hospital
                    </button>
                    <button class="tab-btn" data-tab="users">
                        <i class="fas fa-users"></i> Usuários
                    </button>
                    <button class="tab-btn" data-tab="system">
                        <i class="fas fa-cog"></i> Sistema
                    </button>
                    <button class="tab-btn" data-tab="horarios">
                        <i class="fas fa-clock"></i> Horários
                    </button>
                </div>

                <!-- Hospital Tab -->
                <div id="hospital" class="tab-content active">
                    <div class="config-section">
                        <h3><i class="fas fa-hospital text-blue-500 mr-2"></i>Informações do Hospital</h3>
                        <form id="hospital-form" class="config-form" onsubmit="saveHospitalConfig(event)">
                            <div class="form-group">
                                <label for="hospital-name">Nome do Hospital <span class="required">*</span></label>
                                <input type="text" id="hospital-name" class="form-input" 
                                       value="<?= htmlspecialchars($config['hospital_name'] ?? 'Centro de Saúde Da Matola II'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="hospital-address">Endereço <span class="required">*</span></label>
                                <input type="text" id="hospital-address" class="form-input" 
                                       value="<?= htmlspecialchars($config['hospital_address'] ?? 'Av. 25 de Setembro, Maputo'); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="hospital-phone">Telefone <span class="required">*</span></label>
                                <input type="tel" id="hospital-phone" class="form-input" 
                                       value="<?= htmlspecialchars($config['hospital_phone'] ?? '+258 84 123 4567'); ?>" 
                                       placeholder="+258 84 123 4567" required>
                                <div class="form-hint">Formato: +258 84 1234567</div>
                            </div>
                            <div class="form-group">
                                <label for="hospital-email">E-mail</label>
                                <input type="email" id="hospital-email" class="form-input" 
                                       value="<?= htmlspecialchars($config['hospital_email'] ?? 'contato@hospitalmatlhovele.mz'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="hospital-description">Descrição</label>
                                <textarea id="hospital-description" class="form-textarea" rows="3"><?= htmlspecialchars($config['hospital_description'] ?? 'Hospital público dedicado ao cuidado de qualidade em Maputo.'); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-success" style="align-self: flex-start;">
                                <i class="fas fa-save mr-1"></i> Salvar Configurações
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Users Tab -->
                <div id="users" class="tab-content">
                    <div class="config-section">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-4">
                            <h3><i class="fas fa-users text-blue-500 mr-2"></i>Gestão de Usuários</h3>
                            <div class="flex flex-col sm:flex-row gap-2">
                                <div class="relative">
                                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                    <input type="text" id="user-search" class="form-input pl-9" placeholder="Pesquisar usuários..." style="max-width: 250px;">
                                </div>
                                <button class="btn btn-primary" id="add-user-btn">
                                    <i class="fas fa-plus mr-1"></i> Adicionar
                                </button>
                            </div>
                        </div>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>E-mail</th>
                                        <th>Tipo</th>
                                        <th>Status</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="users-table">
                                    <?php if (empty($users ?? [])): ?>
                                        <tr>
                                            <td colspan="5" class="empty-state">
                                                <i class="fas fa-users"></i>
                                                <p class="text-lg font-medium mb-2">Nenhum usuário encontrado</p>
                                                <button class="btn btn-primary" id="add-user-btn-inline">
                                                    <i class="fas fa-plus mr-1"></i> Adicionar Usuário
                                                </button>
                                            </td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($users ?? [] as $user): ?>
                                            <tr data-id="<?= $user['id'] ?? ''; ?>">
                                                <td class="font-medium"><?= htmlspecialchars($user['nome'] ?? $user['name'] ?? ''); ?></td>
                                                <td><?= htmlspecialchars($user['email'] ?? ''); ?></td>
                                                <td>
                                                    <span class="role-badge <?= strtolower($user['tipo'] ?? $user['role'] ?? ''); ?>">
                                                        <?= ucfirst($user['tipo'] ?? $user['role'] ?? ''); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="status-badge <?= ($user['status'] ?? 'ativo') === 'ativo' ? 'ativo' : 'inativo'; ?>">
                                                        <i class="fas fa-circle" style="font-size: 0.4rem;"></i>
                                                        <?= ucfirst($user['status'] ?? 'Ativo'); ?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn-sm btn-edit edit-user" data-id="<?= $user['id'] ?? ''; ?>" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn-sm btn-delete delete-user" data-id="<?= $user['id'] ?? ''; ?>" title="Excluir">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- System Tab -->
                <div id="system" class="tab-content">
                    <div class="config-section">
                        <h3><i class="fas fa-cog text-blue-500 mr-2"></i>Configurações do Sistema</h3>
                        <form id="system-form" class="config-form" onsubmit="saveSystemConfig(event)">
                            <div class="grid-2">
                                <div class="form-group">
                                    <label for="default-duration">Duração Padrão da Consulta (minutos) <span class="required">*</span></label>
                                    <input type="number" id="default-duration" class="form-input" 
                                           value="<?= htmlspecialchars($config['default_duration'] ?? '45'); ?>" 
                                           min="15" max="120" required>
                                </div>
                                <div class="form-group">
                                    <label for="max-appointments">Máximo de Agendamentos por Dia <span class="required">*</span></label>
                                    <input type="number" id="max-appointments" class="form-input" 
                                           value="<?= htmlspecialchars($config['max_appointments'] ?? '20'); ?>" 
                                           min="1" max="50" required>
                                </div>
                                <div class="form-group">
                                    <label for="email-notifications">Notificações por E-mail</label>
                                    <select id="email-notifications" class="form-select">
                                        <option value="1" <?= ($config['email_notifications'] ?? 1) ? 'selected' : ''; ?>>Ativadas</option>
                                        <option value="0" <?= !($config['email_notifications'] ?? 1) ? 'selected' : ''; ?>>Desativadas</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="sms-notifications">Notificações por SMS</label>
                                    <select id="sms-notifications" class="form-select">
                                        <option value="1" <?= ($config['sms_notifications'] ?? 0) ? 'selected' : ''; ?>>Ativadas</option>
                                        <option value="0" <?= !($config['sms_notifications'] ?? 0) ? 'selected' : ''; ?>>Desativadas</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success" style="align-self: flex-start;">
                                <i class="fas fa-save mr-1"></i> Salvar Configurações
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Horários Tab -->
                <div id="horarios" class="tab-content">
                    <div class="config-section">
                        <h3><i class="fas fa-clock text-blue-500 mr-2"></i>Horários de Funcionamento</h3>
                        <div class="grid-2">
                            <div class="form-group">
                                <label for="weekday-start">Horário de Abertura (Dias Úteis)</label>
                                <input type="time" id="weekday-start" class="form-input" 
                                       value="<?= htmlspecialchars($config['weekday_start'] ?? '07:30'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="weekday-end">Horário de Fechamento (Dias Úteis)</label>
                                <input type="time" id="weekday-end" class="form-input" 
                                       value="<?= htmlspecialchars($config['weekday_end'] ?? '16:30'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="saturday-start">Horário de Abertura (Sábado)</label>
                                <input type="time" id="saturday-start" class="form-input" 
                                       value="<?= htmlspecialchars($config['saturday_start'] ?? '08:00'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="saturday-end">Horário de Fechamento (Sábado)</label>
                                <input type="time" id="saturday-end" class="form-input" 
                                       value="<?= htmlspecialchars($config['saturday_end'] ?? '12:00'); ?>">
                            </div>
                        </div>
                        <button class="btn btn-success" id="save-schedule-btn" style="align-self: flex-start;">
                            <i class="fas fa-save mr-1"></i> Salvar Horários
                        </button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // ==================== CSRF ====================
        function getCsrfToken() {
            const metaToken = document.querySelector('meta[name="csrf-token"]');
            if (metaToken) {
                const token = metaToken.getAttribute('content');
                if (token && token.length > 0) return token;
            }
            
            const cookies = document.cookie.split(';');
            for (let cookie of cookies) {
                const [name, value] = cookie.trim().split('=');
                if (name === 'csrf_cookie_name') {
                    return value;
                }
            }
            return '';
        }

        function getCsrfName() {
            return 'csrf_test_name';
        }

        // ==================== NOTIFICAÇÕES ====================
        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            const messageEl = document.getElementById('notification-message');
            if (!notification || !messageEl) return;
            messageEl.innerHTML = message;
            notification.className = `show ${type}`;
            setTimeout(() => { notification.classList.remove('show'); }, 5000);
        }

        // ==================== TABS ====================
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                document.getElementById(this.dataset.tab).classList.add('active');
            });
        });

        // ==================== SAVE HOSPITAL CONFIG ====================
        async function saveHospitalConfig(event) {
            event.preventDefault();

            const name = document.getElementById('hospital-name').value.trim();
            const address = document.getElementById('hospital-address').value.trim();
            const phone = document.getElementById('hospital-phone').value.trim();
            const email = document.getElementById('hospital-email').value.trim();
            const description = document.getElementById('hospital-description').value.trim();

            if (!name || !address || !phone) {
                showNotification('Nome, endereço e telefone são obrigatórios.', 'error');
                return;
            }

            const csrfToken = getCsrfToken();
            const csrfName = getCsrfName();

            const formData = new FormData();
            formData.append('hospital_name', name);
            formData.append('hospital_address', address);
            formData.append('hospital_phone', phone);
            formData.append('hospital_email', email);
            formData.append('hospital_description', description);
            formData.append(csrfName, csrfToken);

            const btn = event.target.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Salvando...';

            try {
                const response = await fetch('<?= site_url('admin/save_hospital_config') ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });

                const result = await response.json();

                if (result.error) {
                    showNotification(result.error, 'error');
                } else {
                    showNotification(result.success || 'Configurações salvas com sucesso!', 'success');
                }
            } catch (error) {
                console.error('Erro:', error);
                showNotification('Erro ao salvar configurações.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save mr-1"></i> Salvar Configurações';
            }
        }

        // ==================== SAVE SYSTEM CONFIG ====================
        async function saveSystemConfig(event) {
            event.preventDefault();

            const defaultDuration = document.getElementById('default-duration').value;
            const maxAppointments = document.getElementById('max-appointments').value;
            const emailNotifications = document.getElementById('email-notifications').value;
            const smsNotifications = document.getElementById('sms-notifications').value;

            if (!defaultDuration || !maxAppointments) {
                showNotification('Duração e máximo de agendamentos são obrigatórios.', 'error');
                return;
            }

            const csrfToken = getCsrfToken();
            const csrfName = getCsrfName();

            const formData = new FormData();
            formData.append('default_duration', defaultDuration);
            formData.append('max_appointments', maxAppointments);
            formData.append('email_notifications', emailNotifications);
            formData.append('sms_notifications', smsNotifications);
            formData.append(csrfName, csrfToken);

            const btn = event.target.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Salvando...';

            try {
                const response = await fetch('<?= site_url('admin/save_system_config') ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });

                const result = await response.json();

                if (result.error) {
                    showNotification(result.error, 'error');
                } else {
                    showNotification(result.success || 'Configurações salvas com sucesso!', 'success');
                }
            } catch (error) {
                console.error('Erro:', error);
                showNotification('Erro ao salvar configurações.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save mr-1"></i> Salvar Configurações';
            }
        }

        // ==================== SAVE SCHEDULE ====================
        document.getElementById('save-schedule-btn').addEventListener('click', async function() {
            const weekdayStart = document.getElementById('weekday-start').value;
            const weekdayEnd = document.getElementById('weekday-end').value;
            const saturdayStart = document.getElementById('saturday-start').value;
            const saturdayEnd = document.getElementById('saturday-end').value;

            const csrfToken = getCsrfToken();
            const csrfName = getCsrfName();

            const formData = new FormData();
            formData.append('weekday_start', weekdayStart);
            formData.append('weekday_end', weekdayEnd);
            formData.append('saturday_start', saturdayStart);
            formData.append('saturday_end', saturdayEnd);
            formData.append(csrfName, csrfToken);

            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Salvando...';

            try {
                const response = await fetch('<?= site_url('admin/save_schedule_config') ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });

                const result = await response.json();

                if (result.error) {
                    showNotification(result.error, 'error');
                } else {
                    showNotification(result.success || 'Horários salvos com sucesso!', 'success');
                }
            } catch (error) {
                console.error('Erro:', error);
                showNotification('Erro ao salvar horários.', 'error');
            } finally {
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-save mr-1"></i> Salvar Horários';
            }
        });

        // ==================== USER SEARCH ====================
        document.getElementById('user-search').addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('#users-table tr');
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });

        // ==================== ADD USER ====================
        document.querySelectorAll('#add-user-btn, #add-user-btn-inline').forEach(btn => {
            btn.addEventListener('click', function() {
                window.location.href = '<?= site_url('admin/add_user') ?>';
            });
        });

        // ==================== EDIT USER ====================
        document.querySelectorAll('.edit-user').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                window.location.href = `<?= site_url('admin/edit_user') ?>?id=${id}`;
            });
        });

        // ==================== DELETE USER ====================
        document.querySelectorAll('.delete-user').forEach(btn => {
            btn.addEventListener('click', async function() {
                const id = this.dataset.id;
                if (!confirm('Tem certeza que deseja excluir este usuário?')) return;

                const csrfToken = getCsrfToken();
                const csrfName = getCsrfName();

                const formData = new FormData();
                formData.append('id', id);
                formData.append(csrfName, csrfToken);

                try {
                    const response = await fetch('<?= site_url('admin/delete_user') ?>', {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: formData
                    });

                    const result = await response.json();

                    if (result.error) {
                        showNotification(result.error, 'error');
                    } else {
                        showNotification(result.success || 'Usuário excluído com sucesso!', 'success');
                        // Remove a linha da tabela
                        const row = this.closest('tr');
                        if (row) row.remove();
                    }
                } catch (error) {
                    console.error('Erro:', error);
                    showNotification('Erro ao excluir usuário.', 'error');
                }
            });
        });

        // ==================== INICIALIZAÇÃO ====================
        document.addEventListener('DOMContentLoaded', function() {
            // Fechar notificação
            document.getElementById('notification-close').addEventListener('click', function() {
                document.getElementById('notification').classList.remove('show');
            });

            // Sidebar
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const sidebarMenu = document.getElementById('sidebar-menu');
            const closeSidebarBtn = document.getElementById('close-sidebar-btn');
            const toggleSidebarBtn = document.getElementById('toggle-sidebar-btn');
            const pageWrapper = document.querySelector('.page-wrapper');
            const sidebarOverlay = document.getElementById('sidebar-overlay');

            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    sidebarMenu.classList.add('show');
                    sidebarOverlay.classList.add('show');
                    pageWrapper.classList.add('expanded');
                });
            }

            if (closeSidebarBtn) {
                closeSidebarBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    sidebarMenu.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                    pageWrapper.classList.remove('expanded');
                });
            }

            if (toggleSidebarBtn) {
                toggleSidebarBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    sidebarMenu.classList.toggle('expanded');
                    pageWrapper.classList.toggle('expanded');
                });
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebarMenu.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                    pageWrapper.classList.remove('expanded');
                });
            }

            document.addEventListener('click', function(e) {
                const isClickInsideSidebar = sidebarMenu.contains(e.target);
                const isClickOnMenuBtn = mobileMenuBtn.contains(e.target);
                const isSidebarOpen = sidebarMenu.classList.contains('show');

                if (!isClickInsideSidebar && !isClickOnMenuBtn && isSidebarOpen) {
                    sidebarMenu.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                    pageWrapper.classList.remove('expanded');
                }
            });

            // Logout
            document.getElementById('logout-btn').addEventListener('click', function() {
                if (confirm('Tem certeza que deseja sair?')) {
                    window.location.href = SITE_URL + '/auth/logout';
                }
            });
        });
    </script>
</body>
</html>