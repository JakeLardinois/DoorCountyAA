<?php 
  require_once '../infrastructure/dbconfig.php';
  
  //prevents unauthorized access
  if(empty($_SESSION['LoggedIn']) && empty($_SESSION['Username'])){exit();}
  if($_SESSION['auth'] < 2){exit();} //you must have access greater than 2 to update a user's properties
  
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
  /* notice that in this implementation I am using the $columnName variable from datatables. Note that this variable is populated with the 
	  <th></th> tags in the html table that is defined for datatables, so this implementation will work as long as those column headers aren't changed*/
  $sql = "UPDATE users SET ".$columnName."=? WHERE userid=".$id;
  $q = $bdd->prepare($sql);
  $q->execute(array($value));
  
  //echo json_encode(array('Success' => true));//sends json response back to ajax telling it it was successful...
  echo $value;
?>