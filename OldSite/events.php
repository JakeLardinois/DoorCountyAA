<?php
require("config.php");
require("./lang/lang." . LANGUAGE_CODE . ".php");
require("functions.php");

# testing whether var set necessary to suppress notices when E_NOTICES on
$month = 
	(isset($_GET['month'])) ? (int) $_GET['month'] : null;
$year =
	(isset($_GET['year'])) ? (int) $_GET['year'] : null;

# set month and year to present if month 
# and year not received from query string
$m = (!$month) ? date("n") : $month;
$y = (!$year)  ? date("Y") : $year;

$scrollarrows = scrollArrows($m, $y);
$auth 		  = auth();

require("./templates/" . TEMPLATE_NAME . ".php");
?>
