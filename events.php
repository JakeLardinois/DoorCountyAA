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
?>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <?php require("./templates/scripts.php"); ?>
    <?php javaScript() ?>
    <?php require("./templates/css.php"); ?>
    <link rel="stylesheet" type="text/css" href="css/eventcalendar.css">
    <title>Meetings & Events</title>
  </head>
  <body>
	<div id="wrapper">
    	<?php require("./templates/header.php"); ?>
        <div id="maincontent">
		  <?php echo $scrollarrows ?>
          <span class="date_header">
          	&nbsp;
			<?php echo $lang['months'][$m-1] ?>
            &nbsp;
			<?php echo $y ?>
          </span>
          <form name="monthYear">
          	<?php monthPullDown($m, $lang['months']); yearPullDown($y); ?>
            <input type="button" value="GO" onClick="submitMonthYear()"/>
          </form>
          <?php echo writeCalendar($m, $y); ?>
          <?php echo footprint($auth, $m, $y) ?>
        </div>
        <?php require("./templates/footer.php"); ?>
    </div>
  </body>
</html>