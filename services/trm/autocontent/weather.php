<?
header('Content-Type: text/xml');
include( "/home/autocontent/app/class/DataBase.class.php" );

$db = new DataBase("localhost", "autocontent", "parser", "autocontent" );

$cities = explode(",", $_GET['city'] );
/*$sql = "SELECT *, w.clouds wclouds, wf.clouds wfclouds " .
	"FROM weather w, weather_forecast wf " .
	"WHERE " .
	"w.id = wf.id_weather AND ".
	"city IN( ";*/

$sql = "SELECT *, w.clouds wclouds, wf.clouds wfclouds " .
	"FROM weather w LEFT JOIN weather_forecast wf " .
	"ON w.id = wf.id_weather ".
	"WHERE " .
	"city IN( ";

foreach( $cities as $city )
{
	if(trim($city) != "" ) $sql .= "'" . trim($city) . "', ";
}
$sql = substr( $sql, 0, -2 ). ")";

$db->query( $sql );
$row = $db->fetchArray();
$city = $row['city'];

echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
echo "<main>\n";
echo "<weather>\n";
echo "<city name=\"" . utf8_encode( $row['city'] ) . "\" celsius=\"" . $row['temperature_c'] . "\" farenheit=\"" . $row['temperature_f'] . "\" >\n";
echo "<clouds>" . $row['wclouds'] . "</clouds>\n";
while( $row = $db->fetchArray() )
{
	if( $city != $row['city'] )
	{
		echo "</city>\n";
		echo "<city name=\"" . utf8_encode( $row['city'] ) . "\" celsius=\"" . $row['temperature_c'] . "\" farenheit=\"" . $row['temperature_f'] . "\" >\n";
		echo "<clouds>" . $row['wclouds'] . "</clouds>\n";
		$city = $row['city'];
	}
	if( $row['id_weather'] != "" )
	{
		echo "<forecast weekday=\"" . $row['weekday'] . "\" hi_celsius=\"" . $row['temperature_hi_c'] . "\" hi_farenheit=\"" . $row['temperature_hi_f'] . "\" lo_celsius=\"" . $row['temperature_lo_c'] . "\" lo_farenheit=\"" . $row['temperature_lo_f'] . "\" >\n";
		echo "<clouds>" . $row['wfclouds'] . "</clouds>\n";
		echo "</forecast>\n";
	}
}
echo "</city>\n";
echo "</weather>\n";
echo "</main>\n";
?>
