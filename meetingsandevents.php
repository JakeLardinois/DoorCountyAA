<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <?php require_once("./templates/scripts.php"); ?>
    <script src="./scripts/fullcalendar.min.js"></script>
    
    <script src="./scripts/jquery-ui-timepicker-addon.js"></script>
    <script src="scripts/jquery-impromptu.min.js"></script>
    <?php require_once("./templates/css.php"); ?>
    <link rel="stylesheet" type="text/css" href="./css/fullcalendar.css">
    <link rel="stylesheet" type="text/css" href="./css/jquery-ui-timepicker-addon.css">
    <link rel="stylesheet" type="text/css" href="css/jquery-impromptu.css">
    <title>Meetings & Events</title>
    <script type="application/javascript">
	
	</script>
  </head>
  <body>
	<div id="wrapper">
        <?php require_once("./templates/header.php"); ?>
		<br>
        <br>
        <div id="maincontent">
        	<div id='calendar'></div>
        </div>
        <div id="Event" style="display: none;">
          <form id="frmEvent" > 
            <input type="hidden" id="eventID">
            
            <label for="description">Event Title</label>
            <input type="text" id="description" name="description" required><br />
            
            <label for="url">URL</label>
            <input type="url" id="url" name="url" ><br />
            
            <label for="start">Start date</label>
            <input type="datetime" id="start" name="start" required><br />
            
            <label for="end">End date</label>
            <input type="datetime" id="end" name="end" required><br />
            
            <label for="repeats">Recurring</label>
            <input type="checkbox" id="repeats" name="repeats" >
            <div id="repeat-options" >
                 Repeat every: day <input type="radio" value="1" name="repeat-freq" align="bottom">
                 week <input type="radio" value="7" name="repeat-freq" align="bottom">
                 two weeks <input type="radio" value="14" name="repeat-freq" align="bottom">
            </div>
          </form>
        </div>
        <?php require_once("./templates/footer.php"); ?>
    </div>
    <?php 
		/*load buildreadonlycalendar.js or buildeditablecalendar.js based on if the user is authenticated.  Note that this script must be loaded 
		at the end of the page since the authenticated user is loaded in the footer and the calendar must be built before the datetime picker is rendered.*/
		if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['Username'])){ echo "<script src=\"./scripts/buildeditablecalendar.js\"></script>"; }
		else { echo "<script src=\"./scripts/buildreadonlycalendar.js\"></script>";}
	?>
  </body>
</html>