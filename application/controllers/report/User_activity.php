<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class user_activity extends CI_Controller
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
    $this->timestamp = date("Y-m-d H:i:s");

    $languange = !empty($this->session->userdata('lang')) ? $this->session->userdata('lang') :"en";
    $this->load->library("gtrans/gtrans",["lang" => $languange]);

    $this->load->model("menu_model");
    
    $this->system_model->checkSession(14);
    $this->listMenu = $this->menu_model->list_menu();
  }

  function index(){
    load_model(["area_model"]);

    $sqlArea = $this->area_model->getAll();
    $data['dataArea'] = $sqlArea;
    $parentViewData = [
      "title"   => $this->gtrans->line("User Activity Report"),  // title page
      "content" => "report/user_activity",  // content view
      "viewData"=> $data,
      "listMenu"=> $this->listMenu,
      "externalCSS" => [
        base_url("asset/plugins/daterangepicker3.0.5/daterangepicker.css"),
        base_url("asset/plugins/pace/pace-1.0.2/templates/pace-theme-big-counter.tmpl.css"),
        base_url("asset/template/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css")
      ],
      "externalJS"  => [
        base_url("asset/template/bower_components/moment/min/moment.min.js"),
        base_url("asset/plugins/daterangepicker3.0.5/daterangepicker.js"),
        base_url("asset/template/bower_components/datatables.net/js/jquery.dataTables.min.js"),
        base_url("asset/template/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js")
      ]
    ];

    $this->load->view("layouts/main",$parentViewData);
    $this->gtrans->saveNewWords();
  }

  function loadDataActivity()
  {
    load_model(["user_model"]);
    $appid   = $this->session->userdata("ses_appid");
    $reservation = $this->input->post("reservation");
    $arrPeriode  = explode(" - ",$reservation);

    $datestart   = date("Y-m-d 00:00:00",strtotime($arrPeriode[0]));
    $dateend     = date("Y-m-d 00:00:00",strtotime($arrPeriode[1]));

    $draw   = $_REQUEST['draw'];
		$length = $_REQUEST['length'];
		$start  = $_REQUEST['start'];
		$search = $_REQUEST['search']["value"];

    $allRecord  = $this->user_model->countAllActivity($appid);
    $sql = $this->user_model->getActivity($datestart,$dateend,$start,$length,$appid);
    $sqlWithoutLimit = $this->user_model->getActivity($datestart,$dateend,"","",$appid);
    $output['recordsTotal']    = $allRecord;
    $output['recordsFiltered'] = $sqlWithoutLimit->num_rows();

    $no  = $start;
    $output['data'] = array();
    foreach ($sql->result() as $row) {
      $no++;
      $output['data'][] = array(
        textCenter($no),
        $row->user_fullname,
        $row->activity_timestamp,
        Ucwords($row->menu),
        textCenter($row->activity_type)
      );
		}
    echo json_encode($output);
  }

  function reportPrint()
  {
    load_model(["user_model"]);
    $reservation = $_GET["reservation"];
    $arrPeriode  = explode(" - ",$reservation);
    $datestart   = date("Y-m-d 00:00:00",strtotime($arrPeriode[0]));
    $dateend     = date("Y-m-d 00:00:00",strtotime($arrPeriode[1]));

    $appid    = $this->session->userdata("ses_appid");

    $sql = $this->user_model->getActivity($datestart,$dateend,"","",$appid);
    $no  = 0;
    $this->table->set_template($this->table_print);
    $this->table->set_heading(
      ["width"=>"10%", "data"=>"No"],
      ["data" => "User Name", "class" => "text-center"],
      ["data" => "Time Activity", "class" => "text-center"],
      ["data" => "Menu", "class" => "text-center"],
      ["data" => "Action", "class" => "text-center"]
    );

    foreach ($sql->result() as $row) {
      $no++;
      $this->table->add_row(
        ["data" => $no,"align"=>"center"],
        $row->user_fullname,
        $row->activity_timestamp,
        Ucwords($row->menu),
        $row->activity_type
      );
    }

    $data["title"] = "Report User Activity";
    $data["subtitle"] = "Activity Date Range : ".$reservation;
    $data["tabel"] = ($sql->num_rows()>0) ? $this->table->generate() : '<p style="text-align:center">No Data Found!</p>';
    $data["type"]  = "print";
    $this->load->view("report/print_layout",$data);
  }
}
