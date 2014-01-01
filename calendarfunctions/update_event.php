<?php
	require_once '../infrastructure/dbconfig.php';

	//This prevents an unauthenticated user from utilizing the buildeditablecalendar.js file to update events; it is a failsafe measure since the first line against unauthorized
	//access is loading buildreadonlycalendar.js for unauthenticated users.
	if(empty($_SESSION['LoggedIn']) && empty($_SESSION['Username'])){exit();}

	$userid = $_SESSION['userid']; //used to tag created records
	/* Values received via ajax */
	$id = $_POST['id'];
	//$title = $_POST['title'];
	$title = $_POST['description'];
	$start = new DateTime($_POST['start']);
	$end = new DateTime($_POST['end']);

	// connection to the database
	try {
		$bdd = new PDO('mysql:host='.mysql_hostname.';dbname='.mysql_dbname, mysql_username, mysql_password);
	} catch(Exception $e) {
		exit('Unable to connect to database.');
	}
	
	//do some check here to make sure that a valid email address is entered if that is the column being updated..
	
	
	// update the records
	$sql = "UPDATE events SET title=?, start=?, end=?, updatedby=? WHERE id=?";
	$q = $bdd->prepare($sql);
	$q->execute(array($title,$start->format('Y/m/d H:i:s'),$end->format('Y/m/d H:i:s'),$userid,$id));
	echo json_encode(array('Success' => true));//sends json response back to ajax telling it it was successful...
?>