<?
include("../config.php");
include("../lang/lang.admin." . LANGUAGE_CODE . ".php");

mysql_connect(DB_HOST, DB_USER, DB_PASS) or die(mysql_error());
mysql_select_db(DB_NAME) or die(mysql_error());

$sql = "SELECT id, start_time, end_time FROM " . DB_TABLE_PREFIX . "mssgs";
$result = mysql_query($sql) or die(mysql_error());

$count = 0;

while($row = mysql_fetch_array($result)) {
	
	if ( $row[1] == "00:00:00" && $row[2] == "00:00:00" ) {
		
		$sql = "UPDATE " . DB_TABLE_PREFIX . "mssgs SET start_time='55:55:55', end_time='55:55:55' ";
		$sql .= "WHERE id=" . $row[0];
		
		mysql_query($sql) or die(mysql_error());
		
		$count++;
	}
}

echo "$count records adjusted.  Database adjustment successful";
?>
