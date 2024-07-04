<?php 

class Company_manager extends Root_controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->library("session");
		$this->setMenu(6);
		$this->checkPermission();

		$this->load->model("subscription_model");
		$this->load->library("encryption_org");
	}

	function index(){
		
		$main    			= $this->getMain();
		$main["title"] 		= "Company Manager";
		$main["content"] 	= "root/company_manager";
		$this->table->set_template($this->getTableTemplate());
		$this->table->set_heading(
			"No",
			"Company Name",
			"App ID",
			"Main Address",
			"Join Date",
			"Status",
			""
		);
		
		$sql = $this->subscription_model->getActiveCompany();
		$no  = 0;

		foreach ($sql->result() as $row) {
			$no++;
			$encId = $this->encryption_org->encode($row->iasubscription_id);
			if($row->is_real=="yes"){
				$memberType = '<span id="cs'.$no.'" value="yes" class="text-green pointer" onclick="switchCompanyType(\''.$encId.'\','.$no.')"><i class="fa fa-unlink"></i> Real Company</span>';
			}else{
				$memberType = '<span id="cs'.$no.'" value="no" class="text-red pointer" onclick="switchCompanyType(\''.$encId.'\','.$no.')"><i class="fa fa-link"></i> Internal Account</span>';
			}

			$this->table->add_row(
				$no,
				$row->company_name,
				$row->appid,
				$row->company_addr." ".$row->company_city,
				$row->registration_date,
				$memberType
			);
		}

		$data["dataPerusahaan"] = $this->table->generate();
		$main["viewData"]  	= $data;

		$this->load->view("layouts/main_root",$main);
	}

	function switchCompanyType(){
		$id = $this->encryption_org->decode($this->input->post("id"));
		$isreal = $this->input->post("isreal");
		if($isreal=="yes"){
			$this->subscription_model->switchCompanyType($id,"no");
		}elseif ($isreal=="no") {
			$this->subscription_model->switchCompanyType($id,"yes");
		}
		
		echo "OK";
	}

}