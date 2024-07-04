<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//use Restserver\Libraries\REST_Controller;
//require APPPATH . 'libraries/REST_Controller.php';
//require APPPATH . 'libraries/Format.php';
/**
 *
 */
//class Getrequest extends REST_Controller
class Getrequest extends CI_Controller
{

  var $now;
  var $apikey = "3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9";

  public function __construct()
  {
    parent::__construct();
    $this->load->model("datafinger_model");
    $this->now = date('Y-m-d H:i:s');
    $this->load->library("device_door");

    //$this->methods['users_get']['limit'] = 500; // 500 requests per hour per user/key
    //$this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
    //$this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    $this->load->model("datafinger_model");

    //$dataget = !empty( json_encode($_GET)) ?  json_encode($_GET) : "";
    //$datapost= !empty(file_get_contents( 'php://input' )) ? file_get_contents( 'php://input' ) : "";
  }

  /*
  * Identifikasi mesin
  */
  public function getRequest_get($strAPIkey)
  {
    $this->load->model("device_model");
    $arrKey  = explode("-",$strAPIkey);
    $SN      = $arrKey[1];
    $apikey  = $arrKey[0];
    //$this->device_model->markLastActive($SN);
    if($this->device_door->isOpenPermission($SN)==true){

      load_model(['device_model','deviceshipments_model']);
      $firewallState = $this->device_model->getFirewallState();
	  
	  /*
      if($SN=="CL9M205160766" || $SN=="CL9M205160781" || $SN=="CL9M205160273" || $SN=="CL9M212260073"|| $SN=="CL5Z202560039"){
        $exists      = $this->device_model->checkMachineExist($SN);
        $firewallState=="off";
      }else{
        $exists = false;
      }
	  */
	  
      if($firewallState=="on"){
		$deviceAttr  = $this->device_model->checkMachineAvailable($SN);
        
        $gateKey     = $deviceAttr["gateKey"];
        $exists      = $deviceAttr["deviceData"];
      }else{
        $exists      = $this->device_model->checkMachineExist($SN);
      }
            
      $appid    = !empty($exists->appid) ? $exists->appid : '';
      

      if($exists!=false && $this->apikey==$apikey){
        $deviceID = $exists->device_id;
        $this->load->model("firewall_model");
        $strCommand = "";
        if($exists->response_code==3){
          $this->load->model("thermorequest_model");
          $command = $this->thermorequest_model->getRequest($deviceID,$exists->appid,$exists->device_SN,$exists);
          $strCommand .= $command;
          echo $command;
        }elseif($exists->response_code==4){
          $this->load->model("thermorequestversion4_model");
          $command = $this->thermorequestversion4_model->getRequest($deviceID,$exists->appid,$exists->device_SN,$exists);
          $strCommand .= $command;
          echo $command;

          if($firewallState=="on"){
            if(str_replace(" ", "", $strCommand)==""){
              $this->firewall_model->evaluate($gateKey);
            }
          }
        }elseif($exists->response_code==5){
          $this->load->model("thermorequestversion5_model");
          $command = $this->thermorequestversion5_model->getRequest($deviceID,$exists->appid,$exists->device_SN,$exists);
          $strCommand .= $command;
          echo $command;

          if($firewallState=="on"){
            if(str_replace(" ", "", $strCommand)==""){
              $this->firewall_model->evaluate($gateKey);
            }
          }
        }elseif($exists->response_code==6){
          $this->load->model("thermorequestversion6_model");
          $command = $this->thermorequestversion6_model->getRequest($deviceID,$exists->appid,$exists->device_SN,$exists);
          $strCommand .= $command;
          echo $command;

          if($firewallState=="on"){
            if(str_replace(" ", "", $strCommand)==""){
              $this->firewall_model->evaluate($gateKey);
            }
          }
        }elseif($exists->response_code==7){
          $this->load->model("thermorequestversion7_model");
          $command = $this->thermorequestversion7_model->getRequest($deviceID,$exists->appid,$exists->device_SN,$exists);
          $strCommand .= $command;
          echo $command;

          if($firewallState=="on"){
            if(str_replace(" ", "", $strCommand)==""){
              $this->firewall_model->evaluate($gateKey);
            }
          }
        }else{
          if($exists->alg_version==""){
            // get all version
            $commandInfo = "C:GAV.".$exists->device_id.":INFO";
            $strCommand  .= $commandInfo;
            echo $commandInfo;
          }else{
            // send new user
            $newData = $this->device_model->getNewEmployeeActiveDate($deviceID);

            if($newData->num_rows()>0){

              foreach ($newData->result() as $row) {
                // add user
                $c1 = "C:AU.".$row->employeeareacabang_id."|".$row->employee_id.":DATA USER PIN=".$row->employee_account_no."\tName=".$row->employee_nick_name."\tPri=".$row->employee_level."\tPasswd=".$row->employee_password."\tCard=".$row->employee_card."\tGrp=1\tTZ=\r\n";
                $strCommand .= $c1;
                echo $c1;
              }
            }else{
              // update user
              $updateData = $this->device_model->getNeedUpdateUser($deviceID);

              if($updateData->num_rows()>0){
                foreach ($updateData->result() as $rowUpdate) {
                  $c2 = "C:UU.".$rowUpdate->employeelocationdevice_id.":DATA USER PIN=".$rowUpdate->employee_account_no."\tName=".$rowUpdate->employee_nick_name."\tPri=".$rowUpdate->employee_level."\tPasswd=".$rowUpdate->employee_password."\tCard=".$rowUpdate->employee_card."\tGrp=1\tTZ=\r\n";
                  $strCommand .= $c2;
                  echo $c2;
                }
              }else{
                // delete user
                $deleteData = $this->device_model->getResignEmployee($deviceID);
                if($deleteData->num_rows()>0){
                  foreach ($deleteData->result() as $row) {

                    $c3 = "C:DU.".$row->employeeareacabang_id."|".$row->device_id."|".$row->employee_id.":DATA DEL_USER PIN=".$row->employee_account_no."\r\n";
                    $strCommand .= $c3;
                    echo $c3;
                  }
                }else{
                  // insert template
                  $sqlTemplate = $this->device_model->getTemplateNeedUpdate($deviceID);

                  if($sqlTemplate->num_rows()>0){
                    foreach ($sqlTemplate->result() as $rowTemplate) {
                      if($rowTemplate->employeetemplate_jenis=="fingerprint"){
                        /*
                        $c4 = [
                          "commandid" => "ADDTEMPLATE.".$rowTemplate->employeelocationdevice_id."|".$rowTemplate->employeetemplate_id."|".$rowTemplate->counter,
                          "accountno" => $rowTemplate->employee_account_no,
                          "fid" => $rowTemplate->employeetemplate_index,
                          "template" => $rowTemplate->employeetemplate_template
                        ];
                        */
                        // add template
                        $c4 = "C:AT.".$rowTemplate->employeelocationdevice_id."|".$rowTemplate->employeetemplate_id."|".$rowTemplate->counter.":DATA UPDATE FINGERTMP PIN=".$rowTemplate->employee_account_no."\tFID=".$rowTemplate->employeetemplate_index."\tValid=1\tTMP=".$rowTemplate->employeetemplate_template."\r\n";
                        //$this->db->insert("tbtemplatesentlast",["template"=>$rowTemplate->employeetemplate_template]);
                      }elseif ($rowTemplate->employeetemplate_jenis=="face") {
                        $c4 = "C:AT.".$rowTemplate->employeelocationdevice_id."|".$rowTemplate->employeetemplate_id."|".$rowTemplate->counter.":DATA UPDATE FACE PIN=".$rowTemplate->employee_account_no."\tFID=".$rowTemplate->employeetemplate_index."\tValid=1\tTMP=".$rowTemplate->employeetemplate_template."\r\n";
                      }else{
                        $c4 = "";
                      }
                      $strCommand .= $c4;
                      echo ($c4);
                    }
                  }else{
                    // distribusi mutasi out
                    load_model(["employeemutation_model"]);
                    $mutationOut = $this->employeemutation_model->getMutationOutToday($deviceID);

                    if($mutationOut->num_rows()>0){
                      foreach ($mutationOut->result() as $row) {
                        // mutation out
                        $c6 = "C:MO.".$row->employeemutation_id."|".$row->employeemutation_c_id.":DATA DEL_USER PIN=".$row->employee_account_no."\r\n";
                        $strCommand .= $c6;
                        echo $c6;
                      }
                    }else{
                      // Distribusi mutasi in

                      $mutationIn = $this->employeemutation_model->getMutationInToday($deviceID);
                      if($mutationIn->num_rows()>0){
                        foreach ($mutationIn->result() as $row) {
                          // mutation in
                          $c5 = "C:MI.".$row->employeemutation_id."|".$row->employeemutation_c_id.":DATA USER PIN=".$row->employee_account_no."\tName=".$row->employee_nick_name."\tPri=0\tPasswd=".$row->employee_password."\tCard=".$row->employee_card."\tGrp=1\tTZ=\r\n";
                          $strCommand .= $c5;
                          echo $c5;
                        }
                      }else{
                        // update picture
                        load_model(["employeelocationdevice_model"]);
                        $sqlPicNeedUpdate = $this->employeelocationdevice_model->getPicNeedUpdate($deviceID,$exists->appid);
                        if($sqlPicNeedUpdate->num_rows()>0){
                          foreach ($sqlPicNeedUpdate->result() as $row) {
                            
                            $linkImage = FCPATH.'sys_upload\employeepic\\'.$row->image;
                            // jika ada file
                            if($row->image!=""){
                              if(file_exists($linkImage)){
                                // ambil file
                                $decodedImage = file_get_contents($linkImage);
                                
                                // diencoded 
                                $encodedImage  = base64_encode($decodedImage);
                                
                                // dikirim ke device
                                $c8 = "C:UP.".$row->employeelocationdevice_id.":DATA UPDATE USERPIC PIN=".$row->employee_account_no."\tSize=".strlen($row->picture)."\tContent=".$encodedImage."\r\n";
                                $strCommand .= $c8;
                                echo $c8;
                              }
                            }
                          }
                        }else{
                          $this->load->model("device_model");
                          if($this->device_model->isNeedReboot($SN)==true){
                            $c9 = "C:".$SN."122:REBOOT";
                            $strCommand .= $c9;
                            echo $c9;
                            $this->device_model->setFinishReboot($SN);
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }
        }
        if($firewallState=="on"){
          if(str_replace(" ", "", $strCommand)==""){
            $this->firewall_model->evaluate($gateKey);
          }
        }
      }else{
        echo "";
      }
    }else{
      redirect("not found");
    }
  }
}
