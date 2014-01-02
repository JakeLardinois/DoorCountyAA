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
    <div id="changepassword" style="display: none;">
      <form method="post" action="userfunctions/changepassword.php" name="changepwform" id="changepwform">
        <fieldset style="text-align:right">
            <label for="password">Old Password:</label><input type="password" name="oldpassword" id="oldpassword" required /><br />
            <label for="password">New Password:</label><input pattern="^((?=.*(\d|\W))(?=.*[a-zA-Z]).{8,})$" title="Alphanumeric and 8 characters in length" type="password" name="newpassword" id="newpassword" required /><br />
            <label for="password">Re-Enter New Password:</label><input pattern="^((?=.*(\d|\W))(?=.*[a-zA-Z]).{8,})$" title="Alphanumeric and 8 characters in length" type="password" name="password_confirm" id="password_confirm" required /><br />
            <input type="submit" name="changepw" id="changepw" value="Change Password" />
        </fieldset>
      </form>
      <script language='javascript' type='text/javascript'>
	  	var password = document.querySelector(' input[name=newpassword]');
		var passwordConfirm = document.querySelector(' input[name=password_confirm]');
		[].forEach.call([password, passwordConfirm], function(el) {
			el.addEventListener('input', function() {
				if (!el.validity.patternMismatch) {
					if ( password.value === passwordConfirm.value ) {
						try{password.setCustomValidity('')}catch(e){}
					} else {
						password.setCustomValidity("Password and password confirm doesn\'t match")
					}
				}
			}, false)
		});
	  </script>
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
				  <li><a href=\"javascript:loadChangePasswordDialog()\">Change Password</a></li>
                  <li><a href=\"userfunctions/logout.php\">Logout</a></li>";
				  //<li><a href=\"javascript:logout()\">Logout</a></li>";
              } else if ( $_SESSION['auth'] < 2){
                  echo "
                  <li><a href=\"javascript:loadChangePasswordDialog()\">Change Password</a></li>
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