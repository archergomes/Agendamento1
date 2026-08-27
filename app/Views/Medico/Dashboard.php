<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Médico - Centro de Saúde Da Matola II</title>
    <meta name="description" content="Dashboard do médico no Centro de Saúde Da Matola II">
    <meta name="csrf-token" content="<?= csrf_hash(); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

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
            display: flex;
            align-items: center;
            justify-content: center;
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

        .metric-card {
            background-color: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            padding: 1.1rem 1.3rem;
            display: flex;
            align-items: center;
            gap: 0.9rem;
            border: 1px solid #eef1f6;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .metric-card:hover { transform: translateY(-2px); box-shadow: 0 10px 22px rgba(17,24,39,0.07); }
        .metric-card .icon-wrap {
            width: 46px; height: 46px; border-radius: 0.6rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }
        .metric-card p.value { font-size: 1.55rem; font-weight: 700; line-height: 1.1; font-family: 'Outfit', sans-serif; }
        .metric-card h3 { font-size: 0.75rem; color: var(--ink-500); font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em; margin-bottom: 0.1rem; }

        /* Next appointment */
        .next-appt-card {
            background: linear-gradient(120deg, #123a80 0%, #1d4ed8 55%, #0d9488 130%);
            border-radius: 0.9rem;
            padding: 1.5rem;
            color: white;
            box-shadow: 0 12px 30px rgba(29,78,216,0.25);
            position: relative;
            overflow: hidden;
            min-height: 160px;
        }
        .next-appt-card::after {
            content: '';
            position: absolute; right: -30px; top: -30px;
            width: 160px; height: 160px; border-radius: 50%;
            background: rgba(255,255,255,0.08);
        }
        .next-appt-empty {
            background: white; border: 1px dashed #d8dee8; border-radius: 0.9rem;
            padding: 2rem; text-align: center; color: var(--ink-500);
            min-height: 160px;
            display: flex; flex-direction: column; justify-content: center; align-items: center;
        }

        .btn {
            padding: 0.6rem 1.1rem; border-radius: 0.55rem; font-weight: 500;
            border: none; cursor: pointer; transition: all 0.2s; font-size: 0.85rem;
            display: inline-flex; align-items: center; gap: 0.5rem;
        }
        .btn-primary { background-color: white; color: var(--brand-700); }
        .btn-primary:hover { background-color: #f1f5f9; transform: translateY(-1px); }
        .btn-outline-light { background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.4); }
        .btn-outline-light:hover { background: rgba(255,255,255,0.25); }

        /* Timeline */
        .timeline { position: relative; padding-left: 1.6rem; }
        .timeline::before {
            content: ''; position: absolute; left: 0.45rem; top: 0.4rem; bottom: 0.4rem;
            width: 2px; background: #e5e7eb;
        }
        .timeline-item { position: relative; padding-bottom: 1.3rem; }
        .timeline-item:last-child { padding-bottom: 0; }
        .timeline-dot {
            position: absolute; left: -1.6rem; top: 0.15rem;
            width: 0.9rem; height: 0.9rem; border-radius: 50%;
            background: white; border: 3px solid var(--brand-500);
        }
        .timeline-item.done .timeline-dot { border-color: var(--teal-500); }
        .timeline-item.cancelled .timeline-dot { border-color: var(--rose-500); }
        .timeline-time { font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 0.95rem; color: var(--ink-900); }
        .timeline-card {
            background: #f9fafc; border: 1px solid #eef1f6; border-radius: 0.6rem;
            padding: 0.75rem 1rem; margin-top: 0.35rem;
        }

        .patient-cell { display: flex; align-items: center; gap: 0.6rem; }
        .patient-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: var(--brand-100); color: var(--brand-700);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.75rem; flex-shrink: 0;
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
            padding: 0.3rem 0.65rem; font-size: 0.75rem; border-radius: 0.4rem;
            border: none; cursor: pointer; transition: all 0.2s;
            display: inline-flex; align-items: center; gap: 0.3rem;
        }
        .btn-sm:hover { transform: scale(1.05); }
        .btn-view { background-color: var(--brand-500); color: white; }
        .btn-view:hover { background-color: var(--brand-600); }
        .btn-complete { background-color: var(--teal-500); color: white; }
        .btn-complete:hover { background-color: var(--teal-600); }
        .btn-cancel-appt { background-color: #f1f5f9; color: var(--rose-500); }
        .btn-cancel-appt:hover { background-color: #fee2e2; }

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
            padding: 0.55rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.5rem;
            font-size: 0.85rem; background: white;
        }
        .form-input:focus, .form-select:focus {
            outline: none; border-color: var(--brand-500); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }

        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        thead th {
            background-color: #f3f5f9;
            padding: 0.75rem 1rem; text-align: left;
            font-weight: 600; color: var(--ink-700);
            font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.03em;
        }
        tbody td { padding: 0.8rem 1rem; border-bottom: 1px solid #eef1f6; vertical-align: middle; }
        tbody tr:hover { background-color: #f9fafc; }

        .chart-card {
            background: white; border-radius: 0.75rem; border: 1px solid #eef1f6;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06); padding: 1.25rem 1.4rem 0.75rem;
            display: flex; flex-direction: column;
        }
        .chart-card .chart-title { font-family: 'Outfit', sans-serif; font-weight: 600; font-size: 1rem; }
        .chart-card .chart-sub { font-size: 0.78rem; color: var(--ink-500); margin-bottom: 0.5rem; }
        .chart-canvas-wrap { position: relative; flex: 1; min-height: 220px; }
        .chart-empty {
            display: none; position: absolute; inset: 0;
            align-items: center; justify-content: center; flex-direction: column;
            color: var(--ink-500); font-size: 0.8rem; text-align: center; gap: 0.4rem;
        }
        .legend-dot { width: 9px; height: 9px; border-radius: 50%; display: inline-block; }

        .empty-state { text-align: center; padding: 2.5rem 1rem; color: #6b7280; }
        .empty-state i { font-size: 2.2rem; color: #d1d5db; margin-bottom: 0.5rem; }

        .loading-spinner {
            display: inline-block; width: 1.3rem; height: 1.3rem;
            border: 3px solid #e5e7eb; border-top-color: var(--brand-500);
            border-radius: 50%; animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        .pulse-line { width: 120px; height: 34px; opacity: 0.9; }
        .pulse-path { stroke-dasharray: 300; stroke-dashoffset: 300; animation: draw-pulse 3.2s ease-in-out infinite; }
        @keyframes draw-pulse {
            0% { stroke-dashoffset: 300; }
            55% { stroke-dashoffset: 0; }
            100% { stroke-dashoffset: -300; }
        }
        @media (prefers-reduced-motion: reduce) { .pulse-path { animation: none; stroke-dashoffset: 0; } }

        /* Modal */
        .modal-overlay {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(15, 23, 42, 0.55);
            z-index: 950; justify-content: center; align-items: center; padding: 1rem;
        }
        .modal-overlay.show { display: flex; animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .modal-content {
            background-color: white; padding: 1.5rem; border-radius: 0.85rem;
            max-width: 560px; width: 100%; box-shadow: 0 24px 60px rgba(0,0,0,0.35);
            animation: slideUp 0.3s ease-out; max-height: 90vh; overflow-y: auto;
        }
        @keyframes slideUp { from { transform: translateY(30px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        .modal-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #f3f4f6; padding-bottom: 1rem; margin-bottom: 1.2rem; }
        .modal-header h3 { font-size: 1.15rem; font-weight: 600; color: #1f2937; }
        .modal-close { background: none; border: none; font-size: 1.5rem; color: #6b7280; cursor: pointer; padding: 0.25rem 0.5rem; border-radius: 0.25rem; }
        .modal-close:hover { color: #1f2937; background-color: #f3f4f6; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.9rem; margin-bottom: 1rem; }
        .info-item .label { font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.03em; color: var(--ink-500); font-weight: 600; }
        .info-item .val { font-size: 0.92rem; color: var(--ink-900); margin-top: 0.1rem; }

        @media (max-width: 640px) {
            .metric-card { padding: 0.9rem 1rem; }
            .metric-card p.value { font-size: 1.35rem; }
            .main-content { padding: 0.5rem; }
            table { font-size: 0.75rem; }
            thead th, tbody td { padding: 0.55rem; }
            .pulse-line { display: none; }
            .info-grid { grid-template-columns: 1fr; }
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
                <a href="<?= site_url('medico') ?>" class="active">
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
                <div class="mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-1" id="greeting">Olá, Dr(a). <?= htmlspecialchars($medico->Nome ?? ''); ?></h2>
                    <p class="text-gray-500 text-sm" id="current-date"></p>
                </div>

                <!-- Next appointment + metrics -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
                    <div class="lg:col-span-1" id="next-appt-wrap">
                        <div class="next-appt-card" style="display:none;" id="next-appt-card">
                            <div class="flex items-center justify-between mb-3" style="position:relative;">
                                <span class="text-xs font-semibold uppercase tracking-wide bg-white/15 px-2 py-1 rounded-full">Próxima Consulta</span>
                                <span class="text-xs font-semibold" id="next-appt-countdown">—</span>
                            </div>
                            <div style="position:relative;">
                                <div class="text-2xl font-bold display-font" id="next-appt-time">--:--</div>
                                <div class="text-sm opacity-90" id="next-appt-patient">—</div>
                                <div class="text-xs opacity-70 mt-1" id="next-appt-room">—</div>
                                <div class="flex gap-2 mt-4">
                                    <button class="btn btn-primary" id="next-appt-view-btn">
                                        <i class="fas fa-file-medical mr-1"></i>Ver Ficha
                                    </button>
                                    <button class="btn btn-outline-light" id="next-appt-complete-btn">
                                        <i class="fas fa-check mr-1"></i>Concluir
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="next-appt-empty" id="next-appt-empty">
                            <i class="fas fa-calendar-check text-3xl mb-2 block text-gray-300"></i>
                            <p class="font-medium">Sem mais consultas por hoje</p>
                            <p class="text-xs mt-1">Aproveite para rever o histórico ou atualizar a sua disponibilidade.</p>
                        </div>
                    </div>

                    <div class="lg:col-span-2 grid grid-cols-2 gap-4">
                        <div class="metric-card">
                            <div class="icon-wrap" style="background:#dbeafe; color:#2563eb;">
                                <i class="fas fa-calendar-day"></i>
                            </div>
                            <div>
                                <h3>Consultas Hoje</h3>
                                <p class="value" id="metric-today"><span class="loading-spinner"></span></p>
                            </div>
                        </div>
                        <div class="metric-card">
                            <div class="icon-wrap" style="background:#ccfbf1; color:#0d9488;">
                                <i class="fas fa-calendar-week"></i>
                            </div>
                            <div>
                                <h3>Esta Semana</h3>
                                <p class="value" id="metric-week"><span class="loading-spinner"></span></p>
                            </div>
                        </div>
                        <div class="metric-card">
                            <div class="icon-wrap" style="background:#ede9fe; color:#7c3aed;">
                                <i class="fas fa-user-friends"></i>
                            </div>
                            <div>
                                <h3>Pacientes Ativos</h3>
                                <p class="value" id="metric-patients"><span class="loading-spinner"></span></p>
                            </div>
                        </div>
                        <div class="metric-card">
                            <div class="icon-wrap" style="background:#fee2e2; color:#dc2626;">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <div>
                                <h3>Taxa de Comparecimento</h3>
                                <p class="value" id="metric-attendance"><span class="loading-spinner"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
                    <div class="chart-card lg:col-span-2">
                        <div class="chart-title">Consultas — últimos 14 dias</div>
                        <div class="chart-sub">Volume diário de atendimentos</div>
                        <div class="chart-canvas-wrap">
                            <canvas id="chart-trend"></canvas>
                            <div class="chart-empty" id="empty-trend">
                                <i class="fas fa-chart-line text-2xl"></i>
                                <span>Sem dados suficientes para exibir a tendência.</span>
                            </div>
                        </div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-title">Status das Consultas</div>
                        <div class="chart-sub">Últimos 30 dias</div>
                        <div class="chart-canvas-wrap" style="min-height:190px;">
                            <canvas id="chart-status"></canvas>
                            <div class="chart-empty" id="empty-status">
                                <i class="fas fa-calendar-check text-2xl"></i>
                                <span>Nenhuma consulta registada.</span>
                            </div>
                        </div>
                        <div class="flex justify-center flex-wrap gap-3 text-xs text-gray-600 pb-3 pt-1" id="status-legend"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-8">
                    <div class="chart-card">
                        <div class="chart-title">Novos vs. Retorno</div>
                        <div class="chart-sub">Perfil dos atendimentos do mês</div>
                        <div class="chart-canvas-wrap">
                            <canvas id="chart-new-return"></canvas>
                            <div class="chart-empty" id="empty-new-return">
                                <i class="fas fa-user-plus text-2xl"></i>
                                <span>Sem dados este mês.</span>
                            </div>
                        </div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-title">Pacientes por Faixa Etária</div>
                        <div class="chart-sub">Distribuição da carteira de pacientes</div>
                        <div class="chart-canvas-wrap">
                            <canvas id="chart-age"></canvas>
                            <div class="chart-empty" id="empty-age">
                                <i class="fas fa-users text-2xl"></i>
                                <span>Sem dados suficientes.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Agenda do dia -->
                <div class="card-panel mb-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Agenda do Dia</h3>
                            <p class="text-xs text-gray-500" id="agenda-date-label"></p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <input type="date" id="agenda-date-picker" class="form-input">
                            <select id="agenda-status-filter" class="form-select">
                                <option value="">Todos os status</option>
                                <option value="pendente">Pendente</option>
                                <option value="confirmado">Confirmado</option>
                                <option value="concluido">Concluído</option>
                                <option value="cancelado">Cancelado</option>
                            </select>
                            <div class="search-box" style="width:220px;">
                                <i class="fas fa-search"></i>
                                <input type="text" id="agenda-search" placeholder="Buscar paciente...">
                            </div>
                        </div>
                    </div>

                    <div id="agenda-timeline">
                        <div class="empty-state"><span class="loading-spinner"></span> Carregando agenda...</div>
                    </div>
                </div>

                <!-- Próximos dias -->
                <div class="card-panel">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Próximas Consultas</h3>
                        <span class="text-xs text-gray-400" id="upcoming-count"></span>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th>Data</th>
                                    <th>Hora</th>
                                    <th>Status</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="upcoming-list">
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
        </main>

        <footer class="bg-gray-800 text-white py-6">
            <div class="container mx-auto px-4 text-center text-gray-400 text-sm">
                <p>© <?= date('Y') ?> Centro de Saúde Da Matola II. Todos os direitos reservados.</p>
            </div>
        </footer>
    </div>

    <!-- Modal Ficha do Paciente -->
    <div id="patient-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-file-medical text-blue-500 mr-2"></i>Ficha do Paciente</h3>
                <button class="modal-close" onclick="closeModal('patient-modal')">&times;</button>
            </div>
            <div id="patient-modal-body">
                <div class="empty-state"><span class="loading-spinner"></span> Carregando dados...</div>
            </div>
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

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
            document.body.style.overflow = 'auto';
        }
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        async function fetchJSON(url) {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
        }

        function todayISO() {
            const d = new Date();
            return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
        }

        function setHeaderDates() {
            const now = new Date();
            const dateEl = document.getElementById('current-date');
            if (dateEl) {
                const formatted = now.toLocaleDateString('pt-PT', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
                dateEl.textContent = formatted.charAt(0).toUpperCase() + formatted.slice(1);
            }
            const greeting = document.getElementById('greeting');
            if (greeting) {
                const hour = now.getHours();
                const period = hour < 12 ? 'Bom dia' : (hour < 18 ? 'Boa tarde' : 'Boa noite');
                greeting.textContent = `${period}, Dr(a). ${(DOCTOR_NAME || '').split(' ')[0] || ''}`.trim();
            }
        }

        // ==================== MÉTRICAS ====================
        async function renderMetrics() {
            const fields = {
                today_count: 'metric-today',
                week_count: 'metric-week',
                active_patients: 'metric-patients',
                attendance_rate: 'metric-attendance'
            };
            try {
                const data = await fetchJSON(AJAX_URL + '/medico/metrics');
                if (data.error) { showNotification(data.error, 'error'); return; }
                Object.entries(fields).forEach(([key, id]) => {
                    const el = document.getElementById(id);
                    if (!el) return;
                    if (key === 'attendance_rate') {
                        el.textContent = (data[key] !== undefined && data[key] !== null) ? `${data[key]}%` : '—';
                    } else {
                        el.textContent = (data[key] !== undefined && data[key] !== null) ? data[key] : 0;
                    }
                });
            } catch (error) {
                console.error('Erro ao buscar métricas:', error);
                Object.values(fields).forEach(id => { const el = document.getElementById(id); if (el) el.textContent = '—'; });
            }
        }

        // ==================== GRÁFICOS ====================
        const COLORS = {
            brand: '#2563eb',
            brandFill: 'rgba(37,99,235,0.12)',
            teal: '#0d9488',
            amber: '#f59e0b',
            rose: '#ef4444',
            purple: '#7c3aed',
            slate: '#94a3b8'
        };
        Chart.defaults.font.family = "'Roboto', sans-serif";
        Chart.defaults.color = '#6b7280';

        function toggleEmpty(id, isEmpty) {
            const el = document.getElementById(id);
            if (el) el.style.display = isEmpty ? 'flex' : 'none';
        }

        let trendChart, statusChart, newReturnChart, ageChart;

        async function renderTrendChart() {
            try {
                const data = await fetchJSON(AJAX_URL + '/medico/chart_consultas_trend');
                const labels = data.labels || [];
                const values = data.data || [];
                const total = values.reduce((a, b) => a + Number(b || 0), 0);
                toggleEmpty('empty-trend', total === 0);

                if (trendChart) trendChart.destroy();
                trendChart = new Chart(document.getElementById('chart-trend'), {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            data: values,
                            borderColor: COLORS.brand,
                            backgroundColor: COLORS.brandFill,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 3,
                            pointBackgroundColor: COLORS.brand,
                            borderWidth: 2.5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            } catch (e) { console.error(e); toggleEmpty('empty-trend', true); }
        }

        async function renderStatusChart() {
            try {
                const data = await fetchJSON(AJAX_URL + '/medico/chart_status');
                const map = {
                    pendente: { label: 'Pendente', color: COLORS.amber },
                    confirmado: { label: 'Confirmado', color: COLORS.brand },
                    concluido: { label: 'Concluído', color: COLORS.teal },
                    cancelado: { label: 'Cancelado', color: COLORS.rose }
                };
                const keys = ['pendente', 'confirmado', 'concluido', 'cancelado'];
                const values = keys.map(k => Number(data[k] || 0));
                const total = values.reduce((a, b) => a + b, 0);
                toggleEmpty('empty-status', total === 0);

                const legendEl = document.getElementById('status-legend');
                if (legendEl) {
                    legendEl.innerHTML = keys.map((k, i) =>
                        `<span class="inline-flex items-center gap-1">
                            <span class="legend-dot" style="background:${map[k].color}"></span>
                            ${map[k].label} (${values[i]})
                        </span>`
                    ).join('');
                }

                if (statusChart) statusChart.destroy();
                statusChart = new Chart(document.getElementById('chart-status'), {
                    type: 'doughnut',
                    data: {
                        labels: keys.map(k => map[k].label),
                        datasets: [{
                            data: values,
                            backgroundColor: keys.map(k => map[k].color),
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        plugins: { legend: { display: false } }
                    }
                });
            } catch (e) { console.error(e); toggleEmpty('empty-status', true); }
        }

        async function renderNewReturnChart() {
            try {
                const data = await fetchJSON(AJAX_URL + '/medico/chart_novos_retorno');
                const values = [Number(data.novos || 0), Number(data.retorno || 0)];
                const total = values[0] + values[1];
                toggleEmpty('empty-new-return', total === 0);

                if (newReturnChart) newReturnChart.destroy();
                newReturnChart = new Chart(document.getElementById('chart-new-return'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Primeira vez', 'Retorno'],
                        datasets: [{
                            data: values,
                            backgroundColor: [COLORS.purple, COLORS.brand],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '60%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { boxWidth: 10, padding: 12 }
                            }
                        }
                    }
                });
            } catch (e) { console.error(e); toggleEmpty('empty-new-return', true); }
        }

        async function renderAgeChart() {
            try {
                const data = await fetchJSON(AJAX_URL + '/medico/chart_faixa_etaria');
                const labels = data.labels || [];
                const values = data.data || [];
                toggleEmpty('empty-age', labels.length === 0);

                if (ageChart) ageChart.destroy();
                ageChart = new Chart(document.getElementById('chart-age'), {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [{
                            data: values,
                            backgroundColor: COLORS.teal,
                            borderRadius: 6,
                            maxBarThickness: 40
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: '#f1f5f9' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            } catch (e) { console.error(e); toggleEmpty('empty-age', true); }
        }

        function renderAllCharts() {
            renderTrendChart();
            renderStatusChart();
            renderNewReturnChart();
            renderAgeChart();
        }

        // ==================== PRÓXIMA CONSULTA ====================
        function formatCountdown(msDiff) {
            if (msDiff <= 0) return 'Agora';
            const mins = Math.floor(msDiff / 60000);
            if (mins < 60) return `em ${mins} min`;
            const hrs = Math.floor(mins / 60);
            const rem = mins % 60;
            return `em ${hrs}h${rem > 0 ? rem + 'm' : ''}`;
        }

        async function renderNextAppointment() {
            try {
                const data = await fetchJSON(AJAX_URL + '/medico/proxima_consulta');
                const card = document.getElementById('next-appt-card');
                const empty = document.getElementById('next-appt-empty');

                if (!data || data.error || !data.time) {
                    card.style.display = 'none';
                    empty.style.display = 'block';
                    return;
                }

                empty.style.display = 'none';
                card.style.display = 'block';
                document.getElementById('next-appt-time').textContent = data.time;
                document.getElementById('next-appt-patient').textContent = data.patient_name || '—';
                document.getElementById('next-appt-room').innerHTML = `<i class="fas fa-door-open mr-1"></i>${data.room || 'Sala não definida'}`;

                if (data.datetime) {
                    const diff = new Date(data.datetime).getTime() - Date.now();
                    document.getElementById('next-appt-countdown').textContent = formatCountdown(diff);
                }

                document.getElementById('next-appt-view-btn').onclick = () => showPatientModal(data.patient_bi);
                document.getElementById('next-appt-complete-btn').onclick = () => updateAppointmentStatus(data.appointment_id, 'concluido');

            } catch (e) {
                console.error('Erro ao buscar próxima consulta:', e);
                document.getElementById('next-appt-card').style.display = 'none';
                document.getElementById('next-appt-empty').style.display = 'block';
            }
        }

        // ==================== AGENDA DO DIA ====================
        function statusIcon(status) {
            const icons = {
                pendente: 'fa-hourglass-half',
                confirmado: 'fa-check-circle',
                concluido: 'fa-check-double',
                cancelado: 'fa-times-circle'
            };
            return icons[status] || 'fa-circle';
        }

        async function renderAgenda() {
            const container = document.getElementById('agenda-timeline');
            const date = document.getElementById('agenda-date-picker').value || todayISO();
            const status = document.getElementById('agenda-status-filter').value;
            const search = document.getElementById('agenda-search').value.trim();

            const label = document.getElementById('agenda-date-label');
            if (label) {
                const d = new Date(date + 'T00:00:00');
                label.textContent = d.toLocaleDateString('pt-PT', { weekday: 'long', day: 'numeric', month: 'long' });
            }

            container.innerHTML = `<div class="empty-state"><span class="loading-spinner"></span> Carregando agenda...</div>`;

            try {
                let url = AJAX_URL + `/medico/agenda?date=${encodeURIComponent(date)}`;
                if (status) url += `&status=${encodeURIComponent(status)}`;
                if (search) url += `&query=${encodeURIComponent(search)}`;

                const appointments = await fetchJSON(url);
                if (appointments.error) {
                    showNotification(appointments.error, 'error');
                    container.innerHTML = `<div class="empty-state">Erro ao carregar agenda.</div>`;
                    return;
                }

                if (!appointments || appointments.length === 0) {
                    container.innerHTML = `<div class="empty-state"><i class="fas fa-calendar-day"></i><p>Nenhuma consulta para este dia.</p></div>`;
                    return;
                }

                container.innerHTML = `<div class="timeline">${appointments.map(a => {
                    const st = (a.status || 'pendente').toLowerCase();
                    const canAct = st !== 'concluido' && st !== 'cancelado';
                    return `
                        <div class="timeline-item ${st === 'concluido' ? 'done' : ''} ${st === 'cancelado' ? 'cancelled' : ''}">
                            <div class="timeline-dot"></div>
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <span class="timeline-time">${a.time || '--:--'}</span>
                                <span class="status-badge ${st}">
                                    <i class="fas ${statusIcon(st)}" style="font-size:0.65rem;"></i>
                                    ${a.status_label || a.status || ''}
                                </span>
                            </div>
                            <div class="timeline-card">
                                <div class="flex items-center justify-between flex-wrap gap-2">
                                    <div class="patient-cell">
                                        <div class="patient-avatar">${(a.patient_name || '?').charAt(0).toUpperCase()}</div>
                                        <div>
                                            <div class="font-medium">${a.patient_name || 'Paciente'}</div>
                                            <div class="text-xs text-gray-500">${a.room ? '<i class="fas fa-door-open mr-1"></i>' + a.room : ''}</div>
                                        </div>
                                    </div>
                                    <div class="flex gap-1">
                                        <button class="btn-sm btn-view" onclick="showPatientModal('${a.patient_bi}')" title="Ver ficha">
                                            <i class="fas fa-file-medical"></i>
                                        </button>
                                        ${canAct ? `
                                            <button class="btn-sm btn-complete" onclick="updateAppointmentStatus('${a.id}', 'concluido')" title="Concluir">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn-sm btn-cancel-appt" onclick="updateAppointmentStatus('${a.id}', 'cancelado')" title="Cancelar">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        ` : ''}
                                    </div>
                                </div>
                                ${a.reason ? `<p class="text-xs text-gray-500 mt-2"><i class="fas fa-comment-medical mr-1"></i>${a.reason}</p>` : ''}
                            </div>
                        </div>`;
                }).join('')}</div>`;

            } catch (e) {
                console.error('Erro ao buscar agenda:', e);
                container.innerHTML = `<div class="empty-state">Erro ao carregar agenda.</div>`;
            }
        }

        // ==================== PRÓXIMAS CONSULTAS (TABELA) ====================
        async function renderUpcoming() {
            const list = document.getElementById('upcoming-list');
            const countEl = document.getElementById('upcoming-count');
            try {
                const appointments = await fetchJSON(AJAX_URL + '/medico/proximas_consultas');
                if (appointments.error) {
                    showNotification(appointments.error, 'error');
                    list.innerHTML = `<tr><td colspan="5" class="py-4 text-center text-gray-500">Erro ao carregar.</td></tr>`;
                    return;
                }

                if (countEl) countEl.textContent = appointments.length ? `${appointments.length} registo(s)` : '';

                if (!appointments || appointments.length === 0) {
                    list.innerHTML = `<tr>
                        <td colspan="5" class="py-4 text-center text-gray-500">
                            <i class="fas fa-inbox text-2xl block mb-2 text-gray-300"></i>
                            Nenhuma consulta futura agendada.
                        </td>
                    </tr>`;
                    return;
                }

                list.innerHTML = appointments.map(a => {
                    const st = (a.status || 'pendente').toLowerCase();
                    const canAct = st !== 'concluido' && st !== 'cancelado';
                    return `
                        <tr>
                            <td>
                                <div class="patient-cell">
                                    <div class="patient-avatar">${(a.patient_name || '?').charAt(0).toUpperCase()}</div>
                                    <span class="font-medium">${a.patient_name || '—'}</span>
                                </div>
                            </td>
                            <td>${a.date ? new Date(a.date).toLocaleDateString('pt-PT') : '—'}</td>
                            <td>${a.time || '—'}</td>
                            <td><span class="status-badge ${st}">${a.status_label || a.status || ''}</span></td>
                            <td class="text-center">
                                <button class="btn-sm btn-view" onclick="showPatientModal('${a.patient_bi}')" title="Ver ficha">
                                    <i class="fas fa-file-medical"></i>
                                </button>
                                ${canAct ? `
                                    <button class="btn-sm btn-cancel-appt" onclick="updateAppointmentStatus('${a.id}', 'cancelado')" title="Cancelar">
                                        <i class="fas fa-times"></i>
                                    </button>
                                ` : ''}
                            </td>
                        </tr>`;
                }).join('');
            } catch (e) {
                console.error('Erro ao buscar próximas consultas:', e);
                list.innerHTML = `<tr><td colspan="5" class="py-4 text-center text-gray-500">Erro ao carregar.</td></tr>`;
            }
        }

        // ==================== AÇÕES ====================
        async function updateAppointmentStatus(appointmentId, status) {
            if (status === 'cancelado' && !confirm('Confirma o cancelamento desta consulta?')) return;

            const csrfToken = getCsrfToken();
            const formData = new FormData();
            formData.append('appointment_id', appointmentId);
            formData.append('status', status);
            formData.append('csrf_test_name', csrfToken);

            try {
                const response = await fetch(AJAX_URL + '/medico/update_appointment_status', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                const result = await response.json();
                if (result.error) { showNotification(result.error, 'error'); return; }

                showNotification(result.success || 'Status atualizado com sucesso!', 'success');
                renderAgenda();
                renderUpcoming();
                renderNextAppointment();
                renderMetrics();
                renderStatusChart();
            } catch (error) {
                console.error('Erro ao atualizar status:', error);
                showNotification('Erro ao atualizar status da consulta.', 'error');
            }
        }

        async function showPatientModal(bi) {
            if (!bi) return;
            openModal('patient-modal');
            const body = document.getElementById('patient-modal-body');
            body.innerHTML = `<div class="empty-state"><span class="loading-spinner"></span> Carregando dados...</div>`;

            try {
                const data = await fetchJSON(AJAX_URL + `/medico/patient/${bi}`);
                if (data.error) {
                    body.innerHTML = `<div class="empty-state">${data.error}</div>`;
                    return;
                }

                const historyItems = (data.history || []).map(h => `
                    <div class="history-row">
                        <span>${h.date ? new Date(h.date).toLocaleDateString('pt-PT') : '—'} — ${h.reason || 'Consulta'}</span>
                        <span class="status-badge ${(h.status || '').toLowerCase()}">${h.status_label || h.status || ''}</span>
                    </div>
                `).join('') || '<p class="text-xs text-gray-500 py-2">Sem histórico de consultas anteriores.</p>';

                body.innerHTML = `
                    <div class="info-grid">
                        <div class="info-item"><div class="label">Nome</div><div class="val">${data.name || '—'}</div></div>
                        <div class="info-item"><div class="label">BI</div><div class="val">${data.bi || bi}</div></div>
                        <div class="info-item"><div class="label">Idade</div><div class="val">${data.age !== undefined ? data.age + ' anos' : '—'}</div></div>
                        <div class="info-item"><div class="label">Telefone</div><div class="val">${data.phone || '—'}</div></div>
                        <div class="info-item"><div class="label">Alergias</div><div class="val">${data.allergies || 'Nenhuma registada'}</div></div>
                        <div class="info-item"><div class="label">Condições Crónicas</div><div class="val">${data.chronic_conditions || 'Nenhuma registada'}</div></div>
                    </div>
                    <h4 class="text-sm font-semibold text-gray-700 mb-1">Histórico de Consultas</h4>
                    <div class="history-list">${historyItems}</div>
                `;
            } catch (e) {
                console.error('Erro ao buscar paciente:', e);
                body.innerHTML = `<div class="empty-state">Erro ao carregar dados do paciente.</div>`;
            }
        }

        // ==================== INICIALIZAÇÃO ====================
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
                    setTimeout(() => {
                        [trendChart, statusChart, newReturnChart, ageChart].forEach(c => c && c.resize());
                    }, 320);
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

            document.getElementById('logout-btn').addEventListener('click', function() {
                if (confirm('Tem certeza que deseja sair?')) {
                    window.location.href = SITE_URL + '/auth/logout';
                }
            });

            // Agenda controls
            const datePicker = document.getElementById('agenda-date-picker');
            datePicker.value = todayISO();
            datePicker.addEventListener('change', renderAgenda);
            document.getElementById('agenda-status-filter').addEventListener('change', renderAgenda);
            let searchTimeout;
            document.getElementById('agenda-search').addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(renderAgenda, 300);
            });

            setHeaderDates();
            renderMetrics();
            renderAllCharts();
            renderNextAppointment();
            renderAgenda();
            renderUpcoming();

            // Atualização periódica
            setInterval(() => {
                renderMetrics();
                renderNextAppointment();
            }, 60000);
        });
    </script>
</body>
</html>