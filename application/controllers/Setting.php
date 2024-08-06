<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Setting extends CI_Controller
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
    
    $this->system_model->checkSession(19);
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
      "title"      => $this->gtrans->line("Company Setting"),  // title page
      "content"    => "setting",  // content view
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

  function getProvince(){
    $this->load->model("region_model");
    $countryID = getCountryID($this->input->post('countryID'));
    $resSQL = $this->region_model->getProvinceByCountry($countryID);
    if($resSQL){
      $dataArray = (array) $resSQL;
      echo json_encode($dataArray);
    }
  }

  function getCity(){
    $this->load->model("region_model");
    $provinceID = getProvinceID($this->input->post('provinceID'));
    $resSQL = $this->region_model->getCityByProvince($provinceID);
    if($resSQL){
      $dataArray = (array) $resSQL;
      echo json_encode($dataArray);
    }
  }

  function updateSetting(){
    $this->load->model("region_model");
    $this->load->model("subscription_model");
    $companyName  = $this->input->post("companyName");
    $country      = $this->input->post("country");
    $province     = $this->input->post("province");
    $city         = $this->input->post("city");
    $address      = $this->input->post("address");
    $telp         = $this->input->post("telp");
    //$email        = $this->input->post("email");
    $website      = $this->input->post("website");
    $companysize  = $this->input->post("companysize");
    $date_start  = $this->input->post("date_start");
    $date_end  = $this->input->post("date_end");
	$access   	  = $this->input->post("access");
    $appid = $this->session->userdata("ses_appid");

    $countryName = !empty($country)  ? $country : "";
    $provinceName= !empty($province) ? $province : "";
    $cityName    = !empty($city)     ? $city : "";
	
		$dataUpdate = [
		  "company_name" => $companyName,
		  "company_addr" => $address,
		  "company_city" => $cityName,
		  "company_province" => $provinceName,
		  "company_country" => $countryName,
		  "company_telp" => $telp,
		  //"company_email" => $email,
		  "company_websiteurl" => $website,
		  "company_size" => $companysize,
		  "date_start_period" => $date_start,
		  "date_end_period" => $date_end,
		  "access_presence" => $access
		];
	

    $res = $this->subscription_model->updateByAppId($dataUpdate,$appid);
    if($res){
      echo "success";
	  //print_r($dataUpdate);
    }else{
      echo "failed";
	  //print_r($dataUpdate);
    }
  }
  function openNotif(){
    $this->load->model("notif_model");
    $id = $this->encryption_org->decode($this->input->post('id'));
    $dataNotif = $this->notif_model->getById($id);
    if($dataNotif->num_rows()>0){
      $rowNotif = $dataNotif->row();

      $output =[
        "title" => $rowNotif->notif_header,
        "content" => html_entity_decode($rowNotif->notif_content)
      ];

      // close notifikasi
      $this->notif_model->closeNotif($id);
      echo json_encode($output);
    }
  }
  
  function linkRegister(){
    $this->load->library("encryption_org");

    $email_intrax = $this->input->post("email_intrax");
    $link_intrax = $this->input->post("link_intrax");
    $result    = $this->sendLinkRegister($email_intrax,$link_intrax);
    if($result){
      echo "success";
    }else{
      echo "failed";
    }
  }
  
  function sendLinkRegister($email,$link)
  {
    // kirim email
    $body_msg = '
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html>
      <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
      <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
      </head>
      <style type="text/css" data-hse-inline-css="true">
        @media only screen and (max-width: 385px) {
        .main-page{
          background-color:#dfdfdf;
          padding:5px;"
        }
        @media only screen and (min-width: 386px) {
        .main-page{
          background-color:#dfdfdf;
          padding:40px;"
        }
      </style>
      <body style="font-family: \'Roboto\', sans-serif;">
        <div class="main-page"  >
		  <img src="https://interactive.co.id/product/images/assets/intrax/logo.png" style="width: 200px;">
          <div style="max-width:653px; background-color:#ffffff;margin-left:auto; margin-right:auto; margin-top:40px; margin-bottom:40px;" >
            <div style="vertical-align: middle; padding:30px 30px 30px 30px;">
              <center style="padding-bottom:30px"></center>
                <hr style="height: 1px;color: #dee0e3;background-color: #dee0e3;border: none;">
                <p style="font-family: Roboto;
                      font-size: 24px;
                      font-weight: bold;
                      font-style: normal;
                      font-stretch: normal;
                      line-height: 1.17;
                      letter-spacing: normal;
                      text-align: left;
                      color: rgba(0, 0, 0, 0.7);
                      ">
                      Hello '.$email.',</p>
                      <p style="font-family: Roboto;
                      font-size: 15px;
                      font-weight: normal;
                      font-style: normal;
                      font-stretch: normal;
                      line-height: 1.67;
                      letter-spacing: normal;
                      text-align: left;
                      color: rgba(0, 0, 0, 0.7);
                      ">Thank you for using InAct<b></b></p>
                      <p style="font-family: Roboto;
                      font-size: 15px;
                      font-weight: normal;
                      font-style: normal;
                      font-stretch: normal;
                      line-height: 1.67;
                      letter-spacing: normal;
                      text-align: left;
                      color: rgba(0, 0, 0, 0.7);
                      ">To register your institution and employees, please click the following link to go directly to the registration form:</p>
                      <p style="font-family: Roboto;
                        font-size: 15px;
                        font-weight: normal;
                        font-style: normal;
                        font-stretch: normal;
                        line-height: 1.67;
                        letter-spacing: normal;
                        text-align: left;
                        color: rgba(0, 0, 0, 0.7);
                        "><b>'.$link.'</b></p>
                      <br>
                      <p style="font-family: Roboto;
                      font-size: 15px;
                      font-weight: normal;
                      font-style: normal;
                      font-stretch: normal;
                      line-height: 1.67;
                      letter-spacing: normal;
                      text-align: left;
                      color: rgba(0, 0, 0, 0.7);">
                          If you experience problems using InterActive Products, please contact our Customer Support team through  <font style="font-weight: bold;color: #00cbce;">(031) 535 5353</font> <b>Ext. 34</b>
                      </p>
                      <br>
                      <p style="font-family: Roboto;
                      font-size: 15px;
                      font-weight: normal;
                      font-style: normal;
                      font-stretch: normal;
                      line-height: 1.67;
                      letter-spacing: normal;
                      text-align: left;
                      color: rgba(0, 0, 0, 0.7);">Greetings,</p>
                      <p style="font-family: Roboto;
                      font-size: 15px;
                      font-weight: normal;
                      font-style: normal;
                      font-stretch: normal;
                      line-height: 1.67;
                      letter-spacing: normal;
                      text-align: left;
                      color: rgba(0, 0, 0, 0.7);">Interactive Team,</p>
                  </div>
              </div>
              <center>
                  <img src="https://cloud.interactive.co.id/mybilling/asset/img/interactive.png" height="15px" />
                  <p style="font-family: Roboto;
                  font-size: 12px;
                  font-weight: 500;
                  font-style: normal;
                  font-stretch: normal;
                  line-height: 1.67;
                  letter-spacing: normal;
                  text-align: center;
                  color: rgba(0, 0, 0, 0.38);">Jl. Ambengan No. 85, Surabaya 60136, Indonesia <br>
                  @ '.date('Y').', InterActive Technologies Corp. All rights reserved.</p>
                  <p style="font-family: Roboto;
                  font-size: 12px;
                  font-weight: 500;
                  font-style: normal;
                  font-stretch: normal;
                  line-height: 1.83;
                  letter-spacing: normal;
                  text-align: center;
                  color: rgba(0, 0, 0, 0.38);"><a href="https://www.youtube.com/user/interactivecorp">Youtube</a> - <a href="https://www.instagram.com/interactive_tech/">Instagram</a> -  <a href="https://www.facebook.com/InteractiveTec/">Facebook</a> - <a href="https://www.interactive.co.id">Website</a></p>
              </center>
          </div>
      </body>
    </html>';
    $this->load->library("intermailer");
    $this->intermailer->initialize();
    // $this->intermailer->initialize_allin();
    $this->intermailer->to([$email=>$email]);
    $this->intermailer->set_content("Link for InAct Registration",$body_msg,"Alt Body tes");
    if($this->intermailer->send())
    {
      return true;
    }else{
      return false;
    }
  }
  
  function importPhotocompany(){
    $this->load->library("encryption_org");
    $this->load->model("subscription_model");
    
    $config['upload_path']="./sys_upload/company_profile";
    $config['allowed_types']='jpeg|jpg|png|JPEG|JPG|PNG';
    $config['encrypt_name'] = TRUE;
    // $config['max_size']    = 2048; // 2mb
    $this->load->library('upload',$config);
    
    $fileName = "";
    if(!$this->upload->do_upload("photocompany")){
      $data = array('upload_data' => $this->upload->data());

      $judul= $this->input->post('judul');
      $fileName = $data['upload_data']['file_name'];
      $output = [
				"response" => "error",
				"code" => "500",
				"msg" => $this->upload->display_errors()
			];
      echo json_encode($output);
      return;
    }
    $data_upload = array('upload_data' => $this->upload->data());

    $fileName = $data_upload['upload_data']['file_name'];
    // $fileName = $upload["filename"];
    $appid = $this->session->userdata("ses_appid");
    
    if($fileName!=""){
      $error = "";

		if($error==""){
			$dataUpdate= [
				"company_photo"=> $fileName
			];
			$this->subscription_model->updateByAppId($dataUpdate,$appid);
			$output = [
				"response" => "success",
				"code" => "200",
				"msg" => "photo updated"
			];
		}else{
			$output = [
				"response" => "error",
				"code" => "500",
				"msg" => "failed upload"
			];
		}
		
    }else{
		$output = [
            "response" => "error",
            "code" => "500",
            "msg" => "failed upload"
        ];
    }
	echo json_encode($output);
  }
  
  function do_upload(){
    $config['upload_path']="./sys_upload/company_profile";
    $config['allowed_types']='jpeg|jpg|png|JPEG|JPG|PNG';
    $config['encrypt_name'] = TRUE;
     $config['max_size']    = 2048; // 2mb
    $this->load->library('upload',$config);
    if($this->upload->do_upload("photocompany")){
      $data = array('upload_data' => $this->upload->data());

      $judul= $this->input->post('judul');
      $fileName = $data['upload_data']['file_name'];
      $error    = "";
    }else{
      $fileName = "";
      $error    = strip_tags($this->upload->display_errors());
    }
    return [
      "error"    => $error,
      "filename" => $fileName
    ];
  }
}
