USE DH;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `akt` (
  `id` int(11) NOT NULL,
  `Timestamp` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Point_ID` int(11) NOT NULL DEFAULT 0,
  `Value` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Einstellungen` (
  `Einstellung_ID` int(11) NOT NULL,
  `Parameter` varchar(255) NOT NULL,
  `Wert` varchar(255) DEFAULT NULL,
  `Zusatz` varchar(255) DEFAULT NULL,
  `Eltern_ID` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `Einstellungen` (`Einstellung_ID`, `Parameter`, `Wert`, `Zusatz`, `Eltern_ID`) VALUES
(132, 'Kollektiv', '', '', 0),
(162, 'Serverkomponenten', '', '', 0),
(163, 'außer Betrieb', '', '', 0),
(170, 'temp Verzeichnis', '/opt/DH/tmp', 'Das Verzeichnis, in  dem die Dateien abgelegt, oder gesucht werden.', 162),
(198, 'Zwangsintervall', '600', 'Zeit nach der ein Wert auf jeden Fall an den Server geschickt wird, auch wenn er sich nicht geändert hat.', 0),
(205, 'localhost', '', '', 132),
(206, 'Database', 'DH', '', 205),
(207, 'IP', 'localhost', '', 205),
(208, 'Password', 'unidb', '', 205),
(209, 'User', 'root', '', 205),
(218, 'Schnittstellen', '', '', 0),
(668, 'comp', NULL, '', 218),
(669, 'Script', 'DH_comp.py', 'localhost', 668),
(670, 'Intervall', '60', 'comp', 668),
(671, 'von_Point_ID', '1', '', 668),
(672, 'bis_Point_ID', '1000000', '', 668),
(673, 'watchdog', NULL, '', 218),
(674, 'Script', 'DH_watchdog.py', 'localhost', 673),
(675, 'Intervall', '10', 'watchdog', 673),
(676, 'calc', NULL, '', 218),
(677, 'Script', 'DH_calc.py', 'localhost', 676),
(678, 'Intervall', '60', 'calc', 676),
(679, 'sysdata', NULL, '', 218),
(680, 'Script', 'DH_sysdata.py', 'localhost', 679),
(681, 'Intervall', '300', 'sysdata', 679),
(682, 'Wetter', NULL, '', 218),
(683, 'Script', 'DH_Wetter.py', 'localhost', 682),
(684, 'Intervall', '60', 'Wetter', 682),
(685, 'batchfl', NULL, '', 218),
(686, 'Script', 'DH_batchfl.py', 'localhost', 685),
(687, 'Intervall', '60', 'batchfl', 685),
(688, 'Pfad', '/opt/DH/batchfl', 'Ordner in dem die Dateien liegen.', 685),
(689, 'Schnittstellenrechner', '', '', 0);

CREATE TABLE `Geraete` (
  `Geraete_ID` int(11) NOT NULL,
  `Nummer` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  `Benutzername` varchar(100) NOT NULL,
  `Passwort` varchar(100) NOT NULL,
  `lokale_IP` varchar(15) NOT NULL,
  `Bezeichnung` varchar(255) DEFAULT NULL,
  `Bemerkung` varchar(1024) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Geraete_Points` (
  `Geraete_Points_ID` int(11) NOT NULL,
  `Geraete_ID` int(11) NOT NULL,
  `Point_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `gesperrt` (
  `gesperrt_id` int(11) NOT NULL,
  `Dokument` varchar(255) NOT NULL,
  `Zeitpunkt` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=MEMORY DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Log` (
  `Timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `Source` varchar(20) DEFAULT NULL,
  `Text` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Meldungen` (
  `Meldungen_ID` int(11) NOT NULL,
  `Timestamp` datetime DEFAULT current_timestamp(),
  `Schnittstelle` varchar(100) DEFAULT NULL,
  `Meldung` varchar(100) DEFAULT NULL,
  `Rechner` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Multistates` (
  `Multistate_ID` int(11) NOT NULL,
  `Gruppe` varchar(30) NOT NULL,
  `User_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Multistates_Detail` (
  `Multistate_detail_ID` int(11) NOT NULL,
  `Multistate_ID` int(11) NOT NULL,
  `Operant` varchar(3) DEFAULT NULL,
  `Wert` double DEFAULT NULL,
  `Bild` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Points` (
  `Path` varchar(970) NOT NULL,
  `Pointname` varchar(30) NOT NULL,
  `Point_ID` int(11) NOT NULL,
  `Description` varchar(128) NOT NULL DEFAULT '',
  `EUDESC` varchar(10) DEFAULT NULL,
  `scan` int(11) NOT NULL DEFAULT 0 COMMENT 'o = nicht scannen und 1 = Tag wird gescannt',
  `Interface` varchar(50) DEFAULT NULL,
  `archive` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0 = keine Archivierung, 1 = Archivierung',
  `step` int(11) NOT NULL DEFAULT 0 COMMENT '0 = aus und 1 = ein',
  `compression` double DEFAULT NULL,
  `minarch` int(11) NOT NULL DEFAULT 3600,
  `Info` text DEFAULT NULL,
  `Property_1` int(11) DEFAULT NULL,
  `Property_2` varchar(1024) DEFAULT NULL,
  `Property_3` varchar(1024) DEFAULT NULL,
  `Property_4` varchar(1024) DEFAULT NULL,
  `Property_5` varchar(1024) DEFAULT NULL,
  `Point_Type` varchar(6) NOT NULL DEFAULT 'double',
  `Dezimalstellen` int(11) NOT NULL DEFAULT 0,
  `Scale_min` double DEFAULT NULL,
  `Scale_max` double DEFAULT NULL,
  `Intervall` int(11) DEFAULT NULL COMMENT 'Berechnungsintervall in Sekunden, wird nur für die Calc Engine benötigt.',
  `Mittelwerte` tinyint(4) NOT NULL,
  `Changedate` timestamp NOT NULL DEFAULT current_timestamp(),
  `first_value` timestamp NULL DEFAULT current_timestamp(),
  `Point_owner` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Puffer` (
  `Puffer_ID` int(11) NOT NULL,
  `Timestamp` datetime NOT NULL DEFAULT current_timestamp(),
  `Server` varchar(255) NOT NULL,
  `SQL_Text` varchar(2048) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `Tagtable` (
  `Tag_ID` int(11) NOT NULL,
  `Point_ID` int(11) NOT NULL,
  `Tagname` varchar(50) NOT NULL,
  `Path` varchar(1024) NOT NULL,
  `Tag_owner` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `User_Skalen` (
  `User_Skala_ID` int(11) NOT NULL,
  `User_ID` int(11) NOT NULL,
  `Point_ID` int(11) NOT NULL,
  `min` float DEFAULT NULL,
  `max` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `akt` ADD PRIMARY KEY (`id`), ADD KEY `Zeit-Tag` (`Timestamp`,`Point_ID`);

ALTER TABLE `Einstellungen` ADD PRIMARY KEY (`Einstellung_ID`);

ALTER TABLE `Geraete` ADD PRIMARY KEY (`Geraete_ID`), ADD UNIQUE KEY `User_ID` (`User_ID`);

ALTER TABLE `Geraete_Points` ADD PRIMARY KEY (`Geraete_Points_ID`), ADD UNIQUE KEY `Geraete_ID` (`Geraete_ID`);

ALTER TABLE `gesperrt` ADD PRIMARY KEY (`gesperrt_id`);

ALTER TABLE `Log` ADD KEY `Timestamp` (`Timestamp`);

ALTER TABLE `Meldungen` ADD PRIMARY KEY (`Meldungen_ID`), ADD UNIQUE KEY `Index` (`Timestamp`,`Schnittstelle`);

ALTER TABLE `Multistates` ADD PRIMARY KEY (`Multistate_ID`);

ALTER TABLE `Multistates_Detail` ADD PRIMARY KEY (`Multistate_detail_ID`);

ALTER TABLE `Points` ADD PRIMARY KEY (`Point_ID`), ADD UNIQUE KEY `Pointname` (`Point_ID`,`Pointname`);

ALTER TABLE `Puffer` ADD PRIMARY KEY (`Puffer_ID`), ADD KEY `Server_Zeit` (`Server`,`Timestamp`);

ALTER TABLE `Tagtable` ADD PRIMARY KEY (`Tag_ID`), ADD KEY `Point_ID` (`Point_ID`);

ALTER TABLE `User_Skalen` ADD PRIMARY KEY (`User_Skala_ID`), ADD UNIQUE KEY `User_Point_unique` (`User_ID`,`Point_ID`);

ALTER TABLE `akt` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Einstellungen` MODIFY `Einstellung_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=711;

ALTER TABLE `Geraete` MODIFY `Geraete_ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Geraete_Points` MODIFY `Geraete_Points_ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `gesperrt` MODIFY `gesperrt_id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Meldungen` MODIFY `Meldungen_ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Multistates` MODIFY `Multistate_ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Multistates_Detail` MODIFY `Multistate_detail_ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Points` MODIFY `Point_ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Puffer` MODIFY `Puffer_ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `Tagtable` MODIFY `Tag_ID` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `User_Skalen` MODIFY `User_Skala_ID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;
