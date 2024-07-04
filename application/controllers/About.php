<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH."third_party/parsedown/Parsedown.php";
/**
 *
 */
class About extends CI_Controller
{
  var $listMenu = "";
  var $timestamp;

  function __construct()
  {
    parent::__construct();
    $this->load->model("system_model");
    $this->timestamp = date("Y-m-d H:i:s");

    $languange = !empty($this->session->userdata('lang')) ? $this->session->userdata('lang') :"en";
    $this->load->library("gtrans/gtrans",["lang" => $languange]);

    $this->load->model("menu_model");
    
    $this->system_model->checkSession(1);
    $this->listMenu = $this->menu_model->list_menu();
  }

  function index(){
    load_model(['area_model']);
    $addons = $this->session->userdata("activeaddons");
    $data   = [];
    $filePath    = FCPATH."versions".DIRECTORY_SEPARATOR.$this->session->userdata("lang").DIRECTORY_SEPARATOR."inact-".APP_VERSION.".md";
    if(file_exists($filePath)){
      $myfile      = fopen( $filePath , "r") or die("Unable to open file!");
      $fileMD      = fread($myfile,filesize($filePath));

      $Parsedown   = new Parsedown();

      $data["detailVersion"] = $Parsedown->text($fileMD);
    }else{
      $data["detailVersion"] = 'Detail Version Not Found';
    }

    $parentViewData = [
      "title"   => "About",  // title page
      "content" => "about_us",  // content view
      "viewData"=> $data,
      "listMenu"=> $this->listMenu,
    ];
    $this->load->view("layouts/main",$parentViewData);
  }
}
