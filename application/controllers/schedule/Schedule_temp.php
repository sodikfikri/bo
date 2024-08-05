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
            ["data"=> $this->gtrans->line("Shift Name"), "class"=>"text-left"],
            ["data"=> $this->gtrans->line("Effective Date"), "class"=>"text-left"],
            ["data"=> $this->gtrans->line("Number Of Rounds"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Unit"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Preview"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Action"), "class"=>"text-center"]
        );

        $data['departement'] = $this->schedule_model->getDetaprtementByAppid($this->appid);
        $data['hour'] = $this->schedule_model->listHour($this->appid);

        $data['dataTable'] = $this->table->generate();

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
}