<?php
	//error_reporting ( E_ALL );

	header( 'Content-Type: text/xml' );
	include( "app/class/DataBase.class.php" );
	//include( "/home/autocontent/app/class/DataBase.class.php" );
	//include( "../app/class/XML.class.php" );

	$db = new DataBase( "localhost", "autocontent", "parser", "autocontent" );

	echo "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
	echo "<main>\n";

	if( isset( $_GET['historical'] ) && $_GET['historical'] > 1 )	{
		require( 'historical.php' );
		echo "</main>\n";
		return;
	}


	/* Economy */
	if( isset($_GET['economy']) )
	{
		if( $_GET['economy'] == "*" )
		{
			$sql = "select * from economy_test";
		}
		else
		{
			$economy = explode(",", $_GET['economy'] );
			$sql = "SELECT * FROM economy_test WHERE name IN( ";

			foreach( $economy as $item )
			{
				if($item != "" ) $sql .= "'" . trim( $item ) . "', ";
			}
			$sql = substr( $sql, 0, -2 ). ") OR short IN (";

			foreach( $economy as $item )
			{
				if($item != "" ) $sql .= "'" . trim( $item ) . "', ";
			}
			$sql = substr( $sql, 0, -2 ). ") ORDER BY name, unit";
		}

		$db->query( $sql );
		echo "  <economy>\n";
		while( $row = $db->fetchArray() )
		{
  		$row['date'] = str_replace("dic","dec",$row['date']);
			echo "<item short=\"" . $row['short'] . "\" name=\"" . str_replace( "&", "&amp;", $row['name'] ) . "\" value=\"" . $row['value'] . "\" unit=\"" . $row['unit'] . "\" change_percent=\"" . $row['change_percent'] . "\" country=\"" . $row['country'] . "\" date=\"" . date("Y-m-d",strtotime($row['date'])) . "\" />";
		}
		echo "  </economy>\n";
	}

	/* Stocks */
	if( isset($_GET['stocks']) )
	{
		if( $_GET['stocks'] == "*" )
		{
			$sql = "select * from stocks";
		}
		else
		{
			$economy = explode(",", $_GET['stocks'] );
			$sql = "SELECT * FROM stocks WHERE name IN( ";

			foreach( $economy as $item )
			{
				if($item != "" ) $sql .= "'" . trim( $item ) . "', ";
			}
			$sql = substr( $sql, 0, -2 ). ")";
		}

		$db->query( $sql );
		echo "  <stocks>\n";
		while( $row = $db->fetchArray() )
		{
			echo "<item " .
			"name=\"" . str_replace( "&", "&amp;", $row['name'] ) . "\" " .
			"value=\"" . $row['value'] . "\" " .
			"variation=\"" . $row['variation'] . "\" " .
			"trend=\"" . $row['trend'] . "\" " .
			"change_percent=\"" . $row['change_percent'] . "\" />";
		}
		echo "  </stocks>\n";
	}

	/* Pantry */
	if( isset($_GET['pantry']) )
	{
		if( $_GET['pantry'] == "*" )
		{
			$sql = "select * from pantry";
		}
		else
		{
			$economy = explode(",", $_GET['pantry'] );
			$sql = "SELECT * FROM pantry WHERE name IN( ";

			foreach( $economy as $item )
			{
				if($item != "" ) $sql .= "'" . trim( $item ) . "', ";
			}
			$sql = substr( $sql, 0, -2 ). ")";
		}

		$db->query( $sql );
		echo "  <pantry>\n";
		while( $row = $db->fetchArray() )
		{
			echo "<item " .
			"short=\"" . $row['short'] . "\" " .
			"name=\"" . str_replace( "&", "&amp;", $row['name'] ) . "\" " .
			"value=\"" . $row['value'] . "\" " .
			"unit=\"" . $row['unit'] . "\" " .
			"change_percent=\"" . $row['change_percent'] . "\" >";
			echo "</item>";
		}
		echo "  </pantry>\n";
	}


	echo "</main>\n";

?>
