<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registar | Centro de Saúde Da Matola II</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root {
            --brand-700: #1d4ed8;
            --brand-600: #2563eb;
            --brand-500: #3b82f6;
            --brand-400: #60a5fa;
            --teal-500: #14b8a6;
            --teal-600: #0d9488;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f2f66 0%, #1a4a8a 30%, #2563eb 70%, #0d9488 100%);
            position: relative;
            overflow: hidden;
            padding: 1rem;
        }

        /* ---------- ANIMAÇÃO DE FUNDO ---------- */
        .bg-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.08;
            animation: floatShape 20s ease-in-out infinite;
        }

        .shape-1 {
            width: 600px;
            height: 600px;
            background: white;
            top: -200px;
            right: -200px;
            animation-delay: 0s;
        }

        .shape-2 {
            width: 400px;
            height: 400px;
            background: var(--teal-500);
            bottom: -100px;
            left: -100px;
            animation-delay: -5s;
        }

        .shape-3 {
            width: 300px;
            height: 300px;
            background: var(--brand-400);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation-delay: -10s;
        }

        @keyframes floatShape {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            25% {
                transform: translate(30px, -40px) scale(1.05);
            }

            50% {
                transform: translate(-20px, 20px) scale(0.95);
            }

            75% {
                transform: translate(40px, 30px) scale(1.02);
            }
        }

        /* ---------- PARTÍCULAS ---------- */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            animation: floatParticle 15s ease-in-out infinite;
        }

        @keyframes floatParticle {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
                opacity: 0.3;
            }

            25% {
                transform: translate(50px, -80px) scale(1.2);
                opacity: 0.6;
            }

            50% {
                transform: translate(-30px, 40px) scale(0.8);
                opacity: 0.2;
            }

            75% {
                transform: translate(70px, 60px) scale(1.1);
                opacity: 0.5;
            }
        }

        /* ---------- CARD PRINCIPAL ---------- */
        .register-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 620px;
            max-height: 98vh;
            padding: 0.25rem;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 1.5rem;
            padding: 2rem 1.75rem;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1);
            animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            transform: translateY(30px);
            opacity: 0;
            max-height: 96vh;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: rgba(0, 0, 0, 0.1) transparent;
        }

        .register-card::-webkit-scrollbar {
            width: 4px;
        }

        .register-card::-webkit-scrollbar-track {
            background: transparent;
        }

        .register-card::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 2px;
        }

        @keyframes slideUp {
            0% {
                transform: translateY(30px);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .register-card:hover {
            box-shadow: 0 40px 100px rgba(0, 0, 0, 0.35), 0 0 0 1px rgba(255, 255, 255, 0.15);
        }

        /* ---------- LOGO ---------- */
        .logo-container {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .logo-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--brand-600), var(--teal-500));
            border-radius: 1.25rem;
            color: white;
            font-size: 1.75rem;
            margin-bottom: 0.75rem;
            box-shadow: 0 8px 24px rgba(37, 99, 235, 0.3);
            animation: pulseIcon 3s ease-in-out infinite;
            transition: transform 0.3s ease;
        }

        .logo-icon:hover {
            transform: scale(1.05) rotate(-3deg);
        }

        @keyframes pulseIcon {

            0%,
            100% {
                box-shadow: 0 8px 24px rgba(37, 99, 235, 0.3);
            }

            50% {
                box-shadow: 0 8px 40px rgba(37, 99, 235, 0.5);
            }
        }

        .logo-container h1 {
            font-family: 'Outfit', 'Roboto', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            color: #1f2937;
            letter-spacing: -0.02em;
        }

        .logo-container h1 span {
            background: linear-gradient(135deg, var(--brand-600), var(--teal-500));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo-container p {
            color: #6b7280;
            font-size: 0.8rem;
            margin-top: 0.15rem;
            font-weight: 400;
        }

        /* ---------- MENSAGENS ---------- */
        .msg-container {
            margin-bottom: 1.25rem;
        }

        .msg {
            display: none;
            padding: 0.6rem 0.9rem;
            border-radius: 0.75rem;
            font-size: 0.8rem;
            font-weight: 500;
            text-align: center;
            animation: slideDown 0.4s ease-out forwards;
        }

        @keyframes slideDown {
            0% {
                transform: translateY(-10px);
                opacity: 0;
            }

            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .msg-error {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            color: #991b1b;
            border-left: 4px solid #ef4444;
        }

        .msg-success {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            color: #065f46;
            border-left: 4px solid #10b981;
        }

        /* ---------- FORMULÁRIO ---------- */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .form-grid-full {
            grid-column: span 2;
        }

        .form-group {
            margin-bottom: 0.75rem;
        }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.3rem;
        }

        .form-group label i {
            margin-right: 0.4rem;
            color: var(--brand-500);
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            border: 2px solid #e5e7eb;
            border-radius: 0.75rem;
            transition: all 0.3s ease;
            background: #f9fafb;
        }

        .input-wrapper:focus-within {
            border-color: var(--brand-500);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
            background: white;
        }

        .input-wrapper .input-icon {
            flex-shrink: 0;
            padding: 0 0 0 0.9rem;
            color: #9ca3af;
            transition: color 0.3s ease;
            font-size: 0.85rem;
        }

        .input-wrapper:focus-within .input-icon {
            color: var(--brand-500);
        }

        .input-wrapper input,
        .input-wrapper select {
            width: 100%;
            padding: 0.65rem 0.9rem;
            border: none;
            background: transparent;
            outline: none;
            font-size: 0.9rem;
            color: #1f2937;
            font-weight: 400;
            font-family: 'Roboto', sans-serif;
        }

        .input-wrapper input::placeholder {
            color: #9ca3af;
            font-weight: 300;
            font-size: 0.85rem;
        }

        .input-wrapper select {
            cursor: pointer;
        }

        .input-wrapper .toggle-password {
            flex-shrink: 0;
            padding: 0 0.9rem 0 0;
            color: #9ca3af;
            cursor: pointer;
            transition: color 0.3s ease;
            background: none;
            border: none;
            font-size: 0.9rem;
        }

        .input-wrapper .toggle-password:hover {
            color: #374151;
        }

        .error-validation {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }

        .error-validation i {
            font-size: 0.6rem;
        }

        /* Campos inválidos */
        .is-invalid {
            border-color: #ef4444 !important;
            background-color: #fef2f2;
        }

        .is-invalid:focus-within {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.12) !important;
        }

        /* ---------- FORÇA DA SENHA ---------- */
        .password-strength {
            margin-top: 0.4rem;
        }

        .password-strength .bar-container {
            height: 4px;
            background: #e5e7eb;
            border-radius: 9999px;
            overflow: hidden;
            flex: 1;
        }

        .password-strength .bar {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
            border-radius: 9999px;
        }

        .password-strength .strength-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .password-strength .strength-text {
            font-size: 0.7rem;
            color: #9ca3af;
            white-space: nowrap;
        }

        /* ---------- BOTÃO ---------- */
        .btn-register {
            width: 100%;
            padding: 0.7rem;
            background: linear-gradient(135deg, var(--brand-600), var(--teal-500));
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            font-family: 'Roboto', sans-serif;
        }

        .btn-register::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
            transition: left 0.6s ease;
        }

        .btn-register:hover::before {
            left: 100%;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(37, 99, 235, 0.35);
        }

        .btn-register:active {
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.25);
        }

        .btn-register:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
        }

        /* ---------- LINK LOGIN ---------- */
        .login-link {
            text-align: center;
            margin-top: 1.25rem;
            padding-top: 1.25rem;
            border-top: 1px solid #f3f4f6;
        }

        .login-link p {
            color: #6b7280;
            font-size: 0.8rem;
        }

        .login-link a {
            color: var(--brand-500);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .login-link a:hover {
            color: var(--brand-700);
            text-decoration: underline;
        }

        /* ---------- RODAPÉ ---------- */
        .footer {
            text-align: center;
            margin-top: 1.25rem;
            padding-top: 1.25rem;
            border-top: 1px solid #f3f4f6;
        }

        .footer p {
            color: #9ca3af;
            font-size: 0.7rem;
        }

        .footer .hospital-name {
            color: var(--brand-500);
            font-weight: 600;
        }

        /* ---------- RESPONSIVO ---------- */
        @media (max-width: 640px) {
            body {
                padding: 0.5rem;
                align-items: center;
            }

            .register-card {
                padding: 1.5rem 1.25rem;
                border-radius: 1.25rem;
                max-height: 98vh;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .form-grid-full {
                grid-column: span 1;
            }

            .logo-icon {
                width: 56px;
                height: 56px;
                font-size: 1.5rem;
                border-radius: 1rem;
            }

            .logo-container h1 {
                font-size: 1.3rem;
            }

            .logo-container p {
                font-size: 0.7rem;
            }

            .form-group {
                margin-bottom: 0.6rem;
            }

            .form-group label {
                font-size: 0.75rem;
            }

            .input-wrapper input,
            .input-wrapper select {
                padding: 0.55rem 0.75rem;
                font-size: 0.85rem;
            }

            .btn-register {
                padding: 0.6rem;
                font-size: 0.9rem;
            }

            .login-link {
                margin-top: 1rem;
                padding-top: 1rem;
            }

            .footer {
                margin-top: 1rem;
                padding-top: 1rem;
            }

            .footer p {
                font-size: 0.65rem;
            }
        }

        @media (max-height: 700px) {
            .register-card {
                padding: 1.25rem 1.25rem;
            }

            .logo-container {
                margin-bottom: 1rem;
            }

            .logo-icon {
                width: 50px;
                height: 50px;
                font-size: 1.25rem;
                margin-bottom: 0.5rem;
            }

            .logo-container h1 {
                font-size: 1.2rem;
            }

            .logo-container p {
                font-size: 0.7rem;
            }

            .form-group {
                margin-bottom: 0.5rem;
            }

            .msg-container {
                margin-bottom: 0.8rem;
            }

            .footer {
                margin-top: 0.8rem;
                padding-top: 0.8rem;
            }
        }

        /* ---------- ANIMAÇÃO DE ENTRADA DOS CAMPOS ---------- */
        .form-group {
            opacity: 0;
            animation: fadeInField 0.6s ease forwards;
        }

        .form-group:nth-child(1) {
            animation-delay: 0.15s;
        }

        .form-group:nth-child(2) {
            animation-delay: 0.25s;
        }

        .form-group:nth-child(3) {
            animation-delay: 0.35s;
        }

        .form-group:nth-child(4) {
            animation-delay: 0.45s;
        }

        .form-group:nth-child(5) {
            animation-delay: 0.55s;
        }

        .form-group:nth-child(6) {
            animation-delay: 0.65s;
        }

        .btn-register {
            opacity: 0;
            animation: fadeInField 0.6s ease 0.75s forwards;
        }

        .login-link {
            opacity: 0;
            animation: fadeInField 0.6s ease 0.85s forwards;
        }

        .footer {
            opacity: 0;
            animation: fadeInField 0.6s ease 0.95s forwards;
        }

        @keyframes fadeInField {
            0% {
                opacity: 0;
                transform: translateY(10px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ---------- SCROLLBAR PERSONALIZADA ---------- */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
    </style>
</head>

<body>
    <!-- Fundo com formas animadas -->
    <div class="bg-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <!-- Partículas flutuantes -->
    <div class="particles" id="particles"></div>

    <!-- Container Principal -->
    <div class="register-container">
        <div class="register-card">
            <!-- Logo -->
            <div class="logo-container">
                <div class="logo-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h1>Centro de Saúde <span>Da Matola II</span></h1>
                <p>Criar nova conta de utilizador</p>
            </div>

            <!-- Mensagens -->
            <div class="msg-container">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="msg msg-error" style="display:block;">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        <?= session()->getFlashdata('error'); ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="msg msg-success" style="display:block;">
                        <i class="fas fa-check-circle mr-1"></i>
                        <?= session()->getFlashdata('success'); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($validation) && $validation->getErrors()): ?>
                    <div class="msg msg-error" style="display:block;">
                        <i class="fas fa-exclamation-circle mr-1"></i>
                        Por favor, corrija os erros abaixo.
                    </div>
                <?php endif; ?>
            </div>

            <!-- Formulário -->
            <form action="<?= site_url('auth/register'); ?>" method="POST" id="register-form">
                <?= csrf_field(); ?>

                <div class="form-grid">
                    <!-- Nome -->
                    <div class="form-group">
                        <label for="nome">
                            <i class="fas fa-user"></i> Nome
                        </label>
                        <div class="input-wrapper <?= isset($validation) && $validation->hasError('nome') ? 'is-invalid' : ''; ?>">
                            <span class="input-icon">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" id="nome" name="nome" value="<?= old('nome'); ?>"
                                required autofocus placeholder="Seu nome"
                                autocomplete="given-name">
                        </div>
                        <?php if (isset($validation) && $validation->hasError('nome')): ?>
                            <div class="error-validation">
                                <i class="fas fa-circle"></i>
                                <?= $validation->getError('nome'); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Sobrenome -->
                    <div class="form-group">
                        <label for="sobrenome">
                            <i class="fas fa-user"></i> Sobrenome
                        </label>
                        <div class="input-wrapper <?= isset($validation) && $validation->hasError('sobrenome') ? 'is-invalid' : ''; ?>">
                            <span class="input-icon">
                                <i class="fas fa-user"></i>
                            </span>
                            <input type="text" id="sobrenome" name="sobrenome" value="<?= old('sobrenome'); ?>"
                                required placeholder="Seu sobrenome"
                                autocomplete="family-name">
                        </div>
                        <?php if (isset($validation) && $validation->hasError('sobrenome')): ?>
                            <div class="error-validation">
                                <i class="fas fa-circle"></i>
                                <?= $validation->getError('sobrenome'); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Data de Nascimento -->
                    <div class="form-group">
                        <label for="data_nascimento">
                            <i class="fas fa-calendar-alt"></i> Data de Nascimento
                        </label>
                        <div class="input-wrapper <?= isset($validation) && $validation->hasError('data_nascimento') ? 'is-invalid' : ''; ?>">
                            <span class="input-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <input type="date" id="data_nascimento" name="data_nascimento" value="<?= old('data_nascimento'); ?>"
                                required>
                        </div>
                        <?php if (isset($validation) && $validation->hasError('data_nascimento')): ?>
                            <div class="error-validation">
                                <i class="fas fa-circle"></i>
                                <?= $validation->getError('data_nascimento'); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Género -->
                    <div class="form-group">
                        <label for="genero">
                            <i class="fas fa-venus-mars"></i> Género
                        </label>
                        <div class="input-wrapper <?= isset($validation) && $validation->hasError('genero') ? 'is-invalid' : ''; ?>">
                            <span class="input-icon">
                                <i class="fas fa-venus-mars"></i>
                            </span>
                            <select id="genero" name="genero" required>
                                <option value="">Selecione...</option>
                                <option value="Masculino" <?= old('genero') == 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                                <option value="Feminino" <?= old('genero') == 'Feminino' ? 'selected' : ''; ?>>Feminino</option>
                            </select>
                        </div>
                        <?php if (isset($validation) && $validation->hasError('genero')): ?>
                            <div class="error-validation">
                                <i class="fas fa-circle"></i>
                                <?= $validation->getError('genero'); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Telefone -->
                    <div class="form-group">
                        <label for="telefone">
                            <i class="fas fa-phone"></i> Telefone
                        </label>
                        <div class="input-wrapper <?= isset($validation) && $validation->hasError('telefone') ? 'is-invalid' : ''; ?>">
                            <span class="input-icon">
                                <i class="fas fa-phone"></i>
                            </span>
                            <input type="tel" id="telefone" name="telefone" value="<?= old('telefone'); ?>"
                                required placeholder="+258 84 000 0000"
                                autocomplete="tel">
                        </div>
                        <?php if (isset($validation) && $validation->hasError('telefone')): ?>
                            <div class="error-validation">
                                <i class="fas fa-circle"></i>
                                <?= $validation->getError('telefone'); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- BI -->
                    <div class="form-group">
                        <label for="bi">
                            <i class="fas fa-id-card"></i> Número do BI
                        </label>
                        <div class="input-wrapper <?= isset($validation) && $validation->hasError('bi') ? 'is-invalid' : ''; ?>">
                            <span class="input-icon">
                                <i class="fas fa-id-card"></i>
                            </span>
                            <input type="text" id="bi" name="bi" value="<?= old('bi'); ?>"
                                required placeholder="000000000000A"
                                autocomplete="off">
                        </div>
                        <?php if (isset($validation) && $validation->hasError('bi')): ?>
                            <div class="error-validation">
                                <i class="fas fa-circle"></i>
                                <?= $validation->getError('bi'); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Email -->
                    <div class="form-group form-grid-full">
                        <label for="email">
                            <i class="fas fa-envelope"></i> Email
                        </label>
                        <div class="input-wrapper <?= isset($validation) && $validation->hasError('email') ? 'is-invalid' : ''; ?>">
                            <span class="input-icon">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" id="email" name="email" value="<?= old('email'); ?>"
                                required placeholder="seu.email@dominio.com"
                                autocomplete="email">
                        </div>
                        <?php if (isset($validation) && $validation->hasError('email')): ?>
                            <div class="error-validation">
                                <i class="fas fa-circle"></i>
                                <?= $validation->getError('email'); ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Senha -->
                    <div class="form-group form-grid-full">
                        <label for="senha">
                            <i class="fas fa-lock"></i> Senha
                        </label>
                        <div class="input-wrapper <?= isset($validation) && $validation->hasError('senha') ? 'is-invalid' : ''; ?>">
                            <span class="input-icon">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" id="senha" name="senha" required
                                placeholder="Mínimo 6 caracteres" autocomplete="new-password" minlength="6">
                            <button type="button" class="toggle-password" id="togglePassword" aria-label="Mostrar senha">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <?php if (isset($validation) && $validation->hasError('senha')): ?>
                            <div class="error-validation">
                                <i class="fas fa-circle"></i>
                                <?= $validation->getError('senha'); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Força da Senha -->
                        <div class="password-strength" id="password-strength">
                            <div class="strength-row">
                                <div class="bar-container">
                                    <div id="strength-bar" class="bar"></div>
                                </div>
                                <span id="strength-text" class="strength-text">Digite uma senha</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botão Registar -->
                <button type="submit" class="btn-register" id="register-btn" style="margin-top: 0.5rem;">
                    <span class="spinner" style="display:none; width:18px; height:18px; border:2px solid rgba(255,255,255,0.3); border-top-color:white; border-radius:50%; animation: spin 0.8s linear infinite;"></span>
                    <span class="btn-text">
                        <i class="fas fa-user-plus mr-2"></i> Registar
                    </span>
                </button>
            </form>

            <!-- Link para Login -->
            <div class="login-link">
                <p>Já tem uma conta?
                    <a href="<?= site_url('auth/login'); ?>">
                        <i class="fas fa-sign-in-alt mr-1"></i> Entre aqui
                    </a>
                </p>
            </div>

            <!-- Rodapé -->
            <div class="footer">
                <p>
                    <i class="fas fa-heartbeat text-red-500 mr-1"></i>
                    <span class="hospital-name">Centro de Saúde Da Matola II</span>
                    <span class="mx-1">•</span>
                    © <?= date('Y') ?>
                    <span class="mx-1">•</span>
                    <i class="fas fa-shield-alt text-blue-500 ml-1"></i> Seguro
                </p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ==================== PARTÍCULAS ====================
            function createParticles() {
                const container = document.getElementById('particles');
                const count = 20;

                for (let i = 0; i < count; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'particle';
                    const size = Math.random() * 6 + 2;
                    particle.style.width = size + 'px';
                    particle.style.height = size + 'px';
                    particle.style.left = Math.random() * 100 + '%';
                    particle.style.top = Math.random() * 100 + '%';
                    particle.style.animationDuration = (Math.random() * 20 + 10) + 's';
                    particle.style.animationDelay = (Math.random() * 10) + 's';
                    container.appendChild(particle);
                }
            }
            createParticles();

            // ==================== TOGGLE SENHA ====================
            const togglePassword = document.getElementById('togglePassword');
            const senhaInput = document.getElementById('senha');

            if (togglePassword && senhaInput) {
                togglePassword.addEventListener('click', function() {
                    const type = senhaInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    senhaInput.setAttribute('type', type);
                    this.querySelector('i').classList.toggle('fa-eye');
                    this.querySelector('i').classList.toggle('fa-eye-slash');
                });
            }

            // ==================== FORÇA DA SENHA ====================
            const strengthBar = document.getElementById('strength-bar');
            const strengthText = document.getElementById('strength-text');

            if (senhaInput && strengthBar && strengthText) {
                senhaInput.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;

                    if (password.length === 0) {
                        strengthBar.style.width = '0%';
                        strengthBar.style.background = '#ef4444';
                        strengthText.textContent = 'Digite uma senha';
                        strengthText.style.color = '#9ca3af';
                        return;
                    }

                    if (password.length >= 6) strength += 1;
                    if (password.length >= 10) strength += 1;
                    if (/[a-z]/.test(password)) strength += 1;
                    if (/[A-Z]/.test(password)) strength += 1;
                    if (/[0-9]/.test(password)) strength += 1;
                    if (/[^a-zA-Z0-9]/.test(password)) strength += 1;

                    const percent = (strength / 6) * 100;
                    strengthBar.style.width = percent + '%';

                    if (strength <= 2) {
                        strengthBar.style.background = '#ef4444';
                        strengthText.textContent = 'Senha fraca';
                        strengthText.style.color = '#ef4444';
                    } else if (strength <= 3) {
                        strengthBar.style.background = '#f59e0b';
                        strengthText.textContent = 'Senha média';
                        strengthText.style.color = '#d97706';
                    } else if (strength <= 4) {
                        strengthBar.style.background = '#3b82f6';
                        strengthText.textContent = 'Senha boa';
                        strengthText.style.color = '#2563eb';
                    } else {
                        strengthBar.style.background = '#10b981';
                        strengthText.textContent = 'Senha forte';
                        strengthText.style.color = '#059669';
                    }
                });
            }

            // ==================== PREVENIR DUPLO SUBMIT ====================
            const form = document.getElementById('register-form');
            const registerBtn = document.getElementById('register-btn');

            if (form && registerBtn) {
                form.addEventListener('submit', function() {
                    registerBtn.disabled = true;
                    const spinner = registerBtn.querySelector('.spinner');
                    const btnText = registerBtn.querySelector('.btn-text');
                    if (spinner) spinner.style.display = 'inline-block';
                    if (btnText) btnText.style.display = 'none';
                });
            }
        });
    </script>
</body>

</html>