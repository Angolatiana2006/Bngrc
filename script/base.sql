CREATE DATABASE IF NOT EXISTS bngrc;
USE bngrc;


CREATE TABLE villes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);
INSERT INTO villes (name) VALUES
     ('Antananarivo'),
     ('Toamasina'),
     ('Fianarantsoa'),
     ('Mahajanga'),
     ('Toliara');



CREATE TABLE besoins_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('nature','materiaux','argent','autres') NOT NULL,
    name VARCHAR(100) NOT NULL
);
INSERT INTO besoins_types (type, name) VALUES
     ('nature', 'Arbres'),
     ('nature', 'Plantes'),
     ('materiaux', 'Ciment'),
     ('materiaux', 'Bois'),
     ('argent', 'Fonds urgents'),
     ('autres', 'Équipement scolaire');





CREATE TABLE besoins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ville_id INT NOT NULL,
    besoin_type_id INT NOT NULL,
    quantite DECIMAL(10,2) NOT NULL,
    unite VARCHAR(20) DEFAULT NULL, 
    FOREIGN KEY (ville_id) REFERENCES villes(id) ON DELETE CASCADE,
    FOREIGN KEY (besoin_type_id) REFERENCES besoins_types(id) ON DELETE CASCADE
);
ALTER TABLE besoins ADD COLUMN date_creation DATETIME DEFAULT CURRENT_TIMESTAMP;
INSERT INTO besoins (ville_id, besoin_type_id, quantite, unite) VALUES
     (1, 1, 100, 'arbres'),
     (1, 3, 500, 'kg'),
     (2, 2, 200, 'plantes'),
     (2, 5, 1000, 'Ar'),
     (3, 4, 50, 'bois'),
     (4, 6, 30, 'kits'),
     (5, 3, 300, 'kg');


CREATE TABLE dons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    besoin_type_id INT NOT NULL,
    quantite DECIMAL(10,2) NOT NULL,
    date_don DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (besoin_type_id) REFERENCES besoins_types(id) ON DELETE CASCADE
);
INSERT INTO dons (besoin_type_id, quantite) VALUES
     (1, 20),
     (3, 100),
     (5, 500),
     (6, 10);



CREATE TABLE attributions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    besoin_id INT NOT NULL,
    don_id INT NOT NULL,
    quantite DECIMAL(10,2) NOT NULL,
    date_attribution DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (besoin_id) REFERENCES besoins(id) ON DELETE CASCADE,
    FOREIGN KEY (don_id) REFERENCES dons(id) ON DELETE CASCADE
);
INSERT INTO attributions (besoin_id, don_id, quantite) VALUES
     (1, 1, 20),
     (2, 2, 100),
     (4, 3, 200),
     (6, 4, 10);



CREATE TABLE prix_unitaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    besoin_type_id INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    date_mise_a_jour DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (besoin_type_id) REFERENCES besoins_types(id) ON DELETE CASCADE,
    UNIQUE KEY unique_besoin_type (besoin_type_id)
);
INSERT INTO prix_unitaires (besoin_type_id, prix_unitaire) VALUES
     (1, 5000),   
     (2, 3000),   
     (3, 25000),  
     (4, 15000),  
     (5, 1),      
     (6, 10000);  


CREATE TABLE achats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    don_id INT NOT NULL,
    besoin_type_id INT NOT NULL,
    quantite DECIMAL(10,2) NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    montant_total DECIMAL(10,2) NOT NULL,
    fournisseur VARCHAR(255),
    date_achat DATETIME DEFAULT CURRENT_TIMESTAMP,
    ville_id INT NOT NULL, 
    besoin_id INT, 
    FOREIGN KEY (don_id) REFERENCES dons(id) ON DELETE CASCADE,
    FOREIGN KEY (besoin_type_id) REFERENCES besoins_types(id) ON DELETE CASCADE,
    FOREIGN KEY (ville_id) REFERENCES villes(id) ON DELETE CASCADE,
    FOREIGN KEY (besoin_id) REFERENCES besoins(id) ON DELETE SET NULL
);

ALTER TABLE achats ADD COLUMN ville_id INT NULL AFTER montant_total;


ALTER TABLE achats ADD FOREIGN KEY (ville_id) REFERENCES villes(id) ON DELETE SET NULL;


INSERT INTO prix_unitaires (besoin_type_id, prix_unitaire) VALUES
(1, 5000),   -- Arbres
(2, 3000),   -- Plantes
(3, 25000),  -- Ciment
(4, 15000),  -- Bois
(5, 1),      -- Fonds urgents (1 Ar = 1 Ar)
(6, 10000);  -- Équipement scolaire
