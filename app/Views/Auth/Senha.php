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
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(37, 99, 235, 0.35);
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

        .password-strength {
            margin-top: 0.4rem;
        }

        .password-strength .strength-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
            background: #ef4444;
            transition: all 0.3s ease;
            border-radius: 9999px;
        }

        .password-strength .strength-text {
            font-size: 0.7rem;
            color: #9ca3af;
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <div class="bg-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>

    <div class="particles" id="particles"></div>

    <div class="login-container">
        <div class="login-card">
            <div class="logo-container">
                <div class="logo-icon">
                    <i class="fas fa-lock"></i>
                </div>
                <h1>Centro de Saúde <span>Da Matola II</span></h1>
                <p>Defina a sua nova senha</p>
            </div>

            <div class="msg-container">
                <div id="form-error" class="msg msg-error">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    <span id="error-text">Erro ao redefinir senha.</span>
                </div>
                <div id="form-success" class="msg msg-success">
                    <i class="fas fa-check-circle mr-1"></i>
                    <span id="success-text">Senha redefinida com sucesso!</span>
                </div>
            </div>

            <form id="reset-form">
                <?= csrf_field(); ?>
                <input type="hidden" name="usuario_id" value="<?= esc($usuario_id); ?>">
                <input type="hidden" name="token" value="<?= esc($token); ?>">

                <div class="form-group">
                    <label for="senha">
                        <i class="fas fa-lock"></i> Nova Senha
                    </label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="senha" name="senha" required
                            placeholder="Mínimo 6 caracteres" minlength="6" autocomplete="new-password">
                        <button type="button" class="toggle-password" data-target="senha" aria-label="Mostrar senha">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength" id="password-strength">
                        <div class="strength-row">
                            <div class="bar-container">
                                <div id="strength-bar" class="bar"></div>
                            </div>
                            <span id="strength-text" class="strength-text">Digite uma senha</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirmar_senha">
                        <i class="fas fa-lock"></i> Confirmar Nova Senha
                    </label>
                    <div class="input-wrapper">
                        <span class="input-icon">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" required
                            placeholder="Repita a nova senha" minlength="6" autocomplete="new-password">
                        <button type="button" class="toggle-password" data-target="confirmar_senha" aria-label="Mostrar senha">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-login" id="reset-btn">
                    <span class="spinner"></span>
                    <span class="btn-text">
                        <i class="fas fa-check mr-2"></i> Redefinir Senha
                    </span>
                </button>
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
            // Partículas
            const container = document.getElementById('particles');
            for (let i = 0; i < 20; i++) {
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

            // Toggle password
            document.querySelectorAll('.toggle-password').forEach(btn => {
                btn.addEventListener('click', function() {
                    const target = document.getElementById(this.dataset.target);
                    if (!target) return;
                    const type = target.getAttribute('type') === 'password' ? 'text' : 'password';
                    target.setAttribute('type', type);
                    this.querySelector('i').classList.toggle('fa-eye');
                    this.querySelector('i').classList.toggle('fa-eye-slash');
                });
            });

            // Força da senha
            const senhaInput = document.getElementById('senha');
            const strengthBar = document.getElementById('strength-bar');
            const strengthText = document.getElementById('strength-text');

            senhaInput.addEventListener('input', function() {
                const pwd = this.value;
                let strength = 0;

                if (pwd.length === 0) {
                    strengthBar.style.width = '0%';
                    strengthText.textContent = 'Digite uma senha';
                    strengthText.style.color = '#9ca3af';
                    return;
                }

                if (pwd.length >= 6) strength++;
                if (pwd.length >= 10) strength++;
                if (/[a-z]/.test(pwd)) strength++;
                if (/[A-Z]/.test(pwd)) strength++;
                if (/[0-9]/.test(pwd)) strength++;
                if (/[^a-zA-Z0-9]/.test(pwd)) strength++;

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

            // Submeter formulário
            const form = document.getElementById('reset-form');
            const errorBox = document.getElementById('form-error');
            const successBox = document.getElementById('form-success');
            const errorText = document.getElementById('error-text');
            const successText = document.getElementById('success-text');
            const resetBtn = document.getElementById('reset-btn');

            form.addEventListener('submit', async function(e) {
                e.preventDefault();

                errorBox.style.display = 'none';
                successBox.style.display = 'none';
                resetBtn.classList.add('loading');
                resetBtn.disabled = true;

                const formData = new FormData(form);
                const senha = formData.get('senha');
                const confirmar = formData.get('confirmar_senha');

                if (senha !== confirmar) {
                    errorText.textContent = 'As senhas não coincidem.';
                    errorBox.style.display = 'block';
                    resetBtn.classList.remove('loading');
                    resetBtn.disabled = false;
                    return;
                }

                try {
                    const response = await fetch("<?= site_url('auth/atualizar-senha'); ?>", {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const text = await response.text();
                    console.log('Status:', response.status);
                    console.log('Resposta:', text);

                    let result;
                    try {
                        result = JSON.parse(text);
                    } catch (parseErr) {
                        errorText.textContent = 'Erro no servidor. Verifique a consola (F12).';
                        errorBox.style.display = 'block';
                        return;
                    }

                    if (response.ok && result.status === 'success') {
                        successText.textContent = result.message || 'Senha redefinida com sucesso!';
                        successBox.style.display = 'block';
                        setTimeout(() => {
                            window.location.href = "<?= site_url('auth/login'); ?>";
                        }, 2000);
                    } else {
                        errorText.textContent = result.message || 'Erro ao redefinir senha.';
                        errorBox.style.display = 'block';
                    }
                } catch (err) {
                    console.error('Erro:', err);
                    errorText.textContent = 'Erro ao conectar ao servidor.';
                    errorBox.style.display = 'block';
                } finally {
                    resetBtn.classList.remove('loading');
                    resetBtn.disabled = false;
                }
            });
        });
    </script>
</body>

</html>