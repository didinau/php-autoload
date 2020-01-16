<?php
/*
 * filehunter.php
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

if(!isset($GLOBALS["_al_nop"]))
{
	require_once __DIR__."/../autoload.php";
	spl_autoload_unregister(array('_my_autoload', 'load'));
}

class classhunter extends _my_autoload_config
{
	static function mail($_class,$_content)
	{
		// limit mail to once an hour
		if(strlen(_my_autoload_config::$email))
		{
			$mailok=false;
			$tmp=sys_get_temp_dir();
			if(is_dir($tmp))
			{
				$balancer=$tmp."/".__CLASS__.".bal";
				if(is_file($balancer))
				{
					$ft=filemtime($balancer);
					if(time()>$ft+3600)
						$mailok=true;
				}else{
					$mailok=true;
				}
				if($mailok)
				{
					file_put_contents($balancer," ");
					mail(_my_autoload_config::$email,"From autoload on ".gethostname().": problems loading class ".$_class,$_content);
				}
			}
		}
	}
	static function help($_idx)
	{
		$help="\n\nHelp:\n\n";
		switch($_idx)
		{
			case 1:
				return $help."chdir to ".__DIR__."\nand there run classhunter: php ./classhunter.php putstatic\n";
				break;
			case 2:
				return $help."do you have write permissions?";
		}
	}

	function ch_serialize($_array)
	{
		return json_encode($_array,JSON_UNESCAPED_SLASHES | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
	}

	function ch_dir($_path,$_rekursiv=false)
	{
		$ret=array();
		if(is_dir($_path))
		{
			if($dh=opendir($_path))
			{
				while(($fn=readdir($dh)))
				{
					if(is_file($_path."/".$fn))
					{
						if(($pos=strrpos($fn,"."))!==false)
						{
							$fext=substr($fn,$pos+1);
						}else{
							$fext="";
						}
						$ret[$_path."/".$fn]=array(
							"mtime"=>filemtime($_path."/".$fn),
							"size"=>filesize($_path."/".$fn),
							"fext"=>$fext,
							);
					}else{
						if($fn!="." and $fn!="..")
						{
							if($_rekursiv and is_dir($_path."/".$fn))
							{
								$ret=array_merge($ret,$this->ch_dir($_path."/".$fn,true));
							}
						}
					}
				}
				closedir($dh);
			}
		}
		return $ret;
	}

	function find_classes()
	{
		$files=array();
		foreach(parent::$dirs as $idx)
		{
			$files=array_merge($files,$this->ch_dir(parent::$path.$idx,true));
		}

		$dyn_classes=array();
		if(count($files)) foreach($files as $idx=>$val)
		{
			if(in_array($val["fext"],parent::$fext) or in_array("*",parent::$fext))
			{
				$dyn_classes=array_merge_recursive($dyn_classes,$this->parse_file($idx));
			}
		}
		return $dyn_classes;
	}

	function write_classes_static($_dyn_classes)
	{
		$autoload_templ=file_get_contents(parent::$tpl);
		$repl="";
		foreach($_dyn_classes as $class=>$file)
		{
			$repl.="\t\t'".$class."'=>'".$file."',\n";
		}
		$out=str_replace(parent::$needle,$repl,$autoload_templ);
		if(file_put_contents(parent::$incl,$out)===false)
		{
			error_log(_E_."File ".parent::$incl." not written!\n".$this->help(2));
		}else{
			print parent::$incl." written for ".count($_dyn_classes)."\n";
		}
	}

	function get_needle($_src)
	{
		return is_array($_src) ? $_src[1]:$_src;
	}

	function parse_file($_file)
	{
		$result=array();
		$namespace="";
		$ptoken = token_get_all(php_strip_whitespace($_file));
		for($i=0;$i<count($ptoken);$i++)
		{
			$needle=$this->get_needle($ptoken[$i]);
			$needle_=strtolower($needle);
			if($needle_=="namespace" and isset($ptoken[$i+2]) and $this->get_needle($ptoken[$i+1])==" ")
			{
				$s=2;
				$namespace="";
				while(isset($ptoken[$i+$s]))
				{
					$n=$this->get_needle($ptoken[$i+$s]);
					if($n==";")
					{
						break;
					}else{
						$namespace.=$n;
					}
					$s++;
				}
				$i+=$s;
				$namespace.="\\";
			}
			if(in_array($needle_,parent::$related) and isset($ptoken[$i+2]) and isset($ptoken[$i+1][1]) and $ptoken[$i+1][1]==" ")
			{
				$result[$namespace.$ptoken[$i+2][1]]=substr($_file,strlen(parent::$path));
				$i+=2;
			}
		}
		return $result;
	}
}

if(!isset($GLOBALS["_al_nop"]))
{
	$ch=new classhunter();
	$argv=$_SERVER["argv"];

	if(!isset($argv[1]))
	{
		$argv[1]="";
	}

	switch(strtolower(substr($argv[1],0,3)))
	{
		case "tes":
			print_r($ch->find_classes());
			break;
		case "get":
			print $ch->ch_serialize($ch->find_classes())."\n";
			break;
		case "put":
			$ch->write_classes_static($ch->find_classes());
			break;
		default:
			print "call: ".$argv[0]." test|putstatic|getdynamic\n";
			exit(1);
			break;
	}
}

?>