<?php
//error_reporting(-1);
//xdebug_disable();
define("REPLICATE_DATABASE_ERROR_LOG", "logloglog");
date_default_timezone_set("America/Bogota");
ini_set('error_log', REPLICATE_DATABASE_ERROR_LOG);

if($argc == 0)
{
	echo "Uso: php bot.php NOMBRE_DEL_ARCHIVO_DE_PROPIEDADES";
	exit();
}

$propertiesFileName = $argv[1];

$p            = new Properties($propertiesFileName);
$dbFileName   = "db_" . str_replace("/", "_", $propertiesFileName);
$db           = file($dbFileName ,FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES);

$imgExtension = ".jpg";
$imgFileType  = "image/jpeg";

$importFiles    = $p->getProperty("import");
$url            = $p->getProperty("url");
$mainNode       = $p->getProperty("main_node");
$objAttributes  = $p->getProperty("dayware_object");
$idSite         = $p->getProperty("id_site");
$idUser         = $p->getProperty("id_user");
$idFolder       = $p->getProperty("id_folder");
$dayFileType    = $p->getProperty("dayware_file_type");
$datetimeFormat = $p->getProperty("datetime_format");
$idPiece        = $p->getProperty("id");
$article_name   = $p->getProperty("article_name");
$cdate          = $p->getProperty("cdate");

if(!$db)
	$db = array();

if(!is_array($importFiles))
	$importFiles = array($importFiles);
	
foreach($importFiles as $file)
{
	if($file != "")
		require_once($file);
}

$log = new LogWriter(REPLICATE_DATABASE_ERROR_LOG);
$dom = new DomDocument();

$fm = new FileManager();
$fm->setIdUser($idUser);
$fm->setActualFolder($idFolder);

$log->writeLn("Iniciando archivo de propiedades: " . $propertiesFileName);
if(!$dom->load($url))
{
	$log->writeLn("Error leyendo xml: " . $url);
	die("error");
}
else
{
	$log->writeLn("Leyendo xml: " . $url);
}

$xpRoot = new domxpath($dom);
$mainNodes = $xpRoot->query($mainNode);
$idList = "";

foreach($mainNodes as $node)
{
	$idN = $xpRoot->query($idPiece, $node);
	$id = $idN->item(0)->nodeValue;

	$log->writeLn("Agregando artículo: " . $id);

	if(array_search(trim($id), $db) !== false)
	{
		$log->writeLn("Artículo repetido: " . $id);
		$idList .= $id . "\n";
		 continue;
	}

	try
	{
		$log->writeLn("Creando artículo: " . $id);
		$dayFileName = $xpRoot->query($article_name, $node)->item(0)->nodeValue;
		$dayFileName = utf8_decode($dayFileName);
		$dayFileName = addslashes($dayFileName);
		$idFile = $fm->addFile($dayFileName, $dayFileType, $idSite);
	}
	catch( Exception $e )
	{
		$log->writeLn("Error creando archivo: " . $e->getMessage());	
		continue;
	}
	
	$daywareObj = $fm->createObject($dayFileType);
	$daywareObj->setIdFile($idFile);
	$daywareObj->setParentFile($idFolder);
	$daywareObj->load();

	$dayFileCreationDate = $xpRoot->query($cdate, $node)->item(0)->nodeValue;
	preg_match("/([0-9]{2})\/([0-9]{2})\/([0-9]{4})(.*$)/", $dayFileCreationDate, $match );
	$daywareObj->setCreationDate($match[3] . "-" . $match[2] . "-" . $match[1] . (isset($match[4]) ? $match[4] : ""));

	$log->writeLn("Agregando propiedades");	
	foreach($objAttributes["properties"] as $property => $value)
	{
		if(is_array($value))
		{
			$nv = "";
			foreach($value as $val)
			{
				$v = $xpRoot->query($val, $node);
				$nv .= $v->item(0)->nodeValue . "\n";
			}			
		}
		else
		{
			$v = $xpRoot->query($value, $node);
			$nv = $v->item(0)->nodeValue;
		}
		$nv = utf8_decode(str_replace("<br />", "<br /><br />", nl2br($nv)));
//		$nv = mysql_real_escape_string($nv);
		$daywareObj->setProperty($property, $nv);
	}
	
	$log->writeLn("Agregando atributos");	
	foreach($objAttributes["attributes"] as $property => $value)
	{
		$method = "set" . ucfirst($property);
		$v = $xpRoot->query($value, $node);
		$nv = $v->item(0)->nodeValue;
//		$nv = mysql_real_escape_string($nv);
		$nv = utf8_decode(nl2br($nv));
		$daywareObj->$method($nv);
	}

	$log->writeLn("Agregando imágenes");	
	foreach($objAttributes["images"] as $imgName => $imgArr)
	{
		$imgUrl  = $xpRoot->query($imgArr["url"], $node)->item(0)->nodeValue;
		if($imgUrl == "") continue;
		
		$imgSize = explode("x", strtolower($imgArr["size"]));
		
		$imgObj = new SimpleImage();
		$imgObj->load($imgUrl);
		$imgObj->cropAndResize($imgSize[0], $imgSize[1]);
		@ob_start();
		$imgObj->output();
		$imageblob = @ob_get_contents();
		@ob_end_clean();
		$daywareObj->storeContentFile($imgName . $imgExtension, $imgFileType, addslashes($imageblob));
		$imgObj->destroy();
	}
	$daywareObj->setIdSite($idSite);
	$daywareObj->setPublished(1);
	$daywareObj->setMainContent(1);
	$daywareObj->setFilePosition(0);

	try
	{
		$daywareObj->commit();
	}
	catch( Exception $e )
	{
		$log->writeLn("Error commit: " . $e->getMessage());
		continue;
	}
	
	$log->writeLn("Noticia insertada: " . $id);
	$idList .= $id . "\n";
}

$dbHd = fopen($dbFileName, "w");
fwrite($dbHd, $idList);
fclose($dbHd);
echo "¡Gracias!, vuelvan pronto\n";
die();


/* ******************************************************
 * Classes  *********************************************
********************************************************/

Class LogWriter
{
	var $hd;
	var $file_path;
	function LogWriter( $file_path )
	{
		$this->hd = fopen($file_path, "a");
	}
	
	function write($str)
	{
		fwrite($this->hd, date("Ymd Hi: ") . $str );
	}
	
	function writeLn($str)
	{
		$this->write( $str . "\n" );
	}
	
	function close()
	{
		fclose($this->hd);
	}
}

Class Properties
{
	var $props;
	
	function Properties( $file_name )
	{
		$this->props = array();
		
		$file = file( $file_name );
		if( empty( $file ) ) die ( "Properties File Empty or not present\n\n" );
		$i = 0;
		foreach( $file as $ln )
		{
			$i++;
//			if ( !ereg("^#", trim ($ln) ) && trim($ln) != "" )
			if ( !preg_match("/^#/", trim ($ln) ) && trim($ln) != "" )
			{
				$ln_arr = explode( "=", $ln );
				if ( count( $ln_arr ) != 0 )
				{
					if( count( $ln_arr ) == 2 )
					{
						$arr = $this->strToParams(trim($ln_arr[0]),trim($ln_arr[1]));
						$this->props = array_merge_recursive($this->props, $arr);
					}
					else 
						echo ( "Sintaxis error in properties File on line " . $i . "\n\n" );
				}
			}
		}
	}
	
	function strToParams($str,$val)
	{
		if(($pos=strpos($str, ".")) !== false )
		{
			$key = substr($str, 0, $pos);
			$str = preg_replace( "/^" . $key . "\./", "", $str);
			$arr[$key] = $this->strToParams($str,$val);
		}
		else
			$arr[$str]=$val;
			
		return $arr;
	}
	
	function getProperty( $name )
	{
		return $this->props[$name];
	}
}

class SimpleImage
{
	var $image;
	var $image_type;
	
	function load($filename)
	{
		$image_info = getimagesize($filename);
		$this->image_type = $image_info[2];
		if( $this->image_type == IMAGETYPE_JPEG )
		{
			$this->image = imagecreatefromjpeg($filename);
		}
		elseif( $this->image_type == IMAGETYPE_GIF ) 
		{
			$this->image = imagecreatefromgif($filename);
		} 
		elseif( $this->image_type == IMAGETYPE_PNG ) 
		{
			$this->image = imagecreatefrompng($filename);
		}
	}
	function save($filename, $image_type=IMAGETYPE_JPEG, $compression=90, $permissions=null) 
	{
		if( $image_type == IMAGETYPE_JPEG )
		{
			imagejpeg($this->image,$filename,$compression);
		}
		elseif( $image_type == IMAGETYPE_GIF )
		{
			imagegif($this->image,$filename);         
		} elseif( $image_type == IMAGETYPE_PNG )
		{
			imagepng($this->image,$filename);
		}   
		if( $permissions != null)
		{
			chmod($filename,$permissions);
		}
	}
	function output($image_type=IMAGETYPE_JPEG)
	{
		if( $image_type == IMAGETYPE_JPEG )
		{
			imagejpeg($this->image);
		}
		elseif( $image_type == IMAGETYPE_GIF )
		{
			imagegif($this->image);         
		} 
		elseif( $image_type == IMAGETYPE_PNG ) 
		{
			imagepng($this->image);
		}   
	}
	function getWidth() 
	{
		return imagesx($this->image);
	}
	function getHeight()
	{
		return imagesy($this->image);
	}
	function resizeToHeight($height) 
	{
		$ratio = $height / $this->getHeight();
		$width = $this->getWidth() * $ratio;
		$this->resize($width,$height);
	}
	function resizeToWidth($width) 
	{
		$ratio = $width / $this->getWidth();
		$height = $this->getheight() * $ratio;
		$this->resize($width,$height);
	}
	function scale($scale) 
	{
		$width = $this->getWidth() * $scale/100;
		$height = $this->getheight() * $scale/100; 
		$this->resize($width,$height);
	}
	function resize($width,$height) 
	{
		$new_image = imagecreatetruecolor($width, $height);
		imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
		$this->image = $new_image;   
	}
	function cropAndResize($width, $height)
	{
		$actualRatio = $this->getWidth() / $this->getHeight();
		$newRatio = $width / $height;

		$new_image = imagecreatetruecolor($width, $height);
		if($actualRatio < $newRatio)
		{
			$this->resizeToWidth($width);
			$src_y = ($this->getHeight() - $height) / 2;
			imagecopyresampled($new_image, $this->image, 0, 0, 0, $src_y, $width, $height, $width, $height);
		}
		else
		{
			$this->resizeToHeight($height);
			$src_x = ($this->getWidth() - $width) / 2;
			imagecopyresampled($new_image, $this->image, 0, 0, $src_x, 0, $width, $height, $width, $height);
		}	
		$this->image = $new_image;   		
	}

	function destroy()
	{
		imagedestroy($this->image);
	}
}
?>