<?
header('Content-Type: text/plain');
include( "/home/autocontent/app/class/DataBase.class.php" );
//include( "../app/class/XML.class.php" );

$db = new DataBase("localhost", "autocontent", "parser", "autocontent" );

if( $_GET['economy'] == "*" )
{
	$sql = "select * from economy";
}
else
{
	$economy = explode(",", $_GET['economy'] );
	$sql = "SELECT * FROM economy WHERE name IN( ";
	
	foreach( $economy as $item )
	{
		if($item != "" ) $sql .= "'" . trim( $item ) . "', ";
	}
	$sql = substr( $sql, 0, -2 ). ")";
}

$db->query( $sql );
$i = 1;
while( $row = $db->fetchArray() )
{
	echo 
	  "&indic_" .   $i . "=" . rawurlencode (rawurlencode (str_replace ("ó", "o", $row['name']))) . 
		"&actual_" . $i . "=" . $row['value'] . 
		"&cambio_" . $i . "=" . $row['change_percent'];
	$i++;
}
?>