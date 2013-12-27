<?php require_once 'config/config.php'; ?> 

<footer>
	<div id="login" style="display: none;">
      <form method="post" action="./meetingsandevents.php" name="loginform" id="loginform">
        <fieldset>
            <label for="username">Username:</label><input type="text" name="username" id="username" /><br />
            <label for="password">Password:</label><input type="password" name="password" id="password" /><br />
            <input type="submit" name="login" id="login" value="Login" />
        </fieldset>
      </form>
    </div>
    <div class="footercontent">
	  <?php
        if(!empty($_SESSION['LoggedIn']) && !empty($_SESSION['Username']))
        {// let the user access the authenticated page
		
          /*echo "<a href=\"logout.php\">Logout</a>";*/
          echo "<a href=\"javascript:loadChangePasswordDialog()\">Test</a>";
		  
          echo "<ul>";
              
              
              if ( $_SESSION['auth'] == 2 ) {
                  echo "
                  <li><a href=\"useradmin.php\">User Admin</a></li>
				  <li><a href=\"changepassword.php\">Change Password</a></li>
                  <li><a href=\"userfunctions/logout.php\">Logout</a></li>";
              } else {
                  echo "
                  <li><a href=\"changepassword.php\">Change Password</a></li>
                  <li><a href=\"userfunctions/logout.php\">Logout</a></li>";
              }
          echo "</ul>";
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
                $auth = $row['userlevel'];
                
                $_SESSION['Username'] = $username;
                $_SESSION['EmailAddress'] = $email;
                $_SESSION['auth'] = $auth;
                $_SESSION['LoggedIn'] = 1;
                
                echo "<meta http-equiv='refresh' content='=2;meetingsandevents.php' />";
            }
            else
            {
                echo "<ul>";
                echo "<li><a href=\"javascript:loadLoginDialog()\">Try Again</a></li>";
                echo "</ul>";
            }
        }
        else
        {// display the login form
        ?>
          <div id="logininfo">
              <a href="javascript:loadLoginDialog()">Login</a>
          </div>		  
        <?php  
        }
      ?>
      <p>AA World Services has neither reviewed
          nor endorsed this page. It is provided by the Door and Kewaunee county
          area Intergroup, and does not represent Alcoholics Anonymous as a whole.
      </p>
      <p>Alcoholics Anonymous&reg;, A.A.&reg;, and The
          Big Book&reg; are registered trademarks of Alcoholics Anonymous World Services,
          Inc.
      </p>
	</div>
</footer>