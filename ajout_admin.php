<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <title>Ajouter Admin - College Gamal Nasser</title>
</head>
<style>
    :root {
        --primary-color: #4a6baf;
        --error-color: #e94f64;
        --success-color: #28a745;
        --warning-color: #ffc107;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    body {
        background: linear-gradient(135deg, #2c3e50, #4a6baf);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        color: white;
        padding: 20px;
    }

    .admin-container {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 25px;
        width: 100%;
        max-width: 450px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        z-index: 1;
        animation: fadeIn 0.4s ease forwards;
    }

    .admin-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .admin-header h1 {
        font-size: 1.5rem;
        margin-bottom: 8px;
    }

    .alert-message {
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.9rem;
        animation: slideIn 0.3s ease-out;
    }

    .alert-error {
        background-color: rgba(233, 79, 100, 0.2);
        border-left: 4px solid var(--error-color);
    }

    .alert-success {
        background-color: rgba(40, 167, 69, 0.2);
        border-left: 4px solid var(--success-color);
    }

    .form-group {
        margin-bottom: 15px;
        position: relative;
    }

    .form-group label {
        display: block;
        margin-bottom: 6px;
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.9);
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.1);
        color: white;
        font-size: 0.9rem;
        transition: all 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(74, 107, 175, 0.3);
    }

    .is-invalid {
        border-color: var(--error-color) !important;
    }

    .invalid-feedback {
        color: var(--error-color);
        font-size: 0.8rem;
        margin-top: 5px;
        display: none;
    }

    .select-wrapper {
        position: relative;
        margin-bottom: 1rem;
    }

    .select-wrapper::after {
        content: "⌄";
        position: absolute;
        top: 50%;
        right: 1rem;
        transform: translateY(-50%);
        color: var(--text);
        font-size: 1.1rem;
        pointer-events: none;
        transition: all 0.3s ease;
        z-index: 2;
    }

    .select-wrapper:hover::after {
        color: var(--primary);
        transform: translateY(-50%) scale(1.2);
    }

    .select-wrapper select {
        width: 100%;
        padding: 12px 16px;
        padding-right: 3rem;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.1);
        color: var(--text);
        font-size: 1rem;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .select-wrapper select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(74, 107, 175, 0.2);
    }

    .select-wrapper select option {
        background: #2c3e50;
        color: white;
        padding: 12px;
    }

    .select-wrapper.is-invalid::after {
        color: var(--error);
        animation: shake 0.5s ease;
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateY(-50%) translateX(0);
        }

        20%,
        60% {
            transform: translateY(-50%) translateX(-5px);
        }

        40%,
        80% {
            transform: translateY(-50%) translateX(5px);
        }
    }

    .btn {
        width: 100%;
        padding: 10px;
        border: none;
        border-radius: 6px;
        background: var(--primary-color);
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn:hover {
        background: #3a5a9f;
        transform: translateY(-1px);
    }

    .back-link {
        text-align: center;
        margin-top: 15px;
    }

    .back-link a {
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
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

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-10px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @media (max-width: 480px) {
        .admin-container {
            padding: 20px;
        }

        .admin-header h1 {
            font-size: 1.3rem;
        }
    }
</style>

<body>
    <?php
    // session_start();

    // Initialisation des variables d'erreur
    $email_error = $password_error = $confirm_error = $role_error = $general_error = $success_msg = '';

    if (isset($_POST['ajouter_admin'])) {
        $email = trim($_POST['email']);
        $mot_de_passe = $_POST['mot_de_passe'];
        $confirmation = $_POST['confirmation'];
        $user_type = $_POST['user_type'] ?? '';

        // Validation des champs
        if (empty($email)) {
            $email_error = "L'email est requis";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_error = "Format d'email invalide";
        }

        if (empty($mot_de_passe)) {
            $password_error = "Le mot de passe est requis";
        } elseif (strlen($mot_de_passe) < 6) {
            $password_error = "Le mot de passe doit contenir au moins 6 caractères";
        }

        if (empty($confirmation)) {
            $confirm_error = "La confirmation est requise";
        } elseif ($mot_de_passe !== $confirmation) {
            $confirm_error = "Les mots de passe ne correspondent pas";
        }

        if (empty($user_type)) {
            $role_error = "Veuillez sélectionner un rôle";
        }

        // Si aucune erreur de validation
        if (empty($email_error) && empty($password_error) && empty($confirm_error) && empty($role_error)) {
            include 'database.php';

            try {
                // Vérifier si l'email existe déjà
                $req = $bd->prepare('SELECT id FROM administration WHERE email = ?');
                $req->execute([$email]);

                if ($req->fetch()) {
                    $email_error = "Cet email est déjà utilisé";
                } else {
                    // Insertion dans la base de données
                    $req = $bd->prepare('INSERT INTO administration (email, mot_de_passe, role) VALUES (?, ?, ?)');
                    if ($req->execute([$email, $mot_de_passe, $user_type])) {
                        $success_msg = "Compte $user_type créé avec succès";
                        // Réinitialiser les champs après succès
                        $_POST = array();
                    } else {
                        $general_error = "Une erreur est survenue lors de la création du compte";
                    }
                }
            } catch (PDOException $e) {
                $general_error = "Erreur de base de données: " . $e->getMessage();
            }
        } else {
            $general_error = "Veuillez corriger les erreurs dans le formulaire";
        }
    }
    ?>

    <div class="admin-container">
        <div class="admin-header">
            <h1><i class="fas fa-user-shield"></i> Nouveau Compte</h1>
            <p>Créez un compte administrateur ou enseignant</p>
        </div>

        <?php if (!empty($general_error)): ?>
            <div class="alert-message alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= htmlspecialchars($general_error) ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_msg)): ?>
            <div class="alert-message alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?= htmlspecialchars($success_msg) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <!-- Champ Email -->
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" id="email" name="email" class="form-control <?= !empty($email_error) ? 'is-invalid' : '' ?>"
                    value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    placeholder="exemple@ecole.edu">
                <?php if (!empty($email_error)): ?>
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-triangle"></i> <?= $email_error ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Champ Mot de passe -->
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Mot de passe</label>
                <input type="password" id="password" name="mot_de_passe"
                    class="form-control <?= !empty($password_error) ? 'is-invalid' : '' ?>"
                    placeholder="Minimum 6 caractères">
                <?php if (!empty($password_error)): ?>
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-triangle"></i> <?= $password_error ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Champ Confirmation -->
            <div class="form-group">
                <label for="confirm_password"><i class="fas fa-lock"></i> Confirmation</label>
                <input type="password" id="confirm_password" name="confirmation"
                    class="form-control <?= !empty($confirm_error) ? 'is-invalid' : '' ?>"
                    placeholder="Retapez votre mot de passe">
                <?php if (!empty($confirm_error)): ?>
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-triangle"></i> <?= $confirm_error ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Champ Rôle -->
            <div class="form-group">
                <label for="user_type"><i class="fas fa-user-tag"></i> Rôle</label>
                <select id="user_type" name="user_type" class="form-control <?= !empty($role_error) ? 'is-invalid' : '' ?>">
                    <option value="" disabled selected>-- Sélectionnez --</option>
                    <option value="admin" <?= ($_POST['user_type'] ?? '') == 'admin' ? 'selected' : '' ?>>Administrateur</option>
                    <option value="enseignant" <?= ($_POST['user_type'] ?? '') == 'enseignant' ? 'selected' : '' ?>>Enseignant</option>
                </select>
                <?php if (!empty($role_error)): ?>
                    <div class="invalid-feedback">
                        <i class="fas fa-exclamation-triangle"></i> <?= $role_error ?>
                    </div>
                <?php endif; ?>
            </div>

            <button type="submit" name="ajouter_admin" class="btn">
                <i class="fas fa-user-plus"></i> Créer le compte
            </button>

            <div class="back-link">
                <a href="dashbord.php"><i class="fas fa-arrow-left"></i> Retour au tableau de bord</a>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Afficher les messages d'erreur
            const invalidFields = document.querySelectorAll('.is-invalid');
            invalidFields.forEach(field => {
                const feedback = field.parentElement.querySelector('.invalid-feedback');
                if (feedback) feedback.style.display = 'block';
            });

            // Gestion des interactions
            const formControls = document.querySelectorAll('.form-control');
            formControls.forEach(control => {
                control.addEventListener('focus', function() {
                    this.parentElement.querySelector('label').style.color = 'var(--primary-color)';
                    this.classList.remove('is-invalid');
                    const feedback = this.parentElement.querySelector('.invalid-feedback');
                    if (feedback) feedback.style.display = 'none';
                });

                control.addEventListener('blur', function() {
                    this.parentElement.querySelector('label').style.color = '';
                });
            });
        });
    </script>
</body>

</html>