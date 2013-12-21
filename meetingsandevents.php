<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <?php require("./templates/scripts.php"); ?>
    <script src="./scripts/fullcalendar.min.js"></script>
    <script src="./scripts/buildeditablecalendar.js"></script>
    <script src="./scripts/jquery-ui-timepicker-addon.js"></script>
    <?php require("./templates/css.php"); ?>
    <link rel="stylesheet" type="text/css" href="./css/fullcalendar.css">
    <link rel="stylesheet" type="text/css" href="./css/jquery-ui-timepicker-addon.css">
    <title>Meetings & Events</title>
    <script type="application/javascript">
		
        $(document).ready(function () {
			BuildCalendar();
			WireEvents();
        });
		
	</script>
  </head>
  <body>
	<div id="wrapper">
    	<?php require("./templates/header.php"); ?>
        
        <div id="maincontent">
        	<div id='calendar'></div>
        </div>
        <div id="AddEvent" style="display: none;">
          <form id="frmAddEvent" name="test" > 
            <input type="hidden" id="eventID">
            
            <label>Event title</label>
            <input type="text" id="description" name="description" required><br />
            
            <label>URL</label>
            <input type="url" id="url" name="url" required><br />
            
            <label>Start date</label>
            <input type="datetime" id="start" name="start" required><br />
            
            <label>End date</label>
            <input type="datetime" id="end" name="end" required><br />
            
            <button type="submit" id="btnSave" >Save event</button>
          </form>
        </div> 
        <?php require("./templates/footer.php"); ?>
    </div>
    <script type="text/javascript">
		$('#start').datetimepicker();
		$('#end').datetimepicker();
	</script>
  </body>
</html>