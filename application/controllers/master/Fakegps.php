<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Fakegps extends CI_Controller
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
    
    $this->load->model("fakegps_model");
    $this->system_model->checkSession(4);
    $this->listMenu = $this->menu_model->list_menu();
    // bahasa
  }

  function index(){
    $this->load->library("form_validation");
    $this->load->library("encryption_org");

    $dataFakegps = $this->fakegps_model->getAll();
    $this->table->set_template($this->tabel_template);
    $this->table->set_heading(
      ["data"=> "No","class"=>"text-center"],
      ["data"=> $this->gtrans->line("Code"),"class"=>"text-center"],
      ["data"=> $this->gtrans->line("Name"),"class"=>"text-center"],
      ["data"=> $this->gtrans->line("Description"),"class"=>"text-center"],
      ["data"=> $this->gtrans->line("Option"),"class"=>"text-center"]);
    $no = 0;
    foreach ($dataFakegps as $row) {
      $no++;
      $encId = $this->encryption_org->encode($row->fakegps_id);
      $encCode  = base64_encode($row->fakegps_code);
      $encName  = base64_encode($row->fakegps_name);
      $encDescription = base64_encode($row->fakegps_keterangan);
      $btnEdit = '<i style="cursor:pointer" class="fa fa-edit fa-lg color-primary" onclick="edit(\''.$encId.'\',\''.$encCode.'\',\''.$encName.'\',\''.$encDescription.'\')"></i>';
      $option  = $btnEdit.' <span class="text-red" style="cursor:pointer" onclick="delFakegps(\''.$encId.'\')"><i class="fa fa-trash fa-lg "></i></span>';
      $this->table->add_row(
        textCenter($no),
        textCenter($row->fakegps_code),
        $row->fakegps_name,
        $row->fakegps_keterangan,
        ["data" => $option, "class"=>"text-center"]
      );
    }

    if(!empty($this->session->userdata("ses_msg"))){
      $msg = $this->session->userdata("ses_msg");
      $data["notif"] =createNotif($msg['type'],$msg['header'],$msg['msg']);
      $this->session->set_userdata("ses_msg");
    }

    $data["fakegpsTable"] = $this->table->generate();
    $parentViewData = [
      "title"   => $this->gtrans->line("Master Fake GPS"),  // title page
      "content" => "master/fakegps",  // content view
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

  function saveFakegps(){
    $fakegpscode = $this->input->post("fakegpscode");
    $fakegpsname = $this->input->post("fakegpsname");
    $fakegpsdesc = $this->input->post("fakegpsdesc");
    $encId       = $this->input->post("id");

    $dataInsert = [
      "fakegps_code" => $fakegpscode,
      "fakegps_name" => $fakegpsname,
      "fakegps_keterangan" => $fakegpsdesc
    ];
    if($encId==""){
      $res = $this->fakegps_model->insert($dataInsert);
      if($res){
        setActivity("master fakegps","add");
        $this->session->set_userdata("ses_msg",["type"=>"success","header"=>"success","msg"=> $this->gtrans->line("Fake GPS has been added successfully")."!"]);
        $this->gtrans->saveNewWords();
        redirect("master-fakegps");
      }
    }else{
      $this->load->library("encryption_org");
      $id = $this->encryption_org->decode($encId);
      $res = $this->fakegps_model->update($dataInsert,$id);

      if($res){
        setActivity("master fakegps","edit");
        $this->session->set_userdata("ses_msg",["type"=>"success","header"=>"success","msg"=> $this->gtrans->line("Fake GPS has been updated successfully")."!"]);
        $this->gtrans->saveNewWords();
        redirect("master-fakegps");
      }
    }
  }
  function deleteFakegps($encId){
    $this->load->library("encryption_org");
    $id  = $this->encryption_org->decode($encId);
    $res = $this->fakegps_model->delete($id);
    if($res){
      setActivity("master fakegps","delete");
      $this->session->set_userdata("ses_msg",["type"=>"success","header"=>"success","msg"=> $this->gtrans->line("Fake GPS has been deleted successfully")."!"]);
      $this->gtrans->saveNewWords();
      redirect("master-fakegps");
    }
  }

  function checkCodeExists(){
    load_model(["fakegps_model"]);
    load_library(["encryption_org"]);
    $fakegpsCode = $this->input->post("code");
    $fakegpsid   = $this->encryption_org->decode($this->input->post("entityID"));
    $appid    = $this->session->userdata("ses_appid");
    $codeExists = $this->fakegps_model->isCodeExists($areaCode,$areaid,$appid);
    if($codeExists==true){
      echo "exists";
    }else{
      echo "notExists";
    }
  }

  function checkNameExists(){
    load_model(["fakegps_model"]);
    load_library(["encryption_org"]);
    $fakegpsName = $this->input->post("code");
    $fakegpsid   = $this->encryption_org->decode($this->input->post("entityID"));
    $nameExists = $this->fakegps_model->isNameExists($fakegpsName,$fakegpsid);

    if($nameExists==true){
      echo "exists";
    }else{
      echo "notExists";
    }
  }
}
