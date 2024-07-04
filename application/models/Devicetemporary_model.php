<?php

/**
 * 
 */
class Devicetemporary_model extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();

	}

	function getTemporaryInfo(){
		$sqlUnprocessed = $this->db->query("select count(id) as total from inact_devicedata.raw_data where isProcessed='no' and post !=''");
		$sqlProcessed = $this->db->query("select count(id) as total from inact_devicedata.raw_data_processed where isProcessed='yes'");
		$sqlBroken = $this->db->query("select count(id) as total from inact_devicedata.raw_data where isProcessed='no' and post=''");
		$output = [
			"unprocessed" => $sqlUnprocessed->row()->total,
			"processed"   => $sqlProcessed->row()->total,
			"broken"      => $sqlBroken->row()->total
		];
		
		return $output;
	}

	function reduceProcessedData($limit){
		$this->db->where("post !=","");
		$this->db->where("isProcessed","yes");
		$this->db->order_by("id","asc");
		$this->db->limit($limit);
		$this->db->delete("inact_devicedata.raw_data_processed");
		
		return $this->db->affected_rows();
	}
}