<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Notification extends CI_Controller
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
    $this->timestamp = date("Y-m-d H:i:s");
    $this->load->model("menu_model");
    
    $this->system_model->checkSession(1);
    $this->listMenu = $this->menu_model->list_menu();
  }

  function index(){
    $this->load->model("notif_model");
    $appid = $this->session->userdata("ses_appid");
    $this->notif_model->closeAllNotif($appid);

    $this->db->order_by("notif_id","DESC");
    $sqlNotif = $this->notif_model->get($appid);
    $this->table->set_template($this->tabel_template);
    $this->table->set_heading(
      "No",
      $this->gtrans->line("Title"),
      $this->gtrans->line("Description"),
      $this->gtrans->line("Date"));
    $no = 0;

    foreach ($sqlNotif->result() as $row) {
      $no++;
      $this->table->add_row($no,$row->notif_header,html_entity_decode($row->notif_content),date("d-m-Y",strtotime($row->date_create)));
    }

    $data["tableDate"] = $this->table->generate();
    $parentViewData = [
      "title"   => "Notification",  // title page
      "content" => "notification",  // content view
      "viewData"=> $data,
      "listMenu"=> $this->listMenu,
      "externalCSS" => [
        base_url("asset/plugins/daterangepicker3.0.5/daterangepicker.css")
      ],
      "externalJS"  => [
        base_url("asset/template/bower_components/chart.js/Chart.js"),
        base_url("asset/template/bower_components/moment/min/moment.min.js")
      ]
    ];

    $this->load->view("layouts/main",$parentViewData);
  }
}
