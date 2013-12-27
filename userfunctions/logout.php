<?php 
//require_once '../config/config.php'; //I had done this initially to bring in session_start()
/*Notice how session_start() in this scenario gets ahold of the existing session and then destroys it...  session_start() can be used to start a session state
	or as in this case where it gets ahold of the existing session in order to destroy it.*/
session_start();
$_SESSION = array(); 
session_destroy(); 
?>
<meta http-equiv="refresh" content="0;../meetingsandevents.php"> <!--redirects to the calendar page-->
