<?php
class Notif_Model extends CI_Model
{

	function __construct()
	{
		parent::__construct();
	}

	function insert($data){
		$res = $this->db->insert("tbnotification",$data);
		return $res;
	}

	function get($appid){
		$this->db->where("appid",$appid);
		$sql = $this->db->get("tbnotification");
		return $sql;
	}

	function getById($id){
		$this->db->where("notif_id",$id);
		$sql = $this->db->get("tbnotification");
		return $sql;
	}

	function closeNotif($id){
		$dataUpdate = [
			"status" => 'close'
		];

		$this->db->where("notif_id",$id);
		$res = $this->db->update("tbnotification",$dataUpdate);
		return $res;
	}

	function closeAllNotif($appid){
		$dataUpdate = [
			"status" => 'close'
		];

		$this->db->where("appid",$appid);
		$res = $this->db->update("tbnotification",$dataUpdate);
		return $res;
	}

}
