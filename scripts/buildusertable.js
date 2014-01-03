// JavaScript Document
//localServer
var SERVER_URL = 'http://localhost:8080/DoorCountyAA/'
//Remote Server
//var SERVER_URL = './'

var oTable;



$(document).ready(function () {
    var oTimerId;


    oTable = $('#objUsers').dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "sDom": 'Rrtip', 
        "sScrollX": "100%",
        "bJQueryUI": true,
        "sPaginationType": "full_numbers",
        "sAjaxSource": SERVER_URL + 'userfunctions/getusers.php',
        "sServerMethod": "POST",
        "fnServerData": function (sSource, aoData, fnCallback, oSettings) {

            window.clearTimeout(oTimerId);
            oTimerId = window.setTimeout(function () {
                oSettings.jqXHR = $.ajax({
                    "dataType": 'json',
                    "type": "GET", // "POST",
                    "url": sSource,
                    "data": aoData,
                    "success": fnCallback
                });
            }, 1000)
        },
        "aoColumns": [
            { 
			  "mDataProp": null,
			  "sDefaultContent": "ID",
			  "fnRender": function (oObj) {	//I needed to do this in order to set the id property that is sent to the makeeditable() urls below
				  return oObj.aData["userid"]; //accessing the ID column of the specific row
			  }
			},
            { "mDataProp": "username" },
            { "mDataProp": "fname" },
            { "mDataProp": "lname" },
            { "mDataProp": "EmailAddress" },
            { "mDataProp": "userlevel" },
            { "mDataProp": "password" }]
    }).makeEditable({ //I kept getting the error "Object doesn't support property or method 'live'" until I changed line 1260 of jquery.dataTables.editable.js 'live' method to 'on'
        sUpdateURL: SERVER_URL + 'userfunctions/updateuser.php', // sUpdateVRUrl,
        sAddURL: SERVER_URL + 'userfunctions/adduser.php', // sAddVRUrl,
        sDeleteURL: SERVER_URL + 'userfunctions/deleteuser.php', // sDeleteVRUrl,
        sAddNewRowFormId: "frmAddUser", //specifies the ID of the form that will be used to add a new row...
        "oAddNewRowFormOptions": { //These options are the same/taken from the jquery.dialog() options http://api.jqueryui.com/dialog/
            "title": "Add New User", //This can also be done by setting the attribute 'title = "Add new Vendor Request"' in the form tag
            "height": 370,
            "width": 600
        },
        aoColumns: [
            null,
            {},
            {},
			{},
			{},
			{
                type: 'select',
                onblur: 'submit',  //values can be "ignore", "submit", or "cancel"
                data: "{'0':'0', '1':'1', '2':'2'}", 
                //data: RequestCategories()
                //submit: 'Save changes'//shows a submit button
            },
			{},
        ]
    });

});