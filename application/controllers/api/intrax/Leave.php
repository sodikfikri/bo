<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
/**
 *
 */
class Leave extends REST_Controller
{
    var $now;
    var $apikey = "IAdev-apikey3fapikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9ed48151b389apikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fapikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9e7b7c2f8d891dfc9b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9";

    public $prefix = 'https://f877-36-90-57-45.prefix-free.app/inact/bo';
    public $response = [];

    function __construct()
    {
        parent::__construct();
        $this->now = date("Y-m-d H:i:s");
        $this->load->model("ijin_model");
        $this->load->model("leave_model");

        $this->response = [
            'meta' => [
                'code' => '',
                'message' => ''
            ]
        ];
    }

    public function SetRespose($code, $message, $data = '') {
        $this->response['meta']['code'] = $code;
        $this->response['meta']['message'] = $message;
        if ($data) {
            $this->response['data'] = $data;
        }

        return $this->response;
    }

    function List_category_get() {
        $headers = getRequestHeaders();
        // $data = json_decode(file_get_contents('php://input'), true);
        $apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;
        $appid  = !empty($this->input->get('appid')) ? $this->input->get('appid') : "";

        if(empty($apikey)){
            $response = $this->SetRespose(400, 'Token not provide');
            header("Content-Type:application/json");
            echo json_encode($response); return;
        }
        
        $leave_data = $this->leave_model->getcategoryList($appid);

        foreach($leave_data as $items) {
            $items->icon = $this->prefix . ltrim($items->icon, '.');
        }
        if (count($leave_data) == 0) {
            $response = $this->SetRespose(404, 'Data not found!');
            header("Content-Type:application/json");
            echo json_encode($response); return;
        }
        $response = $this->SetRespose(200, 'Success get data', $leave_data);
        header("Content-Type:application/json");
        echo json_encode($response); return;
    }

    function Submit_leave_post() {
        // $this->load->library("encryption_org");
        // $catsid = $this->encryption_org->decode('QmlBbDRzNzcvbTR0TFFJWjU0L29Ddz09');
        // print_r($catsid); return;
        $headers = getRequestHeaders();
        // $data = json_decode(file_get_contents('php://input'), true);
        $apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;

        if(empty($apikey)){
            $response = $this->SetRespose(400, 'Token not provide');
            header("Content-Type:application/json");
            echo json_encode($response); return;
        }

        $params = [
            'category_id' => $this->input->post('category_id'),
            'created_at' => (new DateTime())->format('Y-m-d H:i:s'),
            'appid' => $this->input->post('appid'),
            'employee_id' => $this->input->post('employee_id')
        ];

        if (!$this->input->post('appid') || !$this->input->post('category_id') || !$this->input->post('employee_id') || !$this->input->post('start_date') || !$this->input->post('end_date')) {
            $response = $this->SetRespose(400, 'incomplete parameters');
            header("Content-Type:application/json");
            echo json_encode($response); return;
        }

        $params['start_date'] = $this->input->post('start_date');
        $params['end_date'] = $this->input->post('end_date');
        $params['start_time'] = $this->input->post('start_time');
        $params['end_time'] = $this->input->post('end_time');
        
        $params['reason'] = $this->input->post('reason');
        if ($this->input->post('doc')) {
            $doc = $this->input->post('doc');

            $decode = base64_decode($doc);
            $doc_name = uniqid().'.'.$this->input->post('doc_extension');
            $file = "./sys_upload/leave/doc/".$doc_name;
            $success = file_put_contents($file, $decode);

            $params['doc_name'] = $doc_name;
            $params['doc_path'] = $file;
        }

        $add = $this->leave_model->addLeave($params, $this->input->post('appid'), $this->input->post('employee_id'));
        
        $response = $this->SetRespose(200, 'Success submit leave', $data);
        header("Content-Type:application/json");
        echo json_encode($response); return;
        
    }

    function CountLeaveByEmployee_get() {
        $headers = getRequestHeaders();
        $apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;

        if(empty($apikey)){
            $response = $this->SetRespose(400, 'Token not provide');
            header("Content-Type:application/json");
            echo json_encode($response); return;
        }
        $appid = $this->input->get('appid');
        $employee_id = $this->input->get('employee_id');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        $data = $this->leave_model->countLeave($appid, $employee_id, $start_date, $end_date);
        
        if ($data[0]->total == 0) {
            $response = $this->SetRespose(404, 'Data not found', []);
            header("Content-Type:application/json");
            echo json_encode($response); return;
        }
        $response = $this->SetRespose(200, 'Success get data', $data[0]);
        header("Content-Type:application/json");
        echo json_encode($response); return;;
    }

    function ListLeave_get() {
        $headers = getRequestHeaders();
        $apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;

        if(empty($apikey)){
            $response = $this->SetRespose(400, 'Token not provide');
            header("Content-Type:application/json");
            echo json_encode($response); return;
        }

        $appid = $this->input->get('appid');
        $employee_id = $this->input->get('employee_id');
        $leave_id = $this->input->get('leave_id');
        $start_date = $this->input->get('start_date');
        $end_date = $this->input->get('end_date');

        $list = $this->leave_model->leaveListMobile($appid, $employee_id, $leave_id, $start_date, $end_date);

        if (count($list) == 0) {
            $response = $this->SetRespose(404, 'Data not found', $data);
            header("Content-Type:application/json");
            echo json_encode($response); return;
        }

        $data = [];
        foreach($list as $items) {
            $obj = [
                "id" => $items->id,
                "employee_full_name" => $items->employee_full_name,
                "category_name" => $items->category_name,
                "start_date" => $items->start_date,
                "start_time" => $items->start_time,
                "end_date" => $items->end_date,
                "end_time" => $items->end_time,
                "reason" => $items->reason,
                "doc" => $items->doc_path ? $this->prefix . ltrim($items->doc_path, '.') : '',
                "created_at" => $items->created_at
            ];

            array_push($data, $obj);
        }

        $response = $this->SetRespose(200, 'Success get data', $data);
        header("Content-Type:application/json");
        echo json_encode($response); return;
    }

    function PeriodLeave_get() {
        $headers = getRequestHeaders();
        $apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;

        if(empty($apikey)){
            $response = $this->SetRespose(400, 'Token not provide');
            header("Content-Type:application/json");
            echo json_encode($response); return;
        }

        $data = $this->leave_model->activePeriode($this->input->get('appid'));

        if (count($data) == 0) {
            $response = $this->SetRespose(404, 'Data not found', $data);
            header("Content-Type:application/json");
            echo json_encode($response); return;
        }

        $start = $data[0]->start_date < 10 ? '0'.$data[0]->start_date : $data[0]->start_date;
        $end = $data[0]->end_date < 10 ? '0'.$data[0]->end_date : $data[0]->end_date;

        $start_date = (new DateTime())->format('Y') . '-' . (new DateTime())->format('m') . '-' . $start;
        
        if ($data[0]->start_date == 1) {
            $end_date = (new DateTime())->format('Y') . '-' . (new DateTime())->format('m') . '-' . $end;
        } else {
            $end_date = (new DateTime())->modify('+1 year')->format('Y') . '-' . (new DateTime())->modify('+1 month')->format('m') . '-' . $end;
        }


        $period = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'status' => $data[0]->is_active == 1 ? 'Active' : 'Inactive'
        ];

        $response = $this->SetRespose(200, 'Success get data', $period);
        header("Content-Type:application/json");
        echo json_encode($response); return;
    }

    function apiTest_post() {
        $body_msg = '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
        <html>
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        </head>
        <style type="text/css" data-hse-inline-css="true">
            @media only screen and (max-width: 385px) {
            .main-page{
            background-color:#dfdfdf;
            padding:5px;"
            }
            @media only screen and (min-width: 386px) {
            .main-page{
            background-color:#dfdfdf;
            padding:40px;"
            }
        </style>
        <body style="font-family: \'Roboto\', sans-serif;">
            <div class="main-page"  >
            <img src="https://inact.interactiveholic.net/bo/asset/images/Logo_inact.png" style="width: 200px;">
            <div style="max-width:653px; background-color:#ffffff;margin-left:auto; margin-right:auto; margin-top:40px; margin-bottom:40px;" >
                <div style="vertical-align: middle; padding:30px 30px 30px 30px;">
                <center style="padding-bottom:30px"></center>
                    <hr style="height: 1px;color: #dee0e3;background-color: #dee0e3;border: none;">
                    <p style="font-family: Roboto;
                        font-size: 24px;
                        font-weight: bold;
                        font-style: normal;
                        font-stretch: normal;
                        line-height: 1.17;
                        letter-spacing: normal;
                        text-align: left;
                        color: rgba(0, 0, 0, 0.7);
                        ">
                        Hello Sodik,</p>
                        <p style="font-family: Roboto;
                        font-size: 15px;
                        font-weight: normal;
                        font-style: normal;
                        font-stretch: normal;
                        line-height: 1.67;
                        letter-spacing: normal;
                        text-align: left;
                        color: rgba(0, 0, 0, 0.7);
                        ">
                        You are doing login on INTRAX123534534522 app. 
                        Here is the One-Time Password that must be entered</p>
                        <p style="font-family: Roboto;
                        font-size: 20px;
                        font-weight: normal;
                        font-style: normal;
                        font-stretch: normal;
                        line-height: 1.67;
                        letter-spacing: normal;
                        text-align: left;
                        color: rgba(0, 0, 0, 0.7);
                        "><strong>One Time Password: </strong>
                            112233
                        </p>
                        
                        <p style="font-family: Roboto;
                        font-size: 15px;
                        font-weight: normal;
                        font-style: normal;
                        font-stretch: normal;
                        line-height: 1.67;
                        letter-spacing: normal;
                        text-align: left;
                        color: rgba(0, 0, 0, 0.7);">
                            If you experience problems using InterActive Products, please contact our Customer Support team through  <font style="font-weight: bold;color: #00cbce;">(031) 535 5353</font> <b>Ext. 16 or <a href="https://wa.me/6285879123123" target="_blank">+62 858-79-123-123</a></b>
                        </p>
                        <br>
                        <p style="font-family: Roboto;
                        font-size: 15px;
                        font-weight: normal;
                        font-style: normal;
                        font-stretch: normal;
                        line-height: 1.67;
                        letter-spacing: normal;
                        text-align: left;
                        color: rgba(0, 0, 0, 0.7);">Greetings,</p>
                        <p style="font-family: Roboto;
                        font-size: 15px;
                        font-weight: normal;
                        font-style: normal;
                        font-stretch: normal;
                        line-height: 1.67;
                        letter-spacing: normal;
                        text-align: left;
                        color: rgba(0, 0, 0, 0.7);">Interactive Team,</p>
                    </div>
                </div>
                <center>
                    <img src="https://cloud.interactive.co.id/mybilling/asset/img/interactive.png" height="15px" />
                    <p style="font-family: Roboto;
                    font-size: 12px;
                    font-weight: 500;
                    font-style: normal;
                    font-stretch: normal;
                    line-height: 1.67;
                    letter-spacing: normal;
                    text-align: center;
                    color: rgba(0, 0, 0, 0.38);">Jl. Ambengan No. 85, Surabaya 60136, Indonesia <br>
                    @ '.date('Y').', InterActive Technologies Corp. All rights reserved.</p>
                    <p style="font-family: Roboto;
                    font-size: 12px;
                    font-weight: 500;
                    font-style: normal;
                    font-stretch: normal;
                    line-height: 1.83;
                    letter-spacing: normal;
                    text-align: center;
                    color: rgba(0, 0, 0, 0.38);"><a href="https://www.youtube.com/user/interactivecorp">Youtube</a> - <a href="https://www.instagram.com/interactive_tech/">Instagram</a> -  <a href="https://www.facebook.com/InteractiveTec/">Facebook</a> - <a href="https://www.interactive.co.id">Website</a></p>
                </center>
            </div>
        </body>
        </html>';
        // echo $body_msg;
        // return;
        $this->load->library("intermailer");
        $init = $this->intermailer->initialize_allin();
        // header("Content-Type:application/json");
        // echo json_encode($init); return;
        $this->intermailer->to(['sodikfikri.job@gmail.com'=>'sodikfikri.job@gmail.com']);
        $this->intermailer->set_content("Employee OTP",$body_msg,"Alt Body tes");
        $this->intermailer->send();

        $response = $this->SetRespose(200, 'Success send email', []);
        header("Content-Type:application/json");
        echo json_encode($response); return;
    }

}