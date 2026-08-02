<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento de Consultas - Hospital Matlhovele</title>
    <meta name="description" content="Sistema inteligente de agendamento de consultas para o Hospital Público de Matlhovele">
    <meta name="csrf-token" content="<?= csrf_hash(); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>

    <!-- URL Base para AJAX - CI4 -->
    <script>
        var BASE_URL = '<?= base_url(); ?>';
        var SITE_URL = '<?= site_url(); ?>';
        var AJAX_URL = SITE_URL;
    </script>

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background-color: #f9fafb;
        }

        .fc-button {
            background-color: #3b82f6 !important;
            border-color: #3b82f6 !important;
            color: white !important;
        }

        .fc-button:hover {
            background-color: #2563eb !important;
        }

        .specialty-item.selected,
        .doctor-item.selected {
            background-color: #3b82f6;
            color: white;
        }

        .specialty-item.selected i,
        .doctor-item.selected i {
            color: white !important;
        }

        #calendar {
            max-width: 900px;
            margin: 0 auto;
        }

        .fc-daygrid-day.selected {
            background-color: #e0f2fe !important;
        }

        .fc-daygrid-day.available {
            background-color: #3b82f6 !important;
            color: white !important;
        }

        .fc-daygrid-day.available .fc-daygrid-day-number {
            color: white !important;
        }

        .fc-day-past {
            background-color: #f3f4f6 !important;
            cursor: not-allowed !important;
        }

        #time-slot-modal,
        #review-modal,
        #confirmation-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 950;
            justify-content: center;
            align-items: center;
        }

        #time-slot-modal.show,
        #review-modal.show,
        #confirmation-modal.show {
            display: flex;
        }

        .modal-content {
            background-color: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-height: 80vh;
            overflow-y: auto;
        }

        #doctor-container {
            max-height: 24rem;
            overflow-y: auto;
            scrollbar-width: thin;
        }

        #doctor-container::-webkit-scrollbar {
            width: 6px;
        }

        #doctor-container::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        #doctor-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        /* Chat Bot Styles */
        #chat-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background-color: #10b981;
            color: white;
            border: none;
            border-radius: 50%;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        #chat-btn:hover {
            transform: scale(1.1);
        }

        #chat-modal {
            display: none;
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 350px;
            height: 500px;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            flex-direction: column;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }

        #chat-modal.show {
            display: flex;
            animation: slideInUp 0.3s ease-out;
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chat-header {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-radius: 12px 12px 0 0;
        }

        .chat-header h3 {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
        }

        #close-chat-btn {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 4px;
            border-radius: 50%;
            transition: background-color 0.2s;
        }

        #close-chat-btn:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .chat-messages {
            flex: 1;
            padding: 1rem;
            overflow-y: auto;
            background-color: #f8fafc;
            max-height: 350px;
        }

        .chat-message {
            margin-bottom: 1rem;
            padding: 0.75rem 1rem;
            border-radius: 1rem;
            max-width: 85%;
            word-wrap: break-word;
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .chat-message.user {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 0.25rem;
        }

        .chat-message.bot {
            background-color: white;
            color: #374151;
            border: 1px solid #e5e7eb;
            margin-right: auto;
            border-bottom-left-radius: 0.25rem;
        }

        .chat-input-container {
            display: flex;
            padding: 1rem;
            gap: 0.5rem;
            background-color: white;
            border-top: 1px solid #e5e7eb;
        }

        .chat-input {
            flex: 1;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 1.5rem;
            outline: none;
            font-size: 0.875rem;
            transition: border-color 0.2s;
        }

        .chat-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .chat-send-btn {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 50%;
            cursor: pointer;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .chat-send-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
        }

        .chat-send-btn:active {
            transform: scale(0.95);
        }

        @media (max-width: 640px) {
            #chat-modal {
                width: calc(100vw - 40px);
                right: 20px;
                left: 20px;
                height: 70vh;
                bottom: 80px;
            }

            #chat-btn {
                bottom: 20px;
                right: 20px;
                width: 56px;
                height: 56px;
            }
        }

        .chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 3px;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 80px;
            background-color: white;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            transform: translateX(0);
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
            width: 250px;
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
            padding: 1rem;
        }

        header {
            position: relative;
            z-index: 800;
            background-color: #2563eb;
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
            margin-left: 250px;
            width: calc(100% - 250px);
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
                width: 250px;
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
            padding: 1rem;
        }

        .sidebar-nav a,
        .sidebar-nav button {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-radius: 0.25rem;
            color: #4b5563;
        }

        .sidebar-nav i {
            font-size: 1.5rem;
            width: 28px;
            text-align: center;
        }

        .sidebar.desktop .sidebar-nav a,
        .sidebar.desktop .sidebar-nav button {
            justify-content: center;
            padding: 10px;
        }

        .sidebar.desktop.expanded .sidebar-nav a,
        .sidebar.desktop.expanded .sidebar-nav button {
            justify-content: flex-start;
            padding: 10px 16px;
        }

        .sidebar-nav .logout {
            margin-top: auto;
        }

        .appointments-list {
            margin-top: 1rem;
        }

        .appointment-item {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .slot-button {
            display: block;
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.25rem;
            text-align: center;
            cursor: pointer;
        }

        .slot-button.available:hover {
            background-color: #e0f2fe;
        }

        .slot-button.booked {
            background-color: #ef4444;
            color: white;
            cursor: not-allowed;
        }

        #notification {
            display: none;
            position: fixed;
            top: 1rem;
            right: 1rem;
            z-index: 1000;
            padding: 1rem;
            border-radius: 0.25rem;
            color: white;
            max-width: 300px;
        }

        #notification.error {
            background-color: #ef4444;
        }

        #notification.success {
            background-color: #10b981;
        }

        #notification.info {
            background-color: #3b82f6;
        }

        #notification.show {
            display: block;
        }

        #time-slot-list {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #d1d5db;
            border-radius: 0.25rem;
            padding: 0.5rem;
            margin-bottom: 1rem;
        }

        #time-slot-list::-webkit-scrollbar {
            width: 6px;
        }

        #time-slot-list::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }

        #time-slot-list::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        #time-slot-list::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        .skip-link {
            position: absolute;
            left: -999px;
            top: 0;
            background: #1d4ed8;
            color: white;
            padding: 0.75rem 1rem;
            z-index: 2000;
            border-radius: 0 0 0.375rem 0;
        }

        .skip-link:focus {
            left: 0;
        }

        a:focus-visible,
        button:focus-visible,
        input:focus-visible,
        textarea:focus-visible,
        [tabindex]:focus-visible {
            outline: 3px solid #f59e0b;
            outline-offset: 2px;
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }

        .booking-stepper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.75rem;
            gap: 0.25rem;
        }

        .booking-step {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex: 1;
            min-width: 0;
        }

        .booking-step .circle {
            flex-shrink: 0;
            width: 2.25rem;
            height: 2.25rem;
            border-radius: 9999px;
            background: #e5e7eb;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
            border: 2px solid #e5e7eb;
            transition: all 0.25s ease;
        }

        .booking-step .label {
            font-size: 0.8rem;
            font-weight: 500;
            color: #6b7280;
            white-space: nowrap;
            display: none;
        }

        @media (min-width: 768px) {
            .booking-step .label {
                display: inline;
            }
        }

        .booking-step .connector {
            flex: 1;
            height: 2px;
            background: #e5e7eb;
            margin: 0 0.5rem;
            transition: background 0.25s ease;
        }

        .booking-step:last-child .connector {
            display: none;
        }

        .booking-step.active .circle {
            background: #2563eb;
            border-color: #2563eb;
            color: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
        }

        .booking-step.active .label {
            color: #1d4ed8;
            font-weight: 600;
        }

        .booking-step.complete .circle {
            background: #10b981;
            border-color: #10b981;
            color: white;
        }

        .booking-step.complete .connector {
            background: #10b981;
        }

        .booking-step.complete .label {
            color: #059669;
        }

        #selection-summary {
            position: sticky;
            top: 0.5rem;
            z-index: 40;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1.75rem;
        }

        .summary-chip {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            background: white;
            border: 1px solid #dbeafe;
            border-radius: 9999px;
            padding: 0.35rem 0.85rem;
            font-size: 0.8rem;
            color: #374151;
        }

        .summary-chip i {
            color: #93c5fd;
            font-size: 0.75rem;
        }

        .summary-chip.filled {
            border-color: #93c5fd;
            background: #dbeafe;
            color: #1d4ed8;
            font-weight: 500;
        }

        .summary-chip.filled i {
            color: #2563eb;
        }

        .specialty-item {
            position: relative;
            text-align: center;
            transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        }

        .specialty-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.06);
        }

        .specialty-item .icon-wrap {
            width: 3rem;
            height: 3rem;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.6rem;
            background: #eff6ff;
        }

        .specialty-item .icon-wrap i {
            margin: 0 !important;
        }

        .specialty-item .check-badge {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            width: 1.25rem;
            height: 1.25rem;
            border-radius: 9999px;
            background: white;
            color: #2563eb;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
        }

        .specialty-item.selected .check-badge {
            display: flex;
        }

        .specialty-item.selected .icon-wrap {
            background: rgba(255, 255, 255, 0.2);
        }

        .doctor-toolbar {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 0.75rem;
        }

        .doctor-toolbar .search-wrap {
            position: relative;
            flex: 1;
        }

        .doctor-toolbar .search-wrap i {
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .doctor-toolbar input {
            width: 100%;
            padding: 0.55rem 0.75rem 0.55rem 2.25rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 0.875rem;
        }

        .doctor-toolbar input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .doctor-item {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .doctor-item:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
        }

        .doctor-item .rating {
            color: #f59e0b;
            font-size: 0.75rem;
            margin-top: 0.15rem;
        }

        .doctor-item img {
            object-fit: cover;
        }

        .skeleton-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            background: white;
        }

        .skeleton-circle,
        .skeleton-line {
            background: linear-gradient(90deg, #eee 25%, #f5f5f5 37%, #eee 63%);
            background-size: 400% 100%;
            animation: skeleton-loading 1.4s ease infinite;
            border-radius: 0.25rem;
        }

        .skeleton-circle {
            width: 3rem;
            height: 3rem;
            border-radius: 9999px;
            flex-shrink: 0;
        }

        .skeleton-line {
            height: 0.75rem;
            margin-bottom: 0.5rem;
        }

        @keyframes skeleton-loading {
            0% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0 50%;
            }
        }

        .empty-state {
            text-align: center;
            padding: 2rem 1rem;
            color: #6b7280;
        }

        .empty-state i {
            font-size: 1.75rem;
            color: #93c5fd;
            margin-bottom: 0.5rem;
            display: block;
        }

        .calendar-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            justify-content: center;
            margin-top: 0.75rem;
            font-size: 0.8rem;
            color: #4b5563;
        }

        .calendar-legend span {
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .legend-dot {
            width: 0.65rem;
            height: 0.65rem;
            border-radius: 9999px;
            display: inline-block;
        }

        .legend-dot.available {
            background: #3b82f6;
        }

        .legend-dot.selected {
            background: #e0f2fe;
            border: 1px solid #3b82f6;
        }

        .legend-dot.unavailable {
            background: #f3f4f6;
            border: 1px solid #d1d5db;
        }

        .modal-content {
            border-radius: 1rem;
            position: relative;
        }

        .modal-close-x {
            position: absolute;
            top: 0.9rem;
            right: 0.9rem;
            width: 2rem;
            height: 2rem;
            border-radius: 9999px;
            background: #f3f4f6;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            transition: background 0.15s ease;
        }

        .modal-close-x:hover {
            background: #e5e7eb;
        }

        .modal-icon-header {
            width: 3rem;
            height: 3rem;
            border-radius: 9999px;
            background: #dbeafe;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
        }

        .modal-icon-header.success {
            background: #d1fae5;
            color: #059669;
        }

        .slot-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
        }

        .review-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem 1rem;
            background: #f9fafb;
            border-radius: 0.5rem;
            padding: 0.9rem;
        }

        .review-grid dt {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #9ca3af;
            font-weight: 600;
        }

        .review-grid dd {
            color: #111827;
            font-weight: 500;
            margin: 0;
        }

        .field-label {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            color: #374151;
            margin-bottom: 0.4rem;
            font-weight: 500;
        }

        .field-label i {
            color: #2563eb;
            width: 1rem;
            text-align: center;
        }

        .field-label .required-dot {
            color: #ef4444;
        }

        .field-input {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .field-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
            outline: none;
        }

        .field-input.field-error {
            border-color: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .field-hint {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 0.25rem;
        }
    </style>
</head>

<body class="bg-gray-50">
    <a href="#main-content" class="skip-link">Saltar para o conteúdo principal</a>

    <!-- Notification -->
    <div id="notification" role="alert">
        <span id="notification-message"></span>
        <button id="notification-close" class="ml-2 text-white hover:text-gray-200">×</button>
    </div>

    <!-- Left Sidebar -->
    <div id="sidebar-menu" class="sidebar bg-white shadow-lg desktop">
        <div class="sidebar-header flex justify-between items-center p-4 border-b">
            <h2 class="text-lg font-semibold text-gray-700 sidebar-text">Menu do Paciente</h2>
            <button id="toggle-sidebar-btn" class="text-gray-700 hover:text-gray-900" aria-label="Alternar menu">
                <i class="fas fa-bars text-xl"></i>
            </button>
            <button id="close-sidebar-btn" class="text-gray-700 hover:text-gray-900 md:hidden close-sidebar-btn" aria-label="Fechar menu">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <nav class="sidebar-nav">
            <div class="main-menu">
                <a href="<?= site_url('agenda'); ?>" class="block text-gray-700 hover:bg-blue-50 rounded">
                    <i class="fas fa-home"></i>
                    <span class="sidebar-text">Home</span>
                </a>
                <a href="<?= site_url('agenda/agendamentos'); ?>" id="meus-agendamentos-btn" class="block text-gray-700 hover:bg-blue-50 rounded">
                    <i class="fas fa-calendar-check"></i>
                    <span class="sidebar-text">Meus Agendamentos</span>
                </a>
                <a href="<?= site_url('agenda/perfil'); ?>" class="block text-gray-700 hover:bg-blue-50 rounded">
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

    <!-- Overlay para fechar sidebar em mobile -->
    <div id="sidebar-overlay"></div>

    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <!-- Header -->
        <header class="bg-blue-600 text-white shadow-lg">
            <div class="container mx-auto px-4 py-3 flex justify-between items-center">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-hospital-alt text-2xl" aria-label="Ícone do Hospital Matlhovele"></i>
                    <h1 class="text-xl font-bold">Hospital Matlhovele</h1>
                </div>
                <button id="mobile-menu-btn" class="md:hidden text-white hover:text-gray-200" aria-label="Abrir menu">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </header>

        <!-- Main Content -->
        <main class="main-content" id="main-content">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-2">Agendar Consulta</h2>
                    <p class="text-gray-500 mb-6">Siga os 4 passos abaixo para marcar a sua consulta no Hospital Matlhovele.</p>

                    <!-- Indicador de progresso -->
                    <div class="booking-stepper" id="booking-stepper" role="list" aria-label="Progresso do agendamento">
                        <div class="booking-step" data-step="1" role="listitem">
                            <div class="circle"><i class="fas fa-stethoscope" aria-hidden="true"></i></div>
                            <span class="label">Especialidade</span>
                            <div class="connector"></div>
                        </div>
                        <div class="booking-step" data-step="2" role="listitem">
                            <div class="circle"><i class="fas fa-user-md" aria-hidden="true"></i></div>
                            <span class="label">Médico</span>
                            <div class="connector"></div>
                        </div>
                        <div class="booking-step" data-step="3" role="listitem">
                            <div class="circle"><i class="fas fa-calendar-alt" aria-hidden="true"></i></div>
                            <span class="label">Data e Hora</span>
                            <div class="connector"></div>
                        </div>
                        <div class="booking-step" data-step="4" role="listitem">
                            <div class="circle"><i class="fas fa-check" aria-hidden="true"></i></div>
                            <span class="label">Confirmação</span>
                        </div>
                    </div>

                    <!-- Resumo da seleção atual -->
                    <div id="selection-summary" aria-live="polite">
                        <div class="summary-chip" id="summary-specialty"><i class="fas fa-circle"></i> Especialidade: <strong>&nbsp;a escolher</strong></div>
                        <div class="summary-chip" id="summary-doctor"><i class="fas fa-circle"></i> Médico: <strong>&nbsp;a escolher</strong></div>
                        <div class="summary-chip" id="summary-date"><i class="fas fa-circle"></i> Data: <strong>&nbsp;a escolher</strong></div>
                        <div class="summary-chip" id="summary-time"><i class="fas fa-circle"></i> Horário: <strong>&nbsp;a escolher</strong></div>
                    </div>

                    <!-- Step 1: Selecionar Especialidade -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-700 mb-4">1. Selecione a especialidade médica</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="specialty-container">
                            <?php if (!empty($especialidades)): ?>
                                <?php foreach ($especialidades as $esp): ?>
                                    <div class="specialty-item border rounded-lg p-4 hover:bg-blue-50 cursor-pointer"
                                        data-specialty="<?= htmlspecialchars($esp->Nome ?? $esp->nome ?? ''); ?>"
                                        tabindex="0" role="button" aria-pressed="false">
                                        <span class="check-badge"><i class="fas fa-check"></i></span>
                                        <div class="icon-wrap">
                                            <i class="fas fa-user-md text-blue-500 text-xl" aria-hidden="true"></i>
                                        </div>
                                        <p class="font-medium"><?= htmlspecialchars($esp->Nome ?? $esp->nome ?? ''); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <!-- Fallback caso não haja especialidades no banco -->
                                <div class="specialty-item border rounded-lg p-4 hover:bg-blue-50 cursor-pointer" data-specialty="Medicina Geral" tabindex="0" role="button" aria-pressed="false">
                                    <span class="check-badge"><i class="fas fa-check"></i></span>
                                    <div class="icon-wrap"><i class="fas fa-user-md text-blue-500 text-xl" aria-hidden="true"></i></div>
                                    <p class="font-medium">Medicina Geral</p>
                                </div>
                                <div class="specialty-item border rounded-lg p-4 hover:bg-blue-50 cursor-pointer" data-specialty="Cardiologia" tabindex="0" role="button" aria-pressed="false">
                                    <span class="check-badge"><i class="fas fa-check"></i></span>
                                    <div class="icon-wrap"><i class="fas fa-heartbeat text-blue-500 text-xl" aria-hidden="true"></i></div>
                                    <p class="font-medium">Cardiologia</p>
                                </div>
                                <div class="specialty-item border rounded-lg p-4 hover:bg-blue-50 cursor-pointer" data-specialty="Pediatria" tabindex="0" role="button" aria-pressed="false">
                                    <span class="check-badge"><i class="fas fa-check"></i></span>
                                    <div class="icon-wrap"><i class="fas fa-child text-blue-500 text-xl" aria-hidden="true"></i></div>
                                    <p class="font-medium">Pediatria</p>
                                </div>
                                <div class="specialty-item border rounded-lg p-4 hover:bg-blue-50 cursor-pointer" data-specialty="Ortopedia" tabindex="0" role="button" aria-pressed="false">
                                    <span class="check-badge"><i class="fas fa-check"></i></span>
                                    <div class="icon-wrap"><i class="fas fa-bone text-blue-500 text-xl" aria-hidden="true"></i></div>
                                    <p class="font-medium">Ortopedia</p>
                                </div>
                                <div class="specialty-item border rounded-lg p-4 hover:bg-blue-50 cursor-pointer" data-specialty="Ginecologia" tabindex="0" role="button" aria-pressed="false">
                                    <span class="check-badge"><i class="fas fa-check"></i></span>
                                    <div class="icon-wrap"><i class="fas fa-female text-blue-500 text-xl" aria-hidden="true"></i></div>
                                    <p class="font-medium">Ginecologia</p>
                                </div>
                                <div class="specialty-item border rounded-lg p-4 hover:bg-blue-50 cursor-pointer" data-specialty="Neurologia" tabindex="0" role="button" aria-pressed="false">
                                    <span class="check-badge"><i class="fas fa-check"></i></span>
                                    <div class="icon-wrap"><i class="fas fa-brain text-blue-500 text-xl" aria-hidden="true"></i></div>
                                    <p class="font-medium">Neurologia</p>
                                </div>
                                <div class="specialty-item border rounded-lg p-4 hover:bg-blue-50 cursor-pointer" data-specialty="Cirurgia Geral" tabindex="0" role="button" aria-pressed="false">
                                    <span class="check-badge"><i class="fas fa-check"></i></span>
                                    <div class="icon-wrap"><i class="fas fa-cut text-blue-500 text-xl" aria-hidden="true"></i></div>
                                    <p class="font-medium">Cirurgia Geral</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Step 2: Selecionar Médico -->
                    <div class="mb-8 doctor-selection-container">
                        <h3 class="text-lg font-medium text-gray-700 mb-4">2. Escolha o médico</h3>
                        <div class="doctor-toolbar">
                            <div class="search-wrap">
                                <i class="fas fa-search" aria-hidden="true"></i>
                                <input type="text" id="doctor-search" placeholder="Procurar médico pelo nome..." aria-label="Procurar médico pelo nome" disabled>
                            </div>
                        </div>
                        <div id="doctor-header" class="hidden mb-2 text-sm font-medium text-blue-600"></div>
                        <div id="doctor-container" class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto border rounded-lg p-4 bg-gray-50">
                            <div class="empty-state col-span-full">
                                <i class="fas fa-user-md" aria-hidden="true"></i>
                                Selecione uma especialidade para ver os médicos disponíveis.
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Selecionar Data e Horário -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium text-gray-700 mb-4">3. Escolha a data e horário</h3>
                        <div id="calendar" class="bg-white p-4 rounded-lg shadow-inner"></div>
                        <div class="calendar-legend">
                            <span><i class="legend-dot available"></i> Com vagas</span>
                            <span><i class="legend-dot selected"></i> Selecionado</span>
                            <span><i class="legend-dot unavailable"></i> Sem vagas / indisponível</span>
                        </div>
                    </div>

                    <!-- Modal para Seleção de Horários -->
                    <div id="time-slot-modal">
                        <div class="modal-content">
                            <button class="modal-close-x" id="time-slot-close-x" aria-label="Fechar"><i class="fas fa-times"></i></button>
                            <div class="modal-icon-header"><i class="fas fa-clock"></i></div>
                            <h3 class="text-lg font-medium text-gray-700 mb-1">Selecione um horário</h3>
                            <p class="text-sm text-gray-500 mb-4" id="time-slot-subtitle">Horários disponíveis para a data escolhida</p>
                            <div id="time-slot-list"></div>
                            <button id="modal-cancel-btn" class="bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition w-full">
                                Cancelar
                            </button>
                        </div>
                    </div>

                    <!-- Modal para Revisão do Agendamento -->
                    <div id="review-modal">
                        <div class="modal-content">
                            <button class="modal-close-x" id="review-close-x" aria-label="Fechar"><i class="fas fa-times"></i></button>
                            <div class="modal-icon-header"><i class="fas fa-clipboard-check"></i></div>
                            <h3 class="text-lg font-medium text-gray-700 mb-1">Revisar Agendamento</h3>
                            <p class="text-sm text-gray-500 mb-4">Confirme os dados antes de finalizar a marcação.</p>
                            <dl id="review-details" class="review-grid mb-4">
                                <div>
                                    <dt>Especialidade</dt>
                                    <dd id="review-specialty"></dd>
                                </div>
                                <div>
                                    <dt>Médico</dt>
                                    <dd id="review-doctor"></dd>
                                </div>
                                <div>
                                    <dt>Data</dt>
                                    <dd id="review-date"></dd>
                                </div>
                                <div>
                                    <dt>Horário</dt>
                                    <dd id="review-time"></dd>
                                </div>
                                <div>
                                    <dt>Nome</dt>
                                    <dd id="review-name"></dd>
                                </div>
                                <div>
                                    <dt>Telefone</dt>
                                    <dd id="review-phone"></dd>
                                </div>
                                <div>
                                    <dt>BI</dt>
                                    <dd id="review-bi"></dd>
                                </div>
                            </dl>
                            <div class="flex space-x-2">
                                <button id="review-confirm-btn" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition flex-1">
                                    <i class="fas fa-check mr-1"></i> Confirmar
                                </button>
                                <button id="review-cancel-btn" class="bg-gray-300 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-400 transition flex-1">
                                    Voltar
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal para Confirmação Final -->
                    <div id="confirmation-modal">
                        <div class="modal-content">
                            <div class="modal-icon-header success"><i class="fas fa-check-circle"></i></div>
                            <h3 class="text-lg font-medium text-gray-700 mb-4">Agendamento Confirmado</h3>
                            <p id="confirmation-message" class="mb-4"></p>
                            <p class="text-xs text-gray-500 mb-4"><i class="fas fa-info-circle mr-1"></i> Chegue com 15 minutos de antecedência e leve o seu Bilhete de Identidade.</p>
                            <button id="confirmation-close-btn" class="bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition w-full">
                                Fechar
                            </button>
                        </div>
                    </div>

                    <!-- Step 4: Confirmar Agendamento -->
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <h3 class="text-lg font-medium text-gray-700 mb-1">4. Confirmar agendamento</h3>
                        <p class="text-sm text-gray-500 mb-4">Verifique os seus dados de contacto — usaremos estes dados para confirmar a consulta.</p>
                        <div class="mb-4">
                            <label class="field-label" for="name"><i class="fas fa-user"></i> Nome completo <span class="required-dot">*</span></label>
                            <input type="text" id="name" value="<?= htmlspecialchars($nome_completo ?? ''); ?>" class="field-input" aria-required="true">
                        </div>
                        <div class="mb-4">
                            <label class="field-label" for="phone"><i class="fas fa-phone"></i> Número de telefone <span class="required-dot">*</span></label>
                            <input type="tel" id="phone" value="<?= htmlspecialchars($telefone ?? ''); ?>" class="field-input" aria-required="true" placeholder="84 123 4567">
                            <p class="field-hint">Vamos enviar a confirmação e lembretes por SMS para este número.</p>
                        </div>
                        <div class="mb-4">
                            <label class="field-label" for="bi"><i class="fas fa-id-card"></i> Número do BI <span class="required-dot">*</span></label>
                            <input type="text" id="bi" value="<?= htmlspecialchars($bi ?? ''); ?>" class="field-input" aria-required="true">
                        </div>
                        <div class="mb-4">
                            <label class="field-label" for="motivo"><i class="fas fa-notes-medical"></i> Motivo da consulta (opcional)</label>
                            <textarea id="motivo" class="field-input" rows="3" maxlength="300" placeholder="Descreva o motivo da consulta..."></textarea>
                            <p class="field-hint">Isto ajuda o médico a preparar-se antes da sua consulta.</p>
                        </div>
                        <button id="confirm-btn" class="bg-blue-600 text-white py-2 px-6 rounded-lg hover:bg-blue-700 transition" aria-label="Confirmar agendamento">
                            <i class="fas fa-calendar-check mr-1"></i> Confirmar Agendamento
                        </button>
                    </div>

                    <!-- Lista de Meus Agendamentos -->
                    <div id="appointments-section" class="hidden mt-8">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Meus Agendamentos</h3>
                        <div id="appointments-list" class="appointments-list"></div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Chat Bot Button -->
    <button id="chat-btn" title="Falar com Assistente">
        <i class="fas fa-comments"></i>
    </button>

    <!-- Chat Modal -->
    <div id="chat-modal">
        <div class="chat-header">
            <h3>Assistente de Agendamento</h3>
            <button id="close-chat-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="chat-messages" id="chat-messages">
            <div class="chat-message bot">
                Olá! Sou o assistente do Hospital Matlhovele. Como posso ajudar? Descreva seus sintomas ou o problema para eu sugerir a especialidade certa.
            </div>
        </div>
        <div class="chat-input-container">
            <input type="text" id="chat-input" class="chat-input" placeholder="Digite sua mensagem...">
            <button id="chat-send-btn" class="chat-send-btn">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <script>
        // Variáveis globais
        let doctors = [];
        let availableSlots = {};
        let selectedSpecialty = null;
        let selectedDoctor = null;
        let selectedDoctorId = null;
        let selectedDate = null;
        let selectedTime = null;
        let calendar;

        // Função para exibir notificações
        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            const messageEl = document.getElementById('notification-message');
            messageEl.innerHTML = `<i class="fas ${notificationIcon(type)} mr-2"></i>${message}`;
            notification.className = `show ${type}`;
            setTimeout(() => {
                notification.classList.remove('show');
            }, 5000);
        }

        // Close notification
        document.getElementById('notification-close')?.addEventListener('click', () => {
            document.getElementById('notification').classList.remove('show');
        });

        // Ícones por tipo de notificação
        function notificationIcon(type) {
            return type === 'error' ? 'fa-exclamation-circle' :
                type === 'success' ? 'fa-check-circle' :
                'fa-info-circle';
        }

        // Atualiza o indicador de progresso
        function updateStepper() {
            const steps = document.querySelectorAll('.booking-step');
            const state = [
                !!selectedSpecialty,
                !!selectedDoctorId,
                !!(selectedDate && selectedTime),
                false
            ];
            let activeIndex = state.findIndex(done => !done);
            if (activeIndex === -1) activeIndex = 3;

            steps.forEach((stepEl, i) => {
                stepEl.classList.remove('active', 'complete');
                if (i < activeIndex) {
                    stepEl.classList.add('complete');
                } else if (i === activeIndex) {
                    stepEl.classList.add('active');
                }
            });
        }

        // Atualiza a barra de resumo
        function updateSummary() {
            const map = [
                ['summary-specialty', 'Especialidade', selectedSpecialty],
                ['summary-doctor', 'Médico', selectedDoctor],
                ['summary-date', 'Data', selectedDate],
                ['summary-time', 'Horário', selectedTime]
            ];
            map.forEach(([id, label, value]) => {
                const el = document.getElementById(id);
                if (!el) return;
                el.innerHTML = `<i class="fas ${value ? 'fa-check-circle' : 'fa-circle'}"></i> ${label}: <strong>&nbsp;${value ? value : 'a escolher'}</strong>`;
                el.classList.toggle('filled', !!value);
            });
            updateStepper();
        }

        // ==================== CHAT BOT ====================
        const chatBtn = document.getElementById('chat-btn');
        const chatModal = document.getElementById('chat-modal');
        const closeChatBtn = document.getElementById('close-chat-btn');
        const chatInput = document.getElementById('chat-input');
        const chatSendBtn = document.getElementById('chat-send-btn');
        const chatMessages = document.getElementById('chat-messages');

        const specialtySuggestions = {
            'coração': 'Cardiologia',
            'dor no peito': 'Cardiologia',
            'pressão alta': 'Cardiologia',
            'batimento cardíaco': 'Cardiologia',
            'colesterol': 'Cardiologia',
            'criança': 'Pediatria',
            'bebé': 'Pediatria',
            'bebê': 'Pediatria',
            'infantil': 'Pediatria',
            'vacina': 'Pediatria',
            'dor nas costas': 'Ortopedia',
            'fratura': 'Ortopedia',
            'osso': 'Ortopedia',
            'articulação': 'Ortopedia',
            'joelho': 'Ortopedia',
            'ombro': 'Ortopedia',
            'gravidez': 'Ginecologia',
            'menstruação': 'Ginecologia',
            'ginecológica': 'Ginecologia',
            'obstetrícia': 'Ginecologia',
            'cabeça': 'Neurologia',
            'dor de cabeça': 'Neurologia',
            'enxaqueca': 'Neurologia',
            'tontura': 'Neurologia',
            'convulsão': 'Neurologia',
            'memória': 'Neurologia',
            'cirurgia': 'Cirurgia Geral',
            'operar': 'Cirurgia Geral',
            'ferida': 'Cirurgia Geral',
            'febre': 'Medicina Geral',
            'gripe': 'Medicina Geral',
            'tosse': 'Medicina Geral',
            'dor de garganta': 'Medicina Geral',
            'check-up': 'Medicina Geral'
        };

        const welcomeMessages = [
            "Olá! Sou o assistente virtual do Hospital Matlhovele. Como posso ajudar você hoje?",
            "Bem-vindo! Descreva seus sintomas ou o motivo da consulta para eu sugerir a especialidade adequada.",
            "Oi! Estou aqui para ajudar. Conte-me sobre o que você está sentindo para indicar o melhor especialista."
        ];

        if (chatBtn) {
            chatBtn.addEventListener('click', () => {
                chatModal.classList.add('show');
                chatInput.focus();
                if (chatMessages.children.length <= 1) {
                    const welcomeMsg = welcomeMessages[Math.floor(Math.random() * welcomeMessages.length)];
                    addBotMessage(welcomeMsg);
                }
            });
        }

        if (closeChatBtn) {
            closeChatBtn.addEventListener('click', () => {
                chatModal.classList.remove('show');
                chatInput.value = '';
            });
        }

        if (chatSendBtn) {
            chatSendBtn.addEventListener('click', sendChatMessage);
        }

        if (chatInput) {
            chatInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') {
                    sendChatMessage();
                }
            });
        }

        function addBotMessage(message) {
            const botMsg = document.createElement('div');
            botMsg.className = 'chat-message bot';
            botMsg.innerHTML = message;
            chatMessages.appendChild(botMsg);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function addUserMessage(message) {
            const userMsg = document.createElement('div');
            userMsg.className = 'chat-message user';
            userMsg.textContent = message;
            chatMessages.appendChild(userMsg);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        }

        function sendChatMessage() {
            const message = chatInput.value.trim();
            if (!message) return;

            addUserMessage(message);
            chatInput.value = '';

            setTimeout(() => {
                let response = '';
                let foundSpecialty = null;

                for (let keyword in specialtySuggestions) {
                    if (message.toLowerCase().includes(keyword)) {
                        foundSpecialty = specialtySuggestions[keyword];
                        break;
                    }
                }

                if (foundSpecialty) {
                    response = `Com base na sua descrição, recomendo a especialidade de <strong>${foundSpecialty}</strong>. `;
                    response += `Clique em "${foundSpecialty}" na lista de especialidades acima para agendar sua consulta. `;
                    response += `Posso ajudar com mais alguma coisa?`;
                } else if (message.toLowerCase().includes('obrigado') || message.toLowerCase().includes('obrigada')) {
                    response = `De nada! Estou aqui para ajudar. Se precisar de mais alguma coisa, é só falar. 😊`;
                } else if (message.toLowerCase().includes('horário') || message.toLowerCase().includes('funcionamento')) {
                    response = `O Hospital Matlhovele funciona:<br>• Segunda a Sexta: 7h30 - 16h30<br>• Sábado: 8h00 - 12h00<br>• Emergências: 24 horas`;
                } else if (message.toLowerCase().includes('telefone') || message.toLowerCase().includes('contacto')) {
                    response = `📞 Telefone: +258 84 123 4567<br>📍 Endereço: Av. 25 de Setembro, Maputo<br>📧 Email: info@mathlovele.gov.mz`;
                } else {
                    response = `Desculpe, não entendi completamente. Pode descrever melhor seus sintomas? `;
                    response += `Por exemplo: "estou com dor de cabeça frequente" ou "minha filha está com febre".`;
                }

                addBotMessage(response);
                chatInput.focus();
            }, 1000);
        }

        // Fechar chat ao clicar fora
        document.addEventListener('click', (e) => {
            if (chatModal.classList.contains('show') &&
                !chatModal.contains(e.target) &&
                e.target !== chatBtn) {
                chatModal.classList.remove('show');
            }
        });

        // ==================== AJAX FUNCTIONS ====================

        // Carregar médicos
        async function loadDoctors(query = '', specialty = '', limit = 10, offset = 0) {
            try {
                console.log('=== loadDoctors chamado ===');
                console.log('specialty:', specialty);

                // Mostra loading
                const container = document.getElementById('doctor-container');
                container.innerHTML = Array(3).fill(`
            <div class="skeleton-card">
                <div class="skeleton-circle"></div>
                <div style="flex:1">
                    <div class="skeleton-line" style="width:60%"></div>
                    <div class="skeleton-line" style="width:85%"></div>
                </div>
            </div>
        `).join('');

                // Obter token CSRF (sempre o mais recente)
                const csrfToken = getCsrfToken();
                const csrfName = getCsrfName();

                console.log('CSRF Token:', csrfToken);
                console.log('CSRF Name:', csrfName);

                // Se não tiver token, recarrega a página
                if (!csrfToken) {
                    console.warn('Token CSRF não encontrado. Recarregando página...');
                    showNotification('Recarregando página para segurança...', 'info');
                    setTimeout(() => location.reload(), 1500);
                    return;
                }

                // Construir dados com CSRF
                const formData = new FormData();
                formData.append('q', query);
                formData.append('specialty', specialty);
                formData.append('limit', limit);
                formData.append('offset', offset);
                formData.append(csrfName, csrfToken);

                const url = SITE_URL + '/agenda/get_doctors';
                console.log('URL chamada:', url);

                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                console.log('Status da resposta:', response.status);

                if (!response.ok) {
                    const text = await response.text();
                    console.error('Erro resposta:', text);

                    if (response.status === 403) {
                        // Se for 403, tenta recarregar a página uma vez
                        if (!sessionStorage.getItem('csrf_reload_attempt')) {
                            sessionStorage.setItem('csrf_reload_attempt', '1');
                            showNotification('Token expirado. Recarregando página...', 'info');
                            setTimeout(() => location.reload(), 1500);
                            return;
                        } else {
                            sessionStorage.removeItem('csrf_reload_attempt');
                            showNotification('Erro de segurança. Tente novamente.', 'error');
                            return;
                        }
                    }

                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const text = await response.text();
                console.log('Resposta bruta:', text.substring(0, 200));

                let result;
                try {
                    result = JSON.parse(text);
                } catch (parseErr) {
                    console.error('Erro ao fazer parse:', parseErr);
                    throw new Error('Resposta inválida do servidor');
                }

                console.log('Resultado:', result);

                if (result.status === 'success') {
                    processDoctors(result, specialty, offset, limit);
                } else {
                    showNotification(result.message || 'Erro ao carregar médicos.', 'error');
                }
            } catch (err) {
                console.error('Erro completo:', err);
                showNotification('Erro de conexão ao carregar médicos: ' + err.message, 'error');
            }
        }

        // Renderizar lista de médicos
        function renderDoctors(specialty = null, total = 0) {
            const container = document.getElementById('doctor-container');
            const header = document.getElementById('doctor-header');
            const searchInput = document.getElementById('doctor-search');

            if (specialty && doctors.length > 0) {
                header.classList.remove('hidden');
                header.textContent = `Médicos em ${specialty} (${total})`;
            } else {
                header.classList.add('hidden');
            }

            if (searchInput) searchInput.disabled = doctors.length === 0;

            container.innerHTML = doctors.length > 0 ?
                doctors.map(doctor => `
                    <div class="doctor-item flex items-center p-4 border rounded-lg hover:bg-blue-50 cursor-pointer" 
                         data-id="${doctor.id}" data-name="${doctor.name}" tabindex="0" role="button" aria-pressed="false">
                        <img src="${doctor.image || 'https://picsum.photos/100?random=' + doctor.id}" 
                             alt="${doctor.name}" class="w-12 h-12 rounded-full mr-4">
                        <div>
                            <p class="font-medium">${doctor.name}</p>
                            <p class="text-sm text-gray-600">${doctor.specialty || specialty} - Experiência: ${doctor.experience || 'N/A'}</p>
                            ${doctor.rating ? `<p class="rating"><i class="fas fa-star"></i> ${doctor.rating} de 5</p>` : ''}
                        </div>
                    </div>
                `).join('') :
                `<div class="empty-state col-span-full"><i class="fas fa-user-md"></i>Nenhum médico disponível para esta especialidade.</div>`;

            document.querySelectorAll('.doctor-item').forEach(item => {
                item.addEventListener('click', function() {
                    document.querySelectorAll('.doctor-item').forEach(el => {
                        el.classList.remove('selected');
                        el.setAttribute('aria-pressed', 'false');
                    });
                    this.classList.add('selected');
                    this.setAttribute('aria-pressed', 'true');
                    selectedDoctor = this.dataset.name;
                    selectedDoctorId = this.dataset.id;
                    selectedDate = null;
                    selectedTime = null;
                    updateAvailableDays();
                    updateSummary();
                    showNotification(`Médico selecionado: ${selectedDoctor}`, 'info');
                });
            });
        }

        // Botão "Carregar Mais"
        function renderLoadMoreButton(remaining) {
            const container = document.getElementById('doctor-container');
            if (container.querySelector('.load-more-btn')) return;
            const loadMoreBtn = document.createElement('button');
            loadMoreBtn.innerHTML = `<i class="fas fa-plus mr-2"></i>Carregar Mais Médicos (${remaining} restantes)`;
            loadMoreBtn.className = 'load-more-btn w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition mt-4';
            loadMoreBtn.addEventListener('click', () => {
                loadDoctors('', selectedSpecialty, 10, doctors.length);
            });
            container.appendChild(loadMoreBtn);
        }

        // Selecionar especialidade
        function selectSpecialty(item) {
            document.querySelectorAll('.specialty-item').forEach(el => {
                el.classList.remove('selected');
                el.setAttribute('aria-pressed', 'false');
            });
            item.classList.add('selected');
            item.setAttribute('aria-pressed', 'true');
            selectedSpecialty = item.dataset.specialty;
            selectedDoctor = null;
            selectedDoctorId = null;
            selectedDate = null;
            selectedTime = null;

            const container = document.getElementById('doctor-container');
            const header = document.getElementById('doctor-header');
            const searchInput = document.getElementById('doctor-search');

            container.innerHTML = Array(4).fill(`
                <div class="skeleton-card">
                    <div class="skeleton-circle"></div>
                    <div style="flex:1">
                        <div class="skeleton-line" style="width:60%"></div>
                        <div class="skeleton-line" style="width:85%"></div>
                    </div>
                </div>
            `).join('');
            header.classList.add('hidden');
            if (searchInput) {
                searchInput.value = '';
                searchInput.disabled = true;
            }

            updateSummary();
            loadDoctors('', selectedSpecialty, 6, 0);
        }

        document.querySelectorAll('.specialty-item').forEach(item => {
            item.addEventListener('click', function() {
                selectSpecialty(this);
            });
            item.addEventListener('keypress', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    selectSpecialty(this);
                }
            });
        });

        // Pesquisa de médicos
        document.getElementById('doctor-search')?.addEventListener('input', function() {
            const term = this.value.trim().toLowerCase();
            document.querySelectorAll('#doctor-container .doctor-item').forEach(item => {
                const name = (item.dataset.name || '').toLowerCase();
                item.style.display = name.includes(term) ? '' : 'none';
            });
        });

        // Carregar horários disponíveis
        async function loadAvailableSlots(dateStr, medicoId) {
            try {
                // Obter token CSRF
                const csrfToken = getCsrfToken();
                const csrfName = getCsrfName();

                console.log('loadAvailableSlots - CSRF Token:', csrfToken);
                console.log('loadAvailableSlots - CSRF Name:', csrfName);

                // Se não tiver token, recarrega a página
                if (!csrfToken) {
                    console.warn('Token CSRF não encontrado em loadAvailableSlots');
                    showNotification('Erro de segurança. Recarregando página...', 'error');
                    setTimeout(() => location.reload(), 1500);
                    return;
                }

                const formData = new FormData();
                formData.append('data', dateStr);
                formData.append('medico_id', medicoId);
                formData.append(csrfName, csrfToken); // Adiciona CSRF

                const response = await fetch(SITE_URL + '/agenda/get_available_slots', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                console.log('loadAvailableSlots - Status:', response.status);

                if (!response.ok) {
                    const text = await response.text();
                    console.error('loadAvailableSlots - Erro:', text);

                    if (response.status === 403) {
                        showNotification('Token expirado. Recarregando página...', 'error');
                        setTimeout(() => location.reload(), 1500);
                        return;
                    }

                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                console.log('loadAvailableSlots - Resultado:', result);

                if (result.status === 'success') {
                    const slots = result.data || [];
                    if (!availableSlots[dateStr]) availableSlots[dateStr] = {};
                    availableSlots[dateStr][selectedDoctor || 'default'] = slots;

                    // Atualiza o token CSRF se retornado
                    if (result.csrf_token) {
                        updateCsrfToken(result.csrf_token, result.csrf_name);
                    }
                } else {
                    showNotification(result.message || 'Erro ao carregar horários.', 'error');
                }
            } catch (err) {
                console.error('Erro AJAX slots:', err);
                showNotification('Erro ao carregar horários: ' + err.message, 'error');
            }
        }

        // Salvar agendamento 
        async function saveAppointment(formData) {
            try {
                // Verificar se formData é um objeto FormData
                if (!(formData instanceof FormData)) {
                    console.error('formData não é um FormData:', formData);
                    // Criar um novo FormData a partir do objeto
                    const newFormData = new FormData();
                    for (const key in formData) {
                        if (formData.hasOwnProperty(key)) {
                            newFormData.append(key, formData[key]);
                        }
                    }
                    formData = newFormData;
                }

                // Adicionar CSRF ao FormData
                const csrfToken = getCsrfToken();
                const csrfName = getCsrfName();

                console.log('saveAppointment - CSRF Token:', csrfToken);
                console.log('saveAppointment - FormData:', formData);

                if (!csrfToken) {
                    showNotification('Erro de segurança. Recarregue a página.', 'error');
                    setTimeout(() => location.reload(), 1500);
                    return false;
                }

                formData.append(csrfName, csrfToken);

                const response = await fetch(SITE_URL + '/agenda/save_appointment', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                console.log('saveAppointment - Status:', response.status);

                if (!response.ok) {
                    const text = await response.text();
                    console.error('saveAppointment - Erro:', text);

                    if (response.status === 403) {
                        showNotification('Token expirado. Recarregando página...', 'error');
                        setTimeout(() => location.reload(), 1500);
                        return false;
                    }
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                console.log('saveAppointment - Resultado:', result);

                // Atualiza o token CSRF se retornado
                if (result.csrf_token) {
                    updateCsrfToken(result.csrf_token, result.csrf_name);
                }

                if (result.status === 'success') {
                    showNotification(result.message, 'success');
                    return true;
                } else {
                    showNotification(result.message || 'Erro ao salvar agendamento.', 'error');
                    return false;
                }
            } catch (err) {
                console.error('Erro ao salvar:', err);
                showNotification('Erro de conexão. Tente novamente.', 'error');
                return false;
            }
        }

        // Atualizar dias disponíveis
        async function updateAvailableDays() {
            if (!selectedDoctorId) return;

            const today = new Date();
            today.setHours(0, 0, 0, 0);
            const visibleStart = calendar.view.activeStart;
            const visibleEnd = calendar.view.activeEnd;

            document.querySelectorAll('.fc-daygrid-day').forEach(el => {
                el.classList.remove('available', 'selected', 'unavailable');
            });

            const loadPromises = [];
            for (let date = new Date(Math.max(visibleStart, today)); date < visibleEnd && ((date - today) / (1000 * 60 * 60 * 24)) < 14; date.setDate(date.getDate() + 1)) {
                const dateStr = date.toISOString().split('T')[0];
                if (date >= today) {
                    loadPromises.push(loadAvailableSlots(dateStr, selectedDoctorId));
                }
            }

            await Promise.all(loadPromises);

            Object.keys(availableSlots).forEach(dateStr => {
                const dayEl = document.querySelector(`.fc-daygrid-day[data-date="${dateStr}"]`);
                if (dayEl) {
                    const slots = availableSlots[dateStr][selectedDoctor || 'default'] || [];
                    if (slots.length > 0) {
                        dayEl.classList.add('available');
                    } else {
                        dayEl.classList.add('unavailable');
                    }
                }
            });
        }

        // Mostrar horários
        async function showTimeSlots(dateStr, doctorName) {
            const medicoId = doctors.find(d => d.name === doctorName)?.id;
            if (!medicoId) {
                showNotification('Médico inválido.', 'error');
                return;
            }

            // Obter token CSRF antes de carregar
            const csrfToken = getCsrfToken();
            const csrfName = getCsrfName();

            console.log('showTimeSlots - CSRF Token:', csrfToken);

            // Se não tiver token, recarrega
            if (!csrfToken) {
                showNotification('Erro de segurança. Recarregando página...', 'error');
                setTimeout(() => location.reload(), 1500);
                return;
            }

            await loadAvailableSlots(dateStr, medicoId);

            const slots = availableSlots[dateStr] ? availableSlots[dateStr][doctorName] || [] : [];
            const modal = document.getElementById('time-slot-modal');
            const slotList = document.getElementById('time-slot-list');
            const subtitle = document.getElementById('time-slot-subtitle');

            if (subtitle) subtitle.textContent = `Horários disponíveis em ${dateStr} com ${doctorName}`;

            slotList.innerHTML = slots.length > 0 ?
                slots.map(slot => `<button class="slot-button available" data-time="${slot}"><i class="fas fa-clock"></i> ${slot} (Disponível)</button>`).join('') :
                '<div class="empty-state"><i class="fas fa-calendar-times"></i>Nenhum horário disponível para esta data.</div>';

            modal.classList.add('show');
            updateSummary();

            document.querySelectorAll('.slot-button.available').forEach(button => {
                button.addEventListener('click', function() {
                    selectedTime = this.dataset.time;
                    updateSummary();
                    showNotification(`Horário selecionado: ${selectedTime}`, 'success');
                    modal.classList.remove('show');
                });
            });
        }

        // ==================== EVENT LISTENERS ====================

        // Confirmar agendamento
        document.getElementById('review-confirm-btn').addEventListener('click', async function() {
            console.log('=== Confirmando agendamento ===');

            // Coletar dados do formulário
            const formData = new FormData(); // CRIAR UM NOVO FORMDATA

            const nome = document.getElementById('name')?.value?.trim() || '';
            const telefone = document.getElementById('phone')?.value?.trim() || '';
            const bi = document.getElementById('bi')?.value?.trim() || '';
            const motivo = document.getElementById('motivo')?.value?.trim() || '';

            // Validar campos obrigatórios
            if (!nome || !telefone || !bi) {
                showNotification('Por favor, preencha todos os campos do formulário.', 'error');
                return;
            }

            if (!selectedSpecialty || !selectedDoctor || !selectedDate || !selectedTime) {
                showNotification('Selecione especialidade, médico, data e horário antes de continuar.', 'error');
                return;
            }

            // Adicionar dados ao FormData
            formData.append('especialidade', selectedSpecialty);
            formData.append('medico', selectedDoctorId);
            formData.append('data_consulta', selectedDate);
            formData.append('horario', selectedTime);
            formData.append('nome', nome);
            formData.append('telefone', telefone);
            formData.append('bi', bi);
            formData.append('motivo', motivo);

            console.log('Dados do agendamento:', {
                especialidade: selectedSpecialty,
                medico: selectedDoctorId,
                data_consulta: selectedDate,
                horario: selectedTime,
                nome: nome,
                telefone: telefone,
                bi: bi,
                motivo: motivo
            });

            // Mostrar loading no botão
            const confirmBtn = document.getElementById('review-confirm-btn');
            const originalText = confirmBtn.innerHTML;
            confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
            confirmBtn.disabled = true;

            try {
                const success = await saveAppointment(formData);

                if (success) {
                    const confirmationMessage = `
                Agendamento confirmado!<br>
                <strong>Nome:</strong> ${nome}<br>
                <strong>Especialidade:</strong> ${selectedSpecialty}<br>
                <strong>Médico:</strong> ${selectedDoctor}<br>
                <strong>Data:</strong> ${selectedDate}<br>
                <strong>Horário:</strong> ${selectedTime}
            `;
                    document.getElementById('confirmation-message').innerHTML = confirmationMessage;
                    document.getElementById('confirmation-modal').classList.add('show');
                    document.getElementById('review-modal').classList.remove('show');

                    // Reset
                    selectedSpecialty = null;
                    selectedDoctor = null;
                    selectedDoctorId = null;
                    selectedDate = null;
                    selectedTime = null;

                    document.querySelectorAll('.specialty-item').forEach(el => {
                        el.classList.remove('selected');
                        el.setAttribute('aria-pressed', 'false');
                    });

                    document.getElementById('doctor-container').innerHTML = '<div class="empty-state col-span-full"><i class="fas fa-user-md"></i>Selecione uma especialidade para ver os médicos disponíveis.</div>';
                    document.getElementById('doctor-header').classList.add('hidden');

                    const searchInput = document.getElementById('doctor-search');
                    if (searchInput) {
                        searchInput.value = '';
                        searchInput.disabled = true;
                    }

                    updateSummary();
                }
            } catch (error) {
                console.error('Erro ao confirmar:', error);
                showNotification('Erro ao confirmar agendamento. Tente novamente.', 'error');
            } finally {
                // Restaurar botão
                confirmBtn.innerHTML = originalText;
                confirmBtn.disabled = false;
            }
        });

        // Carregar agendamentos do paciente
        async function loadPatientAppointments() {
            try {
                const csrfToken = getCsrfToken();
                const csrfName = getCsrfName();

                const formData = new FormData();
                formData.append(csrfName, csrfToken);

                const response = await fetch(SITE_URL + '/agenda/get_patient_appointments', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                if (result.status === 'success') {
                    renderAppointments(result.data);
                } else {
                    showNotification(result.message, 'error');
                }
            } catch (err) {
                showNotification('Erro ao carregar agendamentos.', 'error');
                console.error('Erro AJAX:', err);
            }
        }

        // Renderizar agendamentos
        function renderAppointments(appointments) {
            const container = document.getElementById('appointments-list');
            if (appointments.length === 0) {
                container.innerHTML = '<p class="text-gray-500">Nenhum agendamento encontrado.</p>';
                return;
            }

            container.innerHTML = appointments.map(appointment => `
                <div class="appointment-item">
                    <h4 class="font-medium">${appointment.especialidade || 'N/A'} - ${appointment.medico || 'N/A'}</h4>
                    <p class="text-sm text-gray-600">Data: ${appointment.date} às ${appointment.time}</p>
                    <p class="text-sm text-gray-600">Status: <span class="font-semibold ${appointment.status === 'Pendente' ? 'text-yellow-600' : appointment.status === 'Confirmado' ? 'text-green-600' : 'text-red-600'}">${appointment.status}</span></p>
                    <p class="text-sm text-gray-600">Motivo: ${appointment.motivo || 'N/A'}</p>
                </div>
            `).join('');
        }

        // Evento para Meus Agendamentos
        document.getElementById('meus-agendamentos-btn').addEventListener('click', () => {
            document.getElementById('appointments-section').classList.toggle('hidden');
            if (!document.getElementById('appointments-section').classList.contains('hidden')) {
                loadPatientAppointments();
            }
        });

        // Logout
        document.getElementById('logout-btn').addEventListener('click', () => {
            window.location.href = SITE_URL + '/auth/logout';
        });

        // ==================== DOMContentLoaded ====================
        document.addEventListener('DOMContentLoaded', function() {
            // Sidebar Handlers
            const mobileMenuBtn = document.getElementById('mobile-menu-btn');
            const sidebarMenu = document.getElementById('sidebar-menu');
            const closeSidebarBtn = document.getElementById('close-sidebar-btn');
            const toggleSidebarBtn = document.getElementById('toggle-sidebar-btn');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            const pageWrapper = document.querySelector('.page-wrapper');

            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', () => {
                    sidebarMenu.classList.add('show');
                    sidebarOverlay.classList.add('show');
                    pageWrapper.classList.add('expanded');
                });
            }

            if (closeSidebarBtn) {
                closeSidebarBtn.addEventListener('click', () => {
                    sidebarMenu.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                    pageWrapper.classList.remove('expanded');
                });
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', () => {
                    sidebarMenu.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                    pageWrapper.classList.remove('expanded');
                });
            }

            if (toggleSidebarBtn) {
                toggleSidebarBtn.addEventListener('click', () => {
                    sidebarMenu.classList.toggle('expanded');
                    pageWrapper.classList.toggle('expanded');
                });
            }

            // FullCalendar
            const calendarEl = document.getElementById('calendar');
            const today = new Date();
            const tomorrow = new Date(today);
            tomorrow.setDate(today.getDate() + 1);
            const tomorrowStr = tomorrow.toISOString().split('T')[0];

            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'pt',
                initialDate: tomorrowStr,
                validRange: {
                    start: tomorrowStr
                },
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                events: [],
                dateClick: (info) => {
                    if (!selectedDoctorId) {
                        showNotification('Selecione um médico antes de escolher a data.', 'error');
                        return;
                    }
                    const clickedDate = new Date(info.dateStr);
                    if (clickedDate < today) {
                        showNotification('Não é possível selecionar datas passadas.', 'error');
                        return;
                    }
                    document.querySelectorAll('.fc-daygrid-day').forEach(el => el.classList.remove('selected'));
                    info.dayEl.classList.add('selected');
                    selectedDate = info.dateStr;
                    showTimeSlots(selectedDate, selectedDoctor);
                },
                datesSet: () => {
                    updateAvailableDays();
                },
                selectable: true,
                select: (info) => {
                    if (!selectedDoctorId) {
                        showNotification('Selecione um médico antes de escolher a data.', 'error');
                        return;
                    }
                    const selected = new Date(info.startStr);
                    if (selected < today) {
                        showNotification('Não é possível selecionar datas passadas.', 'error');
                        return;
                    }
                    selectedDate = info.startStr;
                    document.querySelectorAll('.fc-daygrid-day').forEach(el => el.classList.remove('selected'));
                    const dayEl = document.querySelector(`.fc-daygrid-day[data-date="${selectedDate}"]`);
                    if (dayEl) dayEl.classList.add('selected');
                    showTimeSlots(selectedDate, selectedDoctor);
                }
            });
            calendar.render();

            // Modal handlers
            document.getElementById('modal-cancel-btn').addEventListener('click', () => {
                document.getElementById('time-slot-modal').classList.remove('show');
            });
            document.getElementById('time-slot-close-x')?.addEventListener('click', () => {
                document.getElementById('time-slot-modal').classList.remove('show');
            });

            document.getElementById('review-cancel-btn').addEventListener('click', () => {
                document.getElementById('review-modal').classList.remove('show');
            });
            document.getElementById('review-close-x')?.addEventListener('click', () => {
                document.getElementById('review-modal').classList.remove('show');
            });

            document.getElementById('confirmation-close-btn').addEventListener('click', () => {
                document.getElementById('confirmation-modal').classList.remove('show');
            });

            // Botão principal "Confirmar Agendamento"
            document.getElementById('confirm-btn').addEventListener('click', function() {
                console.log('=== Botão Confirmar Agendamento clicado ===');

                const nameEl = document.getElementById('name');
                const phoneEl = document.getElementById('phone');
                const biEl = document.getElementById('bi');

                const nome = nameEl?.value?.trim() || '';
                const telefone = phoneEl?.value?.trim() || '';
                const bi = biEl?.value?.trim() || '';

                // Limpar erros anteriores
                [nameEl, phoneEl, biEl].forEach(el => el?.classList.remove('field-error'));

                // Validar campos
                let hasError = false;
                if (!nome) {
                    if (nameEl) nameEl.classList.add('field-error');
                    hasError = true;
                }
                if (!telefone) {
                    if (phoneEl) phoneEl.classList.add('field-error');
                    hasError = true;
                }
                if (!bi) {
                    if (biEl) biEl.classList.add('field-error');
                    hasError = true;
                }

                if (hasError) {
                    showNotification('Por favor, preencha todos os campos do formulário.', 'error');
                    return;
                }

                if (!selectedSpecialty || !selectedDoctor || !selectedDate || !selectedTime) {
                    showNotification('Selecione especialidade, médico, data e horário antes de continuar.', 'error');
                    return;
                }

                // Preencher os dados na revisão
                document.getElementById('review-specialty').textContent = selectedSpecialty;
                document.getElementById('review-doctor').textContent = selectedDoctor;
                document.getElementById('review-date').textContent = selectedDate;
                document.getElementById('review-time').textContent = selectedTime;
                document.getElementById('review-name').textContent = nome;
                document.getElementById('review-phone').textContent = telefone;
                document.getElementById('review-bi').textContent = bi;

                // Mostrar modal de revisão
                document.getElementById('review-modal').classList.add('show');
            });
        });

        // Função para obter o token CSRF
        function getCsrfToken() {
            // 1. Tentar pegar do meta tag
            const metaToken = document.querySelector('meta[name="csrf-token"]');
            if (metaToken) {
                const token = metaToken.getAttribute('content');
                if (token && token.length > 0) {
                    console.log('CSRF Token obtido do meta tag:', token);
                    return token;
                }
            }

            // 2. Tentar pegar do cookie
            const cookies = document.cookie.split(';');
            for (let cookie of cookies) {
                const [name, value] = cookie.trim().split('=');
                if (name === 'csrf_cookie_name') {
                    console.log('CSRF Token obtido do cookie:', value);
                    return value;
                }
            }

            // 3. Tentar pegar do input hidden (se existir)
            const inputToken = document.querySelector('input[name="csrf_test_name"]');
            if (inputToken) {
                const token = inputToken.value;
                if (token && token.length > 0) {
                    console.log('CSRF Token obtido do input:', token);
                    return token;
                }
            }

            console.warn('CSRF Token não encontrado!');
            return '';
        }


        // Função para obter o nome do campo CSRF
        function getCsrfName() {
            // Verificar se existe no input
            const input = document.querySelector('input[name="csrf_test_name"]');
            if (input) {
                return input.name;
            }
            // Verificar se existe no meta
            const meta = document.querySelector('meta[name="csrf-name"]');
            if (meta) {
                return meta.getAttribute('content');
            }
            // Padrão do CI4
            return 'csrf_test_name';
        }

        // Função para atualizar o token CSRF
        function updateCsrfToken(newToken, newName) {
            if (newToken) {
                // Atualiza o meta tag
                const metaToken = document.querySelector('meta[name="csrf-token"]');
                if (metaToken) {
                    metaToken.setAttribute('content', newToken);
                    console.log('CSRF Token atualizado no meta tag:', newToken);
                }

                // Atualiza o cookie manualmente (se necessário)
                document.cookie = `csrf_cookie_name=${newToken}; path=/; SameSite=Lax`;
            }

            if (newName) {
                const metaName = document.querySelector('meta[name="csrf-name"]');
                if (metaName) {
                    metaName.setAttribute('content', newName);
                }
            }
        }

        // Função auxiliar para processar os médicos - COM ATUALIZAÇÃO DE CSRF
        function processDoctors(result, specialty, offset, limit) {
            // Atualiza o token CSRF se retornado
            if (result.csrf_token) {
                updateCsrfToken(result.csrf_token, result.csrf_name);
                console.log('Token CSRF atualizado com sucesso!');
            }

            if (offset === 0) {
                doctors = result.data || [];
            } else {
                doctors = doctors.concat(result.data || []);
            }
            renderDoctors(specialty, result.total || 0);

            if ((result.data || []).length === 0 && offset === 0) {
                showNotification('Nenhum médico encontrado para esta especialidade.', 'info');
            }
            if ((result.total || 0) > (offset + limit)) {
                renderLoadMoreButton(result.total - (offset + limit));
            }
        }
    </script>
</body>

</html>