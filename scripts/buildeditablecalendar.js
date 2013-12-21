// JavaScript source code
//var calendar;


function BuildCalendar() {
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

        events: "http://localhost:8080/DoorCountyAA/events.php",

        timeFormat: 'H(:mm)', // uppercase H for 24-hour clock
		allDayDefault: false,
		//allDaySlot: false,


        // Convert the allDay from string to boolean
        /*eventRender: function (event, element, view) {
            if (event.allDay === 'true') {
                event.allDay = true;
            } else {
                event.allDay = false;
            }
        },*/
		
        selectable: true,
        selectHelper: true,
        /*select: function (start, end, allDay) {
            var title = prompt('Event Title:');
            var url = prompt('Type Event url, if exits:');
            if (title) {
                var start = $.fullCalendar.formatDate(start, "yyyy-MM-dd HH:mm:ss");
                var end = $.fullCalendar.formatDate(end, "yyyy-MM-dd HH:mm:ss");
                $.ajax({
                    url: 'http://localhost:8080/DoorCountyAA/add_events.php',
                    data: 'title=' + title + '&start=' + start + '&end=' + end + '&url=' + url,
                    type: "POST",
                    success: function (json) {
                        alert('Added Successfully');
                    }
                });
                calendar.fullCalendar('renderEvent',
                {
                    title: title,
                    start: start,
                    end: end,
                    allDay: allDay
                },
                true // make the event "stick"
                );
            }
            calendar.fullCalendar('unselect');
        },*/
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
                url: 'http://localhost:8080/DoorCountyAA/update_events.php',
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
                url: 'http://localhost:8080/DoorCountyAA/update_events.php',
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
			alert($.fullCalendar.formatDate(event.start, "yyyy-MM-dd HH:mm:ss"));
			$('#end').val($.fullCalendar.formatDate(event.end, "yyyy-MM-dd HH:mm:ss"));
			alert($.fullCalendar.formatDate(event.end, "yyyy-MM-dd HH:mm:ss"));
			$('#allday').prop('checked'); //allday
			
			ShowEditEventPopup(event);
			return false; //stops the navigation to the URL
			
            /*var decision = confirm("Do you really want to do that?");
            if (decision) {
                $.ajax({
                    type: "POST",
                    url: "http://localhost:8080/DoorCountyAA/delete_event.php",
                    data: "&id=" + event.id,
                    type: "POST",
                    success: function (json) {
                        //alert("Deleted Successfully");
                    }
                });
                $('#calendar').fullCalendar('removeEvents', event.id);
                return false;
            }
            else {

            }*/
        }
    });
}

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
        title: 'Add Event',
        buttons: {
            Ok: function () {
                if ($('#frmEvent')[0].checkValidity()) { //check if the data in the form passes appropriate validity checks
				
                    var dataRow = {	//create an object of variables and populate them with the html from the form; these then get passed to the php form via the URL...
                        'title': $('#description').val(), //could not use #title for some reason...
                        'url': $('#url').val(),
                        'start': $('#start').val(),
                        'end': $('#end').val(),
						'allday': $('#allday').prop('checked')
                    }
					//$('#start').val($.fullCalendar.formatDate(date, "yyyy-MM-dd HH:mm:ss"));

                    $.ajax({
                        type: 'POST',
                        url: 'http://localhost:8080/DoorCountyAA/add_events.php',
                        data: dataRow,
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
	$('#allday').prop('checked') == false; //allday
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
        title: 'Add Event',
        buttons: {
			Delete: function () {
				$.ajax({
                    type: "POST",
                    url: "http://localhost:8080/DoorCountyAA/delete_event.php",
                    data: "&id=" + event.id,
                    type: "POST",
                    success: function (json) {
                        //alert("Deleted Successfully");
						$('#Event').dialog('close');
                    }
                });
                $('#calendar').fullCalendar('removeEvents', event.id);
				},
            Update: function () {
                if ($('#frmEvent')[0].checkValidity()) { //check if the data in the form passes appropriate validity checks
				
					
                    var dataRow = {	//create an object of variables and populate them with the html from the form; these then get passed to the php form via the URL...
                        'title': $('#description').val(), //could not use #title for some reason...
                        'url': $('#url').val(),
                        'start': $('#start').val(),
                        'end': $('#end').val(),
						'allday': $('#allday').prop('checked')
                    }
					//$('#start').val($.fullCalendar.formatDate(date, "yyyy-MM-dd HH:mm:ss"));

                    $.ajax({
                        type: 'POST',
                        url: 'http://localhost:8080/DoorCountyAA/add_events.php',
                        data: dataRow,
                        success: function (response) {
                            sValue = JSON.parse(response);
                            if (sValue.Success) {
                                $('#calendar').fullCalendar('refetchEvents');
                                $('#AddEvent').dialog('close');
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


