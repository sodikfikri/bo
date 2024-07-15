<?php 

class Auth extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library("session");
		$this->load->library("string_manipulation");
	}

	function login(){
		$this->load->helper("form_helper");
		$this->load->library("form_validation");
		$this->form_validation->set_rules("submit","submit","required");
		$data = [];
		if($this->form_validation->run()==true){
			$username = $this->input->post("username");
			$password = $this->string_manipulation->hash_rootpassw($this->input->post("password"));
			load_model(["rootuser_model"]);
			$res = $this->rootuser_model->auth($username,$password);
			if($res!=false){
				$session = [
					"ses_islogin" => "yes",
					"ses_type" 	  => "root",
					"ses_rootid"  => $res->id,
					"ses_name" 	  => $res->fullname,
					"ses_access"  => $res->access
				];

				$this->session->set_userdata($session);
				redirect("rootaccess");
			}else{
				$data["msg"] = '<div class="callout callout-danger" >Unknown Username Or Password</div>';
			}
		}
		//echo $this->session->userdata("ses_msg");
		if(!empty($_SESSION["ses_msg"])){
			$data["msg"] = '<div class="callout callout-danger" >'.$_SESSION["ses_msg"].'</div>';
			$this->session->unset_userdata('ses_msg');
		}

		$this->load->view("root/login",$data);
	}

	function logout(){
		$this->session->sess_destroy();
		redirect("rootaccess");
	}
}