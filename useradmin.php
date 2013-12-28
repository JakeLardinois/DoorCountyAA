<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <?php require_once("./templates/scripts.php"); ?>
    <?php require_once("./templates/css.php"); ?>
    <script src="scripts/buildusertable.js"></script>
    <title>Links Page</title>
    <script type="application/javascript">
	</script>
  </head>
  

  <body>
	<div id="wrapper">
    	<?php require_once("./templates/header.php"); ?>
        <div id="maincontent">
        	<br>
            <br>
            <table id="objUsers">
              <thead>
                  <tr>
                      <th>
                          userid
                      </th>
                      <th>
                          username
                      </th>
                      <th>
                          fname
                      </th>
                      <th>
                          lname
                      </th>
                      <th>
                          EmailAddress
                      </th>
                      <th>
                          userlevel
                      </th>
                  </tr>
              </thead>
              <button id="btnAddNewRow">Add</button>
              <button id="btnDeleteRow">Delete</button> 
          </table>
          <form id="frmAddUser" style="display: none;" > 
          
            <input type="hidden" id="userid" name="userid" rel="0">
            
            <label for="username">User Name</label>
            <input type="text" name="username" id="username" class="required" rel="1" />
            
            <label for="fname">First Name</label>
            <input type="text" name="fname" id="fname" class="required" rel="2" />
            
            <label for="lname">Last Name</label>
            <input type="text" name="lname" id="lname" class="required" rel="3" />
            
            <label for="EmailAddress">E-mail Address</label>
            <input type="email" name="EmailAddress" id="EmailAddress" class="required" rel="4" />
            
            <label for="userlevel">User Level</label>
            <input type="number" min="0" max="2" name="userlevel" id="userlevel" class="required" rel="5" />
            
            <!--<input list="userlevel" >
            <datalist name="userlevel" id="userlevel" class="required" rel="5" >
            	<option value="Internet Explorer">
            </datalist>-->
            
            
            <label for="password">Password</label>
            <input type="text" name="password" id="password" class="required" rel="6" />
            
          </form>
        </div>
        <?php require_once("./templates/footer.php"); ?>
    </div>
  </body>
</html>