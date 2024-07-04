<?php 

class Deviceshipments_model extends CI_Model
{
	private $tableName = "device_shipments";

	function __construct()
	{
		parent::__construct();
	}

	function insert($data){
		$res = $this->db->insert($this->tableName,$data);
		return $res;
	}

	function getActivity(){
		$this->db->order_by("datecreated","DESC");
		$this->db->limit(10);
		
		$res = $this->db->get($this->tableName);
		return $res;
	}

	function clearShipment($finish){
		$this->db->where("DATE(datecreated) <=",$finish);
		$res = $this->db->delete($this->tableName);
		return $res;
	}

	function searchShipment($from,$to,$company="",$SN="",$pattern){
		$this->db->where("DATE(datecreated) >=",$from);
		$this->db->where("DATE(datecreated) <=",$to);
		
		if ($company!="") {
			$this->db->where("appid",$company);
		}
		
		if ($SN!="") {
			$this->db->where("SN",$SN);
		}
		
		$this->db->like("post",$pattern);
		$res = $this->db->get($this->tableName);

		return $res;
	}
	
}