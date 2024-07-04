<?php
//use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/REST_Controller.php';
//require APPPATH . 'libraries/Format.php';
/**
 *
 */
class Cdata extends REST_Controller
//class Cdata extends CI_Controller
{

  var $now;

  public function __construct()
  {
    parent::__construct();
    $this->now = date('Y-m-d H:i:s');
    $this->load->model("datafinger_model");

    //$this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
    //$this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
    //$this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key


    $dataget = !empty( json_encode($_GET)) ?  json_encode($_GET) : "";
    $datapost= !empty(file_get_contents( 'php://input' )) ? file_get_contents( 'php://input' ) : "";
    $dataInsert = [
      "get_data"   => $dataget,
      "post_data"  => $datapost,
      "datecreated"=> date("Y-m-d H:i:s")
    ];
    $this->datafinger_model->insert($dataInsert);

  }

  /*
  * Identifikasi mesin
  */
  public function index_get()
  {
    //$myfile = fopen("berkas/berkas".time().".txt", "w") or die("Unable to open file!");
    //fwrite($myfile, "lol");
    load_model(['device_model']);

    $SN      = $_GET["SN"];

    $options = $_GET['options'];
    $exists  = $this->device_model->checkMachineExist($SN);
    if($exists!=false){

      header('Content-Type: text/plain');
      header('Vary : Accept-Encoding');
      header('Server : Apache');
      $stamp = time();
      echo "GET OPTION FROM: $SN\r\n
      Stamp=$stamp\r\n
      OptStamp=$stamp\r\n
      PhotStamp=0\r\n
      ErrorDelay=60\r\n
      Delay=30\r\n
      TransTime=00:00;14:05\r\n
      TransInterval=1\r\n
      TransFlag=1111101000\r\n
      TimeZone=7\r\n
      RealTime=1\r\n
      Encrypt=0";
    }
  }

  public function index_post()
  {
    load_model(['device_model']);
    $SN      = $_GET["SN"];
    $exists  = $this->device_model->checkMachineExist($SN);
    if($exists!=false){
      $appid    = $exists->appid;
      //$myfile = fopen("face.txt", "r") or die("Unable to open file!");
      //$dataPost = fread($myfile,filesize("face.txt"));
      $dataPost = file_get_contents("php://input");
      /// insert ke database penampung
      /*
      $dataInsert = [
        "get_data"   => $SN,
        "post_data"  => $dataPost,
        "datecreated"=> date("Y-m-d H:i:s")
      ];
      $this->datafinger_model->insert($dataInsert);
      */
      ///
      $postIdentify = $this->postIdentify($dataPost);
      if($postIdentify=="attendance"){
        $this->load->model("checkinout_model");
        $arrayAttendance = $this->prepareAttendanceToArray($dataPost,$appid,$SN,$exists->device_id);
        $this->checkinout_model->insert_batch($arrayAttendance);
        echo "OK";
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
            $this->employeelocationdevice_model->setNeedUpdate($employeeID,"yes");
          }
        }
        echo "OK";
      }elseif($postIdentify=="template_fp"){
        $this->load->library("machinepost_reader");
        $this->load->model("employeetemplate_model");
        $this->load->model("employeelocationdevice_model");

        $arrTemplate = $this->machinepost_reader->readFingerprint($dataPost,$appid);

        $arrEmployeeID = [];
        foreach ($arrTemplate as $row) {
          $employeeid = $row["employeetemplate_employee_id"];
          $index      = $row["employeetemplate_index"];
          $jenis      = $row["employeetemplate_jenis"];
          // cek apakah sudah pernah melakukan perekaman jari
          $templateExist = $this->employeelocationdevice_model->checkTemplateExists($employeeid,$index,$jenis);

          $this->employeetemplate_model->replace($row);

          if($templateExist){
            $this->employeelocationdevice_model->rePushTemplate($employeeid,$templateExist);
          }
        }
        echo "OK";
      }elseif($postIdentify=="template_face"){
        $this->load->library("machinepost_reader");
        $this->load->model("employeetemplate_model");

        $arrTemplate = $this->machinepost_reader->readFace($exists->appid,$dataPost);
        $arrEmployeeID = [];
        foreach ($arrTemplate as $row) {
          $this->employeetemplate_model->replace($row);
        }

        echo "OK";
      }elseif ($postIdentify=="sayhello") {
        echo "OK";
      }else{
        echo "OK";
      }
    }
  }

  function prepareAttendanceToArray($dataPost,$appid,$sn,$deviceID){

    $this->load->model("employee_model");
    $output = [];
    $arrRow = explode('\r\n',$dataPost);
    $arrEmployeeID = $this->employee_model->getAllEmployeeCode($appid);

    foreach ($arrRow as $row) {
      $arrField = preg_split("/[\t]/", $row);
      $employeeCode  = $arrField[0];
      $checkDateTime = $arrField[1];

      $employeeID = !empty($arrEmployeeID[$employeeCode]) ? $arrEmployeeID[$employeeCode] : 0;

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
        "checkinout_area_id"=>"0",
        "checkinout_cabang_id" => "0"
      ];
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
    }else{
      $output = 'attendance';
    }

    return $output;
  }
}
