CREATE ROLE 'Benutzer@%' WITH ADMIN 'root'@'%';
GRANT USAGE ON `DH`.* TO 'Benutzer@%';
GRANT USAGE ON `unidb`.* TO 'Benutzer@%';
GRANT SELECT ON `DH`.`User_Archiv` TO 'Benutzer@%';
GRANT SELECT ON `DH`.`User_Tags` TO 'Benutzer@%';
GRANT SELECT ON `DH`.`User_akt` TO 'Benutzer@%';
GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, REFERENCES ON `unidb`.`User_Baum` TO 'Benutzer@%';
GRANT SELECT ON `unidb`.`Hilfe` TO 'Benutzer@%';
GRANT SELECT ON `unidb`.`Vorlagen` TO 'Benutzer@%';
