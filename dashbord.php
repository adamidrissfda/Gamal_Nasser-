<?php
session_start();
// // Vérification de l'authentification
// if (!isset($_SESSION['admin_logged_in'])) {
//     header('Location: connecter.php');
//     exit();
// }
// Récupération des statistiques
require_once 'database.php';
try {
    // Comptage des élèves
    $studentCount = $bd->query("SELECT COUNT(*) FROM eleves")->fetchColumn();

    // Comptage des enseignants
    $teacherCount = $bd->query("SELECT COUNT(*) FROM enseignants")->fetchColumn();

    // Comptage des matières
    $subjectCount = $bd->query("SELECT COUNT(*) FROM matieres")->fetchColumn();

    // Comptage des classes distinctes
    $classCount = $bd->query("SELECT COUNT(DISTINCT classe) FROM eleves")->fetchColumn();

    // Répartition par sexe
    // Dans la partie PHP, remplacez la requête genderStats par :
    $genderStats = $bd->query("SELECT 
    SUM(CASE WHEN sexe = 'M' THEN 1 ELSE 0 END) as male,
    SUM(CASE WHEN sexe = 'F' THEN 1 ELSE 0 END) as female
    FROM eleves")->fetch(PDO::FETCH_ASSOC);

    // Derniers élèves inscrits
    $recentTeachers = $bd->query("SELECT id, nom, prenom, matiere FROM enseignants ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    $recentStudents = $bd->query("SELECT id, nom, prenom, classe FROM eleves ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Log l'erreur et définir des valeurs par défaut
    error_log("Erreur de base de données: " . $e->getMessage());
    $studentCount = $teacherCount = $subjectCount = $classCount = 0;
    $genderStats = ['male' => 0, 'female' => 0];
    $recentStudents = [];
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Dashboard - Collège Gamal Nasser</title>
    <link rel="stylesheet" href="css/dashbord.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.css">
</head>

<body>
    <nav class="sidebar" aria-label="Menu latéral">
        <div class="sidebar-header">
            <img src="Images/logo1.jpg" alt="Logo du Collège Gamal Nasser">
            <h2>Collège Gamal Nasser</h2>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashbord.php" class="active"><i class="fas fa-home"></i><span>Accueil</span></a></li>
            <li><a href="eleve.php"><i class="fas fa-users"></i><span>Élèves</span></a></li>
            <li><a href="enseignant.php"><i class="fas fa-chalkboard-teacher"></i><span>Enseignants</span></a></li>
            <li><a href="classe.php"><i class="fas fa-school"></i><span>Classes</span></a></li>
            <li><a href="matiere.php"><i class="fas fa-book-open"></i><span>Matières</span></a></li>
            <li><a href="emploi_par_classe.php"><i class="fas fa-calendar-alt"></i><span>Emploi du temps</span></a></li>
            <li><a href="bulletin.php"><i class="fas fa-file-alt"></i><span>Bulletins</span></a></li>
            <li><a href="ajout_admin.php"><i class="fas fa-user-plus"></i><span>Ajouter Admin</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Déconnexion</span></a></li>
        </ul>
    </nav>

    <main class="main-content">
        <header>
            <button class="menu-toggle" id="menuToggle" aria-label="Ouvrir/fermer le menu"><i class="fas fa-bars"></i></button>
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Rechercher..." aria-label="Champ de recherche">
            </div>
            <div class="user-info">
                <img src="Images/profile.jpg" alt="Photo de profil de l'administrateur">
                <span class="user-name"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span>
            </div>
        </header>

        <section class="content">
            <h1 class="page-title"><i class="fas fa-home"></i> Tableau de Bord</h1>

            <div class="welcome-container">
                <div class="welcome-header">
                    <h2 class="welcome-title">Bienvenue, <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Administrateur') ?> !</h2>
                    <div class="welcome-image"><i class="fas fa-graduation-cap"></i></div>
                </div>
                <p class="welcome-subtitle">Gérez efficacement votre établissement scolaire avec notre plateforme intuitive et complète.</p>
            </div>

            <div class="stats-container">
                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <h3>Élèves</h3>
                        <span class="stat-number"><?= htmlspecialchars($studentCount) ?></span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-chalkboard-teacher"></i></div>
                    <div class="stat-info">
                        <h3>Enseignants</h3>
                        <span class="stat-number"><?= htmlspecialchars($teacherCount) ?></span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-book-open"></i></div>
                    <div class="stat-info">
                        <h3>Matières</h3>
                        <span class="stat-number"><?= htmlspecialchars($subjectCount) ?></span>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon"><i class="fas fa-school"></i></div>
                    <div class="stat-info">
                        <h3>Classes</h3>
                        <span class="stat-number"><?= htmlspecialchars($classCount) ?></span>
                    </div>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="recent-card">
                    <h3>Derniers Élèves Inscrits</h3>
                    <ul class="recent-list">
                        <?php foreach ($recentStudents as $student): ?>
                            <li class="recent-item">
                                <div class="student-info">
                                    <span class="student-name"><?= htmlspecialchars($student['nom'] . ' ' . $student['prenom']) ?></span>
                                    <span class="student-class"><?= htmlspecialchars($student['classe']) ?></span>
                                </div>
                                <a href="eleve.php?id=<?= htmlspecialchars($student['id']) ?>" class="view-link" title="Voir détails">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="recent-card">
                    <h3>Derniers Enseignants Ajoutes</h3>
                    <ul class="recent-list">
                        <?php foreach ($recentTeachers as $teacher): ?>
                            <li class="recent-item">
                                <div class="student-info">
                                    <span class="student-name"><?= htmlspecialchars($teacher['nom'] . ' ' . $student['prenom']) ?></span>
                                    <span class="student-class"><?= htmlspecialchars($teacher['matiere']) ?></span>
                                </div>
                                <a href="enseignant2.php?id=<?= htmlspecialchars($teacher['id']) ?>" class="view-link" title="Voir détails">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </section>
    </main>

    <div class="overlay" id="overlay"></div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gestion du menu mobile
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.getElementById('overlay');

            function toggleMenu() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
                document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
            }

            menuToggle.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleMenu();
            });

            overlay.addEventListener('click', toggleMenu);

            document.addEventListener('click', function(e) {
                if (!sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
            window.addEventListener('resize', function() {
                if (window.innerWidth > 992) {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                    document.body.style.overflow = '';
                }
            });
        });
    </script>
</body>

</html>