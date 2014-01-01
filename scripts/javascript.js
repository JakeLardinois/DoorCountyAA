// JavaScript Document

//localServer
//var SERVER_URL = 'http://localhost:8080/DoorCountyAA/'
//Remote Server
var SERVER_URL = './'


WebFont.load({
  google: {
	families: ['Ribeye Marrow', 'cursive', 'Tangerine']
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
	$.prompt("<label for=\"username\">Username:</label><input type=\"text\" name=\"username\" id=\"username\" />", {
	  title: "Change Password?",
	  buttons: { "Change Password": 1, "Cancel": 0 },
	  close: function (e, v, m, f) {
		  
	  }
	});
}

//shows another way of doing an ajax post http://www.w3schools.com/ajax/ajax_xmlhttprequest_send.asp
/*function logout() {
	var xmlhttp;
	
	xmlhttp = new XMLHttpRequest();
	xmlhttp.open("POST", SERVER_URL + "userfunctions/logout.php",true);
 	xmlhttp.send();
}*/