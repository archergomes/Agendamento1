<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - Centro de Saúde Da Matola II</title>
    <meta name="description" content="Gerencie suas informações de perfil no Centro de Saúde Da Matola II">
    <meta name="csrf-token" content="<?= csrf_hash(); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <script>
        var BASE_URL = '<?= rtrim(base_url(), '/'); ?>';
        var SITE_URL = '<?= rtrim(site_url(), '/'); ?>';
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

        #notification.error {
            background-color: var(--rose-500);
        }

        #notification.success {
            background-color: var(--teal-600);
        }

        #notification.info {
            background-color: var(--brand-500);
        }

        #notification.warning {
            background-color: var(--amber-500);
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

        /* ---------- SIDEBAR ---------- */
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
            background: none;
            border: none;
            cursor: pointer;
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

        @media (min-width: 768px) {

            #mobile-menu-btn,
            #sidebar-overlay {
                display: none;
            }
        }

        @media (max-width: 767px) {
            .sidebar.desktop {
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

        #sidebar-overlay.show {
            display: block;
        }

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

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
        }

        /* ---------- PERFIL ---------- */
        .profile-container[aria-hidden="true"] {
            display: none;
        }

        .profile-container[aria-hidden="false"] {
            display: block;
        }

        .profile-card {
            background: white;
            border: 1px solid #eef1f6;
            border-radius: 0.75rem;
            padding: 1rem 1.25rem;
            transition: box-shadow 0.2s ease;
        }

        .profile-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        .profile-card .label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #9ca3af;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .profile-card .label i {
            color: var(--brand-500);
            width: 1rem;
        }

        .profile-card .value {
            font-size: 1rem;
            font-weight: 500;
            color: var(--ink-900);
        }

        /* ---------- FORMULÁRIO ---------- */
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

        .form-group label .required {
            color: var(--rose-500);
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
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

        .form-hint {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 0.25rem;
        }

        .input-error {
            border-color: var(--rose-500) !important;
            background-color: #fef2f2;
        }

        .input-error:focus {
            border-color: var(--rose-500) !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1) !important;
        }

        .input-success {
            border-color: var(--teal-500) !important;
            background-color: #f0fdf4;
        }

        .input-success:focus {
            border-color: var(--teal-500) !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1) !important;
        }

        .loading {
            display: none;
        }

        .loading.show {
            display: inline-block;
        }

        /* ---------- AVATAR ---------- */
        .profile-avatar {
            width: 4rem;
            height: 4rem;
            border-radius: 9999px;
            background: var(--brand-100);
            color: var(--brand-600);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            font-weight: 600;
        }

        /* ---------- RESPONSIVO ---------- */
        @media (max-width: 640px) {
            .profile-card {
                padding: 0.75rem 1rem;
            }

            .profile-card .value {
                font-size: 0.875rem;
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
                <a href="<?= site_url('agenda/agendamentos'); ?>">
                    <i class="fas fa-calendar-check"></i>
                    <span class="sidebar-text">Meus Agendamentos</span>
                </a>
                <a href="<?= site_url('agenda/perfil'); ?>" class="active">
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
                        <p class="text-xs text-blue-100 opacity-90">Meu Perfil</p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <!-- Animação do Eletrocardiograma -->
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
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="bg-white rounded-lg shadow-md p-6" style="border:1px solid #eef1f6;">
                    <!-- Header -->
                    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
                        <div>
                            <h2 class="text-2xl font-semibold text-gray-800">Meu Perfil</h2>
                            <p class="text-gray-500 mt-1">Gerencie suas informações pessoais</p>
                        </div>
                        <button id="edit-profile-btn" class="btn btn-primary">
                            <i class="fas fa-edit mr-1"></i> Editar
                        </button>
                    </div>

                    <!-- Loading State -->
                    <div id="profile-loading" class="loading text-center py-4">
                        <i class="fas fa-spinner fa-spin text-blue-600 text-2xl mr-2"></i>
                        <span class="text-gray-500">Carregando dados do perfil...</span>
                    </div>

                    <!-- View Profile -->
                    <div id="view-profile" class="profile-container" aria-hidden="false">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div class="profile-card">
                                <div class="label"><i class="fas fa-user"></i> Nome Completo</div>
                                <div class="value" id="view-name">
                                    <?= htmlspecialchars(trim(($paciente->Nome ?? '') . ' ' . ($paciente->Sobrenome ?? '')) ?: 'Não informado') ?>
                                </div>
                            </div>
                            <div class="profile-card">
                                <div class="label"><i class="fas fa-phone"></i> Telefone</div>
                                <div class="value" id="view-phone">
                                    <?= htmlspecialchars($paciente->Telefone ?? 'Não informado') ?>
                                </div>
                            </div>
                            <div class="profile-card">
                                <div class="label"><i class="fas fa-id-card"></i> Número do BI</div>
                                <div class="value" id="view-bi">
                                    <?= htmlspecialchars($paciente->BI ?? 'Não informado') ?>
                                </div>
                            </div>
                            <div class="profile-card">
                                <div class="label"><i class="fas fa-envelope"></i> Email</div>
                                <div class="value" id="view-email">
                                    <?= htmlspecialchars($paciente->email ?? 'Não informado') ?>
                                </div>
                            </div>
                            <div class="profile-card">
                                <div class="label"><i class="fas fa-venus-mars"></i> Gênero</div>
                                <div class="value" id="view-gender">
                                    <?= htmlspecialchars($paciente->Genero ?? 'Não informado') ?>
                                </div>
                            </div>
                            <div class="profile-card">
                                <div class="label"><i class="fas fa-birthday-cake"></i> Data de Nascimento</div>
                                <div class="value" id="view-birthdate">
                                    <?php
                                    if (isset($paciente->Data_Nascimento) && !empty($paciente->Data_Nascimento)) {
                                        echo date('d/m/Y', strtotime($paciente->Data_Nascimento));
                                    } else {
                                        echo 'Não informado';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Profile -->
                    <div id="edit-profile" class="profile-container" aria-hidden="true">
                        <form id="edit-profile-form" onsubmit="return false;">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label for="edit-nome">Nome <span class="required">*</span></label>
                                    <input type="text" id="edit-nome"
                                        value="<?= htmlspecialchars($paciente->Nome ?? '') ?>"
                                        required>
                                </div>
                                <div class="form-group">
                                    <label for="edit-sobrenome">Sobrenome <span class="required">*</span></label>
                                    <input type="text" id="edit-sobrenome"
                                        value="<?= htmlspecialchars($paciente->Sobrenome ?? '') ?>"
                                        required>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label for="edit-telefone">Telefone <span class="required">*</span></label>
                                    <input type="tel" id="edit-telefone"
                                        value="<?= htmlspecialchars($paciente->Telefone ?? '') ?>"
                                        required placeholder="+258 8X XXXXXXX">
                                    <div class="form-hint">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Formato: +258 8X XXXXXXX
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="edit-bi">Número do BI <span class="required">*</span></label>
                                    <input type="text" id="edit-bi"
                                        value="<?= htmlspecialchars($paciente->BI ?? '') ?>"
                                        required>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="form-group">
                                    <label for="edit-email">Email</label>
                                    <input type="email" id="edit-email"
                                        value="<?= htmlspecialchars($paciente->email ?? '') ?>"
                                        placeholder="exemplo@email.com">
                                    <div class="form-hint">Opcional</div>
                                </div>
                                <div class="form-group">
                                    <label for="edit-genero">Gênero</label>
                                    <select id="edit-genero">
                                        <option value="">Selecione</option>
                                        <option value="Masculino" <?= ($paciente->Genero ?? '') === 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                                        <option value="Feminino" <?= ($paciente->Genero ?? '') === 'Feminino' ? 'selected' : '' ?>>Feminino</option>
                                        <option value="Outro" <?= ($paciente->Genero ?? '') === 'Outro' ? 'selected' : '' ?>>Outro</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="edit-data_nascimento">Data de Nascimento</label>
                                <input type="date" id="edit-data_nascimento"
                                    value="<?= isset($paciente->Data_Nascimento) && !empty($paciente->Data_Nascimento) ? date('Y-m-d', strtotime($paciente->Data_Nascimento)) : '' ?>">
                            </div>

                            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200">
                                <button type="submit" id="save-profile-btn" class="btn btn-success flex-1">
                                    <i class="fas fa-save mr-1"></i>
                                    <span>Salvar Alterações</span>
                                    <i id="save-loading" class="fas fa-spinner fa-spin ml-2 hidden"></i>
                                </button>
                                <button type="button" id="cancel-edit-btn" class="btn btn-secondary flex-1">
                                    <i class="fas fa-times mr-1"></i>
                                    Cancelar
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

        // ==================== VALIDAÇÕES ====================
        function validatePhone(phone) {
            const cleanPhone = phone.replace(/\s/g, '');
            const phoneRegex = /^(\+258)?[8][0-9]{8}$/;
            return phoneRegex.test(cleanPhone);
        }

        function formatPhone(phone) {
            const clean = phone.replace(/\D/g, '');
            if (clean.length === 12 && clean.startsWith('258')) {
                return '+' + clean.substring(0, 3) + ' ' + clean.substring(3, 5) + ' ' + clean.substring(5, 8) + ' ' + clean.substring(8, 12);
            }
            if (clean.length === 9 && clean.startsWith('8')) {
                return '+258 ' + clean.substring(0, 2) + ' ' + clean.substring(2, 5) + ' ' + clean.substring(5, 9);
            }
            return phone;
        }

        // ==================== ALTERNAR MODO ====================
        function toggleEditMode(isEditing) {
            document.getElementById('view-profile').setAttribute('aria-hidden', isEditing);
            document.getElementById('edit-profile').setAttribute('aria-hidden', !isEditing);

            const editBtn = document.getElementById('edit-profile-btn');
            if (isEditing) {
                editBtn.innerHTML = '<i class="fas fa-times mr-1"></i> Cancelar';
                editBtn.className = 'btn btn-danger';
            } else {
                editBtn.innerHTML = '<i class="fas fa-edit mr-1"></i> Editar';
                editBtn.className = 'btn btn-primary';
            }
        }

        // ==================== SALVAR PERFIL ====================
        function saveProfile() {
            const nome = document.getElementById('edit-nome').value.trim();
            const sobrenome = document.getElementById('edit-sobrenome').value.trim();
            const telefone = document.getElementById('edit-telefone').value.trim();
            const bi = document.getElementById('edit-bi').value.trim();
            const email = document.getElementById('edit-email').value.trim();
            const genero = document.getElementById('edit-genero').value;
            const dataNascimento = document.getElementById('edit-data_nascimento').value;

            // Limpar classes de erro
            document.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));

            // Validações
            let hasError = false;
            let errorMessages = [];

            if (!nome) {
                document.getElementById('edit-nome').classList.add('input-error');
                hasError = true;
                errorMessages.push('Nome é obrigatório');
            }
            if (!sobrenome) {
                document.getElementById('edit-sobrenome').classList.add('input-error');
                hasError = true;
                errorMessages.push('Sobrenome é obrigatório');
            }
            if (!telefone) {
                document.getElementById('edit-telefone').classList.add('input-error');
                hasError = true;
                errorMessages.push('Telefone é obrigatório');
            } else if (!validatePhone(telefone)) {
                document.getElementById('edit-telefone').classList.add('input-error');
                hasError = true;
                errorMessages.push('Número de telefone inválido. Use o formato: +258 8X XXXXXXX');
            }
            if (!bi) {
                document.getElementById('edit-bi').classList.add('input-error');
                hasError = true;
                errorMessages.push('BI é obrigatório');
            }
            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                document.getElementById('edit-email').classList.add('input-error');
                hasError = true;
                errorMessages.push('Email inválido');
            }

            if (hasError) {
                showNotification(errorMessages.join('. '), 'error');
                return;
            }

            // Mostrar loading
            const saveBtn = document.getElementById('save-profile-btn');
            const saveLoading = document.getElementById('save-loading');
            saveBtn.disabled = true;
            saveLoading.classList.remove('hidden');

            // Preparar dados
            const csrfToken = getCsrfToken();
            const formData = new FormData();
            formData.append('nome', nome);
            formData.append('sobrenome', sobrenome);
            formData.append('telefone', telefone);
            formData.append('bi', bi);
            formData.append('email', email);
            formData.append('genero', genero);
            formData.append('data_nascimento', dataNascimento);
            formData.append('csrf_test_name', csrfToken);

            fetch('<?= site_url('agenda/atualizar-perfil') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(async response => {
                    const text = await response.text();
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error('Resposta inválida do servidor');
                    }
                })
                .then(data => {
                    if (data.status === 'success') {
                        showNotification(data.message || 'Perfil atualizado com sucesso!', 'success');

                        // Atualizar a view
                        document.getElementById('view-name').textContent = nome + ' ' + sobrenome;
                        document.getElementById('view-phone').textContent = formatPhone(telefone);
                        document.getElementById('view-bi').textContent = bi;
                        document.getElementById('view-email').textContent = email || 'Não informado';
                        document.getElementById('view-gender').textContent = genero || 'Não informado';
                        if (dataNascimento) {
                            const dateObj = new Date(dataNascimento + 'T00:00:00');
                            if (!isNaN(dateObj)) {
                                document.getElementById('view-birthdate').textContent = dateObj.toLocaleDateString('pt-BR');
                            }
                        }

                        // Voltar para modo de visualização
                        toggleEditMode(false);
                    } else {
                        showNotification(data.message || data.error || 'Erro ao atualizar perfil.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    showNotification('Erro ao conectar ao servidor: ' + error.message, 'error');
                })
                .finally(() => {
                    saveBtn.disabled = false;
                    saveLoading.classList.add('hidden');
                });
        }

        // ==================== INICIALIZAÇÃO ====================
        document.addEventListener('DOMContentLoaded', function() {
            // Esconder loading
            const loading = document.getElementById('profile-loading');
            if (loading) loading.style.display = 'none';

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

            // Edit button
            document.getElementById('edit-profile-btn').addEventListener('click', function() {
                const isEditing = document.getElementById('edit-profile').getAttribute('aria-hidden') === 'false';
                toggleEditMode(!isEditing);
            });

            // Cancel button
            document.getElementById('cancel-edit-btn').addEventListener('click', function() {
                toggleEditMode(false);
                // Resetar valores originais                document.getElementById('edit-nome').value = '<?= htmlspecialchars($paciente->Nome ?? '') ?>';
                document.getElementById('edit-sobrenome').value = '<?= htmlspecialchars($paciente->Sobrenome ?? '') ?>';
                document.getElementById('edit-telefone').value = '<?= htmlspecialchars($paciente->Telefone ?? '') ?>';
                document.getElementById('edit-bi').value = '<?= htmlspecialchars($paciente->BI ?? '') ?>';
                document.getElementById('edit-email').value = '<?= htmlspecialchars($paciente->email ?? '') ?>';
                document.getElementById('edit-genero').value = '<?= htmlspecialchars($paciente->Genero ?? '') ?>';
                document.getElementById('edit-data_nascimento').value = '<?= isset($paciente->Data_Nascimento) && !empty($paciente->Data_Nascimento) ? date('Y-m-d', strtotime($paciente->Data_Nascimento)) : '' ?>';

                // Remover classes de erro
                document.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
            });

            // Save button
            document.getElementById('save-profile-btn').addEventListener('click', function(e) {
                e.preventDefault();
                saveProfile();
            });

            // Validação em tempo real do telefone
            document.getElementById('edit-telefone').addEventListener('input', function() {
                this.classList.remove('input-error', 'input-success');
                if (this.value.length > 0) {
                    if (validatePhone(this.value)) {
                        this.classList.add('input-success');
                    } else {
                        this.classList.add('input-error');
                    }
                }
            });

            // Validação em tempo real do email
            document.getElementById('edit-email').addEventListener('input', function() {
                this.classList.remove('input-error', 'input-success');
                if (this.value.length > 0) {
                    if (/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(this.value)) {
                        this.classList.add('input-success');
                    } else {
                        this.classList.add('input-error');
                    }
                }
            });

            // Enter key para salvar
            document.getElementById('edit-profile-form').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    saveProfile();
                }
            });
        });
    </script>
</body>

</html>