<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Trx_leave extends CI_Controller
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
        // bahasa
    }

    function index() {
        // print_r('masuk'); die;
        $this->table->set_template($this->tabel_template);
        $this->table->set_heading(
            ["data"=> $this->gtrans->line("Employee Name"), "class"=>"text-left"],
            ["data"=> $this->gtrans->line("Category"), "class"=>"text-left"],
            ["data"=> $this->gtrans->line("Start Time"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("End Time"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Reason"), "class"=>"text-left"],
            ["data"=> $this->gtrans->line("Document"), "class"=>"text-center"]
        );

        $data_leave = $this->leave_model->leaveList($this->appid);
        foreach($data_leave as $key => $items) {
            $this->table->add_row(
                $items->employee_full_name,
                $items->category_name,
                [
                    'data' => $items->category_id == 1 || $items->category_id == 3 ? $items->start_date : (new DateTime($items->created_at))->format('Y-m-d') .' '. $items->start_time,
                    'style' => 'text-align:center;'
                ],
                [
                    'data' => $items->category_id == 1 || $items->category_id == 3 ? $items->end_date : (new DateTime($items->created_at))->format('Y-m-d') .' '.  $items->end_time,
                    'style' => 'text-align:center;'
                ],
                $items->reason,
                [
                    'data' => '<span style="cursor:pointer" data-id="'.$this->encryption_org->encode($items->id).'" class="text-blue btn-download"><i  class="fa fa-download fa-lg"></i></span>',
                    'style' => 'text-align:center;'
                ]
            );
        }
        $data['datatable'] = $this->table->generate();

        $parentViewData = [
            "title"   => "History Leave",  // title page
            "content" => "transaction/trx_leave",  // content view
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

    public function download_file($encId) {
        $this->load->library("encryption_org");
        $this->load->helper(array('url','download'));	
        $leaveid = $this->encryption_org->decode($encId);

        $data = $this->leave_model->getLeaveClassById($leaveid);

        force_download($data[0]->doc_path, NULL);
    }

    function toxlsx() {

        $data = $this->leave_model->leaveList($this->appid);

        echo json_encode($data);
    }
}