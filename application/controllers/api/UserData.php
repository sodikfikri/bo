<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
/**
 *
 */
class Userdata extends REST_Controller
{
  var $now;
  var $apikey = "3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9";
  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
  }

  function auth_post(){
    load_model(["user_model"]);
    load_library(["string_manipulation"]);
    if(!empty($this->post("key")) && !empty($this->post("app_id"))){
      $key      = $this->post("key");
      if($key==$this->apikey){
        $appid    = $this->post("app_id");
        $username = $this->post("username");
        $password = $this->post("password");
        $passwordHashed = $this->string_manipulation->hash_password($password);
        $this->db->where("appid",$appid);
        $result = $this->user_model->getDataUser($username,$passwordHashed);
        if($result!=false)
        {
          $status = "success";
        }else{
          $status = "failed";
        }
        $output = [
          "app_id" => $appid,
          "status" => $status
        ];
      }else{
        $output = "Invalid API Key";
      }
    }else{
      $output = "You Must Set app_id and key";
    }
    echo json_encode($output);
  }
}
