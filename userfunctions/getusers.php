<?php
	require_once '../infrastructure/dbconfig.php';
	require_once '../infrastructure/customclasses.php';
	
	
	//This prevents an unauthenticated user from getting a list of users
	if(empty($_SESSION['LoggedIn']) && empty($_SESSION['Username'])){exit();}
	if($_SESSION['auth'] < 2){exit();} //you must have access greater than 2 to view users
	
    /*
     * Script:    DataTables server-side script for PHP and MySQL
     * Copyright: 2010 - Allan Jardine, 2012 - Chris Wright
     * License:   GPL v2 or BSD (3-point)
     */
     
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * Easy set variables
     */
     
    /* Array of database columns which should be read and sent back to DataTables. Use a space where
     * you want to insert a non-database field (for example a counter or static image)
     */
    $aColumns = array( 'userid', 'username', 'fname', 'lname', 'EmailAddress', 'userlevel' );
     
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "userid";
     
    /* DB table to use */
    $sTable = "users";
     
    /* Database connection information */
    $gaSql['user']       = mysql_username;
    $gaSql['password']   = mysql_password;
    $gaSql['db']         = mysql_dbname;
    $gaSql['server']     = mysql_hostname;
     
     
    /* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
     * If you just want to use the basic configuration for DataTables with PHP server-side, there is
     * no need to edit below this line
     */
     
    /* 
     * Local functions
     */
    function fatal_error ( $sErrorMessage = '' )
    {
        header( $_SERVER['SERVER_PROTOCOL'] .' 500 Internal Server Error' );
        die( $sErrorMessage );
    }
 
     
    /* 
     * MySQL connection
     */
    if ( ! $gaSql['link'] = mysql_pconnect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) )
    {
        fatal_error( 'Could not open connection to server' );
    }
 
    if ( ! mysql_select_db( $gaSql['db'], $gaSql['link'] ) )
    {
        fatal_error( 'Could not select database ' );
    }
     
     
    /* 
     * Paging
     */
    $sLimit = "";
    if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
    {
        $sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".
            intval( $_GET['iDisplayLength'] );
    }
     
     
    /*
     * Ordering
     */
    $sOrder = "";
    if ( isset( $_GET['iSortCol_0'] ) )
    {
        $sOrder = "ORDER BY  ";
        for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
        {
            if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
            {
                $sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
                    ".($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
            }
        }
         
        $sOrder = substr_replace( $sOrder, "", -2 );
        if ( $sOrder == "ORDER BY" )
        {
            $sOrder = "";
        }
    }
     
     
    /* 
     * Filtering
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
     */
    $sWhere = "";
    if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" )
    {
        $sWhere = "WHERE (";
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" )
            {
                $sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
            }
        }
        $sWhere = substr_replace( $sWhere, "", -3 );
        $sWhere .= ')';
    }
     
    /* Individual column filtering */
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
        {
            if ( $sWhere == "" )
            {
                $sWhere = "WHERE ";
            }
            else
            {
                $sWhere .= " AND ";
            }
            $sWhere .= $aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
        }
    }
     
     
    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "
        SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
        FROM   $sTable
        $sWhere
        $sOrder
        $sLimit
    ";
    $rResult = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
     
    /* Data set length after filtering */
    $sQuery = "
        SELECT FOUND_ROWS()
    ";
    $rResultFilterTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
    $aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $aResultFilterTotal[0];
     
    /* Total data set length */
    $sQuery = "
        SELECT COUNT(".$sIndexColumn.")
        FROM   $sTable
    ";
    $rResultTotal = mysql_query( $sQuery, $gaSql['link'] ) or fatal_error( 'MySQL Error: ' . mysql_errno() );
    $aResultTotal = mysql_fetch_array($rResultTotal);
    $iTotal = $aResultTotal[0];
     
     
    /*
     * Output
     */
    $output = array(
        "sEcho" => intval($_GET['sEcho']), // isset($_GET['sEcho']) ? intval($_GET['sEcho']) : 1, //I was POSTing to this form when I should 
        "iTotalRecords" => $iTotal,			//have been using GET. I left the condensed if commented out for reference...
        "iTotalDisplayRecords" => $iFilteredTotal,
        "aaData" => array()
    );
     
	
    while ( $aRow = mysql_fetch_array( $rResult ) )
    {
		//I'm accustomed to populating custom object like such, however php uses late binding so I can populate this object in the below loop..
		/*$objUser = new User();
		$objUser->userid = $aRow[ $aColumns[0] ];
		$objUser->username = $aRow[ $aColumns[1] ];
		$objUser->fname = $aRow[ $aColumns[2] ];
		$objUser->lname = $aRow[ $aColumns[3] ];
		$objUser->EmailAddress = $aRow[ $aColumns[4] ];
		$objUser->userlevel = $aRow[ $aColumns[5] ];*/
		
		$objUser = new User();
		for ( $i=0 ; $i < count($aColumns) ; $i++ )
        {
			if ( $aColumns[$i] == "version" )//left here for reference in case sometime I want something done to a particular column
            {
                /* Special output formatting for 'version' column */
                //$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : $aRow[ $aColumns[$i] ];
            }
            else if ( $aColumns[$i] != ' ' )
            {
				/*notice how this uses late binding so that I am actually calling on the property of my object via variable!
					this is the .Net equivalent to reflection or something...*/
				$objUser->$aColumns[$i] = $aRow[ $aColumns[$i] ];
            }
		}
		
        array_push($output['aaData'], $objUser);//add the object to the array of users...
    }
     
    echo json_encode( $output );
?>
