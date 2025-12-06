<?php

date_default_timezone_set('Asia/Manila');

$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$db = "eBarangay";

// $dbhost = "localhost";
// $dbuser = "u482770917_nnaes";
// $dbpass = "5-T79_Oo8Z";
// $db = "u482770917_nnaes";

$conn = new mysqli($dbhost, $dbuser, $dbpass, $db) or die("Connect failed: %s\n" . $conn->error);

if (!$conn) {
	die("Connection Failed. " . mysqli_connect_error());
	echo "can't connect to database";
}

function executeQuery($query)
{
	$conn = $GLOBALS['conn'];
	return mysqli_query($conn, $query);
}
?>