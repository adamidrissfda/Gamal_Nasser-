CREATE TABLE administration (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    mot_de_passe CHAR(64) NOT NULL 
);

CREATE TABLE enseignants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50),
    prenom VARCHAR(50),
    sexe ENUM('M', 'F'),
    niveau_etude VARCHAR(100),
    matiere VARCHAR(100),
    adresse VARCHAR(255),
    tel VARCHAR(20)
);

CREATE TABLE eleves (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50),
    prenom VARCHAR(50),
    date_naissance DATE,
    sexe ENUM('M', 'F'),
    classe VARCHAR(50),
    adresse VARCHAR(255),
    tel_parent VARCHAR(20)
);


CREATE TABLE matieres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    coefficient INT NOT NULL DEFAULT 1
);

CREATE TABLE notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    eleve_id INT NOT NULL,
    matiere_id INT NOT NULL,
    note DECIMAL(5,2) NOT NULL,
    date_note DATE NOT NULL,
    type_note VARCHAR(50) NOT NULL,
    FOREIGN KEY (eleve_id) REFERENCES eleves(id) ON DELETE CASCADE,
    FOREIGN KEY (matiere_id) REFERENCES matieres(id) ON DELETE CASCADE
);



CREATE TABLE emploi_du_temps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jour VARCHAR(10) NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    matiere VARCHAR(100) NOT NULL,
    enseignant_id INT NULL,
    classe VARCHAR(50) NOT NULL,
    FOREIGN KEY (enseignant_id) REFERENCES enseignants(id) ON DELETE SET NULL
);
