<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Device extends CI_Controller
{
  var $now;
  var $listMenu = "";
  var $machineLicense = 0;
  var $tabel_template  = array(
        'table_open'            => '<table class="table table-bordered table-stripped" id="datatable">',
        'table_close'           => '</table>'
	);

  function __construct()
  {
    parent::__construct();
    $this->load->model("system_model");
    $this->now = date("Y-m-d H:i:s");
    $this->load->library("device_caching");
    $languange = !empty($this->session->userdata('lang')) ? $this->session->userdata('lang') :"en";
    $this->load->library("gtrans/gtrans",["lang" => $languange]);

    $this->load->model("menu_model");
    
    $this->load->model("device_model");
    $this->listMenu = $this->menu_model->list_menu();
    $this->system_model->checkSession(6);

    $addons = $this->session->userdata("activeaddons");
    if(!empty($addons['machinelicense'])){
      $this->machinelicense = $addons['machinelicense'];
    }
  }

  function index(){
    $this->load->model("area_model");
    $this->load->model("cabang_model");
    $this->load->helper("form");
    $this->load->library("encryption_org");
    $this->db->where("device_license","active");
    $no = $this->device_model->countAll();
    if(!empty($this->machinelicense) && $this->machinelicense>0){

      if($no<$this->machinelicense){
        $data['licenseInfo'] = '<div class="alert alert-info alert-dismissible" style="text-align:center">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                  <p>'.$this->gtrans->line('You have').' '.($this->machinelicense - $no).' '.$this->gtrans->line('device slot available of').' '.$this->machinelicense.' Slot.</p>
                                </div>';
        $data['leftLicense'] = $this->machinelicense - $no;
      }else{
        $data['licenseInfo'] = '<div class="alert alert-danger alert-dismissible" style="text-align:center">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                  <p>'.$this->gtrans->line("You have no device slot over").'!</p>
                                </div>';
        $data['leftLicense'] = 0;
      }
    }else{
      $data['licenseInfo'] = '<div class="alert alert-danger alert-dismissible" style="text-align:center">
                              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <p>'.$this->gtrans->line("You have no device slot or it was expired").'!</p>
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
    if(!empty($this->session->userdata("ses_msg"))){
      $msg = $this->session->userdata("ses_msg");
      $data["notif"] =createNotif($msg['type'],$msg['header'],$msg['msg']);
      $this->session->set_userdata("ses_msg");
    }
    $parentViewData = [
      "title"   => "Master Device",  // title page
      "content" => "master/device",  // content view
      "viewData"=> $data,
      "listMenu"=> $this->listMenu,
      "externalCSS" => [
        base_url("asset/plugins/daterangepicker3.0.5/daterangepicker.css"),
        base_url("asset/template/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css")
      ],
      "externalJS" => [
        base_url("asset/template/bower_components/moment/min/moment.min.js"),
        base_url("asset/plugins/daterangepicker3.0.5/daterangepicker.js"),
        base_url("asset/template/bower_components/datatables.net/js/jquery.dataTables.min.js"),
        base_url("asset/template/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"),
        "https://cdn.jsdelivr.net/npm/sweetalert2@8",
        base_url("asset/js/checkCode.js")
      ],
      "varJS" => ["url" => base_url()]
    ];

    $this->load->view("layouts/main",$parentViewData);
    $this->gtrans->saveNewWords();
  }

  function loadTableDevice(){
    $sArea  = $this->input->post("sArea");
    $sCabang= $this->input->post("sCabang");
    $this->load->library("encryption_org");
    $this->table->set_template($this->tabel_template);
    $this->table->set_heading(
      ["data" => "No","class" => "text-center"],
      ["data" => $this->gtrans->line("Code"),"class" => "text-center"],
      ["data" => $this->gtrans->line("Name"),"class" => "text-center"],
      ["data" => "SN","class" => "text-center"],
      ["data" => $this->gtrans->line("License"),"class" => "text-center"],
      ["data" => $this->gtrans->line("Branch"),"class" => "text-center"],
      ["data" => $this->gtrans->line("Status"),"class" => "text-center"],
      ["data" => $this->gtrans->line("Algoritm Version"),"class" => "text-center"],
      ["data" => $this->gtrans->line("Option"),"class" => "text-center"]
    );

    if(!empty($sArea)){
      $this->db->where("device_area_id",$sArea);
    }
    if(!empty($sCabang)){
      $this->db->where("device_cabang_id",$sCabang);
    }
    $sql = $this->device_model->getAll();
    $no = 0;
    foreach ($sql as $row) {
      $rangeActive = dateDifferenceTime($this->now,$row->device_last_communication);
      if($row->device_last_communication!=null && $rangeActive<120){
        $deviceStatus = '<div class="text-green" style="cursor:pointer" data-toggle="tooltip" data-placement="top" title="Last connection '.(!empty($row->device_last_communication)? $row->device_last_communication : '-').'"><i class="fa fa-check-circle-o"></i> '.$this->gtrans->line("Connect").'</div>';
      }else{
        $deviceStatus = '<div class="text-red" style="cursor:pointer" data-toggle="tooltip" data-placement="top" title="Last connection '.(!empty($row->device_last_communication)? $row->device_last_communication : '-').'"><i class="fa fa-times-circle-o"></i> '.$this->gtrans->line("Disconnect").'</div>';
      }

      $no++;
      $encId = $this->encryption_org->encode($row->device_id);
      $encodeSN = $this->encryption_org->encode($row->device_SN);
      $encArea  = base64_encode($row->device_area_id);
      $encCabang= base64_encode($row->device_cabang_id);
      $encSN    = base64_encode($row->device_SN);
      $encCode  = base64_encode($row->device_code);
      $encName  = base64_encode($row->device_name);
      $encIP    = base64_encode($row->device_ip);
      $encResponse    = base64_encode($row->response_code);

      if($row->device_license=="active"){
        $lbLicense = '<div class="text-green"><i class="fa fa-check"></i> ACTIVE</div>';
      }elseif ($row->device_license=="notactive") {
        $lbLicense = '<div class="text-red"><i class="fa fa-close"></i> NOT ACTIVE</div>';
      }

      $btnEdit = '<i style="cursor:pointer" class="fa fa-edit fa-lg color-primary"
      onclick="edit(
        \''.$encId.'\',
        \''.$encArea.'\',
        \''.$encCabang.'\',
        \''.$encSN.'\',
        \''.$encCode.'\',
        \''.$encName.'\',
        \''.$encIP.'\',
        \''.$encResponse.'\'
        )"></i>';
      $delete  = '<span class="text-red" style="cursor:pointer" onclick="delDevice(\''.$encId.'\')"><i class="fa fa-trash fa-lg "></i></span>';
      $suspended = !empty(explode("|",$row->device_SN)[1]) ? explode("|",$row->device_SN)[1] : "";
      $btnGetLog = '<span class="text-blue" style="cursor:pointer" onclick="showGetLogPanel(\''.$encodeSN.'\',\''.$encId.'\')"><i class="fa fa-cloud-upload fa-lg "></i></span>';
      if($suspended==""){
        $option  = $btnGetLog.' '.$btnEdit.' '.$delete;
      }else{
        $option  = '<div class="text-red">SUSPEND <br> By InterActive</div>';
      }
      if($suspended=="suspend"){
        $addClass = "text-red";
      }else{
        $addClass = "";
      }
      $this->table->add_row(
        [
          "class"=> $addClass,
          "data" => [
            $no,
            $row->device_code,
            $row->device_name.' <span class="label label-success"> Version '.$row->response_code.'.0</span>',
            explode("|",$row->device_SN)[0],
            $lbLicense,
            $row->cabang_name,
            $deviceStatus,
            ["data" => $row->alg_version,"class" => "text-center" ],
            ["data" => $option,"class" => "text-center" ]
          ]
        ]
      );
    }
    $output = $this->table->generate();
    $this->gtrans->saveNewWords();
    echo json_encode($output);
  }

  function saveDevice(){
    $this->load->model("cabang_model");
    $encId = $this->input->post("id");

    $area = $this->input->post("area");
    $branchname = $this->input->post("branchname");
    $serialnumber = $this->input->post("serialnumber");
    $devicecode = $this->input->post("devicecode");
    $devicename = $this->input->post("devicename");
    $internetprotocol = $this->input->post("internetprotocol");
    $responseVersion  = $this->input->post("response-version");

    $dataSource = [
      "device_area_id" => $area,
      "device_cabang_id" => $branchname,
      "device_SN" => $serialnumber,
      "device_code" => $devicecode,
      "device_name" => $devicename,
      "device_ip" => $internetprotocol,
      "device_license" => "notactive",
      "response_code" => $responseVersion
    ];

    if($encId==""){
      // add
      $res = $this->device_model->insert($dataSource);
      if($res){
        $this->device_caching->cacheSN();
        setActivity("master device","add");
        $this->session->set_userdata("ses_msg",["type"=>"success","header"=>"Success","msg"=> $this->gtrans->line("Device has been added successfully")."!"]);
        redirect("master-device");
      }
    }else{
      unset($dataSource["device_license"]);
      
      // edit
      $this->load->library("encryption_org");
      $id = $this->encryption_org->decode($encId);
      if(!empty($this->input->post("reboot")) && $this->input->post("reboot")=="yes"){
        $dataSource["reboot"] = "yes";
      }
      $res = $this->device_model->update($dataSource,$id);

      if($res){
        $this->device_caching->cacheSN();
        setActivity("master device","edit");
        $this->session->set_userdata("ses_msg",["type"=>"success","header"=>"Success","msg"=> $this->gtrans->line("Device has been updated successfully")."!"]);
        redirect("master-device");
      }
    }
  }

  function deleteDevice($encId){
    $this->load->library("encryption_org");
    $id  = $this->encryption_org->decode($encId);
    $res = $this->device_model->delete($id);
    if($res){
      $this->device_caching->cacheSN();
      setActivity("master device","delete");
      $this->session->set_userdata("ses_msg",["type"=>"success","header"=>"Success","msg"=> $this->gtrans->line("Device has been deleted successfully")."!"]);
      redirect("master-device");
    }
  }

  function switchLicense(){
    $this->load->library("encryption_org");
    $encDevice = $this->input->post("device");
    $status    = $this->input->post("status");
    $deviceID  = $this->encryption_org->decode($encDevice);
    if($status=="active"){
      $licenseUsed = $this->device_model->getLicenseUsed();
      if($licenseUsed<$this->machinelicense){
        $this->device_model->changeLicenseTo("active",$deviceID);
        $this->device_caching->cacheSN();
        echo "success";
      }else{
        echo "failed";
      }
    }elseif ($status=="inactive") {
      $this->device_model->changeLicenseTo("notactive",$deviceID);
      $this->device_caching->cacheSN();
      echo "success";
    }
  }

  function checkCodeExists(){
    load_model(["device_model"]);
    load_library(["encryption_org"]);
    $deviceCode = $this->input->post("code");
    $deviceID   = $this->encryption_org->decode($this->input->post("entityID"));
    $appid      = $this->session->userdata("ses_appid");
    $codeExists = $this->device_model->isCodeExists($deviceCode,$deviceID,$appid);
    if($codeExists==true){
      echo "exists";
    }else{
      echo "notExists";
    }
  }

  function checkSNExists(){
    load_model(["device_model"]);
    load_library(["encryption_org"]);
    $deviceSN = $this->input->post("code");
    $deviceID = $this->encryption_org->decode($this->input->post("entityID"));
    $appid    = $this->session->userdata("ses_appid");
    $SNExists = $this->device_model->isSNExists($deviceSN,$deviceID);
    if($SNExists==true){
      echo "exists";
    }else{
      echo "notExists";
    }
  }

  function isDeviceUsed(){
    load_model(["employeelocationdevice_model"]);
    load_library(["encryption_org"]);

    $deviceID = $this->encryption_org->decode($this->input->post("deviceID"));
    $appid    = $this->session->userdata("ses_appid");
    $isUsed   = $this->employeelocationdevice_model->isUsed($deviceID,$appid);
    if($isUsed==true){
      echo 'yes';
    }else{
      echo 'no';
    }
  }

  function setCmdPullAttendance(){
    $this->load->library("encryption_org");
    $this->load->model("command_model");
    $this->load->model("firewall_model");

    $periode     = $this->input->post("periode");
    $arrPeriode  = explode(" - ", $periode);
    $datestart   = date("Y-m-d",strtotime($arrPeriode[0]));
    $dateend     = date("Y-m-d",strtotime($arrPeriode[1]));

    $encDeviceSN = $this->input->post("encDeviceSN");
    $deviceSN    = $this->encryption_org->decode($encDeviceSN);

    $encDeviceID = $this->input->post("tempDeviceID");
    $deviceID    = $this->encryption_org->decode($encDeviceID);
    
    $command     = "DATA QUERY ATTLOG StartTime=".$datestart." 00:00:00\tEndTime=".$dateend." 23:59:59";
    $data        = [
      "device_SN" => $deviceSN,
      "command"   => $command
    ];

    $result = $this->command_model->insert($data);
    // firewall open
    $date = date("Y-m-d");
    
    if($result==true){
      $id = $this->db->insert_id();
      $this->firewall_model->setSchedule($deviceID,$date);
      echo $id;
    }else{
      echo "error";
    }
  }

  function openFirewall(){
    $this->load->model("firewall_model");
    $appid  = $this->session->userdata("ses_appid");
    $result = $this->firewall_model->openByAppid($appid);
    if($result==true){
      echo "ok";
    }
  }
}
