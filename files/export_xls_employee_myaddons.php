<?php 
include '../application/config/koneksi_external.php';
include '../application/libraries/Encryption_org.php';
$subsid = decode($_GET['subsid']);
function decode($value){
	if(!$value){return false;}
	$output = false;
	$encrypt_method = "AES-256-CBC";
	$secret_key = md5("ineedcoffe");
	$secret_iv = md5("ineedcafeine");
	// hash
	$key = hash('sha256', $secret_key);

	// iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	$iv = substr(hash('sha256', $secret_iv), 0, 16);

	$output = openssl_decrypt(safe_b64decode($value), $encrypt_method, $key, 0, $iv);

	return $output;
}
function safe_b64decode($string) {
	$data = str_replace(array('-','_'),array('+','/'),$string);
	$mod4 = strlen($data) % 4;
	if ($mod4) {
		$data .= substr('====', $mod4);
	}
	return base64_decode($data);
}
?>
<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<style type="text/css">
	body{
		font-family: sans-serif;
	}
	table{
		margin: 20px auto;
		border-collapse: collapse;
	}
	table th,
	table td{
		border: 1px solid #3c3c3c;
		padding: 3px 8px;
 
	}
	a{
		background: blue;
		color: #fff;
		padding: 8px 10px;
		text-decoration: none;
		border-radius: 2px;
	}
	</style>
 
	<?php
	header("Content-type: application/vnd-ms-excel");
	header("Content-Disposition: attachment; filename=MyAddons_".$subsid."_Employee.xls");
	?>
 
	<center>
		<h1>Export Data Employee <br/> Subscription ID <?= $subsid; ?></h1>
	</center>
 
	<table border="1">
		<tr>
			<th>Employee Code</th>
			<th>Name</th>
		</tr>
		<?php
		$queryneList = "SELECT * FROM tbemployee WHERE subscription_id='$subsid'";
        $qmr = mysqli_query($conn_model, $queryneList);
		while($rmr = mysqli_fetch_array($qmr)){
			$specialNumber = '`';
			if(substr($rmr['employee_account_no'],0,1)==0){
				$specialNumber = '`';
			}
			?>
			<tr>
				<td><?php echo $specialNumber.$rmr['employee_account_no']; ?></td>
				<td><?= $rmr['employee_full_name'] ?></td>
			</tr>
			<?php
		}
		?>
	</table>
</body>
</html>