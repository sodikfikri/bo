<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Email extends REST_Controller{
    var $now;
    //var $apikey = "IAdev-apikey3fapikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9ed48151b389apikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fapikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9e7b7c2f8d891dfc9b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9";
    var $apikey = "IAdev-apikey3fapikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9ed48151b389apikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fapikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9e7b7c2f8d891dfc9b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9";

    function __construct()
    {
        parent::__construct();
        $this->now = date("Y-m-d H:i:s");
    }

    function sendOtp_post(){
        $data = file_get_contents("php://input");
        $mailData = json_decode($data);
        $headers = getRequestHeaders();
        
        $apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;
        //$apikey  = $mailData->Apikey;

        if($apikey!=""){
            if($apikey==$this->apikey){
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
                                Hello '.$mailData->employee_name.',</p>
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
                                You are doing '.$mailData->type.' on intrax app. 
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
                                    '.$mailData->otp.'
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
                // $this->intermailer->initialize();
                $this->intermailer->initialize_allin();
                $this->intermailer->to([$mailData->email=>$mailData->email]);
                $this->intermailer->set_content("InTrax Employee OTP",$body_msg,"Alt Body tes");
                if($this->intermailer->send())
                {
                    $arrOutput = [
                        'success' 		=> true,
                        'error_code' 	=> "",
                        'message' 		=> "success",
                        'data' 			=> ""
                    ];
                }else{
                    $arrOutput = [
                        'success' 		=> false,
                        'error_code' 	=> "401",
                        'message' 		=> "SMTP Error",
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

    function sendDefaultPin_post(){
        $data = file_get_contents("php://input");
        $mailData = json_decode($data);
        $headers = getRequestHeaders();
        
        $apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;

        if($apikey!=""){
            if($apikey==$this->apikey){
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
                                Hello '.$mailData->employee_name.',</p>
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
                                You have reset the password on the intrax application.
                                Here are the Default Passwords based on your date of birth.</p>
                                <p style="font-family: Roboto;
                                font-size: 20px;
                                font-weight: normal;
                                font-style: normal;
                                font-stretch: normal;
                                line-height: 1.67;
                                letter-spacing: normal;
                                text-align: left;
                                color: rgba(0, 0, 0, 0.7);
                                "><strong>Default Password: </strong>
                                    '.$mailData->pin.'
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
                // $this->intermailer->initialize();
                $this->intermailer->initialize_allin();
                $this->intermailer->to([$mailData->email=>$mailData->email]);
                $this->intermailer->set_content("InTrax Reset PIN",$body_msg,"Alt Body tes");
                if($this->intermailer->send())
                {
                    $arrOutput = [
                        'success' 		=> true,
                        'error_code' 	=> "",
                        'message' 		=> "success",
                        'data' 			=> ""
                    ];
                }else{
                    $arrOutput = [
                        'success' 		=> false,
                        'error_code' 	=> "401",
                        'message' 		=> "SMTP Error",
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
}