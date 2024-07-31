<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Employee extends CI_Controller
{
  var $now;
  var $listMenu = "";
  var $employeeLicense = 0;
  var $filePath = FCPATH."sys_upload".DIRECTORY_SEPARATOR."user_profile".DIRECTORY_SEPARATOR;
  var $tabel_template  = array(
        'table_open'            => '<table class="table table-bordered table-stripped" >',
        'table_close'           => '</table>'
	);

  function __construct()
  {
    parent::__construct();
    $this->load->model("system_model");
    $this->now = date("Y-m-d H:i:s");

    $languange = !empty($this->session->userdata('lang')) ? $this->session->userdata('lang') :"en";
    $this->load->library("gtrans/gtrans",["lang" => $languange]);

    $this->load->model("menu_model");
    
    $this->load->model("employee_model");
    $this->load->model("employeehistory_model");
    $this->system_model->checkSession(7);
    $this->listMenu = $this->menu_model->list_menu();
    $addons = $this->session->userdata("activeaddons");

    if(!empty($addons['employeelicense'])){
      $this->employeeLicense = $addons['employeelicense'];
    }
  }

  function index(){
    $this->load->model("area_model");
    $this->load->model("cabang_model");
    $this->load->model("device_model");
    
    $this->load->helper("form");
    $this->load->library("encryption_org");
    
    $no = $this->employee_model->countAll();

    if($this->employeeLicense>0){
      if($no<$this->employeeLicense){
        $data['licenseInfo'] = '<div class="alert alert-info alert-dismissible" style="text-align:center">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                  <p>'.$this->gtrans->line('You have').' '.($this->employeeLicense - $no).' '.$this->gtrans->line('employee slot available of').' '.$this->employeeLicense.' Slot.</p>
                                </div>';
        $data['leftLicense'] = $this->employeeLicense - $no;
      }else{
        $data['licenseInfo'] = '<div class="alert alert-danger alert-dismissible" style="text-align:center">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                  <p>'.$this->gtrans->line('You have no employee slot available').'!</p>
                                </div>';
        $data['leftLicense'] = 0;
      }
    }else{
      $data['licenseInfo'] = '<div class="alert alert-danger alert-dismissible" style="text-align:center">
                              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <p>'.$this->gtrans->line('You have no employee slot or it was expired').'!</p>
                              </div>';
      $data['leftLicense'] = 0;
    }

    $sqlArea = $this->area_model->getAll();

    $cmbArea = '<select data-validation-engine="validate[required]" name="area" id="area" class="form-control"><option value="" />';
    foreach ($sqlArea as $rowArea) {
      $cmbArea .= '<option value="'.$rowArea->area_id.'" />'.$rowArea->area_name;
    }

    $cmbArea   .= '</select>';

    $data['cmbArea']     = $cmbArea;
    $data['dataArea']    = $sqlArea;

    $data["notif"] = $this->checkNotif();
    $parentViewData = [
      "title"   => $this->gtrans->line("Master Employee"),  // title page
      "content" => "master/employee",  // content view
      "viewData"=> $data,
      "listMenu"=> $this->listMenu,
      "externalCSS" => [
        base_url("asset/template/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css"),
        base_url("asset/template/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css"),
        base_url("asset/plugins/pace/pace-1.0.2/templates/pace-theme-big-counter.tmpl.css")
      ],
      "externalJS"  => [
        "https://cdn.jsdelivr.net/npm/sweetalert2@8",
        base_url("asset/template/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"),
        base_url("asset/js/tooltip.min.js"),
        base_url("asset/template/bower_components/datatables.net/js/jquery.dataTables.min.js"),
        base_url("asset/template/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js")
      ]
    ];
    
    $this->load->view("layouts/main",$parentViewData);
    $this->gtrans->saveNewWords();
  }


  function loadTableEmployee(){
    //$this->system_model->checkSession();
    $this->load->library("encryption_org");
    $sArea  = $this->input->post("sArea");
    $sCabang= $this->input->post("sCabang");
    $strCari= $this->input->post("strCari");

    $this->table->set_template($this->tabel_template);
    $this->table->set_heading(
      ["data" => '<input type="checkbox" id="checkAll" onclick="if(this.checked) {$(\':checkbox\').each(function() {this.checked = true;});}else{$(\':checkbox\').each(function() {this.checked = false;});}">', "class" => "text-left"],
      ["data" => "Code", "class" => "text-left"],
      ["data" => "Name", "class" => "text-left"],
    );
    $no  = 0;
    /*
    if($sArea!="-"){
      $this->db->where("tbemployeeareacabang.employee_area_id",$sArea);
    }

    if($sCabang!="-"){
      $this->db->where("tbemployeeareacabang.employee_cabang_id",$sCabang);
    }
    */
    if(!empty($strCari)){
      $this->db->like("tbemployee.employee_full_name",$strCari);
    }

    $sql = $this->employee_model->getAll();
    $tempId     = "";
    $tempArea   = "";
    $tempCabang = "";

    $tempLocation  = "";
    $arrData       = [];
    foreach ($sql as $index => $row) {

      $arrData[$index] = [
        "employee_id" => $row->employee_id,
        "employee_license" => $row->employee_license,
        "employee_full_name" => $row->employee_full_name,
        "employee_join_date" => $row->employee_join_date,
        "area_name"          => ucfirst($row->area_name),
        "cabang_name"        => ucfirst($row->cabang_name),
        "location"           => $row->location_status,
        "employee_is_active" => $row->employee_is_active,
        "employee_resign_date" => $row->employee_resign_date,
        "total_fingerprint" => $row->total_fingerprint,
        "total_face" => $row->total_face,
        "card"       => $row->employee_card,
        "password"   => $row->employee_password,
        "image"      => $row->image,
        "picture"    => $row->picture,
        "code"       => $row->employee_account_no
      ];
    }


    foreach ($arrData as $index => $row) {

      if($row['location']=="pending"){
        $locStatus ='text-red';
        $locStatusIcon = 'fa fa-frown-o';
      }elseif ($row['location']=="active") {
        $locStatus ='text-green';
        $locStatusIcon = 'fa fa-smile-o';
      }elseif($row['location']=="archived"){
        $locStatus ='text-red';
        $locStatusIcon = 'fa fa-frown-o';
      }

      if(!empty($arrData[$index + 1])){
        $endIndex = false;
      }else{
        $endIndex = true;
        goto process;
      }

      if($endIndex==false && $row['employee_id'] != $arrData[$index + 1]['employee_id']){
        process:

        $tempLocation .= ($row['location']!="archived") ? '<p class="'.$locStatus.'"><i class="'.$locStatusIcon.'"></i> '.$row['area_name'].' <i class="fa fa-angle-double-right"></i> '.$row['cabang_name'].'</p>' : "";

        $no++;
        $encId = $this->encryption_org->encode($row['employee_id']);
        $btnEdit = '<i style="cursor:pointer" class="fa fa-edit fa-lg color-primary"
        onclick="edit(\''.$encId.'\')"></i>';
        $btnDelete    = anchor('delete-employee/'.$encId,'<i class="fa fa-trash fa-lg "></i>',['style'=>'color:#f56954','onclick'=>'return confirm(\'Are you sure want to delete this Employee?\')']);
        $btnSetResign = '<span class="text-red" onclick="setResign(\''.$encId.'\')"
 style="cursor:pointer"><i class="fa fa-user-times"></i> Set Resign</span>';

        if($row['employee_is_active']=="0" && empty($row['employee_resign_date'])){
          $option = $btnEdit.' '.$btnDelete;
        }elseif($row['employee_is_active']=="1" && empty($row['employee_resign_date'])){
          $btnEditIdentity = '<i style="cursor:pointer" class="fa fa-edit fa-lg color-primary"
          onclick="window.open(\''.base_url("edit-employee/".$encId).'\',\'_self\')"></i>';
          $option = $btnEditIdentity." ".$btnSetResign;
        }elseif ($row['employee_is_active']=="1" && !empty($row['employee_resign_date'])) {
          $resigndate = $row['employee_resign_date'];
          $option = '<p class="text-red">Will resign at '.date("d F Y",strtotime($resigndate)).'</p>';
        }

        if($row['employee_license']=="active"){
          //$lbLicense = '<p class="text-green"><i class="fa fa-lightbulb-o"></i> Active</p>';
          $checked = 'checked';
        }elseif ($row['employee_license']=="notactive") {
          //$lbLicense = '<p class="text-red"><i class="fa fa-frown-o"></i> Not Active</p>';
          $checked = '';
        }

        $lbLicense = '<label class="switch" id="toggleSwitch'.$encId.'">
                        <input type="checkbox" '.$checked.' onclick="switchLicense(this.checked,\''.$encId.'\')">
                        <span class="slider"></span>
                      </label>';
        $template = "";
        if($row['total_face']>0){
          $template .= ' <i class="material-icons">face</i>';
        }
        if($row['total_fingerprint']>0){
          $template .= ' <i class="material-icons">fingerprint</i>';
        }
        if($row['card']!=""){
          $template .= ' <i class="material-icons">assignment</i>';
        }
        if($row['password']!=""){
          $template .= ' <i class="material-icons">vpn_key</i>';
        }

        $this->table->add_row(
          '<input type="checkbox" name="employee-id[]" value="'.$encId.'">',
          $row['code'],
          $row['employee_full_name'],
        );
        
        $tempLocation = "";
      }else{
        $tempLocation .= ($row['location']!="archived") ? '<p class="'.$locStatus.'"><i class="'.$locStatusIcon.'"></i> '.$row['area_name'].' <i class="fa fa-angle-double-right"></i> '.$row['cabang_name'].'</p>' : "";
      }
    }
    $output = $this->table->generate();
    echo json_encode($output);
  }

  function loadDTemployee(){
    load_model(["employee_model","employeeareacabang_model","employeetemplate_model"]);
    load_library(["encryption_org"]);
	$dtArea = explode("|",$this->session->userdata("ses_area"));
	$lsArea = join(",",$dtArea); 
    $sArea = $this->input->post("sArea");
    $sCabang = $this->input->post("sCabang");
    $strCari = $this->input->post("strCari");
    $appid    = $this->session->userdata("ses_appid");
    $haveTemplate = $this->input->post("haveTemplate");
    $draw   = $_REQUEST['draw'];
		$length = $_REQUEST['length'];
		$start  = $_REQUEST['start'];
		$search = $_REQUEST['search']["value"];
	
	if($this->session->userdata("ses_status")=="admin_area"){
		$allRecord  = $this->employee_model->countAvailableAllAdminArea($lsArea,$appid);
	} else {
		$allRecord  = $this->employee_model->countAvailableAll($appid);
	}
    

    $this->db->order_by("A.employee_account_no","ASC");
    
    $this->db->group_start();
    $this->db->like("A.employee_full_name",$strCari);
    $this->db->or_like("A.employee_account_no",$strCari);
    $this->db->group_end();
    if($haveTemplate!=""){
      if($haveTemplate==1){
        $stringFilter = "> '0'";
      }elseif ($haveTemplate==2) {
        $stringFilter = "= '0'";
      }
      $this->db->where("(select count(tbemployeetemplate.employeetemplate_id) as total from tbemployeetemplate where tbemployeetemplate.employeetemplate_employee_id = A.employee_id) ".$stringFilter,null,false);
    }
    
	if($this->session->userdata("ses_status")=="admin_area"){
		$sql = $this->employee_model->getAvailableAdminArea($lsArea,$sArea,$sCabang,$start,$length,$appid);
		$this->db->like("A.employee_full_name",$strCari);
		$recordsFiltered = $this->employee_model->countAvailableFilteredAdminArea($lsArea,$sArea,$sCabang,$appid);
	} else {
		$sql = $this->employee_model->getAvailable($sArea,$sCabang,$start,$length,$appid);
		$this->db->like("A.employee_full_name",$strCari);
		$recordsFiltered = $this->employee_model->countAvailableFiltered($sArea,$sCabang,$appid);
	}

    

    $output['recordsTotal']    = $allRecord;
    $output['recordsFiltered'] = $recordsFiltered;

    $no  = $start;
    $output['data'] = array();
    foreach ($sql->result() as $row) {
      $no++;
      $arrLocation = $this->employeeareacabang_model->getLocationName($row->employee_id,$appid);
      $location    = '';
      foreach ($arrLocation as $rowLocation) {
        $activeLocation = ($rowLocation->status=="active") ? '<i class="fa fa-lightbulb-o text-green"></i>' : '<i class="fa fa-warning text-red icon-location-status"></i>';
        $location .= $activeLocation.' '.$rowLocation->area_name.' <i class="fa fa-long-arrow-right "></i> '.$rowLocation->cabang_name.' (radius scan: '.$rowLocation->employeeareacabang_radius.' meter)<br>';
      }
      $templates = $this->employeetemplate_model->getEmployeeTemplate($row->employee_id);
      $faceTemplate   = 0;
      $fingerTemplate = 0;
      $visibleFace = 0;
      $palm = 0;
      foreach ($templates->result() as $row1) {
        if($row1->employeetemplate_jenis == "fingerprint"){
          $fingerTemplate++;
        }elseif ($row1->employeetemplate_jenis == "face") {
          $faceTemplate++;
        }elseif ($row1->employeetemplate_jenis == "visible_light_face") {
          $visibleFace++;
        }elseif ($row1->employeetemplate_jenis == "palm") {
          $palm++;
        }
      }

      $template = "";

      if($faceTemplate>0 || $visibleFace>0){
        $template .= ' <i class="material-icons">face</i>';
      }
      if($fingerTemplate>0){
        $template .= ' <i class="material-icons">fingerprint</i>';
      }
      if($palm>0){
        $template .= ' <i class="material-icons">pan_tool</i>';
      }
      if($row->employee_card!=""){
        $template .= ' <i class="material-icons">assignment</i>';
      }
      if($row->employee_password!=""){
        $template .= ' <i class="material-icons">vpn_key</i>';
      }
      $encId = $this->encryption_org->encode($row->employee_id);
      $btnEdit = '<i style="cursor:pointer" class="fa fa-edit fa-lg color-primary"
      onclick="edit(\''.$encId.'\')"></i>';
      $btnDelete    = '<span class="text-red" style="cursor:pointer" onclick="delEmployee(\''.$encId.'\')"><i class="fa fa-trash fa-lg "></i></span>';

      $btnSetResign = '<span class="text-red" onclick="setResign(\''.$encId.'\')"
style="cursor:pointer"><i class="fa fa-user-times"></i> Set Resign</span>';

      if($row->employee_is_active=="0" && empty($row->employee_resign_date)){
        $option = $btnEdit.' '.$btnDelete;
      }elseif($row->employee_is_active=="1" && empty($row->employee_resign_date)){
        $btnEditIdentity = '<i style="cursor:pointer" class="fa fa-edit fa-lg color-primary"
        onclick="window.open(\''.base_url("edit-employee/".$encId).'\',\'_self\')"></i>';
        $option = $btnEditIdentity." ".$btnSetResign;
      }elseif ($row->employee_is_active=="1" && !empty($row->employee_resign_date)) {
        $resigndate = $row->employee_resign_date;
        $option = '<p class="text-red">'.$this->gtrans->line("Will resign at").' '.date("d F Y",strtotime($resigndate)).'</p>';
      }

      if($location==""){
        $location = '<span class="text-red"><i class="fa  fa-ban"></i> Not Set</span>';
      }
      
      if($row->employee_level==14){
        $admin = '<i class="material-icons icon-gold" >stars</i>';
      }else{
        $admin = '';
      }
      $chkbox = '<input type="checkbox" name="employee-id[]" value="'.$encId.'">';
      $output['data'][] = array(
        $chkbox,
        '<a href="#" onclick="showDetail(\''.$encId.'\')">'.$row->employee_account_no.'</a>',
        ($row->image!="") ? '<img src="'.base_url("sys_upload/employeepic/".$row->image).'" class="img-circle" width="50"></img>' : "",
        $row->employee_full_name.' '.$admin,
        date("Y-m-d",strtotime($row->employee_join_date)),
        $template,
        $location,
        $option
      );
		}
    $this->gtrans->saveNewWords();
    echo json_encode($output);
  }

  function edit_employee($encId){
    load_model(["employee_model"]);
    $this->load->library("encryption_org");
    $this->load->library("form_validation");
    $this->form_validation->set_rules("submit","submit","required");
    if($this->form_validation->run()==true){

      $fullname   = $this->input->post("fullname");
      $nickname   = $this->input->post("nickname");
      $password   = $this->input->post("password");
      $gender       = $this->input->post("gender");
      $birthday     = !empty($this->input->post("birthday")) ? date('Y-m-d',strtotime($this->input->post("birthday"))) : "";
      $phoneNumber  = $this->input->post("phone-number");
      $email        = $this->input->post("email");
      $address      = $this->input->post("address");
      $intraxPin    = $this->input->post("intrax-pin");
	  $method   = $this->input->post('method');
	  $strMethod= !empty($method) ? implode("|", $method) : "";
      $presenceMode    = $this->input->post("presence_mode");
      $presenceLocation    = $this->input->post("presence_location");
      $inputLevel = !empty($this->input->post("level")) ? $this->input->post("level") : "";
      $employee_id= $this->encryption_org->decode($encId);

      $dataUpdate = [
        "employee_full_name" => $fullname,
        "employee_nick_name" => $nickname,
        "gender" =>$gender,
        "birthday" => $birthday,
        "phone_number" => $phoneNumber,
        "email" => $email,
        "address" => $address,
        "intrax_pin" => $intraxPin,
        "presence_method" => $strMethod,
        "presence_mode" => $presenceMode,
        "presence_location" => $presenceLocation
      ];

      //if($password!=""){
        $dataUpdate["employee_password"] = $password;
      //}

      if(!empty($inputLevel) && $inputLevel=="admin"){
        $dataUpdate["employee_level"] = "14";
      }else{
        $dataUpdate["employee_level"] = "0";
      }
      $appid = $this->session->userdata("ses_appid");
      $res   = $this->employee_model->update($dataUpdate,$employee_id,$appid);
      
      if($res){
        load_model(["employeelocationdevice_model"]);
        $res1 = $this->employeelocationdevice_model->setNeedUpdate([$employee_id],"yes");
        if($res1){
          $this->session->set_userdata("ses_msg",["type"=>"success","header"=>"success","msg"=> $this->gtrans->line("Employee has been updated successfully")."!"]);
          redirect("master-employee");
        }
      }
    }

    $employee_id = $this->encryption_org->decode($encId);
    $detailEmployee = $this->employee_model->getById($employee_id);
    $data['detailEmployee'] = $detailEmployee;

    $parentViewData = [
      "title"   => "Edit Employee",  // title page
      "content" => "master/edit_employee",  // content view
      "viewData"=> $data,
      "listMenu"=> $this->listMenu,
      "externalCSS" => [
        base_url("asset/template/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css")
      ],
      "externalJS"  => [
        base_url("asset/template/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"),
        base_url("asset/js/tooltip.min.js")
      ]
    ];

    $this->load->view("layouts/main",$parentViewData);
    $this->gtrans->saveNewWords();
  }

  function getDataEdit(){
    $this->load->model("employeeareacabang_model");
    $this->load->library("encryption_org");
    $encId = $this->input->post("id");
    $id    = $this->encryption_org->decode($encId);
    $dataEmployee = $this->employee_model->getById($id);
    if($dataEmployee){
      $dataLocation = $this->employeeareacabang_model->getPendingLocation($id);

      $arrArea      = [];
      $arrCabang    = [];
      $arrRadius    = [];

      if($dataLocation){
        foreach ($dataLocation as $row) {
          if(!in_array($row->employee_area_id,$arrArea)){
            $arrArea[] = $row->employee_area_id;
          }
          if(!in_array($row->employee_cabang_id,$arrCabang)){
            $arrCabang[] = $row->employee_cabang_id;
            $arrRadius[] = $row->employeeareacabang_radius;
          }
        }
      }

      $level  = ($dataEmployee->employee_level==14) ? "admin" : "user";

      $output = [
        "accountno" => $dataEmployee->employee_account_no,
        "fullname"  => $dataEmployee->employee_full_name,
        "nickname"  => $dataEmployee->employee_nick_name,
        "password"  => $dataEmployee->employee_password,
        "joindate"  => date('d-m-Y',strtotime($dataEmployee->employee_join_date)),
        "area"      => $arrArea,
        "cabang"    => $arrCabang,
        "radius"    => $arrRadius,
        "level"     => $level,
        "gender"    => $dataEmployee->gender,
        "birthday"  => date('d-m-Y',strtotime($dataEmployee->birthday)),
        "phone_number" => $dataEmployee->phone_number,
        "email"     => $dataEmployee->email,
        "address"   => $dataEmployee->address,
        "intrax_pin"=> $dataEmployee->intrax_pin,
        "presence_method"=> $dataEmployee->presence_method,
        "presence_mode"=> $dataEmployee->presence_mode,
        "employee_photo"=> $dataEmployee->employee_photo,
        "presence_location"=> $dataEmployee->presence_location
      ];
      
      echo json_encode($output);
    }
  }
  
  function importPhotoprofile(){
    $this->load->library("encryption_org");
    $encId = $this->input->post("id");
    $upload   = $this->do_upload();
    $fileName = $upload["filename"];

    if($fileName!=""){
		$dataSource= [
			"employee_photo"=> $fileName
		];
		//checkif sheet exist
		$employeeID = $this->encryption_org->decode($encId);
		$this->employee_model->update($dataSource,$employeeID);
    }else{
		$output = [
            "response" => "error",
            "code" => "500",
            "msg" => "failed upload"
          ];
		echo json_encode($output);
    }
  }

  function saveEmployee(){
    $this->load->library("encryption_org");
    $this->load->model("employeeareacabang_model");
    $encId = $this->input->post("id");
    $accountno = $this->input->post("accountno");
    $fullname  = $this->input->post("fullname");
    $nickname  = $this->input->post("nickname");
    $joindate  = date('Y-m-d H:i:s',strtotime($this->input->post("joindate")));
    //$area      = !empty($this->input->post("area"))  ? $this->input->post("area")  : "";
    $cabang    = !empty($this->input->post("cabang"))? $this->input->post("cabang"): "";
    $appid     = $this->session->userdata("ses_appid");
    $level     = !empty($this->input->post("level")) ? $this->input->post("level") : "";
    $password  = $this->input->post("password");

    $gender       = $this->input->post("gender");
    $birthday     = !empty($this->input->post("birthday")) ? date('Y-m-d',strtotime($this->input->post("birthday"))) : "";
    $phoneNumber  = $this->input->post("phone-number");
    $email        = $this->input->post("email");
    $address      = $this->input->post("address");
    $intraxPin    = $this->input->post("intrax-pin");
	$method       = $this->input->post("method");
	$strMethod	  = !empty($method) ? implode("|", $method) : null;
    $presenceMode = $this->input->post("presence_mode");
    $presenceLocation = $this->input->post("presence_location");
    
    if($this->employee_model->checkAccountNoExist($accountno,$this->encryption_org->decode($encId))==true){
      $msg = "Duplicate Account No, Please Use another Account No (PIN)!";
      $output = [
            "response" => "error",
            "code" => "500",
            "msg" => $msg
          ];
      echo json_encode($output);
    }else{
		$dataSource= [
			"employee_account_no"=> $accountno,
			"employee_full_name" => $fullname,
			"employee_nick_name" => $nickname,
			"employee_join_date" => $joindate,
			"gender" =>$gender,
			"birthday" => $birthday,
			"phone_number" => $phoneNumber,
			"email" => $email,
			"address" => $address,
			"intrax_pin" => $intraxPin,
			"presence_method" => $strMethod,
			"presence_mode" => $presenceMode,
			"presence_location" => $presenceLocation
		];
      
      //if($password!=""){
        $dataSource["employee_password "] = $password;
      //}

      if(!empty($level) && $level=="admin"){
        $dataSource["employee_level"] = "14";
      }else{
        $dataSource["employee_level"] = "0";
      }

      if($encId==""){
        // add
        $this->employee_model->insert( $dataSource);
        $employeeID = $this->db->insert_id();
        $mode = "add";
        setActivity("master employee","add");
        $this->employeehistory_model->insert($employeeID,$this->now,"add");
      }else{
        // edit
        $employeeID = $this->encryption_org->decode($encId);
        $this->employee_model->update($dataSource,$employeeID);

        $this->employeeareacabang_model->deleteAreaCabang($employeeID);
        
        $mode = "update";
        setActivity("master employee","edit");
        $this->employeehistory_model->insert($employeeID,$this->now,"edit");
      }

      $no = 0;
      if ($cabang) {
        $this->load->model("device_model");
        $this->load->model("firewall_model");
        if($mode=="update"){
          // jika actionnya update maka delete lokasi yang lama
          //$this->employeeareacabang_model->deleteAreaCabang($employeeID,$appid);
        }
        $deviceToOpen = []; 
        foreach ($cabang as $lokasi) {

          $no++;
          $arrLokasi = explode(".",$lokasi);
          $areaID    = $arrLokasi[0];
          $cabangID  = $arrLokasi[1];
		  $radius    = !empty($this->input->post("radius".$arrLokasi[1]))? $this->input->post("radius".$arrLokasi[1]): "";
          $areaCabangInsert =[
            "employeeareacabang_employee_id"=> $employeeID,
            "employee_area_id"=> $areaID,
            "employee_cabang_id"=> $cabangID,
            "employeeareacabang_effdt"=> $joindate,
            "status"=> "pending",
            "employeeareacabang_radius"=> $radius
          ];
          
          // open firewall
          $sqlDevice = $this->device_model->getByLocation($areaID,$cabangID,$appid);
          foreach ($sqlDevice->result() as $rowDevice) {
            $this->firewall_model->setSchedule($rowDevice->device_id,$joindate);
          }
          //

          if($mode=="add"){
            $this->employeeareacabang_model->insert($areaCabangInsert);
          }elseif ($mode=="update") {
            $this->employeeareacabang_model->insert($areaCabangInsert);
          }

        }
        $finishProcess ="ok";
      }else{
        $finishProcess ="nocabang";
      }

      if($finishProcess){
        if($no>=count($cabang)){
          if($mode=="add"){
            $msg = ["type"=>"success","header"=>"success","msg"=> $this->gtrans->line("Employee has been added successfully")."!"];
          }elseif ($mode=="update") {
            $msg = ["type"=>"success","header"=>"success","msg"=> $this->gtrans->line("Employee has been updated successfully")."!"];
          }

          $output = [
            "response" => "ok",
            "code" => "200",
            "msg" => $msg
          ];
        }else{
          if($mode=="add"){
            $msg = ["type"=>"success","header"=>"success","msg"=> $this->gtrans->line("Employee has been added successfully")."!"];
          }elseif ($mode=="update") {
            $msg = ["type"=>"success","header"=>"success","msg"=> $this->gtrans->line("Employee has been updated successfully")."!"];
          }
          $output = [
            "response" => "ok",
            "code" => "200",
            "msg" => $msg
          ];
        }
        $this->gtrans->saveNewWords();
        echo json_encode($output);
      }
    }
  }

  function checkNotif(){
    if(!empty($this->session->userdata("ses_msg"))){
      $msg = $this->session->userdata("ses_msg");
      $notif =createNotif($msg['type'],$msg['header'],$msg['msg']);
      $this->session->set_userdata("ses_msg");
      return $notif;
    }else{
      return "";
    }
  }
  function deleteEmployee($encId){
    $this->load->model("employeeareacabang_model");
    $this->load->library("encryption_org");
    $id  = $this->encryption_org->decode($encId);
    $res = $this->employee_model->delete($id);

    if($res){
      $this->employeeareacabang_model->setArchive($id);
      setActivity("master employee","delete");
      $this->session->set_userdata("ses_msg",["type"=>"success","header"=>"success","msg"=> $this->gtrans->line("Employee has been deleted successfully")."!"]);
      redirect("master-employee");
    }
  }

  function switchLicense(){
    $this->load->library("encryption_org");
    $encEmployee = $this->input->post("employee");
    $status      = $this->input->post("status");
    $employeeID  = $this->encryption_org->decode($encEmployee);
    if($status=="active"){
      $licenseUsed = $this->employee_model->getLicenseUsed();
      if($licenseUsed<$this->employeeLicense){
        $this->employee_model->changeLicenseTo("active",$employeeID);
        echo "success";
      }else{
        echo "failed";
      }
    }elseif ($status=="inactive") {
      $this->employee_model->changeLicenseTo("notactive",$employeeID);
      echo "success";
    }
  }

  function setResign(){
    load_library(["encryption_org"]);
    load_model(["employee_model","employeeresign_model","employeehistory_model","employeeareacabang_model"]);

    $encId = $this->input->post("id");
    $postDate = $this->input->post("dateresign");
    $dateresign = date("Y-m-d H:i:s",strtotime($postDate));
    $appid = $this->session->userdata("ses_appid");
    $employeeId = $this->encryption_org->decode($encId);

    // set resign di tabel karyawan
    $res  = $this->employee_model->setResign($employeeId,$dateresign);

    // set date archive di employee area cabang
    $res1 = $this->employeeareacabang_model->setDateArchive($employeeId,$dateresign,$appid);

    // insert jadwal resign ke tabel employee resign
    //$res1 = $this->employeeresign_model->setResign($employeeId,$dateresign);

    // insert history employee
    //$res3 = $this->employeehistory_model->insert($employeeId,$this->now,"resign");

    // hitung ulang pending resign untuk master area dan cabang
    /*
    $listLocation = $this->employeeareacabang_model->getListActiveEmployeeLocation($employeeId,$appid);

    $arrUpdateAreaBatch = [];
    $arrUpdateCabangBatch = [];
    foreach ($listLocation['area'] as $rowArea) {
      $areaCount = $this->employeeareacabang_model->countAreaPendingResign($rowArea,$appid);
      $arrUpdateAreaBatch[] = [
        "area_id" => $rowArea,
        "area_total_emp_pending_resign" => $areaCount
      ];
    }

    foreach ($listLocation['cabang'] as $rowCabang) {
      $cabangCount = $this->employeeareacabang_model->countCabangPendingResign($rowCabang,$appid);
      $arrUpdateCabangBatch[] = [
        "cabang_id" => $rowCabang,
        "cabang_total_emp_pending_resign" => $cabangCount
      ];
    }
    */
    // mengupdate data di master dengan update batch

    if($res&&$res1){
      echo "ok";
    }
  }

  function checkAccountNoExist(){
    load_model(['employee_model']);
    $noAccount = $this->input->post("no_account");
    if($this->employee_model->checkAccountNoExist($noAccount)==true){
      echo "yes";
    }else{
      echo "no";
    }
  }
  
  function checkEmailNoExist(){
    load_model(['employee_model']);
    $email = $this->input->post("email_addr");
    if($this->employee_model->checkEmailNoExist($email)==true){
      echo "yes";
    }else{
      echo "no";
    }
  }

   public function loadDetailEmployee()
   {
     load_library(["encryption_org"]);
     load_model(["employee_model","employeeareacabang_model","employeetemplate_model"]);

     $encId = $this->input->post("id");
     $employeeID   = $this->encryption_org->decode($encId);
     $employeeData = $this->employee_model->getById($employeeID);

     if($employeeData){
       $locationData = $this->employeeareacabang_model->getActiveLocationByEmployee($employeeID);
       $sqlTemplate  = $this->employeetemplate_model->getEmployeeTemplate($employeeID);
       $fingerCount  = 0;
       $faceCount    = 0;
       $visibleFaceCount = 0;
       $palmCount = 0;

       foreach ($sqlTemplate->result() as $row) {
         if($row->employeetemplate_jenis=="fingerprint"){
           $fingerCount++;
         }elseif($row->employeetemplate_jenis=="face"){
           $faceCount++;
         }elseif($row->employeetemplate_jenis=="visible_light_face"){
           $visibleFaceCount++;
         }elseif($row->employeetemplate_jenis=="palm"){
           $palmCount++;
         }
       }

       $this->table->set_template($this->tabel_template);
       $this->table->set_heading(
         $this->gtrans->line("Area"),
         $this->gtrans->line("Branch"),
         $this->gtrans->line("Device Name"),
         "SN",
         "FP",
         $this->gtrans->line("FACE")
       );
       $tempArea  = "";
       $tempBranch= "";
       foreach ($locationData->result() as $row) {
         $rangeActive = dateDifference($this->now,$row->device_last_communication);
         if($row->device_last_communication!=null && str_replace("-","",$rangeActive["minute"])<2){
           $deviceStatus = 'text-green';
         }else{
           $deviceStatus = 'text-red';
         }

         $this->table->add_row(
           ($row->area_name!=$tempArea)?$row->area_name:"",
           ($row->cabang_name!=$tempBranch)?$row->cabang_name:"",
           $row->device_name,
           '<i class="fa fa-circle '.$deviceStatus.'"></i> '.$row->device_SN,
           $row->total_fingerprint,
           (($row->total_face>=12) ? 1 : 0)
         );
         $tempArea   = $row->area_name;
         $tempBranch = $row->cabang_name;
       }

       $faceCount = (($faceCount>=12)?1:0);
       $strLocation = $this->table->generate();
       $output = '
       <div class="col-md-4">
        <div class="row">
          <div class="col-md-4">
            <img src="'.(($employeeData->image!="")?base_url("sys_upload/employeepic/".$employeeData->image) : base_url("asset/images/nopicture.png")).'" width="80px">
          </div>
          <div class="col-md-8">
            <p class="detail-label">'.$this->gtrans->line("Employee Code").'</p>
            <p class="detail-content">'.$employeeData->employee_account_no.'</p>
            <p class="detail-label">'.$this->gtrans->line("Full Name").'</p>
            <p class="detail-content">'.$employeeData->employee_full_name.'</p>
            <p class="detail-label">'.$this->gtrans->line("Nick Name").'</p>
            <p class="detail-content">'.$employeeData->employee_nick_name.'</p>
            <p class="detail-label">'.$this->gtrans->line("Join Date").'</p>
            <p class="detail-content">'.date("d F Y",strtotime($employeeData->employee_join_date)).'</p>
            <p class="detail-label">'.$this->gtrans->line("Verification Agent").'</p>
            <table>
              '.(($faceCount>0)?'<tr><td><i class="material-icons">face</i></td><td>'.$faceCount.'</td></tr>':'').'
              '.(($visibleFaceCount>0)?'<tr><td><i class="material-icons">face</i></td><td>'.$visibleFaceCount.'</td></tr>':'').'
              '.(($fingerCount>0)?'<tr><td><i class="material-icons">fingerprint</i></td><td>'.$fingerCount.'</td></tr>':'').'
              '.(($palmCount>0)?'<tr><td><i class="material-icons">pan_tool</i></td><td>'.$palmCount.'</td></tr>':'').'
              '.(($employeeData->employee_card!="")? '<tr><td><i class="material-icons">assignment</i></td><td>'.$employeeData->employee_card.'</td></tr>' :'').'

              '.(($employeeData->employee_password!=0)?'<tr><td><i class="material-icons">vpn_key</i></td><td>'.$employeeData->employee_password.'</td></tr>':'').'

            </table>
          </div>
        </div>
        <div class="row">

        </div>
       </div>
       <div class="col-md-8">
        <p class="detail-label">Template Distribution <a href="'.base_url("master/employee/history_push/".$this->encryption_org->encode($employeeID)).'" target="_blank">History Tempalate Push</a></p>
        '.$strLocation.'
       </div>
       ';
       $this->gtrans->saveNewWords();
       echo json_encode($output);
     }
   }

   function redistribute(){
     try {
       load_library(["encryption_org"]);
       load_model(["employeelocationdevice_model","employeetemplate_model"]);
       $encId = $this->input->post("id");
       //$type  = $this->input->post("type");
       $employeeID = $this->encryption_org->decode($encId);

       // set need update employee
       $this->employeelocationdevice_model->setNeedUpdate([$employeeID],"yes");

       // set need update picture
       $this->employeelocationdevice_model->setPicNeedUpdate([$employeeID]);
       //
       $this->employeelocationdevice_model->rePushTemplate([$employeeID]);
       echo "finish";
     } catch (\Exception $e) {
       var_dump($e->getMessage());
     }
   }

   function redistributeAll(){
     load_library(["encryption_org"]);
     load_model(["employeelocationdevice_model","employeetemplate_model"]);
     $arrEncEmpID = [];
     $arrEncEmpID = $this->input->post("employee-id");
     //$type  = $this->input->post("type");
     if(count($arrEncEmpID)>0){

       $arrEmpID = [];
       foreach ($arrEncEmpID as $encEmployeeID) {
         $employeeID = $this->encryption_org->decode($encEmployeeID);
         // set need update employee
         $arrEmpID[] = $employeeID;
         // set need update picture
       }
       $this->employeelocationdevice_model->setNeedUpdate($arrEmpID,"yes");
       $this->employeelocationdevice_model->rePushTemplate($arrEmpID);
       $this->employeelocationdevice_model->setPicNeedUpdate($arrEmpID);
       echo "finish";
     }else{
       echo "noneselected";
     }

   }
   
   function deleteTempAll(){
     load_library(["encryption_org"]);
     load_model(["employeelocationdevice_model","employeetemplate_model","employee_model","employeeareacabang_model"]);
     $arrEncEmpID = [];
     $arrEncEmpID = $this->input->post("employee-id");
     //$type  = $this->input->post("type");
     if(count($arrEncEmpID)>0){

       $arrEmpID = [];
       foreach ($arrEncEmpID as $encEmployeeID) {
         $employeeID = $this->encryption_org->decode($encEmployeeID);
         // set need update employee
         $arrEmpID[] = $employeeID;
         // set need update picture
       }
	   // set resign karyawan untuk menghapus karyawan dari device
       $this->employee_model->setResignAll($arrEmpID);
       // menghapus template dalam device
       $this->employeelocationdevice_model->setRemoveDeviceTemplate($arrEmpID);
       // menghapus template dalam server
       $this->employeetemplate_model->setDeleteTemplate($arrEmpID);
	   // set active karyawan untuk push ulang ke device
       $this->employee_model->setActiveAll($arrEmpID);
	   // set status pending cabang karyawan untuk push ulang ke device
       $this->employeeareacabang_model->setPendingAll($arrEmpID);
	   // update employee ke device
       $this->employeelocationdevice_model->setNeedUpdate($arrEmpID,"yes");
       echo "finish";
     }else{
       echo "noneselected";
     }

   }

   function history_push($id){
    $this->load->model("historytemplatepush_model");
    $id = $this->encryption_org->decode($id);
    $sql = $this->historytemplatepush_model->get($id);
    $this->table->set_heading([
      "Date",
      "Template Type",
      "Template Index",
      "Device SN"
    ]);
    $this->table->set_template($this->tabel_template);
    foreach ($sql->result() as $row) {
      $this->table->add_row(
        $row->date_create,
        $row->employeetemplate_jenis,
        $row->employeetemplate_index,
        $row->device_SN
      );
    }
    $data["table"] = $this->table->generate();
    $parentViewData = [
      "title"   => $this->gtrans->line("Master Employee"),  // title page
      "content" => "master/history_push",  // content view
      "viewData"=> $data,
      "listMenu"=> $this->listMenu,
      "externalCSS" => [
        base_url("asset/template/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css"),
        base_url("asset/template/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css"),
        base_url("asset/plugins/pace/pace-1.0.2/templates/pace-theme-big-counter.tmpl.css")
      ],
      "externalJS"  => [
        "https://cdn.jsdelivr.net/npm/sweetalert2@8",
        base_url("asset/template/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"),
        base_url("asset/js/tooltip.min.js"),
        base_url("asset/template/bower_components/datatables.net/js/jquery.dataTables.min.js"),
        base_url("asset/template/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js")
      ]
    ];
    
    $this->load->view("layouts/main",$parentViewData);
    $this->gtrans->saveNewWords();
   }

   function pushSelectedFilter(){
    $this->load->model("employeeareacabang_model");
    $appid = $this->session->userdata("ses_appid");
    $area = $this->input->post("area");
    $cabang = $this->input->post("cabang");
    $filterTemplate = $this->input->post('filterTemplate');
    if($filterTemplate==1){
      $this->db->where("(select count(tbemployeetemplate.employeetemplate_id) as total from tbemployeetemplate where tbemployeetemplate.employeetemplate_employee_id = B.employee_id) !=",0);
    }
    $dataEmployee = $this->employeeareacabang_model->getEmployeeActiveByLocation($area,$cabang,$appid);

    
    $arrEmpID = [];
    
    foreach($dataEmployee->result() as $row){
      $arrEmpID[] = $row->employee_id;
    }

    
    load_model(["employeelocationdevice_model","employeetemplate_model"]);
    
    //$type  = $this->input->post("type");
    if(count($arrEmpID)>0){
      $this->employeelocationdevice_model->setNeedUpdate($arrEmpID,"yes");
      $this->employeelocationdevice_model->rePushTemplate($arrEmpID);
      $this->employeelocationdevice_model->setPicNeedUpdate($arrEmpID);
      echo "finish";
    }else{
      echo "noneselected";
    }
  }
  
  function do_upload(){
    $config['upload_path']="./sys_upload/user_profile";
    $config['allowed_types']='jpg';
    $config['encrypt_name'] = TRUE;
     $config['max_size']    = 2048; // 2mb
    $this->load->library('upload',$config);
    if($this->upload->do_upload("photoprofile")){
      $data = array('upload_data' => $this->upload->data());

      $judul= $this->input->post('judul');
      $fileName = $data['upload_data']['file_name'];
      $error    = "";
    }else{
      $fileName = "";
      $error    = strip_tags($this->upload->display_errors());
    }
    return [
      "error"    => $error,
      "filename" => $fileName
    ];
  }

}
