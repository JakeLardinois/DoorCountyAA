<?php require_once 'infrastructure/dbconfig.php'; ?> 

<footer>
	<br>
    <br>
	<div id="login" style="display: none;">
      <form method="post" action="userfunctions/login.php" name="loginform" id="loginform">
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
          /*echo "<a href=\"javascript:loadChangePasswordDialog()\">Test</a>";*/
		  
          echo "<ul>";
              if ( $_SESSION['auth'] == 2 ) {
                  echo "
                  <li><a href=\"useradmin.php\">User Admin</a></li>
				  <li><a href=\"changepassword.php\">Change Password</a></li>
                  <li><a href=\"userfunctions/logout.php\">Logout</a></li>";
				  //<li><a href=\"javascript:logout()\">Logout</a></li>";
              } else if ( $_SESSION['auth'] < 2){
                  echo "
                  <li><a href=\"changepassword.php\">Change Password</a></li>
                  <li><a href=\"userfunctions/logout.php\">Logout</a></li>";
				  //<li><a href=\"javascript:logout()\">Logout</a></li>";
              }
          echo "</ul>";
        }
        else
        {// display the login form
        ?>
        	<ul>
              <li><a href="javascript:loadLoginDialog()">Login</a></li>
            </ul>
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