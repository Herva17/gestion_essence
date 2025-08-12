-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 11, 2025 at 03:41 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gestion_stock_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `designation`, `description`, `date_creation`) VALUES
(9, 'Carburant', 'Carburant', '2025-06-25 20:16:21'),
(11, 'Diesel', 'Diesel', '2025-06-25 20:37:37');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `sexe` enum('Masculin','Féminin') NOT NULL,
  `adresse` text DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `nom`, `prenom`, `sexe`, `adresse`, `telephone`, `email`, `date_creation`) VALUES
(1, 'hervé', 'Herva', '', NULL, '0904108947', 'josuejosue@gmail.com', '2025-06-18 10:49:02'),
(2, 'hervé', 'Herva', '', NULL, '0904108940', 'josue@gmail.com', '2025-06-18 10:50:41'),
(3, 'hervé', 'Herva', '', NULL, '0904108948', 'jos@gmail.com', '2025-06-18 10:57:16'),
(4, 'hervé', 'Herva', '', NULL, '0904108943', 'jo@gmail.com', '2025-06-18 10:59:05'),
(5, 'hervé', 'Herva', '', NULL, '0904103290', 'josh@gmail.com', '2025-06-18 11:01:57');

-- --------------------------------------------------------

--
-- Table structure for table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `id_User` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `date_commande` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commandes`
--

INSERT INTO `commandes` (`id`, `id_client`, `id_produit`, `id_User`, `quantite`, `date_commande`) VALUES
(6, 1, 8, 6, 4, '2025-06-27 20:42:21'),
(7, 3, 9, 1, 21, '2025-06-27 21:58:38');

-- --------------------------------------------------------

--
-- Table structure for table `mouvements`
--

CREATE TABLE `mouvements` (
  `id` int(11) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `Quantite` int(11) NOT NULL,
  `Prix_Unitaire` decimal(11,0) NOT NULL,
  `type` varchar(60) NOT NULL,
  `date_mouvement` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mouvements`
--

INSERT INTO `mouvements` (`id`, `designation`, `Quantite`, `Prix_Unitaire`, `type`, `date_mouvement`) VALUES
(12, 'Essence', 2000, 1, 'entrée', '2025-06-25 21:09:05'),
(13, 'Essence', 3000, 1, 'entrée', '2025-06-25 21:19:07'),
(14, 'Essence', 5, 1, 'entrée', '2025-06-25 21:28:08'),
(15, 'Essence', 21, 1, 'sortie', '2025-06-27 21:58:38');

-- --------------------------------------------------------

--
-- Table structure for table `produits`
--

CREATE TABLE `produits` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `quantite` int(11) DEFAULT 0,
  `prix_unitaire` decimal(10,2) NOT NULL,
  `id_categorie` int(11) DEFAULT NULL,
  `id_User` int(11) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produits`
--

INSERT INTO `produits` (`id`, `nom`, `description`, `quantite`, `prix_unitaire`, `id_categorie`, `id_User`, `date_creation`) VALUES
(8, 'Essence', 'Essence', 2000, 1.00, 9, 1, '2025-06-25 21:09:05'),
(9, 'Essence', 'Essence', 2979, 1.00, 11, 1, '2025-06-25 21:19:07'),
(10, 'Essence', 'Essence', 5, 1.00, 9, 1, '2025-06-25 21:28:08');

-- --------------------------------------------------------

--
-- Table structure for table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `adresse` text DEFAULT NULL,
  `sexe` text DEFAULT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` text NOT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_modification` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `email`, `telephone`, `adresse`, `sexe`, `mot_de_passe`, `role`, `date_creation`, `date_modification`) VALUES
(1, 'Kabanangi', 'Emmanuel', 'emmanuelkabanangi@gmail.com', '0977404036', 'Katoyi', 'M', '$2y$10$Y1h8/zRxPzM1yJlHMlSY5OcJw/GRh98XhTxV7Tt/qSqVC0Z78S3ni', 'Admin', '2025-06-20 11:11:13', '2025-06-20 11:11:13'),
(6, 'IRAGI', 'Hervé', 'herveiragi@gmail.com', '0904108947', 'Himbi', '[object Object]', '$2y$10$2JXkgT1g/L9XzCQk6u06t.3EgZ9Gpwprt6x4BePLGfm/F6neY3bUa', 'Gerant', '2025-06-26 20:32:04', '2025-07-01 10:14:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `commandes`
--
ALTER TABLE `commandes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_client` (`id_client`),
  ADD KEY `id_produit` (`id_produit`),
  ADD KEY `commandes_ibfk_3` (`id_User`);

--
-- Indexes for table `mouvements`
--
ALTER TABLE `mouvements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_categorie` (`id_categorie`),
  ADD KEY `produits_ibfk_3` (`id_User`);

--
-- Indexes for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `telephone` (`telephone`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `mouvements`
--
ALTER TABLE `mouvements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `commandes_ibfk_2` FOREIGN KEY (`id_produit`) REFERENCES `produits` (`id`),
  ADD CONSTRAINT `commandes_ibfk_3` FOREIGN KEY (`id_User`) REFERENCES `utilisateurs` (`id`);

--
-- Constraints for table `produits`
--
ALTER TABLE `produits`
  ADD CONSTRAINT `produits_ibfk_1` FOREIGN KEY (`id_categorie`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `produits_ibfk_3` FOREIGN KEY (`id_User`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
