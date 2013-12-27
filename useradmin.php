<?php require_once 'config/config.php'; ?> 

<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <?php require("./templates/scripts.php"); ?>
    <?php require("./templates/css.php"); ?>
    <style type="text/css">	
		#users{ width: 80%; margin-right: auto; margin-left: auto;  }
		#users .user{ border: solid 1px #bbbbbb; background-color: #dddddd; padding: 10px; margin: 0px; }
		#users .user .controls{ float: right; }
		.userid{ padding-left: 15px; }
		.username{ padding-left: 15px; }
		.fname{ padding-left: 15px; }
		.lname{ padding-left: 15px; }
		.emailaddress{ padding-left: 15px; }
		.userlevel{ padding-left: 15px; }
		
		/*-------------impromptu---------- */			
		div.jqi .jqimessage .field{ padding: 5px 0; }
		div.jqi .jqimessage .field label{ display: block; clear: left; float: left; width: 100px; }
		div.jqi .jqimessage .field input{ width: 150px; border: solid 1px #777777; }
		div.jqi .jqimessage .field input.error{ width: 150px; border: solid 1px #ff0000; }
		/*-------------------------------- */
	</style>
    <title>Links Page</title>
  </head>
  

  <body>
	<div id="wrapper">
    	<?php require("./templates/header.php"); ?>
        <div id="maincontent">
        	<br>
            <br>
        	<?php
				mysql_connect(mysql_hostname, mysql_username, mysql_password) or die("MySQL Error: " . mysql_error());
				mysql_select_db(mysql_dbname) or die("MySQL Error: " . mysql_error()); 
				
				//mysql_real_escape_string cleans database input by keeping out the majority of the malicious code someone could put into the form
				//by stripping unwanted parts of whatever has been put in there.
				//$username = mysql_real_escape_string($_POST['username']);
				//$password = md5(mysql_real_escape_string($_POST['password']));//this takes the password and does an MD5 hash against it 
				
				$users = mysql_query("SELECT * FROM users");
				
				echo "<div id=\"users\">";
				while ($cols = mysql_fetch_array($users)){
					echo "<div class=\"user\">";
					echo "<span class=\"controls\">";
						echo "<a href=\"javascript:;\" title=\"Edit User\" class=\"edituser\" onclick=\"editUser(".$cols['userid'].");\">Edit</a> | ";
						echo "<a href=\"javascript:;\" title=\"Delete User\" class=\"deleteuser\" onclick=\"removeUser(".$cols['userid'].");\">Delete</a> | ";
						echo "<a href=\"javascript:;\" title=\"Delete User\" class=\"deleteuser\" onclick=\"removeUser(".$cols['userid'].");\">Change Password</a>";
					echo "</span>";
					echo "<span class=\"userid\">".$cols['userid']."</span>";
					echo "<span class=\"username\">".$cols['username']."</span>";
					echo "<span class=\"fname\">".$cols['fname']."</span>";
					echo "<span class=\"lname\">".$cols['lname']."</span>";
					echo "<span class=\"emailaddress\">".$cols['EmailAddress']."</span>";
					echo "<span class=\"userlevel\">".$cols['userlevel']."</span>";
					
					echo "</div>";
				}
				echo "</div>";
				
				mysql_free_result($users);
			   	mysql_close();
			?>
        </div>
        <?php require("./templates/footer.php"); ?>
    </div>
  </body>
</html>