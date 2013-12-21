<?php
// List of events
 $json = array();

 // Query that retrieves events
 //explicitly call out my fields-Notice how I get text 'true' 'false' from bool 1 or 0
 $requete = "SELECT id, title, start, end, url, IF(allday,'true','false') FROM evenement ORDER BY id";

 // connection to the database
 try {
 $bdd = new PDO('mysql:host=localhost;dbname=fullcalendar', 'aanon_dbadmin', '***REMOVED***');
 } catch(Exception $e) {
  exit('Unable to connect to database.');
 }
 // Execute the query
 $resultat = $bdd->query($requete) or die(print_r($bdd->errorInfo()));

 // sending the encoded result to success page
 echo json_encode($resultat->fetchAll(PDO::FETCH_ASSOC));

?>