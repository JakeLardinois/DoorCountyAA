<?php
require_once "../mpdf/mpdf.php";
$url = $_SERVER["HTTP_REFERER"];

// To prevent anyone else using your script to create their PDF files; NOTE i at the end of org/ makes this case insensitive!!
//if (!preg_match('/^http:\/\/www\.doorcountyaa\.org/i', $url)) { die("Access denied"); }
if (!preg_match('/^http:\/\/localhost:8080\/doorcountyaa/i', $url)) { die("Access denied"); }

// Define relative path from this script to mPDF
 //define('_MPDF_PATH','./mpdf/');
 //include(_MPDF_PATH . "mpdf.php");
 //$url = urldecode($_REQUEST['url']);
 
//outputs an image to the browser...
//$image = $_POST['image'];
//header("Content-type: image/jpg");
//echo base64_decode(str_replace('data:image/png;base64,', '', $image));

$image = $_POST['image'];
//file_put_contents('my.pdf', base64_decode(str_replace('data:image/png;base64,', '', $image)));
//header('Content-type: application/pdf');
//header('Content-Disposition: attachment; filename="filename.pdf"');

$mpdf=new mPDF(); 
//$mpdf=new mPDF('', 'Letter-L'); //creates pdf in landscape
//$mpdf=new mPDF('', 'Letter'); //creates pdf in portrait
$mpdf->WriteHTML('<img src=\''.$image.'\' />');
$mpdf->Output('MeetingsandEvents.pdf','D'); //the 'D' specifies the download http://mpdf1.com/manual/index.php?tid=125&searchstring=download
exit;

 ?>