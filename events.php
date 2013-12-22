<?php
	require_once 'config/config.php';
	
	try { //I do a try catch in case there are problems with connecting to my db...
	  $dbh = new PDO('mysql:host='.mysql_hostname.';dbname='.mysql_dbname, mysql_username, mysql_password);
	  $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	} 
	catch(Exception $e) { exit('Unable to connect to database.'); }
	
	//the below gets all the events from the events table; explicitly call out my fields-Notice how I get text 'true' 'false' from bool 1 or 0 in allday field
    $stmt = $dbh->prepare("SELECT id, parent_id, title, start, end, url, IF(allday,'true','false') AS allday
                           FROM events");
    $stmt->execute();
    $events = array();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){	//iterate through the rows and put the values in an array
        $eventArray['id'] = $row['id'];
        $eventArray['parent_id'] = $row['parent_id'];
        $eventArray['title'] = stripslashes($row['title']);
        $eventArray['start'] = $row['start'];
        $eventArray['end'] = $row['end'];
		$eventArray['url'] = $row['url'];
		$eventArray['allday'] = $row['allday'];
        $events[] = $eventArray;
    }

    echo json_encode($events); //json encode the array and send them to FullCalendar
	
	
// //List of events (old Query)
// $json = array();
//
// // Query that retrieves events
// //explicitly call out my fields-Notice how I get text 'true' 'false' from bool 1 or 0
// $requete = "SELECT id, title, start, end, url, IF(allday,'true','false') FROM evenement ORDER BY id";
//
// // connection to the database
// try {
// $bdd = new PDO('mysql:host=localhost;dbname=fullcalendar', 'aanon_dbadmin', '***REMOVED***');
// } catch(Exception $e) {
//  exit('Unable to connect to database.');
// }
// // Execute the query
// $resultat = $bdd->query($requete) or die(print_r($bdd->errorInfo()));
//
// // sending the encoded result to success page
// echo json_encode($resultat->fetchAll(PDO::FETCH_ASSOC));


?>