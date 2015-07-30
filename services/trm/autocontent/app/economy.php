<?
	error_reporting( 0 );
	ini_set ( 'display_errors', 'off');
	ini_set ( 'display_startup_errors', 'off');
	
	ini_set( 'date.timezone', 'America/Bogota' );
	
	include ( "/home/autocontent/app/class/HtmlParser.class.php" );
	include ( "/home/autocontent/app/class/DataBase.class.php" );
	include ( "/home/autocontent/app/class/DataKeeper.class.php" );
	
	$dKeeper = new DataKeeper( );
	$dKeeper->setDebug( 0 );
	$dKeeper->setShowErrors( false );
	
	$dKeeper->setConnection( "localhost", "autocontent", "parser", "autocontent" );
	$dKeeper->setMainTable( "economy" );
	$dKeeper->setHistoryTable( "economy_history" );
	
	//$db = $dKeeper->getDatabase( );
	//$sql ="DELETE FROM economy WHERE updated = '" . date("Y-m-d") . "'";
	// Borrar todos los datos de la tabla economy
	$dKeeper->truncateMainTable( );
	
	//Codes: http://www.iso.org/iso/english_country_names_and_code_elements

	//http://www.banrep.gov.co/series-estadisticas/see_ts_cam.htm#trm
	$dKeeper->setURL( "http://portal.banrep.gov.co/j2ee/encuesta/jsp/trm_del_dia.jsp", array( '<b', '</b', '\(', '\)' ) );
	$dKeeper->addData( "TRM", "trm", "COP", "TRM", "co", '</b', array( "value" => 'round( $data[2], 4 )', "date" => 'preg_replace( \'/^([^0-9]*) ([0-9]{1,2}) de ([A-Za-z]{3})([A-Za-z]*) de ([0-9]{4})(.*)$/\', \'$2 $3 $5\', $data[1] )' ) );
	
	$data = $dKeeper->parseData( );
	
	if( !isset( $data['trm'] ) )	{
		$dKeeper->setURL( "http://portal.banrep.gov.co/j2ee/encuesta/jsp/trm_del_dia.jsp", array( '<B', '</B', '\(', '\)' ) );
		$dKeeper->addData( "TRM", "trm", "COP", "TRM", "co", '</b', array( "value" => 'round( $data[2], 4 )', "date" => 'preg_replace( \'/^([^0-9]*) ([0-9]{1,2}) de ([A-Za-z]{3})([A-Za-z]*) de ([0-9]{4})(.*)$/\', \'$2 $3 $5\', $data[1] )' ) );
		
		$data = $dKeeper->parseData( );
	}
	
	$trm = $data['trm']['COP']['value'];
	
	//
	//$dKeeper->setURL( "http://64.76.179.15/InvEconomicas/(yuboim55m2xfti554ifc32ah)/Indicadores/Home.aspx?C=B" );
	$dKeeper->setURL( "http://investigaciones.bancolombia.com/inveconomicas/Indicadores/Home.aspx?id=MM" );
	$dKeeper->addData( "Libor 1 Año", "libor1ano", "%", "Libor 1 Año", "gb" );
	$dKeeper->addData( "Libor 1 Mes", "libor1mes", "%", "Libor 1 Mes", "gb" );
	$dKeeper->addData( "Libor 2 Meses", "libor2mes", "%", "Libor 2 Meses", "gb" );
	$dKeeper->addData( "Libor 3 Meses", "libor3mes", "%", "Libor 3 Meses", "gb" );
	$dKeeper->addData( "Libor 4 Meses", "libor4mes", "%", "Libor 4 Meses", "gb" );
	$dKeeper->addData( "Libor 5 Meses", "libor5mes", "%", "Libor 5 Meses", "gb" );
	$dKeeper->addData( "Libor 6 Meses", "libor6mes", "%", "Libor 6 Meses", "gb" );
	$dKeeper->addData( "DTF 90 Días (E.A.)", "dtf90ea", "%", "DTF 90 Días \(E.A.\)", "co" );
	$dKeeper->addData( "DTF 90 Días (T.A.)", "dtf90ta", "%", "DTF 90 Días \(T.A.\)", "co" );
	$dKeeper->addData( "DTF 180 Días (E.A.)", "dtf180ea", "%", "DTF 180 Días \(E.A.\)", "co" );
	$dKeeper->addData( "DTF 180 Días (T.A.)", "dtf180ta", "%", "DTF 180 Días \(T.A.\)", "co" );
	$dKeeper->addData( "DTF 360 Días (E.A.)", "dtf360ea", "%", "DTF 360 Días \(E.A.\)", "co" );
	$dKeeper->addData( "DTF 360 Días (T.A.)", "dtf360ta", "%", "DTF 360 Días \(T.A.\)", "co" );
	$dKeeper->addData( "Prime", "prime", "%", "Prime", "co" );
	$dKeeper->parseData( );

	//
	$dKeeper->setURL( "http://caixacatalunya.ahorro.com/acnet/mercados_valores/indices/zona_indices.jsp" );
	$dKeeper->addData( "IBEX 35", "ibex35", "", "IBEX  35</a", "es" );
	$dKeeper->addData( "FTSE 100", "ftse100", "", "FTSE 100</a", "gb" );
	$dKeeper->parseData( "<img", array( "value" => 'str_replace( ",", ".", str_replace( ".", "", $data[ 1 ] ) )', "change_percent" => 'str_replace( ",", ".", $data[ 3 ] )', "date" => '$data[ 4 ]' ) );
	
	//
	$dKeeper->setURL( "http://finance.yahoo.com/q?s=^BVSP" );
	$dKeeper->addData( "IBOVESPA (BVSP)", "bovespa", "", "Prev Close:", "br" );
	$dKeeper->parseData( "</tr>", array( "value" => 'str_replace( ",", "", $data[ 1 ] )', "date" => 'date("Y-m-d")' ) );
	
	/*
	//
	$dKeeper->setURL( "http://www.bunkerworld.com/prices/" );
	//$dKeeper->addData( "Bunker", "bunker", "USD", '<td class="wti-date"', "us", '</tr', array( "value" => 'str_replace( \'$\', "", $data[ 1 ] ) ', "date" => 'date("Y-m-d")' ) );
	$dKeeper->addData( "Bunker IFO380 Singapore", "bunker_ifo380_si", "USD", '>Singapore', "sg" );
	$dKeeper->addData( "Bunker IFO180 Singapore", "bunker_ifo180_si", "USD", '>Singapore', "sg", '</tr', array( "value" => 'substr( trim( $data[2] ), 0, strpos( trim( $data[2] ), "." ) + 3 )', "change_percent" => 'substr( trim( $data[2] ), strpos( trim( $data[2] ), "." ) + 3 )', "date" => 'date("Y-m-d")' ) );
	$dKeeper->addData( "Bunker IFO380 Houston", "bunker_ifo380_ho", "USD", '>Houston', "us" );
	$dKeeper->addData( "Bunker IFO180 Houston", "bunker_ifo180_ho", "USD", '>Houston', "us", '</tr', array( "value" => 'substr( trim( $data[2] ), 0, strpos( trim( $data[2] ), "." ) + 3 )', "change_percent" => 'substr( trim( $data[2] ), strpos( trim( $data[2] ), "." ) + 3 )', "date" => 'date("Y-m-d")' ) );
	$dKeeper->addData( "Bunker IFO380 Rotterdam", "bunker_ifo380_ro", "USD", '>Rotterdam', "nl" );
	$dKeeper->addData( "Bunker IFO180 Rotterdam", "bunker_ifo180_ro", "USD", '>Rotterdam', "nl", '</tr', array( "value" => 'substr( trim( $data[2] ), 0, strpos( trim( $data[2] ), "." ) + 3 )', "change_percent" => 'substr( trim( $data[2] ), strpos( trim( $data[2] ), "." ) + 3 )', "date" => 'date("Y-m-d")' ) );
	$dKeeper->addData( "Bunker IFO380 Fujairah", "bunker_ifo380_fu", "USD", '>Fujairah', "ae" );
	$dKeeper->addData( "Bunker IFO180 Fujairah", "bunker_ifo180_fu", "USD", '>Fujairah', "ae", '</tr', array( "value" => 'substr( trim( $data[2] ), 0, strpos( trim( $data[2] ), "." ) + 3 )', "change_percent" => 'substr( trim( $data[2] ), strpos( trim( $data[2] ), "." ) + 3 )', "date" => 'date("Y-m-d")' ) );
	$dKeeper->parseData( '</tr', array( "value" => 'substr( trim( $data[1] ), 0, strpos( trim( $data[1] ), "." ) + 3 )', "change_percent" => 'substr( trim( $data[1] ), strpos( trim( $data[1] ), "." ) + 3 )', "date" => 'date("Y-m-d")' ) );
	*/
	
	//
	//$dKeeper->setURL( "http://www.upme.gov.co/GeneradorConsultas/Consulta_Indicador.aspx?Ind=39" );
	$date = mktime( 0, 0, 0, date( "m" ), 1, date( "Y" ) ) - ( 60 * 60 * 24 );
	$dKeeper->setURL( "http://www.upme.gov.co/GeneradorConsultas/Consulta_Indicador.aspx?Ind=39" );
	$dKeeper->addData( "Gasolina (Bogotá)", "gasolina_bta", "COP", date( "Y-m-d", $date ), "co" );
	$dKeeper->parseData( '</table', array( "value" => 'str_replace( ",", ".", str_replace( ".", "", $data[1] ) )', "date" => '$data[0]' ) );
	
	//
	$dKeeper->setURL( "http://www.upme.gov.co/GeneradorConsultas/Consulta_Indicador.aspx?Ind=29" );
	$dKeeper->addData( "ACPM (Bogotá)", "acpm_bta", "COP", date( "Y-m-d", $date ), "co" );
	$dKeeper->parseData( '</table', array( "value" => 'str_replace( ",", ".", str_replace( ".", "", $data[1] ) )', "date" => '$data[0]' ) );
	
	//
	$dKeeper->setURL( "http://www.asocana.com.co/publico/precios.aspx" );
	$dKeeper->addData( "Azucar Crudo NY (cents/lb)", "azucar_lb", "USD", '<td width="110"><font color="Black">', "us" );
	$dKeeper->parseData( '</tr', array( "value" => 'str_replace( ",", ".", str_replace( ".", "", $data[ 1 ] ) )', "date" => '$data[ 0 ]' ) );
	
	
	$dKeeper->setURL( "https://linea.davivienda.com/dinamicos/ComercialServlet?item=indicadores" );
	$dKeeper->addData( "UVR", "uvr", "", 'UVR', "co" );
	//$dKeeper->addData( "TRM", "trm", "COP", 'TRM', "co" );
	//$dKeeper->addData( "Euro", "euro", "COP", 'Euro', "fr" );
	//$dKeeper->addData( "DTF E.A.", "dtf", "%", 'DTF E.A.', "co" );
	//$dKeeper->addData( "Petróleo (WTI)", "wti", "USD", 'Petróleo WTI', "co" );
	$dKeeper->addData( "Café", "cafe", "USD", 'CAFÉ', "co" );
	//$dKeeper->addData( "Devaluación Diaria", "devaluaciondia", "%", 'Devaluación diaria', "co" );
	$dKeeper->addData( "Interbancaria E.A.", "interbancariaea", "%", 'Tasa Interbancaria', "co" );
	//$dKeeper->addData( "Libor (6 meses)", "libor6mes", "%", 'Libor \\(6 meses\\)', "gb" );
	//$dKeeper->addData( "Prime Rate (180 dìas)", "prime180dia", "%", 'Prime Rate \\(180 dìas\\)', "co" );
	$dKeeper->addData( "IGBC", "igbc", "", 'IGBC', "co" );
	$dKeeper->addData( "COLCAP", "colcap", "", 'COLCAP', "co" );
	//$dKeeper->addData( "Dow Jones", "dowjones", "", 'Dow Jones', "us" );
	//$dKeeper->addData( "NASDAQ", "nasdaq", "", 'Nasdaq', "us" );
	//$dKeeper->addData( "Desempleo", "desempleo", "", 'Desempleo Nac', "co" );
	//$dKeeper->addData( "Devaluación Anual", "devaluacionano", "%", 'Devaluación anual', "co" );
	//$dKeeper->addData( "IPP Mensual", "ippmes", "%", 'IPP Mensual', "co" );
	//$dKeeper->addData( "PIB", "pib", "%", 'PIB', "co" );
	
	$dKeeper->parseData( '</tR', array( "value" => 'trim( str_replace( array("$","U","%",","), "", $data[1] ) )', "date" => 'date("Y-m-d")') );
	
	//
	/*
	try	{
		$xml = new SimpleXMLElement( implode( "", file( "http://www.finanzaspersonales.com.co/indicadores/Indicadores_.xml" ) ) );
		$use = array(
			"bolivar" => array( "COP", "ve", "bolivar", "Bolívar" ),
			//"dow_jones" => array( "", "us", "dowjones", "Dow Jones" ),
			"ecopetrol___" => array( "COP", "co", "ecopetrol", "Ecopetrol" ),
			//"euro__tasa_vigente" => array( "COP", "fr", "euro", "Euro" ),
			"ipc" => array( "COP", "co", "ipc", "IPC" ),
			//"petroleo_wti__pre" => array( "COP", "co", "wti", "Petroleo (WTI)" )
		);
		
		foreach( $xml->Indicador as $item )	{
			$short = $dKeeper->shapeName( utf8_decode( $item['nombre'] ) );
			if( isset( $use[$short] ) )
				$dKeeper->saveData( $use[$short][2], $use[$short][3], $use[$short][0], str_replace( ",", ".", str_replace( ".", "", $item['valor'] ) ), date( "Y-m-d" ), 0, $use[$short][1] );
		}
	}
	catch( Exception $e )	{
		echo "\nERROR: " . $e->getMessage( );
	}
	*/
	
	//
	try	{
		$xml = new SimpleXMLElement( implode( "", file( "http://quote.cnbc.com/quote-html-webservice/quote.htm?&symbols=GBP%3DX|CAD%3DX|AUD%3DX|EUR%3DX|.djia|COMP|.spx&requestMethod=quick&noform=1&realtime=1&client=flexQuote&output=xml" ) ) );
		$use = array(
			"GBP=X" => array( "USD", "uk", "libra", "Libra Esterlina", false ),
			"CAD=X" => array( "USD", "ca", "cad", "Dolar Canadiense", true ),
			"AUD=X" => array( "USD", "au", "aud", "Dolar Australiano", false ),
			"EUR=X" => array( "USD", "fr", "euro", "Euro", false ),
			".DJIA" => array( "", "us", "dowjones", "Dow Jones", false ),
			"COMP" => array( "", "us", "nasdaq", "NASDAQ", false ),
			".SPX" => array( "", "us", "syp", "S&P 500", false )
		);
		
		foreach( $xml->QuickQuote as $item )	{
			$short = (string)$item->symbol;
			if( isset( $use[$short] ) )	{
				$dKeeper->saveData( $use[$short][2], $use[$short][3], $use[$short][0], ( $use[$short][4] ? ( 1 / (float)$item->last ) : (float)$item->last ), date( "Y-m-d" ), 0, $use[$short][1] );
				if( $use[$short][0] == "USD" )
					$dKeeper->saveData( $use[$short][2], $use[$short][3], "COP", ( $use[$short][4] ? ( 1 / (float)$item->last ) : (float)$item->last ) * $trm, date( "Y-m-d" ), 0, $use[$short][1] );
			}
		}
	}
	catch( Exception $e )	{
		echo "\nERROR: " . $e->getMessage( );
	}
	
	/*
	//
	$sql = "INSERT INTO economy " .
		"( short, name, unit, value, date, change_percent, updated ) VALUES " .
		"( 'libor3v', 'LIBOR T.V.', '', '3.12%', '', '', '" . date("Y-m-d") . "')";
	//$db->query( $sql );
	*/
