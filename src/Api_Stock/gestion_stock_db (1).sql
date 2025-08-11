-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 11, 2025 at 04:35 AM
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
-- Table structure for table `approvisionnement`
--

CREATE TABLE `approvisionnement` (
  `id_approvisionnement` int(11) NOT NULL,
  `id_User` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `id_fournisseur` int(11) NOT NULL,
  `date_approvisionnement` date NOT NULL,
  `quantite` int(11) NOT NULL,
  `prix_unitaire` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `approvisionnement`
--

INSERT INTO `approvisionnement` (`id_approvisionnement`, `id_User`, `id_produit`, `id_fournisseur`, `date_approvisionnement`, `quantite`, `prix_unitaire`) VALUES
(2, 1, 24, 1, '2025-08-11', 1040, 15.00);

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `sexe` text NOT NULL,
  `adresse` text DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `nom`, `prenom`, `sexe`, `adresse`, `telephone`, `email`, `date_creation`) VALUES
(1, 'Julien', 'King', 'M', 'Katindo', '0904108947', 'josuejosue@gmail.com', '2025-06-18 10:49:02'),
(12, 'IRAGI', 'Herva', 'M', 'Katoyi', '0903456748', 'herveiragi@gmail.com', '2025-07-25 21:18:16'),
(13, 'KIBIHIRA', 'ESTHER', 'F', 'Q.BOJOVU', '0972232442', 'kibihiraesther@gmail.com', '2025-08-04 10:40:03');

-- --------------------------------------------------------

--
-- Table structure for table `commandes`
--

CREATE TABLE `commandes` (
  `id` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `Id_appro` int(11) NOT NULL,
  `id_User` int(11) NOT NULL,
  `quantite` int(11) NOT NULL,
  `date_commande` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `commandes`
--

INSERT INTO `commandes` (`id`, `id_client`, `Id_appro`, `id_User`, `quantite`, `date_commande`) VALUES
(21, 13, 2, 1, 800, '2025-08-11 01:06:54');

-- --------------------------------------------------------

--
-- Table structure for table `fournisseur`
--

CREATE TABLE `fournisseur` (
  `id_fournisseur` int(11) NOT NULL,
  `nom_fournisseur` varchar(100) NOT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fournisseur`
--

INSERT INTO `fournisseur` (`id_fournisseur`, `nom_fournisseur`, `adresse`, `telephone`, `email`) VALUES
(1, 'Fournisseur_KMJ', 'Goma', '0990674538', 'fournisseur@gmail.com');

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
(21, 'Carburant', 20000, 1, 'entrée', '2025-07-18 10:11:50'),
(22, 'Carburant', 3000, 1, 'entrée', '2025-07-18 10:21:34'),
(23, 'Carburant', 5000, 2, 'entrée', '2025-07-18 10:28:25'),
(24, 'djkhjzhjzhz', 21, 1, 'entrée', '2025-07-18 11:09:25'),
(25, 'djkhjzhjzhz', 21, 1, 'entrée', '2025-07-18 11:09:25'),
(26, 'HGJHGHJGSJHS', 34, 1, 'entrée', '2025-07-18 11:10:07'),
(27, 'HDJHDJHKJDHD', 12, 23, 'entrée', '2025-07-18 11:27:50'),
(28, 'SKJHSJHJHS', 10, 1, 'entrée', '2025-07-18 11:29:28'),
(29, 'Carburant', 60, 1, 'sortie', '2025-07-18 16:55:03'),
(30, 'Essence', 3000, 1, 'entrée', '2025-07-18 18:07:38'),
(31, 'Diesel(ou Gazole)', 5000, 2, 'entrée', '2025-07-18 19:06:11'),
(32, 'Kérosène', 4000, 1, 'entrée', '2025-07-18 19:07:56'),
(33, 'Kérosène', 300, 1, 'sortie', '2025-07-18 19:09:48'),
(34, 'Essence', 100, 1, 'sortie', '2025-07-25 21:18:50'),
(35, 'Diesel(ou Gazole)', 50, 2, 'sortie', '2025-07-26 20:00:40'),
(36, 'Kérosène', 23, 1, 'sortie', '2025-07-30 12:54:25'),
(37, 'Essence', 10, 1, 'sortie', '2025-07-31 08:30:11'),
(38, 'Kérosène', 10, 1, 'sortie', '2025-08-04 10:35:10'),
(39, 'Essence', 20, 1, 'sortie', '2025-08-04 10:41:01'),
(40, 'Essence', 10, 1, 'sortie', '2025-08-05 12:54:53'),
(41, 'Essence', 50, 15, 'sortie', '2025-08-10 23:55:45'),
(42, 'Essence', 900, 15, 'sortie', '2025-08-11 01:06:54');

-- --------------------------------------------------------

--
-- Table structure for table `produit`
--

CREATE TABLE `produit` (
  `id` int(11) NOT NULL,
  `designation` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produit`
--

INSERT INTO `produit` (`id`, `designation`, `description`, `date_creation`) VALUES
(24, 'Essence', 'Le carburant désigne toute substance utilisée pour la bonne usage', '2025-07-18 18:02:09');

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
(1, 'SHABANI', 'Franck', 'chabanifranck@gmail.com', '0990345645', '2Lampes', '[object Object]', '$2y$10$aaTQ/VaSAPT1/XS2iCOJHecie7DGKF5KSwdnUv3HeXaUDJukWiMLK', 'Admin', '2025-06-20 11:11:13', '2025-07-30 12:48:01'),
(6, 'BUHUNI', 'nathalie', 'nathybuuni04@gmail.com', '0977523456', 'AROXI', '[object Object]', '$2y$10$RDJTS6/iZKGmBp/Lgs6nL.xjOpa0zlcwBdd2o4/2by6JXR3u8E2J.', 'Comptable', '2025-06-26 20:32:04', '2025-08-11 02:15:48'),
(8, 'NGOSUMA', 'Cadette', 'ngosumacadette@gmail.com', '099987345', 'ILPGL', '[object Object]', '$2y$10$e22C8SvocEsUK.d1obr6hOiXD92O6yoUA6XN3FQZIHSlexomuesr6', 'Gerant', '2025-07-28 11:14:49', '2025-08-09 22:47:56'),
(9, 'BAHIGABOSE', 'Enock', 'Bahigaboseenock@gmail.com', '0826461360', 'BIRERE', '[object Object]', '$2y$10$8UfPDa6atT5IwgpSWdYoMefBOcGagLNUHPR.arKrUHArwqaBX3PO.', 'Gerant', '2025-07-28 11:19:05', '2025-08-11 02:10:34');

-- --------------------------------------------------------

--
-- Table structure for table `vente`
--

CREATE TABLE `vente` (
  `id_vente` int(11) NOT NULL,
  `id_produit` int(11) NOT NULL,
  `id_commande` int(11) NOT NULL,
  `quantite` int(11) NOT NULL CHECK (`quantite` > 0),
  `prix_unitaire` decimal(10,2) NOT NULL,
  `date_vente` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `approvisionnement`
--
ALTER TABLE `approvisionnement`
  ADD PRIMARY KEY (`id_approvisionnement`),
  ADD KEY `id_fournisseur` (`id_fournisseur`),
  ADD KEY `approvisionnement_ibfk_1` (`id_produit`),
  ADD KEY `approvisionnement_ibfk_3` (`id_User`);

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
  ADD KEY `commandes_ibfk_3` (`id_User`),
  ADD KEY `commandes_ibfk_2` (`Id_appro`);

--
-- Indexes for table `fournisseur`
--
ALTER TABLE `fournisseur`
  ADD PRIMARY KEY (`id_fournisseur`);

--
-- Indexes for table `mouvements`
--
ALTER TABLE `mouvements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produit`
--
ALTER TABLE `produit`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `telephone` (`telephone`);

--
-- Indexes for table `vente`
--
ALTER TABLE `vente`
  ADD PRIMARY KEY (`id_vente`),
  ADD KEY `id_commande` (`id_commande`),
  ADD KEY `vente_ibfk_1` (`id_produit`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `approvisionnement`
--
ALTER TABLE `approvisionnement`
  MODIFY `id_approvisionnement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `commandes`
--
ALTER TABLE `commandes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `fournisseur`
--
ALTER TABLE `fournisseur`
  MODIFY `id_fournisseur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mouvements`
--
ALTER TABLE `mouvements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `produit`
--
ALTER TABLE `produit`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `vente`
--
ALTER TABLE `vente`
  MODIFY `id_vente` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `approvisionnement`
--
ALTER TABLE `approvisionnement`
  ADD CONSTRAINT `approvisionnement_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id`),
  ADD CONSTRAINT `approvisionnement_ibfk_2` FOREIGN KEY (`id_fournisseur`) REFERENCES `fournisseur` (`id_fournisseur`),
  ADD CONSTRAINT `approvisionnement_ibfk_3` FOREIGN KEY (`id_User`) REFERENCES `utilisateurs` (`id`);

--
-- Constraints for table `commandes`
--
ALTER TABLE `commandes`
  ADD CONSTRAINT `commandes_ibfk_1` FOREIGN KEY (`id_client`) REFERENCES `clients` (`id`),
  ADD CONSTRAINT `commandes_ibfk_2` FOREIGN KEY (`Id_appro`) REFERENCES `approvisionnement` (`id_approvisionnement`),
  ADD CONSTRAINT `commandes_ibfk_3` FOREIGN KEY (`id_User`) REFERENCES `utilisateurs` (`id`);

--
-- Constraints for table `vente`
--
ALTER TABLE `vente`
  ADD CONSTRAINT `vente_ibfk_1` FOREIGN KEY (`id_produit`) REFERENCES `produit` (`id`),
  ADD CONSTRAINT `vente_ibfk_2` FOREIGN KEY (`id_commande`) REFERENCES `commandes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
