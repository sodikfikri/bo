<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Leave_categories extends CI_Controller
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
        
        $this->load->model("leave_model");
        $this->system_model->checkSession(15);
        $this->listMenu = $this->menu_model->list_menu();
        $this->now = date("Y-m-d H:i:s");
        $this->appid = $this->session->userdata("ses_appid");
        $this->load->library('upload');
        // bahasa
    }

    public function index() {

        // print_r('masuk'); die;
        $this->table->set_template($this->tabel_template);
        $this->table->set_heading(
            ["data"=> '<div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="head-check">
                            <label class="form-check-label" for="head-check"></label>
                        </div>', "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Name"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Icon"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Created At"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Action"), "class"=>"text-center"]
        );

        $data_cats = $this->leave_model->getcategoryList($this->appid);

        foreach($data_cats as $key => $items) {
            $encId = $this->encryption_org->encode($items->id);
            $this->table->add_row(
                [
                    'data' => '<div class="form-check">
                                    <input class="form-check-input" type="checkbox" name id="checkid[]" value="'.$encId.'">
                                    <label class="form-check-label" for="checkid[]"></label>
                                </div>',
                    'style' => 'text-align: center;'
                ],
                $items->name,
                [
                    'data' => $items->icon ? '<img style="height: 30px;" src="'.base_url() . ltrim($items->icon, '.').'">' : '',
                    'style' => 'text-align:center;'
                ],
                $items->created_at,
                [
                    'data' => '<span style="cursor:pointer" data-id="'.$encId.'" data-name="'.$items->name.'" data-imgname="'.basename($items->icon).'" class="text-blue btn-detail"><i  class="fa fa-edit fa-lg"></i></span>
                                <span style="cursor:pointer" data-id="'.$encId.'" class="text-red btn-del"><i  class="fa fa-trash fa-lg"></i></span>',
                    'style' => 'text-align:center;'
                ]
            );
        }

        $data['catsTable'] = $this->table->generate();
        if(!empty($this->session->userdata("ses_notif"))){
            $arrNotif = $this->session->userdata("ses_notif");
      
            $notif    = createNotif($arrNotif['type'],$arrNotif['header'],$arrNotif['msg']);
            $data['notif'] = $notif;
            $this->session->unset_userdata("ses_notif");
        }
        $parentViewData = [
            "title"   => "Leave Categories",  // title page
            "content" => "leave/categories/index",  // content view
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

    function save_act() {
        $id = $this->input->post('id');
        $name = $this->input->post('name');

        $params = [
            'name' => $name,
            'appid' => $this->appid
        ];

        // $file_name = 'iconnih';
        $config['upload_path']          = './sys_upload/leave/icon/';
        $config['allowed_types']        = 'gif|jpg|jpeg|png';
        $config['file_name']            = uniqid();
        $config['overwrite']            = true;
        $config['max_size']             = 1024; // 1MB
        $config['max_width']            = 1080;
        $config['max_height']           = 1080;

        $this->upload->initialize($config);

        if (!empty($_FILES['icon']['name'])) {
            if (!$this->upload->do_upload('icon')) {
                $this->session->set_userdata('ses_notif',['type'=>'failed','header'=>'Failed','msg'=> $this->gtrans->line('Fail save icon')]);
                return redirect("master-leave-categories");
            }
            $uploaded_data = $this->upload->data();
            $params['icon'] = $config['upload_path'].$uploaded_data['file_name'];
        }

        if ($id == 0) {
            $params['created_at'] = (new DateTime())->format('Y-m-d H:i:s');

            $ins = $this->leave_model->addCats($params);
            if ($ins) {
                $this->session->set_userdata('ses_notif',['type'=>'success','header'=>'Success','msg'=> $this->gtrans->line('Success add data')]);
                setActivity("master leavel categories","add");
            } else {
                $this->session->set_userdata('ses_notif',['type'=>'failed','header'=>'Failed','msg'=> $this->gtrans->line('Fail to add category')]);
            }
            return redirect("master-leave-categories");
        } else {
            $this->load->library("encryption_org");
            $catsid = $this->encryption_org->decode($id);
            
            // remove file
            unlink('./sys_upload/leave/icon/'.$this->input->post('icon_name'));
            
            $upt = $this->leave_model->updateCats($catsid, $params);
            if ($upt) {
                $this->session->set_userdata('ses_notif',['type'=>'success','header'=>'Success','msg'=> $this->gtrans->line('Success update data')]);
                setActivity("master leavel categories","update");
            } else {
                $this->session->set_userdata('ses_notif',['type'=>'failed','header'=>'Failed','msg'=> $this->gtrans->line('Fail to update category')]);
            }
            return redirect("master-leave-categories");
        }
    }

    function del_act($encId) {
        $this->load->library("encryption_org");
        $catsid = $this->encryption_org->decode($encId);
        $del = $this->leave_model->delCats($catsid);

        if ($del) {
            $this->session->set_userdata('ses_notif',['type'=>'success','header'=>'Success','msg'=> $this->gtrans->line('Categories Was Deleted')]);
            setActivity("master leavel categories","delete");
            redirect("master-leave-categories");
        } else {
            $this->session->set_userdata('ses_notif',['type'=>'danger','header'=>'Failed','msg'=> $this->gtrans->line('Filed to delete categories')]);
            redirect("master-leave-categories");
        }
    }

}