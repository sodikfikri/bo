<?php
/**
 *
 */
class Employeebatchcabang_model extends CI_Model
{
  var $tableName= "tbemployeebatchcabang";
  var $tableId  = "employeeareacabang_id";
  var $now;

  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
  }

  function insert($dataInsert,$inputAppid=null){
    if($inputAppid==null){
      $appid = !empty($this->session->userdata("ses_appid")) ? $this->session->userdata("ses_appid") : "";
    }else{
      $appid = $inputAppid;
    }

    if(!empty($appid)){
      /*
      if(!empty($this->session->userdata("ses_userid"))){
        $userID = $this->session->userdata("ses_userid");
        $dataInsert['employeeareacabang_user_add']    = $userID;
      }
      */
      
      $dataInsert['appid'] = $appid;

      $this->db->insert($this->tableName,$dataInsert);
      return $insertID;
    }else{
      return false;
    }
  }
}
