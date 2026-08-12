<?php
include 'database.php';

// Récupération des enseignants triés par nom
$req = $bd->prepare('SELECT * FROM enseignants ORDER BY nom, prenom');
$req->execute();
$enseignants = $req->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Gestion des Enseignants</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="css/enseignant.css">
</head>

<body>
    <div class="container">
        <div class="header">
            <a href="dashbord.php" class="btn btn-secondary">
                <i class="fas fa-home"></i> Accueil
            </a>
            <h1 class="page-title">
                <i class="fas fa-chalkboard-teacher"></i> Gestion des Enseignants
            </h1>
            <button class="btn btn-primary" id="addTeacherBtn">
                <i class="fas fa-plus"></i> Ajouter un enseignant
            </button>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-list"></i> Liste des Enseignants
                </h2>
                <div class="search-container">
                    <input type="text" class="search-input" placeholder="Rechercher un enseignant..." id="searchInput">
                    <button class="search-btn" id="searchBtn">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table id="teachersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Sexe</th>
                            <th>Niveau Étude</th>
                            <th>Matière</th>
                            <th>Adresse</th>
                            <th>Téléphone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($enseignants)): ?>
                            <tr>
                                <td colspan="9" class="empty-state">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                    <p>Aucun enseignant enregistré pour le moment</p>
                                    <button class="btn btn-primary" id="addFirstTeacherBtn">
                                        <i class="fas fa-plus"></i> Ajouter un enseignant
                                    </button>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($enseignants as $enseignant): ?>
                                <tr data-id="<?= $enseignant['id'] ?>">
                                    <td><?= htmlspecialchars($enseignant['id']) ?></td>
                                    <td><?= htmlspecialchars($enseignant['nom']) ?></td>
                                    <td><?= htmlspecialchars($enseignant['prenom']) ?></td>
                                    <td>
                                        <span class="badge <?= $enseignant['sexe'] === 'M' ? 'badge-male' : 'badge-female' ?>">
                                            <?= $enseignant['sexe'] === 'M' ? 'Homme' : 'Femme' ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($enseignant['niveau_etude']) ?></td>
                                    <td>
                                        <span class="badge badge-matiere">
                                            <?= htmlspecialchars($enseignant['matiere']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($enseignant['adresse']) ?></td>
                                    <td><?= htmlspecialchars($enseignant['tel']) ?></td>
                                    <td class="actions">
                                        <button class="btn btn-sm btn-warning modify-btn" data-id="<?= $enseignant['id'] ?>">
                                            <i class="fas fa-edit"></i> <span class="action-text">Modifier</span>
                                        </button>
                                        <form action="requete.php" method="POST" class="delete-form" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet enseignant ?');">
                                            <input type="hidden" name="id" value="<?= $enseignant['id'] ?>">
                                            <button type="submit" name="supprimer_enseignant" class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash-alt"></i> <span class="action-text">Supprimer</span>
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

    <!-- Modal d'ajout -->
    <div class="modal" id="addTeacherModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-user-plus"></i> Ajouter un nouvel enseignant
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
                    <label for="sexe" class="form-label">Sexe</label>
                    <select name="sexe" id="sexe" class="form-control" required>
                        <option value="">Sélectionner un sexe</option>
                        <option value="F">Féminin</option>
                        <option value="M">Masculin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="niveau_etude" class="form-label">Niveau d'Étude</label>
                    <select name="niveau_etude" id="niveau_etude" class="form-control" required>
                        <option value="">Sélectionner un niveau</option>
                        <option value="Licence I">Licence I</option>
                        <option value="Licence II">Licence II</option>
                        <option value="Licence III">Licence III</option>
                        <option value="Master I">Master I</option>
                        <option value="Master II">Master II</option>
                        <option value="Doctorat">Doctorat</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="matiere" class="form-label">Matière</label>
                    <select name="matiere" id="matiere" class="form-control" required>
                        <option value="">Sélectionner une matière</option>
                        <option value="Mathématiques">Mathématiques</option>
                        <option value="Physique">Physique</option>
                        <option value="SVT">Sciences de la Vie et de la Terre</option>
                        <option value="Français">Français</option>
                        <option value="Histoire">Histoire</option>
                        <option value="Géographie">Géographie</option>
                        <option value="Arabe">Arabe</option>
                        <option value="Anglais">Anglais</option>
                        <option value="Philosophie">Philosophie</option>
                        <option value="Informatique">Informatique</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="adresse" class="form-label">Adresse</label>
                    <input type="text" id="adresse" name="adresse" class="form-control">
                </div>
                <div class="form-group">
                    <label for="tel" class="form-label">Téléphone</label>
                    <input type="tel" id="tel" name="tel" class="form-control" pattern="[0-9]{11}" maxlength="11" required>
                    <small class="text-muted">Format: 23512345678 (11 chiffres)</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" id="cancelAddBtn">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary" name="enregistre_enseignant">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal de modification -->
    <div class="modal" id="modifyTeacherModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">
                    <i class="fas fa-user-edit"></i> Modifier l'enseignant
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
                    <label for="modify-sexe" class="form-label">Sexe</label>
                    <select name="sexe" id="modify-sexe" class="form-control" required>
                        <option value="">Sélectionner un sexe</option>
                        <option value="F">Féminin</option>
                        <option value="M">Masculin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="modify-niveau_etude" class="form-label">Niveau d'Étude</label>
                    <select name="niveau_etude" id="modify-niveau_etude" class="form-control" required>
                        <option value="">Sélectionner un niveau</option>
                        <option value="Licence I">Licence I</option>
                        <option value="Licence II">Licence II</option>
                        <option value="Licence III">Licence III</option>
                        <option value="Master I">Master I</option>
                        <option value="Master II">Master II</option>
                        <option value="Doctorat">Doctorat</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="modify-matiere" class="form-label">Matière</label>
                    <select name="matiere" id="modify-matiere" class="form-control" required>
                        <option value="">Sélectionner une matière</option>
                        <option value="Mathématiques">Mathématiques</option>
                        <option value="Physique">Physique-Chimie</option>
                        <option value="SVT">Sciences de la Vie et de la Terre</option>
                        <option value="Français">Français</option>
                        <option value="Histoire">Histoire</option>
                        <option value="Géographie">Géographie</option>
                        <option value="Arabe">Arabe</option>
                        <option value="Anglais">Anglais</option>
                        <option value="Philosophie">Philosophie</option>
                        <option value="Informatique">Informatique</option>
                        <option value="Informatique">Education Physique et Sportive</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="modify-adresse" class="form-label">Adresse</label>
                    <input type="text" id="modify-adresse" name="adresse" class="form-control">
                </div>
                <div class="form-group">
                    <label for="modify-tel" class="form-label">Téléphone</label>
                    <input type="tel" id="modify-tel" name="tel" class="form-control" pattern="[0-9]{11}" maxlength="11" required>
                    <small class="text-muted">Format: 23512345678 (11 chiffres)</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" id="cancelModifyBtn">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary" name="modifier_enseignant">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Fonctions utilitaires
        function formatPhoneNumber(phone) {
            return phone.replace(/(\d{3})(\d{2})(\d{2})(\d{2})(\d{2})/, '$1 $2 $3 $4 $5');
        }

        // Gestion des modals
        const addModal = document.getElementById('addTeacherModal');
        const modifyModal = document.getElementById('modifyTeacherModal');
        
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
        const addBtn = document.getElementById('addTeacherBtn');
        const addFirstBtn = document.getElementById('addFirstTeacherBtn');
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

        function openModifyModal(enseignant) {
            document.getElementById('modify-id').value = enseignant.id;
            document.getElementById('modify-nom').value = enseignant.nom;
            document.getElementById('modify-prenom').value = enseignant.prenom;
            document.getElementById('modify-sexe').value = enseignant.sexe;
            document.getElementById('modify-niveau_etude').value = enseignant.niveau_etude;
            document.getElementById('modify-matiere').value = enseignant.matiere;
            document.getElementById('modify-adresse').value = enseignant.adresse || '';
            document.getElementById('modify-tel').value = enseignant.tel || '';

            showModal(modifyModal);
        }

        modifyForms.forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                const enseignant = <?= json_encode(array_column($enseignants, null, 'id')) ?>[id];
                
                if (enseignant) {
                    openModifyModal(enseignant);
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
        const teachersTable = document.getElementById('teachersTable');
        
        if (searchInput && teachersTable) {
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const rows = teachersTable.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    if (row.querySelector('.empty-state')) return;
                    
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            });
        }

        // Validation des formulaires
        const addForm = document.getElementById('addForm');
        const modifyForm = document.getElementById('modifyForm');
        
        if (addForm) {
            addForm.addEventListener('submit', function(e) {
                const tel = this.querySelector('#tel');
                if (!/^[0-9]{11}$/.test(tel.value)) {
                    alert('Le numéro de téléphone doit contenir exactement 11 chiffres');
                    e.preventDefault();
                }
            });
        }
        
        if (modifyForm) {
            modifyForm.addEventListener('submit', function(e) {
                const tel = this.querySelector('#modify-tel');
                if (!/^[0-9]{11}$/.test(tel.value)) {
                    alert('Le numéro de téléphone doit contenir exactement 11 chiffres');
                    e.preventDefault();
                }
            });
        }

        // Confirmation avant suppression
        const deleteForms = document.querySelectorAll('.delete-form');
        deleteForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Êtes-vous sûr de vouloir supprimer cet enseignant ? Cette action est irréversible.')) {
                    e.preventDefault();
                }
            });
        });

        // Formatage automatique du téléphone
        const phoneInputs = document.querySelectorAll('input[type="tel"]');
        phoneInputs.forEach(input => {
            input.addEventListener('input', function() {
                this.value = this.value.replace(/\D/g, '').substring(0, 11);
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