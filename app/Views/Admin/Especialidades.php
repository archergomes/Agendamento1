<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Especialidades - Administrador - Centro de Saúde Da Matola II</title>
    <meta name="description" content="Gestão de especialidades médicas">
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
            position: fixed; top: 0; left: 0;
            height: 100vh; width: 80px;
            background: linear-gradient(180deg, #0f2f66 0%, #123a80 100%);
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease-in-out, width 0.3s ease-in-out;
            z-index: 900; display: flex; flex-direction: column;
        }
        .sidebar.show { transform: translateX(0); }
        .sidebar.desktop { transform: translateX(0); }
        .sidebar.desktop.expanded { width: 260px; }
        .sidebar.desktop .sidebar-text { display: none; }
        .sidebar.desktop.expanded .sidebar-text { display: inline; }
        .sidebar.desktop .sidebar-header { justify-content: center; padding: 1rem; }
        .sidebar.desktop.expanded .sidebar-header { justify-content: space-between; padding: 1rem 1.25rem; }
        .sidebar-header { border-bottom: 1px solid rgba(255, 255, 255, 0.12); }
        .sidebar-header h2 { color: white; }
        .sidebar-header button { color: rgba(255, 255, 255, 0.85); }
        .sidebar-header button:hover { color: white; }

        header {
            position: relative; z-index: 800;
            background: linear-gradient(90deg, #1d4ed8 0%, #2563eb 55%, #0d9488 130%);
            width: 100%; margin-left: 0;
        }

        .page-wrapper {
            margin-left: 80px;
            transition: margin-left 0.3s ease-in-out;
            width: calc(100% - 80px);
            min-height: 100vh;
            display: flex; flex-direction: column;
        }
        .page-wrapper.expanded { margin-left: 260px; width: calc(100% - 260px); }
        .main-content { flex: 1; width: 100%; padding: 1rem; min-height: calc(100vh - 80px); }

        .sidebar-overlay {
            display: none; position: fixed; top: 0; left: 0;
            width: 100%; height: 100%;
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
        .main-menu { overflow-y: auto; flex-grow: 1; }
        .sidebar-nav a, .sidebar-nav button {
            display: flex; align-items: center; gap: 10px;
            padding: 11px 16px; margin-bottom: 2px;
            border-radius: 0.5rem;
            color: rgba(255, 255, 255, 0.8);
            transition: background-color 0.2s, color 0.2s;
            font-size: 0.92rem;
            width: 100%; text-align: left;
            border: none; background: none; cursor: pointer;
        }
        .sidebar-nav a:hover, .sidebar-nav button:hover {
            background-color: rgba(255, 255, 255, 0.1); color: white;
        }
        .sidebar-nav a.active {
            background: rgba(255, 255, 255, 0.16); color: white;
            box-shadow: inset 3px 0 0 var(--teal-500);
        }
        .sidebar-nav i { font-size: 1.3rem; width: 26px; text-align: center; }
        .sidebar.desktop .sidebar-nav a, .sidebar.desktop .sidebar-nav button { justify-content: center; padding: 11px; }
        .sidebar.desktop.expanded .sidebar-nav a, .sidebar.desktop.expanded .sidebar-nav button { justify-content: flex-start; padding: 11px 16px; }
        .sidebar-nav .logout { margin-top: 0.5rem; border-top: 1px solid rgba(255, 255, 255, 0.12); padding-top: 0.5rem; }

        /* Cards / Table */
        .card-panel {
            background-color: white; border-radius: 0.75rem; border: 1px solid #eef1f6;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06); padding: 1.5rem;
        }
        .search-box { position: relative; }
        .search-box input {
            padding: 0.6rem 0.9rem 0.6rem 2.6rem;
            border: 1px solid #d8dee8; border-radius: 0.6rem;
            font-size: 0.875rem; width: 100%; background: white;
            transition: all 0.2s;
        }
        .search-box input:focus {
            outline: none; border-color: var(--brand-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }
        .search-box i {
            position: absolute; left: 0.9rem; top: 50%;
            transform: translateY(-50%); color: #9ca3af;
        }

        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        thead th {
            background-color: #f3f5f9; padding: 0.75rem 1rem;
            text-align: left; font-weight: 600; color: var(--ink-700);
            font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.03em;
        }
        tbody td { padding: 0.8rem 1rem; border-bottom: 1px solid #eef1f6; vertical-align: middle; }
        tbody tr:hover { background-color: #f9fafc; }

        .btn {
            padding: 0.6rem 1.1rem; border-radius: 0.55rem;
            font-weight: 500; border: none; cursor: pointer;
            transition: all 0.2s; font-size: 0.85rem;
            display: inline-flex; align-items: center; gap: 0.5rem;
        }
        .btn-primary { background-color: white; color: var(--brand-700); border: 1px solid #d8dee8; }
        .btn-primary:hover { background-color: #f1f5f9; transform: translateY(-1px); }
        .btn-success { background-color: var(--teal-500); color: white; }
        .btn-success:hover { background-color: var(--teal-600); transform: translateY(-1px); }
        .btn-secondary { background-color: #e5e7eb; color: #374151; }
        .btn-secondary:hover { background-color: #d1d5db; }
        .btn:disabled { opacity: .55; cursor: not-allowed; transform: none !important; }

        .btn-sm {
            padding: 0.3rem 0.75rem; font-size: 0.75rem;
            border-radius: 0.4rem; border: none; cursor: pointer;
            transition: all 0.2s;
            display: inline-flex; align-items: center; gap: 0.3rem;
        }
        .btn-sm:hover { transform: scale(1.05); }
        .btn-edit { background-color: var(--brand-500); color: white; }
        .btn-edit:hover { background-color: var(--brand-600); }
        .btn-delete { background-color: #ef4444; color: white; }
        .btn-delete:hover { background-color: #dc2626; }

        .empty-state { text-align: center; padding: 2.5rem 1rem; color: #6b7280; }
        .empty-state i { font-size: 2.2rem; color: #d1d5db; margin-bottom: 0.5rem; }

        .loading-spinner {
            display: inline-block; width: 1.3rem; height: 1.3rem;
            border: 3px solid #e5e7eb; border-top-color: var(--brand-500);
            border-radius: 50%; animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* Modal */
        .modal-overlay {
            display: none; position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(15, 23, 42, 0.55);
            z-index: 950; justify-content: center; align-items: center;
            padding: 1rem;
        }
        .modal-overlay.show { display: flex; animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        .modal-content {
            background-color: white; padding: 1.5rem; border-radius: 0.85rem;
            max-width: 500px; width: 100%;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.35);
            animation: slideUp 0.3s ease-out; max-height: 90vh; overflow-y: auto;
        }
        @keyframes slideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .modal-header {
            display: flex; justify-content: space-between; align-items: center;
            border-bottom: 2px solid #f3f4f6; padding-bottom: 1rem; margin-bottom: 1.5rem;
        }
        .modal-header h3 { font-size: 1.15rem; font-weight: 600; color: #1f2937; }
        .modal-close {
            background: none; border: none; font-size: 1.5rem; color: #6b7280;
            cursor: pointer; padding: 0.25rem 0.5rem; border-radius: 0.25rem;
        }
        .modal-close:hover { color: #1f2937; background-color: #f3f4f6; }

        .form-group { margin-bottom: 1rem; }
        .form-group label {
            display: block; font-weight: 500; color: #374151;
            margin-bottom: 0.25rem; font-size: 0.875rem;
        }
        .form-group input, .form-group textarea {
            width: 100%; padding: 0.55rem 0.75rem;
            border: 1px solid #d1d5db; border-radius: 0.5rem;
            transition: border-color 0.2s; font-size: 0.875rem;
            font-family: inherit;
        }
        .form-group input:focus, .form-group textarea:focus {
            outline: none; border-color: var(--brand-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .form-group label .required { color: #ef4444; }

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
                <a href="<?= site_url('admin/especialidades') ?>" class="active"><i class="fas fa-stethoscope"></i><span class="sidebar-text">Especialidades</span></a>
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
        <header class="text-white shadow-lg">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <i class="fas fa-hospital-alt text-2xl"></i>
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

        <main class="main-content">
            <div class="container mx-auto px-4 py-8">
                <!-- Header da página -->
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">Especialidades</h2>
                        <p class="text-gray-500 text-sm">Gerir as especialidades médicas disponíveis</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn btn-success" id="new-specialty-btn">
                            <i class="fas fa-plus"></i> Nova Especialidade
                        </button>
                        <button class="btn btn-primary" onclick="window.location.href='<?= site_url('admin') ?>'">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </button>
                    </div>
                </div>

                <!-- Cartão de resumo -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div class="card-panel flex items-center gap-4">
                        <div class="icon-wrap" style="width:52px;height:52px;border-radius:0.65rem;background:#dbeafe;color:#2563eb;display:flex;align-items:center;justify-content:center;font-size:1.35rem;">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                        <div>
                            <h3 style="font-size:0.75rem;color:#6b7280;font-weight:600;text-transform:uppercase;">Total</h3>
                            <p id="stat-total" style="font-size:1.6rem;font-weight:700;font-family:'Outfit',sans-serif;">0</p>
                        </div>
                    </div>
                    <div class="card-panel flex items-center gap-4">
                        <div class="icon-wrap" style="width:52px;height:52px;border-radius:0.65rem;background:#ccfbf1;color:#0d9488;display:flex;align-items:center;justify-content:center;font-size:1.35rem;">
                            <i class="fas fa-user-md"></i>
                        </div>
                        <div>
                            <h3 style="font-size:0.75rem;color:#6b7280;font-weight:600;text-transform:uppercase;">Médicos associados</h3>
                            <p id="stat-medicos" style="font-size:1.6rem;font-weight:700;font-family:'Outfit',sans-serif;">0</p>
                        </div>
                    </div>
                    <div class="card-panel flex items-center gap-4">
                        <div class="icon-wrap" style="width:52px;height:52px;border-radius:0.65rem;background:#fef3c7;color:#92400e;display:flex;align-items:center;justify-content:center;font-size:1.35rem;">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div>
                            <h3 style="font-size:0.75rem;color:#6b7280;font-weight:600;text-transform:uppercase;">Ativas</h3>
                            <p id="stat-ativas" style="font-size:1.6rem;font-weight:700;font-family:'Outfit',sans-serif;">0</p>
                        </div>
                    </div>
                </div>

                <!-- Tabela -->
                <div class="card-panel">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Lista de Especialidades</h3>
                        <div class="search-box" style="max-width:280px;width:100%;">
                            <i class="fas fa-search"></i>
                            <input type="text" id="search-input" placeholder="Pesquisar especialidade..." aria-label="Pesquisar">
                        </div>
                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width:50px;">#</th>
                                    <th>Nome</th>
                                    <th>Descrição</th>
                                    <th class="text-center">Médicos</th>
                                    <th>Criado em</th>
                                    <th class="text-center" style="width:140px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="specialty-list">
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-gray-500">
                                        <span class="loading-spinner"></span> Carregando especialidades...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Criar/Editar -->
    <div id="specialty-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="specialty-modal-title"><i class="fas fa-stethoscope text-blue-600 mr-2"></i>Nova Especialidade</h3>
                <button class="modal-close" onclick="closeModal('specialty-modal')">&times;</button>
            </div>
            <form id="specialty-form" onsubmit="saveSpecialty(event)">
                <input type="hidden" id="specialty-id">
                <div class="form-group">
                    <label for="specialty-name">Nome <span class="required">*</span></label>
                    <input type="text" id="specialty-name" required minlength="3" maxlength="100" placeholder="Ex: Cardiologia">
                </div>
                <div class="form-group">
                    <label for="specialty-description">Descrição (opcional)</label>
                    <textarea id="specialty-description" rows="3" maxlength="300" placeholder="Breve descrição da especialidade..."></textarea>
                </div>
                <div class="flex gap-3 mt-4">
                    <button type="submit" class="btn btn-success flex-1" id="specialty-submit-btn">
                        <i class="fas fa-check mr-1"></i> Guardar
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('specialty-modal')">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de confirmação de eliminação -->
    <div id="delete-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>Eliminar Especialidade</h3>
                <button class="modal-close" onclick="closeModal('delete-modal')">&times;</button>
            </div>
            <div style="padding:0.5rem 0;">
                <p class="text-gray-700">Tem a certeza que deseja eliminar a especialidade <strong id="delete-name"></strong>?</p>
                <p class="text-sm text-red-500 mt-2"><i class="fas fa-info-circle mr-1"></i>Esta ação não pode ser desfeita.</p>
            </div>
            <div class="flex gap-3 mt-6">
                <button class="btn" style="background-color:#ef4444;color:white;flex:1;" id="confirm-delete-btn">
                    <i class="fas fa-trash mr-1"></i> Sim, Eliminar
                </button>
                <button class="btn btn-secondary flex-1" onclick="closeModal('delete-modal')">Cancelar</button>
            </div>
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

        function updateCsrfToken(newToken) {
            if (!newToken) return;
            const meta = document.querySelector('meta[name="csrf-token"]');
            if (meta) meta.setAttribute('content', newToken);
            document.cookie = `csrf_cookie_name=${newToken}; path=/; SameSite=Lax`;
        }

        // ==================== NOTIFICAÇÕES ====================
        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            const messageEl = document.getElementById('notification-message');
            if (!notification || !messageEl) return;
            messageEl.innerHTML = message;
            notification.className = `show ${type}`;
            setTimeout(() => notification.classList.remove('show'), 5000);
        }

        // ==================== MODAIS ====================
        function openModal(id) {
            document.getElementById(id).classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('show');
            document.body.style.overflow = 'auto';
        }

        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if (e.target === this) closeModal(this.id);
            });
        });

        // ==================== ESCAPE ====================
        function escapeHtml(str) {
            if (str === null || str === undefined) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        // ==================== CARREGAR LISTA ====================
        let lastSpecialties = [];

        async function loadSpecialties() {
            const list = document.getElementById('specialty-list');

            list.innerHTML = `<tr>
                <td colspan="6" class="text-center py-4 text-gray-500">
                    <span class="loading-spinner"></span> Carregando especialidades...
                </td>
            </tr>`;

            try {
                const response = await fetch(AJAX_URL + '/admin/get_especialidades', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }

                const result = await response.json();
                lastSpecialties = result.data || [];

                // Estatísticas
                document.getElementById('stat-total').textContent = lastSpecialties.length;
                document.getElementById('stat-medicos').textContent = lastSpecialties.reduce(
                    (s, e) => s + Number(e.total_medicos || 0), 0
                );
                document.getElementById('stat-ativas').textContent = lastSpecialties.length;

                if (lastSpecialties.length === 0) {
                    list.innerHTML = `<tr>
                        <td colspan="6" class="text-center py-8 text-gray-500">
                            <i class="fas fa-stethoscope text-3xl block mb-2 text-gray-300"></i>
                            Nenhuma especialidade registada. Clique em <strong>Nova Especialidade</strong> para começar.
                        </td>
                    </tr>`;
                    return;
                }

                renderSpecialties(lastSpecialties);
            } catch (error) {
                console.error('Erro ao carregar especialidades:', error);
                list.innerHTML = `<tr>
                    <td colspan="6" class="text-center py-4 text-red-500">
                        <i class="fas fa-exclamation-circle text-2xl block mb-2"></i>
                        Erro ao carregar especialidades.
                    </td>
                </tr>`;
            }
        }

        function renderSpecialties(items) {
            const list = document.getElementById('specialty-list');
            list.innerHTML = items.map((esp, i) => {
                const totalMedicos = Number(esp.total_medicos || 0);
                const medicosBadge = totalMedicos > 0
                    ? `<span class="status-badge" style="background:#ccfbf1;color:#0f766e;"><i class="fas fa-user-md"></i> ${totalMedicos}</span>`
                    : `<span class="status-badge" style="background:#f3f4f6;color:#6b7280;">0</span>`;

                return `
                    <tr data-id="${esp.ID_Especialidade}">
                        <td class="text-gray-500">${i + 1}</td>
                        <td class="font-medium">${escapeHtml(esp.Nome)}</td>
                        <td class="text-gray-600 text-sm">${escapeHtml(esp.Descricao) || '<em class="text-gray-400">—</em>'}</td>
                        <td class="text-center">${medicosBadge}</td>
                        <td class="text-gray-500 text-sm">${esp.Criado_Em ? new Date(esp.Criado_Em).toLocaleDateString('pt-PT') : '—'}</td>
                        <td class="text-center">
                            <div class="flex justify-center gap-1">
                                <button class="btn-sm btn-edit" onclick="editSpecialty(${esp.ID_Especialidade})" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-sm btn-delete" onclick="confirmDeleteSpecialty(${esp.ID_Especialidade})" title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');
        }

        // ==================== CRIAR / EDITAR ====================
        function openNewSpecialty() {
            document.getElementById('specialty-form').reset();
            document.getElementById('specialty-id').value = '';
            document.getElementById('specialty-modal-title').innerHTML =
                '<i class="fas fa-stethoscope text-blue-600 mr-2"></i>Nova Especialidade';
            document.getElementById('specialty-submit-btn').innerHTML =
                '<i class="fas fa-check mr-1"></i> Guardar';
            openModal('specialty-modal');
        }

        function editSpecialty(id) {
            const esp = lastSpecialties.find(e => Number(e.ID_Especialidade) === Number(id));
            if (!esp) {
                showNotification('Especialidade não encontrada.', 'error');
                return;
            }
            document.getElementById('specialty-id').value = esp.ID_Especialidade;
            document.getElementById('specialty-name').value = esp.Nome || '';
            document.getElementById('specialty-description').value = esp.Descricao || '';
            document.getElementById('specialty-modal-title').innerHTML =
                '<i class="fas fa-stethoscope text-blue-600 mr-2"></i>Editar Especialidade';
            document.getElementById('specialty-submit-btn').innerHTML =
                '<i class="fas fa-save mr-1"></i> Salvar Alterações';
            openModal('specialty-modal');
        }

        async function saveSpecialty(event) {
            event.preventDefault();

            const id = document.getElementById('specialty-id').value;
            const nome = document.getElementById('specialty-name').value.trim();
            const descricao = document.getElementById('specialty-description').value.trim();

            if (nome.length < 3) {
                showNotification('O nome deve ter pelo menos 3 caracteres.', 'error');
                return;
            }

            const formData = new FormData();
            if (id) formData.append('id', id);
            formData.append('nome', nome);
            formData.append('descricao', descricao);
            formData.append('csrf_test_name', getCsrfToken());

            const endpoint = id ? '/admin/update_especialidade' : '/admin/create_especialidade';
            const btn = document.getElementById('specialty-submit-btn');
            const originalHtml = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Salvando...';

            try {
                const response = await fetch(AJAX_URL + endpoint, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                const result = await response.json();

                if (result.csrf_token) updateCsrfToken(result.csrf_token);

                if (result.error) {
                    showNotification(result.error, 'error');
                } else {
                    showNotification(result.success || 'Operação concluída.', 'success');
                    closeModal('specialty-modal');
                    loadSpecialties();
                }
            } catch (error) {
                console.error('Erro ao guardar especialidade:', error);
                showNotification('Erro ao guardar especialidade.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }

        // ==================== ELIMINAR ====================
        let deleteTargetId = null;

        function confirmDeleteSpecialty(id) {
            const esp = lastSpecialties.find(e => Number(e.ID_Especialidade) === Number(id));
            if (!esp) return;

            const totalMedicos = Number(esp.total_medicos || 0);
            if (totalMedicos > 0) {
                showNotification(
                    `Não é possível eliminar: existem <strong>${totalMedicos}</strong> médico(s) associados.`,
                    'error'
                );
                return;
            }

            deleteTargetId = id;
            document.getElementById('delete-name').textContent = esp.Nome;
            openModal('delete-modal');
        }

        document.getElementById('confirm-delete-btn').addEventListener('click', async function() {
            if (!deleteTargetId) return;

            const formData = new FormData();
            formData.append('id', deleteTargetId);
            formData.append('csrf_test_name', getCsrfToken());

            this.disabled = true;
            const originalHtml = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Eliminando...';

            try {
                const response = await fetch(AJAX_URL + '/admin/delete_especialidade', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });
                const result = await response.json();

                if (result.csrf_token) updateCsrfToken(result.csrf_token);

                if (result.error) {
                    showNotification(result.error, 'error');
                } else {
                    showNotification(result.success || 'Especialidade eliminada.', 'success');
                    closeModal('delete-modal');
                    deleteTargetId = null;
                    loadSpecialties();
                }
            } catch (error) {
                console.error('Erro ao eliminar especialidade:', error);
                showNotification('Erro ao eliminar especialidade.', 'error');
            } finally {
                this.disabled = false;
                this.innerHTML = originalHtml;
            }
        });

        // ==================== PESQUISA LOCAL ====================
        document.getElementById('search-input').addEventListener('input', function() {
            const q = this.value.trim().toLowerCase();
            if (q === '') {
                renderSpecialties(lastSpecialties);
                return;
            }
            const filtered = lastSpecialties.filter(e =>
                (e.Nome || '').toLowerCase().includes(q) ||
                (e.Descricao || '').toLowerCase().includes(q)
            );
            if (filtered.length === 0) {
                document.getElementById('specialty-list').innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center py-8 text-gray-500">
                            <i class="fas fa-search text-3xl block mb-2 text-gray-300"></i>
                            Nenhuma especialidade corresponde a "${escapeHtml(q)}".
                        </td>
                    </tr>`;
                return;
            }
            renderSpecialties(filtered);
        });

        // ==================== INICIALIZAÇÃO ====================
        document.addEventListener('DOMContentLoaded', function() {
            // Notification close
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
                const isClickOnMenuBtn = mobileMenuBtn ? mobileMenuBtn.contains(e.target) : false;
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

            // Novo / Editar
            document.getElementById('new-specialty-btn').addEventListener('click', openNewSpecialty);

            // Carregar
            loadSpecialties();
        });
    </script>
</body>

</html>