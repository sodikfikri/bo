<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Push extends CI_Controller
{

  var $now;
  var $apikey = "3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9";
  public function __construct()
  {
    parent::__construct();
    $this->now = date('Y-m-d H:i:s');
    $this->load->library("device_door");
    //$this->load->model("datafinger_model");
  }

  public function push_post($strAPIkey)
  {
    
    $arrKey   = explode("-",$strAPIkey);
    $SN       = $arrKey[1];
    $apikey   = $arrKey[0];
    if($this->device_door->isOpenPermission($SN)==true){
      load_model(['device_model']);
      $exists   = $this->device_model->checkMachineExist($SN);
      
      $dataPost = $this->input->post("postBody");
      $sessionID= $this->input->post("sessionID");
      if($exists!=false && $this->apikey==$apikey){
        
        //$registryCode = "f1736d93f";
        
        $response =   "ServerVersion=2.4.1\n".
                      "ServerName=InAct\n".
                      "PushVersion=2.4.1\n".
                      "ErrorDelay=60\n".
                      "RequestDelay=2\n".
                      "TransTimes=00:00 14:00\n".
                      "TransInterval=1\n".
                      "TransTables=User Transaction\n".
                      "Realtime=1\n".
                      "SessionID=".$sessionID."\n".
                      "TimeoutSec=10";
        //header("Content-Length: ".strlen($response));
        //header("Content-Type: text/plain;charset=ISO-8859-1");
        echo $response;
      }
    }else{
      redirect("hsfjshfhskfhks");
    }
  }
}
