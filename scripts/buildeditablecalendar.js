// JavaScript source code

function WireEvents() {
	var $frmAddEvent = $('#frmAddEvent');
	
	
	$frmAddEvent.on('submit', function(ev){
		ev.preventDefault();
        //$('#popupEventForm').dialog('close');
        var dataRow = {
            'title': $('#description').val(), //could not use #title for some reason...
            'url': $('#url').val(),
            'start': $('#start').val(),
            'end': $('#end').val()
        }
        //ClearPopupFormValues();
        $.ajax({
            type: 'POST',
            url: 'http://localhost:8080/DoorCountyAA/add_events.php',
            data: dataRow,
            success: function (response) {
				sValue = JSON.parse(response);
                if (sValue.Success) {
					
                    $('#calendar').fullCalendar('refetchEvents');
					
                    alert('New event saved!');
					$('#frmAddEvent').dialog('close');
                }
                else {
                    alert('Error, could not save event!');
                }
            }
        });
    });
	
    /*$('#btnSave').click(function (event) {
        //$('#popupEventForm').dialog('close');
        var dataRow = {
            'title': $('#title').val(),
            'url': $('#url').val(),
            'start': $('#start').val(),
            'end': $('#end').val()
        }
        //ClearPopupFormValues();
        $.ajax({
            type: 'POST',
            url: 'http://localhost:8080/DoorCountyAA/add_events.php',
            data: dataRow,
            success: function (response) {
                if (response == 'True') {
                    $('#calendar').fullCalendar('refetchEvents');
					$('#popupEventForm').dialog('close');
                    alert('New event saved!');
                }
                else {
                    alert('Error, could not save event!');
                }
            }
        });
    });*/
}

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



        // Convert the allDay from string to boolean
        eventRender: function (event, element, view) {
            if (event.allDay === 'true') {
                event.allDay = true;
            } else {
                event.allDay = false;
            }
        },
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
            ShowEventPopup(date);
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
            var decision = confirm("Do you really want to do that?");
            if (decision) {
                $.ajax({
                    type: "POST",
                    url: "http://localhost:8080/DoorCountyAA/delete_event.php",
                    data: "&id=" + event.id,
                    type: "POST",
                    success: function (json) {
                        alert("Deleted Successfully");
                    }
                });
                $('#calendar').fullCalendar('removeEvents', event.id);
                return false;
            }
            else {

            }
        }
    });
}
function ShowEventPopup(date) {
    //ClearPopupFormValues();
    /*$('#popupEventForm').show();
    $('#eventTitle').focus(); */
    $('#AddEvent').dialog({
        modal: true,
        resizable: true,
        position: 'center',
        width: 'auto',
        autoResize: true,
        title: 'Add Event'
    });
}

