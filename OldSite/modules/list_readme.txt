//For the phpEventCalendar 0.2 written by ikemcg (http://www.ikemcg.com)
//written by Dominik Zumstein nick@olp.ch

//use it as you need it! (license of course GPL;-)

the "Show all events in a list file"


Changelog:
============

Version 0.1.3 
==================
-second bug since 0.1.1 in the mysql statement. Now hopefully last correction
-added (commented out) suggestion how to restrict number of events shown 
(i.e. you can make a link on your page saying: next 10 events and the pop-up window will show you those 10 events)
-removed the possibility to set a time interval for the events displayed due to the different approach now used.

version 0.1.1
==================
-correction of a bug causing to display by far not the correct events:(


version 0.1
==================


-now only events in the future will show up in the list by default (old events can remain in the calendar but won't show up in the "upcoming events" list
-above modification which allows to set a date range for the list to display
-corrected minor spelling errors in the readme file (this file)


TODO-list
===================

future release might include:
-possibility to change number of events displayed, incl. putting them on different sites..
-possibility to define a time interval to narrow the events displayed




ABOUT:
========

I made a rather simple solution to provide a possibility to provide the demanded feature of displaying all events in your phpEventcalendar

download the necessary file and updates from
http://olp.ch/list.zip

What does it do:
-makes a list of the oncoming events in your database in the style of eventdisplay.php file, including cross-browser compatible JavaScript Print and CloseWindow function.
-you can define a date range (for example "next week") where only the events during this period show up. look in the list.php file for further instruction!


What does it NOT do:
-up to now, the events are displayed in one big list. One could split them up in a handy amount of events per page


Installation:

1. in the /phpEventCalendar/lang folder copy and paste into your lang.XX.php file (translate for your language):

$lang['list'] = " events found:";
$lang['listevents'] = "list all events";

upload the modified file

2. add a title to the list.php file (look out for "your title goes here")

3. upload the list.php file into the /phpEventCalendar folder

-> it should be working now! try it by pointing your browser to http://yourdomain.com/path/to/phpEventCalendar/list.php


If so far everything is ok, we continue making a little link from your original calendar

4. in the /templates/default.php file add in the first <td></td> after
Code:
<?= $lang['months'][$m-1] ?>&<?= $y ?>

the following:

<a href="javascript:openAll()"><?= $lang['listevents'] ?></a>


5. Add in the functions.php file following code to the JavaScript functions
(don't forget to edit the path to your script there!!):


   function openAll() {
      window.open('path/to/phpEventCalendar/list.php', 'mssgDisplay', 'toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=340,height=600');
   }


That's all folks! hope you enjoy!

Nick