<?
include ( "class/HtmlParser.class.php" );
include ( "class/DataBase.class.php" );

$db  = new DataBase( "localhost", "autocontent", "parser", "autocontent", true );
$sql ="truncate stocks";
$db->query( $sql );

$pag = new HtmlParser( "http://www.bvc.com.co/bvcweb/mostrarpagina.jsp?codpage=500" );
if(!$pag->isEmpty())
{
	$item = array(	
			"Buscar por fecha:" => "Buscar por fecha:"
			);
	foreach( $item as $name => $val )
	{
		if( ( $err = $pag->select( $val, "esquina_inf_mercados_1.jpg" ) ) == 0 )
		{
			$pag->stripTags("<input>");
			ereg("(value=')([0-9]{4}-[0-9]{2}-[0-9]{2})(')", $pag->getElement(1), &$regs);
			for($i=13,$max=count($pag->selection)-13-(count($pag->selection)%5); $i<$max; $i+=5 )
			{
				$trend = ($pag->getElement($i+4)==0)?"0":($pag->getElement($i+4)<0?"-1":"1");
				$sql = "INSERT INTO stocks " . 
					"( name, short, value, variation, trend, date, updated ) VALUES " .
					"('" . $pag->getElement($i) . "', '" . 
					$pag->getElement($i) . "', '" . 
					$pag->getElement($i+1) . "', '" . 
					$pag->getElement($i+4) . "', '"  .
					$trend . "', '"  .
					$regs[2] . "', '"  .
					date("Y-m-d") . "')";
				$db->query( $sql );
			}
			
		}
		else
		{
			echo "Error: " . $err . " en " . $val . " \n";
		}
	}
}

?>