<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Address extends REST_Controller
{
  var $now;
  var $apikey = "IAdev-apikey3fapikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9ed48151b389apikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fapikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9e7b7c2f8d891dfc9b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9";

  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
  }
  
  function getAddress_post(){
	if (!file_exists("application/controllers/api/intrax/logs/log-getAddress-".date("Y-m-d").".txt")) {
		$myfile = fopen("application/controllers/api/intrax/logs/log-getAddress-".date("Y-m-d").".txt", "a");
	} else {
		$myfile = fopen("application/controllers/api/intrax/logs/log-getAddress-".date("Y-m-d").".txt", "a");
	}
	
	$data = json_decode(file_get_contents('php://input'), true);
	$txt = "-------------------------------------------------------------\n";
	fwrite($myfile, $txt);
	$txt = "REQUEST-".date("Ymd-His")."->".json_encode($data)."\n";
	fwrite($myfile, $txt);
    $headers = getRequestHeaders();
    $apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;
	$latitude  = !empty($data['latitude']) ? $data['latitude'] : "";
	$longitude  = !empty($data['longitude']) ? $data['longitude'] : "";
    if($apikey!=""){
		if($apikey==$this->apikey){
			if($latitude!="" AND $longitude!=""){
				$latlng = $latitude.','.$longitude;
				$key = 'AIzaSyA9CGovNSzUMvQanVxrqfELtx4i_stuYBM';
				$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.$latlng.'&key='.$key;
			    $ch = curl_init($url);
				$txt = "REQUEST-".date("Ymd-His")."->".$url."\n";
				fwrite($myfile, $txt);
			    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 3000);
			    $result = curl_exec($ch);
			    $arr = json_decode($result, true);
				$txt = "REQUEST-".date("Ymd-His")."->".$arr."\n";
				fwrite($myfile, $txt);
				$arrOutput = [
					'result'		=> true,
					'error_code' 	=> "",
					'message' 		=> "Succesfully get data.",
					'data' 			=> $arr
				];					
			} else {
			   $arrOutput = [
				'result'		=> false,
				'error_code' 	=> "401",
				'message' 		=> "please fill all mandatory",
                'data' 			=> ""
			   ];
			}
		}else{
			$arrOutput = [
			  'result'		=> false,
			  'error_code' 	=> "401",
			  'message'		=> "apikey is not valid",
              'data' 		=> ""
			];
		}
    }else{
      $arrOutput = [
		'result'		=> false,
        'error_code' 	=> "401",
		'message'		=> "apikey is not defined",
        'data'	 		=> ""
      ];
    }
	header("Content-Type:application/json");
    echo json_encode($arrOutput);
	$txt = "RESPON-".date("Ymd-His")."->".json_encode($arrOutput)."\n";
	fwrite($myfile, $txt);
	$txt = "-------------------------------------------------------------\n";
	fwrite($myfile, $txt);
	fclose($myfile);
  }
}
