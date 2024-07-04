<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
/**
 *
 */
class Inout extends REST_Controller
{
  var $now;
  var $apikey = "3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9";

  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
  }

  function index_post(){
    if(!empty($_POST["key"])){
      $key   = $_POST["key"];
      if($key==$this->apikey){
        load_model(["subscription_model","checkinout_model"]);
        $appid = $_POST['app_id'];
        $subscription = $this->subscription_model->getByAppId($appid);
        if($subscription!=false){
          $start     = $_POST['start_date'];
          $end       = $_POST['end_date'];

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
          // jika proses trial sudah fix ini bisa diaktifkan
          //$this->checkinout_model->updateByCollectId($flagUpdate,$arrInOutID,$appid);
          $msg = json_encode($output);
        }else{
          // appid not exist
          $msg = "Appid Not Exist";
        }
      }else{
        // apikey not match
        $msg = "API Key No Match";
      }
    }else{
      $msg = "API Key Not Set";
    }
    echo $msg;
  }
}
