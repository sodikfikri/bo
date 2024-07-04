<?php 

class Fakegps extends Root_controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->library("session");
		$this->load->library("encryption_org");
		$this->load->model("fakegps_model");
		
		$this->setMenu(7);
		$this->checkPermission();

	}

	function index(){
		$main    			= $this->getMain();
		$main["title"] 		= "Fake GPS Manager";
		$main["content"] 	= "root/fakegps";

		$sql = $this->fakegps_model->getAll();

		$this->table->set_template($this->getTableTemplate());
		$this->table->set_heading(
			"No",
			"Code",
			"Application Name",
			"Description",
			[
				"data" => "Option",
				"class"=> "text-center"
			]
		);

		$no= 0;
		foreach ($sql->result() as $row) {
			$encId = $this->encryption_org->encode($row->fakegps_id);
			$no++;
			$edit   = anchor('rootaccess/fakegps-manager/edit/'.$encId,'<i class="fa fa-edit"></i> Edit',['class' => 'text-blue']);
			$delete = anchor('rootaccess/fakegps-manager/delete/'.$encId,'<i class="fa fa-trash"></i> Delete',['class' => 'text-red']);
			$option = $edit.' '.$delete;
			$this->table->add_row(
				$no,
				$row->fakegps_code,
				$row->fakegps_name,
				$row->fakegps_keterangan,
				[
					"data" => $option,
					"class" => 'text-center'
				]
			);
		}
		$data['tableData']  = $this->table->generate();
		
		if(!empty($this->session->userdata("ses_msg"))){
			$data["msg"] = $this->session->userdata("ses_msg");
			$this->session->unset_userdata("ses_msg");
		}
		$main["viewData"]  	= $data;
		$this->load->view("layouts/main_root",$main);
	}

	function manage_fakegps($encID=""){
		$main    			= $this->getMain();
		if($encID==""){
			$main["title"] = $data["title"] = "Add Fake GPS";
			$data["type"] = "add";
		}else{
			$data["type"] = "edit";
			$id 		= $this->encryption_org->decode($encID);
			$dataFakegps 	= $this->fakegps_model->getById($id);
			if($dataFakegps==false){
				$data["edit"] = [];
			}else{
				$data["edit"]   = $dataFakegps;
				$data["access"] = explode("|", $dataFakegps->access);
			}
			$main["title"] = $data["title"]		= "Edit Fake GPS";
		}
		
		$data["id"] 	 = $encID;
		$main["content"] = "root/form_fakegps_manager";
		
		$main["viewData"]  	= $data;
		$this->load->view("layouts/main_root",$main);
	}

	function save_fakegps(){
		$this->load->library("string_manipulation");
		$id = $this->input->post("id");
		$fakegps_code = $this->input->post("fakegps_code");
		$fakegps_name = $this->input->post("fakegps_name");
		$fakegps_keterangan 	  = $this->input->post("fakegps_keterangan");
		

		$strAccess= !empty($access) ? implode("|", $access) : "";
		
		$data = [
				"fakegps_name" => $fakegps_name,
				"fakegps_code" => $fakegps_code,
				"fakegps_keterangan"    => $fakegps_keterangan
		];

		if($id==""){
			// add 
			$this->fakegps_model->insert($data);
			$this->session->set_userdata("ses_msg",'<div class="callout callout-success">Data Inserted!</div>');
		}else{
			// update
			$this->fakegps_model->update($data,$this->encryption_org->decode($id));
			$this->session->set_userdata("ses_msg",'<div class="callout callout-success">Data updated!</div>');

		}
		redirect("rootaccess/fakegps-manager");
	}

	function delete($encID){
		$id = $this->encryption_org->decode($encID);
		$this->fakegps_model->delete($id);
		$this->session->set_userdata("ses_msg",'<div class="callout callout-success">Data deleted!</div>');
		redirect("rootaccess/fakegps-manager");
	}
}