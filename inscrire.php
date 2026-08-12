<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/connecter.css">
    <title>Connexion - College Gamal Nasser</title>
</head>

<body>
    <div class="particles" id="particles"></div>

    <div class="login-container">
        <div class="login-header">
            <h1>College Gammal Nasser</h1>
            <p>Connectez-vous pour accéder à votre espace</p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error'];
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form class="login-form" method="POST" action="requete.php">
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" placeholder="Entrez votre nom" required>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Entrez votre email" required>
            </div>

            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="mot_de_passe" placeholder="Entrez votre mot de passe" required>
            </div>

            <button type="submit" class="login-btn" name="inscrire">S'incrire</button>

            <div class="forgot-password">
                <a href="#">Mot de passe oublié ?</a>
            </div>
        </form>

        <div class="login-footer">
            &copy; 2025 Système de Gestion Scolaire. Tous droits réservés.
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 20;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');

                const size = Math.random() * 5 + 3;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;

                particle.style.left = `${Math.random() * 100}%`;
                particle.style.top = `${Math.random() * 100}%`;

                const duration = Math.random() * 20 + 10;
                particle.style.animation = `float ${duration}s linear infinite`;

                particle.style.animationDelay = `${Math.random() * 10}s`;

                particlesContainer.appendChild(particle);
            }

            const inputs = document.querySelectorAll('input[type="text"], input[type="password"], select');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.querySelector('label').style.color = 'var(--primary-color)';
                });

                input.addEventListener('blur', function() {
                    if (!this.value) {
                        this.parentElement.querySelector('label').style.color = 'rgba(255, 255, 255, 0.8)';
                    }
                });
            });

            const loginContainer = document.querySelector('.login-container');
            setTimeout(() => {
                loginContainer.style.opacity = '1';
                loginContainer.style.transform = 'translateY(0)';
            }, 100);

            loginContainer.style.opacity = '0';
            loginContainer.style.transform = 'translateY(20px)';
            loginContainer.style.transition = 'all 0.5s ease';
        });
    </script>
</body>

</html>