<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Update_employee extends CI_Controller
{
  var $tabel_template  = array(
        'table_open'            => '<table class="table table-bordered table-stripped" >',
        'table_close'           => '</table>'
		);
  function __construct()
  {
    parent::__construct();
    $this->load->model("system_model");
    // model general
    $this->timestamp = date("Y-m-d H:i:s");
    $languange = "english";

    $this->lang->load("ui",$languange);
    $this->load->model("menu_model");
    
    $this->system_model->checkSession();
    $this->listMenu = $this->menu_model->list_menu();
  }

  function index($encEmployeeID){
    load_model(["employee_model"]);
    $this->system_model->checkSession();
    $sesAppid = $this->session->userdata("ses_appid");
    $this->load->library("encryption_org");

    if(!empty($encEmployeeID)){
      $employeeID   = $this->encryption_org->decode($encEmployeeID);
      $dataEmployee = $this->employee_model->getById($employeeID,$sesAppid);
      $data["detailEmployee"] = $dataEmployee;
    }

    $sql = $this->employee_model->getAll();
    $this->table->set_template($this->tabel_template);
    $this->table->set_heading("No","Account No","Nick Name","Full Name","Change");
    $no = 0;
    foreach ($sql as $row) {
      $no++;
      $pickButton = '<a href="'.base_url("update-employee/".$this->encryption_org->encode($row->employee_id)).'" class="btn btn-primary">Pick</a>';
      $this->table->add_row(
        $no,
        $row->employee_account_no,
        $row->employee_nick_name,
        $row->employee_full_name,
        $pickButton
      );
    }

    $data["tabelData"] = $this->table->generate();
    $parentViewData = [
      "title"   => "Update Employee",  // title page
      "content" => "transaction/update_employee",  // content view
      "viewData"=> $data,
      "listMenu"=> $this->listMenu,
      "externalJS" => [
        base_url("asset/template/bower_components/chart.js/Chart.js"),
        base_url("asset/js/dashboard.js")
      ]
    ];

    $this->load->view("layouts/main",$parentViewData);
  }
}
