<?php
/**
 *
 */
class Area_model extends CI_Model
{
  var $tableName= "tbarea";
  var $tableId  = "area_id";
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
      $dataInsert["area_user_add"]    = $userID;
      $dataInsert["area_date_create"] = $this->now;

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

      $dataUpdate["area_user_modif"]    = $userID;
      $dataUpdate["area_date_modif"]    = $this->now;
      $dataUpdate["area_jenis_modif"]   = "edit";

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

      $dataUpdate["area_user_modif"]    = $userID;
      $dataUpdate["area_date_modif"]    = $this->now;
      $dataUpdate["area_jenis_modif"]   = "delete";
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
      $this->db->select("tbarea.*");
      $this->db->select(
        "
        (
          select count(tbcabang.cabang_id) from tbcabang
          where
          tbcabang.cabang_area_id = tbarea.area_id
          and tbcabang.is_del = '0'
        ) as totalActiveBranch
        ");

      $this->db->from("tbarea");

      $this->db->where("tbarea.appid",$appid);
      $this->db->where("tbarea.is_del !=","1");
      $sql = $this->db->get();
      return $sql->result();
    }else {
      return false;
    }
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

  function setTotalEmployee($totalEmployee,$id){
    $appid = $this->session->userdata("ses_appid");
    if(!empty($appid)){
      $dataUpdate = [
        "area_total_emp" => $totalEmployee
      ];
      $this->db->where("appid",$appid);
      $this->db->where($this->tableId,$id);
      $res = $this->db->update($this->tableName,$dataUpdate);
      return $res;
    }else {
      return false;
    }
  }

  function setTotalDevice($totalDevice,$id){
    $appid = $this->session->userdata("ses_appid");
    if(!empty($appid)){
      $dataUpdate = [
        "area_total_device" => $totalDevice
      ];
      $this->db->where("appid",$appid);
      $this->db->where($this->tableId,$id);
      $res = $this->db->update($this->tableName,$dataUpdate);
      return $res;
    }else {
      return false;
    }
  }


  function setTotalEmployeePendingMutasiMasuk(){

  }

  function setTotalEmployeePendingMutasiKeluar(){

  }

  function setTotalEmployeePendingNew(){

  }

  function setTotalEmployeePendingResign(){

  }

  function isCodeExists($code,$areaid,$appid){
    $this->db->where("appid",$appid);
    $this->db->where("area_code",$code);
    $sql = $this->db->get($this->tableName);
    if($sql->num_rows()>0){
      $data = $sql->row();
      if($data->area_id==$areaid){
        return false;
      }else{
        return true;
      }
    }else{
      return false;
    }
  }

  function isNameExists($areaName,$areaid,$appid){
    $this->db->where("appid",$appid);
    $this->db->where("LCASE(area_name)",strtolower($areaName));
    $this->db->where("is_del","0");
    $sql = $this->db->get($this->tableName);

    if($sql->num_rows()>0){
      $data = $sql->row();
      if($data->area_id==$areaid){
        return false;
      }else{
        return true;
      }
    }else{
      return false;
    }
  }

  function getNotActiveArea($from,$to,$appid){
    $this->db->where("area_jenis_modif","delete");
    $this->db->where("DATE(area_date_modif) >=",$from);
    $this->db->where("DATE(area_date_modif) <=",$to);
    $sql = $this->db->get($this->tableName);
    return $sql;
  }


  function saveIgnoreDuplicate($dataInsert,$checkerAreaCode="",$checkerAreaName=""){
    $this->db->select("area_id");
    $where = $dataInsert;

    if(!empty($dataInsert["area_code"])){
      unset($where["area_code"]);
      $where[" REPLACE(LOWER(area_code), ' ', '') ="] = createIdentification($dataInsert["area_code"]);
    }

    if(!empty($dataInsert["area_name"])){
      unset($where["area_name"]);
      $where[" REPLACE(LOWER(area_name), ' ', '') ="] = createIdentification($dataInsert["area_name"]);
    }

    unset($where["area_user_add"]);
    unset($where["area_date_create"]);

    $sqlCheck   = $this->db->get_where($this->tableName,$where);

    if($sqlCheck->num_rows()>0){
      $rows     = $sqlCheck->row();
      $areaID   = $rows->area_id;
      $insertStatus = "skipped";
    }else{
      if(!in_array(createIdentification($dataInsert['area_code']),$checkerAreaCode) && !in_array(createIdentification($dataInsert['area_name']),$checkerAreaName)){
        $insert_query = $this->db->insert_string($this->tableName, $dataInsert);
        $insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
        $this->db->query($insert_query);
        $areaID       = $this->db->insert_id();
        $insertStatus = "inserted";
      }elseif(in_array(createIdentification($dataInsert['area_code']),$checkerAreaCode)&&!in_array(createIdentification($dataInsert['area_name']),$checkerAreaName)){
        $insertStatus = "duplicated_code";
        $areaID       = "";
      }elseif(!in_array(createIdentification($dataInsert['area_code']),$checkerAreaCode)&&in_array(createIdentification($dataInsert['area_name']),$checkerAreaName)){
        $insertStatus = "duplicated_name";
        $areaID       = "";
      }
    }
    $output = [
      "area_id"     => $areaID,
      "insertStatus"=> $insertStatus
    ];
    return $output;
  }
  /* mendapatkan array identifikasi area, berasal dari nama
   * area yang di hilangkan spasinya dan di lower
   */
  function getActiveAreaIdentification($appid){
    $this->db->select("area_id");
    $this->db->select("area_name");
    $this->db->where("appid",$appid);
    $this->db->where("is_del","0");
    $sql = $this->db->get($this->tableName);
    $output = [];
    foreach ($sql->result() as $row) {
      $output[$row->area_id] = createIdentification($row->area_name);
    }
    return $output;
  }

  function getAreaCode($appid){
    $this->db->select("area_code");
    $this->db->where("appid",$appid);
    $sql = $this->db->get($this->tableName);
    $result = [];
    foreach ($sql->result() as $row) {
      $result[] = createIdentification($row->area_code);
    }
    return $result;
  }

  function getAreaName($appid){
    $this->db->select("area_name");
    $this->db->where("appid",$appid);
    $this->db->where("is_del","0");
    $sql = $this->db->get($this->tableName);
    $result = [];
    foreach ($sql->result() as $row) {
      $result[] = createIdentification($row->area_name);
    }
    return $result;
  }

  function makeDummyArea($appid){
    $data = [
      "appid" => $appid,
      "area_code" => "1",
      "area_name" => "Area 1",
      "area_date_create" => date("Y-m-d H:i:s"),
      "area_user_add" => "0"
    ];
    $result = $this->db->insert($this->tableName,$data);
    if($result){
      return $this->db->insert_id();
    }else{
      return false;
    }
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
