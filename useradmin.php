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
          <form id="frmAddUser"  style="display: none;">
              <input type="hidden" id="userid" name="userid" rel="0">
          	<div class="AddUserControl">
              <label for="username">UserName</label>
              <input type="text" name="username" id="username" class="required" style="width:145px;" rel="1" />
          	</div>
            <div class="AddUserControl">
              <label for="fname">First Name</label>
              <input type="text" name="fname" id="fname" class="required" style="width:140px;" rel="2" />
            </div>
            <div class="AddUserControl">
              <label for="lname">Last Name</label>
              <input type="text" name="lname" id="lname" class="required" rel="3" />
            </div>
            <div class="AddUserControl">
              <label for="emailaddress">E-mail</label>
              <input type="email" name="emailaddress" id="emailaddress" class="required" style="width:240px;" rel="4" />
            </div>
            <div class="AddUserControl">
              <label for="password">Password</label>
              <input type="text"  name="password" id="password" class="required" rel="6" />
            </div>
            <div class="AddUserControl">
              <label for="userlevel">Access Level</label>
              
              <select name="userlevel" id="userlevel" class="required" rel="5" />
              	<option value = "0">zero</option>
              	<option value = "1">one</option>
               	<option value = "2">two</option>
              </select>
            </div>
          </form>
        </div>
        <?php require_once("./templates/footer.php"); ?>
    </div>
  </body>
</html>