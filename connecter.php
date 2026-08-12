<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Page de connexion pour le Collège Gamal Nasser">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Connexion - Collège Gamal Nasser</title>
    <style>
        :root {
            --primary-color: #4a6baf;
            --primary-hover: #3a5a9f;
            --error-color: #e94f64;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --text-light: rgba(255, 255, 255, 0.9);
            --text-muted: rgba(255, 255, 255, 0.7);
            --bg-dark: #2c3e50;
            --bg-light: rgba(255, 255, 255, 0.1);
            --border-light: rgba(255, 255, 255, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--bg-dark), var(--primary-color));
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 20px;
            line-height: 1.6;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 12px;
            padding: 30px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            z-index: 1;
            animation: fadeIn 0.5s ease forwards;
            border: 1px solid var(--border-light);
        }

        .login-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .login-header h1 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .login-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .alert-message {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 0.95rem;
            animation: slideIn 0.3s ease-out;
        }

        .alert-error {
            background-color: rgba(233, 79, 100, 0.25);
            border-left: 4px solid var(--error-color);
            color: var(--text-light);
        }

        .form-group {
            margin-bottom: 18px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.95rem;
            color: var(--text-light);
            transition: color 0.2s;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-light);
            border-radius: 8px;
            background: var(--bg-light);
            color: white;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .form-control::placeholder {
            color: var(--text-muted);
            opacity: 0.7;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(74, 107, 175, 0.25);
            background: rgba(255, 255, 255, 0.15);
        }

        .is-invalid {
            border-color: var(--error-color) !important;
        }

        .invalid-feedback {
            color: var(--error-color);
            font-size: 0.85rem;
            margin-top: 6px;
            display: none;
        }

        .select-wrapper {
            position: relative;
            transition: all 0.3s ease;
        }

        .select-wrapper::after {
            content: "▼";
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 0.9rem;
            pointer-events: none;
            z-index: 2;
            transition: all 0.2s;
        }

        .select-wrapper select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            padding-right: 40px;
            cursor: pointer;
        }

        .select-wrapper:hover::after {
            color: var(--primary-color);
        }

        .select-wrapper select option {
            background-color: var(--bg-dark);
            color: white;
            padding: 12px;
        }

        .select-wrapper select option[value=""][disabled] {
            color: var(--text-muted);
        }

        .btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            background: var(--primary-color);
            color: white;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        .btn:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn:active {
            transform: translateY(0);
        }

        .forgot-password {
            text-align: center;
            margin-top: 20px;
        }

        .forgot-password a {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: color 0.2s;
        }

        .forgot-password a:hover {
            color: var(--primary-color);
            text-decoration: underline;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 25px 20px;
            }

            .login-header h1 {
                font-size: 1.5rem;
            }
            
            .form-control {
                padding: 10px 12px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-graduation-cap"></i> Collège Gamal Nasser</h1>
            <p>Connectez-vous pour accéder à votre espace</p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert-message alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST" action="requete.php" autocomplete="on">
            <!-- Champ Email -->
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" id="email" name="email" class="form-control"
                    placeholder="exemple@ecole.edu" required
                    autocomplete="username">
            </div>

            <!-- Champ Mot de passe -->
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Mot de passe</label>
                <input type="password" id="password" name="mot_de_passe"
                    class="form-control"
                    placeholder="Entrez votre mot de passe" required
                    autocomplete="current-password">
            </div>

            <!-- Champ Rôle -->
            <div class="form-group">
                <label for="user_type"><i class="fas fa-user-tag"></i> Rôle</label>
                <div class="select-wrapper">
                    <select id="user_type" name="user_type" class="form-control" required>
                        <option value="" disabled selected>-- Sélectionnez votre rôle --</option>
                        <option value="admin">Administrateur</option>
                        <option value="enseignant">Enseignant</option>
                    </select>
                </div>
            </div>

            <button type="submit" name="connecter" class="btn">
                <i class="fas fa-sign-in-alt"></i> Se connecter
            </button>

            <div class="forgot-password">
                <a href="#"><i class="fas fa-key"></i> Mot de passe oublié ?</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Amélioration des interactions
            const formControls = document.querySelectorAll('.form-control');
            
            formControls.forEach(control => {
                // Gestion du focus
                control.addEventListener('focus', function() {
                    const label = this.parentElement.querySelector('label');
                    if (label) {
                        label.style.color = 'var(--primary-color)';
                    }
                    this.style.borderColor = 'var(--primary-color)';
                });

                // Gestion du blur
                control.addEventListener('blur', function() {
                    const label = this.parentElement.querySelector('label');
                    if (label) {
                        label.style.color = 'var(--text-light)';
                    }
                    this.style.borderColor = 'var(--border-light)';
                });

                // Animation au chargement pour les champs
                setTimeout(() => {
                    control.style.opacity = '1';
                    control.style.transform = 'translateY(0)';
                }, 100);
            });

            // Validation en temps réel
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    let isValid = true;
                    
                    // Validation basique côté client
                    formControls.forEach(control => {
                        if (control.required && !control.value.trim()) {
                            isValid = false;
                            control.classList.add('is-invalid');
                        }
                    });
                    
                    if (!isValid) {
                        e.preventDefault();
                        alert('Veuillez remplir tous les champs obligatoires.');
                    }
                });
            }
        });
    </script>
</body>

</html>