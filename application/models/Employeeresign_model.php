<?php
/**
 *
 */
class Employeeresign_model extends CI_Model
{
  var $now;
  var $tableName = "tbemployeeresign";
  var $tableId   = "employeeresign_id";

  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
  }

  function setResign($employee,$resignDate){
    $appid = $this->session->userdata("ses_appid");
    if(!empty($appid)){
      $userID = $this->session->userdata("ses_userid");
      $data['appid']                        = $appid;
      $data['employeeresign_date_create']   = $this->now;
      $data['employeeresign_user_add']      = $userID;
      $data['employeeresign_status_resign'] = "pending";
      $data['employeeresign_employee_id']   = $employee;
      $data['employeeresign_effdt']         = $resignDate;

      $res = $this->db->replace($this->tableName,$data);
      return $res;
    }
  }

  function changeStatus($employeeID,$status,$appid){
    $dataUpdate = ["employeeresign_status_resign" => $status];
    $this->db->where("appid",$appid);
    $this->db->where("employeeresign_employee_id",$employeeID);
    $res = $this->db->update($this->tableName,$dataUpdate);
    return $res;
  }
}
