<?php 
/**
* 
*/
class Rootuser_model extends CI_Model
{
	private $tableName = "root_user";
	private $tableId   = "id";
	function __construct()
	{
		parent::__construct();

	}

	function auth($username,$password){
		$where = [
			"username" => $username,
			"password" => $password
		];
		$this->db->limit(1);
		$res = $this->db->get_where($this->tableName,$where);
		if($res->num_rows()>0){
			return $res->row();
		}else{
			return false;
		}
	}

	function getAll(){
		$res = $this->db->get($this->tableName);
		return $res;
	}

	function getById($id){
		$this->db->where($this->tableId,$id);
		$sql = $this->db->get($this->tableName);
		
		if($sql->num_rows()>0){
			return $sql->row();
		}else{
			return false;
		}
	}

	function insert($data){
		$res = $this->db->insert($this->tableName,$data);
		return $res;
	}

	function update($data,$id){
		$this->db->where($this->tableId,$id);
		$res = $this->db->update($this->tableName,$data);
		return $res;
	}

	function delete($id){
		$this->db->where($this->tableId,$id);
		$res = $this->db->delete($this->tableName);
		return $res;
	}

}