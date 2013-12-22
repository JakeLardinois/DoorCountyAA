<?php
    require_once './config/db_config.php';
	
	$objException = null;
    $title = $_POST['description']; 
	$start = new DateTime($_POST['start']);
	$start_date = $start->format("y-m-d");
	$start_time = $start->format('H:i:s'); 	//$end->format('Y/m/d H:i:s') Prints "2011/03/20 07:16:17"
	$end = new DateTime($_POST['end']);
	$end_time = $end->format('H:i:s'); 	//$end->format('Y/m/d H:i:s') Prints "2011/03/20 07:16:17"
	$weekday = date('N', strtotime($start_date));
	$url = $_POST['url']; 
	$allday = !isset($_POST['allday']) ? 0 : $_POST['allday']; // if allday is not set then set it to 0, else set it to it's value otherwise below sql fails...
	//The below populates repeats with appropriate values...
	if (!isset($_POST['repeats'])){$repeats = 0;}//if repeats isn't set then set it to 0 meaning it doesn't repeat
	else {
		if (($_POST['repeats']) == 'on') {$repeats = 1;} //when a checkbox is used, the posted data shows the value 'on', so in this case set it to 1
		else {$repeats = $_POST['repeats'];}	//else set it to whatever the value is that is being passed which needs to be a 0 or 1.
	}

	//place a try catch here for db connection exception
	$dbh = new PDO('mysql:host=localhost;dbname=fullcalendar', mysql_username, mysql_password);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // set the error mode to excptions
	$dbh->beginTransaction();
		
    if (!$repeats) {
        $repeat_freq = 0;  
        try{
            $stmt = $dbh->prepare("INSERT INTO events_parent 
                (title,start_date, start_time, end_time, weekday, repeats, repeat_freq, url, allday)
                VALUES (:title,:start_date, :start_time, :end_time, :weekday, :repeats, :repeat_freq, :url, :allday)");

            $stmt->bindParam(':title', $title );
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':start_time', $start_time);
            $stmt->bindParam(':end_time', $end_time);
            $stmt->bindParam(':weekday', $weekday);
            $stmt->bindParam(':repeats', $repeats);
            $stmt->bindParam(':repeat_freq', $repeat_freq);
			$stmt->bindParam(':url', $url);
			$stmt->bindParam(':allday', $allday);
            $stmt->execute();
            $last_id = $dbh->lastInsertId();

            $stmt = $dbh->prepare("INSERT INTO events 
                (parent_id, title, start, end, url, allday)
                VALUES (:parent_id, :title, :start, :end,  :url, :allday)");

			$strTempStart = $start->format('Y/m/d H:i:s');//kept getting an 'Strict standards: Only variables should be passed by reference' error
			$strTempEnd = $end->format('Y/m/d H:i:s');		//when format was placed in the below...
			
            $stmt->bindParam(':title', $title );
            $stmt->bindParam(':start', $strTempStart);
            $stmt->bindParam(':end', $strTempEnd);
            $stmt->bindParam(':parent_id', $last_id);
			$stmt->bindParam(':url', $url);
            $stmt->bindParam(':allday', $allday);
			
            $stmt->execute();
            $dbh->commit();

        }
        catch(Exception $objException){
            $dbh->rollback();
        }
    }
    else {
        $repeat_freq = $_POST['repeat-freq'];
        $until = (365/$repeat_freq);
        if ($repeat_freq == 1){
            $weekday = 0;
        }
        
        try{
            $stmt = $dbh->prepare("INSERT INTO events_parent 
                (title,start_date, start_time, end_time, weekday, repeats, repeat_freq, url, allday)
                VALUES (:title, :start_date, :start_time, :end_time, :weekday, :repeats, :repeat_freq, :url, :allday)");

            $stmt->bindParam(':title', $title );
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':start_time', $start_time);
            $stmt->bindParam(':end_time', $end_time);
            $stmt->bindParam(':weekday', $weekday);
            $stmt->bindParam(':repeats', $repeats);
            $stmt->bindParam(':repeat_freq', $repeat_freq);
			$stmt->bindParam(':url', $url);
            $stmt->bindParam(':allday', $allday);
            $stmt->execute(); //executes the sql
            $last_id = $dbh->lastInsertId(); //gets the id (parent_id) of the newly created record

			$strTempStart = $start->format('Y/m/d H:i:s');//This sets my initial start and end values for the below loop
			$strTempEnd = $end->format('Y/m/d H:i:s');
            for($x = 0; $x <$until; $x++){
				
                $stmt = $dbh->prepare("INSERT INTO events 
                    (title, start, end, parent_id, url, allday)
                    VALUES (:title, :start, :end, :parent_id, :url, :allday)");
                $stmt->bindParam(':title', $title );
                $stmt->bindParam(':start', $strTempStart); //adds the datetime to the start and end fields of the event
                $stmt->bindParam(':end', $strTempEnd);
                $stmt->bindParam(':parent_id', $last_id);
				$stmt->bindParam(':url', $url);
				$stmt->bindParam(':allday', $allday);
                $stmt->execute();
				
				//adjusts the dates for the next iteration...
				$start_date = strtotime($strTempStart . '+' . $repeat_freq . 'DAYS'); //uses strtotime to add the recurring interval to the datetime
				$end_date = strtotime($strTempEnd . '+' . $repeat_freq . 'DAYS');
                $strTempStart = date("y-m-d H:i:s", $start_date); //sets the strTempStart datetime to the next date with the interval added...
                $strTempEnd = date("y-m-d H:i:s", $end_date);
            }
            $dbh->commit();
        }
        catch(Exception $objException){
            $dbh->rollback();
        }
    }
	if ($objException != null){
		echo json_encode(array('Success' => false));//sends json response back to ajax telling it it failed...
	}
	else {
		echo json_encode(array('Success' => true));//sends json response back to ajax telling it it was successful...
	}
	



//// Values received via ajax
//$title = $_POST['title'];
//$start = $_POST['start'];
//$end = $_POST['end'];
//$url = $_POST['url'];
//$recurring = $_POST['recurring'];
//// connection to the database
//try {
//$bdd = new PDO('mysql:host=localhost;dbname=fullcalendar', 'aanon_dbadmin', '***REMOVED***');
//} catch(Exception $e) {
//exit('Unable to connect to database.');
//}
//
//// insert the records
////$sql = "INSERT INTO evenement (title, start, end, url, allday) VALUES (:title, :start, :end, :url, :allday)";
//$sql = "INSERT INTO evenement (title, start, end, url) VALUES (:title, :start, :end, :url)";
//$q = $bdd->prepare($sql);
////$q->execute(array(':title'=>$title, ':start'=>$start, ':end'=>$end,  ':url'=>$url, ':allday'=>($recurring == "true" ? 1 : 0)));
//$q->execute(array(':title'=>$title, ':start'=>$start, ':end'=>$end,  ':url'=>$url ));
//echo json_encode(array('Success' => true));//sends json response back to ajax telling it it was successful...
?>

