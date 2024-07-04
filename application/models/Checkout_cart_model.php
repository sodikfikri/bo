<?php
/**
 *
 */
class Checkout_cart_model extends CI_Model
{
  var $tableName= "tbcabang";
  var $tableOrder= "order";
  var $tableId  = "cabang_id";
  var $now;

  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
  }

  function insert($dataInsert,$appid=""){
    if($appid==""){
      $appid = $this->session->userdata("ses_appid");
    }


    if(!empty($appid)){
      $userID = $this->session->userdata("ses_userid");
      $dataInsert["appid"]              = $appid;
      $dataInsert["cabang_user_add"]    = $userID;
      $dataInsert["cabang_date_create"] = $this->now;

      $res = $this->db->insert($this->tableName,$dataInsert);
      if($res){
        if(!empty($dataInsert['cabang_area_id'])){
          $areaID = $dataInsert['cabang_area_id'];
          $totalCabang = $this->countActiveCabangByArea($areaID);
          $this->load->model("area_model");
          $this->area_model->setTotalCabang($totalCabang,$areaID);
        }

        return true;
      }else{
        return false;
      }

    }else {
      return false;
    }
  }
  
  function updateOrder($orderID,$dataUpdate,$appid=""){
    if($appid==""){
      $appid = $this->session->userdata("ses_appid");
    }
    
    if(!empty($appid)){
      $userID = $this->session->userdata("ses_userid");
	  $this->db->where("order_id",$orderID);
      $res = $this->db->update($this->tableOrder,$dataUpdate);
      if($res){
        return true;
      }else{
        return false;
      }
    }else {
      return false;
    }
  }
  
  function activeOrder($invoiceId,$dataUpdate,$appid=""){
    if($appid==""){
      $appid = $this->session->userdata("ses_appid");
    }
    
    if(!empty($appid)){
      $userID = $this->session->userdata("ses_userid");
	  $this->db->where("qris_invoiceid",$invoiceId);
	  $this->db->where("status","pending");
      $res = $this->db->update($this->tableOrder,$dataUpdate);
      if($res){
        return true;
      }else{
        return false;
      }
    }else {
      return false;
    }
  }
  
  function activeEmpOrder($order_id,$dataUpdate,$appid=""){
    if($appid==""){
      $appid = $this->session->userdata("ses_appid");
    }
    
    if(!empty($order_id)){
      $userID = $this->session->userdata("ses_userid");
	  $this->db->where("order_id",$order_id);
	  $this->db->where("status","paid");
      $res = $this->db->update($this->tableOrder,$dataUpdate);
      if($res){
        return true;
      }else{
        return false;
      }
    }else {
      return false;
    }
  }
  
  function getDetailOrder($invoiceId,$appid=""){
    if($appid==""){
      $appid = $this->session->userdata("ses_appid");
    }
    
    if(!empty($appid)){
	  $this->db->select([
        "A.license_count",
        "A.gtotal",
        "A.price",
        "B.user_emailaddr",
        "B.user_fullname",
        "C.cabang_name"
      ]);
	  $this->db->from("order A");
	  $this->db->join("iauser B","B.userid = A.user_add");
	  $this->db->join("tbcabang C","C.cabang_id = A.cabang_id");
      $this->db->where("A.appid",$appid);
	  $this->db->where("A.qris_invoiceid",$invoiceId);
      $sql = $this->db->get();
      if ($sql->num_rows()>0) {
        return $sql->row();
      }else{
        return false;
      }
    }else {
      return false;
    }
  }

  function update($dataUpdate,$id){
    $appid = $this->session->userdata("ses_appid");
    if(!empty($appid)){
      $userID = $this->session->userdata("ses_userid");

      $dataUpdate["cabang_user_modif"]    = $userID;
      $dataUpdate["cabang_date_modif"]    = $this->now;
      $dataUpdate["cabang_jenis_modif"]   = "edit";

      $this->db->where("appid",$appid);
      $this->db->where($this->tableId,$id);
      $res = $this->db->update($this->tableName,$dataUpdate);
      if($res){
        if(!empty($dataUpdate['cabang_area_id'])){
          $areaID = $dataUpdate['cabang_area_id'];
          $totalCabang = $this->countActiveCabangByArea($areaID);
          $this->load->model("area_model");
          $this->area_model->setTotalCabang($totalCabang,$areaID);
        }
        return true;
      }else{
        return false;
      }
    }else {
      return false;
    }
  }

  function delete($id){
    $appid = $this->session->userdata("ses_appid");
    if(!empty($appid)){
      $userID = $this->session->userdata("ses_userid");
      // ambil data cabang
      $dataCabang = $this->getById($id);
      $areaID     = $dataCabang->cabang_area_id;
      //
      $dataUpdate["cabang_user_modif"]    = $userID;
      $dataUpdate["cabang_date_modif"]    = $this->now;
      $dataUpdate["cabang_jenis_modif"]   = "delete";
      $dataUpdate["is_del"]               = "1";

      $this->db->where("appid",$appid);
      $this->db->where($this->tableId,$id);
      $res = $this->db->update($this->tableName,$dataUpdate);
      if($res){
        if(!empty($areaID)){
          $totalCabang = $this->countActiveCabangByArea($areaID);
          $this->load->model("area_model");
          $this->area_model->setTotalCabang($totalCabang,$areaID);
        }
        return true;
      }else{
        return false;
      }
      return $res;
    }else {
      return false;
    }
  }

  function getById($id,$appid=null){
    if($appid==null)
    {
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
	  $ses_id = $this->session->userdata("ses_userid");
      $this->db->select("tbcabang.*");
      $this->db->select("
      (
        1
      ) as totalDevice

      ");
      $this->db->select("
      (
        select
        count(tbemployeeareacabang.employeeareacabang_id)
        from
        tbemployeeareacabang
        where
		tbemployeeareacabang.appid = tbcabang.appid AND
        tbemployeeareacabang.employee_cabang_id = tbcabang.cabang_id
      ) as totalEmployee
      ");

      $this->db->where("tbcabang.appid",$appid);
      $this->db->where("tbcabang.cabang_user_add",$ses_id);
      $this->db->where("tbcabang.cabang_area_id","0");
      $this->db->where("tbcabang.is_del !=","1");

      $this->db->from($this->tableName);

      $sql = $this->db->get();
      return $sql->result();
    }else {
      return false;
    }
  }
  function getByAppid($appid){
    if(!empty($appid)){
      $this->db->select("*");
	  $this->db->from($this->tableName);
      $this->db->where("appid",$appid);
      $this->db->where("is_del !=","1");
      $sql = $this->db->get();
      if($sql->num_rows()>0){
        return $sql->result();
      }else{
        return false;
      }
    }else{
      return false;
    }
  }
  
  function getActiveOrder($orderid){
    if(!empty($orderid)){
      $this->db->select("*");
	  $this->db->from($this->tableOrder);
      $this->db->where("order_id",$orderid);
      $this->db->where("status","pending");
      $this->db->where("qris_invoiceid !=","");
      $sql = $this->db->get();
      return $sql->result();
    }else{
      return false;
    }
  }
  
  function getPaidOrder($orderid){
    if(!empty($orderid)){
      $this->db->select("*");
	  $this->db->from($this->tableOrder);
      $this->db->where("order_id",$orderid);
      $this->db->where("qris_invoiceid !=","");
      $sql = $this->db->get();
      if($sql->num_rows()>0){
        return $sql->result();
      }else{
        return false;
      }
    }else{
      return false;
    }
  }
  
  function getName($id){
    $this->db->select("cabang_name");
    $this->db->where("cabang_id",$id);
    $sql = $this->db->get($this->tableName);
    if($sql->num_rows()>0){
      return $sql->row()->cabang_name;
    }
  }
  /*
  untuk menghitung jumlah cabang dalam 1 area
  */
  function countActiveCabangByArea($areaID){
    $appid = $this->session->userdata("ses_appid");
    if(!empty($appid)){
      $this->db->select("count(cabang_id) as total");
      $this->db->where("appid",$appid);
      $this->db->where("is_del !=","1");
      $this->db->where("cabang_area_id",$areaID);

      $sql = $this->db->get($this->tableName);
      return $sql->row()->total;
    }else {
      return false;
    }
  }

  function setTotalDevice($totalDevice,$id){
    $appid = $this->session->userdata("ses_appid");
    if(!empty($appid)){
      $dataUpdate = [
        "cabang_total_device" => $totalDevice
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
        "cabang_total_emp" => $totalEmployee
      ];
      $this->db->where("appid",$appid);
      $this->db->where($this->tableId,$id);
      $res = $this->db->update($this->tableName,$dataUpdate);
      return $res;
    }else {
      return false;
    }
  }
  function getLocationReview($area,$cabang,$appid){
    $countActiveEmployee = "(
    select count(C.employeeareacabang_id) as totalEmployee from tbemployeeareacabang C
    where
    C.appid = '$appid'
    and
    C.employee_area_id = A.area_id
    and
    C.employee_cabang_id = B.cabang_id
    and
    C.status = 'active'
    ) as totalEmployee";
    $countActiveDevice = "(
      select count(D.device_id) as totalDevice from tbdevice D
      where
      D.appid ='$appid'
      and
      D.is_del = '0'
      and
      D.device_area_id = A.area_id
      and
      D.device_cabang_id = B.cabang_id
    ) as totalDevice";
    $this->db->select([
      "A.area_name",
      "B.cabang_name",
      $countActiveEmployee,
      $countActiveDevice
    ]);
    $this->db->from("tbarea A");
    $this->db->join("tbcabang B","B.cabang_area_id = A.area_id","left");
    $this->db->where("A.appid",$appid);
    $this->db->where("B.appid",$appid);
    $this->db->where("A.is_del","0");
    $this->db->where("B.is_del","0");
    if($area!=""){
      $this->db->where("A.area_id",$area);
    }
    if($cabang!=""){
      $this->db->where("B.cabang_id",$cabang);
    }
    $sql = $this->db->get();
    $data = $sql->result();
    return $data;
  }

  function isCodeExists($code,$cabang_id,$appid){
    $this->db->where("appid",$appid);
    $this->db->where("cabang_code",$code);
    $sql = $this->db->get($this->tableName);
    if($sql->num_rows()>0){
      $data = $sql->row();
      if($data->cabang_id==$cabang_id){
        return false;
      }else{
        return true;
      }
    }else{
      return false;
    }
  }

  function isNameExists($cabangName,$cabangid,$appid){
    $this->db->where("appid",$appid);
    $this->db->where("LCASE(cabang_name)",strtolower($cabangName));
    $this->db->where("is_del","0");
    $sql = $this->db->get($this->tableName);

    if($sql->num_rows()>0){
      $data = $sql->row();
      if($data->cabang_id == $cabangid){
        return false;
      }else{
        return true;
      }
    }else{
      return false;
    }
  }

  function getNotActiveCabang($from,$to,$appid){
    $this->db->where("cabang_jenis_modif","delete");
    $this->db->where("DATE(cabang_date_modif) >=",$from);
    $this->db->where("DATE(cabang_date_modif) <=",$to);
    $sql = $this->db->get($this->tableName);
    return $sql;
  }

  function saveIgnoreDuplicate($dataInsert,$checkerCabangCode="",$checkerCabangName=""){
    $this->db->select("cabang_id");
    $where = $dataInsert;

    if(!empty($dataInsert["cabang_code"])){
      unset($where["cabang_code"]);
      $where[" REPLACE(LOWER(cabang_code), ' ', '') ="] = createIdentification($dataInsert["cabang_code"]);
    }

    if(!empty($dataInsert["cabang_name"])){
      unset($where["cabang_name"]);
      $where[" REPLACE(LOWER(cabang_name), ' ', '') ="] = createIdentification($dataInsert["cabang_name"]);
    }

    unset($where["cabang_user_add"]);
    unset($where["cabang_date_create"]);

    $sqlCheck   = $this->db->get_where($this->tableName,$where);

    if($sqlCheck->num_rows()>0){
      $rows     = $sqlCheck->row();
      $institutionID = $rows->cabang_id;
      $insertStatus = "skipped";
    }else{

      if(!in_array(createIdentification($dataInsert['cabang_code']),$checkerCabangCode) && !in_array(createIdentification($dataInsert['cabang_name']),$checkerCabangName)){
        $insert_query = $this->db->insert_string($this->tableName, $dataInsert);
        $insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
        $this->db->query($insert_query);
        $institutionID     = $this->db->insert_id();
        $insertStatus = "inserted";
      }elseif(in_array(createIdentification($dataInsert['cabang_code']),$checkerCabangCode) && !in_array(createIdentification($dataInsert['cabang_name']),$checkerCabangName)){
        $insertStatus = "duplicated_code";
        $institutionID     = "";
      }elseif(!in_array(createIdentification($dataInsert['cabang_code']),$checkerCabangCode) && in_array(createIdentification($dataInsert['cabang_name']),$checkerCabangName)){
        $insertStatus = "duplicated_name";
        $institutionID     = "";
      }elseif(in_array(createIdentification($dataInsert['cabang_code']),$checkerCabangCode) && in_array(createIdentification($dataInsert['cabang_name']),$checkerCabangName)){
        $insertStatus = "duplicated_code_name";
        $institutionID     = "";
      }else{
        $insertStatus = "";
        $institutionID     = "";
      }
    }

    $output = array(
      "institution_id" => $institutionID,
      "insertStatus"  => $insertStatus
    );
    return $output;
  }

  function getActiveInstitutionIdentification($appid){
    $this->db->select("cabang_id");
    $this->db->select("cabang_name");
    $this->db->where("appid",$appid);
    $this->db->where("is_del","0");
    $sql = $this->db->get($this->tableName);
    $output = [];
    foreach ($sql->result() as $row) {
      $output[$row->cabang_id] = createIdentification($row->cabang_name);
    }
    return $output;
  }

  function getActiveInstitution($appid){
    $this->db->select("cabang_id");
    $this->db->select("cabang_area_id");
    $this->db->select("cabang_name");
    $this->db->where("appid",$appid);
    $sql = $this->db->get($this->tableName);
    return $sql->result_array();
  }

  function getInstitutionCode($appid){
    $this->db->select("cabang_code");
    $this->db->where("appid",$appid);
    $sql = $this->db->get($this->tableName);
    $result = [];
    foreach ($sql->result() as $row) {
      $result[] = createIdentification($row->cabang_code);
    }
    return $result;
  }

  function getInstitutionName($appid){
    $this->db->select("cabang_name");
    $this->db->where("appid",$appid);
    $this->db->where("is_del","0");
    $sql = $this->db->get($this->tableName);
    $result = [];
    foreach ($sql->result() as $row) {
      $result[] = createIdentification($row->cabang_name);
    }
    return $result;
  }
}
