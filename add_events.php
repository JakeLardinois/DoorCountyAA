<?php
// Values received via ajax
$title = $_POST['title'];
$start = $_POST['start'];
$end = $_POST['end'];
$url = $_POST['url'];
$recurring = $_POST['recurring'];
// connection to the database
try {
$bdd = new PDO('mysql:host=localhost;dbname=fullcalendar', 'aanon_dbadmin', '***REMOVED***');
} catch(Exception $e) {
exit('Unable to connect to database.');
}

// insert the records
//$sql = "INSERT INTO evenement (title, start, end, url, allday) VALUES (:title, :start, :end, :url, :allday)";
$sql = "INSERT INTO evenement (title, start, end, url) VALUES (:title, :start, :end, :url)";
$q = $bdd->prepare($sql);
//$q->execute(array(':title'=>$title, ':start'=>$start, ':end'=>$end,  ':url'=>$url, ':allday'=>($recurring == "true" ? 1 : 0)));
$q->execute(array(':title'=>$title, ':start'=>$start, ':end'=>$end,  ':url'=>$url ));
echo json_encode(array('Success' => true));//sends json response back to ajax telling it it was successful...
?>