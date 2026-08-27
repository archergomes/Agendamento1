<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendamento de Consultas - Centro de Saúde Da Matola II</title>
    <meta name="description" content="Sistema inteligente de agendamento de consultas para o Centro de Saúde Da Matola II">
    <meta name="csrf-token" content="<?= csrf_hash(); ?>">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>

    <script>
        var BASE_URL = '<?= rtrim(base_url(), '/'); ?>';
        var SITE_URL = '<?= rtrim(site_url(), '/'); ?>';
        var AJAX_URL = SITE_URL;
    </script>

    <style>
        /* ---------- Paleta partilhada com as views de Admin / Médico / Secretário ---------- */
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
            overflow-x: hidden;
            background-color: var(--paper);
            color: var(--ink-900);
        }

        h1,
        h2,
        h3,
        .display-font {
            font-family: 'Outfit', 'Roboto', sans-serif;
        }

        /* ---------- Botões partilhados (mesmos nomes de classe das outras 3 views) ---------- */
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

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none !important;
        }

        .fc-button {
            background-color: var(--brand-500) !important;
            border-color: var(--brand-500) !important;
            color: white !important;
        }

        .fc-button:hover {
            background-color: var(--brand-600) !important;
        }

        .specialty-item.selected,
        .doctor-item.selected {
            background-color: var(--brand-500);
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
            background-color: var(--brand-500) !important;
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
            background-color: rgba(15, 23, 42, 0.55);
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
            border-radius: 0.85rem;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.35);
            max-height: 85vh;
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

        /* ---------- Sidebar (mesma estrutura visual das outras views) ---------- */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 80px;
            background: linear-gradient(180deg, #0f2f66 0%, #123a80 100%);
            box-shadow: 2px 0 12px rgba(0, 0, 0, 0.15);
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

        .slot-button {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 0.4rem;
            text-align: center;
            cursor: pointer;
        }

        .slot-button.available:hover {
            background-color: #eff6ff;
            border-color: var(--brand-500);
        }

        .slot-button.booked {
            background-color: var(--rose-500);
            color: white;
            cursor: not-allowed;
        }

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

        #notification.show {
            display: block;
        }

        #time-slot-list {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
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
            background: var(--brand-700);
            color: white;
            padding: 0.75rem 1rem;
            z-index: 2000;
            border-radius: 0 0 0.375rem 0;
        }

        .skip-link:focus {
            left: 0;
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
            color: var(--ink-500);
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
            background: var(--brand-600);
            border-color: var(--brand-600);
            color: white;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15);
        }

        .booking-step.active .label {
            color: var(--brand-700);
            font-weight: 600;
        }

        .booking-step.complete .circle {
            background: var(--teal-500);
            border-color: var(--teal-500);
            color: white;
        }

        .booking-step.complete .connector {
            background: var(--teal-500);
        }

        .booking-step.complete .label {
            color: var(--teal-600);
        }

        #selection-summary {
            position: sticky;
            top: 0.5rem;
            z-index: 40;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            background: #eff6ff;
            border: 1px solid var(--brand-100);
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            margin-bottom: 1.75rem;
        }

        .summary-chip {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            background: white;
            border: 1px solid var(--brand-100);
            border-radius: 9999px;
            padding: 0.35rem 0.85rem;
            font-size: 0.8rem;
            color: var(--ink-700);
        }

        .summary-chip i {
            color: #93c5fd;
            font-size: 0.75rem;
        }

        .summary-chip.filled {
            border-color: #93c5fd;
            background: var(--brand-100);
            color: var(--brand-700);
            font-weight: 500;
        }

        .summary-chip.filled i {
            color: var(--brand-600);
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
            color: var(--brand-600);
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
            border-color: var(--brand-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            outline: none;
        }

        .doctor-item {
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .doctor-item:hover {
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
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
            color: var(--ink-500);
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
            color: var(--ink-700);
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
            background: var(--brand-500);
        }

        .legend-dot.selected {
            background: #e0f2fe;
            border: 1px solid var(--brand-500);
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
            color: var(--ink-500);
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
            background: var(--brand-100);
            color: var(--brand-600);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
        }

        .modal-icon-header.success {
            background: #d1fae5;
            color: var(--teal-600);
        }

        .review-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem 1rem;
            background: var(--paper);
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
            color: var(--ink-900);
            font-weight: 500;
            margin: 0;
        }

        .review-section-label {
            grid-column: 1 / -1;
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--brand-600);
            margin-top: 0.4rem;
            padding-top: 0.4rem;
            border-top: 1px dashed #e2e8f0;
        }

        .review-grid>.review-section-label:first-child {
            border-top: none;
            margin-top: 0;
            padding-top: 0;
        }

        .field-label {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--ink-700);
            margin-bottom: 0.4rem;
            font-weight: 500;
        }

        .field-label i {
            color: var(--brand-600);
            width: 1rem;
            text-align: center;
        }

        .field-label .required-dot {
            color: var(--rose-500);
        }

        .field-input {
            width: 100%;
            padding: 0.6rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .field-input:focus {
            border-color: var(--brand-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.12);
            outline: none;
        }

        .field-input.field-error {
            border-color: var(--rose-500);
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
        }

        .field-hint {
            font-size: 0.75rem;
            color: #9ca3af;
            margin-top: 0.25rem;
        }

        /* ---------- "Para quem é a consulta?" ---------- */
        .who-toggle {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            margin-bottom: 1.25rem;
        }

        .who-option {
            border: 1.5px solid #d1d5db;
            border-radius: 0.6rem;
            padding: 0.9rem;
            text-align: center;
            cursor: pointer;
            background: white;
            transition: all 0.15s ease;
        }

        .who-option:hover {
            border-color: var(--brand-500);
        }

        .who-option i {
            font-size: 1.3rem;
            color: var(--brand-600);
            display: block;
            margin-bottom: 0.4rem;
        }

        .who-option .title {
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--ink-900);
        }

        .who-option .sub {
            font-size: 0.7rem;
            color: var(--ink-500);
            margin-top: 0.15rem;
        }

        .who-option.selected {
            border-color: var(--brand-500);
            background: #eff6ff;
        }

        .who-option.selected i {
            color: var(--brand-700);
        }

        .form-section-title {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--brand-600);
            margin: 1.4rem 0 0.75rem;
        }

        .form-section-title:first-of-type {
            margin-top: 0;
        }

        /* ---------- Chat Bot ---------- */
        #chat-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 60px;
            height: 60px;
            background-color: var(--teal-500);
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
            background-color: var(--teal-600);
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
            background: linear-gradient(135deg, var(--brand-500), var(--brand-700));
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
            background-color: var(--paper);
            max-height: 350px;
        }

        .chat-message {
            margin-bottom: 1rem;
            padding: 0.75rem 1rem;
            border-radius: 1rem;
            max-width: 88%;
            word-wrap: break-word;
            animation: fadeIn 0.3s ease-in;
            line-height: 1.45;
            font-size: 0.87rem;
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
            background: linear-gradient(135deg, var(--brand-500), var(--brand-700));
            color: white;
            margin-left: auto;
            border-bottom-right-radius: 0.25rem;
        }

        .chat-message.bot {
            background-color: white;
            color: var(--ink-700);
            border: 1px solid #e5e7eb;
            margin-right: auto;
            border-bottom-left-radius: 0.25rem;
        }

        .chat-message.bot.urgent {
            background-color: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            font-weight: 500;
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
            border-color: var(--brand-500);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .chat-send-btn {
            background: linear-gradient(135deg, var(--teal-500), var(--teal-600));
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
            box-shadow: 0 4px 8px rgba(13, 148, 136, 0.3);
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

            .who-toggle {
                grid-template-columns: 1fr;
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
    </style>
</head>

<body>
    <a href="#main-content" class="skip-link">Saltar para o conteúdo principal</a>

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
                <a href="<?= site_url('agenda'); ?>" class="active">
                    <i class="fas fa-home"></i>
                    <span class="sidebar-text">Home</span>
                </a>
                <a href="<?= site_url('agenda/agendamentos'); ?>" id="meus-agendamentos-btn">
                    <i class="fas fa-calendar-check"></i>
                    <span class="sidebar-text">Meus Agendamentos</span>
                </a>
                <a href="<?= site_url('agenda/perfil'); ?>">
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
        <!-- Header -->
        <header class="text-white shadow-lg">
            <div class="container mx-auto px-4 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <i class="fas fa-hospital-alt text-2xl" aria-label="Ícone do Centro de Saúde Da Matola II"></i>
                    <div>
                        <h1 class="text-xl font-bold leading-tight">Centro de Saúde Da Matola II</h1>
                        <p class="text-xs text-blue-100 opacity-90">Agendamento de Consultas</p>
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
        <main class="main-content" id="main-content">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="bg-white rounded-lg shadow-md p-6 mb-8" style="border:1px solid #eef1f6;">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-2">Agendar Consulta</h2>
                    <p class="text-gray-500 mb-6">Siga os 4 passos abaixo para marcar a sua consulta no Centro de Saúde Da Matola II.</p>

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
                        <div id="doctor-header" class="hidden mb-2 text-sm font-medium" style="color:var(--brand-600);"></div>
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
                            <button id="modal-cancel-btn" class="btn btn-secondary" style="width:100%;">Cancelar</button>
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
                                <div class="review-section-label">Consulta</div>
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

                                <div class="review-section-label" id="review-patient-section-label">Paciente</div>
                                <div>
                                    <dt>Nome do paciente</dt>
                                    <dd id="review-patient-name"></dd>
                                </div>
                                <div id="review-patient-relation-wrap">
                                    <dt>Parentesco</dt>
                                    <dd id="review-patient-relation"></dd>
                                </div>

                                <div class="review-section-label" id="review-responsible-section-label">Responsável / Contacto</div>
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
                                <button id="review-confirm-btn" class="btn btn-primary flex-1">
                                    <i class="fas fa-check mr-1"></i> Confirmar
                                </button>
                                <button id="review-cancel-btn" class="btn btn-secondary flex-1">Voltar</button>
                            </div>
                        </div>
                    </div>

                    <!-- Modal para Confirmação Final -->
                    <div id="confirmation-modal">
                        <div class="modal-content">
                            <div class="modal-icon-header success"><i class="fas fa-check-circle"></i></div>
                            <h3 class="text-lg font-medium text-gray-700 mb-4">Agendamento Confirmado</h3>
                            <p id="confirmation-message" class="mb-4"></p>
                            <p class="text-xs text-gray-500 mb-4"><i class="fas fa-info-circle mr-1"></i> Chegue com 15 minutos de antecedência e leve o documento de identificação do paciente (BI ou Certidão de Nascimento).</p>
                            <button id="confirmation-close-btn" class="btn btn-primary" style="width:100%;">Fechar</button>
                        </div>
                    </div>

                    <!-- Step 4: Confirmar Agendamento -->
                    <div class="p-6 rounded-lg" style="background:#eff6ff;">
                        <h3 class="text-lg font-medium text-gray-700 mb-1">4. Confirmar agendamento</h3>
                        <p class="text-sm text-gray-500 mb-4">Estes dados são usados para identificar o paciente e para o contactarmos sobre a consulta.</p>

                        <!-- Para quem é a consulta -->
                        <div class="mb-2">
                            <label class="field-label"><i class="fas fa-users"></i> Para quem é esta consulta? <span class="required-dot">*</span></label>
                        </div>
                        <div class="who-toggle" id="who-toggle" role="radiogroup" aria-label="Para quem é a consulta">
                            <div class="who-option selected" data-who="self" tabindex="0" role="radio" aria-checked="true">
                                <i class="fas fa-user"></i>
                                <div class="title">Para mim</div>
                                <div class="sub">Eu sou o paciente</div>
                            </div>
                            <div class="who-option" data-who="other" tabindex="0" role="radio" aria-checked="false">
                                <i class="fas fa-child"></i>
                                <div class="title">Para outra pessoa</div>
                                <div class="sub">Ex: meu filho/filha, ou outro dependente</div>
                            </div>
                        </div>

                        <!-- Dados do paciente (só visível quando "Para outra pessoa") -->
                        <div id="patient-fields" class="hidden">
                            <div class="form-section-title">Dados do paciente</div>
                            <div class="mb-4">
                                <label class="field-label" for="patient-name"><i class="fas fa-child"></i> Nome completo do paciente <span class="required-dot">*</span></label>
                                <input type="text" id="patient-name" class="field-input" aria-required="true" placeholder="Nome completo de quem vai à consulta">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="field-label" for="patient-birthdate"><i class="fas fa-birthday-cake"></i> Data de nascimento</label>
                                    <input type="date" id="patient-birthdate" class="field-input">
                                </div>
                                <div>
                                    <label class="field-label" for="patient-relation"><i class="fas fa-heart"></i> Parentesco / relação <span class="required-dot">*</span></label>
                                    <select id="patient-relation" class="field-input">
                                        <option value="">Seleccione</option>
                                        <option value="Mãe">Mãe</option>
                                        <option value="Pai">Pai</option>
                                        <option value="Tutor legal">Tutor legal</option>
                                        <option value="Outro">Outro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="field-label" for="patient-doc-type"><i class="fas fa-id-badge"></i> Documento do paciente</label>
                                    <select id="patient-doc-type" class="field-input">
                                        <option value="Nenhum">Sem documento (menor sem BI)</option>
                                        <option value="BI">Bilhete de Identidade</option>
                                        <option value="Certidao">Certidão de Nascimento</option>
                                    </select>
                                </div>
                                <div id="patient-doc-number-wrap">
                                    <label class="field-label" for="patient-doc-number"><i class="fas fa-hashtag"></i> Número do documento</label>
                                    <input type="text" id="patient-doc-number" class="field-input" placeholder="Opcional se não possuir">
                                </div>
                            </div>
                        </div>

                        <div class="form-section-title" id="responsible-section-title">Os seus dados de contacto</div>
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
                            <p class="field-hint">Isto ajuda o médico a preparar-se antes da consulta.</p>
                        </div>
                        <button id="confirm-btn" class="btn btn-primary" aria-label="Confirmar agendamento">
                            <i class="fas fa-calendar-check mr-1"></i> Confirmar Agendamento
                        </button>
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
            <h3>Assistente do Centro de Saúde Da Matola II</h3>
            <button id="close-chat-btn"><i class="fas fa-times"></i></button>
        </div>
        <div class="chat-messages" id="chat-messages"></div>
        <div class="chat-input-container">
            <input type="text" id="chat-input" class="chat-input" placeholder="Descreva os sintomas ou a sua dúvida...">
            <button id="chat-send-btn" class="chat-send-btn">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>

    <script>
        // ==================== VARIÁVEIS GLOBAIS ====================
        let doctors = [];
        let availableSlots = {};
        let selectedSpecialty = null;
        let selectedDoctor = null;
        let selectedDoctorId = null;
        let selectedDate = null;
        let selectedTime = null;
        let calendar;
        let bookingFor = 'self'; // 'self' | 'other'

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

        function getCsrfName() {
            const input = document.querySelector('input[name="csrf_test_name"]');
            if (input) return input.name;
            return 'csrf_test_name';
        }

        // ==================== NOTIFICAÇÕES ====================
        function showNotification(message, type = 'info') {
            const notification = document.getElementById('notification');
            const messageEl = document.getElementById('notification-message');
            const icon = type === 'error' ? 'fa-exclamation-circle' :
                type === 'success' ? 'fa-check-circle' : 'fa-info-circle';
            messageEl.innerHTML = `<i class="fas ${icon} mr-2"></i>${message}`;
            notification.className = `show ${type}`;
            setTimeout(() => {
                notification.classList.remove('show');
            }, 5000);
        }

        document.getElementById('notification-close')?.addEventListener('click', () => {
            document.getElementById('notification').classList.remove('show');
        });

        // ==================== PARA QUEM É A CONSULTA ====================
        const whoToggle = document.getElementById('who-toggle');
        const patientFields = document.getElementById('patient-fields');
        const responsibleSectionTitle = document.getElementById('responsible-section-title');
        const patientDocType = document.getElementById('patient-doc-type');
        const patientDocNumberWrap = document.getElementById('patient-doc-number-wrap');

        function setBookingFor(who) {
            bookingFor = who;
            whoToggle.querySelectorAll('.who-option').forEach(opt => {
                const isSelected = opt.dataset.who === who;
                opt.classList.toggle('selected', isSelected);
                opt.setAttribute('aria-checked', isSelected ? 'true' : 'false');
            });

            if (who === 'other') {
                patientFields.classList.remove('hidden');
                responsibleSectionTitle.textContent = 'Dados do responsável (quem está a agendar)';
            } else {
                patientFields.classList.add('hidden');
                responsibleSectionTitle.textContent = 'Os seus dados de contacto';
            }
        }

        whoToggle.querySelectorAll('.who-option').forEach(opt => {
            opt.addEventListener('click', () => setBookingFor(opt.dataset.who));
            opt.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    setBookingFor(opt.dataset.who);
                }
            });
        });

        patientDocType?.addEventListener('change', function() {
            patientDocNumberWrap.style.display = this.value === 'Nenhum' ? 'none' : 'block';
        });

        // ==================== UPDATE SUMMARY ====================
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

        function updateStepper() {
            const steps = document.querySelectorAll('.booking-step');
            const state = [!!selectedSpecialty, !!selectedDoctorId, !!(selectedDate && selectedTime), false];
            let activeIndex = state.findIndex(done => !done);
            if (activeIndex === -1) activeIndex = 3;

            steps.forEach((stepEl, i) => {
                stepEl.classList.remove('active', 'complete');
                if (i < activeIndex) stepEl.classList.add('complete');
                else if (i === activeIndex) stepEl.classList.add('active');
            });
        }

        // ==================== CHAT BOT ====================
        // Assistente reformulado para um tom claro, directo e profissional — evita gírias,
        // emojis e frases hesitantes. Inclui deteção básica de sinais de urgência: nesses
        // casos a prioridade passa a ser encaminhar para a Urgência, não sugerir agendamento.
        const chatBtn = document.getElementById('chat-btn');
        const chatModal = document.getElementById('chat-modal');
        const closeChatBtn = document.getElementById('close-chat-btn');
        const chatInput = document.getElementById('chat-input');
        const chatSendBtn = document.getElementById('chat-send-btn');
        const chatMessages = document.getElementById('chat-messages');

        const WELCOME_MESSAGE =
            'Bem-vindo(a) ao Centro de Saúde Da Matola II. Sou o assistente virtual e posso ajudá-lo(a) a identificar a ' +
            'especialidade indicada com base nos sintomas descritos, ou esclarecer informações sobre horários e ' +
            'contactos. Esta orientação não substitui uma avaliação médica.';

        const URGENT_KEYWORDS = [
            'dor no peito', 'falta de ar', 'dificuldade em respirar', 'hemorragia', 'sangramento intenso',
            'perda de consciência', 'desmaio', 'convulsão', 'convulsao', 'não consigo respirar',
            'acidente grave', 'ferida profunda', 'envenenamento', 'overdose', 'avc', 'paralisia súbita'
        ];

        const specialtySuggestions = {
            'coração': 'Cardiologia',
            'dor no peito leve': 'Cardiologia',
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
            'dor de cabeça': 'Neurologia',
            'enxaqueca': 'Neurologia',
            'tontura': 'Neurologia',
            'memória': 'Neurologia',
            'cirurgia': 'Cirurgia Geral',
            'ferida': 'Cirurgia Geral',
            'operar': 'Cirurgia Geral',
            'febre': 'Medicina Geral',
            'gripe': 'Medicina Geral',
            'tosse': 'Medicina Geral',
            'dor de garganta': 'Medicina Geral',
            'check-up': 'Medicina Geral'
        };

        function addBotMessage(message, urgent = false) {
            const botMsg = document.createElement('div');
            botMsg.className = 'chat-message bot' + (urgent ? ' urgent' : '');
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

        if (chatBtn) {
            chatBtn.addEventListener('click', () => {
                chatModal.classList.add('show');
                chatInput.focus();
                if (chatMessages.children.length === 0) addBotMessage(WELCOME_MESSAGE);
            });
        }

        if (closeChatBtn) {
            closeChatBtn.addEventListener('click', () => {
                chatModal.classList.remove('show');
                chatInput.value = '';
            });
        }

        if (chatSendBtn) chatSendBtn.addEventListener('click', sendChatMessage);
        if (chatInput) {
            chatInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') sendChatMessage();
            });
        }

        function sendChatMessage() {
            const message = chatInput.value.trim();
            if (!message) return;

            addUserMessage(message);
            chatInput.value = '';

            setTimeout(() => {
                const lower = message.toLowerCase();

                const isUrgent = URGENT_KEYWORDS.some(k => lower.includes(k));
                if (isUrgent) {
                    addBotMessage(
                        'Os sintomas descritos podem indicar uma situação de urgência. Dirija-se imediatamente à ' +
                        'Urgência do Centro de Saúde Da Matola II ou ao serviço de urgência mais próximo. Não aguarde por ' +
                        'uma consulta agendada.',
                        true
                    );
                    chatInput.focus();
                    return;
                }

                let response;
                let foundSpecialty = null;
                for (const keyword in specialtySuggestions) {
                    if (lower.includes(keyword)) {
                        foundSpecialty = specialtySuggestions[keyword];
                        break;
                    }
                }

                if (foundSpecialty) {
                    response = `Com base na informação fornecida, a especialidade indicada é <strong>${foundSpecialty}</strong>. ` +
                        `Seleccione-a na lista de especialidades acima para prosseguir com o agendamento.`;
                } else if (lower.includes('obrigado') || lower.includes('obrigada')) {
                    response = 'Disponha. Se surgir alguma dúvida adicional durante o processo de agendamento, estou disponível para ajudar.';
                } else if (lower.includes('horário') || lower.includes('funcionamento')) {
                    response = 'Horário de funcionamento do Hospital:<br>' +
                        '• Segunda a Sexta: 7h30 – 16h30<br>' +
                        '• Sábado: 8h00 – 12h00<br>' +
                        '• Urgências: atendimento permanente, 24 horas.';
                } else if (lower.includes('telefone') || lower.includes('contacto')) {
                    response = 'Telefone: +258 84 123 4567<br>Morada: Av. 25 de Setembro, Maputo.';
                } else {
                    response = 'Não foi possível identificar a especialidade com a informação fornecida. Descreva os ' +
                        'sintomas de forma mais específica — por exemplo, localização, duração ou sintomas associados ' +
                        '— para que eu possa orientá-lo(a) correctamente.';
                }

                addBotMessage(response);
                chatInput.focus();
            }, 700);
        }

        document.addEventListener('click', (e) => {
            if (chatModal.classList.contains('show') && !chatModal.contains(e.target) && e.target !== chatBtn) {
                chatModal.classList.remove('show');
            }
        });

        // ==================== LOAD DOCTORS ====================
        async function loadDoctors(query = '', specialty = '', limit = 10, offset = 0) {
            try {
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

                const csrfToken = getCsrfToken();
                const csrfName = getCsrfName();

                if (!csrfToken) {
                    showNotification('Recarregando página por segurança...', 'info');
                    setTimeout(() => location.reload(), 1500);
                    return;
                }

                const formData = new FormData();
                formData.append('q', query);
                formData.append('specialty', specialty);
                formData.append('limit', limit);
                formData.append('offset', offset);
                formData.append(csrfName, csrfToken);

                const response = await fetch(SITE_URL + '/agenda/get_doctors', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                if (!response.ok) {
                    if (response.status === 403) {
                        showNotification('Sessão expirada. Recarregando...', 'error');
                        setTimeout(() => location.reload(), 1500);
                        return;
                    }
                    throw new Error(`HTTP ${response.status}`);
                }

                const text = await response.text();
                let result = JSON.parse(text);

                if (result.status === 'success') {
                    processDoctors(result, specialty, offset, limit);
                } else {
                    showNotification(result.message || 'Erro ao carregar médicos.', 'error');
                }
            } catch (err) {
                console.error('Erro:', err);
                showNotification('Erro ao carregar médicos.', 'error');
            }
        }

        function processDoctors(result, specialty, offset, limit) {
            if (result.csrf_token) updateCsrfToken(result.csrf_token, result.csrf_name);

            if (offset === 0) doctors = result.data || [];
            else doctors = doctors.concat(result.data || []);
            renderDoctors(specialty, result.total || 0);

            if ((result.data || []).length === 0 && offset === 0) {
                showNotification('Nenhum médico encontrado.', 'info');
            }
            if ((result.total || 0) > (offset + limit)) {
                renderLoadMoreButton(result.total - (offset + limit));
            }
        }

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
                            <p class="text-sm text-gray-600">${doctor.specialty || specialty} - ${doctor.experience || 'N/A'}</p>
                            ${doctor.rating ? `<p class="rating"><i class="fas fa-star"></i> ${doctor.rating} de 5</p>` : ''}
                        </div>
                    </div>
                `).join('') :
                `<div class="empty-state col-span-full"><i class="fas fa-user-md"></i>Nenhum médico disponível.</div>`;

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

        function renderLoadMoreButton(remaining) {
            const container = document.getElementById('doctor-container');
            if (container.querySelector('.load-more-btn')) return;
            const loadMoreBtn = document.createElement('button');
            loadMoreBtn.innerHTML = `<i class="fas fa-plus mr-2"></i>Carregar Mais (${remaining} restantes)`;
            loadMoreBtn.className = 'load-more-btn btn btn-primary w-full mt-4';
            loadMoreBtn.style.width = '100%';
            loadMoreBtn.addEventListener('click', () => {
                loadDoctors('', selectedSpecialty, 10, doctors.length);
            });
            container.appendChild(loadMoreBtn);
        }

        // ==================== SELECT SPECIALTY ====================
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

        // ==================== DOCTOR SEARCH ====================
        document.getElementById('doctor-search')?.addEventListener('input', function() {
            const term = this.value.trim().toLowerCase();
            document.querySelectorAll('#doctor-container .doctor-item').forEach(item => {
                const name = (item.dataset.name || '').toLowerCase();
                item.style.display = name.includes(term) ? '' : 'none';
            });
        });

        // ==================== AVAILABLE SLOTS ====================
        async function loadAvailableSlots(dateStr, medicoId) {
            try {
                const csrfToken = getCsrfToken();
                const csrfName = getCsrfName();

                if (!csrfToken) {
                    showNotification('Erro de segurança. Recarregando...', 'error');
                    setTimeout(() => location.reload(), 1500);
                    return;
                }

                const formData = new FormData();
                formData.append('data', dateStr);
                formData.append('medico_id', medicoId);
                formData.append(csrfName, csrfToken);

                const response = await fetch(SITE_URL + '/agenda/get_available_slots', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                if (!response.ok) {
                    if (response.status === 403) {
                        showNotification('Sessão expirada. Recarregando...', 'error');
                        setTimeout(() => location.reload(), 1500);
                        return;
                    }
                    throw new Error(`HTTP ${response.status}`);
                }

                const result = await response.json();
                if (result.status === 'success') {
                    const slots = result.data || [];
                    if (!availableSlots[dateStr]) availableSlots[dateStr] = {};
                    availableSlots[dateStr][selectedDoctor || 'default'] = slots;
                    if (result.csrf_token) updateCsrfToken(result.csrf_token, result.csrf_name);
                } else {
                    showNotification(result.message || 'Erro ao carregar horários.', 'error');
                }
            } catch (err) {
                console.error('Erro:', err);
                showNotification('Erro ao carregar horários.', 'error');
            }
        }

        function updateCsrfToken(newToken, newName) {
            if (newToken) {
                const metaToken = document.querySelector('meta[name="csrf-token"]');
                if (metaToken) metaToken.setAttribute('content', newToken);
                document.cookie = `csrf_cookie_name=${newToken}; path=/; SameSite=Lax`;
            }
        }

        // ==================== UPDATE AVAILABLE DAYS ====================
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
                if (date >= today) loadPromises.push(loadAvailableSlots(dateStr, selectedDoctorId));
            }

            await Promise.all(loadPromises);

            Object.keys(availableSlots).forEach(dateStr => {
                const dayEl = document.querySelector(`.fc-daygrid-day[data-date="${dateStr}"]`);
                if (dayEl) {
                    const slots = availableSlots[dateStr][selectedDoctor || 'default'] || [];
                    dayEl.classList.add(slots.length > 0 ? 'available' : 'unavailable');
                }
            });
        }

        // ==================== SHOW TIME SLOTS ====================
        async function showTimeSlots(dateStr, doctorName) {
            const medicoId = doctors.find(d => d.name === doctorName)?.id;
            if (!medicoId) {
                showNotification('Médico inválido.', 'error');
                return;
            }

            const csrfToken = getCsrfToken();
            if (!csrfToken) {
                showNotification('Erro de segurança. Recarregando...', 'error');
                setTimeout(() => location.reload(), 1500);
                return;
            }

            await loadAvailableSlots(dateStr, medicoId);

            const slots = availableSlots[dateStr] ? availableSlots[dateStr][doctorName] || [] : [];
            const modal = document.getElementById('time-slot-modal');
            const slotList = document.getElementById('time-slot-list');
            const subtitle = document.getElementById('time-slot-subtitle');

            if (subtitle) subtitle.textContent = `Horários disponíveis em ${dateStr} com ${doctorName}`;

            if (slots.length === 0) {
                slotList.innerHTML = '<div class="empty-state"><i class="fas fa-calendar-times"></i>Nenhum horário disponível para esta data.</div>';
                modal.classList.add('show');
                return;
            }

            slotList.innerHTML = slots.map(slot =>
                `<button class="slot-button available" data-time="${slot}"><i class="fas fa-clock"></i> ${slot} (Disponível)</button>`
            ).join('');

            modal.classList.add('show');

            document.querySelectorAll('.slot-button.available').forEach(button => {
                button.addEventListener('click', function() {
                    selectedTime = this.dataset.time;
                    updateSummary();
                    showNotification(`Horário selecionado: ${selectedTime}`, 'success');
                    modal.classList.remove('show');
                });
            });
        }

        // ==================== SAVE APPOINTMENT ====================
        async function saveAppointment(formData) {
            try {
                if (!(formData instanceof FormData)) {
                    const newFormData = new FormData();
                    for (const key in formData) {
                        if (formData.hasOwnProperty(key)) newFormData.append(key, formData[key]);
                    }
                    formData = newFormData;
                }

                const csrfToken = getCsrfToken();
                const csrfName = getCsrfName();

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

                if (!response.ok) {
                    if (response.status === 403) {
                        showNotification('Sessão expirada. Recarregando...', 'error');
                        setTimeout(() => location.reload(), 1500);
                        return false;
                    }
                    throw new Error(`HTTP ${response.status}`);
                }

                const result = await response.json();
                if (result.csrf_token) updateCsrfToken(result.csrf_token, result.csrf_name);

                if (result.status === 'success') {
                    showNotification(result.message, 'success');
                    return true;
                } else {
                    showNotification(result.message || 'Erro ao salvar agendamento.', 'error');
                    return false;
                }
            } catch (err) {
                console.error('Erro:', err);
                showNotification('Erro ao salvar agendamento.', 'error');
                return false;
            }
        }

        // ==================== LOAD PATIENT APPOINTMENTS ====================
        async function loadPatientAppointments() {
            try {
                const csrfToken = getCsrfToken();
                const csrfName = getCsrfName();
                const formData = new FormData();
                if (csrfToken) formData.append(csrfName, csrfToken);

                const response = await fetch(SITE_URL + '/agenda/get_patient_appointments', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                if (!response.ok) throw new Error(`HTTP ${response.status}`);

                const result = await response.json();
                if (result.status === 'success') renderAppointments(result.data);
                else showNotification(result.message, 'error');
            } catch (err) {
                console.error('Erro:', err);
                showNotification('Erro ao carregar agendamentos.', 'error');
            }
        }

        function renderAppointments(appointments) {
            const container = document.getElementById('appointments-list');
            if (!appointments || appointments.length === 0) {
                container.innerHTML = '<p class="text-gray-500">Nenhum agendamento encontrado.</p>';
                return;
            }

            container.innerHTML = appointments.map(appointment => {
                const statusColor = appointment.status === 'Pendente' ? '#92400e' :
                    appointment.status === 'Confirmado' ? 'var(--brand-700)' :
                    'var(--rose-500)';
                return `
                    <div class="appointment-item" style="border:1px solid #eef1f6;border-radius:0.6rem;padding:0.9rem 1rem;margin-bottom:0.6rem;background:white;">
                        <h4 class="font-medium">${appointment.especialidade || 'N/A'} - ${appointment.medico || 'N/A'}</h4>
                        ${appointment.paciente_nome ? `<p class="text-sm text-gray-600">Paciente: ${appointment.paciente_nome}</p>` : ''}
                        <p class="text-sm text-gray-600">Data: ${appointment.date} às ${appointment.time}</p>
                        <p class="text-sm text-gray-600">Status: <span class="font-semibold" style="color:${statusColor};">${appointment.status}</span></p>
                        <p class="text-sm text-gray-600">Motivo: ${appointment.motivo || 'N/A'}</p>
                    </div>
                `;
            }).join('');
        }

        // ==================== EVENTS ====================
        document.getElementById('meus-agendamentos-btn').addEventListener('click', (e) => {
            e.preventDefault();
            window.location.href = SITE_URL + '/agenda/agendamentos';
        });

        document.getElementById('logout-btn').addEventListener('click', () => {
            window.location.href = SITE_URL + '/auth/logout';
        });

        // ==================== REVIEW CONFIRM ====================
        document.getElementById('review-confirm-btn').addEventListener('click', async function() {
            const nome = document.getElementById('name')?.value?.trim() || '';
            const telefone = document.getElementById('phone')?.value?.trim() || '';
            const bi = document.getElementById('bi')?.value?.trim() || '';
            const motivo = document.getElementById('motivo')?.value?.trim() || '';

            const isOther = bookingFor === 'other';
            const patientName = isOther ? (document.getElementById('patient-name')?.value?.trim() || '') : nome;
            const patientBirthdate = isOther ? (document.getElementById('patient-birthdate')?.value || '') : '';
            const patientRelation = isOther ? (document.getElementById('patient-relation')?.value || '') : '';
            const patientDocType = isOther ? (document.getElementById('patient-doc-type')?.value || 'Nenhum') : '';
            const patientDocNumber = isOther ? (document.getElementById('patient-doc-number')?.value?.trim() || '') : '';

            if (!nome || !telefone || !bi) {
                showNotification('Preencha todos os campos de contacto.', 'error');
                return;
            }
            if (isOther && (!patientName || !patientRelation)) {
                showNotification('Indique o nome do paciente e o parentesco.', 'error');
                return;
            }
            if (!selectedSpecialty || !selectedDoctor || !selectedDate || !selectedTime) {
                showNotification('Selecione especialidade, médico, data e horário.', 'error');
                return;
            }

            const formData = new FormData();
            formData.append('especialidade', selectedSpecialty);
            formData.append('medico', selectedDoctorId);
            formData.append('data_consulta', selectedDate);
            formData.append('horario', selectedTime);
            formData.append('nome', nome);
            formData.append('telefone', telefone);
            formData.append('bi', bi);
            formData.append('motivo', motivo);

            // Novos campos — distinguem o paciente do responsável quando a marcação é feita
            // por um pai/mãe/tutor para outra pessoa (ex: filho/a menor).
            formData.append('agendado_para', bookingFor);
            formData.append('paciente_nome', patientName);
            formData.append('paciente_data_nascimento', patientBirthdate);
            formData.append('paciente_parentesco', patientRelation);
            formData.append('paciente_documento_tipo', patientDocType);
            formData.append('paciente_documento_numero', patientDocNumber);

            const confirmBtn = this;
            const originalText = confirmBtn.innerHTML;
            confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
            confirmBtn.disabled = true;

            try {
                const success = await saveAppointment(formData);

                if (success) {
                    const confirmationMessage = isOther ? `
                        Agendamento confirmado!<br>
                        <strong>Paciente:</strong> ${patientName}<br>
                        <strong>Responsável:</strong> ${nome}<br>
                        <strong>Especialidade:</strong> ${selectedSpecialty}<br>
                        <strong>Médico:</strong> ${selectedDoctor}<br>
                        <strong>Data:</strong> ${selectedDate}<br>
                        <strong>Horário:</strong> ${selectedTime}
                    ` : `
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
                    setBookingFor('self');
                    document.getElementById('patient-name').value = '';
                    document.getElementById('patient-birthdate').value = '';
                    document.getElementById('patient-relation').value = '';
                    document.getElementById('motivo').value = '';

                    document.querySelectorAll('.specialty-item').forEach(el => {
                        el.classList.remove('selected');
                        el.setAttribute('aria-pressed', 'false');
                    });

                    document.getElementById('doctor-container').innerHTML =
                        '<div class="empty-state col-span-full"><i class="fas fa-user-md"></i>Selecione uma especialidade para ver os médicos disponíveis.</div>';
                    document.getElementById('doctor-header').classList.add('hidden');

                    const searchInput = document.getElementById('doctor-search');
                    if (searchInput) {
                        searchInput.value = '';
                        searchInput.disabled = true;
                    }

                    updateSummary();
                }
            } catch (error) {
                console.error('Erro:', error);
                showNotification('Erro ao confirmar agendamento.', 'error');
            } finally {
                confirmBtn.innerHTML = originalText;
                confirmBtn.disabled = false;
            }
        });

        // ==================== CONFIRM BUTTON ====================
        document.getElementById('confirm-btn').addEventListener('click', function() {
            const nameEl = document.getElementById('name');
            const phoneEl = document.getElementById('phone');
            const biEl = document.getElementById('bi');
            const patientNameEl = document.getElementById('patient-name');
            const patientRelationEl = document.getElementById('patient-relation');

            const nome = nameEl?.value?.trim() || '';
            const telefone = phoneEl?.value?.trim() || '';
            const bi = biEl?.value?.trim() || '';

            [nameEl, phoneEl, biEl, patientNameEl, patientRelationEl].forEach(el => el?.classList.remove('field-error'));

            const isOther = bookingFor === 'other';
            let hasError = false;

            if (!nome) {
                nameEl?.classList.add('field-error');
                hasError = true;
            }
            if (!telefone) {
                phoneEl?.classList.add('field-error');
                hasError = true;
            }
            if (!bi) {
                biEl?.classList.add('field-error');
                hasError = true;
            }

            if (isOther) {
                if (!patientNameEl?.value?.trim()) {
                    patientNameEl?.classList.add('field-error');
                    hasError = true;
                }
                if (!patientRelationEl?.value) {
                    patientRelationEl?.classList.add('field-error');
                    hasError = true;
                }
            }

            if (hasError) {
                showNotification(
                    isOther ?
                    'Preencha os dados do paciente e do responsável antes de continuar.' :
                    'Por favor, preencha todos os campos do formulário.',
                    'error'
                );
                return;
            }

            if (!selectedSpecialty || !selectedDoctor || !selectedDate || !selectedTime) {
                showNotification('Selecione especialidade, médico, data e horário antes de continuar.', 'error');
                return;
            }

            const patientName = isOther ? patientNameEl.value.trim() : nome;
            const patientRelation = isOther ? patientRelationEl.value : '';

            document.getElementById('review-specialty').textContent = selectedSpecialty;
            document.getElementById('review-doctor').textContent = selectedDoctor;
            document.getElementById('review-date').textContent = selectedDate;
            document.getElementById('review-time').textContent = selectedTime;
            document.getElementById('review-name').textContent = nome;
            document.getElementById('review-phone').textContent = telefone;
            document.getElementById('review-bi').textContent = bi;

            document.getElementById('review-patient-name').textContent = patientName;
            document.getElementById('review-patient-section-label').textContent = isOther ? 'Paciente (dependente)' : 'Paciente';
            document.getElementById('review-responsible-section-label').textContent = isOther ? 'Responsável / Contacto' : 'Contacto';
            const relationWrap = document.getElementById('review-patient-relation-wrap');
            if (isOther) {
                relationWrap.style.display = '';
                document.getElementById('review-patient-relation').textContent = patientRelation;
            } else {
                relationWrap.style.display = 'none';
            }

            document.getElementById('review-modal').classList.add('show');
        });

        // ==================== MODAL HANDLERS ====================
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

        // ==================== DOMContentLoaded ====================
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
</body>

</html>