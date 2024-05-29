# ************************************************************
# Sequel Ace SQL dump
# Version 20064
#
# https://sequel-ace.com/
# https://github.com/Sequel-Ace/Sequel-Ace
#
# Host: v.je (MySQL 11.3.2-MariaDB-1:11.3.2+maria~ubu2204)
# Database: football
# Generation Time: 2024-05-29 08:05:12 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE='NO_AUTO_VALUE_ON_ZERO', SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table games
# ------------------------------------------------------------

DROP TABLE IF EXISTS `games`;

CREATE TABLE `games` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `round` int(11) unsigned DEFAULT NULL,
  `tournament_id` int(11) unsigned DEFAULT NULL,
  `team1` int(11) unsigned DEFAULT NULL,
  `points_team1` int(11) unsigned DEFAULT NULL,
  `points_team2` int(11) unsigned DEFAULT NULL,
  `team2` int(11) unsigned DEFAULT NULL,
  `stage` varchar(11) DEFAULT 'waiting',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `games` WRITE;
/*!40000 ALTER TABLE `games` DISABLE KEYS */;

INSERT INTO `games` (`id`, `round`, `tournament_id`, `team1`, `points_team1`, `points_team2`, `team2`, `stage`)
VALUES
	(1,1,1,21,10,0,24,'ended'),
	(2,2,1,24,1,10,21,'ended'),
	(135,1,29,18,10,2,29,'ended'),
	(136,1,29,29,1,10,18,'ended'),
	(675,1,132,53,10,4,2,'ended'),
	(676,1,132,2,10,12,53,'ended'),
	(709,1,148,35,10,0,29,'ended'),
	(710,1,148,29,1,10,35,'ended'),
	(713,1,150,4,4,10,35,'ended'),
	(714,1,150,35,10,5,4,'ended'),
	(719,1,153,16,2,10,53,'ended'),
	(720,1,153,53,11,9,16,'ended');

/*!40000 ALTER TABLE `games` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table players
# ------------------------------------------------------------

DROP TABLE IF EXISTS `players`;

CREATE TABLE `players` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `players` WRITE;
/*!40000 ALTER TABLE `players` DISABLE KEYS */;

INSERT INTO `players` (`id`, `name`)
VALUES
	(1,'Karol Tomaszewski'),
	(2,'Paweł Szumiak'),
	(3,'Michał Łukaszczyk'),
	(4,'Paweł Jureczko'),
	(6,'Wojciech Mzyk'),
	(8,'Karolina Wanot'),
	(9,'Łukasz Turowski'),
	(11,'Krzysztof Kot'),
	(12,'Nina Olczyk'),
	(13,'Paweł Sikorski'),
	(14,'Damian Szydłowski');

/*!40000 ALTER TABLE `players` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table team_stats
# ------------------------------------------------------------

DROP TABLE IF EXISTS `team_stats`;

CREATE TABLE `team_stats` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tournament_id` int(11) DEFAULT NULL,
  `team_id` int(11) DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  `win` int(11) DEFAULT NULL,
  `lose` int(11) DEFAULT NULL,
  `draw` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



# Dump of table teams
# ------------------------------------------------------------

DROP TABLE IF EXISTS `teams`;

CREATE TABLE `teams` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `player1_id` int(11) NOT NULL,
  `player2_id` int(11) NOT NULL,
  `elo` double(7,2) DEFAULT 1000.00,
  `win` int(11) DEFAULT 0,
  `lose` int(11) DEFAULT 0,
  `points` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `teams` WRITE;
/*!40000 ALTER TABLE `teams` DISABLE KEYS */;

INSERT INTO `teams` (`id`, `player1_id`, `player2_id`, `elo`, `win`, `lose`, `points`)
VALUES
	(1,8,6,1000.00,0,0,0),
	(2,2,1,969.47,0,2,-8),
	(3,8,1,1000.00,0,0,0),
	(4,2,6,969.47,0,2,-11),
	(5,2,8,1000.00,0,0,0),
	(6,6,1,1000.00,0,0,0),
	(7,9,12,1000.00,0,0,0),
	(8,3,6,1000.00,0,0,0),
	(9,4,11,1000.00,0,0,0),
	(10,8,11,1000.00,0,0,0),
	(11,9,2,1000.00,0,0,0),
	(12,1,4,1000.00,0,0,0),
	(13,12,3,1000.00,0,0,0),
	(14,11,12,1000.00,0,0,0),
	(15,9,6,1000.00,0,0,0),
	(16,3,2,972.12,0,2,-10),
	(17,9,4,1000.00,0,0,0),
	(18,1,11,1030.50,2,0,17),
	(19,3,8,1000.00,0,0,0),
	(20,2,4,1000.00,0,0,0),
	(21,3,1,1030.50,2,0,19),
	(22,6,4,1000.00,0,0,0),
	(23,3,4,1000.00,0,0,0),
	(24,2,11,969.47,0,2,-19),
	(25,3,11,1000.00,0,0,0),
	(26,4,8,1000.00,0,0,0),
	(27,1,9,1000.00,0,0,0),
	(28,11,9,1000.00,0,0,0),
	(29,2,12,944.20,0,4,-36),
	(30,6,11,1000.00,0,0,0),
	(31,9,3,1000.00,0,0,0),
	(32,9,8,1000.00,0,0,0),
	(33,12,6,1000.00,0,0,0),
	(34,2,14,1000.00,0,0,0),
	(35,13,1,1070.52,4,0,30),
	(36,2,13,1000.00,0,0,0),
	(37,1,14,1000.00,0,0,0),
	(38,14,13,1000.00,0,0,0),
	(53,13,4,1058.38,4,0,18),
	(54,14,9,1000.00,0,0,0),
	(55,14,6,1000.00,0,0,0),
	(56,14,8,1000.00,0,0,0),
	(57,11,14,1000.00,0,0,0);

/*!40000 ALTER TABLE `teams` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tournament_teams
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tournament_teams`;

CREATE TABLE `tournament_teams` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `team_id` int(11) unsigned NOT NULL,
  `tournament_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `tournament_teams` WRITE;
/*!40000 ALTER TABLE `tournament_teams` DISABLE KEYS */;

INSERT INTO `tournament_teams` (`id`, `team_id`, `tournament_id`)
VALUES
	(1,21,1),
	(2,24,1),
	(80,18,29),
	(81,29,29),
	(344,2,132),
	(345,53,132),
	(376,29,148),
	(377,35,148),
	(378,21,149),
	(379,24,149),
	(380,35,150),
	(381,4,150),
	(386,53,153),
	(387,16,153);

/*!40000 ALTER TABLE `tournament_teams` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tournaments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tournaments`;

CREATE TABLE `tournaments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

LOCK TABLES `tournaments` WRITE;
/*!40000 ALTER TABLE `tournaments` DISABLE KEYS */;

INSERT INTO `tournaments` (`id`, `name`)
VALUES
	(1,'Pierwszy turniej'),
	(29,'21.05'),
	(132,'23.05'),
	(148,'24.05'),
	(150,'24.05'),
	(153,'27.05');

/*!40000 ALTER TABLE `tournaments` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
