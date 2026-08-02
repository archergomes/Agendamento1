<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Administrador - Hospital Matlhovele</title>
    <meta name="description" content="Dashboard de administração do Hospital Público de Matlhovele">
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

        h1,
        h2,
        h3,
        .display-font {
            font-family: 'Outfit', 'Roboto', sans-serif;
        }

        /* ---------- Notification ---------- */
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

        #notification.error {
            background-color: #ef4444;
        }

        #notification.success {
            background-color: #0d9488;
        }

        #notification.info {
            background-color: #3b82f6;
        }

        #notification.warning {
            background-color: #f59e0b;
        }

        #notification.show {
            display: block;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                transform: translateX(110%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* ---------- Sidebar ---------- */
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

        .sidebar.show {
            transform: translateX(0);
        }

        .sidebar.desktop {
            transform: translateX(0);
        }

        .sidebar.desktop.expanded {
            width: 260px;
        }

        .sidebar.desktop .sidebar-text {
            display: none;
        }

        .sidebar.desktop.expanded .sidebar-text {
            display: inline;
        }

        .sidebar.desktop .sidebar-header {
            justify-content: center;
            padding: 1rem;
        }

        .sidebar.desktop.expanded .sidebar-header {
            justify-content: space-between;
            padding: 1rem 1.25rem;
        }

        .sidebar-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }

        .sidebar-header h2 {
            color: white;
        }

        .sidebar-header button {
            color: rgba(255, 255, 255, 0.85);
        }

        .sidebar-header button:hover {
            color: white;
        }

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

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 899;
        }

        .sidebar-overlay.show {
            display: block;
        }

        @media (min-width: 768px) {
            #mobile-menu-btn {
                display: none;
            }

            .sidebar.desktop {
                display: flex;
            }
        }

        @media (max-width: 767px) {
            .sidebar.desktop {
                display: none;
            }

            .sidebar {
                transform: translateX(-100%);
                width: 260px;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .page-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
            }

            .page-wrapper.expanded {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            height: calc(100% - 64px);
            padding: 0.5rem;
        }

        .main-menu {
            overflow-y: auto;
            flex-grow: 1;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.35) transparent;
        }

        .main-menu::-webkit-scrollbar {
            width: 6px;
        }

        .main-menu::-webkit-scrollbar-track {
            background: transparent;
        }

        .main-menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.35);
            border-radius: 3px;
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

        /* ---------- Metric cards ---------- */
        .metric-card {
            background-color: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
            padding: 1.25rem 1.4rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform 0.2s, box-shadow 0.2s;
            border: 1px solid #eef1f6;
        }

        .metric-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(17, 24, 39, 0.08);
        }

        .metric-card .icon-wrap {
            width: 52px;
            height: 52px;
            border-radius: 0.65rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        .metric-card p.value {
            font-size: 1.9rem;
            font-weight: 700;
            line-height: 1.1;
            font-family: 'Outfit', sans-serif;
        }

        .metric-card h3 {
            font-size: 0.8rem;
            color: var(--ink-500);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            margin-bottom: 0.15rem;
        }

        .metric-trend {
            font-size: 0.72rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 3px;
            margin-top: 2px;
        }

        .metric-trend.up {
            color: #0d9488;
        }

        .metric-trend.down {
            color: #ef4444;
        }

        .metric-trend.flat {
            color: var(--ink-500);
        }

        /* ---------- Chart cards ---------- */
        .chart-card {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #eef1f6;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            padding: 1.25rem 1.4rem 0.75rem;
            display: flex;
            flex-direction: column;
        }

        .chart-card .chart-title {
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 1rem;
            color: var(--ink-900);
        }

        .chart-card .chart-sub {
            font-size: 0.78rem;
            color: var(--ink-500);
            margin-bottom: 0.5rem;
        }

        .chart-canvas-wrap {
            position: relative;
            flex: 1;
            min-height: 230px;
        }

        .chart-empty {
            display: none;
            position: absolute;
            inset: 0;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: var(--ink-500);
            font-size: 0.8rem;
            text-align: center;
            gap: 0.4rem;
        }

        .legend-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            display: inline-block;
        }

        /* ---------- Search ---------- */
        .search-box {
            position: relative;
        }

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
            outline: none;
            border-color: var(--brand-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }

        .search-box i {
            position: absolute;
            left: 0.9rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        /* ---------- Table ---------- */
        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        thead th {
            background-color: #f3f5f9;
            padding: 0.75rem 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--ink-700);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        tbody td {
            padding: 0.8rem 1rem;
            border-bottom: 1px solid #eef1f6;
            vertical-align: middle;
        }

        tbody tr:hover {
            background-color: #f9fafc;
        }

        .btn-sm {
            padding: 0.3rem 0.8rem;
            font-size: 0.75rem;
            border-radius: 0.4rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            font-weight: 500;
        }

        .btn-sm:hover {
            transform: scale(1.05);
        }

        .btn-edit {
            background-color: var(--brand-500);
            color: white;
        }

        .btn-edit:hover {
            background-color: var(--brand-600);
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
        }

        .btn-success {
            background-color: var(--teal-500);
            color: white;
        }

        .btn-success:hover {
            background-color: var(--teal-600);
        }

        .status-badge {
            padding: 0.3rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.72rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .status-badge.pendente {
            background-color: #fef3c7;
            color: #92400e;
        }

        .status-badge.confirmado {
            background-color: #ccfbf1;
            color: #0f766e;
        }

        .status-badge.cancelado {
            background-color: #fee2e2;
            color: #991b1b;
        }

        /* ---------- Modal ---------- */
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
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .modal-content {
            background-color: white;
            padding: 1.5rem;
            border-radius: 0.85rem;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.35);
            animation: slideUp 0.3s ease-out;
            max-height: 90vh;
            overflow-y: auto;
        }

        @keyframes slideUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
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
            font-size: 1.15rem;
            font-weight: 600;
            color: #1f2937;
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
            color: #1f2937;
            background-color: #f3f4f6;
        }

        .modal-body {
            margin-bottom: 1.5rem;
        }

        .modal-footer {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
            border-top: 2px solid #f3f4f6;
            padding-top: 1rem;
            flex-wrap: wrap;
        }

        .form-group {
            margin-bottom: 1rem;
        }

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
            padding: 0.55rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            transition: border-color 0.2s;
            font-size: 0.875rem;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--brand-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-group .input-error {
            border-color: #ef4444;
        }

        .form-group .input-error:focus {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
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

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        .empty-state {
            text-align: center;
            padding: 2rem 1rem;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 2rem;
            color: #d1d5db;
            margin-bottom: 0.5rem;
        }

        .loading-spinner {
            display: inline-block;
            width: 1.3rem;
            height: 1.3rem;
            border: 3px solid #e5e7eb;
            border-top-color: var(--brand-500);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .card-panel {
            background-color: white;
            border-radius: 0.75rem;
            border: 1px solid #eef1f6;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
        }

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
            0% {
                stroke-dashoffset: 300;
            }

            55% {
                stroke-dashoffset: 0;
            }

            100% {
                stroke-dashoffset: -300;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            .pulse-path {
                animation: none;
                stroke-dashoffset: 0;
            }
        }

        /* Loading skeleton para métricas */
        .metric-loading {
            display: inline-block;
            width: 40px;
            height: 28px;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 4px;
        }

        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        @media (max-width: 640px) {
            .metric-card {
                padding: 0.9rem 1rem;
            }

            .metric-card p.value {
                font-size: 1.5rem;
            }

            .main-content {
                padding: 0.5rem;
            }

            table {
                font-size: 0.75rem;
            }

            thead th,
            tbody td {
                padding: 0.55rem;
            }

            .pulse-line {
                display: none;
            }
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
                <a href="<?= site_url('admin') ?>" class="active">
                    <i class="fas fa-chart-pie"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
                <a href="<?= site_url('admin/pacientes') ?>">
                    <i class="fas fa-users"></i>
                    <span class="sidebar-text">Pacientes</span>
                </a>
                <a href="<?= site_url('admin/medicos') ?>">
                    <i class="fas fa-user-md"></i>
                    <span class="sidebar-text">Médicos</span>
                </a>
                <a href="<?= site_url('admin/secretarios') ?>">
                    <i class="fas fa-user-tie"></i>
                    <span class="sidebar-text">Secretários</span>
                </a>
                <a href="<?= site_url('admin/agendamentos') ?>">
                    <i class="fas fa-calendar-check"></i>
                    <span class="sidebar-text">Agendamentos</span>
                </a>
                <a href="<?= site_url('admin/cad_paciente') ?>">
                    <i class="fas fa-user-plus"></i>
                    <span class="sidebar-text">Cadastrar Paciente</span>
                </a>
                <a href="<?= site_url('admin/cad_secretario') ?>">
                    <i class="fas fa-user-plus"></i>
                    <span class="sidebar-text">Cadastrar Secretário</span>
                </a>
                <a href="<?= site_url('admin/cad_medico') ?>">
                    <i class="fas fa-user-plus"></i>
                    <span class="sidebar-text">Cadastrar Médico</span>
                </a>
                <a href="<?= site_url('admin/relatorios') ?>">
                    <i class="fas fa-chart-bar"></i>
                    <span class="sidebar-text">Relatórios</span>
                </a>
                <a href="<?= site_url('admin/configuracoes') ?>">
                    <i class="fas fa-cog"></i>
                    <span class="sidebar-text">Configurações</span>
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
        <!-- Header -->
        <header class="text-white shadow-lg">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <i class="fas fa-hospital-alt text-2xl" aria-label="Ícone do Hospital Matlhovele"></i>
                    <div>
                        <h1 class="text-xl font-bold leading-tight">Hospital Matlhovele</h1>
                        <p class="text-xs text-blue-100 opacity-90">Painel de Administração</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <svg class="pulse-line hidden sm:block" viewBox="0 0 140 40" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path class="pulse-path" d="M0 20 H35 L45 6 L55 34 L65 14 L72 20 H140" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" />
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
                <div class="mb-6 flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800 mb-1">Visão Geral</h2>
                        <p class="text-gray-500 text-sm" id="current-date"></p>
                    </div>
                    <button onclick="refreshData()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center gap-2 text-sm">
                        <i class="fas fa-sync-alt"></i> Atualizar
                    </button>
                </div>

                <!-- Metrics -->
                <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8" id="metrics-grid">
                    <div class="metric-card">
                        <div class="icon-wrap" style="background:#dbeafe; color:#2563eb;"><i class="fas fa-users"></i></div>
                        <div>
                            <h3>Pacientes</h3>
                            <p id="total-patients" class="value">0</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="icon-wrap" style="background:#ccfbf1; color:#0d9488;"><i class="fas fa-user-md"></i></div>
                        <div>
                            <h3>Médicos</h3>
                            <p id="total-doctors" class="value">0</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="icon-wrap" style="background:#fde7d5; color:#c2410c;"><i class="fas fa-user-tie"></i></div>
                        <div>
                            <h3>Secretários</h3>
                            <p id="total-secretaries" class="value">0</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="icon-wrap" style="background:#ede9fe; color:#7c3aed;"><i class="fas fa-calendar-day"></i></div>
                        <div>
                            <h3>Hoje</h3>
                            <p id="appointments-today" class="value">0</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="icon-wrap" style="background:#dbeafe; color:#1d4ed8;"><i class="fas fa-calendar-check"></i></div>
                        <div>
                            <h3>Futuros</h3>
                            <p id="upcoming-appointments" class="value">0</p>
                        </div>
                    </div>
                    <div class="metric-card">
                        <div class="icon-wrap" style="background:#fee2e2; color:#dc2626;"><i class="fas fa-calendar-times"></i></div>
                        <div>
                            <h3>Cancelados</h3>
                            <p id="cancelled-appointments" class="value">0</p>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-8">
                    <div class="chart-card lg:col-span-2">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="chart-title">Agendamentos — últimos 14 dias</div>
                                <div class="chart-sub">Volume diário de consultas marcadas</div>
                            </div>
                            <span class="text-xs font-semibold text-teal-700 bg-teal-50 px-2 py-1 rounded-full" id="trend-badge">—</span>
                        </div>
                        <div class="chart-canvas-wrap">
                            <canvas id="chart-appointments-trend"></canvas>
                            <div class="chart-empty" id="empty-appointments-trend">
                                <i class="fas fa-chart-line text-2xl"></i>
                                <span>Sem dados suficientes para exibir a tendência.</span>
                            </div>
                        </div>
                    </div>

                    <div class="chart-card">
                        <div class="chart-title">Status dos Agendamentos</div>
                        <div class="chart-sub">Distribuição atual</div>
                        <div class="chart-canvas-wrap" style="min-height:200px;">
                            <canvas id="chart-status"></canvas>
                            <div class="chart-empty" id="empty-status">
                                <i class="fas fa-calendar-check text-2xl"></i>
                                <span>Nenhum agendamento registado.</span>
                            </div>
                        </div>
                        <div class="flex justify-center gap-4 text-xs text-gray-600 pb-3 pt-1" id="status-legend"></div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-8">
                    <div class="chart-card">
                        <div class="chart-title">Médicos por Especialidade</div>
                        <div class="chart-sub">Corpo clínico atual</div>
                        <div class="chart-canvas-wrap">
                            <canvas id="chart-specialty"></canvas>
                            <div class="chart-empty" id="empty-specialty">
                                <i class="fas fa-user-md text-2xl"></i>
                                <span>Nenhum médico cadastrado ainda.</span>
                            </div>
                        </div>
                    </div>
                    <div class="chart-card">
                        <div class="chart-title">Novos Pacientes por Mês</div>
                        <div class="chart-sub">Últimos 6 meses</div>
                        <div class="chart-canvas-wrap">
                            <canvas id="chart-patients-monthly"></canvas>
                            <div class="chart-empty" id="empty-patients-monthly">
                                <i class="fas fa-user-plus text-2xl"></i>
                                <span>Sem registos suficientes.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="mb-6">
                    <div class="search-box max-w-lg">
                        <i class="fas fa-search"></i>
                        <input type="text" id="search-input" placeholder="Pesquisar por nome ou BI (pacientes ou médicos)..." aria-label="Pesquisar">
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card-panel">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Atividade Recente</h3>
                        <span class="text-xs text-gray-400" id="activity-count"></span>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Detalhes</th>
                                    <th>Data</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="activity-list">
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
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-6">
            <div class="container mx-auto px-4 text-center text-gray-400 text-sm">
                <p>© <?= date('Y') ?> Hospital Público de Matlhovele. Todos os direitos reservados.</p>
            </div>
        </footer>
    </div>

    <!-- ==================== MODAIS ==================== -->

    <!-- Modal de Edição de Paciente -->
    <div id="edit-patient-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-user-edit text-blue-500 mr-2"></i>Editar Paciente</h3>
                <button class="modal-close" onclick="closeModal('edit-patient-modal')">&times;</button>
            </div>
            <form id="edit-patient-form" onsubmit="savePatient(event)">
                <input type="hidden" id="edit-patient-bi" name="bi">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit-patient-name">Nome Completo <span class="text-red-500">*</span></label>
                        <input type="text" id="edit-patient-name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-patient-phone">Telefone <span class="text-red-500">*</span></label>
                        <input type="tel" id="edit-patient-phone" name="phone" required placeholder="+258 8X XXXXXXX">
                        <p class="text-xs text-gray-500 mt-1">Formato: +258 8X XXXXXXX</p>
                    </div>
                    <div class="form-group">
                        <label for="edit-patient-email">Email</label>
                        <input type="email" id="edit-patient-email" name="email" placeholder="exemplo@email.com">
                    </div>
                    <div class="form-group">
                        <label for="edit-patient-bi-display">BI</label>
                        <input type="text" id="edit-patient-bi-display" readonly class="bg-gray-100">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('edit-patient-modal')">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="save-patient-btn">
                        <i class="fas fa-save mr-1"></i>Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Edição de Médico -->
    <div id="edit-doctor-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-user-md text-teal-600 mr-2"></i>Editar Médico</h3>
                <button class="modal-close" onclick="closeModal('edit-doctor-modal')">&times;</button>
            </div>
            <form id="edit-doctor-form" onsubmit="saveDoctor(event)">
                <input type="hidden" id="edit-doctor-bi" name="bi">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit-doctor-name">Nome Completo <span class="text-red-500">*</span></label>
                        <input type="text" id="edit-doctor-name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-doctor-specialty">Especialidade <span class="text-red-500">*</span></label>
                        <input type="text" id="edit-doctor-specialty" name="specialty" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-doctor-phone">Telefone <span class="text-red-500">*</span></label>
                        <input type="tel" id="edit-doctor-phone" name="phone" required placeholder="+258 8X XXXXXXX">
                        <p class="text-xs text-gray-500 mt-1">Formato: +258 8X XXXXXXX</p>
                    </div>
                    <div class="form-group">
                        <label for="edit-doctor-email">Email</label>
                        <input type="email" id="edit-doctor-email" name="email" placeholder="exemplo@email.com">
                    </div>
                    <div class="form-group">
                        <label for="edit-doctor-bi-display">BI</label>
                        <input type="text" id="edit-doctor-bi-display" readonly class="bg-gray-100">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('edit-doctor-modal')">Cancelar</button>
                    <button type="submit" class="btn btn-success" id="save-doctor-btn">
                        <i class="fas fa-save mr-1"></i>Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // ==================== VERIFICAR CHART.JS ====================
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js não carregou. Tentando carregar novamente...');
            const script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js';
            script.onload = function() {
                console.log('Chart.js carregado com sucesso!');
                renderAllCharts();
            };
            document.head.appendChild(script);
        }
        
        // ==================== FUNÇÕES UTILITÁRIAS ====================

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
            setTimeout(() => {
                notification.classList.remove('show');
            }, 5000);
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        function openModal(modalId) {
            document.getElementById(modalId).classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function setDateHeader() {
            const el = document.getElementById('current-date');
            if (!el) return;
            const now = new Date();
            const formatted = now.toLocaleDateString('pt-PT', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            el.textContent = formatted.charAt(0).toUpperCase() + formatted.slice(1);
        }

        async function fetchJSON(url) {
            const response = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
        }

        // ==================== REFRESH DATA ====================
        function refreshData() {
            renderMetrics();
            renderAllCharts();
            renderActivity(document.getElementById('search-input')?.value || '');
            showNotification('Dados atualizados com sucesso!', 'success');
        }

        // ==================== MÉTRICAS ====================
        async function renderMetrics() {
            const fields = {
                total_patients: 'total-patients',
                total_doctors: 'total-doctors',
                total_secretaries: 'total-secretaries',
                appointments_today: 'appointments-today',
                upcoming_appointments: 'upcoming-appointments',
                cancelled_appointments: 'cancelled-appointments'
            };

            try {
                const data = await fetchJSON(AJAX_URL + '/admin/metrics');
                if (data.error) {
                    showNotification(data.error, 'error');
                    return;
                }

                Object.entries(fields).forEach(([key, id]) => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = (data[key] !== undefined && data[key] !== null) ? data[key] : 0;
                });
            } catch (error) {
                console.error('Erro ao buscar métricas:', error);
                showNotification('Erro ao carregar métricas.', 'error');
                Object.values(fields).forEach(id => {
                    const el = document.getElementById(id);
                    if (el) el.textContent = '—';
                });
            }
        }

        // ==================== GRÁFICOS ====================
        const CHART_COLORS = {
            brand: '#2563eb',
            brandFill: 'rgba(37, 99, 235, 0.12)',
            teal: '#0d9488',
            amber: '#f59e0b',
            rose: '#ef4444',
            slate: '#94a3b8'
        };
        Chart.defaults.font.family = "'Roboto', sans-serif";
        Chart.defaults.color = '#6b7280';
        Chart.defaults.plugins.legend.labels.usePointStyle = true;

        let trendChart, statusChart, specialtyChart, patientsMonthlyChart;

        function toggleEmptyState(emptyId, isEmpty) {
            const el = document.getElementById(emptyId);
            if (el) el.style.display = isEmpty ? 'flex' : 'none';
        }

        async function renderAppointmentsTrend() {
            const ctx = document.getElementById('chart-appointments-trend');
            const badge = document.getElementById('trend-badge');
            if (!ctx) return;

            try {
                const data = await fetchJSON(AJAX_URL + '/admin/chart_appointments_trend');
                const labels = data.labels || [];
                const values = data.data || [];
                const total = values.reduce((a, b) => a + Number(b || 0), 0);
                toggleEmptyState('empty-appointments-trend', total === 0);

                if (badge) {
                    if (total === 0) {
                        badge.textContent = 'Sem dados';
                        badge.className = 'text-xs font-semibold text-gray-500 bg-gray-100 px-2 py-1 rounded-full';
                    } else {
                        const avg = (total / values.length).toFixed(1);
                        badge.textContent = '📊 Média: ' + avg + '/dia';
                        badge.className = 'text-xs font-semibold text-teal-700 bg-teal-50 px-2 py-1 rounded-full';
                    }
                }

                if (trendChart) trendChart.destroy();
                trendChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Agendamentos',
                            data: values,
                            borderColor: CHART_COLORS.brand,
                            backgroundColor: CHART_COLORS.brandFill,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 3,
                            pointBackgroundColor: CHART_COLORS.brand,
                            borderWidth: 2.5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                },
                                grid: {
                                    color: '#f1f5f9'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Erro ao buscar tendência de agendamentos:', error);
                toggleEmptyState('empty-appointments-trend', true);
                if (badge) {
                    badge.textContent = '⚠️ Erro';
                    badge.className = 'text-xs font-semibold text-red-700 bg-red-50 px-2 py-1 rounded-full';
                }
            }
        }

        async function renderStatusChart() {
            const ctx = document.getElementById('chart-status');
            const legendEl = document.getElementById('status-legend');
            if (!ctx) return;

            try {
                const data = await fetchJSON(AJAX_URL + '/admin/chart_appointments_status');
                const map = {
                    pendente: {
                        label: 'Pendente',
                        color: CHART_COLORS.amber
                    },
                    confirmado: {
                        label: 'Confirmado',
                        color: CHART_COLORS.teal
                    },
                    cancelado: {
                        label: 'Cancelado',
                        color: CHART_COLORS.rose
                    }
                };
                const keys = Object.keys(map);
                const values = keys.map(k => Number(data[k] || 0));
                const total = values.reduce((a, b) => a + b, 0);
                toggleEmptyState('empty-status', total === 0);

                if (legendEl) {
                    legendEl.innerHTML = keys.map((k, i) => `
                        <span class="inline-flex items-center gap-1">
                            <span class="legend-dot" style="background:${map[k].color}"></span>${map[k].label} (${values[i]})
                        </span>`).join('');
                }

                if (statusChart) statusChart.destroy();
                statusChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: keys.map(k => map[k].label),
                        datasets: [{
                            data: values,
                            backgroundColor: keys.map(k => map[k].color),
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
                                display: false
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Erro ao buscar status de agendamentos:', error);
                toggleEmptyState('empty-status', true);
            }
        }

        async function renderSpecialtyChart() {
            const ctx = document.getElementById('chart-specialty');
            if (!ctx) return;

            try {
                const data = await fetchJSON(AJAX_URL + '/admin/chart_doctors_specialty');
                const labels = data.labels || [];
                const values = data.data || [];
                toggleEmptyState('empty-specialty', labels.length === 0);

                if (specialtyChart) specialtyChart.destroy();
                specialtyChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Médicos',
                            data: values,
                            backgroundColor: CHART_COLORS.teal,
                            borderRadius: 6,
                            maxBarThickness: 34
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        indexAxis: 'y',
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                },
                                grid: {
                                    color: '#f1f5f9'
                                }
                            },
                            y: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Erro ao buscar médicos por especialidade:', error);
                toggleEmptyState('empty-specialty', true);
            }
        }

        async function renderPatientsMonthlyChart() {
            const ctx = document.getElementById('chart-patients-monthly');
            if (!ctx) return;

            try {
                const data = await fetchJSON(AJAX_URL + '/admin/chart_patients_monthly');
                const labels = data.labels || [];
                const values = data.data || [];
                const total = values.reduce((a, b) => a + Number(b || 0), 0);
                toggleEmptyState('empty-patients-monthly', total === 0);

                if (patientsMonthlyChart) patientsMonthlyChart.destroy();
                patientsMonthlyChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Novos pacientes',
                            data: values,
                            backgroundColor: CHART_COLORS.brand,
                            borderRadius: 6,
                            maxBarThickness: 40
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                },
                                grid: {
                                    color: '#f1f5f9'
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Erro ao buscar pacientes por mês:', error);
                toggleEmptyState('empty-patients-monthly', true);
            }
        }

        function renderAllCharts() {
            renderAppointmentsTrend();
            renderStatusChart();
            renderSpecialtyChart();
            renderPatientsMonthlyChart();
        }

        // ==================== ATIVIDADES ====================
        async function renderActivity(searchQuery = '') {
            const list = document.getElementById('activity-list');
            const countEl = document.getElementById('activity-count');
            if (!list) return;

            try {
                const url = AJAX_URL + '/admin/activity' + (searchQuery ? `?query=${encodeURIComponent(searchQuery)}` : '');
                const activities = await fetchJSON(url);

                if (countEl) countEl.textContent = activities.length ? `${activities.length} registo(s)` : '';

                if (!activities || activities.length === 0) {
                    list.innerHTML = `<tr><td colspan="4" class="py-4 text-center text-gray-500">
                        <i class="fas fa-inbox text-2xl block mb-2 text-gray-300"></i>
                        Nenhuma atividade encontrada.
                    </td></tr>`;
                    return;
                }

                list.innerHTML = activities.map(activity => {
                    const typeIcon = {
                        patient: 'fa-user',
                        doctor: 'fa-user-md',
                        appointment: 'fa-calendar-check',
                        secretary: 'fa-user-tie'
                    } [activity.type] || 'fa-circle';

                    const typeColor = {
                        patient: 'text-blue-500',
                        doctor: 'text-teal-600',
                        appointment: 'text-purple-500',
                        secretary: 'text-orange-500'
                    } [activity.type] || 'text-gray-500';

                    let statusBadge = '';
                    if (activity.status) {
                        const statusKey = String(activity.status).toLowerCase();
                        statusBadge = ` <span class="status-badge ${statusKey}">${activity.status}</span>`;
                    }

                    return `
                        <tr>
                            <td>
                                <i class="fas ${typeIcon} ${typeColor} mr-2"></i>
                                <span class="capitalize">${activity.type}</span>
                            </td>
                            <td>${activity.details || '—'}${statusBadge}</td>
                            <td>${activity.date ? new Date(activity.date).toLocaleString('pt-PT') : '—'}</td>
                            <td class="text-center">
                                ${activity.action_type && activity.action_type !== 'appointment' ? `
                                    <button class="btn-sm btn-edit" onclick="showEditModal('${activity.action_type}', '${activity.bi}')">
                                        <i class="fas fa-edit"></i> Editar
                                    </button>
                                ` : '-'}
                            </td>
                        </tr>
                    `;
                }).join('');

            } catch (error) {
                console.error('Erro ao buscar atividades:', error);
                list.innerHTML = `<tr><td colspan="4" class="py-4 text-center text-gray-500">Erro ao carregar atividades.</td></tr>`;
            }
        }

        // ==================== MODAIS DE EDIÇÃO ====================

        function showEditModal(type, bi) {
            if (type === 'patient') showEditPatientModal(bi);
            else if (type === 'doctor') showEditDoctorModal(bi);
        }

        async function showEditPatientModal(bi) {
            try {
                const data = await fetchJSON(AJAX_URL + `/admin/patient/${bi}`);
                if (data.error) {
                    showNotification(data.error, 'error');
                    return;
                }

                document.getElementById('edit-patient-bi').value = bi;
                document.getElementById('edit-patient-name').value = data.name || '';
                document.getElementById('edit-patient-phone').value = data.phone || '';
                document.getElementById('edit-patient-email').value = data.email || '';
                document.getElementById('edit-patient-bi-display').value = bi;

                openModal('edit-patient-modal');
            } catch (error) {
                console.error('Erro ao buscar paciente:', error);
                showNotification('Erro ao carregar dados do paciente.', 'error');
            }
        }

        async function showEditDoctorModal(bi) {
            try {
                const data = await fetchJSON(AJAX_URL + `/admin/doctor/${bi}`);
                if (data.error) {
                    showNotification(data.error, 'error');
                    return;
                }

                document.getElementById('edit-doctor-bi').value = bi;
                document.getElementById('edit-doctor-name').value = data.name || '';
                document.getElementById('edit-doctor-specialty').value = data.specialty || '';
                document.getElementById('edit-doctor-phone').value = data.phone || '';
                document.getElementById('edit-doctor-email').value = data.email || '';
                document.getElementById('edit-doctor-bi-display').value = bi;

                openModal('edit-doctor-modal');
            } catch (error) {
                console.error('Erro ao buscar médico:', error);
                showNotification('Erro ao carregar dados do médico.', 'error');
            }
        }

        // ==================== SALVAR EDIÇÕES ====================

        async function savePatient(event) {
            event.preventDefault();

            const bi = document.getElementById('edit-patient-bi').value;
            const name = document.getElementById('edit-patient-name').value.trim();
            const phone = document.getElementById('edit-patient-phone').value.trim();
            const email = document.getElementById('edit-patient-email').value.trim();

            if (!name || !phone) {
                showNotification('Nome e telefone são obrigatórios.', 'error');
                return;
            }

            const phoneRegex = /^\+258\s*[8][0-9]{8}$/;
            if (!phoneRegex.test(phone.replace(/\s/g, ''))) {
                showNotification('Número de telefone inválido. Use o formato +258 8X XXXXXXX', 'error');
                return;
            }

            const csrfToken = getCsrfToken();
            const formData = new FormData();
            formData.append('bi', bi);
            formData.append('name', name);
            formData.append('phone', phone);
            formData.append('email', email);
            formData.append('csrf_test_name', csrfToken);

            const btn = document.getElementById('save-patient-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Salvando...';

            try {
                const response = await fetch(AJAX_URL + '/admin/update_patient', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                const result = await response.json();

                if (result.error) {
                    showNotification(result.error, 'error');
                    return;
                }

                showNotification(result.success || 'Paciente atualizado com sucesso!', 'success');
                closeModal('edit-patient-modal');

                renderActivity(document.getElementById('search-input')?.value || '');
                renderMetrics();
                renderPatientsMonthlyChart();

            } catch (error) {
                console.error('Erro ao atualizar paciente:', error);
                showNotification('Erro ao atualizar paciente.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save mr-1"></i>Salvar';
            }
        }

        async function saveDoctor(event) {
            event.preventDefault();

            const bi = document.getElementById('edit-doctor-bi').value;
            const name = document.getElementById('edit-doctor-name').value.trim();
            const specialty = document.getElementById('edit-doctor-specialty').value.trim();
            const phone = document.getElementById('edit-doctor-phone').value.trim();
            const email = document.getElementById('edit-doctor-email').value.trim();

            if (!name || !specialty || !phone) {
                showNotification('Nome, especialidade e telefone são obrigatórios.', 'error');
                return;
            }

            const phoneRegex = /^\+258\s*[8][0-9]{8}$/;
            if (!phoneRegex.test(phone.replace(/\s/g, ''))) {
                showNotification('Número de telefone inválido. Use o formato +258 8X XXXXXXX', 'error');
                return;
            }

            const csrfToken = getCsrfToken();
            const formData = new FormData();
            formData.append('bi', bi);
            formData.append('name', name);
            formData.append('specialty', specialty);
            formData.append('phone', phone);
            formData.append('email', email);
            formData.append('csrf_test_name', csrfToken);

            const btn = document.getElementById('save-doctor-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Salvando...';

            try {
                const response = await fetch(AJAX_URL + '/admin/update_doctor', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                const result = await response.json();

                if (result.error) {
                    showNotification(result.error, 'error');
                    return;
                }

                showNotification(result.success || 'Médico atualizado com sucesso!', 'success');
                closeModal('edit-doctor-modal');

                renderActivity(document.getElementById('search-input')?.value || '');
                renderMetrics();
                renderSpecialtyChart();

            } catch (error) {
                console.error('Erro ao atualizar médico:', error);
                showNotification('Erro ao atualizar médico.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save mr-1"></i>Salvar';
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
                    setTimeout(() => {
                        [trendChart, statusChart, specialtyChart, patientsMonthlyChart].forEach(c => c && c.resize());
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

            // Fechar sidebar ao clicar fora (mobile)
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

            // Busca
            const searchInput = document.getElementById('search-input');
            if (searchInput) {
                let timeoutId;
                searchInput.addEventListener('input', function() {
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(() => {
                        renderActivity(this.value);
                    }, 300);
                });
            }

            // Logout
            const logoutBtn = document.getElementById('logout-btn');
            if (logoutBtn) {
                logoutBtn.addEventListener('click', function() {
                    if (confirm('Tem certeza que deseja sair?')) {
                        window.location.href = SITE_URL + '/auth/logout';
                    }
                });
            }

            // Inicialização
            setDateHeader();
            renderMetrics();
            renderActivity();
            renderAllCharts();

            // Atualiza a cada 60 segundos
            setInterval(() => {
                renderMetrics();
            }, 60000);
        });
    </script>
</body>

</html>