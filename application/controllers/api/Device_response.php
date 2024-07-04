<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
/**
* 
*/

class Device_response extends REST_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library("string_manipulation");
	}

	function getdeviceresponse_post(){
		$this->load->helper("responsecode_helper");

		$apikey = $this->input->post("apikey");
		$SN     = $this->input->post("SN");
		if ($apikey==$this->string_manipulation->hash_authkey(date("dmY"))) {
			$responsecode_id = 2;
		    $response_code   = getResponseCode($responsecode_id,$SN,"+7");

		    echo $response_code;
		}
		
	}
	
	/*
	function toDeviceTimeZone($zone){
    
    $zone = str_replace("UTC","", $zone);
    if(substr($zone, 0,1)=="+"){
        $zone = substr($zone, 1);
        if(substr($zone, 0,1)==0){
            $zone = substr($zone, 1);
        }
        $arrTime = explode(":", $zone);
        if($arrTime[1]=="00"){
            $zone = $arrTime[0];
        }else{
            $zone = $arrTime[0].".".$arrTime[1];
        }
    }else{
        $zone = substr($zone, 1);
        if(substr($zone, 0,1)==0){
            $zone = substr($zone, 1);
        }
        $arrTime = explode(":", $zone);
        if($arrTime[1]=="00"){
            $zone = $arrTime[0];
        }else{
            $zone = $arrTime[0].".".$arrTime[1];
        }
        $zone = "-".$zone;
    }
    return $zone;
  }
  */

}