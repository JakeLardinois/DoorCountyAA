<?php 
/*$redirect_url = isset($_SERVER["HTTP_REFERER"]) ?
  $_SERVER["HTTP_REFERER"] : "../meetingsandevents.php";
//
// avoid an infinite loop: redirect to the meetingsandevents page:
//
if(empty($redirect_url) || preg_match("/\/login/", $redirect_url))
{
  $redirect_url = "../meetingsandevents.php";
}
header("Location: " . $redirect_url);
exit;*/
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <?php
		$redirect_url = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "../meetingsandevents.php";
		echo "<meta http-equiv=\"refresh\" content=\"0;".$redirect_url."\">";
	?>
    <title>Change password</title>
    <script type="application/javascript">
          alert('Not Yet Implemented');
    </script>
  </head>
  <body>
    <div id="wrapper">
        <div id="maincontent">
            Redirecting...
        </div>
    </div>
  </body>
</html>
