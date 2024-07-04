<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Setting_user extends CI_Controller
{
  var $appid;
  var $listMenu = "";
  var $timestamp;
  var $tabel_template  = array(
        'table_open'            => '<table class="table table-bordered table-stripped" >',
        'table_close'           => '</table>'
		);

  function __construct()
  {
    parent::__construct();
    $this->load->model("system_model");
    $this->timestamp = date("Y-m-d H:i:s");

    $languange = !empty($this->session->userdata('lang')) ? $this->session->userdata('lang') :"en";
    $this->load->library("gtrans/gtrans",["lang" => $languange]);

    $this->load->model("menu_model");
    
    $this->load->model("user_model");
    $this->system_model->checkSession(20);
    $this->listMenu = $this->menu_model->list_menu();
  }

  function index(){
    $this->load->library("form_validation");
    $this->load->library("string_manipulation");
    $this->load->model("subscription_model");
    $userId   = $this->session->userdata("ses_userid");

    $dataUser    = $this->user_model->getById($userId);
    $dataCompany = $this->subscription_model->getByAppId($dataUser->appid);

    $data["dataUser"] = $dataUser;
    $data["appid"]    = $dataUser->appid;
    $data['dataCompany'] = $dataCompany;
    if(!empty($this->session->userdata("ses_msg"))){
      $data["msg"] = $this->session->userdata("ses_msg");
      $this->session->unset_userdata("ses_msg");
    }
    $parentViewData = [
      "title"      => "User Profile",  // title page
      "content"    => "setting_user",  // content view
      "viewData"   => $data,
      "listMenu"   => $this->listMenu,
      "varJS"      => [
        "url" => base_url()
      ],
      "externalJS" => [
        "https://cdn.jsdelivr.net/npm/sweetalert2@8",
        "https://fonts.googleapis.com/css?family=Roboto+Mono&display=swap"
      ]
    ];

    $this->load->view("layouts/main",$parentViewData);
    $this->gtrans->saveNewWords();
  }

  function saveChanges(){
    $this->load->library("string_manipulation");
    $fullname       = $this->input->post("fullname");
    $phonenumber    = $this->input->post("phonenumber");
    $password       = $this->input->post("password");
    $confirmpassword= $this->input->post("confirmpassword");

    if($password!="" && $confirmpassword!=""){
      // set new password
      if($password==$confirmpassword){
        $passwordHashed = $this->string_manipulation->hash_password($password);
        $dataUpdate = [
          "user_fullname"=> $fullname,
          "user_phone"   => $phonenumber,
          "user_passw"   => $passwordHashed
        ];
        $output = "fullupdate";
      }else{
        $dataUpdate = [
          "user_fullname"=> $fullname,
          "user_phone"   => $phonenumber
        ];
        $output = "passwordnotmatch";
      }
    }else{
      $dataUpdate = [
        "user_fullname"=> $fullname,
        "user_phone"   => $phonenumber
      ];
      $output = "halfupdate";
    }
    $this->session->set_userdata("ses_username",$fullname);
    $userid = $this->session->userdata("ses_userid");
    $this->user_model->update($dataUpdate,$userid);
    echo $output;
  }

  function saveUserImage(){
    $config['upload_path']          = './sys_upload/userpic/';
    $config['allowed_types']        = 'jpg|jpeg|png';
    $config['max_size']             = 1000;
    $config["encrypt_name"]         = TRUE;
    
    $this->load->library('upload', $config);

    if ( ! $this->upload->do_upload('file')){
      $error  = array('error' => $this->upload->display_errors());
      $errMsg = "";
      foreach ($error as $err) {
        $errMsg .= $err.'<br>';
      }

      $msg = '<div class="alert alert-danger">'.$errMsg.'</div>';
      $this->session->set_userdata("ses_msg",$msg);

    }else{
      $this->load->model("user_model");
      $userID = $this->session->userdata("ses_userid");
      $dataUser = $this->user_model->getById($userID);
      $oldFile = FCPATH.DIRECTORY_SEPARATOR.'sys_upload'.DIRECTORY_SEPARATOR.'userpic'.DIRECTORY_SEPARATOR.$dataUser->user_imgprofile;
      if((!empty($dataUser->user_imgprofile) && file_exists($oldFile))){
        unlink($oldFile);
      }
      $data = $this->upload->data();
      
      $dataUpdate = [
        "user_imgprofile" => $data["file_name"]
      ];
      $this->session->set_userdata("ses_userImage",$data["file_name"]);
      $this->user_model->update($dataUpdate,$userID);
    }
    redirect("user-profile");
  }
}
