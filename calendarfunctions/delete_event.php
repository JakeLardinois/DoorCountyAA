<?php
	require_once '../infrastructure/dbconfig.php';

	//This prevents an unauthenticated user from utilizing the buildeditablecalendar.js file to delete events; it is a failsafe measure since the first line against unauthorized
	//access is loading buildreadonlycalendar.js for unauthenticated users.
	if(empty($_SESSION['LoggedIn']) && empty($_SESSION['Username'])){exit();}
	/* Values received via ajax */
	$id = $_POST['id'];
	$parent_id = $_POST['parent_id'];

	// connection to the database
	try {
		$bdd = new PDO('mysql:host='.mysql_hostname.';dbname='.mysql_dbname, mysql_username, mysql_password);
	} catch(Exception $e) { exit('Unable to connect to database.'); }
 
	// get all the records from the events table so I can get a count of them to see if I have to delete the associated Parent row
	$sql = "SELECT * from events WHERE parent_id=".$parent_id;
	$q = $bdd->prepare($sql);
	$q->execute();

	if ($q->rowCount() == 1) {	//if your going to delete the last record from the events table
		$row = $q->fetch();		//get a row from the resultset
		//$sql = "DELETE from events_parent WHERE parent_id=".$row['parent_id']; //delete the associated parent
		$sql = "DELETE from events_parent WHERE parent_id=".$parent_id; //delete the associated parent
		$q = $bdd->prepare($sql);
		$q->execute();
	}

	// delete the records
	$sql = "DELETE from events WHERE id=".$id;
	$q = $bdd->prepare($sql);
	$q->execute();
?>