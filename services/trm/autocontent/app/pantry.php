<?
include ( "class/HtmlParser.class.php" );
include ( "class/DataBase.class.php" );

$db  = new DataBase( "localhost", "autocontent", "parser", "autocontent", true );
//$sql ="DELETE FROM economy WHERE updated = '" . date("Y-m-d") . "'";
$sql ="truncate pantry";
$db->query( $sql );

$pag = new HtmlParser( "http://www.cci.org.co/cci/cci_x/datos/Diario/DPR1.HTM" );
if(!$pag->isEmpty())
{
	$item = array(	
			"Azúcar refinada" => "<strong>Azúcar refinada"
			);
	$short = array(	
			"Azúcar refinada" => "azuref"
			);
	foreach( $item as $name => $val )
	{
		if( ( $err = $pag->select( $val, "<strong>", true ) ) == 0 )
		{
			$pag->stripTags();
			for($i=0,$max=count($pag->selection); $i<$max; $i++ )
				if( ereg( "Btá Corabastos", $pag->selection[$i] ) ) break;
			
			$sql = "INSERT INTO pantry " . 
				"( name, short, unit, value, date, updated ) VALUES " .
				"('" . $name . " - " . $pag->getElement($i) . "', '" . $short[$name] . "', 'Kg', '" . $pag->getElement($i+7) . "', '" . $pag->getElement($i+8) . "', '" . date("Y-m-d") . "')";
			$db->query( $sql );
		}
		else
		{
			echo "Error: " . $err . " en " . $val . " \n";
		}
	}
}

$pag = new HtmlParser( "http://www.cci.org.co/cci/cci_x/datos/Diario/DPR2.HTM" );
if(!$pag->isEmpty())
{
	$item = array(	
			"Chocolate dulce" => "<strong>Chocolate dulce"
			);
	$short = array(	
			"Chocolate dulce" => "chodul"
			);
	foreach( $item as $name => $val )
	{
		if( ( $err = $pag->select( $val, "<strong>", true ) ) == 0 )
		{
			$pag->stripTags();
			for($i=0,$max=count($pag->selection); $i<$max; $i++ )
				if( ereg( "Btá Corabastos", $pag->selection[$i] ) ) break;
			
			$sql = "INSERT INTO pantry " . 
				"( name, short, unit, value, date, updated ) VALUES " .
				"('" . $name . " - " . $pag->getElement($i) . "', '" . $short[$name] . "', 'Kg', '" . $pag->getElement($i+7) . "', '" . $pag->getElement($i+8) . "', '" . date("Y-m-d") . "')";
			$db->query( $sql );
		}
		else
		{
			echo "Error: " . $err . " en " . $val . " \n";
		}
	}
}

$pag = new HtmlParser( "http://www.cci.org.co/cci/cci_x/datos/Diario/DFR4.HTM" );
if(!$pag->isEmpty())
{
	$item = array(	
			"Naranja común" => "<strong>Naranja común"
			);
	$short = array(	
			"Naranja común" => "narcom"
			);
	foreach( $item as $name => $val )
	{
		if( ( $err = $pag->select( $val, "<strong>", true ) ) == 0 )
		{
			$pag->stripTags();
			for($i=0,$max=count($pag->selection); $i<$max; $i++ )
				if( ereg( "Btá Corabastos", $pag->selection[$i] ) ) break;
			
			$sql = "INSERT INTO pantry " . 
				"( name, short, unit, value, date, updated ) VALUES " .
				"('" . $name . " - " . $pag->getElement($i) . "', '" . $short[$name] . "', 'kg', '" . $pag->getElement($i+7) . "', '" . $pag->getElement($i+8) . "', '" . date("Y-m-d") . "')";
			$db->query( $sql );
		}
		else
		{
			echo "Error: " . $err . " en " . $val . " \n";
		}
	}
}

$pag = new HtmlParser( "http://www.cci.org.co/cci/cci_x/datos/Diario/DLAC.HTM" );
if(!$pag->isEmpty())
{
	$item = array(	
			"Leche past. 1000 c.c" => "<strong>Leche past. 1000 c.c"
			);
	$short = array(	
			"Leche past. 1000 c.c" => "leche1l"
			);
	foreach( $item as $name => $val )
	{
		if( ( $err = $pag->select( $val, "<strong>", true ) ) == 0 )
		{
			$pag->stripTags();
			for($i=0,$max=count($pag->selection); $i<$max; $i++ )
				if( ereg( "Cúcuta Cenabasto", $pag->selection[$i] ) ) break;
			
			$sql = "INSERT INTO pantry " . 
				"( name, short, unit, value, date, updated ) VALUES " .
				"('" . $name . " - " . $pag->getElement($i) . "', '" . $short[$name] . "', 'l', '" . $pag->getElement($i+5) . "', '" . $pag->getElement($i+8) . "', '" . date("Y-m-d") . "')";
			$db->query( $sql );
		}
		else
		{
			echo "Error: " . $err . " en " . $val . " \n";
		}
	}
}

//http://www.agronet.gov.co/www/htm3b/excepciones/cargaNet/netcarga127.aspx?cod=127&reporte=Reporte%20Precios%20SIPSA%20Semanal%20Para%20Peque%F1os%20Productores&submit.x=63&file=2006102617358_PreciosPP.rpt&mercado=23&codigo=127&excepcion=1&producto=501010&submit.y=21
?>
