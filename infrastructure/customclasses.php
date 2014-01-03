<?php

class User {
	public $userid;
    public $username;
	public $fname;
    public $lname;
	public $EmailAddress;
    public $userlevel;
	public $password;
}
/*class DateTime extends DateTime
{
    public static function setTimestamp( $timestamp )
    {
		$objDateTime = new self();
        $date = getdate( ( int ) $timestamp );
        $objDateTime->setDate( $date['year'] , $date['mon'] , $date['mday'] );
        $objDateTime->setTime( $date['hours'] , $date['minutes'] , $date['seconds'] );
		return $objDateTime;
    }

    public static function getTimestamp($objDateTime)
    {
        return $objDateTime->format( 'U' );
    }
}*/

?>