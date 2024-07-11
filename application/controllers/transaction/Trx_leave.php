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
        $is_filter = false;
        $this->table->set_template($this->tabel_template);
        $this->table->set_heading(
            ["data"=> '<div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="head-check">
                            <label class="form-check-label" for="head-check"></label>
                        </div>', "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Employee Name"), "class"=>"text-left"],
            ["data"=> $this->gtrans->line("Category"), "class"=>"text-left"],
            ["data"=> $this->gtrans->line("Start Time"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("End Time"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Reason"), "class"=>"text-left"],
            ["data"=> $this->gtrans->line("Document"), "class"=>"text-center"],
            ["data"=> $this->gtrans->line("Action"), "class"=>"text-center"]
        );

        $params = [];
        if ($this->input->post()) {
            if ($this->input->post('category') != 0) {
                $params['category'] = $this->input->post('category');
            } 
            
            if ($this->input->post('start_date') && $this->input->post('end_date')) {
                $params['start_date'] = $this->input->post('start_date');
                $params['end_date'] = $this->input->post('end_date');
            }
        }
        $data_cats = $this->leave_model->getcategoryList($this->appid);

        $data_leave = $this->leave_model->leaveList($this->appid, $params);

        
        foreach($data_leave as $key => $items) {
            $encId = $this->encryption_org->encode($items->id);
            $this->table->add_row(
                [
                    'data' => '<div class="form-check">
                                    <input class="form-check-input checkid" type="checkbox" name="checkid" id="checkid" value="'.$encId.'">
                                    <label class="form-check-label" for="checkid"></label>
                                </div>',
                    'style' => 'text-align: center;'
                ],
                $items->employee_full_name,
                $items->category_name,
                [
                    'data' => $items->category_id == 1 || $items->category_id == 3 ? $items->start_date : $items->start_date .' '. $items->start_time,
                    'style' => 'text-align:center;'
                ],
                [
                    'data' => $items->category_id == 1 || $items->category_id == 3 ? $items->end_date : $items->end_date .' '.  $items->end_time,
                    'style' => 'text-align:center;'
                ],
                $items->reason,
                [
                    'data' => '<span style="cursor:pointer" data-id="'.$this->encryption_org->encode($items->id).'" data-name="'.$items->doc_name.'" class="text-blue btn-show-files"><i  class="fa fa-file fa-lg"></i></span>',
                    'style' => 'text-align:center;'
                ],
                [
                    'data' => '<span style="cursor:pointer" data-id="'.$encId.'" class="text-red btn-del"><i  class="fa fa-trash fa-lg"></i></span>',
                    'style' => 'text-align:center;'
                ]
            );
        }
        if(!empty($this->session->userdata("ses_notif"))){
            $arrNotif = $this->session->userdata("ses_notif");
      
            $notif    = createNotif($arrNotif['type'],$arrNotif['header'],$arrNotif['msg']);
            $data['notif'] = $notif;
            $this->session->unset_userdata("ses_notif");
        }
        $data['datatable'] = $this->table->generate();

        $parentViewData = [
            "title"   => "History Leave",  // title page
            "content" => "transaction/trx_leave",  // content view
            "viewData"=> $data,
            "listMenu"=> $this->listMenu,
            'listCategory' => $data_cats,
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

        $data = $this->leave_model->leaveList($this->appid,[]);

        echo json_encode($data);
    }

    function deleteData() {
        $data = $this->input->post('id');

        $arr_id = [];

        foreach($data as $item) {
            $this->load->library("encryption_org");
            $delId = $this->encryption_org->decode($item);

            array_push($arr_id, $delId);
        }

        $del = $this->leave_model->delTrxLeave($arr_id);

        $this->session->set_userdata('ses_notif',['type'=>'success','header'=>'Success','msg'=> $this->gtrans->line('Success delete data')]);

        $response = [
            'meta' => [
                'code' => 200,
            ]
        ];

        echo json_encode($response);
    }
}