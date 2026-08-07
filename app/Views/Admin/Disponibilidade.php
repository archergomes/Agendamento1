<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disponibilidade - Administrador - Hospital Matlhovele</title>
    <meta name="description" content="Gerenciar disponibilidade dos médicos no Hospital Público de Matlhovele">
    <meta name="csrf-token" content="<?= csrf_hash(); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

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

        /* Sidebar - mesma estrutura das outras views */
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

        /* Metric cards */
        .metric-card {
            background-color: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            padding: 1.1rem 1.3rem;
            display: flex; align-items: center; gap: 0.9rem;
            border: 1px solid #eef1f6;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .metric-card:hover { transform: translateY(-2px); box-shadow: 0 10px 22px rgba(17,24,39,0.07); }
        .metric-card .icon-wrap {
            width: 46px; height: 46px; border-radius: 0.6rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; flex-shrink: 0;
        }
        .metric-card p.value { font-size: 1.55rem; font-weight: 700; line-height: 1.1; font-family: 'Outfit', sans-serif; }
        .metric-card h3 { font-size: 0.75rem; color: var(--ink-500); font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.1rem; }

        /* Form */
        .form-group { margin-bottom: 1rem; }
        .form-group label {
            display: block;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
        }
        .form-group label .required { color: #ef4444; }
        .form-hint { font-size: 0.72rem; color: var(--ink-500); margin-top: 0.25rem; }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            transition: border-color 0.2s;
            font-size: 0.875rem;
            background: white;
            font-family: 'Roboto', sans-serif;
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--brand-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }
        fieldset.form-section {
            border: 1px dashed #e2e8f0;
            border-radius: 0.6rem;
            padding: 0.9rem 1rem 0.3rem;
            margin-bottom: 1.1rem;
        }
        fieldset.form-section legend {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--brand-600);
            padding: 0 0.4rem;
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
        .btn-primary { background-color: var(--brand-500); color: white; }
        .btn-primary:hover { background-color: var(--brand-600); transform: translateY(-1px); }
        .btn-success { background-color: #10b981; color: white; }
        .btn-success:hover { background-color: #059669; transform: translateY(-1px); }
        .btn-danger { background-color: #ef4444; color: white; }
        .btn-danger:hover { background-color: #dc2626; transform: translateY(-1px); }
        .btn-secondary { background-color: #e5e7eb; color: #374151; }
        .btn-secondary:hover { background-color: #d1d5db; }
        .btn:disabled { opacity: 0.55; cursor: not-allowed; transform: none !important; }

        .btn-sm {
            padding: 0.3rem 0.65rem;
            font-size: 0.75rem;
            border-radius: 0.4rem;
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
        .btn-duplicate { background-color: #f1f5f9; color: var(--ink-700); }
        .btn-duplicate:hover { background-color: #e2e8f0; }
        .btn-delete { background-color: #ef4444; color: white; }
        .btn-delete:hover { background-color: #dc2626; }

        /* Table */
        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        thead th {
            background-color: #f3f5f9;
            padding: 0.75rem 1rem; text-align: left;
            font-weight: 600; color: var(--ink-700);
            font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.03em;
            white-space: nowrap;
        }
        tbody td { padding: 0.8rem 1rem; border-bottom: 1px solid #eef1f6; vertical-align: middle; }
        tbody tr:hover { background-color: #f9fafc; }
        tbody tr.row-inactive { opacity: 0.55; }

        .doctor-cell { display: flex; align-items: center; gap: 0.6rem; }
        .doctor-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: var(--brand-100); color: var(--brand-700);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.75rem; flex-shrink: 0;
        }
        .specialty-tag { font-size: 0.72rem; color: var(--ink-500); }

        .day-badge {
            display: inline-flex; align-items: center; gap: 0.3rem;
            background: #eef2ff; color: #3730a3;
            padding: 0.2rem 0.6rem; border-radius: 0.4rem; font-size: 0.78rem; font-weight: 600;
        }

        .time-block { display: flex; flex-direction: column; line-height: 1.3; }
        .time-block .main-time { font-weight: 600; font-family: 'Outfit', sans-serif; }
        .time-block .break-time { font-size: 0.72rem; color: var(--ink-500); }
        .time-block .break-time i { color: var(--amber-500); }

        .capacity-pill {
            display: inline-flex; align-items: center; gap: 0.3rem;
            font-size: 0.75rem; font-weight: 600; color: var(--teal-700, #0f766e);
            background: #f0fdfa; border: 1px solid #ccfbf1;
            padding: 0.15rem 0.55rem; border-radius: 1rem;
        }

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

        .vigencia-note { font-size: 0.72rem; color: var(--ink-500); }
        .vigencia-note.expiring { color: var(--amber-500); font-weight: 600; }

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

        .weekday-selector {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        .weekday-btn {
            padding: 0.45rem 0.85rem;
            border-radius: 0.4rem;
            border: 2px solid #e5e7eb;
            background: white;
            cursor: pointer;
            transition: all 0.15s;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--ink-700);
        }
        .weekday-btn:hover { border-color: var(--brand-500); background: #eff6ff; }
        .weekday-btn.active { border-color: var(--brand-500); background: var(--brand-500); color: white; }

        .chart-card {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #eef1f6;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            padding: 1.25rem 1.4rem 0.75rem;
        }
        .chart-card .chart-title { font-family: 'Outfit', sans-serif; font-weight: 600; font-size: 1rem; }
        .chart-card .chart-sub { font-size: 0.78rem; color: var(--ink-500); margin-bottom: 0.5rem; }
        .chart-canvas-wrap { position: relative; min-height: 220px; }

        @media (max-width: 640px) {
            .main-content { padding: 0.5rem; }
            .card-panel { padding: 1rem; }
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
                <a href="<?= site_url('admin/disponibilidade') ?>" class="active"><i class="fas fa-calendar-alt"></i><span class="sidebar-text">Disponibilidade</span></a>
                <a href="<?= site_url('admin/relatorios') ?>"><i class="fas fa-chart-bar"></i><span class="sidebar-text">Relatórios</span></a>
                <a href="<?= site_url('admin/configuracoes') ?>"><i class="fas fa-cog"></i><span class="sidebar-text">Configurações</span></a>
            </div>
            <button id="logout-btn" class="logout"><i class="fas fa-sign-out-alt"></i><span class="sidebar-text">Sair</span></button>
        </nav>
    </div>

    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <header class="text-white shadow-lg">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <i class="fas fa-hospital-alt text-2xl"></i>
                    <div>
                        <h1 class="text-xl font-bold leading-tight">Hospital Matlhovele</h1>
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

        <main class="main-content">
            <div class="container mx-auto px-4 py-8">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">Disponibilidade dos Médicos</h2>
                        <p class="text-gray-500 text-sm">Gerencie horários, salas, intervalos e vigência dos atendimentos</p>
                    </div>
                    <button class="btn btn-primary" id="add-schedule-btn">
                        <i class="fas fa-plus mr-1"></i> Adicionar Horário
                    </button>
                </div>

                <!-- Metrics -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <div class="metric-card">
                        <div class="icon-wrap" style="background:#dbeafe; color:#2563eb;"><i class="fas fa-user-md"></i></div>
                        <div>
                            <h3>Médicos com horário</h3>
                            <p class="value" id="metric-doctors-scheduled">—</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="icon-wrap" style="background:#ccfbf1; color:#0d9488;"><i class="fas fa-hourglass-half"></i></div>
                        <div>
                            <h3>Horas semanais</h3>
                            <p class="value" id="metric-weekly-hours">—</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="icon-wrap" style="background:#ede9fe; color:#7c3aed;"><i class="fas fa-calendar-day"></i></div>
                        <div>
                            <h3>Vagas/dia (estim.)</h3>
                            <p class="value" id="metric-daily-slots">—</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="icon-wrap" style="background:#fee2e2; color:#dc2626;"><i class="fas fa-calendar-times"></i></div>
                        <div>
                            <h3>Horários inativos</h3>
                            <p class="value" id="metric-inactive">—</p>
                        </div>
                    </div>
                </div>

                <!-- Coverage chart -->
                <div class="chart-card mb-6">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div>
                            <div class="chart-title">Cobertura Semanal</div>
                            <div class="chart-sub">Horas de atendimento agregadas por dia da semana (todos os médicos ativos)</div>
                        </div>
                        <span class="text-xs font-semibold text-amber-700 bg-amber-50 px-2 py-1 rounded-full" id="coverage-gap-badge">—</span>
                    </div>
                    <div class="chart-canvas-wrap">
                        <canvas id="chart-coverage"></canvas>
                    </div>
                </div>

                <!-- Filters -->
                <div class="card-panel mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="form-group mb-0">
                            <label for="filter-doctor">Médico</label>
                            <select id="filter-doctor" class="form-select">
                                <option value="">Todos os Médicos</option>
                                <?php if (!empty($medicos)): ?>
                                    <?php foreach ($medicos as $medico): ?>
                                        <option value="<?= $medico->ID_Medico ?? ''; ?>" data-specialty="<?= htmlspecialchars($medico->Especialidade ?? ''); ?>">
                                            <?= htmlspecialchars(($medico->Nome ?? '') . ' ' . ($medico->Sobrenome ?? '')); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label for="filter-day">Dia da Semana</label>
                            <select id="filter-day" class="form-select">
                                <option value="">Todos os Dias</option>
                                <option value="Segunda">Segunda-feira</option>
                                <option value="Terça">Terça-feira</option>
                                <option value="Quarta">Quarta-feira</option>
                                <option value="Quinta">Quinta-feira</option>
                                <option value="Sexta">Sexta-feira</option>
                                <option value="Sábado">Sábado</option>
                                <option value="Domingo">Domingo</option>
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label for="filter-status">Status</label>
                            <select id="filter-status" class="form-select">
                                <option value="">Todos</option>
                                <option value="ativo">Ativo</option>
                                <option value="inativo">Inativo</option>
                            </select>
                        </div>
                        <div class="form-group mb-0">
                            <label for="filter-search">Sala / Consultório</label>
                            <input type="text" id="filter-search" class="form-input" placeholder="Ex: Consultório 3">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 mt-4">
                        <button class="btn btn-secondary" id="clear-filter-btn">Limpar</button>
                        <button class="btn btn-primary" id="apply-filter-btn">
                            <i class="fas fa-filter mr-1"></i> Aplicar Filtro
                        </button>
                    </div>
                </div>

                <!-- Schedule Table -->
                <div class="card-panel">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Horários Cadastrados</h3>
                        <span class="text-xs text-gray-400" id="schedule-count"></span>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Médico</th>
                                    <th>Dia</th>
                                    <th>Horário</th>
                                    <th>Sala</th>
                                    <th>Duração / Vagas</th>
                                    <th>Vigência</th>
                                    <th>Status</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="schedule-table">
                                <?php if (empty($horarios ?? [])): ?>
                                    <tr>
                                        <td colspan="8" class="empty-state">
                                            <i class="fas fa-clock"></i>
                                            <p class="text-lg font-medium mb-2">Nenhum horário cadastrado</p>
                                            <button class="btn btn-primary" id="add-schedule-empty-btn">
                                                <i class="fas fa-plus mr-1"></i> Adicionar Horário
                                            </button>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($horarios ?? [] as $horario):
                                        $inicio = $horario->Hora_Inicio ?? '00:00:00';
                                        $fim = $horario->Hora_Fim ?? '00:00:00';
                                        $intervaloInicio = $horario->Intervalo_Inicio ?? '';
                                        $intervaloFim = $horario->Intervalo_Fim ?? '';
                                        $duracao = (int)($horario->Duracao_Consulta ?? 30);
                                        $status = $horario->Status ?? 'ativo';

                                        $toMin = function ($t) {
                                            if (empty($t)) return null;
                                            [$h, $m] = array_map('intval', explode(':', $t));
                                            return $h * 60 + $m;
                                        };
                                        $totalMin = ($toMin($fim) ?? 0) - ($toMin($inicio) ?? 0);
                                        if (!empty($intervaloInicio) && !empty($intervaloFim)) {
                                            $totalMin -= (($toMin($intervaloFim) ?? 0) - ($toMin($intervaloInicio) ?? 0));
                                        }
                                        $vagas = $duracao > 0 && $totalMin > 0 ? intdiv($totalMin, $duracao) : 0;

                                        $vigenciaFim = $horario->Data_Fim_Vigencia ?? '';
                                        $vigenciaExpiringClass = '';
                                        if (!empty($vigenciaFim)) {
                                            $diff = (strtotime($vigenciaFim) - time()) / 86400;
                                            if ($diff >= 0 && $diff <= 14) $vigenciaExpiringClass = 'expiring';
                                        }
                                    ?>
                                        <tr data-id="<?= $horario->ID_Horario ?? ''; ?>" class="<?= $status !== 'ativo' ? 'row-inactive' : ''; ?>">
                                            <td>
                                                <div class="doctor-cell">
                                                    <div class="doctor-avatar"><?= strtoupper(substr($horario->medico_nome ?? '?', 0, 1)); ?></div>
                                                    <div>
                                                        <div class="font-medium"><?= htmlspecialchars(($horario->medico_nome ?? '') . ' ' . ($horario->medico_sobrenome ?? '')); ?></div>
                                                        <div class="specialty-tag"><?= htmlspecialchars($horario->especialidade ?? ''); ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="day-badge"><i class="fas fa-calendar-day"></i> <?= htmlspecialchars($horario->Dia_Semana ?? ''); ?></span></td>
                                            <td>
                                                <div class="time-block">
                                                    <span class="main-time"><?= substr($inicio, 0, 5); ?> – <?= substr($fim, 0, 5); ?></span>
                                                    <?php if (!empty($intervaloInicio) && !empty($intervaloFim)): ?>
                                                        <span class="break-time"><i class="fas fa-mug-hot mr-1"></i>Intervalo <?= substr($intervaloInicio, 0, 5); ?>–<?= substr($intervaloFim, 0, 5); ?></span>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($horario->Sala ?? '—'); ?></td>
                                            <td>
                                                <div class="text-sm"><?= $duracao; ?> min / consulta</div>
                                                <span class="capacity-pill"><i class="fas fa-user-check"></i> ~<?= $vagas; ?> vagas</span>
                                            </td>
                                            <td>
                                                <?php if (!empty($horario->Data_Inicio_Vigencia) || !empty($vigenciaFim)): ?>
                                                    <div class="vigencia-note <?= $vigenciaExpiringClass; ?>">
                                                        <?= !empty($horario->Data_Inicio_Vigencia) ? date('d/m/Y', strtotime($horario->Data_Inicio_Vigencia)) : '—'; ?>
                                                        até
                                                        <?= !empty($vigenciaFim) ? date('d/m/Y', strtotime($vigenciaFim)) : 'indeterminado'; ?>
                                                        <?= $vigenciaExpiringClass ? '<i class="fas fa-exclamation-triangle ml-1"></i>' : ''; ?>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="vigencia-note">Permanente</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="status-badge <?= $status === 'ativo' ? 'ativo' : 'inativo'; ?>">
                                                    <i class="fas fa-circle" style="font-size: 0.4rem;"></i>
                                                    <?= ucfirst($status); ?>
                                                </span>
                                            </td>
                                            <td class="text-center whitespace-nowrap">
                                                <button class="btn-sm btn-edit edit-schedule" data-id="<?= $horario->ID_Horario ?? ''; ?>" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button class="btn-sm btn-duplicate duplicate-schedule" data-id="<?= $horario->ID_Horario ?? ''; ?>" title="Duplicar">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                                <button class="btn-sm btn-delete delete-schedule" data-id="<?= $horario->ID_Horario ?? ''; ?>" title="Excluir">
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
        </main>

        <footer class="bg-gray-800 text-white py-6">
            <div class="container mx-auto px-4 text-center text-gray-400 text-sm">
                <p>© <?= date('Y') ?> Hospital Público de Matlhovele. Todos os direitos reservados.</p>
            </div>
        </footer>
    </div>

    <!-- Modal para Adicionar/Editar Horário -->
    <div id="schedule-modal" class="modal-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15,23,42,0.55); z-index: 950; justify-content: center; align-items: center; padding: 1rem;">
        <div class="modal-content" style="background: white; padding: 1.5rem; border-radius: 0.85rem; max-width: 560px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 24px 60px rgba(0,0,0,0.35);">
            <div class="modal-header" style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f3f4f6; padding-bottom: 1rem; margin-bottom: 1.5rem;">
                <h3 id="modal-title" style="font-size: 1.15rem; font-weight: 600; color: #1f2937;"><i class="fas fa-clock text-blue-500 mr-2"></i>Adicionar Horário</h3>
                <button class="modal-close" onclick="closeScheduleModal()" style="background: none; border: none; font-size: 1.5rem; color: #6b7280; cursor: pointer;">&times;</button>
            </div>
            <form id="schedule-form" onsubmit="saveSchedule(event)">
                <input type="hidden" id="schedule-id" name="id">

                <fieldset class="form-section">
                    <legend>Médico e Local</legend>
                    <div class="form-group">
                        <label for="schedule-doctor">Médico <span class="required">*</span></label>
                        <select id="schedule-doctor" class="form-select" required>
                            <option value="">Selecione um médico</option>
                            <?php if (!empty($medicos)): ?>
                                <?php foreach ($medicos as $medico): ?>
                                    <option value="<?= $medico->ID_Medico ?? ''; ?>">
                                        <?= htmlspecialchars(($medico->Nome ?? '') . ' ' . ($medico->Sobrenome ?? '')); ?>
                                        <?= !empty($medico->Especialidade) ? ' — ' . htmlspecialchars($medico->Especialidade) : ''; ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="schedule-room">Sala / Consultório</label>
                        <input type="text" id="schedule-room" class="form-input" placeholder="Ex: Consultório 3, Sala de Pediatria">
                    </div>
                </fieldset>

                <fieldset class="form-section">
                    <legend>Dias e Horário</legend>
                    <div class="form-group">
                        <label>Dias da Semana <span class="required">*</span></label>
                        <div class="weekday-selector" id="schedule-weekday-selector">
                            <button type="button" class="weekday-btn" data-day="Segunda">Seg</button>
                            <button type="button" class="weekday-btn" data-day="Terça">Ter</button>
                            <button type="button" class="weekday-btn" data-day="Quarta">Qua</button>
                            <button type="button" class="weekday-btn" data-day="Quinta">Qui</button>
                            <button type="button" class="weekday-btn" data-day="Sexta">Sex</button>
                            <button type="button" class="weekday-btn" data-day="Sábado">Sáb</button>
                            <button type="button" class="weekday-btn" data-day="Domingo">Dom</button>
                        </div>
                        <p class="form-hint">Ao criar, pode selecionar vários dias para gerar horários iguais de uma vez. Na edição, apenas um dia fica selecionado.</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="schedule-start">Início do Atendimento <span class="required">*</span></label>
                            <input type="time" id="schedule-start" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label for="schedule-end">Fim do Atendimento <span class="required">*</span></label>
                            <input type="time" id="schedule-end" class="form-input" required>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="schedule-break-start">Início do Intervalo</label>
                            <input type="time" id="schedule-break-start" class="form-input">
                        </div>
                        <div class="form-group">
                            <label for="schedule-break-end">Fim do Intervalo</label>
                            <input type="time" id="schedule-break-end" class="form-input">
                        </div>
                    </div>
                    <p class="form-hint">Deixe o intervalo em branco se o médico não tiver pausa fixa (ex: almoço) neste horário.</p>
                </fieldset>

                <fieldset class="form-section">
                    <legend>Consultas</legend>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="schedule-duration">Duração da Consulta</label>
                            <select id="schedule-duration" class="form-select">
                                <option value="15">15 minutos</option>
                                <option value="20">20 minutos</option>
                                <option value="30" selected>30 minutos</option>
                                <option value="45">45 minutos</option>
                                <option value="60">60 minutos</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Vagas estimadas</label>
                            <input type="text" id="schedule-slots-preview" class="form-input bg-gray-50" readonly value="—">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="form-section">
                    <legend>Vigência (opcional)</legend>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="form-group">
                            <label for="schedule-start-date">Válido a partir de</label>
                            <input type="date" id="schedule-start-date" class="form-input">
                        </div>
                        <div class="form-group">
                            <label for="schedule-end-date">Válido até</label>
                            <input type="date" id="schedule-end-date" class="form-input">
                        </div>
                    </div>
                    <p class="form-hint">Útil para horários temporários (ex: substituições, campanhas sazonais). Deixe em branco para vigência permanente.</p>
                </fieldset>

                <div class="form-group">
                    <label for="schedule-notes">Observações</label>
                    <textarea id="schedule-notes" class="form-textarea" rows="2" placeholder="Ex: Atende apenas consultas de retorno às sextas."></textarea>
                </div>

                <div class="form-group">
                    <label for="schedule-status">Status</label>
                    <select id="schedule-status" class="form-select">
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                    </select>
                </div>

                <div class="flex gap-3 mt-4">
                    <button type="submit" class="btn btn-success flex-1" id="save-schedule-btn"><i class="fas fa-save mr-1"></i> Salvar</button>
                    <button type="button" class="btn btn-secondary" onclick="closeScheduleModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ==================== UTILITÁRIOS ====================
        function getCsrfToken() {
            const metaToken = document.querySelector('meta[name="csrf-token"]');
            if (metaToken) return metaToken.getAttribute('content');
            const cookies = document.cookie.split(';');
            for (let cookie of cookies) {
                const [name, value] = cookie.trim().split('=');
                if (name === 'csrf_cookie_name') return value;
            }
            return '';
        }

        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            const messageEl = document.getElementById('notification-message');
            if (!notification || !messageEl) return;
            messageEl.innerHTML = message;
            notification.className = `show ${type}`;
            setTimeout(() => { notification.classList.remove('show'); }, 5000);
        }

        function timeToMinutes(t) {
            if (!t) return null;
            const [h, m] = t.split(':').map(Number);
            return h * 60 + m;
        }

        // ==================== SELETOR DE DIAS ====================
        let selectedDays = [];
        let isEditingSingleDay = false;

        document.getElementById('schedule-weekday-selector').addEventListener('click', function(e) {
            const btn = e.target.closest('.weekday-btn');
            if (!btn) return;

            if (isEditingSingleDay) {
                // Em edição, apenas um dia pode ficar selecionado
                document.querySelectorAll('.weekday-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                selectedDays = [btn.dataset.day];
                return;
            }

            btn.classList.toggle('active');
            const day = btn.dataset.day;
            if (selectedDays.includes(day)) {
                selectedDays = selectedDays.filter(d => d !== day);
            } else {
                selectedDays.push(day);
            }
        });

        function setSelectedDays(days) {
            selectedDays = Array.isArray(days) ? days : [days];
            document.querySelectorAll('.weekday-btn').forEach(b => {
                b.classList.toggle('active', selectedDays.includes(b.dataset.day));
            });
        }

        // ==================== PREVIEW DE VAGAS ====================
        function updateSlotsPreview() {
            const start = timeToMinutes(document.getElementById('schedule-start').value);
            const end = timeToMinutes(document.getElementById('schedule-end').value);
            const breakStart = timeToMinutes(document.getElementById('schedule-break-start').value);
            const breakEnd = timeToMinutes(document.getElementById('schedule-break-end').value);
            const duration = parseInt(document.getElementById('schedule-duration').value, 10) || 30;
            const preview = document.getElementById('schedule-slots-preview');

            if (start === null || end === null || end <= start) {
                preview.value = '—';
                return;
            }

            let total = end - start;
            if (breakStart !== null && breakEnd !== null && breakEnd > breakStart) {
                total -= (breakEnd - breakStart);
            }

            const slots = total > 0 ? Math.floor(total / duration) : 0;
            preview.value = slots > 0 ? `${slots} vagas / dia` : '0 vagas / dia';
        }

        ['schedule-start', 'schedule-end', 'schedule-break-start', 'schedule-break-end', 'schedule-duration']
            .forEach(id => document.getElementById(id).addEventListener('input', updateSlotsPreview));

        // ==================== MODAL ====================
        function openScheduleModal(data = null) {
            const modal = document.getElementById('schedule-modal');
            const title = document.getElementById('modal-title');

            if (data) {
                isEditingSingleDay = true;
                title.innerHTML = '<i class="fas fa-edit text-blue-500 mr-2"></i>Editar Horário';
                document.getElementById('schedule-id').value = data.id || '';
                document.getElementById('schedule-doctor').value = data.doctor_id || '';
                document.getElementById('schedule-room').value = data.room || '';
                setSelectedDays(data.day || []);
                document.getElementById('schedule-start').value = data.start || '';
                document.getElementById('schedule-end').value = data.end || '';
                document.getElementById('schedule-break-start').value = data.break_start || '';
                document.getElementById('schedule-break-end').value = data.break_end || '';
                document.getElementById('schedule-duration').value = data.duration || '30';
                document.getElementById('schedule-start-date').value = data.start_date || '';
                document.getElementById('schedule-end-date').value = data.end_date || '';
                document.getElementById('schedule-notes').value = data.notes || '';
                document.getElementById('schedule-status').value = data.status || 'ativo';
            } else {
                isEditingSingleDay = false;
                title.innerHTML = '<i class="fas fa-plus text-blue-500 mr-2"></i>Adicionar Horário';
                document.getElementById('schedule-form').reset();
                document.getElementById('schedule-id').value = '';
                document.getElementById('schedule-duration').value = '30';
                setSelectedDays([]);
            }

            updateSlotsPreview();
            modal.style.display = 'flex';
        }

        function closeScheduleModal() {
            document.getElementById('schedule-modal').style.display = 'none';
        }

        async function saveSchedule(event) {
            event.preventDefault();

            const id = document.getElementById('schedule-id').value;
            const doctorId = document.getElementById('schedule-doctor').value;
            const room = document.getElementById('schedule-room').value.trim();
            const start = document.getElementById('schedule-start').value;
            const end = document.getElementById('schedule-end').value;
            const breakStart = document.getElementById('schedule-break-start').value;
            const breakEnd = document.getElementById('schedule-break-end').value;
            const duration = document.getElementById('schedule-duration').value;
            const startDate = document.getElementById('schedule-start-date').value;
            const endDate = document.getElementById('schedule-end-date').value;
            const notes = document.getElementById('schedule-notes').value.trim();
            const status = document.getElementById('schedule-status').value;

            if (!doctorId || selectedDays.length === 0 || !start || !end) {
                showNotification('Selecione o médico, pelo menos um dia e o horário de atendimento.', 'error');
                return;
            }
            if (start >= end) {
                showNotification('O horário de início deve ser menor que o horário de fim.', 'error');
                return;
            }
            if (breakStart && breakEnd) {
                if (breakStart >= breakEnd) {
                    showNotification('O intervalo tem uma hora de início posterior à hora de fim.', 'error');
                    return;
                }
                if (breakStart < start || breakEnd > end) {
                    showNotification('O intervalo deve estar dentro do horário de atendimento.', 'error');
                    return;
                }
            }
            if (startDate && endDate && startDate > endDate) {
                showNotification('A data de início de vigência deve ser anterior à data de fim.', 'error');
                return;
            }

            const csrfToken = getCsrfToken();
            const btn = document.getElementById('save-schedule-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Salvando...';

            try {
                // Um horário é criado por dia selecionado (permite escolher vários dias de uma vez ao adicionar)
                for (const day of selectedDays) {
                    const formData = new FormData();
                    formData.append('id', id);
                    formData.append('doctor_id', doctorId);
                    formData.append('day', day);
                    formData.append('start', start);
                    formData.append('end', end);
                    formData.append('break_start', breakStart);
                    formData.append('break_end', breakEnd);
                    formData.append('room', room);
                    formData.append('duration', duration);
                    formData.append('start_date', startDate);
                    formData.append('end_date', endDate);
                    formData.append('notes', notes);
                    formData.append('status', status);
                    formData.append('csrf_test_name', csrfToken);

                    const response = await fetch('<?= site_url('admin/save_schedule') ?>', {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: formData
                    });
                    const result = await response.json();
                    if (result.error) throw new Error(result.error);
                }

                showNotification('Horário salvo com sucesso!', 'success');
                closeScheduleModal();
                setTimeout(() => location.reload(), 1200);

            } catch (error) {
                console.error('Erro:', error);
                showNotification(error.message || 'Erro ao salvar horário.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save mr-1"></i> Salvar';
            }
        }

        // ==================== LEITURA DE LINHAS DA TABELA ====================
        function readRowData(row) {
            const cells = row.querySelectorAll('td');
            const dayText = cells[1]?.textContent?.trim() || '';
            const timeMain = cells[2]?.querySelector('.main-time')?.textContent?.trim() || '';
            const [start, end] = timeMain.split('–').map(s => s.trim());
            const breakText = cells[2]?.querySelector('.break-time')?.textContent?.trim() || '';
            let breakStart = '', breakEnd = '';
            const breakMatch = breakText.match(/(\d{2}:\d{2})[–-](\d{2}:\d{2})/);
            if (breakMatch) { breakStart = breakMatch[1]; breakEnd = breakMatch[2]; }
            const room = cells[3]?.textContent?.trim() || '';
            const durationText = cells[4]?.querySelector('div')?.textContent?.trim() || '';
            const durationMatch = durationText.match(/(\d+)/);
            const statusText = cells[6]?.textContent?.trim()?.toLowerCase() || 'ativo';

            return {
                id: row.dataset.id,
                doctor_id: row.dataset.doctorId || '',
                day: [dayText],
                start: start || '',
                end: end || '',
                break_start: breakStart,
                break_end: breakEnd,
                room: room === '—' ? '' : room,
                duration: durationMatch ? durationMatch[1] : '30',
                status: statusText.includes('inativo') ? 'inativo' : 'ativo'
            };
        }

        document.getElementById('schedule-table').addEventListener('click', function(e) {
            const editBtn = e.target.closest('.edit-schedule');
            const dupBtn = e.target.closest('.duplicate-schedule');
            const delBtn = e.target.closest('.delete-schedule');

            if (editBtn) {
                const row = editBtn.closest('tr');
                openScheduleModal(readRowData(row));
            }

            if (dupBtn) {
                const row = dupBtn.closest('tr');
                const data = readRowData(row);
                data.id = '';
                openScheduleModal(data);
                isEditingSingleDay = false; // permite escolher outros dias ao duplicar
                showNotification('Horário duplicado — ajuste o dia ou os dados e salve.', 'info');
            }

            if (delBtn) {
                const id = delBtn.dataset.id;
                if (!confirm('Tem certeza que deseja excluir este horário?')) return;

                const csrfToken = getCsrfToken();
                const formData = new FormData();
                formData.append('id', id);
                formData.append('csrf_test_name', csrfToken);

                fetch('<?= site_url('admin/delete_schedule') ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(r => r.json())
                .then(result => {
                    if (result.error) {
                        showNotification(result.error, 'error');
                    } else {
                        showNotification(result.success || 'Horário excluído com sucesso!', 'success');
                        const row = delBtn.closest('tr');
                        if (row) row.remove();
                        recalcMetricsFromTable();
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showNotification('Erro ao excluir horário.', 'error');
                });
            }
        });

        document.querySelectorAll('#add-schedule-btn, #add-schedule-empty-btn').forEach(btn => {
            btn.addEventListener('click', () => openScheduleModal());
        });

        // ==================== FILTROS ====================
        function applyFilters() {
            const doctor = document.getElementById('filter-doctor');
            const day = document.getElementById('filter-day').value;
            const status = document.getElementById('filter-status').value;
            const search = document.getElementById('filter-search').value.trim().toLowerCase();
            const selectedDoctorText = doctor.selectedOptions[0]?.text?.trim().toLowerCase() || '';

            const rows = document.querySelectorAll('#schedule-table tr[data-id]');
            let visibleCount = 0;

            rows.forEach(row => {
                const doctorCell = row.querySelector('td:first-child')?.textContent?.toLowerCase() || '';
                const dayCell = row.querySelector('td:nth-child(2)')?.textContent || '';
                const roomCell = row.querySelector('td:nth-child(4)')?.textContent?.toLowerCase() || '';
                const statusCell = row.querySelector('.status-badge')?.textContent?.trim().toLowerCase() || '';

                let show = true;
                if (doctor.value && !doctorCell.includes(selectedDoctorText)) show = false;
                if (day && !dayCell.includes(day)) show = false;
                if (status && !statusCell.includes(status)) show = false;
                if (search && !roomCell.includes(search)) show = false;

                row.style.display = show ? '' : 'none';
                if (show) visibleCount++;
            });

            const countEl = document.getElementById('schedule-count');
            if (countEl) countEl.textContent = `${visibleCount} horário(s) exibido(s)`;
        }

        document.getElementById('apply-filter-btn').addEventListener('click', applyFilters);
        document.getElementById('clear-filter-btn').addEventListener('click', function() {
            document.getElementById('filter-doctor').value = '';
            document.getElementById('filter-day').value = '';
            document.getElementById('filter-status').value = '';
            document.getElementById('filter-search').value = '';
            applyFilters();
        });

        // ==================== MÉTRICAS E GRÁFICO (calculados a partir da tabela renderizada) ====================
        let coverageChart;
        const DAY_ORDER = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];
        const DAY_SHORT = { Segunda: 'Seg', Terça: 'Ter', Quarta: 'Qua', Quinta: 'Qui', Sexta: 'Sex', Sábado: 'Sáb', Domingo: 'Dom' };

        function recalcMetricsFromTable() {
            const rows = Array.from(document.querySelectorAll('#schedule-table tr[data-id]'));
            const activeDoctors = new Set();
            let totalMinutes = 0;
            let totalSlots = 0;
            let inactiveCount = 0;
            const hoursByDay = Object.fromEntries(DAY_ORDER.map(d => [d, 0]));

            rows.forEach(row => {
                const statusText = row.querySelector('.status-badge')?.textContent?.trim().toLowerCase() || '';
                const isActive = statusText.includes('ativo') && !statusText.includes('inativo');
                if (!isActive) { inactiveCount++; return; }

                const data = readRowData(row);
                const start = timeToMinutes(data.start);
                const end = timeToMinutes(data.end);
                if (start === null || end === null || end <= start) return;

                let minutes = end - start;
                const bs = timeToMinutes(data.break_start);
                const be = timeToMinutes(data.break_end);
                if (bs !== null && be !== null && be > bs) minutes -= (be - bs);

                totalMinutes += minutes;
                const duration = parseInt(data.duration, 10) || 30;
                totalSlots += minutes > 0 ? Math.floor(minutes / duration) : 0;

                if (data.doctor_id) activeDoctors.add(data.doctor_id);
                else activeDoctors.add(row.querySelector('.font-medium')?.textContent?.trim());

                const dayCell = row.querySelector('td:nth-child(2)')?.textContent || '';
                const matchedDay = DAY_ORDER.find(d => dayCell.includes(d));
                if (matchedDay) hoursByDay[matchedDay] += minutes / 60;
            });

            document.getElementById('metric-doctors-scheduled').textContent = activeDoctors.size;
            document.getElementById('metric-weekly-hours').textContent = Math.round(totalMinutes / 60) + 'h';
            document.getElementById('metric-daily-slots').textContent = totalSlots;
            document.getElementById('metric-inactive').textContent = inactiveCount;

            renderCoverageChart(hoursByDay);
        }

        function renderCoverageChart(hoursByDay) {
            const ctx = document.getElementById('chart-coverage');
            const values = DAY_ORDER.map(d => Math.round(hoursByDay[d] * 10) / 10);
            const max = Math.max(...values, 0);
            const badge = document.getElementById('coverage-gap-badge');

            if (max > 0) {
                const minDay = DAY_ORDER[values.indexOf(Math.min(...values.filter(v => v >= 0)))];
                const lowest = Math.min(...values);
                if (badge) badge.textContent = lowest === max ? 'Cobertura equilibrada' : `Menor cobertura: ${minDay}`;
            } else if (badge) {
                badge.textContent = 'Sem horários ativos';
            }

            if (coverageChart) coverageChart.destroy();
            coverageChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: DAY_ORDER.map(d => DAY_SHORT[d]),
                    datasets: [{
                        label: 'Horas cobertas',
                        data: values,
                        backgroundColor: '#2563eb',
                        borderRadius: 6,
                        maxBarThickness: 46
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { color: '#f1f5f9' }, title: { display: true, text: 'horas' } },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        // ==================== SIDEBAR ====================
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('notification-close').addEventListener('click', function() {
                document.getElementById('notification').classList.remove('show');
            });

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
                    setTimeout(() => coverageChart && coverageChart.resize(), 320);
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
                const isModalOpen = document.getElementById('schedule-modal').style.display === 'flex';

                if (!isClickInsideSidebar && !isClickOnMenuBtn && isSidebarOpen && !isModalOpen) {
                    sidebarMenu.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                    pageWrapper.classList.remove('expanded');
                }
            });

            document.getElementById('logout-btn').addEventListener('click', function() {
                if (confirm('Tem certeza que deseja sair?')) {
                    window.location.href = SITE_URL + '/auth/logout';
                }
            });

            recalcMetricsFromTable();
        });
    </script>
</body>
</html>