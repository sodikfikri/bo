<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class History_intrax extends CI_Controller
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
    
    $this->load->model("area_model");
	$this->load->model("cabang_model");
	$this->load->model("employee_model");
	$this->load->model("inoutmobile_model");
    
    $this->system_model->checkSession(24);
    $this->listMenu = $this->menu_model->list_menu();
  }

  function index()
  {
    load_model(["area_model"]);
    $sqlArea = $this->area_model->getAll();
    $data['dataArea'] = $sqlArea;
    $parentViewData = [
      "title"   => $this->gtrans->line("History Log Intrax"),  // title page
      "content" => "report/history_intrax",  // content view
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
    $sArea       = !empty($this->input->post("sArea")) ? $this->input->post("sArea") : "";
    $sCabang     = $this->input->post("sCabang");
    $sName       = $this->input->post("sName");

    $draw   = $_REQUEST['draw'];
		$length = $_REQUEST['length'];
		$start  = $_REQUEST['start'];
		//$search = $_REQUEST['search']["value"];

		$output = array();
		$output['draw'] = $draw;
		$output['data'] = array();

    $arrPeriode = explode(" - ",$reservation);
    $datestart  = date("Y-m-d",strtotime($arrPeriode[0]));
    $dateend    = date("Y-m-d",strtotime($arrPeriode[1]));

    $this->db->where("B.employee_full_name is NOT null",null,false);
    $this->db->where("B.employee_account_no is NOT null",null,false);
    $allRecord  = $this->checkinout_model->countActive($appid,$datestart,$dateend,$sArea,$sCabang);

    $output['recordsTotal'] = $allRecord;

    $this->db->group_start();
    $this->db->like("B.employee_full_name",$sName);
    $this->db->or_like("B.employee_account_no",$sName);
    //$this->db->or_where("B.employee_full_name is null",null,false);
    //$this->db->or_where("B.employee_account_no is null",null,false);
    $this->db->group_end();
    $sql = $this->checkinout_model->getActive($datestart,$dateend,$sArea,$sCabang,$start,$length,$appid);
    $this->db->group_start();
    $this->db->like("B.employee_full_name",$sName);
    $this->db->or_like("B.employee_account_no",$sName);
    //$this->db->or_where("B.employee_full_name is null",null,false);
    //$this->db->or_where("B.employee_account_no is null",null,false);
    $this->db->group_end();
    $sqlWithoutLimit = $this->checkinout_model->getActive($datestart,$dateend,$sArea,$sCabang,"","",$appid);
    // echo $this->db->last_query();
    $output['recordsFiltered'] = $sqlWithoutLimit->num_rows();
    

    foreach ($sql->result() as $row) {
      
      $output['data'][] = array(
        ($row->log_image!="")?'<img src="'.base_url("sys_upload/log_image/".$row->log_image).'" width="90px">':'',
        $row->area_name.' <i class="fa fa-long-arrow-right "></i> '.$row->cabang_name.'<br>'.
        '('.$row->checkinout_SN.')',
        $row->checkinout_employeecode,
        $row->employee_full_name,
        $row->checkinout_datetime,
        '<div style="text-align:center">'.$row->checkinout_code.'</div>',
        '<div style="text-align:center">'.$row->checkinout_verification_mode.'</div>',
        '<div style="text-align:center">'.$row->temperature.'</div>',
        '<div style="text-align:center">'.$row->mask_flag.'</div>',
      );
		}
    echo json_encode($output);
  }

  function loadDataFromFinalTable()
  {
    load_model(["checkinout_model","employeeareacabang_model"]);
    $this->load->library("dbconnection");
    $conn = $this->dbconnection->connect();
	function get_nearest_timezone($cur_lat, $cur_long, $country_code = '') {
		$timezone_ids = ($country_code) ? DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $country_code)
										: DateTimeZone::listIdentifiers();

		if($timezone_ids && is_array($timezone_ids) && isset($timezone_ids[0])) {

			$time_zone = '';
			$tz_distance = 0;

			//only one identifier?
			if (count($timezone_ids) == 1) {
				$time_zone = $timezone_ids[0];
			} else {

				foreach($timezone_ids as $timezone_id) {
					$timezone = new DateTimeZone($timezone_id);
					$location = $timezone->getLocation();
					$tz_lat   = $location['latitude'];
					$tz_long  = $location['longitude'];

					$theta    = $cur_long - $tz_long;
					$distance = (sin(deg2rad($cur_lat)) * sin(deg2rad($tz_lat)))
					+ (cos(deg2rad($cur_lat)) * cos(deg2rad($tz_lat)) * cos(deg2rad($theta)));
					$distance = acos($distance);
					$distance = abs(rad2deg($distance));
					// echo '<br />'.$timezone_id.' '.$distance;

					if (!$time_zone || $tz_distance > $distance) {
						$time_zone   = $timezone_id;
						$tz_distance = $distance;
					}

				}
			}
			return  $time_zone;
		}
		return 'unknown';
	}

    $appid       = $this->session->userdata("ses_appid");
    $reservation = $this->input->post("reservation");
    $draw   = $_REQUEST['draw'];
    $length = $_REQUEST['length'];
    $start  = $_REQUEST['start'];
    //$search = $_REQUEST['search']["value"];

    $output = array();
	$output['draw'] = $draw;
	$output['data'] = array();

	$arrPeriode = explode(" - ",$reservation);
	$datestart  = date("Y-m-d",strtotime($arrPeriode[0]));
	$dateend    = date("Y-m-d",strtotime($arrPeriode[1]));
    
    $dataInout = $this->inoutmobile_model->getAllDataInOutMobile($datestart,$dateend,$appid);
	foreach ($dataInout as $row) {
      $encId = $this->encryption_org->encode($row->id);
	  if(!empty($row->image)){
		  $photo = '<img src="https://inact.azurewebsites.net/sys_upload/absen_image/'.$row->image.'" alt="absen-image" width="50px"></img>';
	  }else{
		  $photo = '';
	  }
	  $arrLocation = $this->employeeareacabang_model->getLocationName($row->employee_id,$appid);
      $location    = '';
	  $dataArea = [];
	  $i = 0;
	  $arrArea = explode("|",$this->session->userdata("ses_area"));
      foreach ($arrLocation as $rowLocation) {
		$dataArea[] = $rowLocation->area_id;
        $location .= $rowLocation->area_name.'<br>';
		if(in_array($rowLocation->area_id, $arrArea)){
		  $i++;
		}
      }
      $encCode  = base64_encode($row->id);
	  if($this->session->userdata("ses_status")=="admin_area"){
		if($i>0){
		  $no++;
		  $output['data'][] = array(
			textCenter($no),
			$row->checklog_address,
			$row->checklog_latitude.', '.$row->checklog_longitude,
			get_nearest_timezone($row->checklog_latitude,$row->checklog_longitude),
			$row->employee_id,
			$row->employee_full_name,
			$location,
			$row->checklog_date,
			$row->checklog_event,
			$photo
		  );
		}
	  } else {
		$no++;
		  $output['data'][] = array(
			textCenter($no),
			$row->checklog_address,
			$row->checklog_latitude.', '.$row->checklog_longitude,
			get_nearest_timezone($row->checklog_latitude,$row->checklog_longitude),
			$row->employee_id,
			$row->employee_full_name,
			$location,
			$row->checklog_date,
			$row->checklog_event,
			$photo
		  );
	  }
      
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
      ["data" => "<p class='table-content'>SN</p>","class" => "text-center"],
      ["data" => "<p class='table-content'>ACCOUNT NO</p>","class" => "text-center"],
      ["data" => "<p class='table-content'>NAME</p>","class" => "text-center"],
      ["data" => "<p class='table-content'>CHECKDATE</p>","class" => "text-center"],
      ["data" => "<p class='table-content'>CHECKTIME</p>","class" => "text-center"],
      ["data" => "<p class='table-content'>ABSEN CODE</p>","class" => "text-center"],
      ["data" => "<p class='table-content'>VERIFY CODE</p>","class" => "text-center"],
      ["data" => "<p class='table-content'>TEMPERATURE</p>","class" => "text-center"],
      ["data" => "<p class='table-content'>USE MASKER</p>","class" => "text-center"]
    );

    //if(!empty($area)){
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
            "data" => '<p class="table-content">'.$row->checkinout_SN.'</p>'
          ],
          [
            "style"=> "text-align:center",
            "data" => '<p class="table-content">'.$row->checkinout_employeecode.'</p>'
          ],
          $row->employee_full_name
          ,
          [
            "style"=> "text-align:center",
            "data" => '<p class="table-content">'.$arrDateTime[0].'</p>'
          ],
          [
            "style"=> "text-align:center",
            "data" => '<p class="table-content">'.$arrDateTime[1].'</p>'
          ],
          [
            "style"=> "text-align:center",
            "data" => '<p class="table-content">'.$row->checkinout_code.'</p>'
          ],
          [
            "style"=> "text-align:center",
            "data" => '<p class="table-content">'.$row->checkinout_verification_mode.'</p>'
          ],
          [
            "style"=> "text-align:center",
            "data" => '<p class="table-content">'.$row->temperature.'</p>'
          ],
          [
            "style"=> "text-align:center",
            "data" => '<p class="table-content">'.$row->mask_flag.'</p>'
          ]
        );
      }
    //}

    $data["title"] = "Report History Log ".$dateFrom." - ".$dateTo;
    $data["subtitle"] = "Area : ".$areaName.", Branch : ".$cabangName.", Name or No Account Have String : ".$term;
    
    /*
    if(empty($area)){
      $data["tabel"] = '<p style="text-align:center">No Data Found!</p>';
    }else{
      
    }
    */

    $data["tabel"] = ($sql->num_rows()>0) ? $this->table->generate() : '<p style="text-align:center">No Data Found!</p>';
    $data["type"]  = "print";
    $this->load->view("report/print_layout",$data);
  }


}
