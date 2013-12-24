<?php require_once 'config/config.php'; ?> 


<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <?php require("./templates/scripts.php"); ?>
    <script src="./scripts/fullcalendar.min.js"></script>
    
    <!--<script src="./scripts/buildeditablecalendar.js"></script>--><!--you could load a buildreadablecalendar.js and load that one based on if the user is authenticated...-->
    <?php 
		if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['Username'])){ echo "<script src=\"./scripts/buildeditablecalendar.js\"></script>"; }
		else { echo "<script src=\"./scripts/buildreadonlycalendar.js\"></script>";}
	?>
    
    <script src="./scripts/jquery-ui-timepicker-addon.js"></script>
    <script src="scripts/jquery-impromptu.min.js"></script>
    <?php require("./templates/css.php"); ?>
    <link rel="stylesheet" type="text/css" href="./css/fullcalendar.css">
    <link rel="stylesheet" type="text/css" href="./css/jquery-ui-timepicker-addon.css">
    <link rel="stylesheet" type="text/css" href="css/jquery-impromptu.css">
    <title>Meetings & Events</title>
    <script type="application/javascript">
        $(document).ready(function () {
			BuildCalendar();
        });
		
	</script>
  </head>
  <body>
	<div id="wrapper">
    	<?php require("./templates/header.php"); ?>
        <br>
        <br>
        <?php
		  if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['Username']))
		  {// let the user access the authenticated page
		  ?>
            <pThanks for logging in! You are <b><?=$_SESSION['Username']?></b> and your email address is <b><?=$_SESSION['EmailAddress']?></b>.</p>
            <br>
            <a href="logout.php">click here to logout</a>
          <?php
		  }
		  elseif(!empty($_POST['username']) && !empty($_POST['password']))
		  {	   // let the user login
		  	  
			  mysql_connect(mysql_hostname, mysql_username, mysql_password) or die("MySQL Error: " . mysql_error());
			  mysql_select_db(mysql_dbname) or die("MySQL Error: " . mysql_error()); 
			  
			  //mysql_real_escape_string cleans database input by keeping out the majority of the malicious code someone could put into the form
			  //by stripping unwanted parts of whatever has been put in there.
			  $username = mysql_real_escape_string($_POST['username']);
			  $password = md5(mysql_real_escape_string($_POST['password']));//this takes the password and does an MD5 hash against it 
			  
			  $checklogin = mysql_query("SELECT * FROM users WHERE Username = '".$username."' AND Password = '".$password."'");
			  if(mysql_num_rows($checklogin) == 1)
			  {
				  $row = mysql_fetch_array($checklogin);
				  $email = $row['EmailAddress'];
				  
				  $_SESSION['Username'] = $username;
				  $_SESSION['EmailAddress'] = $email;
				  $_SESSION['LoggedIn'] = 1;
				  
				  //echo "<h1>Success</h1>";
				  //echo "<p>We are now redirecting you to the member area.</p>";
				  echo "<meta http-equiv='refresh' content='=2;meetingsandevents.php' />";
			  }
			  else
			  {
				  echo "<h1>Error</h1>";
				  echo "<p>Sorry, your account could not be found. Please <a href=\"meetingsandevents.php\">click here to try again</a>.</p>";
			  }
		  }
		  else
		  {// display the login form
		  ?>
            <p>Thanks for visiting! Please either login below, or <a href="register.php">click here to register</a>.</p>
            <form method="post" action="meetingsandevents.php" name="loginform" id="loginform">
            <fieldset>
                <label for="username">Username:</label><input type="text" name="username" id="username" /><br />
                <label for="password">Password:</label><input type="password" name="password" id="password" /><br />
                <input type="submit" name="login" id="login" value="Login" />
            </fieldset>
            </form>
		  <?php  
		  }
		  ?>

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
        <?php require("./templates/footer.php"); ?>
    </div>
    <script type="text/javascript">
		$('#start').datetimepicker({
			timeFormat: "HH:mm:ss",
			dateFormat: "yy-m-dd"
			});
		$('#end').datetimepicker({
			timeFormat: "HH:mm:ss",
			dateFormat: "yy-m-dd"
			});
	</script>
  </body>
</html>