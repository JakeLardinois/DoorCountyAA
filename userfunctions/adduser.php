<?php
	require_once '../infrastructure/dbconfig.php';
	
	////prevents unauthorized access
	if(empty($_SESSION['LoggedIn']) && empty($_SESSION['Username'])){exit();} 
	if($_SESSION['auth'] < 2){exit();} //you must have access greater than 2 to add a user
	
	$username = $_POST['username'];
	$password = md5($_POST['password']);
	$fname = $_POST['fname'];
	$lname = $_POST["lname"];
	$emailaddress = $_POST['emailaddress']; 
	$userlevel = $_POST["userlevel"];
	$last_id = null;
	
	try {
		$dbh = new PDO('mysql:host='.mysql_hostname.';dbname='.mysql_dbname, mysql_username, mysql_password);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // set the error mode to excptions
		$dbh->beginTransaction();
	} catch(Exception $objException) { exit('Unable to connect to database.'); }
	
	try{
		$stmt = $dbh->prepare("INSERT INTO users 
			(username, password, fname, lname, emailaddress, userlevel)
			VALUES (:username, :password, :fname, :lname, :emailaddress, :userlevel)");

		$stmt->bindParam(':username', $username );
		$stmt->bindParam(':password', $password);
		$stmt->bindParam(':fname', $fname);
		$stmt->bindParam(':lname', $lname);
		$stmt->bindParam(':emailaddress', $emailaddress);
		$stmt->bindParam(':userlevel', $userlevel);
		$stmt->execute();
		$last_id = $dbh->lastInsertId();
		$dbh->commit();

	}
	catch(Exception $objException){
		$dbh->rollback();
		exit($objException->getMessage());
	}
	echo "User ".$username." with id = ".$last_id." was created!";
?>