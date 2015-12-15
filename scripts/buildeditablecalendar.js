// JavaScript source code
//localServer
var SERVER_URL = 'http://localhost:8080/DoorCountyAA/'
//Remote Server
//var SERVER_URL = './'

$(document).ready(function () {
    var date = new Date();
    var d = date.getDate();
    var m = date.getMonth();
    var y = date.getFullYear();

    var calendar = $('#calendar').fullCalendar({
        editable: true,
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
		weekMode: 'liquid', //This option is what set the number of weeks displayed in month view; default value is 'fixed' which shows 6 weeks for every month.
        events: SERVER_URL + 'calendarfunctions/events.php', //loads the events into json

        timeFormat: 'h:mm{ - h:mmtt}', // 'H(:mm)', // uppercase H for 24-hour clock
        allDayDefault: false,  //for some reason this allows the time to be displayed on the month view...
        allDaySlot: false, //removes the all-day slot from the week veiw of the calendar

        selectable: true,
        selectHelper: true,
        dayClick: function (date, allDay, jsEvent, view) {
            //$('#title').val("");
            //$('#test').val("");
            //$('#url').val("");
            $('#start').val($.fullCalendar.formatDate(date, "yyyy-MM-dd HH:mm:ss"));
            $('#end').val($.fullCalendar.formatDate(date, "yyyy-MM-dd HH:mm:ss"));
			//Maybe the below should be done in a reset function that is displayed anytime before the form shows up..
			document.getElementById("repeatingoptions").style.visibility = 'visible';
			document.getElementById("repeats").checked = false;
            ShowAddEventPopup(date);
        },

        editable: true,
        eventDrop: function (event, delta) {
            var start = $.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss");
            var end = $.fullCalendar.formatDate(event.end, "yyyy-MM-dd HH:mm:ss");
            $.ajax({
                url: SERVER_URL + 'calendarfunctions/update_event.php',
                data: 'description=' + event.title + '&start=' + start + '&end=' + end + '&id=' + event.id,
                type: "POST",
                success: function (json) {
                    //alert("Updated Successfully");
                }
            });
        },
        eventResize: function (event) {
            var start = $.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss");
            var end = $.fullCalendar.formatDate(event.end, "yyyy-MM-dd HH:mm:ss");
            $.ajax({
                url: SERVER_URL + 'calendarfunctions/update_event.php',
                data: 'description=' + event.title + '&start=' + start + '&end=' + end + '&id=' + event.id,
                type: "POST",
                success: function (json) {
                    //alert("Updated Successfully");
                }
            });
        },
        eventClick: function (event) {
            var isRecurring;

			document.getElementById("id").value = event.id; //note that you populate by 'id' property, but data gets posted by the 'name' property
			document.getElementById("parent_id").value = event.parent_id;
            $('#description').val(event.title);
            $('#url').val(event.url);
			
			$('#start').val($.fullCalendar.formatDate(event.start, "yyyy-MM-dd hh:mmtt"));
            //document.getElementById("start").value = $.fullCalendar.formatDate(event.start, "yyyy-MM-dd hh:mmtt");
            //alert($.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss"));
			
			$('#end').val($.fullCalendar.formatDate(event.end, "yyyy-MM-dd hh:mmtt"));
            //document.getElementById("end").value = $.fullCalendar.formatDate(event.end, "yyyy-MM-dd hh:mmtt")
            //alert($.fullCalendar.formatDate(event.end, "yyyy-MM-dd HH:mm:ss"));

            isRecurring = (event.repeats == "true"); //this was the only setup I could get to properly function
            if (isRecurring) {
				document.getElementById("repeatingoptions").style.visibility = 'visible';
                document.getElementById("repeats").checked = isRecurring; //for some reason I had difficulty getting this to work and jquery handle didn'g function properly...
                //alert(event.repeat_freq);
                switch (parseInt(event.repeat_freq)) {
                    case 1:
                        document.getElementById("rad1days").checked = true;
                        break;
                    case 7:
                        document.getElementById("rad7days").checked = true;
                        break;
                    case 14:
                        document.getElementById("rad14days").checked = true;
                        break;
                }
                ShowEditEventPopup(event);
            }
            else {
                //document.getElementById("repeatingoptions").style.visibility = 'hidden';
				document.getElementById("repeatingoptions").style.visibility = 'visible';
				document.getElementById("repeats").checked = false;
                ShowEditEventPopup(event);
            }

            return false; //stops the navigation to the URL of the event
        }
    });

});


function ShowAddEventPopup(date) {
    //ClearPopupFormValues();
    /*$('#popupEventForm').show();
    $('#eventTitle').focus(); */
    $('#Event').dialog({
        modal: true,
        resizable: true,
        position: 'center',
        width: 'auto',
        autoResize: true,
        title: 'Create Event',
        buttons: {
            'Add': function () {
                if ($('#frmEvent')[0].checkValidity()) { //check if the data in the form passes appropriate validity checks
                    $.ajax({
                        type: 'POST',
                        url: SERVER_URL + 'calendarfunctions/add_events.php',
                        data: $('#frmEvent').serialize(),
                        success: function (response) {
                            sValue = JSON.parse(response);
                            if (sValue.Success) {
                                $('#calendar').fullCalendar('refetchEvents');
                                $('#Event').dialog('close');
                                ClearFormValues();
                            }
                            else {
                                alert('Error, could not save event!');
                            }
                        }
                    });
                }
            }
        }
    });
}

function ClearFormValues() {
    $('#description').val('');
    $('#url').val('');
    $('#start').val('');
    $('#end').val('');
    //$('#repeats').prop('checked') == false; //recurring
}

var objUpdatePrompts = {
	state0: {
		title: "Update Event or Event Series?",
		html: 'Do you want update this event or the series?',
		buttons: { "Update Event": 1, "Update Series": 2, "Cancel": 0 },
		submit: function (e, v, m, f) { //my below if statement wasn't firing when using 'close:'
			switch (v) {
				case 0:
					break;
				case 1: //Chose to update the Event
					$.ajaxSetup({ async: false });
					$.ajax({
						type: "POST",
						url: SERVER_URL + 'calendarfunctions/update_event.php',
						data: $('#frmEvent').serialize(), //.replace('description','title'),//for some reason I can't use title on my form, but this is what my php expects
						type: "POST",													//so I just change the value here...UPDATE- For some reason this stopped being important...
						success: function (json) {										//But I left it here for reference.
							sValue = JSON.parse(json);
							if (sValue.Success) {
								$('#calendar').fullCalendar('refetchEvents');
								$('#Event').dialog('close');
								//ClearFormValues();
							}
							else {
								alert('Error, could not save event!');
							}
						}
					});
					$.ajaxSetup({ async: true }); //Sets ajax back up to synchronous
					break;
				case 2: //Chose to update the Event Series
					e.preventDefault();
					$.prompt.goToState('state1', true);
					//return false;
					break;
				
			}
			//$.prompt.close();
		}
	},
	state1: {
		html: 'Are you sure? Updating the series deletes all past &amp; future events that are associated with it...  ',
		buttons: { No: -1, Yes: 0 },
		focus: 1, //puts the focus on No; I guess this value is an absolute? 
		submit:function(e,v,m,f){
			e.preventDefault();
			if(v==0){
				$.ajaxSetup({ async: false });
				$.ajax({
					type: "POST",
					url: SERVER_URL + 'calendarfunctions/update_eventseries.php',
					data: $('#frmEvent').serialize(), //.replace('description','title'),//for some reason I can't use title on my form, but this is what my php expects
					type: "POST",													//so I just change the value here...UPDATE- For some reason this stopped being important...
					success: function (json) {											//But I left it here for reference.
						sValue = JSON.parse(json);
						if (sValue.Success) {
							$('#calendar').fullCalendar('refetchEvents');
							$('#Event').dialog('close');
							//ClearFormValues();
						}
						else {
							alert('Error, could not save event!');
						}
					}
				});
				$.ajaxSetup({ async: true }); //Sets ajax back up to synchronous
				$.prompt.close();
			}
			else if(v==-1){
				$.prompt.close();
			}
		}
	}
};

function ShowEditEventPopup(event) {
    //ClearPopupFormValues();
    /*$('#popupEventForm').show();
    $('#eventTitle').focus(); */
    $('#Event').dialog({
        modal: true,
        resizable: true,
        position: 'center',
        width: 'auto',
        autoResize: true,
        title: 'Edit or Delete Event',
        buttons: {
            Delete: function () {
                isRecurring = (event.repeats == "true");

                if (isRecurring) {
                    var decision = $.prompt("Do you want delete this event or the series?", {
                        title: "Delete?",
                        buttons: { "Delete Event": 1, "Delete Series": 2, "Cancel": 0 },
                        close: function (e, v, m, f) {
                            if (v == 1) {
                                $.ajaxSetup({ async: false });
                                $.ajax({
                                    type: "POST",
                                    url: SERVER_URL + 'calendarfunctions/delete_event.php',
                                    data: "&id=" + event.id + "&parent_id=" + event.parent_id,
                                    type: "POST",
                                    success: function (json) {
                                        //alert("Deleted Successfully");
                                        $('#Event').dialog('close');
                                    }
                                });
                                $.ajaxSetup({ async: true }); //Sets ajax back up to synchronous
                                $('#calendar').fullCalendar('removeEvents', event.id);
                            }
                            if (v == 2) {
                                $.ajaxSetup({ async: false });//had to turn off asynchronous calls or else when series of events were deleted, the lag was such
                                $.ajax({						//that the database wouldn't respond fast enough before the events were refetched below...
                                    type: "POST",
                                    url: SERVER_URL + 'calendarfunctions/delete_eventseries.php',
                                    data: "&parent_id=" + event.parent_id,
                                    type: "POST",
                                    success: function (json) {
                                        //alert("Deleted Successfully");
                                        $('#Event').dialog('close');
                                    }
                                });
                                $.ajaxSetup({ async: true }); //Sets ajax back up to synchronous
                                $('#calendar').fullCalendar('refetchEvents');
                            }
                        }
                    });
                }
                else {
                    var decision = $.prompt("Are you sure that you want delete this event?", {
                        title: "Delete?",
                        buttons: { "Delete Event": 1, "Cancel": 0 },
                        close: function (e, v, m, f) {
                            if (v == 1) {
                                $.ajaxSetup({ async: false });
                                $.ajax({
                                    type: "POST",
                                    url: SERVER_URL + 'calendarfunctions/delete_event.php',
                                    data: "&id=" + event.id + "&parent_id=" + event.parent_id,
                                    type: "POST",
                                    success: function (json) {
                                        //alert("Deleted Successfully");
                                        $('#Event').dialog('close');
                                    }
                                });
                                $.ajaxSetup({ async: true }); //Sets ajax back up to synchronous
                                $('#calendar').fullCalendar('removeEvents', event.id);
                            }
                        }
                    });
                }
            },
            Update: function () {
                if ($('#frmEvent')[0].checkValidity()) { //check if the data in the form passes appropriate validity checks
                    isRecurring = (event.repeats == "true");
					
					
                    if (isRecurring) {
						$.prompt(objUpdatePrompts);
                    }
                    else {
                        /*var dataRow = {	//create an object of variables and populate them with the html from the form; these then get passed to the php form via the URL...
										//using $('#frmEvent').serialize() is easier, but I wanted to leave this here for reference...
                            //'title': $('#description').val(), //could not use #title for some reason...
							'description': $('#description').val(),
                            'url': $('#url').val(),
                            'start': $('#start').val(),
                            'end': $('#end').val(),
                            //'allday': $('#repeats').prop('checked'),
							'repeats': $('#repeats').prop('checked'),
							'repeat-freq': $('input[name=repeat-freq]:checked').val(), //$('#repeat-freq').val()
                            'id': event.id,
							'parent_id': event.parent_id
                        }*/
                        //$('#start').val($.fullCalendar.formatDate(date, "yyyy-MM-dd HH:mm:ss")); ,

                        $.ajax({
                            type: 'POST',													//Upated this so that a non-recurring event could be made to recur...
                            url: SERVER_URL + 'calendarfunctions/update_eventseries.php', //this now entails the deletion and recreation of the original event.
                            //data: dataRow, //I had to remove this because it was returning 'true' for my repeats variable and serialize returns 'on'
							data: $('#frmEvent').serialize(),
                            success: function (response) {
                                sValue = JSON.parse(response);
                                if (sValue.Success) {
                                    $('#calendar').fullCalendar('refetchEvents');
                                    $('#Event').dialog('close');
                                    //ClearFormValues();
                                }
                                else {
                                    alert('Error, could not save event!');
                                }
                            }
                        });
                    }
                }
            }
        }
    });
}

/*I used to have these in meatingsandevents.php because they need to be loaded at the bottom of the page. I moved them inside this script when I moved the loading
of this entire script to the bottom of meetingsandevents.php...*/
$('#start').datetimepicker({
    timeFormat: "hh:mmtt",
    dateFormat: "yy-m-dd"
});
$('#end').datetimepicker({
    timeFormat: "hh:mmtt",
    dateFormat: "yy-m-dd"
});

