<?php 

class MY_Controller extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
	}
}

/**
* 
*/
class Root_controller extends MY_Controller
{
	var $tabel_template  = array(
        'table_open'            => '<table class="table table-bordered table-stripped" id="datatable">',
        'table_close'           => '</table>'
	);

	var $main = [];
	function __construct()
	{
		parent::__construct();
		$this->load->library("session");
		if($this->session->userdata("ses_islogin")=="yes" && $this->session->userdata("ses_type")=="root"){
			$this->main["permission"] = !empty($this->session->ses_access) ? explode("|",$this->session->ses_access) : [];
		}else{
			$this->session->sess_destroy();
			$_SESSION["ses_msg"] = "You dont have access permission!";

			redirect("rootaccess-login");
		}
	}
	public function checkPermission(){
		if(!in_array($this->main["menu"], $this->main["permission"])){
			redirect("unauthorized");
		}
	}
	public function getTableTemplate(){
		return  $this->tabel_template;
	}

	public function setMenu($menu){
		$this->main["menu"] = $menu;
	}
	
	public function getMain(){
		return $this->main;
	}

}