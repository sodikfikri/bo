<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Mutation_report extends CI_Controller
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
    
    $this->system_model->checkSession(13);
    $this->listMenu = $this->menu_model->list_menu();
  }

  function index()
  {
    load_model(["area_model"]);
    $sqlArea = $this->area_model->getAll();
    $data['dataArea'] = $sqlArea;
    $parentViewData = [
      "title"   => $this->gtrans->line("Mutation Report"),  // title page
      "content" => "report/mutation_report",  // content view
      "viewData"=> $data,
      "listMenu"=> $this->listMenu,
      "externalCSS" => [
        base_url("asset/plugins/daterangepicker3.0.5/daterangepicker.css"),
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

  function loadDataMutation(){
    load_model(["employeemutation_model"]);

    $draw   = $_REQUEST['draw'];
		$length = $_REQUEST['length'];
		$start  = $_REQUEST['start'];
		$search = $_REQUEST['search']["value"];

    $reservation = $this->input->post("reservation");
    $arrPeriode  = explode(" - ",$reservation);

    $datefrom = date("Y-m-d",strtotime($arrPeriode[0]));
    $dateto   = date("Y-m-d",strtotime($arrPeriode[1]));

    $appid    = $this->session->userdata("ses_appid");
    $allRecord                 = $this->employeemutation_model->countAll($datefrom, $dateto, $appid);
    $recordFilteredWithoutLimit= $this->employeemutation_model->getData($datefrom, $dateto, "", "", $appid);

    $output['recordsTotal']    = $allRecord;
    $output['recordsFiltered'] = $recordFilteredWithoutLimit->num_rows();

    $sql = $this->employeemutation_model->getData($datefrom,$dateto,$start,$length,$appid);
    $output['data'] = array();
    $no  = $start;

    foreach ($sql->result() as $row) {
      $arrSource = $this->employeemutation_model->getSourceLocation($row->employeemutation_id);
      $arrDestination = $this->employeemutation_model->destinationLocation($row->employeemutation_id);
      $txtSource = "";
      $txtDestination = "";
      foreach ($arrSource as $rowSource) {
        $txtSource .= $rowSource['area']." >> ".$rowSource['branch'].'<br>';
      }
      foreach ($arrDestination as $rowDestination) {
        $txtDestination .= $rowDestination['area']." >> ".$rowDestination['branch'].'<br>';
      }
      $no++;
      $output['data'][] = array(
        textCenter($no),
        date("Y-m-d",strtotime($row->employeemutation_effdt)),
        $row->employee_account_no,
        $row->employee_full_name,
        $txtSource,
        $txtDestination
      );
    }
    echo json_encode($output);
  }

  function reportPrint(){
    load_model(["employeemutation_model"]);
    $reservation = $_GET["reservation"];
    $arrPeriode  = explode(" - ",$reservation);

    $datefrom = date("Y-m-d",strtotime($arrPeriode[0]));
    $dateto   = date("Y-m-d",strtotime($arrPeriode[1]));

    $appid    = $this->session->userdata("ses_appid");

    $sql = $this->employeemutation_model->getData($datefrom,$dateto,"","",$appid);
    $no  = 0;
    $this->table->set_template($this->table_print);
    $this->table->set_heading(
      ["width"=>"10%","class"=>"text-center", "data"=>"No"],
      ["data" => "Effective Date","class" => "text-center"],
      ["data" => "Account No","class" => "text-center"],
      ["data" => "Employee Name","class" => "text-center"],
      ["data" => "Location Source","class" => "text-center"],
      ["data" => "Location Destination","class" => "text-center"]
    );

    foreach ($sql->result() as $row) {
      $arrSource = $this->employeemutation_model->getSourceLocation($row->employeemutation_id);
      $arrDestination = $this->employeemutation_model->destinationLocation($row->employeemutation_id);
      $txtSource = "";
      $txtDestination = "";
      foreach ($arrSource as $rowSource) {
        $txtSource .= $rowSource['area']." >> ".$rowSource['branch'].'<br>';
      }
      foreach ($arrDestination as $rowDestination) {
        $txtDestination .= $rowDestination['area']." >> ".$rowDestination['branch'].'<br>';
      }
      $no++;
      $this->table->add_row(
        ["data" => $no,"align"=>"center"],
        ["data" => date("Y-m-d",strtotime($row->employeemutation_effdt)),"align"=>"center"],
        ["data" => $row->employee_account_no,"align"=>"center"],
        $row->employee_full_name,
        $txtSource,
        $txtDestination
      );
    }
    $data["title"] = "Report Employee";
    $data["subtitle"] = "Effective Date Range : ".$reservation;
    $data["tabel"] = ($sql->num_rows()>0) ? $this->table->generate() : '<p style="text-align:center">No Data Found!</p>';
    $data["type"]  = "print";
    $this->load->view("report/print_layout",$data);
  }
}
