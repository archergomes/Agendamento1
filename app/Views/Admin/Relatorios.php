<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios - Administrador - Centro de Saúde Da Matola II</title>
    <meta name="description" content="Gerar e visualizar relatórios no Centro de Saúde Da Matola II">
    <meta name="csrf-token" content="<?= csrf_hash(); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
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

        /* Card Panel */
        .card-panel {
            background-color: white;
            border-radius: 0.75rem;
            border: 1px solid #eef1f6;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            padding: 1.5rem;
        }

        .report-card {
            background-color: white;
            border-radius: 0.75rem;
            border: 1px solid #eef1f6;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
        }

        .stat-item {
            text-align: center;
            padding: 1rem;
            background-color: #f9fafb;
            border-radius: 0.5rem;
            border: 1px solid #eef1f6;
            transition: all 0.2s;
        }
        .stat-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        .stat-number {
            font-family: 'Outfit', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--brand-500);
        }
        .stat-label {
            color: var(--ink-500);
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-top: 0.25rem;
        }

        .filter-section {
            background-color: white;
            border-radius: 0.75rem;
            border: 1px solid #eef1f6;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
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
        .btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
        }
        .btn-secondary:hover {
            background-color: #d1d5db;
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
        .btn-purple {
            background-color: #8b5cf6;
            color: white;
        }
        .btn-purple:hover {
            background-color: #7c3aed;
            transform: translateY(-1px);
        }
        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        .report-type-btn {
            padding: 0.5rem 1.25rem;
            border-radius: 0.5rem;
            font-size: 0.875rem;
            font-weight: 500;
            border: 2px solid #e5e7eb;
            background: white;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s;
        }
        .report-type-btn:hover {
            background-color: #f9fafb;
            border-color: #d1d5db;
        }
        .report-type-btn.active {
            border-color: var(--brand-500);
            background-color: #eff6ff;
            color: #1d4ed8;
        }

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
            padding: 0.3rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.72rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        .status-badge.pendente { background-color: #fef3c7; color: #92400e; }
        .status-badge.confirmado { background-color: #d1fae5; color: #065f46; }
        .status-badge.cancelado { background-color: #fee2e2; color: #991b1b; }
        .status-badge.concluido { background-color: #dbeafe; color: #1e40af; }

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

        .loading-spinner {
            display: inline-block; width: 1.3rem; height: 1.3rem;
            border: 3px solid #e5e7eb; border-top-color: var(--brand-500);
            border-radius: 50%; animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
        }

        @media (max-width: 640px) {
            .main-content { padding: 0.5rem; }
            .stat-grid { grid-template-columns: 1fr 1fr; }
            .filter-grid { grid-template-columns: 1fr; }
            table { font-size: 0.75rem; }
            thead th, tbody td { padding: 0.55rem; }
            .pulse-line { display: none; }
            .report-type-btn { font-size: 0.75rem; padding: 0.35rem 0.8rem; }
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
                <a href="<?= site_url('admin/disponibilidade') ?>" ><i class="fas fa-calendar-alt"></i><span class="sidebar-text">Disponibilidade</span></a>
                <a href="<?= site_url('admin/relatorios') ?>" class="active"><i class="fas fa-chart-bar"></i><span class="sidebar-text">Relatórios</span></a>
                <a href="<?= site_url('admin/configuracoes') ?>"><i class="fas fa-cog"></i><span class="sidebar-text">Configurações</span></a>
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
                        <h2 class="text-2xl font-semibold text-gray-800">Relatórios</h2>
                        <p class="text-gray-500 text-sm">Gere e visualize relatórios do sistema</p>
                    </div>
                </div>

                <!-- Report Type Selection -->
                <div class="filter-section">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Tipo de Relatório</h3>
                    <div class="flex flex-wrap gap-2">
                        <button class="report-type-btn active" data-type="overview">
                            <i class="fas fa-chart-pie mr-1"></i>Visão Geral
                        </button>
                        <button class="report-type-btn" data-type="appointments">
                            <i class="fas fa-calendar-check mr-1"></i>Agendamentos
                        </button>
                        <button class="report-type-btn" data-type="doctors">
                            <i class="fas fa-user-md mr-1"></i>Médicos
                        </button>
                        <button class="report-type-btn" data-type="patients">
                            <i class="fas fa-users mr-1"></i>Pacientes
                        </button>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filter-section" id="filters-section">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Filtros</h3>
                    <div class="filter-grid">
                        <div>
                            <label for="date-from" class="block text-sm font-medium text-gray-700 mb-1">Data Início</label>
                            <input type="date" id="date-from" class="form-input w-full p-2 border rounded-lg" value="<?= date('Y-m-01'); ?>">
                        </div>
                        <div>
                            <label for="date-to" class="block text-sm font-medium text-gray-700 mb-1">Data Fim</label>
                            <input type="date" id="date-to" class="form-input w-full p-2 border rounded-lg" value="<?= date('Y-m-d'); ?>">
                        </div>
                        <div>
                            <label for="doctor-filter" class="block text-sm font-medium text-gray-700 mb-1">Médico</label>
                            <select id="doctor-filter" class="form-input w-full p-2 border rounded-lg">
                                <option value="">Todos os Médicos</option>
                                <?php if (!empty($medicos)): ?>
                                    <?php foreach ($medicos as $medico): ?>
                                        <option value="<?= $medico->ID_Medico ?? ''; ?>">
                                            <?= htmlspecialchars(($medico->Nome ?? '') . ' ' . ($medico->Sobrenome ?? '')); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div>
                            <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select id="status-filter" class="form-input w-full p-2 border rounded-lg">
                                <option value="">Todos</option>
                                <option value="Pendente">Pendente</option>
                                <option value="Confirmado">Confirmado</option>
                                <option value="Cancelado">Cancelado</option>
                                <option value="Concluido">Concluído</option>
                            </select>
                        </div>
                    </div>
                    <div class="flex flex-wrap gap-2 mt-4">
                        <button id="generate-report" class="btn btn-primary">
                            <i class="fas fa-chart-bar mr-1"></i>Gerar Relatório
                        </button>
                        <button id="export-pdf" class="btn btn-danger">
                            <i class="fas fa-file-pdf mr-1"></i>Exportar PDF
                        </button>
                        <button id="export-csv" class="btn btn-success">
                            <i class="fas fa-file-csv mr-1"></i>Exportar CSV
                        </button>
                    </div>
                </div>

                <!-- Report Content -->
                <div id="report-content">
                    <!-- Overview Section -->
                    <div id="overview-section">
                        <div class="report-card">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Visão Geral do Período</h3>
                            <div class="stat-grid" id="overview-stats">
                                <div class="stat-item">
                                    <div class="stat-number" id="stat-total-appointments">0</div>
                                    <div class="stat-label">Total de Agendamentos</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number" id="stat-total-patients">0</div>
                                    <div class="stat-label">Total de Pacientes</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number" id="stat-total-doctors">0</div>
                                    <div class="stat-label">Total de Médicos</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number" id="stat-occupancy-rate">0%</div>
                                    <div class="stat-label">Taxa de Ocupação</div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="report-card">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Agendamentos por Médico</h3>
                                <div class="chart-container">
                                    <canvas id="chart-by-doctor"></canvas>
                                </div>
                            </div>
                            <div class="report-card">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Agendamentos por Status</h3>
                                <div class="chart-container">
                                    <canvas id="chart-by-status"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Appointments Section -->
                    <div id="appointments-section" class="hidden">
                        <div class="report-card">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Lista de Agendamentos</h3>
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Paciente</th>
                                            <th>Médico</th>
                                            <th>Data</th>
                                            <th>Hora</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="appointments-table-body">
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-gray-500">
                                                <span class="loading-spinner"></span> Carregando...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Doctors Section -->
                    <div id="doctors-section" class="hidden">
                        <div class="report-card">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Médicos</h3>
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Médico</th>
                                            <th>Especialidade</th>
                                            <th>Agendamentos</th>
                                            <th>Taxa de Ocupação</th>
                                        </tr>
                                    </thead>
                                    <tbody id="doctors-table-body">
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-gray-500">
                                                <span class="loading-spinner"></span> Carregando...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Patients Section -->
                    <div id="patients-section" class="hidden">
                        <div class="report-card">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pacientes</h3>
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Paciente</th>
                                            <th>Visitas</th>
                                            <th>Última Visita</th>
                                            <th>Status Médio</th>
                                        </tr>
                                    </thead>
                                    <tbody id="patients-table-body">
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-gray-500">
                                                <span class="loading-spinner"></span> Carregando...
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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

        // ==================== CHART.JS ====================
        let chartByDoctor = null;
        let chartByStatus = null;

        function createCharts(labels, data) {
            // Chart by Doctor
            const ctxDoctor = document.getElementById('chart-by-doctor');
            if (ctxDoctor) {
                if (chartByDoctor) chartByDoctor.destroy();
                chartByDoctor = new Chart(ctxDoctor, {
                    type: 'bar',
                    data: {
                        labels: labels.doctors || ['Nenhum dado'],
                        datasets: [{
                            label: 'Agendamentos',
                            data: data.doctors || [0],
                            backgroundColor: ['#3b82f6', '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444'],
                            borderRadius: 6,
                            maxBarThickness: 40
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            // Chart by Status
            const ctxStatus = document.getElementById('chart-by-status');
            if (ctxStatus) {
                if (chartByStatus) chartByStatus.destroy();
                const statusColors = {
                    'Pendente': '#f59e0b',
                    'Confirmado': '#10b981',
                    'Cancelado': '#ef4444',
                    'Concluido': '#3b82f6'
                };
                const statusLabels = data.status_labels || ['Pendente', 'Confirmado', 'Cancelado', 'Concluido'];
                const statusValues = data.status_values || [0, 0, 0, 0];
                const colors = statusLabels.map(s => statusColors[s] || '#6b7280');

                chartByStatus = new Chart(ctxStatus, {
                    type: 'doughnut',
                    data: {
                        labels: statusLabels,
                        datasets: [{
                            data: statusValues,
                            backgroundColor: colors,
                            borderWidth: 2,
                            borderColor: '#ffffff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { usePointStyle: true, padding: 15 }
                            }
                        }
                    }
                });
            }
        }

        // ==================== GET FILTERS ====================
        function getFilters() {
            return {
                date_from: document.getElementById('date-from').value,
                date_to: document.getElementById('date-to').value,
                doctor_id: document.getElementById('doctor-filter').value,
                status: document.getElementById('status-filter').value
            };
        }

        // ==================== RENDER REPORT ====================
        let currentReportType = 'overview';

        async function renderReport(type = 'overview') {
            currentReportType = type;
            
            // Show/hide sections
            document.querySelectorAll('[id$="-section"]').forEach(el => {
                el.classList.add('hidden');
            });
            const targetSection = document.getElementById(type + '-section');
            if (targetSection) targetSection.classList.remove('hidden');

            // Show/hide filters
            const filtersSection = document.getElementById('filters-section');
            if (filtersSection) {
                filtersSection.style.display = type === 'overview' ? 'none' : 'block';
            }

            const filters = getFilters();
            const csrfToken = getCsrfToken();
            const csrfName = getCsrfName();

            const formData = new FormData();
            formData.append('type', type);
            formData.append('date_from', filters.date_from);
            formData.append('date_to', filters.date_to);
            formData.append('doctor_id', filters.doctor_id);
            formData.append('status', filters.status);
            formData.append(csrfName, csrfToken);

            try {
                const response = await fetch('<?= site_url('admin/get_report_data') ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                
                const data = await response.json();
                console.log('Dados do relatório:', data);
                
                if (data.error) {
                    showNotification(data.error, 'error');
                    return;
                }

                if (type === 'overview') {
                    updateOverview(data);
                } else if (type === 'appointments') {
                    updateAppointmentsTable(data);
                } else if (type === 'doctors') {
                    updateDoctorsTable(data);
                } else if (type === 'patients') {
                    updatePatientsTable(data);
                }
            } catch (error) {
                console.error('Erro ao gerar relatório:', error);
                showNotification('Erro ao gerar relatório.', 'error');
            }
        }

        // ==================== UPDATE OVERVIEW ====================
        function updateOverview(data) {
            // Update stats
            document.getElementById('stat-total-appointments').textContent = data.total_appointments || 0;
            document.getElementById('stat-total-patients').textContent = data.total_patients || 0;
            document.getElementById('stat-total-doctors').textContent = data.total_doctors || 0;
            document.getElementById('stat-occupancy-rate').textContent = (data.occupancy_rate || 0) + '%';

            // Update charts
            createCharts(
                { doctors: data.doctor_labels || [] },
                { doctors: data.doctor_data || [], status_labels: data.status_labels || [], status_values: data.status_values || [] }
            );
        }

        // ==================== UPDATE TABLES ====================
        function updateAppointmentsTable(data) {
            const tbody = document.getElementById('appointments-table-body');
            if (!tbody) return;

            if (!data || data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <p>Nenhum agendamento encontrado para os filtros selecionados.</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = data.map(row => `
                <tr>
                    <td class="font-medium">${row.paciente || 'N/A'}</td>
                    <td>${row.medico || 'N/A'}</td>
                    <td>${row.data || '-'}</td>
                    <td>${row.hora || '-'}</td>
                    <td>${getStatusBadge(row.status)}</td>
                </tr>
            `).join('');
        }

        function updateDoctorsTable(data) {
            const tbody = document.getElementById('doctors-table-body');
            if (!tbody) return;

            if (!data || data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="empty-state">
                            <i class="fas fa-user-md"></i>
                            <p>Nenhum médico encontrado para os filtros selecionados.</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = data.map(row => `
                <tr>
                    <td class="font-medium">${row.name || 'N/A'}</td>
                    <td>${row.specialty || '-'}</td>
                    <td>${row.appointments || 0}</td>
                    <td>${row.occupancy || '0%'}</td>
                </tr>
            `).join('');
        }

        function updatePatientsTable(data) {
            const tbody = document.getElementById('patients-table-body');
            if (!tbody) return;

            if (!data || data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="4" class="empty-state">
                            <i class="fas fa-users"></i>
                            <p>Nenhum paciente encontrado para os filtros selecionados.</p>
                        </td>
                    </tr>
                `;
                return;
            }

            tbody.innerHTML = data.map(row => `
                <tr>
                    <td class="font-medium">${row.name || 'N/A'}</td>
                    <td>${row.visits || 0}</td>
                    <td>${row.last_visit || '-'}</td>
                    <td>${getStatusBadge(row.avg_status)}</td>
                </tr>
            `).join('');
        }

        // ==================== STATUS BADGE ====================
        function getStatusBadge(status) {
            if (!status) return '<span class="text-gray-500">N/A</span>';
            const statusMap = {
                'pendente': { label: 'Pendente', class: 'pendente' },
                'confirmado': { label: 'Confirmado', class: 'confirmado' },
                'cancelado': { label: 'Cancelado', class: 'cancelado' },
                'concluido': { label: 'Concluído', class: 'concluido' }
            };
            const key = status.toLowerCase();
            const data = statusMap[key] || { label: status, class: 'pendente' };
            return `<span class="status-badge ${data.class}">${data.label}</span>`;
        }

        // ==================== EXPORT ====================
        async function exportReport(format) {
            const filters = getFilters();
            const csrfToken = getCsrfToken();
            const csrfName = getCsrfName();

            const formData = new FormData();
            formData.append('format', format);
            formData.append('type', currentReportType);
            formData.append('date_from', filters.date_from);
            formData.append('date_to', filters.date_to);
            formData.append('doctor_id', filters.doctor_id);
            formData.append('status', filters.status);
            formData.append(csrfName, csrfToken);

            try {
                showNotification('Gerando arquivo...', 'info');
                
                const response = await fetch('<?= site_url('admin/export_report') ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });

                if (!response.ok) {
                    throw new Error('Erro ao exportar');
                }

                // Download do arquivo
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `relatorio_${currentReportType}_${new Date().toISOString().split('T')[0]}.${format === 'pdf' ? 'pdf' : 'csv'}`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);

                showNotification(`Relatório exportado como ${format.toUpperCase()}!`, 'success');
            } catch (error) {
                console.error('Erro ao exportar:', error);
                showNotification('Erro ao exportar relatório.', 'error');
            }
        }

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

            // ==================== REPORT TYPE BUTTONS ====================
            document.querySelectorAll('.report-type-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.report-type-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    renderReport(this.dataset.type);
                });
            });

            // ==================== GENERATE REPORT ====================
            document.getElementById('generate-report').addEventListener('click', function() {
                const activeBtn = document.querySelector('.report-type-btn.active');
                if (activeBtn) {
                    renderReport(activeBtn.dataset.type);
                }
            });

            // ==================== EXPORT BUTTONS ====================
            document.getElementById('export-pdf').addEventListener('click', function() {
                exportReport('pdf');
            });

            document.getElementById('export-csv').addEventListener('click', function() {
                exportReport('csv');
            });

            // ==================== LOGOUT ====================
            document.getElementById('logout-btn').addEventListener('click', function() {
                if (confirm('Tem certeza que deseja sair?')) {
                    window.location.href = SITE_URL + '/auth/logout';
                }
            });

            // ==================== CARREGAR RELATÓRIO INICIAL ====================
            renderReport('overview');
        });
    </script>
</body>
</html>