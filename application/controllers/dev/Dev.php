<?php

class Dev extends CI_Controller
{

	var $tabel_template  = array(
        'table_open'            => '<table border="1" class="table table-bordered table-stripped" id="datatable">',
        'table_close'           => '</table>'
	);

	function __construct()
	{
		parent::__construct();
	}

	function win_sys_current_cpu_usage() {
	  $cmd = 'wmic cpu get loadpercentage';
	  exec($cmd, $lines, $retval);
	  
	  if($retval == 0) {
	    
	    echo "Server CPU Usage ".$lines[1]."%";
	  } else {
	    echo false;
	  }
	}
	function scanDirDescTime($dir) {
	    $ignored = array('.', '..', '.svn', '.htaccess');

	    $files = array();    
	    foreach (scandir($dir) as $file) {
	        if (in_array($file, $ignored)) continue;
	        $files[$file] = filemtime($dir . '/' . $file);
	    }

	    asort($files);
	    $files = array_keys($files);

	    return ($files) ? $files : false;
	}
	function copyAPPIDUnknownTemplate(){
		$this->db->select("unknown_template.id");
		$this->db->select("tbdevice.device_SN");
		$this->db->select("tbdevice.appid");
		$this->db->where("unknown_template.appid","");

		$this->db->from("unknown_template");
		$this->db->join("tbdevice","tbdevice.device_SN = unknown_template.device_SN");
		$sql = $this->db->get();
		foreach($sql->result() as $row){
			$this->db->where("id",$row->id);
			$this->db->update("unknown_template",["appid"=> $row->appid]);
		}
		echo $this->db->last_query();
	}

	function index(){
		$this->load->model("employeeareacabang_model");
		$this->employeeareacabang_model->getEmployeeActiveByLocation(191,193,"IA01M6859F20210906613");		

// $curl = curl_init();

// curl_setopt_array($curl, array(
//   CURLOPT_URL => 'https://app.sandbox.midtrans.com/snap/v1/transactions',
//   CURLOPT_RETURNTRANSFER => true,
//   CURLOPT_ENCODING => '',
//   CURLOPT_MAXREDIRS => 10,
//   CURLOPT_TIMEOUT => 0,
//   CURLOPT_FOLLOWLOCATION => true,
//   CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//   CURLOPT_CUSTOMREQUEST => 'POST',
//   CURLOPT_POSTFIELDS =>'{"transaction_details":{"order_id":"SUBS\\/20211001\\/005","gross_amount":454},"item_details":[{"id":"10","price":0,"quantity":1,"name":"InterActive MyProfit"},{"id":"81","price":3,"quantity":"1","name":"BASIC(Addons)"},{"id":"uniqid","price":"451.00","quantity":1,"name":"Unique Code"},{"id":"tax","price":"0.000","quantity":1,"name":"Total Tax"},{"id":"discount","price":"-0.000","quantity":1,"name":"Total Discount"},{"id":"paymentdeposit","price":"-0.00","quantity":1,"name":"Payment from Deposit"}],"customer_details":{"first_name":"InterDev Sanjaya","last_name":"","email":"lodehmboksemah@gmail.com","phone":"+628565522222112","billing_address":{"first_name":"InterDev Sanjaya","last_name":"","email":"lodehmboksemah@gmail.com","phone":"+628565522222112","address":"Jalan Kusuma bangsa No 22","city":"","postal_code":"","country_code":""}}}',
//   CURLOPT_HTTPHEADER => array(
//     'Authorization: Basic U0ItTWlkLXNlcnZlci1FaXRhOTZDY19xbFdPZm1jMzV2Q0kxQWk=',
//     'Content-Type: application/json'
//   ),
// ));

// $response = curl_exec($curl);

// curl_close($curl);
// echo $response;
		//echo dirname(__FILE__);
		//echo $apikey = md5("aisahar".date("Ymd"));
		/*
		$folderPath = FCPATH."storage".DIRECTORY_SEPARATOR."device_raw";
		$fileList = $this->scanDirDescTime($folderPath);
		print_r($fileList);

		
		$myfile = fopen( $filePath , "r") or die("Unable to open file!");

    	$storageJson =  fread($myfile,filesize($filePath));
    	$arrConfig   = json_decode($storageJson);
		
		//$file = fopen( $path , "r") or die("Unable to open file!");;
		//$read = fread($file, filesize($path));
		
		/*
		$sql = $this->db->get("commandrequest");
		$res = $sql->row_array();
		
		$this->load->library("machinepost_reader");
		$postData = file_get_contents("php://input");
		$result = $this->machinepost_reader->readAttLog($postData);
		print_r($result);
		
		$nowHours  = "23:00";

		$openHours = "23:00";
		$closeHours= "09:00";


		$timeOpen  = strtotime($openHours);
		$timeClose = strtotime($closeHours);
		$timeNow   = strtotime($nowHours);

		if($timeNow>$timeClose && $timeNow<$timeOpen){
		}else{
			echo "open it!";
		}

		
		$filePath = FCPATH."application".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."base_config.json";
    	$myfile = fopen( $filePath , "r") or die("Unable to open file!");
   		$storageJson =  fread($myfile,filesize($filePath));
		$arrConfig   = json_decode($storageJson);
		echo $arrConfig->use_firewall;
		
		$this->load->helper("timezone_helper");
		$timezone = getTimezone();
		foreach ($timezone as $zone) {
			$default = $zone;
			$zone = str_replace(')','',str_replace('(','',explode(" ",$zone)[0]));
			$zone = str_replace("UTC","", $zone);
			if(substr($zone, 0,1)=="+"){
				$zone = substr($zone, 1);
				if(substr($zone, 0,1)==0){
					$zone = substr($zone, 1);
				}
				$arrTime = explode(":", $zone);
				if($arrTime[1]=="00"){
					$zone = $arrTime[0];
				}else{
					$zone = $arrTime[0].".".$arrTime[1];
				}
			}else{
				$zone = substr($zone, 1);
				if(substr($zone, 0,1)==0){
					$zone = substr($zone, 1);
				}
				$arrTime = explode(":", $zone);
				if($arrTime[1]=="00"){
					$zone = $arrTime[0];
				}else{
					$zone = $arrTime[0].".".$arrTime[1];
				}
				$zone = "-".$zone;
			}
			echo $default."=>".$zone.'<br>';
		}
		/*
	  	$myfile = fopen("storage/emp_templatewajah_4001.txt", "r") or die("Unable to open file!");
		$str = fread($myfile,filesize("storage/emp_templatewajah_4001.txt"));
		$arrRow = preg_split("/[\r\n]/",$str);
		print_r($arrRow);
		fclose($myfile);
		*/
	}

	function maintenance(){
		$query = "select 
					tbemployee.employee_id,
					tbemployee.employee_account_no,
					tbemployee.employee_full_name,
					tbemployee.employee_resign_date,
					tbemployee.is_del,
					tbarea.area_name,
					tbcabang.cabang_name,
					tbdevice.device_SN
					from 
					tbemployee 
					left join tbemployeeareacabang on tbemployeeareacabang.employeeareacabang_employee_id = tbemployee.employee_id
					left join tbemployeelocationdevice on tbemployeelocationdevice.employeeareacabang_id = tbemployeeareacabang.employeeareacabang_id
					left join tbarea on tbarea.area_id = tbemployeeareacabang.employee_area_id
					left join tbcabang on tbcabang.cabang_id = tbemployeeareacabang.employee_cabang_id
					left join tbdevice on tbdevice.device_id = tbemployeelocationdevice.device_id
					where
					tbemployee.appid = 'IA01M3375F20191126388'
					and 
					tbemployee.employee_account_no in ('2084','2085','2087','2089','2091','2092','2093','2094','2095','2096','2097','2098','2101','2102','2103','2104','2105','2106','2107','2108','2109','2118','2119','2120','2121','2124','2125','2126','2127','2129','2130','2133','2138','2139','2145','2146','2147','2148','2149','2150','2151','2152','2153','2154','2155','2158','2159','2160','2161','2162','2163','2164','2165','2166','2167','2168','2169','2170','2171','2172','2173','2174','2175','2176','2177','2178','2179','2180','1550','2181','2182','2183','2184','2185','2186','2187','2188','2189','225'
					)";
		$sql = $this->db->query($query);
		$this->table->set_template($this->tabel_template);

		$this->table->set_heading(
			"No Akun",
			"Name",
			"Date Resign",
			"Area Name",
			"Cabang Name",
			"SN",
			"Is Del"
		);

		foreach ($sql->result() as $row) {
			$this->table->add_row(
				$row->employee_account_no,
				$row->employee_resign_date,
				$row->area_name,
				$row->cabang_name,
				$row->device_SN,
				$row->is_del
			);
		}

		echo $this->table->generate();
	}
	
	/*
	function showDuplicateCheckInOut(){
	  	$sql1 = $this->db->query("SELECT 
			    appid, COUNT(appid),
			    checkinout_employee_id, COUNT(checkinout_employee_id),
			    checkinout_datetime, COUNT(checkinout_datetime),
			    checkinout_verification_mode, COUNT(checkinout_verification_mode),
			    checkinout_code, COUNT(checkinout_code),
			    checkinout_device_id, COUNT(checkinout_device_id)
			 
			FROM
			    tbcheckinout
			GROUP BY 
			    appid,
				checkinout_employee_id,
				checkinout_datetime,
				checkinout_verification_mode,
				checkinout_code,
				checkinout_device_id
			HAVING 
			    (COUNT(appid) > 1) AND 
			    (COUNT(checkinout_employee_id) > 1) AND
			    (COUNT(checkinout_datetime) > 1) AND 
			    (COUNT(checkinout_verification_mode) > 1) AND
			    (COUNT(checkinout_code) > 1) AND 
			    (COUNT(checkinout_device_id) > 1)
	    ");
	  	$this->table->set_template($this->tabel_template);
	  	$this->table->set_heading(
	  		"ID",
	  		"APP ID",
	  		"employee ID",
	  		"Date Time",
	  		"verification Mode",
	  		"Checkinout Code",
	  		"Device ID",
	  		"Delete"
	  	);

	    foreach ($sql1->result() as $row) {
	    	
	    	$appid      = $row->appid;
	    	$employeeId = $row->checkinout_employee_id;
	    	$dateTime   = $row->checkinout_datetime;
	    	$verifMode  = $row->checkinout_verification_mode;
	    	$checkCode  = $row->checkinout_code;
	    	$deviceID   = $row->checkinout_device_id;
	    	
	    	$where = [
	    		"appid" => $appid,
				"checkinout_employee_id" => $employeeId,
				"checkinout_datetime" => $dateTime,
				"checkinout_verification_mode" => $verifMode,
				"checkinout_code" => $checkCode,
				"checkinout_device_id" => $deviceID
	    	];
	    	$sql2 = $this->db->get_where("tbcheckinout",$where);
	    	$countData = 0;

	    	foreach ($sql2->result() as $map) {
	    		$countData++;
	    		
	    		if($countData>1){
	    			$delete = '<a href="'.base_url("dev/dev/deleteCheckInOut/".$map->checkinout_id).'">Delete Duplicate</a>';
	    		}else{
	    			$delete = '';
	    		}

	    		$this->table->add_row(
	    			$map->checkinout_id,
	    			$map->appid,
	    			$map->checkinout_employee_id,
	    			$map->checkinout_datetime,
	    			$map->checkinout_verification_mode,
	    			$map->checkinout_code,
	    			$map->checkinout_device_id,
	    			$delete
	    		);
	    	}
	    }
	    echo $this->table->generate();
	}

	function deleteCheckInOut($id){
		$this->db->where("checkinout_id",$id);
		$this->db->delete("tbcheckinout");
		redirect("dev/dev/showDuplicateCheckInOut");
	}

	*/
}
