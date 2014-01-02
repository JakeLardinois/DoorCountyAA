<?php //session_start(); ?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8">
    <?php require_once("./templates/scripts.php"); ?>
    <?php require_once("./templates/css.php"); ?>
    <title>Links Page</title>
  </head>
  

  <body>
	<div id="wrapper">
    	<?php require_once("./templates/header.php"); ?>
        <div id="maincontent" style="overflow:hidden" >
        	<img style="float: left;" src="images/agegender.gif" width="433" height="397" alt="Age or Gender">
            <br>
            <br>
            <br>
        	<ul>
            	<li class="linklistitem">
                	<a href="http://www.aa.org/" target="_blank">Alcoholics Anonymous World Services</a> - created and maintained by the General Service Office of US/Canada which is the national office serving AA in the US and Canada
                </li>
                <li class="linklistitem">
                	<a href="http://www.aa.org/lang/en/central_offices.cfm?origpage=373&cmd=getgroups&state=Wisconsin&country=United States" target="_blank"> WISCONSIN Intergroup Phone Numbers</a>
                </li>
                <li class="linklistitem">
                	<a href="http://www.area74.org/" target="_blank">Alcoholics Anonymous Area 74 </a> - Door and Kewaunee County is located in District 22 of Area 74.
                </li>
                <li class="linklistitem">
                	<a href="http://www.aagrapevine.org/" target="_blank">AA Grapevine</a> - the international journal of Alcoholics Anonymous, widely known as "our meeting in print". Their magazine, <em>The Grapevine</em>, is published every month.
                </li>
                <li class="linklistitem">
                	
                	<audio controls id="drbobaudio">
                       <source src="./multimedia/DrBob1948.mp3" type="audio/mpeg">
                       Your browser does not support the audio element.
                     </audio> 
                     <label for="drbobaudio">Dr. Bob 1948 Detroit Michigan</label>
                </li>
                <li class="linklistitem">
                	<a href="./OldSite" target="_blank">The Old DoorCountyAA.org Website</a>
                </li>
            </ul>
            
        </div>
        <?php require_once("./templates/footer.php"); ?>
    </div>
  </body>
</html>
