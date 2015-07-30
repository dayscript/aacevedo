<?
	include ( "class/HtmlParser.class.php" );
	include ( "class/DataBase.class.php" );
	
	$db  = new DataBase( "localhost", "autocontent", "parser", "autocontent", true );
	
	//$sql ="DELETE FROM economy WHERE updated = '" . date("Y-m-d") . "'";
	$sql ="TRUNCATE economy";
	$db->query( $sql );
	
	
	function shapeDate ( $str )	{
		echo "{" . preg_match( "[a-zA-z]3. [0-9]2/[0-9]4", $str ) . "}";
	}
	
	function saveData( $short, $name, $unit, $value, $date )	{
		global $db;
		
		$sql = "INSERT INTO economy " . 
			"( short, name, unit, value, date, updated ) VALUES " .
			"('" . $short . "', '" . $name . "', '" . $unit . "', '" . $value . "', '" . $date . "', NOW( ) )";
		
		$db->query( $sql );
		
		// History
		$sql = "INSERT REPLACE INTO economy " . 
			"( short, name, unit, value, date, shaped_date, updated ) VALUES " .
			"('" . $short . "', '" . $name . "', '" . $unit . "', '" . $value . "', '" . $date . "', '" . shape( $date ) . "', NOW( ) )";
		$db->query( $sql );
	}

	
	$pag = new HtmlParser( "http://64.76.179.15/InvEconomicas/(yuboim55m2xfti554ifc32ah)/Indicadores/Home.aspx?C=B" );
	if( !$pag->isEmpty( ) )	{
		$item = array(	
				"Oro" => "Precio del Oro",
				"Café" => "Precios del Café Colombiano Suave Arábica",
				"Petróleo" => "Precios del Petróleo"
				);
		$short = array(	
				"Oro" => "oro",
				"Café" => "cafe",
				"Petróleo" => "petro"
				);
		foreach( $item as $name => $val )	{
			if( ( $err = $pag->select( $val, "</tr>" ) ) == 0 )	{
				$pag->stripTags( );
				saveData( $short[$name], $name, 'USD', $pag->getElement(1), $pag->getElement(2) );
			}
			else
				echo "Error: " . $err . " en " . $val . " \n";
		}
	
		$pag->reset( );
		if( ( $err = $pag->select( "UVR", "</tr>" ) ) == 0 )	{
			$pag->stripTags();
			saveData( $short[$name], 'UVR', 'UVR', $pag->getElement(1), $pag->getElement(2) );
			$db->query( $sql );
		}
		else
			echo "Error: " . $err . " en UVR \n";
		
		$pag->reset( );
	
		$item = array(	
				"IPC Año Corrido" => "IPC Año Corrido"
				);
		$short = array(	
				"IPC Año Corrido" => "ipcanocor"
				);
		foreach( $item as $name => $val )
		{
			if( ( $err = $pag->select( $val, "</tr>" ) ) == 0 )
			{
				$pag->stripTags();
				$sql = "INSERT INTO economy " . 
					"( short, name, unit, value, date, updated ) VALUES " .
					"('" . $short[$name] . "', '" . $name . "', '', '" . $pag->getElement(1) . "', '" . $pag->getElement(2) . "', '" . date("Y-m-d") . "')";
				$db->query( $sql );
			}
			else
			{
				echo "Error: " . $err . " en " . $val . " \n";
			}
		}
	}
	
	/********************************************/
	$pag->reset();
	if(!$pag->isEmpty())
	{
		$item = array(	
				"Dow Jones" => "Dow Jones \(Valor\)",
				"Nasdaq" => "Nasdaq \(Valor\)",
				"S&P 500" => "S&P 500 \(Valor\)",
				"Euro" => "Euro \(Valor\)",
				"IGBC" => "IGBC \(Valor\)"
				);
		$short = array(	
				"Dow Jones" => "dowjones",
				"Nasdaq" => "nasdaq",
				"S&P 500" => "syp",
				"Euro" => "euro",
				"IGBC" => "igbc"
				);
		foreach( $item as $name => $val )
		{
			if( ( $err = $pag->select( $val, "</tr>" ) ) == 0 )
			{
				$pag->stripTags();
				$sql = "INSERT INTO economy " . 
					"( short, name, unit, value, date, change_percent, updated ) VALUES " .
					"('" . $short[$name] . "', '" . $name . "', '', '" . ereg_replace( "[^0-9.]", "", $pag->getElement(1) ) . "', '" . $pag->getElement(2) . "', '', '" . date("Y-m-d") . "')";
				$db->query( $sql );
			}
			else
			{
				echo "Error: " . $err . " en " . $val . " \n";
			}
			$pag->reset();		
		}
	
		$item = array(	
				"Dow Jones" => "Dow Jones \(Variación Diaria\)",
				"Nasdaq" => "Nasdaq \(Variación Diaria\)",
				"S&P 500" => "S&P 500 \(Variación Diaria\)",
				"Euro" => "Euro \(Variación Diaria\)",
				"IGBC" => "IGBC \(Variación Diaria\)"
				);
		foreach( $item as $name => $val )
		{
			if( ( $err = $pag->select( $val, "</tr>" ) ) == 0 )
			{
				$pag->stripTags();
				$sql = "UPDATE economy SET change_percent = '" . $pag->getElement(1) . "' WHERE name = '" . $name . "'";
				$db->query( $sql );
			}
			else
			{
				echo "Error: " . $err . " en " . $val . " \n";
			}
			$pag->reset();		
		}
	
		$item = array(	
				"Libor 1 Año" => "Libor 1 Año",
				"Libor 1 Mes" => "Libor 1 Mes",
				"Libor 2 Meses" => "Libor 2 Meses",
				"Libor 3 Meses" => "Libor 3 Meses",
				"Libor 4 Meses" => "Libor 4 Meses",
				"Libor 5 Meses" => "Libor 5 Meses",
				"Libor 6 Meses" => "Libor 6 Meses",
				"Tasa de Usura E.A." => "Tasa de Usura E.A.",
				"Tasa Interbancaria E. A." => "Tasa Interbancaria E. A.",
				"DTF 180 Días (E.A.)" => "DTF 180 Días \(E.A.\)",
				"DTF 180 Días (T.A.)" => "DTF 180 Días \(T.A.\)",
				"DTF 360 Días (E.A.)" => "DTF 360 Días \(E.A.\)",
				"DTF 360 Días (T.A.)" => "DTF 360 Días \(T.A.\)",
				"DTF 90 Días (E.A.)" => "DTF 90 Días \(E.A.\)",
				"DTF 90 Días (T.A.)" => "DTF 90 Días \(T.A.\)",
				"Devaluación (Devaluación Año Corrido)" => "Devaluación \(Devaluación Año Corrido\)",
				"Devaluación (Devaluación Diaria)" => "Devaluación \(Devaluación Diaria\)",
				"Devaluación (Devaluación Ultimos Doce Meses)" => "Devaluación \(Devaluación Ultimos Doce Meses\)",
				"TES 10" => "TF 12-Feb-10",
				"TES 20" => "TF 24-Jul-20"
				);
	
		$short = array(	
				"Libor 1 Año" => "libor1ano",
				"Libor 1 Mes" => "libor1mes",
				"Libor 2 Meses" => "libor2mes",
				"Libor 3 Meses" => "libor3mes",
				"Libor 4 Meses" => "libor4mes",
				"Libor 5 Meses" => "libor5mes",
				"Libor 6 Meses" => "libor6mes",
				"Tasa de Usura E.A." => "tasausuea",
				"Tasa Interbancaria E. A." => "tibcariaea",
				"DTF 180 Días (E.A.)" => "dtf180ea",
				"DTF 180 Días (T.A.)" => "dtf180ta",
				"DTF 360 Días (E.A.)" => "dtf360ea",
				"DTF 360 Días (T.A.)" => "dtf360ta",
				"DTF 90 Días (E.A.)" => "dtf90ea",
				"DTF 90 Días (T.A.)" => "dtf90ta",
				"Devaluación (Devaluación Año Corrido)" => "devanoc",
				"Devaluación (Devaluación Diaria)" => "devdia",
				"Devaluación (Devaluación Ultimos Doce Meses)" => "devult12mes",
				"TES 10" => "tes10",
				"TES 20" => "tes20"
				);
		foreach( $item as $name => $val )
		{
			if( ( $err = $pag->select( $val, "</tr>" ) ) == 0 )
			{
				$pag->stripTags();
				$sql = "INSERT INTO economy " . 
					"( short, name, unit, value, date, updated ) VALUES " .
					"('" . $short[$name] . "', '" . $name . "', '', '" . $pag->getElement(1) . "', '" . $pag->getElement(2) . "', '" . date("Y-m-d") . "')";
				$db->query( $sql );
			}
			else
			{
				echo "Error: " . $err . " en " . $val . " \n";
			}
			$pag->reset();
		}
	
		if( ( $err = $pag->select( "Prime", "</tr>" ) ) == 0 )
		{
			$pag->stripTags();
			$sql = "INSERT INTO economy " . 
				"( short, name, unit, value, date, updated ) VALUES " .
				"('prime', 'Prime', '', '" . $pag->getElement(1) . "', '" . $pag->getElement(2) . "', '" . date("Y-m-d") . "')";
			$db->query( $sql );
		}
		else
		{
			echo "Error: " . $err . " en Prime \n";
		}
		$pag->reset();		
	}
	
	/********************************************/
	
	$pag = new HtmlParser( "http://64.76.179.15/InvEconomicas/(yuboim55m2xfti554ifc32ah)/Simuladores/TasasCambio.aspx?C=B" );
	if(!$pag->isEmpty())
	{
		if( ( $err = $pag->select( "TblTdItemHome02\">CAD - Dólar Canadiense", "</tr>" ) ) == 0 )
		{
			$pag->stripTags();
			$usd = round( (1/str_replace( ",", ".", $pag->getElement(1) ) ), 4 );
			$sql = "INSERT INTO economy " . 
				"( short, name, unit, value, date, updated ) VALUES " .
				"( 'cad', '" . $pag->getElement(0) . "', 'USD', '" . $usd . "', '" . date("Y-m-d") . "', '" . date("Y-m-d") . "')";
			$db->query( $sql );
			$sql = "INSERT INTO economy " . 
				"( short, name, unit, value, date, updated ) VALUES " .
				"( 'cad', '" . $pag->getElement(0) . "', 'COP', '" . $pag->getElement(2) . "', '" . date("Y-m-d") . "', '" . date("Y-m-d") . "')";
			$db->query( $sql );
		}
		else
		{
			echo "Error: " . $err . " en CAD \n";
		}
		$pag->reset();
		if( ( $err = $pag->select( "TblTdItemHome02\">GBP - Libra", "</tr>" ) ) == 0 )
		{
			$pag->stripTags();
			$usd = round( (1/str_replace( ",", ".", $pag->getElement(1) ) ), 4 );
	//		$usd = 1/str_replace( ",", ".", $pag->getElement(1) );
			$sql = "INSERT INTO economy " . 
				"( short, name, unit, value, date, updated ) VALUES " .
				"( 'libra', 'Libra', 'USD', '" . $usd . "', '" . date("Y-m-d") . "', '" . date("Y-m-d") . "')";
			$db->query( $sql );
			$sql = "INSERT INTO economy " . 
				"( short, name, unit, value, date, updated ) VALUES " .
				"( 'short', 'Libra', 'COP', '" . $pag->getElement(2) . "', '" . date("Y-m-d") . "', '" . date("Y-m-d") . "')";
			$db->query( $sql );
		}
		else
		{
			echo "Error: " . $err . " en CAD \n";
		}
	}
	
	/********************************************/
	
	$pag = new HtmlParser( "http://64.76.179.15/InvEconomicas/(uv1zpe45xqpdjiyucya04045)/Indicadores/Home.aspx?C=S&Id=5" );
	if(!$pag->isEmpty())
	{
		if( ( $err = $pag->select( ">TRM</a", "</tr>" ) ) == 0 )
		{
			$pag->stripTags();
			$trm = ereg_replace("[^0-9.]","",$pag->getElement(1));
			$sql = "INSERT INTO economy " . 
				"( short, name, unit, value, date, updated ) VALUES " .
				"( 'trm', 'TRM', 'COP', '" . $trm . "', '" . date("Y-m-d") . "', '" . $pag->getElement(2) . "')";
			$db->query( $sql );
		}
		else
		{
			echo "Error: " . $err . " en TRM \n";
		}
	}
	
	$pag = new HtmlParser( "http://64.76.179.15/InvEconomicas/(uv1zpe45xqpdjiyucya04045)/Indicadores/Home.aspx?C=S&Id=7" );
	if(!$pag->isEmpty())
	{
		if( ( $err = $pag->select( ">Euro \(Valor\)</a", "</tr" ) ) == 0 )
		{
			$pag->stripTags();
			$euro = ereg_replace("[^0-9.]","",$pag->getElement(1));
			$sql = "INSERT INTO economy " . 
				"( short, name, unit, value, date, updated ) VALUES " .
				"( 'euro', 'Euro', 'COP', '" . round($euro*$trm,2) . "', '" . date("Y-m-d") . "', '" . $pag->getElement(2) . "')";
			$db->query( $sql );
		}
		else
		{
			echo "Error: " . $err . " en Euro \n";
		}
	}
	
	$pag = new HtmlParser( "http://www.rba.gov.au/" );
	if(!$pag->isEmpty())
	{
		if( ( $err = $pag->select( ">Exchange Rate</a>", "</tr>" ) ) == 0 )
		{
			$pag->stripTags();
			$AUD = $pag->getElement(1);
			$sql = "INSERT INTO economy " . 
				"( short, name, unit, value, date, updated ) VALUES " .
				"( 'aud', 'Dólar Australiano', 'USD', '" . $AUD . "', '" . date("Y-m-d") . "', '" . ereg_replace("^.*AEST ","",$pag->getElement(0)) . "')";
			$db->query( $sql );
			$sql = "INSERT INTO economy " . 
				"( short, name, unit, value, date, updated ) VALUES " .
				"( 'aud', 'Dólar Australiano', 'COP', '" . round($AUD*$trm,2) . "', '" . date("Y-m-d") . "', '" . ereg_replace("^.*AEST ","",$pag->getElement(0)) . "')";
			$db->query( $sql );
		}
		else
		{
			echo "Error: " . $err . " en TRM \n";
		}
	}
	/********************************************/
	
	$pag = new HtmlParser( "http://caixacatalunya.ahorro.com/acnet/mercados_valores/indices/zona_indices.jsp" );
	if(!$pag->isEmpty())
	{
		$item = array(	
				"IBEX 35" => "IBEX  35",
				"FTSE 100" => "FTSE 100"
				);
		$short = array(	
				"IBEX 35" => "ibex35",
				"FTSE 100" => "ftse100"
				);
		foreach( $item as $name => $val )
		{
			if( ( $err = $pag->select( $val . "</a", "<img" ) ) == 0 )
			{
				$pag->stripTags();
				$sql = "INSERT INTO economy " . 
					"( short, name, unit, value, date, change_percent, updated ) VALUES " .
					"( '" . $short[$name] . "', '" . $name . "', '', '" . $pag->getElement(1) . "', '" . $pag->getElement(4) . "', '" . str_replace( ",", ".", $pag->getElement(3) ) . "', '" . date("Y-m-d") . "')";
				$db->query( $sql );
			}
			else
			{
				echo "Error: " . $err . " en " . $val . " \n";
			}
			$pag->reset();		
		}
	
	}
	
	/********************************************/
	
	$pag = new HtmlParser( "http://ar.finance.yahoo.com/q?s=^BVSP&d=t" );
	if(!$pag->isEmpty())
	{
		if( ( $err = $pag->select( "ltima transacci", "</tr" ) ) == 0 )
		{
			$pag->stripTags();
			$val    = explode( "&#183;", $pag->getElement(0) );
			ereg( "\((.*)\)", $pag->getElement(1), $regs );
			$vals = explode( "&#183;", $pag->getElement(0) );
	
			$sql = "INSERT INTO economy " . 
				"( short, name, unit, value, date, change_percent, updated ) VALUES " .
				"( 'bovespa', 'BOVESPA (IBOV)', '', '" . trim( $vals[1] ) . "', '', '" . $regs[1] . "', '" . date("Y-m-d") . "')";
			$db->query( $sql );
		}
		else
		{
			echo "Error: " . $err . " en IBOV \n";
		}
	}
	
	/********************************************/
	$sql = "INSERT INTO economy " . 
		"( short, name, unit, value, date, change_percent, updated ) VALUES " .
		"( 'libor3v', 'LIBOR T.V.', '', '3.12%', '', '', '" . date("Y-m-d") . "')";
	$db->query( $sql );
?>
