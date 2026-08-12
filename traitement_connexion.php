<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['connecter'])) {
    // Récupération des données du formulaire
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['mot_de_passe'] ?? '';
    $role = $_POST['user_type'] ?? '';
    
    // Initialisation du tableau d'erreurs
    $errors = [];
    
    // Validation de l'email
    if (empty($email)) {
        $errors['email'] = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Format d'email invalide";
    }
    
    // Validation du mot de passe
    if (empty($password)) {
        $errors['password'] = "Le mot de passe est requis";
    } elseif (strlen($password) < 4) {
        $errors['password'] = "Le mot de passe doit contenir au moins 4 caractères";
    }
    
    // Validation du rôle
    if (empty($role)) {
        $errors['role'] = "Veuillez sélectionner un rôle";
    } elseif (!in_array($role, ['admin', 'enseignant'])) {
        $errors['role'] = "Rôle invalide";
    }
    
    // Si aucune erreur de validation
    if (empty($errors)) {
        // Connexion à la base de données (à adapter)
        include 'database.php';
        
        try {
            // Vérification des identifiants
            $req = $bd->prepare('SELECT * FROM administration WHERE email = ? AND role = ?');
            $req->execute([$email, $role]);
            $user = $req->fetch();
            
            if ($user && password_verify($password, $user['mot_de_passe'])) {
                // Authentification réussie
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ];
                
                // Redirection selon le rôle
                if ($user['role'] === 'admin') {
                    header('Location: dashboard.php');
                } else {
                    header('Location: dashboard2.php');
                }
                exit;
            } else {
                $errors['general'] = "Identifiants incorrects ou rôle invalide";
            }
        } catch (PDOException $e) {
            $errors['general'] = "Erreur de base de données : " . $e->getMessage();
        }
    }
    
    // Stockage des erreurs et des valeurs soumises en session
    $_SESSION['login_errors'] = $errors;
    $_SESSION['submitted_email'] = $email;
    $_SESSION['submitted_role'] = $role;
    
    // Redirection vers la page de connexion
    header('Location: connecter.php');
    exit;
}

// Si accès direct au fichier, redirection vers la page de connexion
header('Location: connecter.php');
exit;
?>