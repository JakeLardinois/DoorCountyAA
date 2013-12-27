<?php
	require_once '../config/config.php';

	//This prevents an unauthenticated user from utilizing the buildeditablecalendar.js file to update events; it is a failsafe measure since the first line against unauthorized
	//access is loading buildreadonlycalendar.js for unauthenticated users.
	if(empty($_SESSION['LoggedIn']) && empty($_SESSION['Username'])){exit();}

	/* Values received via ajax */
	$id = $_POST['id'];
	$title = $_POST['title'];
	$start = $_POST['start'];
	$end = $_POST['end'];

	// connection to the database
	try {
		$bdd = new PDO('mysql:host='.mysql_hostname.';dbname='.mysql_dbname, mysql_username, mysql_password);
	} catch(Exception $e) {
		exit('Unable to connect to database.');
	}
	// update the records
	$sql = "UPDATE events SET title=?, start=?, end=? WHERE id=?";
	$q = $bdd->prepare($sql);
	$q->execute(array($title,$start,$end,$id));
	echo json_encode(array('Success' => true));//sends json response back to ajax telling it it was successful...
?>