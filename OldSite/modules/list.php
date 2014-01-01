<?PHP
//session_start();
//little add-on for the neat phpEventCalendar (ikemcg.com) by Nick77
//this is version 0.1.3, third and hopefully final correction of the SQL Statement to show only events in the future
//License: of course GPL;-)
//and sorry for the mess; it's my *first* work in php:))

require("../config.php");
require("../lang/lang." . LANGUAGE_CODE . ".php");

$id = $HTTP_GET_VARS['id'];

//if you want a.m./p.m. displayed in your time, change %k:%i to %l:%i:%p in the following sql command

//if you want to limit the number of events displayed to X events, place 'LIMIT X' (without the " ' " signs) at the end of the $sql statement right before the closing ".

mysql_connect(DB_HOST, DB_USER, DB_PASS) or die(mysql_error());
mysql_select_db(DB_NAME) or die(mysql_error());

$sql = "SELECT id, y, m, d, title, text, TIME_FORMAT(start_time, '%k:%i') AS stime, ";
$sql .= "TIME_FORMAT(end_time, '%k:%i') AS etime, " . DB_TABLE_PREFIX . "users.uid, fname, lname ";
$sql .= "FROM " . DB_TABLE_PREFIX . "mssgs ";
$sql .= "LEFT JOIN " . DB_TABLE_PREFIX . "users ";
$sql .= "ON (" . DB_TABLE_PREFIX . "mssgs.uid = " . DB_TABLE_PREFIX . "users.uid) ";
$sql .= "WHERE text IS NOT NULL AND (y*10000+m*100+d) >= ";
$sql .= "(YEAR(NOW())*10000+MONTH(NOW())*100+DAYOFMONTH(NOW())) ";
$sql .= "ORDER BY y, m, d, start_time";
$result = mysql_query($sql) or die(mysql_error());
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Your Title goes here</title>
	<link rel="stylesheet" type="text/css" href="css/popwin.css">

	<SCRIPT Language="Javascript">

	/*
	This script originally written by Eric (Webcrawl@usa.net)
	downloaded at dynamicdrive.com
	modified to fit phpEventCalendar by nick77
	*/
	function printit(){  
		if (window.print) {
			window.print() ;
		} else {
   			var WebBrowser = '<OBJECT ID="WebBrowser1" WIDTH=0 HEIGHT=0 CLASSID="CLSID:8856F961-340A-11D0-A96B-00C04FD705A2"></OBJECT>';
			document.body.insertAdjacentHTML('beforeEnd', WebBrowser);
   	 		WebBrowser1.ExecWB(6, 2); //Use a 1 vs. a 2 for a prompting dialog box    WebBrowser1.outerHTML = "";  
		}
	}
	</script>

</head>
<body>




<img src="images/clear.gif" width="1" height="3" border="0"><br clear="all">


<!-- we first ask, where in our database the entries are not NULL and take place in the future To customize the timeinterval (for example: next week) of which events should be displayed: read below!-->

<?
//we get todays date
$day = date("j");
$month = date("n");
$year = date("Y") ;

//HOW TO NARROW THE DISPLAYED TIME INTERVAL
//this is not implemented yet in this version. If you want to add it or have already done, please let me know!



//number of events found
$num_rows = mysql_num_rows($result); 





//if we find some entries, we go on...
if ($myrow = mysql_fetch_array($result)) {

//Main title of the site
	echo "<span class=\"display_header\">" . $num_rows . $lang['list'] . "</span>";
	echo '<br clear="all"><img src="/images/clear.gif" width="1" height="3" border="0"><br clear="all"><br><br>';
	

  //as long as there is another event we build our tables;-)
  do {


	$wday =  date ("w", mktime(0,0,0,$myrow['m'],$myrow['d'],$myrow['y']));
	$title = stripslashes($myrow["title"]);
	$body = stripslashes(str_replace("\n", "<br />", $myrow["text"]));
	

//this sets how the time should be displayed: 
//1. start-time 0:00->no time displayed
//2. start-time = end-time -> only start-time displayed
//3. normal start- and end-time

//If you want to use German language you have to write Uhr instead of o'clock of course;-)

	if ($myrow["stime"] == "55:55") {
		$timestr = "";
	}
	elseif ($myrow["stime"] == $myrow["etime"]) {
		$timestr = $myrow["stime"] . " o'clock";
	} else { 
		$timestr = $myrow["stime"] . "-" . $myrow["etime"] . " o'clock";
	}
	
	echo "<tr>";
	echo "<td><span class=\"display_header\">";
	echo $lang['days'][$wday] . ", " ;
	//If you prefer the German Dataformatting, uncomment next line:
	//printf("<tr><td>%s %s</td><td>%s</tr>\n", $myrow["d"] . ". ", $myrow["m"] . ". ", $myrow["y"]);
	//this is the english data formatting line, comment it out, if you want to use the German one!
	printf("<tr><td>%s %s</td><td>%s</tr>\n", $myrow["m"] . "/", $myrow["d"] . "/", $myrow["y"]);
	echo "</span></td>";

	echo "<td align=\"right\"><span class=\"display_header\">&nbsp;</span></td>";
	echo "</tr>";
	echo "<img src=<\"images/clear.gif\" width=\"1\" height=\"12\" border=\"0\"><br clear=\"all\">";
	
	global $lang;

?>
	<table cellspacing="0" cellpadding="0" border="0" width="300">
		<tr><td bgcolor="#000000">
			<table cellspacing="1" cellpadding="1" border="0" width="100%">
				<tr>
					<td class="display_title_bg"><table cellspacing="0" cellpadding="0" border="0" width="100%"><tr>
							<td width="100%"><span class="display_title">&nbsp;<?= $title ?></span></td>
							<td><img src="images/clear.gif" width="20" height="1" border="0"></td>
							<td align="right" nowrap="yes"><span class="display_title"><?= $timestr ?></span></td>
					</tr></table></td>
				</tr>
				<tr><td class="display_txt_bg">
					<table cellspacing="1" cellpadding="1" border="0" width="100%">
						<tr>
							<td><span class="display_txt"><?= $body ?></span></td>
						</tr>
				
						
					</table>
				</td></tr>
			</table>
	</td></tr></table>
<?



	echo "<br>";

  } while ($myrow = mysql_fetch_array($result));

//this will be the footer of the page
	echo "</table>\n";
	echo "<span class=\"display_edit\"><br>";
	echo "[<a href=\"JavaScript:printit()\">Print this page</a>]&nbsp;[<a href=\"JavaScript:window.close()\">Close window</a>]</span><br>";
	

} else {

	echo "Sorry, no events  were found!";	

}
?>