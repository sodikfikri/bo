<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Register_intrax extends CI_Controller
{
  var $now;
  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
    $this->load->library("session");
  }

  function index()
  {
    $this->load->library("form_validation");
    $this->form_validation->set_rules("submit","submit","required");
    $this->form_validation->set_rules('password', 'Password', 'required');
    $this->form_validation->set_rules('repassword', 'Password Confirmation', 'required|matches[password]');
    if($this->form_validation->run()){
      $this->save_register();
    }
    $this->load->helper("form");
    $this->load->view("layouts/register_intrax");
  }

  function save_register()
  {
    $full_name    = $this->input->post("full-name");
    $email        = $this->input->post("email");
    $appid        = $this->input->post("appid");
    $password     = $this->input->post("password");
    $repassword   = $this->input->post("repassword");
    $phone		  = $this->input->post("phone");
    $ipAddr       = $this->input->ip_address();
	$pwlength 	  = strlen($password)-1;
	$pwstart      = substr($password,0,1);
	$pwend        = substr($password,$pwlength,1);
	$repwlength   = strlen($repassword)-1;
	$repwstart    = substr($repassword,0,1);
	$repwend      = substr($repassword,$repwlength,1);
	if(preg_match("/create/i", $full_name)==0 AND preg_match("/drop/i", $full_name)==0 AND preg_match("/delete/i", $full_name)==0 AND preg_match("/select/i", $full_name)==0 AND preg_match("/insert/i", $full_name)==0 AND preg_match("/update/i", $full_name)==0){
		if($pwstart!=' ' AND $pwend!=' ' AND $repwstart!=' ' AND $repwend!=' '){
			if($password==$repassword){
			  $this->load->library("string_manipulation");
			  $this->load->library("encryption_org");
			  $passwordHashed = $this->string_manipulation->hash_password($password);
			  $this->load->model("external_model");
			  $this->load->model("user_model");
			  $this->load->model("subscription_model");
			  $this->load->model("otp_model");
			  $this->load->model("menu_model");
			  $arrAccess = $this->menu_model->getAllActiveID();
			  $strAccess = implode("|", $arrAccess);
			  $dataInsertUser = [
				  "appid"          => $appid,
				  "user_emailaddr" => $email,
				  "user_fullname"  => $full_name,
				  "user_phone"     => $phone,
				  "user_datecreate"=> $this->now,
				  "user_passw"     => $passwordHashed,
				  "user_access"    => $strAccess,
				  "userStat"       => 2
				];

				$this->user_model->insert($dataInsertUser);
				$userID    = $this->db->insert_id();
				$encUserID = $this->encryption_org->encode($userID);
				$OTP = $this->otp_model->generate($userID,"register","email");

				// kirim email
				$emailSent = $this->sendEmailOTP($full_name,$email,$OTP);
				if ($emailSent) {
				  redirect("register-otp-input-intrax/".$encUserID);
				}else{
				  echo 'Cannot Send Email OTP';
				}
			}else{
			  echo "<script>alert('Those passwords did not match. Try again!');window.location.href='register-intrax';</script>";
			}
		} else {
			echo "<script>alert('Your password cannot begin or end with a blank space!');window.location.href='register-intrax';</script>";
		}
	} else {
		echo "<script>alert('It is forbidden to use the words CREATE, DROP, DELETE, SELECT, INSERT, UPDATE!');window.location.href='register-intrax';</script>";
	}
  }

  /** step 2 registration **/
  function inputOTP($encUserID)
  {
    $this->load->model("user_model");
    $this->load->model("otp_model");

    $this->load->helper("form");
    $this->load->library("encryption_org");
    $userID     = $this->encryption_org->decode($encUserID);
    $dataUser   = $this->user_model->getById($userID);
    $dataOutput['email'] = $dataUser->user_emailaddr;
    $dataOutput['id']    = $encUserID;
    $dataOTP   = $this->otp_model->getActiveOTP($userID,"register","email");
    if($dataOTP){
      $dataOutput['expiredDate'] = date("M d, Y H:i:s",strtotime($dataOTP->expired_date));
    }else{
      $dataOutput['expiredDate'] = date("M d, Y H:i:s");
    }

    $this->load->view("layouts/register_inputotp_intrax",$dataOutput);
  }

  function resendOTP(){
    $this->load->library("encryption_org");
    $this->load->model("otp_model");
    $this->load->model("user_model");

    $encUserID = $this->input->post("id");
    $userID    = $this->encryption_org->decode($encUserID);
    $newOTP    = $this->otp_model->resendOTP($userID,"register","email");
    $dataUser  = $this->user_model->getById($userID);
    $result    = $this->sendEmailOTP($dataUser->user_fullname,$dataUser->user_emailaddr,$newOTP);
    if($result){
      $dataActiveOTP = $this->otp_model->getActiveOTP($userID,"register","email");
      echo date("M d, Y H:i:s",strtotime($dataActiveOTP->expired_date));
    }else{
      echo "failed";
    }
  }

  function sendEmailOTP($userName,$email,$OTP)
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
                      Hello '.$userName.',</p>
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
                      ">
                      This Your OTP Number for register to InAct System, it will expired after 5 minutes.</p>
                      <p style="font-family: Roboto;
                        font-size: 15px;
                        font-weight: normal;
                        font-style: normal;
                        font-stretch: normal;
                        line-height: 1.67;
                        letter-spacing: normal;
                        text-align: left;
                        color: rgba(0, 0, 0, 0.7);
                        "><b>'.$OTP.'</b></p>
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
    // $this->intermailer->initialize();
    $this->intermailer->initialize_allin();
    $this->intermailer->to([$email=>$userName]);
    $this->intermailer->set_content("OTP Code for InAct Registration",$body_msg,"Alt Body tes");
    if($this->intermailer->send())
    {
      return true;
    }else{
      return false;
    }
  }

  function submitOTP()
  {
    $this->load->library("encryption_org");
    $this->load->model("otp_model");
    $this->load->model("user_model");
    $this->load->model("subscription_model");

    $encID  = $this->input->post("id");
    $otp    = $this->input->post("otp");
    $user_id= $this->encryption_org->decode($encID);

    $dataOTP = $this->otp_model->getActiveOTP($user_id,"register","email");
    if($dataOTP){

      if($dataOTP->otp == $otp){
        $dataUser = $this->user_model->getById($user_id);
        // activate user
        $this->user_model->activate($user_id);
        
        // activate subscription
        $this->subscription_model->activate($dataUser->appid);
        // make default area dan branch
        
        $this->load->model("area_model");
        $this->load->model("cabang_model");
        
        //$areaId = $this->area_model->makeDummyArea($dataUser->appid);
        
        //$this->cabang_model->makeDummyBranch($dataUser->appid,$areaId);
        // set OTP success
        $this->otp_model->setSuccess($dataOTP->otp_id);

        // send confirm to myBilling
        $this->load->model("external_model");
        $activate = $this->external_model->myBillingActivateUser($dataUser->appid);
        if($activate==true){
          $this->autoOrderTrialAddons($dataUser->appid);
          echo "found";
        }
      }else{
        echo "notmatch";
      }
    }else{
      echo "notfound";
    }
  }

  function checkEmailExist(){
    $this->load->model("user_model");
    $email = $this->input->post("email");
    $res = $this->user_model->getDataByEmail($email);
    if($res==false){
      echo "NOtExist";
    }else{
      echo "Exist";
    }
  }

  private function autoOrderTrialAddons($appid){
    $this->load->model("external_model");
    $mustHaveAddons = ["machinelicense","employeelicense"];
    $this->db->where_in("systemaddons_code",$mustHaveAddons);
    $sql = $this->db->get("systemaddons");
    $dataSystemAddons = $sql->result();

    $arrMustAddonsCode = [];
    foreach ($dataSystemAddons as $row) {
      $arrMustAddonsCode[$row->addonscode] = $row->trial_quota;
    }

    $appAddons = $this->external_model->myBillingGetAppAddons($appid);

    foreach ($appAddons as $row1) {
      if(array_key_exists($row1["addonscode"],$arrMustAddonsCode)){
        $qty = $arrMustAddonsCode[$row1["addonscode"]];
        if($qty>0){
          $this->external_model->myBillingTakeTrialAddons($appid,$row1["pluginsid"],$qty);
        }
      }
    }
  }
}
