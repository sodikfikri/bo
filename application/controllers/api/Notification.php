<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Notification extends REST_Controller
{
	var $apikey = "IAdev-apikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9";

	function __construct()
	{
		parent::__construct();
	}

	function pushNotification_post(){
		$apikey = !empty($this->input->post("apikey")) ? $this->input->post("apikey") : "";
		if($apikey==$this->apikey){
			$notifHeader = $this->input->post("header");
			$notifContent= $this->input->post("content");
			$appid  	 = $this->input->post("appid");
			$notifStatus = "open";

			$now = date("Y-m-d");
			$dataInsert  = [
				"appid" => $appid,
				"notif_header" => $notifHeader,
				"notif_content" => htmlentities($notifContent),
				"status" => $notifStatus,
				"date_create" => $now
			];

			$this->load->model("notif_model");
			$result = $this->notif_model->insert($dataInsert);
			if($result){
				$arrOutput = [
					'success' 		=> "OK",
					'error_code' 	=> "200",
					'message' 		=> "Notif save successfully",
					'data' 			=> ""
				];
			}else{
				$arrOutput = [
					'success' 		=> "",
					'error_code' 	=> "500",
					'message' 		=> "Internal Server Error",
					'data' 			=> ""
				];
			}
		}else{
			$arrOutput = [
				'success' 		=> "",
				'error_code' 	=> "401",
				'message' 		=> "API Key Not Match",
				'data' 			=> ""
			];
		}
		echo output_api($arrOutput,"json");
	}
}
