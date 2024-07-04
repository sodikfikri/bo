<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Forgot_password extends CI_Controller
{

  function __construct()
  {
    parent::__construct();
    $this->load->library("session");
  }

  function index(){
    $this->load->library("form_validation");
    $this->load->helper("form");
    $this->form_validation->set_rules("submit","submit","required");

    if($this->form_validation->run()==true){
      $this->load->model("user_model");
      $email = $this->input->post("email");
      $this->db->where("user_isdel","0");
      $dataUser = $this->user_model->getDataByEmail($email);
      if($dataUser){
        $this->load->model("otp_model");
        // delete otp lama
        $this->otp_model->stopOTP($dataUser->userid,"forgot_password","email");
        // generate otp baru
        $OTP = $this->otp_model->generate($dataUser->userid,"forgot_password","email");

        $resSendEmail = $this->sendEmailOTP($dataUser->user_fullname,$dataUser->user_emailaddr,$OTP);
        if($resSendEmail){
          $this->load->library("encryption_org");
          $encUserID = $this->encryption_org->encode($dataUser->userid);
          redirect("forgot-password-otpauth/".$encUserID);
        }
      }else{
        $msg = 'Your Email Was Not Found!';
      }
    }
    if(!empty($msg)){
      $dataToView['msg'] = '<div class="callout callout-danger">
                              <h4>'.$msg.'</h4>
                            </div>';
    }else{
      $dataToView['msg'] = '';
    }
    $this->load->view("layouts/forgot_password",$dataToView);
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
                      This Your OTP Number for reset your password on InAct System, it will expired after 5 minutes.</p>
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
    $this->intermailer->initialize();
    $this->intermailer->to([$email=>$userName]);
    $this->intermailer->set_content("OTP Code For InAct Reset Password",$body_msg,"Alt Body tes");
    if($this->intermailer->send())
    {
      return true;
    }else{
      return false;
    }
  }

  function OTPAuth($encUserID){
    $this->load->library("encryption_org");
    $this->load->model("user_model");
    $this->load->model("otp_model");
    $this->load->helper("form");
    $userID     = $this->encryption_org->decode($encUserID);
    $dataUser   = $this->user_model->getById($userID);
    $sql        = $this->otp_model->getActiveOTP($userID,"forgot_password","email");

    if($sql!=false){
      // Jan 5, 2021 15:37:25
      $dataOutput['expired'] = date("M d, Y H:i:s",strtotime($sql->expired_date));
    }else{
      $dataOutput['expired'] = date("M d, Y H:i:s");
    }

    $dataOutput['email'] = $dataUser->user_emailaddr;
    $dataOutput['id']    = $encUserID;
    $this->load->view("layouts/forgotpassword_inputotp",$dataOutput);
  }

  function resendOTP(){
    $this->load->library("encryption_org");
    $this->load->model("otp_model");
    $this->load->model("user_model");

    $encUserID = $this->input->post("id");
    $userID    = $this->encryption_org->decode($encUserID);
    $newOTP    = $this->otp_model->resendOTP($userID,"forgot_password","email");
    $dataUser  = $this->user_model->getById($userID);
    $result    = $this->sendEmailOTP($dataUser->user_fullname,$dataUser->user_emailaddr,$newOTP);
    if($result){
      $dataOTP = $this->otp_model->getActiveOTP($userID,"forgot_password","email");

      echo date("M d, Y H:i:s",strtotime($dataOTP->expired_date));
    }else{
      echo "failed";
    }
  }
  function submitOTP()
  {
    $this->load->library("string_manipulation");
    $this->load->library("encryption_org");
    $this->load->model("otp_model");
    $this->load->model("user_model");
    $this->load->model("subscription_model");

    $encID  = $this->input->post("id");
    $otp    = $this->input->post("otp");
    $user_id= $this->encryption_org->decode($encID);

    $dataOTP = $this->otp_model->getActiveOTP($user_id,"forgot_password","email");

    if($dataOTP){
      if($dataOTP->otp == $otp){
        // set OTP success
        $this->otp_model->setSuccess($dataOTP->otp_id);
        $authkey = $this->string_manipulation->hash_authkey($user_id.date('YmdHis'));
        $this->user_model->setAuthKey($authkey,$user_id);
        $output = [
          "status"=> "found",
          "data"  => $authkey
        ];
      }else{
        $output = [
          "status"=> "notmatch",
          "data"  => ""
        ];
      }
    }else{
      $output = [
        "status"=> "notfound",
        "data"  => ""
      ];
    }

    echo json_encode($output);
  }

  function change_password($authkey,$encUserID){
    $this->load->library("string_manipulation");
    $this->load->library("encryption_org");
    $this->load->library("form_validation");
    $this->load->model("user_model");
    $this->load->helper("form");
    $userID   = $this->encryption_org->decode($encUserID);
    $dataUser = $this->user_model->getAuthenticateUser($userID,$authkey);

    $this->form_validation->set_rules("submit","submit","required");
    if($this->form_validation->run()==true){
      $password1 = $this->input->post("password1");
      $password2 = $this->input->post("password2");

      if($password1 == $password2){
        $passwordHashed = $this->string_manipulation->hash_password($password1);
        // pengecekan dengan password lama
        if($passwordHashed==$dataUser->user_passw){
          $msg = '<div class="callout callout-danger">
                  <h4>Password Cannot Same With Old Password!</h4>
                  </div>';
        }else{
          $res = $this->user_model->setNewPassword($passwordHashed,$userID,$authkey);
          if($res){
            echo '<script type="text/javascript">alert("Change Password Success!");</script>';
            $submitResult = "success";
            $cloudMsg = '<div class="callout callout-success">
                    <h4>Reset password success!</h4>
                    <p>You now can use the new password</p>
                    </div>';
            $this->session->set_userdata("ses_cloudmsg",$cloudMsg);
            redirect("login");
          }
        }
      }else{
        $msg = '<div class="callout callout-danger">
                <h4>Password is not match!</h4>
                </div>';
      }
    }

    if(!empty($msg)){
      $data['msg'] = $msg;
    }else{
      $data = '';
    }
    if($dataUser!=false){
      $this->load->view("layouts/changepassword",$data);
    }else{
      echo 'Authorized Page';
    }

  }
}
