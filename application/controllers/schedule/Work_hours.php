<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Work_hours extends CI_Controller
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

    public function SetRespose($code, $message, $data = '') {
        $this->response['meta']['code'] = $code;
        $this->response['meta']['message'] = $message;
        if ($data) {
            $this->response['data'] = $data;
        }

        return $this->response;
    }

    function index() {
        $this->table->set_template($this->tabel_template);
        $this->table->set_heading(
            ["data"=> $this->gtrans->line("No"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Name"), "class"=>"text-left"],
            ["data"=> $this->gtrans->line("Effective Date"), "class"=>"text-left"],
            ["data"=> $this->gtrans->line("Unit"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Action"), "class"=>"text-center"]
        );

        $data = $this->schedule_model->listHour($this->appid);
        foreach($data as $key => $items) {
            $encId = $this->encryption_org->encode($items->id);
            $workday = $items->workday == 1 ? 'Hari' : 'Minggu';
            $this->table->add_row(
                [
                    'data' => $key+1,
                    'style' => 'text-align:center'
                ],
                [
                    'data' => $items->name,
                    'style' => 'text-align:left'
                ],
                [
                    'data' => (new DateTime($items->created_at))->format('Y-m-d'),
                    'style' => 'text-align:left'
                ],
                [
                    'data' => $items->unit . ' ' . $workday,
                    'style' => 'text-align:center'
                ],
                [
                    'data' => '<span style="cursor:pointer" data-id="'.$encId.'" class="text-blue btn-detail"><i  class="fa fa-edit fa-lg"></i></span>
                                <span style="cursor:pointer" data-id="'.$encId.'" class="text-red btn-del"><i  class="fa fa-trash fa-lg"></i></span>',
                    'style' => 'text-align:center;'
                ]
            );
        }

        $data['dataTable'] = $this->table->generate();
        $branchData = $this->schedule_model->getBranch($this->appid);

        if(!empty($this->session->userdata("ses_notif"))){
            $notif    = $this->session->userdata("ses_notif");
            $data['notif'] = $notif;
            $this->session->unset_userdata("ses_notif");
        }
        $parentViewData = [
            "title"   => "Jam Kerja",  // title page
            "content" => "schedule/work_hours",  // content view
            "viewData"=> $data,
            "listMenu"=> $this->listMenu,
            "branchData"=> $branchData,
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

    function saveData() {

        $idx = $this->input->post('id_hidden');
        $params = [
            'appid' => $this->appid,
            'name' => $this->input->post('name'),
            'location' => json_encode($this->input->post('location')),
            'start_time' => $this->input->post('start_work'),
            'end_time' => $this->input->post('end_work'),
            'start_checkin_time' => $this->input->post('start_checkin_time'),
            'end_checkin_time' => $this->input->post('end_checkin_time'),
            'start_checkout_time' => $this->input->post('start_checkout_time'),
            'end_checkout_time' => $this->input->post('end_checkout_time'),
            'break_type' => $this->input->post('break_type'),
            'late_minutes' => $this->input->post('late_tolerance'),
            'early_minutes' => $this->input->post('early_leave_tolerance'),
            'color' => $this->input->post('colour'),
        ];
        
        if ($this->input->post('break_type') == '1') {
            $params['break_duration'] = $this->input->post('break_duration');
        } elseif ($this->input->post('break_type') == '2') {
            $params['break_in'] = $this->input->post('break_hour_start');
            $params['break_out'] = $this->input->post('break_hour_end');
        }

        if ($idx == '0') {
            # code...
            $params['created_at'] = (new DateTime())->format('Y-m-d H:i:s');
    
            $ins = $this->schedule_model->insHour($params);
            
            if ($ins) {
                $this->session->set_userdata('ses_notif',['type' => 'success', 'title' => 'Success', 'msg'=> $this->gtrans->line('Add data has success full')]);
                setActivity("schedule working hours","add");
            } 
        } else {
            $this->load->library("encryption_org");
            $uptid = $this->encryption_org->decode($idx);
            $params['updated_at'] = (new DateTime())->format('Y-m-d H:i:s');
    
            $upt = $this->schedule_model->uptHour($uptid, $params);
            
            if ($upt) {
                $this->session->set_userdata('ses_notif',['type' => 'success', 'title' => 'Success', 'msg'=> $this->gtrans->line('Update data has success full')]);
                setActivity("schedule working hours","update");
            } 
        }

        return redirect("schedule-work-hours");
    }

    function getDetailData() {
        $this->load->library("encryption_org");
        $id = $this->encryption_org->decode($this->input->get('id'));

        $data = $this->schedule_model->getDetailHour($id);
        if (count($data) == 0) {
            # code...
            echo json_encode([
                'meta' => [
                    'code' => 404,
                    'message' => 'Data not found'
                ],
                'data' => $data
            ]); return;
        }

        $data[0]->id = $this->encryption_org->encode($data[0]->id);
        
        echo json_encode([
            'meta' => [
                'code' => 200,
                'message' => 'Success'
            ],
            'data' => $data[0]
        ]); return;
    }

    function delData($encId) {
        $this->load->library("encryption_org");
        $idx = $this->encryption_org->decode($encId);

        $upt = $this->schedule_model->uptHour($idx, [
            'is_delete' => 1
        ]);

        if ($upt) {
            $this->session->set_userdata('ses_notif',['type' => 'success', 'title' => 'Success', 'msg'=> $this->gtrans->line('Delete data has success full')]);
            setActivity("schedule working hours","delete");
        } 

        return redirect("schedule-work-hours");
    }

}