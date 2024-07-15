<?php
/**
 *
 */
class Employee_model extends CI_Model
{
  var $tableName= "tbemployee";
  var $tableNameAreaCabang= "tbemployeeareacabang";
  var $tableId  = "employee_id";
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
      if(!empty($this->session->userdata("ses_userid"))){
        $userID = $this->session->userdata("ses_userid");
        $dataInsert["employee_user_add"]    = $userID;
      }
      
      $dataInsert["appid"]              = $appid;
      $dataInsert["employee_date_create"] = $this->now;

      $res = $this->db->insert($this->tableName,$dataInsert);
      if($res){
        return $this->db->insert_id();
      }else{
        return false;
      }
    }else {
      return false;
    }
  }
  
  function getAppIdByEmail($email)
  {
	$this->db->select("appid");
    $this->db->where("email",$email);
    $this->db->where("intrax_license","active");
    $sql = $this->db->get($this->tableName);

    if($sql->num_rows()>0){
      return $sql->row();
    }else {
      return false;
    }
  }
  
  function update_temp($dataUpdate,$employeeID,$appid=null){
    if($appid==null){
      $appid = $this->session->userdata("ses_appid");
    }

    if(!empty($appid)){
      $userID = $this->session->userdata("ses_userid");

      $dataUpdate["employee_user_modif"] = $userID;
      $dataUpdate["employee_date_modif"] = $this->now;
      $dataUpdate["employee_jenis_modif"]= "edit";

      $this->db->where("appid",$appid);
      $this->db->where("employee_id",$employeeID);
      $res = $this->db->update('tbemployee_temp',$dataUpdate);

      if($res){
        return true;
      }else{
        return false;
      }
    }else {
      return false;
    }
  }
  function update($dataUpdate,$employeeID,$appid=null){
    if($appid==null){
      $appid = $this->session->userdata("ses_appid");
    }

    if(!empty($appid)){
      $userID = $this->session->userdata("ses_userid");

      $dataUpdate["employee_user_modif"] = $userID;
      $dataUpdate["employee_date_modif"] = $this->now;
      $dataUpdate["employee_jenis_modif"]= "edit";

      $this->db->where("appid",$appid);
      $this->db->where("employee_id",$employeeID);
      $res = $this->db->update($this->tableName,$dataUpdate);

      if($res){
        return true;
      }else{
        return false;
      }
    }else {
      return false;
    }
  }
  
  function updateEmp($dataUpdate,$orderid,$appid=null){
    if($appid==null){
      $appid = $this->session->userdata("ses_appid");
    }

    if(!empty($appid)){
      $userID = $this->session->userdata("ses_userid");
	  
      $dataUpdate["employee_user_modif"] = $userID;
      $dataUpdate["employee_date_modif"] = $this->now;
      $dataUpdate["employee_jenis_modif"]= "edit";

      $this->db->where("appid",$appid);
      $this->db->where("parent_order_id",$orderid);
      $res = $this->db->update($this->tableName,$dataUpdate);

      if($res){
        return true;
      }else{
        return false;
      }
    }else {
      return false;
    }
  }
  
  function updateProfile($employeeID,$name,$address,$phone,$birthdate,$employee_gender,$photo){
	$dataUpdate["employee_user_modif"] = $employeeID;
	$dataUpdate["employee_full_name"] = $name;
	$dataUpdate["address"] = $address;
	$dataUpdate["phone_number"] = $phone;
	$dataUpdate["birthday"] = $birthdate;
	$dataUpdate["gender"] = $employee_gender;
    $dataUpdate["employee_date_modif"] = $this->now;
    $dataUpdate["employee_jenis_modif"]= "edit";
	if($photo!=''){$dataUpdate["employee_photo"] = $photo;}

    $this->db->where("employee_id",$employeeID);
    $res = $this->db->update($this->tableName,$dataUpdate);

    if($res){
		return true;
    }else{
		return false;
    }  
  }
  
  function updateMethodEmployee($dataUpdateEmp,$id){
    $appid = $this->session->userdata("ses_appid");
    if(!empty($appid)){
      $userID = $this->session->userdata("ses_userid");
		
      $this->db->join('tbemployeeareacabang','tbemployee.employee_id = tbemployeeareacabang.employeeareacabang_employee_id',"inner");
      $this->db->where("tbemployee.appid",$appid);
      $this->db->where("tbemployeeareacabang.cabang_area_id",$id);
      $res = $this->db->update($this->tableName,$dataUpdateEmp);
      if($res){
        return true;
      }else{
        return false;
      }
    }else {
      return false;
    }
  }
	
  function getAllEmpCabang($appid=null,$cabang_id){
    if ($appid==null) {
      $appid = $this->session->userdata("ses_appid");
    }

    if(!empty($appid)){
      $this->db->select("tbemployee.employee_full_name");
	  $this->db->select("
      (
        select
        count(tbemployeeareacabang.employeeareacabang_employee_id)
        from
        tbemployeeareacabang
        where
		tbemployeeareacabang.appid = tbemployee.appid AND
        tbemployeeareacabang.employeeareacabang_employee_id = tbemployee.employee_id

      ) as totalBranch
      ");

      $this->db->from("tbemployee");
      $this->db->join("tbemployeeareacabang","tbemployeeareacabang.employeeareacabang_employee_id = tbemployee.employee_id","left");

      $this->db->where("tbemployee.is_del !=","1");
      $this->db->where("tbemployee.employee_jenis_modif !=","delete");
      $this->db->where("tbemployee.status_added","active");

      $this->db->where("tbemployee.appid",$appid);
      $this->db->where("tbemployeeareacabang.employee_cabang_id",$cabang_id);

      $this->db->where("resign_confirmed","no");
      //$this->db->limit(1);
      $this->db->order_by("tbemployee.employee_id","ASC");
      $sql = $this->db->get();

      return $sql->result();
    }else {
      return false;
    }
  }
  
  function getAllEmpReqCabang($appid=null,$cabang_id){
    if ($appid==null) {
      $appid = $this->session->userdata("ses_appid");
    }

    if(!empty($appid)){
      $this->db->select("tbemployee.employee_full_name");

      $this->db->from("tbemployee");
      $this->db->join("tbemployeeareacabang","tbemployeeareacabang.employeeareacabang_employee_id = tbemployee.employee_id","left");

      $this->db->where("tbemployee.is_del !=","1");
      $this->db->where("tbemployee.employee_jenis_modif !=","delete");

      $this->db->where("tbemployee.appid",$appid);
      $this->db->where("tbemployeeareacabang.employee_cabang_id",$cabang_id);

      $this->db->where("resign_confirmed","no");
      //$this->db->limit(1);
      $this->db->order_by("tbemployee.employee_id","ASC");
      $sql = $this->db->get();

      return $sql->result();
    }else {
      return false;
    }
  }
  
  function getDetailEmp($appid=null,$order_id){
    if ($appid==null) {
      $appid = $this->session->userdata("ses_appid");
    }

    if(!empty($appid)){
      $this->db->select("tbemployee.employee_full_name");
      $this->db->from("tbemployee");
      $this->db->where("tbemployee.is_del !=","1");
      $this->db->where("tbemployee.employee_jenis_modif !=","delete");
      $this->db->where("tbemployee.appid",$appid);
      $this->db->where("tbemployee.parent_order_id",$order_id);
      $this->db->where("resign_confirmed","no");
      $this->db->order_by("tbemployee.employee_id","ASC");
      $sql = $this->db->get();
      return $sql->result();
    }else {
      return false;
    }
  }
  
  function getAll($appid=null){
    if ($appid==null) {
      $appid = $this->session->userdata("ses_appid");
    }

    if(!empty($appid)){
      $this->db->select("tbemployee.*");
      $this->db->select("tbcabang.cabang_name");
      $this->db->select("tbarea.area_name");
      $this->db->select("iasubscription.intrax_plan_code");
      $this->db->select("tbemployeeareacabang.status as location_status");
      $this->db->select("tbemployeeareacabang.employeeareacabang_id");

      $this->db->select("1 as total_fingerprint");
      $this->db->select("1 as total_face");
      $this->db->from("tbemployee");
	  $this->db->join("iasubscription","iasubscription.appid = tbemployee.appid");
      $this->db->join("tbemployeeareacabang","tbemployeeareacabang.employeeareacabang_employee_id = tbemployee.employee_id","left");
      $this->db->join("tbarea","tbarea.area_id = tbemployeeareacabang.employee_area_id","left");
      $this->db->join("tbcabang","tbcabang.cabang_id = tbemployeeareacabang.employee_cabang_id","left");

      $this->db->where("tbemployee.is_del !=","1");
      $this->db->where("tbemployee.employee_jenis_modif !=","delete");
	  $this->db->where("tbemployee.status_added","active");

      //$this->db->where("tbarea.is_del !=","1");
      //$this->db->where("tbcabang.is_del !=","1");

      $this->db->where("tbemployee.appid",$appid);

      $this->db->where("resign_confirmed","no");
      $this->db->order_by("tbemployee.employee_id","ASC");

      //$this->db->group_by("tbemployee.employee_id");
      //$this->db->where("tbarea.appid",$appid);
      //$this->db->where("tbcabang.appid",$appid);
      //$this->db->where("tbemployeeareacabang.appid",$appid);
      $sql = $this->db->get();

      return $sql->result();
    }else {
      return false;
    }
  }

  /*
  hanya mereturn karyawan dengan status yang tidak resign (pending, active)
  */
  function getAllEmployeeCode($appid,$employeeCodes=null){
    $this->db->select("employee_account_no");
    $this->db->select("employee_id");
    $this->db->from("tbemployee");
    $this->db->where("tbemployee.is_del !=","1");

    $this->db->group_start();
    $this->db->where('employee_resign_date IS NULL', null, false);
    $this->db->or_where('employee_resign_date >', $this->now);
    $this->db->group_end();

    $this->db->where("tbemployee.appid",$appid);
    if($employeeCodes!=null){
      $this->db->where_in("employee_account_no",$employeeCodes);
    }
    $sql = $this->db->get();
    if($sql->num_rows()>0){
      $output = [];
      foreach ($sql->result_array() as $row) {
        $output[$row["employee_account_no"]] =  $row["employee_id"];
      }
      return $output;
    }else{
      return false;
    }
  }
  function getAllEmployeeCodeAdvance($appid,$employeeCodes=null){
    $this->db->select("employee_account_no");
    $this->db->select("employee_id");
    $this->db->from("tbemployee");
    $this->db->where("tbemployee.is_del !=","1");

    $this->db->group_start();
    $this->db->where('employee_resign_date IS NULL', null, false);
    $this->db->or_where('employee_resign_date >', $this->now);
    $this->db->group_end();

    $this->db->where("tbemployee.appid",$appid);
    if($employeeCodes!=null){
      $this->db->where_in("employee_account_no",$employeeCodes);
    }
    $sql = $this->db->get();
    $output = array();
    if($sql->num_rows()>0){
      foreach ($sql->result() as $index => $row) {
        $output[$index] =  [
          "id" => $row->employee_id,
          "pin"=> $row->employee_account_no
        ];
      }
      return $output;
    }else{
      return false;
    }
  }

  function countAll(){
    $appid = $this->session->userdata("ses_appid");
    
    if(!empty($appid)){
      $this->db->select("count(tbemployee.employee_id) as total");

      $this->db->from("tbemployee");

      $this->db->where("tbemployee.is_del !=","1");
      $this->db->where("tbemployee.status_added","active");

      $this->db->where("tbemployee.appid",$appid);
      $sql = $this->db->get();

      if($sql->num_rows()>0){
        return $sql->row()->total;
      }else{
        return 0;
      }

    }else {
      return 0;
    }
  }

  function getById($id,$appid=null){
    if($appid==null){
      $appid = $this->session->userdata("ses_appid");
    }

    if(!empty($appid)){
      $this->db->select("tbemployee.*");
      $this->db->select("iasubscription.intrax_plan_code");
      $this->db->from("tbemployee");
	  $this->db->join("iasubscription","tbemployee.appid = iasubscription.appid");
      $this->db->where("tbemployee.appid",$appid);
      $this->db->where("tbemployee.is_del !=","1");
      $this->db->where("tbemployee.employee_id",$id);
      $sql = $this->db->get();
      if($sql->num_rows()>0){
        return $sql->row();
      }else{
        return false;
      }
    }else{
      return false;
    }
  }
  
  function getByEmail($email,$appid){
    if(!empty($email)){
      $this->db->select("tbemployee.*");
      $this->db->select("iasubscription.intrax_plan_code");
      $this->db->from("tbemployee");
	  $this->db->join("iasubscription","tbemployee.appid = iasubscription.appid");
      $this->db->where("tbemployee.is_del !=","1");
      $this->db->where("tbemployee.email",$email);
      $this->db->where("tbemployee.appid",$appid);
      $sql = $this->db->get();
      if($sql->num_rows()>0){
        return $sql->row();
      }else{
        return false;
      }
    }else{
      return false;
    }
  }
  
  function getByEmailOnly($email){
    if(!empty($email)){
      $this->db->select("tbemployee.*");
      $this->db->from("tbemployee");
      $this->db->where("tbemployee.is_del !=","1");
      $this->db->where("tbemployee.email",$email);
      $sql = $this->db->get();
      if($sql->num_rows()>0){
        return $sql->row();
      }else{
        return false;
      }
    }else{
      return false;
    }
  }
  
  function delete($employeeID){
    $appid = $this->session->userdata("ses_appid");
    if(!empty($appid)){
      $userID = $this->session->userdata("ses_userid");
      $dataUpdate = [
        "employee_user_modif" => $userID,
        "employee_date_modif" => $this->now,
        "employee_jenis_modif"=> "delete",
        "is_del"              => "1"
      ];
      $this->db->where("appid",$appid);
      $this->db->where("employee_id",$employeeID);
      $res = $this->db->update($this->tableName,$dataUpdate);
      return $res;
    }else{
      return false;
    }
  }

  function renewEmployeeLicense($activeAddons){
    $employeeLicense = !empty($activeAddons['employeelicense']) ? $activeAddons['employeelicense'] : 0;

    $totalEmployee = $this->countAll();
    if($totalEmployee<=$employeeLicense){
      // jika employee terpasang masih kurang dari lisense
      // tidak ada action
    }else{
      // jika melebihi lisensi hitung berapa yang harus dinonaktifkan
      $mustInactiveEmployee = $totalEmployee - $employeeLicense;
      $appid = $this->session->userdata("ses_appid");
      if(!empty($appid)){
        $this->db->select("tbemployee.employee_id");
        $this->db->limit($mustInactiveEmployee);
        $sql = $this->getAll();
        foreach ($sql as $row) {
          $this->changeLicenseTo("notactive",$row->employee_id);
        }
      }
    }
  }
  
  // non active license employee jika subscription_id expired
  function nonactiveLicenseExpired($sessSubscription){
	$this->db->select("tbemployee.employee_id");
	$this->db->where_not_in("subscription_id",$sessSubscription);
	$sql = $this->getAll();
	foreach ($sql as $row) {
	  $this->changeActiveIntrax("notactive",$row->employee_id);
	}
  }

  function changeActiveIntrax($changeTo,$employeeID){
    $appid = $this->session->userdata("ses_appid");
    if(!empty($appid)){
      $dataUpdate = [
        "intrax_license" => $changeTo,
        "subscription_id" => NULL
      ];
      $this->db->where("appid",$appid);
      $this->db->where("employee_id",$employeeID);
      $res = $this->db->update($this->tableName,$dataUpdate);
      return $res;
    }else{
      return false;
    }
  }
  
  function changeLicenseTo($changeTo,$employeeID){
    $appid = $this->session->userdata("ses_appid");
    if(!empty($appid)){
      $dataUpdate = [
        "employee_license" => $changeTo
      ];
      $this->db->where("appid",$appid);
      $this->db->where("employee_id",$employeeID);
      $res = $this->db->update($this->tableName,$dataUpdate);
      return $res;
    }else{
      return false;
    }
  }

  function getLicenseUsed(){
    $appid = $this->session->userdata("ses_appid");
    if(!empty($appid)){
      $this->db->select("count(employee_id) as total");
      $this->db->where("appid",$appid);
      $this->db->where("is_del !=","1");
      $this->db->where("employee_license","active");
      $res = $this->db->get($this->tableName);
      if($res->num_rows()>0){
        return $res->row()->total;
      }else{
        return 0;
      }
    }else{
      return false;
    }
  }

  function activateEmployee($employee_id){
    $dataUpdate = [
      "employee_is_active" => "1"
    ];

    $this->db->where($this->tableId,$employee_id);
    //$this->db->where("employee_is_active","0");
    $res = $this->db->update($this->tableName,$dataUpdate);
    return $res;
  }

  function notactiveEmployee($employee_id){
    $dataUpdate = [
      "employee_is_active" => "0"
    ];

    $this->db->where($this->tableId,$employee_id);
    //$this->db->where("employee_is_active","0");
    $res = $this->db->update($this->tableName,$dataUpdate);
    return $res;
  }
  function confirmResign($employee_id){
    $dataUpdate = [
      "employee_is_active" => "0",
      "resign_confirmed" => "yes"
    ];

    $this->db->where($this->tableId,$employee_id);
    //$this->db->where("employee_is_active","0");
    $res = $this->db->update($this->tableName,$dataUpdate);
    return $res;
  }
  function confirmResignAll($arrEmployeeID){
    $dataUpdate = [
      "employee_is_active" => "0",
      "resign_confirmed" => "yes"
    ];

	$this->db->where_in('employee_id', $arrEmployeeID);
    //$this->db->where("employee_is_active","0");
    $res = $this->db->update($this->tableName,$dataUpdate);
    return $res;
  }
  function setResign($employeeId,$dateresign){
    $appid = $this->session->userdata("ses_appid");
    if(!empty($appid)){

      $dataUpdate = [
        "employee_resign_date" => $dateresign
      ];


      $this->db->where("appid",$appid);
      $this->db->where($this->tableId,$employeeId);
      $res = $this->db->update($this->tableName,$dataUpdate);


      return $res;
    }
  }
  // function untuk delete employee di device
  function setResignAll($arrEmployeeID){
    $dateresign = date("Y-m-d 00:00:00");
    $dataUpdate = [
        "employee_resign_date" => $dateresign
      ];


	  $this->db->where_in('employee_id', $arrEmployeeID);
      $res = $this->db->update($this->tableName,$dataUpdate);


      return $res;
  }
  // function untuk set employee aktif kembali atau push ke device
  function setActiveAll($arrEmployeeID){
    $dateresign = date("Y-m-d 00:00:00");
    $dataUpdate = [
        "employee_resign_date" => NULL,
        "resign_confirmed" => 'no',
        "employee_is_active" => 1
      ];


	  $this->db->where_in('employee_id', $arrEmployeeID);
      $res = $this->db->update($this->tableName,$dataUpdate);


      return $res;
  }
  // hanya mengecek karyawan yang tidak dalam status resign

  function checkAccountNoExist($noAccount,$employeeID=""){
    $appid = $this->session->userdata("ses_appid");
    if(!empty($appid)){
      $this->db->where("appid",$appid);
      //$this->db->where("is_del !=","1");
      $this->db->where("employee_account_no",$noAccount);
      //$this->db->group_start();
      //$this->db->where('employee_resign_date IS NULL', null, false);
      //$this->db->or_where('employee_resign_date >', $this->now);
      //$this->db->group_end();
      $res = $this->db->get($this->tableName);
      
      if($res->num_rows()>0){
        $data = $res->row();
        if($data->employee_id==$employeeID){
          return false;
        }else{
          return true;
        }
      }else{
        return false;
      }
    }
  }
  
  function resetPasswordPin($employee_id){
    $this->db->where("employee_id",$employee_id);
    $res = $this->db->get($this->tableName);
      
    if($res->num_rows()>0){
		return $res->row();
    }else{
		return false;
    }
  }
  
  function changePasswordPin($email,$newpin){
	$dataUpdate["intrax_pin"]= $newpin;
    $this->db->where("email",$email);
    $res = $this->db->update($this->tableName,$dataUpdate);
    if($res){
        return true;
    }else{
		return false;
    }
  }
  
  function checkEmailNoExist($email,$employeeID=""){
    $appid = $this->session->userdata("ses_appid");
    if(!empty($appid)){
      $this->db->where("appid",$appid);
      //$this->db->where("is_del !=","1");
      $this->db->where("email",$email);
      //$this->db->group_start();
      //$this->db->where('employee_resign_date IS NULL', null, false);
      //$this->db->or_where('employee_resign_date >', $this->now);
      //$this->db->group_end();
      $res = $this->db->get($this->tableName);
      
      if($res->num_rows()>0){
        $data = $res->row();
        if($data->employee_id==$employeeID){
          return false;
        }else{
          return true;
        }
      }else{
        return false;
      }
    }
  }
  
  function checkEmailExist($email,$appid){
    if(!empty($appid)){
      $this->db->where("appid",$appid);
      $this->db->where("email",$email);
      $res = $this->db->get($this->tableName);
      
      if($res->num_rows()>0){
        return true;
      }else{
        return false;
      }
    }
  }

  function update_batch($dataUpdate,$primary){
    $res = $this->db->update_batch($this->tableName,$dataUpdate,$primary);
    return $res;
  }

  function countResign($date,$area,$cabang,$appid){
    $this->db->select("A.employee_id");
    $this->db->from("tbemployee A");
    $this->db->join("tbemployeeareacabang B","B.employeeareacabang_employee_id = A.employee_id","inner");

    $this->db->where(" DATE(employee_resign_date) ",$date);
    if($area!=""){
      $this->db->where("B.employee_area_id",$area);
    }
    if($cabang!=""){
      $this->db->where("B.employee_cabang_id",$cabang);
    }
    $this->db->where("A.appid",$appid);
    $this->db->group_by("A.employee_id");
    $sql = $this->db->get();
    return $sql->num_rows();
  }

  function countActive($appid)
  {
    $this->db->select("count(A.employee_id) as total");
    $this->db->from("tbemployee A");
    $this->db->where("A.appid",$appid);
    $this->db->where("A.is_del","0");
    $this->db->where("A.resign_confirmed","no");
    $sql = $this->db->get();
    return $sql->row()->total;
  }

  function getActive($areaid,$cabangid,$record_start,$record_length,$appid)
  {
    $this->db->select([
      "A.employee_id",
      "A.employee_account_no",
      "A.employee_full_name",
      "A.employee_join_date",
      "A.employee_last_mutasi",
      "A.employee_license",
      "A.employee_password",
      "A.employee_card",
      "A.employee_level",
      "A.image",
      "A.employee_is_active",
      "A.employee_resign_date"
    ]);

    $this->db->from("tbemployee A");
    $this->db->join("tbemployeeareacabang B","B.employeeareacabang_employee_id = A.employee_id","left");

    if($record_length!="" && $record_start!=""){
      $this->db->limit($record_length,$record_start);
    }

    if($areaid!=""){
      $this->db->where("B.employee_area_id",$areaid);
    }

    if($cabangid!=""){
      $this->db->where("B.employee_cabang_id",$cabangid);
    }

    $this->db->where("A.appid",$appid);
    $this->db->where("A.is_del","0");
    $this->db->where("A.resign_confirmed","no");
    $this->db->where("B.status","active");

    $this->db->group_by("A.employee_id");
    $sql = $this->db->get();
    return $sql;
  }

  function getAvailable($areaid,$cabangid,$record_start,$record_length,$appid)
  {
    $this->db->select([
      "A.employee_id",
      "A.employee_account_no",
      "A.employee_full_name",
      "A.employee_join_date",
      "A.employee_last_mutasi",
      "A.employee_license",
      "A.employee_password",
      "A.employee_card",
      "A.employee_level",
      "A.image",
      "A.employee_is_active",
      "A.employee_resign_date"
    ]);

    $this->db->from("tbemployee A");
    $this->db->join("tbemployeeareacabang B","B.employeeareacabang_employee_id = A.employee_id","left");

    if($record_length!="" && $record_start!=""){
      $this->db->limit($record_length,$record_start);
    }

    if($areaid!=""){
      $this->db->where("B.employee_area_id",$areaid);
    }

    if($cabangid!=""){
      $this->db->where("B.employee_cabang_id",$cabangid);
    }

    $this->db->where("A.appid",$appid);
    $this->db->where("A.is_del","0");
    $this->db->where("A.resign_confirmed","no");

    $this->db->group_start();
    $this->db->where("B.status","pending");
    $this->db->or_where("B.status","active");
    $this->db->group_end();

    $this->db->group_by("A.employee_id");
    $sql = $this->db->get();
    return $sql;
  }
  
  function getAvailableAdminArea($lsArea,$areaid,$cabangid,$record_start,$record_length,$appid)
  {
    $this->db->select([
      "A.employee_id",
      "A.employee_account_no",
      "A.employee_full_name",
      "A.employee_join_date",
      "A.employee_last_mutasi",
      "A.employee_license",
      "A.employee_password",
      "A.employee_card",
      "A.employee_level",
      "A.image",
      "A.employee_is_active",
      "A.employee_resign_date"
    ]);

    $this->db->from("tbemployee A");
    $this->db->join("tbemployeeareacabang B","B.employeeareacabang_employee_id = A.employee_id","left");

    if($record_length!="" && $record_start!=""){
      $this->db->limit($record_length,$record_start);
    }

    if($areaid!=""){
      $this->db->where("B.employee_area_id",$areaid);
    }
	
	//if($lsArea!=""){
      $this->db->where("B.employee_area_id IN (".$lsArea.')');
    //}


    if($cabangid!=""){
      $this->db->where("B.employee_cabang_id",$cabangid);
    }

    $this->db->where("A.appid",$appid);
    $this->db->where("A.is_del","0");
    $this->db->where("A.resign_confirmed","no");

    $this->db->group_start();
    $this->db->where("B.status","pending");
    $this->db->or_where("B.status","active");
    $this->db->group_end();

    $this->db->group_by("A.employee_id");
    $sql = $this->db->get();
    return $sql;
  }

  function countActiveFiltered($areaid,$cabangid,$appid)
  {
    $this->db->select("count(A.employee_id) as total");

    $this->db->from("tbemployee A");
    $this->db->join("tbemployeeareacabang B","B.employeeareacabang_employee_id = A.employee_id","left");

    if($areaid!=""){
      $this->db->where("B.employee_area_id",$areaid);
    }

    if($cabangid!=""){
      $this->db->where("B.employee_cabang_id",$cabangid);
    }

    $this->db->where("A.appid",$appid);
    $this->db->where("A.is_del","0");
    $this->db->where("A.resign_confirmed","no");
    $this->db->where("B.status","active");
    $this->db->group_by("A.employee_id");
    $sql = $this->db->get();
    return $sql->num_rows();
  }

  function countAvailableFiltered($areaid,$cabangid,$appid)
  {
    $this->db->select("A.employee_id");

    $this->db->from("tbemployee A");
    $this->db->join("tbemployeeareacabang B","B.employeeareacabang_employee_id = A.employee_id","left");

    if($areaid!=""){
      $this->db->where("B.employee_area_id",$areaid);
    }

    if($cabangid!=""){
      $this->db->where("B.employee_cabang_id",$cabangid);
    }

    $this->db->where("A.appid",$appid);
    $this->db->where("A.is_del","0");
    $this->db->where("A.resign_confirmed","no");

    $this->db->group_start();
    $this->db->where("B.status","active");
    $this->db->or_where("B.status","pending");
    $this->db->group_end();

    $this->db->group_by("A.employee_id");
    $sql = $this->db->get();
    return $sql->num_rows();
  }
  
  function countAvailableFilteredAdminarea($lsArea,$areaid,$cabangid,$appid)
  {
    $this->db->select("A.employee_id");

    $this->db->from("tbemployee A");
    $this->db->join("tbemployeeareacabang B","B.employeeareacabang_employee_id = A.employee_id","left");

    if($areaid!=""){
      $this->db->where("B.employee_area_id",$areaid);
    }
	
	//if($lsArea!=""){
      $this->db->where("B.employee_area_id IN (".$lsArea.')');
    //}

    if($cabangid!=""){
      $this->db->where("B.employee_cabang_id",$cabangid);
    }

    $this->db->where("A.appid",$appid);
    $this->db->where("A.is_del","0");
    $this->db->where("A.resign_confirmed","no");

    $this->db->group_start();
    $this->db->where("B.status","active");
    $this->db->or_where("B.status","pending");
    $this->db->group_end();

    $this->db->group_by("A.employee_id");
    $sql = $this->db->get();
    return $sql->num_rows();
  }

  function countAvailableAll($appid)
  {
    $this->db->select("A.employee_id");

    $this->db->from("tbemployee A");
    $this->db->join("tbemployeeareacabang B","B.employeeareacabang_employee_id = A.employee_id","left");

    $this->db->where("A.appid",$appid);
    $this->db->where("A.is_del","0");
    $this->db->where("A.resign_confirmed","no");

    $this->db->group_start();
    $this->db->where("B.status","active");
    $this->db->or_where("B.status","pending");
    $this->db->group_end();

    $this->db->group_by("A.employee_id");
    $sql = $this->db->get();
    return $sql->num_rows();
  }
  
  function countAvailableAllAdminArea($lsArea,$appid)
  {
    $this->db->select("A.employee_id");

    $this->db->from("tbemployee A");
    $this->db->join("tbemployeeareacabang B","B.employeeareacabang_employee_id = A.employee_id","left");

    $this->db->where("A.appid",$appid);
    $this->db->where("A.is_del","0");
    $this->db->where("A.resign_confirmed","no");
	
	//if($lsArea!=""){
      $this->db->where("B.employee_area_id IN (".$lsArea.')');
    //}

    $this->db->group_start();
    $this->db->where("B.status","active");
    $this->db->or_where("B.status","pending");
    $this->db->group_end();

    $this->db->group_by("A.employee_id");
    $sql = $this->db->get();
    return $sql->num_rows();
  }

  function getSpecifiedDetailEmployee($employeeId,$arrDataNeeded,$appid){
    $this->db->select($arrDataNeeded);
    $this->db->where("employee_id",$employeeId);
    $this->db->where("appid",$appid);
    $sql = $this->db->get($this->tableName);

    if($sql->num_rows()>0){
      return $sql->row();
    }else{
      return false;
    }
  }
  
  function getEmployeeById($employeeId){
	$this->db->select("*");
    $this->db->where("employee_id",$employeeId);
    $sql = $this->db->get($this->tableName);

    if($sql->num_rows()>0){
      return $sql->row();
    }else{
      return false;
    }
  }

  function saveIgnoreDuplicate($dataInsert,$checker=""){
    $this->db->select("employee_id");
    $where = $dataInsert;

    if(!empty($dataInsert["employee_account_no"])){
      unset($where["employee_account_no"]);
      $where[" REPLACE(LOWER(employee_account_no), ' ', '') ="] = createIdentification($dataInsert["employee_account_no"]);
    }

    unset($where["employee_user_add"]);
    unset($where["employee_date_create"]);

    $sqlCheck = $this->db->get_where($this->tableName,$where);
    if($sqlCheck->num_rows()>0){
      $rows = $sqlCheck->row();
      $employeeID   = $rows->employee_id;
      $insertStatus = "skipped";
    }else{
      if(!in_array(createIdentification($dataInsert['employee_account_no']),$checker)){
        $insert_query = $this->db->insert_string($this->tableName, $dataInsert);
        $insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
        $this->db->query($insert_query);
        $employeeID   = $this->db->insert_id();
        $insertStatus = "inserted";
      }else{
        $where = [
          "employee_account_no" => $dataInsert["employee_account_no"],
          "appid" => $dataInsert["appid"]
        ];
        $this->db->select("employee_id");
        $this->db->where($where);
        $sql = $this->db->get($this->tableName);
        $updatedData = $sql->row();
        $this->db->where($where);
        $this->db->update($this->tableName,$dataInsert);
        $insertStatus = "updated";
        $employeeID   = !empty($updatedData->employee_id) ? $updatedData->employee_id : 0;
      }
    }

    $output = [
      "employee_id" => $employeeID,
      "insertStatus"  => $insertStatus
    ];
    return $output;
  }

  function saveIgnoreDuplicate_temp($dataInsert,$checker=""){
    $this->db->select("employee_id");
    $where = $dataInsert;

    if(!empty($dataInsert["employee_account_no"])){
      unset($where["employee_account_no"]);
      $where[" REPLACE(LOWER(employee_account_no), ' ', '') ="] = createIdentification($dataInsert["employee_account_no"]);
    }

    unset($where["employee_user_add"]);
    unset($where["employee_date_create"]);

    $sqlCheck = $this->db->get_where('tbemployee_temp',$where);
    if($sqlCheck->num_rows()>0){
      $rows = $sqlCheck->row();
      $employeeID   = $rows->employee_id;
      $insertStatus = "skipped";
    }else{
      // if(!in_array(createIdentification($dataInsert['employee_account_no']),$checker)){
        $insert_query = $this->db->insert_string('tbemployee_temp', $dataInsert);
        $insert_query = str_replace('INSERT INTO','INSERT IGNORE INTO',$insert_query);
        $this->db->query($insert_query);
        $employeeID   = $this->db->insert_id();
        $insertStatus = "inserted";
      // }else{
      //   $where = [
      //     "employee_account_no" => $dataInsert["employee_account_no"],
      //     "appid" => $dataInsert["appid"]
      //   ];
      //   $this->db->select("employee_id");
      //   $this->db->where($where);
      //   $sql = $this->db->get('tbemployee_temp');
      //   $updatedData = $sql->row();
      //   $this->db->where($where);
      //   $this->db->update('tbemployee_temp',$dataInsert);
      //   $insertStatus = "updated";
      //   $employeeID   = !empty($updatedData->employee_id) ? $updatedData->employee_id : 0;
      // }
    }

    $output = [
      "employee_id" => $employeeID,
      "insertStatus"  => $insertStatus
    ];
    return $output;
  }

  function getEmployeeCode($appid){
    $this->db->select("employee_account_no");
    $this->db->where("appid",$appid);
    $sql = $this->db->get($this->tableName);
    $result = [];
    foreach ($sql->result() as $row) {
      $result[] = createIdentification($row->employee_account_no);
    }
    return $result;
  }
  function getEmployeeCode_temp($appid){
    $this->db->select("employee_account_no");
    $this->db->where("appid",$appid);
    $sql = $this->db->get('tbemployee_temp');
    $result = [];
    foreach ($sql->result() as $row) {
      $result[] = createIdentification($row->employee_account_no);
    }
    return $result;
  }
  
  function getSubscription($appid){
    $this->db->select("intrax_plan_code");
    $this->db->from("iasubscription");
    $this->db->where("appid",$appid);
    $sql = $this->db->get();
    $result = [];
    foreach ($sql->result() as $row) {
      $result[] = createIdentification($row->intrax_plan_code);
    }
    return $result;
  }
  
  function getEmailAvailable($appid,$email){
    $this->db->select("email");
    $this->db->where("appid",$appid);
    $this->db->where("email",$email);
    $res = $this->db->get($this->tableName);
    return $res->num_rows();
  }

  function getEmailAvailable_temp($appid,$email){
    $this->db->select("email");
    $this->db->where("appid",$appid);
    $this->db->where("email",$email);
    $res = $this->db->get('tbemployee_temp');
    return $res->num_rows();
  }

  function countResignEmployee($appid){

    $this->db->select("employee_id");
    $this->db->where("appid",$appid);
    $this->db->where("resign_confirmed","yes");
    $res = $this->db->get($this->tableName);
    return $res->num_rows();
  }

  function getResignEmployee($appid){
    $this->db->where("resign_confirmed","yes");
    $this->db->where("appid",$appid);
    $sql = $this->db->get($this->tableName);
    return $sql;
  }

  function isAccountExist($accountNo,$appid){
    $this->db->where("appid",$appid);
    $this->db->where("employee_account_no",$accountNo);
    $sql = $this->db->get($this->tableName);
    if($sql->num_rows()>0){
      return $sql->row();
    }else{
      return false;
    }
  }

  function getGroupEmployee($arrIdEmployee){
    if(count($arrIdEmployee)){
      $this->db->where_in("employee_id",$arrIdEmployee);
    }
    
    $sql = $this->db->get("tbemployee");
    return $sql;
  }

  function getActiveIntrax($ppid){
    $this->db->where("appid",$ppid);
    $this->db->where("intrax_license","active");
    $this->db->where("is_del","0");
    $this->db->where("employee_is_active",'1');
    $this->db->where("employee_resign_date is null",null,false);
    $sql = $this->db->get($this->tableName);
    if($sql->num_rows()!=0){
      return $sql;
    }else{
      return false;
    }
  }

  function getEmployeeIntrax($companyId,$checklogID,$employeeID){
    $this->db->select("tbemployee.*");
    $this->db->from("tbemployee");
    $this->db->join("iasubscription","iasubscription.appid = tbemployee.appid");
    $this->db->where("iasubscription.intrax_company_id",$companyId);
    $this->db->where("tbemployee.employee_id",$employeeID);
    $this->db->where("tbemployee.employee_account_no",$checklogID);
    $this->db->where("tbemployee.employee_license","active");
    $sql = $this->db->get();
    if($sql->num_rows()>0){
      return $sql->row();
    }else{
      return false;
    }
  }
  
  function getEmployeeByEmail($email,$appid){
    $this->db->select("*");
    $this->db->from("tbemployee");
    $this->db->where("email",$email);
    $this->db->where("appid",$appid);
    $sql = $this->db->get();
    if($sql->num_rows()>0){
      return $sql->row();
    }else{
      return false;
    }
  }

  function getIntraxActiveEmployeeID($appid,$subscription_id){
    $this->db->select("employee_id");
	$this->db->group_start();
    $this->db->where("subscription_id",$subscription_id);
    $this->db->or_where("subscription_id",NULL);
	$this->db->group_end();
    $this->db->where("appid",$appid);
    $this->db->where("intrax_license","active");
	$this->db->where("resign_confirmed","no");
    $sql = $this->db->get($this->tableName);
    $result = []; 
    foreach($sql->result_array() as $row){
      $result[] = $row["employee_id"];
    }
    return $result;
  }

  function getEmployeeByArray($appid,$arrEmployeId){
    $this->db->where("appid",$appid);
    $this->db->where_in("employee_id",$arrEmployeId);
    $sql = $this->db->get($this->tableName);
    return $sql;
  }
  function getCountData($appid){
    $this->db->select("employee_id");
    $this->db->from("tbemployee");
    $this->db->where("tbemployee.is_del !=","1");
    $this->db->where("tbemployee.employee_jenis_modif !=","delete");
    $this->db->where("tbemployee.appid",$appid);
    $this->db->where("tbemployee.resign_confirmed","no");

    $sql = $this->db->get();
    return $sql->num_rows();
  }

  function getAllWithOffset($appid=null,$number,$offset){
    if ($appid==null) {
      $appid = $this->session->userdata("ses_appid");
    }

    if(!empty($appid)){
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
      
      $this->db->join("tbemployeeareacabang","tbemployeeareacabang.employeeareacabang_employee_id = tbemployee.employee_id","left");
      $this->db->join("tbarea","tbarea.area_id = tbemployeeareacabang.employee_area_id","left");
      $this->db->join("tbcabang","tbcabang.cabang_id = tbemployeeareacabang.employee_cabang_id","left");

      $this->db->where("tbemployee.is_del !=","1");
      $this->db->where("tbemployee.employee_jenis_modif !=","delete");

      $this->db->where("tbemployee.appid",$appid);

      $this->db->where("resign_confirmed","no");
      $this->db->order_by("tbemployee.employee_id","ASC");

      $sql = $this->db->get("tbemployee",$number,$offset);
      
      return $sql->result();
    }else {
      return false;
    }
  }
  
  function getAllEmployeeLicense($appid=null,$number,$offset,$search_box){
    if ($appid==null) {
      $appid = $this->session->userdata("ses_appid");
    }

    if(!empty($appid)){
      $this->db->select("tbemployee.*");

      $this->db->select("1 as total_fingerprint");
      $this->db->select("1 as total_face");

      $this->db->where("tbemployee.is_del !=","1");
	  $this->db->like("tbemployee.employee_full_name",$search_box);
      $this->db->where("tbemployee.employee_jenis_modif !=","delete");

      $this->db->where("tbemployee.appid",$appid);

      $this->db->where("resign_confirmed","no");
      $this->db->order_by("tbemployee.employee_id","ASC");

      $sql = $this->db->get("tbemployee",$number,$offset);
      
      return $sql->result();
    }else {
      return false;
    }
  }

  function getIntraxActive($appid,$subscription_id){
    $this->db->select("employee_id");
	$this->db->group_start();
    $this->db->where("subscription_id",$subscription_id);
    $this->db->or_where("subscription_id",NULL);
	$this->db->group_end();
    $this->db->where("intrax_license","active");
    $this->db->where("appid",$appid);
	$this->db->where("resign_confirmed","no");
    $sql = $this->db->get("tbemployee");
    return $sql;
  }

  function getName($id){
    $this->db->select("employee_full_name");
    $this->db->where("employee_id",$id);
    $sql = $this->db->get($this->tableName);
    if($sql->num_rows()>0){
      return $sql->row()->employee_full_name;
    }
  }
  function getByEmployeeCode($appid,$employeeCode){
    $this->db->where("appid",$appid);
    $this->db->where("employee_account_no",$employeeCode);
    // echo $appid."<br>";
    // echo $employeeCode;
    $sql = $this->db->get($this->tableName);

    if($sql->num_rows()>0){
      return $sql->row();
    }else{
      return false;
    }
  }
}
