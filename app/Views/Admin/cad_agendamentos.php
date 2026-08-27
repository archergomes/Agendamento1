<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Agendamento - Administrador - Centro de Saúde Da Matola II</title>
    <meta name="description" content="Cadastrar agendamentos no Centro de Saúde Da Matola II">
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
        /* ... estilos padrão (mesmo das outras views) ... */
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

        .form-container {
            background-color: white;
            border-radius: 0.75rem;
            border: 1px solid #eef1f6;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
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

        .form-group { margin-bottom: 1rem; }
        .form-group label {
            display: block;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.25rem;
            font-size: 0.875rem;
        }
        .form-group label .required { color: #ef4444; }

        .form-input, .form-select {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            transition: border-color 0.2s;
            font-size: 0.875rem;
            background: white;
        }
        .form-input:focus, .form-select:focus {
            outline: none;
            border-color: var(--brand-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
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
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
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

        @media (max-width: 640px) {
            .main-content { padding: 0.5rem; }
            .form-container { padding: 1rem; }
            .card-header { margin: -1rem -1rem 1rem -1rem; padding: 0.75rem 1rem; }
            .form-actions { flex-direction: column; }
            .form-actions .btn { flex: none; }
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
                <a href="<?= site_url('admin/cad_paciente') ?>"><i class="fas fa-user-plus"></i><span class="sidebar-text">Cadastrar Paciente</span></a>
                <a href="<?= site_url('admin/cad_secretario') ?>"><i class="fas fa-user-plus"></i><span class="sidebar-text">Cadastrar Secretário</span></a>
                <a href="<?= site_url('admin/cad_medico') ?>"><i class="fas fa-user-plus"></i><span class="sidebar-text">Cadastrar Médico</span></a>
                <a href="<?= site_url('admin/cad_agendamento') ?>" class="active"><i class="fas fa-calendar-plus"></i><span class="sidebar-text">Cadastrar Agendamento</span></a>
                <a href="<?= site_url('admin/disponibilidade') ?>"><i class="fas fa-clock"></i><span class="sidebar-text">Disponibilidade</span></a>
                <a href="<?= site_url('admin/relatorios') ?>"><i class="fas fa-chart-bar"></i><span class="sidebar-text">Relatórios</span></a>
                <a href="<?= site_url('admin/configuracoes') ?>"><i class="fas fa-cog"></i><span class="sidebar-text">Configurações</span></a>
            </div>
            <button id="logout-btn" class="logout"><i class="fas fa-sign-out-alt"></i><span class="sidebar-text">Sair</span></button>
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
                <div class="text-center mb-6">
                    <h2 class="form-title" id="form-title">Cadastrar Agendamento</h2>
                    <p class="form-subtitle">Crie um novo agendamento para um paciente</p>
                </div>

                <div class="form-container">
                    <div class="card-header">
                        <h3><i class="fas fa-calendar-plus text-blue-500 mr-2"></i>Dados do Agendamento</h3>
                    </div>
                    <form id="appointment-form" onsubmit="saveAppointment(event)">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="patient-id">Paciente <span class="required">*</span></label>
                                <select id="patient-id" class="form-select" required>
                                    <option value="">Selecione um paciente</option>
                                    <?php if (!empty($pacientes)): ?>
                                        <?php foreach ($pacientes as $paciente): ?>
                                            <option value="<?= $paciente->ID_Paciente ?? ''; ?>">
                                                <?= htmlspecialchars(($paciente->Nome ?? '') . ' ' . ($paciente->Sobrenome ?? '')); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="doctor-id">Médico <span class="required">*</span></label>
                                <select id="doctor-id" class="form-select" required>
                                    <option value="">Selecione um médico</option>
                                    <?php if (!empty($medicos)): ?>
                                        <?php foreach ($medicos as $medico): ?>
                                            <option value="<?= $medico->ID_Medico ?? ''; ?>">
                                                <?= htmlspecialchars(($medico->Nome ?? '') . ' ' . ($medico->Sobrenome ?? '')); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="appointment-date">Data <span class="required">*</span></label>
                                <input type="date" id="appointment-date" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label for="appointment-time">Horário <span class="required">*</span></label>
                                <input type="time" id="appointment-time" class="form-input" required>
                            </div>
                            <div class="form-group form-full-width">
                                <label for="appointment-status">Status</label>
                                <select id="appointment-status" class="form-select">
                                    <option value="Pendente">Pendente</option>
                                    <option value="Confirmado">Confirmado</option>
                                    <option value="Concluido">Concluído</option>
                                </select>
                            </div>
                            <div class="form-group form-full-width">
                                <label for="appointment-motive">Motivo</label>
                                <textarea id="appointment-motive" class="form-input" rows="3" placeholder="Motivo da consulta..."></textarea>
                            </div>
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
    </div>

    <script>
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

        async function saveAppointment(event) {
            event.preventDefault();

            const patientId = document.getElementById('patient-id').value;
            const doctorId = document.getElementById('doctor-id').value;
            const date = document.getElementById('appointment-date').value;
            const time = document.getElementById('appointment-time').value;
            const status = document.getElementById('appointment-status').value;
            const motive = document.getElementById('appointment-motive').value.trim();

            if (!patientId || !doctorId || !date || !time) {
                showNotification('Preencha todos os campos obrigatórios.', 'error');
                return;
            }

            const csrfToken = getCsrfToken();
            const csrfName = 'csrf_test_name';

            const formData = new FormData();
            formData.append('paciente_id', patientId);
            formData.append('medico_id', doctorId);
            formData.append('data', date);
            formData.append('hora', time);
            formData.append('status', status);
            formData.append('motivo', motive);
            formData.append(csrfName, csrfToken);

            const btn = document.getElementById('save-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Salvando...';

            try {
                const response = await fetch('<?= site_url('admin/create_appointment') ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                });

                const result = await response.json();

                if (result.error) {
                    showNotification(result.error, 'error');
                } else {
                    showNotification(result.success || 'Agendamento criado com sucesso!', 'success');
                    setTimeout(() => {
                        window.location.href = '<?= site_url('admin/agendamentos') ?>';
                    }, 1500);
                }
            } catch (error) {
                console.error('Erro:', error);
                showNotification('Erro ao criar agendamento.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save mr-1"></i>Salvar';
            }
        }

        document.getElementById('cancel-btn').addEventListener('click', function() {
            window.location.href = '<?= site_url('admin/agendamentos') ?>';
        });

        // Sidebar handlers
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('notification-close').addEventListener('click', function() {
                document.getElementById('notification').classList.remove('show');
            });

            // ... (sidebar handlers padrão)
        });
    </script>
</body>
</html>