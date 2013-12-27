// JavaScript Document

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