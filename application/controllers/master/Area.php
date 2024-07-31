<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Area extends CI_Controller
{
  var $listMenu = "";
  var $tabel_template  = array(
        'table_open'            => '<table class="table table-bordered table-stripped" id="datatable">',
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
    
    $this->load->model("area_model");
	$this->load->model("cabang_model");
	$this->load->model("employee_model");
    $this->system_model->checkSession(4);
    $this->listMenu = $this->menu_model->list_menu();
    // bahasa
  }

  function index(){
    $this->load->library("form_validation");
    $this->load->library("encryption_org");

    $dataArea = $this->area_model->getAll();
    $this->table->set_template($this->tabel_template);
    $this->table->set_heading(
      ["data"=> "No","class"=>"text-center"],
      ["data"=> $this->gtrans->line("Code"),"class"=>"text-center"],
      ["data"=> $this->gtrans->line("Name"),"class"=>"text-center"],
      ["data"=> $this->gtrans->line("Description"),"class"=>"text-center"],
      ["data"=> $this->gtrans->line("Option"),"class"=>"text-center"]);
    $no = 0;
    foreach ($dataArea as $row) {
      $no++;
      $encId = $this->encryption_org->encode($row->area_id);
      $encCode  = base64_encode($row->area_code);
      $encName  = base64_encode($row->area_name);
      $encDescription = base64_encode($row->area_keterangan);
      $encMethod = base64_encode($row->presence_method);
      $encMode = base64_encode($row->presence_mode);
      $btnEdit = '<i style="cursor:pointer" class="fa fa-edit fa-lg color-primary" onclick="edit(\''.$encId.'\',\''.$encCode.'\',\''.$encName.'\',\''.$encDescription.'\',\''.$encMethod.'\',\''.$encMode.'\')"></i>';
      $option  = $btnEdit.' <span class="text-red" style="cursor:pointer" onclick="delArea(\''.$encId.'\','.$row->totalActiveBranch.')"><i class="fa fa-trash fa-lg "></i></span>';
      $this->table->add_row(
        textCenter($no),
        textCenter($row->area_code),
        $row->area_name,
        $row->area_keterangan,
        ["data" => $option, "class"=>"text-center"]
      );
    }

    if(!empty($this->session->userdata("ses_msg"))){
      $msg = $this->session->userdata("ses_msg");
      $data["notif"] =createNotif($msg['type'],$msg['header'],$msg['msg']);
      $this->session->set_userdata("ses_msg");
    }

    $data["areaTable"] = $this->table->generate();
    $parentViewData = [
      "title"   => $this->gtrans->line("Master Area"),  // title page
      "content" => "master/area",  // content view
      "viewData"=> $data,
      "listMenu"=> $this->listMenu,
      "externalCSS" => [
        base_url("asset/template/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css")
      ],
      "externalJS" => [
        base_url("asset/template/bower_components/datatables.net/js/jquery.dataTables.min.js"),
        base_url("asset/template/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"),
        "https://cdn.jsdelivr.net/npm/sweetalert2@8",
        base_url("asset/js/checkCode.js"),
      ],
      "varJS" => ["url" => base_url()]
    ];
    $this->load->view("layouts/main",$parentViewData);
    $this->gtrans->saveNewWords();
  }

  function saveArea(){
    $areacode = $this->input->post("areacode");
    $areaname = $this->input->post("areaname");
    $areadesc = $this->input->post("areadesc");
	$method   = $this->input->post("method");
	$strMethod= !empty($method) ? implode("|", $method) : "";
    $mode = $this->input->post("presence_mode");
    $encId       = $this->input->post("id");

    $dataInsert = [
      "area_code" => $areacode,
      "area_name" => $areaname,
      "area_keterangan" => $areadesc,
      "presence_method" => $strMethod,
      "presence_mode" => $mode
    ];
	$dataUpdate = [
      "presence_method" => $strMethod,
      "presence_mode" => $mode
    ];
	$dataUpdateEmp = [
      "tbemployee.presence_method" => $strMethod,
      "tbemployee.presence_mode" => $mode
    ];
    if($encId==""){
      $res = $this->area_model->insert($dataInsert);
      if($res){
        setActivity("master area","add");
        $this->session->set_userdata("ses_msg",["type"=>"success","header"=>"success","msg"=> $this->gtrans->line("Area has been added successfully")."!"]);
        $this->gtrans->saveNewWords();
        redirect("master-area");
      }
    }else{
      $this->load->library("encryption_org");
      $id = $this->encryption_org->decode($encId);
      $res = $this->area_model->update($dataInsert,$id);
	  //$resC = $this->cabang_model->updateMethodCabang($dataUpdate,$id);
	  //$resE = $this->employee_model->updateMethodEmployee($dataUpdateEmp,$id);

      if($res){
        setActivity("master area","edit");
        $this->session->set_userdata("ses_msg",["type"=>"success","header"=>"success","msg"=> $this->gtrans->line("Area has been updated successfully")."!"]);
        $this->gtrans->saveNewWords();
        redirect("master-area");
      }
    }
  }
  function deleteArea($encId){
    $this->load->library("encryption_org");
    $id  = $this->encryption_org->decode($encId);
    $res = $this->area_model->delete($id);
    if($res){
      setActivity("master area","delete");
      $this->session->set_userdata("ses_msg",["type"=>"success","header"=>"success","msg"=> $this->gtrans->line("Area has been deleted successfully")."!"]);
      $this->gtrans->saveNewWords();
      redirect("master-area");
    }
  }

  function checkCodeExists(){
    load_model(["area_model"]);
    load_library(["encryption_org"]);
    $areaCode = $this->input->post("code");
    $areaid   = $this->encryption_org->decode($this->input->post("entityID"));
    $appid    = $this->session->userdata("ses_appid");
    $codeExists = $this->area_model->isCodeExists($areaCode,$areaid,$appid);
    if($codeExists==true){
      echo "exists";
    }else{
      echo "notExists";
    }
  }

  function checkNameExists(){
    load_model(["area_model"]);
    load_library(["encryption_org"]);
    $areaName = $this->input->post("code");
    $areaid   = $this->encryption_org->decode($this->input->post("entityID"));
    $appid    = $this->session->userdata("ses_appid");
    $nameExists = $this->area_model->isNameExists($areaName,$areaid,$appid);

    if($nameExists==true){
      echo "exists";
    }else{
      echo "notExists";
    }
  }
}
