<?php
include 'database.php';

// Vérifier si l'ID de l'élève est passé en paramètre
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: eleve.php');
    exit();
}

$eleve_id = $_GET['id'];

// Récupérer les informations de l'élève
$req = $bd->prepare('SELECT * FROM eleves WHERE id = ?');
$req->execute([$eleve_id]);
$eleve = $req->fetch(PDO::FETCH_ASSOC);

if (!$eleve) {
    header('Location: eleves.php');
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
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Notes - <?= htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom']) ?></title>
    <link rel="stylesheet" href="css/note.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="page-title">
                <i class="fas fa-graduation-cap"></i> Gestion des Notes
            </h1>
            <a href="eleve2.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Retour aux élèves
            </a>
        </div>

        <div class="student-info">
            <div>
                <h2><?= htmlspecialchars($eleve['nom']  . ' ' . $eleve['prenom']) ?></h2>
                <p><strong>Classe:</strong> <?= htmlspecialchars($eleve['classe']) ?></p>
                <p><strong>Date de naissance:</strong> <?= date('d/m/Y', strtotime($eleve['date_naissance'])) ?></p>
            </div>
        </div>

        <div class="average-card">
            <h3><i class="fas fa-chart-line"></i> Moyennes</h3>
            
            <?php if (!empty($moyennes_par_matiere)): ?>
                <div class="average-display">
                    Moyenne générale: <?= number_format($moyenne_generale, 2) ?>/20
                </div>
                
                <?php foreach ($moyennes_par_matiere as $matiere_id => $matiere): ?>
                    <div class="subject-average">
                        <span><?= htmlspecialchars($matiere['nom']) ?> (coef. <?= $matiere['coefficient'] ?>)</span>
                        <span><?= number_format($matiere['somme'] / $matiere['count'], 2) ?>/20</span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune note enregistrée pour le moment</p>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-list"></i> Notes de l'élève
                </h2>
                <button class="btn btn-primary" id="addNoteBtn">
                    <i class="fas fa-plus"></i> Ajouter une note
                </button>
            </div>

            <div class="table-responsive">
                <table id="notesTable">
                    <thead>
                        <tr>
                            <th>Matière</th>
                            <th>Note</th>
                            <th>Coefficient</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($notes)): ?>
                            <tr>
                                <td colspan="6" class="empty-state">
                                    <i class="fas fa-book-open"></i>
                                    <p>Aucune note enregistrée pour cet élève</p>
                                    <button class="btn btn-primary" id="addFirstNoteBtn">
                                        <i class="fas fa-plus"></i> Ajouter une note
                                    </button>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($notes as $note): ?>
                                <tr data-id="<?= $note['id'] ?>">
                                    <td><?= htmlspecialchars($note['matiere_nom']) ?></td>
                                    <td><?= number_format($note['note'], 2) ?>/20</td>
                                    <td><?= $note['coefficient'] ?></td>
                                    <td><?= htmlspecialchars($note['type_note']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($note['date_note'])) ?></td>
                                    <td class="actions">
                                        <button class="btn btn-sm btn-warning modify-note-btn" data-id="<?= $note['id'] ?>">
                                            <i class="fas fa-edit"></i> Modifier
                                        </button>
                                        <a href="bulletin.php?id=<?= $eleve['id'] ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-file-alt"></i> <span class="action-text">Bulletin</span>
                                        </a>
                                        <form action="requete.php" method="POST" class="delete-note-form" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette note ?');">
                                            <input type="hidden" name="id" value="<?= $note['id'] ?>">
                                            <button type="submit" name="supprimer_note" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash-alt"></i> Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal d'ajout de note -->
    <div class="modal" id="addNoteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-plus-circle"></i> Ajouter une note
                </h2>
                <span class="close">&times;</span>
            </div>

            <form action="requete.php" method="post" id="addNoteForm">
                <input type="hidden" name="eleve_id" value="<?= $eleve_id ?>">
                
                <div class="form-group">
                    <label for="add-matiere" class="form-label">Matière</label>
                    <select name="matiere_id" id="add-matiere" class="form-control" required>
                        <option value="">Sélectionner une matière</option>
                        <?php foreach ($matieres as $matiere): ?>
                            <option value="<?= $matiere['id'] ?>"><?= htmlspecialchars($matiere['nom']) ?> (coef. <?= $matiere['coefficient'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="add-note" class="form-label">Note</label>
                    <input type="number" id="add-note" name="note" class="form-control" min="0" max="20" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="add-type" class="form-label">Type de note</label>
                    <select name="type_note" id="add-type" class="form-control" required>
                        <option value="">Sélectionner un type</option>
                        <option value="Devoir">Devoir</option>
                        <option value="Composition">Composition</option>
                        <!-- <option value="Interrogation">Interrogation</option>
                        <option value="Projet">Projet</option>
                        <option value="Oral">Oral</option> -->
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="add-date" class="form-label">Date</label>
                    <input type="date" id="add-date" name="date_note" class="form-control" required value="<?= date('Y-m-d') ?>">
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn" id="cancelAddNoteBtn">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary" name="ajouter_note">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de modification de note -->
    <div class="modal" id="modifyNoteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-edit"></i> Modifier la note
                </h2>
                <span class="close">&times;</span>
            </div>

            <form action="requete.php" method="post" id="modifyNoteForm">
                <input type="hidden" name="id" id="modify-note-id">
                <input type="hidden" name="eleve_id" value="<?= $eleve_id ?>">
                
                <div class="form-group">
                    <label for="modify-note-matiere" class="form-label">Matière</label>
                    <select name="matiere_id" id="modify-note-matiere" class="form-control" required>
                        <option value="">Sélectionner une matière</option>
                        <?php foreach ($matieres as $matiere): ?>
                            <option value="<?= $matiere['id'] ?>"><?= htmlspecialchars($matiere['nom']) ?> (coef. <?= $matiere['coefficient'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="modify-note-valeur" class="form-label">Note</label>
                    <input type="number" id="modify-note-valeur" name="note" class="form-control" min="0" max="20" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label for="modify-note-type" class="form-label">Type de note</label>
                    <select name="type_note" id="modify-note-type" class="form-control" required>
                        <option value="">Sélectionner un type</option>
                        <option value="Devoir">Devoir</option>
                        <option value="Composition">Composition</option>
                        <!-- <option value="Interrogation">Interrogation</option>
                        <option value="Projet">Projet</option>
                        <option value="Oral">Oral</option> -->
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="modify-note-date" class="form-label">Date</label>
                    <input type="date" id="modify-note-date" name="date_note" class="form-control" required>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn" id="cancelModifyNoteBtn">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary" name="modifier_note">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Fonctions utilitaires
        function formatDate(dateString) {
            const [year, month, day] = dateString.split('-');
            return `${day}/${month}/${year}`;
        }

        function parseDate(dateString) {
            const [day, month, year] = dateString.split('/');
            return `${year}-${month}-${day}`;
        }

        // Gestion des modals
        const addNoteModal = document.getElementById('addNoteModal');
        const modifyNoteModal = document.getElementById('modifyNoteModal');
        
        function showModal(modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
        
        function hideModal(modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        // Modal d'ajout de note
        const addNoteBtn = document.getElementById('addNoteBtn');
        const addFirstNoteBtn = document.getElementById('addFirstNoteBtn');
        const addNoteClose = addNoteModal.querySelector('.close');
        const cancelAddNoteBtn = document.getElementById('cancelAddNoteBtn');

        [addNoteBtn, addFirstNoteBtn].forEach(btn => {
            btn?.addEventListener('click', () => showModal(addNoteModal));
        });
        
        [addNoteClose, cancelAddNoteBtn].forEach(btn => {
            btn?.addEventListener('click', () => hideModal(addNoteModal));
        });

        // Modal de modification de note
        const modifyNoteClose = modifyNoteModal.querySelector('.close');
        const cancelModifyNoteBtn = document.getElementById('cancelModifyNoteBtn');
        const modifyNoteForms = document.querySelectorAll('.modify-note-btn');

        function openModifyNoteModal(note) {
            document.getElementById('modify-note-id').value = note.id;
            document.getElementById('modify-note-matiere').value = note.matiere_id;
            document.getElementById('modify-note-valeur').value = note.note;
            document.getElementById('modify-note-type').value = note.type_note;
            document.getElementById('modify-note-date').value = note.date_note;

            showModal(modifyNoteModal);
        }

        modifyNoteForms.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const note = <?= json_encode(array_column($notes, null, 'id')) ?>[id];
                
                if (note) {
                    openModifyNoteModal(note);
                }
            });
        });

        [modifyNoteClose, cancelModifyNoteBtn].forEach(btn => {
            btn.addEventListener('click', () => hideModal(modifyNoteModal));
        });

        // Fermer les modals en cliquant à l'extérieur
        window.addEventListener('click', (event) => {
            if (event.target === addNoteModal) hideModal(addNoteModal);
            if (event.target === modifyNoteModal) hideModal(modifyNoteModal);
        });

        // Validation des formulaires
        const addNoteForm = document.getElementById('addNoteForm');
        const modifyNoteForm = document.getElementById('modifyNoteForm');
        
        if (addNoteForm) {
            addNoteForm.addEventListener('submit', function(e) {
                const note = this.querySelector('#add-note');
                if (note.value < 0 || note.value > 20) {
                    alert('La note doit être comprise entre 0 et 20');
                    e.preventDefault();
                }
            });
        }
        
        if (modifyNoteForm) {
            modifyNoteForm.addEventListener('submit', function(e) {
                const note = this.querySelector('#modify-note-valeur');
                if (note.value < 0 || note.value > 20) {
                    alert('La note doit être comprise entre 0 et 20');
                    e.preventDefault();
                }
            });
        }

        // Confirmation avant suppression
        const deleteNoteForms = document.querySelectorAll('.delete-note-form');
        deleteNoteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cette note ?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>

</html>