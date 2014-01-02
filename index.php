<?php /* A got an 'Warning: session_start() [function.session-start]: Cannot send session cache limiter - headers already sent' error when I had session_start()
	executing in infrastructure/dbconfig.php only since my footer uses session data; the resolution was to put it here also... */
	//session_start();  
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <?php require_once("./templates/scripts.php"); ?>
    <?php require_once("./templates/css.php"); ?>
    <title>Alcoholics Anonymous District 22 Door and Kewaunee Counties, WI</title>
  </head>
  

  <body>
	<div id="wrapper">
    	<?php require_once("./templates/header.php"); ?>
        <div id="maincontent" style="overflow:hidden">
        	<br>
            <br>
            <br>
            <br>
            <br>
            <br>
          <img class="bigbook" src="images/bigbook.gif" width="160" height="212" alt="big book">
          <p class="indextext">Alcoholics Anonymous is a fellowship of men and women who share their experience,
          strength and hope with each other that they may solve their common problem
          and help others to recover from alcoholism. The only requirement for membership
          is a desire to stop drinking. There are no dues or fees for AA membership;
          we are self-supporting through our own contributions. AA is not allied
          with any sect, denomination, politics, organization or institution; does
          not wish to engage in any controversy, neither endorses nor opposes any
          causes. Our primary purpose is to stay sober and help other alcoholics
          to achieve sobriety</p>
          <br>
          <br>
          <br>
        </div>
        <?php require_once("./templates/footer.php"); ?>
    </div>
  </body>
</html>