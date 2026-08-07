<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Paciente - Administrador - Hospital Matlhovele</title>
    <meta name="description" content="Cadastrar ou editar pacientes no Hospital Público de Matlhovele">
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

        /* Form Container */
        .form-container {
            background-color: white;
            border-radius: 0.75rem;
            border: 1px solid #eef1f6;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
            max-width: 700px;
            margin: 0 auto;
        }

        .form-title {
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--ink-900);
        }

        .form-subtitle {
            color: var(--ink-500);
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
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

        .form-group label .required {
            color: #ef4444;
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

        .form-hint {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        .form-error {
            font-size: 0.75rem;
            color: #ef4444;
            margin-top: 0.25rem;
            display: none;
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

        .form-actions {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
        }

        .form-actions .btn {
            flex: 1;
            justify-content: center;
        }

        .card-header {
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            padding: 1rem 1.5rem;
            border-radius: 0.75rem 0.75rem 0 0;
            margin: -1.5rem -1.5rem 1.5rem -1.5rem;
            border-bottom: 1px solid #eef1f6;
        }

        .card-header h3 {
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            color: var(--ink-900);
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

        @media (max-width: 640px) {
            .main-content {
                padding: 0.5rem;
            }

            .form-container {
                padding: 1rem;
            }

            .card-header {
                margin: -1rem -1rem 1rem -1rem;
                padding: 0.75rem 1rem;
            }

            .form-actions {
                flex-direction: column;
            }

            .form-actions .btn {
                flex: none;
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
                <div class="text-center mb-6">
                    <h2 class="form-title" id="form-title">Cadastrar Novo Paciente</h2>
                    <p class="form-subtitle">Preencha os dados abaixo para cadastrar ou editar um paciente.</p>
                </div>

                <!-- Patient Form -->
                <div class="form-container">
                    <div class="card-header">
                        <h3><i class="fas fa-user-plus text-blue-500 mr-2"></i>Dados Pessoais</h3>
                    </div>
                    <form id="patient-form" onsubmit="savePatient(event)">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="patient-name">Nome Completo <span class="required">*</span></label>
                                <input type="text" id="patient-name" class="form-input" required placeholder="Nome completo">
                                <div class="form-error" id="name-error">Por favor, insira um nome válido</div>
                            </div>
                            <div class="form-group">
                                <label for="patient-phone">Telefone <span class="required">*</span></label>
                                <input type="tel" id="patient-phone" class="form-input" required placeholder="+258 84 1234567">
                                <div class="form-hint">Formatos: +258 84 1234567 | +258841234567 | 841234567</div>
                                <div class="form-error" id="phone-error">Por favor, insira um telefone válido</div>
                            </div>
                            <div class="form-group">
                                <label for="patient-bi">BI <span class="required">*</span></label>
                                <input type="text" id="patient-bi" class="form-input" required placeholder="Número do BI">
                                <div class="form-error" id="bi-error">Por favor, insira um BI válido</div>
                            </div>
                            <div class="form-group">
                                <label for="patient-email">Email</label>
                                <input type="email" id="patient-email" class="form-input" placeholder="exemplo@email.com">
                                <div class="form-hint">Opcional</div>
                                <div class="form-error" id="email-error">Por favor, insira um email válido</div>
                            </div>
                            <div class="form-group">
                                <label for="patient-birthday">Data de Nascimento</label>
                                <input type="date" id="patient-birthday" class="form-input">
                                <div class="form-hint">Opcional</div>
                            </div>
                            <div class="form-group">
                                <label for="patient-gender">Gênero</label>
                                <select id="patient-gender" class="form-input">
                                    <option value="">Selecione o gênero</option>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Feminino">Feminino</option>
                                    <option value="Outro">Outro</option>
                                </select>
                                <div class="form-hint">Opcional</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="patient-address">Endereço</label>
                            <input type="text" id="patient-address" class="form-input" placeholder="Endereço completo">
                            <div class="form-hint">Opcional</div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary" id="save-btn">
                                <i class="fas fa-save mr-1"></i>Salvar
                            </button>
                            <button type="button" class="btn btn-secondary" id="cancel-btn">
                                <i class="fas fa-times mr-1"></i>Cancelar
                            </button>
                        </div>
                    </form>
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

        // ==================== URL PARAMETERS ====================
        function getQueryParam(param) {
            const urlParams = new URLSearchParams(window.location.search);
            return urlParams.get(param);
        }

        // ==================== FETCH PATIENT ====================
        async function fetchPatient(bi) {
            try {
                const csrfToken = getCsrfToken();
                const formData = new FormData();
                formData.append('bi', bi);
                formData.append(getCsrfName(), csrfToken);

                const response = await fetch('<?= site_url('admin/get_patient_details') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                if (!response.ok) {
                    throw new Error('Erro na requisição: ' + response.status);
                }

                const data = await response.json();
                return data.error ? null : data;
            } catch (error) {
                console.error('Erro ao buscar paciente:', error);
                showNotification('Erro ao carregar dados do paciente.', 'error');
                return null;
            }
        }

        // ==================== FORM VALIDATION ====================
        function validateForm() {
            let isValid = true;

            // Name validation
            const nameInput = document.getElementById('patient-name');
            const nameError = document.getElementById('name-error');
            if (!nameInput.value.trim()) {
                nameInput.classList.add('input-error');
                nameError.style.display = 'block';
                isValid = false;
            } else {
                nameInput.classList.remove('input-error');
                nameError.style.display = 'none';
            }

            // Phone validation
            const phoneInput = document.getElementById('patient-phone');
            const phoneError = document.getElementById('phone-error');
            if (!phoneInput.value.trim() || !validateMozambicanPhone(phoneInput.value)) {
                phoneInput.classList.add('input-error');
                phoneError.style.display = 'block';
                isValid = false;
            } else {
                phoneInput.classList.remove('input-error');
                phoneError.style.display = 'none';
            }

            // BI validation
            const biInput = document.getElementById('patient-bi');
            const biError = document.getElementById('bi-error');
            if (!biInput.value.trim()) {
                biInput.classList.add('input-error');
                biError.style.display = 'block';
                isValid = false;
            } else {
                biInput.classList.remove('input-error');
                biError.style.display = 'none';
            }

            // Email validation (if provided)
            const emailInput = document.getElementById('patient-email');
            const emailError = document.getElementById('email-error');
            if (emailInput.value.trim() && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)) {
                emailInput.classList.add('input-error');
                emailError.style.display = 'block';
                isValid = false;
            } else {
                emailInput.classList.remove('input-error');
                emailError.style.display = 'none';
            }

            return isValid;
        }

        // ==================== SAVE PATIENT ====================
        async function savePatient(event) {
            event.preventDefault();

            if (!validateForm()) {
                showNotification('Por favor, corrija os erros no formulário.', 'error');
                return;
            }

            const nameInput = document.getElementById('patient-name');
            const phoneInput = document.getElementById('patient-phone');
            const biInput = document.getElementById('patient-bi');
            const emailInput = document.getElementById('patient-email');
            const birthdayInput = document.getElementById('patient-birthday');
            const genderInput = document.getElementById('patient-gender');
            const addressInput = document.getElementById('patient-address');

            const name = nameInput.value.trim();
            const telefone = cleanPhoneForDatabase(phoneInput.value.trim());
            const bi = biInput.value.trim();
            const email = emailInput.value.trim() || '';
            const birthday = birthdayInput.value || '';
            const gender = genderInput.value || '';
            const address = addressInput.value.trim() || '';
            const isEditMode = biInput.hasAttribute('readonly');

            const csrfToken = getCsrfToken();
            const csrfName = getCsrfName();

            const formData = new FormData();
            formData.append('bi', bi);
            formData.append('nome', name);
            formData.append('telefone', telefone);
            formData.append('email', email);
            formData.append('data_nascimento', birthday);
            formData.append('genero', gender);
            formData.append('endereco', address);
            formData.append(csrfName, csrfToken);

            const btn = document.getElementById('save-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Salvando...';

            const endpoint = isEditMode ? '<?= site_url('admin/update_patient') ?>' : '<?= site_url('admin/create_patient') ?>';

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                // Tentar ler a resposta como texto primeiro para debug
                const text = await response.text();
                console.log('Resposta bruta:', text);

                let result;
                try {
                    result = JSON.parse(text);
                } catch (parseError) {
                    console.error('Erro ao parsear JSON:', parseError);
                    showNotification('Erro no servidor: ' + text.substring(0, 100), 'error');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-save mr-1"></i>Salvar';
                    return;
                }

                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save mr-1"></i>Salvar';

                if (!response.ok) {
                    showNotification(result.error || 'Erro ao salvar paciente.', 'error');
                    return;
                }

                if (result.error) {
                    showNotification(result.error, 'error');
                } else {
                    showNotification(result.success || (isEditMode ? 'Paciente atualizado com sucesso!' : 'Paciente cadastrado com sucesso!'), 'success');
                    setTimeout(() => {
                        window.location.href = '<?= site_url('admin/pacientes') ?>';
                    }, 1500);
                }
            } catch (error) {
                console.error('Erro detalhado:', error);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save mr-1"></i>Salvar';
                showNotification('Erro ao conectar ao servidor: ' + error.message, 'error');
            }
        }

        // ==================== INICIALIZAÇÃO ====================
        document.addEventListener('DOMContentLoaded', async function() {
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

            // ==================== VALIDAÇÃO EM TEMPO REAL DO TELEFONE ====================
            const phoneInput = document.getElementById('patient-phone');
            if (phoneInput) {
                phoneInput.addEventListener('input', function() {
                    this.classList.remove('input-success', 'input-error');

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

            // ==================== VALIDAÇÃO EM TEMPO REAL DOS OUTROS CAMPOS ====================
            const inputs = ['patient-name', 'patient-bi', 'patient-email'];
            inputs.forEach(id => {
                const input = document.getElementById(id);
                if (input) {
                    input.addEventListener('input', function() {
                        this.classList.remove('input-error');
                        const errorElement = document.getElementById(`${this.id}-error`);
                        if (errorElement) errorElement.style.display = 'none';
                    });
                    input.addEventListener('blur', function() {
                        if (this.value.trim() && this.id !== 'patient-email') {
                            this.classList.add('input-success');
                        }
                    });
                }
            });

            // ==================== CHECK FOR EDIT MODE ====================
            const bi = getQueryParam('bi');
            const formTitle = document.getElementById('form-title');
            const biInput = document.getElementById('patient-bi');

            if (bi) {
                formTitle.textContent = 'Editar Paciente';
                const patient = await fetchPatient(bi);
                if (patient) {
                    document.getElementById('patient-name').value = patient.name || '';
                    document.getElementById('patient-phone').value = patient.phone || '';
                    document.getElementById('patient-bi').value = patient.bi || '';
                    document.getElementById('patient-bi').setAttribute('readonly', 'true');
                    document.getElementById('patient-email').value = patient.email || '';
                    document.getElementById('patient-birthday').value = patient.birthday || '';
                    document.getElementById('patient-gender').value = patient.gender || '';
                    document.getElementById('patient-address').value = patient.address || '';

                    // Validar telefone
                    if (patient.phone && validateMozambicanPhone(patient.phone)) {
                        document.getElementById('patient-phone').classList.add('input-success');
                    }
                } else {
                    showNotification('Paciente não encontrado.', 'error');
                }
            } else {
                formTitle.textContent = 'Cadastrar Novo Paciente';
                biInput.removeAttribute('readonly');
            }

            // ==================== CANCEL BUTTON ====================
            document.getElementById('cancel-btn').addEventListener('click', function() {
                window.location.href = '<?= site_url('admin/pacientes') ?>';
            });

            // ==================== LOGOUT ====================
            document.getElementById('logout-btn').addEventListener('click', function() {
                if (confirm('Tem certeza que deseja sair?')) {
                    window.location.href = SITE_URL + '/auth/logout';
                }
            });
        });
    </script>
</body>

</html>