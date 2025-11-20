-- SQL: simple blog schema + sample queries (PostgreSQL-compatible)

BEGIN;

CREATE TABLE Materiel (
    id_Materiel INT AUTO_INCREMENT PRIMARY KEY,
    nom_materiel VARCHAR(100) NOT NULL,
    description_materiel TEXT,
    type_materiel VARCHAR(50),
    stock_total INT NOT NULL DEFAULT 0,
    stock_disponible INT NOT NULL DEFAULT 0,
    Empruntable BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE Fabriquer (
    id_fabriqué INT,
    id_composant INT,
    PRIMARY KEY (id_fabriqué,id_composant),
    FOREIGN KEY (id_fabriqué) REFERENCES Materiel(id_Materiel),
    FOREIGN KEY (id_composant) REFERENCES Materiel(id_Materiel)
);

CREATE TABLE Admin (
    id_admin INT AUTO_INCREMENT PRIMARY KEY,
    email_admin VARCHAR(100) NOT NULL UNIQUE,
    hash_admin VARCHAR(255) NOT NULL
);

CREATE TABLE Emprunt (
    id_emprunt INT AUTO_INCREMENT PRIMARY KEY,
    motif_emprunt TEXT,
    email_emprunt VARCHAR(100) NOT NULL,
    date_emprunt DATE,
    date_retour_prevue DATE,
    date_retour_reelle DATE NULL,
    statut_emprunt VARCHAR(50) NOT NULL DEFAULT 'en cours',
    id_admin INT,
    FOREIGN KEY(id_admin) REFERENCES Admin(id_admin)
);

CREATE TABLE Emprunter (
    id_emprunt INT,
    id_Materiel INT,
    PRIMARY KEY (id_emprunt, id_Materiel),
    FOREIGN KEY (id_emprunt) REFERENCES Emprunt(id_emprunt),
    FOREIGN KEY (id_Materiel) REFERENCES Materiel(id_Materiel)
);