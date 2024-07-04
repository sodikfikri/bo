<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
/**
 *
 */
class Device extends REST_Controller
{
  var $now;
  var $apikey = "InterActive-fa040d-adb49aa-c02fe7-b7c2f8d-891dfc9";

  function __construct()
  {
    parent::__construct();

  }

  function index_get($key){
  	load_model(["device_model","subscription_model"]);
    $key = str_replace("_", "-", $key);
    
    if(!empty($key)){
      if($key==$this->apikey){
      	// get appid
    	$appid   = !empty($_GET["app_id"]) ? $_GET["app_id"]  : "";
        $factory = $this->subscription_model->getByAppId($appid);
        if ($factory!=false) {
        	$devices = $this->device_model->getWholeByAppId($appid);
	        $arrDevices = [];
	        $activeDevice = 0;
	        foreach ($devices->result() as $row) {
	          if(strpos($row->device_SN, "suspend")==0){
	            $rangeActive = dateDifference($this->now,$row->device_last_communication);
	            if($row->is_del!=1){
	              $activeDevice++;
	              $arrDevices[] = [
	                "area_device" => $row->area_name,
	                "branch_device" => $row->cabang_name,
	                "sn_device"   => $row->device_SN,
	                "code_device" => $row->device_code,
	                "device_name" => $row->device_name,
	                "ip_device"   => $row->device_ip
	              ];
	            }
	          }
	        }

	        $data = [
	          "app_id" => $appid,
	          "tot_device_active" => $activeDevice,
	          "tot_device" => $devices->num_rows(),
	          "data_device" => $arrDevices
	        ];
	        $output = [
		        'success' 		=> "",
		        'error_code' 	=> "200",
		        'message' 		=> "",
		        'data' 			=> $data
		    ];
        }else{
        	$output = [
		        'success' 		=> "",
		        'error_code' 	=> "401",
		        'message' 		=> "app_id is not valid",
		        'data' 			=> ""
		    ];
        }
      }else{
      	$output = [
	        'success' 		=> "",
	        'error_code' 	=> "401",
	        'message' 		=> "Key is not valid",
	        'data' 			=> ""
	      ];
      }
    }else{
      $output = [
        'success' 		=> "",
        'error_code' 	=> "401",
        'message' 		=> "apikey is not defined",
        'data' 			=> ""
      ];
    }
    echo output_api($output,"json");
  }
}
