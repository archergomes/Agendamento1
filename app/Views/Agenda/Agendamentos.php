<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Agendamentos - Centro de Saúde Da Matola II</title>
    <meta name="description" content="Visualize e gerencie seus agendamentos no Centro de Saúde Da Matola II">
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

        h1, h2, h3, .display-font {
            font-family: 'Outfit', 'Roboto', sans-serif;
        }

        /* ---------- ANIMAÇÃO DO PULSO ---------- */
        .pulse-line {
            width: 120px;
            height: 34px;
            opacity: 0.9;
        }

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

        /* ---------- NOTIFICATION ---------- */
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
        #notification.error { background-color: var(--rose-500); }
        #notification.success { background-color: var(--teal-600); }
        #notification.info { background-color: var(--brand-500); }
        #notification.warning { background-color: var(--amber-500); }
        #notification.show { display: block; animation: slideIn 0.3s ease-out; }
        @keyframes slideIn {
            from { transform: translateX(110%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* ---------- SIDEBAR (mesmo estilo da view agenda) ---------- */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 80px;
            background: linear-gradient(180deg, #0f2f66 0%, #123a80 100%);
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.15);
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

        .sidebar-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }
        .sidebar-header h2 { color: white; }
        .sidebar-header button {
            color: rgba(255, 255, 255, 0.85);
            background: none;
            border: none;
            cursor: pointer;
        }
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
        .page-wrapper.expanded {
            margin-left: 260px;
            width: calc(100% - 260px);
        }

        .main-content {
            flex: 1;
            width: 100%;
            padding: 1rem;
            min-height: calc(100vh - 80px);
        }

        @media (min-width: 768px) {
            #mobile-menu-btn, #sidebar-overlay { display: none; }
        }

        @media (max-width: 767px) {
            .sidebar.desktop {
                transform: translateX(-100%);
                width: 260px;
            }
            .sidebar.show { transform: translateX(0); }
            .page-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
            }
            .page-wrapper.expanded {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }

        #sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 800;
        }
        #sidebar-overlay.show { display: block; }

        .sidebar-nav {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 0.5rem;
        }

        .sidebar-nav a,
        .sidebar-nav button {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 16px;
            margin-bottom: 2px;
            border-radius: 0.5rem;
            color: rgba(255, 255, 255, 0.8);
            transition: background-color 0.2s, color 0.2s;
            font-size: 0.92rem;
            width: 100%;
            text-align: left;
            border: none;
            background: none;
            cursor: pointer;
        }

        .sidebar-nav a:hover,
        .sidebar-nav button:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar-nav a.active {
            background: rgba(255, 255, 255, 0.16);
            color: white;
            box-shadow: inset 3px 0 0 var(--teal-500);
        }

        .sidebar-nav i {
            font-size: 1.3rem;
            width: 26px;
            text-align: center;
        }

        .sidebar.desktop .sidebar-nav a,
        .sidebar.desktop .sidebar-nav button {
            justify-content: center;
            padding: 11px;
        }

        .sidebar.desktop.expanded .sidebar-nav a,
        .sidebar.desktop.expanded .sidebar-nav button {
            justify-content: flex-start;
            padding: 11px 16px;
        }

        .sidebar-nav .logout {
            margin-top: 0.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            padding-top: 0.5rem;
        }

        /* ---------- BOTÕES ---------- */
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
            background-color: var(--teal-500);
            color: white;
        }
        .btn-success:hover {
            background-color: var(--teal-600);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
        }
        .btn-secondary:hover {
            background-color: #d1d5db;
        }

        .btn-danger {
            background-color: var(--rose-500);
            color: white;
        }
        .btn-danger:hover {
            background-color: #dc2626;
            transform: translateY(-1px);
        }

        .btn-sm {
            padding: 0.35rem 0.75rem;
            font-size: 0.75rem;
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        /* ---------- TABELA ---------- */
        .table-container {
            overflow-x: auto;
            border-radius: 0.5rem;
            border: 1px solid #e5e7eb;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        thead {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: white;
        }

        thead th {
            padding: 0.75rem 1rem;
            text-align: left;
            font-weight: 600;
            white-space: nowrap;
        }

        tbody tr {
            border-bottom: 1px solid #e5e7eb;
            transition: background-color 0.2s;
        }

        tbody tr:hover {
            background-color: #f3f4f6;
        }

        tbody td {
            padding: 0.75rem 1rem;
            vertical-align: middle;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        /* ---------- STATUS BADGE ---------- */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .status-badge.pendente {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-badge.confirmado {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-badge.cancelado {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-badge.concluido {
            background-color: #dbeafe;
            color: #1d4ed8;
        }

        /* ---------- FILTROS ---------- */
        .filter-btn {
            padding: 0.375rem 1rem;
            border-radius: 0.375rem;
            font-size: 0.75rem;
            font-weight: 500;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.2s;
            background-color: #f3f4f6;
            color: #6b7280;
        }

        .filter-btn:hover {
            background-color: #e5e7eb;
        }

        .filter-btn.active {
            border-color: var(--brand-500);
            background-color: #eff6ff;
            color: var(--brand-700);
        }

        .filter-btn .count {
            background-color: #d1d5db;
            color: #4b5563;
            padding: 0.125rem 0.5rem;
            border-radius: 1rem;
            font-size: 0.625rem;
            margin-left: 0.25rem;
        }

        .filter-btn.active .count {
            background-color: #bfdbfe;
            color: var(--brand-700);
        }

        /* ---------- BUSCA ---------- */
        .search-box {
            position: relative;
        }

        .search-box input {
            padding: 0.5rem 0.75rem 0.5rem 2rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            width: 100%;
            max-width: 300px;
            transition: all 0.2s;
            background: white;
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--brand-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .search-box i {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        /* ---------- EMPTY STATE ---------- */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: var(--ink-500);
        }

        .empty-state i {
            font-size: 3rem;
            color: #93c5fd;
            margin-bottom: 1rem;
            display: block;
        }

        .empty-state h3 {
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--ink-900);
            margin-bottom: 0.5rem;
        }

        /* ---------- MODAL (mesmo estilo da view agenda) ---------- */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 23, 42, 0.55);
            z-index: 950;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }

        .modal-overlay.show {
            display: flex;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            background-color: white;
            padding: 1.5rem;
            border-radius: 0.85rem;
            max-width: 550px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.35);
            animation: slideUp 0.3s ease-out;
            position: relative;
        }

        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #f3f4f6;
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .modal-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--ink-900);
            font-family: 'Outfit', 'Roboto', sans-serif;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #6b7280;
            cursor: pointer;
            transition: color 0.2s;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }

        .modal-close:hover {
            color: var(--ink-900);
            background-color: #f3f4f6;
        }

        .modal-body { margin-bottom: 1.5rem; }

        .modal-footer {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
            border-top: 2px solid #f3f4f6;
            padding-top: 1rem;
            flex-wrap: wrap;
        }

        .detail-row {
            display: flex;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #6b7280;
            width: 35%;
            flex-shrink: 0;
        }

        .detail-value {
            color: var(--ink-900);
            width: 65%;
        }

        /* ---------- FORMULÁRIO ---------- */
        .form-group { margin-bottom: 1rem; }

        .form-group label {
            display: block;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            transition: border-color 0.2s;
            font-size: 0.875rem;
            background: white;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--brand-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 60px;
        }

        /* ---------- RESPONSIVO ---------- */
        @media (max-width: 640px) {
            .table-container { font-size: 0.75rem; }
            thead th, tbody td { padding: 0.5rem 0.5rem; }
            .btn-sm { font-size: 0.65rem; padding: 0.125rem 0.375rem; }
            .modal-content { padding: 1rem; }
            .detail-row { flex-direction: column; }
            .detail-label { width: 100%; margin-bottom: 0.25rem; }
            .detail-value { width: 100%; }
        }

        /* ---------- SKELETON LOADING ---------- */
        .skeleton-row {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            gap: 1rem;
        }

        .skeleton-line {
            height: 0.75rem;
            background: linear-gradient(90deg, #eee 25%, #f5f5f5 37%, #eee 63%);
            background-size: 400% 100%;
            animation: skeleton-loading 1.4s ease infinite;
            border-radius: 0.25rem;
        }

        @keyframes skeleton-loading {
            0% { background-position: 100% 50%; }
            100% { background-position: 0 50%; }
        }
    </style>
</head>

<body>
    <!-- Notification -->
    <div id="notification" role="alert">
        <span id="notification-message"></span>
        <button id="notification-close" class="ml-2 text-white hover:text-gray-200">×</button>
    </div>

    <!-- Left Sidebar -->
    <div id="sidebar-menu" class="sidebar desktop">
        <div class="sidebar-header flex justify-between items-center">
            <h2 class="text-lg font-semibold sidebar-text">Menu do Paciente</h2>
            <button id="toggle-sidebar-btn" aria-label="Alternar menu">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <button id="close-sidebar-btn" class="md:hidden close-sidebar-btn" aria-label="Fechar menu">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
            <div class="main-menu">
                <a href="<?= site_url('agenda'); ?>">
                    <i class="fas fa-home"></i>
                    <span class="sidebar-text">Home</span>
                </a>
                <a href="<?= site_url('agenda/agendamentos'); ?>" class="active">
                    <i class="fas fa-calendar-check"></i>
                    <span class="sidebar-text">Meus Agendamentos</span>
                </a>
                <a href="<?= site_url('agenda/perfil'); ?>">
                    <i class="fas fa-user"></i>
                    <span class="sidebar-text">Perfil</span>
                </a>
            </div>
            <button id="logout-btn" class="logout">
                <i class="fas fa-sign-out-alt"></i>
                <span class="sidebar-text">Sair</span>
            </button>
        </nav>
    </div>

    <!-- Overlay para fechar sidebar em mobile -->
    <div id="sidebar-overlay"></div>

    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <!-- Header com animação -->
        <header class="text-white shadow-lg">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <i class="fas fa-hospital-alt text-2xl" aria-label="Ícone do Centro de Saúde Da Matola II"></i>
                    <div>
                        <h1 class="text-xl font-bold leading-tight">Centro de Saúde Da Matola II</h1>
                        <p class="text-xs text-blue-100 opacity-90">Meus Agendamentos</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <!-- Animação do Eletrocardiograma -->
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
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="bg-white rounded-lg shadow-md p-6" style="border:1px solid #eef1f6;">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-800">Meus Agendamentos</h2>
                            <p class="text-gray-500 mt-1">Visualize e gerencie suas consultas agendadas</p>
                        </div>
                        <a href="<?= site_url('agenda'); ?>" class="btn btn-primary">
                            <i class="fas fa-plus mr-1"></i> Novo Agendamento
                        </a>
                    </div>

                    <!-- Filtros e Busca -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                        <div class="flex flex-wrap gap-2" id="filter-container">
                            <button class="filter-btn active" data-filter="all">
                                <i class="fas fa-list mr-1"></i>Todos
                                <span class="count" id="count-all">0</span>
                            </button>
                            <button class="filter-btn" data-filter="pendente">
                                <i class="fas fa-clock mr-1"></i>Pendentes
                                <span class="count" id="count-pendente">0</span>
                            </button>
                            <button class="filter-btn" data-filter="confirmado">
                                <i class="fas fa-check-circle mr-1"></i>Confirmados
                                <span class="count" id="count-confirmado">0</span>
                            </button>
                            <button class="filter-btn" data-filter="cancelado">
                                <i class="fas fa-times-circle mr-1"></i>Cancelados
                                <span class="count" id="count-cancelado">0</span>
                            </button>
                            <button class="filter-btn" data-filter="concluido">
                                <i class="fas fa-check-double mr-1"></i>Concluídos
                                <span class="count" id="count-concluido">0</span>
                            </button>
                        </div>
                        <div class="search-box w-full sm:w-auto">
                            <i class="fas fa-search"></i>
                            <input type="text" id="search-input" placeholder="Buscar por médico, especialidade..." class="w-full sm:w-64">
                        </div>
                    </div>

                    <!-- Loading -->
                    <div id="appointments-loading" class="text-center py-8">
                        <i class="fas fa-spinner fa-spin text-blue-600 text-2xl mr-2"></i>
                        <span class="text-gray-500">Carregando agendamentos...</span>
                    </div>

                    <!-- Tabela -->
                    <div id="appointments-container">
                        <?php if (!empty($agendamentos)): ?>
                            <div class="table-container">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Médico</th>
                                            <th>Especialidade</th>
                                            <th>Data</th>
                                            <th>Horário</th>
                                            <th>Status</th>
                                            <th>Motivo</th>
                                            <th class="text-center">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody id="appointments-table-body">
                                        <?php 
                                        $counter = 0;
                                        foreach ($agendamentos as $appt): 
                                            $counter++;
                                            $isObject = is_object($appt);
                                            $id = $isObject ? $appt->id : $appt['ID_Agendamento'];
                                            $status = $isObject ? $appt->status : $appt['Status'];
                                            $data = $isObject ? $appt->date : $appt['Data_Agendamento'];
                                            $hora = $isObject ? $appt->time : $appt['Hora_Agendamento'];
                                            $especialidade = $isObject ? ($appt->especialidade ?? 'N/A') : ($appt['Especialidade'] ?? 'N/A');
                                            $medico = $isObject ? ($appt->medico ?? 'N/A') : (($appt['medico_nome'] ?? '') . ' ' . ($appt['medico_sobrenome'] ?? ''));
                                            $medico = trim($medico) ?: 'N/A';
                                            $motivo = $isObject ? ($appt->motivo ?? '') : ($appt['Motivo'] ?? '');
                                            
                                            $status_class = strtolower($status);
                                            $status_icon = $status_class === 'pendente' ? 'fa-clock' : 
                                                           ($status_class === 'confirmado' ? 'fa-check-circle' : 
                                                           ($status_class === 'concluido' ? 'fa-check-double' : 'fa-times-circle'));
                                        ?>
                                        <tr data-id="<?= $id ?>" data-status="<?= $status_class ?>">
                                            <td class="text-gray-500"><?= $counter ?></td>
                                            <td>
                                                <div class="font-semibold text-gray-800">Dr. <?= htmlspecialchars($medico) ?></div>
                                            </td>
                                            <td>
                                                <div class="text-gray-600 text-sm"><?= htmlspecialchars($especialidade) ?></div>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($data)) ?></td>
                                            <td><?= substr($hora, 0, 5) ?></td>
                                            <td>
                                                <span class="status-badge <?= $status_class ?>">
                                                    <i class="fas <?= $status_icon ?>" style="font-size: 0.5rem;"></i>
                                                    <?= $status ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="text-gray-600 text-sm max-w-[200px] truncate" title="<?= htmlspecialchars($motivo) ?>">
                                                    <?= htmlspecialchars($motivo ?: '—') ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="flex flex-wrap gap-1 justify-center">
                                                    <button class="btn btn-sm btn-primary" onclick="viewAppointment(<?= $id ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <?php if (in_array($status, ['Pendente', 'Confirmado'])): ?>
                                                        <button class="btn btn-sm btn-success" onclick="editAppointment(<?= $id ?>)">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger" onclick="confirmCancel(<?= $id ?>)">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="fas fa-calendar-times"></i>
                                <h3>Nenhum agendamento encontrado</h3>
                                <p class="text-gray-500 mb-4">Você ainda não possui consultas agendadas.</p>
                                <a href="<?= site_url('agenda'); ?>" class="btn btn-primary">
                                    <i class="fas fa-plus mr-1"></i> Fazer Primeiro Agendamento
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- ==================== MODAIS ==================== -->
    <!-- Modal Visualização -->
    <div id="view-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-file-medical text-blue-500 mr-2"></i>Detalhes do Agendamento</h3>
                <button class="modal-close" onclick="closeModal('view-modal')">&times;</button>
            </div>
            <div class="modal-body" id="view-modal-body"></div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('view-modal')">Fechar</button>
            </div>
        </div>
    </div>

    <!-- Modal Edição -->
    <div id="edit-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-edit text-green-500 mr-2"></i>Editar Agendamento</h3>
                <button class="modal-close" onclick="closeModal('edit-modal')">&times;</button>
            </div>
            <form id="edit-form" onsubmit="saveEdit(event)">
                <input type="hidden" id="edit-id" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit-data"><i class="fas fa-calendar-alt mr-1"></i>Data <span class="text-red-500">*</span></label>
                        <input type="date" id="edit-data" name="data" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-hora"><i class="fas fa-clock mr-1"></i>Horário <span class="text-red-500">*</span></label>
                        <input type="time" id="edit-hora" name="hora" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-motivo"><i class="fas fa-sticky-note mr-1"></i>Motivo</label>
                        <textarea id="edit-motivo" name="motivo" placeholder="Descreva o motivo da consulta..." rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('edit-modal')">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="save-edit-btn">
                        <i class="fas fa-save mr-1"></i>Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Cancelamento -->
    <div id="confirmation-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>Confirmar Cancelamento</h3>
                <button class="modal-close" onclick="closeModal('confirmation-modal')">&times;</button>
            </div>
            <div class="modal-body">
                <p class="text-gray-600">Tem certeza que deseja cancelar este agendamento?</p>
                <p class="text-sm text-red-500 mt-2"><i class="fas fa-info-circle mr-1"></i>Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeModal('confirmation-modal')">Manter</button>
                <button class="btn btn-danger" id="confirm-cancel-btn">
                    <i class="fas fa-times mr-1"></i>Sim, Cancelar
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentCancelId = null;
        let currentViewId = null;
        let currentEditId = null;

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
                if (name === 'csrf_cookie_name') return value;
            }
            return '';
        }

        // ==================== NOTIFICAÇÕES ====================
        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            const messageEl = document.getElementById('notification-message');
            if (!notification || !messageEl) return;
            const icon = type === 'error' ? 'fa-exclamation-circle' :
                type === 'success' ? 'fa-check-circle' :
                type === 'warning' ? 'fa-exclamation-triangle' : 'fa-info-circle';
            messageEl.innerHTML = `<i class="fas ${icon} mr-2"></i>${message}`;
            notification.className = `show ${type}`;
            setTimeout(() => {
                notification.classList.remove('show');
            }, 5000);
        }

        document.getElementById('notification-close')?.addEventListener('click', () => {
            document.getElementById('notification').classList.remove('show');
        });

        // ==================== MODAIS ====================
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('show');
                    document.body.style.overflow = 'auto';
                }
            });
        });

        // ==================== CONTAGEM DE FILTROS ====================
        function updateCounts() {
            const rows = document.querySelectorAll('#appointments-table-body tr');
            const counts = { all: rows.length, pendente: 0, confirmado: 0, cancelado: 0, concluido: 0 };
            
            rows.forEach(row => {
                const status = row.dataset.status;
                if (status === 'pendente') counts.pendente++;
                else if (status === 'confirmado') counts.confirmado++;
                else if (status === 'cancelado') counts.cancelado++;
                else if (status === 'concluido') counts.concluido++;
            });

            document.getElementById('count-all').textContent = counts.all;
            document.getElementById('count-pendente').textContent = counts.pendente;
            document.getElementById('count-confirmado').textContent = counts.confirmado;
            document.getElementById('count-cancelado').textContent = counts.cancelado;
            document.getElementById('count-concluido').textContent = counts.concluido;
        }

        // ==================== FILTROS ====================
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                const filter = this.dataset.filter;
                const rows = document.querySelectorAll('#appointments-table-body tr');
                const searchTerm = document.getElementById('search-input').value.toLowerCase();

                rows.forEach(row => {
                    const status = row.dataset.status;
                    const text = row.textContent.toLowerCase();
                    let show = true;

                    if (filter !== 'all' && status !== filter) show = false;
                    if (searchTerm && !text.includes(searchTerm)) show = false;

                    row.style.display = show ? '' : 'none';
                });
            });
        });

        // ==================== BUSCA ====================
        document.getElementById('search-input').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const activeFilter = document.querySelector('.filter-btn.active')?.dataset?.filter || 'all';
            const rows = document.querySelectorAll('#appointments-table-body tr');

            rows.forEach(row => {
                const status = row.dataset.status;
                const text = row.textContent.toLowerCase();
                let show = true;

                if (activeFilter !== 'all' && status !== activeFilter) show = false;
                if (searchTerm && !text.includes(searchTerm)) show = false;

                row.style.display = show ? '' : 'none';
            });
        });

        // ==================== VISUALIZAR ====================
        function viewAppointment(id) {
            const csrfToken = getCsrfToken();
            const formData = new FormData();
            formData.append('id', id);
            if (csrfToken) formData.append('csrf_test_name', csrfToken);

            fetch('<?= site_url('agenda/get_appointment_details') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const appt = data.data;
                    const statusClass = appt.status.toLowerCase();
                    const statusIcon = appt.status === 'Pendente' ? 'fa-clock' :
                                     appt.status === 'Confirmado' ? 'fa-check-circle' :
                                     appt.status === 'Concluido' ? 'fa-check-double' : 'fa-times-circle';

                    document.getElementById('view-modal-body').innerHTML = `
                        <div class="detail-row">
                            <span class="detail-label"><i class="fas fa-user-md mr-1"></i>Médico</span>
                            <span class="detail-value">Dr. ${appt.medico || 'Não informado'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label"><i class="fas fa-stethoscope mr-1"></i>Especialidade</span>
                            <span class="detail-value">${appt.especialidade || 'Não informada'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label"><i class="fas fa-calendar-alt mr-1"></i>Data</span>
                            <span class="detail-value">${appt.data_formatada || appt.data}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label"><i class="fas fa-clock mr-1"></i>Horário</span>
                            <span class="detail-value">${appt.hora}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label"><i class="fas fa-tag mr-1"></i>Status</span>
                            <span class="detail-value">
                                <span class="status-badge ${statusClass}">
                                    <i class="fas ${statusIcon} mr-1"></i>${appt.status}
                                </span>
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label"><i class="fas fa-sticky-note mr-1"></i>Motivo</span>
                            <span class="detail-value">${appt.motivo || 'Não informado'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label"><i class="fas fa-calendar-plus mr-1"></i>Criado em</span>
                            <span class="detail-value">${appt.criado_em || 'Não informado'}</span>
                        </div>
                    `;
                    openModal('view-modal');
                } else {
                    showNotification(data.message || 'Erro ao carregar detalhes.', 'error');
                }
            })
            .catch(err => {
                console.error('Erro:', err);
                showNotification('Erro ao carregar detalhes.', 'error');
            });
        }

        // ==================== EDITAR ====================
        function editAppointment(id) {
            const csrfToken = getCsrfToken();
            const formData = new FormData();
            formData.append('id', id);
            if (csrfToken) formData.append('csrf_test_name', csrfToken);

            fetch('<?= site_url('agenda/get_appointment_details') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const appt = data.data;
                    document.getElementById('edit-id').value = id;
                    document.getElementById('edit-data').value = appt.data;
                    document.getElementById('edit-hora').value = appt.hora;
                    document.getElementById('edit-motivo').value = appt.motivo || '';
                    openModal('edit-modal');
                } else {
                    showNotification(data.message || 'Erro ao carregar dados.', 'error');
                }
            })
            .catch(err => {
                console.error('Erro:', err);
                showNotification('Erro ao carregar dados para edição.', 'error');
            });
        }

        // ==================== SALVAR EDIÇÃO ====================
        function saveEdit(event) {
            event.preventDefault();

            const id = document.getElementById('edit-id').value;
            const data = document.getElementById('edit-data').value;
            const hora = document.getElementById('edit-hora').value;
            const motivo = document.getElementById('edit-motivo').value;

            if (!data || !hora) {
                showNotification('Preencha a data e horário.', 'warning');
                return;
            }

            const csrfToken = getCsrfToken();
            const formData = new FormData();
            formData.append('id', id);
            formData.append('data', data);
            formData.append('hora', hora);
            formData.append('motivo', motivo);
            if (csrfToken) formData.append('csrf_test_name', csrfToken);

            const btn = document.getElementById('save-edit-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Salvando...';

            fetch('<?= site_url('agenda/update_appointment') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save mr-1"></i>Salvar Alterações';

                if (data.status === 'success') {
                    showNotification(data.message, 'success');
                    closeModal('edit-modal');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showNotification(data.message || 'Erro ao atualizar.', 'error');
                }
            })
            .catch(err => {
                console.error('Erro:', err);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save mr-1"></i>Salvar Alterações';
                showNotification('Erro ao atualizar agendamento.', 'error');
            });
        }

        // ==================== CANCELAR ====================
        function confirmCancel(id) {
            currentCancelId = id;
            openModal('confirmation-modal');
        }

        document.getElementById('confirm-cancel-btn').addEventListener('click', function() {
            if (currentCancelId) {
                const csrfToken = getCsrfToken();
                const formData = new FormData();
                formData.append('id', currentCancelId);
                if (csrfToken) formData.append('csrf_test_name', csrfToken);

                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Cancelando...';

                fetch('<?= site_url('agenda/cancelar_agendamento') ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-times mr-1"></i>Sim, Cancelar';

                    if (data.status === 'success') {
                        showNotification(data.message, 'success');
                        closeModal('confirmation-modal');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification(data.message || 'Erro ao cancelar.', 'error');
                    }
                })
                .catch(err => {
                    console.error('Erro:', err);
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-times mr-1"></i>Sim, Cancelar';
                    showNotification('Erro ao cancelar agendamento.', 'error');
                });
            }
        });

        // ==================== INICIALIZAÇÃO ====================
        document.addEventListener('DOMContentLoaded', function() {
            // Esconder loading
            setTimeout(() => {
                const loading = document.getElementById('appointments-loading');
                if (loading) loading.style.display = 'none';
            }, 500);

            // Atualizar contagens
            updateCounts();

            // Sidebar
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const sidebarMenu = document.getElementById('sidebar-menu');
            const closeSidebarBtn = document.getElementById('close-sidebar-btn');
            const toggleSidebarBtn = document.getElementById('toggle-sidebar-btn');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            const pageWrapper = document.querySelector('.page-wrapper');

            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function() {
                    sidebarMenu.classList.add('show');
                    sidebarOverlay.classList.add('show');
                    pageWrapper.classList.add('expanded');
                });
            }

            if (closeSidebarBtn) {
                closeSidebarBtn.addEventListener('click', function() {
                    sidebarMenu.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                    pageWrapper.classList.remove('expanded');
                });
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    sidebarMenu.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                    pageWrapper.classList.remove('expanded');
                });
            }

            if (toggleSidebarBtn) {
                toggleSidebarBtn.addEventListener('click', function() {
                    sidebarMenu.classList.toggle('expanded');
                    pageWrapper.classList.toggle('expanded');
                });
            }

            // Logout
            document.getElementById('logout-btn').addEventListener('click', function() {
                window.location.href = '<?= site_url('auth/logout') ?>';
            });
        });
    </script>
</body>
</html>