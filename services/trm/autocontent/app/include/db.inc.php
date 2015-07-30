<?
#------------------------------
# Database Functions - Implementation
#------------------------------

function db_query ($sql, $echo=false)
{	global $db, $link;
	if ($echo){
		echo "<b>SQL query : $sql</b><br>";
	}
	return mysql_db_query($db,$sql,$link);
}

function db_numrows($result)
{	return mysql_numrows($result);
}

function db_fetch_array($result)
{	return mysql_fetch_array($result);
}

function db_insert_id()
{	global $db, $link;
return mysql_insert_id($link);
}
?>
