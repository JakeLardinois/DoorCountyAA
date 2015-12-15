// JavaScript Document

//localServer
var SERVER_URL = 'http://localhost:8080/DoorCountyAA/'
//Remote Server
//var SERVER_URL = './'


WebFont.load({
  google: {
	families: ['Ribeye Marrow', 'cursive', 'Droid Sans Mono', 'Italianno']
  }
});

function loadLoginDialog() {
	$('#login').dialog({
        modal: true,
        resizable: true,
        position: 'center',
        width: 'auto',
        autoResize: true,
        title: 'Login',
        buttons: {}
	});
}

function loadChangePasswordDialog() {
	$('#changepassword').dialog({
        modal: true,
        resizable: true,
        position: 'center',
        width: 'auto',
        autoResize: true,
        title: 'Change Password',
        buttons: {}
	});
}

/*This function takes a screenshot of the calendar, creates a form, appends it to the DOM and then posts to the screenshot via the form to create a pdf*/
function makecalendarpdf() {
	//$('head').append('<link href="./css/fullcalendar.print.css" rel="stylesheet" type="text/css"/>'); //a method of appending a stylesheet
	$(".fc-button").hide();/*Hides the <,>,today,month,week,day buttons*/
	$(".fc-today").css("background", "#fff"); /*Removes the current day background coloring*/
	
	html2canvas(document.getElementById("calendar"), {
		onrendered: function (canvas) {
			var form = document.createElement("form");
			form.setAttribute("method", "post");
			form.setAttribute("action", SERVER_URL + "userfunctions/makepdf.php");

			form.setAttribute("target", "view");

			var hiddenField = document.createElement("input");
			hiddenField.setAttribute("type", "hidden");
			hiddenField.setAttribute("name", "image");
			hiddenField.setAttribute("value", canvas.toDataURL());
			form.appendChild(hiddenField);
			document.body.appendChild(form);
			//window.open('', 'view1');
			//alert('fired in htm2canvas');
			form.submit();
			$(".fc-button").show();
			$(".fc-today").css("background", "");
		}
	});
}

/*function loadChangePasswordDialog() {
	$.prompt("<label for=\"username\">Username:</label><input type=\"text\" name=\"username\" id=\"username\" />", {
	  title: "Change Password?",
	  buttons: { "Change Password": 1, "Cancel": 0 },
	  close: function (e, v, m, f) {
		  
	  }
	});
}*/

//shows another way of doing an ajax post http://www.w3schools.com/ajax/ajax_xmlhttprequest_send.asp
/*function logout() {
	var xmlhttp;
	
	xmlhttp = new XMLHttpRequest();
	xmlhttp.open("POST", SERVER_URL + "userfunctions/logout.php",true);
 	xmlhttp.send();
}*/