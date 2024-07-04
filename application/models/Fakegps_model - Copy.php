<?php
/**
 *
 */
class Fakegps_model extends CI_Model
{
  var $tableName= "tbfakegps";
  var $tableId  = "fakegps_id";
  var $now;
  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
  }

  function insert($dataInsert){
    $dataInsert["create_date"] = $this->now;

    $res = $this->db->insert($this->tableName,$dataInsert);
    return $res;
  }

  function update($dataUpdate,$id){
    $dataUpdate["update_date"] = $this->now;

    $this->db->where($this->tableId,$id);
    $res = $this->db->update($this->tableName,$dataUpdate);
    return $res;
  }

  function delete($id){
	$dataUpdate["update_date"]    = $this->now;
    $dataUpdate["is_del"]             = "1";

    $this->db->where($this->tableId,$id);
    $res = $this->db->update($this->tableName,$dataUpdate);
    return $res;
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
  function getAll(){

    $this->db->select("tbfakegps.*");
    $this->db->from("tbfakegps");

    $this->db->where("tbfakegps.is_del !=","1");
    $sql = $this->db->get();
    return $sql->result();
  }

  function setTotalCabang($totalCabang,$id){
    $appid = $this->session->userdata("ses_appid");
    if(!empty($appid)){
      $dataUpdate = [
        "area_total_cabang" => $totalCabang
      ];
      $this->db->where("appid",$appid);
      $this->db->where($this->tableId,$id);
      $res = $this->db->update($this->tableName,$dataUpdate);
      return $res;
    }else {
      return false;
    }
  }

  function isNameExists($fakegpsName,$fakegpsid){
    $this->db->where("LCASE(fakegps_name)",strtolower($fakegpsName));
    $this->db->where("is_del","0");
    $sql = $this->db->get($this->tableName);

    if($sql->num_rows()>0){
      $data = $sql->row();
      if($data->area_id==$fakegpsid){
        return false;
      }else{
        return true;
      }
    }else{
      return false;
    }
  }

  function getNotActiveFakegps($from,$to,$appid){
    $this->db->where("area_jenis_modif","delete");
    $this->db->where("DATE(area_date_modif) >=",$from);
    $this->db->where("DATE(area_date_modif) <=",$to);
    $sql = $this->db->get($this->tableName);
    return $sql;
  }


  /* mendapatkan array identifikasi area, berasal dari nama
   * area yang di hilangkan spasinya dan di lower
   */
  function getActiveFakegpsIdentification($fakegps_id){
    $this->db->select("fakegps_id");
    $this->db->select("fakegps_name");
    $this->db->where("is_del","0");
    $sql = $this->db->get($this->tableName);
    $output = [];
    foreach ($sql->result() as $row) {
      $output[$row->fakegps_id] = createIdentification($row->fakegps_name);
    }
    return $output;
  }

  function getFakegpsCode($fakegps_id){
    $this->db->select("fakegps_code");
    $sql = $this->db->get($this->tableName);
    $result = [];
    foreach ($sql->result() as $row) {
      $result[] = createIdentification($row->fakegps_code);
    }
    return $result;
  }

  function getFakegpsName($fakegps_id){
    $this->db->select("fakegps_name");
    $this->db->where("is_del","0");
    $sql = $this->db->get($this->tableName);
    $result = [];
    foreach ($sql->result() as $row) {
      $result[] = createIdentification($row->fakegps_name);
    }
    return $result;
  }

  function getName($id){
    $this->db->select("area_name");
    $this->db->where("area_id",$id);
    $sql = $this->db->get($this->tableName);
    if($sql->num_rows()>0){
      return $sql->row()->area_name;
    }
  }
}
