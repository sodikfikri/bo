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

    public $prefix = 'https://inact.azurewebsites.net';
    public $response = [];

    function __construct()
    {
        parent::__construct();
        $this->now = date("Y-m-d H:i:s");
        $this->load->model("ijin_model");
        $this->load->model("leave_model");
        $this->load->library('upload');

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
       
        $config['upload_path']          = './sys_upload/leave/doc/';
        $config['allowed_types']        = 'gif|jpg|jpeg|png|pdf';
        $config['file_name']            = uniqid();
        $config['overwrite']            = true;

        $this->upload->initialize($config);

        $headers = getRequestHeaders();
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
        
        if (!empty($_FILES['doc']['name'])) {
            if (!$this->upload->do_upload('doc')) {
                $response = $this->SetRespose(400, 'Fail to upload document', []);
                header("Content-Type:application/json");
                echo json_encode($response); return;
            }
            $uploaded_data = $this->upload->data();
            $params['doc_path'] = $config['upload_path'].$uploaded_data['file_name'];
            $params['doc_name'] = $uploaded_data['file_name'];
        }

        $add = $this->leave_model->addLeave($params, $this->input->post('appid'), $this->input->post('employee_id'));
        
        $response = $this->SetRespose(200, 'Success submit leave', []);
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
        $this->load->model("schedule_model");

        $data = $this->schedule_model->getDataUserUsedClass();

        $response = $this->SetRespose(200, 'Success get data', $data);
        header("Content-Type:application/json");
        echo json_encode($response); return;
        
    }

}