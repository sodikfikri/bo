<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
class Attendance extends REST_Controller{
    var $now;
    var $apikey = "IAdev-apikey3fapikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9ed48151b389apikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fapikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9e7b7c2f8d891dfc9b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9";

    function __construct()
    {
        parent::__construct();
        $this->now = date("Y-m-d H:i:s");
    }

    function postMobileAttendance_post(){
        $headers = getRequestHeaders();

        $apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;

        if($apikey!=""){
            if($apikey==$this->apikey){
                $company_id     = !empty($this->input->post("company_id")) ? $this->input->post("company_id") : null;
                $checklog_id    = !empty($this->input->post("checklog_id"))? $this->input->post("checklog_id") : null;
                $employee_id    = !empty($this->input->post("employee_id"))? $this->input->post("employee_id") : null;
                $checklog_event = !empty($this->input->post("checklog_event")) ? $this->input->post("checklog_event") : "0";
                $checklog_date  = !empty($this->input->post("checklog_date")) ? $this->input->post("checklog_date") : null;
                $time_zone  = !empty($this->input->post("time_zone")) ? $this->input->post("time_zone") : null;
                $mood  = !empty($this->input->post("mood")) ? $this->input->post("mood") : null;

                if($company_id!=null && $checklog_id!=null && $employee_id!=null && $checklog_event !=null && $checklog_date !=null){
                    $this->load->model("employee_model");
                    $employeeData = $this->employee_model->getEmployeeIntrax($company_id,$checklog_id,$employee_id);
                    if($employeeData!=false){
                        $dataInsert = [
                            "appid" => $employeeData->appid,
                            "company_id" => $company_id,
                            "employee_id" => $employee_id,
                            "checklog_id" => $checklog_id,
                            "checklog_event" => $checklog_event,
                            "checklog_date" => $checklog_date,
                            "gmt_timezone" => $time_zone,
                            "mood" => $mood
                        ];
                        $this->load->model("inoutmobile_model");
                        $result = $this->inoutmobile_model->insert($dataInsert);
                        if($result){
                            $arrOutput = [
                                'success' 		=> true,
                                'error_code' 	=> "",
                                'message' 		=> "success",
                                'data' 			=> ""
                            ];
                        }
                    }else{
                        $arrOutput = [
                            'success' 		=> false,
                            'error_code' 	=> "401",
                            'message' 		=> "Employee Data Not Found",
                            'data' 			=> ""
                        ];
                    }
                }else{
                    $arrOutput = [
                        'success' 		=> false,
                        'error_code' 	=> "401",
                        'message' 		=> "fill all mandatory parameter",
                        'data' 			=> ""
                    ];
                }
            }else{
                $arrOutput = [
                'success' 		=> false,
                'error_code' 	=> "401",
                'message' 		=> "apikey is not valid",
                'data' 			=> ""
                ];
            }
        }else{
            $arrOutput = [
                'success' 		=> false,
                'error_code' 	=> "401",
                'message' 		=> "apikey is not defined",
                'data' 			  => ""
            ];
        }
        echo output_api($arrOutput,"json");
    }

    // function ini tidak di pakai
	function pushAttEmployee($appid=null){
		$this->load->model("employee_model");
		$this->load->model("inoutmobile_model");
		if(!empty($appid)){
			$dataInout = $this->inoutmobile_model->getAllDataPush($appid);
			foreach ($dataInout as $row) {
				
			  $employeeData = $this->employee_model->getById($row->employee_id);
			  $employee_id		= $employeeData->employee_id;
			  $checklog_id		= $employeeData->employee_account_no;
			  $checklog_date	= $row->checklog_date;
			  $checklog_event	= $row->checklog_event;
			  $checklog_longitude= $row->checklog_longitude;
			  $checklog_latitude= $row->checklog_latitude;
			  
			  if($checklog_event=='CheckIn'){
				$checklog = 'in';
			  } elseif($checklog_event=='CheckOut'){
				$checklog = 'out';
			  }
			  //$url = 'http://202.67.9.84/api/absen/transaksi/save';
			  $url = 'https://apimws.malukuprov.go.id/api/absen/transaksi/save';
			  $ch = curl_init($url);
			  $data = array(
				'id_orang' => 'intrax_mobile',
				'nip' => $checklog_id,
				'tanggal' => date_format(date_create($checklog_date),"Y/m/d"),
				'jenis_absen' => 'mobile',
				'jenis_waktu_absen' => $checklog,
				'waktu' => date_format(date_create($checklog_date),"Y/m/d H:i:s"),
				'longitude' => $checklog_longitude,
				'latitude' => $checklog_latitude
			  );
			  $txt = "REQUEST-BKD-".date("Ymd-His")."->".json_encode($data)."\n";
			  fwrite($myfile, $txt);
			  $payload = json_encode($data);
			  curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
			  curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
			  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			  curl_setopt($ch, CURLOPT_TIMEOUT_MS, 3000);
			  $result = curl_exec($ch);
			  $arr = json_decode($result, true);
			  if($arr['pesan']=="Berhasil"){
				$this->inoutmobile_model->update($employee_id,$checklog_date);
			  }
			  curl_close($ch);$txt = "RESPON-BKD-".date("Ymd-His")."->".$result."\n";
			  fwrite($myfile, $txt);

			  $arrOutput = [
				'result' 		=> true,
				'message' 		=> "succesfully push data."
			  ];
			}
		} else {
			$arrOutput = [
                'result' 		=> false,
                'message' 		=> "appid is not defined"
            ];
		}
		header("Content-Type:application/json");
		echo json_encode($arrOutput);
	}

	function saveAndroidChecklog_post(){
		if (!file_exists("application/controllers/api/intrax/logs/log-saveAndroidChecklog-".date("Y-m-d").".txt")) {
			$myfile = fopen("application/controllers/api/intrax/logs/log-saveAndroidChecklog-".date("Y-m-d").".txt", "a");
		} else {
			$myfile = fopen("application/controllers/api/intrax/logs/log-saveAndroidChecklog-".date("Y-m-d").".txt", "a");
		}

		$data = json_decode(file_get_contents('php://input'), true);
		$txt = "-------------------------------------------------------------\n";
		fwrite($myfile, $txt);
		$txt = "REQUEST-".date("Ymd-His")."->".json_encode($data)."\n";
		fwrite($myfile, $txt);
		$headers = getRequestHeaders();
		$apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;
        $this->load->model("inoutmobile_model");
        $this->load->model("employee_model");

        $cekLicence = $this->inoutmobile_model->getEmployeeById($data['employee_id']);
        if (count($cekLicence) == 0) {
            $arrOutput = [
                'result' 		=> false,
                'message' 		=> "Account Not Found!",
            ];
            header("Content-Type:application/json");
            echo json_encode($arrOutput);
            return;
        }

        if ($cekLicence[0]->employee_license == 'notactive') {
            $arrOutput = [
                'result' 		=> false,
                'message' 		=> "This account's license is inactive!",
            ];
            header("Content-Type:application/json");
            echo json_encode($arrOutput);
            return;
        }

		function get_nearest_timezone($cur_lat, $cur_long, $country_code = '') {
			$timezone_ids = ($country_code) ? DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $country_code)
											: DateTimeZone::listIdentifiers();

			if($timezone_ids && is_array($timezone_ids) && isset($timezone_ids[0])) {

				$time_zone = '';
				$tz_distance = 0;

				//only one identifier?
				if (count($timezone_ids) == 1) {
					$time_zone = $timezone_ids[0];
				} else {

					foreach($timezone_ids as $timezone_id) {
						$timezone = new DateTimeZone($timezone_id);
						$location = $timezone->getLocation();
						$tz_lat   = $location['latitude'];
						$tz_long  = $location['longitude'];

						$theta    = $cur_long - $tz_long;
						$distance = (sin(deg2rad($cur_lat)) * sin(deg2rad($tz_lat)))
						+ (cos(deg2rad($cur_lat)) * cos(deg2rad($tz_lat)) * cos(deg2rad($theta)));
						$distance = acos($distance);
						$distance = abs(rad2deg($distance));
						// echo '<br />'.$timezone_id.' '.$distance;

						if (!$time_zone || $tz_distance > $distance) {
							$time_zone   = $timezone_id;
							$tz_distance = $distance;
						}

					}
				}
				return  $time_zone;
			}
			return 'unknown';
		}
		
        if($apikey!=""){
            if($apikey==$this->apikey){
                $machine_id     = !empty($data['machine_id']) ? $data['machine_id'] : "-9";
                $company_id     = !empty($data['company_id']) ? $data['company_id'] : null;
                $checklog_id    = !empty($data['checklog_id2'])? $data['checklog_id2'] : null;
                $employee_id    = !empty($data['employee_id'])? $data['employee_id'] : null;
                $checklog_event = !empty($data['checklog_event']) ? $data['checklog_event'] : "0";
                $checklog_date  = !empty($data['checklog_timestamp']) ? $data['checklog_timestamp'] : null;
                $gmt_timezone  = !empty($data['time_zone']) ? $data['time_zone'] : null;
                $checklog_latitude  = !empty($data['checklog_latitude']) ? $data['checklog_latitude'] : null;
                $checklog_longitude  = !empty($data['checklog_longitude']) ? $data['checklog_longitude'] : null;
                $checklog_timezone  = get_nearest_timezone($checklog_latitude,$checklog_longitude);
                $image  = !empty($data['image']) ? $data['image'] : null;
                $address  = !empty($data['address']) ? $data['address'] : null;
                $mood  = !empty($data['mood']) ? $data['mood'] : null;
                $checklog_from  = !empty($data['checklog_from'])? $data['checklog_from'] : "INTRAX_2";
                $rowStatus  = "real";
				$arrData = [];
				//date_default_timezone_set($checklog_timezone);
				//$new_time = date("Y-m-d H:i:s");
				$strTimeChecklog = strtotime($checklog_date);
				//$strTimeNew = strtotime($new_time);
				//$diff = $strTimeNew - $strTimeChecklog;
				//$jam = floor($diff / (60 * 60));
				//if($jam!=0 AND strpos($address, '[Offline]') === FALSE){
				//	$checklog_date = $new_time;
				//	$rowStatus = "fake";
				//}


				$checkDataAbsen = $this->inoutmobile_model->checkDataInOutMobile($checklog_date,$employee_id,$checklog_event);
				if($checkDataAbsen==0){
					if($company_id!=null && $checklog_id!=null && $employee_id!=null && $checklog_event !=null && $checklog_date !=null && $address !=null){
                    $employeeData = $this->employee_model->getEmployeeIntrax($company_id,$checklog_id,$employee_id);
						if($employeeData!=false){
							if(!empty($data['image'])){
								$img = $data['image'];
								$img = str_replace('data:image/png;base64,', '', $img);
								$img = str_replace(' ', '+', $img);
								$data = base64_decode($img);
								$file = "./sys_upload/absen_image/".uniqid().'.png';
								$success = file_put_contents($file, $data);
								$photo = substr($file,25);
								$dataInsert = [
									"appid" => $employeeData->appid,
									"company_id" => strtoupper($company_id),
									"employee_id" => $employee_id,
									"checklog_id" => $machine_id,
									"checklog_event" => $checklog_event,
									"checklog_date" => $checklog_date,
									"checklog_latitude" => $checklog_latitude,
									"checklog_longitude" => $checklog_longitude,
									"checklog_from" => $checklog_from,
									"checklog_address" => $address,
									"image" => $photo,
									"rowStatus" => $rowStatus,
									"checklog_timezone" => $checklog_timezone,
									"gmt_timezone" => $gmt_timezone,
									"mood" => $mood
								];
							}else{
								$dataInsert = [
									"appid" => $employeeData->appid,
									"company_id" => strtoupper($company_id),
									"employee_id" => $employee_id,
									"checklog_id" => $machine_id,
									"checklog_event" => $checklog_event,
									"checklog_date" => $checklog_date,
									"checklog_latitude" => $checklog_latitude,
									"checklog_longitude" => $checklog_longitude,
									"checklog_from" => $checklog_from,
									"checklog_address" => $address,
									"rowStatus" => $rowStatus,
									"checklog_timezone" => $checklog_timezone,
									"gmt_timezone" => $gmt_timezone,
									"mood" => $mood
								];
							}

							$result = $this->inoutmobile_model->insert($dataInsert);
							if($result){
								/*if($checklog_event=='CheckIn'){
									$checklog = 'in';
								} elseif($checklog_event=='CheckOut'){
									$checklog = 'out';
								}
								//$url = 'http://202.67.9.84/api/absen/transaksi/save';
								$url = 'https://apimws.malukuprov.go.id/api/absen/transaksi/save';
								$ch = curl_init($url);
								$data = array(
									'id_orang' => 'intrax_mobile',
									'nip' => $checklog_id,
									'tanggal' => date_format(date_create($checklog_date),"Y/m/d"),
									'jenis_absen' => 'mobile',
									'jenis_waktu_absen' => $checklog,
									'waktu' => date_format(date_create($checklog_date),"Y/m/d H:i:s"),
									'longitude' => $checklog_longitude,
									'latitude' => $checklog_latitude
								);
								$txt = "REQUEST-BKD-".date("Ymd-His")."->".json_encode($data)."\n";
								fwrite($myfile, $txt);
								$payload = json_encode($data);
								curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
								curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
								curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
								curl_setopt($ch, CURLOPT_TIMEOUT_MS, 3000);
								$result = curl_exec($ch);
								$arr = json_decode($result, true);
								if($arr['pesan']=="Berhasil"){
									$this->inoutmobile_model->update($employee_id,$checklog_date);
								}
								curl_close($ch);$txt = "RESPON-BKD-".date("Ymd-His")."->".$result."\n";
								fwrite($myfile, $txt);*/

								$arrData[] = [
									"user_record"			=> "android",
									"employee_id"			=> $employee_id,
									"checklog_id"			=> $checklog_id,
									"checklog_id2"			=> $checklog_id,
									"notes"					=> $address,
									"checklog_latitude"		=> $checklog_latitude,
									"checklog_longitude"	=> $checklog_longitude,
									"checklog_timestamp"	=> $checklog_date,
									"checklog_event"		=> $checklog_event,
									"user_modified"			=> "android",
									"checklog_image_path"	=> null,
									"dt_modified"			=> $checklog_date,
									"dt_record"				=> $checklog_date
								];

								$arrOutput = [
									'result' 		=> true,
									'message' 		=> "succesfully insert data.",
									'data' 			=> $arrData
								];
							}
						}else{
							$arrOutput = [
								'result' 		=> false,
								'message' 		=> "Employee Data Not Found"
							];
						}
					}else{
						$arrOutput = [
							'result' 		=> false,
							'message' 		=> "fill all mandatory parameter"
						];
					}
				}else{
					$arrOutput = [
					'result' 		=> true,
					'message' 		=> "present data already exists"
					];
				}
            }else{
                $arrOutput = [
                'result' 		=> false,
                'message' 		=> "apikey is not valid"
                ];
            }
        }else{
            $arrOutput = [
                'result' 		=> false,
                'message' 		=> "apikey is not defined"
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

	function getAndroidChecklog_post(){
		if (!file_exists("application/controllers/api/intrax/logs/log-getAndroidChecklog-".date("Y-m-d").".txt")) {
			$myfile = fopen("application/controllers/api/intrax/logs/log-getAndroidChecklog-".date("Y-m-d").".txt", "a");
		} else {
			$myfile = fopen("application/controllers/api/intrax/logs/log-getAndroidChecklog-".date("Y-m-d").".txt", "a");
		}

		$data = json_decode(file_get_contents('php://input'), true);
		$txt = "-------------------------------------------------------------\n";
		fwrite($myfile, $txt);
		$txt = "REQUEST-".date("Ymd-His")."->".json_encode($data)."\n";
		fwrite($myfile, $txt);
		$headers = getRequestHeaders();
		$apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;
        if($apikey!=""){
            if($apikey==$this->apikey){
                $checklog_id2     = !empty($data['checklog_id2']) ? $data['checklog_id2'] : null;
                $employee_id     = !empty($data['employee_id']) ? $data['employee_id'] : null;
                $startdate    = !empty($data['startdate'])? $data['startdate'] : null;
                $enddate    = !empty($data['enddate'])? $data['enddate'] : null;
                $checklog_from  = "INTRAX_2";
				$arrData = [];

                if($checklog_id2!=null && $employee_id!=null && $startdate!=null && $startdate !=null && $enddate !=null){
                    $this->load->model("employee_model");
                    $this->load->model("inoutmobile_model");
					$fromDate=date('Y-m-d',strtotime($startdate));
					$toDate=date('Y-m-d',strtotime($enddate));
					$dataInOutMobile = $this->inoutmobile_model->getDataInOutMobile($fromDate,$toDate,$employee_id);
					if($dataInOutMobile){
						foreach ($dataInOutMobile as $row) {
							if(!empty($row->image)){
								$arrOutput[] = [
									"checklog_timestamp"		=> $row->checklog_date,
									"checklog_event"			=> $row->checklog_event,
									"checklog_latitude"			=> $row->checklog_latitude,
									"checklog_longitude"		=> $row->checklog_longitude,
									"checklog_address"			=> $row->checklog_address,
									"checklog_path_image"		=> 'https://inact.interactiveholic.net/bo/sys_upload/absen_image/'.$row->image,
									"mood"						=> $row->mood
								];
							}else{
								$arrOutput[] = [
									"checklog_timestamp"		=> $row->checklog_date,
									"checklog_event"			=> $row->checklog_event,
									"checklog_latitude"			=> $row->checklog_latitude,
									"checklog_longitude"		=> $row->checklog_longitude,
									"checklog_address"			=> $row->checklog_address,
									"checklog_path_image"		=> null,
									"mood"						=> $row->mood
								];
							}

						}
					}
                }else{
                    $arrOutput = [
                        'result' 		=> false,
                        'message' 		=> "fill all mandatory parameter"
                    ];
                }
            }else{
                $arrOutput = [
                'result' 		=> false,
                'message' 		=> "apikey is not valid"
                ];
            }
        }else{
            $arrOutput = [
                'result' 		=> false,
                'message' 		=> "apikey is not defined"
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
	
	function getSummaryChecklog_post(){
		if (!file_exists("application/controllers/api/intrax/logs/log-getSummaryChecklog-".date("Y-m-d").".txt")) {
			$myfile = fopen("application/controllers/api/intrax/logs/log-getSummaryChecklog-".date("Y-m-d").".txt", "a");
		} else {
			$myfile = fopen("application/controllers/api/intrax/logs/log-getSummaryChecklog-".date("Y-m-d").".txt", "a");
		}

		$data = json_decode(file_get_contents('php://input'), true);
		$txt = "-------------------------------------------------------------\n";
		fwrite($myfile, $txt);
		$txt = "REQUEST-".date("Ymd-His")."->".json_encode($data)."\n";
		fwrite($myfile, $txt);
		$headers = getRequestHeaders();
		$apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;
        if($apikey!=""){
            if($apikey==$this->apikey){
                $employee_id     = !empty($data['employee_id']) ? $data['employee_id'] : null;
                $startdate    = !empty($data['startdate'])? $data['startdate'] : null;
                $enddate    = !empty($data['enddate'])? $data['enddate'] : null;
                $checklog_from  = "INTRAX_2";
				$arrData = [];

                if($employee_id!=null && $startdate!=null && $startdate !=null && $enddate !=null){
                    $this->load->model("employee_model");
                    $this->load->model("inoutmobile_model");
					$fromDate=date('Y-m-d',strtotime($startdate));
					$toDate=date('Y-m-d',strtotime($enddate));
					$dataInOutMobile = $this->inoutmobile_model->getSummaryData($fromDate,$toDate,$employee_id);
					if($dataInOutMobile){
						$arrOutput[] = [
							"checklog_count"		=> $dataInOutMobile,
							"checklog_status"		=> null
						];
					}
                }else{
                    $arrOutput = [
                        'result' 		=> false,
                        'message' 		=> "fill all mandatory parameter"
                    ];
                }
            }else{
                $arrOutput = [
                'result' 		=> false,
                'message' 		=> "apikey is not valid"
                ];
            }
        }else{
            $arrOutput = [
                'result' 		=> false,
                'message' 		=> "apikey is not defined"
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


    function getMachineAttendanceOld_post(){
        $headers = getRequestHeaders();

        $apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;

        if($apikey!=""){
            if($apikey==$this->apikey){
                $companyId = $this->input->post("company_id");

                load_model(["subscription_model","checkinout_model"]);
                $subscription = $this->subscription_model->getByIntraxCompanyID($companyId);

                if($subscription!=false){
                    $start     = $this->input->post('start_date');
                    $end       = $this->input->post('end_date');

                    $inOutData = $this->checkinout_model->getDataActiveIntrax($start,$end,"0",$subscription->appid);
                    //echo $this->db->last_query();
                    $dataLog =[];
                    foreach ($inOutData->result() as $row) {
                        //$index++;
                        //$strInOut .= $row->checkinout_employeecode.";".$row->checkinout_datetime.";".$row->checkinout_code.";\n";
                        switch ($row->checkinout_code){
                            case "0":
                                $checklogEvent = "CheckIn";
                                break;
                            case "1":
                                $checklogEvent = "CheckOut";
                                break;
                            case "2":
                                $checklogEvent = "BreakIn";
                                break;
                            case "2":
                                $checklogEvent = "BreakOut";
                                break;
                            default:
                                $checklogEvent = "AM";
                        }

                        $checklogEvent = "AM";

                        $dataLog[] = [
                            "company_id"    => $companyId,
                            "checklog_id"   => $row->checkinout_employeecode,
                            "employee_id"   => $row->checkinout_employee_id,
                            "checklog_event"=> $checklogEvent,
                            "checklog_date" => $row->checkinout_datetime
                        ];
                    }
                    $arrOutput = [
                        'success' 		=> true,
                        'error_code' 	=> "",
                        'message' 		=> "",
                        'data' 			=> $dataLog
                    ];
                }
            }else{
                $arrOutput = [
                'success' 		=> false,
                'error_code' 	=> "401",
                'message' 		=> "apikey is not valid",
                'data' 			=> ""
                ];
            }
        }else{
            $arrOutput = [
                'success' 		=> false,
                'error_code' 	=> "401",
                'message' 		=> "apikey is not defined",
                'data' 			  => ""
            ];
        }
        echo output_api($arrOutput,"json");
    }
    function getMachineAttendance_post(){
        $headers = getRequestHeaders();

        $apikey  = !empty($headers["Apikey"]) ? $headers["Apikey"] : null;

        if($apikey!=""){
            if($apikey==$this->apikey){
                $companyId = $this->input->post("company_id");

                load_model(["subscription_model"]);
                $subscription = $this->subscription_model->getByIntraxCompanyID($companyId);
                if($subscription!=false){
                    $this->load->library("dbconnection");
                    $conn = $this->dbconnection->connect();
                    $start     = $this->input->post('start_date');
                    $end       = $this->input->post('end_date');
                    $this->db->select("employee_id");
                    $this->db->where("appid",$subscription->appid);
                    $this->db->where("intrax_license","active");
                    $arrId = [];
                    $service1Query = $this->db->get("tbemployee");
                    foreach ($service1Query->result() as $rowQuery) {
                        $arrId[] = $rowQuery->employee_id;
                    }
                    $strId = implode(",", $arrId);

                    $sql = $conn->prepare("
                        select * from tbfinal_checkinout where appid='$subscription->appid' and (DATE(datetime) between '$start' and '$end') and employee_id in (".$strId.")
                        group
                        by appid,
                        employee_id,
                        datetime,
                        verify_code,
                        absen_code,
                        sn,
                        area_id,
                        cabang_id");
                    $sql->execute();
                    $fetched = $sql->fetchAll();
                    //echo $this->db->last_query();
                    $dataLog =[];
                    $logIds  =[];
                    if(!empty($fetched)){
                        foreach ($fetched as $row) {
                            //$index++;
                            //$strInOut .= $row->checkinout_employeecode.";".$row->checkinout_datetime.";".$row->checkinout_code.";\n";
                            // switch ($row->absencode){
                            //     case "0":
                            //         $checklogEvent = "CheckIn";
                            //         break;
                            //     case "1":
                            //         $checklogEvent = "CheckOut";
                            //         break;
                            //     case "2":
                            //         $checklogEvent = "BreakIn";
                            //         break;
                            //     case "2":
                            //         $checklogEvent = "BreakOut";
                            //         break;
                            //     default:
                            //         $checklogEvent = "AM";
                            // }

                            $checklogEvent = "AM";

                            $dataLog[] = [
                                "company_id"    => $companyId,
                                "checklog_id"   => $row["account_no"],
                                "employee_id"   => $row["employee_id"],
                                "checklog_event"=> $checklogEvent,
                                "checklog_date" => $row["datetime"]
                            ];
                            //$logIds[] = $row->checkinout_id;
                        }
                    }

                    // $this->db->where_in("checkinout_id",$logIds);
                    // $this->db->update("tbcheckinout",['intrax_download_flag'=>'1']);

                    $arrOutput = [
                        'success'       => true,
                        'error_code'    => "",
                        'message'       => "",
                        'data'          => $dataLog
                    ];
                }else{
                    $arrOutput = [
                        'success'       => false,
                        'error_code'    => "401",
                        'message'       => "company id not exists",
                        'data'          => ""
                    ];
                }
            }else{
                $arrOutput = [
                'success'       => false,
                'error_code'    => "401",
                'message'       => "apikey is not valid",
                'data'          => ""
                ];
            }
        }else{
            $arrOutput = [
                'success'       => false,
                'error_code'    => "401",
                'message'       => "apikey is not defined",
                'data'            => ""
            ];
        }
        echo output_api($arrOutput,"json");
    }
	function do_upload(){
		$config['upload_path']="./sys_upload/absen_image";
		$config['allowed_types']='jpg';
		$config['encrypt_name'] = TRUE;
		 $config['max_size']    = 2048; // 2mb
		$this->load->library('upload',$config);
		if($this->upload->do_upload("photocompany")){
		  $data = array('upload_data' => $this->upload->data());

		  $judul= $this->input->post('judul');
		  $fileName = $data['upload_data']['file_name'];
		  $error    = "";
		}else{
		  $fileName = "";
		  $error    = strip_tags($this->upload->display_errors());
		}
		return [
		  "error"    => $error,
		  "filename" => $fileName
		];
	}
}
