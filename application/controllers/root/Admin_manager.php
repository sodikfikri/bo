<?php 

class Admin_manager extends Root_controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->library("session");
		$this->load->library("encryption_org");
		$this->load->model("rootuser_model");
		
		$this->setMenu(4);
		$this->checkPermission();

	}

	function index(){
		$main    			= $this->getMain();
		$main["title"] 		= "Admin Manager";
		$main["content"] 	= "root/admin_manager";

		$sql = $this->rootuser_model->getAll();

		$this->table->set_template($this->getTableTemplate());
		$this->table->set_heading(
			"No",
			"Full Name",
			"Username",
			"Email",
			[
				"data" => "Option",
				"class"=> "text-center"
			]
		);

		$no= 0;
		foreach ($sql->result() as $row) {
			$encId = $this->encryption_org->encode($row->id);
			$no++;
			$edit   = anchor('rootaccess/admin-manager/edit/'.$encId,'<i class="fa fa-edit"></i> Edit',['class' => 'text-blue']);
			$delete = anchor('rootaccess/admin-manager/delete/'.$encId,'<i class="fa fa-trash"></i> Delete',['class' => 'text-red']);
			$option = $edit.' '.$delete;
			$this->table->add_row(
				$no,
				$row->fullname,
				$row->username,
				$row->email,
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

	function manage_admin($encID=""){
		$main    			= $this->getMain();
		if($encID==""){
			$main["title"] = $data["title"] = "Add Admin";
			$data["type"] = "add";
		}else{
			$data["type"] = "edit";
			$id 		= $this->encryption_org->decode($encID);
			$dataAdmin 	= $this->rootuser_model->getById($id);
			if($dataAdmin==false){
				$data["edit"] = [];
			}else{
				$data["edit"]   = $dataAdmin;
				$data["access"] = explode("|", $dataAdmin->access);
			}
			$main["title"] = $data["title"]		= "Edit Admin";
		}
		
		$data["id"] 	 = $encID;
		$main["content"] = "root/form_admin_manager";
		
		$main["viewData"]  	= $data;
		$this->load->view("layouts/main_root",$main);
	}

	function save_admin(){
		$this->load->library("string_manipulation");
		$id = $this->input->post("id");
		$username = $this->input->post("username");
		$fullname = $this->input->post("fullname");
		$email 	  = $this->input->post("email");
		$password = $this->input->post("password");
		$access   = $this->input->post("access");
		

		$strAccess= !empty($access) ? implode("|", $access) : "";
		
		$data = [
				"fullname" => $fullname,
				"username" => $username,
				"access"   => $strAccess,
				"email"    => $email
		];

		if($id==""){
			// add 
			$encPass = $this->string_manipulation->hash_rootpassw($password);
			$data["password"] = $encPass;
			$this->rootuser_model->insert($data);
			$this->session->set_userdata("ses_msg",'<div class="callout callout-success">Data Inserted!</div>');
		}else{
			// update
			if($password!=""){
				$encPass = $this->string_manipulation->hash_rootpassw($password);
				$data["password"] = $encPass;
			}
			$this->rootuser_model->update($data,$this->encryption_org->decode($id));
			$this->session->set_userdata("ses_msg",'<div class="callout callout-success">Data updated!</div>');

		}
		redirect("rootaccess/admin-manager");
	}

	function delete($encID){
		$id = $this->encryption_org->decode($encID);
		$this->rootuser_model->delete($id);
		$this->session->set_userdata("ses_msg",'<div class="callout callout-success">Data deleted!</div>');
		redirect("rootaccess/admin-manager");
	}
}