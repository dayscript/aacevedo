<?
include ( "class/HtmlParser.class.php" );
include ( "class/DataBase.class.php" );

$db  = new DataBase( "localhost", "autocontent", "parser", "autocontent", true );

/********************************************/
$trm = 1780;
$pag = new HtmlParser( "http://www.rba.gov.au/" );
if(!$pag->isEmpty())
{
	if( ( $err = $pag->select( ">Exchange Rate</a>", "</tr>" ) ) == 0 )
	{
		$pag->stripTags();
//		$trm = ereg_replace("[^0-9.]","",$pag->getElement(1));
		$AUD = $pag->getElement(1);
		$sql = "INSERT INTO economy " . 
			"( short, name, unit, value, date, updated ) VALUES " .
			"( 'aud', 'Dólar Australiano', 'USD', '" . $AUD . "', '" . date("Y-m-d") . "', '" . ereg_replace("^.*AEST ","",$pag->getElement(0)) . "')";
//		$db->query( $sql );
echo $sql . "\n";
		$sql = "INSERT INTO economy " . 
			"( short, name, unit, value, date, updated ) VALUES " .
			"( 'aud', 'Dólar Australiano', 'COP', '" . $AUD*$trm . "', '" . date("Y-m-d") . "', '" . ereg_replace("^.*AEST ","",$pag->getElement(0)) . "')";
echo $sql . "\n";
//		$db->query( $sql );
	}
	else
	{
		echo "Error: " . $err . " en TRM \n";
	}
}
?>
