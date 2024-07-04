<?php 
/**
 * 
 */
class Queue extends CI_Controller
{
	var $now;
	function __construct()
	{
		parent::__construct();
		//$this->load->library("file_library");
		$this->now = date("Y-m-d H:i:s");
	}

	function processQueue(){
		$dir 		= FCPATH."storage".DIRECTORY_SEPARATOR."device_raw".DIRECTORY_SEPARATOR;
		$newDir 	= FCPATH."storage".DIRECTORY_SEPARATOR."device_raw_has_processed".DIRECTORY_SEPARATOR;
		$arrFile 	= $this->file_library->scanDirDescTime($dir);

		if ($arrFile!=false) {
			$fileName = $dir.$arrFile[0];
        	if (file_exists($fileName)) {
        		$myfile = fopen( $fileName , "r") or die("Unable to open file!");
			    $storageJson =  fread($myfile,filesize($fileName));
			    $arrData     = (json_decode($storageJson));
			    
			    
			    $SN 		= $arrData->get->SN;
			    $get 		= $arrData->get;
			    $table 		= !empty($arrData->get->table) ? $arrData->get->table : "";
			    $tableName 	= !empty($arrData->get->tablename) ? $arrData->get->tablename : "";
			    $dataPost   = $arrData->post;
			    $result 	= $this->cdataProcess($SN,$get,$dataPost,$table,$tableName);
			    fclose($myfile);
			    if($result==true){
			    	//unlink($fileName);
			    	rename($fileName, $newDir.$arrFile[0]);
			    	echo "OK";
			    }
        	}
	    }
	}

	function readFromLocal(){
	    $filePath = FCPATH."application".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."base_config.json";
	    $myfile = fopen( $filePath , "r") or die("Unable to open file!");
	    $storageJson =  fread($myfile,filesize($filePath));
	    fclose($myfile);
	    return $storageJson;
  	}
  	
  	function newConnection(){
  		$servername = "localhost";
		$username = "root";
		$password = "root";
		try {
			$conn = new PDO("mysql:host=$servername;dbname=inact_devicedata", $username, $password);
			// set the PDO error mode to exception
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			// echo "Connected successfully";
			return $conn;
		} catch(PDOException $e) {
			// echo "Connection failed: " . $e->getMessage();
		}
  	}
	
	function pushAttEmployee($appid=null){
		$this->load->model("employee_model");
		$this->load->model("inoutmobile_model");
		if(!empty($appid)){
			$dataInout = $this->inoutmobile_model->getAllDataPush($appid);
			$arr=[];
			foreach ($dataInout as $row) {
				
			  $employeeData = $this->employee_model->getById($row->employee_id,$appid);
			  $employee_id		= $employeeData->employee_id;
			  $checklog_id		= $employeeData->employee_account_no;
			  $checklog_date	= $row->checklog_date;
			  $checklog_event	= $row->checklog_event;
			  $checklog_longitude= $row->checklog_longitude;
			  $checklog_latitude= $row->checklog_latitude;
			  
			  if($checklog_event=='CheckIn'){
				$checklog = 'in';
			  } elseif($checklog_event=='CheckOut'){
				$checklog = 'out';
			  }
			  //$url = 'http://202.67.9.84/api/absen/transaksi/save';
			  $url = 'https://apimws.malukuprov.go.id/api/absen/transaksi/save';
			  $ch = curl_init($url);
			  $data = array(
				'id_orang' => 'intrax_mobile',
				'nip' => $checklog_id,
				'tanggal' => date_format(date_create($checklog_date),"Y/m/d"),
				'jenis_absen' => 'mobile',
				'jenis_waktu_absen' => $checklog,
				'waktu' => date_format(date_create($checklog_date),"Y/m/d H:i:s"),
				'longitude' => $checklog_longitude,
				'latitude' => $checklog_latitude
			  );
			  $payload = json_encode($data);
			  curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
			  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			  //curl_setopt($ch, CURLOPT_TIMEOUT_MS, 3000);
			  $result = curl_exec($ch);
			  $arr[] = json_decode($result, true);
			  $arrRespon = json_decode($result, true);
			  if($arrRespon['pesan']=="Berhasil" || $arrRespon['pesan']=="Data Sudah Ada"){
				$this->inoutmobile_model->update($employee_id,$checklog_date);
			  }
			  curl_close($ch);			  
			}
			$arrOutput = [
				'result' 		=> true,
				'message' 		=> "succesfully push data.",
				'data' 			=> $arr
			  ];
		} else {
			$arrOutput = [
                'result' 		=> false,
                'message' 		=> "appid is not defined"
            ];
		}
		header("Content-Type:application/json");
		echo json_encode($arrOutput);
	}

	function processQueueDB($executeType=null){
		$this->load->library("dbconnection");
		$cfg = $this->readFromLocal();
		$cfg = json_decode($cfg);
		$processCount = !empty($cfg->processCount) ? $cfg->processCount : 1;
		$conn = $this->dbconnection->connect();
		if($executeType=="attendance"){
			$sql = $conn->prepare("select * from inact_devicedata.raw_data where isProcessed='no' and `post` !='' and (`get` like '%ATTLOG%' OR `get` like '%rtlog%') order by `datetime` ASC limit ".$processCount." ", array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
		} elseif($executeType=="specificattendance"){
			$sql = $conn->prepare("select * from inact_devicedata.raw_data where isProcessed='no' and post !='' and (`get` like '%ATTLOG%' or `get` like '%rtlog%') and (`get` like '%CQIM232460042%' or `get` like '%CQIM232460047%' or `get` like '%CQIM232460049%' or `get` like '%CQIM232460040%') order by `datetime` ASC limit ".$processCount." ",
			array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
		}else{
			/*
			$sql = $conn->prepare("select * 
			from inact_devicedata.raw_data 
			where isProcessed='no' 
			and (`get` like '%CL9M205160766%' or `get` like '%CL9M205160781%' or `get` like '%CL9M205160273%' or `get` like '%CL9M212260073%')
			and (`get` not like '%ATTLOG%' and `get` not like '%rtlog%')
			and post !='' 
			order by `datetime` ASC
			limit ".$processCount." ",
			array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
			*/
			
			$sql = $conn->prepare("select * from inact_devicedata.raw_data where isProcessed='no' and (`get` not like '%ATTLOG%' and `get` not like '%rtlog%') and post !='' order by `datetime` ASC limit ".$processCount." ",
			array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
		}
		$sql->execute();

		$fetchData = $sql->fetchAll();

		if(count($fetchData)>0){

			$dataUpdate = [];
			$arrId      = [];
			foreach ($fetchData as $data) {
				
				$dataGet    = json_decode($data["get"]);
				
				$SN 		= $dataGet->SN;
				$get 		= $dataGet;
				$table 		= !empty($dataGet->table) ? $dataGet->table : "";
				$tableName 	= !empty($dataGet->tablename) ? $dataGet->tablename : "";
				$dataPost   = $data["post"];
				$result 	= $this->cdataProcess($SN,$get,$dataPost,$table,$tableName);

				if ($result==true) {
					$arrId[] = $data["id"];
				}
			}
			// print_r($arrId);
			// exit;
			if(count($arrId)>0){
				$strId = implode(',', $arrId);
				
				$sql = $conn->prepare("delete from raw_data where id in (".$strId.")");
				$res = $sql->execute();
				if($res==true){
					echo "OK";
				}
				
			}
		}else{
			echo "noqueue";
		}
		$conn = null;
	}
	
	function openFirewall($appid){
		load_model(['firewall_model']);
        $response = $this->firewall_model->openByAppid($appid);
		if($response==true){
			echo "Success update firewall";
		}
	}

	private function cdataProcess($SN,$get,$dataPost,$table,$tablename)
  	{
    	load_model(['device_model','deviceshipments_model']);
      	$exists   = $this->device_model->checkMachineExist($SN);

      	// echo $this->db->last_query();
		// var_dump($exists);
      	if($exists!=false){
        	$arrGet = $get;

        	if($exists->response_code==3){

          		// kondisi jika mesin pakek thermo attendance
          		$this->load->model("thermoattendance_model");
          		$appID = $exists->appid;
          		$dataCount = !empty($arrGet->count) ? $arrGet->count : 0;
          		$response = $this->thermoattendance_model->procedCdata($dataPost,$exists,$table,$tablename,$dataCount);
          		if ($response!="") {
          			return true;
          		}
          	}elseif($exists->response_code==4){
          		$this->load->model("thermoattendance_model");
          		$appID = $exists->appid;
          		$dataCount = !empty($arrGet->count) ? $arrGet->count : 0;
          		$response = $this->thermoattendance_model->procedCdata($dataPost,$exists,$table,$tablename,$dataCount);
          		if ($response!="") {
          			return true;
          		}
          		/*
          		$this->load->model("attendance04_model");
		        $appID = $exists->appid;
		        $dataCount = !empty($arrGet->count) ? $arrGet->count : 0;
		        $response = $this->attendance04_model->procedCdata($dataPost,$exists,$table,$tablename,$dataCount);
		          
		        $dataShipment = [
		            "post"     => $dataPost,
		            "SN"       => $SN,
		            "appid"    => (!empty($exists->appid) ? $exists->appid : ''),
		            "endpoint" => "cdata",
		            "method"   => "post",
		            "get" => $get
		        ];

		        //if($SN=="CKUH202361603"){
		        $this->deviceshipments_model->insert($dataShipment);
		        //}

		        echo "OK";
				*/
        	}elseif($exists->response_code==5) {
				$this->load->model("thermoattendance_model");
          		$appID = $exists->appid;
          		$dataCount = !empty($arrGet->count) ? $arrGet->count : 0;
          		$response = $this->thermoattendance_model->procedCdata($dataPost,$exists,$table,$tablename,$dataCount);
          		if ($response!="") {
          			return true;
          		}
			}elseif($exists->response_code==6) {
				$this->load->model("thermoattendance_model");
          		$appID = $exists->appid;
          		$dataCount = !empty($arrGet->count) ? $arrGet->count : 0;
          		$response = $this->thermoattendance_model->procedCdata($dataPost,$exists,$table,$tablename,$dataCount);
          		if ($response!="") {
          			return true;
          		}
			} else{

        		// print_r($exists);
          		// create device shipment        
          		$appid    = $exists->appid;
          		///
          		$postIdentify = $this->postIdentify($dataPost);

          		if($postIdentify=="attendance"){
            
            		$this->load->model("checkinout_model");
					$this->load->library("dbconnection");
					$this->load->model("area_model");
					$this->load->model("cabang_model");
					$this->load->model("employee_model");
            		$arrayAttendance = $this->prepareAttendanceToArray($dataPost,$appid,$SN,$exists->device_id,$exists->device_area_id,$exists->device_cabang_id);

            		if(count($arrayAttendance)>0){

              			$insertAttendance = $this->checkinout_model->bulk_insert($arrayAttendance);
              			if($insertAttendance==true){
							$conn = $this->dbconnection->connect();
							foreach($arrayAttendance as $row){
								$query = "insert into tbfinal_checkinout SET
								appid='".$row["appid"]."',
								area_id='".$row['checkinout_area_id']."',
								area_name='".$this->area_model->getName($row['checkinout_area_id'])."',
								cabang_id='".$row['checkinout_cabang_id']."',
								cabang_name='".$this->cabang_model->getName($row['checkinout_cabang_id'])."',
								sn='".$SN."',
								employee_id='".$row['checkinout_employee_id']."',
								account_no='".$row['checkinout_employeecode']."',
								name='".$this->employee_model->getName($row['checkinout_employee_id'])."',
								datetime='".$row['checkinout_datetime']."',
								absen_code='".$row['checkinout_code']."',
								verify_code='".$row['checkinout_verification_mode']."'
								";
								$sql = $conn->prepare($query);
								$sql->execute();
							}
							return true;
              			}
            		}else{
              			return true;
            		}
         		}elseif ($postIdentify=="adduser") {
            		$this->load->library("machinepost_reader");
            		$arrUser    = $this->machinepost_reader->readUser($appid,$dataPost);
            		$userUpdate = [];

            		if(count($arrUser)>0){
              			$arrEmployeeID = [];
              			foreach ($arrUser as $row) {
                			$userUpdate[] = [
                  				"employee_id" => $row["employee_id"],
			                  	"employee_password" => $row["password"],
			                  	"employee_card" => $row["card"]
                			];
                			if(!in_array($row["employee_id"],$arrEmployeeID)){
                  				$arrEmployeeID[] = $row["employee_id"];
                			}
              			}
              			$this->load->model("employee_model");
              			$this->load->model("employeelocationdevice_model");
              			// update data user
              			$this->employee_model->update_batch($userUpdate,"employee_id");

              			// set need update di tiap location device
              			foreach ($arrEmployeeID as $employeeID) {
                			$this->employeelocationdevice_model->setNeedUpdate([$employeeID],"yes");
              			}
              			return true;
            		}else{
              			return true;
            		}
          		}elseif($postIdentify=="template_fp"){
		            $this->load->library("machinepost_reader");
		            $this->load->model("employeetemplate_model");
		            $this->load->model("employeelocationdevice_model");
		            $this->load->model("firewall_model");
		           $arrTemplate = $this->machinepost_reader->readFingerprint($dataPost,$appid);
					
					$arrTmp = explode("\n",$dataPost);
					$arrEmployeeID = [];
					// if(count($arrTmp)>15){
					// 	$arrInsert = array();
					// 	foreach($arrTmp as $rowPost){
					// 		if($rowPost!=''){
					// 			array_push($arrInsert,array(
					// 				"get"         => json_encode($get),
					// 				"post"		  => $rowPost,
					// 				"datetime"    => date("Y-m-d H:i:s"),
					// 				"isProcessed" => "no"
					// 			));
					// 		}
					// 	}
					// 	$this->db->insert_batch("inact_devicedata.raw_data",$arrInsert);
					// }else{
						foreach ($arrTemplate as $row) {
							$row["employeetemplate_employee_id"];

							if($row["employeetemplate_employee_id"]==0){
								// $this->load->model("unknowntemplate_model");
								// $dataUnknown = [
								// 	"appid" => $appid,
								// 	"device_SN" => $SN,
								// 	"pin" => $row["pin"],
								// 	"type" => $row["employeetemplate_jenis"],
								// 	"template_index" => $row["employeetemplate_index"],
								// 	"template" => $row["employeetemplate_template"]
								// ];
								//$this->unknowntemplate_model->insert($dataUnknown);
							}else{
								unset($row["pin"]);
								$employeeid = $row["employeetemplate_employee_id"];
								$index      = $row["employeetemplate_index"];
								$jenis      = $row["employeetemplate_jenis"];
								
								// cek apakah sudah pernah melakukan perekaman jari
								$this->employeetemplate_model->replace($row);
								$templateExist = $this->employeelocationdevice_model->checkTemplateExists($employeeid,$index,$jenis);
								
								//$this->firewall_model->openGate($employeeid);
								
								if($templateExist){
									//$this->employeelocationdevice_model->rePushTemplate([$employeeid],$templateExist);
								}
								
							}
						}
					//}
					return true;
					
          		}elseif($postIdentify=="template_face"){
		            $this->load->library("machinepost_reader");
		            $this->load->model("employeetemplate_model");
		            $this->load->model("employeelocationdevice_model");
		            $this->load->model("firewall_model");
		            
		            $arrTemplate = $this->machinepost_reader->readFace($exists->appid,$dataPost);
		            
					$arrEmployeeID = [];
		            foreach ($arrTemplate as $row) {
						if($row["employeetemplate_employee_id"]==0){
							$this->load->model("unknowntemplate_model");
							$dataUnknown = [
								"appid" => $appid,
								"device_SN" => $SN,
								"pin" => $row["pin"],
								"type" => $row["employeetemplate_jenis"],
								"template_index" => $row["employeetemplate_index"],
								"template" => $row["employeetemplate_template"]
							];
							$this->unknowntemplate_model->insert($dataUnknown);
						}else{
							unset($row["pin"]);
							$this->employeetemplate_model->replace($row);
							//$this->firewall_model->openGate($row["employeetemplate_employee_id"]);

							// set need Update
							$templateID = $this->employeetemplate_model->getTemplateID($row["employeetemplate_employee_id"],$row["employeetemplate_index"],$row["employeetemplate_jenis"]);
							
							if($templateID){
								$this->employeelocationdevice_model->rePushTemplate([$row["employeetemplate_employee_id"]],$templateID);
							}
						}
		            }

		            return true;
					
		        }elseif ($postIdentify=="sayhello") {
		            return true;
		        }elseif ($postIdentify=="face_pic") {
		            $this->load->library("machinepost_reader");
		            $this->load->model("firewall_model");

		            load_model(['employee_model']);
		            $arrPicture = $this->machinepost_reader->readProfileImage($exists->appid,$dataPost);
		            $updatePic  = [];
		            $needUpdatePic = [];
		            foreach ($arrPicture as $row) {
		              // save to file
		              $image      = base64_decode($row['employee_image']);
		              $imageName  = $row['file_name'];
		              $employeeID = $row['employee_id'];

		              $rowLastDetail = $this->employee_model->getSpecifiedDetailEmployee($employeeID,["image"],$exists->appid);

		              // delete last image;
		              if($rowLastDetail!=false){
		                if(!empty($rowLastDetail->image)){
		                  $filePath = FCPATH.'sys_upload\employeepic\\'.$rowLastDetail->image;
		                  if(file_exists($filePath)){
		                    unlink($filePath);
		                  }
		                }
		              }
		              // create image
		              file_put_contents('./sys_upload/employeepic/'.$imageName, $image);

		              $updatePic[] = [
		                "picture" => "", // picture dikosongkan
		                "image" => $imageName,
		                "employee_id" => $employeeID
		              ];

		              $needUpdatePic[] = [
		                "employee_id" => $employeeID,
		                "pic_need_update" => "yes"
		              ];
		              $this->firewall_model->openGate($employeeID);
		            }
		            
		            if(count($updatePic)>0){
		              $this->employee_model->update_batch($updatePic,"employee_id");
		            }

		            if(count($needUpdatePic)){
		              load_model(["employeelocationdevice_model"]);

		              $this->employeelocationdevice_model->update_batch($needUpdatePic,"employee_id");
		            }
		            return true;
          		}else{
            		// menolak semua selain type kondisi
            		return true;
          		}
        	}
      	}else{
      		return true;
      	}
  	}
	
	function prepareAttendanceToArray($dataPost,$appid,$sn,$deviceID,$areaid,$cabangid){

	    $this->load->model("employee_model");
	    $output = [];
	    $arrRow = preg_split("/[\r\n]/",$dataPost);
	    $arrEmployeeID = $this->employee_model->getAllEmployeeCode($appid);

	    foreach ($arrRow as $row) {
	      	if($row!=""){
	        	$arrField = preg_split("/[\t]/", $row);
	        	$employeeCode  = $arrField[0];
	        	$checkDateTime = $arrField[1];

	        	$employeeID = !empty($arrEmployeeID[$employeeCode]) ? $arrEmployeeID[$employeeCode] : 0;
	        	// jika tidak ditemukan employeecode maka data akan diabaikan
	        	if(!empty($arrEmployeeID[$employeeCode]) && strlen($arrField[1])>=19){
	          		$output[] = [
			            "appid" => $appid,
			            "checkinout_employee_id"  => $employeeID,
			            "checkinout_employeecode" => $arrField[0],
			            "checkinout_datetime"     => $arrField[1],
			            "checkinout_code"         => $arrField[2],
			            "checkinout_verification_mode" => $arrField[3],
			            "checkinout_device_id"    => $deviceID,
			            "checkinout_SN"           => $sn,
			            "checkinout_date_create"  => $this->now,
			            "checkinout_area_id"      => $areaid,
			            "checkinout_cabang_id"    => $cabangid
	          		];
	        	}
	      	}
	    }
	    return $output;
  	}
  	private function checkStringExists($param,$postdata){
    	preg_match("/".$param."/",$postdata,$matches);
    	if(count($matches)>0){
	    	return true;
    	}else{
       		return false;
    	}
  	}

  	private function postIdentify($string){
    	if($this->checkStringExists("USER PIN",$string)==true){
      		$output = 'adduser';
    	}elseif($this->checkStringExists("OPLOG",$string)==true){
      		$output = 'sayhello';
    	}elseif ($this->checkStringExists("FP PIN",$string)==true && $this->checkStringExists("TMP",$string)==true && $this->checkStringExists("FID",$string)==true) {
      		$output = 'template_fp';
    	}elseif ($this->checkStringExists("FACE PIN",$string)==true && $this->checkStringExists("TMP",$string)==true && $this->checkStringExists("FID",$string)==true) {
      		$output = 'template_face';
    	}elseif ($this->checkStringExists("USERPIC",$string)==true && $this->checkStringExists("FileName",$string)==true && $this->checkStringExists("Content",$string)==true) {
      		$output = 'face_pic';
    	}else{
      		$output = 'attendance';
    	}
    	return $output;
  	}

	function createTemporaryQueue($file){
		$dir 		= FCPATH."storage".DIRECTORY_SEPARATOR."tempQueue.json";
		
	}

	function processQueueDBTemplate($executeType=null){
		$this->load->library("dbconnection");
		$cfg = $this->readFromLocal();
		$cfg = json_decode($cfg);
		$processCount = !empty($cfg->processCount) ? $cfg->processCount : 1;
		$conn = $this->dbconnection->connect();
		$sql = $conn->prepare("select * 
				from inact_devicedata.raw_data 
				where isProcessed='no' 
				and (`get` not like '%ATTLOG%' and `get` not like '%rtlog%')
				and post !='' 
				order by `datetime` ASC
				limit ".$processCount." ",
				array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
		
		$sql->execute();

		$fetchData = $sql->fetchAll();

		if(count($fetchData)>0){

			$dataUpdate = [];
			$arrId      = [];
			foreach ($fetchData as $data) {

				$dataGet    = json_decode($data["get"]);
				
				$SN 		= $dataGet->SN;
				$get 		= $dataGet;
				$table 		= !empty($dataGet->table) ? $dataGet->table : "";
				$tableName 	= !empty($dataGet->tablename) ? $dataGet->tablename : "";
				$dataPost   = $data["post"];
				$result 	= $this->cdataProcess($SN,$get,$dataPost,$table,$tableName);

				if ($result==true) {
					$arrId[] = $data["id"];
				}
			}
			// print_r($arrId);
			// exit;
			if(count($arrId)>0){
				$strId = implode(',', $arrId);
				
				// $sql = $conn->prepare("delete from raw_data where id in (".$strId.")");
				// $res = $sql->execute();
				// if($res==true){
				// 	echo "OK";
				// }
				
			}
		}else{
			echo "noqueue";
		}
		$conn = null;
	}
}