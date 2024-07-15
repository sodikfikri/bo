<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Prospective_employees extends CI_Controller
{
  var $now;
  var $listMenu = "";
  var $reader;
  var $employeeLicense = 0;
  var $filePath = FCPATH."sys_upload".DIRECTORY_SEPARATOR."user_profile".DIRECTORY_SEPARATOR;
  var $filePathEmp = FCPATH."sys_upload".DIRECTORY_SEPARATOR."temp".DIRECTORY_SEPARATOR;
  var $tabel_template  = array(
        'table_open'            => '<table class="table table-bordered table-stripped" >',
        'table_close'           => '</table>'
	);

  function __construct()
  {
    parent::__construct();
    $this->load->model("system_model");
    $this->now = date("Y-m-d H:i:s");
    $this->timestamp = date("Y-m-d H:i:s");

    $languange = !empty($this->session->userdata('lang')) ? $this->session->userdata('lang') :"en";
    $this->load->library("gtrans/gtrans",["lang" => $languange]);

    $this->load->model("menu_model");
    
    $this->load->model("prospective_employees_model");
    $this->load->model("employeehistory_model");
    $this->system_model->checkSession(101);
    $this->listMenu = $this->menu_model->list_menu();
    $this->reader   = \PhpOffice\PhpSpreadsheet\IOFactory::createReader("Xls");
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

  }
  
  function view_prospective($encSystemAddonsCode){
    $this->load->model("area_model");
    $this->load->model("cabang_model");
    $this->load->model("device_model");

    $this->load->helper("form");
    $this->load->library("encryption_org");
	  $no = $this->prospective_employees_model->countAll();

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

    $data["notif"] = $this->checkNotif();
    $parentViewData = [
      "title"   => $this->gtrans->line("Register Prospective Employees"),  // title page
      "content" => "master/prospective_employees",  // content view
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
    $this->load->model("prospective_employees_model");
    $this->load->library("encryption_org");
    $sArea  = $this->input->post("sArea");
    $sCabang= $this->input->post("sCabang");
    $strCari= $this->input->post("strCari");

    $this->table->set_template($this->tabel_template);
    $this->table->set_heading(
      ["data" => "NIP", "class" => "text-left"],
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
	
	

    $sql = $this->prospective_employees_model->getAll();
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
        $btnDelete    = anchor('delete-employee-intrax/'.$encId,'<i class="fa fa-trash fa-lg "></i>',['style'=>'color:#f56954','onclick'=>'return confirm(\'Are you sure want to delete this Employee?\')']);
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
    load_model(["prospective_employees_model","employeeareacabang_model","employeetemplate_model"]);
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
	$appid          = $this->session->userdata("ses_appid");
    $dataUrl		= $this->encryption_org->decode($this->uri->segment(2));
	
	// $allRecord  = $this->prospective_employees_model->countAvailableAll($appid,$dataUrl);
	$allRecord  = $this->prospective_employees_model->countAvailableAll_temp($appid,$dataUrl);
    

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
    
	$sql = $this->prospective_employees_model->getAvailable_temp($sArea,$sCabang,$start,$length,$appid,$dataUrl);
	// $sql = $this->prospective_employees_model->getAvailable($sArea,$sCabang,$start,$length,$appid,$dataUrl);
	$this->db->like("A.employee_full_name",$strCari);
	$recordsFiltered = $this->prospective_employees_model->countAvailableFiltered_temp($sArea,$sCabang,$appid,$dataUrl);
	// $recordsFiltered = $this->prospective_employees_model->countAvailableFiltered($sArea,$sCabang,$appid,$dataUrl);

    

    $output['recordsTotal']    = $allRecord;
    $output['recordsFiltered'] = $recordsFiltered;

    $no  = $start;
    $output['data'] = array();
	
	$dataRequest = [
		"nama_pengguna" => "intrax_mobile",
		"kata_sandi" => "Pattimuraraya01*"
	  ];
	$jsonDataRequest = json_encode($dataRequest);
	//get access token
	  $curls = curl_init();
	  curl_setopt_array($curls, array(
		CURLOPT_URL => 'https://apimws.malukuprov.go.id/api/admin/akun/otentikasi/login',
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'POST',
		CURLOPT_POSTFIELDS => $jsonDataRequest,
		CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json'
		),
	  ));
	  $responseToken = curl_exec($curls);
	  $arrResponseToken = json_decode($responseToken);
    foreach ($sql->result() as $row) {
      $no++;
      $encId = $this->encryption_org->encode($row->employee_id);
      $btnEdit = '<i style="cursor:pointer" class="fa fa-edit fa-lg color-primary"
      onclick="edit(\''.$encId.'\')"></i>';
      $btnDelete    = '<span class="text-red" style="cursor:pointer" onclick="delEmployee(\''.$encId.'\')"><i class="fa fa-trash fa-lg "></i></span>';

      $option = $btnEdit.' '.$btnDelete;
      
      if($row->employee_level==14){
        $admin = '<i class="material-icons icon-gold" >stars</i>';
      }else{
        $admin = '';
      }
	  
	  //get data from simpeg
	  $curl = curl_init();
	  curl_setopt_array($curl, array(
		CURLOPT_URL => 'https://apimws.malukuprov.go.id/api/data/pegawai/'.$row->employee_account_no,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => '',
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => 'GET',
		CURLOPT_HTTPHEADER => array(
			'Authorization: Bearer '.$arrResponseToken->access_token,
			'Content-Type: application/json'
		),
	  ));
	  $responseCheck = curl_exec($curl);
	  $arrResponseCheck = json_decode($responseCheck);
	  $statusData="correct";
	  if(!empty($arrResponseCheck->data)){
		  if($row->employee_full_name==$arrResponseCheck->data->nama){
			$lbCheck = '<p class="text-green"><i class="fa fa-check"></i> '.$this->gtrans->line("Name is correct").'</p>';
			$recomendationName = '';
			$realName = $row->employee_full_name;
			$buttonCorrection = '';
			$chkbox = '<input type="checkbox" name="employee-id[]" value="'.$encId.'">';
		  }else{
			$lbCheck = '<p class="text-red"><i class="fa fa-times"></i> '.$this->gtrans->line("Name is not correct").'</p>';
			$recomendationName = '<p class="text-green" style="margin-top: -10px">'.$arrResponseCheck->data->nama.'</p>';
			$realName = '<p class="text-red" style="text-decoration: line-through">'.$row->employee_full_name.'</p>';
			$buttonCorrection = '<button onclick="dataCorrection(\''.$row->employee_id.'\',\''.$arrResponseCheck->data->nama.'\')" type="button" class="btn btn-primary btn-sm">'.$this->gtrans->line("accept correction").'</button>';
			$chkbox = '';
		  } 
	  } else {
		$lbCheck = '<p class="text-red"><i class="fa fa-times"></i> '.$this->gtrans->line("NIP is not valid").'</p>';
		$recomendationName = '';
		$realName = $row->employee_full_name;
		$buttonCorrection = '';
		$chkbox = '';
	  }
	  
      $output['data'][] = array(
		$chkbox,
        '<a href="#" onclick="">'.$row->employee_account_no.'</a>',
        $realName.$recomendationName,
		$row->gender,
		$row->employee_position,
		$row->intrax_pin,
        date("Y-m-d",strtotime($row->employee_join_date)),
        $lbCheck.$buttonCorrection,
        $option
      );
	}
    $this->gtrans->saveNewWords();
    echo json_encode($output);
  }

  function edit_employee($encId){
    load_model(["prospective_employees_model"]);
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
      $employee_id= $this->encryption_org->decode($encId);

      $dataUpdate = [
        "employee_full_name" => $fullname,
        "employee_nick_name" => $nickname,
        "gender" =>$gender,
        "phone_number" => $phoneNumber,
        "email" => $email,
        "address" => $address,
        "intrax_pin" => $intraxPin,
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
      $res   = $this->prospective_employees_model->update($dataUpdate,$employee_id,$appid);
      
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
    $detailEmployee = $this->prospective_employees_model->getById($employee_id);
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
    // $dataEmployee = $this->prospective_employees_model->getById($id);
    $dataEmployee = $this->prospective_employees_model->getById_temp($id);
    if($dataEmployee){

      $level  = ($dataEmployee->employee_level==14) ? "admin" : "user";

      $output = [
        "accountno" => $dataEmployee->employee_account_no,
        "fullname"  => $dataEmployee->employee_full_name,
        "nickname"  => $dataEmployee->employee_nick_name,
        "password"  => $dataEmployee->employee_password,
        "joindate"  => date('d-m-Y',strtotime($dataEmployee->employee_join_date)),
        "level"     => $level,
        "gender"    => $dataEmployee->gender,
        "birthday"  => date('d-m-Y',strtotime($dataEmployee->birthday)),
        "phone_number" => $dataEmployee->phone_number,
        "email"     => $dataEmployee->email,
        "address"   => $dataEmployee->address,
        "intrax_pin"=> $dataEmployee->intrax_pin,
        "position"=> $dataEmployee->employee_position
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
		$this->prospective_employees_model->update($dataSource,$employeeID);
    }else{
		$output = [
            "response" => "error",
            "code" => "500",
            "msg" => "failed upload"
          ];
		echo json_encode($output);
    }
  }

  function saveEmployee($dataUrl){
    $this->load->library("encryption_org");
    $this->load->model("employeeareacabang_model");
    $this->load->model("employeebatchcabang_model");
    $encId = $this->input->post("id");
    $accountno = $this->input->post("accountno");
    $fullname  = $this->input->post("fullname");
    $position  = $this->input->post("position");
    $appid     = $this->session->userdata("ses_appid");
    $level     = "";
    $password  = NULL;

    $gender       = $this->input->post("gender");
    $birthday     = "";
    $phoneNumber  = $this->input->post("phone-number");
    $email        = $this->input->post("email");
    $address      = NULL;
    $intraxPin    = $this->input->post("intrax-pin");
    $userID     = $this->session->userdata("ses_userid");
    
    if($this->prospective_employees_model->checkAccountNoExist($accountno,$this->encryption_org->decode($encId))==true){
      $msg = "Duplicate Account No, Please Use another Account No (NIP)!";
      $output = [
            "response" => "error",
            "code" => "500",
            "msg" => $msg
          ];
      echo json_encode($output);
    }else{
		$dataSource= [
			"appid" => $appid,
			"employee_account_no" => $accountno,
			"employee_full_name" => $fullname,
			"employee_position" => $position,
			"gender" =>$gender,
			"phone_number" => $phoneNumber,
			"email" => $email,
			"employee_user_add" => $userID,
			"employee_join_date" => $this->timestamp,
			"employee_date_create" => $this->timestamp,
			"employee_license" => "active",
			"employee_is_active" => "0",
			"is_del" => "0",
			"intrax_pin" => $intraxPin,
			"status_added" => "notactive"
		];
      
      //if($password!=""){
        $dataSource["employee_password "] = $password;
      //}

      $dataSource["employee_level"] = "0";

      if($encId==""){
        // add
        $this->prospective_employees_model->insert($dataSource);
        $employeeID = $this->db->insert_id();
        $dataInsertLocation = [
          "appid" => $appid,
          "employeeareacabang_employee_id" => $employeeID,
          "employee_area_id" => 0,
          "employee_cabang_id" => $this->encryption_org->decode($dataUrl),
          "employeeareacabang_effdt" => $this->timestamp,
          "employeeareacabang_date_create" => $this->timestamp,
          "employeeareacabang_user_add" => $userID,
          "status" => "pending"
        ];
        $this->employeeareacabang_model->saveIgnoreDuplicate($dataInsertLocation);
        $dataInsertBatch = [
          "appid" => $appid,
          "employee_id" => $employeeID,
          "cabang_id" => $this->encryption_org->decode($dataUrl),
          "batch_name" => 'NEW',
          "employeebatchcabang_date_create" => $this->timestamp,
          "employeebatchcabang_user_add" => $userID
        ];
        $this->employeebatchcabang_model->insert($dataInsertBatch);
        $mode = "add";
        setActivity("master employee","add");
        $this->employeehistory_model->insert($employeeID,$this->now,"add");
      }else{
        // edit
        $employeeID = $this->encryption_org->decode($encId);
        $this->prospective_employees_model->update($dataSource,$employeeID);
        
        $mode = "update";
        setActivity("master employee","edit");
        $this->employeehistory_model->insert($employeeID,$this->now,"edit");
      }

      $no = 0;
      $finishProcess ="nocabang";

      if($finishProcess){
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
        $this->gtrans->saveNewWords();
        echo json_encode($output);
      }
    }
  }

  function saveEmployeeTemp($dataUrl) {
    $this->load->library("encryption_org");
    $this->load->model("employeeareacabang_model");
    $this->load->model("employeebatchcabang_model");
    $encId = $this->input->post("id");
    $accountno = $this->input->post("accountno");
    $fullname  = $this->input->post("fullname");
    $position  = $this->input->post("position");
    $appid     = $this->session->userdata("ses_appid");
    $level     = "";
    $password  = NULL;

    $gender       = $this->input->post("gender");
    $birthday     = "";
    $phoneNumber  = $this->input->post("phone-number");
    $email        = $this->input->post("email");
    $address      = NULL;
    $intraxPin    = $this->input->post("intrax-pin");
    $userID     = $this->session->userdata("ses_userid");

    $dataSource= [
			"appid" => $appid,
			"employee_account_no" => $accountno,
			"employee_full_name" => $fullname,
			"employee_position" => $position,
			"gender" =>$gender,
			"phone_number" => $phoneNumber,
			"email" => $email,
			"employee_user_add" => $userID,
			"employee_join_date" => $this->timestamp,
			"employee_date_create" => $this->timestamp,
			"employee_license" => "active",
			"employee_is_active" => "0",
			"is_del" => "0",
			"intrax_pin" => $intraxPin,
			"status_added" => "notactive"
		];

    $dataSource["employee_password "] = $password;

    $dataSource["employee_level"] = "0";
    // print_r($encId); return;
    if($encId==""){
      $this->prospective_employees_model->insert_temp($dataSource);
  
      $employeeID = $this->db->insert_id();
        $dataInsertLocation = [
          "appid" => $appid,
          "employeeareacabang_employee_id" => $employeeID,
          "employee_area_id" => 0,
          "employee_cabang_id" => $this->encryption_org->decode($dataUrl),
          "employeeareacabang_effdt" => $this->timestamp,
          "employeeareacabang_date_create" => $this->timestamp,
          "employeeareacabang_user_add" => $userID,
          "status" => "pending"
        ];
        // print_r($dataInsertLocation); return;
      $this->employeeareacabang_model->saveIgnoreDuplicate_temp($dataInsertLocation);
    } else {
      // edit
      $employeeID = $this->encryption_org->decode($encId);
      $this->prospective_employees_model->update_temp($dataSource,$employeeID);
      
      $mode = "update";
      // setActivity("master employee","edit");
      // $add_history = $this->employeehistory_model->insert_temp($employeeID,$this->now,"edit");
    }

    $output = [
			"response" => "ok",
			"code" => "200",
			"msg" => ["type"=>"success","header"=>"success","msg"=> $this->gtrans->line("Employee has been added successfully")."!"]
		];

    $this->gtrans->saveNewWords();
    echo json_encode($output);
  }
  
  function acceptEmployee($dataUrl){
    $this->load->library("encryption_org");
    $this->load->model("employeeareacabang_model");
    $this->load->model("employeebatchcabang_model");
    $employeeID = $this->input->post("id");
    $fullname  = $this->input->post("fullname");
    $userID     = $this->session->userdata("ses_userid");
    
    $dataSource= [
		"employee_full_name" => $fullname,
		"employee_user_modif" => $userID,
		"employee_date_modif" => $this->timestamp,
	];
  
	$this->prospective_employees_model->update($dataSource,$employeeID);

	$no = 0;
	$finishProcess ="nocabang";

	if($finishProcess){
		$msg = ["type"=>"success","header"=>"success","msg"=> $this->gtrans->line("Employee has been updated successfully")."!"];
		$output = [
			"response" => "ok",
			"code" => "200",
			"msg" => $msg
		];
		$this->gtrans->saveNewWords();
		echo json_encode($output);
	}
  }
  
  function readEmployeeIntrax($fileName){
    $tempPath = $this->filePathEmp.$fileName;
    $this->reader->setLoadSheetsOnly("KARYAWAN");
    $this->reader->setReadDataOnly(true);
	$appid    = $this->session->userdata("ses_appid");

    $spreadsheet = $this->reader->load($tempPath);
    $worksheet   = $spreadsheet->getActiveSheet();
    $arrSheet    = $worksheet->toArray();
    $output      = [];
    foreach ($arrSheet as $index => $row) {
      if($index>2){
		$employeeCode = $row[0];
		$name         = str_replace("'","`",str_replace('"',"`",$row[1])); // " => ` , ' => `
		$position     = $row[2];
		$gender		  = $row[3];
		$phoneNumber  = $row[4];
		$email   	  = $row[5];
		$pinIntrax    = $row[6];
		$output[] = [
		  "employeeCode" => str_replace(" ","",$employeeCode),
		  "employeeName" => $name,
		  "position"     => $position,
		  "gender"    	 => $gender,
		  "phoneNumber"  => $phoneNumber,
		  "email"     	 => $email,
		  "pinIntrax"    => $pinIntrax,
		]; 
      }
    }
    return $output;
  }
  
  function PrepareDataForCheckOut(){
    $this->system_model->checkSession(100);
    $this->load->library("encryption_org");
    load_model([
      "prospective_employees_model",
      "employee_model"
    ]);
    $buyingCount = $this->input->post("buyingCount");
    $arrEmployeeid = $this->input->post("employeeid");
    $pluginsId   = $this->encryption_org->decode($this->input->post('pluginsid'));
	//add order cart
	$data_insert  = [
      "cabang_id_temp"      		=> $pluginsId,
      "price_currency"      => 'IDR',
      "price"         		=> 150000,
      "license_count" 		=> $buyingCount,
      "renewalperiod_uom"   => 'year',
      "renewalperiod_int"   => 1,
      "payment_method" 		=> 'system',
      "payment_vendor" 		=> 'qris',
      "payment_status"   	=> '',
      "totaltax"  			=> 0,
      "total_discount"     	=> 0,
      "merchantfee"      	=> 0,
      "totalunique"         => 0,
      "status_checkout"     => "yes",
      "cart_status"        	=> "open",
      "ipaddr"     			=> $this->input->ip_address(),
      "gtotal"         		=> $buyingCount*150000,
      "discountall"    		=> 0,
      "substaxall"    		=> 0,
      "payment_reason"    	=> 'Pembelian License InAct HRIS Sebanyak '.$buyingCount.' License',
      "typeorder"         	=> "new",
      "nota_iboss"       	=> "OKOK"
    ];
    
	$insertResult = $this->prospective_employees_model->insertOrder($data_insert);
	if($insertResult>0){
		//cancel all order if status is pending
		$this->prospective_employees_model->updateOrder($insertResult);
		foreach ($arrEmployeeid as $rowID) {
			//update order id
			$data_update  = [
			  "parent_order_id"  => $insertResult
			];
			$employeeid = $this->encryption_org->decode($rowID);
			// $this->employee_model->update($data_update,$employeeid);
			$this->employee_model->update_temp($data_update,$employeeid);
		}
	}
    $strBuy      = $pluginsId."|".$buyingCount."|".$insertResult;
    echo $this->encryption_org->encode($strBuy);
  }
  
  function importEmployeeIntrax($dataUrl){
    load_model([
      "area_model",
      "cabang_model",
      "employee_model",
      "employeeareacabang_model",
      "firewall_model"
    ]);

    $appid      = $this->session->userdata("ses_appid");
    $upload     = $this->do_uploadEmp();
    $fileName   = $upload["filename"];
    $userID     = $this->session->userdata("ses_userid");
    $importType = $this->input->post("type-import");
    $error      = [];
    $inserted   = 0;
    $skipped    = 0;
    $updated    = 0;
	$pinIntrax	= 0;
	$identify	= 1;
	$dataPin	= "";
    if($fileName!=""){
      $tempPath = $this->filePathEmp.$fileName;
      $sheetOnFile = $this->reader->listWorksheetNames($tempPath);
      if(in_array("KARYAWAN",$sheetOnFile)){
        $arrEmployee = $this->readEmployeeIntrax($fileName);
        if(count($arrEmployee)>0){
          // $listEmployeeCode = $this->employee_model->getEmployeeCode($appid);
          $listEmployeeCode = $this->employee_model->getEmployeeCode_temp($appid);
          $dataSubscription = $this->employee_model->getSubscription($appid);
          foreach ($arrEmployee as $index => $row) {
            // filter 1
            $line = $index + 4;
			// pengecekan subscription
			if($dataSubscription[0]!=0){
				$dataPin	= $row["pinIntrax"]=="";
				$pinIntrax	= (int) filter_var($row["pinIntrax"], FILTER_SANITIZE_NUMBER_INT);
				$identify	= 6;
			} else {
				if($row["pinIntrax"]!=0 OR $row["pinIntrax"]!=""){
					$dataPin	= $row["pinIntrax"]=="";
					$pinIntrax	= (int) filter_var($row["pinIntrax"], FILTER_SANITIZE_NUMBER_INT);
					$identify	= 6;
				} else {
					$pinIntrax	= 0;
				}
			}
            if($row["employeeCode"]=="" || $row["employeeName"]=="" || $row["position"]=="" || $row["gender"]=="" || $row["phoneNumber"]=="" || $row["email"]=="" || $dataPin){
              $error[] = 'There is empty required field on line '.$line;
            }else{
			  /*
			  if(!in_array($row["employeeCode"],$listEmployeeCode)){

			  }else{

			  }*/
			  // ok
			  // $getEmail = $this->employee_model->getEmailAvailable($appid,$row["email"]);
			  $getEmail = $this->employee_model->getEmailAvailable_temp($appid,$row["email"]);
			  // pengecekan format email
			  if(filter_var($row["email"], FILTER_VALIDATE_EMAIL)){
				  // cek available email
				  if($getEmail==0) {
					  // pengecekan karakter spesial
					  $spCharCode     = isSpecialCharExists($row["employeeCode"],["-","."]);
					  $spCharName     = isSpecialCharExists($row["employeeName"],["-","`",".","(",")"]);
					  if($spCharCode==false && $spCharName==false){
						// insert data employee
						$joinDate = date("Y-m-d");
						$dataInsert = [
						  "appid"               => $appid,
						  "employee_account_no" => $row["employeeCode"],
						  "employee_full_name"  => $row["employeeName"],
						  "email"  				=> $row["email"],
						  "gender"  			=> $row["gender"],
						  "phone_number"  		=> $row["phoneNumber"],
						  "employee_user_add"   => $userID,
						  "employee_join_date"	=> $this->timestamp,
						  "employee_date_create"=> $this->timestamp,
						  "employee_license"    => "active",
						  "employee_is_active"  => "0",
						  "is_del"              => "0",
						  "intrax_pin"          => $pinIntrax,
						  "employee_position"   => $row["position"],
						  "status_added"  		=> "notactive"
						];
						
						// pengecekan subscription
						  if($dataSubscription[0]!=0){
							  //cek pin intrax 123456
							  if($pinIntrax!=123456){
								 if(strlen($pinIntrax)==6 AND $pinIntrax>0){
									// add employee
									// $insertResult = $this->employee_model->saveIgnoreDuplicate($dataInsert,$listEmployeeCode);
									$insertResult = $this->employee_model->saveIgnoreDuplicate_temp($dataInsert,$listEmployeeCode);
								} else {
									$error[] = 'There is error value pin intrax ('.$row["pinIntrax"].') on line '.$line;
								}
							  } else {
								  $error[] = 'There is error value pin intrax ('.$row["pinIntrax"].') on line '.$line;
							  }
							} else {
							  //cek pin intrax 123456
							  if($pinIntrax!=123456){
								  if($pinIntrax==0){
									 // add employee
									// $insertResult = $this->employee_model->saveIgnoreDuplicate($dataInsert,$listEmployeeCode);
									$insertResult = $this->employee_model->saveIgnoreDuplicate_temp($dataInsert,$listEmployeeCode);
								  } else {
									 if(strlen($pinIntrax)==6 AND $pinIntrax>0){
										// add employee
										// $insertResult = $this->employee_model->saveIgnoreDuplicate($dataInsert,$listEmployeeCode);
										$insertResult = $this->employee_model->saveIgnoreDuplicate_temp($dataInsert,$listEmployeeCode);
									} else {
										$error[] = 'There is error value pin intrax ('.$row["pinIntrax"].') on line '.$line;
									}
								  }
							  } else {
								  $error[] = 'There is error value pin intrax ('.$row["pinIntrax"].') on line '.$line;
							  }
								
							}
						

						if($insertResult["insertStatus"]=="inserted"){
						  $employeeID = $insertResult["employee_id"];
						  // add location
						  $dataInsertLocation = [
							"appid" => $appid,
							"employeeareacabang_employee_id" => $employeeID,
							"employee_area_id" => 0,
							"employee_cabang_id" => $this->encryption_org->decode($dataUrl),
							"employeeareacabang_effdt" => $this->timestamp,
							"employeeareacabang_date_create" => $this->timestamp,
							"employeeareacabang_user_add" => $userID,
							"status" => "pending"
						  ];
						  // $this->employeeareacabang_model->saveIgnoreDuplicate($dataInsertLocation);
						  $this->employeeareacabang_model->saveIgnoreDuplicate_temp($dataInsertLocation);
						  $inserted++;
						}elseif ($insertResult["insertStatus"]=="skipped") {
						  $skipped++;
						}elseif ($insertResult["insertStatus"]=="duplicated_code") {
						  $error[] = 'There is duplicate "NO AKUN/ BARCODE" on line '.$line;
						}elseif ($insertResult["insertStatus"]=="updated") {
						  $employeeID = $insertResult["employee_id"];
						  $updated++;
						}
						// open gate
						if($insertResult["insertStatus"]=="inserted"||$insertResult["insertStatus"]=="updated"){
						  $this->firewall_model->openGate($employeeID,$joinDate);
						}

						// 
					  }else{
						if($spCharCode!=false){
						  $error[] = 'Special Character "'.$spCharCode.'" found on field "BARCODE" line '.$line;
						}

						if($spCharName!=false){
						  $error[] = 'Special Character "'.$spCharName.'" found on field "NAMA KARYAWAN" line '.$line;
						}
					  }
				  } else {
					$error[] = 'There is error email unavailable on line '.$line;
				  }
			  }else{
				 $error[] = 'There is error format email on line '.$line;
			  }
            }
          }
        }
        $file_error = "";
      }else {
        $file_error = "Sheet KARYAWAN was not found, please correct the file";
      }
      // delete temporary file
      $tempFile = $this->filePathEmp.$fileName;
      if(file_exists($tempFile)){
        unlink($tempFile);
      }
    }else{
      $file_error = $upload["error"];
    }
    $output = [
      "skipped"    => $skipped,
      "inserted"   => $inserted,
      "updated"    => $updated,
      "error"      => $error,
      "file_error" => $file_error
    ];
    echo json_encode($output);
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
  function deleteEmployee($encId, $uniq){
    $this->load->model("employeeareacabang_model");
    $this->load->library("encryption_org");
    $id  = $this->encryption_org->decode($encId);
    // $res = $this->prospective_employees_model->delete($id);
    $res = $this->prospective_employees_model->delete_temp($id);

    if($res){
      $this->employeeareacabang_model->setArchive($id);
      setActivity("master employee","delete");
      $this->session->set_userdata("ses_msg",["type"=>"success","header"=>"success","msg"=> $this->gtrans->line("Employee has been deleted successfully")."!"]);
      redirect("master-prospective-employees/".$uniq);
    }
  }

  function checkAccountNoExist(){
    load_model(['prospective_employees_model']);
    $noAccount = $this->input->post("no_account");
    if($this->prospective_employees_model->checkAccountNoExist($noAccount)==true){
      echo "yes";
    }else{
      echo "no";
    }
  }
  
  function checkEmailNoExist(){
    load_model(['prospective_employees_model']);
    $email = $this->input->post("email_addr");
    if($this->prospective_employees_model->checkEmailNoExist($email)==true){
      echo "yes";
    }else{
      echo "no";
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
  
  function do_uploadEmp(){
    $config['upload_path']="./sys_upload/temp";
    $config['allowed_types']='xls';
    $config['encrypt_name'] = TRUE;
     $config['max_size']    = 2048; // 2mb
    $this->load->library('upload',$config);
    if($this->upload->do_upload("file")){
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
