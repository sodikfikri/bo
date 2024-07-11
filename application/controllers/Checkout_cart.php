<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 *
 */
class Checkout_cart extends CI_Controller
{
  var $listMenu = "";
  var $tabel_template  = array(
        'table_open'            => '<table class="table table-bordered table-stripped" id="datatable">',
        'table_close'           => '</table>'
	);

  function __construct()
  {
    parent::__construct();
    $this->load->model("system_model");
    $this->timestamp = date("Y-m-d H:i:s");

    $languange = !empty($this->session->userdata('lang')) ? $this->session->userdata('lang') :"en";
    $this->load->library("gtrans/gtrans",["lang" => $languange]);

    $this->load->model("menu_model");
    $this->load->model("employee_model");
    $this->load->model("institution_model");
    $this->system_model->checkSession(100);
    $this->listMenu = $this->menu_model->list_menu();
  }

  function index(){
    $this->load->model("area_model");
	$this->load->model("cabang_model");
    $this->load->helper("form");
    $this->load->helper("timezone");
    $this->load->library("encryption_org");
	$encData = $this->encryption_org->decode($this->uri->segment(2));
	$arrData = explode("|",$encData);
    $data['codeLicense']     = $arrData[0];
	$cabang = $this->cabang_model->getById($arrData[0]);
    $data['ctLicense']     = $arrData[1];
    $data['lastID']     = $arrData[2];
    $data['cabangName']     = $cabang->cabang_name;
    $parentViewData = [
      "title"   => $this->gtrans->line("Checkout License"),  // title page
      "content" => "checkout_cart",  // content view
      "viewData"=> $data,
      "listMenu"=> $this->listMenu,
      "externalCSS" => [
        base_url("asset/template/bower_components/select2/dist/css/select2.min.css"),
        base_url("asset/template/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css")
      ],
      "externalJS" => [
        base_url("asset/template/bower_components/datatables.net/js/jquery.dataTables.min.js"),
        base_url("asset/template/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js"),
        "https://cdn.jsdelivr.net/npm/sweetalert2@8",
        base_url("asset/template/bower_components/select2/dist/js/select2.full.min.js"),
        base_url("asset/js/checkCode.js")
      ],
      "varJS" => ["url" => base_url()]
    ];
    $this->load->view("layouts/main",$parentViewData);
    $this->gtrans->saveNewWords();
  }
  
  public function generateQris()
  {

    $this->load->library("encryption_org");
    $enc_subs_id = $this->input->post("subscription");
    $price = $this->input->post("price");
    $this->load->model("checkout_cart_model");
    $subs_id     = $this->encryption_org->decode($enc_subs_id);

    $amount = 0;
    $amount += remove_decimal($price);
    $amount = $this->session->userdata("ses_appid") == "IA01M6858F20210906256" ? 100 : $amount;
	// print_r($amount); die;
	$activeOrder = $this->checkout_cart_model->getActiveOrder($subs_id);
	if($activeOrder==false){
		$data = array(
		  "mID" => 'FM2404040012'
		  //"mID" => '195255996627'
		);
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://m.qris.online/restapi/backend/v2/generate-auth.php",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => json_encode($data),
		  CURLOPT_HTTPHEADER => array(
			'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);
		curl_close($curl);
		$response = json_decode($response, TRUE);
		$headerQris = $response['token'];
		
		$dataGen = array(
		  "gtotal" => $amount,
		  "info" => 'Pembelian License InAct BKD',
		  "disableTip" => '1'
		);
		
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://m.qris.online/restapi/backend/v2.1/generate-qris-dinamis.php',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => json_encode($dataGen),
			CURLOPT_HTTPHEADER => array(
				'Authorization: Bearer '.$headerQris,
				'Content-Type: application/json'
			),
		));
		$responseGenerate = curl_exec($curl);
		$resGen = json_decode($responseGenerate, TRUE);
		$content = $resGen['data']['content'];
		$invoiceId = $resGen['data']['invoiceId'];
		$createDate = $resGen['data']['timeStamp'];
		$expiredDate = $resGen['data']['expiredDate'];
		$refNo = $resGen['data']['refNo'];
		
		//update order payment
		$data_update  = [
		  "datecartclose"     => $createDate,
		  "qris_content"      => $content,
		  "qris_invoiceid"    => $invoiceId,
		  "qris_noref"		  => $refNo,
		  "qris_date_create"  => $createDate,
		  "qris_date_expire"  => $expiredDate
		];
		$this->checkout_cart_model->updateOrder($subs_id,$data_update);
	} else {
		foreach ($activeOrder as $index => $row) {
			$content = $row->qris_content;
			$invoiceId = $row->qris_invoiceid;
			$createDate = $row->qris_date_create;
			$expiredDate = $row->qris_date_expire;
			$refNo = $row->qris_noref;
		}
	}
	
    $res = array(
      'qris_content' => $content,
      'qris_invoiceid' => $invoiceId,
      'qris_date_create' => $createDate,
      'qris_date_expired' => date_format(date_create($expiredDate),"d F Y H:i:s"),
      'qris_refno' => $refNo
    );

    echo json_encode($res);
  }
  
  public function checkQris()
  {

    $this->load->library("encryption_org");
    $this->load->model("checkout_cart_model");
    $invoiceId = $this->input->post("invoiceId");
    $refNo = $this->input->post("refNo");
	
    $data = array(
      "mID" => 'FM2404040012',
      "invoiceId" => $invoiceId,
      "refNo" => $refNo
    );

    $curl = curl_init();
    curl_setopt_array($curl, array(
      CURLOPT_URL => "https://m.qris.online/restapi/backend/v2/check-transaction-qris1.php",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => json_encode($data),
      CURLOPT_HTTPHEADER => array(
		'Content-Type: application/json'
	  ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    $response = json_decode($response, TRUE);
    $res = array(
      'statusCheck' => $response['status']
    );
	if($response['status'] == 'success'){
		//update order payment active
		$data_update  = [
		  "datestart"     	=> $this->timestamp,
		  "dateend"     	=> date("Y-m-d H:i:s", strtotime($this->timestamp . "+365 days")),
		  "status"     		=> "paid",
		  "payment_status"  => "success",
		  "cart_status"		=> "close"
		];
		$this->checkout_cart_model->activeOrder($invoiceId,$data_update);
		
		$arrDataOrder = $this->checkout_cart_model->getDetailOrder($invoiceId);
		
		$token = md5("InterActiveAPI".date('Ymd'));
		$datane = array(
		  "kode_transaksi"		=> $invoiceId,
		  "nama_lembaga" 		=> $arrDataOrder->cabang_name,
		  "pic" 				=> $arrDataOrder->user_fullname,
		  "email" 				=> $arrDataOrder->user_emailaddr,
		  "jumlah_lisensi" 		=> $arrDataOrder->license_count,
		  "harga_per_lisensi" 	=> $arrDataOrder->price,
		  "total_transaksi" 	=> $arrDataOrder->gtotal,
		  "jumlah_bayar" 		=> $arrDataOrder->gtotal,
		  "info_pembayaran" 	=> "Pembelian lisensi sejumlah ".$arrDataOrder->license_count,
		  "appid" 				=> $this->session->userdata("ses_appid")
		);
		
		$data = array(
		  "data_token"	=> $token,
		  "datane" 		=> json_encode($datane)
		);

		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => "https://interactive.co.id/_iboss/restapi/mybilling_transpj.php",
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => "",
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "POST",
		  CURLOPT_POSTFIELDS => http_build_query($data),
		  CURLOPT_HTTPHEADER => array(
			'Content-Type: application/x-www-form-urlencoded'
		  ),
		));
		
		$response = curl_exec($curl);
		curl_close($curl);
	}
    echo json_encode($res);
  }
}
