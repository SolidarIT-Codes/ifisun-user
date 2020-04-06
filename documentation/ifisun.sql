-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Client :  127.0.0.1
-- Généré le :  Lun 06 Avril 2020 à 10:36
-- Version du serveur :  5.7.14
-- Version de PHP :  7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `ifisun`
--

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

CREATE TABLE `client` (
  `idclient` bigint(20) NOT NULL,
  `nomclient` varchar(255) NOT NULL,
  `prenomclient` varchar(255) NOT NULL,
  `contactclient` varchar(255) NOT NULL,
  `emailclient` varchar(60) NOT NULL,
  `typeclient` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `client`
--

INSERT INTO `client` (`idclient`, `nomclient`, `prenomclient`, `contactclient`, `emailclient`, `typeclient`) VALUES
(9, 'Gaba', 'kenneth', '67341587', 'kenne@mail.fr', 'client');

-- --------------------------------------------------------

--
-- Structure de la table `clientdossier`
--

CREATE TABLE `clientdossier` (
  `idclientdossier` bigint(20) NOT NULL,
  `fkiddossier` bigint(20) NOT NULL,
  `fkidclient` bigint(10) NOT NULL,
  `statut` varchar(25) NOT NULL DEFAULT '1',
  `datevenu` datetime NOT NULL,
  `dateprochainrdv` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `clientdossier`
--

INSERT INTO `clientdossier` (`idclientdossier`, `fkiddossier`, `fkidclient`, `statut`, `datevenu`, `dateprochainrdv`) VALUES
(1, 2, 1, '1', '2019-09-05 14:06:00', '2019-08-09 14:06:00'),
(2, 1, 1, '3', '2019-07-28 15:10:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Structure de la table `complains`
--

CREATE TABLE `complains` (
  `id` int(11) NOT NULL,
  `member_id` int(10) UNSIGNED NOT NULL,
  `complain_type_id` tinyint(4) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `complain_meta`
--

CREATE TABLE `complain_meta` (
  `id` int(10) UNSIGNED NOT NULL,
  `complain_id` int(11) NOT NULL,
  `key` varchar(50) NOT NULL,
  `value` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `complain_types`
--

CREATE TABLE `complain_types` (
  `id` tinyint(4) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `photo` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `complain_types`
--

INSERT INTO `complain_types` (`id`, `name`, `description`, `photo`) VALUES
(3, 'Violence', '', ''),
(4, 'Harcelement', '', ''),
(5, 'Viole', '', '');

-- --------------------------------------------------------

--
-- Structure de la table `complain_updates`
--

CREATE TABLE `complain_updates` (
  `id` int(11) NOT NULL,
  `complain_id` int(11) NOT NULL,
  `moderator_id` int(10) UNSIGNED NOT NULL,
  `description` text NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `complain_uploads`
--

CREATE TABLE `complain_uploads` (
  `id` int(10) UNSIGNED NOT NULL,
  `complain_id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `link` text NOT NULL,
  `description` text,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `complain_violences`
--

CREATE TABLE `complain_violences` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `photo` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `denonce`
--

CREATE TABLE `denonce` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
  `telephone` int(11) NOT NULL,
  `victime` varchar(60) NOT NULL,
  `type` varchar(60) NOT NULL,
  `tel_vic` int(11) NOT NULL,
  `adresse_vic` varchar(60) NOT NULL,
  `description` text NOT NULL,
  `preuve` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `denonce`
--

INSERT INTO `denonce` (`id`, `nom`, `prenom`, `email`, `age`, `telephone`, `victime`, `type`, `tel_vic`, `adresse_vic`, `description`, `preuve`) VALUES
(1, 'Scott', 'Travis', 'yose@mail.travis', 22, 90210, 'Kylie Jenner', 'viole', 2248, 'Los Angeles', 'j\'entends tres souvent des cries de viol la nuit....', ''),
(2, 'tony', 'sossa', 'ts@bob.fr', 35, 158, 'BIGMICH', '1', 6598, 'FRANCE/pARIS', 'CELLULAIRE ? VICTOIRE ENUMM7RE', ''),
(3, 'zkhbkz', 'jezhbgdkze', 'he@ddbdjk.ff', 55, 55464654, 'jeke', 'Violence', 35435454, 'calavi', 'khshbdbkhdbscsdckshcckhs', ''),
(4, 'momo', 'skccd', 'kd@ldkd.dd', 4, 442444, 'rrjd', 'Violence', 35435454, 'calavi', 'sdhbdkbdddfc', 'BEANS_.pdf');

-- --------------------------------------------------------

--
-- Structure de la table `dossier`
--

CREATE TABLE `dossier` (
  `iddossier` bigint(20) NOT NULL,
  `libelledossier` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `dossier`
--

INSERT INTO `dossier` (`iddossier`, `libelledossier`) VALUES
(1, 'IMMOBILIER'),
(2, 'CREDIT'),
(3, 'FAMILLE'),
(4, 'AFFAIRE');

-- --------------------------------------------------------

--
-- Structure de la table `gestionacceuil`
--

CREATE TABLE `gestionacceuil` (
  `idgestionaccueil` bigint(20) NOT NULL,
  `hasrdv` int(2) NOT NULL,
  `fkidclient` bigint(20) NOT NULL,
  `collaborateurfkiduser` bigint(20) NOT NULL,
  `descriptionvisite` longtext,
  `secretairefkiduser` bigint(20) NOT NULL,
  `datearrive` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `gestionacceuil`
--

INSERT INTO `gestionacceuil` (`idgestionaccueil`, `hasrdv`, `fkidclient`, `collaborateurfkiduser`, `descriptionvisite`, `secretairefkiduser`, `datearrive`) VALUES
(1, 1, 6, 2, NULL, 1, '2019-07-25 22:47:28'),
(2, 2, 1, 2, NULL, 1, '2019-07-25 23:31:29'),
(3, 2, 1, 2, NULL, 1, '2019-07-25 23:32:30'),
(4, 2, 7, 3, 'ff', 1, '2019-07-27 12:57:35'),
(5, 2, 1, 5, NULL, 1, '2019-07-27 21:03:02');

-- --------------------------------------------------------

--
-- Structure de la table `groups`
--

CREATE TABLE `groups` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`) VALUES
(1, 'admin', 'Administrateur'),
(2, 'members', 'Abonné'),
(3, 'moderator', 'Modérateur');

-- --------------------------------------------------------

--
-- Structure de la table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) UNSIGNED NOT NULL,
  `ip_address` varchar(15) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `method` varchar(6) NOT NULL,
  `params` text,
  `api_key` varchar(40) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `time` int(11) NOT NULL,
  `rtime` float DEFAULT NULL,
  `authorized` varchar(1) NOT NULL,
  `response_code` smallint(3) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `note`
--

CREATE TABLE `note` (
  `idnote` bigint(20) NOT NULL,
  `fkiddossier` bigint(20) NOT NULL,
  `accueil` varchar(60) NOT NULL,
  `delai` varchar(60) NOT NULL,
  `dispo` varchar(60) NOT NULL,
  `clarte` varchar(60) NOT NULL,
  `suivi` varchar(60) NOT NULL,
  `efficacite` varchar(60) NOT NULL,
  `access` varchar(60) NOT NULL,
  `review` varchar(255) NOT NULL,
  `note` varchar(60) NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `note`
--

INSERT INTO `note` (`idnote`, `fkiddossier`, `accueil`, `delai`, `dispo`, `clarte`, `suivi`, `efficacite`, `access`, `review`, `note`, `date`) VALUES
(1, 1, 'Neutre', 'Neutre', 'Neutre', 'Neutre', 'Neutre', 'Neutre', 'Neutre', 'ml', '7', '2019-07-26');

-- --------------------------------------------------------

--
-- Structure de la table `options`
--

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `key` varchar(35) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `options`
--

INSERT INTO `options` (`id`, `key`, `value`) VALUES
(1, 'siteLogo', 'de2bf6b2498883657db7da1bcf9488bb.jpg'),
(2, 'siteAvatar', '2c36a605b5e360984a672fcfba817773.jpg'),
(3, 'siteName', 'Femin\'IT'),
(4, 'siteDescription', '© 2018 Feminit | Propulsée par Solidar\'IT'),
(5, 'siteBackgroundImage', '95930e2b96ff99b925e65198f58b7cec.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `plainte`
--

CREATE TABLE `plainte` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
  `telephone` int(11) NOT NULL,
  `type` varchar(60) NOT NULL,
  `description` text NOT NULL,
  `preuve` text,
  `effectue` int(11) NOT NULL DEFAULT '0',
  `partage` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `plainte`
--

INSERT INTO `plainte` (`id`, `nom`, `prenom`, `email`, `age`, `telephone`, `type`, `description`, `preuve`, `effectue`, `partage`) VALUES
(21, 'lol', 'momo', 'iji@kil.ko', 45, 123, 'Violence', 'jgfjggjvjv', 'allo', 0, 0),
(20, 'mal', 'mal', 'ddd@kl.dd', 45, 6464, 'Violence', 'eddq', 'Acumen_Network.pdf', 0, 0),
(19, 'lapinou', 'arghhh', 'lo@lr.fr', 24, 34335454, 'Viole', 'tgtgtgrgtr', 'yo', 0, 0),
(18, 'kolo', 'gg', 'kolo@gkg.gg', 54, 2147483647, 'Violence', 'vnghn', 'VID-20181224-WA0000.mp4', 0, 0),
(17, 'lol', 'molk', 'mm@ml.fr', 54, 659875, 'Harcelement', 'rrhyhh', '007.PNG', 0, 0),
(16, 'BOB', 'Angel', 'mmen@kjl.dk', 15, 469845, 'Harcelement', 'kgfnkjgjbbyt', 'icon.png', 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `rdvfutur`
--

CREATE TABLE `rdvfutur` (
  `idrdvfutur` bigint(20) NOT NULL,
  `fkidclient` bigint(20) NOT NULL,
  `collaborateurfkiduser` bigint(20) NOT NULL,
  `descriptionvisite` longtext,
  `secretairefkiduser` bigint(20) NOT NULL,
  `statut` int(1) DEFAULT '0',
  `datearrive` datetime NOT NULL,
  `dateretenu` datetime DEFAULT NULL,
  `isdo` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `rdvfutur`
--

INSERT INTO `rdvfutur` (`idrdvfutur`, `fkidclient`, `collaborateurfkiduser`, `descriptionvisite`, `secretairefkiduser`, `statut`, `datearrive`, `dateretenu`, `isdo`) VALUES
(1, 1, 5, NULL, 1, 0, '2019-07-27 21:11:37', '2019-11-08 12:52:00', 0);

-- --------------------------------------------------------

--
-- Structure de la table `rdvimmediat`
--

CREATE TABLE `rdvimmediat` (
  `idrdvimmediat` bigint(20) NOT NULL,
  `hasrdv` int(2) NOT NULL,
  `fkidclient` bigint(20) NOT NULL,
  `collaborateurfkiduser` bigint(20) NOT NULL,
  `descriptionvisite` longtext,
  `secretairefkiduser` bigint(20) NOT NULL,
  `statut` int(1) DEFAULT '0',
  `datearrive` datetime NOT NULL,
  `dateretenu` datetime DEFAULT NULL,
  `isdo` tinyint(1) NOT NULL DEFAULT '0',
  `isview` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `rdvimmediat`
--

INSERT INTO `rdvimmediat` (`idrdvimmediat`, `hasrdv`, `fkidclient`, `collaborateurfkiduser`, `descriptionvisite`, `secretairefkiduser`, `statut`, `datearrive`, `dateretenu`, `isdo`, `isview`) VALUES
(1, 2, 1, 5, NULL, 1, 2, '2019-07-28 21:11:37', '2019-07-27 22:59:00', 0, 0),
(2, 1, 1, 3, 'Visite amical', 1, 1, '2019-07-29 00:00:00', '2019-07-27 22:50:00', 0, 0),
(3, 0, 1, 3, 'Visite amicale', 1, 0, '0000-00-00 00:00:00', NULL, 0, 0),
(4, 0, 1, 3, 'Visite amicale', 1, 0, '0000-00-00 00:00:00', NULL, 0, 0),
(5, 0, 1, 3, 'Visite amicale', 1, 0, '0000-00-00 00:00:00', NULL, 0, 0),
(6, 0, 1, 3, 'Visite amicale', 1, 0, '0000-00-00 00:00:00', NULL, 0, 0),
(7, 0, 1, 3, 'Visite amicale', 1, 0, '0000-00-00 00:00:00', NULL, 0, 0),
(8, 0, 1, 3, 'Visite amicale', 1, 0, '0000-00-00 00:00:00', NULL, 0, 0),
(9, 0, 1, 3, 'Visite amicale', 1, 0, '0000-00-00 00:00:00', NULL, 0, 0),
(10, 0, 1, 3, 'Visite amicale', 1, 0, '0000-00-00 00:00:00', NULL, 0, 0),
(11, 1, 2, 3, 'Visite amicaale', 1, 2, '2019-07-29 10:03:55', '2019-11-08 10:06:00', 0, 0),
(12, 1, 2, 3, 'Visite amicaale', 1, 0, '2019-07-29 10:04:11', NULL, 0, 0);

-- --------------------------------------------------------

--
-- Structure de la table `role`
--

CREATE TABLE `role` (
  `idrole` bigint(20) NOT NULL,
  `libellerole` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `role`
--

INSERT INTO `role` (`idrole`, `libellerole`) VALUES
(1, 'admin'),
(2, 'Moderateur'),
(3, 'partenaire');

-- --------------------------------------------------------

--
-- Structure de la table `temoignage`
--

CREATE TABLE `temoignage` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `prenom` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
  `profession` varchar(50) NOT NULL,
  `description` text NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Contenu de la table `temoignage`
--

INSERT INTO `temoignage` (`id`, `nom`, `prenom`, `age`, `profession`, `description`) VALUES
(1, 'Znoun', 'Forkich', 18, 'Etude', 'jai vu hzjssjsdnbskdkzjbdkbejdebdjebdjejdjebdbedbedb\r\nejbkjedjebdjbejdejdjnednend'),
(3, 'kd', 'dd', 35, 'dnsks', 'dddd');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `iduser` bigint(20) NOT NULL,
  `nomuser` varchar(255) NOT NULL,
  `prenomuser` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `createdat` datetime NOT NULL,
  `fkidrole` bigint(20) NOT NULL,
  `isdeleted` tinyint(1) NOT NULL DEFAULT '0',
  `emailuser` varchar(100) NOT NULL,
  `contactuser` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contenu de la table `user`
--

INSERT INTO `user` (`iduser`, `nomuser`, `prenomuser`, `username`, `password`, `createdat`, `fkidrole`, `isdeleted`, `emailuser`, `contactuser`) VALUES
(1, 'Edgar', 'Ayena', 'ed', '$2y$10$H40H0lg/ehzF5wY6K9Ek0.MAA4fwbu3ZV4px48HIDTCCp4PyaG2/W', '2019-06-26 00:00:00', 1, 0, '', ''),
(2, 'Papa', 'Moussa', 'ed1', '$2y$10$H40H0lg/ehzF5wY6K9Ek0.MAA4fwbu3ZV4px48HIDTCCp4PyaG2/W', '0000-00-00 00:00:00', 3, 0, '', ''),
(3, 'Yao', 'herve', 'ed2', '$2y$10$H40H0lg/ehzF5wY6K9Ek0.MAA4fwbu3ZV4px48HIDTCCp4PyaG2/W', '0000-00-00 00:00:00', 2, 0, 'tisloy@gmail.com', ''),
(4, 'Charbel', 'Am', 'ed3', '$2y$10$H40H0lg/ehzF5wY6K9Ek0.MAA4fwbu3ZV4px48HIDTCCp4PyaG2/W', '0000-00-00 00:00:00', 2, 0, '', ''),
(5, 'Fiacre', 'ANaTo', 'ed4', '$2y$10$H40H0lg/ehzF5wY6K9Ek0.MAA4fwbu3ZV4px48HIDTCCp4PyaG2/W', '0000-00-00 00:00:00', 2, 0, '', ''),
(16, 'montana', 'toni', 'toni', '$2y$10$jgb1WWSy1PAm7hiU.7MQB.QVM1QyfoLL5jSJ6kteZHKLnCphovOHa', '2019-11-12 11:21:47', 1, 0, 'lo.ki', '456'),
(17, 'GABA', 'kenneth', 'kenny', '$2y$10$p8T5uKJydqnrrujkT8zOqOttSQ9W2v6Belznqi/6oIUrIv4Ap0HNy', '2019-11-12 11:33:41', 2, 0, 'kenn@mail.com', '67341587'),
(18, 'morice', 'comlan', 'coco', '$2y$10$AaZf094iLJ6B79Oto643FOFFoYFptsw7/SfJkzg2iCSEzDMGFwhuG', '2019-11-19 14:44:10', 3, 0, 'okolm@lo.com', '458798');

--
-- Index pour les tables exportées
--

--
-- Index pour la table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`idclient`);

--
-- Index pour la table `clientdossier`
--
ALTER TABLE `clientdossier`
  ADD PRIMARY KEY (`idclientdossier`);

--
-- Index pour la table `complains`
--
ALTER TABLE `complains`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`),
  ADD KEY `complain_type_id` (`complain_type_id`);

--
-- Index pour la table `complain_meta`
--
ALTER TABLE `complain_meta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `complain_id` (`complain_id`);

--
-- Index pour la table `complain_types`
--
ALTER TABLE `complain_types`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `complain_updates`
--
ALTER TABLE `complain_updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `complain_id` (`complain_id`),
  ADD KEY `moderator_id` (`moderator_id`);

--
-- Index pour la table `complain_uploads`
--
ALTER TABLE `complain_uploads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `complain_id` (`complain_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `complain_violences`
--
ALTER TABLE `complain_violences`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `denonce`
--
ALTER TABLE `denonce`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `dossier`
--
ALTER TABLE `dossier`
  ADD PRIMARY KEY (`iddossier`);

--
-- Index pour la table `gestionacceuil`
--
ALTER TABLE `gestionacceuil`
  ADD PRIMARY KEY (`idgestionaccueil`);

--
-- Index pour la table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `note`
--
ALTER TABLE `note`
  ADD PRIMARY KEY (`idnote`);

--
-- Index pour la table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `plainte`
--
ALTER TABLE `plainte`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `rdvfutur`
--
ALTER TABLE `rdvfutur`
  ADD PRIMARY KEY (`idrdvfutur`);

--
-- Index pour la table `rdvimmediat`
--
ALTER TABLE `rdvimmediat`
  ADD PRIMARY KEY (`idrdvimmediat`);

--
-- Index pour la table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`idrole`);

--
-- Index pour la table `temoignage`
--
ALTER TABLE `temoignage`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`iduser`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `client`
--
ALTER TABLE `client`
  MODIFY `idclient` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT pour la table `clientdossier`
--
ALTER TABLE `clientdossier`
  MODIFY `idclientdossier` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `complains`
--
ALTER TABLE `complains`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `complain_meta`
--
ALTER TABLE `complain_meta`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `complain_types`
--
ALTER TABLE `complain_types`
  MODIFY `id` tinyint(4) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `complain_updates`
--
ALTER TABLE `complain_updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `complain_uploads`
--
ALTER TABLE `complain_uploads`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `complain_violences`
--
ALTER TABLE `complain_violences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `denonce`
--
ALTER TABLE `denonce`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `dossier`
--
ALTER TABLE `dossier`
  MODIFY `iddossier` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT pour la table `gestionacceuil`
--
ALTER TABLE `gestionacceuil`
  MODIFY `idgestionaccueil` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `note`
--
ALTER TABLE `note`
  MODIFY `idnote` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT pour la table `plainte`
--
ALTER TABLE `plainte`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT pour la table `rdvfutur`
--
ALTER TABLE `rdvfutur`
  MODIFY `idrdvfutur` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `rdvimmediat`
--
ALTER TABLE `rdvimmediat`
  MODIFY `idrdvimmediat` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT pour la table `role`
--
ALTER TABLE `role`
  MODIFY `idrole` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `temoignage`
--
ALTER TABLE `temoignage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `iduser` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;
--
-- Contraintes pour les tables exportées
--

--
-- Contraintes pour la table `complains`
--
ALTER TABLE `complains`
  ADD CONSTRAINT `complains_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `complains_ibfk_2` FOREIGN KEY (`complain_type_id`) REFERENCES `complain_types` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `complain_meta`
--
ALTER TABLE `complain_meta`
  ADD CONSTRAINT `complain_meta_ibfk_1` FOREIGN KEY (`complain_id`) REFERENCES `complains` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `complain_updates`
--
ALTER TABLE `complain_updates`
  ADD CONSTRAINT `complain_updates_ibfk_1` FOREIGN KEY (`complain_id`) REFERENCES `complains` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `complain_updates_ibfk_2` FOREIGN KEY (`moderator_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `complain_uploads`
--
ALTER TABLE `complain_uploads`
  ADD CONSTRAINT `complain_uploads_ibfk_1` FOREIGN KEY (`complain_id`) REFERENCES `complains` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `complain_uploads_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
