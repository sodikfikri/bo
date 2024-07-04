<?php
/**
 * Library untuk membaca string template
 */
class Machinepost_reader
{
  var $CI;
  var $now;
  var $employeeCodesInclude;
  function __construct()
  {
    $this->CI =& get_instance();
    $this->now = date("Y-m-d H:i:s");
  }

  function readFingerprint($dataPost,$appid){
    $this->CI->load->model("employee_model");
    $this->CI->load->library("raw_extractor");
    $output = [];
    $arrRow = explode("\n",$dataPost);
    //
    $arrPin = [];
    foreach ($arrRow as $row) {
      $arrData = $this->CI->raw_extractor->lineToArrayLowerIndex($row);
      if(!empty($arrData["fp pin"])){
        $arrPin[] = $arrData["fp pin"];
      }
    }
    //
    $arrEmployeeID = $this->CI->employee_model->getAllEmployeeCode($appid,$arrData);

    foreach ($arrRow as $row) {
      $arrField = preg_split("/[\t]/", $row);

      if(!empty($arrField[1])&&!empty($arrField[2])&&!empty($arrField[3])&&!empty($arrField[4])){
        $arrIndex0 = explode(" ",$arrField[0]);

        $arrEmployeeAccount = explode("=",$arrIndex0[1],2);
        $arrFingerIndex     = explode("=",$arrField[1],2);
        $arrFingerSize      = explode("=",$arrField[2],2);
        $arrFingerValid     = explode("=",$arrField[3],2);
        $arrFingerTemplate  = explode("=",$arrField[4],2);
        
        //
        $employeeAccount = !empty($arrEmployeeAccount[1]) ? $arrEmployeeAccount[1] : "";
        $fingerIndex     = (string) $arrFingerIndex[1];//!empty($arrFingerIndex[1]) ? (string) $arrFingerIndex[1] : "";
        $fingerSize      = !empty($arrFingerSize[1]) ? $arrFingerSize[1] : "";
        $fingerValid     = !empty($arrFingerValid[1]) ? $arrFingerValid[1] : "";
        $fingerTemplate  = !empty($arrFingerTemplate[1]) ? $arrFingerTemplate[1] : "";
        
        if(!empty($employeeAccount) && (!empty($fingerIndex)|| $fingerIndex!="") && !empty($fingerSize) && !empty($fingerValid) && !empty($fingerTemplate)){
          $employeeID = !empty($arrEmployeeID[$employeeAccount]) ? $arrEmployeeID[$employeeAccount] : 0;
          $output[] = [
            "pin" => $employeeAccount,
            "appid" => $appid,
            "employeetemplate_employee_id" => $employeeID,
            "employeetemplate_template" => $fingerTemplate,
            "employeetemplate_index" => $fingerIndex,
            "employeetemplate_jenis" => "fingerprint",
          ];
        }
      }
    }
    return $output;
  }

  function readFace($appid,$dataPost){
    $this->CI->load->model("employee_model");
    $this->CI->load->library("raw_extractor");

    $output = [];
    $arrRow = explode("\n",$dataPost);
    //
    $arrPin = [];
    foreach ($arrRow as $row) {
      $arrData = $this->CI->raw_extractor->lineToArrayLowerIndex($row);
      if(!empty($arrData["face pin"])){
        $arrPin[] = $arrData["face pin"];
      }
    }
    
    //
    $arrEmployeeID = $this->CI->employee_model->getAllEmployeeCode($appid,$arrPin);

    foreach ($arrRow as $row) {
      $arrField = preg_split("/[\t]/", $row);

      if(!empty($arrField[1])&&!empty($arrField[2])&&!empty($arrField[3])&&!empty($arrField[4])){
        $arrIndex0 = explode(" ",$arrField[0]);

        $arrEmployeeAccount = explode("=",$arrIndex0[1],2);
        $arrFingerIndex     = explode("=",$arrField[1],2);
        $arrFingerSize      = explode("=",$arrField[2],2);
        $arrFingerValid     = explode("=",$arrField[3],2);
        $arrFingerTemplate  = explode("=",$arrField[4],2);
        //
        $employeeAccount = !empty($arrEmployeeAccount[1]) ? $arrEmployeeAccount[1] : "";
        
        $fingerIndex     = (string) $arrFingerIndex[1]; //!empty($arrFingerIndex[1]) ? (string) $arrFingerIndex[1] : "";
        $fingerSize      = !empty($arrFingerSize[1]) ? $arrFingerSize[1] : "";
        $fingerValid     = !empty($arrFingerValid[1]) ? $arrFingerValid[1] : "";
        $faceTemplate  = !empty($arrFingerTemplate[1]) ? $arrFingerTemplate[1] : "";
        if(!empty($employeeAccount) && (!empty($fingerIndex)|| $fingerIndex!="") && !empty($fingerSize) && !empty($fingerValid) && !empty($faceTemplate)){
          $employeeID = !empty($arrEmployeeID[$employeeAccount]) ? $arrEmployeeID[$employeeAccount] : 0;
          $output[] = [
            "pin" => $employeeAccount,
            "appid" => $appid,
            "employeetemplate_employee_id" => $employeeID,
            "employeetemplate_template" => $faceTemplate,
            "employeetemplate_index" => $fingerIndex,
            "employeetemplate_jenis" => "face",
          ];
        }
      }
    }
    return $output;
  }

  function readUser($appid,$dataPost){
    $this->CI->load->model("employee_model");
    $output = [];
    $arrRow = explode("\n",$dataPost);

    $arrEmployeeID = $this->CI->employee_model->getAllEmployeeCode($appid);

    foreach ($arrRow as $row) {
      $arrField = preg_split("/[\t]/", $row);

      if(!empty($arrField[0])&&!empty($arrField[1])&&!empty($arrField[2])&&!empty($arrField[3])&&!empty($arrField[4])&&!empty($arrField[5])){
        $arrUserPin     = explode("=",$arrField[0]);
        $arrName        = explode("=",$arrField[1]);
        $arrPrimary     = explode("=",$arrField[2]);
        $arrPassword    = explode("=",$arrField[3]);
        $arrCard        = explode("=",$arrField[4]);
        $arrGroup       = explode("=",$arrField[5]);
        $arrTZ          = explode("=",$arrField[6]);
        $arrVerify      = explode("=",$arrField[7]);
        $arrViceCard    = !empty($arrField[8]) ? explode("=",$arrField[8]) : "";
        //
        $userPin     = !empty((string)$arrUserPin[1]) ? (string)$arrUserPin[1]  : "";
        $name        = !empty((string)$arrName[1])    ? (string)$arrName[1]     : "";
        $primary     = (string)$arrPrimary[1];
        $password    = !empty((string)$arrPassword[1])? (string)$arrPassword[1] : "";
        $card        = !empty((string)$arrCard[1])    ? (string)$arrCard[1]     : "";
        $group       = !empty((string)$arrGroup[1])   ? (string)$arrGroup[1]    : "";
        $TZ          = !empty((string)$arrTZ[1])      ? (string)$arrTZ[1]       : "";
        $verify      = !empty((string)$arrVerify[1])  ? (string)$arrVerify[1]   : "";
        
        $viceCard    = !empty($arrViceCard) ? (!empty((string)$arrViceCard[1])? (string)$arrViceCard[1] : "") : "";

        $employeeID = !empty($arrEmployeeID[$userPin]) ? $arrEmployeeID[$userPin] : 0;
        // hanya data user yang terdaftar saja yang akan diupdate,
        // jika belum terdaftar maka diabaikan
        if($employeeID!=""){
          $output[] = [
            "appid" => $appid,
            "employee_id" => $employeeID,
            "userpin" => $userPin,
            "name" => $name,
            "primary" => $primary,
            "password" => $password,
            "card" => $card,
            "group" => $group,
            "tz" => $TZ,
            "verify" => $verify,
            "vicecard" =>$viceCard
          ];
        }
      }
    }
    return $output;
  }

  function readData($dataPost){
    $output = [];
    $arrRow = explode("\n",$dataPost);
    //$arrEmployeeID = $this->CI->employee_model->getAllEmployeeCode($appid);
    print_r($arrRow);
    foreach ($arrRow as $row) {
      $arrField = preg_split("/[\t]/", $row);
    }
  }

  function readProfileImage($appid,$dataPost){
    $this->CI->load->library("encryption_org");
    $this->CI->load->model("employee_model");
    $output = [];
    $arrRow = explode("\n",$dataPost);
    $arrEmployeeID = $this->CI->employee_model->getAllEmployeeCode($appid);

    foreach ($arrRow as $row) {
      $arrField = preg_split("/[\t]/", $row);
      if(!empty($arrField[1])&&!empty($arrField[2])&&!empty($arrField[3])){
        $arrIndex0 = explode(" ",$arrField[0]);

        $arrEmployeeAccount = explode("=",$arrIndex0[1],2);
        $arrFileName        = explode("=",$arrField[1],2);
        $arrSize            = explode("=",$arrField[2],2);
        $arrContent         = explode("=",$arrField[3],2);

        //
        $employeeAccount  = !empty($arrEmployeeAccount[1]) ? $arrEmployeeAccount[1] : "";
        $FileName         = (string) $arrFileName[1];//!empty($arrFingerIndex[1]) ? (string) $arrFingerIndex[1] : "";
        $Size             = !empty($arrSize[1]) ? $arrSize[1] : "";
        $Content          = !empty($arrContent[1]) ? $arrContent[1] : "";

        if(!empty($employeeAccount) && (!empty($FileName)|| $Size!="") && !empty($Content)){
          $employeeID = !empty($arrEmployeeID[$employeeAccount]) ? $arrEmployeeID[$employeeAccount] : 0;
          if($employeeID>0){
            $filename = $this->CI->encryption_org->encode($appid.'|'.$employeeID.'|'.$employeeAccount).".jpg";
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
    }
    return $output;
    /*
    list($type, $file) 	= explode(';', $file);
    list(, $file)      	= explode(',', $file);
    $file 				      = base64_decode($file);
    $justname  			    = hash('sha256', time());
    $imageName 			    = $justname.'.png';

    file_put_contents('.'.SEPARATORF.'sys_upload'.SEPARATORF.'member_photo'.SEPARATORF.$imageName, $file);
    */
  }

  function ReadDeviceInfo($dataPost){
    $arrRow = explode("\n",$dataPost);
    $output = [];
    foreach ($arrRow as $row) {
      if($row!=""){
        $arrCmd = explode("=",$row);
        $dAtrribute = $arrCmd[0];
        $dValue     = !empty($arrCmd[1]) ? $arrCmd[1] : "";

        $output[$dAtrribute] = $dValue;
      }
    }
    return $output;
  }

  function prepareAttendanceToArray($dataPost,$appid,$sn,$deviceID,$areaid,$cabangid){

    $this->CI->load->model("employee_model");
    $output = [];
    $arrRow = preg_split("/[\r\n]/",$dataPost);
    $arrEmployeeID = $this->CI->employee_model->getAllEmployeeCode($appid);
    
    foreach ($arrRow as $row) {
      if($row!=""){
        $arrField = preg_split("/[\t]/", $row);
        $employeeCode  = $arrField[0];
        $checkDateTime = $arrField[1];

        $employeeID = !empty($arrEmployeeID[$employeeCode]) ? $arrEmployeeID[$employeeCode] : 0;
        // jika tidak ditemukan employeecode maka data akan diabaikan
        if(!empty($arrEmployeeID[$employeeCode])){
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

  public function postIdentify($string){
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

  public function readAttLog($dataPost){
    $this->CI->load->model("employee_model");
    $output = [];
    $arrRow = preg_split("/[\r\n]/",$dataPost);
    
    foreach ($arrRow as $row) {

      if($row!=""){
        $arrField = preg_split("/[\t]/", $row);
        $employeeCode  = $arrField[0];
        $checkDateTime = $arrField[1];

        $employeeID = !empty($arrEmployeeID[$employeeCode]) ? $arrEmployeeID[$employeeCode] : 0;
        // jika tidak ditemukan employeecode maka data akan diabaikan
        
        $maskFlag   = !empty($arrField[7]) ? $arrField[7] : "";
        $temperature= !empty($arrField[8]) ? $arrField[8] : "";

        $output[] = [
          "checkinout_employeecode" => $arrField[0],
          "checkinout_datetime"     => $arrField[1],
          "checkinout_code"         => $arrField[2],
          "checkinout_verification_mode" => $arrField[3],
          "mask_flag"               => $maskFlag,
          "temperature"             => $temperature
        ];
      }
    }
    return $output;
  }
}
