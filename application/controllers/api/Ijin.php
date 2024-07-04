<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
/**
 *
 */
class Ijin extends REST_Controller
{
  var $now;
  var $apikey = "IAdev-apikey3fapikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9ed48151b389apikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fapikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9e7b7c2f8d891dfc9b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9";
  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
	$this->load->model("ijin_model");
  }

  function index_post(){
	$headers = getRequestHeaders();
	$data = json_decode(file_get_contents('php://input'), true);
    $apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;
    $company_id  = !empty($data['company_id']) ? $data['company_id'] : "";

    if(!empty($apikey) && !empty($company_id)){
		$dataIjin = $this->ijin_model->getAllByCompanyid($company_id);
		if($apikey==$this->apikey && $dataIjin!=false){
			foreach ($dataIjin as $row) {
				$arrOutput[] = [
					"absenceType_id" => "IJ".sprintf("%06d", $row->ijin_id),
					"absenceType_name" => $row->ijin_name,
					"flag_edit"   => null,
					"custom_flag"   => null,
					"flag_cuti"   => null,
					"flag_show_on_cuti"   => null,
					"flag_include"   => null,
					"max_days"   => null,
					"notes"   => $row->ijin_keterangan,
					"user_record"   => $row->ijin_user_add,
					"user_modified"   => $row->ijin_user_modif,
					"dt_record"   => $row->ijin_date_create,
					"dt_modified"   => $row->ijin_date_modif,
					"duration"   => null
				];
			}

		}else{
			$arrOutput = [
				'result'		=> false,
				'message' 		=> "company_id or apikey are not valid"
			];
		}
    }else{
		$arrOutput = [
			'result'		=> false,
			'message' 		=> "company_id or apikey are not defined"
		];
    }
    header("Content-Type:application/json");
    echo json_encode($arrOutput);
  }
}
