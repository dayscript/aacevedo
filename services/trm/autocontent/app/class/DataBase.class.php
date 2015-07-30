<?php
Class DataBase
{
	var $link;
	var $db;
	var $result;
	var $debug;
	var $SHOWERRORS;

	function DataBase( $host, $usr, $pass, $db, $debug = false )
	{
		$this->link = mysql_connect( $host, $usr, $pass );
		mysql_select_db( $db, $this->link );

		$this->db = $db;
		$this->debug = $debug;
		$this->SHOWERRORS = false;
	}

	function query ( $sql, $showquery=false )
	{
		if( !($this->result = mysql_query( $sql, $this->link ) ) )
		{
			if( $this->debug || $this->SHOWERRORS )
			{
				echo date("[Y-m-d H:i:s] ") . $sql . "\n";
				echo $this->error() . "\n";
			}
			return false;
		}
		else
		{
			if( $this->debug || $showquery )
			{
				echo date("[Y-m-d h:i] ") . $sql . "\n";
			}
			return true;
		}
	}

	function fetchArray( )
	{
		return mysql_fetch_array($this->result);
	}

	function insertId()
	{
		return mysql_insert_id();
	}

	function affectedRows()
	{
		return mysql_affected_rows();
	}

	function numRows()
	{
		return mysql_num_rows( $this->result );
	}

	function error()
	{
		return mysql_error($this->link);
	}

	function errno()
	{
		return mysql_errno($this->link);
	}

	function close()
	{
		return mysql_close($this->link);
	}
}
?>
