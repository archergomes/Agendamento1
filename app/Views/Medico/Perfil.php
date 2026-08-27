<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meu Perfil - Médico - Centro de Saúde Da Matola II</title>
    <meta name="description" content="Gerencie suas informações pessoais">
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

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            font-weight: 700;
            color: var(--brand-700);
            margin: 0 auto 1rem;
            border: 4px solid white;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
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
        .btn-primary { background-color: white; color: var(--brand-700); }
        .btn-primary:hover { background-color: #f1f5f9; transform: translateY(-1px); }
        .btn-success { background-color: var(--teal-500); color: white; }
        .btn-success:hover { background-color: var(--teal-600); transform: translateY(-1px); }
        .btn-secondary { background-color: #e5e7eb; color: #374151; }
        .btn-secondary:hover { background-color: #d1d5db; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none !important; }

        .form-group { margin-bottom: 1rem; }
        .form-group label {
            display: block;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
        }
        .form-group label .required { color: #ef4444; }
        .form-group .form-hint {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        .form-input {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            transition: border-color 0.2s;
            font-size: 0.875rem;
            background: white;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--brand-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }
        .form-input:read-only {
            background-color: #f9fafb;
            color: #6b7280;
            cursor: not-allowed;
        }
        .form-input.input-success {
            border-color: #10b981 !important;
            background-color: #f0fdf4;
        }
        .form-input.input-error {
            border-color: #ef4444 !important;
            background-color: #fef2f2;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        @media (min-width: 640px) {
            .form-grid {
                grid-template-columns: 1fr 1fr;
            }
        }
        .form-full-width {
            grid-column: 1 / -1;
        }

        .profile-info-item {
            display: flex;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .profile-info-item:last-child { border-bottom: none; }
        .profile-info-label {
            font-weight: 600;
            color: #6b7280;
            width: 35%;
            flex-shrink: 0;
        }
        .profile-info-value {
            color: #1f2937;
            width: 65%;
        }

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

        @media (max-width: 640px) {
            .main-content { padding: 0.5rem; }
            .profile-avatar { width: 80px; height: 80px; font-size: 2rem; }
            .profile-info-item { flex-direction: column; padding: 0.5rem 0; }
            .profile-info-label { width: 100%; margin-bottom: 0.25rem; }
            .profile-info-value { width: 100%; }
            .card-panel { padding: 1rem; }
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
                <a href="<?= site_url('medico/disponibilidade') ?>">
                    <i class="fas fa-clock"></i>
                    <span class="sidebar-text">Disponibilidade</span>
                </a>
                <a href="<?= site_url('medico/historico') ?>">
                    <i class="fas fa-history"></i>
                    <span class="sidebar-text">Histórico</span>
                </a>
                <a href="<?= site_url('medico/perfil') ?>" class="active">
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
                <h2 class="text-2xl font-semibold text-gray-800 mb-6">Meu Perfil</h2>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Perfil e Informações -->
                    <div class="card-panel text-center">
                        <div class="profile-avatar">
                            <?= strtoupper(substr($medico->Nome ?? 'M', 0, 1)); ?>
                        </div>
                        <h3 class="text-xl font-semibold mt-2">Dr(a). <?= htmlspecialchars(($medico->Nome ?? '') . ' ' . ($medico->Sobrenome ?? '')); ?></h3>
                        <p class="text-gray-500"><?= htmlspecialchars($medico->Especialidade ?? ''); ?></p>
                        <p class="text-sm text-gray-400 mt-1">
                            <i class="fas fa-id-badge mr-1"></i>
                            Nº Licença: <?= htmlspecialchars($medico->Numero_Licenca ?? ''); ?>
                        </p>
                        <p class="text-sm text-gray-400 mt-1">
                            <i class="fas fa-calendar-plus mr-1"></i>
                            Cadastrado em: <?= isset($medico->Criado_Em) ? date('d/m/Y', strtotime($medico->Criado_Em)) : '—'; ?>
                        </p>
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <button id="edit-toggle-btn" class="btn btn-primary w-full">
                                <i class="fas fa-edit mr-1"></i> Editar Perfil
                            </button>
                        </div>
                    </div>

                    <!-- Visualização do Perfil -->
                    <div class="lg:col-span-2 card-panel" id="profile-view">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-user-circle text-blue-500 mr-2"></i>Informações Pessoais
                        </h3>
                        <div class="profile-info-item">
                            <span class="profile-info-label"><i class="fas fa-user mr-1"></i>Nome</span>
                            <span class="profile-info-value font-medium"><?= htmlspecialchars($medico->Nome ?? ''); ?></span>
                        </div>
                        <div class="profile-info-item">
                            <span class="profile-info-label"><i class="fas fa-user mr-1"></i>Sobrenome</span>
                            <span class="profile-info-value font-medium"><?= htmlspecialchars($medico->Sobrenome ?? ''); ?></span>
                        </div>
                        <div class="profile-info-item">
                            <span class="profile-info-label"><i class="fas fa-phone mr-1"></i>Telefone</span>
                            <span class="profile-info-value"><?= htmlspecialchars($medico->Telefone ?? ''); ?></span>
                        </div>
                        <div class="profile-info-item">
                            <span class="profile-info-label"><i class="fas fa-envelope mr-1"></i>Email</span>
                            <span class="profile-info-value"><?= htmlspecialchars($medico->Email ?? ''); ?></span>
                        </div>
                        <div class="profile-info-item">
                            <span class="profile-info-label"><i class="fas fa-stethoscope mr-1"></i>Especialidade</span>
                            <span class="profile-info-value font-medium text-teal-600"><?= htmlspecialchars($medico->Especialidade ?? ''); ?></span>
                        </div>
                        <div class="profile-info-item">
                            <span class="profile-info-label"><i class="fas fa-id-badge mr-1"></i>Nº Licença</span>
                            <span class="profile-info-value font-mono"><?= htmlspecialchars($medico->Numero_Licenca ?? ''); ?></span>
                        </div>
                    </div>

                    <!-- Edição do Perfil -->
                    <div class="lg:col-span-2 card-panel hidden" id="profile-edit">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                            <i class="fas fa-edit text-green-500 mr-2"></i>Editar Perfil
                        </h3>
                        <form id="profile-form" onsubmit="updateProfile(event)">
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="edit-nome">Nome <span class="required">*</span></label>
                                    <input type="text" id="edit-nome" class="form-input" value="<?= htmlspecialchars($medico->Nome ?? ''); ?>" required>
                                    <div class="form-error" id="nome-error" style="display:none;color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">Por favor, insira um nome válido</div>
                                </div>
                                <div class="form-group">
                                    <label for="edit-sobrenome">Sobrenome <span class="required">*</span></label>
                                    <input type="text" id="edit-sobrenome" class="form-input" value="<?= htmlspecialchars($medico->Sobrenome ?? ''); ?>" required>
                                    <div class="form-error" id="sobrenome-error" style="display:none;color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">Por favor, insira um sobrenome válido</div>
                                </div>
                                <div class="form-group">
                                    <label for="edit-telefone">Telefone <span class="required">*</span></label>
                                    <input type="tel" id="edit-telefone" class="form-input" value="<?= htmlspecialchars($medico->Telefone ?? ''); ?>" required>
                                    <div class="form-hint">Formato: +258 84 1234567</div>
                                    <div class="form-error" id="telefone-error" style="display:none;color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">Por favor, insira um telefone válido</div>
                                </div>
                                <div class="form-group">
                                    <label for="edit-email">Email</label>
                                    <input type="email" id="edit-email" class="form-input" value="<?= htmlspecialchars($medico->Email ?? ''); ?>">
                                    <div class="form-error" id="email-error" style="display:none;color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">Por favor, insira um email válido</div>
                                </div>
                                <div class="form-group form-full-width">
                                    <label for="edit-especialidade">Especialidade <span class="required">*</span></label>
                                    <input type="text" id="edit-especialidade" class="form-input" value="<?= htmlspecialchars($medico->Especialidade ?? ''); ?>" required>
                                    <div class="form-error" id="especialidade-error" style="display:none;color:#ef4444;font-size:0.75rem;margin-top:0.25rem;">Por favor, insira uma especialidade válida</div>
                                </div>
                                <div class="form-group form-full-width">
                                    <label for="edit-licenca">Nº Licença</label>
                                    <input type="text" id="edit-licenca" class="form-input" value="<?= htmlspecialchars($medico->Numero_Licenca ?? ''); ?>" readonly>
                                    <div class="form-hint">Este campo não pode ser alterado</div>
                                </div>
                            </div>
                            <div class="flex gap-3 mt-4">
                                <button type="submit" class="btn btn-success" id="save-profile-btn">
                                    <i class="fas fa-save mr-1"></i> Salvar Alterações
                                </button>
                                <button type="button" class="btn btn-secondary" id="cancel-edit-btn">
                                    <i class="fas fa-times mr-1"></i> Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
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
            if (!phone) return '';
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

        // ==================== TOGGLE EDIT MODE ====================
        function toggleEditMode(isEditing) {
            const viewSection = document.getElementById('profile-view');
            const editSection = document.getElementById('profile-edit');
            const editBtn = document.getElementById('edit-toggle-btn');

            if (isEditing) {
                viewSection.classList.add('hidden');
                editSection.classList.remove('hidden');
                editBtn.innerHTML = '<i class="fas fa-times mr-1"></i> Cancelar Edição';
                editBtn.className = 'btn btn-secondary w-full';
            } else {
                viewSection.classList.remove('hidden');
                editSection.classList.add('hidden');
                editBtn.innerHTML = '<i class="fas fa-edit mr-1"></i> Editar Perfil';
                editBtn.className = 'btn btn-primary w-full';
                // Resetar valores
                resetForm();
            }
        }

        function resetForm() {
            document.getElementById('edit-nome').value = '<?= htmlspecialchars($medico->Nome ?? ''); ?>';
            document.getElementById('edit-sobrenome').value = '<?= htmlspecialchars($medico->Sobrenome ?? ''); ?>';
            document.getElementById('edit-telefone').value = '<?= htmlspecialchars($medico->Telefone ?? ''); ?>';
            document.getElementById('edit-email').value = '<?= htmlspecialchars($medico->Email ?? ''); ?>';
            document.getElementById('edit-especialidade').value = '<?= htmlspecialchars($medico->Especialidade ?? ''); ?>';
            
            // Remover classes de erro
            document.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
            document.querySelectorAll('.form-error').forEach(el => el.style.display = 'none');
        }

        // ==================== ATUALIZAR PERFIL ====================
        async function updateProfile(event) {
            event.preventDefault();

            const nome = document.getElementById('edit-nome').value.trim();
            const sobrenome = document.getElementById('edit-sobrenome').value.trim();
            const telefone = document.getElementById('edit-telefone').value.trim();
            const email = document.getElementById('edit-email').value.trim();
            const especialidade = document.getElementById('edit-especialidade').value.trim();

            // Validação
            let hasError = false;

            // Limpar erros anteriores
            document.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
            document.querySelectorAll('.form-error').forEach(el => el.style.display = 'none');

            if (!nome) {
                document.getElementById('edit-nome').classList.add('input-error');
                document.getElementById('nome-error').style.display = 'block';
                hasError = true;
            }

            if (!sobrenome) {
                document.getElementById('edit-sobrenome').classList.add('input-error');
                document.getElementById('sobrenome-error').style.display = 'block';
                hasError = true;
            }

            if (!telefone) {
                document.getElementById('edit-telefone').classList.add('input-error');
                document.getElementById('telefone-error').style.display = 'block';
                hasError = true;
            } else if (!validateMozambicanPhone(telefone)) {
                document.getElementById('edit-telefone').classList.add('input-error');
                document.getElementById('telefone-error').textContent = 'Número de telefone inválido. Use o formato +258 84 1234567';
                document.getElementById('telefone-error').style.display = 'block';
                hasError = true;
            }

            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById('edit-email').classList.add('input-error');
                document.getElementById('email-error').style.display = 'block';
                hasError = true;
            }

            if (!especialidade) {
                document.getElementById('edit-especialidade').classList.add('input-error');
                document.getElementById('especialidade-error').style.display = 'block';
                hasError = true;
            }

            if (hasError) {
                showNotification('Por favor, corrija os erros no formulário.', 'error');
                return;
            }

            const telefoneLimpo = cleanPhoneForDatabase(telefone);

            const csrfToken = getCsrfToken();
            const formData = new FormData();
            formData.append('nome', nome);
            formData.append('sobrenome', sobrenome);
            formData.append('telefone', telefoneLimpo);
            formData.append('email', email);
            formData.append('especialidade', especialidade);
            formData.append('csrf_test_name', csrfToken);

            const btn = document.getElementById('save-profile-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Salvando...';

            try {
                const response = await fetch(AJAX_URL + '/medico/update_profile', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });

                const result = await response.json();

                if (result.error) {
                    showNotification(result.error, 'error');
                } else {
                    showNotification(result.success || 'Perfil atualizado com sucesso!', 'success');
                    
                    // Atualizar a view com os novos dados
                    document.querySelector('#profile-view .profile-info-value:nth-child(1)').textContent = nome;
                    document.querySelector('#profile-view .profile-info-value:nth-child(2)').textContent = sobrenome;
                    document.querySelector('#profile-view .profile-info-value:nth-child(3)').textContent = formatMozambicanPhone(telefone);
                    document.querySelector('#profile-view .profile-info-value:nth-child(4)').textContent = email || '—';
                    document.querySelector('#profile-view .profile-info-value:nth-child(5)').textContent = especialidade;
                    
                    // Atualizar avatar e nome no sidebar
                    document.querySelector('.profile-avatar').textContent = nome.charAt(0).toUpperCase();
                    document.querySelector('.sidebar-profile .name').textContent = 'Dr(a). ' + nome + ' ' + sobrenome;
                    document.querySelector('.sidebar-profile .specialty').textContent = especialidade;
                    
                    // Atualizar o título da página
                    document.querySelector('h3.text-xl.font-semibold').textContent = 'Dr(a). ' + nome + ' ' + sobrenome;
                    document.querySelector('p.text-gray-500').textContent = especialidade;

                    toggleEditMode(false);
                }
            } catch (error) {
                console.error('Erro ao atualizar perfil:', error);
                showNotification('Erro ao atualizar perfil.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save mr-1"></i> Salvar Alterações';
            }
        }

        // ==================== VALIDAÇÃO EM TEMPO REAL ====================
        document.addEventListener('DOMContentLoaded', function() {
            // Validação do telefone em tempo real
            const phoneInput = document.getElementById('edit-telefone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function() {
                    this.classList.remove('input-success', 'input-error');
                    document.getElementById('telefone-error').style.display = 'none';
                    
                    if (this.value.length > 0) {
                        if (validateMozambicanPhone(this.value)) {
                            this.classList.add('input-success');
                        } else {
                            this.classList.add('input-error');
                        }
                    }
                });
                
                phoneInput.addEventListener('blur', function() {
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

            // Validação dos outros campos
            ['edit-nome', 'edit-sobrenome', 'edit-email', 'edit-especialidade'].forEach(id => {
                const input = document.getElementById(id);
                if (input) {
                    input.addEventListener('input', function() {
                        this.classList.remove('input-error');
                        const errorId = this.id.replace('edit-', '') + '-error';
                        const errorEl = document.getElementById(errorId);
                        if (errorEl) errorEl.style.display = 'none';
                    });
                }
            });
        });

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

            // Logout
            document.getElementById('logout-btn').addEventListener('click', function() {
                if (confirm('Tem certeza que deseja sair?')) {
                    window.location.href = SITE_URL + '/auth/logout';
                }
            });

            // Toggle edit mode
            document.getElementById('edit-toggle-btn').addEventListener('click', function() {
                const isEditing = document.getElementById('profile-edit').classList.contains('hidden');
                toggleEditMode(isEditing);
            });

            // Cancelar edição
            document.getElementById('cancel-edit-btn').addEventListener('click', function() {
                toggleEditMode(false);
            });
        });
    </script>
</body>
</html>