<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minha Agenda - Médico - Centro de Saúde Da Matola II</title>
    <meta name="description" content="Gerencie sua agenda de consultas">
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

        .search-box { position: relative; }
        .search-box input {
            padding: 0.6rem 0.9rem 0.6rem 2.6rem;
            border: 1px solid #d8dee8;
            border-radius: 0.6rem;
            font-size: 0.875rem;
            width: 100%;
            background: white;
            transition: all 0.2s;
        }
        .search-box input:focus {
            outline: none; border-color: var(--brand-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }
        .search-box i { position: absolute; left: 0.9rem; top: 50%; transform: translateY(-50%); color: #9ca3af; }

        .form-input, .form-select {
            width: 100%;
            padding: 0.55rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            background: white;
        }
        .form-input:focus, .form-select:focus {
            outline: none; border-color: var(--brand-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
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
        .btn-secondary { background-color: #e5e7eb; color: #374151; }
        .btn-secondary:hover { background-color: #d1d5db; }
        .btn-success { background-color: var(--teal-500); color: white; }
        .btn-success:hover { background-color: var(--teal-600); transform: translateY(-1px); }
        .btn:disabled { opacity: .55; cursor: not-allowed; transform: none !important; }

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

        .patient-cell { display: flex; align-items: center; gap: 0.6rem; }
        .patient-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: var(--brand-100);
            color: var(--brand-700);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.75rem;
            flex-shrink: 0;
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
        .status-badge.pendente { background-color: #fef3c7; color: #92400e; }
        .status-badge.confirmado { background-color: #dbeafe; color: #1e40af; }
        .status-badge.concluido { background-color: #d1fae5; color: #065f46; }
        .status-badge.cancelado { background-color: #fee2e2; color: #991b1b; }

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
        .btn-view { background-color: var(--brand-500); color: white; }
        .btn-view:hover { background-color: var(--brand-600); }
        .btn-reschedule { background-color: #f1f5f9; color: var(--ink-700); }
        .btn-reschedule:hover { background-color: #e2e8f0; }
        .btn-complete { background-color: var(--teal-500); color: white; }
        .btn-complete:hover { background-color: var(--teal-600); }
        .btn-cancel-appt { background-color: #f1f5f9; color: var(--rose-500); }
        .btn-cancel-appt:hover { background-color: #fee2e2; }

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
        @media (prefers-reduced-motion: reduce) { .pulse-path { animation: none; stroke-dashoffset: 0; } }

        /* View toggle Dia / Semana */
        .view-toggle { display: inline-flex; border: 1.5px solid #e5e7eb; border-radius: 0.5rem; overflow: hidden; }
        .view-toggle button {
            padding: 0.45rem 0.9rem; border: none; background: white; color: var(--ink-500);
            font-size: 0.78rem; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 0.35rem;
        }
        .view-toggle button.active { background: var(--brand-500); color: white; }
        .view-toggle button + button { border-left: 1.5px solid #e5e7eb; }

        /* Vista semanal */
        .cal-grid { display: grid; grid-template-columns: repeat(7, minmax(150px, 1fr)); gap: 0.65rem; overflow-x: auto; padding-bottom: 0.25rem; }
        .cal-day { background: var(--paper); border-radius: 0.6rem; padding: 0.7rem; min-height: 160px; }
        .cal-day__header { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .02em; color: var(--ink-700); text-align: center; padding-bottom: 0.5rem; margin-bottom: 0.5rem; border-bottom: 1px solid #e5e7eb; }
        .cal-day__header .date { display: block; font-weight: 400; font-size: 0.62rem; color: var(--ink-500); text-transform: none; margin-top: 1px; }
        .cal-day.is-today .cal-day__header { color: var(--brand-600); }
        .cal-block {
            background: white; border: 1px solid #e5e7eb; border-left: 3px solid var(--brand-500);
            border-radius: 0.45rem; padding: 0.5rem 0.6rem; margin-bottom: 0.4rem; font-size: 0.7rem; cursor: pointer;
        }
        .cal-block:hover { border-color: var(--brand-500); }
        .cal-block.confirmado { border-left-color: var(--brand-500); }
        .cal-block.concluido { border-left-color: var(--teal-500); }
        .cal-block.cancelado { border-left-color: var(--rose-500); opacity: .6; }
        .cal-block.pendente { border-left-color: var(--amber-500); }
        .cal-block__time { font-weight: 700; }
        .cal-block__patient { margin-top: 0.1rem; }
        .cal-empty { font-size: 0.66rem; color: #9ca3af; text-align: center; padding: 1rem 0; }

        /* Modal (reaproveitado das outras views do hospital) */
        .modal-overlay {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(15, 23, 42, 0.55); z-index: 950;
            justify-content: center; align-items: center; padding: 1rem;
        }
        .modal-overlay.show { display: flex; animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .modal-content {
            background-color: white; padding: 1.5rem; border-radius: 0.85rem;
            max-width: 480px; width: 100%; box-shadow: 0 24px 60px rgba(0,0,0,0.35);
            animation: slideUp 0.3s ease-out; max-height: 90vh; overflow-y: auto;
        }
        @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f3f4f6; padding-bottom: 1rem; margin-bottom: 1.2rem; }
        .modal-header h3 { font-size: 1.15rem; font-weight: 600; color: #1f2937; }
        .modal-close { background: none; border: none; font-size: 1.5rem; color: #6b7280; cursor: pointer; padding: 0.25rem 0.5rem; border-radius: 0.25rem; }
        .modal-close:hover { color: #1f2937; background-color: #f3f4f6; }

        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; font-weight: 500; color: #374151; margin-bottom: 0.25rem; font-size: 0.875rem; }
        .form-textarea {
            width: 100%; padding: 0.55rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem;
            font-size: 0.85rem; background: white; resize: vertical; min-height: 80px;
        }
        .form-textarea:focus { outline: none; border-color: var(--brand-500); box-shadow: 0 0 0 3px rgba(59,130,246,0.12); }

        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem 1.25rem; margin-bottom: 1rem; }
        .info-grid .label { font-size: 0.66rem; text-transform: uppercase; letter-spacing: 0.03em; color: var(--ink-500); font-weight: 600; margin-bottom: 0.15rem; }
        .info-grid .value { font-size: 0.88rem; color: var(--ink-900); font-weight: 500; }
        .info-note { background: var(--paper); border-radius: 0.5rem; padding: 0.7rem 0.85rem; font-size: 0.82rem; color: var(--ink-700); margin-bottom: 1rem; }

        .details-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; border-top: 2px solid #f3f4f6; padding-top: 1rem; margin-top: 0.5rem; }
        .details-actions .btn { flex: 1; min-width: 110px; justify-content: center; }

        @media (max-width: 640px) {
            .main-content { padding: 0.5rem; }
            table { font-size: 0.75rem; }
            thead th, tbody td { padding: 0.55rem; }
            .pulse-line { display: none; }
            .info-grid { grid-template-columns: 1fr; }
            .view-toggle { width: 100%; }
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
                <a href="<?= site_url('medico/agenda') ?>" class="active">
                    <i class="fas fa-calendar-alt"></i>
                    <span class="sidebar-text">Minha Agenda</span>
                </a>
                <a href="<?= site_url('medico/pacientes') ?>">
                    <i class="fas fa-users"></i>
                    <span class="sidebar-text">Meus Pacientes</span>
                </a>
                <a href="<?= site_url('medico/disponibilidade') ?>">
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
                        <h2 class="text-2xl font-semibold text-gray-800">Minha Agenda</h2>
                        <p class="text-gray-500 text-sm">Visualize e gerencie suas consultas</p>
                    </div>
                    <button class="btn btn-primary" onclick="window.location.href='<?= site_url('medico') ?>'">
                        <i class="fas fa-arrow-left mr-1"></i> Voltar
                    </button>
                </div>

                <!-- Filtros -->
                <div class="card-panel mb-6">
                    <div class="flex flex-wrap items-end justify-between gap-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 flex-1">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1" id="filter-date-label">Data</label>
                                <input type="date" id="filter-date" class="form-input" value="<?= date('Y-m-d'); ?>">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select id="filter-status" class="form-select">
                                    <option value="">Todos</option>
                                    <option value="pendente">Pendente</option>
                                    <option value="confirmado">Confirmado</option>
                                    <option value="concluido">Concluído</option>
                                    <option value="cancelado">Cancelado</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pesquisar</label>
                                <div class="search-box">
                                    <i class="fas fa-search"></i>
                                    <input type="text" id="filter-search" class="form-input" style="padding-left:2.6rem;" placeholder="Nome do paciente...">
                                </div>
                            </div>
                        </div>
                        <div class="view-toggle">
                            <button data-view="day" class="active"><i class="fas fa-calendar-day"></i> Dia</button>
                            <button data-view="week"><i class="fas fa-calendar-week"></i> Semana</button>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 mt-4">
                        <button class="btn btn-primary" id="apply-filters">
                            <i class="fas fa-filter mr-1"></i> Filtrar
                        </button>
                        <button class="btn btn-secondary" id="clear-filters">
                            <i class="fas fa-undo mr-1"></i>
                        </button>
                    </div>
                </div>

                <!-- Vista Dia (tabela) -->
                <div class="card-panel" id="view-day">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Consultas do Dia</h3>
                        <span class="text-xs text-gray-400" id="appointment-count"></span>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Hora</th>
                                    <th>Paciente</th>
                                    <th>Status</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="agenda-list">
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-gray-500">
                                        <span class="loading-spinner"></span> Carregando agenda...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Vista Semana (calendário) -->
                <div class="card-panel" id="view-week" style="display:none;">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Consultas da Semana</h3>
                        <span class="text-xs text-gray-400" id="week-range-label"></span>
                    </div>
                    <div class="cal-grid" id="cal-grid"></div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal de Detalhes da Consulta -->
    <div id="details-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-file-medical text-blue-500 mr-2"></i>Detalhes da Consulta</h3>
                <button class="modal-close" onclick="closeModal('details-modal')">&times;</button>
            </div>
            <div id="details-modal-body"></div>
        </div>
    </div>

    <!-- Modal de Reagendamento -->
    <div id="reschedule-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-calendar-alt text-blue-500 mr-2"></i>Reagendar Consulta</h3>
                <button class="modal-close" onclick="closeModal('reschedule-modal')">&times;</button>
            </div>
            <form id="reschedule-form" onsubmit="submitReschedule(event)">
                <input type="hidden" id="reschedule-id">
                <div class="form-group">
                    <label for="reschedule-date">Nova Data <span style="color:#ef4444;">*</span></label>
                    <input type="date" id="reschedule-date" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="reschedule-time">Nova Hora <span style="color:#ef4444;">*</span></label>
                    <input type="time" id="reschedule-time" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="reschedule-reason">Motivo do reagendamento (opcional)</label>
                    <textarea id="reschedule-reason" class="form-textarea" placeholder="Ex: conflito de agenda, pedido do paciente..."></textarea>
                </div>
                <div class="flex gap-3 mt-4">
                    <button type="submit" class="btn btn-success flex-1" id="reschedule-submit-btn">
                        <i class="fas fa-check mr-1"></i> Confirmar Novo Horário
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('reschedule-modal')">Cancelar</button>
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

        function openModal(id) { document.getElementById(id).classList.add('show'); document.body.style.overflow = 'hidden'; }
        function closeModal(id) { document.getElementById(id).classList.remove('show'); document.body.style.overflow = 'auto'; }
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function (e) { if (e.target === this) closeModal(this.id); });
        });

        // NOTA: os valores de status aqui são em minúsculas (pendente/confirmado/concluido/cancelado).
        // A view de Agendamentos do secretário usa valores capitalizados (Pendente/Confirmado/...).
        // Confirmar com o backend se ambas as views leem/escrevem a mesma coluna Status antes de
        // assumir que os filtros funcionam de forma cruzada entre painéis.
        const STATUS_ICONS = { pendente: 'fa-hourglass-half', confirmado: 'fa-check-circle', concluido: 'fa-check-double', cancelado: 'fa-times-circle' };
        const STATUS_LABELS = { pendente: 'Pendente', confirmado: 'Confirmado', concluido: 'Concluído', cancelado: 'Cancelado' };

        // Guarda os últimos itens carregados (vista dia) para alimentar o modal de detalhes sem novo fetch
        let lastAgendaItems = [];
        let currentView = 'day';

        // ==================== VISTA DIA ====================
        async function loadAgenda() {
            const date = document.getElementById('filter-date').value;
            const status = document.getElementById('filter-status').value;
            const search = document.getElementById('filter-search').value.trim();

            const list = document.getElementById('agenda-list');
            const countEl = document.getElementById('appointment-count');

            list.innerHTML = `<tr><td colspan="4" class="text-center py-4 text-gray-500">
                <span class="loading-spinner"></span> Carregando agenda...
            </td></tr>`;

            try {
                const data = await fetchAgendaForDate(date, status, search);
                lastAgendaItems = data;

                if (!data || data.length === 0) {
                    list.innerHTML = `<tr>
                        <td colspan="4" class="text-center py-4 text-gray-500">
                            <i class="fas fa-calendar-day text-2xl block mb-2 text-gray-300"></i>
                            Nenhuma consulta para este dia.
                        </td>
                    </tr>`;
                    if (countEl) countEl.textContent = '0 consultas';
                    return;
                }

                if (countEl) countEl.textContent = `${data.length} consulta(s)`;

                list.innerHTML = data.map(item => {
                    const statusClass = item.status || 'pendente';
                    const icon = STATUS_ICONS[statusClass] || 'fa-circle';
                    const canAct = statusClass !== 'concluido' && statusClass !== 'cancelado';

                    return `
                        <tr>
                            <td class="font-medium">${item.time || '--:--'}</td>
                            <td>
                                <div class="patient-cell">
                                    <div class="patient-avatar">${(item.patient_name || '?').charAt(0).toUpperCase()}</div>
                                    <div>
                                        <div class="font-medium">${item.patient_name || 'Paciente'}</div>
                                        ${item.room ? `<div class="text-xs text-gray-500">${item.room}</div>` : ''}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge ${statusClass}">
                                    <i class="fas ${icon}" style="font-size:0.65rem;"></i>
                                    ${item.status_label || STATUS_LABELS[statusClass] || 'Pendente'}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="flex justify-center gap-1">
                                    <button class="btn-sm btn-view" onclick="viewDetails('${item.id}')" title="Detalhes">
                                        <i class="fas fa-file-medical"></i>
                                    </button>
                                    ${canAct ? `
                                        <button class="btn-sm btn-reschedule" onclick="openReschedule('${item.id}')" title="Reagendar">
                                            <i class="fas fa-calendar-alt"></i>
                                        </button>
                                        <button class="btn-sm btn-complete" onclick="updateStatus('${item.id}', 'concluido')" title="Concluir">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn-sm btn-cancel-appt" onclick="updateStatus('${item.id}', 'cancelado')" title="Cancelar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    ` : ''}
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');

            } catch (error) {
                console.error('Erro ao carregar agenda:', error);
                list.innerHTML = `<tr>
                    <td colspan="4" class="text-center py-4 text-red-500">
                        <i class="fas fa-exclamation-circle text-2xl block mb-2"></i>
                        Erro ao carregar agenda: ${error.message}
                    </td>
                </tr>`;
                showNotification('Erro ao carregar agenda.', 'error');
            }
        }

        async function fetchAgendaForDate(date, status, search) {
            let url = AJAX_URL + `/medico/agenda?date=${encodeURIComponent(date)}`;
            if (status) url += `&status=${encodeURIComponent(status)}`;
            if (search) url += `&query=${encodeURIComponent(search)}`;

            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!response.ok) {
                const text = await response.text();
                console.error('Erro na resposta:', text);
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        }

        // ==================== VISTA SEMANA ====================
        function getWeekDates(anchorDateStr) {
            const anchor = new Date(anchorDateStr + 'T00:00:00');
            const dow = anchor.getDay(); // 0=Domingo..6=Sábado
            const diffToMonday = dow === 0 ? -6 : 1 - dow;
            const monday = new Date(anchor);
            monday.setDate(anchor.getDate() + diffToMonday);

            const dates = [];
            for (let i = 0; i < 7; i++) {
                const d = new Date(monday);
                d.setDate(monday.getDate() + i);
                dates.push(d);
            }
            return dates;
        }

        function fmtDate(d) {
            const yyyy = d.getFullYear();
            const mm = String(d.getMonth() + 1).padStart(2, '0');
            const dd = String(d.getDate()).padStart(2, '0');
            return `${yyyy}-${mm}-${dd}`;
        }

        const DAY_LABELS = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];

        async function loadWeek() {
            const anchor = document.getElementById('filter-date').value;
            const status = document.getElementById('filter-status').value;
            const search = document.getElementById('filter-search').value.trim();
            const grid = document.getElementById('cal-grid');
            const rangeLabel = document.getElementById('week-range-label');

            grid.innerHTML = `<div class="cal-empty" style="grid-column:1/-1;"><span class="loading-spinner"></span> Carregando semana...</div>`;

            const weekDates = getWeekDates(anchor);
            const todayStr = fmtDate(new Date());
            rangeLabel.textContent = `${weekDates[0].toLocaleDateString('pt-PT')} – ${weekDates[6].toLocaleDateString('pt-PT')}`;

            try {
                // NOTA: chama o endpoint uma vez por dia (7 pedidos). Se o volume de consultas for alto,
                // vale a pena criar futuramente um endpoint /medico/agenda_semana?start=&end= que devolva tudo de uma vez.
                const results = await Promise.all(
                    weekDates.map(d => fetchAgendaForDate(fmtDate(d), status, search).catch(() => []))
                );

                lastAgendaItems = results.flat();

                grid.innerHTML = weekDates.map((d, i) => {
                    const dateStr = fmtDate(d);
                    const items = (results[i] || []).slice().sort((a, b) => (a.time || '').localeCompare(b.time || ''));
                    const isToday = dateStr === todayStr;

                    const blocks = items.length ? items.map(item => {
                        const statusClass = item.status || 'pendente';
                        return `
                            <div class="cal-block ${statusClass}" onclick="viewDetails('${item.id}')">
                                <div class="cal-block__time">${item.time || '--:--'}</div>
                                <div class="cal-block__patient">${item.patient_name || 'Paciente'}</div>
                            </div>`;
                    }).join('') : `<div class="cal-empty">Sem consultas</div>`;

                    return `
                        <div class="cal-day ${isToday ? 'is-today' : ''}">
                            <div class="cal-day__header">${DAY_LABELS[d.getDay()]}<span class="date">${d.getDate()}/${d.getMonth() + 1}</span></div>
                            ${blocks}
                        </div>`;
                }).join('');

            } catch (error) {
                console.error('Erro ao carregar semana:', error);
                grid.innerHTML = `<div class="cal-empty" style="grid-column:1/-1;">Erro ao carregar a semana.</div>`;
            }
        }

        function loadCurrentView() {
            if (currentView === 'day') loadAgenda(); else loadWeek();
        }

        document.querySelectorAll('.view-toggle button').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.view-toggle button').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentView = this.dataset.view;
                document.getElementById('view-day').style.display = currentView === 'day' ? 'block' : 'none';
                document.getElementById('view-week').style.display = currentView === 'week' ? 'block' : 'none';
                document.getElementById('filter-date-label').textContent = currentView === 'day' ? 'Data' : 'Semana de';
                loadCurrentView();
            });
        });

        // ==================== DETALHES + NOTAS CLÍNICAS ====================
        function findItem(id) {
            return lastAgendaItems.find(i => String(i.id) === String(id));
        }

        function viewDetails(id) {
            const item = findItem(id);
            if (!item) { showNotification('Consulta não encontrada.', 'error'); return; }

            const statusClass = item.status || 'pendente';
            const canAct = statusClass !== 'concluido' && statusClass !== 'cancelado';

            document.getElementById('details-modal-body').innerHTML = `
                <div class="info-grid">
                    <div><div class="label">Paciente</div><div class="value">${item.patient_name || 'N/A'}</div></div>
                    <div><div class="label">Hora</div><div class="value">${item.time || '--:--'}</div></div>
                    <div><div class="label">Sala</div><div class="value">${item.room || '—'}</div></div>
                    <div><div class="label">Status</div><div class="value"><span class="status-badge ${statusClass}">${STATUS_LABELS[statusClass] || item.status}</span></div></div>
                </div>
                ${item.reason ? `<div class="info-note"><strong>Motivo:</strong> ${item.reason}</div>` : ''}

                <div class="form-group">
                    <label for="clinical-note-text">Notas clínicas (visíveis apenas para a equipa médica)</label>
                    <textarea id="clinical-note-text" class="form-textarea" placeholder="Ex: paciente refere dor há 3 dias, sem febre...">${item.notes || ''}</textarea>
                </div>
                <button class="btn btn-secondary" style="width:100%;" id="save-note-btn" onclick="saveClinicalNote('${item.id}')">
                    <i class="fas fa-save mr-1"></i> Guardar Nota
                </button>

                <div class="details-actions">
                    ${canAct ? `
                        <button class="btn btn-complete" onclick="updateStatus('${item.id}', 'concluido'); closeModal('details-modal');"><i class="fas fa-check"></i> Concluir</button>
                        <button class="btn btn-reschedule" onclick="closeModal('details-modal'); openReschedule('${item.id}');"><i class="fas fa-calendar-alt"></i> Reagendar</button>
                        <button class="btn btn-cancel-appt" onclick="updateStatus('${item.id}', 'cancelado'); closeModal('details-modal');"><i class="fas fa-times"></i> Cancelar</button>
                    ` : ''}
                    ${item.patient_bi ? `<button class="btn btn-secondary" onclick="showPatient('${item.patient_bi}')"><i class="fas fa-address-card"></i> Ficha completa</button>` : ''}
                </div>
            `;
            openModal('details-modal');
        }

        async function saveClinicalNote(id) {
            const note = document.getElementById('clinical-note-text').value.trim();
            const btn = document.getElementById('save-note-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Guardando...';

            const formData = new FormData();
            formData.append('appointment_id', id);
            formData.append('note', note);
            formData.append('csrf_test_name', getCsrfToken());

            try {
                // TODO API: POST /medico/save_appointment_note (appointment_id, note)
                const response = await fetch(AJAX_URL + '/medico/save_appointment_note', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                const result = await response.json();
                if (result.error) { showNotification(result.error, 'error'); return; }

                showNotification(result.success || 'Nota clínica guardada.', 'success');
                const item = findItem(id);
                if (item) item.notes = note;
            } catch (error) {
                console.error('Erro ao guardar nota:', error);
                showNotification('Erro ao guardar nota clínica. Verifique se o endpoint do backend já existe.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save mr-1"></i> Guardar Nota';
            }
        }

        // ==================== REAGENDAR ====================
        function openReschedule(id) {
            const item = findItem(id);
            document.getElementById('reschedule-id').value = id;
            document.getElementById('reschedule-date').value = document.getElementById('filter-date').value;
            document.getElementById('reschedule-time').value = item ? (item.time || '') : '';
            document.getElementById('reschedule-reason').value = '';
            openModal('reschedule-modal');
        }

        async function submitReschedule(event) {
            event.preventDefault();
            const id = document.getElementById('reschedule-id').value;
            const date = document.getElementById('reschedule-date').value;
            const time = document.getElementById('reschedule-time').value;
            const reason = document.getElementById('reschedule-reason').value.trim();

            if (!date || !time) { showNotification('Indique a nova data e hora.', 'error'); return; }

            const formData = new FormData();
            formData.append('appointment_id', id);
            formData.append('date', date);
            formData.append('time', time);
            formData.append('reason', reason);
            formData.append('csrf_test_name', getCsrfToken());

            const btn = document.getElementById('reschedule-submit-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> A confirmar...';

            try {
                // TODO API: POST /medico/reschedule_appointment (appointment_id, date, time, reason)
                const response = await fetch(AJAX_URL + '/medico/reschedule_appointment', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                const result = await response.json();
                if (result.error) { showNotification(result.error, 'error'); return; }

                showNotification(result.success || 'Consulta reagendada com sucesso!', 'success');
                closeModal('reschedule-modal');
                loadCurrentView();
            } catch (error) {
                console.error('Erro ao reagendar:', error);
                showNotification('Erro ao reagendar consulta. Verifique se o endpoint do backend já existe.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check mr-1"></i> Confirmar Novo Horário';
            }
        }

        // ==================== ATUALIZAR STATUS ====================
        async function updateStatus(id, status) {
            if (status === 'cancelado' && !confirm('Tem certeza que deseja cancelar esta consulta?')) return;

            const formData = new FormData();
            formData.append('appointment_id', id);
            formData.append('status', status);
            formData.append('csrf_test_name', getCsrfToken());

            try {
                const response = await fetch(AJAX_URL + '/medico/update_appointment_status', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                const result = await response.json();
                if (result.error) { showNotification(result.error, 'error'); return; }

                showNotification(result.success || 'Status atualizado com sucesso!', 'success');
                loadCurrentView();
            } catch (error) {
                console.error('Erro ao atualizar status:', error);
                showNotification('Erro ao atualizar status da consulta.', 'error');
            }
        }

        // ==================== VER FICHA COMPLETA ====================
        function showPatient(bi) {
            if (!bi) { showNotification('BI do paciente não disponível.', 'warning'); return; }
            window.open(AJAX_URL + `/medico/patient/${bi}`, '_blank');
        }

        // ==================== INICIALIZAÇÃO ====================
        document.addEventListener('DOMContentLoaded', function() {
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
                const isModalOpen = document.querySelector('.modal-overlay.show') !== null;

                if (!isClickInsideSidebar && !isClickOnMenuBtn && isSidebarOpen && !isModalOpen) {
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

            // Filtros
            document.getElementById('apply-filters').addEventListener('click', loadCurrentView);
            document.getElementById('clear-filters').addEventListener('click', function() {
                document.getElementById('filter-date').value = '<?= date('Y-m-d'); ?>';
                document.getElementById('filter-status').value = '';
                document.getElementById('filter-search').value = '';
                loadCurrentView();
            });
            document.getElementById('filter-search').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') loadCurrentView();
            });

            // Carregar vista inicial
            loadAgenda();
        });
    </script>
</body>
</html>