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

        events: SERVER_URL + 'calendarfunctions/events.php', //loads the events into json

        timeFormat: 'h:mm{ - h:mm}', // 'H(:mm)', // uppercase H for 24-hour clock
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
            ShowAddEventPopup(date);
        },

        editable: true,
        eventDrop: function (event, delta) {
            var start = $.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss");
            var end = $.fullCalendar.formatDate(event.end, "yyyy-MM-dd HH:mm:ss");
            $.ajax({
                url: SERVER_URL + 'calendarfunctions/update_events.php',
                data: 'title=' + event.title + '&start=' + start + '&end=' + end + '&id=' + event.id,
                type: "POST",
                success: function (json) {
                    alert("Updated Successfully");
                }
            });
        },
        eventResize: function (event) {
            var start = $.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss");
            var end = $.fullCalendar.formatDate(event.end, "yyyy-MM-dd HH:mm:ss");
            $.ajax({
                url: SERVER_URL + 'calendarfunctions/update_events.php',
                data: 'title=' + event.title + '&start=' + start + '&end=' + end + '&id=' + event.id,
                type: "POST",
                success: function (json) {
                    alert("Updated Successfully");
                }
            });
        },
        eventClick: function (event) {
            $('#description').val(event.title);
            $('#url').val(event.url);
            $('#start').val($.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss"));
            //alert($.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss"));
            $('#end').val($.fullCalendar.formatDate(event.end, "yyyy-MM-dd HH:mm:ss"));
            //alert($.fullCalendar.formatDate(event.end, "yyyy-MM-dd HH:mm:ss"));
            $('#repeats').prop('checked'); //recurring

            ShowEditEventPopup(event);
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
    $('#repeats').prop('checked') == false; //recurring
}

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
                var decision = $.prompt("Do you want delete this event or the series?", {
                    title: "Delete?",
                    buttons: { "Delete Event": 1, "Delete Series": 2, "Cancel": 0 },
                    close: function (e, v, m, f) {
                        if (v == 1) {
							$.ajaxSetup({ async: false });
                            $.ajax({
                                type: "POST",
                                url: SERVER_URL + 'calendarfunctions/delete_event.php',
                                data: "&id=" + event.id,
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
            },
            Update: function () {
                if ($('#frmEvent')[0].checkValidity()) { //check if the data in the form passes appropriate validity checks

                    var dataRow = {	//create an object of variables and populate them with the html from the form; these then get passed to the php form via the URL...
                        'title': $('#description').val(), //could not use #title for some reason...
                        'url': $('#url').val(),
                        'start': $('#start').val(),
                        'end': $('#end').val(),
                        'allday': $('#repeats').prop('checked'),
                        'id': event.id
                    }
                    //$('#start').val($.fullCalendar.formatDate(date, "yyyy-MM-dd HH:mm:ss"));

                    $.ajax({
                        type: 'POST',
                        url: SERVER_URL + 'calendarfunctions/update_events.php',
                        data: dataRow,
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
    });
}

/*I used to have these in meatingsandevents.php because they need to be loaded at the bottom of the page. I moved them inside this script when I moved the loading
of this entire script to the bottom of meetingsandevents.php...*/
$('#start').datetimepicker({
  timeFormat: "HH:mm:ss",
  dateFormat: "yy-m-dd"
});
$('#end').datetimepicker({
  timeFormat: "HH:mm:ss",
  dateFormat: "yy-m-dd"
});

