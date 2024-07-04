<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Login_intrax extends CI_Controller
{

  function __construct()
  {
    parent::__construct();
    $this->load->library("session");
  }

  function index(){
    if(!empty($this->session->userdata("ses_userid"))){
      redirect("dashboard");
    }
    $this->load->helper("form");
    $this->load->library("string_manipulation");
    $this->load->library("form_validation");
    $this->load->library("encryption_org");
    $this->form_validation->set_rules("submit","submit","required");
    if($this->form_validation->run()==true){
      $this->load->model("user_model");
      $username = $this->input->post('username');
      $password = $this->input->post('password');

      $passwordHashed = $this->string_manipulation->hash_password($password);
      $result = $this->user_model->getDataUser($username,$passwordHashed);

      if($result!=false){
        if($result->user_isdel=="0" && $result->user_isactive=="1"){
          $this->load->model("subscription_model");
          $this->load->model("external_model");
          $this->load->model("device_model");
          
          $companyData = $this->subscription_model->getByAppId($result->appid);
          $license = $this->external_model->mybillingRecordLogin($result->appid);
          //$commandExist        = $this->device_model->isCommandExist($result->appid);
          
          if($license['orderstatus']=="active"){
            if($companyData){
              $companyName = $companyData->company_name;
            }else{
              $companyName = '';
            }
            // get active addons
            $arrActiveAddons = $this->external_model->myBillingGetActiveAddons($result->appid);
            $arrTrialAddons  = $this->external_model->myBillingGetTrialAddons($result->appid);
            $sessAddons      = [];
            $sessSubscription      = [];
            $infoAddons      = [];

            foreach ($arrActiveAddons as $index => $map) {
              $sessAddons[$index] = $map['qty'];
              $sessSubscription[] = $map['subscription_id'];
              
              $infoAddons[$index] = [
                "name"   => $map["name"],
                "expired"=> $map["expired"],
                "qty"=> $map["qty"]
              ];

            }

            foreach ($arrTrialAddons as $index => $map) {
              
              if(array_key_exists($index,$sessAddons)){
                $sessAddons[$index] += $map['qty'];
              }else{
                $sessAddons[$index] = $map['qty'];
              }
            }
            
            $userFile = FCPATH.DIRECTORY_SEPARATOR.'sys_upload'.DIRECTORY_SEPARATOR.'userpic'.DIRECTORY_SEPARATOR.$result->user_imgprofile;
            if((!empty($result->user_imgprofile) && file_exists($userFile))){
              $imageProfile = $result->user_imgprofile;
            }else{
              $imageProfile = "";
            }
            $encPassword = $this->encryption_org->encode($password);
			$user_access = "100|101";
			$iauser_area_id = $result->iauser_area_id;
            $dataUser = [
              "ses_userid" => $result->userid,
              "ses_appid" => $result->appid,
              "ses_username" => $result->user_fullname,
              "ses_companyname" => $companyName,
              "ses_email" => $username,
              "ses_encpassword" => $encPassword,
              "ses_userImage" => $imageProfile,
              "activeaddons" => $sessAddons,
              "sessSubscription" => $sessSubscription,
              "infoAddons" => $infoAddons,
              "lang" => $result->lang,
              "access" => $user_access,
              "ses_status" => $result->status_user,
              "ses_area" => $iauser_area_id,
              "ses_userStat" => $result->userStat
            ];
            // if($commandExist==true){
            //   $dataUser["msgCommandExist"] = "yes";
            // }
            $this->session->set_userdata($dataUser);
            $this->load->model("device_model");
            $this->load->model("employee_model");
            $this->device_model->renewDeviceLicense($sessAddons,$result->appid,$sessSubscription);

            //tidak ada employee license
            //$this->employee_model->renewEmployeeLicense($sessAddons);
			if($result->appid=='IA01M6858F20210906256' OR $result->appid=='IA01M82337F20230627732'){
				$this->employee_model->nonactiveLicenseExpired($sessSubscription);
			}
            redirect("master-institution");
          }else{
            $msg = 'Connection failed. Please contact our CS';
          }

        }elseif($result->user_isdel=="1"){
          $msg = 'Your Account Was Deleted!';
        }elseif ($result->user_isactive=="0") {
          $msg = 'Your Account Is Not Active!';
        }
      }else{
        $msg = 'Unknown Username or Password!';
      }
    }
    if(!empty($msg)){
      $dataToView['msg'] = '<div class="callout callout-danger">
                              <h4>'.$msg.'</h4>
                            </div>';
    }else{
      $dataToView['msg'] = '';
    }
    if(!empty($this->session->userdata("ses_cloudmsg"))){
      $dataToView['msg'] = $this->session->userdata("ses_cloudmsg");
      $this->session->unset_userdata("ses_cloudmsg");
    }
    $this->load->view("layouts/login_intrax",$dataToView);
  }

  function logout()
  {
    $this->session->sess_destroy();
    redirect("login-intrax");
  }
  
  function renewSession(){
    load_model([
      "device_model",
      "employee_model",
      "external_model"
    ]);

    $appid = $this->session->userdata("ses_appid");
    // get active addons
    $arrActiveAddons = $this->external_model->myBillingGetActiveAddons($appid);
    $arrTrialAddons = $this->external_model->myBillingGetTrialAddons($appid);

    $sessAddons = [];
    foreach ($arrActiveAddons as $index => $map) {
      $sessAddons[$index] = $map['qty'];
    }

    foreach ($arrTrialAddons as $index => $map) {
      if(array_key_exists($index,$sessAddons)){
        $sessAddons[$index] += $map['qty'];
      }else{
        $sessAddons[$index] = $map['qty'];
      }
    }

    $this->device_model->renewDeviceLicense($sessAddons);
    $this->employee_model->renewEmployeeLicense($sessAddons);

    $this->session->set_userdata("activeaddons",$sessAddons);
    echo "OK";
  }
}
