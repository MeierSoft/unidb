#!/usr/bin/env python3
# -*- coding: ISO-8859-1 -*-

import subprocess, sys, os
from pathlib import Path

Version = "1"
Versionsdatum = "2024-01-30"

def Ordner_anlegen(Ordner):
    Zielordner = Ordner.split("/")
    Pfad = ""
    for Ordner in Zielordner:
        if Pfad == "/":
            Pfad = ""
        Pfad = Pfad + "/" + Ordner
        if os.path.exists(Pfad) == False:
            kop = subprocess.Popen('mkdir ' + Pfad, shell=True)
            subprocess.Popen.wait(kop)
            
Quelle = sys.argv[0]
while Quelle[len(Quelle) - 1:] != "/":
    Quelle = Quelle[0:len(Quelle) - 1]

#Variablen aus config.txt einlesen
Einst = {}
Datei = open(Quelle + "config.txt", "r")
Inhalt = Datei.read()
Datei.close()
Zeilen = Inhalt.split("\n")
for Zeile in Zeilen:
    if len(Zeile) > 0:
        if Zeile[0:1] != "#":
            Variable = Zeile.split(":")
            Einst [Variable[0]] = Variable[1]

# Dateien kopieren
while Einst["Folder_Python_Scripts"][len(Einst["Folder_Python_Scripts"]) - 1:] == "/":
    Einst["Folder_Python_Scripts"] = Einst["Folder_Python_Scripts"][0:len(Einst["Folder_Python_Scripts"]) - 1]
while Einst["Folder_Archives"][len(Einst["Folder_Archives"]) - 1:] == "/":
    Einst["Folder_Archives"] = Einst["Folder_Archives"][0:len(Einst["Folder_Archives"]) - 1]
while Einst["Folder_Webserver_Dokument_Root"][len(Einst["Folder_Webserver_Dokument_Root"]) - 1:] == "/":
    Einst["Folder_Webserver_Dokument_Root"] = Einst["Folder_Webserver_Dokument_Root"][0:len(Einst["Folder_Webserver_Dokument_Root"]) - 1]
try:
    print("copy Python scripts ...")
    Ordner_anlegen(Einst["Folder_Python_Scripts"])
    kop = subprocess.Popen('cp -r ' +  Quelle + 'Dateien/opt/DH ' + Einst["Folder_Python_Scripts"], shell=True)
    subprocess.Popen.wait(kop)
    kop = subprocess.Popen('cp -r ' +  Quelle + 'Dateien/opt/unidb ' + Einst["Folder_Python_Scripts"], shell=True)
    subprocess.Popen.wait(kop)
    kop = subprocess.Popen('chown -R root:root ' + Einst["Folder_Python_Scripts"], shell=True)
    subprocess.Popen.wait(kop)
    kop = subprocess.Popen('chmod -R 775 ' + Einst["Folder_Python_Scripts"], shell=True)
    subprocess.Popen.wait(kop)
    kop = subprocess.Popen('chmod 660 ' + Einst["Folder_Python_Scripts"] + '/DH/DH.ini', shell=True)
    subprocess.Popen.wait(kop)
    kop = subprocess.Popen('chmod 660 ' + Einst["Folder_Python_Scripts"] + '/unidb/unidb.ini', shell=True)
    subprocess.Popen.wait(kop)
    print("... done")
except:
    print("... Copy Python scripts failed.")
try:
    Ordner_anlegen(Einst["Folder_Archives"])
    if Einst["Install_Example_Data"] == "1":
        print("copy archive files ...")
        kop = subprocess.Popen('cp -r ' +  Quelle + 'Dateien/var/lib/DH/2023 ' + Einst["Folder_Archives"], shell=True)
        subprocess.Popen.wait(kop)
        kop = subprocess.Popen('cp -r ' +  Quelle + 'Dateien/var/lib/DH/2024 ' + Einst["Folder_Archives"], shell=True)
        subprocess.Popen.wait(kop)
        kop = subprocess.Popen('chown -R www-data:www-data ' + Einst["Folder_Archives"], shell=True)
        subprocess.Popen.wait(kop)
        kop = subprocess.Popen('chmod -R 660 ' + Einst["Folder_Archives"], shell=True)
        subprocess.Popen.wait(kop)
        kop = subprocess.Popen('find ' + Einst["Folder_Archives"] + ' -type d -exec chmod 775 {} ' + chr(92) + ';', shell=True)
        subprocess.Popen.wait(kop)
        print("... done")
except:
    print("... Copy archive files failed.")
try:
    print("copy unidb files ...")
    Ordner_anlegen(Einst["Folder_Webserver_Dokument_Root"])
    kop = subprocess.Popen('cp -r ' +  Quelle + 'Dateien/var/www/Fenster ' + Einst["Folder_Webserver_Dokument_Root"], shell=True)
    subprocess.Popen.wait(kop)
    kop = subprocess.Popen('cp -r ' +  Quelle + 'Dateien/var/www/jpgraph ' + Einst["Folder_Webserver_Dokument_Root"], shell=True)
    subprocess.Popen.wait(kop)
    kop = subprocess.Popen('cp -r ' +  Quelle + 'Dateien/var/www/unidb ' + Einst["Folder_Webserver_Dokument_Root"], shell=True)
    subprocess.Popen.wait(kop)
    path = Path(Einst["Folder_Webserver_Dokument_Root"])
    kop = subprocess.Popen('chown -R ' + path.owner() + ':' + path.group() + ' ' + Einst["Folder_Webserver_Dokument_Root"], shell=True)
    subprocess.Popen.wait(kop)
    kop = subprocess.Popen('chmod -R 660 ' + Einst["Folder_Webserver_Dokument_Root"], shell=True)
    subprocess.Popen.wait(kop)
    kop = subprocess.Popen('find ' + Einst["Folder_Webserver_Dokument_Root"] + ' -type d -exec chmod 770 {} ' + chr(92) + ';', shell=True)
    subprocess.Popen.wait(kop)
    print("... done")
except:
    print("... Copy unidb files failed.")

if Einst["Automatic_Start_Interfaces"] == "1" :
    try:
        print("create service dh ...")
        kop = subprocess.Popen('cp -r ' +  Quelle + 'Dateien/etc/init.d/dh /etc/init.d/', shell=True)
        subprocess.Popen.wait(kop)
        Datei = open(Quelle + "Dateien/etc/init.d/dh", "r")
        Textinhalt = Datei.read()
        Datei.close()
        Textinhalt = Textinhalt.replace("/opt", Einst["Folder_Python_Scripts"])
        Datei = open("/etc/init.d/dh", "w")
        Datei.write(Textinhalt)
        Datei.close()
        kop = subprocess.Popen('update-rc.d dh defaults', shell=True)
        subprocess.Popen.wait(kop)
        print("... done")
    except:
        print("... create service dh failed.")

#Datenbanken anlegen
print("create databases DH and unidb ...")
Datei = open(Quelle + "Dateien/Mariadb_vorbereiten.sql", "w")
Datei.write("CREATE DATABASE unidb;\n")
Datei.write("FLUSH PRIVILEGES;\n")
Datei.write("CREATE DATABASE DH;\n")
Datei.write("FLUSH PRIVILEGES;\n")
Passw = Einst["Password_Root_Rights"]
if len(Passw) == 0:
    Datei.write("CREATE USER '" + Einst["User_Root_Rights"] + "'@'localhost';\n")
else:
    Datei.write("CREATE USER '" + Einst["User_Root_Rights"] + "'@'localhost' identified by '" + Passw + "';\n")
Datei.write("GRANT ALL privileges on *.* to '" + Einst["User_Root_Rights"] + "'@'localhost' with grant option;\n")
Passw = Einst["Password_Read_DH"]
if len(Passw) == 0:
    Datei.write("CREATE USER '" + Einst["User_Read_DH"] + "'@'%';\n")
else:
    Datei.write("CREATE USER '" + Einst["User_Read_DH"]  + "'@'%' identified by '" + Passw + "';\n")
Datei.write("GRANT SELECT ON `DH%`.* TO '" + Einst["User_Read_DH"]  + "'@'%';\n")
Passw = Einst["Password_Write_unidb"]
if len(Passw) == 0:
    Datei.write("CREATE USER '" + Einst["User_Write_unidb"] + "'@'%';\n")
else:
    Datei.write("CREATE USER '" + Einst["User_Write_unidb"] + "'@'%' identified by '" + Passw + "';\n")
Datei.write("GRANT SELECT, INSERT, UPDATE, DELETE ON `unidb`.* TO '" + Einst["User_Write_unidb"] + "'@'%';\n")
Passw = Einst["Password_Write_DH"]
if len(Passw) == 0:
    Datei.write("CREATE USER '" + Einst["User_Write_DH"] + "'@'%';\n")
else:
    Datei.write("CREATE USER '" + Einst["User_Write_DH"] + "'@'%' identified by '" + Passw + "';\n")        
Datei.write("GRANT SELECT, INSERT, UPDATE, DELETE ON `DH`.* TO '" + Einst["User_Write_DH"] + "'@'%';\n")
Passw = Einst["Password_useradmin"]
if len(Passw) == 0:
    Datei.write("CREATE USER '" + Einst["User_useradmin"] + "'localhost'%';\n")
    Datei.write("GRANT SELECT, RELOAD, LOCK TABLES, CREATE USER ON *.* TO `" + Einst["User_useradmin"] + "`@`localhost` WITH GRANT OPTION;\n")
else:
    Datei.write("CREATE USER '" + Einst["User_useradmin"]  + "'@'localhost' identified by '" + Passw + "';\n")
    Datei.write("GRANT SELECT, RELOAD, LOCK TABLES, CREATE USER ON *.* TO `" + Einst["User_useradmin"] + "`@`localhost` IDENTIFIED BY '" + Passw+ "' WITH GRANT OPTION;\n")
Datei.write("CREATE USER 'admin'@'localhost' identified by 'b511e6d04233d58608dd559b2ff8f21d';\n")
Datei.write("GRANT SELECT, INSERT, UPDATE, DELETE ON `DH`.* TO 'admin'@'localhost';\n")
Datei.write("GRANT SELECT, INSERT, UPDATE, DELETE ON `unidb`.* TO 'admin'@'localhost';\n")
Datei.write("FLUSH PRIVILEGES;\n")
Datei.close()
kop = subprocess.Popen('mysql -u root < ' + Quelle + 'Dateien/Mariadb_vorbereiten.sql', shell=True)
subprocess.Popen.wait(kop)
#Datenbanken kopieren
print (Quelle + 'Dateien/unidb_Tab.sql')
kop = subprocess.Popen('mysql -u root < ' + Quelle + 'Dateien/unidb_Tab.sql', shell=True)
subprocess.Popen.wait(kop)
print (Quelle + 'Dateien/DH_Tab.sql')
kop = subprocess.Popen('mysql -u root < ' + Quelle + 'Dateien/DH_Tab.sql', shell=True)
subprocess.Popen.wait(kop)
print (Quelle + 'Dateien/unidb_Sichten.sql')
kop = subprocess.Popen('mysql -u root < ' + Quelle + 'Dateien/unidb_Sichten.sql', shell=True)
subprocess.Popen.wait(kop)
print (Quelle + 'Dateien/DH_Sichten.sql')
kop = subprocess.Popen('mysql -u root < ' + Quelle + 'Dateien/DH_Sichten.sql', shell=True)
subprocess.Popen.wait(kop)
#Beispieldaten
if Einst["Install_Example_Data"] == "1":
    print (Quelle + 'Dateien/unidb_Beispieldaten.sql')
    kop = subprocess.Popen('mysql -u root < ' + Quelle + 'Dateien/unidb_Beispieldaten.sql', shell=True)
    subprocess.Popen.wait(kop)
    print (Quelle + 'Dateien/DH_Beispieldaten.sql')
    kop = subprocess.Popen('mysql -u root < ' + Quelle + 'Dateien/DH_Beispieldaten.sql', shell=True)
    subprocess.Popen.wait(kop)
print("... done")
#Versionsinfo schreiben und Kollektiv-Benutzer aktualisieren
print("doing some configuration ...")
try:
    import MySQLdb
    db = MySQLdb.connect("localhost", "root", "", "unidb")
except:
    import _mysql
    db = _mysql.connect("localhost", "root", "", "unidb")
db.query("SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Parameter` = 'Versionsinfo';")
M = db.store_result()
Einstellungen = M.fetch_row(maxrows=0, how=1)
Eltern_ID = str(Einstellungen[0]['Einstellung_ID'])
db.query("START TRANSACTION;")
db.query("UPDATE `Einstellungen` SET `Wert`='"+ Version + "' WHERE `Eltern_ID`= '" + Eltern_ID + "' AND `Parameter`='Version';")
db.query("UPDATE `Einstellungen` SET `Wert`='"+ Versionsdatum + "' WHERE `Eltern_ID`= '" + Eltern_ID + "' AND `Parameter`='Version Datum';")
db.query("UPDATE `Einstellungen` SET `Wert`='"+ Einst["Folder_Python_Scripts"] + "' WHERE `Eltern_ID`= '" + Eltern_ID + "' AND `Parameter`='Ordner Schnittstellen';")
db.query("UPDATE `Einstellungen` SET `Wert`='"+ Einst["Folder_Archives"]+ "' WHERE `Eltern_ID`= '" + Eltern_ID + "' AND `Parameter`='Archivordner';")
db.query("UPDATE `Einstellungen` SET `Wert`='"+ Einst["Folder_Webserver_Dokument_Root"] + "' WHERE `Eltern_ID`= '" + Eltern_ID + "' AND `Parameter`='Webroot';")
db.query("COMMIT;")

db.query("SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Parameter` = 'Kollektiv';")
M = db.store_result()
Einstellungen=M.fetch_row(maxrows=0, how=1)
Eltern_ID = str(Einstellungen[0]['Einstellung_ID'])
db.query("SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Eltern_ID` = " + Eltern_ID + ";")
M = db.store_result()
Einstellungen=M.fetch_row(maxrows=0, how=1)
Eltern_ID = str(Einstellungen[0]['Einstellung_ID'])
db.query("START TRANSACTION;")
db.query("UPDATE `Einstellungen` SET `Wert`='"+ Einst["User_Write_unidb"] + "' WHERE `Eltern_ID`= " + Eltern_ID + " AND `Parameter`='User';")
db.query("UPDATE `Einstellungen` SET `Wert`='"+ Einst["Password_Write_unidb"]  + "' WHERE `Eltern_ID`= " + Eltern_ID + " AND `Parameter`='Password';")
db.query("UPDATE `Einstellungen` SET `Wert`='"+ Einst["User_useradmin"]  + "' WHERE `Eltern_ID`= " + Eltern_ID + " AND `Parameter`='useradmin';")
db.query("UPDATE `Einstellungen` SET `Wert`='"+ Einst["Password_useradmin"]  + "' WHERE `Eltern_ID`= " + Eltern_ID + " AND `Parameter`='useradmin Passwort';")
db.query("COMMIT;")
db.close()

db = MySQLdb.connect("localhost", "root", "", "DH")
db.query("SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Parameter` = 'Kollektiv';")
M = db.store_result()
Einstellungen=M.fetch_row(maxrows=0, how=1)
Eltern_ID = str(Einstellungen[0]['Einstellung_ID'])
db.query("SELECT `Einstellung_ID` FROM `Einstellungen` WHERE `Eltern_ID` = " + Eltern_ID + ";")
M = db.store_result()
Einstellungen=M.fetch_row(maxrows=0, how=1)
Eltern_ID = str(Einstellungen[0]['Einstellung_ID'])
db.query("START TRANSACTION;")
db.query("UPDATE `Einstellungen` SET `Wert`='"+ Einst["User_Write_DH"] + "' WHERE `Eltern_ID`= " + Eltern_ID + " AND `Parameter`='User';")
db.query("UPDATE `Einstellungen` SET `Wert`='"+ Einst["Password_Write_DH"] + "' WHERE `Eltern_ID`= " + Eltern_ID + " AND `Parameter`='Password';")
db.query("COMMIT;")
db.close()

#ini Dateien anpassen
Datei = open(Einst["Folder_Python_Scripts"]  + "/DH/DH.ini", "w")
Datei.write("localhost\n")
Datei.write(Einst["User_Write_DH"]  + "\n")
Datei.write(Einst["Password_Write_DH"] + "\n")
Datei.write("DH\n")
Datei.write("localhost\n")
Datei.write(Einst["Folder_Python_Scripts"] + "/DH/tmp")
Datei.close()

Datei = open(Einst["Folder_Python_Scripts"] + "/unidb/unidb.ini", "w")
Datei.write("localhost\n")
Datei.write(Einst["User_Write_unidb"] + "\n")
Datei.write(Einst["Password_Write_unidb"] + "\n")
Datei.write("unidb\n")
Datei.write("localhost\n")
Datei.close()

Datei = open(Einst["Folder_Webserver_Dokument_Root"] + "/unidb/admin/conf_unidb.php", "w")
Inhalt = "<?php\n"
Inhalt = Inhalt + "$sqlhostname = 'localhost';\n"
Inhalt = Inhalt + "$login = '" + Einst["User_Write_unidb"] + "';\n"
Inhalt = Inhalt + "$password = '" + Einst["Password_Write_unidb"] + "';\n"
Inhalt = Inhalt + "$base = 'unidb';\n"
Inhalt = Inhalt + "$db = mysqli_connect($sqlhostname,$login,$password,$base) or die('Verbindungsfehler!');\n"
Inhalt = Inhalt + "mysqli_query($db, 'set character set utf8;');\n"
Inhalt = Inhalt + "?>"
Datei.write(Inhalt)
Datei.close()

Datei = open(Einst["Folder_Webserver_Dokument_Root"] + "/unidb/conf_unidb.php", "w")
Datei.write(Inhalt)
Datei.close()

Datei = open(Einst["Folder_Webserver_Dokument_Root"] + "/unidb/conf_DH_schreiben.php", "w")
Datei.write("<?php\n")
Datei.write("$sqlhostname = 'localhost';\n")
Datei.write("$login = '" + Einst["User_Write_DH"] + "';\n")
Datei.write("$password = '" + Einst["Password_Write_DH"] + "';\n")
Datei.write("$base = 'DH';\n")
Datei.write("$Rechner = 'localhost';\n")
Datei.write("$dbDH = mysqli_connect($sqlhostname,$login,$password,$base) or die('Verbindungsfehler!');\n")
Datei.write("mysqli_query($dbDH, 'set character set utf8;');\n")
Datei.write("?>")
Datei.close()

Datei = open(Einst["Folder_Webserver_Dokument_Root"] + "/unidb/conf_DH.php", "w")
Datei.write("<?php\n")
Datei.write("$sqlhostname = 'localhost';\n")
Datei.write("$login = '" + Einst["User_Read_DH"] + "';\n")
Datei.write("$password = '" + Einst["Password_Read_DH"] + "';\n")
Datei.write("$base = 'DH';\n")
Datei.write("$Rechner = 'localhost';\n")
Datei.write("$dbDH = mysqli_connect($sqlhostname,$login,$password,$base) or die('Verbindungsfehler!');\n")
Datei.write("mysqli_query($dbDH, 'set character set utf8;');\n")
Datei.write("?>")
Datei.close()
print("... done")
#ggf Service dh starten
if Einst["Automatic_Start_Interfaces"]  == "1":
    kop = subprocess.Popen('cp -r ' +  Quelle + 'Dateien/etc/init.d/dh /etc/init.d/', shell=True)
    subprocess.Popen.wait(kop)
    Datei = open(Quelle + "Dateien/etc/init.d/dh", "r")
    Textinhalt = Datei.read()
    Datei.close()
    Textinhalt = Textinhalt.replace("/opt", Einst["Folder_Python_Scripts"])
    Datei = open("/etc/init.d/dh", "w")
    Datei.write(Textinhalt)
    Datei.close()
    kop = subprocess.Popen('update-rc.d dh defaults', shell=True)
    subprocess.Popen.wait(kop)
    
    kop = subprocess.Popen('cp -r ' +  Quelle + 'Dateien/etc/init.d/unidb /etc/init.d/', shell=True)
    subprocess.Popen.wait(kop)
    Datei = open(Quelle + "Dateien/etc/init.d/unidb", "r")
    Textinhalt = Datei.read()
    Datei.close()
    Textinhalt = Textinhalt.replace("/opt", Einst["Folder_Python_Scripts"])
    Datei = open("/etc/init.d/unidb", "w")
    Datei.write(Textinhalt)
    Datei.close()
    kop = subprocess.Popen('update-rc.d unidb defaults', shell=True)
    subprocess.Popen.wait(kop)
    print("Services DH and unidb created.")

# eMail Einstellungen
print("Setting up the email configuration ...")
Datei = open(Quelle + "Dateien/eMail_einstellen.sql", "w")
Datei.write("USE unidb;\nUPDATE `Einstellungen` SET `Wert`='" + Einst["eMail_admin_address"] + "' WHERE `Parameter`='addAddress' AND `Eltern_ID` = 31;\n")
Datei.write("UPDATE `Einstellungen` SET `Wert`='" + Einst["eMail_ReplyTo"]  + "' WHERE `Parameter`='	addReplyTo' AND `Eltern_ID` = 31;\n")
Datei.write("UPDATE `Einstellungen` SET `Wert`='" + Einst["eMail_CharSet"]  + "' WHERE `Parameter`='	CharSet' AND `Eltern_ID` = 31;\n")
Datei.write("UPDATE `Einstellungen` SET `Wert`='" + Einst["eMail_Host"]  + "' WHERE `Parameter`='	Host' AND `Eltern_ID` = 31;\n")
Datei.write("UPDATE `Einstellungen` SET `Wert`='" + Einst["eMail_Password"] + "' WHERE `Parameter`='	Password' AND `Eltern_ID` = 31;\n")
Datei.write("UPDATE `Einstellungen` SET `Wert`='" + Einst["eMail_Port"] + "' WHERE `Parameter`='	Port' AND `Eltern_ID` = 31;\n")
Datei.write("UPDATE `Einstellungen` SET `Wert`='" + Einst["eMail_From"] + "' WHERE `Parameter`='	SetFrom' AND `Eltern_ID` = 31;\n")
Datei.write("UPDATE `Einstellungen` SET `Wert`='" + Einst["eMail_SMTPAuth"] + "' WHERE `Parameter`='	SMTPAuth' AND `Eltern_ID` = 31;\n")
Datei.write("UPDATE `Einstellungen` SET `Wert`='" + Einst["eMail_SMTPSec"] + "' WHERE `Parameter`='	SMTPSecure' AND `Eltern_ID` = 31;\n")
Datei.write("UPDATE `Einstellungen` SET `Wert`='" + Einst["eMail_Subject"] + "' WHERE `Parameter`='	Subject' AND `Eltern_ID` = 31;\n")
Datei.write("UPDATE `Einstellungen` SET `Wert`='" + Einst["eMail_urlPasswordCode"] + "' WHERE `Parameter`='	url_passwortcode' AND `Eltern_ID` = 31;\n")
Datei.write("UPDATE `Einstellungen` SET `Wert`='" + Einst["eMail_Username"] + "' WHERE `Parameter`='	Username' AND `Eltern_ID` = 31;\n")
Datei.close()
kop = subprocess.Popen('mysql -u root < ' + Quelle + 'Dateien/eMail_einstellen.sql', shell=True)
subprocess.Popen.wait(kop)
print("... done")
print()
print()
print("Installation completed.")
print("Next step: Log in to unidb and change the admin password.")
print()
print("user: admin")
print("passwort: adminDH")
print()
print("THE FILE config.txt CONTAINS A LOT OF USER NAMES AND PASSWORDS IN CLEAR TEXT FORMAT.")
print("YOU SHOULD AT LEAST DELETE THIS FILE FOR SAFETY REASONS!")
