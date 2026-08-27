<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha | Centro de Saúde Da Matola II</title>
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

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
            max-height: 98vh;
            padding: 0.25rem;
        }

        .login-card {
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

        .login-card::-webkit-scrollbar {
            width: 4px;
        }

        .login-card::-webkit-scrollbar-track {
            background: transparent;
        }

        .login-card::-webkit-scrollbar-thumb {
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

        .form-group {
            margin-bottom: 1rem;
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

        .input-wrapper input {
            width: 100%;
            padding: 0.65rem 0.9rem;
            border: none;
            background: transparent;
            outline: none;
            font-size: 0.9rem;
            color: #1f2937;
            font-weight: 400;
        }

        .input-wrapper input::placeholder {
            color: #9ca3af;
            font-weight: 300;
            font-size: 0.85rem;
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

        .btn-login {
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
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
            transition: left 0.6s ease;
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(37, 99, 235, 0.35);
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.25);
        }

        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none !important;
        }

        .btn-login .spinner {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        .btn-login.loading .spinner {
            display: inline-block;
        }

        .btn-login.loading .btn-text {
            display: none;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 1rem;
            color: var(--brand-500);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .back-link:hover {
            color: var(--brand-700);
            text-decoration: underline;
        }

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

        @media (max-width: 480px) {
            body {
                padding: 0.5rem;
                align-items: center;
            }

            .login-card {
                padding: 1.5rem 1.25rem;
                border-radius: 1.25rem;
                max-height: 98vh;
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
                margin-bottom: 0.75rem;
            }

            .form-group label {
                font-size: 0.75rem;
            }

            .input-wrapper input {
                padding: 0.55rem 0.75rem;
                font-size: 0.85rem;
            }

            .btn-login {
                padding: 0.6rem;
                font-size: 0.9rem;
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
            .login-card {
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
                margin-bottom: 0.6rem;
            }

            .msg-container {
                margin-bottom: 0.8rem;
            }
        }

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

        .btn-login {
            opacity: 0;
            animation: fadeInField 0.6s ease 0.35s forwards;
        }

        .back-link {
            opacity: 0;
            animation: fadeInField 0.6s ease 0.45s forwards;
        }

        .footer {
            opacity: 0;
            animation: fadeInField 0.6s ease 0.55s forwards;
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
    <div class="login-container">
        <div class="login-card">
            <!-- Logo -->
            <div class="logo-container">
                <div class="logo-icon">
                    <i class="fas fa-key"></i>
                </div>
                <h1>Redefinir <span>Senha</span></h1>
                <p>Digite sua nova senha para continuar</p>
            </div>

            <!-- Mensagens -->
            <div class="msg-container">
                <div id="redefinir-error" class="msg msg-error">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    <span id="error-text">Erro ao redefinir senha.</span>
                </div>
                <div id="redefinir-success" class="msg msg-success">
                    <i class="fas fa-check-circle mr-1"></i>
                    Senha redefinida com sucesso!
                </div>
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
            </div>

            <!-- Formulário -->
            <form id="redefinir-form" class="space-y-4">
                <?= csrf_field(); ?>

                <input type="hidden" name="token" id="token" value="<?= $token ?? '' ?>">
                <input type="hidden" name="usuario_id" id="usuario_id" value="<?= $usuario_id ?? '' ?>">

                <div class="form-group">
                    <label for="senha">
                        <i class="fas fa-lock"></i> Nova Senha
                    </label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="senha" name="senha" required
                            placeholder="Digite sua nova senha" autocomplete="new-password" minlength="6">
                        <button type="button" class="toggle-password" data-target="senha" aria-label="Mostrar senha">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="error-validation" id="senha-error" style="display:none;">
                        <i class="fas fa-circle"></i>
                        <span id="senha-error-text">A senha deve ter pelo menos 6 caracteres.</span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirmar_senha">
                        <i class="fas fa-check-circle"></i> Confirmar Senha
                    </label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" required
                            placeholder="Confirme sua nova senha" autocomplete="new-password" minlength="6">
                        <button type="button" class="toggle-password" data-target="confirmar_senha" aria-label="Mostrar senha">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="error-validation" id="confirmar-error" style="display:none;">
                        <i class="fas fa-circle"></i>
                        <span id="confirmar-error-text">As senhas não coincidem.</span>
                    </div>
                </div>

                <button type="submit" class="btn-login" id="redefinir-btn">
                    <span class="spinner"></span>
                    <span class="btn-text">
                        <i class="fas fa-check-double mr-2"></i> Redefinir Senha
                    </span>
                </button>

                <a href="<?= site_url('auth/login') ?>" class="back-link">
                    <i class="fas fa-arrow-left mr-1"></i> Voltar ao Login
                </a>
            </form>

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
            document.querySelectorAll('.toggle-password').forEach(btn => {
                btn.addEventListener('click', function() {
                    const targetId = this.dataset.target;
                    const input = document.getElementById(targetId);
                    if (input) {
                        const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                        input.setAttribute('type', type);
                        this.querySelector('i').classList.toggle('fa-eye');
                        this.querySelector('i').classList.toggle('fa-eye-slash');
                    }
                });
            });

            // ==================== FORMULÁRIO ====================
            const form = document.getElementById('redefinir-form');
            const errorMsg = document.getElementById('redefinir-error');
            const successMsg = document.getElementById('redefinir-success');
            const errorText = document.getElementById('error-text');
            const btn = document.getElementById('redefinir-btn');
            const senhaInput = document.getElementById('senha');
            const confirmarInput = document.getElementById('confirmar_senha');
            const senhaError = document.getElementById('senha-error');
            const confirmarError = document.getElementById('confirmar-error');

            // Validação em tempo real
            senhaInput.addEventListener('input', function() {
                if (this.value.length > 0 && this.value.length < 6) {
                    senhaError.style.display = 'flex';
                    document.getElementById('senha-error-text').textContent = 'A senha deve ter pelo menos 6 caracteres.';
                } else {
                    senhaError.style.display = 'none';
                }
                validarConfirmacao();
            });

            confirmarInput.addEventListener('input', validarConfirmacao);

            function validarConfirmacao() {
                const senha = senhaInput.value;
                const confirmar = confirmarInput.value;
                if (confirmar.length > 0 && senha !== confirmar) {
                    confirmarError.style.display = 'flex';
                    document.getElementById('confirmar-error-text').textContent = 'As senhas não coincidem.';
                } else {
                    confirmarError.style.display = 'none';
                }
            }

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                // Resetar mensagens
                errorMsg.style.display = 'none';
                successMsg.style.display = 'none';
                btn.classList.add('loading');
                btn.disabled = true;

                const senha = senhaInput.value.trim();
                const confirmar = confirmarInput.value.trim();
                const token = document.getElementById('token').value;
                const usuario_id = document.getElementById('usuario_id').value;

                // Validações
                if (!senha || !confirmar) {
                    errorText.textContent = 'Preencha todos os campos.';
                    errorMsg.style.display = 'block';
                    btn.classList.remove('loading');
                    btn.disabled = false;
                    return;
                }

                if (senha.length < 6) {
                    errorText.textContent = 'A senha deve ter pelo menos 6 caracteres.';
                    errorMsg.style.display = 'block';
                    btn.classList.remove('loading');
                    btn.disabled = false;
                    return;
                }

                if (senha !== confirmar) {
                    errorText.textContent = 'As senhas não coincidem.';
                    errorMsg.style.display = 'block';
                    btn.classList.remove('loading');
                    btn.disabled = false;
                    return;
                }

                try {
                    const formData = new FormData();
                    formData.append('usuario_id', usuario_id);
                    formData.append('token', token);
                    formData.append('senha', senha);
                    formData.append('confirmar_senha', confirmar);

                    const response = await fetch("<?= site_url('auth/atualizar-senha'); ?>", {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const text = await response.text();
                    let result;
                    try {
                        result = JSON.parse(text);
                    } catch (parseErr) {
                        console.error('Parse erro:', parseErr);
                        throw new Error('Resposta inválida do servidor');
                    }

                    if (response.ok && result.status === 'success') {
                        successMsg.style.display = 'block';
                        btn.classList.remove('loading');
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-check-circle"></i> Redefinido com Sucesso!';
                        btn.style.background = 'linear-gradient(135deg, #059669, #047857)';

                        // Redirecionar após 2 segundos
                        setTimeout(() => {
                            window.location.href = "<?= site_url('auth/login') ?>";
                        }, 2000);
                    } else {
                        errorText.textContent = result.message || 'Erro ao redefinir senha. Tente novamente.';
                        errorMsg.style.display = 'block';
                        btn.classList.remove('loading');
                        btn.disabled = false;
                    }
                } catch (err) {
                    console.error('Erro:', err);
                    errorText.textContent = 'Erro ao conectar ao servidor. Tente novamente.';
                    errorMsg.style.display = 'block';
                    btn.classList.remove('loading');
                    btn.disabled = false;
                }
            });

            // Enter key nos campos
            document.querySelectorAll('input').forEach(input => {
                input.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        form.dispatchEvent(new Event('submit'));
                    }
                });
            });
        });
    </script>
</body>

</html>