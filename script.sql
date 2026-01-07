CREATE DATABASE IF NOT EXISTS gestion_stock;
USE gestion_stock;

-- 1. Table des catégories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT
);

-- 2. Table des produits
CREATE TABLE produits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    description TEXT,
    prix_unitaire DECIMAL(10, 2) NOT NULL,
    quantite_stock INT DEFAULT 0,
    categorie_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- 3. Table des mouvements de stock
CREATE TABLE mouvements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produit_id INT NOT NULL,
    type_mouvement ENUM('entree', 'sortie') NOT NULL,
    quantite INT NOT NULL,
    date_mouvement TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    commentaire VARCHAR(255),
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE
);

-- 4. Table des utilisateurs
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_utilisateur VARCHAR(50) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('admin', 'employe') DEFAULT 'employe'
);

-- Table des Fournisseurs
CREATE TABLE fournisseurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    contact VARCHAR(100),
    telephone VARCHAR(20),
    email VARCHAR(100),
    adresse TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des Clients
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    telephone VARCHAR(20),
    email VARCHAR(100),
    adresse TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- On ajoute les colonnes dans la table mouvements pour faire le lien
ALTER TABLE mouvements ADD COLUMN client_id INT NULL;
ALTER TABLE mouvements ADD COLUMN fournisseur_id INT NULL;
ALTER TABLE mouvements ADD CONSTRAINT fk_mouv_client FOREIGN KEY (client_id) REFERENCES clients(id);
ALTER TABLE mouvements ADD CONSTRAINT fk_mouv_fourn FOREIGN KEY (fournisseur_id) REFERENCES fournisseurs(id);