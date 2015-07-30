<?
header('Content-Type: text/xml');
include( "/home/autocontent/app/class/DataBase.class.php" );
//include( "../app/class/XML.class.php" );

$db = new DataBase("localhost", "autocontent", "parser", "autocontent" );

if( $_GET['economy'] == "*" )
{
	$sql = "select * from stocks";
}
else
{
	$economy = explode(",", $_GET['economy'] );
	$sql = "SELECT * FROM stocks WHERE name IN( ";
	
	foreach( $economy as $item )
	{
		if($item != "" ) $sql .= "'" . trim( $item ) . "', ";
	}
	$sql = substr( $sql, 0, -2 ). ")";
}

$db->query( $sql );

echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
echo "<stocks>\n";
while( $row = $db->fetchArray() )
{
	echo "<item name=\"" . str_replace( "&", "&amp;", $row['name'] ) . "\" value=\"" . $row['value'] . "\" unit=\"" . $row['unit'] . "\" change_percent=\"" . $row['change_percent'] . "\" />";
}
echo "</stocks>\n";

?>
