<?php
include 'database.php';

// Vérifier si l'ID de l'élève est passé en paramètre
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: eleve2.php');
    exit();
}

$eleve_id = $_GET['id'];

// Récupérer les informations de l'élève
$req = $bd->prepare('SELECT * FROM eleves WHERE id = ?');
$req->execute([$eleve_id]);
$eleve = $req->fetch(PDO::FETCH_ASSOC);

if (!$eleve) {
    header('Location: eleves2.php');
    exit();
}

// Récupérer les matières
$req = $bd->query('SELECT * FROM matieres ORDER BY nom');
$matieres = $req->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les notes de l'élève
$req = $bd->prepare('SELECT n.*, m.nom as matiere_nom, m.coefficient 
                    FROM notes n 
                    JOIN matieres m ON n.matiere_id = m.id 
                    WHERE n.eleve_id = ? 
                    ORDER BY m.nom, n.date_note DESC');
$req->execute([$eleve_id]);
$notes = $req->fetchAll(PDO::FETCH_ASSOC);

// Calculer les moyennes par matière
$moyennes_par_matiere = [];
foreach ($notes as $note) {
    $matiere_id = $note['matiere_id'];
    if (!isset($moyennes_par_matiere[$matiere_id])) {
        $moyennes_par_matiere[$matiere_id] = [
            'somme' => 0,
            'count' => 0,
            'coefficient' => $note['coefficient'],
            'nom' => $note['matiere_nom']
        ];
    }
    $moyennes_par_matiere[$matiere_id]['somme'] += $note['note'];
    $moyennes_par_matiere[$matiere_id]['count']++;
}

// Calculer la moyenne générale
$total_points = 0;
$total_coefficients = 0;
foreach ($moyennes_par_matiere as $matiere) {
    $moyenne_matiere = $matiere['somme'] / $matiere['count'];
    $total_points += $moyenne_matiere * $matiere['coefficient'];
    $total_coefficients += $matiere['coefficient'];
}

$moyenne_generale = $total_coefficients > 0 ? $total_points / $total_coefficients : 0;

// Récupérer l'année scolaire actuelle (vous pouvez adapter cette partie selon votre besoin)
$annee_scolaire = date('Y') . '-' . (date('Y') + 1);
$trimestre = isset($_GET['trimestre']) ? $_GET['trimestre'] : 1;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bulletin Scolaire - <?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?></title>
    <link rel="stylesheet" href="css/bulletin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="bulletin-container">
        <div class="header">
            <div class="school-name">Gamal Nasser</div>
            <div class="school-address">Farcha   +235 66 66 66 66 - 99 99 99 99</div>
            <div class="bulletin-title">BULLETIN SCOLAIRE - TRIMESTRE <?= $trimestre ?></div>
        </div>

        <div class="student-info">
            <div class="info-block">
                <span class="info-label">Nom:</span> <?= htmlspecialchars($eleve['nom']) ?>
            </div>
            <div class="info-block">
                <span class="info-label">Prénom:</span> <?= htmlspecialchars($eleve['prenom']) ?>
            </div>
            <div class="info-block">
                <span class="info-label">Classe:</span> <?= htmlspecialchars($eleve['classe']) ?>
            </div>
            <div class="info-block">
                <span class="info-label">Année scolaire:</span> <?= htmlspecialchars($annee_scolaire) ?>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Matières</th>
                    <th>Coefficient</th>
                    <th>Notes</th>
                    <th>Moyenne</th>
                    <th>Appréciation</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($moyennes_par_matiere as $matiere_id => $matiere): ?>
                    <tr class="subject-row">
                        <td><?= htmlspecialchars($matiere['nom']) ?></td>
                        <td><?= $matiere['coefficient'] ?></td>
                        <td>
                            <?php 
                            $notes_matiere = array_filter($notes, function($note) use ($matiere_id) {
                                return $note['matiere_id'] == $matiere_id;
                            });
                            
                            $notes_display = [];
                            foreach ($notes_matiere as $note) {
                                $notes_display[] = number_format($note['note'], 2);
                            }
                            echo implode(', ', $notes_display);
                            ?>
                        </td>
                        <td><?= number_format($matiere['somme'] / $matiere['count'], 2) ?></td>
                        <td>
                            <?php
                            $moyenne = $matiere['somme'] / $matiere['count'];
                            if ($moyenne >= 18) {
                                echo "Excellent";
                            } elseif ($moyenne >= 16) {
                                echo "Très bien";
                            } elseif ($moyenne >= 14) {
                                echo "Bien";
                            } elseif ($moyenne >= 12) {
                                echo "Assez bien";
                            } elseif ($moyenne >= 10) {
                                echo "Passable";
                            } elseif ($moyenne >= 8) {
                                echo "Insuffisant";
                            } else {
                                echo "Très faible";
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="average-section">
            <div class="average-display">
                MOYENNE GÉNÉRALE: <?= number_format($moyenne_generale, 2) ?> / 20
            </div>
            
            <div>
                <strong>Rang:</strong> <!-- Vous pouvez ajouter le calcul du rang si disponible -->
                <strong>Mention:</strong> 
                <?php
                if ($moyenne_generale >= 18) {
                    echo "Exellent";
                } elseif ($moyenne_generale >= 16) {
                    echo "Très bien";
                } elseif ($moyenne_generale >= 14) {
                    echo "Bien";
                } elseif ($moyenne_generale >= 12) {
                    echo "Assez bien";
                } elseif ($moyenne_generale >= 10) {
                    echo "Passable";
                } elseif ($moyenne_generale >= 8) {
                    echo "Insuffisant";
                } else {
                    echo "Très faible";
                }
                ?>
            </div>
            
            <div style="margin-top: 20px;">
                <strong>Appréciation générale:</strong> 
                <!-- Vous pouvez personnaliser l'appréciation générale ici -->
                <?php
                if ($moyenne_generale >= 18) {
                    echo "Élève excellent, travail remarquable. Continue ainsi !";
                } elseif ($moyenne_generale >= 16) {
                    echo "Très bon travail, peut encore progresser dans certains domaines.";
                } elseif ($moyenne_generale >= 14) {
                    echo "Bon travail, quelques efforts supplémentaires seraient bénéfiques.";
                } elseif ($moyenne_generale >= 12) {
                    echo "Résultats satisfaisants, mais peut faire mieux avec plus de régularité.";
                } elseif ($moyenne_generale >= 10) {
                    echo "Résultats en dessous de la moyenne, besoin de plus de travail.";
                } elseif ($moyenne_generale >= 8) {
                    echo "Résultats insuffisants, nécessite un soutien particulier.";
                } else {
                    echo "Résultats très faibles, un entretien avec les parents est nécessaire.";
                }
                ?>
            </div>
        </div>

        <div class="signature-section">
            <div class="signature">
                Le Professeur Principal
            </div>
            <div class="signature">
                Le Chef d'Établissement
            </div>
        </div>

        <div class="footer">
            Date d'édition: <?= date('d/m/Y') ?>
        </div>
    </div>

    <button class="print-btn" onclick="window.print()">
        <i class="fas fa-print"></i> Imprimer le bulletin
    </button>

</body>
</html>