-- =============================================
-- Base de données : patisserie_db
-- =============================================

CREATE DATABASE IF NOT EXISTS patisserie_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE patisserie_db;

-- Table users
CREATE TABLE IF NOT EXISTS users (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    nom      VARCHAR(100) NOT NULL,
    email    VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role     ENUM('client','admin','super_admin') NOT NULL DEFAULT 'client',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table produits
CREATE TABLE IF NOT EXISTS produits (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(150) NOT NULL,
    prix        DECIMAL(10,2) NOT NULL,
    description TEXT,
    image       VARCHAR(255) DEFAULT 'default.jpg',
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Table commandes
CREATE TABLE IF NOT EXISTS commandes (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    produit_id INT NOT NULL,
    quantite   INT NOT NULL DEFAULT 1,
    statut     ENUM('en préparation','prête','livrée') NOT NULL DEFAULT 'en préparation',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)    REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (produit_id) REFERENCES produits(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =============================================
-- Données de démonstration
-- =============================================

-- Utilisateurs (passwords hachés avec password_hash PHP)
-- Mot de passe : password
INSERT INTO users (nom, email, password, role) VALUES
('Marie Dupont',       'client@gmail.com',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'client'),
('Admin Pâtisserie',   'admin@gmail.com',        '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('Super Admin',        'superadmin@gmail.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'super_admin');

-- Produits
INSERT INTO produits (nom, prix, description, image) VALUES
('Gâteau au Chocolat',   25.00, 'Un délicieux gâteau au chocolat noir fondant, recouvert d\'une ganache onctueuse et décorée de copeaux de chocolat.', 'chocolat.jpg'),
('Gâteau d\'Anniversaire', 45.00, 'Gâteau à plusieurs étages personnalisable avec crème chantilly, fruits frais et décoration selon vos désirs.', 'anniversaire.jpg'),
('Cupcakes (lot de 6)',   18.00, 'Assortiment de 6 cupcakes moelleux avec topping crémeux, disponibles en vanille, chocolat et fraise.', 'cupcakes.jpg'),
('Tarte aux Fruits',     22.00, 'Tarte croustillante garnie d\'une crème pâtissière légère et de fruits de saison frais de qualité.', 'tarte.jpg'),
('Éclair au Café',       12.00, 'Pâte à choux légère, crème mousseline au café intense, glacée avec un fondant brillant fait maison.', 'eclair.jpg'),
('Mille-feuille',        20.00, 'Trois couches de pâte feuilletée caramélisée, garnies d\'une généreuse crème pâtissière à la vanille.', 'millefeuille.jpg');

-- Commandes
INSERT INTO commandes (user_id, produit_id, quantite, statut) VALUES
(1, 1, 2, 'en préparation'),
(1, 3, 1, 'prête'),
(1, 4, 1, 'livrée');
