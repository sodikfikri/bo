<?php 
/**
 * 
 */
class Thermoattendance_model extends CI_Model
{
	var $now;
	function __construct()
	{
		parent::__construct();
    $this->now = date("Y-m-d H:i:s");
	}

	function procedCdata($dataPost,$deviceData,$deviceTable,$deviceTableName,$dataCount){
    $appid    = $deviceData->appid;
    
    if(!empty($deviceTable)){
      // tambal sulam untuk menyesuaikan format baru
      $arrDeviceTableName = [
        "user",
        "extuser",
        "identitycard",
        "templatev10",
        "biophoto",
        "attphoto",
        "userpic",
        "biodata",
        "operlog",
        "errorlog",
        "options"
      ];
      if(in_array(strtolower($deviceTable), $arrDeviceTableName)){
        $deviceTableName = $deviceTable;
        $deviceTable = "tabledata";
      }
      
      //=========================
      if($deviceTable=="rtlog"){
        $this->load->library("machinepost_reader");
        $this->load->model("employee_model");
        $this->load->model("checkinout_model");
        // log absensi
        // $this->load->model("checkinout_model");
        $arrayAttendance = $this->prepareAttendanceToArray($dataPost,$appid,$deviceData->device_SN,$deviceData->device_id,$deviceData->device_area_id,$deviceData->device_cabang_id);

        if(count($arrayAttendance)>0){
          $insertAttendance = $this->checkinout_model->bulk_insert($arrayAttendance);
          if($insertAttendance==true){
            // Done
            $this->load->library("dbconnection");
            $this->load->model("area_model");
            $this->load->model("cabang_model");
            $this->load->model("employee_model");
            $conn = $this->dbconnection->connect();
            foreach($arrayAttendance as $row){
              $sql = $conn->prepare("insert into tbfinal_checkinout SET
                appid='".$row["appid"]."',
                area_id='".$row['checkinout_area_id']."',
                area_name='".$this->area_model->getName($row['checkinout_area_id'])."',
                cabang_id='".$row['checkinout_cabang_id']."',
                cabang_name='".$this->cabang_model->getName($row['checkinout_cabang_id'])."',
                sn='".$row["checkinout_SN"]."',
                employee_id='".$row['checkinout_employee_id']."',
                account_no='".$row['checkinout_employeecode']."',
                name='".$this->employee_model->getName($row['checkinout_employee_id'])."',
                datetime='".$row['checkinout_datetime']."',
                absen_code='".$row['checkinout_code']."',
                verify_code='".$row['checkinout_verification_mode']."',
                temperature='".$row['temperature']."',
                use_masker='".$row['mask_flag']."'
                ");
              $sql->execute();
            }
            return "OK";
          }
        }else{
          //return "OK";
        }
      }elseif ($deviceTable=="ATTLOG") {
        
        $this->load->library("machinepost_reader");
        $this->load->model("employee_model");
        $this->load->model("checkinout_model");
        $this->load->library("dbconnection");
        $this->load->model("area_model");
        $this->load->model("cabang_model");
        $this->load->model("employee_model");
        $arrayAttendance = $this->machinepost_reader->readAttLog($dataPost);
        $arrEmployeeID = $this->employee_model->getAllEmployeeCode($appid);
				
        if(count($arrayAttendance)>0){
          
					$arrayInsert = [];
          $conn = $this->dbconnection->connect();
          foreach ($arrayAttendance as $row) {
            $employeeCode = $row["checkinout_employeecode"];
            $employeeID = !empty($arrEmployeeID[$employeeCode]) ? $arrEmployeeID[$employeeCode] : 0;
            
            $row["appid"]                 = $appid;
            $row["checkinout_employee_id"]= $employeeID;
            $row["checkinout_device_id"]  = $deviceData->device_id;
            $row["checkinout_SN"]         = $deviceData->device_SN;
            $row["checkinout_date_create"]=  date("Y-m-d H:i:s");
            $row["checkinout_area_id"]    = $deviceData->device_area_id;
            $row["checkinout_cabang_id"]  = $deviceData->device_cabang_id;
            //echo $row["checkinout_datetime"];exit;
						$dataExist = false; //$this->checkinout_model->isExist($appid,$employeeID,$row["checkinout_datetime"]);
						
            if($dataExist==false){
              $sql = $conn->prepare("insert into tbfinal_checkinout SET
                appid='".$row["appid"]."',
                area_id='".$row['checkinout_area_id']."',
                area_name='".$this->area_model->getName($row['checkinout_area_id'])."',
                cabang_id='".$row['checkinout_cabang_id']."',
                cabang_name='".$this->cabang_model->getName($row['checkinout_cabang_id'])."',
                sn='".$row["checkinout_SN"]."',
                employee_id='".$row['checkinout_employee_id']."',
                account_no='".$row['checkinout_employeecode']."',
                name='".$this->employee_model->getName($row['checkinout_employee_id'])."',
                datetime='".$row['checkinout_datetime']."',
                absen_code='".$row['checkinout_code']."',
                verify_code='".$row['checkinout_verification_mode']."',
                temperature='".$row['temperature']."',
                use_masker='".$row['mask_flag']."'
                ");
              $sql->execute();
              $arrayInsert[] = $row;
            }
          }
        }

        $insertAttendance = $this->checkinout_model->bulk_insert($arrayInsert);
        //echo $this->db->last_query();
        if($insertAttendance==true){
          // Done
          return "OK";
        }
        
      }elseif ($deviceTable=="rtstate") {
        // realtime status
        // Done
        return "OK";
      }elseif ($deviceTable=="tabledata") {

        if($deviceTableName=="user"){
          // user information
          $arrUser    = $this->readUser($appid,$dataPost);
          $userUpdate = [];

          if(count($arrUser)>0){
            $arrEmployeeID = [];
            foreach ($arrUser as $row) {
              $userUpdate[] = [
                "employee_id"       => $row["employee_id"],
                "employee_password" => $row["password"],
                "employee_card"     => $row["cardno"]
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
            // done 
            return "user=".$dataCount;
          }else{
            // done
            return "user=".$dataCount;
          }
        }elseif($deviceTableName=="extuser"){
          // extended user
          return "extuser=".$dataCount;
        }elseif ($deviceTableName=="identitycard") {
          // identity card
          return "identitycard=".$dataCount;
        }elseif($deviceTableName=="templatev10"){
          // template fingerprint
          $this->load->library("machinepost_reader");
          $this->load->model("employeetemplate_model");
          $this->load->model("employeelocationdevice_model");
          $this->load->model("firewall_model");
          $arrTemplate = $this->readFingerprint($dataPost,$appid);

          $arrEmployeeID = [];
          foreach ($arrTemplate as $row) {
            $employeeid = $row["employeetemplate_employee_id"];
            $index      = $row["employeetemplate_index"];
            $jenis      = $row["employeetemplate_jenis"];
            // cek apakah sudah pernah melakukan perekaman jari
            $templateExist = $this->employeelocationdevice_model->checkTemplateExists($employeeid,$index,$jenis);
            $this->firewall_model->openGate($employeeid);
            $this->employeetemplate_model->replace($row);

            if($templateExist){
              $this->employeelocationdevice_model->rePushTemplate([$employeeid],$templateExist);
            }
          }
          return "templatev10=".$dataCount;
        }elseif ($deviceTableName=="biophoto") {
          // upload comparison photo
          $this->load->model("checkinout_model");
          $this->load->library("string_manipulation");
          /*
          $arrBioPhoto = $this->readBioPhoto($dataPost);
          
          foreach ($arrBioPhoto as $row) {
            $logs = $this->checkinout_model->getLastEmptySnapshootRecord($row["biophoto pin"],$appid);
            if ($logs!=false) {
              $punchTimeID  = cleanSpecialChar($logs->checkinout_datetime);
              $imageType    = explode(".", $row["filename"])[1];
              $imageName    = $this->string_manipulation->hashSM($appid.$logs->checkinout_device_id)."-".$punchTimeID.".".$imageType;
              $rawImage     = $row["content"];
              $result = $this->checkinout_model->setSnapshoot($imageName,$logs->checkinout_id,$appid);
              if($result>0){
                $this->saveLogImage($rawImage,$imageName);
              }
            }
          }
          */
          
          return "biophoto=".$dataCount;
        }elseif ($deviceTableName=="ATTPHOTO") {

          
          /*
          $this->load->model("checkinout_model");
          $this->load->library("string_manipulation");

          // upload snapshoot
          $arrSnapshoot = $this->readSnapShoot($dataPost);
          foreach ($arrSnapshoot as $row) {
            $imageName = $this->string_manipulation->hashSM($appid.$deviceData->device_id)."-".$row["pin"];
            $rawImage  = $row["photo"];

            
            $dataUpdate = [
              'log_image' => $imageName
            ];

            $punchTimeID = cleanSpecialChar(explode(".", $row["pin"])[0]);

            $affectedRows = $this->checkinout_model->updateByPunchTimeID($dataUpdate,$punchTimeID,$appid);
            
            if($affectedRows>0){
              $this->saveLogImage($rawImage,$imageName);
            }
          }
          */
          return "ATTPHOTO=".$dataCount;
        }elseif ($deviceTableName=="userpic") {
          // upload user photo
          load_model(['employee_model','firewall_model']);
          $arrPicture = $this->readProfileImage($deviceData->appid,$dataPost);
          $updatePic  = [];
          $needUpdatePic = [];
          foreach ($arrPicture as $row) {
            // save to file
            $image      = base64_decode($row['employee_image']);
            $imageName  = $row['file_name'];
            $employeeID = $row['employee_id'];

            $rowLastDetail = $this->employee_model->getSpecifiedDetailEmployee($employeeID,["image"],$deviceData->appid);

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
          return "userpic=".$dataCount;
        }elseif (strtolower($deviceTableName)=="biodata") {
					
          // integrated template
          $this->load->model("employeelocationdevice_model");
          $this->load->model("employeetemplate_model");
          $this->load->model("firewall_model");
          $arrTemplate = $this->readBioData($deviceData->appid,$dataPost);
          $arrEmployeeID = [];
          // echo $dataPost;
          // echo "<pre>";
          // print_r($arrTemplate);
          // echo "</pre>";
          foreach ($arrTemplate as $row) {
            $this->employeetemplate_model->replace($row);
            // $this->firewall_model->openGate($row["employeetemplate_employee_id"]);
            // set need Update
            $templateID = $this->employeetemplate_model->getTemplateID($row["employeetemplate_employee_id"],$row["employeetemplate_index"],$row["employeetemplate_jenis"]);
            /*
            if($templateID!=false){
              $this->db->where("employeetemplate_id",$templateID);
              $this->db->update("tbemployeelocationdevicetemplate",[
                "push_count" => "0"
              ]);
            }
            */
            if($templateID){
              $this->employeelocationdevice_model->rePushTemplate([$row["employeetemplate_employee_id"]],$templateID);
            }
          }
          return "biodata=".$dataCount;
        }elseif ($deviceTableName=="OPERLOG"){
          // upload error log
          return "OK";
        }elseif ($deviceTableName=="errorlog"){
          // upload error log
          return "errorlog=".$dataCount;
        }else{
          return "OK";
        }
      }elseif ($deviceTable=="options") {
        // info
        return "OK";
      }elseif($deviceTable=="OPERLOG"){
        return "OK";
      }
    }
  }

  function prepareAttendanceToArray($dataPost,$appid,$sn,$deviceID,$areaid,$cabangid){
    $this->load->model("employee_model");
    $this->load->library("raw_extractor");

    $output = [];
    $arrRow = $this->raw_extractor->rawToRow($dataPost);
    $arrEmployeeID = $this->employee_model->getAllEmployeeCode($appid);

    foreach ($arrRow as $row) {
      if($row!=""){
        $arrField = $this->raw_extractor->lineToArray($row);
        $employeeCode  = $arrField["pin"];
        $checkDateTime = $arrField["time"];
        
        $employeeID = !empty($arrEmployeeID[$employeeCode]) ? $arrEmployeeID[$employeeCode] : 0;
        // jika tidak ditemukan employeecode maka data akan diabaikan
        //if(!empty($arrEmployeeID[$employeeCode])){
          $output[] = [
            "appid" => $appid,
            "checkinout_employee_id"  => $employeeID,
            "checkinout_employeecode" => (!empty($arrField["pin"]) ? $arrField["pin"] : 0),
            "checkinout_datetime"     => $arrField["time"],
            "checkinout_code"         => (string) (!empty($arrField["inoutstatus"]) ? $arrField["inoutstatus"] : 0),
            "checkinout_verification_mode" => $arrField["verifytype"],
            "checkinout_device_id"    => $deviceID,
            "checkinout_SN"           => $sn,
            "checkinout_date_create"  => $this->now,
            "checkinout_area_id"      => $areaid,
            "checkinout_cabang_id"    => $cabangid,
            "mask_flag"               => (string) (!empty($arrField["maskflag"]) ? $arrField["maskflag"] : 0),
            "temperature"             => (!empty($arrField["temperature"]) ? $arrField["temperature"] : "")
          ];
        //}
      }
    }
    return $output;
  }

  function readUser($appid,$dataPost){
    $this->load->model("employee_model");
    $this->load->library("raw_extractor");
    $output = [];
    $arrRow = $this->raw_extractor->rawToRow($dataPost);
    $arrEmployeeID = $this->employee_model->getAllEmployeeCode($appid);

    foreach ($arrRow as $row) {
      $arrLine  = explode(" ", $row,2);
      if (!empty($arrLine[1])) {
        $arrField = $this->raw_extractor->lineToArray($arrLine[1]);
      
        $userPin  = $arrField["pin"];

        $employeeID = !empty($arrEmployeeID[$userPin]) ? $arrEmployeeID[$userPin] : 0;
        // hanya data user yang terdaftar saja yang akan diupdate,
        // jika belum terdaftar maka diabaikan
        if($employeeID!=0){
          $dataOutput = $arrField;
          $dataOutput["appid"] = $appid;
          $dataOutput["employee_id"] = $employeeID;
          $output[] = $dataOutput;
        }
      }
    }
    return $output;
  }

  function readSnapShoot($rawData){
    $this->load->model("employee_model");
    $this->load->library("raw_extractor");
    $output = [];
    $arrRow = $this->raw_extractor->rawToRow($rawData);

    foreach ($arrRow as $row) {
      if(!empty($row)){
        $arrField = $this->raw_extractor->lineToArray($row);
        $output[] = $arrField;
      }
    }
    return $output;
  }

  function readBioPhoto($rawData){
    $this->load->model("employee_model");
    $this->load->library("raw_extractor");
    $output = [];
    $arrRow = $this->raw_extractor->rawToRow($rawData);

    foreach ($arrRow as $row) {

      if(!empty($row)){
        $arrField = $this->raw_extractor->lineToArray($row);
        $output[] = $arrField;
      }
    }
    return $output;
  }

  function saveLogImage($rawImage,$imageName){
    file_put_contents('./sys_upload/log_image/'.$imageName, base64_decode($rawImage));
    return true;
  }

  function readFingerprint($dataPost,$appid){
    $this->load->model("employee_model");
    $this->load->library("raw_extractor");

    $output = [];
    $arrRow = $this->raw_extractor->rawToRow($dataPost);
    $arrEmployeeID = $this->employee_model->getAllEmployeeCodeAdvance($appid);

    foreach ($arrRow as $row) {
      $arrLine = explode(" ", $row,2);

      if(!empty($arrLine[1])){
        $arrField = $this->raw_extractor->lineToArray($arrLine[1]);
        
        $employeeAccount = $arrField["pin"];
        $fingerIndex     = (string) !empty($arrField["fingerid"]) ? $arrField["fingerid"] : 0;
        $fingerSize      = $arrField["size"];
        $fingerValid     = (string) !empty($arrField["valid"]) ? $arrField["valid"] : 0;
        $fingerTemplate  = !empty($arrField["template"]) ? $arrField["template"] : "";
        
        $arrEmployeeData = searchInArray($arrEmployeeID,"pin",$employeeAccount);
        $employeeID = $arrEmployeeData!=false ? $arrEmployeeData["id"] : 0;
        
        if(!empty($employeeID)){
          $output[] = [
            "appid" => $appid,
            "employeetemplate_employee_id" => $employeeID,
            "employeetemplate_template" => $fingerTemplate,
            "employeetemplate_index" => $fingerIndex,
            "employeetemplate_jenis" => "fingerprint",
            "major_version" => "10",
            "minor_version" => "0",
            "format" => "0"
          ];
        }
      }
    }
    return $output;
  }
  
  function readBioData($appid,$dataPost){
    $this->load->model("employee_model");
    $this->load->library("raw_extractor");

    $output = [];
    $arrRow = $this->raw_extractor->rawToRow($dataPost);

    //$arrEmployeeID = $this->employee_model->getAllEmployeeCodeAdvance($appid);

    foreach ($arrRow as $row) {
      $arrRow   = explode(" ", $row,2);
      if(!empty($arrRow[1])){
        $arrField = $this->raw_extractor->lineToArray($arrRow[1]);

        $pin      = $arrField["pin"];
        $no       = $arrField["no"];
        $index    = (string) !empty($arrField["index"]) ? $arrField["index"] : 0;
        $valid    = (string) !empty($arrField["valid"]) ? $arrField["valid"] : 0;
        $duress   = $arrField["duress"];
        $type     = (string) !empty($arrField["type"])  ? $arrField["type"]  : 0;
        $majorver = (string) $arrField["majorver"];
        $minorver = (string) !empty($arrField["minorver"]) ? $arrField["minorver"] : 0;
        $format   = (string) !empty($arrField["format"])? $arrField["format"]: 0;

        $tmp      = $arrField["tmp"];

        $employeeAccount = $pin;
        $templateIndex   = (string) $index;
        $templateValid   = $valid;

        $dataEmployee = $this->employee_model->getByEmployeeCode($appid,$employeeAccount);
        
        $employeeID = $dataEmployee!=false ? $dataEmployee->employee_id : 0;
        // echo $employeeID;
        if(($employeeID)!=0){
          $templateType = $this->getTypeTemplate($type); 
          $output[] = [
            "appid" => $appid,
            "employeetemplate_employee_id" => $employeeID,
            "employeetemplate_template" => $tmp,
            "employeetemplate_index" => $templateIndex,
            "employeetemplate_jenis" => $templateType,
            "major_version" => $majorver,
            "minor_version" => $minorver,
            "format" => $format
          ];
        }
      }
    }
    return $output;
  }

  function getTypeTemplate($idType){
    $arrTypeTemplate = $this->getTemplateTypes();
    return !empty($arrTypeTemplate[$idType]) ? $arrTypeTemplate[$idType] : "";
  }

  function getTemplateTypes(){
    return array(
      1 => "fingerprint",
      2 => "face",
      3 => "voice_print",
      4 => "iris",
      5 => "retina",
      6 => "palm_print",
      7 => "finger_vein",
      8 => "palm",
      9 => "visible_light_face",
    );
  }

  function readProfileImage($appid,$dataPost){
    $this->load->library("encryption_org");
    $this->load->model("employee_model");
    $this->load->library("raw_extractor");
    $output         = [];
    $arrRow         = $this->raw_extractor->rawToRow($dataPost);
    $arrEmployeeID  = $this->employee_model->getAllEmployeeCode($appid);

    foreach ($arrRow as $row) {
      $arrRow   = explode(" ", $row, 2);
      
      
      if(!empty($arrRow[1])){
        $arrField = $this->raw_extractor->lineToArray($arrRow[1]);

        $employeeAccount  = $arrField["pin"];
        $FileName         = (string) $arrField["filename"];
        $Size             = $arrField["size"];
        $Content          = $arrField["content"];

        $employeeID = !empty($arrEmployeeID[$employeeAccount]) ? $arrEmployeeID[$employeeAccount] : 0;
        if($employeeID>0){
          $filename = $this->encryption_org->encode($appid.'|'.$employeeID.'|'.$employeeAccount).".jpg";
          $output[] = [
            "appid" => $appid,
            "employee_id" => $employeeID,
            "file_name" => $filename,
            "employee_image" => $Content,
            "size" => $Size
          ];
        }
      }
    }
    return $output;
  }



  /*
	private function postIdentify($string){
	    if(
        $this->checkStringExists("user uid",$string)==true &&
        $this->checkStringExists("cardno",$string)==true &&
        $this->checkStringExists("password",$string)==true
      ){
        // untuk menambahkan user
	      $output = 'adduser';
	    }elseif(
        $this->checkStringExists("time",$string)==true &&
        $this->checkStringExists("sensor",$string)==true &&
        $this->checkStringExists("relay",$string)==true &&
        $this->checkStringExists("alarm",$string)==true
      ){
        // masih belum dimanfaatkan
	      $output = 'realtime-status';
	    }elseif (
        $this->checkStringExists("templatev10",$string)==true &&
        $this->checkStringExists("fingerid",$string)==true &&
        $this->checkStringExists("template",$string)==true
      ) {
	      $output = 'template_fp';
	    }elseif (
        $this->checkStringExists("FACE PIN",$string)==true &&
        $this->checkStringExists("TMP",$string)==true &&
        $this->checkStringExists("FID",$string)==true
      ) {
	      $output = 'template_face';
	    }elseif ($this->checkStringExists("USERPIC",$string)==true && $this->checkStringExists("FileName",$string)==true && $this->checkStringExists("Content",$string)==true) {
	      $output = 'face_pic';
	    }elseif(
        $this->checkStringExists("time",$string)==true && 
        $this->checkStringExists("pin",$string)==true  &&
        $this->checkStringExists("eventaddr",$string)==true  &&
        $this->checkStringExists("event",$string)==true  &&
        $this->checkStringExists("inoutstatus",$string)==true
      ){
	      $output = 'realtime-event'; // attendance
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
    */
}