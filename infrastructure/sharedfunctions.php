<?php
//localServer
//define("SERVER_URL", 'http://localhost:8080/DoorCountyAA/');	
//Remote Server
//define("SERVER_URL", './');


//I was using the DateTime DateTime::getTimestamp (http://www.php.net/manual/en/datetime.gettimestamp.php) to get my datetime from the unix timestamp that is passed to this php file.
	//This worked well on my local machine, but it threw an error on the live site since it uses an older version of php which doesn't define this function.  So I had to create some static functions
	//That will do the conversion manually
class MySharedFunctions {
	//This one returns a DateTime object from a recieved timestamp.
	public static function GetDateTimeFromTimeStamp( $timestamp )
	{
		$objDateTime = new DateTime();
		$date = getdate( ( int ) $timestamp );
		$objDateTime->setDate( $date['year'] , $date['mon'] , $date['mday'] );
		$objDateTime->setTime( $date['hours'] , $date['minutes'] , $date['seconds'] );
		return $objDateTime;
	}
	
	//This one returns a timestamp from a given DateTime object.
	public static function GetTimeStampFromDateTime($objDateTime)
	{
		return $objDateTime->format( 'U' );
	}
}


?>