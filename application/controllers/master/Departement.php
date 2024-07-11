<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Departement extends CI_Controller
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
        $this->load->model("departement_model");
        
        $this->system_model->checkSession(15);
        $this->listMenu = $this->menu_model->list_menu();
        $this->now = date("Y-m-d H:i:s");
        $this->appid = $this->session->userdata("ses_appid");
        $this->load->library('upload');
        // bahasa
    }

    private function buildHierarchy($data, $parentId = '0') {
		$result = [];

		foreach ($data as $key => $item) {
			// return $item['parent'];
			if ($item['parent'] === $parentId) {
				$children = $this->buildHierarchy($data, $item['id']);
				if (!empty($children)) {
					$item['children'] = $children;
				}
				unset($item['parent']); // Menghapus 'parent' dari hasil akhir
				$result[] = $item;
			}
		}

		return $result;
	}

    function index() {
        $departement = $this->departement_model->list_departement($this->appid);
		$hierarchy = $this->buildHierarchy($departement);
        $data['departement'] = $hierarchy;

        if(!empty($this->session->userdata("ses_notif"))){
            $arrNotif = $this->session->userdata("ses_notif");
      
            $notif    = createNotif($arrNotif['type'],$arrNotif['header'],$arrNotif['msg']);
            $data['notif'] = $notif;
            $this->session->unset_userdata("ses_notif");
        }
        
        $parentViewData = [
            "title"   => "Leave Categories",  // title page
            "content" => "master/departement",  // content view
            "viewData"=> $data,
            "listMenu"=> $this->listMenu,
            "varJS" => ["url" => base_url()],
            "externalCSS" => [
                base_url("asset/template/bower_components/select2/dist/css/select2.min.css"),
                base_url("asset/template/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css")
            ],
            "externalJS" => [
                base_url("asset/template/bower_components/datatables.net/js/jquery.dataTables.min.js"),
                base_url("asset/template/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"),
                "https://cdn.jsdelivr.net/npm/sweetalert2@8",
                base_url("asset/template/bower_components/select2/dist/js/select2.full.min.js"),
                base_url("asset/js/checkCode.js"),
                base_url("asset/js/user.js")
            ],
            "varJS" => ["url" => base_url()]
        ];
        $this->load->view("layouts/main",$parentViewData);
        $this->gtrans->saveNewWords();
    }

    function getParent() {
        $departement = $this->departement_model->list_departement($this->appid);

        if (count($departement) == 0) {
            $response = [
                'meta' => [
                    'code' => 404,
                    'message' => 'Data not found!'
                ],
                'data' => $departement
            ];

            echo json_encode($response);
            return;
        }

        $response = [
            'meta' => [
                'code' => 200,
                'message' => 'Success get data'
            ],
            'data' => $departement
        ];

        echo json_encode($response);
    }

    function saveDepartement() {
        $id = $this->input->post('id');
        $parent = $this->input->post('parent');
        $name = $this->input->post('name');
        $label = $this->input->post('label');

        $params = [
            'appid' => $this->appid,
            'name' => $name,
            'parent' => $parent,
            'label' => $label
        ];

        if ($id == 0) {
            $params['created_at'] = (new DateTime())->format('Y-m-d H:i:s');

            $ins = $this->departement_model->saveData($params);
            if ($ins) {
                $this->session->set_userdata('ses_notif',['type'=>'success','header'=>'Success','msg'=> $this->gtrans->line('Success add departement')]);
                setActivity("master departement","add");
            } else {
                $this->session->set_userdata('ses_notif',['type'=>'danger','header'=>'Failed','msg'=> $this->gtrans->line('Filed to add departement')]);
            }
        } else {
            $params['updated_at'] = (new DateTime())->format('Y-m-d H:i:s');
            
            $upt = $this->departement_model->updateData($id, $params);
            if ($upt) {
                $this->session->set_userdata('ses_notif',['type'=>'success','header'=>'Success','msg'=> $this->gtrans->line('Success update departement')]);
                setActivity("master departement","update");
            } else {
                $this->session->set_userdata('ses_notif',['type'=>'danger','header'=>'Failed','msg'=> $this->gtrans->line('Filed to update departement')]);
            }
        }

        redirect("departement");
    }

    function detailData() {
        $id = $this->input->get('id');

        $data = $this->departement_model->getDetail($id);
        // print_r($data[0]['id']); return;
        $this->load->library("encryption_org");
        $data[0]['id'] = $this->encryption_org->encode($data[0]['id']);

        echo json_encode([
            'meta' => [
                'code' => 200,
                'message' => 'success get data'
            ],
            'data' => $data
        ]);
        return;
    }

    function deleteData($encId) {
        $this->load->library("encryption_org");
        $id = $this->encryption_org->decode($encId);

        $validate = $this->departement_model->validateDelete($id);
        
        if (count($validate) != 0) {
            # code...
            $this->session->set_userdata('ses_notif',['type'=>'error','header'=>'Failed','msg'=> $this->gtrans->line('Parent has been used')]);
            redirect("departement");
            return;
        }

        $del = $this->departement_model->updateData($id, ['is_delete' => 0]);

        $this->session->set_userdata('ses_notif',['type'=>'success','header'=>'Success','msg'=> $this->gtrans->line('Success delete departement')]);
        setActivity("master departement","delete");

        redirect("departement");
    }

}