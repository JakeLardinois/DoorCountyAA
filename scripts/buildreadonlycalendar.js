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


	//w = $('#calendar').css('width');
	var beforePrint = function() {
		// prepare calendar for printing
		$('#calendar').css('width', '9.5in');
		$('#calendar').fullCalendar('render');
	};
	var afterPrint = function() {
		//$('#calendar').css('width', w);
		$('#calendar').css('width', '');
		$('#calendar').fullCalendar('render');
	};
	if (window.matchMedia) {
		var mediaQueryList = window.matchMedia('print');
		mediaQueryList.addListener(function(mql) {
			if (mql.matches) {
				beforePrint();
			} else {
				afterPrint();
			}
		});
	}
	window.onbeforeprint = beforePrint;
	window.onafterprint = afterPrint;
		
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
        editable: false,	//This determines if the events can be dragged and resized.
        eventClick: function (event) {
            if (event.url) {
                window.open(event.url);
                return false; //stops the navigation to the URL of the event
            }
            return false; //stops the navigation to the URL of the event
        }
    });
});









