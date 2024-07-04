<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Desktop_app extends CI_Controller
{
  var $apikey = "819a413c70f5954f1a154fe0c077935adb06b5e67c2cfac305255bc3ad74745b";
  var $now;
  function __construct()
  {
    parent::__construct();
    $this->now = date('Y-m-d H:i:s');
  }

  function getMainUser($apikey){
    $response = $this->input->post("response");
    if($apikey == $this->apikey){
      $appId    = $this->input->post("appid");


      load_model([
        "user_model",
        "subscription_model",
        "external_model",
        "device_model"
      ]);

      $subscription = $this->subscription_model->getByAppId($appId);
      $parentUser   = $this->user_model->getRootUser($appId);
      $licenseSubscription = $this->external_model->getDetailSubscription($appId);

      $arrActiveAddons= $this->external_model->myBillingGetActiveAddons($appId);
      $arrTrialAddons = $this->external_model->myBillingGetTrialAddons($appId);
      $addons = [];

      foreach ($arrActiveAddons as $index => $map) {
        $addons[$index] = $map['qty'];
      }

      foreach ($arrTrialAddons as $index => $map) {
        if(array_key_exists($index,$addons)){
          $addons[$index] += $map['qty'];
        }else{
          $addons[$index] = $map['qty'];
        }
      }

      $this->db->select("tbdevice.*");
      $this->db->select("tbcabang.cabang_name");

      $this->db->from("tbdevice");
      $this->db->join("tbcabang","tbcabang.cabang_id = tbdevice.device_cabang_id");

      $this->db->where("tbcabang.is_del !=","1");
      $this->db->where("tbdevice.appid",$appId);
      $this->db->where("tbdevice.is_del !=","1");
      $this->db->where("tbdevice.device_license ","active");
      $sqlDevice = $this->db->get();


      $machineLicense = !empty($addons['machinelicense'])  ? $addons['machinelicense'] : 0;
      $employeeLicense= !empty($addons['employeelicense']) ? $addons['employeelicense'] : 0;

      $device       = "";
      $deviceLeftOver = $machineLicense - $sqlDevice->num_rows();
      if($sqlDevice){
        foreach ($sqlDevice->result() as $row) {
          $device .= 'SN:'.$row->device_SN.'|';
        }
      }
      for ($i=0; $i < $deviceLeftOver ; $i++) {
        $device .= 'SN:-|';
      }

      $datediff = dateDifference($this->now,$licenseSubscription->nextduedate);

      $strOutput    = $subscription->status.'|'.
                      'inact cloud'.'|'.
                      $subscription->company_name.'|'.
                      $licenseSubscription->nextduedate."|".
                      $datediff['day']."|".
                      $machineLicense."|".
                      $parentUser->user_emailaddr."|this is encrypt|".
                      $employeeLicense."|".$device;
      $input = [
                  "success"    => true,
                  "message"    => "",
                  "error_code" => 200,
                  "data"       => $strOutput
               ];
    }else{
      $input = [
        "success"    => false,
        "message"    => "Invalid API key!",
        "error_code" => 401,
        "data"       => ""
      ];
    }
    echo $this->formatAPIStandart($input,$response);
  }

  function login($apikey){
    $response = $this->input->post("response");
    if($apikey == $this->apikey){
      load_model([
        "user_model",
      ]);
      load_library([
        "encryption_org",
        "string_manipulation"
      ]);
      $appid    = $this->input->post("appid");
      $username = $this->input->post("username");
      $password = $this->input->post("password");

      $passwHashed = $this->string_manipulation->hash_password($password);
      $result = $this->user_model->getDataUser($username,$passwHashed);
      if($result){
        $input = [
          "success"   => true,
          "error_code"=> 200,
          "message"   => "",
          "data"      => "true"
        ];
      }else{
        $input = [
          "success"   => true,
          "error_code"=> 200,
          "message"   => "",
          "data"      => "false"
        ];
      }
    }else{
      $input = [
        "success"   => false,
        "error_code"=> 401,
        "message"   => "false apikey",
        "data"      => []
      ];
    }
    echo $this->formatAPIStandart($input,$response);
  }

  function formatAPIStandart($input,$response){
    $success    = $input['success'];
    $error_code = $input['error_code'];
    $message    = $input['message'];
    $data       = $input['data'];
    if($response=="stringvb"){
      if($success==true){
        return $data;
      }else{
        return $message;
      }
    }else{
      $arrOutput = [
        "success"   => $success,
        "error_code"=> $error_code,
        "message"   => $message,
        "data"      => $data
      ];
      header("Content-Type:application/json");
      return json_encode($arrOutput);
    }
  }
}
