<?php 
class Inoutmobile_model extends CI_Model{
    private $tableName = "tbcheckinout_mobile";

    function __construct(){
        parent::__construct();
    }

    function insert($data){
        return $this->db->insert($this->tableName,$data);
    }
	
	function update($employee_id,$checklog_date){
		$dataUpdate = [
			"status" => "1"
		];
		$this->db->where("employee_id",$employee_id);
		$this->db->where("checklog_date",$checklog_date);
		return $this->db->update($this->tableName,$dataUpdate);
    }
	
	function getDataInOutMobile($fromDate,$toDate,$employee_id){
		$this->db->select("tbcheckinout_mobile.*");
		$this->db->where("tbcheckinout_mobile.employee_id",$employee_id);
		$this->db->group_start();
		$this->db->where("tbcheckinout_mobile.checklog_from","INTRAX_2");
		$this->db->or_where("tbcheckinout_mobile.checklog_from","INTRAX_3");
		$this->db->group_end();
		$this->db->where("DATE(tbcheckinout_mobile.checklog_date) >=",$fromDate);
		$this->db->where("DATE(tbcheckinout_mobile.checklog_date) <=",$toDate);
		$this->db->from("tbcheckinout_mobile");
		$this->db->order_by("checklog_date", "desc");
		$sql = $this->db->get();
		if($sql->num_rows()>0){
			return $sql->result();
		}else{
			return false;
		}
	  }
	  
	function getSummaryData($fromDate,$toDate,$employee_id){
		$this->db->select("tbcheckinout_mobile.*");
		$this->db->where("tbcheckinout_mobile.employee_id",$employee_id);
		$this->db->group_start();
		$this->db->where("tbcheckinout_mobile.checklog_from","INTRAX_2");
		$this->db->or_where("tbcheckinout_mobile.checklog_from","INTRAX_3");
		$this->db->group_end();
		$this->db->where("DATE(tbcheckinout_mobile.checklog_date) >=",$fromDate);
		$this->db->where("DATE(tbcheckinout_mobile.checklog_date) <=",$toDate);
		$this->db->from("tbcheckinout_mobile");
		$this->db->order_by("checklog_date", "desc");
		$this->db->group_by("DATE(tbcheckinout_mobile.checklog_date)");
		$sql = $this->db->get();
		return $sql->num_rows();
	  }
	  
	function getAllDataInOutMobile($fromDate,$toDate,$appid){
		$this->db->select("tbcheckinout_mobile.*");
		$this->db->select("tbemployee.employee_full_name");
		$this->db->group_start();
		$this->db->where("tbcheckinout_mobile.checklog_from","INTRAX_2");
		$this->db->or_where("tbcheckinout_mobile.checklog_from","INTRAX_3");
		$this->db->group_end();
		$this->db->where("tbcheckinout_mobile.appid",$appid);
		$this->db->where("DATE(tbcheckinout_mobile.checklog_date) >=",$fromDate);
		$this->db->where("DATE(tbcheckinout_mobile.checklog_date) <=",$toDate);
		$this->db->from("tbcheckinout_mobile");
		$this->db->join("tbemployee","tbcheckinout_mobile.employee_id = tbemployee.employee_id");
		$this->db->order_by("checklog_date", "desc");
		$sql = $this->db->get();
		if($sql->num_rows()>0){
			return $sql->result();
		}else{
			return false;
		}
	}
	
	function getAllDataPush($appid){
		$this->db->select("tbcheckinout_mobile.*");
		$this->db->select("tbemployee.employee_full_name");
		$this->db->group_start();
		$this->db->where("tbcheckinout_mobile.checklog_from","INTRAX_2");
		$this->db->or_where("tbcheckinout_mobile.checklog_from","INTRAX_3");
		$this->db->group_end();
		$this->db->where("tbcheckinout_mobile.appid",$appid);
		$this->db->where("tbcheckinout_mobile.status",'0');
		$this->db->from("tbcheckinout_mobile");
		$this->db->join("tbemployee","tbcheckinout_mobile.employee_id = tbemployee.employee_id");
		$this->db->order_by("checklog_date", "desc");
		$this->db->limit(50);
		$sql = $this->db->get();
		if($sql->num_rows()>0){
			return $sql->result();
		}else{
			return false;
		}
	}
	
	function checkDataInOutMobile($checklog_date,$employee_id,$checklog_event){
		$this->db->select("tbcheckinout_mobile.*");
		$this->db->group_start();
		$this->db->where("tbcheckinout_mobile.checklog_from","INTRAX_2");
		$this->db->or_where("tbcheckinout_mobile.checklog_from","INTRAX_3");
		$this->db->group_end();
		$this->db->where("tbcheckinout_mobile.checklog_date =",$checklog_date);
		$this->db->where("tbcheckinout_mobile.employee_id =",$employee_id);
		$this->db->where("tbcheckinout_mobile.checklog_event =",$checklog_event);
		$this->db->from("tbcheckinout_mobile");
		$this->db->order_by("checklog_date", "desc");
		$sql = $this->db->get();
		return $sql->num_rows();
	}

	function getEmployeeById($emloyee_id) {
		$this->db->select('*');
		$this->db->from("tbemployee");
		$this->db->where('employee_id = ', $emloyee_id);

		$query = $this->db->get();
		return $query->result();
	}
}