<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class User extends CI_Controller
{
  var $appid;
  var $listMenu = "";
  var $now;
  var $tabel_template  = array(
        'table_open'            => '<table class="table table-bordered table-stripped" id="datatable">',
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
    $this->load->model("area_model");
    
    $this->load->model("user_model");
    $this->system_model->checkSession(15);
    $this->listMenu = $this->menu_model->list_menu();
    $this->now = date("Y-m-d H:i:s");
    $this->appid = $this->session->userdata("ses_appid");
    // bahasa
  }

  function index(){
    $this->load->library("encryption_org");
    $sql = $this->user_model->getData($this->appid);
    $this->table->set_template($this->tabel_template);
    $this->table->set_heading(
      ["data"=> $this->gtrans->line("Name"), "class"=>"text-center"],
      ["data"=> $this->gtrans->line("Phone Number"), "class"=>"text-center"],
      ["data"=> "Email", "class"=>"text-center"],
      ["data"=> $this->gtrans->line("Option"), "class"=>"text-center"]
    );

    foreach ($sql as $row) {
      $encId = $this->encryption_org->encode($row->userid);
      $option = ($row->user_parent!=0) ? anchor('edit-user/'.$encId,'<i class="fa fa-edit fa-lg"></i>').' <span style="cursor:pointer" onclick="delUser(\''.$encId.'\')" class="text-red"><i  class="fa fa-trash fa-lg "></i></span>' : '<p class="text-red"><b>ROOT USER</b></p>';
      $this->table->add_row(
        $row->user_fullname,
        $row->user_phone,
        $row->user_emailaddr,
        [
          "data"=>$option,
          "style"=>'text-align:center'
        ]
      );
    }
	

    $data['userTable'] = $this->table->generate();
    if(!empty($this->session->userdata("ses_notif"))){
      $arrNotif = $this->session->userdata("ses_notif");

      $notif    = createNotif($arrNotif['type'],$arrNotif['header'],$arrNotif['msg']);
      $data['notif'] = $notif;
      $this->session->unset_userdata("ses_notif");
    }
    $parentViewData = [
      "title"   => "User",  // title page
      "content" => "master/user",  // content view
      "viewData"=> $data,
      "listMenu"=> $this->listMenu,
      "varJS" => ["url" => base_url()],
      "externalCSS" => [
        base_url("asset/template/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css")
      ],
      "externalJS" => [
        base_url("asset/template/bower_components/datatables.net/js/jquery.dataTables.min.js"),
        base_url("asset/template/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"),
        "https://cdn.jsdelivr.net/npm/sweetalert2@8",
        base_url("asset/js/checkCode.js"),
        base_url("asset/js/user.js")

      ]
    ];
    $this->load->view("layouts/main",$parentViewData);
    $this->gtrans->saveNewWords();
  }

  function save_user(){
    load_library(["encryption_org"]);
    load_model(["user_model"]);

    $password = $this->input->post("password");
    $confirmpassword = $this->input->post("confirmpassword");

    if($password==$confirmpassword){
      $userID = $this->encryption_org->decode($this->input->post("userID"));

      $res = $this->saveUser($userID);
      if($this->input->post("userID") == ""){
        $mode = "add";
      }else{
        $mode = "edit";
      }

      $this->load->library("string_manipulation");
      $name   = $this->input->post("name");
      $email  = $this->input->post("email");
      $phone  = $this->input->post("phone");
      $level  = $this->input->post("level");
      $area  = $this->input->post("area");
	  $arrArea	= !empty($area) ? implode("|", $area) : null;
      $parentID = $this->user_model->getRootID($this->appid);
      if($mode=="add"){
        $password = $this->input->post("password");
        $passwordHashed = $this->string_manipulation->hash_password($password);
      }

      $arrMenu = $this->input->post("menu");


      // get parent menu from all
      $arrParentMenu = [];
      if(!in_array("1", $arrMenu)){
        $arrMenu[] = "1";
      }

      foreach ($arrMenu as $menuid) {
        $currentmenu = $menuid;
        while($currentmenu>0){
          $parentMenuID = $this->menu_model->getParentId($currentmenu);
          if($parentMenuID!=false){
            if(!in_array($parentMenuID,$arrParentMenu)){
              $arrParentMenu[] = $parentMenuID;
            }
            $currentmenu  = ($parentMenuID!=false) ? $parentMenuID : 0;
          }else{
            $currentmenu  = 0;
          }
        }
      }
      //

      foreach ($arrParentMenu as $addMenu) {

        if(!in_array($addMenu,$arrMenu)){

          array_push($arrMenu,$addMenu);
        }
      }

      $strMenu = implode("|",$arrMenu);

      if($mode=="add"){
        $dataInsert = [
          "appid"           => $this->appid,
          "user_emailaddr"  => $email,
          "user_fullname"   => $name,
          "user_phone"      => $phone,
          "user_datecreate" => $this->now,
          "user_isactive"   => "1",
          "user_parent"     => $parentID,
          "user_passw"      => $passwordHashed,
          "defaultlang"     => "english",
          "user_access"     => $strMenu,
          "status_user"     => $level,
          "iauser_area_id"  => $arrArea
        ];
        $res = $this->user_model->insert($dataInsert);
        setActivity("master user","add");
      }elseif ($mode=="edit") {
        $dataUpdate = [
          "user_emailaddr"  => $email,
          "user_fullname"   => $name,
          "user_phone"      => $phone,
          "user_access"     => $strMenu,
          "status_user"     => $level,
          "iauser_area_id"  => $arrArea
        ];
        $res = $this->user_model->update($dataUpdate,$userID);
        setActivity("master user","edit");
      }

      if($res) {
        if($userID!=""){
          // edit;
          $this->session->set_userdata('ses_notif',['type'=>'success','header'=>'Success','msg'=> $this->gtrans->line('User Is Updated')]);
        }else{
          // delete;
          $this->session->set_userdata('ses_notif',['type'=>'success','header'=>'Success','msg'=> $this->gtrans->line('User Added')]);
        }
        echo "ok";
      }
      $this->gtrans->saveNewWords();
    }
  }

  public function email_check($userID){
    $email = $this->input->post("email");
    return false;//$this->user_model->isEmailExists($email,$userID);
  }

  public function phone_check($userID){
    $phone = $this->input->post("phone");
    return $this->user_model->isPhoneExists($phone,$userID);
  }

  function add_user($EncUserID=""){
    $this->load->library("encryption_org");
    $this->load->library("form_validation");

    if($EncUserID!=""){
      $userID = $this->encryption_org->decode($EncUserID);
    }else{
      $userID = "";
    }

    if(!empty($this->input->post("submit"))){
      $duplicateEmail = $this->user_model->isEmailExists($this->input->post("email"),$userID);
      $duplicatePhone = $this->user_model->isPhoneExists($this->input->post("phone"),$userID);
      
    }else{
      $duplicateEmail = false;
      $duplicatePhone = false;
    }

    $this->form_validation->set_rules("name","name","required");
    
    $this->form_validation->set_rules("email","email",'required');
    $this->form_validation->set_rules("phone","phone",'required');

    $this->form_validation->set_rules("password","password","required");
    $this->form_validation->set_rules("confirmpassword","confirmpassword","required");

    $this->form_validation->set_rules("submit","submit","required");

    if($this->form_validation->run()==true && $duplicateEmail==false && $duplicatePhone==false){
      /*
      $password = $this->input->post("password");
      $confirmpassword = $this->input->post("confirmpassword");
      if($password==$confirmpassword){
        $res = $this->saveUser($userID);
        if($res) {
          if($userID!=""){
            // edit;
            $this->session->set_userdata('ses_notif',['type'=>'success','header'=>'Success','msg'=> $this->gtrans->line('User Is Updated')]);
          }else{
            // delete;
            $this->session->set_userdata('ses_notif',['type'=>'success','header'=>'Success','msg'=> $this->gtrans->line('User Added')]);
          }
          redirect("master-user");
        }

      }
      */
    }
    // checklist menu
    if($userID!=""){
      $data['data_edit'] = $this->user_model->getById($userID);
      $arr_saved_menu    = explode("|",$data['data_edit']->user_access);
      $selectedMenu = count($arr_saved_menu);
		}else{
      $selectedMenu = 0;
    }

    $arr_menu 		= $this->menu_model->get_menu_bertingkat();
		$str_list_menu 	= "<table class='table table-access' border='0' width='100%' >";
		$str_list_menu_area 	= "<table class='table table-access' border='0' width='100%' >";
		$no = 1;

    if($selectedMenu==count($arr_menu)){
      $checkedAll = "checked";
    }else{
      $checkedAll = "";
    }
    $str_list_menu .= '<tr>
      <td><input '.$checkedAll.' class="flat-red" type="checkbox" name="all" onclick="selectAll()" value="" id="checkAll"></td>
      <td>'.$this->gtrans->line('Select All').'</td>
    </tr>';
    $notShow = ["Users","Dashboard"];

		foreach ($arr_menu as $menu) {
      if(!in_array($menu['nama'],$notShow)){
        if($userID!=""){
  				$checked = (in_array($menu['id'], $arr_saved_menu)) ? 'checked' : '';
  			}else{
  				$checked = '';
  			}
  			if($no%2==1){
  				$style = "#BDC3C7";
  				$tabcolor = "#BDC3C7";
  			}else{
  				$style = "";
  				$tabcolor = "#ffffff";
  			}
  			switch ($menu["tingkat"]) {
  				case '0':
  					$space = '';
  					break;
  				case '1':
  					$space = '<font color="'.$tabcolor.'">__</font> <i class="fa   fa-angle-right"></i> ';
  					break;
  				case '2':
  					$space = '<font color="'.$tabcolor.'">____</font> <i class="fa   fa-angle-right"></i> ';
  					break;
  				case '3':
  					$space = '<font color="'.$tabcolor.'">______</font> <i class="fa   fa-angle-right"></i> ';
  					break;

  				default:
  					$space = '';
  					break;
  			}
        // bgcolor="'.$style.'"
		//if($this->session->userdata("ses_status")=="admin_area"){
			if ($menu['link']=='master-branch' OR $menu['link']=='master-employee' OR $menu['link']=='report-history-intrax'){
				if($menu["link"]=="#"){
				  $chkBox = '';
				}else{
				  $chkBox = '<input id="menu'.$no.'" class="flat-red"  type="checkbox" name="menu[]" value="'.$menu['id'].'" checked onclick="return false;">';
				}
				$str_list_menu_area .= '<tr >
  									<td>'.$chkBox.'</td>
  									<td> '.$space.' '.$menu['nama'].' ('.$menu['link'].')'.'</td>
  								</tr>';
  		  $no++;
			}
			
		//} else {
			if($menu["link"]=="#"){
			  $chkBox = '';
			}else{
			  $chkBox = '<input id="menu'.$no.'" class="flat-red"  type="checkbox" name="menu[]" value="'.$menu['id'].'" '.$checked.'>';
			}
			$str_list_menu .= '<tr >
  									<td>'.$chkBox.'</td>
  									<td> '.$space.' '.$menu['nama'].' ('.$menu['link'].')'.'</td>
  								</tr>';
  		  $no++;
		//}
        
  			
      }
		}

		$str_list_menu .= "</table>";
		$str_list_menu_area .= "</table>";
    // tabel user
	$sqlArea = $this->area_model->getAll();
	$data['dataArea']  = $sqlArea;
    $data['maxMenu']   = $no - 1;
    $data['userTable'] = $this->table->generate();
    $data['str_list_menu'] = $str_list_menu;
    $data['str_list_menu_area'] = $str_list_menu_area;
    $parentViewData = [
      "title"   => $this->gtrans->line("Add User"),  // title page
      "content" => "master/add_user",  // content view
      "viewData"=> $data,
      "listMenu"=> $this->listMenu,
      "externalJS" => [
        "https://cdn.jsdelivr.net/npm/sweetalert2@8",
        base_url("asset/js/user.js")
      ],
      "varJS" => ["url" => base_url()]
    ];
    $this->load->view("layouts/main",$parentViewData);
    $this->gtrans->saveNewWords();
  }

  private function saveUser($userID){


  }

  function deleteUser($encId){
    $this->load->library("encryption_org");
    $userid = $this->encryption_org->decode($encId);
    $res = $this->user_model->setDeleted($userid);
    if($res){
      $this->session->set_userdata('ses_notif',['type'=>'success','header'=>'Success','msg'=> $this->gtrans->line('User Was Deleted')]);
      setActivity("master user","delete");
      $this->gtrans->saveNewWords();
      redirect("master-user");
    }
  }

  function checkEmail(){
    load_model(["user_model"]);
    load_library(["encryption_org"]);

    $email  = $this->input->post("email");
    $userID = $this->encryption_org->decode($this->input->post("userid"));
    $emailExist = $this->user_model->isEmailExists($email,$userID);
    if($emailExist==true){
      echo 'exists';
    }else{
      echo 'notExists';
    }
  }

  function checkPhone(){
    load_model(["user_model"]);
    load_library(["encryption_org"]);

    $phone = $this->input->post("phone");
    $userID = $this->encryption_org->decode($this->input->post("userid"));
    $phoneExist = $this->user_model->isPhoneExists($phone,$userID);
    if($phoneExist==true){
      echo 'exists';
    }else{
      echo 'notExists';
    }
  }

  function work_hours() {
    // print_r('masuk ke sini');
    // return;
    $parentViewData = [
        "title"   => "Jadwal Kerja",  // title page
        "content" => "schedule/work_hours",  // content view
        "viewData"=> [],
        "listMenu"=> $this->listMenu,
        "varJS" => ["url" => base_url()],
        "externalCSS" => [
            base_url("asset/template/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css")
        ],
        "externalJS" => [
            base_url("asset/template/bower_components/datatables.net/js/jquery.dataTables.min.js"),
            base_url("asset/template/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"),
            "https://cdn.jsdelivr.net/npm/sweetalert2@8",
            base_url("asset/js/checkCode.js"),
            base_url("asset/js/user.js")
    
        ]
    ];
    $this->load->view("layouts/main",$parentViewData);
    $this->gtrans->saveNewWords();
}
}
