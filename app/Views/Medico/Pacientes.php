<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Pacientes - Médico - Centro de Saúde Da Matola II</title>
    <meta name="description" content="Lista de pacientes atendidos por você">
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

        .form-input {
            padding: 0.55rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            background: white;
        }
        .form-input:focus {
            outline: none; border-color: var(--brand-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
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
        .btn-secondary { background-color: #e5e7eb; color: #374151; }
        .btn-secondary:hover { background-color: #d1d5db; }

        .table-container { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
        thead th {
            background-color: #f3f5f9;
            padding: 0.75rem 1rem;
            text-align: left;
            font-weight: 600;
            color: var(--ink-700);
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }
        tbody td { padding: 0.8rem 1rem; border-bottom: 1px solid #eef1f6; vertical-align: middle; }
        tbody tr:hover { background-color: #f9fafc; }

        .patient-cell { display: flex; align-items: center; gap: 0.6rem; }
        .patient-avatar {
            width: 32px; height: 32px; border-radius: 50%;
            background: var(--brand-100);
            color: var(--brand-700);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.75rem;
            flex-shrink: 0;
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
        .status-badge.ativo { background-color: #d1fae5; color: #065f46; }
        .status-badge.inativo { background-color: #fee2e2; color: #991b1b; }

        .btn-sm {
            padding: 0.3rem 0.65rem;
            font-size: 0.75rem;
            border-radius: 0.4rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }
        .btn-sm:hover { transform: scale(1.05); }
        .btn-view { background-color: var(--brand-500); color: white; }
        .btn-view:hover { background-color: var(--brand-600); }

        .empty-state { text-align: center; padding: 2.5rem 1rem; color: #6b7280; }
        .empty-state i { font-size: 2.2rem; color: #d1d5db; margin-bottom: 0.5rem; }

        .loading-spinner {
            display: inline-block; width: 1.3rem; height: 1.3rem;
            border: 3px solid #e5e7eb;
            border-top-color: var(--brand-500);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

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
            table { font-size: 0.75rem; }
            thead th, tbody td { padding: 0.55rem; }
            .pulse-line { display: none; }
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
                <a href="<?= site_url('medico/pacientes') ?>" class="active">
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
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">Meus Pacientes</h2>
                        <p class="text-gray-500 text-sm">Lista de pacientes atendidos por você</p>
                    </div>
                    <div class="search-box" style="width: 300px;">
                        <i class="fas fa-search"></i>
                        <input type="text" id="search-patient" class="form-input" placeholder="Buscar por nome, BI ou telefone...">
                    </div>
                </div>

                <div class="card-panel">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Pacientes</h3>
                        <span class="text-xs text-gray-400" id="patient-count"></span>
                    </div>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th>Telefone</th>
                                    <th>BI</th>
                                    <th>Total Consultas</th>
                                    <th>Última Consulta</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="patients-list">
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-gray-500">
                                        <span class="loading-spinner"></span> Carregando pacientes...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="pagination-container" class="pagination"></div>
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

        // ==================== CARREGAR PACIENTES ====================
        let currentPage = 1;
        let totalPages = 1;
        let searchQuery = '';

        async function loadPatients(page = 1) {
            currentPage = page;
            const search = document.getElementById('search-patient').value.trim();
            searchQuery = search;

            const list = document.getElementById('patients-list');
            const countEl = document.getElementById('patient-count');
            
            list.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-gray-500">
                <span class="loading-spinner"></span> Carregando pacientes...
            </td></tr>`;

            try {
                let url = AJAX_URL + `/medico/get_patients?page=${page}&limit=10`;
                if (search) url += `&search=${encodeURIComponent(search)}`;

                console.log('📡 URL da requisição:', url);

                const response = await fetch(url, {
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    const text = await response.text();
                    console.error('❌ Resposta do servidor (erro):', text.substring(0, 500));
                    throw new Error('HTTP ' + response.status);
                }

                const data = await response.json();
                console.log('✅ Pacientes carregados:', data);

                if (countEl) {
                    countEl.textContent = `${data.total || 0} paciente(s)`;
                }

                totalPages = data.total_pages || 1;

                if (!data.data || data.data.length === 0) {
                    list.innerHTML = `<tr>
                        <td colspan="6" class="text-center py-4 text-gray-500">
                            <i class="fas fa-users text-2xl block mb-2 text-gray-300"></i>
                            ${search ? 'Nenhum paciente encontrado para esta busca.' : 'Nenhum paciente atendido ainda.'}
                        </td>
                    </tr>`;
                    renderPagination();
                    return;
                }

                list.innerHTML = data.data.map(patient => {
                    const statusClass = patient.total_consultas > 0 ? 'ativo' : 'inativo';
                    const statusLabel = patient.total_consultas > 0 ? 'Ativo' : 'Inativo';
                    const lastVisit = patient.ultima_consulta ? new Date(patient.ultima_consulta).toLocaleDateString('pt-PT') : '—';
                    
                    return `
                        <tr>
                            <td>
                                <div class="patient-cell">
                                    <div class="patient-avatar">${(patient.Nome || '?').charAt(0).toUpperCase()}</div>
                                    <div>
                                        <div class="font-medium">${patient.Nome || ''} ${patient.Sobrenome || ''}</div>
                                    </div>
                                </div>
                            </td>
                            <td>${patient.Telefone || '—'}</td>
                            <td><span class="font-mono text-sm">${patient.BI || '—'}</span></td>
                            <td class="text-center font-bold">${patient.total_consultas || 0}</td>
                            <td>${lastVisit}</td>
                            <td class="text-center">
                                <div class="flex justify-center gap-1">
                                    <button class="btn-sm btn-view" onclick="viewPatient('${patient.BI}')" title="Ver ficha">
                                        <i class="fas fa-file-medical"></i>
                                    </button>
                                    <button class="btn-sm btn-view" onclick="viewHistory('${patient.ID_Paciente}')" title="Ver histórico">
                                        <i class="fas fa-history"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');

                renderPagination();

            } catch (error) {
                console.error('❌ Erro ao carregar pacientes:', error);
                list.innerHTML = `<tr>
                    <td colspan="6" class="text-center py-4 text-red-500">
                        <i class="fas fa-exclamation-circle text-2xl block mb-2"></i>
                        Erro ao carregar pacientes: ${error.message}
                    </td>
                </tr>`;
                showNotification('Erro ao carregar pacientes.', 'error');
            }
        }

        // ==================== PAGINAÇÃO ====================
        function renderPagination() {
            const container = document.getElementById('pagination-container');
            if (!container) return;

            container.innerHTML = '';

            if (totalPages <= 1) return;

            // Botão Anterior
            const prevBtn = document.createElement('button');
            prevBtn.textContent = 'Anterior';
            prevBtn.disabled = currentPage === 1;
            prevBtn.addEventListener('click', () => {
                if (currentPage > 1) loadPatients(currentPage - 1);
            });
            container.appendChild(prevBtn);

            // Números das páginas
            const maxButtons = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
            let endPage = Math.min(totalPages, startPage + maxButtons - 1);

            if (endPage - startPage < maxButtons - 1) {
                startPage = Math.max(1, endPage - maxButtons + 1);
            }

            for (let i = startPage; i <= endPage; i++) {
                const btn = document.createElement('button');
                btn.textContent = i;
                btn.classList.toggle('active', i === currentPage);
                btn.addEventListener('click', () => loadPatients(i));
                container.appendChild(btn);
            }

            // Botão Próxima
            const nextBtn = document.createElement('button');
            nextBtn.textContent = 'Próxima';
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.addEventListener('click', () => {
                if (currentPage < totalPages) loadPatients(currentPage + 1);
            });
            container.appendChild(nextBtn);
        }

        // ==================== VER PACIENTE ====================
        function viewPatient(bi) {
            if (!bi) {
                showNotification('BI do paciente não disponível.', 'warning');
                return;
            }
            // Abrir em nova aba ou modal
            window.open(AJAX_URL + `/medico/patient/${bi}`, '_blank');
        }

        function viewHistory(patientId) {
            if (!patientId) {
                showNotification('ID do paciente não disponível.', 'warning');
                return;
            }
            // Redirecionar para histórico do paciente
            window.location.href = AJAX_URL + `/medico/historico?patient=${patientId}`;
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

            // Busca com debounce
            const searchInput = document.getElementById('search-patient');
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    loadPatients(1);
                }, 300);
            });

            // Enter para buscar
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    clearTimeout(searchTimeout);
                    loadPatients(1);
                }
            });

            // Carregar pacientes iniciais
            loadPatients(1);
        });
    </script>
</body>
</html>