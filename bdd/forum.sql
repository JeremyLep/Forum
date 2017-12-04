-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  lun. 04 déc. 2017 à 14:59
-- Version du serveur :  5.7.19
-- Version de PHP :  7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `forum`
--

-- --------------------------------------------------------

--
-- Structure de la table `discussion`
--

DROP TABLE IF EXISTS `discussion`;
CREATE TABLE IF NOT EXISTS `discussion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auteur_id` int(11) DEFAULT NULL,
  `theme_id` int(11) DEFAULT NULL,
  `date` datetime NOT NULL,
  `contenu` varchar(5000) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `IDX_C0B9F90F60BB6FE6` (`auteur_id`),
  KEY `IDX_C0B9F90F59027487` (`theme_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `discussion`
--

INSERT INTO `discussion` (`id`, `auteur_id`, `theme_id`, `date`, `contenu`) VALUES
(1, 1, 3, '2017-12-04 00:00:00', 'Sed quis luctus nibh. Etiam lacinia eros ante, a faucibus ligula pretium in. Maecenas euismod fringilla libero sit amet venenatis. Vivamus eget erat non justo elementum venenatis non aliquet metus. Mauris nec rutrum ligula, id luctus enim. Vestibulum vel commodo nulla. Donec quis diam vitae metus auctor aliquet. Vestibulum ullamcorper diam id consequat mattis.'),
(2, 2, 3, '2017-12-04 08:13:14', 'Sed quis luctus nibh. Etiam lacinia eros ante, a faucibus ligula pretium in. Maecenas euismod fringilla libero sit amet venenatis. Vivamus eget erat non justo elementum venenatis non aliquet metus. Mauris nec rutrum ligula, id luctus enim. Vestibulum vel commodo nulla. Donec quis diam vitae metus auctor aliquet. Vestibulum ullamcorper diam id consequat mattis.'),
(3, 1, 3, '2017-12-04 15:43:35', 'Sed quis luctus nibh. Etiam lacinia eros ante, a faucibus ligula pretium in. Maecenas euismod fringilla libero sit amet venenatis. Vivamus eget erat non justo elementum venenatis non aliquet metus. Mauris nec rutrum ligula, id luctus enim. Vestibulum vel commodo nulla. Donec quis diam vitae metus auctor aliquet. Vestibulum ullamcorper diam id consequat mattis.'),
(4, 2, 1, '2017-12-05 02:22:15', 'Sed quis luctus nibh. Etiam lacinia eros ante, a faucibus ligula pretium in. Maecenas euismod fringilla libero sit amet venenatis. Vivamus eget erat non justo elementum venenatis non aliquet metus. Mauris nec rutrum ligula, id luctus enim. Vestibulum vel commodo nulla. Donec quis diam vitae metus auctor aliquet. Vestibulum ullamcorper diam id consequat mattis.'),
(5, 1, 2, '2017-12-06 09:28:41', 'Sed quis luctus nibh. Etiam lacinia eros ante, a faucibus ligula pretium in. Maecenas euismod fringilla libero sit amet venenatis. Vivamus eget erat non justo elementum venenatis non aliquet metus. Mauris nec rutrum ligula, id luctus enim. Vestibulum vel commodo nulla. Donec quis diam vitae metus auctor aliquet. Vestibulum ullamcorper diam id consequat mattis.'),
(6, 2, 2, '2017-12-04 05:32:17', 'Sed quis luctus nibh. Etiam lacinia eros ante, a faucibus ligula pretium in. Maecenas euismod fringilla libero sit amet venenatis. Vivamus eget erat non justo elementum venenatis non aliquet metus. Mauris nec rutrum ligula, id luctus enim. Vestibulum vel commodo nulla. Donec quis diam vitae metus auctor aliquet. Vestibulum ullamcorper diam id consequat mattis.'),
(7, 1, 5, '2017-12-05 04:52:45', 'Sed quis luctus nibh. Etiam lacinia eros ante, a faucibus ligula pretium in. Maecenas euismod fringilla libero sit amet venenatis. Vivamus eget erat non justo elementum venenatis non aliquet metus. Mauris nec rutrum ligula, id luctus enim. Vestibulum vel commodo nulla. Donec quis diam vitae metus auctor aliquet. Vestibulum ullamcorper diam id consequat mattis.'),
(8, 2, 4, '2017-12-06 00:00:00', 'Sed quis luctus nibh. Etiam lacinia eros ante, a faucibus ligula pretium in. Maecenas euismod fringilla libero sit amet venenatis. Vivamus eget erat non justo elementum venenatis non aliquet metus. Mauris nec rutrum ligula, id luctus enim. Vestibulum vel commodo nulla. Donec quis diam vitae metus auctor aliquet. Vestibulum ullamcorper diam id consequat mattis.');

-- --------------------------------------------------------

--
-- Structure de la table `membres`
--

DROP TABLE IF EXISTS `membres`;
CREATE TABLE IF NOT EXISTS `membres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pseudo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nom` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `prenom` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `tel` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ville` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `valid` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `membres`
--

INSERT INTO `membres` (`id`, `email`, `password`, `pseudo`, `nom`, `prenom`, `age`, `tel`, `ville`, `valid`) VALUES
(1, 'jeremy.leprince1@gmail.com', 'azerty', 'Jester', 'Leprince', 'Jérémy', 22, '0646221155', 'Amiens', 1),
(2, 'john.doe@gmail.com', 'azerty', 'JohnDoe', 'Doe', 'John', 34, '0645899665', 'St-Quentin', 1);

-- --------------------------------------------------------

--
-- Structure de la table `themes`
--

DROP TABLE IF EXISTS `themes`;
CREATE TABLE IF NOT EXISTS `themes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `sous_titre` varchar(700) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `themes`
--

INSERT INTO `themes` (`id`, `titre`, `sous_titre`) VALUES
(1, 'Discussion général', 'Discuter à propos de sport, musique, cinéma, jeux de société ou de tout ce que vous souhaitez.'),
(2, 'Introductions', 'New to the community? Please stop by, say hi and tell us a bit about yourself.'),
(3, 'Announcement', 'This forum features announcements from the community staff. If there is a new post in this forum, please check it out.'),
(4, 'Staff discussion', 'This forum is for private, staff member only discussions, usually pertaining to the community itself.'),
(5, 'Lorem Ipsum', 'Various versions have evolved over the years, sometimes by accident, sometimes on purpose passage of Lorem Ipsum (injected humour and the like).');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `discussion`
--
ALTER TABLE `discussion`
  ADD CONSTRAINT `FK_C0B9F90F59027487` FOREIGN KEY (`theme_id`) REFERENCES `themes` (`id`),
  ADD CONSTRAINT `FK_C0B9F90F60BB6FE6` FOREIGN KEY (`auteur_id`) REFERENCES `membres` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
