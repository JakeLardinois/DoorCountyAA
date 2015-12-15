<?php
//This has to do with session state and creates a session or resumes the current one based on a session identifier passed via a GET or POST request, or passed via a cookie.
//This session data is then accessed both client and server side...
session_start(); 

// 20 mins in seconds
//$inactive = 1200;
// inactive in seconds
//$inactive = 10;
//if( !isset($_SESSION['timeout']) )
//$_SESSION['timeout'] = time() + $inactive; 
//
//$session_life = time() - $_SESSION['timeout'];
//
//if($session_life > $inactive)
//{  
//	session_destroy(); 
//	//header("Location:index.php");     
//}
//$_SESSION['timeout']=time();


/*******************************************************
************* MySQL Database Settings ******************
*******************************************************/
//Local Settings
define("mysql_dbname", "fullcalendar");				// db name
define("mysql_username", "aanon_dbadmin");				// db username
define("mysql_password", "***REMOVED***");				// db password
define("mysql_hostname", "localhost");	// db server

//Remote Settings
/*define("mysql_dbname", "aanon_fullcalendar");				// db name
define("mysql_username", "aanon_dbadmin");				// db username
define("mysql_password", "*12Steps4U");				// db password
define("mysql_hostname", "localhost");*/		// db server

?>
