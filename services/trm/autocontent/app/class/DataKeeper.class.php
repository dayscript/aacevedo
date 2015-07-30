<?php

	/**
	 * Una clase para disminuir el c?digo de los indicadores de Bender
	 *
	 * @autor Nelson Daza
	 * @date 20090331
	 * @version 0.0.0.1
	 *
	**/


	class DataKeeper	{
		private $MONTHS_EN = array ( '', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dic' );
		private $MONTHS_SP = array ( '', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic' );

		private $data;
		private $length;		//int

		private $mainTable;
		private $historyTable;

		private $debug;			// 0,1,2
		private $showErrors;	//Bool

		private $database;
		private $htmlParser;


		function DataKeeper( )	{
			$this->clean( );
		}

		function clean( )	{
			$this->data = array( );
			$this->length = 0;
		}

		function setConnection ( $host, $user, $passwd, $database )	{
			$this->database = new DataBase( $host, $user, $passwd, $database, $this->debug );
			$this->clean( );
		}

		function setURL( $url, $sepTags = array( ) )	{
			$this->htmlParser = new HtmlParser( $url, $sepTags );
			$this->clean( );
		}

		function setDebug( $debug )	{
			$this->debug = $debug;
			$this->database->debug = $this->debug;
		}
		function isDebug( )	{
			return $this->debug;
		}

		function setShowErrors( $errors )	{
			$this->showErrors = ( $errors === true );
			$this->database->SHOWERRORS = $this->showErrors;
		}
		function isShowErrors( )	{
			return $this->showErrors;
		}

		function setMainTable( $table )	{
			$this->mainTable = $table;
		}
		function getMainTable( )	{
			return $this->mainTable;
		}

		function setHistoryTable( $table )	{
			$this->historyTable = $table;
		}
		function getHistoryTable( )	{
			return $this->historyTable;
		}

		function getDatabase( )	{
			return $this->database;
		}

		function truncateMainTable( )	{
			$sql ="TRUNCATE " . $this->mainTable;
			$this->database->query( $sql );
		}

		// Funci?n
		// @name = Nombre com?n del indicador
		// @short = Nombre ?ndice en la base de datos
		// @unit = unidad/moneda
		// @parser = Parte del HTML para buscar en el HtmlParser (como expresi?n regular PHP)
		// @country = C?digo de pa?s usando ISO3166-1 alpha-2 (http://www.iso.org/iso/english_country_names_and_code_elements)
		// @select = Par?metro de parseo para HtmlParser->select
		// @eval = array( campo => codigo_a_evaluar )

		// EJ: eval
		// El valor encontrado debe ser modificado para ser el entero encontrado m?s 10
		// array( 'valor' => 'int($pag->getElement(1)) + 10' )

		// EJ: $data->add( "Oro", "oro", "USD", "Precio del Oro", "us"[, "</tr>"][, "array( 'valor' => 'int($pag->getElement(1)) + 10' )"] );
		function addData( $name, $short, $unit, $parser, $country, $select = NULL, $eval = NULL )	{
			$this->data[$short . "_" . $unit] = array( "name" => $name, "short" => $short, "unit" => $unit, "parser" => $parser, "country" => $country, "select" => $select, "eval" => $eval );
			$this->length = count( array_keys ( $this->data ) );
		}

		function length( )	{
			return $this->$length;
		}

		function getKeys( )	{
			return array_keys ( $this->data );
		}

		function getItem( $index )	{
			return $this->data[$index];
		}

		function getItemAt( $index )	{
			$keys = array_keys( $this->data );
			return $this->data[$keys[$index]];
		}

		// Las fechas encontradas por el HtmlParser deben ser convertidas a DATE
		// EJ: shapeDate( 'Mar. 09/2005' ); ==> '2005-09-03'
		function shapeDate ( $str )	{

			if( preg_match( "/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $str ) )	{
				// Formato correcto
			}
			if( preg_match( "/^([0-9]{2}):([0-9]{2})$/", $str ) )	{
				// Formato de hora correcto ??
				$str = date( "Y-m-d" );
			}
			else if( preg_match( "/^([0-9]{2}) ([A-Za-z]{3}) ([0-9]{4})$/", $str ) )	{
				$date = preg_split( "/^([0-9]{2}) ([A-Za-z]{3}) ([0-9]{4})$/", $str, -1, PREG_SPLIT_DELIM_CAPTURE );
				// Explota la cadena '09 Mar 2005' para convertirla en DATE.
				$month = array_search( $date[2], $this->MONTHS_EN );
				if( !$month )
					$month = array_search( $date[2], $this->MONTHS_SP );
				if( !$month )
					$month = date( "m" );
				$month = ( $month <= 9 ? "0" : "" ) . (int) $month;
				$day = ( $date[1] <= 9 ? "0" : "" ) . (int) $date[1];

				$str = $date[3] . "-" . $month . "-" . $day;
			}
			else if( preg_match( "/([0-9]{2})\/([A-Za-z]{3})\/([0-9]{4})/", $str ) )	{
				$date = preg_split( "/([0-9]{2})\/([A-Za-z]{3})\/([0-9]{4})/", $str, -1, PREG_SPLIT_DELIM_CAPTURE );
				// Explota la cadena '09/Mar/2005' para convertirla en DATE.
				$month = array_search( $date[2], $this->MONTHS_EN );
				if( !$month )
					$month = array_search( $date[2], $this->MONTHS_SP );
				if( !$month )
					$month = date( "m" );
				$month = ( $month <= 9 ? "0" : "" ) . (int) $month;
				$day = ( $date[1] <= 9 ? "0" : "" ) . (int) $date[1];

				$str = $date[3] . "-" . $month . "-" . $day;
			}
			else if( preg_match( "/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/", $str ) )	{
				$date = preg_split( "/^([0-9]{2})\/([0-9]{2})\/([0-9]{4})$/", $str, -1, PREG_SPLIT_DELIM_CAPTURE );
				// Explota la cadena '09/Mar/2005' para convertirla en DATE.
				$month = ( $date[2] <= 9 ? "0" : "" ) . (int) $date[2];
				$day = ( $date[1] <= 9 ? "0" : "" ) . (int) $date[1];

				$str = $date[3] . "-" . $month . "-" . $day;
			}
			else if( preg_match( "/^([A-Za-z]{3}). ([0-9]{2})\/([0-9]{4})$/", $str ) )	{
				$date = preg_split( "/^([A-Za-z]{3}). ([0-9]{2})\/([0-9]{4})$/", $str, -1, PREG_SPLIT_DELIM_CAPTURE );
				// Explota la cadena 'Mar. 09/2005' para convertirla en DATE.
				$month = array_search( $date[1], $this->MONTHS_EN );
				if( !$month )
					$month = array_search( $date[1], $this->MONTHS_SP );
				if( !$month )
					$month = date( "m" );
				$month = ( $month <= 9 ? "0" : "" ) . (int) $month;
				$day = ( $date[2] <= 9 ? "0" : "" ) . (int) $date[2];

				$str = $date[3] . "-" . $month . "-" . $day;
			}
			else if( preg_match( "/^([A-Za-z]{3}) ([0-9]{2})$/", $str ) )	{
				$date = preg_split( "/^([A-Za-z]{3}) ([0-9]{2})$/", $str, -1, PREG_SPLIT_DELIM_CAPTURE );
				// Explota la cadena 'Mar. 09' para convertirla en DATE.
				$month = array_search( $date[1], $this->MONTHS_EN );
				if( !$month )
					$month = array_search( $date[1], $this->MONTHS_SP );
				if( !$month )
					$month = date( "m" );
				$month = ( $month <= 9 ? "0" : "" ) . (int) $month;
				$day = ( $date[2] <= 9 ? "0" : "" ) . (int) $date[2];

				$str = date( "Y" ) . "-" . $month . "-" . $day;
			}
			return $str;
		}

		// Transformaci?n para tener un nombre corto
		// EJ: Tasa de Usura => tasa_de_usura
		function shapeName ( $str )	{
			$base = ' ??????????????????????????????????????????????????????????????Rr';
			$replace = '_aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';

			return preg_replace('/[^_0-9a-z]/', '', strtolower( strtr( $str, $base, $replace ) ) );
		}

		// Insertar los datos en la BDD
		// EJ: saveData( 'trm', 'TRM', 'COP', '2000', 'Mar. 29/2005' );
		function saveData( $short, $name, $unit, $value, $date, $change_percent, $country )	{

			if( !$value || $value == 0 )
				return;

			$sql = "INSERT INTO " . $this->mainTable . " " .
				"( short, name, unit, value, date, change_percent, country, updated ) VALUES " .
				"('" . strtolower( $short ) . "', '" . $name . "', '" . $unit . "', '" . number_format( (float)$value, 4, ".", "" ) . "', '" . $date . "', '" . number_format( (float)$change_percent, 4, ".", "" ) . "', '" . $country . "', NOW( ) )";

			if( $this->database->query( $sql ) )	{

				// History
				$sql = "REPLACE INTO " . $this->historyTable . " " .
					"( short, name, unit, value, date, change_percent, country, shaped_date, updated ) VALUES " .
					"('" . strtolower( $short ) . "', '" . $name . "', '" . $unit . "', '" . number_format( (float)$value, 4, ".", "" ) . "', '" . $date . "', '" . number_format( (float)$change_percent, 4, ".", "" ) . "', '" . $country . "', '" . $this->shapeDate( $date ) . "', NOW( ) )";
				$this->database->query( $sql );

				$this->updateChangePercent ( $short, $unit );
			}
		}

	  // Actualizar el ?ltimo porcentaje de cambio

	  // EJ: updateChangePercent( 'TRM', 'COP' );
		function updateChangePercent( $short, $unit )	{
			// History
			$sql = "SELECT id, value, change_percent FROM " . $this->historyTable . " WHERE short = '" . $short . "' AND unit = '" . $unit . "' ORDER BY shaped_date DESC LIMIT 0, 2 ";
			$this->database->query( $sql );
			$row = $this->database->fetchArray( );
			$row2 = $this->database->fetchArray( );

			if( $row && $row['id'] && !$row['change_percent'] && $row2 && $row2['id'] )	{
				$dif = $row['value'] - $row2['value'];
				$percent = ( $row['value'] != 0 ? number_format( $dif * 100 / $row['value'], 4, ".", "" ) : '0.00' );

				$sql = "UPDATE " . $this->historyTable . " SET change_percent = " . $percent . " WHERE id = " . $row['id'] . " ";
				$this->database->query( $sql );

				$sql = "UPDATE " . $this->mainTable . " SET change_percent = " . $percent . " WHERE short = '" . $short . "' AND unit = '" . $unit . "'";
				$this->database->query( $sql );
			}
		}

	  // Actualizar los ultimos registros
	  // @values = arreglo indexado con los nombres de los campos a actualizar y sus respectivos valores

	  // EJ: updateLastData( 'TRM', 'COP', array( "change_percent" => '0.8' ) );
		function updateLastData( $short, $unit, $values )	{

			$sql = "UPDATE " . $this->mainTable . " SET ";
			foreach( $values as $key => $value )
				$sql .= $key . " = '" . $value . "', ";

			$sql .= " updated = NOW( ) WHERE short = '" . $short . "' AND unit = '" . $unit . "'";
			$this->database->query( $sql );

			// History
			$sql = "SELECT id FROM " . $this->historyTable . " WHERE short = '" . $short . "' AND unit = '" . $unit . "' ORDER BY shaped_date DESC LIMIT 0, 1 ";
			$this->database->query( $sql );
			$row = $this->database->fetchArray( );

			if( $row && $row['id'] )	{
				$sql = "UPDATE " . $this->historyTable . " SET ";
				foreach( $values as $key => $value )
					$sql .= $key . " = '" . $value . "', ";

				$sql .= " updated = NOW( ) WHERE id = " . $row['id'] . " ";
				$this->database->query( $sql );
			}
			$this->updateChangePercent ( $short, $unit );
		}

		// Funci?n espec?fica para el parseo de datos
		// @needle = Texto a buscar
		// @select = Llave de divisi?n para el HtmlParser
		// @keepParsed = Boolean = Evitar llamar al reset del HtmlParser al terminar cada ciclo

		// EJ: getParsedSelection( "/>Oro<"[, "</div>"][, true] );
		function getParsedSelection( $needle, $select = "</tr>", $keepParsed = false )	{
			$result = array( );

			if( !$this->htmlParser || $this->htmlParser->isEmpty( ) )	{
				if( $this->showErrors )
					echo "\nPage Empty";
				return NULL;
			}

			if( ( $err = $this->htmlParser->select( $needle, $select ) ) == 0 )	{
				$this->htmlParser->stripTags( );
				$result = array_values( $this->htmlParser->selection ); // Una copia del arreglo
			}
			else
				echo "\nPage Error: [" . $err . "] en [" . $needle . "] || [" . $select . "] \n";

			if( $this->debug > 2 )
				print_r( $this->htmlParser->page );

			if( $this->htmlParser && !$keepParsed )
				$this->htmlParser->reset( );

			return $result;
		}

		// Funci?n general para el parseo de datos
		// @select = Llave de divisi?n para el HtmlParser
		// @eval = array( campo => codigo_a_evaluar )
		// @keepParsed = Boolean = Evitar llamar al reset del HtmlParser al terminar cada ciclo

		// EJ: eval
		// El valor encontrado debe ser modificado para ser el entero encontrado m?s 10
		// array( 'valor' => 'int($data[1]) + 10' )

		// EJ: parseData( ["</tr>"][, array( 'valor' => 'int($pag->getElement(1)) + 10' )][, true] );
		function parseData( $select = "</tr>", $pEval = array( ), $keepParsed = false )	{
			$result = array( );

			foreach( $this->data as $short => $item )	{
				$eval = $pEval;
				$result = array( );

				if( $item["eval"] )
					$eval = $item["eval"];

				if( $item["select"] )
					$select = $item["select"];

				$data = $this->getParsedSelection( $item["parser"], $select, true );
				if( $this->debug > 1 )
					echo "Parsed: [" . print_r( $data, true ) . "]";

				if( $data )	{
					$mainData = array(
									"short" => $item["short"],
									"name" => $item["name"],
									"unit" => $item["unit"],
									"value" => preg_replace( "/[^0-9\\.]/", "", ( isset( $data[ 1 ] ) ? $data[ 1 ] : '' ) ),
									"date" => ( isset( $data[ 2 ] ) ? $data[ 2 ] : '' ),
									"change_percent" => "",
									"country" => $item["country"]
								);

					foreach( $eval as $key => $action )
						eval( '$mainData[$key] = ' . $action . ";" );

					if( $this->debug > 1 )
						echo "Data: [" . print_r( $mainData, true ) . "]";

					$result[ $mainData['short'] ] = array( $mainData['unit'] => $mainData );

					$this->saveData( $mainData['short'], $mainData['name'], $mainData['unit'], $mainData['value'], $mainData['date'], $mainData['change_percent'], $mainData['country'] );
				}

				if( $this->htmlParser && !$keepParsed )
					$this->htmlParser->reset( );
			}
			return $result;
		}
	}

?>
