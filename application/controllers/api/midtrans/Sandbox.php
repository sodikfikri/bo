<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
class Sandbox extends REST_Controller{

    function __construct()
    {
        parent::__construct();
        $this->now = date("Y-m-d H:i:s");
    }

    function pay_post(){
        $data = file_get_contents("php://input");

        $curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://app.sandbox.midtrans.com/snap/v1/transactions',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => $data,
		  CURLOPT_HTTPHEADER => array(
		    'Authorization: Basic U0ItTWlkLXNlcnZlci1FaXRhOTZDY19xbFdPZm1jMzV2Q0kxQWk=',
		    'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		echo $response;
    }
}