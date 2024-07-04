<?php

/**
 *
 */
class Employeehistory_model extends CI_Model
{

  var $now;
  var $tableName = "tbemployeehistory";
  var $tableId   = "employeehistory_id";
  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
  }

  function insert($employeeID,$transactionDate,$tipe){
    $appid = $this->session->userdata("ses_appid");
    if(!empty($appid)){
      $userID = $this->session->userdata("ses_userid");
      $dataInsert = [
        "employeehistory_employee_id" => $employeeID,
        "employeehistory_transaction_date" => $transactionDate,
        "employeehistory_jenis_history" => $tipe,
        "appid" =>$appid,
        "employeehistory_user_add" =>$userID,
        "employeehistory_date_create" =>$this->now
      ];
      $res = $this->db->insert($this->tableName,$dataInsert);
      return $res;
    }else{
      return false;
    }
  }
}
