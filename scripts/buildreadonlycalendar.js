// JavaScript source code
//localServer
var SERVER_URL = 'http://localhost:8080/DoorCountyAA/'

//Remote Server
//var SERVER_URL = './'


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

        events: SERVER_URL + 'events.php', //loads the events into json

        timeFormat: 'h:mm{ - h:mm}', // 'H(:mm)', // uppercase H for 24-hour clock
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
}








