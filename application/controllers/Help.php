<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Help extends CI_Controller
{
  var $now;
  var $listMenu = "";
  var $employeeLicense = 0;
  var $tabel_template  = array(
        'table_open'            => '<table class="table table-bordered table-stripped" >',
        'table_close'           => '</table>'
	);
  function __construct()
  {
    parent::__construct();
    $this->load->model("system_model");
    $this->now = date("Y-m-d H:i:s");

    $languange = !empty($this->session->userdata('lang')) ? $this->session->userdata('lang') :"en";
    $this->load->library("gtrans/gtrans",["lang" => $languange]);
    
    $this->load->model("menu_model");
    
    $this->system_model->checkSession(1);
    $this->listMenu = $this->menu_model->list_menu();
  }

  function index(){
    $data = "";
    $parentViewData = [
      "title"   => "Help",  // title page
      "content" => "help",  // content view
      "viewData"=> $data,
      "listMenu"=> $this->listMenu,
    ];
    $this->load->view("layouts/main",$parentViewData);
  }
}
