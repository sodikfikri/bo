<?php 
date_default_timezone_set('Asia/Jakarta');
$dbhost = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname_primary = "inact_maindb";

$conn_model = mysqli_connect($dbhost, $dbuser, $dbpass) or die("Tidak dapat konek ke database");

mysqli_select_db($conn_model, $dbname_primary) or die ("Database tidak ditemukan");

$periode_awal = "2023-01-01 00:00:00";
$periode_akhir = "2023-02-28 23:59:59";

$sql = "SELECT tbemployee.employee_account_no, tbcheckinout.checkinout_datetime, tbcheckinout.checkinout_code FROM tbcheckinout INNER JOIN tbemployee ON tbemployee.employee_id = tbcheckinout.checkinout_employee_id WHERE tbcheckinout.appid = 'IA01M5142F20210426276' AND tbcheckinout.checkinout_datetime BETWEEN '$periode_awal' AND '$periode_akhir' ORDER BY tbcheckinout.checkinout_datetime;";
$res = mysqli_query($conn_model,$sql);
while($row = mysqli_fetch_row($res)) {
   $AttendanceDate = substr($row[1],0,10);
   $AttendanceTime = substr($row[1],11,8);
   $AttendanceType = $row[2];
   echo "INSERT INTO AttendanceMachinePolling(Barcode, AttendanceDate,AttendanceTime, AttendanceType) VALUES ('$row[0]', '$AttendanceDate', '$AttendanceTime', $AttendanceType);"."<BR>";	
}
echo "Sukses periode ".$periode_awal." s/d ".$periode_akhir;

?>