<?php
require_once 'config/config.php';

/* Values received via ajax */
$id = $_POST['id'];

// connection to the database
try {
 $bdd = new PDO('mysql:host='.mysql_hostname.';dbname='.mysql_dbname, mysql_username, mysql_password);
 } catch(Exception $e) { exit('Unable to connect to database.'); }
 
 // update the records
$sql = "DELETE from events WHERE id=".$id;
$q = $bdd->prepare($sql);
$q->execute();
?>