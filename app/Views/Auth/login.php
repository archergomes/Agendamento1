<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Hospital Matlhovele</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(135deg, #2563eb, #1e40af);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .fade-in {
            animation: fadeIn 0.8s ease-out forwards;
        }

        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }

            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .glow {
            box-shadow: 0 0 20px rgba(37, 99, 235, 0.2);
            transition: box-shadow 0.3s ease;
        }

        .glow:hover {
            box-shadow: 0 0 30px rgba(37, 99, 235, 0.35);
        }

        .error-msg,
        .success-msg {
            display: none;
            padding: 0.75rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
            text-align: center;
            font-weight: 500;
        }

        .error-msg {
            background-color: #f87171;
            color: white;
        }

        .success-msg {
            background-color: #10b981;
            color: white;
        }

        .error-validation {
            color: #f87171;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
</head>

<body>
    <div class="w-full max-w-md bg-white rounded-2xl shadow-lg p-8 fade-in mx-4 glow">
        <div class="text-center mb-6">
            <i class="fas fa-hospital-user text-4xl text-blue-600 mb-3"></i>
            <h1 class="text-2xl font-semibold text-gray-800">Hospital Matlhovele</h1>
            <p class="text-gray-500 text-sm mt-1">Sistema de Agendamento de Consultas</p>
        </div>

        <!-- Mensagens Flash do CI4 -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="error-msg" style="display:block;"><?= session()->getFlashdata('error'); ?></div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="success-msg" style="display:block;"><?= session()->getFlashdata('success'); ?></div>
        <?php endif; ?>

        <!-- Mensagens Dinâmicas do JS -->
        <div id="login-success" class="success-msg">Login efetuado com sucesso!</div>
        <div id="login-error" class="error-msg">Credenciais inválidas. Tente novamente.</div>

        <!-- Formulário CI4 -->
        <form action="<?= site_url('auth/login'); ?>" method="POST" id="login-form" class="space-y-5">
            <?= csrf_field(); ?>
            <div>
                <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
                <div class="flex items-center border border-gray-300 rounded-lg px-3">
                    <i class="fas fa-envelope text-gray-400 mr-2"></i>
                    <input type="email" id="email" name="email" value="<?= old('email'); ?>"
                        required autofocus placeholder="exemplo@dominio.com"
                        class="w-full py-2 outline-none text-gray-700 placeholder-gray-400">
                </div>
                <?php if (isset($validation) && $validation->hasError('email')): ?>
                    <div class="error-validation"><?= $validation->getError('email'); ?></div>
                <?php endif; ?>
            </div>

            <div>
                <label for="senha" class="block text-gray-700 font-medium mb-1">Senha</label>
                <div class="flex items-center border border-gray-300 rounded-lg px-3">
                    <i class="fas fa-lock text-gray-400 mr-2"></i>
                    <input type="password" id="senha" name="senha" required placeholder="••••••••"
                        class="w-full py-2 outline-none text-gray-700 placeholder-gray-400">
                </div>
                <?php if (isset($validation) && $validation->hasError('senha')): ?>
                    <div class="error-validation"><?= $validation->getError('senha'); ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg font-medium hover:bg-blue-700 transition transform hover:scale-[1.02] duration-150">
                Entrar
            </button>

            <p class="text-center text-sm text-gray-600 mt-3">
                Esqueceu a senha?
                <a href="#" class="text-blue-600 hover:underline">Recuperar</a>
            </p>
        </form>

        <div class="mt-8 text-center text-gray-400 text-xs">
            © <?= date('Y') ?> Hospital Matlhovele. Todos os direitos reservados.
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('login-form');
            const errorMsg = document.getElementById('login-error');
            const successMsg = document.getElementById('login-success');

            if (form) {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const formData = new FormData(form);
                    const email = formData.get('email').trim();
                    const senha = formData.get('senha').trim();

                    errorMsg.style.display = 'none';
                    successMsg.style.display = 'none';

                    try {
                        const response = await fetch("<?= site_url('auth/login'); ?>", {
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
                            throw new Error('Resposta inválida');
                        }

                        if (response.ok && result.status === 'success') {
                            successMsg.style.display = 'block';

                            // Usar o redirect da resposta ou fallback para agenda
                            const redirectUrl = result.redirect || "<?= site_url('agenda'); ?>";
                            console.log('Redirecionando para:', redirectUrl);

                            setTimeout(() => {
                                window.location.href = redirectUrl;
                            }, 500);
                        } else {
                            errorMsg.textContent = result.message || 'Erro desconhecido.';
                            errorMsg.style.display = 'block';
                        }
                    } catch (err) {
                        console.error('Erro geral:', err);
                        errorMsg.textContent = `Erro: ${err.message}`;
                        errorMsg.style.display = 'block';
                    }
                });
            }
        });
    </script>
</body>

</html>