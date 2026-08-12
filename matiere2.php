<?php
include 'database.php';

// Récupération des matières
$req = $bd->prepare('SELECT * FROM matieres ORDER BY nom');
$req->execute();
$matieres = $req->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Gestion des Matières</title>
    <link rel="stylesheet" href="css/matiere.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <a href="dashbord2.php" class="btn btn-secondary">
                <i class="fas fa-home"></i> Accueil
            </a>
            <h1 class="page-title">
                <i class="fas fa-book"></i> Gestion des Matières
            </h1>
            <!-- <button class="btn btn-primary" id="addMatiereBtn">
                <i class="fas fa-plus"></i> Ajouter une matière
            </button> -->
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-list"></i> Liste des Matières
                </h2>
                <div class="search-container">
                    <input type="text" class="search-input" placeholder="Rechercher une matière..." id="searchInput">
                    <button class="search-btn" id="searchBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="matieresTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Coefficient</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($matieres)): ?>
                            <tr>
                                <td colspan="4" class="empty-state">
                                    <i class="fas fa-book"></i>
                                    <p>Aucune matière enregistrée pour le moment</p>
                                    <!-- <button class="btn btn-primary" id="addFirstMatiereBtn">
                                        <i class="fas fa-plus"></i> Ajouter une matière
                                    </button> -->
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($matieres as $matiere): ?>
                                <tr data-id="<?= $matiere['id'] ?>">
                                    <td><?= htmlspecialchars($matiere['id']) ?></td>
                                    <td><?= htmlspecialchars($matiere['nom']) ?></td>
                                    <td><?= htmlspecialchars($matiere['coefficient']) ?></td>
                                    <td class="actions">
                                        <!-- <button class="btn btn-sm btn-warning modify-btn" data-id="<?= $matiere['id'] ?>">
                                            <i class="fas fa-edit"></i> <span class="action-text">Modifier</span>
                                        </button> -->

                                        <!-- <form action="requete.php" method="POST" class="delete-form" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette matière ?');">
                                            <input type="hidden" name="id" value="<?= $matiere['id'] ?>">
                                            <button type="submit" name="supprimer_matiere" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash-alt"></i> <span class="action-text">Supprimer</span>
                                            </button>
                                        </form> -->
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal d'ajout -->
    <div class="modal" id="addMatiereModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-book-medical"></i> Ajouter une nouvelle matière
                </h2>
                <span class="close">&times;</span>
            </div>

            <form action="requete.php" class="ajout" method="post" id="addForm">
                <div class="form-group">
                    <label for="nom" class="form-label">Nom de la matière</label>
                    <input type="text" id="nom" name="nom" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="coefficient" class="form-label">Coefficient</label>
                    <input type="number" id="coefficient" name="coefficient" class="form-control" min="1" value="1" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" id="cancelAddBtn">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary" name="enregistre_matiere">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de modification -->
    <div class="modal" id="modifyMatiereModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-book-open"></i> Modifier la matière
                </h2>
                <span class="close">&times;</span>
            </div>

            <form action="requete.php" class="modify" method="post" id="modifyForm">
                <input type="hidden" name="id" id="modify-id">
                <div class="form-group">
                    <label for="modify-nom" class="form-label">Nom de la matière</label>
                    <input type="text" id="modify-nom" name="nom" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="modify-coefficient" class="form-label">Coefficient</label>
                    <input type="number" id="modify-coefficient" name="coefficient" class="form-control" min="1" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" id="cancelModifyBtn">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary" name="modifier_matiere">
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
        const addBtn = document.getElementById('addMatiereBtn');
        const addFirstBtn = document.getElementById('addFirstMatiereBtn');
        const addClose = document.querySelector('#addMatiereModal .close');
        const cancelAddBtn = document.getElementById('cancelAddBtn');

        [addBtn, addFirstBtn].forEach(btn => {
            btn?.addEventListener('click', () => showModal(document.getElementById('addMatiereModal')));
        });

        [addClose, cancelAddBtn].forEach(btn => {
            btn?.addEventListener('click', () => hideModal(document.getElementById('addMatiereModal')));
        });

        // Modal de modification
        const modifyClose = document.querySelector('#modifyMatiereModal .close');
        const cancelModifyBtn = document.getElementById('cancelModifyBtn');
        const modifyForms = document.querySelectorAll('.modify-btn');

        function openModifyModal(matiere) {
            document.getElementById('modify-id').value = matiere.id;
            document.getElementById('modify-nom').value = matiere.nom;
            document.getElementById('modify-coefficient').value = matiere.coefficient;

            showModal(document.getElementById('modifyMatiereModal'));
        }

        modifyForms.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const matiere = <?= json_encode(array_column($matieres, null, 'id')) ?>[id];

                if (matiere) {
                    openModifyModal(matiere);
                }
            });
        });

        [modifyClose, cancelModifyBtn].forEach(btn => {
            btn.addEventListener('click', () => hideModal(document.getElementById('modifyMatiereModal')));
        });

        // Fermer les modals en cliquant à l'extérieur
        window.addEventListener('click', (event) => {
            if (event.target === document.getElementById('addMatiereModal')) {
                hideModal(document.getElementById('addMatiereModal'));
            }
            if (event.target === document.getElementById('modifyMatiereModal')) {
                hideModal(document.getElementById('modifyMatiereModal'));
            }
        });

        // Recherche en temps réel
        const searchInput = document.getElementById('searchInput');
        const matieresTable = document.getElementById('matieresTable');

        if (searchInput && matieresTable) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = matieresTable.querySelectorAll('tbody tr');

                rows.forEach(row => {
                    if (row.querySelector('.empty-state')) return;

                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }

        // Confirmation avant suppression
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cette matière ?')) {
                    e.preventDefault();
                }
            });
        });

        // Adaptation pour les petits écrans
        function handleResponsive() {
            const actionTexts = document.querySelectorAll('.action-text');
            if (window.innerWidth < 768) {
                actionTexts.forEach(text => {
                    text.style.display = 'none';
                });
            } else {
                actionTexts.forEach(text => {
                    text.style.display = 'inline';
                });
            }
        }

        // Exécuter au chargement et lors du redimensionnement
        window.addEventListener('load', handleResponsive);
        window.addEventListener('resize', handleResponsive);
    </script>
</body>
</html>