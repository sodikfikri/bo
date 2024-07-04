<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Registration_access extends CI_Controller
{

  var $listMenu = "";
  function __construct()
  {
    parent::__construct();
    $this->load->model("system_model");
    $this->timestamp = date("Y-m-d H:i:s");

    $languange = !empty($this->session->userdata('lang')) ? $this->session->userdata('lang') :"en";
    $this->load->library("gtrans/gtrans",["lang" => $languange]);

    $this->load->model("menu_model");
    
    $this->system_model->checkSession(25);
    $this->listMenu = $this->menu_model->list_menu();
  }

  function index(){
    $this->load->model("subscription_model");

    $appid = $this->session->userdata("ses_appid");
    $companyData = $this->subscription_model->getByAppId($appid);

    $data["companyData"]  = $companyData;
	
    $this->load->model("region_model");
    $data["dataCountry"]     = $this->region_model->getCountry();
    $parentViewData = [
      "title"      => $this->gtrans->line("Link Registration Settings"),  // title page
      "content"    => "registration_access",  // content view
      "viewData"   => $data,
      "listMenu"   => $this->listMenu,
      "varJS"      => [
        "url" => base_url(),
        "globalCountryID" => $companyData->company_country,
        "provinceID" => $companyData->company_province,
        "cityID" => $companyData->company_city
      ],
      "externalJS" => [
        base_url("asset/js/company_setting.js")
      ]
    ];
    $this->load->view("layouts/main",$parentViewData);
    $this->gtrans->saveNewWords();
  }

  function updateRegistration_link(){
    $this->load->library("encryption_org");
    $this->load->model("subscription_model");
    $appid = $this->session->userdata("ses_appid");
	$add = (!empty($this->input->post('checkRegistrationAdd'))) ? $this->input->post('checkRegistrationAdd') : "";
	$import = (!empty($this->input->post('checkRegistrationImport'))) ? $this->input->post('checkRegistrationImport') : "";
	$simpeg = (!empty($this->input->post('checkRegistrationSimpeg'))) ? $this->input->post('checkRegistrationSimpeg') : "";
	
	$dataUpdate= [
		"allow_regis_link"=> 1,
		"access_granted_link"=> $add.$import.$simpeg
	];
	$this->subscription_model->updateByAppId($dataUpdate,$appid);
	$output = [
		"response" => "success",
		"code" => "200",
		"msg" => "registration link updated"
	];

    /**if($fileName!=""){
		$dataUpdate= [
			"allow_regis_link"=> 1
		];
		$this->subscription_model->updateByAppId($dataUpdate,$appid);
		$output = [
			"response" => "success",
			"code" => "200",
			"msg" => "registration link updated"
		];
    }else{
		$output = [
            "response" => "error",
            "code" => "500",
            "msg" => "failed registration link"
        ];
    }**/
	echo json_encode($output);
  }
}
