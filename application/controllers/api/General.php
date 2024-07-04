<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
/**
 *
 */
class General extends REST_Controller
{
    var $now;
    var $apikey = "IAdev-apikey3fapikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9ed48151b389apikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fapikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9e7b7c2f8d891dfc9b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9";
    function __construct()
    {
        parent::__construct();
        $this->now = date("Y-m-d H:i:s");
        $this->load->model("ijin_model");
    }

    
}