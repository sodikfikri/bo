<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Language extends CI_Controller
{
  function __construct()
  {
    parent::__construct();
    $this->load->library("session");
  }

  function changeLanguage(){
    $this->load->model("user_model");

    $lang = $this->input->post("lang");
    $id   = $this->session->userdata("ses_userid");
    $dataUpdate = [
      "lang" => $lang
    ];

    $this->session->set_userdata("lang",$lang);
    $this->user_model->update($dataUpdate,$id);

    echo "OK";
  }
}
