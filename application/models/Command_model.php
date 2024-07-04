<?php 
/**
 * 
 */
class Command_model extends CI_Model
{
	private $tableName = "commadrequest";
	private $tableId   = "id";
	
	function __construct()
	{
		parent::__construct();
	}

	function insert($data){
		return $this->db->insert($this->tableName,$data);
	}

	function getCommandNeedExecute($SN){
		$this->db->where("device_SN",$SN);
		$this->db->where("is_execute","no");
		$this->db->order_by("id","ASC");
		$this->db->limit(1);
		$sql  = $this->db->get($this->tableName);
		$data = $sql->row();
		return !empty($data->command) ? "C:cmd.".$data->id.":".$data->command : false;
	}
	
	function finishExecute($commandID){
		$this->db->where("id",$commandID);
		return $this->db->update($this->tableName,[
			"is_execute"=>"yes"
		]);
	}
	
}