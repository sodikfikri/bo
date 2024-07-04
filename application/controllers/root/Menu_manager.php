<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Admin board
 */
class Menu_manager extends Root_controller
{
  var $key = "975fe10374a662c373f7653b19d8888df9f01841c7eba7dc0af418aa694b9545";
  var $tabel_template  = array(
        'table_open'            => '<table class="table table-bordered table-stripped" >',
        'table_close'           => '</table>'
		);
  function __construct()
  {
    parent::__construct();
    $this->load->library("session");
    $this->setMenu(3);
	$this->checkPermission();
  }

  function reorder_menu($urut){
		if($urut!='empty'){
			$this->db->set('urut','urut+1',false);
			$this->db->where("urut >=",$urut);
			$this->db->update("tbmenu");
			return $urut;
		}else{
			$this->db->select_max('urut');
			return $this->db->get('tbmenu')->row()->urut + 1;
		}
	}

  function sideMenu($id=""){
	// if ($this->input->post('kd')) {
	// 	print_r('masuk ke if');
	// } else {
	// 	print_r('masuk ke else');
	// }
	// die;
	$currentUrl = base_url("rootaccess/menu-manager/");//current_url()."/";
	$this->load->library("form_validation");
	$this->load->model("menu_model");
	$mainUrl = $this->getMainPage();
    //
    $this->form_validation->set_rules("submit","submit","required");
    if($this->form_validation->run()==true){
		
      $caption 	= $this->input->post('caption');
  		$parent 	= !empty($this->input->post('parent')) ? $this->input->post('parent') : 0;
  		$class_icon = $this->input->post('class_icon');
  		$link 		= $this->input->post('link');
  		$id         = $this->input->post('kd') ? $this->input->post('kd') : 0;
  		$newtab     = !empty($this->input->post('newtab')) ? $this->input->post('newtab') : '0';
  		$urut       = ($this->input->post('urut')=='') ? 'empty' : $this->input->post('urut');
  		$urut_baru = $this->reorder_menu($urut);
  		$arr_input  = [
  							'menucaption' 	=> $caption,
  							'parentid'     => $parent,
  							'link'          => $link,
  							'class_icon'    => $class_icon,
  							'urut'          => $urut_baru,
  							'newTab'        => $newtab,
                'tags'          => "semuamodul"
  						];

  		$this->load->model('menu_model');
		// print_r('<pre>');
		// print_r($id);
		// print_r('</pre>');
		// die;
  		if($id==0){
  			$this->menu_model->insert_menu($arr_input);
  		}else{
  			$this->menu_model->update_menu($arr_input,$id);

        redirect("rootaccess/menu-manager/");
  		}
    }
      $parent_selected 	= "";
		  if($id!=""){
			  $data['data_edit'] 	= $this->menu_model->get_menu($id);
			  $parent_selected 	= $data['data_edit']->parentid;
		  }

		  $data['combo_parent'] 	= $this->combo_menu_bertingkat($parent_selected,$id);

		  $sql_menu 				= $this->menu_model->get_menu();
		  $this->table->set_template($this->tabel_template);
		  $this->table->set_heading("Lokasi Menu","Caption","Link","Class Icon",["data"=>"Opsi","style"=>"width:50px"]);
		  $temp = [];
		  $delete_confirm = "return confirm('yakin ingin menghapus data ini?')";

      foreach ($sql_menu as $row) {
			  if(!in_array($row->menuid, $temp)){
  			  $opsi = '<div class="btn-group-vertical"><a href="'.$currentUrl.$row->menuid.'" class="btn btn-primary btn-xs" data-toggle="tooltip" data-original-title="Edit"><i class="fa  fa-edit"></i></a>
                   <a href="'.$currentUrl.'delete-menu/'.$row->menuid.'" onclick="'.$delete_confirm.'" class="btn btn-danger btn-xs" data-toggle="tooltip" data-original-title="Hapus"><i class="fa  fa-trash"></i></a></div>
                  ';
          $txtLink = $row->link;
          $child   = '<a href="#" data-toggle="tooltip" data-original-title="New Menu child" onclick="set_parent('.$row->menuid.')" > +</a>';
          $this->table->add_row($row->urut,$row->menucaption.$child,$txtLink,$row->class_icon,'<div align="right">'.$opsi.'</div>');
				  $sql_child1 = $this->menu_model->get_child_menu($row->menuid);
				  if($sql_child1!=FALSE){
				 	 foreach ($sql_child1 as $row1) {
						if(!in_array($row1->menuid, $temp)){
							$opsi = '<div class="btn-group-vertical"><a href="'.$currentUrl.$row1->menuid.'" class="btn btn-primary btn-xs" data-toggle="tooltip" data-original-title="Edit"><i class="fa  fa-edit"></i></a>
                       <a href="'.$currentUrl.'delete-menu/'.$row1->menuid.'" onclick="'.$delete_confirm.'" class="btn btn-danger btn-xs" data-toggle="tooltip" data-original-title="Hapus"><i class="fa  fa-trash"></i></a></div>
                  			';
              $child   = '<a href="#" data-toggle="tooltip" data-original-title="New Menu child" onclick="set_parent('.$row1->menuid.')" > +</a>';
							$this->table->add_row($row1->urut,'<font color="#ffffff">__</font> <i class="fa   fa-angle-right"></i> '.$row1->menucaption.$child ,$row1->link,$row1->class_icon,'<div align="right">'.$opsi.'</div>');
							array_push($temp, $row1->menuid);
							$sql_child2 = $this->menu_model->get_child_menu($row1->menuid);
							if($sql_child2!=FALSE){
								foreach ($sql_child2 as $row2) {
									if(!in_array($row2->menuid, $temp)){
										$opsi = '<div class="btn-group-vertical"><a href="'.$currentUrl.$row2->menuid.'" class="btn btn-primary btn-xs" data-toggle="tooltip" data-original-title="Edit"><i class="fa  fa-edit"></i></a>
                       			 <a href="'.$currentUrl.'delete-menu/'.$row2->menuid.'" onclick="'.$delete_confirm.'" class="btn btn-danger btn-xs" data-toggle="tooltip" data-original-title="Hapus"><i class="fa  fa-trash"></i></a></div>
                  					';
                  					$child   = '<a href="#" data-toggle="tooltip" data-original-title="New Menu child" onclick="set_parent('.$row2->menuid.')" > +</a>';

										$this->table->add_row($row2->urut,'<font color="#ffffff">____</font> <i class="fa   fa-angle-right"></i> '.$row2->menucaption.$child, $row2->link,$row2->class_icon,'<div align="right">'.$opsi.'</div>');
										array_push($temp, $row2->menuid);
										$sql_child3 = $this->menu_model->get_child_menu($row2->menuid);
										if($sql_child3!=FALSE){
											foreach ($sql_child3 as $row3) {
												if(!in_array($row3->menuid, $temp)){
													$opsi = '<div class="btn-group-vertical"><a href="'.$currentUrl.$row3->menuid.'" class="btn btn-primary btn-xs" data-toggle="tooltip" data-original-title="Edit"><i class="fa  fa-edit"></i></a>
                       			 <a href="'.$currentUrl.'delete-menu/'.$row3->menuid.'" onclick="'.$delete_confirm.'" class="btn btn-danger btn-xs" data-toggle="tooltip" data-original-title="Hapus"><i class="fa  fa-trash"></i></a></div>
                  					';
                  					$child   = '<a href="#" data-toggle="tooltip" data-original-title="New Menu child" onclick="set_parent('.$row3->menuid.')" > +</a>';
													$this->table->add_row($row3->urut,'<font color="#ffffff">______</font> <i class="fa   fa-angle-right"></i> '.$row3->menucaption.$child, $row3->link,$row3->class_icon,'<div align="right">'.$opsi.'</div>');
													array_push($temp, $row3->menuid);
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
  		if(!empty($_GET['err'])){
  			if($_GET['err']=="child_exist"){
  				$data['err'] = 'alert("data tidak bisa dihapus, ada child menu!");';
  			}elseif($_GET['err']=="access_exist"){
  				$data['err'] = 'alert("data tidak bisa dihapus, akses menu sudah digunakan!");';
  			}
  		}

  		$data['tabel_data'] = $this->table->generate();
      $data['mainUrl']    	= $mainUrl;
  	  $parentViewData    	= $this->getMain();

  	  $parentViewData["title"]   = "Dashboard";
  	  $parentViewData["content"] = "admin_board/setting_menu";
  	  $parentViewData["viewData"]= $data;
  	  $parentViewData["listMenu"]= "";
  	  $parentViewData["menu"]    = 3;
  	  
  	  $this->load->view("layouts/main_root",$parentViewData);
    
  }

  function combo_menu_bertingkat($selected,$id_menu=""){
    $this->load->model("menu_model");
		$sql_menu 				= $this->menu_model->get_menu();
		$temp 						= [];

		$str  						= '<select name="parent" id="parent" class="form-control" onchange="count_parent(this.value)"><option />';
		foreach ($sql_menu as $row) {
			if(!in_array($row->menuid, $temp)){
				$sel1 = ($selected==$row->menuid) ? 'selected': '';
				if($id_menu!=$row->menuid){
					$str .= '<option value="'.$row->menuid.'" '.$sel1.' />'.$row->menucaption.' | '.$row->link;
				}
				$sql_child1 = $this->menu_model->get_child_menu($row->menuid);
				if($sql_child1!=FALSE){
					foreach ($sql_child1 as $row1) {
						if(!in_array($row1->menuid, $temp)){
							$sel2 = ($selected==$row1->menuid) ? 'selected': '';
							if($id_menu!=$row1->menuid){
								$str .= '<option value="'.$row1->menuid.'" '.$sel2.' /><font color="#ffffff">__</font> <i class="fa   fa-angle-right"></i> '.$row1->menucaption.'|'.$row1->link;
							}
							array_push($temp, $row1->menuid);
							$sql_child2 = $this->menu_model->get_child_menu($row1->menuid);
							if($sql_child2!=FALSE){
								foreach ($sql_child2 as $row2) {
									if(!in_array($row2->menuid, $temp)){
										$sel3 = ($selected==$row2->menuid) ? 'selected': '';
										if($id_menu!=$row2->menuid){
											$str .= '<option value="'.$row2->menuid.'" '.$sel3.' /><font color="#ffffff">____</font> <i class="fa   fa-angle-right"></i> '.$row2->menucaption.' | '.$row2->link;
										}
										array_push($temp, $row2->menuid);
										$sql_child3 = $this->menu_model->get_child_menu($row2->menuid);
										if($sql_child3!=FALSE){
											foreach ($sql_child3 as $row3) {
												if(!in_array($row3->menuid, $temp)){
													$sel4 = ($selected==$row3->menuid) ? 'selected': '';
													if($id_menu!=$row3->menuid){
														$str .= '<option value="'.$row3->menuid.'" '.$sel4.' /><font color="#ffffff">______</font> <i class="fa   fa-angle-right"></i> '.$row3->menucaption.' | '.$row3->link;
													}
													array_push($temp, $row3->menuid);
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
		$str .= '</select>';
		return $str;
	}
  function getMainPage(){
    $currentUrl = base_url("admin_board/sideMenu/975fe10374a662c373f7653b19d8888df9f01841c7eba7dc0af418aa694b9545/");//current_url();
    $arrUrl     = explode("/",$currentUrl);
    array_pop($arrUrl);
    $strUrl = implode("/",$arrUrl);
    return $strUrl;
  }
  function delete_menu($id){
  	$this->db->where("menuid",$id);
  	$res = $this->db->delete("tbmenu");
  	if($res){
  		redirect("rootaccess/menu-manager");
  	}
  }
  function spreadMenu(){
  	$this->load->model("menu_model");
  	$this->load->model("user_model");

  	$arrMenu = $this->menu_model->getAllActiveID();
  	$strMenu = implode("|", $arrMenu);
  	$result  = $this->user_model->setRootUserAccess($strMenu);
  	if($result){
  		echo "OK";
  	}
  }
}
