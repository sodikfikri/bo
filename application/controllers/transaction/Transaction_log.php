<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Transaction_log extends CI_controller
{
  var $tabel_template  = array(
        'table_open'            => '<table class="table table-bordered table-stripped" >',
        'table_close'           => '</table>'
	);
  var $now;
  function __construct()
  {
    parent::__construct();
    $this->load->model("system_model");
    $this->now = date("Y-m-d H:i:s");
    $languange = "english";

    $languange = !empty($this->session->userdata('lang')) ? $this->session->userdata('lang') :"en";
    $this->load->library("gtrans/gtrans",["lang" => $languange]);

    $this->load->model("menu_model");
    
    $this->system_model->checkSession(16);
    $this->listMenu = $this->menu_model->list_menu();
  }

  function index(){
    load_model(['area_model']);
    $sqlArea = $this->area_model->getAll();
    $data['dataArea']    = $sqlArea;
    $parentViewData = [
      "title"   => "Transaction Log",  // title page
      "content" => "transaction/transaction_log",  // content view
      "viewData"=> $data,
      "listMenu"=> $this->listMenu,
      "externalCSS" => [
        base_url("asset/plugins/daterangepicker3.0.5/daterangepicker.css"),
        base_url("asset/plugins/pace/pace-1.0.2/templates/pace-theme-big-counter.tmpl.css"),
        base_url("asset/template/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css")
      ],
      "externalJS"  => [
        base_url("asset/plugins/pace/pace-1.0.2/pace.min.js"),
        base_url("asset/template/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"),
        base_url("asset/template/bower_components/moment/min/moment.min.js"),
        base_url("asset/plugins/daterangepicker3.0.5/daterangepicker.js")
      ]
    ];

    $this->load->view("layouts/main",$parentViewData);
    $this->gtrans->saveNewWords();
  }

  function loadTransaction(){
    load_model(["historidownload_model"]);
    $selRange = $this->input->post("selRange");
    $arrRange = explode(" - ",$selRange);
    $from     = date("Y-m-d",strtotime($arrRange[0]));
    $to       = date("Y-m-d",strtotime($arrRange[1]."+1 days"));
    $appid    = $this->session->userdata("ses_appid");

    $this->db->order_by("historydownloadcheckinout_id","DESC");
    $sql  = $this->historidownload_model->getByDate($from,$to,$appid);

    $this->table->set_template($this->tabel_template);
    $this->table->set_heading(
      ["data" => $this->gtrans->line("File Status"), "class" => "text-center"],
      ["data" => $this->gtrans->line("Date Create"), "class" => "text-center"],
      ["data" => $this->gtrans->line("Filter Date"), "class" => "text-center"],
      ["data" => $this->gtrans->line("Record"), "class" => "text-center"]
    );

    foreach ($sql as $row) {
      $status = ($row->historydownloadcheckinout_status=="success") ? "<span style='color:green'>Downloaded</span>" : "<span style='color:red'>Not Downloaded</span>";
      $this->table->add_row(
        "Success",
        ["data" => $row->historydownloadcheckinout_date_create, "class" => "text-center"],
        ["data" => $row->filter_start." - ".$row->filter_end, "class" => "text-center"],
        ["data" => $row->historydownloadcheckinout_checkinout_count, "class" => "text-center"]
      );
    }

    $output = $this->table->generate();
    $this->gtrans->saveNewWords();
    echo json_encode($output);
  }

}
