<?php 
  require_once '../infrastructure/dbconfig.php';
  
  //prevents unauthorized access
  if(empty($_SESSION['LoggedIn']) && empty($_SESSION['Username'])){exit();}
  
  $oldpassword = md5($_POST['oldpassword']);//passwords are always stored as MD5 hashes on the db
  $newpassword = md5($_POST['newpassword']);
  $userid = $_SESSION['userid'];
  
  
  // connection to the database
  try {
	  $bdd = new PDO('mysql:host='.mysql_hostname.';dbname='.mysql_dbname, mysql_username, mysql_password);
  } catch(Exception $e) {
	  exit('Unable to connect to database.');
  }
  
  $checkoldpw = $bdd->query("SELECT * FROM users WHERE userid = ".$userid);
  $row = $checkoldpw->fetch();
  
  if ($row['password'] == $oldpassword){ //the old password matches the one in the db...
	  // update the records
	  $sql = "UPDATE users SET password =? WHERE userid=".$userid;
	  $q = $bdd->prepare($sql);
	  $q->execute(array($newpassword));
	  ?>
      <!doctype html>
      <html>
        <head>
          <meta charset="utf-8">
          <?php
              $redirect_url = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "../meetingsandevents.php";
              
              echo "<meta http-equiv=\"refresh\" content=\"0;URL=".$redirect_url."\">";
          ?>
          <title>Change password</title>
          <script type="application/javascript">
                alert('Your Password has been updated.');
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
	  <?php
  }
  else {
	  ?>
      <!doctype html>
      <html>
        <head>
          <meta charset="utf-8">
          <?php
              $redirect_url = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "../meetingsandevents.php";
              
              echo "<meta http-equiv=\"refresh\" content=\"0;URL=".$redirect_url."\">";
          ?>
          <title>Change password</title>
          <script type="application/javascript">
                alert('The Password update failed.\r\n Your old password didn\'t match.');
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
      <?php
  }
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

