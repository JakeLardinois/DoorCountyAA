<?php
// Values received via ajax
$title = $_POST['title'];
$start = $_POST['start'];
$end = $_POST['end'];
$url = $_POST['url'];
$allday = $_POST['allday'];
// connection to the database
try {
$bdd = new PDO('mysql:host=localhost;dbname=fullcalendar', 'aanon_dbadmin', '***REMOVED***');
} catch(Exception $e) {
exit('Unable to connect to database.');
}

// insert the records
$sql = "INSERT INTO evenement (title, start, end, url, allday) VALUES (:title, :start, :end, :url, :allday)";
$q = $bdd->prepare($sql);
$q->execute(array(':title'=>$title, ':start'=>$start, ':end'=>$end,  ':url'=>$url, ':allday'=>($allday == "true" ? 1 : 0)));
echo json_encode(array('Success' => true));//sends json response back to ajax telling it it was successful...
?>