<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registar - Hospital Matlhovele</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://kit.fontawesome.com/a2d9d6f5f5.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #2563eb, #1e3a8a);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        .fade-in {
            animation: fadeIn 1s ease-in-out forwards;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .btn-gradient {
            background: linear-gradient(90deg, #2563eb, #1d4ed8);
            transition: all 0.3s ease-in-out;
        }
        .btn-gradient:hover {
            transform: scale(1.03);
            background: linear-gradient(90deg, #1d4ed8, #1e40af);
        }
        .input-focus:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.2);
        }
        .input-group {
            position: relative;
        }
        .input-group i {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
            z-index: 10;
        }
        .input-group input, .input-group select {
            padding-left: 2.5rem;
        }
        .error-message {
            color: #ef4444;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        .error-message i {
            margin-right: 0.25rem;
        }
        /* Estilo para campos com erro */
        .is-invalid {
            border-color: #ef4444 !important;
            background-color: #fef2f2;
        }
        .is-invalid:focus {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2) !important;
        }
        /* Feedback visual para campos válidos */
        .is-valid {
            border-color: #10b981 !important;
        }
        .is-valid:focus {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2) !important;
        }
    </style>
</head>
<body>
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl p-8 fade-in">
        <div class="text-center mb-6">
            <i class="fas fa-user-plus text-4xl text-blue-600 mb-2"></i>
            <h2 class="text-2xl font-semibold text-gray-800">Criar Conta</h2>
            <p class="text-gray-500 text-sm">Preencha os dados abaixo para se registar</p>
        </div>

        <!-- Formulário CI4 - sem form_open do CI3 -->
        <form action="<?= site_url('auth/register'); ?>" method="POST" class="space-y-4" id="register-form">
            <!-- CSRF Token obrigatório no CI4 -->
            <?= csrf_field(); ?>

            <!-- Erros de validação gerais -->
            <?php if (isset($validation) && $validation->getErrors()): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php foreach ($validation->getErrors() as $error): ?>
                        <div class="error-message"><i class="fas fa-times-circle"></i> <?= $error; ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Mensagem de erro flash -->
            <?php if (session()->getFlashdata('error')): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?= session()->getFlashdata('error'); ?>
                </div>
            <?php endif; ?>

            <!-- Mensagem de sucesso flash -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?= session()->getFlashdata('success'); ?>
                </div>
            <?php endif; ?>

            <!-- Campos do formulário -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="nome" placeholder="Nome" 
                           value="<?= old('nome'); ?>" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 input-focus focus:outline-none <?= isset($validation) && $validation->hasError('nome') ? 'is-invalid' : ''; ?>"
                           required>
                    <?php if (isset($validation) && $validation->hasError('nome')): ?>
                        <div class="error-message"><i class="fas fa-times-circle"></i> <?= $validation->getError('nome'); ?></div>
                    <?php endif; ?>
                </div>
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="sobrenome" placeholder="Sobrenome" 
                           value="<?= old('sobrenome'); ?>" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 input-focus focus:outline-none <?= isset($validation) && $validation->hasError('sobrenome') ? 'is-invalid' : ''; ?>"
                           required>
                    <?php if (isset($validation) && $validation->hasError('sobrenome')): ?>
                        <div class="error-message"><i class="fas fa-times-circle"></i> <?= $validation->getError('sobrenome'); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="input-group">
                    <i class="fas fa-calendar-alt"></i>
                    <input type="date" name="data_nascimento" 
                           value="<?= old('data_nascimento'); ?>" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 input-focus focus:outline-none <?= isset($validation) && $validation->hasError('data_nascimento') ? 'is-invalid' : ''; ?>"
                           required>
                    <?php if (isset($validation) && $validation->hasError('data_nascimento')): ?>
                        <div class="error-message"><i class="fas fa-times-circle"></i> <?= $validation->getError('data_nascimento'); ?></div>
                    <?php endif; ?>
                </div>
                <div class="input-group">
                    <i class="fas fa-venus-mars"></i>
                    <select name="genero" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 input-focus focus:outline-none <?= isset($validation) && $validation->hasError('genero') ? 'is-invalid' : ''; ?>"
                            required>
                        <option value="">Selecione...</option>
                        <option value="Masculino" <?= old('genero') == 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                        <option value="Feminino" <?= old('genero') == 'Feminino' ? 'selected' : ''; ?>>Feminino</option>
                        <option value="Outro" <?= old('genero') == 'Outro' ? 'selected' : ''; ?>>Outro</option>
                    </select>
                    <?php if (isset($validation) && $validation->hasError('genero')): ?>
                        <div class="error-message"><i class="fas fa-times-circle"></i> <?= $validation->getError('genero'); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="input-group">
                    <i class="fas fa-phone"></i>
                    <input type="tel" name="telefone" placeholder="Número de telefone" 
                           value="<?= old('telefone'); ?>" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 input-focus focus:outline-none <?= isset($validation) && $validation->hasError('telefone') ? 'is-invalid' : ''; ?>"
                           required>
                    <?php if (isset($validation) && $validation->hasError('telefone')): ?>
                        <div class="error-message"><i class="fas fa-times-circle"></i> <?= $validation->getError('telefone'); ?></div>
                    <?php endif; ?>
                </div>
                <div class="input-group">
                    <i class="fas fa-id-card"></i>
                    <input type="text" name="bi" placeholder="Número do BI" 
                           value="<?= old('bi'); ?>" 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 input-focus focus:outline-none <?= isset($validation) && $validation->hasError('bi') ? 'is-invalid' : ''; ?>"
                           required>
                    <?php if (isset($validation) && $validation->hasError('bi')): ?>
                        <div class="error-message"><i class="fas fa-times-circle"></i> <?= $validation->getError('bi'); ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="E-mail" 
                       value="<?= old('email'); ?>" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 input-focus focus:outline-none <?= isset($validation) && $validation->hasError('email') ? 'is-invalid' : ''; ?>"
                       required>
                <?php if (isset($validation) && $validation->hasError('email')): ?>
                    <div class="error-message"><i class="fas fa-times-circle"></i> <?= $validation->getError('email'); ?></div>
                <?php endif; ?>
            </div>

            <div class="input-group relative">
                <i class="fas fa-lock"></i>
                <input type="password" name="senha" id="password" placeholder="Senha (mínimo 6 caracteres)" 
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 input-focus focus:outline-none pr-10 <?= isset($validation) && $validation->hasError('senha') ? 'is-invalid' : ''; ?>"
                       required minlength="6">
                <i class="fas fa-eye absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 cursor-pointer hover:text-gray-600 transition-colors" 
                   id="toggle-password"></i>
                <?php if (isset($validation) && $validation->hasError('senha')): ?>
                    <div class="error-message"><i class="fas fa-times-circle"></i> <?= $validation->getError('senha'); ?></div>
                <?php endif; ?>
                <!-- Força da senha (opcional) -->
                <div class="mt-2" id="password-strength">
                    <div class="flex items-center space-x-2">
                        <div class="h-1 flex-1 bg-gray-200 rounded-full overflow-hidden">
                            <div id="strength-bar" class="h-full w-0 bg-red-500 transition-all duration-300"></div>
                        </div>
                        <span id="strength-text" class="text-xs text-gray-500">Digite uma senha</span>
                    </div>
                </div>
            </div>

            <button type="submit"
                    class="w-full py-3 text-white font-semibold rounded-lg btn-gradient focus:outline-none hover:shadow-lg transition-all duration-300">
                <i class="fas fa-user-plus mr-2"></i> Registar
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-600">
            <p>Já tem uma conta? <a href="<?= site_url('auth/login'); ?>" class="text-blue-600 hover:underline font-medium">Entre aqui</a></p>
        </div>

        <footer class="mt-8 text-center text-xs text-gray-400">
            &copy; <?= date('Y'); ?> Hospital Matlhovele — Todos os direitos reservados
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle da senha
            const togglePassword = document.querySelector('#toggle-password');
            const passwordInput = document.querySelector('#password');
            
            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.classList.toggle('fa-eye');
                    this.classList.toggle('fa-eye-slash');
                });
            }

            // Validação de força da senha (opcional)
            const strengthBar = document.getElementById('strength-bar');
            const strengthText = document.getElementById('strength-text');
            
            if (passwordInput && strengthBar && strengthText) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;
                    
                    if (password.length === 0) {
                        strengthBar.style.width = '0%';
                        strengthText.textContent = 'Digite uma senha';
                        strengthText.className = 'text-xs text-gray-500';
                        return;
                    }
                    
                    // Critérios de força
                    if (password.length >= 6) strength += 1;
                    if (password.length >= 10) strength += 1;
                    if (/[a-z]/.test(password)) strength += 1;
                    if (/[A-Z]/.test(password)) strength += 1;
                    if (/[0-9]/.test(password)) strength += 1;
                    if (/[^a-zA-Z0-9]/.test(password)) strength += 1;
                    
                    // Percentual
                    const percent = (strength / 6) * 100;
                    strengthBar.style.width = percent + '%';
                    
                    // Cores e textos
                    if (strength <= 2) {
                        strengthBar.className = 'h-full bg-red-500 transition-all duration-300';
                        strengthText.textContent = 'Senha fraca';
                        strengthText.className = 'text-xs text-red-500';
                    } else if (strength <= 3) {
                        strengthBar.className = 'h-full bg-yellow-500 transition-all duration-300';
                        strengthText.textContent = 'Senha média';
                        strengthText.className = 'text-xs text-yellow-600';
                    } else if (strength <= 4) {
                        strengthBar.className = 'h-full bg-blue-500 transition-all duration-300';
                        strengthText.textContent = 'Senha boa';
                        strengthText.className = 'text-xs text-blue-600';
                    } else {
                        strengthBar.className = 'h-full bg-green-500 transition-all duration-300';
                        strengthText.textContent = 'Senha forte';
                        strengthText.className = 'text-xs text-green-600';
                    }
                });
            }

            // Validação em tempo real (opcional)
            const form = document.getElementById('register-form');
            const inputs = form.querySelectorAll('input, select');
            
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    // Remove classes de validação
                    this.classList.remove('is-valid', 'is-invalid');
                    
                    // Se o campo está vazio e é required, marca como inválido
                    if (this.hasAttribute('required') && this.value.trim() === '') {
                        this.classList.add('is-invalid');
                        return;
                    }
                    
                    // Validação específica para email
                    if (this.type === 'email' && this.value.trim() !== '') {
                        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        if (!emailRegex.test(this.value)) {
                            this.classList.add('is-invalid');
                            return;
                        }
                    }
                    
                    // Se passou todas as validações, marca como válido
                    if (this.value.trim() !== '') {
                        this.classList.add('is-valid');
                    }
                });
            });
        });
    </script>
</body>
</html>