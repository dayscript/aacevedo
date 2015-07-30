<?php
/**
 * Parse an html file
 *
 * @version   2.0
 * @author   Bender
 */

class HtmlParser
{
	var $page; //site array
	var $selection; //array with segment selected

	function htmlParser( $url, $sepTags = array( ) )	{
		$sepTags = array_merge( array( '<t', '</t' ), $sepTags );

		$this->page = array( );
		$tmp = "";
		if( $this->page = @file( $url ) )	{
			foreach( $this->page as $linea )	{
				for ( $c = 0, $max = count( $sepTags ) - ( count( $sepTags ) % 2 ) ; $c < $max; $c += 2 )
					$linea = trim( @eregi_replace( $sepTags[$c], "<separador>" . $sepTags[$c], @eregi_replace( $sepTags[$c + 1], "<separador>" . $sepTags[$c + 1], $linea))) ." ";

				$tmp .= $linea;
			}
			$this->page = explode("<separador>", $tmp );
		}
		else
			$this->page = array( );
	}

   /**
    *
    */
	function isEmpty()
	{
		return empty( $this->page );
	}


   /**
    * Returns the length of the selection array
    *
    * @return   int    the lenght
    */
	function getLength( )
	{
		return count( $this->selection );
	}

   /**
    * Returns the value of the indexed component in the selected array
    *
    * @param   index    the index
    */
	function getElement( $index )
	{
		return ( isset( $this->selection[ $index ] ) ? $this->selection[ $index ] : NULL );
	}

   /**
    * Prints Page array
    */
	function printPage()
	{
		print_r( $this->page );
	}

   /**
    * Prints Selection array
    */
	function printSelection()
	{
		print_r( $this->selection );
	}

   /**
    * Resets "page" array
    */
	function reset()
	{
		reset( $this->page );
	}

   /**
    * Selects a part of the html file and store in this->select
    *
    * @param   key1      First key to search in html file
    * @param   key2      First key to search in html file,  if not exist goes to EOF
    *
    * @return  0         If correct
    * @return  -1        key1 Not finded
    * @return  -2        key2 Not finded
    */
	function select( $key1, $key2=false, $nextline = false )
	{
		$this->selection = array();

		while( current( $this->page ) )
		{
			//echo "\n\n" . $key1 . "=>" . current( $this->page ) . "\n";
			if( @ereg( $key1, current( $this->page ) ) ) break;
				next( $this->page );
		}
		if( $nextline ) next( $this->page );

		if( !current( $this->page ) ) return -1;

		while( current( $this->page ) && $key2 )
		{
			$this->selection[] = @ereg_replace ( "&nbsp;", "", current( $this->page ) );
			if( @ereg( $key2, current( $this->page ) ) ) break;
				next( $this->page );
		}
		if( !current( $this->page ) && $key2 ) return -2;

		return 0;
	}

   /**
    * delete html tags
    */
	function stripTags ($tags = false)
	{
		$tmpSeleccion = array();
		foreach($this->selection as $select)
		{
			// Algunos caracteres no son eliminados por el trim normal.
			$i = trim( trim( strip_tags ( html_entity_decode( $select ), $tags ) ), "\x7f..\xff\x0..\x1f" );
			if( $i )
				$tmpSeleccion[] = $i;
		}
		$this->selection = $tmpSeleccion;
	}
}
?>
