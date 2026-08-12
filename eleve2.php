<?php
include 'database.php';

// Récupération des élèves
$req = $bd->prepare('SELECT * FROM eleves ORDER BY nom, prenom');
$req->execute();
$eleves = $req->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Gestion des Élèves</title>
    <link rel="stylesheet" href="css/eleve.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <a href="dashbord2.php" class="btn btn-secondary">
                <i class="fas fa-home"></i> Accueil
            </a>
            <h1 class="page-title">
                <i class="fas fa-users"></i> Gestion des Élèves
            </h1>
            <!-- <button class="btn btn-primary" id="addStudentBtn">
                <i class="fas fa-plus"></i> Ajouter un élève
            </button> -->
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-list"></i> Liste des Élèves
                </h2>
                <div class="search-container">
                    <input type="text" class="search-input" placeholder="Rechercher un élève..." id="searchInput">
                    <button class="search-btn" id="searchBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="studentsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Date Naiss.</th>
                            <th>Sexe</th>
                            <th>Classe</th>
                            <th>Adresse</th>
                            <th>Tél. Parent</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($eleves)): ?>
                            <tr>
                                <td colspan="9" class="empty-state">
                                    <i class="fas fa-user-graduate"></i>
                                    <p>Aucun élève enregistré pour le moment</p>
                                    <button class="btn btn-primary" id="addFirstStudentBtn">
                                        <i class="fas fa-plus"></i> Ajouter un élève
                                    </button>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($eleves as $eleve): ?>
                                <tr data-id="<?= $eleve['id'] ?>">
                                    <td><?= htmlspecialchars($eleve['id']) ?></td>
                                    <td><?= htmlspecialchars($eleve['nom']) ?></td>
                                    <td><?= htmlspecialchars($eleve['prenom']) ?></td>
                                    <td><?= date('d/m/Y', strtotime($eleve['date_naissance'])) ?></td>
                                    <td>
                                        <span class="badge <?= $eleve['sexe'] === 'M' ? 'badge-male' : 'badge-female' ?>">
                                            <?= htmlspecialchars($eleve['sexe']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($eleve['classe']) ?></td>
                                    <td><?= htmlspecialchars($eleve['adresse']) ?></td>
                                    <td><?= htmlspecialchars($eleve['tel_parent']) ?></td>
                                    <td class="actions">
                                        <!-- <button class="btn btn-sm btn-warning modify-btn" data-id="<?= $eleve['id'] ?>">
                                            <i class="fas fa-edit"></i> <span class="action-text">Modifier</span>
                                        </button> -->
                                        <a href="notes2.php?id=<?= $eleve['id'] ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-book"></i> <span class="action-text">Notes</span>
                                        </a>
                                        <a href="bulletin.php?id=<?= $eleve['id'] ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-file-alt"></i> <span class="action-text">Bulletin</span>
                                        </a>
                                        <form action="requete.php" method="POST" class="delete-form" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet élève ?');">
                                            <input type="hidden" name="id" value="<?= $eleve['id'] ?>">
                                            <!-- <button type="submit" name="supprimer_eleve" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash-alt"></i> <span class="action-text">Supprimer</span>
                                            </button> -->
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

    <!-- Modal d'ajout -->
    <div class="modal" id="addStudentModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-user-plus"></i> Ajouter un nouvel élève
                </h2>
                <span class="close">&times;</span>
            </div>

            <form action="requete.php" class="ajout" method="post" id="addForm">
                <div class="form-group">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" id="nom" name="nom" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="prenom" class="form-label">Prénom</label>
                    <input type="text" id="prenom" name="prenom" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="naissance" class="form-label">Date de naissance</label>
                    <input type="date" id="naissance" name="date_naissance" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="sexe" class="form-label">Sexe</label>
                    <select name="sexe" id="sexe" class="form-control" required>
                        <option value="">Sélectionner un sexe</option>
                        <option value="F">Féminin</option>
                        <option value="M">Masculin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="classe" class="form-label">Classe</label>
                    <select name="classe" id="classe" class="form-control" required>
                        <option value="">Sélectionner une classe</option>
                        <option value="6 A">6ème A</option>
                        <option value="6 B">6ème B</option>
                        <option value="5 A">5ème A</option>
                        <option value="5 B">5ème B</option>
                        <option value="4 A">4ème A</option>
                        <option value="4 B">4ème B</option>
                        <option value="3 A">3ème A</option>
                        <option value="3 B">3ème B</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="adresse" class="form-label">Adresse</label>
                    <input type="text" id="adresse" name="adresse" class="form-control">
                </div>
                <div class="form-group">
                    <label for="modify-tel_parent" class="form-label">Téléphone des parents</label>
                    <input type="tel" id="modify-tel_parent" name="tel_parent" class="form-control" pattern="[0-9]{11}" maxlength="11">
                    <small class="text-muted">Format: 23512345678 (11 chiffres)</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" id="cancelAddBtn">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary" name="enregistre_eleve">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de modification -->
    <div class="modal" id="modifyStudentModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-user-edit"></i> Modifier l'élève
                </h2>
                <span class="close">&times;</span>
            </div>

            <form action="requete.php" class="modify" method="post" id="modifyForm">
                <input type="hidden" name="id" id="modify-id">
                <div class="form-group">
                    <label for="modify-nom" class="form-label">Nom</label>
                    <input type="text" id="modify-nom" name="nom" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="modify-prenom" class="form-label">Prénom</label>
                    <input type="text" id="modify-prenom" name="prenom" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="modify-naissance" class="form-label">Date de naissance</label>
                    <input type="date" id="modify-naissance" name="date_naissance" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="modify-sexe" class="form-label">Sexe</label>
                    <select name="sexe" id="modify-sexe" class="form-control" required>
                        <option value="">Sélectionner un sexe</option>
                        <option value="F">Féminin</option>
                        <option value="M">Masculin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="modify-classe" class="form-label">Classe</label>
                    <select name="classe" id="modify-classe" class="form-control" required>
                        <option value="">Sélectionner une classe</option>
                        <option value="6 A">6ème A</option>
                        <option value="6 B">6ème B</option>
                        <option value="5 A">5ème A</option>
                        <option value="5 B">5ème B</option>
                        <option value="4 A">4ème A</option>
                        <option value="4 B">4ème B</option>
                        <option value="3 A">3ème A</option>
                        <option value="3 B">3ème B</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="modify-adresse" class="form-label">Adresse</label>
                    <input type="text" id="modify-adresse" name="adresse" class="form-control">
                </div>
                <div class="form-group">
                    <label for="modify-tel_parent" class="form-label">Téléphone des parents</label>
                    <input type="tel" id="modify-tel_parent" name="tel_parent" class="form-control" pattern="[0-9]{11}" maxlength="11">
                    <small class="text-muted">Format: 23512345678 (11 chiffres)</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" id="cancelModifyBtn">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary" name="modifier_eleve">
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
        const addModal = document.getElementById('addStudentModal');
        const modifyModal = document.getElementById('modifyStudentModal');

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
        const addBtn = document.getElementById('addStudentBtn');
        const addFirstBtn = document.getElementById('addFirstStudentBtn');
        const addClose = addModal.querySelector('.close');
        const cancelAddBtn = document.getElementById('cancelAddBtn');

        [addBtn, addFirstBtn].forEach(btn => {
            btn?.addEventListener('click', () => showModal(addModal));
        });

        [addClose, cancelAddBtn].forEach(btn => {
            btn?.addEventListener('click', () => hideModal(addModal));
        });

        // Modal de modification
        const modifyClose = modifyModal.querySelector('.close');
        const cancelModifyBtn = document.getElementById('cancelModifyBtn');
        const modifyForms = document.querySelectorAll('.modify-btn');

        function openModifyModal(eleve) {
            document.getElementById('modify-id').value = eleve.id;
            document.getElementById('modify-nom').value = eleve.nom;
            document.getElementById('modify-prenom').value = eleve.prenom;
            document.getElementById('modify-naissance').value = eleve.date_naissance;
            document.getElementById('modify-sexe').value = eleve.sexe;
            document.getElementById('modify-classe').value = eleve.classe;
            document.getElementById('modify-adresse').value = eleve.adresse || '';
            document.getElementById('modify-tel_parent').value = eleve.tel_parent || '';

            showModal(modifyModal);
        }

        modifyForms.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const eleve = <?= json_encode(array_column($eleves, null, 'id')) ?>[id];

                if (eleve) {
                    openModifyModal(eleve);
                }
            });
        });

        [modifyClose, cancelModifyBtn].forEach(btn => {
            btn.addEventListener('click', () => hideModal(modifyModal));
        });

        // Fermer les modals en cliquant à l'extérieur
        window.addEventListener('click', (event) => {
            if (event.target === addModal) hideModal(addModal);
            if (event.target === modifyModal) hideModal(modifyModal);
        });

        // Recherche en temps réel
        const searchInput = document.getElementById('searchInput');
        const studentsTable = document.getElementById('studentsTable');

        if (searchInput && studentsTable) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = studentsTable.querySelectorAll('tbody tr');

                rows.forEach(row => {
                    if (row.querySelector('.empty-state')) return;

                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }

        // Dans la validation du formulaire d'ajout
        if (addForm) {
            addForm.addEventListener('submit', function(e) {
                const telParent = this.querySelector('#tel_parent');
                if (telParent.value && !/^[0-9]{11}$/.test(telParent.value)) {
                    alert('Le numéro de téléphone doit contenir 11 chiffres');
                    e.preventDefault();
                }
            });
        }

        // Dans la validation du formulaire de modification
        if (modifyForm) {
            modifyForm.addEventListener('submit', function(e) {
                const telParent = this.querySelector('#modify-tel_parent');
                if (telParent.value && !/^[0-9]{11}$/.test(telParent.value)) {
                    alert('Le numéro de téléphone doit contenir 11 chiffres');
                    e.preventDefault();
                }
            });
        }

        // if (modifyForm) {
        //     modifyForm.addEventListener('submit', function(e) {
        //         const telParent = this.querySelector('#modify-tel_parent');
        //         if (telParent.value && !/^[0-9]{10}$/.test(telParent.value)) {
        //             alert('Le numéro de téléphone doit contenir 10 chiffres');
        //             e.preventDefault();
        //         }
        //     });
        // }

        // Confirmation avant suppression
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cet élève ?')) {
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