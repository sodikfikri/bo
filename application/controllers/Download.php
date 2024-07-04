<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Download extends CI_Controller
{
  var $now;
  var $apikey = "3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9";

  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
  }

  function inout($apikey,$filename){
    if($apikey==$this->apikey){
      $fullFileName = $filename.".txt";
      if(file_exists("storage/inoutdownload/".$fullFileName)){
        $this->load->helper('download');

        $this->db->where("historydownloadcheckinout_name_of_file",$fullFileName);
        $this->db->update("tbhistorydownloadcheckinout",[
          "historydownloadcheckinout_status" => "success"
        ]);
        force_download("storage/inoutdownload/".$fullFileName,NULL);
      }else{
        echo "file Not Found";
      }
    }else{
      echo "You Dont have Permission";
    }
  }
}
