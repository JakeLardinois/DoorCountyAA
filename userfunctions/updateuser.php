<?php 
require_once '../infrastructure/dbconfig.php';

$id = $_POST['id'];
$value = $_POST['value'];
$rowId = $_POST['rowId'];
$columnPosition = $_POST['columnPosition'];
$columnId = $_POST['columnId'];
$columnName = str_replace(' ', '',$_POST['columnName']);

// connection to the database
try {
	$bdd = new PDO('mysql:host='.mysql_hostname.';dbname='.mysql_dbname, mysql_username, mysql_password);
} catch(Exception $e) {
	exit('Unable to connect to database.');
}
// update the records
$sql = "UPDATE users SET ".$columnName."=? WHERE userid=".$id;
$q = $bdd->prepare($sql);
$q->execute(array($value));

//echo json_encode(array('Success' => true));//sends json response back to ajax telling it it was successful...
echo $value;
?>