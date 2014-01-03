<?php 
//require_once '../infrastructure/dbconfig.php'; //I had done this initially to bring in session_start()
/*Notice how session_start() in this scenario gets ahold of the existing session and then destroys it...  session_start() can be used to start a session state
	or as in this case where it gets ahold of the existing session in order to destroy it.*/
session_start();
$redirect_url = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "../meetingsandevents.php";
if (preg_match("/\/useradmin/", $redirect_url)){ $redirect_url = "../meetingsandevents.php";};//if your on the useradmin.php page then you should get redirected somewhere else...
	
$_SESSION = array(); 
session_destroy(); 
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <?php
        
        echo "<meta http-equiv=\"refresh\" content=\"0;URL=".$redirect_url."\">";
    ?>
    <title>Logging Out</title>
    <script type="application/javascript">
    </script>
  </head>
  <body>
    <div id="wrapper">
        <div id="maincontent">
            Logging Out...
        </div>
    </div>
  </body>
</html>
