<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Nova Senha</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4cc9f0;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .password-container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            width: 100%;
            max-width: 450px;
        }

        .password-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .password-icon {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .password-icon i {
            font-size: 2rem;
            color: white;
        }

        .password-title {
            color: var(--dark-color);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .password-subtitle {
            color: #6c757d;
            font-size: 0.9rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 1px solid #e1e5ee;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
        }

        .password-strength {
            height: 5px;
            border-radius: 5px;
            margin-top: 0.5rem;
            background-color: #e9ecef;
            overflow: hidden;
        }

        .password-strength-bar {
            height: 100%;
            width: 0;
            border-radius: 5px;
            transition: width 0.3s, background-color 0.3s;
        }

        .password-requirements {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 0.5rem;
        }

        .password-requirements ul {
            padding-left: 1.2rem;
            margin-bottom: 0;
        }

        .password-requirements li {
            margin-bottom: 0.2rem;
        }

        .requirement-met {
            color: #28a745;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 8px;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s;
            width: 100%;
            margin-top: 1rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.9rem;
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>

<body>
    <div class="password-container">
        <div class="password-header">
            <div class="password-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h2 class="password-title">Criar Nova Senha</h2>
            <p class="password-subtitle">Digite sua nova senha abaixo</p>
        </div>

        <!-- Exemplo de mensagem de erro (remover se não for necessário) -->
        <!-- <div class="alert alert-warning" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Token expirado ou inválido. Solicite um novo link de redefinição.
        </div> -->

        <form method="post" action="<?= site_url('reset-password') ?>">
            <input type="hidden" name="token" value="<?= esc($token) ?>">

            <div class="mb-3">
                <label for="password" class="form-label">Nova Senha</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="password" required placeholder="Digite sua nova senha">
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-strength mt-2">
                    <div class="password-strength-bar" id="passwordStrengthBar"></div>
                </div>
                <div class="password-requirements">
                    <ul>
                        <li id="lengthReq">Pelo menos 8 caracteres</li>
                        <li id="uppercaseReq">Pelo menos uma letra maiúscula</li>
                        <li id="numberReq">Pelo menos um número</li>
                        <li id="specialReq">Pelo menos um caractere especial</li>
                    </ul>
                </div>
            </div>

            <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirmar Senha</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="confirmPassword" name="password" required placeholder="Confirme sua nova senha">
                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="invalid-feedback" id="passwordMatchFeedback">
                    As senhas não coincidem.
                </div>
            </div>

            <button type="submit" class="btn btn-primary" id="submitButton" disabled>
                <i class="fas fa-key me-2"></i> Criar Nova Senha
            </button>
        </form>

        <div class="login-link">
            Já tem conta? <a href="<?= site_url('login') ?>">Fazer login</a>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.getElementById('password');
            const confirmPasswordInput = document.getElementById('confirmPassword');
            const togglePasswordButton = document.getElementById('togglePassword');
            const toggleConfirmPasswordButton = document.getElementById('toggleConfirmPassword');
            const passwordStrengthBar = document.getElementById('passwordStrengthBar');
            const submitButton = document.getElementById('submitButton');
            const passwordMatchFeedback = document.getElementById('passwordMatchFeedback');

            // Elementos de requisitos
            const lengthReq = document.getElementById('lengthReq');
            const uppercaseReq = document.getElementById('uppercaseReq');
            const numberReq = document.getElementById('numberReq');
            const specialReq = document.getElementById('specialReq');

            // Alternar visibilidade da senha
            togglePasswordButton.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });

            // Alternar visibilidade da confirmação de senha
            toggleConfirmPasswordButton.addEventListener('click', function() {
                const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmPasswordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });

            // Verificar força da senha
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                checkPasswordStrength(password);
                validateForm();
            });

            // Verificar correspondência de senhas
            confirmPasswordInput.addEventListener('input', function() {
                validateForm();
            });

            function checkPasswordStrength(password) {
                let strength = 0;

                // Verificar comprimento
                if (password.length >= 8) {
                    strength += 25;
                    lengthReq.classList.add('requirement-met');
                } else {
                    lengthReq.classList.remove('requirement-met');
                }

                // Verificar letras maiúsculas
                if (/[A-Z]/.test(password)) {
                    strength += 25;
                    uppercaseReq.classList.add('requirement-met');
                } else {
                    uppercaseReq.classList.remove('requirement-met');
                }

                // Verificar números
                if (/[0-9]/.test(password)) {
                    strength += 25;
                    numberReq.classList.add('requirement-met');
                } else {
                    numberReq.classList.remove('requirement-met');
                }

                // Verificar caracteres especiais
                if (/[^A-Za-z0-9]/.test(password)) {
                    strength += 25;
                    specialReq.classList.add('requirement-met');
                } else {
                    specialReq.classList.remove('requirement-met');
                }

                // Atualizar barra de força
                passwordStrengthBar.style.width = strength + '%';

                // Atualizar cor da barra
                if (strength < 50) {
                    passwordStrengthBar.style.backgroundColor = '#dc3545'; // Vermelho
                } else if (strength < 75) {
                    passwordStrengthBar.style.backgroundColor = '#ffc107'; // Amarelo
                } else {
                    passwordStrengthBar.style.backgroundColor = '#28a745'; // Verde
                }
            }

            function validateForm() {
                const password = passwordInput.value;
                const confirmPassword = confirmPasswordInput.value;

                // Verificar se a senha atende aos requisitos
                const isPasswordStrong = password.length >= 8 &&
                    /[A-Z]/.test(password) &&
                    /[0-9]/.test(password) &&
                    /[^A-Za-z0-9]/.test(password);

                // Verificar se as senhas coincidem
                const passwordsMatch = password === confirmPassword;

                // Atualizar estado do botão
                submitButton.disabled = !(isPasswordStrong && passwordsMatch && password.length > 0);

                // Mostrar/ocultar feedback de correspondência
                if (confirmPassword.length > 0 && !passwordsMatch) {
                    confirmPasswordInput.classList.add('is-invalid');
                    passwordMatchFeedback.style.display = 'block';
                } else {
                    confirmPasswordInput.classList.remove('is-invalid');
                    passwordMatchFeedback.style.display = 'none';
                }
            }
        });
    </script>
</body>

</html>