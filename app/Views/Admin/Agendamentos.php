<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamentos - Administrador - Centro de Saúde Da Matola II</title>
    <meta name="description" content="Gerenciar agendamentos no Centro de Saúde Da Matola II">
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

        h1,
        h2,
        h3,
        .display-font {
            font-family: 'Outfit', 'Roboto', sans-serif;
        }

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

        /* Sidebar */
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

        /* Card Panel */
        .card-panel {
            background-color: white;
            border-radius: 0.75rem;
            border: 1px solid #eef1f6;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
        }

        /* Search Box */
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

        /* Table */
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

        .btn-view {
            background-color: #8b5cf6;
            color: white;
        }

        .btn-view:hover {
            background-color: #7c3aed;
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

        .btn-cancel {
            background-color: #f59e0b;
            color: white;
        }

        .btn-cancel:hover {
            background-color: #d97706;
        }

        .btn-primary {
            background-color: var(--brand-500);
            color: white;
            padding: 0.65rem 1.25rem;
            border-radius: 0.55rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary:hover {
            background-color: var(--brand-600);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
            padding: 0.65rem 1.25rem;
            border-radius: 0.55rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background-color: #d1d5db;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 3rem;
            color: #d1d5db;
            margin-bottom: 1rem;
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

        /* Modal */
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
            max-width: 550px;
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

        .detail-row {
            display: flex;
            padding: 0.6rem 0;
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
            color: #1f2937;
            width: 65%;
        }

        .detail-value .sub-detail {
            display: block;
            font-size: 0.8rem;
            color: #6b7280;
            font-weight: 400;
            margin-top: 0.15rem;
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

        .form-group input:read-only {
            background-color: #f3f4f6;
            cursor: not-allowed;
        }

        /* Filter Buttons */
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
            color: #1d4ed8;
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
            color: #1d4ed8;
        }

        /* Status Badges */
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
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-badge.cancelado {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .status-badge.concluido {
            background-color: #dbeafe;
            color: #1e40af;
        }

        /* Tipo Badge */
        .tipo-badge {
            display: inline-block;
            padding: 0.15rem 0.6rem;
            border-radius: 9999px;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        .tipo-badge.proprio {
            background-color: #dbeafe;
            color: #1d4ed8;
        }

        .tipo-badge.outro {
            background-color: #fef3c7;
            color: #92400e;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .pagination button {
            padding: 0.4rem 0.8rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            background: white;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.875rem;
        }

        .pagination button:hover:not(:disabled) {
            background-color: var(--brand-500);
            color: white;
            border-color: var(--brand-500);
        }

        .pagination button.active {
            background-color: var(--brand-500);
            color: white;
            border-color: var(--brand-500);
        }

        .pagination button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Confirm Modal */
        .confirm-icon {
            width: 4rem;
            height: 4rem;
            border-radius: 50%;
            background-color: #fee2e2;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #ef4444;
            margin: 0 auto 1rem;
        }

        .confirm-icon.amber {
            background-color: #fef3c7;
            color: #f59e0b;
        }

        .btn-danger-modal {
            background-color: #ef4444;
            color: white;
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

        .btn-danger-modal:hover {
            background-color: #dc2626;
            transform: translateY(-1px);
        }

        .btn-amber {
            background-color: #f59e0b;
            color: white;
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

        .btn-amber:hover {
            background-color: #d97706;
            transform: translateY(-1px);
        }

        @media (max-width: 640px) {
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

            .btn-sm {
                font-size: 0.65rem;
                padding: 0.2rem 0.5rem;
            }

            .modal-content {
                padding: 1rem;
            }

            .pagination button {
                padding: 0.3rem 0.6rem;
                font-size: 0.75rem;
            }

            .filter-btn {
                font-size: 0.65rem;
                padding: 0.25rem 0.6rem;
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
                <a href="<?= site_url('admin') ?>"><i class="fas fa-chart-pie"></i><span class="sidebar-text">Dashboard</span></a>
                <a href="<?= site_url('admin/pacientes') ?>"><i class="fas fa-users"></i><span class="sidebar-text">Pacientes</span></a>
                <a href="<?= site_url('admin/medicos') ?>"><i class="fas fa-user-md"></i><span class="sidebar-text">Médicos</span></a>
                <a href="<?= site_url('admin/secretarios') ?>"><i class="fas fa-user-tie"></i><span class="sidebar-text">Secretários</span></a>
                <a href="<?= site_url('admin/agendamentos') ?>" class="active"><i class="fas fa-calendar-check"></i><span class="sidebar-text">Agendamentos</span></a>
                <a href="<?= site_url('admin/disponibilidade') ?>"><i class="fas fa-calendar-alt"></i><span class="sidebar-text">Disponibilidade</span></a>
                <a href="<?= site_url('admin/especialidades') ?>"><i class="fas fa-stethoscope"></i><span class="sidebar-text">Especialidades</span></a>
                <a href="<?= site_url('admin/relatorios') ?>"><i class="fas fa-chart-bar"></i><span class="sidebar-text">Relatórios</span></a>
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
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">Lista de Agendamentos</h2>
                        <p class="text-gray-500 text-sm">Gerencie todos os agendamentos do sistema</p>
                    </div>
                    <a href="<?= site_url('admin/cad_agendamento') ?>" class="btn-primary">
                        <i class="fas fa-calendar-plus mr-2"></i> Novo Agendamento
                    </a>
                </div>

                <!-- Filters -->
                <div class="flex flex-wrap items-center gap-2 mb-4" id="filter-container">
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

                <!-- Search Bar -->
                <div class="mb-6">
                    <div class="search-box max-w-lg">
                        <i class="fas fa-search"></i>
                        <input type="text" id="search-input" placeholder="Pesquisar por paciente ou médico..." aria-label="Pesquisar">
                    </div>
                </div>

                <!-- Appointments Table -->
                <div class="card-panel">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th>Tipo</th>
                                    <th>Médico</th>
                                    <th>Especialidade</th>
                                    <th>Data</th>
                                    <th>Hora</th>
                                    <th>Status</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="appointments-table">
                                <tr>
                                    <td colspan="8" class="text-center py-8">
                                        <span class="loading-spinner"></span> Carregando...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="no-results" class="hidden text-center py-8 text-gray-500">
                        <i class="fas fa-search text-2xl block mb-2 text-gray-300"></i>
                        Nenhum agendamento encontrado para a pesquisa.
                    </div>
                    <div id="pagination-container" class="pagination"></div>
                </div>
            </div>
        </main>
    </div>

    <!-- ==================== MODAIS ==================== -->

    <!-- Modal de Visualização de Detalhes -->
    <div id="view-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-calendar-check text-blue-500 mr-2"></i>Detalhes do Agendamento</h3>
                <button class="modal-close" onclick="closeModal('view-modal')">&times;</button>
            </div>
            <div class="modal-body" id="view-modal-body">
                <!-- Preenchido via JavaScript -->
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeModal('view-modal')">Fechar</button>
                <button class="btn-primary" id="view-modal-edit-btn" onclick="editFromView()">
                    <i class="fas fa-edit mr-1"></i>Editar
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Edição -->
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
                        <label for="edit-paciente">Paciente</label>
                        <input type="text" id="edit-paciente" readonly class="bg-gray-100">
                    </div>
                    <div class="form-group">
                        <label for="edit-medico">Médico</label>
                        <input type="text" id="edit-medico" readonly class="bg-gray-100">
                    </div>
                    <div class="form-group">
                        <label for="edit-data">Data <span class="text-red-500">*</span></label>
                        <input type="date" id="edit-data" name="data" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-hora">Horário <span class="text-red-500">*</span></label>
                        <input type="time" id="edit-hora" name="hora" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-status">Status <span class="text-red-500">*</span></label>
                        <select id="edit-status" name="status" required>
                            <option value="Pendente">Pendente</option>
                            <option value="Confirmado">Confirmado</option>
                            <option value="Cancelado">Cancelado</option>
                            <option value="Concluido">Concluído</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="edit-motivo">Motivo</label>
                        <textarea id="edit-motivo" name="motivo" rows="3" placeholder="Motivo da consulta..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="closeModal('edit-modal')">Cancelar</button>
                    <button type="submit" class="btn-primary" id="save-edit-btn">
                        <i class="fas fa-save mr-1"></i>Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div id="confirm-delete-modal" class="modal-overlay">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>Confirmar Exclusão</h3>
                <button class="modal-close" onclick="closeConfirmModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="confirm-icon">
                    <i class="fas fa-trash-alt"></i>
                </div>
                <div id="confirm-delete-message">
                    <p class="text-gray-600">Tem certeza que deseja excluir este agendamento?</p>
                    <p class="text-sm text-red-500 mt-2"><i class="fas fa-info-circle mr-1"></i>Esta ação não pode ser desfeita.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeConfirmModal()">Cancelar</button>
                <button class="btn-danger-modal" id="confirm-delete-btn">
                    <i class="fas fa-trash mr-1"></i>Sim, Excluir
                </button>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Cancelamento -->
    <div id="confirm-cancel-modal" class="modal-overlay">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle text-amber-500 mr-2"></i>Confirmar Cancelamento</h3>
                <button class="modal-close" onclick="closeCancelModal()">&times;</button>
            </div>
            <div class="modal-body">
                <div class="confirm-icon amber">
                    <i class="fas fa-ban"></i>
                </div>
                <div id="confirm-cancel-message">
                    <p class="text-gray-600">Tem certeza que deseja cancelar este agendamento?</p>
                    <p class="text-sm text-amber-500 mt-2"><i class="fas fa-info-circle mr-1"></i>O agendamento será marcado como cancelado.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeCancelModal()">Manter Agendamento</button>
                <button class="btn-amber" id="confirm-cancel-btn">
                    <i class="fas fa-ban mr-1"></i>Sim, Cancelar
                </button>
            </div>
        </div>
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
            setTimeout(() => {
                notification.classList.remove('show');
            }, 5000);
        }

        // ==================== MODAIS ====================
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        function openModal(modalId) {
            document.getElementById(modalId).classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        document.querySelectorAll('.modal-overlay').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.remove('show');
                    document.body.style.overflow = 'auto';
                }
            });
        });

        // ==================== VARIÁVEIS ====================
        let allAppointments = [];
        let filteredAppointments = [];
        let currentPage = 1;
        const appointmentsPerPage = 10;
        let isSearching = false;
        let currentFilter = 'all';
        let currentViewId = null;
        let deleteId = null;
        let cancelId = null;

        // ==================== STATUS BADGE ====================
        function getStatusBadge(status) {
            if (!status) return '<span class="text-gray-500">N/A</span>';
            const statusMap = {
                'pendente': {
                    label: 'Pendente',
                    class: 'pendente',
                    icon: 'fa-clock'
                },
                'confirmado': {
                    label: 'Confirmado',
                    class: 'confirmado',
                    icon: 'fa-check-circle'
                },
                'cancelado': {
                    label: 'Cancelado',
                    class: 'cancelado',
                    icon: 'fa-times-circle'
                },
                'concluido': {
                    label: 'Concluído',
                    class: 'concluido',
                    icon: 'fa-check-double'
                }
            };
            const key = status.toLowerCase();
            const data = statusMap[key] || {
                label: status,
                class: 'pendente',
                icon: 'fa-circle'
            };
            return `<span class="status-badge ${data.class}"><i class="fas ${data.icon}" style="font-size:0.5rem;"></i>${data.label}</span>`;
        }

        // ==================== VIEW APPOINTMENT ====================
        function viewAppointment(id) {
            currentViewId = id;
            const modalBody = document.getElementById('view-modal-body');
            modalBody.innerHTML = `
                <div class="text-center py-4">
                    <span class="loading-spinner"></span> Carregando dados...
                </div>
            `;

            const csrfToken = getCsrfToken();
            const csrfName = getCsrfName();

            const formData = new FormData();
            formData.append('id', id);
            formData.append(csrfName, csrfToken);

            fetch('<?= site_url('admin/get_appointment_details') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 403) {
                            throw new Error('Token de segurança expirado. Recarregue a página.');
                        }
                        throw new Error('Erro na requisição: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        modalBody.innerHTML = `
                            <div class="text-center py-4 text-red-500">
                                <i class="fas fa-exclamation-circle text-2xl block mb-2"></i>
                                ${data.error}
                            </div>
                        `;
                        return;
                    }

                    const appt = data;
                    const isForOther = appt.tipo_agendamento === 'other';
                    const tipoLabel = isForOther ? 'Para outra pessoa' : 'Para si';
                    const tipoClass = isForOther ? 'outro' : 'proprio';
                    const tipoIcon = isForOther ? 'fa-users' : 'fa-user';

                    let html = `
                        <div class="detail-row">
                            <span class="detail-label"><i class="fas fa-user mr-1"></i>Paciente</span>
                            <span class="detail-value font-medium">${appt.paciente || 'N/A'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label"><i class="fas fa-user-tag mr-1"></i>Tipo</span>
                            <span class="detail-value">
                                <span class="tipo-badge ${tipoClass}">
                                    <i class="fas ${tipoIcon} mr-1"></i>${tipoLabel}
                                </span>
                            </span>
                        </div>
                    `;

                    // Se for para outra pessoa, mostrar dados do paciente agendado
                    if (isForOther) {
                        html += `
                            <div class="detail-row" style="background-color: #fef3c7; border-radius: 0.25rem; padding: 0.5rem 0.75rem; margin: 0.5rem 0; border-left: 4px solid #f59e0b;">
                                <span class="detail-label" style="color: #92400e;"><i class="fas fa-child mr-1"></i>Paciente Agendado</span>
                                <span class="detail-value">
                                    <strong>${appt.paciente_nome_agendado || 'Não informado'}</strong>
                                    ${appt.paciente_relacao ? `<span class="sub-detail"><i class="fas fa-heart mr-1"></i>Parentesco: ${appt.paciente_relacao}</span>` : ''}
                                    ${appt.paciente_data_nasc_agendado ? `<span class="sub-detail"><i class="fas fa-birthday-cake mr-1"></i>Data Nasc.: ${appt.paciente_data_nasc_agendado}</span>` : ''}
                                    ${appt.paciente_doc_tipo ? `<span class="sub-detail"><i class="fas fa-id-card mr-1"></i>Documento: ${appt.paciente_doc_tipo} ${appt.paciente_doc_num || ''}</span>` : ''}
                                </span>
                            </div>
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-user mr-1"></i>Responsável</span>
                                <span class="detail-value">${appt.responsavel_nome || appt.paciente || 'N/A'}</span>
                            </div>
                            ${appt.responsavel_telefone ? `
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-phone mr-1"></i>Telefone Responsável</span>
                                <span class="detail-value">${appt.responsavel_telefone}</span>
                            </div>` : ''}
                            ${appt.responsavel_bi ? `
                            <div class="detail-row">
                                <span class="detail-label"><i class="fas fa-id-card mr-1"></i>BI Responsável</span>
                                <span class="detail-value">${appt.responsavel_bi}</span>
                            </div>` : ''}
                        `;
                    }

                    html += `
                        <div class="detail-row">
                            <span class="detail-label"><i class="fas fa-user-md mr-1"></i>Médico</span>
                            <span class="detail-value">${appt.medico || 'N/A'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label"><i class="fas fa-stethoscope mr-1"></i>Especialidade</span>
                            <span class="detail-value">${appt.especialidade || 'N/A'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label"><i class="fas fa-calendar-alt mr-1"></i>Data</span>
                            <span class="detail-value">${appt.data_formatada || appt.data}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label"><i class="fas fa-clock mr-1"></i>Horário</span>
                            <span class="detail-value">${appt.hora || 'N/A'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label"><i class="fas fa-tag mr-1"></i>Status</span>
                            <span class="detail-value">${getStatusBadge(appt.status)}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label"><i class="fas fa-sticky-note mr-1"></i>Motivo</span>
                            <span class="detail-value">${appt.motivo || 'Não informado'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label"><i class="fas fa-calendar-plus mr-1"></i>Criado em</span>
                            <span class="detail-value">${appt.criado_em || 'N/A'}</span>
                        </div>
                        ${!isForOther ? `
                        <div class="detail-row">
                            <span class="detail-label"><i class="fas fa-phone mr-1"></i>Telefone</span>
                            <span class="detail-value">${appt.paciente_telefone || 'N/A'}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label"><i class="fas fa-id-card mr-1"></i>BI</span>
                            <span class="detail-value">${appt.paciente_bi || 'N/A'}</span>
                        </div>` : ''}
                    `;

                    modalBody.innerHTML = html;
                    openModal('view-modal');
                })
                .catch(error => {
                    console.error('Erro:', error);
                    modalBody.innerHTML = `
                        <div class="text-center py-4 text-red-500">
                            <i class="fas fa-exclamation-circle text-2xl block mb-2"></i>
                            ${error.message || 'Erro ao carregar dados do agendamento.'}
                        </div>
                    `;
                });
        }

        // ==================== EDIT FROM VIEW ====================
        function editFromView() {
            if (currentViewId) {
                closeModal('view-modal');
                setTimeout(() => editAppointment(currentViewId), 300);
            }
        }

        // ==================== EDIT APPOINTMENT ====================
        function editAppointment(id) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-paciente').value = '';
            document.getElementById('edit-medico').value = '';
            document.getElementById('edit-data').value = '';
            document.getElementById('edit-hora').value = '';
            document.getElementById('edit-status').value = 'Pendente';
            document.getElementById('edit-motivo').value = '';

            const csrfToken = getCsrfToken();
            const csrfName = getCsrfName();

            const formData = new FormData();
            formData.append('id', id);
            formData.append(csrfName, csrfToken);

            fetch('<?= site_url('admin/get_appointment_details') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 403) {
                            throw new Error('Token de segurança expirado. Recarregue a página.');
                        }
                        throw new Error('Erro na requisição: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        showNotification(data.error, 'error');
                        return;
                    }

                    const appt = data;
                    document.getElementById('edit-paciente').value = appt.paciente || '';
                    document.getElementById('edit-medico').value = appt.medico || '';
                    document.getElementById('edit-data').value = appt.data || '';
                    document.getElementById('edit-hora').value = appt.hora || '';
                    document.getElementById('edit-status').value = appt.status || 'Pendente';
                    document.getElementById('edit-motivo').value = appt.motivo || '';

                    openModal('edit-modal');
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showNotification(error.message || 'Erro ao carregar dados para edição.', 'error');
                });
        }

        // ==================== SAVE EDIT ====================
        function saveEdit(event) {
            event.preventDefault();

            const id = document.getElementById('edit-id').value;
            const data = document.getElementById('edit-data').value;
            const hora = document.getElementById('edit-hora').value;
            const status = document.getElementById('edit-status').value;
            const motivo = document.getElementById('edit-motivo').value.trim();

            if (!data || !hora) {
                showNotification('Data e horário são obrigatórios.', 'error');
                return;
            }

            const csrfToken = getCsrfToken();
            const csrfName = getCsrfName();

            const formData = new FormData();
            formData.append('id', id);
            formData.append('data', data);
            formData.append('hora', hora);
            formData.append('status', status);
            formData.append('motivo', motivo);
            formData.append(csrfName, csrfToken);

            const btn = document.getElementById('save-edit-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Salvando...';

            fetch('<?= site_url('admin/update_appointment') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-save mr-1"></i>Salvar Alterações';

                    if (data.error) {
                        showNotification(data.error, 'error');
                        return;
                    }

                    showNotification(data.success || 'Agendamento atualizado com sucesso!', 'success');
                    closeModal('edit-modal');
                    loadAppointments(document.getElementById('search-input')?.value || '');
                })
                .catch(error => {
                    console.error('Erro:', error);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-save mr-1"></i>Salvar Alterações';
                    showNotification('Erro ao atualizar agendamento: ' + error.message, 'error');
                });
        }

        // ==================== DELETE APPOINTMENT ====================
        function confirmDelete(id) {
            if (!id) {
                showNotification('ID do agendamento não encontrado.', 'error');
                return;
            }
            deleteId = id;

            const modal = document.getElementById('confirm-delete-modal');
            const message = document.getElementById('confirm-delete-message');
            message.innerHTML = `
                <p class="text-gray-600">Tem certeza que deseja excluir este agendamento?</p>
                <p class="text-sm text-red-500 mt-2"><i class="fas fa-info-circle mr-1"></i>Esta ação não pode ser desfeita.</p>
            `;
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeConfirmModal() {
            document.getElementById('confirm-delete-modal').classList.remove('show');
            document.body.style.overflow = 'auto';
            deleteId = null;
        }

        document.getElementById('confirm-delete-btn').addEventListener('click', function() {
            if (!deleteId) return;

            const csrfToken = getCsrfToken();
            const csrfName = getCsrfName();

            const formData = new FormData();
            formData.append('id', deleteId);
            formData.append(csrfName, csrfToken);

            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Excluindo...';

            fetch('<?= site_url('admin/delete_appointment') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 403) {
                            throw new Error('Token de segurança expirado. Recarregue a página.');
                        }
                        throw new Error('Erro na requisição: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-trash mr-1"></i>Sim, Excluir';

                    if (data.error) {
                        showNotification(data.error, 'error');
                        return;
                    }
                    showNotification(data.success || 'Agendamento excluído com sucesso!', 'success');
                    closeConfirmModal();
                    loadAppointments(document.getElementById('search-input')?.value || '');
                })
                .catch(error => {
                    console.error('Erro ao excluir:', error);
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-trash mr-1"></i>Sim, Excluir';
                    showNotification(error.message || 'Erro ao excluir agendamento.', 'error');
                    closeConfirmModal();
                });
        });

        // ==================== CANCEL APPOINTMENT ====================
        function confirmCancel(id) {
            if (!id) {
                showNotification('ID do agendamento não encontrado.', 'error');
                return;
            }
            cancelId = id;

            const modal = document.getElementById('confirm-cancel-modal');
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeCancelModal() {
            document.getElementById('confirm-cancel-modal').classList.remove('show');
            document.body.style.overflow = 'auto';
            cancelId = null;
        }

        document.getElementById('confirm-cancel-btn').addEventListener('click', function() {
            if (!cancelId) return;

            const csrfToken = getCsrfToken();
            const csrfName = getCsrfName();

            const formData = new FormData();
            formData.append('id', cancelId);
            formData.append(csrfName, csrfToken);

            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Cancelando...';

            fetch('<?= site_url('admin/cancel_appointment') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 403) {
                            throw new Error('Token de segurança expirado. Recarregue a página.');
                        }
                        throw new Error('Erro na requisição: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-ban mr-1"></i>Sim, Cancelar';

                    if (data.error) {
                        showNotification(data.error, 'error');
                        return;
                    }
                    showNotification(data.success || 'Agendamento cancelado com sucesso!', 'success');
                    closeCancelModal();
                    loadAppointments(document.getElementById('search-input')?.value || '');
                })
                .catch(error => {
                    console.error('Erro ao cancelar:', error);
                    this.disabled = false;
                    this.innerHTML = '<i class="fas fa-ban mr-1"></i>Sim, Cancelar';
                    showNotification(error.message || 'Erro ao cancelar agendamento.', 'error');
                    closeCancelModal();
                });
        });

        // Fechar modal de cancelamento ao clicar fora
        document.getElementById('confirm-cancel-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCancelModal();
            }
        });

        // ==================== PAGINATION ====================
        function renderPagination(totalPages) {
            const container = document.getElementById('pagination-container');
            if (!container) return;

            container.innerHTML = '';

            const prevBtn = document.createElement('button');
            prevBtn.textContent = 'Anterior';
            prevBtn.disabled = currentPage === 1;
            prevBtn.addEventListener('click', () => {
                if (currentPage > 1) {
                    currentPage--;
                    renderCurrentList();
                }
            });
            container.appendChild(prevBtn);

            for (let i = 1; i <= totalPages; i++) {
                const btn = document.createElement('button');
                btn.textContent = i;
                btn.classList.toggle('active', i === currentPage);
                btn.addEventListener('click', () => {
                    currentPage = i;
                    renderCurrentList();
                });
                container.appendChild(btn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.textContent = 'Próxima';
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.addEventListener('click', () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    renderCurrentList();
                }
            });
            container.appendChild(nextBtn);
        }

        // ==================== UPDATE COUNTS ====================
        function updateCounts(appointments) {
            const counts = {
                all: appointments.length,
                pendente: 0,
                confirmado: 0,
                cancelado: 0,
                concluido: 0
            };

            appointments.forEach(appt => {
                const status = appt.status ? appt.status.toLowerCase() : '';
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

        // ==================== RENDER TABLE ====================
        function renderCurrentList() {
            const appointmentsList = isSearching ? filteredAppointments : allAppointments;
            let filtered = appointmentsList;

            if (currentFilter !== 'all') {
                filtered = appointmentsList.filter(appt =>
                    appt.status && appt.status.toLowerCase() === currentFilter
                );
            }

            renderTable(filtered);
            updateCounts(appointmentsList);
        }

        function renderTable(appointmentsList) {
            const tableBody = document.getElementById('appointments-table');
            const noResults = document.getElementById('no-results');
            const paginationContainer = document.getElementById('pagination-container');
            if (!tableBody || !noResults || !paginationContainer) return;

            const startIndex = (currentPage - 1) * appointmentsPerPage;
            const endIndex = startIndex + appointmentsPerPage;
            const paginatedAppointments = appointmentsList.slice(startIndex, endIndex);

            tableBody.innerHTML = '';
            if (appointmentsList.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="empty-state">
                            <i class="fas fa-calendar-check"></i>
                            <p class="text-lg font-medium mb-2">Nenhum agendamento encontrado</p>
                            <p class="text-gray-500 mb-4">Tente uma busca diferente ou cadastre um novo agendamento.</p>
                            <a href="<?= site_url('admin/cad_agendamento') ?>" class="btn-primary inline-flex">
                                <i class="fas fa-calendar-plus mr-2"></i> Novo Agendamento
                            </a>
                        </td>
                    </tr>
                `;
                noResults.classList.add('hidden');
                paginationContainer.innerHTML = '';
                return;
            }

            noResults.classList.add('hidden');
            paginatedAppointments.forEach(appointment => {
                const row = document.createElement('tr');
                row.className = 'border-t';

                // Determinar o tipo de agendamento
                const isForOther = appointment.tipo_agendamento === 'other';
                const tipoLabel = isForOther ? 'Outra pessoa' : 'Próprio';
                const tipoClass = isForOther ? 'outro' : 'proprio';
                const tipoIcon = isForOther ? 'fa-users' : 'fa-user';

                // Nome do paciente com detalhes se for outra pessoa
                let pacienteDisplay = appointment.paciente || 'N/A';
                if (isForOther && appointment.paciente_nome_agendado) {
                    pacienteDisplay = `${appointment.paciente_nome_agendado}`;
                    if (appointment.paciente_relacao) {
                        pacienteDisplay += ` <span class="text-xs text-gray-500">(${appointment.paciente_relacao})</span>`;
                    }
                }

                row.innerHTML = `
                    <td>
                        <div>
                            <div class="font-medium">${pacienteDisplay}</div>
                            ${isForOther ? `<div class="text-xs text-gray-500"><i class="fas fa-user mr-1"></i>Responsável: ${appointment.paciente || 'N/A'}</div>` : ''}
                        </div>
                    </td>
                    <td>
                        <span class="tipo-badge ${tipoClass}">
                            <i class="fas ${tipoIcon} mr-1"></i>
                            ${tipoLabel}
                        </span>
                    </td>
                    <td>${appointment.medico || 'N/A'}</td>
                    <td>${appointment.especialidade || 'N/A'}</td>
                    <td>${appointment.data || '-'}</td>
                    <td>${appointment.hora || '-'}</td>
                    <td>${getStatusBadge(appointment.status)}</td>
                    <td class="text-center">
                        <div class="flex justify-center gap-1 flex-wrap">
                            <button class="btn-sm btn-view view-btn" data-id="${appointment.id}" title="Ver Detalhes">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn-sm btn-edit edit-btn" data-id="${appointment.id}" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            ${appointment.status && appointment.status.toLowerCase() !== 'cancelado' ? 
                                `<button class="btn-sm btn-cancel cancel-btn" data-id="${appointment.id}" title="Cancelar">
                                <i class="fas fa-ban"></i>
                                </button>` : ''
                            }
                            <button class="btn-sm btn-danger delete-btn" data-id="${appointment.id}" title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            const totalPages = Math.ceil(appointmentsList.length / appointmentsPerPage);
            renderPagination(totalPages);

            // Attach events
            document.querySelectorAll('.view-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    viewAppointment(this.dataset.id);
                });
            });

            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    editAppointment(this.dataset.id);
                });
            });

            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    confirmDelete(this.dataset.id);
                });
            });

            document.querySelectorAll('.cancel-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    confirmCancel(this.dataset.id);
                });
            });
        }

        // ==================== LOAD APPOINTMENTS ====================
        async function loadAppointments(query = '') {
            const tableBody = document.getElementById('appointments-table');
            if (tableBody) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="8" class="text-center py-8">
                            <span class="loading-spinner"></span> Carregando...
                        </td>
                    </tr>
                `;
            }

            try {
                const url = AJAX_URL + '/admin/get_appointments' + (query ? `?query=${encodeURIComponent(query)}` : '');
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();

                if (data.error) {
                    showNotification(data.error, 'error');
                    renderTable([]);
                    return;
                }

                if (query === '') {
                    allAppointments = data;
                    isSearching = false;
                } else {
                    filteredAppointments = data;
                    isSearching = true;
                }
                currentPage = 1;
                renderCurrentList();
            } catch (error) {
                console.error('Erro ao carregar agendamentos:', error);
                showNotification('Erro ao carregar agendamentos.', 'error');
                renderTable([]);
            }
        }

        // ==================== HANDLE SEARCH ====================
        function handleSearch(query) {
            if (query === '') {
                isSearching = false;
                currentPage = 1;
                renderCurrentList();
            } else {
                loadAppointments(query);
            }
        }

        // ==================== FILTERS ====================
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentFilter = this.dataset.filter;
                currentPage = 1;
                renderCurrentList();
            });
        });

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

                if (!isClickInsideSidebar && !isClickOnMenuBtn && isSidebarOpen) {
                    sidebarMenu.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                    pageWrapper.classList.remove('expanded');
                }
            });

            // Search
            const searchInput = document.getElementById('search-input');
            if (searchInput) {
                let timeoutId;
                searchInput.addEventListener('input', function() {
                    clearTimeout(timeoutId);
                    timeoutId = setTimeout(() => {
                        const query = this.value.trim();
                        if (query === '') {
                            isSearching = false;
                            currentPage = 1;
                            renderCurrentList();
                        } else {
                            handleSearch(query);
                        }
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

            // Carregar agendamentos iniciais
            loadAppointments('');
        });
    </script>
</body>

</html>