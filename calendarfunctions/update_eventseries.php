<?php
	require_once '../infrastructure/dbconfig.php';
	
	//This entire function is basically the same as add_events.php except that I populate a $parent_id variable and then use it to delete all the events and parent record
	//for the series that is being updated before recreating the entire series.
	
	//This prevents an unauthenticated user from utilizing the buildeditablecalendar.js file to add events; it is a failsafe measure since the first line against unauthorized
	//access is loading buildreadonlycalendar.js for unauthenticated users.
	if(empty($_SESSION['LoggedIn']) && empty($_SESSION['Username'])){exit();} 
	
	$parent_id = $_POST['parent_id'];
	
	$userid = $_SESSION['userid']; //used to tag created records
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
		if ((($_POST['repeats']) == 'on') || (($_POST['repeats']) == 'true')) {$repeats = 1;} //when a checkbox is used, the posted data shows the value 'on', so in this case set it to 1
		else {$repeats = $_POST['repeats'];}	//else set it to whatever the value is that is being passed which needs to be a 0 or 1.
	}

	try {
		$dbh = new PDO('mysql:host='.mysql_hostname.';dbname='.mysql_dbname, mysql_username, mysql_password);
		$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // set the error mode to excptions
		$dbh->beginTransaction();
	} catch(Exception $objException) { exit('Unable to connect to database.'); }
	
	// delete the records from the events table
	$sql = "DELETE from events WHERE parent_id=".$parent_id;
	$q = $dbh->prepare($sql);
	$q->execute();

	// delete the records from the events_parent table
	$sql = "DELETE from events_parent WHERE parent_id=".$parent_id;
	$q = $dbh->prepare($sql);
	$q->execute();
	//do some check here to make sure that a valid email address is entered if that is the column being updated..
		
    if (!$repeats) { //if it is not a repeating/recurring event
        $repeat_freq = 0;  
        try{
            $stmt = $dbh->prepare("INSERT INTO events_parent 
                (title,start_date, start_time, end_time, weekday, repeats, repeat_freq, url, allday, createdby, updatedby)
                VALUES (:title,:start_date, :start_time, :end_time, :weekday, :repeats, :repeat_freq, :url, :allday, :createdby, :updatedby)");

            $stmt->bindParam(':title', $title );
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':start_time', $start_time);
            $stmt->bindParam(':end_time', $end_time);
            $stmt->bindParam(':weekday', $weekday);
            $stmt->bindParam(':repeats', $repeats);
            $stmt->bindParam(':repeat_freq', $repeat_freq);
			$stmt->bindParam(':url', $url);
			$stmt->bindParam(':allday', $allday);
			$stmt->bindParam(':createdby', $userid);
			$stmt->bindParam(':updatedby', $userid);
            $stmt->execute();
            $last_id = $dbh->lastInsertId();

            $stmt = $dbh->prepare("INSERT INTO events 
                (parent_id, title, start, end, url, allday, createdby, updatedby)
                VALUES (:parent_id, :title, :start, :end,  :url, :allday, :createdby, :updatedby)");

			$strTempStart = $start->format('Y/m/d H:i:s');//kept getting an 'Strict standards: Only variables should be passed by reference' error
			$strTempEnd = $end->format('Y/m/d H:i:s');		//when format was placed in the below...
			
            $stmt->bindParam(':title', $title );
            $stmt->bindParam(':start', $strTempStart);
            $stmt->bindParam(':end', $strTempEnd);
            $stmt->bindParam(':parent_id', $last_id);
			$stmt->bindParam(':url', $url);
            $stmt->bindParam(':allday', $allday);
			$stmt->bindParam(':createdby', $userid);
			$stmt->bindParam(':updatedby', $userid);
            $stmt->execute();
            $dbh->commit();

        }
        catch(Exception $objException){
            $dbh->rollback();
        }
    }
    else { //if it is a repeating/recurring event
        $repeat_freq = $_POST['repeat-freq'];
        $until = (365/$repeat_freq);
        if ($repeat_freq == 1){
            $weekday = 0;
        }
        
        try{
            $stmt = $dbh->prepare("INSERT INTO events_parent 
                (title,start_date, start_time, end_time, weekday, repeats, repeat_freq, url, allday, createdby, updatedby)
                VALUES (:title, :start_date, :start_time, :end_time, :weekday, :repeats, :repeat_freq, :url, :allday, :createdby, :updatedby)");

            $stmt->bindParam(':title', $title );
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':start_time', $start_time);
            $stmt->bindParam(':end_time', $end_time);
            $stmt->bindParam(':weekday', $weekday);
            $stmt->bindParam(':repeats', $repeats);
            $stmt->bindParam(':repeat_freq', $repeat_freq);
			$stmt->bindParam(':url', $url);
            $stmt->bindParam(':allday', $allday);
			$stmt->bindParam(':createdby', $userid);
			$stmt->bindParam(':updatedby', $userid);
            $stmt->execute(); //executes the sql
            $last_id = $dbh->lastInsertId(); //gets the id (parent_id) of the newly created record

			$strTempStart = $start->format('Y/m/d H:i:s');//This sets my initial start and end values for the below loop
			$strTempEnd = $end->format('Y/m/d H:i:s');
            for($x = 0; $x <$until; $x++){
				
                $stmt = $dbh->prepare("INSERT INTO events 
                    (title, start, end, parent_id, url, allday, createdby, updatedby)
                    VALUES (:title, :start, :end, :parent_id, :url, :allday, :createdby, :updatedby)");
                $stmt->bindParam(':title', $title );
                $stmt->bindParam(':start', $strTempStart); //adds the datetime to the start and end fields of the event
                $stmt->bindParam(':end', $strTempEnd);
                $stmt->bindParam(':parent_id', $last_id);
				$stmt->bindParam(':url', $url);
				$stmt->bindParam(':allday', $allday);
				$stmt->bindParam(':createdby', $userid);
				$stmt->bindParam(':updatedby', $userid);
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
	

?>
