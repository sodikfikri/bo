<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Employee extends REST_Controller
{
  var $now;
  var $apikey = "IAdev-apikey3fapikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9ed48151b389apikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fapikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9e7b7c2f8d891dfc9b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9";

  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
  }

  function getActive_post(){
    $headers = getRequestHeaders();
        
    $apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;
    
    if($apikey!=""){
        if($apikey==$this->apikey){
            $companyId  = !empty($this->input->post("company_id")) ? $this->input->post("company_id") : null;
            $email      = !empty($this->input->post("pic_email"))  ? $this->input->post("pic_email") : null;
            if($companyId!=null){
                if($email!=null){
                    load_model(["subscription_model"]);
                    $companyData = $this->subscription_model->getIntraxCompany($companyId,$email);
                    if($companyData!=null){
                        load_model(["employee_model"]);
                        $dataEmployee = $this->employee_model->getActiveIntrax($companyData->appid);
                        if($dataEmployee!=false){
                            $dataOutput = [];
                            
                            foreach($dataEmployee->result() as $row){
                                $dataOutput[] = [
                                    'employee_id' => $row->employee_id,
                                    'checklog_id' => $row->employee_account_no,
                                    'name' => $row->employee_full_name,
                                    'intrax_pin' => $row->intrax_pin,
                                    'gender' => $row->gender,
                                    'birthday' => $row->birthday,
                                    'phone_number' => $row->phone_number,
                                    'email' => $row->email,
                                    'address' => $row->address
                                ];
                            }
                            $arrOutput = [
                                'success' 		=> true,
                                'error_code' 	=> "",
                                'message' 		=> "",
                                'data' 			=> $dataOutput
                            ];
                        }else{
                            $arrOutput = [
                                'success' 		=> false,
                                'error_code' 	=> "401",
                                'message' 		=> "no employee filtered",
                                'data' 			  => ""
                            ];
                        }
                    }else{
                        $arrOutput = [
                            'success' 		=> false,
                            'error_code' 	=> "401",
                            'message' 		=> "parameter not valid",
                            'data' 			  => ""
                        ];
                    }
                }else{
                    $arrOutput = [
                        'success' 		=> false,
                        'error_code' 	=> "401",
                        'message' 		=> "pic_email not defined",
                        'data' 			  => ""
                    ];
                }
            }else{
                $arrOutput = [
                    'success' 		=> false,
                    'error_code' 	=> "401",
                    'message' 		=> "company_id not defined",
                    'data' 			=> ""
                ];
            }
            
        }else{
            $arrOutput = [
            'success' 		=> false,
            'error_code' 	=> "401",
            'message' 		=> "apikey is not valid",
            'data' 			=> ""
            ];
        }
    }else{
        $arrOutput = [
            'success' 		=> false,
            'error_code' 	=> "401",
            'message' 		=> "apikey is not defined",
            'data' 			  => ""
        ];
    }
    echo output_api($arrOutput,"json");
  }
  
  function checkEmail_post(){
	if (!file_exists("application/controllers/api/intrax/logs/log-checkEmail-".date("Y-m-d").".txt")) {
		$myfile = fopen("application/controllers/api/intrax/logs/log-checkEmail-".date("Y-m-d").".txt", "a");
	} else {
		$myfile = fopen("application/controllers/api/intrax/logs/log-checkEmail-".date("Y-m-d").".txt", "a");
	}
	
	$data = json_decode(file_get_contents('php://input'), true);
	$txt = "-------------------------------------------------------------\n";
	fwrite($myfile, $txt);
	$txt = "REQUEST-".date("Ymd-His")."->".json_encode($data)."\n";
	fwrite($myfile, $txt);
    $headers = getRequestHeaders();
    $apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;
	$flag  = !empty($data['flag']) ? $data['flag'] : "";
	$email  = !empty($data['email']) ? $data['email'] : "";
	$company_id  = !empty($data['company_id']) ? $data['company_id'] : "";
	$arrData = [];
	$arrLocation = [];
	load_model([
	"employee_model",
	"cabang_model",
	"subscription_model",
	"employeeareacabang_model",
	"otp_model"
	]);
    if($apikey!=""){
		if($apikey==$this->apikey){
			if ($flag==0) {
				if($company_id!="" or $company_id==""){
					if ($company_id==""){
						$sqlGetAppid = $this->employee_model->getAppIdByEmail($email);
						$imageHeader = '<img src="https://inact.interactiveholic.net/bo/asset/images/Logo_inact.png" style="width: 200px;">';
						$appName = 'InAct HRIS';
					} else {
						$sqlGetAppid = $this->subscription_model->getAppIdByCompanyID($company_id);
						$imageHeader = '<img src="https://interactive.co.id/product/images/assets/intrax/logo.png" style="width: 200px;">';
						$appName = 'InTrax';
					}
					$checkEmailExist = $this->employee_model->checkEmailExist($email,$sqlGetAppid->appid);
					if($checkEmailExist==false){
						$dataEmployee = $this->employee_model->getEmployeeByEmail($email,$sqlGetAppid->appid);
						$generateOTP = $this->otp_model->generate($dataEmployee->employee_id,"register_intrax","email");
						$key = "423a1aa70eca39af";
						$iv = "506e6fb150550765";
						$data = $generateOTP;
						$ciphertext = openssl_encrypt($data, "aes-128-cbc", $key, 0, $iv);
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
							'.$imageHeader.'
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
										Hello '.$dataEmployee->employee_nick_name.',</p>
										<p style="font-family: Roboto;
										font-size: 15px;
										font-weight: normal;
										font-style: normal;
										font-stretch: normal;
										line-height: 1.67;
										letter-spacing: normal;
										text-align: left;
										color: rgba(0, 0, 0, 0.7);
										">
										You are doing login on '.$appName.' app. 
										Here is the One-Time Password that must be entered</p>
										<p style="font-family: Roboto;
										font-size: 20px;
										font-weight: normal;
										font-style: normal;
										font-stretch: normal;
										line-height: 1.67;
										letter-spacing: normal;
										text-align: left;
										color: rgba(0, 0, 0, 0.7);
										"><strong>One Time Password: </strong>
											'.$generateOTP.'
										</p>
										
										<p style="font-family: Roboto;
										font-size: 15px;
										font-weight: normal;
										font-style: normal;
										font-stretch: normal;
										line-height: 1.67;
										letter-spacing: normal;
										text-align: left;
										color: rgba(0, 0, 0, 0.7);">
											If you experience problems using InterActive Products, please contact our Customer Support team through  <font style="font-weight: bold;color: #00cbce;">(031) 535 5353</font> <b>Ext. 16 or <a href="https://wa.me/6285879123123" target="_blank">+62 858-79-123-123</a></b>
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
						// $this->intermailer->initialize();
						$this->intermailer->initialize_allin();
						$this->intermailer->to([$email=>$email]);
						$this->intermailer->set_content($appName." Employee OTP",$body_msg,"Alt Body tes");
						if($this->intermailer->send())
						{
							$arrOutput = [
							 'result'				=> true,
							 'message' 				=> "Please check your email for a verification pin",
							 'pin'	 				=> $ciphertext,
							 'activation' 			=> false,
							 'registration' 		=> true,
							 'data' 				=> $arrData,
							 'location' 			=> $arrLocation,
							 'absence_with_location'=> true,
							 'license_url'			=> "https://licenses.genesysindonesia.co.id/api/",
							 'license_token'		=> "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiNjkzNTQwOWYzMWIyNDFlNDIyZjYyMjExOTVlMTlhNTNmZmQ3MzJlYjVhOGYxNzM2ZGRhZTEyNzZhMjU1YWM2NTBmNzkxZWYyMWM0NmYyN2YiLCJpYXQiOjE2NTgzODkyNjAsIm5iZiI6MTY1ODM4OTI2MCwiZXhwIjoxNjg5OTI1MjYwLCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.VgO9GK9tFSuu7hHpabE1DWm3RFkA0NfL-Q92EBRz_h1lZZAJH65Qtq7p1_HlPyt3TYE9FncVFsDjV1LUsAIMzNquJMcQYNQ-pYfj3irFTqp0LCAEOcrc1c7Pm5J2YUHPOGOQGeRB3_SZzUpejHcWICKhXMQMIRK1Ss3hOyXXnv4sH4B7uujUsd0_99q6gz4ufySzmbChBelzl0PrcXbHiRHAiQKfcsfowvrU1pe2mYgTUnkjdKyRXIy-XJ5mL7NQ4Bq3ZPZROPQpS9YT0TLfSRxiocfqutXmYgx4jwjl0jh7fqF7fM8Y4VcKSKRCBL-Pqg-bK-tPzLBK4kOrh-6Ngnp3WJoL0pUKLgbGEeVysU9Ehu__REvJCamdM5aipt6ym1B0hZxfHji4DoP0jHVG8Xxp6nn0vMqUi3gdZp-hY1JawJusqtj3KEeejfLfB-oKlZKqNXmXGNWagDeNLh9HTM2ry4PTGMKAFXVvQcafmE2OEovDQvzuB2npbO3cDPvyf3nMPgzMiDcE79051R8ojFkf82SypYzMcMunhCiBhd-2zmZ7w5NVYuG3bgvd76qBh6lG8hV1aMV5Jz_F14rxIfsKzRz4-y9sO2xtRivXIIstsV5xU6Pr3gjl__3cN-2KRFx0hnMhmYICPxOBApdfOuqj2G5ZJ3kNGUk9RESoMCs"
						   ];
						}else{
							$arrOutput = [
							 'result'				=> false,
							 'message' 				=> "SMTP error"
							];
						}
					} else {
						$arrOutput = [
						 'result'				=> false,
						 'message' 				=> "Email have already been used",
						 'activation' 			=> false,
						 'registration' 		=> false,
						 'data' 				=> $arrData,
						 'location' 			=> $arrLocation,
						 'absence_with_location'=> true
					   ];
					}					
				} else {
				   $arrOutput = [
					 'result'		=> false,
					 'message' 		=> "company_id not defined"
				   ];
				}
			} elseif($flag==1){
				if($company_id!="" or $company_id==""){
					if ($company_id==""){
						$sqlGetAppid = $this->employee_model->getAppIdByEmail($email);
						$imageHeader = '<img src="https://inact.interactiveholic.net/bo/asset/images/Logo_inact.png" style="width: 200px;">';
						$appName = 'InAct HRIS';
					} else {
						$sqlGetAppid = $this->subscription_model->getAppIdByCompanyID($company_id);
						$imageHeader = '<img src="https://interactive.co.id/product/images/assets/intrax/logo.png" style="width: 200px;">';
						$appName = 'InTrax';
					}
					$checkEmailExist = $this->employee_model->checkEmailExist($email,$sqlGetAppid->appid);
					if($checkEmailExist==true){
						$dataEmployee = $this->employee_model->getEmployeeByEmail($email,$sqlGetAppid->appid);
						$generateOTP = $this->otp_model->generate($dataEmployee->employee_id,"login_intrax","email");
						$key = "423a1aa70eca39af";
						$iv = "506e6fb150550765";
						$data = $generateOTP;
						$ciphertext = openssl_encrypt($data, "aes-128-cbc", $key, 0, $iv);
						//$getOTP = $this->otp_model->getActiveOTP($dataEmployee->employee_id,"login_intrax","email");
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
							'.$imageHeader.'
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
										Hello '.$dataEmployee->employee_nick_name.',</p>
										<p style="font-family: Roboto;
										font-size: 15px;
										font-weight: normal;
										font-style: normal;
										font-stretch: normal;
										line-height: 1.67;
										letter-spacing: normal;
										text-align: left;
										color: rgba(0, 0, 0, 0.7);
										">
										You are doing login on '.$appName.' app. 
										Here is the One-Time Password that must be entered</p>
										<p style="font-family: Roboto;
										font-size: 20px;
										font-weight: normal;
										font-style: normal;
										font-stretch: normal;
										line-height: 1.67;
										letter-spacing: normal;
										text-align: left;
										color: rgba(0, 0, 0, 0.7);
										"><strong>One Time Password: </strong>
											'.$generateOTP.'
										</p>
										
										<p style="font-family: Roboto;
										font-size: 15px;
										font-weight: normal;
										font-style: normal;
										font-stretch: normal;
										line-height: 1.67;
										letter-spacing: normal;
										text-align: left;
										color: rgba(0, 0, 0, 0.7);">
											If you experience problems using InterActive Products, please contact our Customer Support team through  <font style="font-weight: bold;color: #00cbce;">(031) 535 5353</font> <b>Ext. 16 or <a href="https://wa.me/6285879123123" target="_blank">+62 858-79-123-123</a></b>
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
						// $this->intermailer->initialize();
						$this->intermailer->initialize_allin();
						$this->intermailer->to([$email=>$email]);
						$this->intermailer->set_content($appName." Employee OTP",$body_msg,"Alt Body tes");
						if($this->intermailer->send())
						{
							$id = $dataEmployee->employee_id;
							$data = $dataEmployee->intrax_pin;
							$cipherpin = openssl_encrypt($data, "aes-128-cbc", $key, 0, $iv);
							if($dataEmployee->intrax_license=="active"){ $license=1; } else { $license=0; }
							$dataEmployee = [
								"department_name"			=> null,
								"position_name"				=> null,
								"employee_id"				=> $id,
								"employee_name"				=> $dataEmployee->employee_full_name,
								"employee_auto_absent"		=> null,
								"employee_ktp_no"			=> null,
								"employee_ktp_address"      => $dataEmployee->address,
								"employee_domicile_address" => $dataEmployee->address,
								"employee_birth_place"     	=> null,
								"employee_email"    		=> $dataEmployee->email,
								"employee_marital_status" 	=> null,
								"employee_password" 		=> $cipherpin,
								"employee_active_status"    => $license,
								"checklog_id2"   			=> $dataEmployee->employee_account_no,
								"employee_birth_date"		=> $dataEmployee->birthday
							  ];
							  
							$arrOutput = [
							 'result'				=> true,
							 'message' 				=> "Please check your email for a verification pin",
							 'permission' 			=> false,
							 'pin' 					=> $ciphertext,
							 'data' 				=> $arrData,
							 'location' 			=> $arrLocation,
							 'absence_with_location'=> true,
							 'employee'				=> $dataEmployee,
							 'license_url'			=> "https://licenses.genesysindonesia.co.id/api/",
							 'license_token'		=> "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiNjkzNTQwOWYzMWIyNDFlNDIyZjYyMjExOTVlMTlhNTNmZmQ3MzJlYjVhOGYxNzM2ZGRhZTEyNzZhMjU1YWM2NTBmNzkxZWYyMWM0NmYyN2YiLCJpYXQiOjE2NTgzODkyNjAsIm5iZiI6MTY1ODM4OTI2MCwiZXhwIjoxNjg5OTI1MjYwLCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.VgO9GK9tFSuu7hHpabE1DWm3RFkA0NfL-Q92EBRz_h1lZZAJH65Qtq7p1_HlPyt3TYE9FncVFsDjV1LUsAIMzNquJMcQYNQ-pYfj3irFTqp0LCAEOcrc1c7Pm5J2YUHPOGOQGeRB3_SZzUpejHcWICKhXMQMIRK1Ss3hOyXXnv4sH4B7uujUsd0_99q6gz4ufySzmbChBelzl0PrcXbHiRHAiQKfcsfowvrU1pe2mYgTUnkjdKyRXIy-XJ5mL7NQ4Bq3ZPZROPQpS9YT0TLfSRxiocfqutXmYgx4jwjl0jh7fqF7fM8Y4VcKSKRCBL-Pqg-bK-tPzLBK4kOrh-6Ngnp3WJoL0pUKLgbGEeVysU9Ehu__REvJCamdM5aipt6ym1B0hZxfHji4DoP0jHVG8Xxp6nn0vMqUi3gdZp-hY1JawJusqtj3KEeejfLfB-oKlZKqNXmXGNWagDeNLh9HTM2ry4PTGMKAFXVvQcafmE2OEovDQvzuB2npbO3cDPvyf3nMPgzMiDcE79051R8ojFkf82SypYzMcMunhCiBhd-2zmZ7w5NVYuG3bgvd76qBh6lG8hV1aMV5Jz_F14rxIfsKzRz4-y9sO2xtRivXIIstsV5xU6Pr3gjl__3cN-2KRFx0hnMhmYICPxOBApdfOuqj2G5ZJ3kNGUk9RESoMCs"
							];
						}else{
							$arrOutput = [
							 'result'				=> false,
							 'message' 				=> "SMTP error"
							];
						}
					} else {
						$arrOutput = [
						 'result'				=> false,
						 'message' 				=> "Login Failed, Please Register First."
						];
					}
				} else {
				   $arrOutput = [
					'result'	=> false,
					'message' 	=> "company_id not defined"
				   ];
				}
			} else{
			  $arrOutput = [
				'result'	=> false,
				'message' 	=> "flag is not valid"
			  ];
			}
		}else{
			$arrOutput = [
			  'result'		=> false,
			  'message' 	=> "apikey is not valid"
			];
		}
    }else{
      $arrOutput = [
		'result'		=> false,
		'message' 		=> "apikey is not defined"
      ];
    }
	header("Content-Type:application/json");
    echo json_encode($arrOutput);
	$txt = "RESPON-".date("Ymd-His")."->".json_encode($arrOutput)."\n";
	fwrite($myfile, $txt);
	$txt = "-------------------------------------------------------------\n";
	fwrite($myfile, $txt);
	fclose($myfile);
  }
  
  function checkActiveOTP_post(){
	if (!file_exists("application/controllers/api/intrax/logs/log-checkActiveOTP-".date("Y-m-d").".txt")) {
		$myfile = fopen("application/controllers/api/intrax/logs/log-checkActiveOTP-".date("Y-m-d").".txt", "a");
	} else {
		$myfile = fopen("application/controllers/api/intrax/logs/log-checkActiveOTP-".date("Y-m-d").".txt", "a");
	}
	
	$data = json_decode(file_get_contents('php://input'), true);
	$txt = "-------------------------------------------------------------\n";
	fwrite($myfile, $txt);
	$txt = "REQUEST-".date("Ymd-His")."->".json_encode($data)."\n";
	fwrite($myfile, $txt);
    $headers = getRequestHeaders();
    $apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;
	$otp  = !empty($data['otp']) ? $data['otp'] : "";
	$employee_id  = !empty($data['employee_id']) ? $data['employee_id'] : "";
	$arrData = [];
	$arrLocation = [];
	load_model([
	"employee_model",
	"otp_model"
	]);
    if($apikey!=""){
		if($apikey==$this->apikey){
			if(!empty($otp) AND !empty($employee_id)){
				$dataOTP   = $this->otp_model->getActiveIntraxOTP($employee_id,"login_intrax","email");
				if($dataOTP){
					if($otp==$dataOTP->otp){
						if($dataOTP->status==""){
							$this->otp_model->setSuccess($dataOTP->otp_id);
							$arrOutput = [
								'result'	=> true,
								'message' 	=> "Successful OTP email verification"
							];
						}else{
							$arrOutput = [
								'result'	=> false,
								'message' 	=> "OTP has been used"
							];
						}
					}else{
						$arrOutput = [
						  'result'		=> false,
						  'message' 	=> "OTP does not match"
						];
					}
				}else{
					$arrOutput = [
					  'result'		=> false,
					  'message' 	=> "OTP not found or has been expired"
					];
				}
			}else{
				$arrOutput = [
				  'result'		=> false,
				  'message' 	=> "all mandatory be filled"
				];
			}
		}else{
			$arrOutput = [
			  'result'		=> false,
			  'message' 	=> "apikey is not valid"
			];
		}
    }else{
      $arrOutput = [
		'result'		=> false,
		'message' 		=> "apikey is not defined"
      ];
    }
	header("Content-Type:application/json");
    echo json_encode($arrOutput);
	$txt = "RESPON-".date("Ymd-His")."->".json_encode($arrOutput)."\n";
	fwrite($myfile, $txt);
	$txt = "-------------------------------------------------------------\n";
	fwrite($myfile, $txt);
	fclose($myfile);
  }
  
  function resetPasswordInact_post(){
	if (!file_exists("application/controllers/api/intrax/logs/log-resetPasswordInact-".date("Y-m-d").".txt")) {
		$myfile = fopen("application/controllers/api/intrax/logs/log-resetPasswordInact-".date("Y-m-d").".txt", "a");
	} else {
		$myfile = fopen("application/controllers/api/intrax/logs/log-resetPasswordInact-".date("Y-m-d").".txt", "a");
	}
	
	$data = json_decode(file_get_contents('php://input'), true);
	$txt = "-------------------------------------------------------------\n";
	fwrite($myfile, $txt);
	$txt = "REQUEST-".date("Ymd-His")."->".json_encode($data)."\n";
	fwrite($myfile, $txt);
    $headers = getRequestHeaders();
    $apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;
	$employee_id  = !empty($data['employee_id']) ? $data['employee_id'] : "";
	load_model([
	"employee_model",
	"cabang_model",
	"subscription_model",
	"employeeareacabang_model"
	]);
    if($apikey!=""){
		if($apikey==$this->apikey){
			if($employee_id!=""){
				$resultPasswordPin = $this->employee_model->resetPasswordPin($employee_id);
				if($resultPasswordPin!=false){
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
							<div class="main-page">
							<img src="https://inact.interactiveholic.net/bo/asset/images/Logo_inact.png" style="width: 200px;">
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
										Hello '.$resultPasswordPin->employee_nick_name.',</p>
										<p style="font-family: Roboto;
										font-size: 15px;
										font-weight: normal;
										font-style: normal;
										font-stretch: normal;
										line-height: 1.67;
										letter-spacing: normal;
										text-align: left;
										color: rgba(0, 0, 0, 0.7);
										">
										You are doing reset pin on InAct HRIS app. 
										Here is the Old pin that must be entered</p>
										<p style="font-family: Roboto;
										font-size: 20px;
										font-weight: normal;
										font-style: normal;
										font-stretch: normal;
										line-height: 1.67;
										letter-spacing: normal;
										text-align: left;
										color: rgba(0, 0, 0, 0.7);
										"><strong>Your PIN: </strong>
											'.$resultPasswordPin->intrax_pin.'
										</p>
										
										<p style="font-family: Roboto;
										font-size: 15px;
										font-weight: normal;
										font-style: normal;
										font-stretch: normal;
										line-height: 1.67;
										letter-spacing: normal;
										text-align: left;
										color: rgba(0, 0, 0, 0.7);">
											If you experience problems using InterActive Products, please contact our Customer Support team through  <font style="font-weight: bold;color: #00cbce;">(031) 535 5353</font> <b>Ext. 16 or <a href="https://wa.me/6285879123123" target="_blank">+62 858-79-123-123</a></b>
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
					// $this->intermailer->initialize();
					$this->intermailer->initialize_allin();
					$this->intermailer->to([$resultPasswordPin->email=>$resultPasswordPin->email]);
					$this->intermailer->set_content("InAct HRIS Employee PIN",$body_msg,"Alt Body tes");
					if($this->intermailer->send())
					{ 
						$arrOutput = [
						 'result'				=> true,
						 'message' 				=> "Success sent old pin. Please check your email"
					   ];
					}
				} else {
					$arrOutput = [
					 'result'				=> false,
					 'message' 				=> "Error when resetting pin"
				   ];
				}					
			} else {
			   $arrOutput = [
				'result'		=> false,
				'message' 		=> "employee_id not defined"
			   ];
			}
		}else{
			$arrOutput = [
			  'result'		=> false,
			  'message'		=> "apikey is not valid"
			];
		}
    }else{
      $arrOutput = [
		'result'		=> false,
		'message'		=> "apikey is not defined"
      ];
    }
	header("Content-Type:application/json");
    echo json_encode($arrOutput);
	$txt = "RESPON-".date("Ymd-His")."->".json_encode($arrOutput)."\n";
	fwrite($myfile, $txt);
	$txt = "-------------------------------------------------------------\n";
	fwrite($myfile, $txt);
	fclose($myfile);
  }
  
  function resetPassword_post(){
	if (!file_exists("application/controllers/api/intrax/logs/log-resetPassword-".date("Y-m-d").".txt")) {
		$myfile = fopen("application/controllers/api/intrax/logs/log-resetPassword-".date("Y-m-d").".txt", "a");
	} else {
		$myfile = fopen("application/controllers/api/intrax/logs/log-resetPassword-".date("Y-m-d").".txt", "a");
	}
	
	$data = json_decode(file_get_contents('php://input'), true);
	$txt = "-------------------------------------------------------------\n";
	fwrite($myfile, $txt);
	$txt = "REQUEST-".date("Ymd-His")."->".json_encode($data)."\n";
	fwrite($myfile, $txt);
    $headers = getRequestHeaders();
    $apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;
	$employee_id  = !empty($data['employee_id']) ? $data['employee_id'] : "";
	load_model([
	"employee_model",
	"cabang_model",
	"subscription_model",
	"employeeareacabang_model"
	]);
    if($apikey!=""){
		if($apikey==$this->apikey){
			if($employee_id!=""){
				$resultPasswordPin = $this->employee_model->resetPasswordPin($employee_id);
				if($resultPasswordPin!=false){
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
										Hello '.$resultPasswordPin->employee_nick_name.',</p>
										<p style="font-family: Roboto;
										font-size: 15px;
										font-weight: normal;
										font-style: normal;
										font-stretch: normal;
										line-height: 1.67;
										letter-spacing: normal;
										text-align: left;
										color: rgba(0, 0, 0, 0.7);
										">
										You are doing reset password on intrax app. 
										Here is the Old Password that must be entered</p>
										<p style="font-family: Roboto;
										font-size: 20px;
										font-weight: normal;
										font-style: normal;
										font-stretch: normal;
										line-height: 1.67;
										letter-spacing: normal;
										text-align: left;
										color: rgba(0, 0, 0, 0.7);
										"><strong>Your Password: </strong>
											'.$resultPasswordPin->intrax_pin.'
										</p>
										
										<p style="font-family: Roboto;
										font-size: 15px;
										font-weight: normal;
										font-style: normal;
										font-stretch: normal;
										line-height: 1.67;
										letter-spacing: normal;
										text-align: left;
										color: rgba(0, 0, 0, 0.7);">
											If you experience problems using InterActive Products, please contact our Customer Support team through  <font style="font-weight: bold;color: #00cbce;">(031) 535 5353</font> <b>Ext. 16 or <a href="https://wa.me/6285879123123" target="_blank">+62 858-79-123-123</a></b>
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
					// $this->intermailer->initialize();
					$this->intermailer->initialize_allin();
					$this->intermailer->to([$resultPasswordPin->email=>$resultPasswordPin->email]);
					$this->intermailer->set_content("InTrax Employee Password",$body_msg,"Alt Body tes");
					if($this->intermailer->send())
					{ 
						$arrOutput = [
						 'result'				=> true,
						 'message' 				=> "Success sent old password. Please check your email"
					   ];
					}
				} else {
					$arrOutput = [
					 'result'				=> false,
					 'message' 				=> "Error when resetting password"
				   ];
				}					
			} else {
			   $arrOutput = [
				'result'		=> false,
				'message' 		=> "employee_id not defined"
			   ];
			}
		}else{
			$arrOutput = [
			  'result'		=> false,
			  'message'		=> "apikey is not valid"
			];
		}
    }else{
      $arrOutput = [
		'result'		=> false,
		'message'		=> "apikey is not defined"
      ];
    }
	header("Content-Type:application/json");
    echo json_encode($arrOutput);
	$txt = "RESPON-".date("Ymd-His")."->".json_encode($arrOutput)."\n";
	fwrite($myfile, $txt);
	$txt = "-------------------------------------------------------------\n";
	fwrite($myfile, $txt);
	fclose($myfile);
  }
  
  function changePassword_post(){
	if (!file_exists("application/controllers/api/intrax/logs/log-changePassword-".date("Y-m-d").".txt")) {
		$myfile = fopen("application/controllers/api/intrax/logs/log-changePassword-".date("Y-m-d").".txt", "a");
	} else {
		$myfile = fopen("application/controllers/api/intrax/logs/log-changePassword-".date("Y-m-d").".txt", "a");
	}
	
	$data = json_decode(file_get_contents('php://input'), true);
	$txt = "-------------------------------------------------------------\n";
	fwrite($myfile, $txt);
	$txt = "REQUEST-".date("Ymd-His")."->".json_encode($data)."\n";
	fwrite($myfile, $txt);
    $headers = getRequestHeaders();
    $apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;
	$email  = !empty($data['email']) ? $data['email'] : "";
	$old_password  = !empty($data['old_password']) ? $data['old_password'] : "";
	$new_password  = !empty($data['new_password']) ? $data['new_password'] : "";
	$confirm_password  = !empty($data['confirm_password']) ? $data['confirm_password'] : "";
	$checklog_id2  = !empty($data['checklog_id2']) ? $data['checklog_id2'] : "";
	load_model([
	"employee_model",
	"cabang_model",
	"subscription_model",
	"employeeareacabang_model"
	]);
    if($apikey!=""){
		if($apikey==$this->apikey){
			if($email!=""){
				$resultEmployee = $this->employee_model->getByEmailOnly($email);
				if($resultEmployee!=false){
					$key = "423a1aa70eca39af";
					$iv = "506e6fb150550765";
					$data = $resultEmployee->intrax_pin;
					$passwordIntrax = openssl_encrypt($data, "aes-128-cbc", $key, 0, $iv);
					if($passwordIntrax==$old_password){
						if($new_password==$confirm_password){
							$newpin = openssl_decrypt($new_password, "aes-128-cbc", $key, 0, $iv);
							$resultChange = $this->employee_model->changePasswordPin($email,$newpin);
							if($resultChange!=false){
								$menu_1 = [
									"component_name"			=> "Location",
									"component_id"				=> 1,
									"value"						=> true
								];
								  
								$menu_2 = [
									"component_name"			=> "Document",
									"component_id"				=> 2,
									"value"						=> true
								];
								  
								$menu_3 = [
									"component_name"			=> "Essay",
									"component_id"				=> 3,
									"value"						=> true
								];
								$arrOutput = [
								 'result'				=> true,
								 'message' 				=> "Sukses Mengganti Password",
								 'pic' 					=> true,
								 'kpi' 					=> true,
								 'slip' 				=> true,
								 'menu_1' 				=> $menu_1,
								 'menu_2' 				=> $menu_2,
								 'menu_3' 				=> $menu_3
							   ];
							} else {
								$arrOutput = [
								 'result'				=> false,
								 'message' 				=> "failed change password"
							   ];
							}
						} else {
							$arrOutput = [
							 'result'				=> false,
							 'message' 				=> "new password not same"
						   ];
						}	
					} else {
						$arrOutput = [
						 'result'				=> false,
						 'message' 				=> "wrong old password"
					   ];
					}	
				} else {
					$arrOutput = [
					 'result'				=> false,
					 'message' 				=> "email not found"
				   ];
				}					
			} else {
			   $arrOutput = [
				'result'		=> false,
				'message' 		=> "email not defined"
			   ];
			}
		}else{
			$arrOutput = [
			  'result'		=> false,
			  'message'		=> "apikey is not valid"
			];
		}
    }else{
      $arrOutput = [
		'result'		=> false,
		'message'		=> "apikey is not defined"
      ];
    }
	header("Content-Type:application/json");
    echo json_encode($arrOutput);
	$txt = "RESPON-".date("Ymd-His")."->".json_encode($arrOutput)."\n";
	fwrite($myfile, $txt);
	$txt = "-------------------------------------------------------------\n";
	fwrite($myfile, $txt);
	fclose($myfile);
  }
  
  function checkLogin_post(){
	if (!file_exists("application/controllers/api/intrax/logs/log-checkLogin-".date("Y-m-d").".txt")) {
		$myfile = fopen("application/controllers/api/intrax/logs/log-checkLogin-".date("Y-m-d").".txt", "a");
	} else {
		$myfile = fopen("application/controllers/api/intrax/logs/log-checkLogin-".date("Y-m-d").".txt", "a");
	}
	
	$data = json_decode(file_get_contents('php://input'), true);
	$txt = "-------------------------------------------------------------\n";
	fwrite($myfile, $txt);
	$txt = "REQUEST-".date("Ymd-His")."->".json_encode($data)."\n";
	fwrite($myfile, $txt);
    $headers = getRequestHeaders();
    $apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;
	$checklog_id2  = !empty($data['checklog_id2']) ? $data['checklog_id2'] : "";
	$email  = !empty($data['email']) ? $data['email'] : "";
	$company_id  = !empty($data['company_id']) ? $data['company_id'] : "";
	$employee_device  = !empty($data['employee_device']) ? $data['employee_device'] : "";
	$password  = !empty($data['password']) ? $data['password'] : "";
	$version  = !empty($data['version']) ? $data['version'] : "";
	$source_id  = !empty($data['source_id']) ? $data['source_id'] : "";
	$arrLocation = [];
	$arrEmpLocation = [];
	load_model([
	"employee_model",
	"cabang_model",
	"subscription_model",
	"employeeareacabang_model"
	]);
	if ($company_id==""){
		$sqlGetAppid = $this->employee_model->getAppIdByEmail($email);
	} else {
		$sqlGetAppid = $this->subscription_model->getAppIdByCompanyID($company_id);
	}
	$appid = $sqlGetAppid->appid;
	$sqlGetDataSubscription = $this->subscription_model->getByAppId($appid);
    if($apikey!=""){
		if($apikey==$this->apikey){
			if($email!=""){
				$dataEmployee = $this->employee_model->getByEmail($email,$appid);
				if($dataEmployee){
					$key = "423a1aa70eca39af";
					$iv = "506e6fb150550765";
					$data = $dataEmployee->intrax_pin;
					$status_license = $dataEmployee->intrax_license;
					$location = $dataEmployee->presence_location;
					$expired = $dataEmployee->subscription_expired;
					$ciphertext = openssl_encrypt($data, "aes-128-cbc", $key, 0, $iv);
					if($password==$ciphertext){
						if($status_license=='active'){
						  $id = $dataEmployee->employee_id;
						  $dataEmpLocation = $this->employeeareacabang_model->getEmployeeLocation($id);
						  if($dataEmpLocation){
							foreach ($dataEmpLocation as $row) {
							  $arrEmpLocation[] = [
								"machine_id"			=> $row->cabang_code,
								"toleransi_radius"		=> $row->employeeareacabang_radius,
								"lat"					=> $row->latitude,
								"long"					=> $row->longitude,
								"alamat_lengkap"		=> $row->cabang_address
							  ];
							}
						  }
						  
						  $dataLocation = $this->cabang_model->getByAppid($appid);
						  if($dataLocation){
							foreach ($dataLocation as $rows) {
							  $arrLocation[] = [
								"machine_id"			=> $rows->cabang_code,
								"toleransi_radius"		=> "100",
								"lat"					=> $rows->latitude,
								"long"					=> $rows->longitude,
								"alamat_lengkap"		=> $rows->cabang_address
							  ];
							}
						  }
						  
						  if($dataEmployee->intrax_license=="active"){ $license=1; } else { $license=0; }
						  if($dataEmployee->presence_mode=="online"){ $mode="false"; } else { $mode="true"; }
						  
						  $ciphertextChecklog = openssl_encrypt($dataEmployee->employee_account_no, "aes-128-cbc", $key, 0, $iv);
							
						  $dataEmployee = [
							"department_name"			=> null,
							"position_name"				=> null,
							"employee_id"				=> $id,
							"employee_name"				=> $dataEmployee->employee_full_name,
							"employee_ktp_no"			=> null,
							"employee_ktp_address"      => $dataEmployee->address,
							"employee_domicile_address" => $dataEmployee->address,
							"employee_birth_place"     	=> null,
							"employee_email"    		=> $dataEmployee->email,
							"employee_marital_status" 	=> null,
							"employee_password" 		=> $ciphertext,
							"employee_active_status"    => $license,
							"checklog_id2"   			=> $dataEmployee->employee_account_no,
							"employee_birth_date"		=> $dataEmployee->birthday,
							"employee_photo"			=> null
						  ];
						  
						  $menu_1 = [
							"component_name"			=> "Location",
							"component_id"				=> 1,
							"value"						=> true
						  ];
						  
						  $menu_2 = [
							"component_name"			=> "Document",
							"component_id"				=> 2,
							"value"						=> true
						  ];
						  
						  $menu_3 = [
							"component_name"			=> "Essay",
							"component_id"				=> 3,
							"value"						=> true
						  ];
						  
						  $arrOutput = [
							 'result'					=> true,
							 'location' 				=> $arrLocation,
							 'employeeLocation' 		=> $arrEmpLocation,
							 'message' 					=> "Login Success",
							 'data' 					=> $dataEmployee,
							 'pic' 						=> true,
							 'kpi' 						=> true,
							 'slip' 					=> true,
							 'menu_1' 					=> $menu_1,
							 'menu_2' 					=> $menu_2,
							 'menu_3' 					=> $menu_3,
							 'permission' 				=> true,
							 'absence_with_location' 	=> $location,
							 'license_url' 				=> "https://licenses.genesysindonesia.co.id/api/",
							 'license_token' 			=> "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiNTg5NWQ3NzJlZjUwYjJiNDIxNzhlNTk3ZGIyZDEyYTFmMzJiMDUwYTg5YmE3NzE2MDQxYjEwZDliNjUxMjM3N2Y4ZjIwYWRlNmNhNjg1Y2UiLCJpYXQiOjE2ODk5MjUzMzYsIm5iZiI6MTY4OTkyNTMzNiwiZXhwIjoxNzIxNTQ3NzM2LCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.fEvQlX2xWE1tT-u89V4jSRUon4bMlr3nPD_zR0pBLGBNEQcnFWrXB63FVRGMCkduDKZ6C1LMJABhzj9OnhAevuaege9QUp2XjafUUDXA_UW4-E10qrIY3WEaCLfMFGaJDknl0_Cao2kQRxs440ftdsWJ_PMzPXOjwS0ywYxqIOrt0DoLzcB3FObxTMyR_QxsN9gTZdD--hk2wQU1iSE7I16432mAgXbWTCwEW1UeHlPMBaiSEivbzqhI6DByaFPLYa4YZMtqqCzJ4e2rOYMGnmQL4FP_WbONGyUKw5iyliUzWzD4Uljev-ZzBfde6LSpcUZWrF6M6liqZz93py-czB3Wpa5-CvfGqDQrF-8Dl5BbrvQjFs0wKd5dMncNjqjfvzNYuTqwq_dDQ5hghFybK9m7OXj4WlCtc0YZWXQwQJhka9EFeiGQMauFg7V4m44Kmdk36Nfqz_L5YWy6dVcgavNr9QqHv00u28aA0q3_Au4ju7poWI3KRheBGF1Iyd4KZ1T-g1MvTxGOYHZn5P8H2qCgSsPDoWvMcg2iMecPVLKAXLudhSvBp2HF50LJjxEBA4a3nago1PCmDcvOhC5TAIjOha2j-GT_Y4Or-CKaIrMF4s-h1vY2Jxy664FlAwBxt_al0MPvqT__gQyJMfLKSgHhnlcIPOwLIdA_KITXrnQ",
							 'license_expired' 			=> $expired,
							 'offline_attendance' 		=> $mode,
							 'checklog_id_include' 		=> "false",
							 'source_id' 				=> $company_id,
							 'warehouse_id' 			=> $company_id,
							 'company_id' 				=> $appid,
							 'external_id' 				=> $sqlGetDataSubscription->intrax_company_id
						   ];
						} else {
						  $arrOutput = [
						   'result'		=> false,
						   'message' 	=> "Account disabled"
						  ];
						}
					} else {
						$arrOutput = [
						 'result'				=> false,
						 'message' 				=> "wrong password"
						];
					}
				} else {
				   $arrOutput = [
					 'result'				=> false,
					 'message' 				=> "user not found"
				   ];
				}
			} else {
			   $arrOutput = [
			     'result'				=> false,
				 'message' 				=> "email not defined"
			   ];
			}
		}else{
			$arrOutput = [
			  'result'				=> false,
			  'message' 			=> "apikey is not valid"
			];
		}
    }else{
      $arrOutput = [
		'result'			=> false,
		'message' 			=> "apikey is not defined"
      ];
    }
	header("Content-Type:application/json");
    echo json_encode($arrOutput);
	$txt = "RESPON-".date("Ymd-His")."->".json_encode($arrOutput)."\n";
	fwrite($myfile, $txt);
	$txt = "-------------------------------------------------------------\n";
	fwrite($myfile, $txt);
	fclose($myfile);
  }
  
  function getPresenceMethod_post(){
	if (!file_exists("application/controllers/api/intrax/logs/log-getPresenceMethod-".date("Y-m-d").".txt")) {
		$myfile = fopen("application/controllers/api/intrax/logs/log-getPresenceMethod-".date("Y-m-d").".txt", "a");;
	} else {
		$myfile = fopen("application/controllers/api/intrax/logs/log-getPresenceMethod-".date("Y-m-d").".txt", "a");;
	}
	
	$data = json_decode(file_get_contents('php://input'), true);
	$txt = "-------------------------------------------------------------\n";
	fwrite($myfile, $txt);
	$txt = "REQUEST-".date("Ymd-His")."->".json_encode($data)."\n";
	fwrite($myfile, $txt);
    $headers = getRequestHeaders();
    $apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;
	$employee_id  = !empty($data['employee_id']) ? $data['employee_id'] : "";
	load_model([
	"employee_model",
	"cabang_model",
	"subscription_model",
	"employeeareacabang_model"
	]);
    if($apikey!=""){
		if($apikey==$this->apikey){
			if($employee_id!=""){
				$dataEmployee = $this->employee_model->getEmployeeById($employee_id);
				if($dataEmployee){
					$pin = '0';
					$finger = '0';
					$face = '0';
					$pic = '0';
					$methodPres = explode("|", $dataEmployee->presence_method);
					if (!empty($dataEmployee->presence_method) && in_array(1, $methodPres)){$pin = '1';}
					if (!empty($dataEmployee->presence_method) && in_array(2, $methodPres)){$finger = '1';}
					if (!empty($dataEmployee->presence_method) && in_array(3, $methodPres)){$face = '1';}
					if (!empty($dataEmployee->presence_method) && in_array(4, $methodPres)){$pic = '1';}
					$arrPresenceMethod = [
					 'presence_method_pin'			=> $pin,
					 'presence_method_finger'		=> $finger,
					 'presence_method_face'			=> $face,
					 'presence_method_picture'		=> $pic
					];
					
					$arrOutput = [
					 'result'				=> true,
					 'message' 				=> "Employee found, presence method successfully fetched",
					 'presence_methods'		=> $arrPresenceMethod
				   ];
				} else {
					$arrOutput = [
					 'result'				=> false,
					 'message' 				=> "user not found"
				   ];
				}					
			} else {
			   $arrOutput = [
				'result'		=> false,
				'message' 		=> "employee_id not defined"
			   ];
			}
		}else{
			$arrOutput = [
			  'result'		=> false,
			  'message'		=> "apikey is not valid"
			];
		}
    }else{
      $arrOutput = [
		'result'		=> false,
		'message'		=> "apikey is not defined"
      ];
    }
	header("Content-Type:application/json");
    echo json_encode($arrOutput);
	$txt = "RESPON-".date("Ymd-His")."->".json_encode($arrOutput)."\n";
	fwrite($myfile, $txt);
	$txt = "-------------------------------------------------------------\n";
	fwrite($myfile, $txt);
	fclose($myfile);
  }
  
  function getProfile_post(){
	if (!file_exists("application/controllers/api/intrax/logs/log-getProfile-".date("Y-m-d").".txt")) {
		$myfile = fopen("application/controllers/api/intrax/logs/log-getProfile-".date("Y-m-d").".txt", "a");;
	} else {
		$myfile = fopen("application/controllers/api/intrax/logs/log-getProfile-".date("Y-m-d").".txt", "a");;
	}
	
	$data = json_decode(file_get_contents('php://input'), true);
	$txt = "-------------------------------------------------------------\n";
	fwrite($myfile, $txt);
	$txt = "REQUEST-".date("Ymd-His")."->".json_encode($data)."\n";
	fwrite($myfile, $txt);
    $headers = getRequestHeaders();
    $apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;
	$employee_id  = !empty($data['employee_id']) ? $data['employee_id'] : "";
	load_model([
	"employee_model",
	"cabang_model",
	"subscription_model",
	"employeeareacabang_model"
	]);
    if($apikey!=""){
		if($apikey==$this->apikey){
			if($employee_id!=""){
				$dataEmployee = $this->employee_model->getEmployeeById($employee_id);
				if($dataEmployee){
					if (!empty($dataEmployee->employee_photo)){$photo = "https://inact.interactiveholic.net/bo/sys_upload/user_profile/".$dataEmployee->employee_photo;} else { if ($dataEmployee->gender=='male'){$photo = "https://inact.interactiveholic.net/bo/img_employee/img_avatar_boy.png";} else {$photo = "https://inact.interactiveholic.net/bo/img_employee/img_avatar_girl.png";}}
					$pin = '0';
					$finger = '0';
					$face = '0';
					$pic = '0';
					$methodPres = explode("|", $dataEmployee->presence_method);
					if (!empty($dataEmployee->presence_method) && in_array(1, $methodPres)){$pin = '1';}
					if (!empty($dataEmployee->presence_method) && in_array(2, $methodPres)){$finger = '1';}
					if (!empty($dataEmployee->presence_method) && in_array(3, $methodPres)){$face = '1';}
					if (!empty($dataEmployee->presence_method) && in_array(4, $methodPres)){$pic = '1';}
					$key = "423a1aa70eca39af";
					$iv = "506e6fb150550765";
					$data = $dataEmployee->intrax_pin;
					$cipherpin = openssl_encrypt($data, "aes-128-cbc", $key, 0, $iv);
					$profile = [
					 'employee_id'									=> $dataEmployee->employee_id,
					 'employee_device'								=> null,
					 'checklog_id'									=> $dataEmployee->employee_account_no,
					 'checklog_id2'									=> null,
					 'source_id'									=> null,
					 'warehouse_id'									=> null,
					 'employee_type_id'								=> null,
					 'department_id'								=> null,
					 'position_id'									=> null,
					 'employee_name'								=> $dataEmployee->employee_full_name,
					 'employee_photo'								=> $photo,
					 'employee_initial'								=> $dataEmployee->employee_nick_name,
					 'employee_gender'								=> ucfirst($dataEmployee->gender),
					 'employee_birth_place'							=> null,
					 'employee_birth_date'							=> date_format(date_create($dataEmployee->birthday),"Y-m-d"),
					 'employee_martial_status'						=> null,
					 'employee_religion'							=> null,
					 'employee_blood_type'							=> null,
					 'employee_phone_no'							=> null,
					 'employee_mobile_phone_no'						=> $dataEmployee->phone_number,
					 'employee_email'								=> $dataEmployee->email,
					 'employee_password'							=> $cipherpin,
					 'employee_domicile_address'					=> $dataEmployee->address,
					 'employee_domicile_village'					=> null,
					 'employee_domicile_district'					=> null,
					 'employee_domicile_city'						=> null,
					 'employee_domicile_postal_code'				=> null,
					 'employee_kk_no'								=> null,
					 'employee_kk_date'								=> null,
					 'employee_ktp_no'								=> null,
					 'employee_ktp_exp_date'						=> null,
					 'employee_ktp_address'							=> $dataEmployee->address,
					 'employee_ktp_village'							=> null,
					 'employee_ktp_district'						=> null,
					 'employee_ktp_city'							=> null,
					 'employee_ktp_postal_code'						=> null,
					 'employee_bank'								=> null,
					 'employee_bank_account_no'						=> null,
					 'employee_bank_account_name'					=> null,
					 'employee_bank_branch'							=> null,
					 'employee_npwp_tax_category'					=> null,
					 'employee_npwp_no'								=> null,
					 'employee_npwp_name'							=> null,
					 'employee_npwp_date'							=> null,
					 'employee_npwp_address'						=> null,
					 'employee_npwp_village'						=> null,
					 'employee_npwp_district'						=> null,
					 'employee_npwp_city'							=> null,
					 'employee_emergency_family_name'				=> null,
					 'employee_emergency_relation'					=> null,
					 'employee_emergency_phone_no'					=> null,
					 'employee_emergency_mobile_phone_no'			=> null,
					 'employee_emergency_address'					=> null,
					 'employee_emergency_village'					=> null,
					 'employee_emergency_district'					=> null,
					 'employee_emergency_city'						=> null,
					 'employee_emergency_postal_code'				=> null,
					 'employee_active_status'						=> $dataEmployee->employee_license,
					 'employee_join_date'							=> $dataEmployee->employee_join_date,
					 'contract_date'								=> null,
					 'end_contract_date'							=> null,
					 'probation_date'								=> null,
					 'end_probation_date'							=> null,
					 'employee_resign_date'							=> null,
					 'employee_resign_reason'						=> null,
					 'user_record'									=> $dataEmployee->employee_user_add,
					 'user_modified'								=> $dataEmployee->employee_user_modif,
					 'dt_record'									=> $dataEmployee->employee_date_create,
					 'dt_modified'									=> $dataEmployee->employee_date_modif,
					 'employee_working_hour'						=> null,
					 'employee_working_day'							=> null,
					 'flag_location'								=> null,
					 'overtime_type'								=> null,
					 'warning_letter'								=> null,
					 'employee_inventory'							=> null,
					 'flag_notification_leave_request'				=> null,
					 'flag_notification_leave_approval'				=> null,
					 'flag_notification_shift_request'				=> null,
					 'flag_notification_shift_approval'				=> null,
					 'flag_notification_overtime_request'			=> null,
					 'flag_notification_overtime_approval'			=> null,
					 'flag_notification_attendance'					=> null,
					 'flag_notification_reimbursement_request'		=> null,
					 'flag_notification_reimbursement_approval'		=> null,
					 'employee_presencemethod_pin'					=> $pin,
					 'employee_presencemethod_finger'				=> $finger,
					 'employee_presencemethod_face'					=> $face,
					 'employee_presencemethod_picture'				=> $pic,
					 'employee_auto_absent'							=> null
					];
					
					$arrOutput = [
					 'result'				=> true,
					 'message' 				=> "Succesfully get data.",
					 'model'				=> $profile
				   ];
				} else {
					$arrOutput = [
					 'result'				=> false,
					 'message' 				=> "user not found"
				   ];
				}					
			} else {
			   $arrOutput = [
				'result'		=> false,
				'message' 		=> "employee_id not defined"
			   ];
			}
		}else{
			$arrOutput = [
			  'result'		=> false,
			  'message'		=> "apikey is not valid"
			];
		}
    }else{
      $arrOutput = [
		'result'		=> false,
		'message'		=> "apikey is not defined"
      ];
    }
	header("Content-Type:application/json");
    echo json_encode($arrOutput);
	$txt = "RESPON-".date("Ymd-His")."->".json_encode($arrOutput)."\n";
	fwrite($myfile, $txt);
	$txt = "-------------------------------------------------------------\n";
	fwrite($myfile, $txt);
	fclose($myfile);
  }
  
  function saveProfile_post(){
	if (!file_exists("application/controllers/api/intrax/logs/log-saveProfile-".date("Y-m-d").".txt")) {
		$myfile = fopen("application/controllers/api/intrax/logs/log-saveProfile-".date("Y-m-d").".txt", "a");
	} else {
		$myfile = fopen("application/controllers/api/intrax/logs/log-saveProfile-".date("Y-m-d").".txt", "a");
	}
	
	$data = json_decode(file_get_contents('php://input'), true);
	$txt = "-------------------------------------------------------------\n";
	fwrite($myfile, $txt);
	$txt = "REQUEST-".date("Ymd-His")."->".json_encode($data)."\n";
	fwrite($myfile, $txt);
    $headers = getRequestHeaders();
    $apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;
	$employee_id  = !empty($data['employee_id']) ? $data['employee_id'] : "";
	$employee_photo  = !empty($data['employee_photo']) ? $data['employee_photo'] : "";
	$name  = !empty($data['name']) ? $data['name'] : "";
	$address  = !empty($data['address']) ? $data['address'] : "";
	$phone  = !empty($data['phone']) ? $data['phone'] : "";
	$birthdate  = !empty($data['birthdate']) ? date_format(date_create($data['birthdate']),"Y-m-d") : "";
	$employee_gender  = !empty($data['employee_gender']) ? strtolower($data['employee_gender']) : "";
	$checklog_id  = !empty($data['checklog_id']) ? $data['checklog_id'] : "";
	$company_id  = !empty($data['company_id']) ? $data['company_id'] : "";
	load_model([
	"employee_model",
	"cabang_model",
	"subscription_model",
	"employeeareacabang_model"
	]);
    if($apikey!=""){
		if($apikey==$this->apikey){
			if($employee_id!=""){
				$dataEmployee = $this->employee_model->getEmployeeById($employee_id);
				if($dataEmployee){
					$key = "423a1aa70eca39af";
					$iv = "506e6fb150550765";
					$data = $dataEmployee->intrax_pin;
					$cipherpin = openssl_encrypt($data, "aes-128-cbc", $key, 0, $iv);
					$pin = '0';
					$finger = '0';
					$face = '0';
					$pic = '0';
					$methodPres = explode("|", $dataEmployee->presence_method);
					if (!empty($dataEmployee->presence_method) && in_array(1, $methodPres)){$pin = '1';}
					if (!empty($dataEmployee->presence_method) && in_array(2, $methodPres)){$finger = '1';}
					if (!empty($dataEmployee->presence_method) && in_array(3, $methodPres)){$face = '1';}
					if (!empty($dataEmployee->presence_method) && in_array(4, $methodPres)){$pic = '1';}
					$menu_1 = [
						"component_name"			=> "Location",
						"component_id"				=> 1,
						"value"						=> true
					];
								  
					$menu_2 = [
						"component_name"			=> "Document",
						"component_id"				=> 2,
						"value"						=> true
					];
								  
					$menu_3 = [
						"component_name"			=> "Essay",
						"component_id"				=> 3,
						"value"						=> true
					];
					
					if(!empty($employee_photo)){
						$img = $employee_photo;
						$img = str_replace('data:image/png;base64,', '', $img);
						$img = str_replace(' ', '+', $img);
						$data = base64_decode($img);
						$file = "./sys_upload/user_profile/".uniqid().'.png';
						$success = file_put_contents($file, $data);
						$photo = substr($file,26);
					}else{
						$photo = '';	
					}
					
					$updateEmployee = $this->employee_model->updateProfile($employee_id,$name,$address,$phone,$birthdate,$employee_gender,$photo);
					if($updateEmployee){						
						$arrOutput = [
							'result'				=> true,
							'message' 				=> "Succesfully update data.",
							'data' 					=> $photo,
							'pic' 					=> true,
							'kpi' 					=> true,
							'slip' 					=> true,
							'menu_1' 				=> $menu_1,
							'menu_2' 				=> $menu_2,
							'menu_3' 				=> $menu_3
						];
					} else {
						$arrOutput = [
						 'result'				=> false,
						 'message' 				=> "failed update data user"
					    ];
					}			
				} else {
					$arrOutput = [
					 'result'				=> false,
					 'message' 				=> "user not found"
				   ];
				}					
			} else {
			   $arrOutput = [
				'result'		=> false,
				'message' 		=> "employee id not defined"
			   ];
			}
		}else{
			$arrOutput = [
			  'result'		=> false,
			  'message'		=> "apikey is not valid"
			];
		}
    }else{
      $arrOutput = [
		'result'		=> false,
		'message'		=> "apikey is not defined"
      ];
    }
	header("Content-Type:application/json");
    echo json_encode($arrOutput);
	$txt = "RESPON-".date("Ymd-His")."->".json_encode($arrOutput)."\n";
	fwrite($myfile, $txt);
	$txt = "-------------------------------------------------------------\n";
	fwrite($myfile, $txt);
	fclose($myfile);
  }
}
