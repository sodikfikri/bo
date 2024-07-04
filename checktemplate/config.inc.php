<?php
date_default_timezone_set('Asia/Jakarta');
$dbhost = "localhost:3308";
$dbuser = "root";
$dbpass = "InterActive2323";
$dbname_primary = "inact_devicedata";
$conn_model_temp = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname_primary) or die("(i) Connection Failed: " . mysqli_connect_error());
?>