<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Addons extends CI_Controller
{
  var $listMenu = "";
  var $tabel_template  = array(
        'table_open'            => '<table class="table table-bordered table-stripped" >',
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
    
    $this->listMenu = $this->menu_model->list_menu();

  }

  function index(){
    $this->system_model->checkSession(2);
    $this->load->model("external_model");
    $this->load->model("employee_model");
    $this->load->model("device_model");
    $this->load->model("subscription_model");

    $this->load->library("encryption_org");

    $appid = $this->session->userdata("ses_appid");
    $dataAddons = $this->external_model->myBillingGetAppAddons($appid);
    $dataAddonsActive = $this->external_model->myBillingGetActiveAddons($appid);
    
    $dataTrialActive  = $this->external_model->myBillingGetTrialAddons($appid);
    
    $intraxLitePaid = array_key_exists("intraxlicenselite", $dataAddonsActive);
    $intraxPremiumPaid = array_key_exists("intraxlicensepremium", $dataAddonsActive);

    $this->table->set_template($this->tabel_template);
    $this->table->set_heading(
      "No",
      "Subscription ID",
      $this->gtrans->line("Addons Code"),
      $this->gtrans->line("Name"),
      $this->gtrans->line("Active License"),
      $this->gtrans->line("Remaining License"),
      $this->gtrans->line("Start Date"),
      $this->gtrans->line("Expired Date"),
      "Status",
      $this->gtrans->line("Option"),
      ""
    );
    $no = 1;
    $arrOTPSlot = ["machinelicense","machinelicenseflash","intraxlicenselite","intraxlicensepremium"];
    if($dataTrialActive!=false){
      foreach ($dataTrialActive as $systemAddonsCodeTrial => $row1) {
        if(in_array($systemAddonsCodeTrial,$arrOTPSlot)){
          $opt = '<a href="'.base_url('addons-placement/'.$this->encryption_org->encode($systemAddonsCodeTrial)).'" ><i class="fa fa-gears"></i></a>';
        }else{
          $opt = '';
        }
        
        if($systemAddonsCodeTrial=="intraxlicensepremium"&&($intraxLitePaid==true || $intraxPremiumPaid==true)){
          $opt = '<i class="fa fa-ban text-red" title="Cannot use trial after activate paid intrax"></i>';
        }

        $this->table->add_row(
          $no,
          $row1["subscription_id"],
          $row1["code"],
          $row1["name"],
          $row1["qty"],
          $row1["qty"],
          $row1["expired"],
          '<span class="label label-danger">TRIAL</span>',
          $opt
        );
        $no++;
      }
    }
    
    $intrxActiveCode = "system die";
    foreach ($dataAddonsActive as $rows) {
	  $useLicense=0;
      if(in_array($rows["systemAddonsCode"],$arrOTPSlot)){
		date_default_timezone_set("Asia/Jakarta");
		$date_now = date("Y-m-d H:i:s");
		if(strtotime($date_now)>=strtotime($rows["start"])){
			$opt = '<a href="'.base_url('addons-placement/'.$this->encryption_org->encode($rows["systemAddonsCode"]).'/'.$this->encryption_org->encode($rows["subscription_id"])).'" ><i class="fa fa-gears"></i></a>';
		} else {
			$opt = '<span title="'.$this->gtrans->line("The license will be active after the start date").'" class="label label-warning">WAITING</span>';
		}
      }else{
        $opt = '';
      }
      // urus tampilan lisensi intrax

      //if($rows["code"]=="INT003" || $rows["code"]=="INT004"){
        //$intrxActiveCode = "ok";
      //}
	  if(strpos($rows['name'], "InAct") !== false){
		  $useLicense = $this->device_model->getInActActive($appid,$rows["subscription_id"])->num_rows();
	  } else {
		  $useLicense = $this->employee_model->getIntraxActive($appid,$rows["subscription_id"])->num_rows();
	  }
	  
	  $exportExcel = '';
	  if($useLicense>0){
		  $exportExcel = '<a href="../files/export_xls_employee_myaddons.php?subsid='.$this->encryption_org->encode($rows["subscription_id"]).'" target="_blank">'.$this->gtrans->line("export employee license").'</a>';
	  }
      
      $this->table->add_row(
        $no,
        $rows["subscription_id"],
        $rows["code"],
        $rows["name"],
        $rows["qty"],
        $rows["qty"]-$useLicense,
        !empty($rows["start"]) ? $rows["start"] : "",
        !empty($rows["expired"]) ? $rows["expired"] : "",
        '<span class="label label-success">PAID</span>',
        $opt,
        $exportExcel
      );
      $no++;
    }
	
    //$data["intrxActiveCode"] = $intrxActiveCode;
    $data["activeaddons"] = $this->createActiveAddons($dataAddonsActive,$dataTrialActive);
     //echo "<pre>";
    //print_r($intrxActiveCode);
     //echo "</pre>";
     //exit;
    $sqlSystemAddons  = $this->db->get("systemaddons");
    $dataSystemAddons = [];
    foreach ($sqlSystemAddons->result() as $rowSystemAddon) {
      $dataSystemAddons[$rowSystemAddon->addonscode] = $rowSystemAddon->systemaddons_code;
    }
    $dataAddons1 = $dataAddons;
    $dataAddons  = [];
    foreach($dataAddons1 as $index => $rowAddons){
      $rowAddons["systemaddonscode"] = $dataSystemAddons[$rowAddons["addonscode"]];
      $dataAddons[$index] = $rowAddons;
    }
    $data["dataAddons"] = $dataAddons;
    if(!empty($this->session->userdata("msg"))){
      $data["msgAddons"] = $this->session->userdata("msg");
      $this->session->unset_userdata("msg");
    }
    $data['myAddons']   = $this->table->generate();
    $data["subscriptionData"] = $this->subscription_model->getByAppId($appid);
    
    $parentViewData = [
      "title"   => "Addons",  // title page
      "content" => "license/addons",  // content view
      "viewData"=> $data,
      "listMenu"=> $this->listMenu
    ];
    //print_r($dataAddonsActive);
    $arrAddons = $this->session->userdata("infoAddons");
    $msgAddons = '<div class="callout callout-danger">';

    $sevenDaysAgain = date("Y-m-d H:i:s",strtotime("+7 day"));
    $addonsExpired  = false;
    
    foreach ($arrAddons as $row) {
      if($row["expired"] <= $sevenDaysAgain){
        $msgAddons    .= $row['name'].' will be expired at '.$row['expired'];
        $addonsExpired = true;
      }
      
    }

    $msgAddons .= '</div>';

    if($addonsExpired==true){
      $parentViewData["msgAddons"] = $msgAddons;
    }
    $this->load->view("layouts/main",$parentViewData);
    $this->gtrans->saveNewWords();
  }

  function createActiveAddons($arrActiveAddons,$arrTrialAddons){
    $appid = $this->session->userdata("ses_appid");
    $this->load->model("external_model");
    
    $sessAddons      = [];
    $infoAddons      = [];

    foreach ($arrActiveAddons as $index => $map) {
      $sessAddons[$index] = $map['qty'];
              
      $infoAddons[$index] = [
        "name"   => $map["name"],
        "expired"=> $map["expired"]
      ];
    }

    foreach ($arrTrialAddons as $index => $map) {
      if(array_key_exists($index,$sessAddons)){
        $sessAddons[$index] += $map['qty'];
      }else{
        $sessAddons[$index] = $map['qty'];
      }
    }
    return $sessAddons;
  }
  function takeTrialAddons(){
    $this->system_model->checkSession(2);
    $this->load->library("encryption_org");
    $appid = $this->session->userdata("ses_appid");
    $selectedTrial = $this->input->post("selectedTrial");
    $selectedAddonsCode = $this->input->post("selectedAddonsCode");

    $addonsCode       = $this->encryption_org->decode($selectedAddonsCode);
    $addonsId         = $this->encryption_org->decode($selectedTrial);
    $dataSystemAddons = getSystemAddons($addonsCode);
    
    if($dataSystemAddons){
      $qty = $dataSystemAddons->trial_quota;
    }else{
      $qty = 0;
    }
    
    $this->load->model("external_model");
    $result = $this->external_model->myBillingTakeTrialAddons($appid,$addonsId,$qty);
    if($result){
      // set new session for addons
      $arrActiveAddons = $this->session->userdata("activeaddons");
      $arrActiveAddons[$dataSystemAddons->systemaddons_code] = $qty;
      $this->session->set_userdata("activeaddons", $arrActiveAddons);

      // menambahkan session addon trial
      $activeTrialAddons   = !empty($this->session->userdata("activeTrialAddons")) ? $this->session->userdata("activeTrialAddons") : [];
      $activeTrialAddons[] = $dataSystemAddons->systemaddons_code;

      $this->session->set_userdata("activeTrialAddons", $activeTrialAddons);

      echo "success";
    }else{
      echo "failed";
    }
  }

  function PrepareDataForBuy(){
    $this->system_model->checkSession(2);
    $this->load->library("encryption_org");
    $buyingCount = $this->input->post("buyingCount");
    $pluginsId   = $this->encryption_org->decode($this->input->post('pluginsid'));
    $strBuy      = $pluginsId."|".$buyingCount;
    echo $this->encryption_org->encode($strBuy);
  }
}
