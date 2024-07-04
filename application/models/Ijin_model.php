<?php
/**
 *
 */
class Ijin_model extends CI_Model
{
  var $tableName= "tbijin";
  var $tableId  = "ijin_id";
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
      $dataInsert["appid"]            = $appid;
      $dataInsert["ijin_user_add"]    = $userID;
      $dataInsert["ijin_date_create"] = $this->now;

      $res = $this->db->insert($this->tableName,$dataInsert);

      return $res;
    }else{
      return false;
    }
  }

  function update($dataUpdate,$id){
    $appid = $this->session->userdata("ses_appid");
    if(!empty($appid)){
      $userID = $this->session->userdata("ses_userid");

      $dataUpdate["ijin_user_modif"]    = $userID;
      $dataUpdate["ijin_date_modif"]    = $this->now;
      $dataUpdate["ijin_jenis_modif"]   = "edit";

      $this->db->where("appid",$appid);
      $this->db->where($this->tableId,$id);
      $res = $this->db->update($this->tableName,$dataUpdate);
      return $res;
    }else {
      return false;
    }
  }

  function delete($id){
    $appid = $this->session->userdata("ses_appid");
    if(!empty($appid)){
      $userID = $this->session->userdata("ses_userid");

      $dataUpdate["ijin_user_modif"]    = $userID;
      $dataUpdate["ijin_date_modif"]    = $this->now;
      $dataUpdate["ijin_jenis_modif"]   = "delete";
      $dataUpdate["is_del"]             = "1";

      $this->db->where("appid",$appid);
      $this->db->where($this->tableId,$id);
      $res = $this->db->update($this->tableName,$dataUpdate);
      return $res;
    }else {
      return false;
    }
  }

  function getById($id,$appid=null){
    if($appid==null){
      $appid = $this->session->userdata("ses_appid");
    }

    if(!empty($appid)){
      $this->db->where("appid",$appid);
      $this->db->where($this->tableId,$id);
      $this->db->where("is_del !=","1");
      $sql = $this->db->get($this->tableName);
      if ($sql->num_rows()>0) {
        return $sql->row();
      }else{
        return false;
      }
    }else {
      return false;
    }
  }
  function getAll($appid=""){
    if($appid==""){
      $appid = $this->session->userdata("ses_appid");
    }

    if(!empty($appid)){
      $this->db->select("tbijin.*");
      $this->db->from("tbijin");

      $this->db->where("tbijin.appid",$appid);
      $this->db->where("tbijin.is_del !=","1");
      $sql = $this->db->get();
      return $sql->result();
    }else {
      return false;
    }
  }
  
  function getAllByCompanyid($companyid){
    if(!empty($companyid)){
		$this->db->select("tbijin.*");
		$this->db->from("tbijin");
		$this->db->join("iasubscription","tbijin.appid = iasubscription.appid");
		$this->db->where("iasubscription.intrax_company_id",$companyid);
		$this->db->where("tbijin.is_del !=","1");
		$sql = $this->db->get();
		return $sql->result();
    }else {
		return false;
    }
  }

  function getIjinName($appid){
    $this->db->select("ijin_name");
    $this->db->where("appid",$appid);
    $this->db->where("is_del","0");
    $sql = $this->db->get($this->tableName);
    $result = [];
    foreach ($sql->result() as $row) {
      $result[] = createIdentification($row->ijin_name);
    }
    return $result;
  }

}
