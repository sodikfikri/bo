<?php

class Employee_resign extends CI_Controller
{
  var $listMenu = "";
  var $timestamp;
  var $tabel_template  = array(
        'table_open'            => '<table width="100%" class="table table-bordered table-stripped" >',
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
    
    $this->load->model("employee_model");
    $this->load->model("employeeareacabang_model");
    $this->system_model->checkSession(22);
    // memanggil list menu harus load library gtrans di atasnya dulu

    $this->listMenu = $this->menu_model->list_menu();
  }

  function index(){
    
    $appid   = $this->session->userdata("ses_appid");
    
    $data    = [];

    $parentViewData = [
      "title"   => "Report Employee Resign",  // title page
      "content" => "report/resign_report",  // content view
      "viewData"=> $data,
      "listMenu"=> $this->listMenu,
      "externalCSS" => [
        base_url("asset/plugins/daterangepicker3.0.5/daterangepicker.css"),
        base_url("asset/template/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css"),
        base_url("asset/template/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css"),
        base_url("asset/plugins/pace/pace-1.0.2/templates/pace-theme-big-counter.tmpl.css")
      ],
      "externalJS"  => [
        "https://cdn.jsdelivr.net/npm/sweetalert2@8",
        base_url("asset/template/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"),
        base_url("asset/template/bower_components/moment/min/moment.min.js"),
        base_url("asset/plugins/daterangepicker3.0.5/daterangepicker.js"),
        base_url("asset/js/tooltip.min.js"),
        base_url("asset/template/bower_components/datatables.net/js/jquery.dataTables.min.js"),
        base_url("asset/template/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js")
      ]
    ];
    $this->gtrans->saveNewWords();
    $this->load->view("layouts/main",$parentViewData);
  }
  
  function getData(){

    $appid         = $this->session->userdata("ses_appid");
    $draw          = $_REQUEST['draw'];
    $record_length = $_REQUEST['length'];
    $record_start  = $_REQUEST['start'];
    $search        = $_REQUEST['search']["value"];

    $periode = $this->input->post("periode");
    $term    = $this->input->post("term");
    $arrPeriode = explode(" - ", $periode);
    
    $from = date("Y-m-d",strtotime($arrPeriode[0]));
    $to   = date("Y-m-d",strtotime($arrPeriode[1]));

    $allRecords      = $this->employee_model->countResignEmployee($appid);

    $this->db->like("employee_full_name",$term);
    $this->db->group_start();
    $this->db->where(" DATE(employee_resign_date) >= ", $from);
    $this->db->where(" DATE(employee_resign_date) <= ", $to);
    $this->db->group_end();

    $filteredRecords = $this->employee_model->countResignEmployee($appid);

    $output['recordsTotal']    = $allRecords;
    $output['recordsFiltered'] = $filteredRecords;

    $output['data'] = array();
    
    $this->db->like("employee_full_name",$term);
    $this->db->group_start();
    $this->db->where(" DATE(employee_resign_date) >= ", $from);
    $this->db->where(" DATE(employee_resign_date) <= ", $to);
    $this->db->group_end();

    if($record_length!="" && $record_start!=""){
      $this->db->limit($record_length,$record_start);
    }

    $sql = $this->employee_model->getResignEmployee($appid);
    //echo $this->db->last_query();
    $no = 0;
    foreach ($sql->result() as $row) {
      $no++;
      $arrLocation = $this->employeeareacabang_model->locationLeave($row->employee_id);
      $strLocation = "";
      foreach ($arrLocation->result() as $rowLocation) {
        $strLocation .= $rowLocation->area_name.'<i class="fa fa-long-arrow-right "></i>'.$rowLocation->cabang_name.'</br>';
      }

      $output['data'][] = [
        $no,
        $row->employee_account_no,
        $row->employee_full_name,
        date("Y-m-d",strtotime($row->employee_resign_date)),
        $strLocation
      ];
    }
    echo json_encode($output);
  }

  function printReport(){
    $periode = $_GET["reservation"];
    $term    = $_GET["term"];

    $arrPeriode = explode(" - ", $periode);
    
    $from = date("Y-m-d",strtotime($arrPeriode[0]));
    $to   = date("Y-m-d",strtotime($arrPeriode[1]));


    $this->load->model("employee_model");

    $appid = $this->session->userdata("ses_appid");

    $this->db->like("employee_full_name",$term);
    $this->db->group_start();
    $this->db->where(" DATE(employee_resign_date) >= ", $from);
    $this->db->where(" DATE(employee_resign_date) <= ", $to);
    $this->db->group_end();

    $sql   = $this->employee_model->getResignEmployee($appid);
    
    $this->table->set_template($this->table_print);
    $this->table->set_heading(
      ["data" => "NO","width"=>"10%","class" => "text-center"],
      ["data" => "Account No","class"=>"text-center"],
      ["data" => "Name","class"=>"text-center"],
      ["data" => "Resign Date","class"=>"text-center"]
    );

    $no = 0;
    foreach ($sql->result() as $row) {
      $no++;
      
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
        date("Y-m-d",strtotime($row->employee_resign_date)),
        
      );
    }

    $data["title"]    = "Report Employee Resign";
    $data["subtitle"] = "";

    $data["tabel"] = ($sql->num_rows()>0) ? $this->table->generate() : '<p style="text-align:center">No Data Found!</p>';
    $data["type"]  = "print";

    $this->load->view("report/print_layout",$data);
  }

}
