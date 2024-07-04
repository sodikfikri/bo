<?php 
/**
* 
*/
class Device_monitor extends Root_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->library("session");
		$this->setMenu(1);
		load_model(["devicetemporary_model"]);
	}

	function index($mode="device-monitor"){
		$main    		 	= $this->getMain();

		$data["mode"] 		= $mode;
		$data["temporaryInfo"] = $this->devicetemporary_model->getTemporaryInfo();
		if($mode=="server-monitor"){
			$filePath = FCPATH."application".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."base_config.json";
	    	$myfile = fopen( $filePath , "r") or die("Unable to open file!");
	   		$storageJson =  fread($myfile,filesize($filePath));
			$arrConfig   = json_decode($storageJson);
			
			$data["firewallStatus"] 		= $arrConfig->use_firewall;
		}
		if(!empty($this->session->userdata("msg"))){
			$data["msg"] = $this->session->userdata("msg");
			$this->session->unset_userdata("msg");
		}
		/////
		$oldJsonConfig = $this->readFromLocal();
		$oldConfig     = json_decode($oldJsonConfig);
		$data["processCount"] = !empty($oldConfig->processCount) ? $oldConfig->processCount : 1;
		////
		$main["title"]   	= "Device Monitor";
		$main["content"] 	= "root/device_monitor";
		$main["viewData"] 	= $data;
		$main["menu"]     	= 1;
		
		$main["externalCSS"] = [
			base_url("asset/plugins/datepicker/datepicker3.css")	
		];

		$main["externalJS"] = [
			"https://cdn.jsdelivr.net/npm/sweetalert2@8",
			base_url("asset/plugins/datepicker/bootstrap-datepicker.js"),
			base_url("asset/plugins/datepicker/locales/bootstrap-datepicker.id.js")
		];
		
		$this->load->view("layouts/main_root",$main);
	}
	
	function reduceProcessedData(){
		$delLimit = $this->input->post("delete-limit");
		$affectedRow = $this->devicetemporary_model->reduceProcessedData($delLimit);
		$this->session->set_userdata("msg",$affectedRow." data has been reduced!");
		redirect("rootaccess/device-monitor");
	}

	function getDeviceActivity(){
		
		
		$dataMonitor = [];

		$oldLastId = $this->input->post("lastID");

		if($oldLastId==""){
			// new access
			$lastHours = date("Y-m-d H:i:s",strtotime('-2 hours'));
			$this->db->where("datecreated > ",$lastHours);
		}else{
			// recursive access
			$this->db->where("id >" , $oldLastId);
		}
		

		$sql  	   = $this->deviceshipments_model->getActivity(100);
		//echo $this->db->last_query();
		// menerima last id untuk membatasi selection
		
		$newLastID = $oldLastId;
		foreach ($sql->result() as $index => $row) {
			
			if($index==0){
				$newLastID = $row->id;
			}

			$dataMonitor[] = [
				"datetime" => $row->datecreated,
				"SN" => $row->SN,
				"appid" => $row->appid,
				"endpoint" => $row->endpoint,
				"method" => strtoupper($row->method),
				"data" => '<textarea>'.$row->post.'</textarea>'
			];
		}

		$output = [
			"dataMonitor" => $dataMonitor,
			"newLastID"   => $newLastID
		];
		
		echo json_encode($output);
	}

	public function clearShipment()
	{
		$datefinish = date("Y-m-d",strtotime($this->input->post("datefinish")));
		$result     = $this->deviceshipments_model->clearShipment($datefinish);

		if($result==true){
			echo "OK";
		}
	}

	public function searchData(){
		$this->load->model("subscription_model");
		$main    		 	= $this->getMain();

		$dataCompany = $this->subscription_model->getActiveCompany();
		
		$data["dataCompany"]= $dataCompany;
		$main["title"]   	= "Device Monitor Searching";
		$main["content"] 	= "root/device_monitor_searching";
		$main["viewData"] 	= $data;
		$main["menu"]     	= 1;
		
		$main["externalCSS"] = [
			base_url("asset/plugins/daterangepicker3.0.5/daterangepicker.css"),
			base_url("asset/plugins/datepicker/datepicker3.css"),
			base_url("asset/template/bower_components/select2/dist/css/select2.min.css")
		];

		$main["externalJS"] = [
			"https://cdn.jsdelivr.net/npm/sweetalert2@8",
			base_url("asset/plugins/datepicker/bootstrap-datepicker.js"),
			base_url("asset/plugins/datepicker/locales/bootstrap-datepicker.id.js"),
			base_url("asset/template/bower_components/moment/min/moment.min.js"),
        	base_url("asset/plugins/daterangepicker3.0.5/daterangepicker.js"),
        	base_url("asset/template/bower_components/select2/dist/js/select2.full.min.js")
		];

		$this->load->view("layouts/main_root",$main);
	}

	function searchShipment(){
		$reservation = $this->input->post("reservation");
		$company 	 = $this->input->post("company");
		$SN 		 = $this->input->post("SN");
		$pattern     = $this->input->post("pattern");

		$arrPeriode  = explode(" - ", $reservation);
		$from = date("Y-m-d",strtotime($arrPeriode[0]));
		$to   = date("Y-m-d",strtotime($arrPeriode[1]));

		$DB = $this->deviceshipments_model->searchShipment($from,$to,$company,$SN,$pattern);

		$output = [];
		foreach ($DB->result() as $row) {
			$output[] = [
				"post" 		=> $row->post,
				"SN"   		=> $row->SN,
				"appid"		=> $row->appid,
				"endpoint"	=> $row->endpoint,
				"method"	=> $row->method,
				"datetime"  => $row->datecreated
			];
		}
		echo json_encode($output);
	}

	function changeFirewall($mode){
		$this->load->model("firewall_model");
		$oldJsonConfig = $this->readFromLocal();
		$oldConfig     = json_decode($oldJsonConfig);
		$oldConfig->use_firewall = $mode;
		$newJsonConfig 			 = json_encode($oldConfig);
		$this->writeToLocal($newJsonConfig);
		if($mode=="on"){
			$this->firewall_model->openAllDevice();
		}
		redirect("rootaccess/device-monitor/server-monitor");
	}

	function readFromLocal(){
	    $filePath = FCPATH."application".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."base_config.json";
	    $myfile = fopen( $filePath , "r") or die("Unable to open file!");
	    $storageJson =  fread($myfile,filesize($filePath));
	    fclose($myfile);
	    return $storageJson;
  	}

  	function writeToLocal($jsonData){
    	$filePath = FCPATH."application".DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR."base_config.json";
	    $myfile = fopen($filePath, "w") or die("Unable to open file!");
	    fwrite($myfile, $jsonData);
	    fclose($myfile);
	    return true;
  	}

  	function saveSetting(){
  		$processCount = $this->input->post("data-processed-count");
  		$oldJsonConfig = $this->readFromLocal();
		$oldConfig     = json_decode($oldJsonConfig);
		$oldConfig->processCount = $processCount;
		$newJsonConfig 			 = json_encode($oldConfig);
		$this->writeToLocal($newJsonConfig);
		$this->session->set_userdata("msg","Seeting has been updated!");
		redirect("rootaccess/device-monitor");
  	}
}