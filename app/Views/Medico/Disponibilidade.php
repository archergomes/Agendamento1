<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disponibilidade - Médico - Centro de Saúde Da Matola II</title>
    <meta name="description" content="Gerencie seus horários de atendimento">
    <meta name="csrf-token" content="<?= csrf_hash(); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <script>
        var BASE_URL = '<?= rtrim(base_url(), '/'); ?>';
        var SITE_URL = '<?= rtrim(site_url(), '/'); ?>';
        var AJAX_URL = SITE_URL;
        var DOCTOR_NAME = '<?= addslashes(($medico->Nome ?? '') . ' ' . ($medico->Sobrenome ?? '')); ?>';
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

        .sidebar-profile {
            display: flex; align-items: center; gap: 0.7rem;
            padding: 0.9rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.12);
        }
        .sidebar.desktop .sidebar-profile { justify-content: center; padding: 0.9rem; }
        .sidebar.desktop.expanded .sidebar-profile { justify-content: flex-start; padding: 0.9rem 1.1rem; }
        .sidebar-profile .avatar {
            width: 38px; height: 38px; border-radius: 50%;
            background: rgba(255,255,255,0.15);
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
            border: 2px solid rgba(255,255,255,0.3);
        }
        .sidebar-profile .info { color: white; overflow: hidden; }
        .sidebar-profile .info .name { font-size: 0.85rem; font-weight: 600; white-space: nowrap; text-overflow: ellipsis; overflow: hidden; }
        .sidebar-profile .info .specialty { font-size: 0.7rem; color: rgba(255,255,255,0.65); white-space: nowrap; }

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

        .sidebar-nav { display: flex; flex-direction: column; height: calc(100% - 64px - 62px); padding: 0.5rem; }
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

        /* Cards */
        .card-panel {
            background-color: white;
            border-radius: 0.75rem;
            border: 1px solid #eef1f6;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            padding: 1.5rem;
        }

        .btn {
            padding: 0.6rem 1.1rem;
            border-radius: 0.55rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary { background-color: white; color: var(--brand-700); border: 1px solid #d8dee8; }
        .btn-primary:hover { background-color: #f1f5f9; transform: translateY(-1px); }
        .btn-success { background-color: var(--teal-500); color: white; }
        .btn-success:hover { background-color: var(--teal-600); transform: translateY(-1px); }
        .btn-secondary { background-color: #e5e7eb; color: #374151; }
        .btn-secondary:hover { background-color: #d1d5db; }
        .btn-danger { background-color: #ef4444; color: white; }
        .btn-danger:hover { background-color: #dc2626; transform: translateY(-1px); }
        .btn:disabled { opacity: .55; cursor: not-allowed; transform: none !important; }

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
        .btn-delete { background-color: #ef4444; color: white; }
        .btn-delete:hover { background-color: #dc2626; }

        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        thead th {
            background-color: #f3f5f9;
            padding: 0.75rem 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--ink-700);
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
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
        .status-badge.bloqueio { background-color: #fef3c7; color: #92400e; }

        .empty-state { text-align: center; padding: 2.5rem 1rem; color: #6b7280; }
        .empty-state i { font-size: 2.2rem; color: #d1d5db; margin-bottom: 0.5rem; }

        .loading-spinner {
            display: inline-block; width: 1.3rem; height: 1.3rem;
            border: 3px solid #e5e7eb;
            border-top-color: var(--brand-500);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Weekday Selector */
        .weekday-selector {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
            align-items: center;
        }
        .weekday-btn {
            padding: 0.4rem 0.8rem;
            border-radius: 0.375rem;
            border: 2px solid #e5e7eb;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .weekday-btn:hover { border-color: var(--brand-500); background: #eff6ff; }
        .weekday-btn.active { border-color: var(--brand-500); background: var(--brand-500); color: white; }

        /* View toggle (Lista / Calendário) */
        .view-toggle { display: inline-flex; border: 1.5px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden; margin-left: auto; }
        .view-toggle button {
            padding: 0.4rem 0.75rem; border: none; background: white; color: var(--ink-500);
            font-size: 0.75rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.35rem;
        }
        .view-toggle button.active { background: var(--brand-500); color: white; }
        .view-toggle button + button { border-left: 1.5px solid #e5e7eb; }

        /* Calendário semanal */
        .cal-grid { display: grid; grid-template-columns: repeat(7, minmax(150px, 1fr)); gap: 0.65rem; overflow-x: auto; padding-bottom: 0.25rem; }
        .cal-day { background: var(--paper); border-radius: 0.6rem; padding: 0.7rem; min-height: 150px; }
        .cal-day__header { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .02em; color: var(--ink-700); text-align: center; padding-bottom: 0.5rem; margin-bottom: 0.5rem; border-bottom: 1px solid #e5e7eb; }
        .cal-block { background: white; border: 1px solid #e5e7eb; border-left: 3px solid var(--teal-500); border-radius: 0.45rem; padding: 0.5rem 0.6rem; margin-bottom: 0.45rem; font-size: 0.72rem; }
        .cal-block.inativo { border-left-color: #d1d5db; opacity: .65; }
        .cal-block__time { font-weight: 700; color: var(--ink-900); }
        .cal-block__meta { color: var(--ink-500); font-size: 0.65rem; margin-top: 0.15rem; }
        .cal-block__actions { display: flex; gap: 0.3rem; margin-top: 0.4rem; }
        .cal-block__actions button { padding: 0.2rem 0.4rem; font-size: 0.65rem; }
        .cal-empty { font-size: 0.68rem; color: #9ca3af; text-align: center; padding: 1rem 0; }

        /* Chips de dias (duplicar horário) */
        .day-checks { display: flex; flex-wrap: wrap; gap: 0.4rem; }
        .day-check-chip input { display: none; }
        .day-check-chip span {
            display: inline-block; padding: 0.35rem 0.7rem; border-radius: 20px;
            border: 1.5px solid #e5e7eb; font-size: 0.72rem; font-weight: 600; color: var(--ink-500); cursor: pointer;
        }
        .day-check-chip input:checked + span { background: var(--brand-500); border-color: var(--brand-500); color: white; }

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(15, 23, 42, 0.55);
            z-index: 950;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }
        .modal-overlay.show { display: flex; animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .modal-content {
            background-color: white;
            padding: 1.5rem;
            border-radius: 0.85rem;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 24px 60px rgba(0,0,0,0.35);
            animation: slideUp 0.3s ease-out;
            max-height: 90vh;
            overflow-y: auto;
        }
        @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 1rem;
            margin-bottom: 1.2rem;
        }
        .modal-header h3 { font-size: 1.15rem; font-weight: 600; color: #1f2937; }
        .modal-close {
            background: none; border: none; font-size: 1.5rem; color: #6b7280; cursor: pointer;
            padding: 0.25rem 0.5rem; border-radius: 0.25rem;
        }
        .modal-close:hover { color: #1f2937; background-color: #f3f4f6; }

        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-weight: 500; color: #374151; margin-bottom: 0.25rem; font-size: 0.875rem; }
        .form-group label .required { color: #ef4444; }
        .form-group .hint { font-size: 0.72rem; color: var(--ink-500); margin-top: 0.25rem; }

        .form-input, .form-select {
            width: 100%;
            padding: 0.55rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            transition: border-color 0.2s;
            font-size: 0.85rem;
            background: white;
        }
        .form-input:focus, .form-select:focus {
            outline: none; border-color: var(--brand-500); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }
        .form-input:disabled { background: #f3f4f6; color: #9ca3af; }

        .checkbox-row { display: flex; align-items: center; gap: 0.5rem; }
        .checkbox-row input { width: 16px; height: 16px; }

        .pulse-line { width: 120px; height: 34px; opacity: 0.9; }
        .pulse-path { stroke-dasharray: 300; stroke-dashoffset: 300; animation: draw-pulse 3.2s ease-in-out infinite; }
        @keyframes draw-pulse {
            0% { stroke-dashoffset: 300; }
            55% { stroke-dashoffset: 0; }
            100% { stroke-dashoffset: -300; }
        }
        @media (prefers-reduced-motion: reduce) { .pulse-path { animation: none; stroke-dashoffset: 0; } }

        @media (max-width: 640px) {
            .main-content { padding: 0.5rem; }
            table { font-size: 0.75rem; }
            thead th, tbody td { padding: 0.55rem; }
            .pulse-line { display: none; }
            .modal-content { padding: 1rem; }
            .weekday-btn { font-size: 0.65rem; padding: 0.25rem 0.6rem; }
            .weekday-selector { justify-content: flex-start; }
            .view-toggle { margin-left: 0; width: 100%; }
            .view-toggle button { flex: 1; justify-content: center; }
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
            <h2 class="text-lg font-semibold sidebar-text">Área do Médico</h2>
            <button id="toggle-sidebar-btn" aria-label="Alternar menu">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <button id="close-sidebar-btn" class="md:hidden close-sidebar-btn" aria-label="Fechar menu">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="sidebar-profile">
            <div class="avatar"><?= strtoupper(substr($medico->Nome ?? 'M', 0, 1)); ?></div>
            <div class="info">
                <div class="name">Dr(a). <?= htmlspecialchars(($medico->Nome ?? '') . ' ' . ($medico->Sobrenome ?? '')); ?></div>
                <div class="specialty"><?= htmlspecialchars($medico->Especialidade ?? 'Médico'); ?></div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="main-menu">
                <a href="<?= site_url('medico') ?>">
                    <i class="fas fa-chart-pie"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
                <a href="<?= site_url('medico/agenda') ?>">
                    <i class="fas fa-calendar-alt"></i>
                    <span class="sidebar-text">Minha Agenda</span>
                </a>
                <a href="<?= site_url('medico/pacientes') ?>">
                    <i class="fas fa-users"></i>
                    <span class="sidebar-text">Meus Pacientes</span>
                </a>
                <a href="<?= site_url('medico/disponibilidade') ?>" class="active">
                    <i class="fas fa-clock"></i>
                    <span class="sidebar-text">Disponibilidade</span>
                </a>
                <a href="<?= site_url('medico/historico') ?>">
                    <i class="fas fa-history"></i>
                    <span class="sidebar-text">Histórico</span>
                </a>
                <a href="<?= site_url('medico/perfil') ?>">
                    <i class="fas fa-user-circle"></i>
                    <span class="sidebar-text">Meu Perfil</span>
                </a>
            </div>
            <button id="logout-btn" class="logout">
                <i class="fas fa-sign-out-alt"></i>
                <span class="sidebar-text">Sair</span>
            </button>
        </nav>
    </div>

    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <header class="text-white shadow-lg">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <i class="fas fa-hospital-alt text-2xl"></i>
                    <div>
                        <h1 class="text-xl font-bold leading-tight">Centro de Saúde Da Matola II</h1>
                        <p class="text-xs text-blue-100 opacity-90">Área do Médico</p>
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
                        <h2 class="text-2xl font-semibold text-gray-800">Minha Disponibilidade</h2>
                        <p class="text-gray-500 text-sm">Gerencie seus horários de atendimento e ausências</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn btn-secondary" id="add-block-btn">
                            <i class="fas fa-calendar-times"></i> Bloquear Data
                        </button>
                        <button class="btn btn-success" id="add-schedule-btn">
                            <i class="fas fa-plus"></i> Adicionar Horário
                        </button>
                    </div>
                </div>

                <!-- Horário semanal -->
                <div class="card-panel mb-6">
                    <div class="weekday-selector">
                        <button class="weekday-btn active" data-day="all">Todos</button>
                        <button class="weekday-btn" data-day="Segunda">Segunda</button>
                        <button class="weekday-btn" data-day="Terça">Terça</button>
                        <button class="weekday-btn" data-day="Quarta">Quarta</button>
                        <button class="weekday-btn" data-day="Quinta">Quinta</button>
                        <button class="weekday-btn" data-day="Sexta">Sexta</button>
                        <button class="weekday-btn" data-day="Sábado">Sábado</button>
                        <button class="weekday-btn" data-day="Domingo">Domingo</button>

                        <div class="view-toggle">
                            <button data-view="list" class="active"><i class="fas fa-list"></i> Lista</button>
                            <button data-view="calendar"><i class="fas fa-calendar-week"></i> Calendário</button>
                        </div>
                    </div>

                    <!-- Vista em lista -->
                    <div id="view-list">
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Dia da Semana</th>
                                        <th>Início</th>
                                        <th>Fim</th>
                                        <th>Duração consulta</th>
                                        <th>Vagas</th>
                                        <th>Status</th>
                                        <th class="text-center">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="schedule-list">
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-gray-500">
                                            <span class="loading-spinner"></span> Carregando horários...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Vista em calendário -->
                    <div id="view-calendar" style="display:none;">
                        <div class="cal-grid" id="cal-grid"></div>
                    </div>
                </div>

                <!-- Bloqueios de agenda -->
                <div class="card-panel">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Bloqueios de Agenda</h3>
                            <p class="text-gray-500 text-xs">Férias, feriados ou ausências pontuais — nestas datas não é possível marcar consultas.</p>
                        </div>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Motivo</th>
                                    <th>Período</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="block-list">
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-gray-500">
                                        <span class="loading-spinner"></span> Carregando bloqueios...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Adicionar/Editar Horário -->
    <div id="schedule-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modal-title"><i class="fas fa-clock text-blue-500 mr-2"></i>Adicionar Horário</h3>
                <button class="modal-close" onclick="closeScheduleModal()">&times;</button>
            </div>
            <form id="schedule-form" onsubmit="saveSchedule(event)">
                <input type="hidden" id="schedule-id" name="id">
                <input type="hidden" id="schedule-mode" value="create">

                <div class="form-group">
                    <label for="schedule-day">Dia da Semana <span class="required">*</span></label>
                    <select id="schedule-day" class="form-select" required>
                        <option value="">Selecione um dia</option>
                        <option value="Segunda">Segunda-feira</option>
                        <option value="Terça">Terça-feira</option>
                        <option value="Quarta">Quarta-feira</option>
                        <option value="Quinta">Quinta-feira</option>
                        <option value="Sexta">Sexta-feira</option>
                        <option value="Sábado">Sábado</option>
                        <option value="Domingo">Domingo</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="schedule-start">Hora Início <span class="required">*</span></label>
                        <input type="time" id="schedule-start" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="schedule-end">Hora Fim <span class="required">*</span></label>
                        <input type="time" id="schedule-end" class="form-input" required>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="schedule-duration">Duração da consulta</label>
                        <select id="schedule-duration" class="form-select">
                            <option value="15">15 minutos</option>
                            <option value="20">20 minutos</option>
                            <option value="30" selected>30 minutos</option>
                            <option value="45">45 minutos</option>
                            <option value="60">60 minutos</option>
                        </select>
                        <div class="hint" id="schedule-vagas-hint"></div>
                    </div>
                    <div class="form-group">
                        <label for="schedule-status">Status</label>
                        <select id="schedule-status" class="form-select">
                            <option value="ativo">Ativo</option>
                            <option value="inativo">Inativo</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" id="duplicate-days-group">
                    <label>Duplicar este horário também para</label>
                    <div class="day-checks">
                        <label class="day-check-chip"><input type="checkbox" value="Segunda"><span>Segunda</span></label>
                        <label class="day-check-chip"><input type="checkbox" value="Terça"><span>Terça</span></label>
                        <label class="day-check-chip"><input type="checkbox" value="Quarta"><span>Quarta</span></label>
                        <label class="day-check-chip"><input type="checkbox" value="Quinta"><span>Quinta</span></label>
                        <label class="day-check-chip"><input type="checkbox" value="Sexta"><span>Sexta</span></label>
                        <label class="day-check-chip"><input type="checkbox" value="Sábado"><span>Sábado</span></label>
                        <label class="day-check-chip"><input type="checkbox" value="Domingo"><span>Domingo</span></label>
                    </div>
                    <div class="hint">Cria uma cópia deste horário nos dias seleccionados (útil para Seg–Sex iguais).</div>
                </div>

                <div class="flex gap-3 mt-4">
                    <button type="submit" class="btn btn-success flex-1" id="save-schedule-btn">
                        <i class="fas fa-save mr-1"></i> Salvar
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeScheduleModal()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Bloquear Data -->
    <div id="block-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-calendar-times text-amber-500 mr-2"></i>Bloquear Data</h3>
                <button class="modal-close" onclick="closeModal('block-modal')">&times;</button>
            </div>
            <form id="block-form" onsubmit="saveBlock(event)">
                <div class="form-group">
                    <label for="block-date">Data <span class="required">*</span></label>
                    <input type="date" id="block-date" class="form-input" required>
                </div>

                <div class="form-group">
                    <label for="block-reason">Motivo <span class="required">*</span></label>
                    <select id="block-reason" class="form-select" required>
                        <option value="">Seleccione o motivo</option>
                        <option value="Férias">Férias</option>
                        <option value="Feriado">Feriado</option>
                        <option value="Ausência médica">Ausência médica</option>
                        <option value="Formação/Congresso">Formação/Congresso</option>
                        <option value="Outro">Outro</option>
                    </select>
                </div>

                <div class="form-group">
                    <div class="checkbox-row">
                        <input type="checkbox" id="block-full-day" checked>
                        <label for="block-full-day" style="margin:0;">Bloquear o dia inteiro</label>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4" id="block-time-fields" style="display:none;">
                    <div class="form-group">
                        <label for="block-start">Das</label>
                        <input type="time" id="block-start" class="form-input">
                    </div>
                    <div class="form-group">
                        <label for="block-end">Até</label>
                        <input type="time" id="block-end" class="form-input">
                    </div>
                </div>

                <div class="flex gap-3 mt-4">
                    <button type="submit" class="btn btn-success flex-1">
                        <i class="fas fa-save mr-1"></i> Guardar Bloqueio
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('block-modal')">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ==================== CSRF ====================
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

        // ==================== NOTIFICAÇÕES ====================
        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            const messageEl = document.getElementById('notification-message');
            if (!notification || !messageEl) return;
            messageEl.innerHTML = message;
            notification.className = `show ${type}`;
            setTimeout(() => { notification.classList.remove('show'); }, 5000);
        }

        function openModal(id) {
            document.getElementById(id).classList.add('show');
            document.body.style.overflow = 'hidden';
        }
        function closeModal(id) {
            document.getElementById(id).classList.remove('show');
            document.body.style.overflow = 'auto';
        }
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function (e) { if (e.target === this) closeModal(this.id); });
        });

        // Guarda os dados já carregados (corrige o bug de editar a partir do texto da tabela)
        let lastSchedules = [];
        let lastBlocks = [];

        const WEEKDAYS = ['Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'];

        function calcVagas(start, end, duration) {
            if (!start || !end || !duration) return 0;
            const [sh, sm] = start.split(':').map(Number);
            const [eh, em] = end.split(':').map(Number);
            const minutes = (eh * 60 + em) - (sh * 60 + sm);
            if (minutes <= 0) return 0;
            return Math.floor(minutes / Number(duration));
        }

        // ==================== MODAL HORÁRIO ====================
        function openScheduleModal(data = null) {
            const title = document.getElementById('modal-title');
            const duplicateGroup = document.getElementById('duplicate-days-group');

            if (data) {
                title.innerHTML = '<i class="fas fa-edit text-blue-500 mr-2"></i>Editar Horário';
                document.getElementById('schedule-mode').value = 'edit';
                document.getElementById('schedule-id').value = data.ID_Horario || '';
                document.getElementById('schedule-day').value = data.Dia_Semana || '';
                document.getElementById('schedule-start').value = data.Hora_Inicio ? data.Hora_Inicio.substring(0, 5) : '';
                document.getElementById('schedule-end').value = data.Hora_Fim ? data.Hora_Fim.substring(0, 5) : '';
                document.getElementById('schedule-duration').value = data.Duracao_Consulta || 30;
                document.getElementById('schedule-status').value = (data.Status || 'ativo').toLowerCase();
                // Duplicar para outros dias só faz sentido ao criar, não ao editar um registo já existente
                duplicateGroup.style.display = 'none';
            } else {
                title.innerHTML = '<i class="fas fa-plus text-blue-500 mr-2"></i>Adicionar Horário';
                document.getElementById('schedule-form').reset();
                document.getElementById('schedule-mode').value = 'create';
                document.getElementById('schedule-id').value = '';
                document.getElementById('schedule-duration').value = 30;
                document.getElementById('schedule-status').value = 'ativo';
                duplicateGroup.style.display = 'block';
                document.querySelectorAll('#duplicate-days-group input[type=checkbox]').forEach(cb => cb.checked = false);
            }
            updateVagasHint();
            openModal('schedule-modal');
        }
        function closeScheduleModal() { closeModal('schedule-modal'); }

        function updateVagasHint() {
            const start = document.getElementById('schedule-start').value;
            const end = document.getElementById('schedule-end').value;
            const duration = document.getElementById('schedule-duration').value;
            const vagas = calcVagas(start, end, duration);
            document.getElementById('schedule-vagas-hint').textContent =
                vagas > 0 ? `≈ ${vagas} vaga${vagas > 1 ? 's' : ''} de consulta neste intervalo` : '';
        }
        ['schedule-start', 'schedule-end', 'schedule-duration'].forEach(id => {
            document.getElementById(id).addEventListener('input', updateVagasHint);
            document.getElementById(id).addEventListener('change', updateVagasHint);
        });

        document.getElementById('add-schedule-btn').addEventListener('click', () => openScheduleModal());

        // ==================== SALVAR HORÁRIO (com duplicação opcional) ====================
        async function saveSchedule(event) {
            event.preventDefault();

            const mode = document.getElementById('schedule-mode').value;
            const id = document.getElementById('schedule-id').value;
            const day = document.getElementById('schedule-day').value;
            const start = document.getElementById('schedule-start').value;
            const end = document.getElementById('schedule-end').value;
            const duration = document.getElementById('schedule-duration').value;
            const status = document.getElementById('schedule-status').value;

            if (!day || !start || !end) { showNotification('Preencha todos os campos obrigatórios.', 'error'); return; }
            if (start >= end) { showNotification('O horário de início deve ser menor que o horário de fim.', 'error'); return; }

            let days = [day];
            if (mode === 'create') {
                document.querySelectorAll('#duplicate-days-group input[type=checkbox]:checked').forEach(cb => {
                    if (!days.includes(cb.value)) days.push(cb.value);
                });
            }

            const btn = document.getElementById('save-schedule-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Salvando...';

            try {
                // Para cada dia seleccionado cria/actualiza um registo.
                // TODO API: /medico/save_schedule já deve aceitar o campo "duration" (minutos).
                for (const d of days) {
                    const formData = new FormData();
                    formData.append('id', mode === 'edit' ? id : '');
                    formData.append('day', d);
                    formData.append('start', start);
                    formData.append('end', end);
                    formData.append('duration', duration);
                    formData.append('status', status);
                    formData.append('csrf_test_name', getCsrfToken());

                    const response = await fetch(AJAX_URL + '/medico/save_schedule', {
                        method: 'POST',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        body: formData
                    });
                    const result = await response.json();
                    if (result.error) throw new Error(result.error);
                }

                showNotification(days.length > 1 ? `Horário guardado em ${days.length} dias com sucesso!` : 'Horário salvo com sucesso!', 'success');
                closeScheduleModal();
                loadSchedules(document.querySelector('.weekday-btn.active')?.dataset.day || 'all');
            } catch (error) {
                console.error('Erro ao salvar horário:', error);
                showNotification(error.message || 'Erro ao salvar horário.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save mr-1"></i> Salvar';
            }
        }

        // ==================== CARREGAR HORÁRIOS ====================
        let currentView = 'list';

        async function loadSchedules(filterDay = 'all') {
            const list = document.getElementById('schedule-list');
            list.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-gray-500">
                <span class="loading-spinner"></span> Carregando horários...
            </td></tr>`;

            try {
                const response = await fetch(AJAX_URL + '/medico/get_schedules', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                if (!response.ok) throw new Error('HTTP ' + response.status);

                const data = await response.json();
                lastSchedules = data || [];

                const filteredData = filterDay === 'all' ? lastSchedules : lastSchedules.filter(item => item.Dia_Semana === filterDay);

                renderScheduleTable(filteredData, filterDay);
                renderScheduleCalendar(lastSchedules);

            } catch (error) {
                console.error('Erro ao carregar horários:', error);
                list.innerHTML = `<tr>
                    <td colspan="7" class="text-center py-4 text-red-500">
                        <i class="fas fa-exclamation-circle text-2xl block mb-2"></i>
                        Erro ao carregar horários: ${error.message}
                    </td>
                </tr>`;
                showNotification('Erro ao carregar horários.', 'error');
            }
        }

        function renderScheduleTable(items, filterDay) {
            const list = document.getElementById('schedule-list');
            if (!items || items.length === 0) {
                list.innerHTML = `<tr>
                    <td colspan="7" class="text-center py-4 text-gray-500">
                        <i class="fas fa-clock text-2xl block mb-2 text-gray-300"></i>
                        ${filterDay === 'all' ? 'Nenhum horário cadastrado.' : 'Nenhum horário para este dia.'}
                    </td>
                </tr>`;
                return;
            }

            list.innerHTML = items.map(item => {
                const statusClass = (item.Status || 'ativo').toLowerCase();
                const statusLabel = statusClass === 'ativo' ? 'Ativo' : 'Inativo';
                const start = item.Hora_Inicio ? item.Hora_Inicio.substring(0, 5) : '';
                const end = item.Hora_Fim ? item.Hora_Fim.substring(0, 5) : '';
                const duration = item.Duracao_Consulta || 30;
                const vagas = calcVagas(start, end, duration);

                return `
                    <tr>
                        <td class="font-medium">${item.Dia_Semana || '—'}</td>
                        <td>${start || '—'}</td>
                        <td>${end || '—'}</td>
                        <td>${duration} min</td>
                        <td>${vagas || '—'}</td>
                        <td>
                            <span class="status-badge ${statusClass}">
                                <i class="fas fa-circle" style="font-size:0.4rem;"></i>
                                ${statusLabel}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="flex justify-center gap-1">
                                <button class="btn-sm btn-edit" data-id="${item.ID_Horario}" onclick="editSchedule(${item.ID_Horario})" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-sm btn-delete" data-id="${item.ID_Horario}" onclick="deleteSchedule(${item.ID_Horario})" title="Excluir">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        function renderScheduleCalendar(items) {
            const grid = document.getElementById('cal-grid');
            grid.innerHTML = WEEKDAYS.map(day => {
                const dayItems = (items || [])
                    .filter(i => i.Dia_Semana === day)
                    .sort((a, b) => (a.Hora_Inicio || '').localeCompare(b.Hora_Inicio || ''));

                const blocks = dayItems.length ? dayItems.map(item => {
                    const statusClass = (item.Status || 'ativo').toLowerCase();
                    const start = item.Hora_Inicio ? item.Hora_Inicio.substring(0, 5) : '';
                    const end = item.Hora_Fim ? item.Hora_Fim.substring(0, 5) : '';
                    const duration = item.Duracao_Consulta || 30;
                    const vagas = calcVagas(start, end, duration);
                    return `
                        <div class="cal-block ${statusClass}">
                            <div class="cal-block__time">${start}–${end}</div>
                            <div class="cal-block__meta">${duration} min · ${vagas} vaga${vagas !== 1 ? 's' : ''}</div>
                            <div class="cal-block__actions">
                                <button class="btn-sm btn-edit" onclick="editSchedule(${item.ID_Horario})"><i class="fas fa-edit"></i></button>
                                <button class="btn-sm btn-delete" onclick="deleteSchedule(${item.ID_Horario})"><i class="fas fa-trash"></i></button>
                            </div>
                        </div>`;
                }).join('') : `<div class="cal-empty">Sem horário</div>`;

                return `<div class="cal-day"><div class="cal-day__header">${day}</div>${blocks}</div>`;
            }).join('');
        }

        // ==================== EDITAR / EXCLUIR (usa dados em memória — corrige o bug antigo) ====================
        function editSchedule(id) {
            const data = lastSchedules.find(item => String(item.ID_Horario) === String(id));
            if (!data) { showNotification('Erro ao carregar dados do horário.', 'error'); return; }
            openScheduleModal(data);
        }

        function deleteSchedule(id) {
            if (!confirm('Tem certeza que deseja excluir este horário?')) return;

            const formData = new FormData();
            formData.append('id', id);
            formData.append('csrf_test_name', getCsrfToken());

            fetch(AJAX_URL + '/medico/delete_schedule', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    showNotification(data.error, 'error');
                } else {
                    showNotification(data.success || 'Horário excluído com sucesso!', 'success');
                    loadSchedules(document.querySelector('.weekday-btn.active')?.dataset.day || 'all');
                }
            })
            .catch(error => {
                console.error('Erro ao excluir horário:', error);
                showNotification('Erro ao excluir horário.', 'error');
            });
        }

        // ==================== BLOQUEIOS DE AGENDA ====================
        document.getElementById('add-block-btn').addEventListener('click', () => {
            document.getElementById('block-form').reset();
            document.getElementById('block-full-day').checked = true;
            document.getElementById('block-time-fields').style.display = 'none';
            openModal('block-modal');
        });

        document.getElementById('block-full-day').addEventListener('change', function () {
            document.getElementById('block-time-fields').style.display = this.checked ? 'none' : 'grid';
        });

        async function saveBlock(event) {
            event.preventDefault();

            const date = document.getElementById('block-date').value;
            const reason = document.getElementById('block-reason').value;
            const fullDay = document.getElementById('block-full-day').checked;
            const start = fullDay ? '' : document.getElementById('block-start').value;
            const end = fullDay ? '' : document.getElementById('block-end').value;

            if (!date || !reason) { showNotification('Preencha a data e o motivo do bloqueio.', 'error'); return; }
            if (!fullDay && (!start || !end)) { showNotification('Indique a hora de início e fim do bloqueio.', 'error'); return; }

            const formData = new FormData();
            formData.append('date', date);
            formData.append('reason', reason);
            formData.append('full_day', fullDay ? '1' : '0');
            formData.append('start', start);
            formData.append('end', end);
            formData.append('csrf_test_name', getCsrfToken());

            try {
                // TODO API: POST /medico/save_schedule_block
                const response = await fetch(AJAX_URL + '/medico/save_schedule_block', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                const result = await response.json();
                if (result.error) {
                    showNotification(result.error, 'error');
                } else {
                    showNotification(result.success || 'Bloqueio guardado com sucesso!', 'success');
                    closeModal('block-modal');
                    loadBlocks();
                }
            } catch (error) {
                console.error('Erro ao guardar bloqueio:', error);
                showNotification('Erro ao guardar bloqueio. Verifique se o endpoint do backend já existe.', 'error');
            }
        }

        async function loadBlocks() {
            const list = document.getElementById('block-list');
            list.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-gray-500">
                <span class="loading-spinner"></span> Carregando bloqueios...
            </td></tr>`;

            try {
                // TODO API: GET /medico/get_schedule_blocks
                const response = await fetch(AJAX_URL + '/medico/get_schedule_blocks', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                if (!response.ok) throw new Error('HTTP ' + response.status);

                const data = await response.json();
                lastBlocks = data || [];

                if (!lastBlocks.length) {
                    list.innerHTML = `<tr>
                        <td colspan="4" class="text-center py-4 text-gray-500">
                            <i class="fas fa-calendar-check text-2xl block mb-2 text-gray-300"></i>
                            Nenhum bloqueio registado.
                        </td>
                    </tr>`;
                    return;
                }

                list.innerHTML = lastBlocks.map(b => `
                    <tr>
                        <td class="font-medium">${b.Data ? new Date(b.Data).toLocaleDateString('pt-PT') : '—'}</td>
                        <td><span class="status-badge bloqueio"><i class="fas fa-ban" style="font-size:0.6rem;"></i> ${b.Motivo || '—'}</span></td>
                        <td>${b.Dia_Inteiro == 1 || b.Dia_Inteiro === true ? 'Dia inteiro' : `${(b.Hora_Inicio || '').substring(0, 5)}–${(b.Hora_Fim || '').substring(0, 5)}`}</td>
                        <td class="text-center">
                            <button class="btn-sm btn-delete" onclick="deleteBlock(${b.ID_Bloqueio})" title="Remover"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                `).join('');

            } catch (error) {
                console.error('Erro ao carregar bloqueios:', error);
                list.innerHTML = `<tr>
                    <td colspan="4" class="text-center py-4 text-red-500">
                        <i class="fas fa-exclamation-circle text-2xl block mb-2"></i>
                        Erro ao carregar bloqueios: ${error.message}
                    </td>
                </tr>`;
            }
        }

        function deleteBlock(id) {
            if (!confirm('Remover este bloqueio? A data volta a ficar disponível.')) return;

            const formData = new FormData();
            formData.append('id', id);
            formData.append('csrf_test_name', getCsrfToken());

            // TODO API: POST /medico/delete_schedule_block
            fetch(AJAX_URL + '/medico/delete_schedule_block', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    showNotification(data.error, 'error');
                } else {
                    showNotification(data.success || 'Bloqueio removido com sucesso!', 'success');
                    loadBlocks();
                }
            })
            .catch(error => {
                console.error('Erro ao remover bloqueio:', error);
                showNotification('Erro ao remover bloqueio.', 'error');
            });
        }

        // ==================== FILTRO POR DIA / VISTA ====================
        document.querySelectorAll('.weekday-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.weekday-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                loadSchedules(this.dataset.day);
            });
        });

        document.querySelectorAll('.view-toggle button').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.view-toggle button').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentView = this.dataset.view;
                document.getElementById('view-list').style.display = currentView === 'list' ? 'block' : 'none';
                document.getElementById('view-calendar').style.display = currentView === 'calendar' ? 'block' : 'none';
            });
        });

        // ==================== INICIALIZAÇÃO ====================
        document.addEventListener('DOMContentLoaded', function () {
            document.getElementById('notification-close').addEventListener('click', function () {
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
                mobileMenuBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    sidebarMenu.classList.add('show');
                    sidebarOverlay.classList.add('show');
                    pageWrapper.classList.add('expanded');
                });
            }
            if (closeSidebarBtn) {
                closeSidebarBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    sidebarMenu.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                    pageWrapper.classList.remove('expanded');
                });
            }
            if (toggleSidebarBtn) {
                toggleSidebarBtn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    sidebarMenu.classList.toggle('expanded');
                    pageWrapper.classList.toggle('expanded');
                });
            }
            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function () {
                    sidebarMenu.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                    pageWrapper.classList.remove('expanded');
                });
            }
            document.addEventListener('click', function (e) {
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
            document.getElementById('logout-btn').addEventListener('click', function () {
                if (confirm('Tem certeza que deseja sair?')) {
                    window.location.href = SITE_URL + '/auth/logout';
                }
            });

            loadSchedules('all');
            loadBlocks();
        });
    </script>
</body>
</html>