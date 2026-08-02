<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meus Agendamentos - Hospital Matlhovele</title>
    <meta name="description" content="Visualize e gerencie seus agendamentos no Hospital Público de Matlhovele">
    <meta name="csrf-token" content="<?= csrf_hash(); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <script>
        var BASE_URL = '<?= rtrim(base_url(), '/'); ?>';
        var SITE_URL = '<?= rtrim(site_url(), '/'); ?>';
        var AJAX_URL = SITE_URL;
    </script>
    
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9fafb;
        }

        /* Notification */
        #notification {
            display: none;
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1000;
            padding: 1rem;
            border-radius: 0.25rem;
            color: white;
            max-width: 350px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        #notification.error { background-color: #ef4444; }
        #notification.success { background-color: #10b981; }
        #notification.info { background-color: #3b82f6; }
        #notification.warning { background-color: #f59e0b; }
        #notification.show {
            display: block;
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* Sidebar */
        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out, width 0.3s ease-in-out;
            width: 250px;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .sidebar.show { transform: translateX(0); }
        .sidebar.desktop {
            transform: translateX(0);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 40;
            width: 80px;
            height: 100vh;
            overflow-y: auto;
            background-color: white;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .sidebar.desktop .sidebar-text { display: none; }
        .sidebar.desktop .sidebar-header { justify-content: center; padding: 1rem; }
        .sidebar.desktop .close-sidebar-btn { display: none; }
        .sidebar.desktop.expanded { width: 250px; }
        .sidebar.desktop.expanded .sidebar-text { display: inline; }
        .sidebar.desktop.expanded .sidebar-header { justify-content: space-between; padding: 1rem; }
        
        .page-wrapper {
            margin-left: 80px;
            transition: margin-left 0.3s ease-in-out;
            width: calc(100% - 80px);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .page-wrapper.expanded {
            margin-left: 250px;
            width: calc(100% - 250px);
        }
        .main-content { flex: 1; width: 100%; padding: 1rem; }

        @media (min-width: 768px) {
            #mobile-menu-btn { display: none; }
            .sidebar.desktop { display: block; }
        }
        @media (max-width: 767px) {
            .sidebar.desktop { display: none; }
            .sidebar { transform: translateX(-100%); }
            .page-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }

        .sidebar-nav {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1rem;
        }
        .sidebar-nav a, .sidebar-nav button {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
            color: #4b5563;
        }
        .sidebar-nav a:hover, .sidebar-nav button:hover {
            background-color: #eff6ff;
            transform: translateX(2px);
        }
        .sidebar-nav i { font-size: 1.25rem; width: 24px; text-align: center; }
        .sidebar.desktop .sidebar-nav a, 
        .sidebar.desktop .sidebar-nav button { justify-content: center; padding: 12px; }
        .sidebar.desktop.expanded .sidebar-nav a,
        .sidebar.desktop.expanded .sidebar-nav button { justify-content: flex-start; padding: 12px 16px; }
        .sidebar-nav .logout { margin-top: auto; }

        /* Tabela */
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

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 1rem;
            font-size: 0.75rem;
            font-weight: 500;
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

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 0.25rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        .btn-sm:hover { transform: scale(1.05); }
        .btn-view { background-color: #3b82f6; color: white; }
        .btn-view:hover { background-color: #2563eb; }
        .btn-edit { background-color: #10b981; color: white; }
        .btn-edit:hover { background-color: #059669; }
        .btn-cancel { background-color: #ef4444; color: white; }
        .btn-cancel:hover { background-color: #dc2626; }
        .btn-sm:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

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
            border-color: #3b82f6;
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
        }
        .search-box input:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .search-box i {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #6b7280;
        }
        .empty-state i {
            font-size: 3rem;
            color: #d1d5db;
            margin-bottom: 1rem;
        }

        .doctor-name {
            font-weight: 600;
            color: #1f2937;
        }
        .specialty-name {
            color: #6b7280;
            font-size: 0.75rem;
        }
        .appointment-motive-text {
            color: #4b5563;
            font-size: 0.8rem;
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 50;
            justify-content: center;
            align-items: center;
            padding: 1rem;
        }
        .modal-overlay.show { display: flex; animation: fadeIn 0.3s ease-out; }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: 0.75rem;
            max-width: 550px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.3s ease-out;
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
        .detail-row {
            display: flex;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f3f4f6;
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
        .modal-footer {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
            border-top: 2px solid #f3f4f6;
            padding-top: 1rem;
            flex-wrap: wrap;
        }
        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 0.375rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .btn-primary { background-color: #3b82f6; color: white; }
        .btn-primary:hover { background-color: #2563eb; transform: translateY(-1px); }
        .btn-danger { background-color: #ef4444; color: white; }
        .btn-danger:hover { background-color: #dc2626; transform: translateY(-1px); }
        .btn-success { background-color: #10b981; color: white; }
        .btn-success:hover { background-color: #059669; transform: translateY(-1px); }
        .btn-secondary { background-color: #e5e7eb; color: #374151; }
        .btn-secondary:hover { background-color: #d1d5db; }
        .btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none !important; }

        .form-group { margin-bottom: 1rem; }
        .form-group label {
            display: block;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.25rem;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            transition: border-color 0.2s;
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        .form-group textarea {
            resize: vertical;
            min-height: 60px;
        }

        @media (max-width: 640px) {
            .table-container { font-size: 0.75rem; }
            thead th, tbody td { padding: 0.5rem 0.5rem; }
            .btn-sm { font-size: 0.65rem; padding: 0.125rem 0.375rem; }
            .appointment-motive-text { max-width: 100px; }
        }
    </style>
</head>
<body>
    <!-- Notification -->
    <div id="notification" role="alert">
        <span id="notification-message"></span>
        <button id="notification-close" class="ml-2 text-white hover:text-gray-200">×</button>
    </div>

    <!-- Sidebar -->
    <div id="sidebar-menu" class="sidebar bg-white shadow-lg desktop">
        <div class="sidebar-header flex justify-between items-center p-4 border-b">
            <h2 class="text-lg font-semibold text-gray-700 sidebar-text">Menu</h2>
            <button id="toggle-sidebar-btn" class="text-gray-700 hover:text-gray-900">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <button id="close-sidebar-btn" class="text-gray-700 hover:text-gray-900 md:hidden">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
            <div class="main-menu">
                <a href="<?= site_url('agenda') ?>" class="block text-gray-700 hover:bg-blue-50 rounded">
                    <i class="fas fa-home"></i>
                    <span class="sidebar-text">Home</span>
                </a>
                <a href="<?= site_url('agenda/agendamentos') ?>" class="block bg-blue-50 text-blue-600 rounded active">
                    <i class="fas fa-calendar-check"></i>
                    <span class="sidebar-text">Meus Agendamentos</span>
                </a>
                <a href="<?= site_url('agenda/perfil') ?>" class="block text-gray-700 hover:bg-blue-50 rounded">
                    <i class="fas fa-user"></i>
                    <span class="sidebar-text">Perfil</span>
                </a>
            </div>
            <button id="logout-btn" class="block w-full text-left text-gray-700 hover:bg-blue-50 rounded logout">
                <i class="fas fa-sign-out-alt"></i>
                <span class="sidebar-text">Sair</span>
            </button>
        </nav>
    </div>

    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <header class="bg-blue-600 text-white shadow-lg">
            <div class="container mx-auto px-4 py-3 flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-hospital-alt text-2xl"></i>
                    <h1 class="text-xl font-bold">Hospital Matlhovele</h1>
                </div>
                <button id="mobile-menu-btn" class="md:hidden text-white hover:text-gray-200">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </header>

        <main class="container mx-auto px-4 py-8 main-content">
            <div class="bg-white rounded-lg shadow-md p-6">
                <!-- Header -->
                <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">Meus Agendamentos</h2>
                        <p class="text-gray-600 mt-1">Visualize e gerencie suas consultas</p>
                    </div>
                    <a href="<?= site_url('agenda') ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition flex items-center justify-center">
                        <i class="fas fa-plus mr-2"></i>Novo Agendamento
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
                                    <?php foreach ($agendamentos as $index => $appt): ?>
                                        <?php
                                        $isObject = is_object($appt);
                                        $id = $isObject ? $appt->id : $appt['ID_Agendamento'];
                                        $status = $isObject ? $appt->status : $appt['Status'];
                                        $data = $isObject ? $appt->date : $appt['Data_Agendamento'];
                                        $hora = $isObject ? $appt->time : $appt['Hora_Agendamento'];
                                        $especialidade = $isObject ? $appt->especialidade : $appt['Especialidade'];
                                        $medico = $isObject ? $appt->medico : ($appt['medico_nome'] . ' ' . $appt['medico_sobrenome']);
                                        $motivo = $isObject ? ($appt->motivo ?? '') : ($appt['Motivo'] ?? '');
                                        
                                        $status_class = strtolower($status);
                                        $status_icon = $status_class === 'pendente' ? 'fa-clock' : 
                                                       ($status_class === 'confirmado' ? 'fa-check-circle' : 'fa-times-circle');
                                        ?>
                                        <tr data-id="<?= $id ?>" data-status="<?= $status_class ?>">
                                            <td class="text-gray-500"><?= $index + 1 ?></td>
                                            <td>
                                                <div class="doctor-name">Dr. <?= htmlspecialchars($medico) ?></div>
                                            </td>
                                            <td>
                                                <div class="specialty-name"><?= htmlspecialchars($especialidade) ?></div>
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
                                                <div class="appointment-motive-text" title="<?= htmlspecialchars($motivo) ?>">
                                                    <?= htmlspecialchars($motivo ?: '—') ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="flex flex-wrap gap-1 justify-center">
                                                    <button class="btn-sm btn-view" onclick="viewAppointment(<?= $id ?>)">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <?php if ($status === 'Pendente'): ?>
                                                        <button class="btn-sm btn-edit" onclick="editAppointment(<?= $id ?>)">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn-sm btn-cancel" onclick="confirmCancel(<?= $id ?>)">
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
                            <p class="text-lg font-medium mb-2">Nenhum agendamento encontrado</p>
                            <p class="text-gray-500 mb-4">Você ainda não possui consultas agendadas.</p>
                            <a href="<?= site_url('agenda') ?>" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition inline-flex items-center">
                                <i class="fas fa-plus mr-2"></i>Fazer Primeiro Agendamento
                            </a>
                        </div>
                    <?php endif; ?>
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
                        <label for="edit-data"><i class="fas fa-calendar-alt mr-1"></i>Data</label>
                        <input type="date" id="edit-data" name="data" required>
                    </div>
                    <div class="form-group">
                        <label for="edit-hora"><i class="fas fa-clock mr-1"></i>Horário</label>
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
        let allAppointments = [];

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
            messageEl.innerHTML = message;
            notification.className = `show ${type}`;
            setTimeout(() => {
                notification.classList.remove('show');
            }, 5000);
        }

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
            const counts = { all: rows.length, pendente: 0, confirmado: 0, cancelado: 0 };
            
            rows.forEach(row => {
                const status = row.dataset.status;
                if (status === 'pendente') counts.pendente++;
                else if (status === 'confirmado') counts.confirmado++;
                else if (status === 'cancelado') counts.cancelado++;
            });

            document.getElementById('count-all').textContent = counts.all;
            document.getElementById('count-pendente').textContent = counts.pendente;
            document.getElementById('count-confirmado').textContent = counts.confirmado;
            document.getElementById('count-cancelado').textContent = counts.cancelado;
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
                                     appt.status === 'Confirmado' ? 'fa-check-circle' : 'fa-times-circle';

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
            const pageWrapper = document.querySelector('.page-wrapper');

            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function() {
                    sidebarMenu.classList.add('show');
                    pageWrapper.classList.add('expanded');
                });
            }

            if (closeSidebarBtn) {
                closeSidebarBtn.addEventListener('click', function() {
                    sidebarMenu.classList.remove('show');
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

            // Fechar notificação
            document.getElementById('notification-close').addEventListener('click', function() {
                document.getElementById('notification').classList.remove('show');
            });
        });
    </script>
</body>
</html>