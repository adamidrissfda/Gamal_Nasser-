<?php
include 'database.php';

// Récupération des classes disponibles
$reqClasses = $bd->prepare('SELECT DISTINCT classe FROM eleves ORDER BY classe');
$reqClasses->execute();
$classes = $reqClasses->fetchAll(PDO::FETCH_COLUMN);

// Récupération de la classe sélectionnée (par défaut la première)
$classeSelectionnee = $_GET['classe'] ?? ($classes[0] ?? null);

// Récupération des cours pour la classe sélectionnée
$cours = [];
if ($classeSelectionnee) {
    $req = $bd->prepare('SELECT e.*, en.nom AS enseignant_nom, en.prenom AS enseignant_prenom 
                         FROM emploi_du_temps e 
                         LEFT JOIN enseignants en ON e.enseignant_id = en.id 
                         WHERE e.classe = ? 
                         ORDER BY 
                             CASE e.jour 
                                 WHEN "Lundi" THEN 1 
                                 WHEN "Mardi" THEN 2 
                                 WHEN "Mercredi" THEN 3 
                                 WHEN "Jeudi" THEN 4 
                                 WHEN "Vendredi" THEN 5 
                                 WHEN "Samedi" THEN 6 
                                 ELSE 7 
                             END, 
                             e.heure_debut');
    $req->execute([$classeSelectionnee]);
    $cours = $req->fetchAll(PDO::FETCH_ASSOC);
}

// Récupération des enseignants pour les select
$req = $bd->prepare('SELECT id, nom, prenom FROM enseignants ORDER BY nom, prenom');
$req->execute();
$enseignants = $req->fetchAll(PDO::FETCH_ASSOC);

// Récupération des matières
$req = $bd->prepare('SELECT nom FROM matieres ORDER BY nom');
$req->execute();
$matieres = $req->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Emploi du Temps par Classe</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Style de base amélioré */
        :root {
            --primary-color: #4361ee;
            --primary-hover: #3a56d4;
            --secondary-color: #6c757d;
            --secondary-hover: #5a6268;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --border-color: #e9ecef;
            --text-color: #212529;
            --text-muted: #6c757d;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background-color: #f5f7fa;
            color: var(--text-color);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* En-tête */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .page-title {
            font-size: 1.75rem;
            color: var(--dark-color);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
        }
        
        /* Cartes */
        .card {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-bottom: 25px;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .card:hover {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            padding: 18px 25px;
            border-bottom: 1px solid var(--border-color);
            background-color: #f8f9fa;
        }
        
        .card-title {
            font-size: 1.25rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
            color: var(--dark-color);
        }
        
        .card-body {
            padding: 25px;
        }
        
        /* Boutons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 18px;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            gap: 8px;
            text-decoration: none;
        }
        
        .btn i {
            font-size: 0.9em;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: var(--secondary-hover);
            transform: translateY(-1px);
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            color: var(--dark-color);
        }
        
        .btn-warning:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        
        .btn-danger {
            background-color: var(--danger-color);
            color: white;
        }
        
        .btn-danger:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }
        
        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8125rem;
        }
        
        /* Onglets */
        .tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 8px 16px;
            background-color: #e9ecef;
            border-radius: 6px;
            text-decoration: none;
            color: var(--text-color);
            font-weight: 500;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }
        
        .tab:hover {
            background-color: #dee2e6;
        }
        
        .tab.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        /* Emploi du temps */
        .emploi-container {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        
        .jour-container {
            border: 1px solid var(--border-color);
            border-radius: 10px;
            overflow: hidden;
            transition: transform 0.2s;
        }
        
        .jour-container:hover {
            transform: translateY(-2px);
        }
        
        .jour-title {
            background-color: #f8f9fa;
            padding: 12px 20px;
            margin: 0;
            font-size: 1.1rem;
            border-bottom: 1px solid var(--border-color);
            font-weight: 500;
            color: var(--dark-color);
        }
        
        .cours-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            padding: 20px;
        }
        
        .cours-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background-color: white;
            border-radius: 8px;
            gap: 20px;
            border: 1px solid var(--border-color);
            transition: all 0.2s ease;
        }
        
        .cours-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-color: #d0d7de;
        }
        
        .cours-heure {
            min-width: 110px;
            font-weight: 500;
            color: var(--dark-color);
            font-size: 0.95rem;
        }
        
        .cours-details {
            flex: 1;
        }
        
        .cours-matiere {
            font-weight: 500;
            color: var(--dark-color);
            margin-bottom: 3px;
        }
        
        .cours-enseignant, .cours-salle {
            font-size: 0.85rem;
            color: var(--text-muted);
        }
        
        .cours-actions {
            display: flex;
            gap: 8px;
        }
        
        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1050;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(2px);
        }
        
        .modal-content {
            background-color: white;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            animation: modalFadeIn 0.3s;
            border: none;
        }
        
        @keyframes modalFadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .modal-header {
            padding: 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-title {
            font-size: 1.4rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 500;
        }
        
        .modal-body {
            padding: 20px;
        }
        
        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .close {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--text-muted);
            cursor: pointer;
            transition: color 0.2s;
            line-height: 1;
        }
        
        .close:hover {
            color: var(--dark-color);
        }
        
        /* Formulaires */
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--dark-color);
        }
        
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 0.9375rem;
            transition: border-color 0.15s, box-shadow 0.15s;
            font-family: inherit;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.25);
        }
        
        .form-row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -10px;
            margin-left: -10px;
        }
        
        .form-row > .form-group {
            padding-right: 10px;
            padding-left: 10px;
            flex: 1 0 0;
        }
        
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
        }
        
        /* Alertes */
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 6px;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .alert i {
            font-size: 1.2rem;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-color: #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #f5c6cb;
        }
        
        /* États vides */
        .empty-state {
            text-align: center;
            padding: 50px 20px;
            color: var(--text-muted);
        }
        
        .empty-state i {
            font-size: 3rem;
            color: #dee2e6;
            margin-bottom: 15px;
        }
        
        .empty-state p {
            font-size: 1.1rem;
            margin-bottom: 20px;
        }
        
        /* Badges */
        .badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 4px;
        }
        
        .badge-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .tabs {
                overflow-x: auto;
                padding-bottom: 8px;
                flex-wrap: nowrap;
            }
            
            .cours-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            
            .cours-heure {
                min-width: auto;
                width: 100%;
                padding-bottom: 8px;
                border-bottom: 1px dashed var(--border-color);
            }
            
            .cours-actions {
                align-self: flex-end;
            }
            
            .form-row {
                flex-direction: column;
            }
            
            .form-row > .form-group {
                padding: 0;
                margin-bottom: 15px;
            }
            
            .modal-content {
                width: 95%;
            }
        }
        
        /* Scrollbar personnalisée */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</head>

<body>
    <div class="container fade-in">
        <div class="header">
            <a href="dashbord2.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
            <h1 class="page-title">
                <i class="fas fa-calendar-alt"></i> Emploi du Temps par Classe
            </h1>
            <!-- <button class="btn btn-primary" id="addCoursBtn">
                <i class="fas fa-plus"></i> Ajouter un cours
            </button> -->
        </div>

        <!-- Affichage des messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <div><?= htmlspecialchars($_SESSION['success']) ?></div>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <div><?= htmlspecialchars($_SESSION['error']) ?></div>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Sélecteur de classe -->
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-users-class"></i> Sélection de la classe
                </h2>
            </div>
            <div class="card-body">
                <div class="tabs">
                    <?php foreach ($classes as $classe): ?>
                        <a href="?classe=<?= urlencode($classe) ?>" class="tab <?= $classe === $classeSelectionnee ? 'active' : '' ?>">
                            <?= htmlspecialchars($classe) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Emploi du temps -->
        <?php if ($classeSelectionnee): ?>
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-calendar-week"></i> Emploi du temps - <?= htmlspecialchars($classeSelectionnee) ?>
                    </h2>
                </div>
                <div class="card-body">
                    <?php if (empty($cours)): ?>
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <p>Aucun cours programmé pour cette classe</p>
                            <!-- <button class="btn btn-primary" id="addFirstCoursBtn">
                                <i class="fas fa-plus"></i> Ajouter le premier cours
                            </button> -->
                        </div>
                    <?php else: ?>
                        <div class="emploi-container">
                            <?php
                            // Organiser les cours par jour
                            $jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
                            $coursParJour = [];
                            
                            foreach ($cours as $c) {
                                $coursParJour[$c['jour']][] = $c;
                            }
                            ?>
                            
                            <?php foreach ($jours as $jour): ?>
                                <?php if (!empty($coursParJour[$jour])): ?>
                                    <div class="jour-container">
                                        <h3 class="jour-title">
                                            <i class="fas fa-calendar-day"></i> <?= htmlspecialchars($jour) ?>
                                            <span class="badge badge-primary" style="float: right;">
                                                <?= count($coursParJour[$jour]) ?> cours
                                            </span>
                                        </h3>
                                        
                                        <div class="cours-list">
                                            <?php foreach ($coursParJour[$jour] as $c): ?>
                                                <div class="cours-item">
                                                    <div class="cours-heure">
                                                        <i class="far fa-clock"></i> <?= substr($c['heure_debut'], 0, 5) ?> - <?= substr($c['heure_fin'], 0, 5) ?>
                                                    </div>
                                                    <div class="cours-details">
                                                        <div class="cours-matiere">
                                                            <i class="fas fa-book"></i> <?= htmlspecialchars($c['matiere']) ?>
                                                        </div>
                                                        <div class="cours-enseignant">
                                                            <?php if ($c['enseignant_id']): ?>
                                                                <i class="fas fa-chalkboard-teacher"></i> <?= htmlspecialchars($c['enseignant_nom'] . ' ' . $c['enseignant_prenom']) ?>
                                                            <?php else: ?>
                                                                <i class="fas fa-user-times"></i> Enseignant non attribué
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="cours-actions">
                                                        <!-- <button class="btn btn-sm btn-warning modify-btn" data-id="<?= $c['id'] ?>">
                                                            <i class="fas fa-edit"></i> Modifier
                                                        </button>
                                                        <form action="requete.php" method="POST" class="delete-form">
                                                            <input type="hidden" name="id" value="<?= $c['id'] ?>">
                                                            <button type="submit" name="supprimer_cours" class="btn btn-sm btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce cours ?');">
                                                                <i class="fas fa-trash-alt"></i> Supprimer
                                                            </button>
                                                        </form> -->
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal d'ajout -->
    <div class="modal" id="addCoursModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-calendar-plus"></i> Ajouter un nouveau cours
                </h2>
                <span class="close">&times;</span>
            </div>

            <form action="requete.php" method="post" id="addForm">
                <input type="hidden" name="classe" value="<?= htmlspecialchars($classeSelectionnee ?? '') ?>">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="jour" class="form-label">Jour</label>
                            <select name="jour" id="jour" class="form-control" required>
                                <option value="">Sélectionner un jour</option>
                                <option value="Lundi">Lundi</option>
                                <option value="Mardi">Mardi</option>
                                <option value="Mercredi">Mercredi</option>
                                <option value="Jeudi">Jeudi</option>
                                <option value="Vendredi">Vendredi</option>
                                <option value="Samedi">Samedi</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="heure_debut" class="form-label">Heure de début</label>
                            <input type="time" id="heure_debut" name="heure_debut" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="heure_fin" class="form-label">Heure de fin</label>
                            <input type="time" id="heure_fin" name="heure_fin" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="matiere" class="form-label">Matière</label>
                            <select name="matiere" id="matiere" class="form-control" required>
                                <option value="">Sélectionner une matière</option>
                                <?php foreach ($matieres as $matiere): ?>
                                    <option value="<?= htmlspecialchars($matiere) ?>"><?= htmlspecialchars($matiere) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="enseignant_id" class="form-label">Enseignant</label>
                            <select name="enseignant_id" id="enseignant_id" class="form-control">
                                <option value="">Non attribué</option>
                                <?php foreach ($enseignants as $enseignant): ?>
                                    <option value="<?= $enseignant['id'] ?>">
                                        <?= htmlspecialchars($enseignant['nom'] . ' ' . $enseignant['prenom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="classe" class="form-label">Classe</label>
                            <select name="classe" id="classe" class="form-control" required>
                                <option value="">Sélectionner une classe</option>
                                <?php foreach ($classes as $classe): ?>
                                    <option value="<?= htmlspecialchars($classe) ?>" <?= $classe === $classeSelectionnee ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($classe) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelAddBtn">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary" name="ajouter_cours">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de modification -->
    <div class="modal" id="modifyCoursModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-calendar-edit"></i> Modifier le cours
                </h2>
                <span class="close">&times;</span>
            </div>

            <form action="requete.php" method="post" id="modifyForm">
                <input type="hidden" name="id" id="modify-id">
                <div class="modal-body">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="modify-jour" class="form-label">Jour</label>
                            <select name="jour" id="modify-jour" class="form-control" required>
                                <option value="">Sélectionner un jour</option>
                                <option value="Lundi">Lundi</option>
                                <option value="Mardi">Mardi</option>
                                <option value="Mercredi">Mercredi</option>
                                <option value="Jeudi">Jeudi</option>
                                <option value="Vendredi">Vendredi</option>
                                <option value="Samedi">Samedi</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="modify-heure_debut" class="form-label">Heure de début</label>
                            <input type="time" id="modify-heure_debut" name="heure_debut" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="modify-heure_fin" class="form-label">Heure de fin</label>
                            <input type="time" id="modify-heure_fin" name="heure_fin" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="modify-matiere" class="form-label">Matière</label>
                            <select name="matiere" id="modify-matiere" class="form-control" required>
                                <option value="">Sélectionner une matière</option>
                                <?php foreach ($matieres as $matiere): ?>
                                    <option value="<?= htmlspecialchars($matiere) ?>"><?= htmlspecialchars($matiere) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="modify-enseignant_id" class="form-label">Enseignant</label>
                            <select name="enseignant_id" id="modify-enseignant_id" class="form-control">
                                <option value="">Non attribué</option>
                                <?php foreach ($enseignants as $enseignant): ?>
                                    <option value="<?= $enseignant['id'] ?>">
                                        <?= htmlspecialchars($enseignant['nom'] . ' ' . $enseignant['prenom']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="modify-classe" class="form-label">Classe</label>
                            <select name="classe" id="modify-classe" class="form-control" required>
                                <option value="">Sélectionner une classe</option>
                                <?php foreach ($classes as $classe): ?>
                                    <option value="<?= htmlspecialchars($classe) ?>"><?= htmlspecialchars($classe) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="cancelModifyBtn">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary" name="modifier_cours">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Fonctions utilitaires
        function showModal(modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            document.documentElement.style.overflow = 'hidden';
        }

        function hideModal(modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            document.documentElement.style.overflow = 'auto';
        }

        // Modal d'ajout
        const addBtn = document.getElementById('addCoursBtn');
        const addFirstBtn = document.getElementById('addFirstCoursBtn');
        const addClose = document.querySelector('#addCoursModal .close');
        const cancelAddBtn = document.getElementById('cancelAddBtn');

        [addBtn, addFirstBtn].forEach(btn => {
            btn?.addEventListener('click', () => showModal(document.getElementById('addCoursModal')));
        });

        [addClose, cancelAddBtn].forEach(btn => {
            btn?.addEventListener('click', () => hideModal(document.getElementById('addCoursModal')));
        });

        // Modal de modification
        const modifyClose = document.querySelector('#modifyCoursModal .close');
        const cancelModifyBtn = document.getElementById('cancelModifyBtn');
        const modifyForms = document.querySelectorAll('.modify-btn');

        function openModifyModal(cours) {
            document.getElementById('modify-id').value = cours.id;
            document.getElementById('modify-jour').value = cours.jour;
            document.getElementById('modify-heure_debut').value = cours.heure_debut;
            document.getElementById('modify-heure_fin').value = cours.heure_fin;
            document.getElementById('modify-matiere').value = cours.matiere;
            document.getElementById('modify-enseignant_id').value = cours.enseignant_id || '';
            document.getElementById('modify-classe').value = cours.classe;

            showModal(document.getElementById('modifyCoursModal'));
        }

        modifyForms.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                
                // Récupérer les données du cours via AJAX
                fetch(`requete.php?id=${id}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erreur réseau');
                        }
                        return response.json();
                    })
                    .then(cours => {
                        if (cours.error) {
                            alert(cours.error);
                        } else {
                            openModifyModal(cours);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert('Erreur lors du chargement des données du cours');
                    });
            });
        });

        [modifyClose, cancelModifyBtn].forEach(btn => {
            btn.addEventListener('click', () => hideModal(document.getElementById('modifyCoursModal')));
        });

        // Fermer les modals en cliquant à l'extérieur
        window.addEventListener('click', (event) => {
            if (event.target === document.getElementById('addCoursModal')) {
                hideModal(document.getElementById('addCoursModal'));
            }
            if (event.target === document.getElementById('modifyCoursModal')) {
                hideModal(document.getElementById('modifyCoursModal'));
            }
        });

        // Validation des heures
        const addForm = document.getElementById('addForm');
        const modifyForm = document.getElementById('modifyForm');

        function validateHours(form) {
            const heureDebut = form.querySelector('input[name="heure_debut"]').value;
            const heureFin = form.querySelector('input[name="heure_fin"]').value;
            
            if (heureDebut && heureFin && heureFin <= heureDebut) {
                alert('L\'heure de fin doit être après l\'heure de début');
                return false;
            }
            return true;
        }

        if (addForm) {
            addForm.addEventListener('submit', function(e) {
                if (!validateHours(this)) {
                    e.preventDefault();
                }
            });
        }

        if (modifyForm) {
            modifyForm.addEventListener('submit', function(e) {
                if (!validateHours(this)) {
                    e.preventDefault();
                }
            });
        }

        // Animation de chargement
        document.addEventListener('DOMContentLoaded', () => {
            document.body.classList.add('loaded');
        });
    </script>
</body>
</html>