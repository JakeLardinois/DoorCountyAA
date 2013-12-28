// JavaScript Document

var oTable;
//localServer
var SERVER_URL = 'http://localhost:8080/DoorCountyAA/'
//Remote Server
//var SERVER_URL = './'

$(document).ready(function () {
    var oTimerId;


    oTable = $('#objUsers').dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "sDom": 'T<"clear">Rlfrtip', //Enables column reorder with resize. 'T<"clear"> adds the 'download' button
        "oTableTools": {
            "aButtons": [
                {
                    "sExtends": "download",
                    "sButtonText": "Excel Download",
                    "sUrl": document.URL // "/generate_csv.php"
                }
            ]
        },
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
        "oLanguage": { "sSearch": "Search Users:" },
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
            { "mDataProp": "userlevel" }]
    }).makeEditable({ //I kept getting the error "Object doesn't support property or method 'live'" until I changed line 1260 of jquery.dataTables.editable.js 'live' method to 'on'
        sUpdateURL: SERVER_URL + 'userfunctions/updateuser.php', // sUpdateVRUrl,
        sAddURL: SERVER_URL + 'userfunctions/adduser.php', // sAddVRUrl,
        sDeleteURL: SERVER_URL + 'userfunctions/deleteuser.php', // sDeleteVRUrl,
		//fnOnAdding: function () {//This event gets fired right before the form is submitted to the server. I can use this time to populate the variables I need to add a new record into the database such as OrderNo, OrderLine, OrderRelease, etc.
            //In order for this to work I needed to add the columns to the datatable and then hide them, put the hiddent textboxes on the frmAddVendorRequest form with the proper rel="#" attribute (where # is the column order
            //That they were added in the column instantiation. I then populate thier values here...

            //uses the GET function defined above and retrieves the values from the querystring...
            /*var dataValues = GET("OrderNo", "LineNo", "ReleaseNo", "ItemID");
            document.getElementById('OrderNo').value = dataValues[0];
            document.getElementById('LineNo').value = dataValues[1];
            document.getElementById('ReleaseNo').value = dataValues[2];
            document.getElementById('Item').value = dataValues[3];
            document.getElementById('RequestCategoryID').value = 1;*/ //Purchase Orders have a request category of 1
			
			//$(oAddNewRowForm).validate();

            //return true;
        //},
        sAddNewRowFormId: "frmAddUser", //specifies the ID of the form that will be used to add a new row...
        "oAddNewRowFormOptions": { //These options are the same/taken from the jquery.dialog() options http://api.jqueryui.com/dialog/
            "title": "Add New User", //This can also be done by setting the attribute 'title = "Add new Vendor Request"' in the form tag
            "height": 450,
            "width": 700
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
        ]
    });

});