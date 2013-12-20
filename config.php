<?php
/*******************************************************
* config.php -											
* 	Configuration file for phpEventCalendar v0.2		
* 	author: isaac mcgowan <isaac@ikemcg.com>			
*														
* 	Configuration directives set with php's define()	
* 	function.  Usage: define("CONSTANT-ID", 			
*	"scalar_value")										
* 												
* For questions or comments see:						
* 	http://www.ikemcg.com/pec?rm=custom	
*******************************************************/

/*******************************************************
************* MySQL Database Settings ******************
*******************************************************/

define("DB_NAME", "aanon_dcaaevents");				// db name
define("DB_USER", "aanon_dbadmin");				// db username
define("DB_PASS", "***REMOVED***");				// db password
define("DB_HOST", "localhost");		// db server

// Prefix added to table names.  Do not change after
// initial installation.
define("DB_TABLE_PREFIX", "pec_");

/*******************************************************
**************** Language Option ***********************
*******************************************************/

define("LANGUAGE_CODE", "en");

/*******************************************************
************* Calendar Layout Options ******************
*******************************************************/

// Maximum number of event titles that appear per day 
// on month-view.  Note: doesn't limit number of 
// events a user can post, just what appears on month
// view.
define("MAX_TITLES_DISPLAYED", 5);

// Title character limit.  Adjust this value so event
// titles don't take more space than you want them to
// on calendar month-view.
define("TITLE_CHAR_LIMIT", 37);

// Template name.  e.g. "default" would indicate that
// the "default.php" file is to be used.
define("TEMPLATE_NAME", "default");

// Allows you to specify the weekstart, or the day
// the calendar starts with.  The value must be
// a numeric value from 0-6.  Zero indicates that the
// weekstart is to be Sunday, 1 indicates that it is
// Monday, 2 Tuesday, 3 Wednesday... For most users
// it will be zero or one.
define("WEEK_START", 0);

// Allows you to specify the format in which time
// values are output.  Currently there are two
// formats available: "12hr", which displays
// hours 1-12 with an am/pm, and "24hr" which
// display hours 00-23 with no am/pm.
define("TIME_DISPLAY_FORMAT", "12hr");

// This directive allows you to specify a number 
// of hours by which the current time will be 
// offset.  The current time is used to highlight
// the present day on the month-view calendar, and 
// it is sometimes necessary to adjust the current 
// time, so that the present day does not roll-over 
// too early, or too late, for your intended 
// audience.  Both positive and negative integer 
// values are valid.
define("CURR_TIME_OFFSET", 0);
?>
