README

Installation-Beispiel:


1.	Entpacken des Archivs

	tar xfv dina-php-autoload.tar.gz

2. 	Zielverzeichnis erstellen

	Beispiel: mkdir /usr/local/lib/phplibs

3.	An den richtigen Ort verschieben

	cd dina-php-autoload
	mv autoload.php autoload /usr/local/lib/phplibs

4. Ablage Ihrer Klassen-Dateien

	Erstellen Sie Unterordner im Verzeichnis /usr/local/lib/phplibs
	und kopieren Sie dahin Ihre Scripte die für den Autoloader
	erreichbar sein sollen.
	
5. Konfiguration

	Öffnen Sie im Verzeichnis /usr/local/lib/phplibs die Datei
	autoload.php und passen Sie die Zeilen zwischen
	// configure starts here
	und
	// configure ends here
	an Ihre Bedürfnisse an:

		// configure starts here

		// which (sub)directory should be scanned: (or array("*") for all dirs)
		public static $dirs=array("my","html","db","test");

		// which filename extentions should be scanned
		public static $fext=array("php","inc");

		// mail address for reporting errors (or leave it blank):
		public static $email="root@localhost";

		// scripts to include allways
		public static $includeallways=array("/var/www/myphp/functions1.php","/var/www/myphp/functions1.php");

		// configure ends here

	Erklärung:

	public static $dirs=array("my","html","db","test");
	Listet die Unterverzeichnisse auf, in denen nach php-Dateien gesucht werden soll

	public static $fext=array("php","inc");
	Geben Sie hier die von Ihnen verwendeten Namenserweiterungen der php-Dateien an

	public static $email="root@localhost";
	Tragen Sie hier die email-Adresse für Fehlerbenachrichtigungen an oder lassen Sie sie leer

	public static $includeallways=array("/var/www/myphp/functions1.php","/var/www/myphp/functions1.php");
	Tragen Sie hier die absoluten Pfadnamen der immer einzubindenden php-dateien ein (oder leer: array())

6. Suchvorgang für die statische Erstellung ausführen

	cd /usr/local/lib/phplibs/autoload

	Testdurchlauf:
		php ./classhunter.php test

	Speichern:
		php ./classhunter.php putstatic

7. Passen Sie Ihre php-Dateien an

	Sie benötigen nur noch eine Zeile in Ihren php-Dateien zum selektiven Einbinden
	aller in den Unterverzeichnissen befindlichen Dateien:
	
	Beispiel: 
	Datei /var/www/html/index.php im Web-Server-Verzeichnis
	
	<?php
	// Alle benötigten Klassen automatisch einbinden:
	require_once "/usr/local/lib/phplibs/autoload.php";
	$test=new test();
	...
	?>

Bei Fehlern oder Fragen: kontaktieren Sie mich gern!

Dieter Naujoks
