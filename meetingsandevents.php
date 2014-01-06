<?php //session_start(); ?>

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <?php require_once("./templates/scripts.php"); ?>
    <script src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/1.6.4/fullcalendar.min.js"></script>
    <script src="./scripts/jquery-ui-timepicker-addon.js"></script>
    <script src="scripts/jquery-impromptu.min.js"></script>
    <script src="scripts/html2canvas.js"></script>
    <?php require_once("./templates/css.php"); ?>
    <link href="./css/fullcalendar.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="./css/jquery-ui-timepicker-addon.css">
    <link rel="stylesheet" type="text/css" href="css/jquery-impromptu.css">
    <title>Meetings & Events</title>
    <style type="text/css">
	</style>
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
            <button title="WYSIWYG - Maximize browser for best results..." class="btnprint">Save As PDF</button>
        </div>
        <div id="Event" style="display: none;">
          <form id="frmEvent" > 
            <input type="hidden" id="id" name="id" value="">
            <input type="hidden" id="parent_id" name="parent_id" value="">
            
            <label for="description">Event Title</label>
            <input type="text" id="description" name="description" required><br />
            
            <label for="url">URL</label>
            <input type="url" id="url" name="url" ><br />
            
            <label for="start">Start date</label>
            <input type="datetime" id="start" name="start" required><br />
            
            <label for="end">End date</label>
            <input type="datetime" id="end" name="end" required><br />
            
            <div id="repeatingoptions">
              <label for="repeats">Recurring</label>
              <input type="checkbox" id="repeats" name="repeats" >
              <div id="repeat-options" >
                   Repeat every: <input type="radio" value="1" name="repeat-freq" id="rad1days" align="bottom">day 
                   <input type="radio" checked value="7" name="repeat-freq" id="rad7days"  align="bottom">week 
                   <input type="radio" value="14" name="repeat-freq" id="rad14days" align="bottom">two weeks
              </div>
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
    <script type="text/javascript">
		$('.btnprint').on('click', function () { makecalendarpdf(); });
	</script>
  </body>
</html>