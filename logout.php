<?php
session_start();

// Détruire toutes les variables de session
$_SESSION = array();

// Effacer le cookie de session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Détruire la session
session_destroy();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déconnexion - College Gamal Nasser</title>
    <link rel="stylesheet" href="css/logout.css">
</head>
<body>
    <div class="particles" id="particles"></div>
    
    <div class="logout-container">
        <div class="logout-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0v2z"/>
                <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3z"/>
            </svg>
        </div>
        
        <div class="logout-message">
            <h2>Vous êtes déconnecté</h2>
            <p>Vous avez été déconnecté avec succès du système de gestion scolaire.</p>
        </div>
        
        <a href="index.php" class="login-again-btn">Se reconnecter</a>
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
        });
    </script>
</body>
</html>