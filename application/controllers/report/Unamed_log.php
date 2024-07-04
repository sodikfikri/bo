<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Unamed_log extends CI_Controller
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
    
    $this->system_model->checkSession(23);
    $this->listMenu = $this->menu_model->list_menu();
  }

  function index()
  {
    load_model(["area_model"]);
    $sqlArea = $this->area_model->getAll();
    $data['dataArea'] = $sqlArea;
    $parentViewData = [
      "title"   => $this->gtrans->line("History Log"),  // title page
      "content" => "report/unamed_log",  // content view
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
  function getVerificationType($code){
    // tidak bisa dispesifikasi
    return $code;
  }

  function loadDataLog()
  {
    load_model(["checkinout_model"]);
    $appid       = $this->session->userdata("ses_appid");
    $reservation = $this->input->post("reservation");
    $sArea       = !empty($this->input->post("sArea")) ? $this->input->post("sArea") : "-";
    $sCabang     = $this->input->post("sCabang");
    $useMasker   = $this->input->post("sUseMasker");

    $draw   = $_REQUEST['draw'];
		$length = $_REQUEST['length'];
		$start  = $_REQUEST['start'];
		$search = $_REQUEST['search']["value"];

		$output = array();
		$output['draw'] = $draw;
		$output['data'] = array();

    $arrPeriode = explode(" - ",$reservation);
    $datestart  = date("Y-m-d",strtotime($arrPeriode[0]));
    $dateend    = date("Y-m-d",strtotime($arrPeriode[1]));

    $this->db->where("B.employee_account_no is null",null,false);

    $allRecord  = $this->checkinout_model->countActive($appid,$datestart,$dateend,$sArea,$sCabang);

    $output['recordsTotal'] = $allRecord;
    if($useMasker!="all"){
      $this->db->where("A.mask_flag",$useMasker);
    }
    $this->db->where("B.employee_account_no is null",null,false);
    $this->db->group_start();
    $this->db->like("B.employee_full_name",$search);
    $this->db->or_like("B.employee_account_no",$search);
    $this->db->or_where("B.employee_full_name is null",null,false);
    $this->db->or_where("B.employee_account_no is null",null,false);
    $this->db->group_end();
    $sql = $this->checkinout_model->getActive($datestart,$dateend,$sArea,$sCabang,$start,$length,$appid);
    
    if($useMasker!="all"){
      $this->db->where("A.mask_flag",$useMasker);
    }
    $this->db->where("B.employee_account_no is null",null,false);
    $this->db->group_start();
    $this->db->like("B.employee_full_name",$search);
    $this->db->or_like("B.employee_account_no",$search);
    $this->db->or_where("B.employee_full_name is null",null,false);
    $this->db->or_where("B.employee_account_no is null",null,false);
    $this->db->group_end();
    $sqlWithoutLimit = $this->checkinout_model->getActive($datestart,$dateend,$sArea,$sCabang,"","",$appid);
    $output['recordsFiltered'] = $sqlWithoutLimit->num_rows();
    
    foreach ($sql->result() as $row) {
      
      $output['data'][] = array(
        ($row->log_image!="")?'<img src="'.base_url("sys_upload/log_image/".$row->log_image).'" width="90px">':'',
        $row->checkinout_SN,
        $row->checkinout_datetime,
        '<div style="text-align:center">'.$row->checkinout_code.'</div>',
        '<div style="text-align:center">'.$row->checkinout_verification_mode.'</div>',
        '<div style="text-align:center">'.$row->temperature.'</div>',
        '<div style="text-align:center">'.($row->mask_flag==1?$this->gtrans->line("Yes"):$this->gtrans->line("No")).'</div>',
      );
		}
    echo json_encode($output);
  }

  function reportPrint()
  {
    load_model(["checkinout_model","area_model","cabang_model"]);

    $reservation = $_GET["reservation"];
    $area = $_GET["area"];
    $cabang = $_GET["cabang"];
    $term   = $_GET['term'];
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

    $arrPeriode = explode(" - ",$reservation);
    $dateFrom = date("Y-m-d",strtotime($arrPeriode[0]));
    $dateTo   = date("Y-m-d",strtotime($arrPeriode[1]));
    $appid    = $this->session->userdata("ses_appid");
    $this->table->set_template($this->table_print);
    $this->table->set_heading(
      ["data" => "SN","class" => "text-center"],
      ["data" => "ACCOUNT NO","class" => "text-center"],
      ["data" => "NAME","class" => "text-center"],
      ["data" => "CHECKDATE","class" => "text-center"],
      ["data" => "CHECKTIME","class" => "text-center"],
      ["data" => "ABSEN CODE","class" => "text-center"],
      ["data" => "VERIFY CODE","class" => "text-center"],
      ["data" => "TEMPERATURE","class" => "text-center"],
      ["data" => "USE MASKER","class" => "text-center"]
    );

    if(!empty($area)){
      if(!empty($term)){
        $this->db->group_start();
        $this->db->like("B.employee_full_name",$term);
        $this->db->or_like("B.employee_account_no",$term);
        $this->db->or_where("B.employee_full_name is null",null,false);
        $this->db->or_where("B.employee_account_no is null",null,false);
        $this->db->group_end();
      }
      $sql = $this->checkinout_model->getActive($dateFrom,$dateTo,$area,$cabang,"","",$appid);

      $no = 0;
      foreach ($sql->result() as $row) {
        $no++;
        $arrDateTime = explode(" ",$row->checkinout_datetime);
        $labelVerifType = ' ('.$this->getVerificationType($row->checkinout_verification_mode).')';

        $this->table->add_row(
          [
            "style"=> "text-align:center",
            "data" => $row->checkinout_SN
          ],
          [
            "style"=> "text-align:center",
            "data" => $row->checkinout_employeecode
          ],
          $row->employee_full_name
          ,
          [
            "style"=> "text-align:center",
            "data" => $arrDateTime[0]
          ],
          [
            "style"=> "text-align:center",
            "data" => $arrDateTime[1]
          ],
          [
            "style"=> "text-align:center",
            "data" => $row->checkinout_code
          ],
          [
            "style"=> "text-align:center",
            "data" => $row->checkinout_verification_mode
          ],
          [
            "style"=> "text-align:center",
            "data" => $row->temperature
          ],
          [
            "style"=> "text-align:center",
            "data" => $row->mask_flag
          ]
        );
      }
    }

    $data["title"] = "Report History Log ".$dateFrom." - ".$dateTo;
    $data["subtitle"] = "Area : ".$areaName.", Branch : ".$cabangName.", Name or No Account Have String : ".$term;
    if(empty($area)){
      $data["tabel"] = '<p style="text-align:center">No Data Found!</p>';
    }else{
      $data["tabel"] = ($sql->num_rows()>0) ? $this->table->generate() : '<p style="text-align:center">No Data Found!</p>';
    }
    $data["type"]  = "print";
    $this->load->view("report/print_layout",$data);
  }

}
