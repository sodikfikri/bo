<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Rtdata extends CI_Controller
{

  var $now;
  var $apikey = "3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9";
  public function __construct()
  {
    parent::__construct();
    $this->now = date('Y-m-d H:i:s');
    // $this->load->model("datafinger_model");
    $this->load->library("device_door");
    
  }

  function toDeviceTimeZone($zone){
    $zone = str_replace("UTC","", $zone);
    $zone = str_replace(":","", $zone);
    return $zone;
  }
  
  function calculateZKDateTime()
  {
    date_default_timezone_set('UTC');
    $year = (integer) date('Y');
    $mon  = (integer) date('m');
    $day  = (integer) date('d');
    $hour = (integer) date('H');
    $min  = (integer) date('i');
    $sec  = (integer) date('s');

    $output = (($year - 2000) * 12 * 31 + (($mon - 1) * 31) + $day - 1) * (24 * 60 * 60) + ($hour * 60 + $min) * 60 + $sec;
    return $output;
  }

  /*
  * Identifikasi mesin
  */

  public function rtdata_get($strAPIkey)
  {
    

    $arrKey  = explode("-",$strAPIkey);
    $SN      = $arrKey[1];
    $apikey  = $arrKey[0];
    
    if($this->device_door->isOpenPermission($SN)==true){
      load_model(['device_model','deviceshipments_model']);
      $this->load->helper("responsecode_helper");
      
      //$options = $_GET['options'];
      $exists  = $this->device_model->checkMachineExist($SN);
      // $get= $this->input->post("get");
      // add shipment
      // $dataShipment = [
      //   "post"     => "",
      //   "SN"       => $SN,
      //   "appid"    => (!empty($exists->appid) ? $exists->appid : ''),
      //   "endpoint" => "cdata",
      //   "method"   => "get",
      //   "get"      => json_encode($get)
      // ];

      // $this->deviceshipments_model->insert($dataShipment);

      if($exists!=false && $this->apikey==$apikey){
      	$zkDateTime = $this->calculateZKDateTime();
        	$machineTimeZone = $this->toDeviceTimeZone($exists->cabang_utc);

        	$response = "DateTime=".$zkDateTime.",ServerTZ=".$machineTimeZone;
        	echo $response;
      }else{
        $option = "Illegal Device";
        echo $option;
      }
    }else{
      redirect("jsfjhsdfjhs");
    }
  }
}
