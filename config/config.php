<?php
//This has to do with session state and creates a session or resumes the current one based on a session identifier passed via a GET or POST request, or passed via a cookie.
//This session data is then accessed both client and server side...
session_start();

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
define("mysql_password", "***REMOVED***");				// db password
define("mysql_hostname", "localhost");*/		// db server

?>
