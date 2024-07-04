<?php

/**
 *
 */
class Subscription_model extends CI_Model
{
  var $tableName = "iasubscription";
  var $tableId   = "iasubscription_id";
  var $now;
  
  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
  }

  function insert($dataInsert)
  {
    $this->db->insert($this->tableName,$dataInsert);
  }

  function activate($appid)
  {
    $dataUpdate = [
      "active_date" => $this->now,
      "status"  =>"active"
    ];

    $this->db->where("appid",$appid);
    $res = $this->db->update($this->tableName,$dataUpdate);
    if($res){
      return true;
    }else{
      return false;
    }
  }
  
  function getByAppId($appid)
  {
    $this->db->where("appid",$appid);
    $sql = $this->db->get($this->tableName);

    if($sql->num_rows()>0){
      return $sql->row();
    }else {
      return false;
    }
  }
  
  function getAppIdByCompanyID($company_id)
  {
	$this->db->select("appid");
    $this->db->where("intrax_company_id",$company_id);
    $sql = $this->db->get($this->tableName);

    if($sql->num_rows()>0){
      return $sql->row();
    }else {
      return false;
    }
  }

  function getByIntraxCompanyID($intraxCompanyID)
  {
    $this->db->where("intrax_company_id",$intraxCompanyID);
    $sql = $this->db->get($this->tableName);

    if($sql->num_rows()>0){
      return $sql->row();
    }else {
      return false;
    }
  }
  
  function updateByAppId($data,$appid){
    $this->db->where("appid",$appid);
    $res = $this->db->update($this->tableName,$data);
    if($res){
      return true;
    }else{
      return false;
    }
  }

  function getActiveCompany(){
    $this->db->where("status","active");
    $res = $this->db->get($this->tableName);
    return $res;
  }

  function switchCompanyType($id,$type){
    $data = [
      "is_real" => $type
    ];
    $this->db->where($this->tableId,$id);
    $res = $this->db->update($this->tableName,$data);
    return $res;
  }

  function getActiveAll(){
    $this->db->where("status","active");
    $sql = $this->db->get($this->tableName);
    return $sql;
  }

  function getCompanyinfo($appid){
    $this->load->model("cabang_model");
    $branchData = $this->cabang_model->getAll($appid);
    
    $output = array(
      "branch" => $branchData
    );

    return $output;
  }
  function getIntraxCompany($intraxCompanyId,$email){
    $this->db->where("company_email",$email);
    $this->db->where("intrax_company_id",$intraxCompanyId);
    $sql = $this->db->get($this->tableName);
    if($sql->num_rows()>0){
      return $sql->row();
    }else{
      return false;
    }
  }

  function changeIntraxPlan($intraxCompanyId,$newPlanCode){
    $dataUpdate = [
      "intrax_plan_code" => $newPlanCode
    ];
    $this->db->where("intrax_company_id",$intraxCompanyId);
    return $this->db->update($this->tableName,$dataUpdate);
  }
}
