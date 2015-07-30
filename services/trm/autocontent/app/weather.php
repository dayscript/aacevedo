<?
include ( "class/HtmlParser.class.php" );
include ( "class/DataBase.class.php" );

$db  = new DataBase( "localhost", "autocontent", "parser", "autocontent", true );
$sql ="TRUNCATE TABLE weather";
$db->query( $sql );
$sql ="TRUNCATE TABLE weather_forecast";
$db->query( $sql );

function wunderground( $city, $url )
{
	global $db;
	
	$pag  = new HtmlParser( $url );
	if(!$pag->isEmpty())
	{
		if( ( $err = $pag->select( "Temperature", "</span>" ) ) == 0 )
		{
			$pag->stripTags();
			$temp = explode( "/", str_replace( "&#176;", "", $pag->getElement(1) ) );
		}
		else
		{
			echo $err . "\n";
		}
	
		if( ( $err = $pag->select( "Conditions", "</span>" ) ) == 0 )
		{
			$pag->stripTags();
			$clouds = ( $pag->getElement(1) == "Unknown Precipitation" ) ? "" : $pag->getElement(1);
		}
		else
		{
			echo $err . "\n";
		}
		
		for( $i=0; $i<5; $i++ )
		{
			if( ( $err = $pag->select( "High", "Low" ) ) == 0 )
			{
				$pag->stripTags("<b>");
				ereg( "<b>(.*)</b> ([^.]*)\. (.*) / (.*)", $pag->getElement(0), $regs );
				$weekday[]         = $regs[1];
				$forecast_clouds[] = ( $regs[2] == "Unknown Precipitation" ) ? "" : $regs[2];
				$forecast_hi_f[]   = ereg_replace( "[^0-9]", "", $regs[3] );
				$forecast_hi_c[]   = ereg_replace( "[^0-9]", "", $regs[4] );
				ereg( "<b>(.*)</b> ([^.]*)\. (.*) / (.*)", $pag->getElement(1), $regs );
				$forecast_lo_f[]   = ereg_replace( "[^0-9]", "", $regs[3] );
				$forecast_lo_c[]   = ereg_replace( "[^0-9]", "", $regs[4] );
			}
			else
			{
				echo $err . "\n";
			}
		}
		
		$sql = "INSERT INTO weather ( city, temperature_c, temperature_f, clouds, date ) " .
				"VALUES (" . 
				"'" . $city . "', " . 
				"'" . ereg_replace( "[^0-9]", "", $temp[1] ) . "', " . 
				"'" . ereg_replace( "[^0-9]", "", $temp[0] ) . "', " . 
				"'" . $clouds . "', " . 
				"'" . date("Y-m-d H:i:s") . "' " . 
				")";
	
		$db->query( $sql );
		$insert_id = $db->insertId();
		
		for( $i=0; $i<5; $i++ )
		{
			$sql = "INSERT INTO weather_forecast ( id_weather, weekday, temperature_hi_c, temperature_lo_c, temperature_hi_f, temperature_lo_f, clouds ) " .
					"VALUES (" . 
					"'" . $insert_id . "', " . 
					"'" . $weekday[$i] . "', " . 
					"'" . ereg_replace( "[^0-9]", "", $forecast_hi_c[$i] ) . "', " . 
					"'" . ereg_replace( "[^0-9]", "", $forecast_lo_c[$i] ) . "', " . 
					"'" . ereg_replace( "[^0-9]", "", $forecast_hi_f[$i] ) . "', " . 
					"'" . ereg_replace( "[^0-9]", "", $forecast_lo_f[$i] ) . "', " . 
					"'" . trim( $forecast_clouds[$i] ) . "' " . 
					")";
			$db->query( $sql );
		}
	}
}

function weatherDotCom( $city, $code )
{
	global $db;
	$pag = new HtmlParser( "http://desktopfw.weather.com/weather/local/" . $code . "?&cc=*&ut=C&ud=M&ur=I&us=M&up=I&fwiam=100&d=10" );
	if(!$pag->isEmpty())
	{
		if( ( $err = $pag->select( "<cc>", "<bar>" ) ) == 0 )
		{
			$pag->stripTags();
//			$pag->printSelection();
			$sql = "INSERT INTO weather " . 
			"( city, temperature_c, clouds, date ) VALUES " .
			"( '" . $city . "', '" . $pag->getElement(1) . "', '" . $pag->getElement(3) . "', '" . date("Y-m-d") . "')";
			$db->query( $sql );
		}
		else
		{
			echo $err . "\n";
		}
		$pag->reset();		
	}
}

wunderground( "La Coca", "http://mobile.wunderground.com/cgi-bin/findweather/getForecast?brand=mobile&query=el+coca" );
wunderground( "Guayaquil", "http://mobile.wunderground.com/cgi-bin/findweather/getForecast?brand=mobile&query=guayaquil" );
wunderground( "Quito", "http://mobile.wunderground.com/cgi-bin/findweather/getForecast?brand=mobile&query=quito" );
wunderground( "Salinas", "http://mobile.wunderground.com/global/stations/84200.html" );

weatherDotCom( "Bogotá", "COXX0004" );
weatherDotCom( "Medellín", "COXX0020" );
weatherDotCom( "Armenia", "COXX0001" );
weatherDotCom( "Manizales", "COXX0019" );
weatherDotCom( "Rionegro", "COXX0043" );
weatherDotCom( "Envigado", "COXX0012" );
?>