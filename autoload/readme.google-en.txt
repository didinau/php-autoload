README

Installation example:


1. Unzip the archive

	tar xfv dina-php-autoload.tar.gz

2. Create target directory

	Example: mkdir /usr/local/lib/phplibs

3. Move to the right place

	cd dina-php-autoload
	mv autoload.php autoload /usr/local/lib/phplibs

4. Storage of your class files

	Create subfolders in the /usr/local/lib/phplibs directory
	and copy your scripts there for the autoloader should be reachable.

5. Configuration

	Open the file autoload.php in the /usr/local/lib/phplibs directory
	and match the lines between
		// configure starts here
	and
		// configure ends here
	to your needs:

		// configure starts here

		// which (sub) directory should be scanned: (or array ("*") for all dirs)
		public static $ dirs = array ("my", "html", "db", "test");

		// which filename extentions should be scanned
		public static $ fext = array ("php", "inc");

		// mail address for reporting errors (or leave it blank):
		public static $ email = "root @ localhost";

		// scripts to include allways
		public static $ includeallways = array ("/ var / www / myphp / functions1.php", "/ var / www / myphp / functions1.php");

		// configure ends here

	Explanation:

	public static $dirs=array("my","html","db","test");
	Lists the subdirectories in which to search for php files

	public static $fext=array("php","inc");
	Enter the name extensions of the php files you are using here

	public static $email="root@localhost";
	Enter the email address for error notifications here or leave it blank

	public static $includeallways=array("/var/www/myphp/functions1.php","/var/www/myphp/functions1.php");
	Enter the absolute path names of the php files to be always included here (maybe empty: array())

6. Perform a search for the static creation

	cd /usr/local/lib/phplibs/autoload

	Test run:
		php ./classhunter.php test

	To save:
		php ./classhunter.php putstatic

7. Customize your php files

	You only need one line in your php files for selective integration
	all files in the subdirectories:

	Example:
	File /var/www/html/index.php in the web server directory

	<?php
	// Automatically include all required classes:
	require_once "/usr/local/lib/phplibs/autoload.php";
	$test=new test ();
	...
	?>

If you have any errors or questions: please contact me!

Dieter Naujoks
