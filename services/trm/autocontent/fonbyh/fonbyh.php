<?
$headers =  getallheaders();
/*if( ereg( "text/xml", $headers['Accept'] ) )
{
	header( "Location: http://www.dayscript.com" );
	exit;
}*/

include( "/home/autocontent/app/class/DataBase.class.php" );

$db = new DataBase("localhost", "autocontent", "parser", "autocontent" );

$sql = "SELECT * FROM economy WHERE name " .
  "IN ( " .
  "'TRM', " . 
  "'UVR', ". 
  "'DTF 90 Días', " . 
  "'Petróleo', " . 
  "'Café', " . 
  "'Tasa de Usura E.A.', " . 
  "'Libor', " .
  "'Dow Jones', " . 
  "'Nasdaq', " .
  "'IPC Básica', " . 
  "'Euro', " . 
  "'Prime', " .
  "'Devaluación Ultimos Doce Meses' " .
  ") " .
  "ORDER BY name";
$db->query( $sql );
$i=0;
echo "tha_name = new Array();\n";
echo "tha_value = new Array();\n";
echo "unit = new Array();\n";
while( $row = $db->fetchArray() )
{
	echo "tha_name[" . $i . "] = '" . $row['name'] . "';\n";
	echo "tha_value[" . $i . "]= '" . $row['value'] . "';\n";
	echo "unit[" . $i . "]= '" . $row['unit'] . "';\n";
	$i++;
}
?>
