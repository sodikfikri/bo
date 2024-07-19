<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Dashboard aplikasi beserta handle data request
 */
class Dashboard extends CI_controller
{
  var $listMenu = "";
  var $timestamp;
  var $tabel_template  = array(
        'table_open'            => '<table width="100%" class="table table-bordered table-stripped" >',
        'table_close'           => '</table>'
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
    
    // memanggil list menu harus load library gtrans di atasnya dulu

    $this->listMenu = $this->menu_model->list_menu();

  }

  function login(){
    redirect("login");
  }

  function index()
  {
    $this->system_model->checkSession(1);
    load_model(['area_model']);
    // view
    // load main view yang isinya template
    // nah di dalam view main template itulah ada fungsi untuk load request view
    // di view juga ada tempat untuk load file js, maupun library custom beberapa file
    $addons = $this->session->userdata("activeaddons");
    $arrAddons = $this->session->userdata("infoAddons");
    $infoSubscription = $this->session->userdata("sessSubscription");
	$deviceLicense = '';
	foreach ($arrAddons as $rows) {
    try {
      if (strpos($rows['name'], "InAct") !== false) {
        $deviceLicense += $rows['qty'];
      }
    } catch (\Throwable $th) {
      continue;
    }
	}
    if(empty($addons['machinelicense']) AND empty($addons['machinelicenseflash']) AND empty($deviceLicense)){
      $addonsAlert = '<a href="'.base_url('addons').'" ><div class="callout callout-danger">
                              <p>You Don`t have machine license to operate this app, please click here for do that!</p>
                            </div></a>';
    }else{
      $addonsAlert = '';
    }
    $data['addonsAlert'] = $addonsAlert;
    $sqlArea = $this->area_model->getAll();
    $data['dataArea']    = $sqlArea;
    if(!empty($this->session->userdata("msgCommandExist")) && $this->session->userdata("msgCommandExist")=="yes"){
      $data["msgCommand"] = "yes"; 
    }
    $parentViewData = [
      "title"   => "Dashboard",  // title page
      "content" => "dashboard",  // content view
      "viewData"=> $data,
      "listMenu"=> $this->listMenu,
      "externalCSS" => [
        base_url("asset/plugins/daterangepicker3.0.5/daterangepicker.css"),
        base_url("asset/plugins/pace/pace-1.0.2/templates/pace-theme-big-counter.tmpl.css")
      ],
      "externalJS"  => [
        //"https://cdn.jsdelivr.net/npm/sweetalert2@8",
        "//cdn.jsdelivr.net/npm/sweetalert2@11",
        base_url("asset/plugins/pace/pace-1.0.2/pace.min.js"),
        base_url("asset/template/bower_components/chart.js/Chart.js"),
        base_url("asset/js/dashboard.js"),
        base_url("asset/template/bower_components/moment/min/moment.min.js"),
        base_url("asset/plugins/daterangepicker3.0.5/daterangepicker.js")

      ]
    ];

    $msgAddons = '<div class="callout callout-danger">';

    $sevenDaysAgain = date("Y-m-d H:i:s",strtotime("+7 day"));
    $addonsExpired  = false;
    
    foreach ($arrAddons as $row) {
      if($row["expired"] <= $sevenDaysAgain){
        $msgAddons    .= $row['name'].' will be expired at '.$row['expired'];
        $addonsExpired = true;
      }
      
    }

    $msgAddons .= '</div>';

    if($addonsExpired==true){
      $parentViewData["msgAddons"] = $msgAddons;
    }
    
    $this->load->view("layouts/main",$parentViewData);
    $this->gtrans->saveNewWords();
  }

  function getDataResign(){
    $this->system_model->checkSession(1);
    load_model(["employee_model"]);
    $appid  = $this->session->userdata("ses_appid");
    $periode= $this->input->post("periode");
    $area   = $this->input->post("area");
    $cabang = $this->input->post("cabang");

    $arrPeriode = explode(" - ",$periode);

    $from       = date("Y-m-d",strtotime($arrPeriode[0]));
    $to         = date("Y-m-d",strtotime($arrPeriode[1]."+1 days"));

    $period     = new DatePeriod(
		 new DateTime($from),
		 new DateInterval('P1D'),
		 new DateTime($to)
		);

    $label = [];
    $value = [];
    foreach ($period as $date) {
      $completeDate = $date->format('Y-m-d');
      $total   = $this->employee_model->countResign($completeDate,$area,$cabang,$appid);
      $label[] = $completeDate;
      $value[] = $total;
    }

    echo json_encode([
      "label" => $label,
      "value" => $value
    ]);
  }

  function getDataMutationIn(){
    $this->system_model->checkSession(1);
    load_model(["employeemutation_model"]);
    $appid  = $this->session->userdata("ses_appid");
    $periode= $this->input->post("periode");
    $area   = $this->input->post("area");
    $cabang = $this->input->post("cabang");

    $arrPeriode = explode(" - ",$periode);

    $from       = date("Y-m-d",strtotime($arrPeriode[0]));
    $to         = date("Y-m-d",strtotime($arrPeriode[1]."+1 days"));

    $period     = new DatePeriod(
		 new DateTime($from),
		 new DateInterval('P1D'),
		 new DateTime($to)
		);

    $label = [];
    $value = [];
    foreach ($period as $date) {
      $completeDate = $date->format('Y-m-d');
      $total   = $this->employeemutation_model->countMutationIn($completeDate,$area,$cabang,$appid);
      $label[] = $completeDate;
      $value[] = $total;
    }

    echo json_encode([
      "label" => $label,
      "value" => $value
    ]);
  }
  function getDataMutationOut(){
    $this->system_model->checkSession(1);
    load_model(["employeemutation_model"]);
    $appid  = $this->session->userdata("ses_appid");
    $periode= $this->input->post("periode");
    $area   = $this->input->post("area");
    $cabang = $this->input->post("cabang");

    $arrPeriode = explode(" - ",$periode);

    $from       = date("Y-m-d",strtotime($arrPeriode[0]));
    $to         = date("Y-m-d",strtotime($arrPeriode[1]."+1 days"));

    $period     = new DatePeriod(
		 new DateTime($from),
		 new DateInterval('P1D'),
		 new DateTime($to)
		);

    $label = [];
    $value = [];
    foreach ($period as $date) {
      $completeDate = $date->format('Y-m-d');
      $total   = $this->employeemutation_model->countMutationOut($completeDate,$area,$cabang,$appid);
      $label[] = $completeDate;
      $value[] = $total;
    }

    echo json_encode([
      "label" => $label,
      "value" => $value
    ]);
  }

  function getLocationReview(){
    $this->system_model->checkSession(1);
    load_model(["cabang_model"]);
    $appid  = $this->session->userdata("ses_appid");
    $area   = $this->input->post("area");
    $cabang = $this->input->post("cabang");
    $appid  = $this->session->userdata("ses_appid");
    $dataLocation = $this->cabang_model->getLocationReview($area,$cabang,$appid);

    $this->table->set_template($this->tabel_template);
    $this->table->set_heading(
      [
        "class" => "text-center",
        "data"=> $this->gtrans->line("Area")
      ],
      [
        "class" => "text-center",
        "data"=> $this->gtrans->line("Branch")
      ],
      [
        "class" => "text-center",
        "data"=> $this->gtrans->line("Total Device")
      ],
      [
        "class" => "text-center",
        "data"=> $this->gtrans->line("Total Employee")
      ]
    );
    foreach ($dataLocation as $row) {
      $this->table->add_row(
        $row->area_name,
        $row->cabang_name,
        [
          "class"=>"text-right",
          "data" => $row->totalDevice
        ],
        [
          "class"=>"text-right",
          "data" => $row->totalEmployee
        ]
      );
    }
    $this->gtrans->saveNewWords();
    $output = $this->table->generate();
    echo json_encode($output);
  }
}
