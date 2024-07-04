<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Device_license extends REST_Controller
{
  var $apikey = "IAdev-apikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9";
  function __construct()
  {
    parent::__construct();
  }

  function insertDeviceLicense_post($apikey){
    if(!empty($apikey)){
      $this->load->model("devicelicense_model");
      $dataInsert = [

      ];
      $this->devicelicense_model->insert($dataInsert);
    }else{

    }

  }
}
