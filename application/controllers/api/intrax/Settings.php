<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
/**
 *
 */
class Settings extends REST_Controller
{
  var $now;
  var $apikey = "IAdev-apikey3fapikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9ed48151b389apikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fapikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9e7b7c2f8d891dfc9b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9";
  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
	$this->load->model("employee_model");
	$this->load->model("subscription_model");
  }

  function index_post(){
    $headers = getRequestHeaders();
    $key  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;
	
	$data = json_decode(file_get_contents('php://input'), true);
	$company_id  = !empty($data['company_id']) ? $data['company_id'] : "";

    if(($company_id!="" OR $company_id=="") && !empty($key)){
		if (strpos(strtoupper($company_id), 'LV') === FALSE){
			$appid = $company_id;
		} else {
			$sqlGetAppid = $this->subscription_model->getAppIdByCompanyID($company_id);
			$appid = $sqlGetAppid->appid;
		}
	  $dataSetting = $this->subscription_model->getByAppId($appid);
      if($key==$this->apikey && $dataSetting!=false){
        $arrSetting = [];
		$checkin = '0';
		$breakin = '0';
		$breakout = '0';
		$checkout = '0';
		$leave = '0';
		$accessPres = explode("|", $dataSetting->access_presence);
		if (!empty($dataSetting->access_presence) && in_array(1, $accessPres)){$checkin = '1';}
		if (!empty($dataSetting->access_presence) && in_array(2, $accessPres)){$breakin = '1';}
		if (!empty($dataSetting->access_presence) && in_array(3, $accessPres)){$breakout = '1';}
		if (!empty($dataSetting->access_presence) && in_array(4, $accessPres)){$checkout = '1';}
		if (!empty($dataSetting->access_presence) && in_array(5, $accessPres)){$leave = '1';}
		if (!empty($dataSetting->company_photo)){$photo = "https://inact.azurewebsites.net/sys_upload/company_profile/".$dataSetting->company_photo;} else { $photo = "https://inact.azurewebsites.net/sys_upload/company_profile/img_avatar_company.png";}
        $arrOutput = [
		  'result'	=> true,
		  'logo' => $photo,
		  'company_name' => $dataSetting->company_name,
          "setting_periodpresence_start" => $dataSetting->date_start_period,
          "setting_periodpresence_end"   => $dataSetting->date_end_period,
          "setting_cutoff"   => $dataSetting->date_end_period,
          "setting_access_checkin"   => $checkin,
          "setting_access_breakin"   => $breakin,
          "setting_access_breakout"   => $breakout,
          "setting_access_checkout"   => $checkout,
          "setting_access_leave"   => $leave,
          "company_id"   => $company_id
        ];
      }else{
		$arrOutput = [
			'result'		=> false,
			'message' 		=> "Appid or Key is not valid"
		];
      }
    }else{
		$arrOutput = [
			'result'		=> false,
			'message' 		=> "Appid and Key must fill"
		];
    }
    header("Content-Type:application/json");
    echo json_encode($arrOutput);
  }
}
