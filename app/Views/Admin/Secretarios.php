<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secretários - Administrador - Hospital Matlhovele</title>
    <meta name="description" content="Gerenciar secretários no Hospital Público de Matlhovele">
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

        /* Card Panel */
        .card-panel {
            background-color: white;
            border-radius: 0.75rem;
            border: 1px solid #eef1f6;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            padding: 1.5rem;
        }

        /* Search Box */
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

        .btn-sm {
            padding: 0.3rem 0.8rem; font-size: 0.75rem; border-radius: 0.4rem;
            border: none; cursor: pointer; transition: all 0.2s;
            display: inline-flex; align-items: center; gap: 0.3rem; font-weight: 500;
        }
        .btn-sm:hover { transform: scale(1.05); }
        .btn-view { background-color: #8b5cf6; color: white; }
        .btn-view:hover { background-color: #7c3aed; }
        .btn-edit { background-color: var(--brand-500); color: white; }
        .btn-edit:hover { background-color: var(--brand-600); }
        .btn-danger { background-color: #ef4444; color: white; }
        .btn-danger:hover { background-color: #dc2626; }

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
        .btn-primary:hover { background-color: var(--brand-600); transform: translateY(-1px); }

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
        .btn-secondary:hover { background-color: #d1d5db; }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6b7280;
        }
        .empty-state i { font-size: 3rem; color: #d1d5db; margin-bottom: 1rem; }

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
            box-shadow: 0 24px 60px rgba(0,0,0,0.35);
            animation: slideUp 0.3s ease-out;
            max-height: 90vh;
            overflow-y: auto;
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

        .input-success {
            border-color: #10b981 !important;
            background-color: #f0fdf4;
        }
        .input-success:focus {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
        }
        .input-error {
            border-color: #ef4444 !important;
            background-color: #fef2f2;
        }
        .input-error:focus {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
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

        @media (max-width: 640px) {
            .main-content { padding: 0.5rem; }
            table { font-size: 0.75rem; }
            thead th, tbody td { padding: 0.55rem; }
            .pulse-line { display: none; }
            .btn-sm { font-size: 0.65rem; padding: 0.2rem 0.5rem; }
            .modal-content { padding: 1rem; }
            .pagination button { padding: 0.3rem 0.6rem; font-size: 0.75rem; }
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
                <a href="<?= site_url('admin/secretarios') ?>" class="active"><i class="fas fa-user-tie"></i><span class="sidebar-text">Secretários</span></a>
                <a href="<?= site_url('admin/agendamentos') ?>"><i class="fas fa-calendar-check"></i><span class="sidebar-text">Agendamentos</span></a>
                <a href="<?= site_url('admin/disponibilidade') ?>"><i class="fas fa-calendar-alt"></i><span class="sidebar-text">Disponibilidade</span></a>
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
                    <i class="fas fa-hospital-alt text-2xl" aria-label="Ícone do Hospital Matlhovele"></i>
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

        <!-- Main Content -->
        <main class="main-content">
            <div class="container mx-auto px-4 py-8">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">Lista de Secretários</h2>
                        <p class="text-gray-500 text-sm">Gerencie todos os secretários cadastrados</p>
                    </div>
                    <a href="<?= site_url('admin/cad_secretario') ?>" class="btn-primary">
                        <i class="fas fa-user-plus mr-2"></i> Cadastrar Secretário
                    </a>
                </div>

                <!-- Search Bar -->
                <div class="mb-6">
                    <div class="search-box max-w-lg">
                        <i class="fas fa-search"></i>
                        <input type="text" id="search-input" placeholder="Pesquisar por nome ou email..." aria-label="Pesquisar">
                    </div>
                </div>

                <!-- Secretaries Table -->
                <div class="card-panel">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Telefone</th>
                                    <th>Email</th>
                                    <th>Cargo</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="secretaries-table">
                                <tr>
                                    <td colspan="5" class="text-center py-8">
                                        <span class="loading-spinner"></span> Carregando...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="no-results" class="hidden text-center py-8 text-gray-500">
                        <i class="fas fa-search text-2xl block mb-2 text-gray-300"></i>
                        Nenhum secretário encontrado para a pesquisa.
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
                <h3><i class="fas fa-user-circle text-blue-500 mr-2"></i>Detalhes do Secretário</h3>
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
                <h3><i class="fas fa-user-edit text-green-500 mr-2"></i>Editar Secretário</h3>
                <button class="modal-close" onclick="closeModal('edit-modal')">&times;</button>
            </div>
            <form id="edit-form" onsubmit="saveEdit(event)">
                <input type="hidden" id="edit-id" name="id">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit-nome">Nome Completo <span class="text-red-500">*</span></label>
                        <input type="text" id="edit-nome" name="nome" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-telefone">Telefone <span class="text-red-500">*</span></label>
                        <input type="tel" id="edit-telefone" name="telefone" required 
                               placeholder="+258 84 1234567" 
                               class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <p class="text-xs text-gray-500 mt-1">
                            <i class="fas fa-info-circle mr-1"></i>
                            Formatos aceitos: +258 84 1234567, +258841234567, 841234567
                        </p>
                    </div>
                    <div class="form-group">
                        <label for="edit-email">Email <span class="text-red-500">*</span></label>
                        <input type="email" id="edit-email" name="email" required placeholder="exemplo@email.com">
                    </div>
                    <div class="form-group">
                        <label for="edit-cargo">Cargo <span class="text-red-500">*</span></label>
                        <input type="text" id="edit-cargo" name="cargo" required placeholder="Ex: Secretário, Recepcionista, etc.">
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
                    <p class="text-gray-600">Tem certeza que deseja excluir este secretário?</p>
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

    <script>
        // ==================== VALIDAÇÃO DE TELEFONE MOÇAMBICANO ====================
        function validateMozambicanPhone(phone) {
            const cleanPhone = phone.replace(/[\s\(\)\-\.]/g, '');
            
            const patterns = [
                /^\+258[8][0-9]{8}$/,      
                /^258[8][0-9]{8}$/,        
                /^[8][0-9]{8}$/            
            ];
            
            for (let pattern of patterns) {
                if (pattern.test(cleanPhone)) {
                    return true;
                }
            }
            return false;
        }

        function formatMozambicanPhone(phone) {
            if (!phone) return '-';
            const clean = phone.replace(/[\s\(\)\-\.]/g, '');
            
            let digits = clean;
            if (clean.startsWith('+258')) {
                digits = clean.substring(4);
            } else if (clean.startsWith('258')) {
                digits = clean.substring(3);
            }
            
            if (digits.length === 9 && digits.startsWith('8')) {
                return '+258 ' + digits.substring(0, 2) + ' ' + digits.substring(2, 5) + ' ' + digits.substring(5, 9);
            }
            
            if (digits.length < 9) {
                return phone;
            }
            
            if (clean.startsWith('+258')) {
                return '+' + clean.substring(0, 4) + ' ' + clean.substring(4, 6) + ' ' + clean.substring(6, 9) + ' ' + clean.substring(9, 13);
            }
            
            return phone;
        }

        function cleanPhoneForDatabase(phone) {
            let clean = phone.replace(/[\s\(\)\-\.]/g, '');
            
            if (!clean.startsWith('+258') && !clean.startsWith('258')) {
                if (clean.length === 9) {
                    clean = '+258' + clean;
                }
            }
            
            if (clean.startsWith('258') && clean.length === 12) {
                clean = '+' + clean;
            }
            
            return clean;
        }

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
        let allSecretaries = [];
        let filteredSecretaries = [];
        let currentPage = 1;
        const secretariesPerPage = 10;
        let isSearching = false;
        let currentViewId = null;
        let deleteId = null;
        let deleteName = null;

        // ==================== VIEW SECRETARY ====================
        function viewSecretary(id) {
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

            fetch('<?= site_url('admin/get_secretary_details') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
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

                const secretary = data;
                modalBody.innerHTML = `
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-user mr-1"></i>Nome</span>
                        <span class="detail-value font-medium">${secretary.name || 'N/A'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-phone mr-1"></i>Telefone</span>
                        <span class="detail-value">${formatMozambicanPhone(secretary.phone)}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-envelope mr-1"></i>Email</span>
                        <span class="detail-value">${secretary.email || 'N/A'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-briefcase mr-1"></i>Cargo</span>
                        <span class="detail-value font-medium text-teal-600">${secretary.cargo || 'N/A'}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label"><i class="fas fa-calendar-plus mr-1"></i>Cadastrado em</span>
                        <span class="detail-value">${secretary.created_at || 'N/A'}</span>
                    </div>
                `;
                openModal('view-modal');
            })
            .catch(error => {
                console.error('Erro:', error);
                modalBody.innerHTML = `
                    <div class="text-center py-4 text-red-500">
                        <i class="fas fa-exclamation-circle text-2xl block mb-2"></i>
                        ${error.message || 'Erro ao carregar dados do secretário.'}
                    </div>
                `;
                if (error.message.includes('403') || error.message.includes('expirado')) {
                    showNotification('Sessão expirada. Recarregue a página.', 'error');
                }
            });
        }

        // ==================== EDIT FROM VIEW ====================
        function editFromView() {
            if (currentViewId) {
                closeModal('view-modal');
                setTimeout(() => editSecretary(currentViewId), 300);
            }
        }

        // ==================== EDIT SECRETARY ====================
        function editSecretary(id) {
            document.getElementById('edit-id').value = id;
            document.getElementById('edit-nome').value = '';
            document.getElementById('edit-telefone').value = '';
            document.getElementById('edit-email').value = '';
            document.getElementById('edit-cargo').value = '';

            document.getElementById('edit-telefone').classList.remove('input-success', 'input-error');

            const csrfToken = getCsrfToken();
            const csrfName = getCsrfName();
            
            const formData = new FormData();
            formData.append('id', id);
            formData.append(csrfName, csrfToken);

            fetch('<?= site_url('admin/get_secretary_details') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
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

                const secretary = data;
                document.getElementById('edit-nome').value = secretary.name || '';
                document.getElementById('edit-telefone').value = secretary.phone || '';
                document.getElementById('edit-email').value = secretary.email || '';
                document.getElementById('edit-cargo').value = secretary.cargo || '';
                
                if (secretary.phone && validateMozambicanPhone(secretary.phone)) {
                    document.getElementById('edit-telefone').classList.add('input-success');
                }
                
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
            const nome = document.getElementById('edit-nome').value.trim();
            const telefone = document.getElementById('edit-telefone').value.trim();
            const email = document.getElementById('edit-email').value.trim();
            const cargo = document.getElementById('edit-cargo').value.trim();

            if (!nome || !telefone || !email || !cargo) {
                showNotification('Todos os campos são obrigatórios.', 'error');
                if (!nome) document.getElementById('edit-nome').classList.add('input-error');
                if (!telefone) document.getElementById('edit-telefone').classList.add('input-error');
                if (!email) document.getElementById('edit-email').classList.add('input-error');
                if (!cargo) document.getElementById('edit-cargo').classList.add('input-error');
                return;
            }

            if (!validateMozambicanPhone(telefone)) {
                showNotification(
                    'Número de telefone inválido.<br>' +
                    'Formatos aceitos:<br>' +
                    '• +258 84 1234567<br>' +
                    '• +258841234567<br>' +
                    '• 841234567',
                    'error'
                );
                document.getElementById('edit-telefone').classList.add('input-error');
                return;
            }

            document.getElementById('edit-nome').classList.remove('input-error');
            document.getElementById('edit-telefone').classList.remove('input-error');
            document.getElementById('edit-email').classList.remove('input-error');
            document.getElementById('edit-cargo').classList.remove('input-error');

            const telefoneLimpo = cleanPhoneForDatabase(telefone);

            const csrfToken = getCsrfToken();
            const csrfName = getCsrfName();
            
            const formData = new FormData();
            formData.append('id', id);
            formData.append('nome', nome);
            formData.append('telefone', telefoneLimpo);
            formData.append('email', email);
            formData.append('cargo', cargo);
            formData.append(csrfName, csrfToken);

            const btn = document.getElementById('save-edit-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Salvando...';

            fetch('<?= site_url('admin/update_secretary') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
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

                showNotification(data.success || 'Secretário atualizado com sucesso!', 'success');
                closeModal('edit-modal');
                loadSecretaries(document.getElementById('search-input')?.value || '');
            })
            .catch(error => {
                console.error('Erro:', error);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save mr-1"></i>Salvar Alterações';
                showNotification('Erro ao atualizar secretário: ' + error.message, 'error');
            });
        }

        // ==================== CONFIRM DELETE ====================
        function confirmDelete(id, name) {
            if (!id) {
                showNotification('ID do secretário não encontrado.', 'error');
                return;
            }
            deleteId = id;
            deleteName = name;
            
            const modal = document.getElementById('confirm-delete-modal');
            const message = document.getElementById('confirm-delete-message');
            message.innerHTML = `
                <p class="text-gray-600">Tem certeza que deseja excluir o secretário <strong>"${name}"</strong>?</p>
                <p class="text-sm text-red-500 mt-2"><i class="fas fa-info-circle mr-1"></i>Esta ação não pode ser desfeita.</p>
            `;
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeConfirmModal() {
            document.getElementById('confirm-delete-modal').classList.remove('show');
            document.body.style.overflow = 'auto';
            deleteId = null;
            deleteName = null;
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

            fetch('<?= site_url('admin/delete_secretary') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
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
                showNotification(data.success || 'Secretário excluído com sucesso!', 'success');
                closeConfirmModal();
                loadSecretaries(document.getElementById('search-input')?.value || '');
            })
            .catch(error => {
                console.error('Erro ao excluir:', error);
                this.disabled = false;
                this.innerHTML = '<i class="fas fa-trash mr-1"></i>Sim, Excluir';
                showNotification(error.message || 'Erro ao excluir secretário.', 'error');
                closeConfirmModal();
            });
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

        // ==================== RENDER TABLE ====================
        function renderCurrentList() {
            const secretariesList = isSearching ? filteredSecretaries : allSecretaries;
            renderTable(secretariesList);
        }

        function renderTable(secretariesList) {
            const tableBody = document.getElementById('secretaries-table');
            const noResults = document.getElementById('no-results');
            const paginationContainer = document.getElementById('pagination-container');
            if (!tableBody || !noResults || !paginationContainer) return;

            const startIndex = (currentPage - 1) * secretariesPerPage;
            const endIndex = startIndex + secretariesPerPage;
            const paginatedSecretaries = secretariesList.slice(startIndex, endIndex);

            tableBody.innerHTML = '';
            if (secretariesList.length === 0) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="empty-state">
                            <i class="fas fa-user-tie"></i>
                            <p class="text-lg font-medium mb-2">Nenhum secretário encontrado</p>
                            <p class="text-gray-500 mb-4">Tente uma busca diferente ou cadastre um novo secretário.</p>
                            <a href="<?= site_url('admin/cad_secretario') ?>" class="btn-primary inline-flex">
                                <i class="fas fa-user-plus mr-2"></i> Cadastrar Secretário
                            </a>
                        </td>
                    </tr>
                `;
                noResults.classList.add('hidden');
                paginationContainer.innerHTML = '';
                return;
            }

            noResults.classList.add('hidden');
            paginatedSecretaries.forEach(secretary => {
                const row = document.createElement('tr');
                row.className = 'border-t';
                row.innerHTML = `
                    <td class="font-medium">${secretary.name || 'N/A'}</td>
                    <td>${formatMozambicanPhone(secretary.phone)}</td>
                    <td>${secretary.email || '-'}</td>
                    <td><span class="text-teal-600 font-medium">${secretary.cargo || '-'}</span></td>
                    <td class="text-center">
                        <div class="flex justify-center gap-1">
                            <button class="btn-sm btn-view view-btn" data-id="${secretary.id}" title="Ver Detalhes">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn-sm btn-edit edit-btn" data-id="${secretary.id}" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-sm btn-danger delete-btn" 
                                    data-id="${secretary.id}" 
                                    data-name="${secretary.name}"
                                    title="Excluir">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            const totalPages = Math.ceil(secretariesList.length / secretariesPerPage);
            renderPagination(totalPages);

            document.querySelectorAll('.view-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    viewSecretary(this.dataset.id);
                });
            });

            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    editSecretary(this.dataset.id);
                });
            });

            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    confirmDelete(id, name);
                });
            });
        }

        // ==================== LOAD SECRETARIES ====================
        async function loadSecretaries(query = '') {
            const tableBody = document.getElementById('secretaries-table');
            if (tableBody) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center py-8">
                            <span class="loading-spinner"></span> Carregando...
                        </td>
                    </tr>
                `;
            }

            try {
                const url = AJAX_URL + '/admin/get_secretaries' + (query ? `?query=${encodeURIComponent(query)}` : '');
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();

                if (data.error) {
                    showNotification(data.error, 'error');
                    renderTable([]);
                    return;
                }

                if (query === '') {
                    allSecretaries = data;
                    isSearching = false;
                } else {
                    filteredSecretaries = data;
                    isSearching = true;
                }
                currentPage = 1;
                renderCurrentList();
            } catch (error) {
                console.error('Erro ao carregar secretários:', error);
                showNotification('Erro ao carregar secretários.', 'error');
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
                loadSecretaries(query);
            }
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

                if (!isClickInsideSidebar && !isClickOnMenuBtn && isSidebarOpen) {
                    sidebarMenu.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                    pageWrapper.classList.remove('expanded');
                }
            });

            // ==================== VALIDAÇÃO EM TEMPO REAL DO TELEFONE ====================
            const editTelefone = document.getElementById('edit-telefone');
            if (editTelefone) {
                editTelefone.addEventListener('input', function() {
                    this.classList.remove('input-success', 'input-error');
                    
                    if (this.value.length > 0) {
                        if (validateMozambicanPhone(this.value)) {
                            this.classList.add('input-success');
                        } else {
                            this.classList.add('input-error');
                        }
                    }
                });
                
                editTelefone.addEventListener('blur', function() {
                    if (validateMozambicanPhone(this.value)) {
                        const formatted = formatMozambicanPhone(this.value);
                        if (formatted !== this.value) {
                            this.value = formatted;
                        }
                        this.classList.remove('input-error');
                        this.classList.add('input-success');
                    }
                });
            }

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

            // Carregar secretários iniciais
            loadSecretaries('');
        });
    </script>
</body>
</html>