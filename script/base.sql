CREATE DATABASE IF NOT EXISTS bngrc;
USE bngrc;

-- Table des villes
CREATE TABLE villes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL
);

-- Table des types de besoins
CREATE TABLE besoins_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('nature','materiaux','argent','autres') NOT NULL,
    name VARCHAR(100) NOT NULL
);

-- Table des besoins par ville
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

-- Table des dons
CREATE TABLE dons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    besoin_type_id INT NOT NULL,
    quantite DECIMAL(10,2) NOT NULL,
    date_don DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (besoin_type_id) REFERENCES besoins_types(id) ON DELETE CASCADE
);

-- Table des attributions de dons aux besoins
CREATE TABLE attributions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    besoin_id INT NOT NULL,
    don_id INT NOT NULL,
    quantite DECIMAL(10,2) NOT NULL,
    date_attribution DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (besoin_id) REFERENCES besoins(id) ON DELETE CASCADE,
    FOREIGN KEY (don_id) REFERENCES dons(id) ON DELETE CASCADE
);

-- Table des prix unitaires (prix fixes)
CREATE TABLE prix_unitaires (
    id INT AUTO_INCREMENT PRIMARY KEY,
    besoin_type_id INT NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    date_mise_a_jour DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (besoin_type_id) REFERENCES besoins_types(id) ON DELETE CASCADE,
    UNIQUE KEY unique_besoin_type (besoin_type_id)
);

-- Table des achats
CREATE TABLE achats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    don_id INT NOT NULL,
    besoin_type_id INT NOT NULL,
    quantite DECIMAL(10,2) NOT NULL,
    prix_unitaire DECIMAL(10,2) NOT NULL,
    montant_total DECIMAL(10,2) NOT NULL,
    fournisseur VARCHAR(255),
    date_achat DATETIME DEFAULT CURRENT_TIMESTAMP,
    ville_id INT NOT NULL, -- Pour savoir à quelle ville est destiné l'achat
    besoin_id INT, -- Optionnel: lier directement au besoin
    FOREIGN KEY (don_id) REFERENCES dons(id) ON DELETE CASCADE,
    FOREIGN KEY (besoin_type_id) REFERENCES besoins_types(id) ON DELETE CASCADE,
    FOREIGN KEY (ville_id) REFERENCES villes(id) ON DELETE CASCADE,
    FOREIGN KEY (besoin_id) REFERENCES besoins(id) ON DELETE SET NULL
);
-- Ajouter la colonne ville_id à la table achats
ALTER TABLE achats ADD COLUMN ville_id INT NULL AFTER montant_total;

-- Ajouter la contrainte de clé étrangère
ALTER TABLE achats ADD FOREIGN KEY (ville_id) REFERENCES villes(id) ON DELETE SET NULL;

-- Ajout de quelques prix unitaires par défaut
INSERT INTO prix_unitaires (besoin_type_id, prix_unitaire) VALUES
(1, 5000),   -- Arbres
(2, 3000),   -- Plantes
(3, 25000),  -- Ciment
(4, 15000),  -- Bois
(5, 1),      -- Fonds urgents (1 Ar = 1 Ar)
(6, 10000);  -- Équipement scolaire
