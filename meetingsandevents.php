<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <?php require("./templates/scripts.php"); ?>
    <script src="scripts/fullcalendar.js"></script>
    <script src="scripts/buildcalendar.js"></script>
    <?php require("./templates/css.php"); ?>
    <link rel="stylesheet" type="text/css" href="css/fullcalendar.css">
    <link rel="stylesheet" type="text/css" href="css/jquery-ui.css">
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
        <div id="popupEventForm" style="display: none;">
            <form id="EventForm" action="http://localhost:8080/DoorCountyAA/add_events.php" method="post" >
                <input type="hidden" id="eventID">
                <label>Event title</label>
                <input type="text" id="title" name="title" ><br />
                <label>URL</label>
                <input type="text" id="url" name="url" ><br />
                <label>Start date</label>
                <input type="text" id="start" name="start" ><br />
                <label>End date</label>
                <input type="text" id="end" name="end" ><br />
                <label>Appointment length (minutes)</label>
                <input type="text" id="eventDuration" placeholder="15"><br />
                <input type="submit" value="POST Save Event">
            </form>
            <button type="input" id="btnPopupSave" data-dismiss="modal" class="btn btn-primary">Save event</button>
            <button type="button" id="btnPopupCancel" data-dismiss="modal" >Cancel</button>
        </div> 
        <?php require("./templates/footer.php"); ?>
    </div>
    <script type="text/javascript">
	</script>
  </body>
</html>