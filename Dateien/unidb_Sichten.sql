USE unidb;
START TRANSACTION;
SET time_zone = "+00:00";

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `User_Pfade`  AS SELECT `User_Path`.`Path` AS `Path`, `User`.`UserName` AS `UserName` FROM (`User` join `User_Path`) WHERE `User`.`User_ID` = `User_Path`.`User_ID` AND `User_Path`.`User_ID` = (select `User`.`User_ID` AS `User_ID` from `User` where `User`.`UserName` = left(user(),locate('@',user()) - 1)) ;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `User_Baum`  AS SELECT `Baum`.`Path` AS `Path`, `Baum`.`Baum_ID` AS `Baum_ID`, `Baum`.`Eltern_ID` AS `Eltern_ID`, `Baum`.`owner` AS `owner`, `Baum`.`Bezeichnung` AS `Bezeichnung`, `Baum`.`Vorlage` AS `Vorlage`, `Baum`.`Inhalt` AS `Inhalt` FROM (`Baum` join `User_Pfade`) WHERE `User_Pfade`.`Path` = left(`Baum`.`Path`,octet_length(`User_Pfade`.`Path`)) ;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `User_ID`  AS SELECT `User`.`User_ID` AS `User_ID` FROM `User` WHERE `User`.`UserName` = left(user(),locate('@',user()) - 1) ;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `Vorl_Eigenschaften`  AS SELECT `Elementvorlagen`.`DE` AS `Vorlage`, `Vorlageneigenschaften`.`Attributtyp` AS `Attributtyp`, `Vorlageneigenschaften`.`Auswahl` AS `Auswahl`, `Vorlageneigenschaften`.`Tab` AS `Tab`, `Vorlageneigenschaften`.`orig_Name` AS `orig_Name`, `Vorlageneigenschaften`.`DE` AS `DE`, `Vorlageneigenschaften`.`EN` AS `EN`, `Vorlageneigenschaften`.`NL` AS `NL`, `Vorlageneigenschaften`.`FR` AS `FR`, `Vorlageneigenschaften`.`Hinweis_DE` AS `Hinweis_DE`, `Vorlageneigenschaften`.`Hinweis_EN` AS `Hinweis_EN`, `Vorlageneigenschaften`.`Hinweis_NL` AS `Hinweis_NL`, `Vorlageneigenschaften`.`Hinweis_FR` AS `Hinweis_FR`, `Vorlageneigenschaften`.`Eigenschaft` AS `Eigenschaft`, `Vorlageneigenschaften`.`Standardwert` AS `Standardwert`, `Vorlageneigenschaften`.`Darstellung_Dialog` AS `Darstellung_Dialog`, `Elementvorlagen`.`Elementvorlage_ID` AS `Elementvorlage_ID`, `Vorlageneigenschaften`.`Vorlageneigenschaften_ID` AS `Vorlageneigenschaften_ID`, `Vorlageneigenschaften`.`Reihenfolge` AS `Reihenfolge` FROM ((`Verb_Vorlagen_Eigenschaften` join `Elementvorlagen`) join `Vorlageneigenschaften`) WHERE `Verb_Vorlagen_Eigenschaften`.`Elementvorlage_ID` = `Elementvorlagen`.`Elementvorlage_ID` AND `Vorlageneigenschaften`.`Vorlageneigenschaften_ID` = `Verb_Vorlagen_Eigenschaften`.`Vorlageneigenschaften_ID` ;
COMMIT;
