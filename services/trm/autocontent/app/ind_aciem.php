<?
include( "/home/autocontent/app/class/DataBase.class.php" );

$db = new DataBase("localhost", "autocontent", "parser", "autocontent" );

$economy = explode(",", "DTF 90 Días,Euro,UVR,TRM,S&P 500,IBEX 35,FTSE 100,Dow Jones");
$sql = "SELECT * FROM economy WHERE name IN( ";

foreach( $economy as $item )
{
	if($item != "" ) $sql .= "'" . trim( $item ) . "', ";
}
$sql = substr( $sql, 0, -2 ). ")";

$db->query( $sql );

$xml = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
$xml .= "<main>\n";
$xml .= "  <economy>\n";
while( $row = $db->fetchArray() )
{
	$xml .= "<item name=\"" . str_replace( "&", "&amp;", $row['name'] ) . "\" value=\"" . $row['value'] . "\" unit=\"" . $row['unit'] . "\" change_percent=\"" . $row['change_percent'] . "\" />";
}
$xml .= "  </economy>\n";
$xml .= "</main>\n";


/**
*
* Envia la informacion por POST
*
*/

$host = "www.centrodenegociosaciem.org";
//$host = "192.168.0.41";

$ReqHeader =
"POST /aciem/servlet/ExternalData HTTP/1.1\n".
"Host: $host\n".
"Content-Type: text/xml\n".
"Content-Length: " . strlen ($xml) . "\n\n".
"$xml\n";

// Open the connection to the host
$socket = fsockopen($host, 80, &$errno, &$errstr);
if (!$socket) {
	$Result["errno"] = $errno;
	$Result["errstr"] = $errstr;
	return $Result;
}

$idx = 0;
fputs($socket, $ReqHeader);
fclose ($socket);
echo "Enviado " . $host;

?>
