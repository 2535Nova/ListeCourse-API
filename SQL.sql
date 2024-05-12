-- Table utilisateur
CREATE TABLE Utilisateur (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255),
    mdp_hash VARCHAR(64) -- Stockage du hash du mot de passe en SHA-256
);

-- Table rayon
CREATE TABLE Rayon (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom_rayon VARCHAR(255)
);

-- Table produit
CREATE TABLE Produit (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255),
    commentaire VARCHAR(255),
    createur_id INT NOT NULL,
    ajout_caddie_id INT DEFAULT NULL,
    rayon_id INT,
    is_caddie BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (createur_id) REFERENCES Utilisateur(id),
    FOREIGN KEY (ajout_caddie_id) REFERENCES Utilisateur(id),
    FOREIGN KEY (rayon_id) REFERENCES Rayon(id)
);

INSERT INTO Rayon (nom_rayon) VALUES ('Bazar');
INSERT INTO Rayon (nom_rayon) VALUES ('Fruits et Légumes');
INSERT INTO Rayon (nom_rayon) VALUES ('Produits frais');
INSERT INTO Rayon (nom_rayon) VALUES ('Boucherie');
INSERT INTO Rayon (nom_rayon) VALUES ('Poissonnerie');
INSERT INTO Rayon (nom_rayon) VALUES ('Surgelés');
INSERT INTO Rayon (nom_rayon) VALUES ('Textile');
INSERT INTO Rayon (nom_rayon) VALUES ('Hygiène');
INSERT INTO Rayon (nom_rayon) VALUES ('Epicerie');
INSERT INTO Rayon (nom_rayon) VALUES ('Liquide');


INSERT INTO Utilisateur (nom, mdp_hash) VALUES 
('test', SHA2('test', 256)),
('admin', SHA2('admin', 256)),
('nova', SHA2('nova', 256)),
('franck', SHA2('franck', 256));
