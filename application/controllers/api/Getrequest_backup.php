<?php
//use Restserver\Libraries\REST_Controller;
require APPPATH . 'libraries/REST_Controller.php';
//require APPPATH . 'libraries/Format.php';
/**
 *
 */
class Getrequest extends REST_Controller
//class Getrequest extends CI_Controller
{

  var $now;

  public function __construct()
  {
    parent::__construct();
    $this->load->model("datafinger_model");
    $this->now = date('Y-m-d H:i:s');

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
    load_model(['device_model']);
    $this->load->library("encryption_org");
    $SN      = $_GET["SN"];

    $exists  = $this->device_model->checkMachineExist($SN);

    if($exists){
      $deviceID = $exists->device_id;
      load_model(['device_model']);
      // send new user
      $newData = $this->device_model->getNewEmployeeActiveDate($deviceID);

      if($newData->num_rows()>0){

        foreach ($newData->result() as $row) {
          //echo 'C:122:DATA USER PIN='.$row->employee_account_no.'\tName='.$row->employee_nick_name.'\tPri=0\r\n';
          $c1 = "C:ADDUSER.".$row->employeeareacabang_id."|".$row->employee_id.":DATA USER PIN=".$row->employee_account_no."\tName=".$row->employee_nick_name."\tPri=0\tPasswd=".$row->employee_password."\tCard=".$row->employee_card."\tGrp=1\tTZ=\r\n";

        }
      }else{
        // update user
        $updateData = $this->device_model->getNeedUpdateUser($deviceID);
        if($updateData->num_rows()>0){
          foreach ($updateData->result() as $rowUpdate) {
            $c1 = "C:UPDATEUSER.".$rowUpdate->employeelocationdevice_id.":DATA USER PIN=".$rowUpdate->employee_account_no."\tName=".$rowUpdate->employee_nick_name."\tPri=0\tPasswd=".$rowUpdate->employee_password."\tCard=".$rowUpdate->employee_card."\tGrp=1\tTZ=\r\n";
          }
        }else{
          // delete user
          $deleteData = $this->device_model->getResignEmployee($deviceID);
          if($deleteData->num_rows()>0){
            foreach ($deleteData->result() as $row) {
              $c1 = "C:DELETEUSER.".$row->employeeareacabang_id."|".$row->device_id."|".$row->employee_id.":DATA DEL_USER PIN=".$row->employee_account_no."\r\n";
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
                  $c1 = "C:"."ADDTEMPLATE.".$rowTemplate->employeelocationdevice_id."|".$rowTemplate->employeetemplate_id."|".$rowTemplate->counter.":DATA UPDATE FINGERTMP PIN=".$rowTemplate->employee_account_no."\tFID=".$rowTemplate->employeetemplate_index."\tValid=1\tTMP=".$rowTemplate->employeetemplate_template."\r\n";
                  //$this->db->insert("tbtemplatesentlast",["template"=>$rowTemplate->employeetemplate_template]);
                }elseif ($rowTemplate->employeetemplate_jenis=="face") {
                  $c1 = "";
                }
              }
            }else{
              $c1 = "";
            }
          }
        }
      }
      $outputLen = strlen($c1);
      if($outputLen>0){
        $this->output->parse_exec_vars = FALSE;
        header('Connection : Keep-Alive');
        date_default_timezone_set('GMT');
        header_remove("Set-Cookie");
        header_remove("Host");
        $gmtDate = date("D, d M Y H:i:s e");

        $this->output->set_content_type('text/plain');
        $this->output->set_status_header(200);
        $this->output->set_header('Vary : Accept-Encoding');

        $this->output->set_header('Content-Length : '.strlen($c1));
        $this->output->set_header('Keep-Alive : timeout=5, max=100');
        $this->output->set_header('Date: '.$gmtDate);
        $this->output->set_header('Server :'.'Apache/2.4.35 (Win64) OpenSSL/1.1.1b PHP/7.2.19');
        $this->output->set_header('Cache-Control:no-store, no-cache, must-revalidate');
        $this->output->set_output($c1);

        //echo $c1;
      }else{
        echo "OK";
      }
    }else{
      echo "OK";
    }
  }
}
