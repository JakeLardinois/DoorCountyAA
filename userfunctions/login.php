<?php 
require_once '../infrastructure/dbconfig.php'; 

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
                //$email = $row['emailaddress'];
                $auth = $row['userlevel'];
				$userid = $row['userid'];
                
                $_SESSION['Username'] = $username;
                //$_SESSION['emailaddress'] = $email;
                $_SESSION['auth'] = $auth;
                $_SESSION['LoggedIn'] = 1;
                $_SESSION['userid'] = $userid;
				//$_SESSION['timeout']=time();
				
				//since I already post login form, I don't need to do this also...
                echo "<meta http-equiv='refresh' content='=2;../meetingsandevents.php' />";
			}
			else {
				//display an error message because login failed...
				echo "<meta http-equiv='refresh' content='=2;../meetingsandevents.php' />";
			}
?> 

