<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamentos - Secretário - Centro de Saúde Da Matola II</title>
    <meta name="description" content="Gerenciar agendamentos">
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

        .sidebar-profile {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            padding: 0.9rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }

        .sidebar.desktop .sidebar-profile {
            justify-content: center;
            padding: 0.9rem;
        }

        .sidebar.desktop.expanded .sidebar-profile {
            justify-content: flex-start;
            padding: 0.9rem 1.1rem;
        }

        .sidebar-profile .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.15);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .sidebar-profile .info {
            color: white;
            overflow: hidden;
        }

        .sidebar-profile .info .name {
            font-size: 0.85rem;
            font-weight: 600;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
        }

        .sidebar-profile .info .role {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.65);
            white-space: nowrap;
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
            height: calc(100% - 64px - 62px);
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

        /* Cards */
        .card-panel {
            background-color: white;
            border-radius: 0.75rem;
            border: 1px solid #eef1f6;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
            padding: 1.5rem;
        }

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

        .form-input,
        .form-select {
            padding: 0.55rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.85rem;
            background: white;
            width: 100%;
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: var(--brand-500);
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

        .btn-primary {
            background-color: white;
            color: var(--brand-700);
            border: 1px solid #d8dee8;
        }

        .btn-primary:hover {
            background-color: #f1f5f9;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background-color: #e5e7eb;
            color: #374151;
        }

        .btn-secondary:hover {
            background-color: #d1d5db;
        }

        .btn-success {
            background-color: var(--teal-500);
            color: white;
        }

        .btn-success:hover {
            background-color: var(--teal-600);
            transform: translateY(-1px);
        }

        .btn-danger {
            background-color: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background-color: #dc2626;
            transform: translateY(-1px);
        }

        .btn:disabled {
            opacity: .55;
            cursor: not-allowed;
            transform: none !important;
        }

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

        .btn-sm:hover {
            transform: scale(1.05);
        }

        .btn-confirm {
            background-color: var(--teal-500);
            color: white;
        }

        .btn-confirm:hover {
            background-color: var(--teal-600);
        }

        .btn-cancel {
            background-color: #f59e0b;
            color: white;
        }

        .btn-cancel:hover {
            background-color: #d97706;
        }

        .btn-edit {
            background-color: var(--brand-500);
            color: white;
        }

        .btn-edit:hover {
            background-color: var(--brand-600);
        }

        .btn-delete {
            background-color: #ef4444;
            color: white;
        }

        .btn-delete:hover {
            background-color: #dc2626;
        }

        .btn-view {
            background-color: #e5e7eb;
            color: #374151;
        }

        .btn-view:hover {
            background-color: #d1d5db;
        }

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
            font-size: 0.72rem;
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

        .status-badge {
            padding: 0.25rem 0.75rem;
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
            background-color: #dbeafe;
            color: #1e40af;
        }

        .status-badge.concluido {
            background-color: #d1fae5;
            color: #065f46;
        }

        .status-badge.cancelado {
            background-color: #fee2e2;
            color: #991b1b;
        }

        .empty-state {
            text-align: center;
            padding: 2.5rem 1rem;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 2.2rem;
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
            max-width: 500px;
            width: 100%;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.35);
            animation: slideUp 0.3s ease-out;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-content.modal-lg {
            max-width: 640px;
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
            margin-bottom: 1.2rem;
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
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }

        .modal-close:hover {
            color: #1f2937;
            background-color: #f3f4f6;
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

        .form-textarea {
            width: 100%;
            padding: 0.55rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            transition: border-color 0.2s;
            font-size: 0.85rem;
            background: white;
            resize: vertical;
            min-height: 80px;
        }

        .form-textarea:focus {
            outline: none;
            border-color: var(--brand-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
        }

        .form-grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0 1rem;
        }

        /* Patient search */
        .patient-search-wrap {
            position: relative;
        }

        .patient-suggestions {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            margin-top: 0.25rem;
            max-height: 190px;
            overflow-y: auto;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
            z-index: 20;
        }

        .patient-suggestions.show {
            display: block;
        }

        .patient-suggestion-item {
            padding: 0.55rem 0.8rem;
            cursor: pointer;
            font-size: 0.85rem;
            border-bottom: 1px solid #f3f4f6;
        }

        .patient-suggestion-item:last-child {
            border-bottom: none;
        }

        .patient-suggestion-item:hover {
            background-color: #f3f5f9;
        }

        .patient-suggestion-item .phone {
            font-size: 0.72rem;
            color: #9ca3af;
        }

        .patient-selected-chip {
            display: none;
            align-items: center;
            gap: 0.5rem;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 0.5rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.82rem;
            color: #1d4ed8;
            margin-top: 0.4rem;
        }

        .patient-selected-chip.show {
            display: flex;
        }

        .patient-selected-chip button {
            margin-left: auto;
            background: none;
            border: none;
            color: #1d4ed8;
            cursor: pointer;
        }

        /* Details modal */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.85rem 1.5rem;
            margin-bottom: 1rem;
        }

        .info-grid .label {
            font-size: 0.68rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--ink-500);
            font-weight: 600;
            margin-bottom: 0.15rem;
        }

        .info-grid .value {
            font-size: 0.9rem;
            color: var(--ink-900);
            font-weight: 500;
        }

        .info-note {
            background: var(--paper);
            border-radius: 0.5rem;
            padding: 0.7rem 0.85rem;
            font-size: 0.83rem;
            color: var(--ink-700);
            margin-top: 0.5rem;
        }

        .details-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            border-top: 2px solid #f3f4f6;
            padding-top: 1rem;
            margin-top: 1rem;
        }

        .details-actions .btn {
            flex: 1;
            min-width: 120px;
            justify-content: center;
        }

        /* Print */
        #print-area {
            display: none;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #print-area,
            #print-area * {
                visibility: visible;
            }

            #print-area {
                display: block;
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                padding: 20px;
            }

            #print-area table {
                width: 100%;
                border-collapse: collapse;
                font-size: 12px;
            }

            #print-area th,
            #print-area td {
                border: 1px solid #999;
                padding: 6px 8px;
                text-align: left;
            }
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

            .pagination button {
                padding: 0.3rem 0.6rem;
                font-size: 0.75rem;
            }

            .filter-btn {
                font-size: 0.65rem;
                padding: 0.25rem 0.6rem;
            }

            .form-grid-2,
            .info-grid {
                grid-template-columns: 1fr;
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
            <h2 class="text-lg font-semibold sidebar-text">Área do Secretário</h2>
            <button id="toggle-sidebar-btn" aria-label="Alternar menu">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <button id="close-sidebar-btn" class="md:hidden close-sidebar-btn" aria-label="Fechar menu">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="sidebar-profile">
            <div class="avatar"><?= strtoupper(substr($secretario->Nome ?? 'S', 0, 1)); ?></div>
            <div class="info">
                <div class="name"><?= htmlspecialchars(($secretario->Nome ?? '') . ' ' . ($secretario->Sobrenome ?? '')); ?></div>
                <div class="role">Secretário(a)</div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <div class="main-menu">
                <a href="<?= site_url('secretario') ?>">
                    <i class="fas fa-chart-pie"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
                <a href="<?= site_url('secretario/agendamentos') ?>" class="active">
                    <i class="fas fa-calendar-check"></i>
                    <span class="sidebar-text">Agendamentos</span>
                </a>
                <a href="<?= site_url('secretario/pacientes') ?>">
                    <i class="fas fa-users"></i>
                    <span class="sidebar-text">Pacientes</span>
                </a>
                <a href="<?= site_url('secretario/medicos') ?>">
                    <i class="fas fa-user-md"></i>
                    <span class="sidebar-text">Médicos</span>
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
                        <p class="text-xs text-blue-100 opacity-90">Área do Secretário</p>
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
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-800">Agendamentos</h2>
                        <p class="text-gray-500 text-sm">Gerencie todos os agendamentos do sistema</p>
                    </div>
                    <div class="flex gap-2">
                        <button class="btn btn-success" id="new-appointment-btn">
                            <i class="fas fa-plus"></i> Novo Agendamento
                        </button>
                        <button class="btn btn-primary" onclick="window.location.href='<?= site_url('secretario') ?>'">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </button>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card-panel mb-6">
                    <div class="flex flex-wrap items-center gap-2 mb-4" id="filter-container">
                        <button class="filter-btn active" data-filter="all">
                            <i class="fas fa-list mr-1"></i>Todos
                            <span class="count" id="count-all">0</span>
                        </button>
                        <button class="filter-btn" data-filter="Pendente">
                            <i class="fas fa-clock mr-1"></i>Pendentes
                            <span class="count" id="count-pendente">0</span>
                        </button>
                        <button class="filter-btn" data-filter="Confirmado">
                            <i class="fas fa-check-circle mr-1"></i>Confirmados
                            <span class="count" id="count-confirmado">0</span>
                        </button>
                        <button class="filter-btn" data-filter="Concluido">
                            <i class="fas fa-check-double mr-1"></i>Concluídos
                            <span class="count" id="count-concluido">0</span>
                        </button>
                        <button class="filter-btn" data-filter="Cancelado">
                            <i class="fas fa-times-circle mr-1"></i>Cancelados
                            <span class="count" id="count-cancelado">0</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Data</label>
                            <input type="date" id="filter-date" class="form-input" value="">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Especialidade</label>
                            <select id="filter-specialty" class="form-select">
                                <option value="">Todas as especialidades</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Médico</label>
                            <select id="filter-doctor" class="form-select">
                                <option value="">Todos os médicos</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pesquisar</label>
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" id="filter-search" class="form-input" style="padding-left:2.6rem;" placeholder="Nome do paciente...">
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-end gap-2 mt-4">
                        <button class="btn btn-primary" id="apply-filters">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                        <button class="btn btn-secondary" id="clear-filters">
                            <i class="fas fa-undo"></i> Limpar
                        </button>
                        <div class="flex-1"></div>
                        <button class="btn btn-secondary" id="export-csv-btn">
                            <i class="fas fa-file-csv"></i> Exportar CSV
                        </button>
                        <button class="btn btn-secondary" id="export-print-btn">
                            <i class="fas fa-print"></i> Imprimir lista
                        </button>
                    </div>
                </div>

                <!-- Lista de Agendamentos -->
                <div class="card-panel">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Lista de Agendamentos</h3>
                        <span class="text-xs text-gray-400" id="appointment-count"></span>
                    </div>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Paciente</th>
                                    <th>Médico</th>
                                    <th>Especialidade</th>
                                    <th>Data</th>
                                    <th>Hora</th>
                                    <th>Status</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="appointments-list">
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-gray-500">
                                        <span class="loading-spinner"></span> Carregando agendamentos...
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

    <!-- Modal de Cancelamento -->
    <div id="cancel-modal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle text-amber-500 mr-2"></i>Cancelar Agendamento</h3>
                <button class="modal-close" onclick="closeModal('cancel-modal')">&times;</button>
            </div>
            <form id="cancel-form" onsubmit="confirmCancel(event)">
                <input type="hidden" id="cancel-id">
                <div class="form-group">
                    <label for="cancel-motivo">Motivo do Cancelamento <span class="required">*</span></label>
                    <textarea id="cancel-motivo" class="form-textarea" required placeholder="Descreva o motivo do cancelamento..."></textarea>
                </div>
                <div class="flex gap-3 mt-4">
                    <button type="submit" class="btn btn-danger flex-1">
                        <i class="fas fa-times mr-1"></i> Confirmar Cancelamento
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('cancel-modal')">Fechar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Criar/Editar Agendamento -->
    <div id="appointment-modal" class="modal-overlay">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <h3 id="appointment-modal-title"><i class="fas fa-calendar-plus text-blue-600 mr-2"></i>Novo Agendamento</h3>
                <button class="modal-close" onclick="closeModal('appointment-modal')">&times;</button>
            </div>
            <form id="appointment-form" onsubmit="submitAppointmentForm(event)">
                <input type="hidden" id="apt-mode" value="create">
                <input type="hidden" id="apt-id">
                <input type="hidden" id="apt-patient-id">

                <div class="form-group">
                    <label for="apt-patient-search">Paciente <span class="required">*</span></label>
                    <div class="patient-search-wrap">
                        <input type="text" id="apt-patient-search" class="form-input" placeholder="Digite o nome do paciente..." autocomplete="off">
                        <div class="patient-suggestions" id="patient-suggestions"></div>
                    </div>
                    <div class="patient-selected-chip" id="patient-selected-chip">
                        <i class="fas fa-user-check"></i>
                        <span id="patient-selected-name"></span>
                        <button type="button" onclick="clearSelectedPatient()"><i class="fas fa-times"></i></button>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="apt-specialty">Especialidade <span class="required">*</span></label>
                        <select id="apt-specialty" class="form-select" required></select>
                    </div>
                    <div class="form-group">
                        <label for="apt-doctor">Médico <span class="required">*</span></label>
                        <select id="apt-doctor" class="form-select" required></select>
                    </div>
                </div>

                <div class="form-grid-2">
                    <div class="form-group">
                        <label for="apt-date">Data <span class="required">*</span></label>
                        <input type="date" id="apt-date" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label for="apt-time">Hora <span class="required">*</span></label>
                        <input type="time" id="apt-time" class="form-input" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="apt-notes">Observações (opcional)</label>
                    <textarea id="apt-notes" class="form-textarea" placeholder="Ex: paciente pediu preferência pelo período da manhã..."></textarea>
                </div>

                <div class="flex gap-3 mt-4">
                    <button type="submit" class="btn btn-success flex-1" id="appointment-submit-btn">
                        <i class="fas fa-check mr-1"></i> Guardar Agendamento
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('appointment-modal')">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de Detalhes -->
    <div id="details-modal" class="modal-overlay">
        <div class="modal-content modal-lg">
            <div class="modal-header">
                <h3><i class="fas fa-info-circle text-blue-600 mr-2"></i>Detalhes do Agendamento</h3>
                <button class="modal-close" onclick="closeModal('details-modal')">&times;</button>
            </div>
            <div id="details-modal-body"></div>
        </div>
    </div>

    <!-- Área usada apenas para impressão -->
    <div id="print-area"></div>

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
            setTimeout(() => {
                notification.classList.remove('show');
            }, 5000);
        }

        function debounce(fn, wait) {
            let t;
            return function(...args) {
                clearTimeout(t);
                t = setTimeout(() => fn.apply(this, args), wait);
            };
        }

        // ==================== DADOS DE APOIO ====================
        const SPECIALTIES = [{
                id: 1,
                nome: 'Clínica Geral'
            },
            {
                id: 2,
                nome: 'Pediatria'
            },
            {
                id: 3,
                nome: 'Ginecologia'
            },
            {
                id: 4,
                nome: 'Cardiologia'
            },
            {
                id: 5,
                nome: 'Ortopedia'
            },
        ];
        const DOCTORS = [{
                id: 1,
                nome: 'Dr. Armando Cossa',
                especialidade_id: 1
            },
            {
                id: 2,
                nome: 'Dra. Fátima Nhaca',
                especialidade_id: 2
            },
            {
                id: 3,
                nome: 'Dr. Bento Machel',
                especialidade_id: 4
            },
            {
                id: 4,
                nome: 'Dra. Ivone Sitoe',
                especialidade_id: 3
            },
            {
                id: 5,
                nome: 'Dr. Paulo Zunguza',
                especialidade_id: 5
            },
            {
                id: 6,
                nome: 'Dra. Amélia Tembe',
                especialidade_id: 1
            },
        ];
        const PATIENTS = [{
                id: 101,
                nome: 'Ana Muchanga',
                telefone: '84 123 4567'
            },
            {
                id: 102,
                nome: 'Carlos Nhantumbo',
                telefone: '82 234 5678'
            },
            {
                id: 103,
                nome: 'Beatriz Cumbe',
                telefone: '87 345 6789'
            },
            {
                id: 104,
                nome: 'Eduardo Massingue',
                telefone: '84 456 7890'
            },
            {
                id: 105,
                nome: 'Ana Paula Sitoe',
                telefone: '86 567 8901'
            },
        ];

        let lastAppointments = [];

        // ==================== POPULAR SELECTS ====================
        function populateSpecialtySelect(selectId, includeAllOption) {
            const sel = document.getElementById(selectId);
            if (!sel) return;
            const allOpt = includeAllOption ? `<option value="">Todas as especialidades</option>` : `<option value="">Selecione a especialidade</option>`;
            sel.innerHTML = allOpt + SPECIALTIES.map(s => `<option value="${s.id}">${s.nome}</option>`).join('');
        }

        function populateDoctorSelect(selectId, specialtyId, includeAllOption) {
            const sel = document.getElementById(selectId);
            if (!sel) return;
            const list = specialtyId ? DOCTORS.filter(d => String(d.especialidade_id) === String(specialtyId)) : DOCTORS;
            const allOpt = includeAllOption ? `<option value="">Todos os médicos</option>` : `<option value="">Selecione o médico</option>`;
            sel.innerHTML = allOpt + list.map(d => `<option value="${d.id}">${d.nome}</option>`).join('');
        }

        // ==================== MODAL GENÉRICO ====================
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

        // ==================== CANCELAMENTO ====================
        let cancelId = null;

        function openCancelModal(id) {
            cancelId = id;
            document.getElementById('cancel-id').value = id;
            document.getElementById('cancel-motivo').value = '';
            openModal('cancel-modal');
        }

        async function confirmCancel(event) {
            event.preventDefault();
            const id = document.getElementById('cancel-id').value;
            const motivo = document.getElementById('cancel-motivo').value.trim();

            if (!motivo) {
                showNotification('Por favor, informe o motivo do cancelamento.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('id', id);
            formData.append('status', 'Cancelado');
            formData.append('motivo', motivo);
            formData.append('csrf_test_name', getCsrfToken());

            try {
                const response = await fetch(AJAX_URL + '/secretario/update_appointment_status', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                const result = await response.json();
                if (result.error) {
                    showNotification(result.error, 'error');
                } else {
                    showNotification(result.success || 'Agendamento cancelado com sucesso!', 'success');
                    closeModal('cancel-modal');
                    closeModal('details-modal');
                    loadAppointments(currentPage);
                }
            } catch (error) {
                console.error('Erro ao cancelar:', error);
                showNotification('Erro ao cancelar agendamento.', 'error');
            }
        }

        // ==================== CONFIRMAR AGENDAMENTO ====================
        async function confirmAppointment(id) {
            if (!confirm('Confirmar este agendamento?')) return;

            const formData = new FormData();
            formData.append('id', id);
            formData.append('status', 'Confirmado');
            formData.append('csrf_test_name', getCsrfToken());

            try {
                const response = await fetch(AJAX_URL + '/secretario/update_appointment_status', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                const result = await response.json();
                if (result.error) {
                    showNotification(result.error, 'error');
                } else {
                    showNotification(result.success || 'Agendamento confirmado com sucesso!', 'success');
                    closeModal('details-modal');
                    loadAppointments(currentPage);
                }
            } catch (error) {
                console.error('Erro ao confirmar:', error);
                showNotification('Erro ao confirmar agendamento.', 'error');
            }
        }

        // ==================== CRIAR / EDITAR AGENDAMENTO ====================
        let selectedPatientId = null;

        function openAppointmentModal(mode, appt) {
            document.getElementById('appointment-form').reset();
            clearSelectedPatient();
            document.getElementById('apt-mode').value = mode;
            document.getElementById('apt-id').value = appt ? appt.ID_Agendamento : '';

            populateSpecialtySelect('apt-specialty', false);
            populateDoctorSelect('apt-doctor', '', false);

            const title = document.getElementById('appointment-modal-title');
            const submitBtn = document.getElementById('appointment-submit-btn');

            if (mode === 'edit' && appt) {
                title.innerHTML = '<i class="fas fa-calendar-edit text-blue-600 mr-2"></i>Editar / Remarcar Agendamento';
                submitBtn.innerHTML = '<i class="fas fa-check mr-1"></i> Guardar Alterações';

                if (appt.paciente_id) {
                    selectedPatientId = appt.paciente_id;
                    document.getElementById('apt-patient-id').value = appt.paciente_id;
                }
                document.getElementById('apt-patient-search').value = appt.paciente_nome || '';
                showPatientChip(appt.paciente_nome || '');

                if (appt.especialidade_id) {
                    document.getElementById('apt-specialty').value = appt.especialidade_id;
                    populateDoctorSelect('apt-doctor', appt.especialidade_id, false);
                }
                if (appt.medico_id) document.getElementById('apt-doctor').value = appt.medico_id;

                document.getElementById('apt-date').value = appt.Data_Agendamento ? appt.Data_Agendamento.substring(0, 10) : '';
                document.getElementById('apt-time').value = appt.Hora_Agendamento ? appt.Hora_Agendamento.substring(0, 5) : '';
                document.getElementById('apt-notes').value = appt.Observacoes || '';
            } else {
                title.innerHTML = '<i class="fas fa-calendar-plus text-blue-600 mr-2"></i>Novo Agendamento';
                submitBtn.innerHTML = '<i class="fas fa-check mr-1"></i> Guardar Agendamento';
                document.getElementById('apt-date').value = document.getElementById('filter-date').value || '';
            }

            openModal('appointment-modal');
        }

        function editAppointment(id) {
            const appt = lastAppointments.find(a => String(a.ID_Agendamento) === String(id));
            if (!appt) {
                showNotification('Agendamento não encontrado na lista actual.', 'error');
                return;
            }
            closeModal('details-modal');
            openAppointmentModal('edit', appt);
        }

        document.getElementById('new-appointment-btn').addEventListener('click', () => openAppointmentModal('create', null));

        document.getElementById('apt-specialty').addEventListener('change', function() {
            populateDoctorSelect('apt-doctor', this.value, false);
        });

        // ---- Pesquisa de paciente (autocomplete simples) ----
        function showPatientChip(nome) {
            document.getElementById('patient-selected-name').textContent = nome;
            document.getElementById('patient-selected-chip').classList.add('show');
        }

        function clearSelectedPatient() {
            selectedPatientId = null;
            document.getElementById('apt-patient-id').value = '';
            document.getElementById('apt-patient-search').value = '';
            document.getElementById('patient-selected-chip').classList.remove('show');
            document.getElementById('patient-suggestions').classList.remove('show');
        }

        const patientSearchInput = document.getElementById('apt-patient-search');
        patientSearchInput.addEventListener('input', debounce(function(e) {
            const q = e.target.value.trim();
            selectedPatientId = null;
            document.getElementById('apt-patient-id').value = '';
            document.getElementById('patient-selected-chip').classList.remove('show');

            const box = document.getElementById('patient-suggestions');
            if (q.length < 2) {
                box.classList.remove('show');
                return;
            }

            const results = PATIENTS.filter(p => p.nome.toLowerCase().includes(q.toLowerCase()));

            if (!results.length) {
                box.innerHTML = `<div class="patient-suggestion-item text-gray-400">Nenhum paciente encontrado</div>`;
            } else {
                box.innerHTML = results.map(p => `
                    <div class="patient-suggestion-item" data-id="${p.id}" data-nome="${p.nome}">
                        <div>${p.nome}</div>
                        <div class="phone">${p.telefone}</div>
                    </div>
                `).join('');
            }
            box.classList.add('show');

            box.querySelectorAll('.patient-suggestion-item[data-id]').forEach(item => {
                item.addEventListener('click', function() {
                    selectedPatientId = this.dataset.id;
                    document.getElementById('apt-patient-id').value = this.dataset.id;
                    patientSearchInput.value = this.dataset.nome;
                    showPatientChip(this.dataset.nome);
                    box.classList.remove('show');
                });
            });
        }, 300));

        document.addEventListener('click', function(e) {
            const box = document.getElementById('patient-suggestions');
            if (box && !box.contains(e.target) && e.target !== patientSearchInput) {
                box.classList.remove('show');
            }
        });

        async function submitAppointmentForm(event) {
            event.preventDefault();

            const mode = document.getElementById('apt-mode').value;
            const patientId = document.getElementById('apt-patient-id').value;
            const specialtyId = document.getElementById('apt-specialty').value;
            const doctorId = document.getElementById('apt-doctor').value;
            const date = document.getElementById('apt-date').value;
            const time = document.getElementById('apt-time').value;

            if (!patientId) {
                showNotification('Seleccione um paciente da lista de sugestões.', 'error');
                return;
            }
            if (!specialtyId || !doctorId) {
                showNotification('Seleccione a especialidade e o médico.', 'error');
                return;
            }
            if (!date || !time) {
                showNotification('Indique a data e a hora do agendamento.', 'error');
                return;
            }

            const formData = new FormData();
            if (mode === 'edit') formData.append('id', document.getElementById('apt-id').value);
            formData.append('paciente_id', patientId);
            formData.append('especialidade_id', specialtyId);
            formData.append('medico_id', doctorId);
            formData.append('data', date);
            formData.append('hora', time);
            formData.append('observacoes', document.getElementById('apt-notes').value.trim());
            formData.append('csrf_test_name', getCsrfToken());

            const endpoint = mode === 'edit' ? '/secretario/update_appointment' : '/secretario/create_appointment';

            const submitBtn = document.getElementById('appointment-submit-btn');
            submitBtn.disabled = true;

            try {
                const response = await fetch(AJAX_URL + endpoint, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });
                const result = await response.json();
                if (result.error) {
                    showNotification(result.error, 'error');
                } else {
                    showNotification(result.success || (mode === 'edit' ? 'Agendamento actualizado com sucesso!' : 'Agendamento criado com sucesso!'), 'success');
                    closeModal('appointment-modal');
                    loadAppointments(currentPage);
                }
            } catch (error) {
                console.error('Erro ao guardar agendamento:', error);
                showNotification('Erro ao guardar agendamento. Verifique se os endpoints do backend já existem.', 'error');
            } finally {
                submitBtn.disabled = false;
            }
        }

        // ==================== DETALHES ====================
        function viewAppointment(id) {
            const appt = lastAppointments.find(a => String(a.ID_Agendamento) === String(id));
            if (!appt) {
                showNotification('Agendamento não encontrado.', 'error');
                return;
            }

            const statusClass = (appt.Status || 'pendente').toLowerCase();
            const canAct = statusClass !== 'concluido' && statusClass !== 'cancelado';

            document.getElementById('details-modal-body').innerHTML = `
                <div class="info-grid">
                    <div><div class="label">Paciente</div><div class="value">${appt.paciente_nome || 'N/A'}</div></div>
                    <div><div class="label">Telefone</div><div class="value">${appt.paciente_telefone || '—'}</div></div>
                    <div><div class="label">Médico</div><div class="value">${appt.medico_nome || 'N/A'}</div></div>
                    <div><div class="label">Especialidade</div><div class="value">${appt.especialidade || 'N/A'}</div></div>
                    <div><div class="label">Data</div><div class="value">${appt.Data_Agendamento ? new Date(appt.Data_Agendamento).toLocaleDateString('pt-PT') : '—'}</div></div>
                    <div><div class="label">Hora</div><div class="value">${appt.Hora_Agendamento ? appt.Hora_Agendamento.substring(0, 5) : '—'}</div></div>
                    <div><div class="label">Status</div><div class="value"><span class="status-badge ${statusClass}">${appt.Status || 'Pendente'}</span></div></div>
                    <div><div class="label">Código</div><div class="value">#${appt.ID_Agendamento}</div></div>
                </div>
                ${appt.Observacoes ? `<div class="info-note"><strong>Observações:</strong> ${appt.Observacoes}</div>` : ''}
                ${appt.Motivo_Cancelamento ? `<div class="info-note"><strong>Motivo do cancelamento:</strong> ${appt.Motivo_Cancelamento}</div>` : ''}

                <div class="details-actions">
                    ${canAct ? `
                        <button class="btn btn-confirm" onclick="confirmAppointment(${appt.ID_Agendamento})"><i class="fas fa-check"></i> Confirmar</button>
                        <button class="btn btn-edit" onclick="editAppointment(${appt.ID_Agendamento})"><i class="fas fa-pen"></i> Editar</button>
                        <button class="btn btn-cancel" onclick="closeModal('details-modal'); openCancelModal(${appt.ID_Agendamento});"><i class="fas fa-times"></i> Cancelar</button>
                    ` : ''}
                    <button class="btn btn-secondary" onclick="printSingleAppointment(${appt.ID_Agendamento})"><i class="fas fa-print"></i> Comprovante</button>
                </div>
            `;
            openModal('details-modal');
        }

        // ==================== EXPORTAR / IMPRIMIR ====================
        function exportCSV() {
            if (!lastAppointments.length) {
                showNotification('Não há dados para exportar.', 'warning');
                return;
            }
            const headers = ['Paciente', 'Telefone', 'Médico', 'Especialidade', 'Data', 'Hora', 'Status'];
            const rows = lastAppointments.map(a => [
                a.paciente_nome || '', a.paciente_telefone || '', a.medico_nome || '', a.especialidade || '',
                a.Data_Agendamento || '', (a.Hora_Agendamento || '').substring(0, 5), a.Status || ''
            ]);
            const csv = headers.join(';') + '\n' + rows.map(r => r.map(v => `"${String(v).replace(/"/g, '""')}"`).join(';')).join('\n');
            const blob = new Blob(['\uFEFF' + csv], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `agendamentos_${new Date().toISOString().slice(0, 10)}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }

        function exportPrint() {
            if (!lastAppointments.length) {
                showNotification('Não há dados para imprimir.', 'warning');
                return;
            }
            const rows = lastAppointments.map(a => `
                <tr>
                    <td>${a.paciente_nome || ''}</td>
                    <td>${a.medico_nome || ''}</td>
                    <td>${a.especialidade || ''}</td>
                    <td>${a.Data_Agendamento ? new Date(a.Data_Agendamento).toLocaleDateString('pt-PT') : ''}</td>
                    <td>${(a.Hora_Agendamento || '').substring(0, 5)}</td>
                    <td>${a.Status || ''}</td>
                </tr>`).join('');

            document.getElementById('print-area').innerHTML = `
                <h2>Centro de Saúde Da Matola II — Lista de Agendamentos</h2>
                <p>Gerado em ${new Date().toLocaleString('pt-PT')}</p>
                <table>
                    <thead><tr><th>Paciente</th><th>Médico</th><th>Especialidade</th><th>Data</th><th>Hora</th><th>Status</th></tr></thead>
                    <tbody>${rows}</tbody>
                </table>`;
            window.print();
        }

        function printSingleAppointment(id) {
            const appt = lastAppointments.find(a => String(a.ID_Agendamento) === String(id));
            if (!appt) return;
            document.getElementById('print-area').innerHTML = `
                <h2>Centro de Saúde Da Matola II — Comprovante de Agendamento</h2>
                <p>Código: #${appt.ID_Agendamento}</p>
                <table>
                    <tbody>
                        <tr><th>Paciente</th><td>${appt.paciente_nome || ''}</td></tr>
                        <tr><th>Médico</th><td>${appt.medico_nome || ''}</td></tr>
                        <tr><th>Especialidade</th><td>${appt.especialidade || ''}</td></tr>
                        <tr><th>Data</th><td>${appt.Data_Agendamento ? new Date(appt.Data_Agendamento).toLocaleDateString('pt-PT') : ''}</td></tr>
                        <tr><th>Hora</th><td>${(appt.Hora_Agendamento || '').substring(0, 5)}</td></tr>
                        <tr><th>Status</th><td>${appt.Status || ''}</td></tr>
                    </tbody>
                </table>`;
            window.print();
        }

        // ==================== CARREGAR AGENDAMENTOS ====================
        let currentPage = 1;
        let totalPages = 1;
        let currentFilter = 'all';

        async function loadAppointments(page = 1) {
            currentPage = page;
            const status = currentFilter === 'all' ? '' : currentFilter;
            const date = document.getElementById('filter-date').value;
            const search = document.getElementById('filter-search').value.trim();
            const specialtyId = document.getElementById('filter-specialty').value;
            const doctorId = document.getElementById('filter-doctor').value;

            const list = document.getElementById('appointments-list');
            const countEl = document.getElementById('appointment-count');

            list.innerHTML = `<tr><td colspan="7" class="text-center py-4 text-gray-500">
                <span class="loading-spinner"></span> Carregando agendamentos...
            </td></tr>`;

            try {
                let url = AJAX_URL + `/secretario/get_appointments?page=${page}&limit=10`;
                if (status) url += `&status=${encodeURIComponent(status)}`;
                if (date) url += `&date=${encodeURIComponent(date)}`;
                if (search) url += `&search=${encodeURIComponent(search)}`;
                if (specialtyId) url += `&specialty_id=${encodeURIComponent(specialtyId)}`;
                if (doctorId) url += `&doctor_id=${encodeURIComponent(doctorId)}`;

                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    const text = await response.text();
                    console.error('Erro na resposta:', text.substring(0, 500));
                    throw new Error('HTTP ' + response.status);
                }

                const data = await response.json();
                lastAppointments = data.data || [];

                if (countEl) countEl.textContent = `${data.total || 0} agendamento(s)`;
                totalPages = data.total_pages || 1;

                if (data.counts) {
                    document.getElementById('count-all').textContent = data.counts.all ?? 0;
                    document.getElementById('count-pendente').textContent = data.counts.Pendente ?? 0;
                    document.getElementById('count-confirmado').textContent = data.counts.Confirmado ?? 0;
                    document.getElementById('count-concluido').textContent = data.counts.Concluido ?? 0;
                    document.getElementById('count-cancelado').textContent = data.counts.Cancelado ?? 0;
                }

                if (!lastAppointments.length) {
                    list.innerHTML = `<tr>
                        <td colspan="7" class="text-center py-4 text-gray-500">
                            <i class="fas fa-calendar-check text-2xl block mb-2 text-gray-300"></i>
                            Nenhum agendamento encontrado.
                        </td>
                    </tr>`;
                    renderPagination();
                    return;
                }

                list.innerHTML = lastAppointments.map(appt => {
                    const statusClass = (appt.Status || 'pendente').toLowerCase();
                    const statusIcons = {
                        'pendente': 'fa-clock',
                        'confirmado': 'fa-check-circle',
                        'concluido': 'fa-check-double',
                        'cancelado': 'fa-times-circle'
                    };
                    const icon = statusIcons[statusClass] || 'fa-circle';
                    const canAct = statusClass !== 'concluido' && statusClass !== 'cancelado';

                    return `
                        <tr>
                            <td class="font-medium">${appt.paciente_nome || 'N/A'}</td>
                            <td>${appt.medico_nome || 'N/A'}</td>
                            <td>${appt.especialidade || 'N/A'}</td>
                            <td>${appt.Data_Agendamento ? new Date(appt.Data_Agendamento).toLocaleDateString('pt-PT') : '—'}</td>
                            <td>${appt.Hora_Agendamento ? appt.Hora_Agendamento.substring(0, 5) : '—'}</td>
                            <td>
                                <span class="status-badge ${statusClass}">
                                    <i class="fas ${icon}" style="font-size:0.65rem;"></i>
                                    ${appt.Status || 'Pendente'}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="flex justify-center gap-1">
                                    ${canAct ? `
                                        <button class="btn-sm btn-confirm" onclick="confirmAppointment(${appt.ID_Agendamento})" title="Confirmar">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn-sm btn-edit" onclick="editAppointment(${appt.ID_Agendamento})" title="Editar / Remarcar">
                                            <i class="fas fa-pen"></i>
                                        </button>
                                        <button class="btn-sm btn-cancel" onclick="openCancelModal(${appt.ID_Agendamento})" title="Cancelar">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    ` : ''}
                                    <button class="btn-sm btn-view" onclick="viewAppointment(${appt.ID_Agendamento})" title="Ver detalhes">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');

                renderPagination();

            } catch (error) {
                console.error('Erro ao carregar agendamentos:', error);
                list.innerHTML = `<tr>
                    <td colspan="7" class="text-center py-4 text-red-500">
                        <i class="fas fa-exclamation-circle text-2xl block mb-2"></i>
                        Erro ao carregar agendamentos: ${error.message}
                    </td>
                </tr>`;
                showNotification('Erro ao carregar agendamentos.', 'error');
            }
        }

        // ==================== PAGINAÇÃO ====================
        function renderPagination() {
            const container = document.getElementById('pagination-container');
            if (!container) return;
            container.innerHTML = '';
            if (totalPages <= 1) return;

            const prevBtn = document.createElement('button');
            prevBtn.textContent = 'Anterior';
            prevBtn.disabled = currentPage === 1;
            prevBtn.addEventListener('click', () => {
                if (currentPage > 1) loadAppointments(currentPage - 1);
            });
            container.appendChild(prevBtn);

            const maxButtons = 5;
            let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
            let endPage = Math.min(totalPages, startPage + maxButtons - 1);
            if (endPage - startPage < maxButtons - 1) startPage = Math.max(1, endPage - maxButtons + 1);

            for (let i = startPage; i <= endPage; i++) {
                const btn = document.createElement('button');
                btn.textContent = i;
                btn.classList.toggle('active', i === currentPage);
                btn.addEventListener('click', () => loadAppointments(i));
                container.appendChild(btn);
            }

            const nextBtn = document.createElement('button');
            nextBtn.textContent = 'Próxima';
            nextBtn.disabled = currentPage === totalPages;
            nextBtn.addEventListener('click', () => {
                if (currentPage < totalPages) loadAppointments(currentPage + 1);
            });
            container.appendChild(nextBtn);
        }

        // ==================== FILTROS ====================
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentFilter = this.dataset.filter;
                loadAppointments(1);
            });
        });

        document.getElementById('filter-specialty').addEventListener('change', function() {
            populateDoctorSelect('filter-doctor', this.value, true);
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

            // Filtros
            document.getElementById('apply-filters').addEventListener('click', () => loadAppointments(1));
            document.getElementById('clear-filters').addEventListener('click', function() {
                document.getElementById('filter-date').value = '';
                document.getElementById('filter-search').value = '';
                document.getElementById('filter-specialty').value = '';
                populateDoctorSelect('filter-doctor', '', true);
                document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
                document.querySelector('.filter-btn[data-filter="all"]').classList.add('active');
                currentFilter = 'all';
                loadAppointments(1);
            });
            document.getElementById('filter-search').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') loadAppointments(1);
            });

            // Exportar / Imprimir
            document.getElementById('export-csv-btn').addEventListener('click', exportCSV);
            document.getElementById('export-print-btn').addEventListener('click', exportPrint);

            // Popular selects de filtro e do formulário
            populateSpecialtySelect('filter-specialty', true);
            populateDoctorSelect('filter-doctor', '', true);
            populateSpecialtySelect('apt-specialty', false);
            populateDoctorSelect('apt-doctor', '', false);

            // Carregar agendamentos iniciais
            loadAppointments(1);
        });
    </script>
</body>

</html>