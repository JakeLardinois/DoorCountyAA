<?php
require("config.php");
require("./lang/lang.admin." . LANGUAGE_CODE . ".php");
require("functions.php");


$auth = auth();
$id   = (isset($_GET['id'])) ? $_GET['id'] : null;
$uid  = (isset($_SESSION['authdata']))
	? $_SESSION['authdata']['uid']
	: null;

if ($auth) {
	if (empty($id)) {
		displayEditForm('Add', $uid);
	} else {
		mysql_connect(DB_HOST, DB_USER, DB_PASS) or die(mysql_error());
		mysql_select_db(DB_NAME) or die(mysql_error());
		
		$sql = "SELECT uid FROM " . DB_TABLE_PREFIX . "mssgs WHERE id = $id";
		
		$result = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_assoc($result);
		
		if ( $auth == 2 || $uid == $row['uid'] ) {
			displayEditForm('Edit', $uid, $id);
		} else {
			echo $lang['accessdenied'];
		}
	}
} else {
	echo $lang['accessdenied'];
}

# ###########################################################
	
function displayEditForm($mode, $uid, $id="")
{
	global $lang;
	
	if ($mode == "Add") {
		$d 			= $_GET['d'];
		$m 			= $_GET['m'];
		$y 			= $_GET['y'];
		$text 		= $title = "";
		$headerstr 	= $lang['addheader'];
		$buttonstr 	= $lang['addbutton'];
		$pgtitle 	= $lang['addeventtitle'];
		$qstr 		= "?flag=add";
		$stime_vals = null;	
		$etime_vals = null;
	} elseif ($mode == "Edit") {
		
		mysql_connect(DB_HOST, DB_USER, DB_PASS) or die(mysql_error());
		mysql_select_db(DB_NAME) or die(mysql_error());
		
		$sql = "SELECT uid, y, m, d, start_time, end_time, title, text ";
		$sql .= "FROM " . DB_TABLE_PREFIX . "mssgs WHERE id = $id";
		
		$result = mysql_query($sql) or die(mysql_error());
		$row = mysql_fetch_assoc($result);
		
		if (!empty($row)) {
			$qstr 		= "?flag=edit&id=$id";
			$headerstr 	= $lang['editheader'];
			$buttonstr	= $lang['editbutton'];
			$pgtitle 	= $lang['editeventtitle'];
			$title 		= stripslashes($row["title"]);
			$text 		= stripslashes($row["text"]);
			$m 			= $row["m"];
			$d 			= $row["d"];
			$y 			= $row["y"];
		}
		
		$stime_vals = getPullDownTimeValues($row["start_time"]);
		$etime_vals = getPullDownTimeValues($row["end_time"]);
		#, $ehour, $eminute, $epm);
	} else {
		$lang['accesswarning'];
	}
?>
	<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<html>
	<head>
		<title><?php echo $pgtitle ?></title>
		<link rel="stylesheet" type="text/css" href="css/popwin.css">
	
		<script language="JavaScript">
		function formSubmit() {
			if (document.eventForm.title.value != "") {
				document.eventForm.method = "post";
				document.eventForm.action = "eventsubmit.php<?php echo $qstr ?>";
				document.eventForm.submit();
			} else {
				alert("<?php echo $lang['titlemissing'] ?>");
			}
		}
		</script>
	
	</head>
	<body>
	<span class="add_new_header"><?php echo $headerstr ?></span>
	<br><img src="images/clear.gif" width="1" height="5"><br>
		<table border=0 cellspacing=7 cellpadding=0>
		<form name="eventForm">
		<input type="hidden" name="uid" value="<?php echo $uid?>">
			<tr>
				<td nowrap valign="top" align="right" nowrap><span class="form_labels"></span></td>
				<td><?php monthPullDown($m, $lang['months']); dayPullDown($d); yearPullDown($y); ?></td>
			</tr>
			<tr>
				<td nowrap valign="top" align="right" nowrap>
				<span class="form_labels"><?php echo $lang['title']?></span></td>
				<td><input type="text" name="title" size="29" value="<?php echo $title ?>" maxlength="50"></td>
			</tr>
			<tr>
				<td nowrap valign="top" align="right" nowrap>
				<span class="form_labels"><?php echo $lang['text']?></span></td>
				<td><textarea cols=22 rows=6 name="text"><?php echo $text ?></textarea></td>
			</tr>
			<tr>
				<td nowrap valign="top" align="right" nowrap><span class="form_labels"><?php echo $lang['starttime'] ?></span></td>
				<td><?php hourPullDown($stime_vals['hour'], "start"); ?><b>:</b><?php minPullDown($stime_vals['minute'], "start"); amPmPullDown($stime_vals['pm'], "start"); ?></td>
			</tr>
			<tr>
				<td nowrap valign="top" align="right" nowrap><span class="form_labels"><?php echo $lang['endtime'] ?></span></td>
				<td><?php hourPullDown($etime_vals['hour'], "end"); ?><b>:</b><?php minPullDown($etime_vals['minute'], "end"); amPmPullDown($etime_vals['pm'], "end"); ?></td>
			</tr>
			<tr><td></td><td><br><input type="button" value="<?php echo $buttonstr ?>" onClick="formSubmit()">&nbsp;<input type="button" value="<?php echo $lang['cancel'] ?>" onClick="window.close();"></td></tr>
		</form>
		</table>
	</body>
	</html>
<?php
}

# ###########################################################

function getPullDownTimeValues($time) 
{
	$hour	= (int) substr($time, 0, 2);
	$minute = (int) substr($time, 3, 2);
	$pm     = false;
	
	if ($hour == 55) {
		$hour	= 0;
		$minute	= 0;
	} elseif ($hour > 12) {
		$hour = $hour - 12;
		$pm = true;
	} elseif ($hour == 12) {
		$pm = true;
	} elseif ($hour == 0) {
		$hour = 12;
		$pm = false;
	}
	return Array('hour' => $hour, 'minute' => $minute, 'pm' => $pm);	
}

# ###########################################################
?>
