<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Cabang extends CI_Controller
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
    $this->timestamp = date("Y-m-d H:i:s");

    $languange = !empty($this->session->userdata('lang')) ? $this->session->userdata('lang') :"en";
    $this->load->library("gtrans/gtrans",["lang" => $languange]);

    $this->load->model("menu_model");
    $this->load->model("employee_model");
    $this->load->model("cabang_model");
    $this->system_model->checkSession(5);
    $this->listMenu = $this->menu_model->list_menu();
  }

  function index(){
    $this->load->model("area_model");
    $this->load->helper("form");
    $this->load->helper("timezone");
    $this->load->library("encryption_org");
    $sqlArea = $this->area_model->getAll();

    $cmbArea = '<select data-validation-engine="validate[required]" name="area" id="area" class="form-control"><option value="" />';
	$arrArea = explode("|",$this->session->userdata("ses_area"));
    foreach ($sqlArea as $rowArea) {
	  if($this->session->userdata("ses_status")=="admin_area"){
		if(in_array($rowArea->area_id, $arrArea)){
		  $cmbArea .= '<option value="'.$rowArea->area_id.'" />'.$rowArea->area_name;
		}
	  } else {
		$cmbArea .= '<option value="'.$rowArea->area_id.'" />'.$rowArea->area_name;
	  }
      
    }

    $cmbArea   .= '</select>';
    $arrTimezone= getTimezone();
    $cmbTimezone = '<select data-validation-engine="validate[required]" name="timezone" id="timezone" style="width:100%" class="select2"><option value="" />';
    foreach ($arrTimezone as $index => $val) {
      $text    = $val;
      $arrText = explode(" ",$text,2);
      $utc = preg_replace('/\(|\)/','',$arrText[0]);
      $timeZoneValue = base64_encode($index."|".$utc);
      
      $cmbTimezone  .= '<option value="'.$timeZoneValue.'" />'.$index.' '.$arrText[0];
    }

    $cmbTimezone   .= '</select>';

    $this->table->set_template($this->tabel_template);
    $this->table->set_heading(
      ["data" => "No","class" => "text-center"],
      ["data" => $this->gtrans->line("Branch Code"),"class" => ""],
      ["data" => $this->gtrans->line("Timezone"),"class" => "text-center"],
      ["data" => $this->gtrans->line("Name"),"class" => "text-center"],
      ["data" => $this->gtrans->line("Area"),"class" => "text-center"],
      ["data" => $this->gtrans->line("Address"),"class" => "text-center"],
      ["data" => $this->gtrans->line("Longitude"),"class" => "text-center"],
      ["data" => $this->gtrans->line("Latitude"),"class" => "text-center"],
      ["data" => $this->gtrans->line("Contact"),"class" => "text-center"],
      ["data" => $this->gtrans->line("Description"),"class" => "text-center"],
      ["data" => $this->gtrans->line("Total Employee"),"class" => "text-center"],
      ["data" => $this->gtrans->line("Option"),"class" => "text-center"],
      ["data" => $this->gtrans->line("Setting"),"class" => "text-center"]
    );
	
	$areaid  = !empty($this->input->post("areaid"))? $this->input->post("areaid"): "";
    $sql = $this->cabang_model->getAll();
    $no = 0;
    foreach ($sql as $row) {
	  if($this->session->userdata("ses_status")=="admin_area"){
		$arrArea = explode("|",$this->session->userdata("ses_area"));
		if(in_array($row->cabang_area_id, $arrArea)){
		  $no++;
		  $encId = $this->encryption_org->encode($row->cabang_id);
		  $encArea = base64_encode($row->cabang_area_id);
		  $encCode = base64_encode($row->cabang_code);
		  $encName = base64_encode($row->cabang_name);
		  $encTimezone = base64_encode(base64_encode($row->cabang_timezone."|".$row->cabang_utc));
		  $encAddress  = base64_encode($row->cabang_address);
		  $encLongitude  = base64_encode($row->longitude);
		  $encLatitude  = base64_encode($row->latitude);
		  $encContact  = base64_encode($row->cabang_contactnumber);
		  $encDescription = base64_encode($row->cabang_keterangan);
		  $encMethod = base64_encode($row->presence_method);
		  $encMode = base64_encode($row->presence_mode);

		  $btnEdit = '<i style="cursor:pointer" class="fa fa-edit fa-lg color-primary"
		  onclick="edit(
			\''.$encId.'\',
			\''.$encArea.'\',
			\''.$encCode.'\',
			\''.$encName.'\',
			\''.$encTimezone.'\',
			\''.$encAddress.'\',
			\''.$encLongitude.'\',
			\''.$encLatitude.'\',
			\''.$encContact.'\',
			\''.$encDescription.'\'
		  )"></i>';
		  
		  $delete  = '<span class="text-red" style="cursor:pointer" onclick="delBranch(\''.$encId.'\','.$row->totalDevice.','.$row->totalEmployee.')"><i class="fa fa-trash fa-lg "></i></span>';
		  $detailEmployee  = '<span class="text-blue" style="cursor:pointer" onclick="detail(\''.$row->cabang_id.'\')">'.$row->totalEmployee.'</span>';
		  $btnEditMethod = '<i style="cursor:pointer" class="fa fa-cog fa-lg color-primary"
		  onclick="editMethod(
			\''.$encId.'\',
			\''.$encMethod.'\',
			\''.$encMode.'\',
		  )"></i>';
		  $option  = $btnEdit.' '.$delete;
		  $this->table->add_row(
			["data" => $no,"class" => "text-center"],
			["data" => $row->cabang_code, "class" => ""],
			$row->cabang_timezone." (".$row->cabang_utc.")",
			$row->cabang_name,
			$row->area_name,
			$row->cabang_address,
			$row->longitude,
			$row->latitude,
			$row->cabang_contactnumber,
			$row->cabang_keterangan,
			["data" => $detailEmployee,"class" => "text-center"],
			$option,
			$btnEditMethod
		  );
		}
	  } else {
	  $no++;
      $encId = $this->encryption_org->encode($row->cabang_id);
      $encArea = base64_encode($row->cabang_area_id);
      $encCode = base64_encode($row->cabang_code);
      $encName = base64_encode($row->cabang_name);
      $encTimezone = base64_encode(base64_encode($row->cabang_timezone."|".$row->cabang_utc));
      $encAddress  = base64_encode($row->cabang_address);
      $encLongitude  = base64_encode($row->longitude);
      $encLatitude  = base64_encode($row->latitude);
      $encContact  = base64_encode($row->cabang_contactnumber);
      $encDescription = base64_encode($row->cabang_keterangan);
      $encMethod = base64_encode($row->presence_method);
      $encMode = base64_encode($row->presence_mode);

      $btnEdit = '<i style="cursor:pointer" class="fa fa-edit fa-lg color-primary"
      onclick="edit(
        \''.$encId.'\',
        \''.$encArea.'\',
        \''.$encCode.'\',
        \''.$encName.'\',
        \''.$encTimezone.'\',
        \''.$encAddress.'\',
        \''.$encLongitude.'\',
        \''.$encLatitude.'\',
        \''.$encContact.'\',
        \''.$encDescription.'\'
      )"></i>';
	  
      $delete  = '<span class="text-red" style="cursor:pointer" onclick="delBranch(\''.$encId.'\','.$row->totalDevice.','.$row->totalEmployee.')"><i class="fa fa-trash fa-lg "></i></span>';
	  $detailEmployee  = '<span class="text-blue" style="cursor:pointer;font-weight:600" onclick="detail(\''.$row->cabang_id.'\')">total : '.$row->totalEmployee.'<i class="fa fa-play-circle fa-lg" style="margin-left:8px"></i></span></span>';
	  $btnEditMethod = '<i style="cursor:pointer" class="fa fa-cog fa-lg color-primary"
      onclick="editMethod(
        \''.$encId.'\',
        \''.$encMethod.'\',
        \''.$encMode.'\',
      )"></i>';
      $option  = $btnEdit.' '.$delete;
      $this->table->add_row(
        ["data" => $no,"class" => "text-center"],
        ["data" => $row->cabang_code, "class" => ""],
        $row->cabang_timezone." (".$row->cabang_utc.")",
        $row->cabang_name,
        $row->area_name,
        $row->cabang_address,
        $row->longitude,
        $row->latitude,
        $row->cabang_contactnumber,
        $row->cabang_keterangan,
		["data" => $detailEmployee,"class" => "text-center"],
        $option,
		$btnEditMethod
      );
	  }
    }
	
	$sqlArea = $this->area_model->getAll();
	$data['dataArea']    = $sqlArea;
	
    if(!empty($this->session->userdata("ses_msg"))){
      $msg = $this->session->userdata("ses_msg");
      $data["notif"] =createNotif($msg['type'],$msg['header'],$msg['msg']);
      $this->session->set_userdata("ses_msg");
    }

    $data['branchTable'] = $this->table->generate();
    $data['cmbArea']     = $cmbArea;
    $data['cmbTimezone'] = $cmbTimezone;
    $parentViewData = [
      "title"   => $this->gtrans->line("Master Branch"),  // title page
      "content" => "master/cabang",  // content view
      "viewData"=> $data,
      "listMenu"=> $this->listMenu,
      "externalCSS" => [
        base_url("asset/template/bower_components/select2/dist/css/select2.min.css"),
        base_url("asset/template/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css")
      ],
      "externalJS" => [
        base_url("asset/template/bower_components/datatables.net/js/jquery.dataTables.min.js"),
        base_url("asset/template/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"),
        "https://cdn.jsdelivr.net/npm/sweetalert2@8",
        base_url("asset/template/bower_components/select2/dist/js/select2.full.min.js"),
        base_url("asset/js/checkCode.js")
      ],
      "varJS" => ["url" => base_url()]
    ];
    $this->load->view("layouts/main",$parentViewData);
    $this->gtrans->saveNewWords();
  }
  
  function showEmployee(){
    $this->load->model("employee_model");
	$cabangId   = $this->input->post("cabangId");
	$appid = $this->session->userdata("ses_appid");
	$sqlEmp = $this->employee_model->getAllEmpCabang($appid,$cabangId);
	$listEmployee = '<ol>';  
	foreach ($sqlEmp as $rowEmp) {
		$listEmployee .= '<li>'.$rowEmp->employee_full_name.', <b>total branch: '.$rowEmp->totalBranch.'</b></li>';  
	}
	$listEmployee .= '</ol>';
	echo json_encode($listEmployee);
  }
  
  function saveCabangMethod(){
	$method   = $this->input->post("method");
	$mode   = $this->input->post("presence_mode");
	$strMethod= !empty($method) ? implode("|", $method) : "1";
    $encId       = $this->input->post("idmethod");
	$appid = $this->session->userdata("ses_appid");

    $dataInsert = [
      "presence_method" => $strMethod,
	  "presence_mode" => $mode
    ];
	$this->load->library("encryption_org");
    $id = $this->encryption_org->decode($encId);
	$cabangId = $id;
	$res = $this->cabang_model->update($dataInsert,$id);
	if($res){
      setActivity("master branch","edit");
      $this->session->set_userdata("ses_msg",["type"=>"success","header"=>"success","msg"=> $this->gtrans->line("Branch Setting has been updated successfully")."!"]);
      $this->gtrans->saveNewWords();
      redirect("master-branch");
    }
  }

  function saveCabang(){
    $encId = $this->input->post("id");
    $area = $this->input->post("area");
    $branchcode  = $this->input->post("branchcode");
    $branchname  = $this->input->post("branchname");
    $strTimezone = $this->input->post("timezone");
    $arrTimezone = explode("|",base64_decode($strTimezone));
    $address = $this->input->post("address");
    $longitude = $this->input->post("longitude");
    $latitude = $this->input->post("latitude");
    $contactnumber = $this->input->post("contactnumber");
    $description = $this->input->post("description");

    $dataSource = [
      "cabang_area_id" => $area,
      "cabang_code" => $branchcode,
      "cabang_timezone" => $arrTimezone[0],
      "cabang_utc" => $arrTimezone[1],
      "cabang_name" => $branchname,
      "cabang_address" => $address,
      "longitude" => $longitude,
      "latitude" => $latitude,
      "cabang_contactnumber" => $contactnumber,
      "cabang_keterangan" => $description
    ];

    if($encId==""){
      // add
      $res = $this->cabang_model->insert($dataSource);
      if($res){
        setActivity("master branch","add");
        $this->session->set_userdata("ses_msg",["type"=>"success","header"=>"Success","msg"=> $this->gtrans->line("Branch has been added successfully")."!"]);
        $this->gtrans->saveNewWords();
        redirect("master-branch");
      }
    }else{
      // edit
      $appid = $this->session->userdata("ses_appid");

      $this->load->model("device_model");
      $this->load->library("encryption_org");
      $id = $this->encryption_org->decode($encId);
      $cabangId = $id;

      $res = $this->cabang_model->update($dataSource,$id);
      if(!empty($this->input->post("reboot")) && $this->input->post("reboot")=="yes"){
        $this->device_model->setReboot("yes",$area,$cabangId,$appid);
      }
      

      if($res){
        setActivity("master branch","edit");
        $this->session->set_userdata("ses_msg",["type"=>"success","header"=>"Success","msg"=> $this->gtrans->line("Branch has been updated successfully")."!"]);
        $this->gtrans->saveNewWords();
        redirect("master-branch");
      }
    }
  }

  function deleteCabang($encId){
    $this->load->library("encryption_org");
    $id  = $this->encryption_org->decode($encId);
    $res = $this->cabang_model->delete($id);
    if($res){
      setActivity("master branch","delete");
      $this->session->set_userdata("ses_msg",["type"=>"success","header"=>"Success","msg"=> $this->gtrans->line("Branch has been deleted successfully")."!"]);
      $this->gtrans->saveNewWords();
      redirect("master-branch");
    }
  }

  function getCabangByArea(){
    load_model(["area_model"]);

    $area = $this->input->post("area");
    $dataArea = $this->area_model->getById($area);
    $this->db->where("cabang_area_id",$area);
    $sql    = $this->cabang_model->getAll();
    $output = [];
    foreach ($sql as $row) {
      $output[] = [
        "id"  => $row->cabang_id,
        "name"=> $row->cabang_name
      ];
    }
    echo json_encode([
      "areaname"=> (!empty($dataArea->area_name)?strtoupper($dataArea->area_name):''),
      "branchs" => $output
    ]);
  }

  function checkCodeExists(){
    load_model(["cabang_model"]);
    load_library(["encryption_org"]);
    $cabangCode = $this->input->post("code");
    $appid      = $this->session->userdata("ses_appid");
    $cabang_id  = $this->encryption_org->decode($this->input->post("entityID"));
    $codeExists = $this->cabang_model->isCodeExists($cabangCode,$cabang_id,$appid);
    if($codeExists==true){
      echo "exists";
    }else{
      echo "notExists";
    }
  }

  function checkNameExists(){
    load_model(["cabang_model"]);
    load_library(["encryption_org"]);
    $cabangName = $this->input->post("code");
    $cabangid = $this->encryption_org->decode($this->input->post("entityID"));
    $appid    = $this->session->userdata("ses_appid");
    $nameExists = $this->cabang_model->isNameExists($cabangName,$cabangid,$appid);

    if($nameExists==true){
      echo "exists";
    }else{
      echo "notExists";
    }
  }
}
