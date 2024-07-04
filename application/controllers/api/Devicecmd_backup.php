<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
/**
 *
 */
class Devicecmd extends REST_Controller
//class Devicecmd extends CI_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->model("datafinger_model");

    $this->load->model("datafinger_model");
    $dataget = !empty( json_encode($_GET)) ?  json_encode($_GET) : "";
    $datapost= !empty(file_get_contents( 'php://input' )) ? file_get_contents( 'php://input' ) : "";
    $dataInsert = [
      "get_data"   => $dataget,
      "post_data"  => $datapost,
      "datecreated"=> date("Y-m-d H:i:s")
    ];
    $this->datafinger_model->insert($dataInsert);
  }

  function index_post(){
    load_model(['device_model']);
    $SN      = $_GET["SN"];
    $exists  = $this->device_model->checkMachineExist($SN);

    if($exists){
      $dataPost = file_get_contents( 'php://input' );
      if(!empty($dataPost)){
        //goto end;
      }
      $arrCmdResponse = explode("\n",$dataPost);
      $arrUserLocationSuccessAdd = [];
      $arrInsertEmployeeLocationDevice = [];
      $arrUserLocationSuccessDel = [];
      $arrUpdateEmployeeLocationDevice = [];

      foreach ($arrCmdResponse as $row) {
        $fieldCmdResponse = explode("&",$row);
        if(!empty($fieldCmdResponse[0])&&!empty($fieldCmdResponse[1])||!empty($fieldCmdResponse[2])){
          $fieldID    = explode("=",$fieldCmdResponse[0])[1];
          $fieldReturn= explode("=",$fieldCmdResponse[1])[1];
          $fieldCMD   = explode("=",$fieldCmdResponse[2])[1];
          //echo $fieldID.'|'.$fieldReturn.'|'.$fieldCMD.'<br>';
          if($fieldReturn==0){
            $arrCommand  = explode(".",$fieldID);
            $commandType = $arrCommand[0];
            $commandId   = $arrCommand[1];
            if($commandType == "ADDUSER"){
              $arrCommandID = explode("|",$commandId);
              $employeeareacabang_id = $arrCommandID[0];
              $employee_id = $arrCommandID[1];

              $arrUserLocationSuccessAdd[] = [
                "employeeareacabang_id" => $employeeareacabang_id,
                "employeeareacabang_employee_id" => $employee_id,
                "status" => "active"
              ];
              $arrInsertEmployeeLocationDevice[] = [
                "employeeareacabang_id" => $employeeareacabang_id,
                "device_id" => $exists->device_id,
                "employee_id" => $employee_id
              ];
            }
            if($commandType == "DELETEUSER"){
              $arrCommandID = explode("|",$commandId);

              $employeeareacabang_id = $arrCommandID[0];

              $deviceID   = $arrCommandID[1];
              $employeeID = $arrCommandID[2];
              $arrUserLocationSuccessDel[] = [
                "location_id" => $employeeareacabang_id,
                "device_id" => $deviceID,
                "employee_id" => $employeeID
              ];
            }
            if($commandType == "UPDATEUSER"){
              $employeeLocationDevice = $commandId;

              $arrUpdateEmployeeLocationDevice[] = [
                "employeelocationdevice_id" => $employeeLocationDevice,
                "need_update" => "no"
              ];
            }
            if ($commandType == "ADDTEMPLATE") {
              $arrCommandID = explode("|",$commandId);
              $employeelocationdevice_id= $arrCommandID[0];
              $employeetemplate_id      = $arrCommandID[1];
              $pushcounter              = $arrCommandID[2] + 1;
              $templateInserted = [
                "employeelocationdevice_id" => $employeelocationdevice_id,
                "employeetemplate_id" => $employeetemplate_id,
                "push_count" => $pushcounter
              ];
              $this->db->replace("tbemployeelocationdevicetemplate",$templateInserted);
            }
          }
        }
      }
      // add user
      if(count($arrUserLocationSuccessAdd)>0){
        $this->load->model("employeeareacabang_model");
        $this->employeeareacabang_model->setActiveEmployeeLocation($arrUserLocationSuccessAdd);
      }

      if(count($arrInsertEmployeeLocationDevice)){
        $this->load->model("employeelocationdevice_model");
        $this->employeelocationdevice_model->insert_batch($arrInsertEmployeeLocationDevice);
      }

      // update employee on device
      if(count($arrUpdateEmployeeLocationDevice)>0){
        $this->load->model("employeelocationdevice_model");
        $this->employeelocationdevice_model->update_batch($arrUpdateEmployeeLocationDevice,"employeelocationdevice_id");
      }
      // delete user when resign
      if(count($arrUserLocationSuccessDel)>0){

        load_model(['employeeareacabang_model','employee_model','employeelocationdevice_model']);
        foreach ($arrUserLocationSuccessDel as $row) {

          // delete di tabel employeelocationdevice
          $this->employeelocationdevice_model->remove($row['location_id'],$row['device_id']);
          // count device location
          //jika 0 maka update lokasi aktif menjadi archive (yang pending biarkan)
          $deviceCount = $this->employeelocationdevice_model->countDeviceLocation($row['location_id']);
          if($deviceCount==0){
            $this->employeeareacabang_model->setArchiveById($row['location_id']);
            // count location active
            $locationCount = $this->employeeareacabang_model->countActiveLocation($row['employee_id']);

            if($locationCount==0){
              // jika 0 update status karyawan menjadi not active
              $this->employee_model->confirmResign($row['employee_id']);

              // insert history karyawan
              // emboh
            }
          }
        }
      }
      end:
      echo "OK";
    }
  }
}
