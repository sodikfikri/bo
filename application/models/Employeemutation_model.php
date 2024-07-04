<?php
/**
 *
 */
class Employeemutation_model extends CI_Model
{
  var $now;
  var $tableName = "tbemployeemutation";
  var $tableId   = "employeemutation_id";

  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
  }

  function insert($dataInsert,$appid){
    $result = $this->db->insert($this->tableName,$dataInsert);
    if($result){
      return $this->db->insert_id();
    }else{
      return false;
    }
  }

  function insert_detail($dataInsert,$appid,$employeeid,$effdt){
    if(!empty($appid)){
      // open gate
      $this->load->model("firewall_model");
      if($dataInsert["child_status"]=="source"){
        $locationID = $dataInsert["employeeareacabang_id"];
        $this->db->select("tbdevice.device_id");
        $this->db->from("tbemployeeareacabang");
        $this->db->join("tbdevice","tbdevice.device_area_id = tbemployeeareacabang.employee_area_id and tbdevice.device_cabang_id = tbemployeeareacabang.employee_cabang_id");
        $this->db->where("tbemployeeareacabang.employeeareacabang_id",$locationID);
        $sql = $this->db->get();
        foreach ($sql->result() as $row) {
          $this->firewall_model->setSchedule($row->device_id,$effdt);
        }
      }elseif ($dataInsert["child_status"]=="destination") {
        $areaId   = $dataInsert["area_id"];
        $cabangId = $dataInsert["cabang_id"];
        $this->db->select("device_id");
        $this->db->where("device_area_id",$areaId);
        $this->db->where("device_cabang_id",$cabangId);
        $sql = $this->db->get("tbdevice");
        foreach ($sql->result() as $row) {
          $this->firewall_model->setSchedule($row->device_id,$effdt);
        }
      }
      // finish open gate
      $this->db->insert("tbemployeemutation_c",$dataInsert);
    }
  }

  function getDetailByLocation($locationID,$childStatus){
    $this->db->where("employeeareacabang_id",$locationID);
    $this->db->where("child_status",$childStatus);
    $sql = $this->db->get("tbemployeemutation_c");
    if($sql->num_rows()>0){
      return $sql->row();
    }else{
      return false;
    }
  }

  function getDetailMutationID($mutationID,$childStatus){
    $this->db->where("employeemutation_id",$mutationID);
    $this->db->where("child_status",$childStatus);
    $sql = $this->db->get("tbemployeemutation_c");
    if($sql->num_rows()>0){
      return $sql->result();
    }else{
      return false;
    }
  }

  function deleteDetailByMutationID($mutationID){
    $this->db->where("employeemutation_id",$mutationID);
    $res = $this->db->delete("tbemployeemutation_c");
    return $res;
  }

  function deleteByMutationID($mutationID){
    $this->db->where("employeemutation_id",$mutationID);
    $res = $this->db->delete("tbemployeemutation");
    return $res;
  }

  function getAll($area,$cabang,$status,$name,$start,$length,$appid=null){
    if($appid==null){
      $appid = $this->session->userdata("ses_appid");
    }

    if($status=="new"){
      $this->db->select("a.employeeareacabang_id");
      $this->db->from("tbemployeemutation_c a");
      $this->db->join("tbemployeeareacabang b","b.employeeareacabang_id = a.employeeareacabang_id");
      $this->db->where("b.appid",$appid);
      $this->db->where("a.child_status","source");
      $sqlCek = $this->db->get();
      $arrCek = [];
      foreach ($sqlCek->result() as $row) {
        $arrCek[] = $row->employeeareacabang_id;
      }

      $this->db->select("tbemployee.*");
      $this->db->select("tbcabang.cabang_name");
      $this->db->select("tbarea.area_name");
      $this->db->select("tbemployeeareacabang.status as location_status");
      $this->db->select("tbemployeeareacabang.employeeareacabang_id");

      $this->db->select("(
        select count(employeetemplate_id) as total from tbemployeetemplate
        where
        employeetemplate_employee_id = tbemployee.employee_id
        and
        employeetemplate_jenis = 'fingerprint'
        ) as total_fingerprint");
      $this->db->select("(
        select count(employeetemplate_id) as total from tbemployeetemplate
        where
        employeetemplate_employee_id = tbemployee.employee_id
        and
        employeetemplate_jenis = 'face'
        ) as total_face");
      $this->db->from("tbemployee");
      $this->db->join("tbemployeeareacabang","tbemployeeareacabang.employeeareacabang_employee_id = tbemployee.employee_id","left");
      $this->db->join("tbarea","tbarea.area_id = tbemployeeareacabang.employee_area_id","left");
      $this->db->join("tbcabang","tbcabang.cabang_id = tbemployeeareacabang.employee_cabang_id","left");

      $this->db->where("tbemployee.is_del !=","1");
      //$this->db->where("tbarea.is_del !=","1");
      //$this->db->where("tbcabang.is_del !=","1");

      $this->db->where("tbemployee.appid",$appid);
      $this->db->like("tbemployee.employee_full_name",$name);
      $this->db->where("resign_confirmed","no");
      $this->db->where("tbemployeeareacabang.employee_area_id",$area);
      $this->db->where("tbemployeeareacabang.employee_cabang_id",$cabang);
      if(count($arrCek)>0){
        $this->db->where_not_in("tbemployeeareacabang.employeeareacabang_id",$arrCek);
      }
      $this->db->order_by("tbemployee.employee_id","ASC");

      if($start!="" && $length!=""){
        $this->db->limit($length,$start);
      }
      $sql = $this->db->get();
    }else{
      if($start!="" && $length!=""){
        $limitQuery = "limit $start, $length ";
      }else{
        $limitQuery = "";
      }
      $sql = $this->db->query("
        select tbemployee.*,
  	           tbcabang.cabang_name,
               tbemployeeareacabang.employeeareacabang_id,
               (select count(D.employeemutation_c_id) as total
                from tbemployeemutation_c D
                where
                D.employeemutation_id = tbemployeemutation.employeemutation_id
                and
                D.child_status = 'destination'
               ) as total_destination,
               tbemployeemutation.employeemutation_id

        from tbemployeemutation
        left join tbemployeemutation_c
          on tbemployeemutation_c.employeemutation_id = tbemployeemutation.employeemutation_id
          and tbemployeemutation_c.child_status = 'source'
        inner join tbemployeeareacabang on tbemployeeareacabang.employeeareacabang_id = tbemployeemutation_c.employeeareacabang_id
        left join tbcabang on tbcabang.cabang_id = tbemployeeareacabang.employee_cabang_id
        inner join tbemployee on tbemployee.employee_id = tbemployeeareacabang.employeeareacabang_employee_id
        where
          tbemployeeareacabang.employee_area_id='$area'
        and
          tbemployeeareacabang.employee_cabang_id='$cabang'
        and
          tbemployeemutation.employeemutation_status='$status'
        and
          tbemployee.appid = '$appid'
        and
          tbemployee.resign_confirmed ='no'
        and
          tbemployee.employee_full_name like '%$name%'
        ".$limitQuery);
    }

      return $sql;
  }

  function countRecordAll($status,$appid=null){
    if($appid==null){
      $appid = $this->session->userdata("ses_appid");
    }
    if($status=="new"){
      /**$this->db->select("a.employeeareacabang_id");
      $this->db->from("tbemployeemutation_c a");
      $this->db->join("tbemployeeareacabang b","b.employeeareacabang_id = a.employeeareacabang_id");
      $this->db->where("b.appid",$appid);
      $this->db->where("a.child_status","source");
      $sqlCek = $this->db->get();
      $arrCek = [];
      foreach ($sqlCek->result() as $row) {
        $arrCek[] = $row->employeeareacabang_id;
      }

      $this->db->select("tbemployee.employee_id");

      $this->db->from("tbemployee");
      $this->db->join("tbemployeeareacabang","tbemployeeareacabang.employeeareacabang_employee_id = tbemployee.employee_id","left");
      $this->db->join("tbarea","tbarea.area_id = tbemployeeareacabang.employee_area_id","left");
      $this->db->join("tbcabang","tbcabang.cabang_id = tbemployeeareacabang.employee_cabang_id","left");

      $this->db->where("tbemployee.is_del !=","1");
      //$this->db->where("tbarea.is_del !=","1");
      //$this->db->where("tbcabang.is_del !=","1");

      $this->db->where("tbemployee.appid",$appid);
      $this->db->where("resign_confirmed","no");
      if(count($arrCek)>0){
        $this->db->where_not_in("tbemployeeareacabang.employeeareacabang_id",$arrCek);
      }
      $sql = $this->db->get();**/
	  $this->db->select("A.employee_id");
      $this->db->from("tbemployee A");
      $this->db->where("A.appid",$appid);
      $this->db->where("A.is_del","0");
      $this->db->where("A.resign_confirmed","no");
      $sql = $this->db->get();
    }else{
      $sql = $this->db->query("
        select
               tbemployeemutation.employeemutation_id

        from tbemployeemutation
        left join tbemployeemutation_c
          on tbemployeemutation_c.employeemutation_id = tbemployeemutation.employeemutation_id
          and tbemployeemutation_c.child_status = 'source'
        inner join tbemployeeareacabang on tbemployeeareacabang.employeeareacabang_id = tbemployeemutation_c.employeeareacabang_id
        left join tbcabang on tbcabang.cabang_id = tbemployeeareacabang.employee_cabang_id
        inner join tbemployee on tbemployee.employee_id = tbemployeeareacabang.employeeareacabang_employee_id
        where
          tbemployeemutation.employeemutation_status='$status'
        and
          tbemployee.appid = '$appid'
        and
          tbemployee.resign_confirmed ='no'
        ");
    }
    return $sql->num_rows();
  }

  function getMutationDestination($mutationID,$appid){
    $this->db->select([
      "B.area_name",
      "C.cabang_name"
    ]);

    $this->db->from("tbemployeemutation_c A");
    $this->db->join("tbarea B","B.area_id = A.area_id","inner");
    $this->db->join("tbcabang C","C.cabang_id = A.cabang_id");
    $this->db->join("tbemployeemutation D","D.employeemutation_id = A.employeemutation_id");

    $this->db->where("A.child_status","destination");
    $this->db->where("D.appid",$appid);
    $this->db->where("A.employeemutation_id",$mutationID);

    $sql = $this->db->get();
    return $sql->result();
  }

  function countMutationIn($date,$area,$cabang,$appid){
    $this->db->select("B.employeeareacabang_employee_id");
    $this->db->from("tbemployeemutation_c A");
    $this->db->join("tbemployeeareacabang B","B.employeeareacabang_id = A.employeeareacabang_id","inner");
    if(!empty($area)){
      $this->db->where("B.employee_area_id",$area);
    }
    if(!empty($cabang)){
      $this->db->where("B.employee_cabang_id",$cabang);
    }
    $this->db->where("DATE(B.employeeareacabang_effdt)",$date);
    $this->db->where("A.child_status","destination");
    $this->db->where("B.appid",$appid);
    $this->db->group_by("B.employeeareacabang_employee_id");
    $sql = $this->db->get();
    return $sql->num_rows();
  }

  function countMutationOut($date,$area,$cabang,$appid){
    $this->db->select("B.employeeareacabang_employee_id");
    $this->db->from("tbemployeemutation_c A");
    $this->db->join("tbemployeeareacabang B","B.employeeareacabang_id = A.employeeareacabang_id","inner");
    if(!empty($area)){
      $this->db->where("B.employee_area_id",$area);
    }
    if(!empty($cabang)){
      $this->db->where("B.employee_cabang_id",$cabang);
    }
    $this->db->where("DATE(B.employeeareacabang_datearchive)",$date);
    $this->db->where("A.child_status","source");
    $this->db->where("B.appid",$appid);

    $this->db->group_by("B.employeeareacabang_employee_id");

    $sql = $this->db->get();
    return $sql->num_rows();
  }

  public function getData($dateStart,$dateEnd,$record_start,$record_length,$appid)
  {
    $this->db->select([
      "A.*",
      "B.employee_full_name",
      "B.employee_account_no",
    ]);

    $this->db->from("tbemployeemutation A");
    $this->db->join("tbemployee B","B.employee_id = A.employeemutation_employeeid","inner");

    $this->db->where("A.appid",$appid);
    $this->db->where("DATE(A.employeemutation_effdt) >=",$dateStart);
    $this->db->where("DATE(A.employeemutation_effdt) <=",$dateEnd);

    if($record_start!="" || $record_length!=""){
      $this->db->limit($record_length,$record_start);
    }

    $sql = $this->db->get();
    return $sql;
  }

  public function countAll($datestart, $dateend, $appid)
  {
    $this->db->select("count(employeemutation_id) as total");
    $sql = $this->db->get($this->tableName);
    return $sql->row()->total;
  }

  function getMutationInToday($deviceID){
    $today = date("Y-m-d");
    $this->db->select([
      "A.employeemutation_c_id",
      "B.employeemutation_id",
      "C.*"
    ]);
    $this->db->from("tbemployeemutation_c A");
    $this->db->join("tbemployeemutation B","B.employeemutation_id = A.employeemutation_id","inner");
    $this->db->join("tbemployee C","C.employee_id = B.employeemutation_employeeid","inner");
    $this->db->join("tbdevice D","D.device_area_id = A.area_id and D.device_cabang_id = A.cabang_id","inner");
    $this->db->where("D.device_id",$deviceID);
    $this->db->where("B.employeemutation_status","pending");
    $this->db->where("DATE(B.employeemutation_effdt) <=",$today);
    $this->db->where("A.child_status","destination");
    $this->db->where("A.transaction_status","pending");

    $sql = $this->db->get();
    return $sql;
  }

  function getMutationOutToday($deviceID){
    $today = date("Y-m-d");

    $this->db->select([
      "A.employeemutation_c_id",
      "B.employeemutation_id",
      "C.*"
    ]);

    $this->db->from("tbemployeemutation_c A");
    $this->db->join("tbemployeemutation B","B.employeemutation_id = A.employeemutation_id","inner");
    $this->db->join("tbemployee C","C.employee_id = B.employeemutation_employeeid","inner");
    $this->db->join("tbemployeeareacabang E","E.employeeareacabang_id = A.employeeareacabang_id");
    $this->db->join("tbdevice D","D.device_area_id = E.employee_area_id and D.device_cabang_id = E.employee_cabang_id","inner");

    $this->db->where("D.device_id",$deviceID);
    $this->db->where("B.employeemutation_status","pending");
    $this->db->where("DATE(B.employeemutation_effdt) <=",$today);
    $this->db->where("A.child_status","source");
    $this->db->where("A.transaction_status","pending");
    $this->db->where("E.status","active");

    $sql = $this->db->get();
    return $sql;
  }

  function finishMutationInProcess($mutationCID){
    load_model(["employeeareacabang_model"]);
    $this->db->select([
      "A.*",
      "B.employeemutation_employeeid",
      "B.appid",
      "B.employeemutation_effdt",
      "B.employeemutation_useradd"
    ]);
    $this->db->from("tbemployeemutation_c A");
    $this->db->join("tbemployeemutation B","B.employeemutation_id = A.employeemutation_id");

    $this->db->where("A.employeemutation_c_id",$mutationCID);
    $sql = $this->db->get();
    if($sql->num_rows()>0){
      $data = $sql->row();
      // update status mutasi >> masih belum fix
      /*
      $this->db->where("employeemutation_id",$data->employeemutation_id);
      $this->db->update("tbemployeemutation",[
        "employeemutation_status" => "success"
      ]);
      */

      // insert ke lokasi sambil update lokasi di mutation c
      $insertLocation = [
        "employeeareacabang_employee_id" => $data->employeemutation_employeeid,
        "employee_area_id" => $data->area_id,
        "employee_cabang_id" => $data->cabang_id,
        "employeeareacabang_effdt" => $data->employeemutation_effdt,
        "employeeareacabang_user_add" => $data->employeemutation_useradd,
        "status" => "active"
      ];
      $locationID = $this->employeeareacabang_model->insert($insertLocation,$data->appid);
      if($locationID!=false){
        $this->db->where("employeemutation_c_id",$data->employeemutation_c_id);
        $this->db->update("tbemployeemutation_c",[
          "employeeareacabang_id" => $locationID,
          "transaction_status"    => "success"
        ]);
      }
    }
  }

  function finishMutationOutProcess($mutationCID,$deviceID){
    load_model(["employeeareacabang_model","employeelocationdevice_model"]);
    $this->db->select([
      "A.*",
      "B.employeemutation_employeeid",
      "B.appid",
      "B.employeemutation_effdt",
      "B.employeemutation_useradd"
    ]);
    $this->db->from("tbemployeemutation_c A");
    $this->db->join("tbemployeemutation B","B.employeemutation_id = A.employeemutation_id");

    $this->db->where("A.employeemutation_c_id",$mutationCID);
    $sql = $this->db->get();
    if($sql->num_rows()>0){
      $data = $sql->row();
      // mengarchivekan lokasi lama
      $this->employeeareacabang_model->setArchiveById($data->employeeareacabang_id);

      $locationDevice = $this->employeelocationdevice_model->getLocationDevice($data->employeeareacabang_id,$deviceID);
      if($locationDevice!=false)
      {
        $locationDeviceID = $locationDevice->employeelocationdevice_id;
        // menghapus data di tabel location device template
        $this->employeelocationdevice_model->removeDeviceTemplate($locationDeviceID);
        // menghapus data di tabel location device
        $this->employeelocationdevice_model->remove($data->employeeareacabang_id,$deviceID);
      }
      // mengupdate status sukses transaksi di tabel employee c
      $this->db->where("employeemutation_c_id",$data->employeemutation_c_id);
      $this->db->update("tbemployeemutation_c",[
        "transaction_status"    => "success"
      ]);
    }
  }

  function checkCompleteMutation($mutationID){
    // dicari yang pending
    $this->db->select("count(employeemutation_c_id) as total");
    $this->db->where("employeemutation_id",$mutationID);
    $this->db->where("transaction_status","pending");
    $sql = $this->db->get("tbemployeemutation_c");
    $row = $sql->row();
    if($row->total==0){
      return true;
    }else{
      return false;
    }
  }

  function setMutationSuccess($mutationId){
    $this->db->where("employeemutation_id",$mutationId);
    $res = $this->db->update("tbemployeemutation",[
      "employeemutation_status" => "success"
    ]);
    return $res;
  }

  function getSourceLocation($mutationID)
  {
    $this->db->select([
      "C.area_name",
      "D.cabang_name"
    ]);
    $this->db->from("tbemployeemutation_c A");
    $this->db->join("tbemployeeareacabang B","B.employeeareacabang_id = A.employeeareacabang_id","left");
    $this->db->join("tbarea C","C.area_id = B.employee_area_id","left");
    $this->db->join("tbcabang D","D.cabang_id = B.employee_cabang_id","left");

    $this->db->where("A.employeemutation_id",$mutationID);
    $this->db->where("A.child_status","source");
    $sql    = $this->db->get();
    $output = [];
    foreach ($sql->result() as $row) {
      $output[] = [
        "area"   => $row->area_name,
        "branch" => $row->cabang_name
      ];
    }
    return $output;
  }

  function destinationLocation($mutationID){
    $this->db->select([
      "B.area_name",
      "C.cabang_name"
    ]);
    $this->db->from("tbemployeemutation_c A");
    $this->db->join("tbarea B","B.area_id = A.area_id","left");
    $this->db->join("tbcabang C","C.cabang_id = A.cabang_id","left");

    $this->db->where("A.employeemutation_id",$mutationID);
    $this->db->where("A.child_status","destination");
    $sql    = $this->db->get();
    $output = [];
    foreach ($sql->result() as $row) {
      $output[] = [
        "area"   => $row->area_name,
        "branch" => $row->cabang_name
      ];
    }
    return $output;
  }

  function getPendingMutation($employeeId,$appid){
    $this->db->where("employeemutation_status","pending");
    $this->db->where("appid",$appid);
    $this->db->where("employeemutation_employeeid",$employeeId);
    $result_sql = $this->db->get($this->tableName);
    return $result_sql;
  }
}
