<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['connecter'])) {
    // Nettoyer les entrées
    $email = trim($_POST['email'] ?? '');
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $user_type = $_POST['user_type'] ?? '';
    
    // Initialisation du tableau d'erreurs
    $errors = [];
    
    // Validation de l'email
    if (empty($email)) {
        $errors['email'] = "L'email est requis";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Format d'email invalide";
    }
    
    // Validation du mot de passe
    if (empty($mot_de_passe)) {
        $errors['password'] = "Le mot de passe est requis";
    }
    
    // Validation du rôle
    if (empty($user_type)) {
        $errors['role'] = "Veuillez sélectionner un rôle";
    }
    
    // Si validation OK
    if (empty($errors)) {
        try {
            // Vérification dans la base de données
            $req = $bd->prepare('SELECT * FROM administration WHERE email = :email AND role = :role');
            $req->execute(['email' => $email, 'role' => $user_type]);
            $user = $req->fetch();
            
            if ($user) {
                // Vérification du mot de passe
                if ($user['mot_de_passe'] === $mot_de_passe) {
                    // Connexion réussie
                    $_SESSION['user'] = [
                        'id' => $user['id'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ];
                    
                    // Redirection selon le rôle
                    $redirect = ($user_type == 'admin') ? 'dashbord.php' : 'dashbord2.php';
                    header("Location: $redirect");
                    exit();
                } else {
                    $_SESSION['error'] = "Mot de passe incorrect";
                }
            } else {
                $_SESSION['error'] = "Identifiants incorrects ou vous n'avez pas les droits pour ce rôle";
            }
        } catch (PDOException $e) {
            $_SESSION['error'] = "Erreur de connexion à la base de données";
        }
    } else {
        $_SESSION['error'] = "Veuillez corriger les erreurs dans le formulaire";
    }
    
    // Redirection vers le formulaire en cas d'erreur
    header('Location: connecter.php');
    exit();
}

// Si accès direct au script, redirection
header('Location: connecter.php');



//ajout eleve

if (isset($_POST['enregistre_eleve'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $date_naissance = $_POST['date_naissance'];
    // $lieu_naissance=$_POST['lieu_naissance'];
    $sexe = $_POST['sexe'];
    $classe = $_POST['classe'];
    $adresse = $_POST['adresse'];
    $tel_parent = $_POST['tel_parent'];

    $req = $bd->prepare('INSERT INTO eleves(nom, prenom,date_naissance,sexe,classe,adresse,tel_parent) VALUES(:nom, :prenom, :date_naissance, :sexe, :classe, :adresse, :tel_parent)');
    $req->execute(['nom' => $nom, 'prenom' => $prenom, 'date_naissance' => $date_naissance, 'sexe' => $sexe, 'classe' => $classe, 'adresse' => $adresse, 'tel_parent' => $tel_parent]);
    header('location:eleve.php');
}
//modifier l'eleve
if (isset($_POST['modifier_eleve'])) {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $date_naissance = $_POST['date_naissance'];
    // $lieu_naissance=$_POST['lieu_naissance'];
    $sexe = $_POST['sexe'];
    $classe = $_POST['classe'];
    $adresse = $_POST['adresse'];
    $tel_parent = $_POST['tel_parent'];

    $req = $bd->prepare('UPDATE eleves SET nom=:nom, prenom=:prenom,date_naissance=:date_naissance,sexe=:sexe,classe=:classe,adresse=:adresse,tel_parent=:tel_parent WHERE id=:id');
    $req->execute(['nom' => $nom, 'prenom' => $prenom, 'date_naissance' => $date_naissance, 'sexe' => $sexe, 'classe' => $classe, 'adresse' => $adresse, 'tel_parent' => $tel_parent, 'id' => $id]);
    header('location:eleve.php');
}

//supprimer eleves

if (isset($_POST['supprimer_eleve'])) {
    $id = $_POST['id'];
    $req = $bd->prepare('DELETE FROM eleves WHERE id=:id');
    $req->execute(['id' => $id]);
    header('location:eleve.php');
}



//ajout enseignant

if (isset($_POST['enregistre_enseignant'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $sexe = $_POST['sexe'];
    $niveau_etude = $_POST['niveau_etude'];
    $matiere = $_POST['matiere'];
    $adresse = $_POST['adresse'];
    $tel = $_POST['tel'];


    $req = $bd->prepare('INSERT INTO enseignants(nom, prenom, sexe, niveau_etude, matiere, adresse, tel) VALUES(:nom, :prenom, :sexe, :niveau_etude, :matiere, :adresse, :tel)');
    $req->execute(['nom' => $nom, 'prenom' => $prenom, 'sexe' => $sexe, 'niveau_etude' => $niveau_etude, 'matiere' => $matiere, 'adresse' => $adresse, 'tel' => $tel]);
    header('location:enseignant.php');
}

//modifier l'enseignant
if (isset($_POST['modifier_enseignant'])) {
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $sexe = $_POST['sexe'];
    $niveau_etude = $_POST['niveau_etude'];
    $matiere = $_POST['matiere'];
    $adresse = $_POST['adresse'];
    $tel = $_POST['tel'];

    $req = $bd->prepare('UPDATE enseignants SET nom=:nom, prenom=:prenom, sexe=:sexe, niveau_etude=:niveau_etude, matiere=:matiere, adresse=:adresse, tel=:tel WHERE id=:id');
    $req->execute(['nom' => $nom, 'prenom' => $prenom,  'sexe' => $sexe, 'niveau_etude' => $niveau_etude, 'matiere' => $matiere, 'adresse' => $adresse, 'tel' => $tel, 'id' => $id]);
    header('location:enseignant.php');
}

//supprimer enseignants

if (isset($_POST['supprimer_enseignant'])) {
    $id = $_POST['id'];
    $req = $bd->prepare('DELETE FROM enseignants WHERE id=:id');
    $req->execute(['id' => $id]);
    header('location:enseignant.php');
}


// Gestion des élèves (existant déjà dans votre fichier)
// ...

// Gestion des notes
if (isset($_POST['ajouter_note'])) {
    $eleve_id = $_POST['eleve_id'];
    $matiere_id = $_POST['matiere_id'];
    $note = $_POST['note'];
    $type_note = $_POST['type_note'];
    $date_note = $_POST['date_note'];

    $req = $bd->prepare('INSERT INTO notes (eleve_id, matiere_id, note, type_note, date_note) 
                        VALUES (?, ?, ?, ?, ?)');
    $req->execute([$eleve_id, $matiere_id, $note, $type_note, $date_note]);

    header("Location: notes.php?id=$eleve_id");
    exit();
}

if (isset($_POST['modifier_note'])) {
    $id = $_POST['id'];
    $eleve_id = $_POST['eleve_id'];
    $matiere_id = $_POST['matiere_id'];
    $note = $_POST['note'];
    $type_note = $_POST['type_note'];
    $date_note = $_POST['date_note'];

    $req = $bd->prepare('UPDATE notes 
                        SET matiere_id = ?, note = ?, type_note = ?, date_note = ? 
                        WHERE id = ?');
    $req->execute([$matiere_id, $note, $type_note, $date_note, $id]);

    header("Location: notes.php?id=$eleve_id");
    exit();
}

if (isset($_POST['supprimer_note'])) {
    $id = $_POST['id'];

    // On récupère l'élève_id avant de supprimer pour la redirection
    $req = $bd->prepare('SELECT eleve_id FROM notes WHERE id = ?');
    $req->execute([$id]);
    $note = $req->fetch();
    $eleve_id = $note['eleve_id'];

    $req = $bd->prepare('DELETE FROM notes WHERE id = ?');
    $req->execute([$id]);

    header("Location: notes.php?id=$eleve_id");
    exit();
}



// Emploi

session_start();
include 'database.php';

function redirect($messageType, $message)
{
    $_SESSION[$messageType] = $message;
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

// Ajouter un cours
if (isset($_POST['ajouter_cours'])) {
    try {
        $jour = $_POST['jour'];
        $heure_debut = $_POST['heure_debut'];
        $heure_fin = $_POST['heure_fin'];
        $matiere = $_POST['matiere'];
        $enseignant_id = !empty($_POST['enseignant_id']) ? $_POST['enseignant_id'] : NULL;
        $classe = $_POST['classe'];

        // Validation des heures
        if (strtotime($heure_fin) <= strtotime($heure_debut)) {
            redirect('error', 'L\'heure de fin doit être après l\'heure de début');
        }

        // Vérifier conflit de salle
        $req = $bd->prepare("SELECT id FROM emploi_du_temps 
                            WHERE jour = ? 
                            -- AND salle = ? 
                            AND classe = ?
                            AND ((heure_debut < ? AND heure_fin > ?) 
                            OR (heure_debut < ? AND heure_fin > ?) 
                            OR (heure_debut >= ? AND heure_fin <= ?))");
        $req->execute([$jour, $classe, $heure_fin, $heure_debut, $heure_fin, $heure_debut, $heure_debut, $heure_fin]);

        if ($req->fetch()) {
            redirect('error', 'La salle est déjà occupée pendant ce créneau pour cette classe');
        }

        // Vérifier conflit enseignant
        if ($enseignant_id) {
            $req = $bd->prepare("SELECT id FROM emploi_du_temps 
                                WHERE jour = ? 
                                AND enseignant_id = ? 
                                AND ((heure_debut < ? AND heure_fin > ?) 
                                OR (heure_debut < ? AND heure_fin > ?) 
                                OR (heure_debut >= ? AND heure_fin <= ?))");
            $req->execute([$jour, $enseignant_id, $heure_fin, $heure_debut, $heure_fin, $heure_debut, $heure_debut, $heure_fin]);

            if ($req->fetch()) {
                redirect('error', 'L\'enseignant a déjà un cours pendant ce créneau');
            }
        }

        // Insertion
        $req = $bd->prepare("INSERT INTO emploi_du_temps 
                            (jour, heure_debut, heure_fin, matiere, enseignant_id,  classe) 
                            VALUES (?, ?, ?, ?, ?, ?)");
        $req->execute([$jour, $heure_debut, $heure_fin, $matiere, $enseignant_id,  $classe]);

        redirect('success', 'Cours ajouté avec succès');
    } catch (PDOException $e) {
        redirect('error', 'Erreur lors de l\'ajout du cours: ' . $e->getMessage());
    }
}

// Modifier un cours
if (isset($_POST['modifier_cours'])) {
    try {
        $id = $_POST['id'];
        $jour = $_POST['jour'];
        $heure_debut = $_POST['heure_debut'];
        $heure_fin = $_POST['heure_fin'];
        $matiere = $_POST['matiere'];
        $enseignant_id = !empty($_POST['enseignant_id']) ? $_POST['enseignant_id'] : NULL;
        // $salle = $_POST['salle'];
        $classe = $_POST['classe'];

        // Validation des heures
        if (strtotime($heure_fin) <= strtotime($heure_debut)) {
            redirect('error', 'L\'heure de fin doit être après l\'heure de début');
        }

        // Vérifier conflit de salle (exclure le cours actuel)
        $req = $bd->prepare("SELECT id FROM emploi_du_temps 
                            WHERE jour = ? 
                            -- AND salle = ? 
                            AND classe = ?
                            AND id != ?
                            AND ((heure_debut < ? AND heure_fin > ?) 
                            OR (heure_debut < ? AND heure_fin > ?) 
                            OR (heure_debut >= ? AND heure_fin <= ?))");
        $req->execute([$jour,  $classe, $id, $heure_fin, $heure_debut, $heure_fin, $heure_debut, $heure_debut, $heure_fin]);

        if ($req->fetch()) {
            redirect('error', 'La salle est déjà occupée pendant ce créneau pour cette classe');
        }

        // Vérifier conflit enseignant (exclure le cours actuel)
        if ($enseignant_id) {
            $req = $bd->prepare("SELECT id FROM emploi_du_temps 
                                WHERE jour = ? 
                                AND enseignant_id = ? 
                                AND id != ?
                                AND ((heure_debut < ? AND heure_fin > ?) 
                                OR (heure_debut < ? AND heure_fin > ?) 
                                OR (heure_debut >= ? AND heure_fin <= ?))");
            $req->execute([$jour, $enseignant_id, $id, $heure_fin, $heure_debut, $heure_fin, $heure_debut, $heure_debut, $heure_fin]);

            if ($req->fetch()) {
                redirect('error', 'L\'enseignant a déjà un cours pendant ce créneau');
            }
        }

        // Mise à jour
        $req = $bd->prepare("UPDATE emploi_du_temps 
                            SET jour = ?, heure_debut = ?, heure_fin = ?, 
                            matiere = ?, enseignant_id = ?, classe = ?
                            WHERE id = ?");
        $req->execute([$jour, $heure_debut, $heure_fin, $matiere, $enseignant_id, $classe, $id]);

        redirect('success', 'Cours modifié avec succès');
    } catch (PDOException $e) {
        redirect('error', 'Erreur lors de la modification du cours: ' . $e->getMessage());
    }
}

// Supprimer un cours
if (isset($_POST['supprimer_cours'])) {
    try {
        $id = $_POST['id'];

        $req = $bd->prepare("DELETE FROM emploi_du_temps WHERE id = ?");
        $req->execute([$id]);

        redirect('success', 'Cours supprimé avec succès');
    } catch (PDOException $e) {
        redirect('error', 'Erreur lors de la suppression du cours: ' . $e->getMessage());
    }
}

// API pour récupérer un cours
if (isset($_GET['id'])) {
    try {
        $req = $bd->prepare("SELECT * FROM emploi_du_temps WHERE id = ?");
        $req->execute([$_GET['id']]);
        $cours = $req->fetch(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        echo json_encode($cours);
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit();
}

include 'database.php';

// Traitement des requêtes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['enregistre_matiere'])) {
        // Ajout d'une nouvelle matière
        $nom = $_POST['nom'] ?? '';
        $coefficient = $_POST['coefficient'] ?? 1;
        
        if (!empty($nom)) {
            try {
                $req = $bd->prepare('INSERT INTO matieres (nom, coefficient) VALUES (?, ?)');
                $req->execute([$nom, $coefficient]);
                
                header('Location: matiere.php?success=1');
                exit;
            } catch (PDOException $e) {
                header('Location: matiere.php?error=1');
                exit;
            }
        } else {
            header('Location: matiere.php?error=2');
            exit;
        }
    }
    elseif (isset($_POST['modifier_matiere'])) {
        // Modification d'une matière
        $id = $_POST['id'] ?? 0;
        $nom = $_POST['nom'] ?? '';
        $coefficient = $_POST['coefficient'] ?? 1;
        
        if (!empty($nom) && $id > 0) {
            try {
                $req = $bd->prepare('UPDATE matieres SET nom = ?, coefficient = ? WHERE id = ?');
                $req->execute([$nom, $coefficient, $id]);
                
                header('Location: matiere.php?success=2');
                exit;
            } catch (PDOException $e) {
                header('Location: matiere.php?error=3');
                exit;
            }
        } else {
            header('Location: matiere.php?error=4');
            exit;
        }
    }
    elseif (isset($_POST['supprimer_matiere'])) {
        // Suppression d'une matière
        $id = $_POST['id'] ?? 0;
        
        if ($id > 0) {
            try {
                $req = $bd->prepare('DELETE FROM matieres WHERE id = ?');
                $req->execute([$id]);
                
                header('Location: matiere.php?success=3');
                exit;
            } catch (PDOException $e) {
                header('Location: matiere.php?error=5');
                exit;
            }
        } else {
            header('Location: matiere.php?error=6');
            exit;
        }
    }

}

exit;
