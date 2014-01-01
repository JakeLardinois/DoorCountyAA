function submitMonthYear() {
	document.monthYear.method = "post";
	document.monthYear.action = 
		"events.php?month=" + document.monthYear.month.value +
		"&year=" + document.monthYear.year.value;
	document.monthYear.submit();
}

function postMessage(day, month, year) {
	eval(
	"page" + day + " = window.open('eventform.php?d=" + day + "&m=" + month + 
	"&y=" + year + "', 'postScreen', 'toolbar=0,scrollbars=1,location=0," +
	"statusbar=0,menubar=0,resizable=1,width=340,height=400');"
	);
}

function openPosting(pId) {
	eval(
	"page" + pId + " = window.open('eventdisplay.php?id=" + pId + 
	"', 'mssgDisplay', 'toolbar=0,scrollbars=1,location=0,statusbar=0," +
	"menubar=0,resizable=1,width=340,height=400');"
	);
}

function loginPop(month, year) {
	eval("logpage = window.open('login.php?month=" + month + "&year=" + year + 
	"', 'mssgDisplay', 'toolbar=0,scrollbars=1,location=0,statusbar=0," +
	"menubar=0,resizable=1,width=340,height=400');"
	);
}
