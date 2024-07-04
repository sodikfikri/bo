<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Ijin extends CI_Controller
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
    
    $this->load->model("ijin_model");
    $this->system_model->checkSession(4);
    $this->listMenu = $this->menu_model->list_menu();
    // bahasa
  }

  function index(){
    $this->load->library("form_validation");
    $this->load->library("encryption_org");

    $dataIjin = $this->ijin_model->getAll();
    $this->table->set_template($this->tabel_template);
    $this->table->set_heading(
      ["data"=> "No","class"=>"text-center"],
      ["data"=> $this->gtrans->line("Code"),"class"=>"text-center"],
      ["data"=> $this->gtrans->line("Name"),"class"=>"text-center"],
      ["data"=> $this->gtrans->line("Description"),"class"=>"text-center"],
      ["data"=> $this->gtrans->line("Option"),"class"=>"text-center"]);
    $no = 0;
    foreach ($dataIjin as $row) {
      $no++;
      $encId = $this->encryption_org->encode($row->ijin_id);
      $encCode  = base64_encode($row->ijin_code);
      $encName  = base64_encode($row->ijin_name);
      $encDescription = base64_encode($row->ijin_keterangan);
      $btnEdit = '<i style="cursor:pointer" class="fa fa-edit fa-lg color-primary" onclick="edit(\''.$encId.'\',\''.$encCode.'\',\''.$encName.'\',\''.$encDescription.'\')"></i>';
      $option  = $btnEdit.' <span class="text-red" style="cursor:pointer" onclick="delIjin(\''.$encId.'\')"><i class="fa fa-trash fa-lg "></i></span>';
      $this->table->add_row(
        textCenter($no),
        textCenter($row->ijin_code),
        $row->ijin_name,
        $row->ijin_keterangan,
        ["data" => $option, "class"=>"text-center"]
      );
    }

    if(!empty($this->session->userdata("ses_msg"))){
      $msg = $this->session->userdata("ses_msg");
      $data["notif"] =createNotif($msg['type'],$msg['header'],$msg['msg']);
      $this->session->set_userdata("ses_msg");
    }

    $data["ijinTable"] = $this->table->generate();
    $parentViewData = [
      "title"   => $this->gtrans->line("Master Ijin"),  // title page
      "content" => "master/ijin",  // content view
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

  function saveIjin(){
    $ijincode = $this->input->post("ijincode");
    $ijinname = $this->input->post("ijinname");
    $ijindesc = $this->input->post("ijindesc");
    $encId       = $this->input->post("id");

    $dataInsert = [
      "ijin_code" => $ijincode,
      "ijin_name" => $ijinname,
      "ijin_keterangan" => $ijindesc
    ];
    if($encId==""){
      $res = $this->ijin_model->insert($dataInsert);
      if($res){
        setActivity("master setting","add");
        $this->session->set_userdata("ses_msg",["type"=>"success","header"=>"success","msg"=> $this->gtrans->line("Ijin has been added successfully")."!"]);
        $this->gtrans->saveNewWords();
        redirect("master-setting");
      }
    }else{
      $this->load->library("encryption_org");
      $id = $this->encryption_org->decode($encId);
      $res = $this->ijin_model->update($dataInsert,$id);

      if($res){
        setActivity("master setting","edit");
        $this->session->set_userdata("ses_msg",["type"=>"success","header"=>"success","msg"=> $this->gtrans->line("Ijin has been updated successfully")."!"]);
        $this->gtrans->saveNewWords();
        redirect("master-setting");
      }
    }
  }
  function deleteIjin($encId){
    $this->load->library("encryption_org");
    $id  = $this->encryption_org->decode($encId);
    $res = $this->ijin_model->delete($id);
    if($res){
      setActivity("master setting","delete");
      $this->session->set_userdata("ses_msg",["type"=>"success","header"=>"success","msg"=> $this->gtrans->line("Ijin has been deleted successfully")."!"]);
      $this->gtrans->saveNewWords();
      redirect("master-setting");
    }
  }

  function checkCodeExists(){
    load_model(["setting_model"]);
    load_library(["encryption_org"]);
    $ijinCode = $this->input->post("code");
    $ijinid   = $this->encryption_org->decode($this->input->post("entityID"));
    $appid    = $this->session->userdata("ses_appid");
    $codeExists = $this->ijin_model->isCodeExists($ijinCode,$ijinid,$appid);
    if($codeExists==true){
      echo "exists";
    }else{
      echo "notExists";
    }
  }

  function checkNameExists(){
    load_model(["setting_model"]);
    load_library(["encryption_org"]);
    $settingName = $this->input->post("code");
    $settingid   = $this->encryption_org->decode($this->input->post("entityID"));
    $appid    = $this->session->userdata("ses_appid");
    $nameExists = $this->setting_model->isNameExists($settingName,$settingid,$appid);

    if($nameExists==true){
      echo "exists";
    }else{
      echo "notExists";
    }
  }
}
