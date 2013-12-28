<?php
	require_once '../infrastructure/dbconfig.php';

	//This prevents an unauthenticated user from utilizing the buildeditablecalendar.js file to delete events; it is a failsafe measure since the first line against unauthorized
	//access is loading buildreadonlycalendar.js for unauthenticated users.
	if(empty($_SESSION['LoggedIn']) && empty($_SESSION['Username'])){exit();}
	
	/* Values received via ajax */
	$parent_id = $_POST['parent_id'];

	// connection to the database
	try {
		$bdd = new PDO('mysql:host='.mysql_hostname.';dbname='.mysql_dbname, mysql_username, mysql_password);
	} catch(Exception $e) { exit('Unable to connect to database.'); }
 
	// delete the records from the events table
	$sql = "DELETE from events WHERE parent_id=".$parent_id;
	$q = $bdd->prepare($sql);
	$q->execute();

	// delete the records from the events_parent table
	$sql = "DELETE from events_parent WHERE parent_id=".$parent_id;
	$q = $bdd->prepare($sql);
	$q->execute();
?>