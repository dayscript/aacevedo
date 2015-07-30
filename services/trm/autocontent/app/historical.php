<?
//error_reporting ( E_ALL );
	
	// HEADERS , Enviados en index.php
	// $db , Definido en index.php
	
	
	function printAsXML ( $type, $data )	{
		echo "<" . $type . ">";
		
		$xmlData = array( );
		$xmlDataKeys = array( );
		
		foreach( $data as $elem )	{
			if( !isset( $xmlData[$elem['short']] ) )	{
				$xmlData[$elem['short']] = array( "name" => utf8_encode( $elem['name'] ), "country" => $elem['country'], "childs" => array( ) );
				if( !in_array( $elem['short'], $xmlDataKeys ) )
					$xmlDataKeys[] = $elem['short'];
			}
			
			if( !isset( $xmlData[$elem['short']]["childs"][$elem['unit']] ) )
				$xmlData[$elem['short']]["childs"][$elem['unit']] = array( "childs" => array( ) );
			
			$xmlData[$elem['short']]["childs"][$elem['unit']]["childs"][] = array( "value" => $elem['value'], "date" => $elem['shaped_date'], "change_percent" => $elem['change_percent'] );
		}
		
		foreach( $xmlDataKeys as $key )	{
			$elem = $xmlData[$key];
			echo '<item short="' . $key . '" name="' . str_replace( '&', '&amp;', utf8_decode( $elem['name'] ) ) . '" country="' . $elem['country'] . '" >';
				foreach( $elem['childs'] as $name => $unit )	 {
					echo '<unit name="' . ( $name ) . '" >';
					foreach( $unit['childs'] as $indic )
						echo '<value value="' . ( $indic['value'] ) . '" date="' . $indic['date'] . '" change_percent="' . $indic['change_percent'] . '" />';
					echo '</unit>';
				}
			echo '</item>';
		}
		echo "</" . $type . ">";
	}
	
	
	$historical = ( isset( $_GET['historical'] ) && is_numeric( $_GET['historical'] ) && $_GET['historical'] > 1 ? $_GET['historical'] : 2 );
	
	/* Economy */
	if( isset( $_GET['economy'] ) )	{
		
		// Creación de las variables para el query siguiente 
		$db->query( "set @index='0'", false );
		$db->query( "set @pos=0", false );
		
		$sql = "
			SELECT * 
			FROM ( 
				SELECT *, 
					IF( @index != CONCAT(short,unit), @pos:=1, @pos:=@pos + 1 ) AS row_index, 
					IF( @index != CONCAT(short,unit), @index:=CONCAT(short,unit), '' ) AS discard 
				FROM economy_history ";
				
		if( $_GET['economy'] != "*" )	{
			$economy = str_replace( ",", "','", $_GET['economy'] );
			$sql .= "WHERE name IN( '" . $economy . "' ) OR short IN ( '" . $economy . "' ) ";
		}
		
		$sql .= "ORDER BY name, unit, shaped_date DESC
				)
			AS result
			WHERE row_index <= " . $historical . " ";

		$db->query( $sql, false );
		
		$data = array( );
		while( $row = $db->fetchArray( ) )
			$data[] = $row;
		
		printAsXML ( "economy", $data );
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
	
?>
