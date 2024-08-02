<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Work_schedule extends CI_Controller 
{

    var $appid;
    var $listMenu = "";
    var $now;
    var $tabel_template  = array(
        'table_open'            => '<table class="table table-bordered table-stripped" id="datatable">',
        'table_close'           => '</table>'
    );

    public $response = [];

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
        
        $this->load->library("form_validation");
        $this->load->library("encryption_org");
    }

    function index() {
        $this->table->set_template($this->tabel_template);
        $this->table->set_heading(
            ["data"=> $this->gtrans->line("#"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Employee"), "class"=>"text-left"],
            ["data"=> $this->gtrans->line("Departemen"), "class"=>"text-left"],
            ["data"=> $this->gtrans->line("Number Of Rounds"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Schedule Type"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Preview"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Action"), "class"=>"text-center"]
        );

        if(!empty($this->session->userdata("ses_notif"))){
            $notif    = $this->session->userdata("ses_notif");
            $data['notif'] = $notif;
            $this->session->unset_userdata("ses_notif");
        }

        $data['departement'] = $this->schedule_model->getDetaprtementByAppid($this->appid);
        $data['shift'] = $this->schedule_model->listShift($this->appid);
        $data['hour'] = $this->schedule_model->listHour($this->appid);
        
        $list = $this->schedule_model->listAssign($this->appid);

        foreach($list as $key => $items) {
            if ($items->unit == 0) {
                $workday = 'Hari';
            } elseif ($items->unit == 1) {
                $workday = 'Minggu';
            } elseif ($items->unit == 2) {
                $workday = 'Bulan';
            }
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
                    'data' => $items->type_schedule == 'Automatic' ? '' : $items->cyle . " $workday",
                    'style' => 'text-align:left;'
                ],
                [
                    'data' =>  $items->type_schedule,
                    'style' => 'text-align:left;'
                ],
                [
                    'data' => $items->type_schedule == 'Automatic' ? '' : '<span class="priview-calendar" data-id="'.$this->encryption_org->encode($items->numrun_id).'" style="color: #039be6; cursor: pointer;"><i class="fa fa-calendar"></i> Preview Calendar</span>',
                    'style' => 'text-align:center'
                ],
                [
                    'data' => '<span style="cursor:pointer" data-batch="'.$items->batch.'" class="text-blue btn-detail"><i  class="fa fa-list"></i></span>',
                    'style' => 'text-align:center;'
                ]
            );
        }

        $data['dataTable'] = $this->table->generate();

        $parentViewData = [
            "title"   => "Work Schedule",  // title page
            "content" => "schedule/work_schedule",  // content view
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

    function getEmpDepartement() {
        $dept_id = $this->input->post('departement_id');

        $data = $this->schedule_model->getEmpByDept($dept_id);

        if (count($data) == 0) {
            $response = [
                'meta' => [
                    'code' => '404',
                    'message' => 'data not found'
                ],
                'data' => []
            ];

            echo json_encode($response);
            return;
        }

        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'success get data'
            ],
            'data' => $data
        ];

        echo json_encode($response);
        return;
    }

    function getAllEmp() {
        $search = $this->input->get('q');
        $page = $this->input->get('page');
        $limit = 10;
        $offset = ($page - 1) * $limit;
        
        $results = $this->schedule_model->getAllEmp($this->appid, $search, $limit, $offset);

        $data = [];
        foreach ($results as $row) {
            $data[] = [
                'id' => $row->employee_id,
                'text' => $row->employee_full_name,
                'dpt' => $row->departement_id
            ];
        }
        $total_count = $this->schedule_model->countEmpByName($this->appid, $search);

        echo json_encode([
            'items' => $data,
            'total_count' => $total_count[0]->total
        ]);
    }

    function AssignWorkSchedule() {
        $params = $this->input->post('data');
        $data = json_decode($params);

        $ins = $this->schedule_model->SaveWorkScheduled($this->appid, $data);

        if (!$ins) {
            $response = [
                'meta' => [
                    'code' => '400',
                    'message' => 'Failed insert data'
                ],
            ];
    
            echo json_encode($response);
            return;
        }
        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'Success insert data'
            ],
            'data' => $ins
        ];

        echo json_encode($response);
        return;
    }

    function findEmployee() {
        $id = $this->input->post('id');

        $data = $this->schedule_model->getEmpById($id);

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

    function employeeInSchedule() {
        $btach = $this->input->get('batch');

        $data = $this->schedule_model->getDetailEmpOnSch($btach);

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

    function deleteScheduleEmployee() {
        $user_id = $this->encryption_org->decode($this->input->post('user_id'));
        $departement_id = $this->encryption_org->decode($this->input->post('departement_id'));
        $batch = $this->input->post('batch');

        $del = $this->schedule_model->deleteScheduleEmployee($this->appid, $user_id, $departement_id, $batch);
        
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