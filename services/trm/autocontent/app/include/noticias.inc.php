<?
$mes = array(
		1  => "Enero",
		2  => "Febrero",
		3  => "Marzo",
		4  => "Abril",
		5  => "Mayo",
		6  => "Junio",
		7  => "Julio",
		8  => "Agosto",
		9  => "Septiembre",
		10 => "Octubre",
		11 => "Noviembre",
		12 => "Diciembre"
);

if (!empty($sitio->arSitio))
{
	$sitio->seleccion("components/bann_small_left.html", "Begin otros titulares seccion" );
	$sitio->arSitio = $sitio->seleccion;
	$iIndice=0;

	if(date("G") <= 3)
	{
		while ($sitio->seleccion("titularlink", "</tr>", $iIndice ))
		{
			$sitio->arStripTags();
			$noticia = ereg_replace("Mostrar Video", "", $sitio->seleccion[1]);
			if(eregi(date("j") ." ". $mes[date("n")] ." \(RCN\)", $noticia) || eregi(date("j") - 1 ." ". $mes[date("n")] ." \(RCN\)", $noticia))
			{
				$noticia = ereg_replace("[ ]{2,}", " ", $noticia);
				$noticia = ereg_replace("&#8211;", "-", $noticia);

				$titular = ereg_replace("[ ]{2,}", " ", $sitio->seleccion[0]);
				$titular = ereg_replace("&#8211;", "-", $titular);

				$sql = "INSERT INTO noticias (tipo, titular, noticia) VALUES ('$tipo', '". ereg_replace("&#8221;", "\"", ereg_replace("&#8220;", "\"", $titular)) ."', '". ereg_replace("&#8220;", "\"", ereg_replace("&#8221;", "\"", $noticia)) ."')";
				db_query($sql);
			}
			$iIndice = $sitio->iIndice;
		}
	} else
	{
			while ($sitio->seleccion("titularlink", "</tr>", $iIndice ))
		{
			$sitio->arStripTags();
			$noticia = ereg_replace("Mostrar Video", "", $sitio->seleccion[1]);
			if(eregi(date("j") ." ". $mes[date("n")] ." \(RCN\)", $noticia))
			{
				$noticia = ereg_replace("[ ]{2,}", " ", $noticia);
				$noticia = ereg_replace("&#8211;", "-", $noticia);
				$sql = "INSERT INTO noticias (tipo, titular, noticia) VALUES ('$tipo', '". ereg_replace("&#8221;", "\"", ereg_replace("&#8220;", "\"", $sitio->seleccion[0])) ."', '". ereg_replace("&#8220;", "\"", ereg_replace("&#8221;", "\"", $noticia)) ."')";
				db_query($sql);
			}
			$iIndice = $sitio->iIndice;
		}
	}

} else
{
		echo "todo está Mal";	
}
?>