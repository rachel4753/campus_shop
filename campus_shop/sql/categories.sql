-- Table des catégories
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Exemple d'insertion de catégories de base
INSERT INTO categories (nom, description) VALUES
('Électronique', 'Appareils électroniques, téléphones, accessoires'),
('Livres', 'Livres, manuels, romans, BD'),
('Vêtements', 'Mode, vêtements, chaussures, accessoires'),
('Alimentation', 'Produits alimentaires, snacks, boissons'),
('Services', 'Services proposés sur le campus');
