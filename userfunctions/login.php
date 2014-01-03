<?php 
  require_once '../infrastructure/dbconfig.php'; 
  
  mysql_connect(mysql_hostname, mysql_username, mysql_password) or die("MySQL Error: " . mysql_error());
  mysql_select_db(mysql_dbname) or die("MySQL Error: " . mysql_error()); 
  
  //mysql_real_escape_string cleans database input by keeping out the majority of the malicious code someone could put into the form
  //by stripping unwanted parts of whatever has been put in there.
  $username = mysql_real_escape_string($_POST['username']);
  $password = md5(mysql_real_escape_string($_POST['password']));//this takes the password and does an MD5 hash against it 
  $redirect_url = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "../meetingsandevents.php";
  
  //to be used when I mess up password authentication
  mysql_query("UPDATE users SET password ='".md5(mysql_real_escape_string('jl'))."' WHERE userid=1");
  
  $checklogin = mysql_query("SELECT * FROM users WHERE Username = '".$username."' AND Password = '".$password."'");
  if(mysql_num_rows($checklogin) == 1)
  {
	  $row = mysql_fetch_array($checklogin);
	  //$email = $row['emailaddress'];
	  $auth = $row['userlevel'];
	  $userid = $row['userid'];
	  
	  $_SESSION['Username'] = $username;
	  //$_SESSION['emailaddress'] = $email;
	  $_SESSION['auth'] = $auth;
	  $_SESSION['LoggedIn'] = 1;
	  $_SESSION['userid'] = $userid;
	  //$_SESSION['timeout']=time();
	  
	  /*if(empty($redirect_url) || preg_match("/\/login/", $redirect_url))
	  {
		$redirect_url = "../meetingsandevents.php";
	  }*/
	  echo "<meta http-equiv=\"refresh\" content=\"0;URL=".$redirect_url."\">";
  }
  else { 
?>
  <!doctype html>
  <html>
    <head>
      <meta charset="utf-8">
      <?php
          echo "<meta http-equiv=\"refresh\" content=\"0;URL=".$redirect_url."\">";
      ?>
      <title>Logging In</title>
      <script type="application/javascript">
            alert('Login Failed!');
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
?> 

