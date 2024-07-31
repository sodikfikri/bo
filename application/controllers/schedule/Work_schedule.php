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
            ["data"=> $this->gtrans->line("Effective date"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Number Of Rounds"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Effective date"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Action"), "class"=>"text-center"]
        );

        $data['dataTable'] = $this->table->generate();

        if(!empty($this->session->userdata("ses_notif"))){
            $notif    = $this->session->userdata("ses_notif");
            $data['notif'] = $notif;
            $this->session->unset_userdata("ses_notif");
        }

        $parentViewData = [
            "title"   => "Jam Kerja",  // title page
            "content" => "schedule/work_schedule",  // content view
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

}