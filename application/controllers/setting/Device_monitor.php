<?php

class Device_monitor extends CI_Controller
{

  function __construct()
  {
    parent::__construct();
    $this->timestamp = date("Y-m-d H:i:s");

    $languange = !empty($this->session->userdata('lang')) ? $this->session->userdata('lang') :"en";
    $this->load->library("gtrans/gtrans",["lang" => $languange]);

    $this->load->model("menu_model");
    $this->load->model("system_model");
    // memanggil list menu harus load library gtrans di atasnya dulu

    $this->listMenu = $this->menu_model->list_menu();
  }

  function index(){
    $data = "";
    $parentViewData = [
      "title"      => "Device Monitor",  // title page
      "content"    => "setting/device_monitor",  // content view
      "viewData"   => $data,
      "listMenu"   => $this->listMenu,
      "varJS"      => [
        "url" => base_url()
      ],
      "externalJS" => [
        "https://cdn.jsdelivr.net/npm/sweetalert2@8"
      ]
    ];

    $this->load->view("layouts/main",$parentViewData);
    $this->gtrans->saveNewWords();
  }
}
