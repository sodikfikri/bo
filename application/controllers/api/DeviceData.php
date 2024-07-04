<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
/**
 *
 */
class DeviceData extends REST_Controller
{
  var $now;
  var $apikey = "3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9";
  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
  }

  function index_post(){
    load_model(["device_model","external_model"]);
    // get appid
    $appid = !empty($_POST["app_id"]) ? $_POST["app_id"]  : "";
    // get key
    $key   = !empty($_POST["key"])   ? $_POST["key"]    : "";

    if(!empty($appid) && !empty($key)){
      if($key==$this->apikey){
        $devices = $this->device_model->getAllByAppId($appid);
        $arrDevices = [];
        $activeDevice = 0;
        //
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
        $totalDeviceLicense = !empty($sessAddons["machinelicense"]) ? $sessAddons["machinelicense"] : 0;
        //
        foreach ($devices->result() as $row) {
          $rangeActive = dateDifference($this->now,$row->device_last_communication);
          if($row->device_last_communication!=null && $rangeActive["minute"]<2){
            $activeDevice++;
            $connectionStatus = "connect";
          }else{
            $connectionStatus = "disconnect";
          }
          $arrDevices[] = [
            "area_device" => $row->area_name,
            "branch_device" => $row->cabang_name,
            "sn_device"   => $row->device_SN,
            "code_device" => $row->device_code,
            "device_name" => $row->device_name,
            "ip_device"   => $row->device_ip,
            "license"     => $row->device_license,
            "connection_status" => $connectionStatus
          ];
        }

        $output = json_encode([
          "app_id" => $appid,
          //"tot_device_active" => $activeDevice,
          "tot_device" => $devices->num_rows(),
          "tot_connect" => $activeDevice,
          "tot_device_license" => $totalDeviceLicense,
          "data_device" => $arrDevices,
        ]);

      }else{
        $output = "Key is not valid";
      }
    }else{
      $output = "appid and key must fill";
    }
    echo $output;
  }

  /* return all registered device
   *
  */
  function registered_post(){
    load_model(["device_model"]);
    // get appid
    $appid = !empty($_POST["app_id"]) ? $_POST["app_id"]  : "";
    // get key
    $key   = !empty($_POST["key"])   ? $_POST["key"]    : "";

    if(!empty($appid) && !empty($key)){
      if($key==$this->apikey){
        $devices = $this->device_model->getWholeByAppId($appid);
        $arrDevices = [];
        $activeDevice = 0;
        foreach ($devices->result() as $row) {
          if(strpos($row->device_SN, "suspend")==0){
            $rangeActive = dateDifference($this->now,$row->device_last_communication);
            if($row->is_del!=1){
              $activeDevice++;
              $arrDevices[] = [
                "area_device" => $row->area_name,
                "branch_device" => $row->cabang_name,
                "sn_device"   => $row->device_SN,
                "code_device" => $row->device_code,
                "device_name" => $row->device_name,
                "ip_device"   => $row->device_ip
              ];
            }
          }
        }

        $output = json_encode([
          "app_id" => $appid,
          "tot_device_active" => $activeDevice,
          "tot_device" => $devices->num_rows(),
          "data_device" => $arrDevices
        ]);

      }else{
        $output = "Key is not valid";
      }
    }else{
      $output = "appid and key must fill";
    }
    echo $output;
  }
}
