<!DOCTYPE html
   PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
        <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title>Alcoholics Anonymous &amp; Al-Anon</title>
        <link rel="stylesheet" type="text/css" href="css/style.css" />
	<?php javaScript() ?>
        <script src="js/JScript.js" type="text/javascript"></script>
</head>
<body onload="MM_preloadImages()">

<table>
    <tr>
        <td colspan="5">
            <img alt="Animated_Header" src="images/headeranimated.gif" width="600" height="153"/>
        </td>
    </tr>
    <tr>
        <td width = "100%" colspan="5"><hr /></td>
    </tr>
    <tr>
        <td><a href="index.htm" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image1','','images/home_in.gif',1)"><img alt="Home_Link_Picture" name="Image1" src="images/home_out.gif"/></a></td>
        <td><a href="meetings.htm" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image2','','images/meetings_in.gif',1)"><img alt="Meetings_Link_Picture" name="Image2" src="images/meetings_out.gif"/></a></td>
        <td><a href="events.php" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image5','','images/calendar_in.gif',1)"><img alt="Calendar_Link_Picture" name="Image5" src="images/calendar_out.gif" /></a></td>
        <td><a href="links.htm" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image3','','images/links_in.gif',1)"><img alt="Links_Link_Picture" name="Image3" src="images/links_out.gif"/></a></td>
        <td><a href="contact.htm" onmouseout="MM_swapImgRestore()" onmouseover="MM_swapImage('Image4','','images/contact_in.gif',1)"><img alt="Contacts_Link_Picture" name="Image4" src="images/contact_out.gif" /></a></td>
    </tr>
    <tr>
        <td width = "100%" colspan="5"><hr/></td>
    </tr>
    <tr class="WelcomeRow">
        <td colspan = "5" height="20">
            <b>Calendar of Events</b>
        </td>
    </tr>
</table>

<br/><br/><br/>

<table cellpadding="0" cellspacing="0" border="0" align="center" >
    <tr>
	<td>
		<?php echo $scrollarrows ?>
		<span class="date_header">
		&nbsp;<?php echo $lang['months'][$m-1] ?>&nbsp;<?php echo $y ?></span>
	</td>

	<!-- form tags must be outside of <td> tags -->
	<form name="monthYear">
	<td align="right">
            <?php monthPullDown($m, $lang['months']); yearPullDown($y); ?>
            <input type="button" value="GO" onClick="submitMonthYear()"/>
	</td>
	</form>

    </tr>

    <tr>
	<td colspan="2" bgcolor="#000000" >
            <?php echo writeCalendar($m, $y); ?>
        </td>
    </tr>

    <tr>
	<td colspan="2" align="center">
            <?php echo footprint($auth, $m, $y) ?>
        </td>
    </tr>
</table>

</body>
</html>
