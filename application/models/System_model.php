<?php
/**
 * model ini hanya untuk fungsi yang membutuhkan session
 */

class System_model extends CI_Model
{

  function __construct()
  {
    parent::__construct();
    $this->load->library("session");
  }

  function checkSession($menuID="")
  {
    if(empty($this->session->userdata('ses_username'))){
      $this->session->set_userdata("ses_cloudmsg",'<div class="callout callout-danger">
                                                    <p>Session Timeout!</p>
                                                   </div>');
      redirect("login");
    }else{
      $userAccess = $this->session->userdata("access");
      $arrAccess  = explode("|", $userAccess);
      if(!in_array($menuID, $arrAccess)){
        redirect("unauthorized");
      }
    }
  }

  function renewLicenseSession($appid){
    $this->load->model("external_model");
    $arrActiveAddons = $this->external_model->myBillingGetActiveAddons($appid);
    $arrTrialAddons  = $this->external_model->myBillingGetTrialAddons($appid);
    $sessAddons      = [];
    $infoAddons      = [];

    foreach ($arrActiveAddons as $index => $map) {
      $sessAddons[$index] = $map['qty'];
              
      $infoAddons[$index] = [
        "name"   => $map["name"],
        "expired"=> $map["expired"],
        "qty"    => $map["qty"]
      ];
    }

    foreach ($arrTrialAddons as $index => $map) {
      if(array_key_exists($index,$sessAddons)){
        $sessAddons[$index] += $map['qty'];
      }else{
        $sessAddons[$index] = $map['qty'];
      }
    }
    $this->session->set_userdata("activeaddons", $sessAddons);
    $this->session->set_userdata("infoAddons", $infoAddons);
  }
}
