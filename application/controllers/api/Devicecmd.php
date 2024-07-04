<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//require APPPATH . 'libraries/REST_Controller.php';
/**
 *
 */
//class Devicecmd extends REST_Controller
class Devicecmd extends CI_Controller
{
  var $apikey = "3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9";
  function __construct()
  {
    parent::__construct();
    // $this->load->model("datafinger_model");
    $this->load->library("device_door");

    //$this->load->model("datafinger_model");
    //$dataget = !empty( json_encode($_GET)) ?  json_encode($_GET) : "";
    //$datapost= !empty(file_get_contents( 'php://input' )) ? file_get_contents( 'php://input' ) : "";
    //$dataInsert = [
    //  "get_data"   => $dataget,
    //  "post_data"  => $datapost,
    //  "datecreated"=> date("Y-m-d H:i:s")
    //];
    //$this->datafinger_model->insert($dataInsert);
  }
  function devicecmd_post($strAPIkey){
    $arrKey   = explode("-",$strAPIkey);
    $SN       = $arrKey[1];
    $apikey   = $arrKey[0];

    if($this->device_door->isOpenPermission($SN)==true){
      load_model(['device_model','deviceshipments_model']);
      $exists   = $this->device_model->checkMachineExist($SN);
      $dataPost = ($this->input->post("postBody")) ? $this->input->post("postBody") : '';
      $get      = $this->input->post("get");
      

      if($exists && $this->apikey==$apikey){
        $this->load->library("dbconnection");
        $conn = $this->dbconnection->connect();
        // $dataShipment  = [
        //     "post"     => $dataPost,
        //     "SN"       => $SN,
        //     "appid"    => (!empty($exists->appid) ? $exists->appid : ''),
        //     "endpoint" => "devicecmd",
        //     "method"   => "post",
        //     "get"      => $get
        // ];

        //$this->deviceshipments_model->insert($dataShipment);
          
        if(!empty($dataPost)){
          //goto end;
        }
        $arrCmdResponse = explode("\n",$dataPost);
        $arrUserLocationSuccessAdd = [];
        $arrInsertEmployeeLocationDevice = [];
        $arrUserLocationSuccessDel = [];
        $arrUpdateEmployeeLocationDevice = [];
        $arrMutationIn = [];
        $arrMutationOut= [];
        $arrUpdatePic  = [];
        $arrIdUserToUpdate = [];
        foreach ($arrCmdResponse as $index => $row) {
          $fieldCmdResponse = explode("&",$row);
          if(!empty($fieldCmdResponse[0])&&!empty($fieldCmdResponse[1])||!empty($fieldCmdResponse[2])){
            $fieldID    = explode("=",$fieldCmdResponse[0])[1];
            $fieldReturn= explode("=",$fieldCmdResponse[1])[1];
            $fieldCMD   = explode("=",$fieldCmdResponse[2])[1];

            //echo $fieldID.'|'.$fieldReturn.'|'.$fieldCMD.'<br>';
            $arrCommand  = explode(".",$fieldID);
            $commandType = $arrCommand[0];
            $commandId   = $arrCommand[1];

            if($fieldReturn!="0" && $commandType=="DU"){
              // untuk delete user jika gagal 3 kali selanjutnya akan diloloskan
              $responseWithUniqueDate = $row."-".date("Ymd");
              $sql = $conn->prepare("select *
                      from command_response 
                      where response_string='$responseWithUniqueDate'",
                      array(PDO::MYSQL_ATTR_FOUND_ROWS => true)
                    );
              $sql->execute();
              /* penambahan unique day ditambahkan agar toleransi 3x gagal bisa dihitung ulang 
              jika code yang sama digunakan ulang di lain hari 
              */

              

              if($sql->rowCount()>0){
                $rowResponse = $sql->fetchAll()[0];
                if($rowResponse["response_receive_count"]<3){
                  $sql = $conn->prepare("update command_response set
                  response_receive_count=response_receive_count + 1
                          where response_string='$responseWithUniqueDate'
                          ");

                  $sql->execute();
                }else{
                  $fieldReturn="0";
                }
              }else{
                $sql = $conn->prepare("insert into command_response set 
                response_string='$responseWithUniqueDate',
                appid = '$exists->appid',
                device_SN='$SN', 
                response_receive_count='1'
                ");
                $sql->execute();
              }
            }
            if($fieldReturn=="0"){

              
              // echo $commandType;
              // general command
              if($commandType == "cmd"){
                $this->load->model("command_model");
                
                $result = $this->command_model->finishExecute($commandId);
                
              }

              // add user
              if($commandType == "AU"){
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
                if(!in_array($employee_id, $arrIdUserToUpdate)){
                  array_push($arrIdUserToUpdate, $employee_id);
                }
              }
              // delete user
              if($commandType == "DU"){
                $arrCommandID = explode("|",$commandId);

                $employeeareacabang_id = $arrCommandID[0];

                $deviceID   = $arrCommandID[1];
                $employeeID = $arrCommandID[2];
                $arrUserLocationSuccessDel[] = [
                  "location_id" => $employeeareacabang_id,
                  "device_id" => $deviceID,
                  "employee_id" => $employeeID
                ];
                if(!in_array($employeeID, $arrIdUserToUpdate)){
                  array_push($arrIdUserToUpdate, $employeeID);
                }
              }
              // update user
              if($commandType == "UU"){
                $employeeLocationDevice = $commandId;

                $arrUpdateEmployeeLocationDevice[] = [
                  "employeelocationdevice_id" => $employeeLocationDevice,
                  "need_update" => "no"
                ];
              }
              // add template
              if ($commandType == "AT") {
                $this->load->model("historytemplatepush_model");
                $arrCommandID = explode("|",$commandId);
                $employeelocationdevice_id= $arrCommandID[0];
                $employeetemplate_id      = $arrCommandID[1];
                $pushcounter              = (!empty($arrCommandID[2])?$arrCommandID[2]:0) + 1;
                $templateInserted = [
                  "employeelocationdevice_id" => $employeelocationdevice_id,
                  "employeetemplate_id" => $employeetemplate_id,
                  "push_count" => $pushcounter
                ];
                $this->historytemplatepush_model->insert([
                  "template_id" => $employeetemplate_id,
                  "device_id" => $exists->device_id
                ]);
                
                $this->db->replace("tbemployeelocationdevicetemplate",$templateInserted);
              }
              // mutation in
              if($commandType == "MI"){
                $arrCommandID = explode("|",$commandId);
                $arrMutationIn[] = [
                  "mutation_id"   => $arrCommandID[0],
                  "mutation_c_id" => $arrCommandID[1]
                ];
              }
              // mutation out
              if($commandType == "MO"){
                $arrCommandID = explode("|",$commandId);
                $arrMutationOut[] = [
                  "mutation_id"   => $arrCommandID[0],
                  "mutation_c_id" => $arrCommandID[1]
                ];
              }
              // get all version
              if($commandType == "GAV"){
                load_library(["machinepost_reader"]);
                //$fieldCMD
                $arrCmd = explode("\n",$dataPost,2);
                $arrinfo = $this->machinepost_reader->ReadDeviceInfo($arrCmd[1]);
                $algVersion = !empty($arrinfo['~AlgVer']) ? $arrinfo['~AlgVer'] : "";

                $this->db->where("device_id",$commandId);

                $this->db->update("tbdevice",[
                  "alg_version" => $algVersion
                ]);
              }

              // update picture
              if($commandType == "UP"){
                $arrUpdatePic[] = [
                  "employeelocationdevice_id" => $commandId,
                  "pic_need_update" => "no"
                ];
              }

              // set device config
              if($commandType=="SDC"){
                $this->device_model->reset_send_default_config($exists->device_id);
                /*
                $this->device_model->update([
                  "send_default_config" => "0"
                ],$exists->device_id,$exists->appid);
                */
              }
            }else{
              // error response from device
            }
          }
        }

        // update picture
        if(count($arrUpdatePic)>0){
          load_model(["employeelocationdevice_model"]);
          $this->employeelocationdevice_model->update_batch($arrUpdatePic,"employeelocationdevice_id");
        }

        // mutasi in
        if(count($arrMutationIn)>0){
          $this->load->model("employeemutation_model");
          foreach ($arrMutationIn as $row) {
            $mutationId = $row['mutation_id'];
            $mutationCID= $row['mutation_c_id'];
            $this->employeemutation_model->finishMutationInProcess($mutationCID);
            $complete = $this->employeemutation_model->checkCompleteMutation($mutationId);
            if($complete==true){
              $this->employeemutation_model->setMutationSuccess($mutationId);
            }
          }
        }

        // mutasi out
        if(count($arrMutationOut)>0){
          $this->load->model("employeemutation_model");
          foreach ($arrMutationOut as $row) {
            $mutationId = $row['mutation_id'];
            $mutationCID= $row['mutation_c_id'];
            $this->employeemutation_model->finishMutationOutProcess($mutationCID,$exists->device_id);
            $complete = $this->employeemutation_model->checkCompleteMutation($mutationId);
            if($complete==true){
              $this->employeemutation_model->setMutationSuccess($mutationId);
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

            // menset archive lokasi
            $this->employeeareacabang_model->setArchiveById($row['location_id']);
            // mengambil locationdevice_id
            $locationDevice = $this->employeelocationdevice_model->getLocationDevice($row['location_id'],$row['device_id']);
            if($locationDevice!=false){
              $locationDeviceID = $locationDevice->employeelocationdevice_id;
              // menghapus template dalam device
              $this->employeelocationdevice_model->removeDeviceTemplate($locationDeviceID);
              // menghapus location device
              $this->employeelocationdevice_model->remove($row['location_id'],$row['device_id']);
              // menset resign employee
              $this->employee_model->confirmResign($row['employee_id']);
            }
          }
        }
        if(count($arrIdUserToUpdate)){
          // $this->load->model("employee_model");
          // $this->employee_model->syncToFinalTable($arrIdUserToUpdate);
        }
        
        echo "OK";
      }else{
        echo "Illegal Device";
      }
    }else{
      redirect("ksfjksjdfklj");
    }
  }
}