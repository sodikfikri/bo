<?php 

class Tes_datatable extends CI_Controller
{
	var $listMenu = "";
	function __construct()
	{
		parent::__construct();
		$this->listMenu = $this->menu_model->list_menu();
	}

	function index(){
		$data["title"]   = "Dashboard";
      	$data["content"] = "dav_layout";

		$this->load->view("layouts/main",$data);
	}
}