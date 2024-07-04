<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';
/**
 *
 */
class Inout_test extends REST_Controller
{
  var $now;
  var $apikey = "3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9";

  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
  }

  function index_post(){
    $this->load->library("dbconnection");

    if(!empty($_POST["key"])){
      $key   = $_POST["key"];
      if($key==$this->apikey){
        $appid     = $_POST['app_id'];
        $start     = $_POST['start_date'];
        $end       = $_POST['end_date'];
        $conn      = $this->dbconnection->connect();
        $strQuery  = "select * from tbfinal_checkinout where appid='$appid' and (DATE(datetime) between '$start' and '$end') ";
        // echo $strQuery;
        $query = $conn->prepare($strQuery,array(PDO::MYSQL_ATTR_FOUND_ROWS => true));
        $query->execute();
        $fetchData = $query->fetchAll();
        // $inOutData = $this->checkinout_model->getData($start,$end,"0",$appid);
        $strInOut  = "";
        $arrInOutID= [];
        $inoutCount= count($fetchData);
        $index     = 0;
        $indexMin  = 0;
        $indexMax  = 0;

        $dataLog =[];
        foreach ($fetchData as $row) {
          $index++;
          $dataLog[] = [
            "id_user"     => $row["account_no"],
            "tanggal_jam" => $row["datetime"],
            "method_absen"=> $row["verify_code"],
            "type_in"     => $row["absen_code"],
            "temperature" => !empty($row["temperature"]) ? $row["temperature"] : "",
            "use_masker"  => !empty($row["use_masker"]) ? $row["use_masker"] : ""
          ];

          array_push($arrInOutID,$row["id"]);

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
        // $dataHistori = [
        //   "appid" => $appid,
        //   "historydownloadcheckinout_date_create" => $this->now,
        //   "historydownloadcheckinout_checkinout_id_min" => $indexMin,
        //   "historydownloadcheckinout_checkinout_id_max" => $indexMax,
        //   "historydownloadcheckinout_checkinout_count" => $inoutCount,
        //   "historydownloadcheckinout_name_of_file" => $completeFilename,
        //   "historydownloadcheckinout_status" => "pending",
        //   "filter_start" => $start,
        //   "filter_end" => $end
        // ];

        // $this->db->insert("tbhistorydownloadcheckinout",$dataHistori);
        // set download flag

        // $flagUpdate = [
        //   "checkinout_flag_download" => "1"
        // ];
        // jika proses trial sudah fix ini bisa diaktifkan
        //$this->checkinout_model->updateByCollectId($flagUpdate,$arrInOutID,$appid);
        $msg = json_encode($output);
        
      }else{
        // apikey not match
        $msg = "API Key No Match";
      }
    }else{
      $msg = "API Key Not Set";
    }
    echo $msg;
    $conn = null;
  }
}
