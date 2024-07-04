<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Employee_report extends CI_Controller
{
  var $listMenu = "";
  var $timestamp;
  var $tabel_template  = array(
        'table_open'            => '<table class="table table-bordered table-stripped" >',
        'table_close'           => '</table>'
	);
  var $table_print  = array(
	 'table_open'       => '<table class="table-print" >',
	 'table_close'      => '</table>'
	);

  function __construct()
  {
    parent::__construct();
    $this->load->model("system_model");
    // model general
    $this->timestamp = date("Y-m-d H:i:s");

    $languange = !empty($this->session->userdata('lang')) ? $this->session->userdata('lang') :"en";
    $this->load->library("gtrans/gtrans",["lang" => $languange]);

    $this->load->model("menu_model");
    
    $this->system_model->checkSession(18);
    $this->listMenu = $this->menu_model->list_menu();
  }

  function index()
  {
    load_model(["area_model"]);
    $sqlArea = $this->area_model->getAll();
    $data['dataArea'] = $sqlArea;
    $parentViewData = [
      "title"   => "Employee Report",  // title page
      "content" => "report/employee_report",  // content view
      "viewData"=> $data,
      "listMenu"=> $this->listMenu,
      "externalCSS" => [
        base_url("asset/template/bower_components/bootstrap-daterangepicker/daterangepicker.css"),
        base_url("asset/template/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css")
      ],
      "externalJS"  => [
        base_url("asset/template/bower_components/moment/min/moment.min.js"),
        base_url("asset/template/bower_components/bootstrap-daterangepicker/daterangepicker.js"),
        base_url("asset/template/bower_components/datatables.net/js/jquery.dataTables.min.js"),
        base_url("asset/template/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js")
      ]
    ];

    $this->load->view("layouts/main",$parentViewData);
    $this->gtrans->saveNewWords();
  }

  function loadDataEmployee()
  {
    load_model(["employee_model","employeeareacabang_model"]);
    $appid   = $this->session->userdata("ses_appid");
    $sArea   = $this->input->post("sArea");
    $sCabang = $this->input->post("sCabang");

    $draw   = $_REQUEST['draw'];
		$length = $_REQUEST['length'];
		$start  = $_REQUEST['start'];
		$search = $_REQUEST['search']["value"];

    $allRecord                 = $this->employee_model->countActive($appid);
    $this->db->order_by("A.employee_account_no","ASC");
    $this->db->like("A.employee_full_name",$search);
    $sql = $this->employee_model->getActive($sArea,$sCabang,$start,$length,$appid);
    $this->db->like("A.employee_full_name",$search);
    $sqlWithoutLimit = $this->employee_model->getActive($sArea,$sCabang,"","",$appid);
    $output['recordsTotal']    = $allRecord;
    $output['recordsFiltered'] = $sqlWithoutLimit->num_rows();


    $no  = $start;
    $output['data'] = array();
    foreach ($sql->result() as $row) {
      $arrLocation = $this->employeeareacabang_model->getLocationName($row->employee_id,$appid);
      $location    = '';
      foreach ($arrLocation as $rowLocation) {
        $location .= $rowLocation->area_name.' <i class="fa fa-long-arrow-right "></i> '.$rowLocation->cabang_name.'<br>';
      }
      $no++;
      $output['data'][] = array(
        textCenter($no),
        $row->employee_account_no,
        $row->employee_full_name,
        date("Y-m-d",strtotime($row->employee_join_date)),
        $location
      );
		}
    echo json_encode($output);
  }

  function reportPrint()
  {
    load_model(["employee_model","area_model","cabang_model","employeeareacabang_model"]);

    $area = $_GET["area"];
    $cabang = $_GET["cabang"];
    $detailArea = $this->area_model->getById($area);
    $detailCabang = $this->cabang_model->getById($cabang);
    if($detailArea==false){
      $areaName = "All";
    }else{
      $areaName = $detailArea->area_name;
    }

    if($detailCabang==false){
      $cabangName = "All";
    }else{
      $cabangName = $detailCabang->cabang_name;
    }


    $appid    = $this->session->userdata("ses_appid");

    $sql = $this->employee_model->getActive($area,$cabang,"","",$appid);
    $this->table->set_template($this->table_print);
    $this->table->set_heading(
      ["data" => "NO","width"=>"10%","class" => "text-center"],
      ["data" => "ACCOUNT NO","class"=>"text-center"],
      ["data" => "NAME","class"=>"text-center"],
      ["data" => "ACTIVE DATE","class"=>"text-center"],
      ["data" => "ACTIVE LOCATION","class"=>"text-center"]
    );
    $no = 0;
    foreach ($sql->result() as $row) {
      $no++;
      $location    = '';
      $arrLocation = $this->employeeareacabang_model->getLocationName($row->employee_id,$appid);
      foreach ($arrLocation as $rowLocation) {
        $location .= $rowLocation->area_name.' >> '.$rowLocation->cabang_name.'<br>';
      }

      $this->table->add_row(
        [
          "style"=> "text-align:center",
          "data" => $no
        ],
        [
          "style"=> "text-align:center",
          "data" => $row->employee_account_no
        ],
        $row->employee_full_name,
        date("Y-m-d",strtotime($row->employee_join_date)),
        $location
      );
    }
    $data["title"] = "Report Employee";
    $data["subtitle"] = "Area : ".$areaName.", Branch : ".$cabangName;
    $data["tabel"] = ($sql->num_rows()>0) ? $this->table->generate() : '<p style="text-align:center">No Data Found!</p>';
    $data["type"]  = "print";
    $this->load->view("report/print_layout",$data);
  }
}
