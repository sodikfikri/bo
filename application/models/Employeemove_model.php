<?php

/**
 *
 */
class Employeemove_model extends CI_Model
{
  var $tableName= "tbemployeemove";
  var $tableId  = "employeemove_id";
  var $now;

  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
  }
  function insert($dataInsert){
    $appid = $this->session->userdata("ses_appid");
    if(!empty($appid)){
      $userID = $this->session->userdata("ses_userid");
      $dataInsert["appid"]              = $appid;
      $dataInsert["employeemove_user_add"]    = $userID;
      $dataInsert["employeemove_date_create"] = $this->now;
      $res = $this->db->insert($this->tableName);
      return $res;
    }else{
      return false;
    }
  }
  function delete($employee){
    $appid = $this->session->userdata("ses_appid");
    if(!empty($appid)){

    }
  }
}
