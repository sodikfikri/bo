<?php

/**
 * 
 */
class Historytemplatepush_model extends CI_Model
{
	var $tableName = "history_template_push";

	function __construct()
	{
		parent::__construct();
	}

	function insert($data){
		return $this->db->insert($this->tableName,$data);
	}

	function get($employeeID){
		$this->db->select("history_template_push.date_create");
		$this->db->select("tbemployeetemplate.employeetemplate_index");
		$this->db->select("tbemployeetemplate.employeetemplate_jenis");
		$this->db->select("tbdevice.device_SN");

		$this->db->from($this->tableName);
		$this->db->join("tbemployeetemplate","tbemployeetemplate.employeetemplate_id = history_template_push.template_id");
		$this->db->join("tbdevice","tbdevice.device_id = history_template_push.device_id");
		$this->db->where("tbemployeetemplate.employeetemplate_employee_id",$employeeID);
		$this->db->order_by("history_template_push.date_create","DESC");
		return $this->db->get();
	}
}