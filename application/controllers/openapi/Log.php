<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
/**
 *
 */
class Log extends REST_Controller
{
  var $now;
  var $apikey = "InterActive-fa040d-adb49aa-c02fe7-b7c2f8d-891dfc9";

  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
  }

  function index_get($key){
    $key = str_replace("_", "-", $key);
    if(!empty($key)){
      if($key==$this->apikey){
        load_model(["subscription_model","checkinout_model"]);
        $appid = $_GET['app_id'];
        $subscription = $this->subscription_model->getByAppId($appid);
        if($subscription!=false){
          $start     = !empty($_GET['start_date']) ? $_GET['start_date'] : ""; // Y-m-d
          $end       = !empty($_GET['end_date']) ? $_GET['end_date'] : ""; // Y-m-d
          if ($start!="" && $end!="") {
            $inOutData = $this->checkinout_model->getData($start,$end,"0",$appid);
            $strInOut  = "";
            $arrInOutID= [];
            $inoutCount= $inOutData->num_rows();
            $index     = 0;
            $indexMin  = 0;
            $indexMax  = 0;

            $dataLog =[];
            foreach ($inOutData->result() as $row) {
              $index++;
              //$strInOut .= $row->checkinout_employeecode.";".$row->checkinout_datetime.";".$row->checkinout_code.";\n";
              $dataLog[] = [
                "id_user"     => $row->checkinout_employeecode,
                "tanggal_jam" => $row->checkinout_datetime,
                "method_absen"=> $row->checkinout_verification_mode,
                "type_in"     => $row->checkinout_code,
                "temperature" => $row->temperature,
                "use_masker"  => $row->mask_flag
              ];

              array_push($arrInOutID,$row->checkinout_id);

              if($index==1){
                $indexMin = $row->checkinout_id;
              }
              if($index==$inoutCount){
                $indexMax = $row->checkinout_id;
              }
            }
            $output = [
              "start_date" => $start,
              "end_date"   => $end,
              "app_id"       => $appid,
              "total_record" => $inoutCount,
              "data_log"   => $dataLog
            ];
            // create file
            $completeFilename = "";//$filename.".txt";
            //$myfile = fopen("storage/inoutdownload/".$completeFilename, "w") or die("Unable to open file!");
            //fwrite($myfile, $strInOut);
            // create record history
            $dataHistori = [
              "appid" => $appid,
              "historydownloadcheckinout_date_create" => $this->now,
              "historydownloadcheckinout_checkinout_id_min" => $indexMin,
              "historydownloadcheckinout_checkinout_id_max" => $indexMax,
              "historydownloadcheckinout_checkinout_count" => $inoutCount,
              "historydownloadcheckinout_name_of_file" => $completeFilename,
              "historydownloadcheckinout_status" => "pending",
              "filter_start" => $start,
              "filter_end" => $end
            ];
            $this->db->insert("tbhistorydownloadcheckinout",$dataHistori);
            // set download flag

            $flagUpdate = [
              "checkinout_flag_download" => "1"
            ];
            
            $arrOutput = [
              'success'     => "",
              'error_code'  => "200",
              'message'     => "",
              'data'        => $output
            ];
          }else{
            $arrOutput = [
              'success'     => "",
              'error_code'  => "500",
              'message'     => "start_date , end_date not set correctly",
              'data'        => ""
            ];
          }         
        }else{
          // appid not exist
          $arrOutput = [
            'success'     => "",
            'error_code'  => "500",
            'message'     => "app_id not found",
            'data'        => ""
          ];
        }
      }else{
        // apikey not match
        $arrOutput = [
          'success'     => "",
          'error_code'  => "401",
          'message'     => "apikey is not valid",
          'data'        => ""
        ];
      }
    }else{
      $arrOutput = [
        'success'     => "",
        'error_code'  => "401",
        'message'     => "apikey is not defined",
        'data'        => ""
      ];
    }
    echo output_api($arrOutput,"json");
  }
}
