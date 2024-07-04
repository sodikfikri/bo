<?php

/**
 * Class untuk menyimpan fungsi yang umum yang digunakan hampir di semua bagian
 * Aplikasi
 **/
class Menu_model extends CI_Model
{
  var $tableName = "tbmenu";
  var $tableId   = "menuid";
  function __construct()
  {
    parent::__construct();
    $languange = !empty($this->session->userdata('lang')) ? $this->session->userdata('lang') :"en";
    $this->load->library("gtrans/gtrans",["lang" => $languange]);
  }

  function list_menu()
  {
		$modul_aktif = "semuamodul";

		$id_menu = !empty($_GET['im']) ? $_GET['im'] : '';
		if(!empty($_GET['im'])){
			$this->session->set_userdata('ses_im',base64_decode($id_menu));
		}

		$link_aktif   = !empty($this->session->userdata('ses_im')) ? $this->session->userdata('ses_im') : '';

		$arr_parent 	= explode("/", $this->build_the_parent_link($link_aktif)) ;
		$arr_akses 	  = explode("|", $this->session->userdata('ses_access'));
    $menuPermission = false;
		$sql_menu 		= $this->get_menu(null,$modul_aktif);

		$temp 	  		= [];
		$strOut   		= "";
		$access         = $this->session->userdata("access");
		$arrAccess      = explode("|", $access);

		foreach ($sql_menu as $row) {
			if(!in_array($row->menuid, $temp) && in_array($row->menuid, $arrAccess)){
				$sql_child1 = $this->get_child_menu($row->menuid,$modul_aktif);
				$has_child1 = ($sql_child1!=FALSE) ? 1 : 0;
				$link_menu  = str_replace(" ", "", explode(",", $row->link)[0]);
				if(in_array($row->menuid, $arr_akses)||$menuPermission==false){
          if($sql_child1!=FALSE){
            $tree_active1 = (in_array($row->menuid, $arr_parent)) ? 'active' : '';
            $strOut .= '<li class="treeview '.$tree_active1.'">
                        <a href="'.base_url($link_menu).'">
                          <i class="'.$row->class_icon.'"></i> <span>'.$this->gtrans->line($row->menucaption).'</span> <i class="fa fa-angle-left pull-right"></i>
                        </a>
                        <ul class="treeview-menu">';
            foreach ($sql_child1 as $row1) {
              if(!in_array($row1->menuid, $temp) && in_array($row1->menuid, $arrAccess)){
                $sql_child2 = $this->get_child_menu($row1->menuid,$modul_aktif);
                $has_child2 = ($sql_child2!=FALSE) ? 1 : 0;
                array_push($temp, $row1->menuid);
                $link_menu1  = str_replace(" ", "", explode(",", $row1->link)[0]) ;
                if(in_array($row1->menuid, $arr_akses)||$menuPermission==false){
                  if($sql_child2!=FALSE){
                    $tree_active2 = (in_array($row1->menuid, $arr_parent)) ? 'active' : '';
                    $strOut .= '<li class="treeview '.$tree_active2.'">
                                <a href="'.base_url($link_menu1).'"><i class="'.$row1->class_icon.'"></i> '.$this->gtrans->line($row1->menucaption).' <i class="fa fa-angle-left pull-right"></i></a>
                                <ul class="treeview-menu">';
                    foreach ($sql_child2 as $row2) {
                      if(!in_array($row2->menuid, $temp) && in_array($row2->menuid, $arrAccess)){
                        $sql_child3 = $this->get_child_menu($row2->menuid,$modul_aktif);
                        $has_child3 = ($sql_child3!=FALSE) ? 1 : 0;
                        array_push($temp, $row2->menuid);
                        $link_menu2  = str_replace(" ", "", explode(",", $row2->link)[0]);

                        if(in_array($row2->menuid, $arr_akses)||$menuPermission==false){
                          if($sql_child3!=FALSE){
                            $tree_active3 = (in_array($row2->menuid, $arr_parent)) ? 'active' : '';
                            $strOut .= '<li class="treeview '.$tree_active3.'">
                                        <a href="'.$link_menu2.'">
                                          <i class="'.$row2->class_icon.'"></i> <span>'.$this->gtrans->line($row2->menucaption).'</span> <i class="fa fa-angle-left pull-right"></i>
                      									</a>
                                        <ul class="treeview-menu">';
                            foreach ($sql_child3 as $row3) {
                              if(!in_array($row3->menuid, $temp) && in_array($row3->menuid, $arrAccess)){
                                if(in_array($row3->menuid, $arr_akses)||$menuPermission==false){
                                  $has_child4  = 0;
                                  $link_menu3  = str_replace(" ", "", explode(",", $row3->link)[0]) ;
                                  array_push($temp, $row3->menuid);

                                  $strAktif = ($link_aktif==$row3->menuid) ? 'class="active"' : '';
                                  $newtab4  = ($row3->newTab==1) ? ' target="blank" ' : '';
                                  preg_match("/http:\/\//", $link_menu3,$match);
                                  $count_match = count($match);
                                  $strLink  = ($link_menu3=='#') ? '#' : (($count_match>0) ? $link_menu3 : base_url($link_menu3).'/?im='.base64_encode($row3->menuid));
                                  $strOut  .= '<li '.$strAktif.'><a href="'.$strLink.'" '.$newtab4.'><i class="'.$row3->class_icon.'"></i><span>  '.$this->gtrans->line($row3->menucaption).'</span></a></li>';
                                }
                              }
                            }
                            $strOut .= '</ul></li>';
                          }else{
                            $strAktif = ($link_aktif==$row2->menuid) ? 'class="active"' : '';
                            $newtab3  = ($row2->newTab==1) ? ' target="blank" ' : '';
                            preg_match("/http:\/\//", $link_menu2,$match);
                            $count_match = count($match);
                            $strLink  = ($link_menu2=='#') ? '#' : (($count_match>0) ? $link_menu2 : base_url($link_menu2).'/?im='.base64_encode($row2->menuid));
                            $strOut .= '<li '.$strAktif.' ><a href="'.$strLink.'" '.$newtab3.' ><i class="'.$row2->class_icon.'"></i><span>  '.$this->gtrans->line($row2->menucaption).'</span></a></li>';
                          }
                        }
                      }
                    }
                    $strOut .= '</ul></li>';
                  }else{
                    $strAktif = ($link_aktif==$row1->menuid) ? 'class="active"' : '';
                    $newtab2  = ($row1->newTab==1) ? ' target="blank" ' : '';
                    preg_match("/http:\/\//", $link_menu1,$match);
                    $count_match = count($match);
                    $strLink  = ($link_menu1=='#') ? '#' : (($count_match>0) ? $link_menu1 : base_url($link_menu1).'/?im='.base64_encode($row1->menuid));
                    $strOut .= '<li '.$strAktif.'><a href="'.$strLink.'" '.$newtab2.' ><i class="'.$row1->class_icon.'"></i><span>  '.$this->gtrans->line($row1->menucaption).'</span></a></li>';
                  }
                }
              }
            }
            $strOut .= '</ul></li>';
          }else{
            $strAktif = ($link_aktif==$row->menuid) ? 'class="active"' : '';
            $newtab1  = ($row->newTab==1) ? ' target="blank" ' : '';
            preg_match("/http:\/\//", $link_menu,$match);
            $count_match = count($match);
            $strLink  = ($link_menu=='#') ? '#' : (($count_match>0) ? $link_menu : base_url($link_menu).'/?im='.base64_encode($row->menuid));
            $strOut  .= '<li '.$strAktif.' ><a href="'.$strLink.'" '.$newtab1.' ><i class="'.$row->class_icon.'"></i><span> '.$this->gtrans->line($row->menucaption).'</span></a></li>';
          }
        }
      }
    }
	  return $strOut;
	}
  
  private function build_the_parent_link($id_link)
  {
		$strParent = '';

		$this->db->select('parentid');

		$this->db->where("menuid",$id_link);
		/* mengambil id link pertama */
		$sql0 = $this->db->get('tbmenu')->row();

		/* akhir link pertama */
		$id_parent = !empty($sql0->parentid) ? $sql0->parentid : 0;

		if ($id_parent!=0) {

			$super_parent = $id_parent;
			while($super_parent!=0) {
				$this->db->select('parentid');
				$this->db->select('menuid');
				$sql = $this->db->get_where('tbmenu',['menuid'=>$id_parent])->row();
				$super_parent = $sql->parentid;
				$link         = $sql->menuid;
				$strParent   .= $link."/";
				if($super_parent!=0){

					$id_parent = $super_parent;
				}
			}
		}
		return $strParent;
	}

  public function get_menu($id=null,$input_tags=null)
  {
		if($id==null){
			if($input_tags==null){
				$this->db->order_by('urut','ASC');
				return $this->db->get('tbmenu')->result();
			}else{
				$tags    = explode(",", $input_tags);
				$max_tag = count($tags)-1;
				$where   = "";

				foreach ($tags as $index => $tag) {
					if($index==0){
						$where .= " tags like '%$tag%' ";
					}else{
						$where .= " or tags like '%$tag%' ";
					}
				}

				$this->db->where($where);
				$this->db->order_by('urut','ASC');
				return $this->db->get('tbmenu')->result();
			}
		}else{

			$this->db->where('menuid',$id);
			return $this->db->get('tbmenu')->row();
		}
	}

  /*
	Updated At : 2018-01-29 12.42
	Action     : Mengambil child menu dari root
	*/
	public function get_child_menu($parent,$input_tag=""){

		if($input_tag!=""){
			$tags = explode(",", $input_tag);
		}
		$this->db->order_by('urut','ASC');
		if($input_tag==""){
			$sql = $this->db->get_where("tbmenu",["parentid"=>$parent]);
		}else{

			$where     = "";
			$max_index = count($tags)-1;

			foreach ($tags as $index => $tag) {

				if(count($tags)==1){
					$where = "tags like '%$tag%' ";
				}else{
					if($index==0){
						$where .= " (tags like '%$tag%' ";
					}elseif($index==$max_index){
						$where .= " or tags like '%$tag%' )";
					}else{
						$where .= " or tags like '%$tag%' ";
					}
				}
			}

			$this->db->where("parentid",$parent);
			$this->db->where($where);
			$sql = $this->db->get("tbmenu");
		}

		if($sql->num_rows()==0){
			return FALSE;
		}else{
			return $sql->result();
		}
	}
  public function insert_menu($data){
		$this->db->insert($this->tableName,$data);
	}

	public function update_menu($data,$id){
		$this->db->where($this->tableId,$id);
		$this->db->update($this->tableName,$data);
	}

  function get_menu_bertingkat(){
		$sql_menu 				= $this->get_menu();
		$temp 					  = [];
		$arr_out    			= [];

		foreach ($sql_menu as $row) {
			if(!in_array($row->menuid, $temp)){

				$sql_child1 = $this->get_child_menu($row->menuid);
				$has_child1 = ($sql_child1!=FALSE) ? 1 : 0;
				$list = ["id"=>$row->menuid,"nama"=>$row->menucaption,"tingkat"=>"0","link"=>$row->link,"icon"=>$row->class_icon,"has_child"=>$has_child1];
				array_push($arr_out, $list);


				if($sql_child1!=FALSE){
					foreach ($sql_child1 as $row1) {
						if(!in_array($row1->menuid, $temp)){
							$sql_child2 = $this->get_child_menu($row1->menuid);
							$has_child2 = ($sql_child2!=FALSE) ? 1 : 0;
							$list1 = ["id"=>$row1->menuid,"nama"=>$row1->menucaption,"tingkat"=>"1","link"=>$row1->link,"icon"=>$row1->class_icon,"has_child"=>$has_child2];
							array_push($arr_out, $list1);

							array_push($temp, $row1->menuid);

							if($sql_child2!=FALSE){
								foreach ($sql_child2 as $row2) {
									if(!in_array($row2->menuid, $temp)){
										$sql_child3 = $this->get_child_menu($row2->menuid);
										$has_child3 = ($sql_child3!=FALSE) ? 1 : 0;
										$list2 = ["id"=>$row2->menuid,"nama"=>$row2->menucaption,"tingkat"=>"2","link"=>$row2->link,"icon"=>$row2->class_icon,"has_child"=>$has_child3];
										array_push($arr_out, $list2);

										array_push($temp, $row2->menuid);

										if($sql_child3!=FALSE){
											foreach ($sql_child3 as $row3) {
												if(!in_array($row3->menuid, $temp)){
													$has_child4 = 0;
													$list3 = ["id"=>$row3->menuid,"nama"=>$row3->menucaption,"tingkat"=>"3","link"=>$row3->link,"icon"=>$row3->class_icon,"has_child"=>$has_child4];
													array_push($arr_out, $list3);

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
		return $arr_out;
	}

  // ambil parent id sampai level 0
  function getParentId($menuid)
  {
    $parents = [];
    $this->db->where("menuid",$menuid);
    $sql = $this->db->get($this->tableName);
    if($sql->num_rows()>0){
      $data = $sql->row();
      if($data->parentid>0){
        return $data->parentid;
      }else{
        return false;
      }
    }else{
      return false;
    }
  }

  function getAllActiveID(){
  	$this->db->select("menuid");
  	$sql    = $this->db->get($this->tableName);
  	$output = [];
  	foreach ($sql->result() as $row) {
  		$output[] = $row->menuid;
  	}
  	return $output;
  }
}
