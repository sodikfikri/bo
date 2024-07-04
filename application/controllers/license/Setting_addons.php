<?php
/**
 *
 */
class Setting_addons extends CI_Controller
{
  var $listMenu = "";
  var $tabel_template  = array(
        'table_open'            => '<table class="table table-bordered table-stripped" >',
        'table_close'           => '</table>'
	);
  function __construct()
  {
    parent::__construct();
    // model general
    $this->load->library("session");
    $this->load->library("device_caching");
    $this->timestamp = date("Y-m-d H:i:s");

    $languange = !empty($this->session->userdata('lang')) ? $this->session->userdata('lang') :"en";
    $this->load->library("gtrans/gtrans",["lang" => $languange]);

    $this->load->model("menu_model");
    $this->load->model("system_model");
    $this->listMenu = $this->menu_model->list_menu();
  }

  function addons_placement($encSystemAddonsCode,$subscription_id,$page=""){
    $this->load->model("devicelicense_model");
    $this->load->model("external_model");
    $this->load->library("encryption_org");

    $systemAddonsCode = $this->encryption_org->decode($encSystemAddonsCode);
	$subscription_id = $this->encryption_org->decode($subscription_id);
    if($systemAddonsCode=="machinelicense" || $systemAddonsCode=="machinelicenseflash"){
      $this->machineLicensePlacement($systemAddonsCode,$subscription_id);
    }elseif($systemAddonsCode=="intraxlicenselite" || $systemAddonsCode=="intraxlicensepremium"){
      $this->intraxLicensePlacement($systemAddonsCode,$subscription_id,$page);
    }
  }
  
  function intraxLicensePlacement($systemAddonsCode,$subscription_id){
    $data["title"]    = "Setting Addons ".$this->gtrans->line("Intrax License");
    $appid            = $this->session->userdata("ses_appid");
    $data["paramCode"]= $this->uri->segment(2);
    $data["subscription_id"]= $this->uri->segment(3);
    if(!empty($this->session->userdata("msg"))){
      $data["msgAddons"] = $this->session->userdata("msg");
      $this->session->unset_userdata("msg");
    }
    $activeAddons = $this->external_model->myBillingGetActiveAddons($appid);
    $activeTrialAddons = $this->external_model->myBillingGetTrialAddons($appid);

    $intraxLitePaid = array_key_exists("intraxlicenselite", $activeAddons);
    $intraxPremiumPaid = array_key_exists("intraxlicensepremium", $activeAddons);
    
    $this->table->set_template($this->tabel_template);
    $no    = 0;
    $arrDeviceLicense = !empty($activeAddons[$systemAddonsCode]) ? $activeAddons[$systemAddonsCode] : $activeAddons[$subscription_id];
    $arrTrialLicense = !empty($activeTrialAddons[$systemAddonsCode]) ? $activeTrialAddons[$systemAddonsCode] : [];
    $deviceQTY  = !empty($arrDeviceLicense['qty']) ? $arrDeviceLicense['qty'] :  $arrTrialLicense['qty'];
    $dateexpired  = !empty($arrDeviceLicense['expired']) ? $arrDeviceLicense['expired'] :  $arrTrialLicense['expired'];
    
    $data["addonsQty"] = $deviceQTY;
    $data["systemAddonsCode"] = $systemAddonsCode;
    $data["date_expired"]= $dateexpired;
    $this->table->set_heading(
      "Employee Code",
      "Name",
      "Subscription ID",
      '<input type="input" name="subscription_id" value="'.$subscription_id.'" hidden>'
    );

    load_model(["employee_model"]);
    $jumlah_data = $this->employee_model->getCountData($appid);
		
    $this->load->library('pagination');

    $config['reuse_query_string'] = true;
		$config['base_url'] = base_url("addons-placement/".$this->uri->segment(2)."/".$this->uri->segment(3));
		$config['total_rows'] = $jumlah_data;
		$config['per_page'] = 20;
    $config['full_tag_open'] = '<div class="card-tools">
                                  <ul class="pagination pagination-sm">';
    $config['full_tag_close'] = '</ul>
                                  </div>';
    $config['first_tag_open']  = '<li class="page-item">';
    $config['first_tag_close'] = '</li>';
    $config['last_tag_open'] = '<li class="page-item">';
    $config['last_tag_close'] = '</li>';
    $config['next_tag_open'] = '<li class="page-item">';
    $config['next_tag_close'] = '</li>';
    $config['prev_tag_open'] = '<li class="page-item">';
    $config['prev_tag_close'] = '</li>';
    $config['cur_tag_open'] = '<li class="page-item active"><a href="#" class="page-link">';
    $config['cur_tag_close'] = '</a></li>';
    $config['num_tag_open'] = '<li class="page-item">';
    $config['num_tag_close'] = '</li>';
    $config['attributes'] = array('class' => 'page-link');
		$from = !empty($this->uri->segment(4)) ? $this->uri->segment(4) : 0;
		$this->pagination->initialize($config);		
		
    if(!empty($_GET["search-box"])){
      $search_box = $_GET["search-box"];
    }
    $sql   = $this->employee_model->getAllEmployeeLicense($appid,$config['per_page'],$from,$search_box);
    $checkedCount = $this->employee_model->getIntraxActive($appid,$subscription_id)->num_rows();
    //echo $checkedCount;
    $arrEmployeeIdDisplayed = [];
    if($sql!=false){
      foreach ($sql as $row) {
        $no++;
		$btnChecked='';
        if($row->intrax_license=="active"){
          $checked ='checked';
			if($row->subscription_id==$subscription_id OR $row->subscription_id==''){
				$btnChecked='';
				//$checkedCount++;
			} else {
				$btnChecked='disabled';
			}
        }else{
          $checked ='';
        }
        $arrEmployeeIdDisplayed[] = $row->employee_id;
        $encEmployeeId = $this->encryption_org->encode($row->employee_id);
        $this->table->add_row(
          $row->employee_account_no,
          $row->employee_full_name,
          $row->subscription_id,
          '<input type="checkbox" name="employeeid[]" value="'.$encEmployeeId.'" id="chk'.$no.'" onclick="selectEmployee(this)" '.$checked.' '.$btnChecked.'>'
        );
      }
    }

    $data["strEmployeeIdDisplayed"] = implode("|",$arrEmployeeIdDisplayed);
    $data["checkedCount"] = $checkedCount;
    $data["tableList"] = $this->table->generate();
    // modal controller

    $licenses = $this->createActiveAddons($activeAddons,$activeTrialAddons);
    $intraxPanelRegister = false;
    if(array_key_exists("intraxlicensepremium",$licenses) || array_key_exists("intraxlicenselite",$licenses) || array_key_exists($subscription_id,$licenses)){
      //$this->load->model("");
      $appid = $this->session->userdata("ses_appid");
      $subscriptionData = $this->subscription_model->getByAppId($appid);
      $companyInfo      = $this->subscription_model->getCompanyinfo($appid);
      $data["intraxCompanyID"]  = $subscriptionData->intrax_company_id;
      $data["subscriptionData"] = $subscriptionData;
      $data["companyInfo"] = $companyInfo;
      $data["needUpgradeInTraxCompanyId"] = false;
      if($subscriptionData->intrax_company_id==""){
        $intraxPanelRegister = true;
      }else{
        if($intraxLitePaid==true || $intraxPremiumPaid==true){
          if($subscriptionData->intrax_plan_code==3){
            $intraxPanelRegister = false;
            //$data["needUpgradeInTraxCompanyId"] = true;
          }else{
            $intraxPanelRegister = false;
          }
        }else{
          $intraxPanelRegister = false;
        }
      }
    }
    $data["intraxPanelRegister"] = $intraxPanelRegister;
    //
    $parentViewData = [
      "title"   => "Setting Addons ".$systemAddonsCode,  // title page
      "content" => "license/setting_intrax_license",  // content view
      "viewData"=> $data,
      "listMenu"=> $this->listMenu,
      "externalJS" => [
        "https://cdn.jsdelivr.net/npm/sweetalert2@8"
      ]
    ];

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

  function machineLicensePlacement($systemAddonsCode,$subscription_id){
    $data["title"] = "Setting Addons ".$this->gtrans->line("Machine License");
    $appid        = $this->session->userdata("ses_appid");
    $activeAddons = $this->external_model->myBillingGetActiveAddons($appid);

    $this->table->set_template($this->tabel_template);
    $no    = 0;
    //if($systemAddonsCode=="machinelicense"){
      $arrDeviceLicense = $activeAddons[$systemAddonsCode];
	  if(empty($arrDeviceLicense)){
		  $arrDeviceLicense = $activeAddons[$subscription_id];
	  }
      $deviceQTY  = $arrDeviceLicense['qty'];
      $data["addonsQty"] = $deviceQTY;

      $this->table->set_heading(
        "No",
        "Device Name",
        "Branch Location",
        "Serial Number",
        "Subscription ID",
        '<input type="input" name="subscription_id" value="'.$subscription_id.'" hidden>'
      );

      load_model(["device_model"]);
      $sql   = $this->device_model->getAllByAppId($appid);
      $checkedCount = 0;
      foreach ($sql->result() as $row) {
        $no++;
		$btnChecked='';
        if($row->device_license=="active"){
          $checked ='checked';
			if($row->subscription_id==$subscription_id OR $row->subscription_id==''){
				$btnChecked='';
				$checkedCount++;
			} else {
				$btnChecked='disabled';
			}
        }else{
          $checked ='';
        }
        $encDeviceId = $this->encryption_org->encode($row->device_id);
        $this->table->add_row(
          $no,
          $row->device_name,
          $row->cabang_name,
          $row->device_SN,
          $row->subscription_id,
          '<input type="checkbox" name="deviceid[]" value="'.$encDeviceId.'" id="chk'.$no.'" onclick="selectDevice(this)" '.$checked.' '.$btnChecked.'>'
        );
      }
    //}

    $data["checkedCount"] = $checkedCount;
    $data["tableList"] = $this->table->generate();
    $parentViewData = [
      "title"   => "Setting Addons ".$systemAddonsCode,  // title page
      "content" => "license/setting_addons",  // content view
      "viewData"=> $data,
      "listMenu"=> $this->listMenu,
      "externalJS" => [
        "https://cdn.jsdelivr.net/npm/sweetalert2@8"
      ]
    ];

    $this->load->view("layouts/main",$parentViewData);
    $this->gtrans->saveNewWords();
  }

  function saveAddonsPlacement(){
    load_library(["encryption_org"]);
    load_model(["device_model","external_model"]);

    $appid          = $this->session->userdata("ses_appid");
    $dataDevices    = $this->device_model->getAllByAppId($appid);
    $arrEncDeviceId = $this->input->post("deviceid");
	$subscription_id = $this->input->post("subscription_id");

    $activeAddons       = $this->external_model->getActiveAddons($appid);
    
	if(!empty($activeAddons["machinelicense"])){
		$deviceLicenseCount = $activeAddons["machinelicense"];
	} elseif(!empty($activeAddons["machinelicenseflash"])) {
		$deviceLicenseCount = $activeAddons["machinelicenseflash"];
	} else {
		$deviceLicenseCount = $activeAddons[$subscription_id];
	}
    //$deviceLicenseCount = !empty($activeAddons["machinelicense"]) ? $activeAddons["machinelicense"] : $activeAddons[$subscription_id];
	
    if(count($arrEncDeviceId)<=$deviceLicenseCount){
      // jika idak ada pelanggaran jumlah lisensi
      $arrUpdate = [];
      // semua data device active, dinonaktifkan semua lisensinya
      foreach ($dataDevices->result() as $device) {
        if($device->subscription_id==$subscription_id OR $device->subscription_id==''){
			$arrUpdate[$device->device_id] = [
			  "device_id" => $device->device_id,
			  "device_license" => "notactive",
			  "subscription_id" => NULL
			];
		}
      }

      // dari daftar device yang nonaktif diaktifkan sesuai yang diinput user

      foreach ($arrEncDeviceId as $encDeviceId) {
        $deviceId    = $this->encryption_org->decode($encDeviceId);
        $arrUpdate[$deviceId] = [
          "device_id" => $deviceId,
          "device_license" => "active",
          "subscription_id" => $subscription_id
        ];
      }
      $res = $this->device_model->update_batch($arrUpdate);
      if($res){
        $this->device_caching->cacheSN();
      }
      echo "OK";
    }else{
      // 
      echo "NOTOKTOK";
    }
  }

  function saveIntraxPlacement(){
    load_library(["encryption_org"]);
    load_model(["employee_model","subscription_model","external_model"]);
    $appid            = $this->session->userdata("ses_appid");
    
	
    
    //$dataEmployees    = $this->employee_model->getAll($appid);
    $arrEncEmployeeId = !empty($this->input->post("employeeid")) ? $this->input->post("employeeid") : [];
    $systemAddonsCode = $this->input->post("system-addons-code");
    $strEmployeeIdDisplayed = $this->input->post("str-employee-id-displayed");
    $arrEmployeeIdDisplayed = explode("|",$strEmployeeIdDisplayed);
	$subscription_id = $this->input->post("subscription_id");
	$date_expired = $this->input->post("date_expired");
    $activeAddons       = $this->external_model->getActiveAddons($appid);
    $lastPosition       = $this->employee_model->getIntraxActiveEmployeeID($appid,$subscription_id);
    $licenseCount = !empty($activeAddons[$systemAddonsCode]) ? $activeAddons[$systemAddonsCode] : $activeAddons[$subscription_id];
    
    $this->db->where_not_in("employee_id",$arrEmployeeIdDisplayed);
    $sqlIntraxActive  = $this->employee_model->getIntraxActive($appid,$subscription_id);

    foreach($sqlIntraxActive->result() as $activeLicense){
      $encEmployeeId = $this->encryption_org->encode($activeLicense->employee_id);
      if(!in_array($encEmployeeId,$arrEncEmployeeId)){
        $arrEncEmployeeId[] = $encEmployeeId;
      }
    }
    if(count($arrEncEmployeeId)<=$licenseCount){
      // jika idak ada pelanggaran jumlah lisensi
      //$arrUpdate = [];
      // semua data employee active, dinonaktifkan semua lisensinya
      
      /*foreach ($dataEmployees as $employee) {
        $arrUpdate[$employee->employee_id] = [
          "employee_id" => $employee->employee_id,
          "intrax_license" => "notactive"
        ];
      }*/

      // dari daftar device yang nonaktif diaktifkan sesuai yang diinput user
      $arrEmployeeId = [];
      foreach ($arrEncEmployeeId as $encEmployeeId) {
        $employeeId     = $this->encryption_org->decode($encEmployeeId);
        $arrEmployeeId[]= $employeeId;
      }
      //unset($arrEncEmployeeId);
      // update intrax
      $companyData     = $this->subscription_model->getByAppId($appid);
      $addedEmployee   = array_diff($arrEmployeeId,$lastPosition);
      $removedEmployee = array_diff($lastPosition,$arrEmployeeId);
      if($companyData->intrax_company_id!=""){
        // remove license
        $arrUpdate = [];
        if(count($removedEmployee)>0){
          $dataRemoveLicense = $this->employee_model->getEmployeeByArray($appid,$removedEmployee);
          $arrEmployeeRemove = [];
          foreach($dataRemoveLicense->result() as $rowEmployeeRemove){
            $arrEmployeeRemove[] = array(
              "employee_id" => $rowEmployeeRemove->employee_id,
              "checklog_id" => $rowEmployeeRemove->employee_account_no,
              "company_id"  => $companyData->intrax_company_id
            );
          }
          $dataRequestRemove = array(
            "company_id" => [$companyData->intrax_company_id],
            "employee"=> $arrEmployeeRemove
          );
          
		  if($appid!='IA01M82337F20230627732'){
			  $jsonDataRequestRemove = json_encode($dataRequestRemove);
			  /*
			  $curl = curl_init();

			  curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://licenses.genesysindonesia.co.id/api/company/deleteEmployee',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS =>$jsonDataRequestRemove,
				CURLOPT_HTTPHEADER => array(
				  'Content-Type: application/json',
				  'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiNjkzNTQwOWYzMWIyNDFlNDIyZjYyMjExOTVlMTlhNTNmZmQ3MzJlYjVhOGYxNzM2ZGRhZTEyNzZhMjU1YWM2NTBmNzkxZWYyMWM0NmYyN2YiLCJpYXQiOjE2NTgzODkyNjAsIm5iZiI6MTY1ODM4OTI2MCwiZXhwIjoxNjg5OTI1MjYwLCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.VgO9GK9tFSuu7hHpabE1DWm3RFkA0NfL-Q92EBRz_h1lZZAJH65Qtq7p1_HlPyt3TYE9FncVFsDjV1LUsAIMzNquJMcQYNQ-pYfj3irFTqp0LCAEOcrc1c7Pm5J2YUHPOGOQGeRB3_SZzUpejHcWICKhXMQMIRK1Ss3hOyXXnv4sH4B7uujUsd0_99q6gz4ufySzmbChBelzl0PrcXbHiRHAiQKfcsfowvrU1pe2mYgTUnkjdKyRXIy-XJ5mL7NQ4Bq3ZPZROPQpS9YT0TLfSRxiocfqutXmYgx4jwjl0jh7fqF7fM8Y4VcKSKRCBL-Pqg-bK-tPzLBK4kOrh-6Ngnp3WJoL0pUKLgbGEeVysU9Ehu__REvJCamdM5aipt6ym1B0hZxfHji4DoP0jHVG8Xxp6nn0vMqUi3gdZp-hY1JawJusqtj3KEeejfLfB-oKlZKqNXmXGNWagDeNLh9HTM2ry4PTGMKAFXVvQcafmE2OEovDQvzuB2npbO3cDPvyf3nMPgzMiDcE79051R8ojFkf82SypYzMcMunhCiBhd-2zmZ7w5NVYuG3bgvd76eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiNjkzNTQwOWYzMWIyNDFlNDIyZjYyMjExOTVlMTlhNTNmZmQ3MzJlYjVhOGYxNzM2ZGRhZTEyNzZhMjU1YWM2NTBmNzkxZWYyMWM0NmYyN2YiLCJpYXQiOjE2NTgzODkyNjAsIm5iZiI6MTY1ODM4OTI2MCwiZXhwIjoxNjg5OTI1MjYwLCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.VgO9GK9tFSuu7hHpabE1DWm3RFkA0NfL-Q92EBRz_h1lZZAJH65Qtq7p1_HlPyt3TYE9FncVFsDjV1LUsAIMzNquJMcQYNQ-pYfj3irFTqp0LCAEOcrc1c7Pm5J2YUHPOGOQGeRB3_SZzUpejHcWICKhXMQMIRK1Ss3hOyXXnv4sH4B7uujUsd0_99q6gz4ufySzmbChBelzl0PrcXbHiRHAiQKfcsfowvrU1pe2mYgTUnkjdKyRXIy-XJ5mL7NQ4Bq3ZPZROPQpS9YT0TLfSRxiocfqutXmYgx4jwjl0jh7fqF7fM8Y4VcKSKRCBL-Pqg-bK-tPzLBK4kOrh-6Ngnp3WJoL0pUKLgbGEeVysU9Ehu__REvJCamdM5aipt6ym1B0hZxfHji4DoP0jHVG8Xxp6nn0vMqUi3gdZp-hY1JawJusqtj3KEeejfLfB-oKlZKqNXmXGNWagDeNLh9HTM2ry4PTGMKAFXVvQcafmE2OEovDQvzuB2npbO3cDPvyf3nMPgzMiDcE79051R8ojFkf82SypYzMcMunhCiBhd-2zmZ7w5NVYuG3bgvd76qBh6lG8hV1aMV5Jz_F14rxIfsKzRz4-y9sO2xtRivXIIstsV5xU6Pr3gjl__3cN-2KRFx0hnMhmYICPxOBApdfOuqj2G5ZJ3kNGUk9RESoMCsqBh6lG8hV1aMV5Jz_F14rxIfsKzRz4-y9sO2xtRivXIIstsV5xU6Pr3gjl__3cN-2KRFx0hnMhmYICPxOBApdfOuqj2G5ZJ3kNGUk9RESoMCs'
				),
			  ));

			  $responseRemove    = curl_exec($curl);
			  */
			  $curl = curl_init();

				curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://licenses.genesysindonesia.co.id/api/company/deleteEmployee',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $jsonDataRequestRemove,
				CURLOPT_HTTPHEADER => array(
					'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiNTg5NWQ3NzJlZjUwYjJiNDIxNzhlNTk3ZGIyZDEyYTFmMzJiMDUwYTg5YmE3NzE2MDQxYjEwZDliNjUxMjM3N2Y4ZjIwYWRlNmNhNjg1Y2UiLCJpYXQiOjE2ODk5MjUzMzYsIm5iZiI6MTY4OTkyNTMzNiwiZXhwIjoxNzIxNTQ3NzM2LCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.fEvQlX2xWE1tT-u89V4jSRUon4bMlr3nPD_zR0pBLGBNEQcnFWrXB63FVRGMCkduDKZ6C1LMJABhzj9OnhAevuaege9QUp2XjafUUDXA_UW4-E10qrIY3WEaCLfMFGaJDknl0_Cao2kQRxs440ftdsWJ_PMzPXOjwS0ywYxqIOrt0DoLzcB3FObxTMyR_QxsN9gTZdD--hk2wQU1iSE7I16432mAgXbWTCwEW1UeHlPMBaiSEivbzqhI6DByaFPLYa4YZMtqqCzJ4e2rOYMGnmQL4FP_WbONGyUKw5iyliUzWzD4Uljev-ZzBfde6LSpcUZWrF6M6liqZz93py-czB3Wpa5-CvfGqDQrF-8Dl5BbrvQjFs0wKd5dMncNjqjfvzNYuTqwq_dDQ5hghFybK9m7OXj4WlCtc0YZWXQwQJhka9EFeiGQMauFg7V4m44Kmdk36Nfqz_L5YWy6dVcgavNr9QqHv00u28aA0q3_Au4ju7poWI3KRheBGF1Iyd4KZ1T-g1MvTxGOYHZn5P8H2qCgSsPDoWvMcg2iMecPVLKAXLudhSvBp2HF50LJjxEBA4a3nago1PCmDcvOhC5TAIjOha2j-GT_Y4Or-CKaIrMF4s-h1vY2Jxy664FlAwBxt_al0MPvqT__gQyJMfLKSgHhnlcIPOwLIdA_KITXrnQ',
					'Content-Type: application/json'
				),
				));
				$responseRemove = curl_exec($curl);
				
			  $arrResponseRemove = json_decode($responseRemove);
			  if($arrResponseRemove->result!=true){
				print_r($responseRemove);
			  }else{
				// update db lokal
				foreach ($removedEmployee as $notactivedemployeeID) {
				  $arrUpdate[$notactivedemployeeID] = [
					"employee_id" => $notactivedemployeeID,
					"intrax_license" => "notactive",
					"subscription_id" => NULL,
					"subscription_expired" => NULL
				  ];
				}
			  }
			  curl_close($curl);
		  } else {
			// update db lokal
            foreach ($removedEmployee as $notactivedemployeeID) {
              $arrUpdate[$notactivedemployeeID] = [
                "employee_id" => $notactivedemployeeID,
                "intrax_license" => "notactive",
                "subscription_id" => NULL,
				"subscription_expired" => NULL
              ];
            }
		  }
		  
        }
        // add license
        if(count($addedEmployee)>0){
          $dataAddLicense = $this->employee_model->getEmployeeByArray($appid,$addedEmployee);
          $arrEmployeeAdd = [];
          foreach($dataAddLicense->result() as $rowEmployeeAdd){
            $arrEmployeeAdd[] = [
              "employee_id" => $rowEmployeeAdd->employee_id,
              "checklog_id" => $rowEmployeeAdd->employee_account_no,
              "company_id"  => $companyData->intrax_company_id,
              "name"        => $rowEmployeeAdd->employee_full_name,
              "intrax_pin"  => $rowEmployeeAdd->intrax_pin,
              "gender"      => $rowEmployeeAdd->gender,
              "birthday"    => $rowEmployeeAdd->birthday,
              "phone_number"=> $rowEmployeeAdd->phone_number,
              "email"       => $rowEmployeeAdd->email,
              "address"     => $rowEmployeeAdd->address
            ];
          }
          $curl = curl_init();
          $dataRequestAdd = [
            "company_id" => [$companyData->intrax_company_id],
            "employee"=> $arrEmployeeAdd
          ];
		  if($appid!='IA01M82337F20230627732'){
			  $jsonDataRequestAdd = json_encode($dataRequestAdd);
			  /*
			  curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://licenses.genesysindonesia.co.id/api/company/addEmployee',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS =>$jsonDataRequestAdd,
				CURLOPT_HTTPHEADER => array(
				  'Authorization: bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiNjkzNTQwOWYzMWIyNDFlNDIyZjYyMjExOTVlMTlhNTNmZmQ3MzJlYjVhOGYxNzM2ZGRhZTEyNzZhMjU1YWM2NTBmNzkxZWYyMWM0NmYyN2YiLCJpYXQiOjE2NTgzODkyNjAsIm5iZiI6MTY1ODM4OTI2MCwiZXhwIjoxNjg5OTI1MjYwLCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.VgO9GK9tFSuu7hHpabE1DWm3RFkA0NfL-Q92EBRz_h1lZZAJH65Qtq7p1_HlPyt3TYE9FncVFsDjV1LUsAIMzNquJMcQYNQ-pYfj3irFTqp0LCAEOcrc1c7Pm5J2YUHPOGOQGeRB3_SZzUpejHcWICKhXMQMIRK1Ss3hOyXXnv4sH4B7uujUsd0_99q6gz4ufySzmbChBelzl0PrcXbHiRHAiQKfcsfowvrU1pe2mYgTUnkjdKyRXIy-XJ5mL7NQ4Bq3ZPZROPQpS9YT0TLfSRxiocfqutXmYgx4jwjl0jh7fqF7fM8Y4VcKSKRCBL-Pqg-bK-tPzLBK4kOrh-6Ngnp3WJoL0pUKLgbGEeVysU9Ehu__REvJCamdM5aipt6ym1B0hZxfHji4DoP0jHVG8Xxp6nn0vMqUi3gdZp-hY1JawJusqtj3KEeejfLfB-oKlZKqNXmXGNWagDeNLh9HTM2ry4PTGMKAFXVvQcafmE2OEovDQvzuB2npbO3cDPvyf3nMPgzMiDcE79051R8ojFkf82SypYzMcMunhCiBhd-2zmZ7w5NVYuG3bgvd76eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiNjkzNTQwOWYzMWIyNDFlNDIyZjYyMjExOTVlMTlhNTNmZmQ3MzJlYjVhOGYxNzM2ZGRhZTEyNzZhMjU1YWM2NTBmNzkxZWYyMWM0NmYyN2YiLCJpYXQiOjE2NTgzODkyNjAsIm5iZiI6MTY1ODM4OTI2MCwiZXhwIjoxNjg5OTI1MjYwLCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.VgO9GK9tFSuu7hHpabE1DWm3RFkA0NfL-Q92EBRz_h1lZZAJH65Qtq7p1_HlPyt3TYE9FncVFsDjV1LUsAIMzNquJMcQYNQ-pYfj3irFTqp0LCAEOcrc1c7Pm5J2YUHPOGOQGeRB3_SZzUpejHcWICKhXMQMIRK1Ss3hOyXXnv4sH4B7uujUsd0_99q6gz4ufySzmbChBelzl0PrcXbHiRHAiQKfcsfowvrU1pe2mYgTUnkjdKyRXIy-XJ5mL7NQ4Bq3ZPZROPQpS9YT0TLfSRxiocfqutXmYgx4jwjl0jh7fqF7fM8Y4VcKSKRCBL-Pqg-bK-tPzLBK4kOrh-6Ngnp3WJoL0pUKLgbGEeVysU9Ehu__REvJCamdM5aipt6ym1B0hZxfHji4DoP0jHVG8Xxp6nn0vMqUi3gdZp-hY1JawJusqtj3KEeejfLfB-oKlZKqNXmXGNWagDeNLh9HTM2ry4PTGMKAFXVvQcafmE2OEovDQvzuB2npbO3cDPvyf3nMPgzMiDcE79051R8ojFkf82SypYzMcMunhCiBhd-2zmZ7w5NVYuG3bgvd76qBh6lG8hV1aMV5Jz_F14rxIfsKzRz4-y9sO2xtRivXIIstsV5xU6Pr3gjl__3cN-2KRFx0hnMhmYICPxOBApdfOuqj2G5ZJ3kNGUk9RESoMCsqBh6lG8hV1aMV5Jz_F14rxIfsKzRz4-y9sO2xtRivXIIstsV5xU6Pr3gjl__3cN-2KRFx0hnMhmYICPxOBApdfOuqj2G5ZJ3kNGUk9RESoMCs',
				  'Content-Type: application/json'
				),
			  ));

			  $responseAdd = curl_exec($curl);
			  */
			  $curl = curl_init();

				curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://licenses.genesysindonesia.co.id/api/company/addEmployee',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POSTFIELDS => $jsonDataRequestAdd,
				CURLOPT_HTTPHEADER => array(
					'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiNTg5NWQ3NzJlZjUwYjJiNDIxNzhlNTk3ZGIyZDEyYTFmMzJiMDUwYTg5YmE3NzE2MDQxYjEwZDliNjUxMjM3N2Y4ZjIwYWRlNmNhNjg1Y2UiLCJpYXQiOjE2ODk5MjUzMzYsIm5iZiI6MTY4OTkyNTMzNiwiZXhwIjoxNzIxNTQ3NzM2LCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.fEvQlX2xWE1tT-u89V4jSRUon4bMlr3nPD_zR0pBLGBNEQcnFWrXB63FVRGMCkduDKZ6C1LMJABhzj9OnhAevuaege9QUp2XjafUUDXA_UW4-E10qrIY3WEaCLfMFGaJDknl0_Cao2kQRxs440ftdsWJ_PMzPXOjwS0ywYxqIOrt0DoLzcB3FObxTMyR_QxsN9gTZdD--hk2wQU1iSE7I16432mAgXbWTCwEW1UeHlPMBaiSEivbzqhI6DByaFPLYa4YZMtqqCzJ4e2rOYMGnmQL4FP_WbONGyUKw5iyliUzWzD4Uljev-ZzBfde6LSpcUZWrF6M6liqZz93py-czB3Wpa5-CvfGqDQrF-8Dl5BbrvQjFs0wKd5dMncNjqjfvzNYuTqwq_dDQ5hghFybK9m7OXj4WlCtc0YZWXQwQJhka9EFeiGQMauFg7V4m44Kmdk36Nfqz_L5YWy6dVcgavNr9QqHv00u28aA0q3_Au4ju7poWI3KRheBGF1Iyd4KZ1T-g1MvTxGOYHZn5P8H2qCgSsPDoWvMcg2iMecPVLKAXLudhSvBp2HF50LJjxEBA4a3nago1PCmDcvOhC5TAIjOha2j-GT_Y4Or-CKaIrMF4s-h1vY2Jxy664FlAwBxt_al0MPvqT__gQyJMfLKSgHhnlcIPOwLIdA_KITXrnQ',
					'Content-Type: application/json'
				),
				));
				$responseAdd = curl_exec($curl);
			  $arrResponseAdd = json_decode($responseAdd);
			  if($arrResponseAdd->result!=true){
				//print_r($arrResponseAdd);
				$error = "ini error".$arrResponseAdd;
				$path = "application-errors.log";
				error_log($error, 3, $path);
			  }else{
				// update db lokal
				foreach ($addedEmployee as $activedemployeeID) {
				  $employeeId     = $this->encryption_org->decode($encEmployeeId);
				  $arrEmployeeId[]= $employeeId;
				  $arrUpdate[$activedemployeeID] = [
					"employee_id" => $activedemployeeID,
					"intrax_license" => "active",
					"employee_is_active" => "1",
					"subscription_id" => $subscription_id,
					"subscription_expired" => $date_expired
				  ];
				}
			  }
			  curl_close($curl);
		  } else {
				// update db lokal
				foreach ($addedEmployee as $activedemployeeID) {
				  $employeeId     = $this->encryption_org->decode($encEmployeeId);
				  $arrEmployeeId[]= $employeeId;
				  $arrUpdate[$activedemployeeID] = [
					"employee_id" => $activedemployeeID,
					"intrax_license" => "active",
					"employee_is_active" => "1",
					"subscription_id" => $subscription_id,
					"subscription_expired" => $date_expired
				  ];
				}
		  }
          
        }
        if(count($arrUpdate)>0){
          // update ke database lokal
          $this->employee_model->update_batch($arrUpdate,"employee_id");
        }
        
        echo "OK";
      }
      
    }else{
      // 
      echo "NOTOKAE";
    }    
  }
}
