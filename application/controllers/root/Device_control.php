<?php 

class Device_control extends Root_Controller
{
	
	var $now;
	function __construct()
	{
		parent::__construct();
		$this->load->library("session");
		$this->now = date("Y-m-d H:i:s");
		load_model(["device_model"]);
		$this->setMenu(2);
		$this->checkPermission();
	}

	function index(){
		$data = [];
		$main    		 = $this->getMain();
		$main["title"]   = "List Device";
		$main["content"] = "root/device_controll";
		//$this->db->where("is_real","yes");
		$sql = $this->device_model->getAllDeviceList();

		$data["responseCode"] = $this->device_model->getResponseCode();
		
		$main["viewData"] = $data;
		
		$main["externalCSS"] = [
			base_url("asset/template/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css")
		];

		$main["externalJS"]  = [
			"https://cdn.jsdelivr.net/npm/sweetalert2@8",
			base_url("asset/template/bower_components/datatables.net/js/jquery.dataTables.min.js"),
        	base_url("asset/template/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js")
		];

		$main["menu"]     = 2;
		$this->load->view("layouts/main_root",$main);
	}

	function suspendDevice(){
		$deviceid = $this->input->post("deviceid");
		$result   = $this->device_model->suspendDevice($deviceid);
		if($result){
			echo "OK";
		}
	}

	function unlockSuspendedDevice(){
		$deviceid = $this->input->post("deviceid");
		$result   = $this->device_model->unlockSuspendDevice($deviceid);
		($result);
		if($result==true){
			echo "OK";
		}elseif ($result==false) {
			echo "unavailable";
		}
	}

	function loadData(){
		$company_type = $this->input->post("company_type");
		$company      = $this->input->post("company");
		$sn   		  = $this->input->post("sn");
		
		if($sn!=""){
			$this->db->like("A.device_SN",$sn);
		}

		if($company_type!=""){
			$this->db->where("B.is_real",$company_type);
		}
		
		if($company!=""){
			$this->db->where("B.appid",$company);
		}

		$sql = $this->device_model->getAllDeviceList();
		
		$this->table->set_template($this->tabel_template);
		$this->table->set_heading(
			"",
			"Company Name",
			"Serial Number",
			"License Status",
			"Connection",
			"Response Code",
			["data"=>"Suspend", "class" => "text-right"]
		);

		foreach ($sql->result() as $row) {
			$rangeActive = dateDifferenceTime($this->now,$row->device_last_communication);
		    if($row->device_last_communication!=null && $rangeActive<120){
		        $deviceStatus = '<div class="text-green" style="cursor:pointer" data-toggle="tooltip" data-placement="top" title="Last connection '.(!empty($row->device_last_communication)? $row->device_last_communication : '-').'"><i class="fa fa-check-circle-o"></i> Connect</div>';
		    }else{
		        $deviceStatus = '<div class="text-red" style="cursor:pointer" data-toggle="tooltip" data-placement="top" title="Last connection '.(!empty($row->device_last_communication)? $row->device_last_communication : '-').'"><i class="fa fa-times-circle-o"></i> Disconnect</div>';
		    }

		    if($row->device_license=="active"){
		    	$lbLicense = '<div class="text-green"><i class="fa fa-check"></i> ACTIVE</div>';
		    }elseif ($row->device_license=="notactive") {
		        $lbLicense = '<div class="text-red"><i class="fa fa-close"></i> NOT ACTIVE</div>';
		    }

		    $arrSN = explode("|",$row->device_SN);

		    if($row->response_code==3){
		    	$btnReloadLog = '<button type="button" class="btn btn-success btn-sm" onclick="getDeviceLog(\''.$row->device_id.'\')"><i class="fa fa-download fa-lg "></i></button>';
			}else{
				$btnReloadLog = '';
		    }

		    if(!empty($arrSN[1]) && $arrSN[1]=="suspend"){
		    	$option = '<button onclick="undoSuspendSN('.$row->device_id.')" class="btn btn-primary btn-sm"><i class="fa fa-unlock"></i> Unlock</>';
		    }else{
		    	$option = '<button onclick="suspendSN('.$row->device_id.')" class="btn btn-danger btn-sm"><i class="fa fa-ban"></i> Suspend</>';
		    }
		    $option .= $btnReloadLog;
		    if($row->is_real=="yes"){
		    	$labelReal = '';
		    }elseif ($row->is_real=="no") {
		    	$labelReal = '<span class="text-red">Dev</div>';
		    }
		    $checkSelect 	 = '<input onclick="selectItem(this)" value="'.$row->device_id.'" type="checkbox" name="selectRow">';
		    
		    $this->table->add_row(
				$checkSelect,
				$row->company_name.' '.$labelReal,
				$arrSN[0],
				$lbLicense,
				$deviceStatus,
				$row->code_name,
				["data"=>$option, "class" => "text-right"]
			);
		}
		
		$table = $this->table->generate();
		echo json_encode($table);
	}

	function getCompanyList(){
		$this->load->model("subscription_model");
		$company_type = $this->input->post("company_type");
		
		if($company_type!=""){
			$this->db->where("is_real",$company_type);
		}
		$sql    = $this->subscription_model->getActiveAll();
		$output = [];
		foreach ($sql->result() as $row) {
			$output[] = [
				"id"  => $row->appid,
				"name"=> $row->company_name
			];
		}
		echo json_encode($output);
	}

	function setResponse(){
		$deviceId   = $this->input->post("deviceId");
		$codeId     = $this->input->post("codeId");
		$dataUpdate = [];

		foreach ($deviceId as $device) {
			$dataUpdate[] = [
				"device_id" 	=> $device,
				"response_code" => $codeId
			];				
		}

		$res = $this->device_model->update_batch($dataUpdate);
		if($res){
			echo "OK";
		}
	}

	function cmdPullLog(){
		
		$deviceID = $this->input->post("deviceID");

	}

	function setReboot(){
		$this->load->model("firewall_model");
		$this->load->model('device_model');

		$deviceIds = $this->input->post("deviceId");
		$dateOpen  = date("Y-m-d");
		foreach ($deviceIds as $deviceId) {
			$dataSource = ["reboot" => "yes"];
	        $res = $this->device_model->update2($dataSource,$deviceId);
	    	$this->firewall_model->setSchedule($deviceId,$dateOpen);
	    }
	    echo "OK";
	}
}
