<?php
	require_once '../infrastructure/dbconfig.php';

	//This prevents an unauthenticated user from deleting a user
	if(empty($_SESSION['LoggedIn']) && empty($_SESSION['Username'])){exit();}
	if($_SESSION['auth'] < 2){exit();} //you must have access greater than 2 to delete a user
	/* Values received via ajax */
	$id = $_POST['id'];

	// connection to the database
	try {
		$bdd = new PDO('mysql:host='.mysql_hostname.';dbname='.mysql_dbname, mysql_username, mysql_password);
	} catch(Exception $e) { exit('Unable to connect to database.'); }

	// delete the records
	$sql = "DELETE from users WHERE userid=".$id;
	$q = $bdd->prepare($sql);
	$q->execute();
?>