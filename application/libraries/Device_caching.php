<?php

class Device_caching
{
	private $CI;
	function __construct()
	{
		$this->CI =& get_instance();
	}

	function cacheSN(){
		
		$this->CI->load->model("device_model");
		$this->CI->db->where("A.device_license","active");
		$deviceList  = $this->CI->device_model->getAllDeviceList();
		$arrActiveSN = [];
		foreach($deviceList->result() as $deviceData ){
			if($deviceData->device_license=="active"){
				$arrActiveSN[] = $deviceData->device_SN;
			}
		}
		if(count($arrActiveSN)>0){
			$jsonStr = json_encode($arrActiveSN);
			$this->writeCahe($jsonStr);
		}
	}

	function readCache(){
	    $filePath = FCPATH."application".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."base_config.json";
	    $myfile = fopen( $filePath , "r") or die("Unable to open file!");
	    $storageJson =  fread($myfile,filesize($filePath));
	    fclose($myfile);
	    return $storageJson;
  	}

  	function writeCahe($jsonData){
    	$filePath = FCPATH."..".DIRECTORY_SEPARATOR."DIR086da7a0b61c4149af03".DIRECTORY_SEPARATOR."SN.json";
	    $myfile = fopen($filePath, "w") or die("Unable to open file!");
	    fwrite($myfile, $jsonData);
	    fclose($myfile);
	    return true;
  	}
}