-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 29 oct. 2025 à 15:41
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `campus_shop`
--

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`, `description`, `date_creation`) VALUES
(1, 'Livres', 'Livres, manuels, romans, BD', '2025-10-25 12:31:11'),
(2, 'Électronique', 'Appareils électroniques, téléphones, accessoires', '2025-10-25 12:31:11'),
(3, 'Vêtements', 'Mode, vêtements, chaussures, accessoires', '2025-10-25 12:31:11'),
(4, 'Accessoires', 'Divers accessoires utiles', '2025-10-25 12:31:11'),
(5, 'Alimentation', 'Produits alimentaires, snacks, boissons', '2025-10-25 12:31:11'),
(6, 'Services', 'Services proposés sur le campus', '2025-10-25 12:31:11'),
(7, 'Livres', 'Livres, manuels, romans, BD', '2025-10-25 12:33:28'),
(8, 'Électronique', 'Appareils électroniques, téléphones, accessoires', '2025-10-25 12:33:28'),
(9, 'Vêtements', 'Mode, vêtements, chaussures, accessoires', '2025-10-25 12:33:28'),
(10, 'Accessoires', 'Divers accessoires utiles', '2025-10-25 12:33:28'),
(11, 'Alimentation', 'Produits alimentaires, snacks, boissons', '2025-10-25 12:33:28'),
(12, 'Services', 'Services proposés sur le campus', '2025-10-25 12:33:28'),
(13, 'meuble', NULL, '2025-10-26 01:15:31'),
(14, 'idiot', NULL, '2025-10-28 09:48:18'),
(15, 'compte', NULL, '2025-10-28 12:28:45');

-- --------------------------------------------------------

--
-- Structure de la table `faq`
--

CREATE TABLE `faq` (
  `id` int(11) NOT NULL,
  `question` varchar(255) NOT NULL,
  `reponse` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `faq`
--

INSERT INTO `faq` (`id`, `question`, `reponse`) VALUES
(1, 'yo', 'yep mon tip xdqw?');

-- --------------------------------------------------------

--
-- Structure de la table `panier`
--

CREATE TABLE `panier` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `produit_id` int(11) DEFAULT NULL,
  `quantite` int(11) DEFAULT 1,
  `date_ajout` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id` int(11) NOT NULL,
  `nom` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `prix` float NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `image` varchar(255) DEFAULT NULL,
  `vues` int(11) DEFAULT 0,
  `note` float DEFAULT 0,
  `dateAjout` datetime DEFAULT current_timestamp(),
  `categorie_id` int(11) DEFAULT NULL,
  `options` text DEFAULT NULL,
  `utilisateur_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `nom`, `description`, `prix`, `stock`, `image`, `vues`, `note`, `dateAjout`, `categorie_id`, `options`, `utilisateur_id`) VALUES
(4, 'Calculatrice scientifique', 'Calculatrice Casio FX-991ES Plus', 15000, 10, 'https://via.placeholder.com/400x300', 10, 4.5, '2025-10-25 13:33:30', 2, NULL, NULL),
(5, 'Sweat Campus', 'Sweat à capuche confortable', 12000, 5, 'https://via.placeholder.com/400x300', 5, 4.8, '2025-10-25 13:33:30', 3, NULL, NULL),
(9, 'ssqfdfdsvfsfsdfg', 'dsfdsfds', 200, 2, 'assets/img/produits/prod_68fcd39bb1848.jpg', 0, 0, '2025-10-25 14:41:48', 4, '[\"\",\"\",\"\"]', NULL),
(10, 'dqsdqs', 'fdsfsd', 1202, 55, 'assets/img/produits/prod_68fd56baa353f.jpg', 0, 0, '2025-10-26 00:01:14', 4, '[\"\",\"\",\"\"]', NULL),
(11, 'sdsfdf', 'gfsgfd', 100, 12, 'assets/img/produits/prod_68fd76339dce3.jpg', 0, 0, '2025-10-26 02:15:31', 13, '[\"\",\"\",\"\"]', NULL),
(12, 'sdsfdf', 'gfsgfd', 100, 12, 'assets/img/produits/prod_68fd77ab601ce.jpg', 0, 0, '2025-10-26 02:21:47', 13, '[\"\",\"\",\"\"]', NULL),
(13, 'fgfg', 'hghh', 25, 255, 'assets/img/produits/prod_68fd79c336a8e.jpg', 0, 0, '2025-10-26 02:30:43', 13, '[\" jn\",\"bjbj\",\"nbb\"]', NULL),
(14, 'hjhj', 'jbnjjn', 20, 1, 'assets/img/produits/prod_68fd7a6db4e87.jpg', 0, 0, '2025-10-26 02:33:33', 13, '[\"jhjh\",\"hgbhghj\",\"hggh\"]', NULL),
(15, 'hbkkhb', 'dfdfd', 54, 4, 'assets/img/produits/prod_68fd830065cc7.png', 0, 0, '2025-10-26 03:10:08', 4, '[\"fdfd\",\"fdff\",\"dfd\"]', NULL),
(16, 'hjkhjkh', 'hjhjghkj', 2, 1, 'assets/img/produits/prod_68fd844721a5e.jpg', 0, 0, '2025-10-26 03:15:35', 4, '[\"jhjhj\",\"jbj\",\"kjkj\"]', NULL),
(17, 'hhjl', 'hghgh', 200, 2, 'assets/img/produits/prod_68fd857719aab.jpg', 0, 0, '2025-10-26 03:20:39', 4, '[\"ghj\",\"khghhk\",\"cdxgxf\"]', NULL),
(18, 'gfhjh', 'khgkh', 2000, 2, 'assets/img/prod_68fd9aece409d_photo_2025-10-14_03-20-14.jpg', 0, 0, '2025-10-26 04:52:13', 4, NULL, NULL),
(19, 'naruto', 'sjdbjbfdjsbhdjmfbdj', 2580, 1, 'assets/img/prod_69009162b1a1d_photo_2025-10-11_19-56-36.jpg', 0, 0, '2025-10-28 10:48:18', 14, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `signalements`
--

CREATE TABLE `signalements` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `produit_id` int(11) DEFAULT NULL,
  `motif` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date_signalement` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `whatsapp` varchar(30) DEFAULT NULL,
  `motdepasse` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `date_inscription` datetime DEFAULT current_timestamp(),
  `role` varchar(30) DEFAULT 'client',
  `livreur_nom` varchar(100) DEFAULT NULL,
  `livreur_transport` varchar(100) DEFAULT NULL,
  `livreur_residence` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `email`, `whatsapp`, `motdepasse`, `photo`, `date_inscription`, `role`, `livreur_nom`, `livreur_transport`, `livreur_residence`) VALUES
(3, 'stive', 'tiktokstivo@gmail.com', '672108067', '$2y$10$HH4utfVouh0yWjXS8u6I7.jxvNmrNMprKhjF7Ck2EiTWSzQm7TWGu', NULL, '2025-10-28 13:18:53', 'acheteur', NULL, NULL, NULL),
(4, 'admin', 'admin@campusshop.com', '654005403', '0192023a7bbd73250516f069df18b500', NULL, '2025-10-29 13:17:43', 'admin', NULL, NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `panier`
--
ALTER TABLE `panier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`),
  ADD KEY `produit_id` (`produit_id`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categorie_id` (`categorie_id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `signalements`
--
ALTER TABLE `signalements`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `panier`
--
ALTER TABLE `panier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `signalements`
--
ALTER TABLE `signalements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `panier`
--
ALTER TABLE `panier`
  ADD CONSTRAINT `panier_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `panier_ibfk_2` FOREIGN KEY (`produit_id`) REFERENCES `produits` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `produits`
--
ALTER TABLE `produits`
  ADD CONSTRAINT `produits_ibfk_1` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `produits_ibfk_2` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
