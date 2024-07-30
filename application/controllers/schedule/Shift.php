<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Shift extends CI_Controller
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

        $shift = $this->schedule_model->listShift($this->appid);
        
        foreach($shift as $key => $items) {
            $encId = $this->encryption_org->encode($items->id);
            $workday = '';
            if ($items->unit == 0) {
                $workday = 'Hari';
            } elseif ($items->unit == 1) {
                $workday = 'Minggu';
            } else {
                $workday = 'Bulan';
            }
            
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
                    'data' => (new DateTime($items->start_date))->format('Y-m-d'),
                    'style' => 'text-align:left'
                ],
                [
                    'data' => $items->cyle,
                    'style' => 'text-align:center'
                ],
                [
                    'data' => $workday,
                    'style' => 'text-align:center'
                ],
                [
                    'data' => '<span class="priview-calendar" style="color: #039be6; cursor: pointer;"  data-id="'.$encId.'"><i class="fa fa-calendar"></i> Preview Calendar</span>',
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

        $listHour = $this->schedule_model->listHour($this->appid);

        $parentViewData = [
            "title"   => "Shift Schedule",  // title page
            "content" => "schedule/shift",  // content view
            "viewData"=> $data,
            "listHour" => $listHour,
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

    function submitShift() {
        $data = $this->input->post('data');

        $params = json_decode($data);

        $data_numrun = [
            'appid' => $this->appid,
            'name' => $params->shift->name,
            'start_date' => $params->shift->effective_start_date,
            'end_date' => $params->shift->effective_end_date,
            'cyle' => $params->shift->rotation_number,
            'unit' => $params->shift->rotation_unit,
            'national_holiday' => $params->shift->national_holiday,
            'created_at' => (new DateTime())->format('Y-m-d H:i:s')
        ];

        $data_numrun_deil = [
            'schclass' => $params->detail->schclass,
            'day' => $params->detail->day
        ];

        $ins = $this->schedule_model->saveShift($data_numrun, $data_numrun_deil);
        setActivity("schedule shift schedule","add");
        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'Success add data' 
            ]
        ];
        echo json_encode($response);
        die;
    }

    function delData($encId) {
        $this->load->library("encryption_org");
        $idx = $this->encryption_org->decode($encId);
        
        $upt = $this->schedule_model->deleteShift($idx, [
            'is_delete' => 1
        ]);

        if ($upt) {
            $this->session->set_userdata('ses_notif',['type' => 'success', 'title' => 'Success', 'msg'=> $this->gtrans->line('Delete data has success full')]);
            setActivity("schedule shift schedule","delete");
        } 

        return redirect("schedule-shift");
    }

    function getDateByDayNumber($day) {
        
        if ($day < 1 || $day > 31) {
            return "Nomor hari tidak valid. Harus antara 1 dan 31.";
        }

        // Mendapatkan bulan dan tahun saat ini
        $month = date('m');
        $year = date('Y');

        // Menghasilkan tanggal
        $dateString = "$year-$month-$day";

        // Memeriksa apakah tanggal tersebut valid
        if (!checkdate($month, $day, $year)) {
            return "Tanggal tidak valid untuk bulan ini.";
        }

        return $dateString;
    }

    function priviewCalendar($encId) {
        $this->load->library("encryption_org");
        $idx = $this->encryption_org->decode($encId);
     
        $data = $this->schedule_model->getDateByDayNumber($idx);
        
        $dateByNumber = [];
        foreach($data as $item) {
            $sdays = $item->sdays < 10 ? '0'.$item->sdays : $item->sdays;
            $dt = $this->getDateByDayNumber($sdays);

            $obj = [
                'id' => $item->id,
                'title' => $item->schname,
                'start' => $dt,
                'color' => $item->color
            ];

            array_push($dateByNumber, $obj);
        }

        $parentViewData = [
            "title"   => "Shift Schedule",  // title page
            "content" => "schedule/shift_calendar",  // content view
            "viewData"=> [],
            "dataCalendar"=> json_encode($dateByNumber),
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

    function detailData() {
        $encId = $this->input->get('id');

        $this->load->library("encryption_org");
        $idx = $this->encryption_org->decode($encId);

        $data = $this->schedule_model->getNumRunById($idx);
        $deil = $this->schedule_model->countNumRunDeilByNumRunId($idx);
        $day_deil = $this->schedule_model->dayNumRunDeil($idx);
        
        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'Success get data'
            ],
            'deil' => $deil,
            'data' => $data[0],
            'day_deil' => $day_deil,
        ];

        echo json_encode($response);
    }

    function updateData() {
        $data = $this->input->post('data');

        $params = json_decode($data);

        // delete data numrundeil by numrunid
        $upt = $this->schedule_model->updateDataShift($params);

        setActivity("schedule shift schedule","edit");
        
        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'Success update data'
            ],
        ];

        echo json_encode($response);
    }
}