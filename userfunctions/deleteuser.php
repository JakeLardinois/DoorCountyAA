<?php
	require_once '../infrastructure/dbconfig.php';

	//This prevents an unauthenticated user from utilizing the buildeditablecalendar.js file to delete events; it is a failsafe measure since the first line against unauthorized
	//access is loading buildreadonlycalendar.js for unauthenticated users.
	if(empty($_SESSION['LoggedIn']) && empty($_SESSION['Username'])){exit();}
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