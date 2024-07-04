<?php
/**
 *
 */
class Employeetemplate_model extends CI_Model
{
  var $now;
  var $tableName = "tbemployeetemplate";
  var $tableId   = "employeetemplate_id";

  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
  }

  function replace($data){
    $this->db->where("appid",$data["appid"]);
    $this->db->where("employeetemplate_employee_id",$data["employeetemplate_employee_id"]);
    $this->db->where("employeetemplate_index",$data["employeetemplate_index"]);
    $this->db->where("employeetemplate_jenis",$data["employeetemplate_jenis"]);
    $sql = $this->db->get($this->tableName);
    if($sql->num_rows()>0){
      $oldTemplate = $sql->row();
      $templateId  = $oldTemplate->employeetemplate_id;
      $this->db->where($this->tableId,$templateId);
      $this->db->update($this->tableName,$data);
    }else{
      $this->db->insert($this->tableName,$data);
    }
    return true;
  }
  function getTemplateID($employee_id,$template_index,$type){
    $this->db->where("employeetemplate_employee_id",$employee_id);
    $this->db->where("employeetemplate_index",$template_index);
    $this->db->where("employeetemplate_jenis",$type);
    $sql = $this->db->get($this->tableName);
    if($sql->num_rows()>0){
      return $sql->row()->employeetemplate_id;
    }else{
      return false;
    }

  }

  function getEmployeeTemplate($employeeID){
    $this->db->where("employeetemplate_employee_id",$employeeID);
    $sql = $this->db->get($this->tableName);
    return $sql;
  }

  function clearTemplate($employeeID, $type, $appid){
    if($type=="face"){
      $this->db->where("appid", $appid);
      $this->db->where("employeetemplate_jenis", $type);
      $this->db->where("employeetemplate_employee_id", $employeeID);
      $this->db->delete($this->tableName);
      
    }elseif ($type=="fingerprint") {
      return false;
    }
  }
  
  function setDeleteTemplate($arrEmployeeID){
	$this->db->where_in('employeetemplate_employee_id', $arrEmployeeID);
    $res = $this->db->delete('tbemployeetemplate');
    return $res;
  }
}
