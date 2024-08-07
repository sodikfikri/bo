<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Schedule_temp extends CI_Controller
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
        
        $this->load->model("schedule_model");
        $this->system_model->checkSession(15);
        $this->listMenu = $this->menu_model->list_menu();
        $this->now = date("Y-m-d H:i:s");
        $this->appid = $this->session->userdata("ses_appid");
        $this->load->library("encryption_org");
        // bahasa
    }

    function index() {
        $this->table->set_template($this->tabel_template);
        $this->table->set_heading(
            ["data"=> $this->gtrans->line("#"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Employee"), "class"=>"text-left"],
            ["data"=> $this->gtrans->line("Departement"), "class"=>"text-left"],
            ["data"=> $this->gtrans->line("Effective Date"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Schedule Type"), "class"=>"text-left"],
            ["data"=> $this->gtrans->line("Preview"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Action"), "class"=>"text-center"]
        );

        $data['departement'] = $this->schedule_model->getDetaprtementByAppid($this->appid);
        $data['hour'] = $this->schedule_model->listHour($this->appid);

        $list = $this->schedule_model->getListSchTemp($this->appid);
        foreach($list as $key => $items) {
            $this->table->add_row(
                [
                    'data' => $key+1,
                    'style' => 'text-align:center;'
                ],
                [
                    'data' => $items->count_user . ' Employee',
                    'style' => 'text-align:left;'
                ],
                [
                    'data' => $items->departement_name,
                    'style' => 'text-align:left;'
                ],
                [
                    'data' => "$items->start_date - $items->end_date",
                    'style' => 'text-align:center;'
                ],
                [
                    'data' =>  $this->gtrans->line("Temporary"),
                    'style' => 'text-align:left;'
                ],
                [
                    'data' => '<span class="priview-calendar" data-id="'.$this->encryption_org->encode($items->numrun_id).'" style="color: #039be6; cursor: pointer;"><i class="fa fa-calendar"></i> Preview Calendar</span>',
                    'style' => 'text-align:center'
                ],
                [
                    'data' => '<span style="cursor:pointer" data-batch="'.$items->batch.'" class="text-blue btn-detail"><i  class="fa fa-list"></i></span>',
                    'style' => 'text-align:center;'
                ]
            );
        }

        $data['dataTable'] = $this->table->generate();

        if(!empty($this->session->userdata("ses_notif"))){
            $notif    = $this->session->userdata("ses_notif");
            $data['notif'] = $notif;
            $this->session->unset_userdata("ses_notif");
        }

        $parentViewData = [
            "title"   => "Shift Schedule",  // title page
            "content" => "schedule/schedule_temp",  // content view
            "viewData"=> $data,
            "listMenu"=> $this->listMenu,
            "varJS" => ["url" => base_url()],
            "externalCSS" => [
                base_url("asset/template/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css"),
                base_url("asset/template/bower_components/select2/dist/css/select2.min.css"),
            ],
            "externalJS" => [
                base_url("asset/template/bower_components/datatables.net/js/jquery.dataTables.min.js"),
                base_url("asset/template/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"),
                "https://cdn.jsdelivr.net/npm/sweetalert2@8",
                base_url("asset/template/bower_components/select2/dist/js/select2.full.min.js"),
                base_url("asset/js/checkCode.js"),
                base_url("asset/js/user.js")
        
            ]
        ];
        $this->load->view("layouts/main",$parentViewData);
        $this->gtrans->saveNewWords();
    }

    function submitData() {
        $data = $this->input->post('data');
        $data_decode = json_decode($data);

        $params = [];
        $batch = uniqid();

        foreach($data_decode->employee as $items) {
            foreach($data_decode->schclass_id as $sch) {
                $obj = [
                    'appid' => $this->appid,
                    'user_id' => $items,
                    'start_date' => $data_decode->start_date,
                    'end_date' => $data_decode->end_date,
                    'departement_id' => $data_decode->departement_id,
                    'schclass_id' => $sch,
                    'batch' => $batch,
                    'created_at' => (new DateTime())->format('Y-m-d H:i:s')
                ];

                array_push($params, $obj);
            }
        }

        $ins = $this->schedule_model->insBatchSchTemp($params);

        if ($ins) {
            $this->session->set_userdata('ses_notif',['type' => 'success', 'title' => 'Success', 'msg'=> $this->gtrans->line('Add data has success full')]);
            setActivity("schedule temporary schedule","add");
        } else {
            $this->session->set_userdata('ses_notif',['type' => 'error', 'title' => 'Failed', 'msg'=> $this->gtrans->line('Fail to add data')]);
        }

        echo json_encode([
            'meta' => [
                'code' => 200,
                'message' => 'Success'
            ],
        ]); return;
    }

    function detailEmpSch() {
        $btach = $this->input->get('batch');

        $data = $this->schedule_model->getDetailEmpSch($this->appid, $btach);

        foreach($data as $item) {
            $item->user_id = $this->encryption_org->encode($item->user_id);
            $item->departement_id = $this->encryption_org->encode($item->departement_id);
        }

        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'Success get data'
            ],
            'data' => $data
        ];

        echo json_encode($response);
        return;
    }

    function delDetailEmpSch() {
        $user_id = $this->encryption_org->decode($this->input->post('user_id'));
        $departement_id = $this->encryption_org->decode($this->input->post('departement_id'));
        $batch = $this->input->post('batch');

        $del = $this->schedule_model->delSchTempEmp($this->appid, $user_id, $departement_id, $batch);
        
        if ($del != 'success') {
            $response = [
                'meta' => [
                    'code' => '400',
                    'message' => 'Failed to delete data'
                ],
            ];
    
            echo json_encode($response);
            return;
        }

        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'Success to delete data'
            ],
        ];

        echo json_encode($response);
        return;
    }
}