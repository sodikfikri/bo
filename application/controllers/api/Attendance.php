<?php 
class Attendance extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->library("machinepost_reader");
		$this->load->model("checkinout_model");
	}

	function index(){
		$this->db->select("device_shipments.*");
		$this->db->select("tbdevice.device_area_id");
		$this->db->select("tbdevice.device_cabang_id");
		$this->db->select("tbdevice.device_id");

		// ambil yang terdaftar saja
		$this->db->where("device_shipments.appid !=","");
		
		// ambil yang dari endpoint cdata saja
		$this->db->where("device_shipments.endpoint","cdata");
		
		// ambil yang methodnya post saja
		$this->db->where("device_shipments.method","post");
		
		// yang sudah diupload tidak boleh diupload lagi
		$this->db->where("device_shipments.is_uploaded","0");

		// ambil satu record saja
		$this->db->limit(10);

		$this->db->from("device_shipments");
		$this->db->join("tbdevice","tbdevice.device_SN = device_shipments.SN");

		$sql = $this->db->get();
		
		$rowCount = $sql->num_rows();
		
		$no = 0;
		foreach ($sql->result() as $row) {
			$no++;
			$result = $this->convertRawData(
				$row->appid,
				$row->device_area_id,
				$row->device_cabang_id,
				$row->SN,
				$row->device_id,
				$row->post
			);

			if($result=="attendance"){
				$statusUpload = "1";
			}else{
				$statusUpload = "-1";
			}

			$dataUpdate = [
				"is_uploaded" => $statusUpload
			];

			$this->db->where("id",$row->id);
			$result = $this->db->update("device_shipments",$dataUpdate);
			if($result==true && $rowCount==$no){
				echo "OK";
			}
		}
	}

	function convertRawData($appid,$area,$cabang,$SN,$device_id,$dataPost){
		
	  	$postIdentify = $this->machinepost_reader->postIdentify($dataPost);
	  	
      	if($postIdentify=="attendance"){
        	$arrayAttendance = $this->machinepost_reader->prepareAttendanceToArray($dataPost,$appid,$SN,$device_id,$area,$cabang);

        	if(count($arrayAttendance)>0){
          		$insertAttendance = $this->checkinout_model->bulk_insert($arrayAttendance);
          		if($insertAttendance==true){
            		return $postIdentify;
          		}
        	}else{
				return $postIdentify;
			}
		}
	}
}