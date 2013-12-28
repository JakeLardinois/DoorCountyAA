<?php 
require_once '../infrastructure/dbconfig.php';

$id = $_POST['id'];
$value = $_POST['value'];
$rowId = $_POST['rowId'];
$columnPosition = $_POST['columnPosition'];
$columnId = $_POST['columnId'];
$columnName = $_POST['columnName'];

echo $value;
?>