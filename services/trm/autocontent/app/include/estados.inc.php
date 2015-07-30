<?
function estado($str)
{
	$estados= array (
			"soleado"  			=>	"caluroso.png",
		 	"despejado" 			=>	"caluroso.png",
		  	"lluvioso"  			=>	"lluvioso.png",
		 	"lluvias aisladas" 		=>	"lluvioso.png",
		 	"lloviznas" 			=>	"lluvioso.png",
		 	"lluvias" 			=>	"lluvioso.png",
		 	"nublado"  			=>	"nublado.png",
		   	"seminublado"  			=>	"seminublado.png",
		 	"parcialmente nublado" 		=>	"seminublado.png",
		 	"ligeramente nublado" 		=>	"seminublado.png",
		 	"cielo parcialmente nublado" 	=>	"seminublado.png",
		 	"cielo ligeramente nublado" 	=>	"seminublado.png",
		    	"tormenta"  			=>	"tormenta.png",
		 	"lluvias y tormentas eléctricas"=> 	"tormenta.png"
		 	);
 	return isset($estados[strtolower($str)]) ? $estados[strtolower($str)] : "transparente.png";
}
?>