<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Holidays extends CI_Controller
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
        // bahasa
    }

    function index() {

        // $curl = curl_init();

		// curl_setopt_array($curl, array(
		// 	CURLOPT_URL => 'https://dayoffapi.vercel.app/api?year=2017',
		// 	CURLOPT_RETURNTRANSFER => true,
		// 	CURLOPT_ENCODING => '',
		// 	CURLOPT_MAXREDIRS => 10,
		// 	CURLOPT_TIMEOUT => 0,
		// 	CURLOPT_FOLLOWLOCATION => true,
		// 	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		// 	CURLOPT_CUSTOMREQUEST => 'GET',
		// 	CURLOPT_HTTPHEADER => array(
		// 		'Content-Type: application/x-www-form-urlencoded'
		// 	),
		// ));

		// $response = json_decode(curl_exec($curl));
        // curl_close($curl);

        // $Arr = [];

        // foreach($response as $key => $items) {
        //     $dt = [
        //         'start_time' => (new DateTime($items->tanggal))->format('Y-m-d'),
        //         'name' => $items->keterangan,
        //         'holiday_type' => $items->is_cuti ? 'Cuti' : 'Libur Nasional',
        //         'created_at' => (new DateTime())->format('Y-m-d H:i:s')
        //     ];

        //     array_push($Arr, $dt);
        // }

        // $add = $this->schedule_model->SaveDataHoliday($Arr);
        // $data = [];
        if(!empty($this->session->userdata("ses_notif"))){
            $arrNotif = $this->session->userdata("ses_notif");
      
            $notif    = createNotif($arrNotif['type'],$arrNotif['header'],$arrNotif['msg']);
            $data['notif'] = $notif;
            $this->session->unset_userdata("ses_notif");
        }

        $list_hoilday = $this->schedule_model->HolidayList();
        $parentViewData = [
            "title"   => "Holidays",  // title page
            "content" => "schedule/holiday",  // content view
            "viewData"=> $data,
            "listMenu"=> $this->listMenu,
            "holidays" => json_encode($list_hoilday),
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

    function saveData() {
        $start_date = new DateTime($this->input->post('start_date'));
        $end_date = new DateTime($this->input->post('end_date'));
        $diff = $start_date->diff($end_date);

        

        $data = [];

        for($n = 0; $n <= $diff->days; $n++) {
            print_r($n); 
            if ($n == 0) {
                $dt = [
                    'name' => $this->input->post('holiday_name'),
                    'start_time' => $start_date->format('Y-m-d'),
                    'holiday_type' => 'Cuti',
                    'created_at' => (new DateTime())->format('Y-m-d H:i:s')
                ];

                array_push($data, $dt);
            } else {
                $dt = [
                    'name' => $this->input->post('holiday_name'),
                    'start_time' => $start_date->modify('1 day')->format('Y-m-d'),
                    'holiday_type' => 'Cuti',
                    'created_at' => (new DateTime())->format('Y-m-d H:i:s')
                ];

                array_push($data, $dt);
            }
        }
        $ins = $this->schedule_model->SaveDataHoliday($data);

        if ($ins) {
            $this->session->set_userdata('ses_notif',['type'=>'success','header'=>'Success','msg'=> $this->gtrans->line('Success add data')]);
            setActivity("master holiday","add");
        } else {
            $this->session->set_userdata('ses_notif',['type'=>'danger','header'=>'Failed','msg'=> $this->gtrans->line('Fail to add data')]);
        }
        return redirect("schedule-holidays");

    }
}