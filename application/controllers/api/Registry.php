<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Registry extends CI_Controller
{

  var $now;
  var $apikey = "3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9";
  public function __construct()
  {
    parent::__construct();
    $this->now = date('Y-m-d H:i:s');
    // $this->load->library("device_door");
    //$this->load->model("datafinger_model");
  }

  public function registry_post($strAPIkey)
  {
    $arrKey   = explode("-",$strAPIkey);
    $SN       = $arrKey[1];
    $apikey   = $arrKey[0];
    
      $this->load->library("session");
      load_model(['device_model']);

      $exists   = $this->device_model->checkMachineExist($SN);
      
      $dataPost = $this->input->post("postBody");
      
      if($exists!=false && $this->apikey==$apikey){
        $this->session->set_userdata([
          "deviceid" => $exists->device_id
        ]);

        $appid    = $exists->appid;
        $registryCode = hash('crc32', $exists->device_id);
        $response = "RegistryCode=".$registryCode;
        //
        
        if($exists->response_code==3){
          $this->device_model->update2([
            "send_default_config" => "1" 
          ],$exists->device_id,$exists->appid);
        }

        echo json_encode([
                "response" => $response,
                "sessionid" => session_id()
              ]);
      }
    
  }
}
