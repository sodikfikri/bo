<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Period extends CI_Controller
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
        $this->load->model("period_model");
        
        $this->system_model->checkSession(15);
        $this->listMenu = $this->menu_model->list_menu();
        $this->now = date("Y-m-d H:i:s");
        $this->appid = $this->session->userdata("ses_appid");
        $this->load->library('upload');
        // bahasa
    }

    function index() {

        $this->table->set_template($this->tabel_template);
        $this->table->set_heading(
            // ["data"=> $this->gtrans->line("#"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Start"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("End"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Type"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Status"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Created At"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Action"), "class"=>"text-center"]
        );

        $list = $this->period_model->list($this->appid);
        
        foreach($list as $key => $items) {
            $encId = $this->encryption_org->encode($items->id);
            $this->table->add_row(
                [
                    'data' => $items->start < 10 ? '0'.$items->start:$items->start,
                    'style' => 'text-align:center'
                ],
                [
                    'data' => $items->end < 10 ? '0'.$items->end:$items->end,
                    'style' => 'text-align:center' 
                ],
                [
                    'data' => $items->type == 1 ? 'Monthly' : 'Weekly',
                    'style' => 'text-align:center' 
                ],
                [
                    'data' => $items->is_active != 0 ? '<span class="label label-success">Active</span>' : '<span class="label label-danger">Inactive</span>',
                    'style' => 'text-align:center;'
                ],
                [
                    'data' => $items->created_at,
                    'style' => 'text-align:center'
                ],
                [
                    'data' => '<span style="cursor:pointer" data-id="'.$encId.'" data-sdate="'.$items->start.'" data-edate="'.$items->end.'" data-status="'.$items->is_active.'" data-type="'.$items->type.'" class="text-blue btn-detail"><i  class="fa fa-edit fa-lg"></i></span>
                                <span style="cursor:pointer" data-id="'.$encId.'" class="text-red btn-del"><i  class="fa fa-trash fa-lg"></i></span>',
                    'style' => 'text-align:center;'
                ]
            );
        }

        $data['datatable'] = $this->table->generate();

        if(!empty($this->session->userdata("ses_notif"))){
            $arrNotif = $this->session->userdata("ses_notif");
      
            $notif    = createNotif($arrNotif['type'],$arrNotif['header'],$arrNotif['msg']);
            $data['notif'] = $notif;
            $this->session->unset_userdata("ses_notif");
        }
        $parentViewData = [
            "title"   => "Leave Categories",  // title page
            "content" => "master/period",  // content view
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

    function submitData() {
        $id = $this->input->post('id');
        $params = [
            'appid' => $this->appid,
            'start' => $this->input->post('start_date'),
            'end' => $this->input->post('end_date'),
            'type' => $this->input->post('type'),
            'is_active' => $this->input->post('status'),
        ];

        if ($id == 0) {
            if ($this->input->post('status') == 1) {
                $validate = $this->period_model->validateStoreData($this->appid, null, $params['type']);
                if (count($validate) != 0) {
                    $this->session->set_userdata('ses_notif',['type'=>'danger','header'=>'Failed','msg'=> $this->gtrans->line('Cannot add data with active status')]);
                    return redirect("active-period");
                }
            }
            $params['created_at'] = (new DateTime())->format('Y-m-d H:i:s');
            $ins = $this->period_model->saveData($params);
            if ($ins) {
                $this->session->set_userdata('ses_notif',['type'=>'success','header'=>'Success','msg'=> $this->gtrans->line('Success add data')]);
                setActivity("master active periode","add");
            } else {
                $this->session->set_userdata('ses_notif',['type'=>'danger','header'=>'Failed','msg'=> $this->gtrans->line('Fail to add period')]);
            }
        } else {
            $this->load->library("encryption_org");
            $pid = $this->encryption_org->decode($id);
            if ($this->input->post('status') == 1) {
                $validate = $this->period_model->validateStoreData($this->appid, $pid, $params['type']);
                if (count($validate) != 0) {
                    $this->session->set_userdata('ses_notif',['type'=>'danger','header'=>'Failed','msg'=> $this->gtrans->line('Cannot change data with active status')]);
                    return redirect("active-period");
                }
            }

            $params['updated_at'] = (new DateTime())->format('Y-m-d H:i:s');
            
            $upt = $this->period_model->updateData($pid, $params);

            if ($upt) {
                $this->session->set_userdata('ses_notif',['type'=>'success','header'=>'Success','msg'=> $this->gtrans->line('Success update data')]);
                setActivity("master active periode","update");
            } else {
                $this->session->set_userdata('ses_notif',['type'=>'danger','header'=>'Failed','msg'=> $this->gtrans->line('Fail to update period')]);
            }   
        }
        return redirect("active-period");
    }

    function delData($encId) {
        $this->load->library("encryption_org");
        $pid = $this->encryption_org->decode($encId);
        $del = $this->period_model->deleteData($pid);

        if ($del) {
            $this->session->set_userdata('ses_notif',['type'=>'success','header'=>'Success','msg'=> $this->gtrans->line('period Was Deleted')]);
            setActivity("master active periode","delete");
        } else {
            $this->session->set_userdata('ses_notif',['type'=>'danger','header'=>'Failed','msg'=> $this->gtrans->line('Filed to delete period')]);
        }
        return redirect("active-period");
    }

}