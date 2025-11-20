-- SQL: simple blog schema + sample queries (PostgreSQL-compatible)



CREATE DATABASE bdfablab;
USE bdfablab;

CREATE TABLE Materiel (
    idMateriel INT AUTO_INCREMENT PRIMARY KEY,
    nomMateriel VARCHAR(100) NOT NULL,
    descriptionMateriel TEXT,
    typeMateriel VARCHAR(50),
    stockTotal INT NOT NULL DEFAULT 0,
    stockDisponible INT NOT NULL DEFAULT 0,
    empruntable BOOLEAN NOT NULL DEFAULT TRUE
);

CREATE TABLE Fabriquer (
    idFabriqué INT,
    idComposant INT,
    quantité INT,
    PRIMARY KEY (idFabriqué,idComposant),
    FOREIGN KEY (idFabriqué) REFERENCES Materiel(idMateriel),
    FOREIGN KEY (idComposant) REFERENCES Materiel(idMateriel)
);

CREATE TABLE Admin (
    idAdmin INT AUTO_INCREMENT PRIMARY KEY,
    emailAdmin VARCHAR(100) NOT NULL UNIQUE,
    hashAdmin VARCHAR(255) NOT NULL
);

CREATE TABLE Emprunt (
    idEmprunt INT AUTO_INCREMENT PRIMARY KEY,
    motifEmprunt TEXT,
    emailEmprunt VARCHAR(100) NOT NULL,
    dateEmprunt DATE,
    dateRetourPrevue DATE,
    dateRetourReelle DATE NULL,
    statutEmprunt VARCHAR(50) NOT NULL DEFAULT 'en cours',
    idAdmin INT,
    FOREIGN KEY(idAdmin) REFERENCES Admin(idAdmin)
);

CREATE TABLE Emprunter (
    idEmprunt INT,
    idMateriel INT,
    quantité INT,
    PRIMARY KEY (idEmprunt, idMateriel),
    FOREIGN KEY (idEmprunt) REFERENCES Emprunt(idEmprunt),
    FOREIGN KEY (idMateriel) REFERENCES Materiel(idMateriel)
);