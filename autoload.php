<?php
/*
 * autoload.php
 * 
 * Copyright 2020 Dieter Naujoks <devops@naujoks.homeip.net>
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * 
 * 
 */

define("_E_","ERR: ");
define("_D_",__DIR__."/");

class _my_autoload_config
{

// configure starts here

	// which (sub)directory should be scanned: (or array("*") for all dirs)
	public static $dirs=array("my","html","geolite2","test");
	#public static $dirs=array("*");

	// which filename extentions should be scanned
	public static $fext=array("php","inc");

	// mail address for reporting errors (or leave it blank):
	public static $email="root@localhost";

	// scripts to include allways
	public static $includeallways=array("/var/www/myphp/functions1.php","/var/www/myphp/functions1.php");

// configure ends here

	public static $path=_D_;
	public static $tpl=_D_."autoload/static.classes.php.template";
	public static $incl=_D_.'autoload/static.classes.php';

	public static $cl=_D_."autoload/classhunter.php";
	public static $needle="\"{CLASSES}\"";
	public static $related=array("class","interface","trait");
}

// try to include static declared data
if(is_file(_my_autoload_config::$incl))
{
	require_once _my_autoload_config::$incl;
}else{
	// not there? try include of the template
	if(is_file(_my_autoload_config::$tpl))
	{
		require_once _my_autoload_config::$tpl;
	}else{
		// nothing found...
		$err=_E_."autoload problem: not found "._my_autoload_config::$incl." or "._my_autoload_config::$tpl."!";
		error_log($err);
		print $err."\n";
		exit(1);
	}
}

class _my_autoload extends _my_statics_classes
{
	static function load($_class)
	{
		// detect errors: file moved/deleted, not scanned 
		if(!isset(parent::$classes[$_class]) or (isset($classes[$_class]) and !is_file($classes[$_class])))
		{
			$err=_E_."autoload-static problem: \"".$_class."\" not found!";
			$ch=_my_autoload_config::$cl;
			if(file_exists($ch))
			{
				// include the classhunter for dyn search
				$GLOBALS["_al_nop"]=true;
				require_once $ch;
				$ch=new classhunter();
				$classes=$ch->find_classes();
				if (isset($classes[$_class]))
				{
					$err.=" ...But found dynamic!";
					$ch->mail($_class,$err.$ch->help(1));
					error_log($err);
					require_once __DIR__.'/'.$classes[$_class];
				}else{
					$err.="\n."._E_."and also not found dynamic!";
					$ch->mail($_class,$err.$ch->help(1));
					error_log($err);
					return false;
				}
			}else{
				// classhunter not found..
				$err.="\n"._E_._my_autoload_config::$cl."not found!\n";
				error_log($err);
				return false;
			}
		}else{
			//found an existing file...
			require_once __DIR__.'/'.parent::$classes[$_class];
		}
	}
}

spl_autoload_register(array('_my_autoload', 'load'));

if(is_array(_my_autoload_config::$includeallways) and count(_my_autoload_config::$includeallways))
{
	foreach(_my_autoload_config::$includeallways as $file)
	{
		if(is_file($file))
		{
			require_once $file;
		}
	}
}

?>