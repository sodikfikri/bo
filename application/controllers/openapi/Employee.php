<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Employee extends REST_Controller
{
  var $now;
  var $apikey = "InterActive-fa040d-adb49aa-c02fe7-b7c2f8d-891dfc9";

  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
  }

  function getActive_get($apikey){
    $apikey = str_replace("_", "-", $apikey);

    load_model([
      "area_model",
      "cabang_model",
      "employee_model"
    ]);

    if($apikey!=""){
      if($apikey==$this->apikey){
        $appid  = !empty($_GET["app_id"]) ? $_GET["app_id"] : "";
        if ($appid!="") {

          load_model([
            "area_model",
            "cabang_model",
            "subscription_model",
            "employeeareacabang_model"
          ]);
          $company = $this->subscription_model->getByAppId($appid);

          if($company!=false){
            $sqlArea = $this->area_model->getAll($appid);

            $locations = [];
            foreach ($sqlArea as $rowArea) {
              $this->db->where("cabang_area_id",$rowArea->area_id);
              $sqlBranch = $this->cabang_model->getAll($appid);
              $branchs   = [];
              foreach ($sqlBranch as $rowBranch) {
                $sqlEmployee = $this->employeeareacabang_model->getEmployeeByLocation($rowArea->area_id,$rowBranch->cabang_id,$appid);
                $arrEmployee = [];

                foreach ($sqlEmployee->result() as $rowEmployee) {
                  $arrEmployee[] = [
                    "employee_code" => $rowEmployee->employee_account_no,
                    "employee_name" => $rowEmployee->employee_full_name,
                    "employee_nickname" => $rowEmployee->employee_nick_name,
                    "employee_joindate" => $rowEmployee->employee_join_date
                  ];
                }

                $branchs[] = [
                  "branch_code" => $rowBranch->cabang_code,
                  "branch_name" => $rowBranch->cabang_name,
                  "employee"    => $arrEmployee
                ];
              }

              $locations[] = [
                "area_code" => $rowArea->area_code,
                "area_name" => $rowArea->area_name,
                "branchs"   => $branchs
              ];
            }
            $result  = [
              "appid" => $appid,
              "company_name" => $company->company_name,
              "locations" => $locations
            ];
            $arrOutput = [
              'success' 		=> "ok",
              'error_code' 	=> "200",
              'message' 		=> "",
              'data' 			  => $result
            ];
          }else{
            $arrOutput = [
              'success' 		=> "",
              'error_code' 	=> "500",
              'message' 		=> "app_id not found",
              'data' 			  => ""
            ];
          }

        }else{
          $arrOutput = [
            'success' 		=> "",
            'error_code' 	=> "500",
            'message' 		=> "app_id not defined",
            'data' 			  => ""
          ];
        }
      }else{
        $arrOutput = [
          'success' 		=> "",
          'error_code' 	=> "401",
          'message' 		=> "apikey is not valid",
          'data' 			  => ""
        ];
      }
    }else{
      $arrOutput = [
        'success' 		=> "",
        'error_code' 	=> "401",
        'message' 		=> "apikey is not defined",
        'data' 			  => ""
      ];
    }
    echo output_api($arrOutput,"json");
  }

  function getNotActive_get($apikey){
    $apikey = str_replace("_", "-", $apikey);
    load_model([
      "area_model",
      "cabang_model",
      "employee_model"
    ]);
    
    if($apikey!=""){
      if($apikey==$this->apikey){
        $appid  = !empty($_GET["app_id"]) ? $_GET["app_id"] : "";
        if ($appid!="") {

          load_model([
            "subscription_model",
            "employee_model"
          ]);
          $company = $this->subscription_model->getByAppId($appid);

          if($company!=false){
            $sqlEmployee = $this->employee_model->getResignEmployee($appid);
            $resign      = [];
            foreach ($sqlEmployee->result() as $row) {
              $resign[] = [
                "employee_code" => $row->employee_account_no,
                "employee_name" => $row->employee_full_name,
                "employee_nickname" => $row->employee_nick_name,
                "employee_joindate" => $row->employee_join_date,
                "employee_resigndate" => $row->employee_resign_date,
              ];
            }
            $result  = [
              "appid" => $appid,
              "company_name" => $company->company_name,
              "resign" => $resign
            ];
            $arrOutput = [
              'success' 		=> "ok",
              'error_code' 	=> "200",
              'message' 		=> "",
              'data' 			  => $result
            ];
          }else{
            $arrOutput = [
              'success' 		=> "",
              'error_code' 	=> "500",
              'message' 		=> "app_id not found",
              'data' 			  => ""
            ];
          }

        }else{
          $arrOutput = [
            'success' 		=> "",
            'error_code' 	=> "500",
            'message' 		=> "app_id not defined",
            'data' 			  => ""
          ];
        }
      }else{
        $arrOutput = [
          'success' 		=> "",
          'error_code' 	=> "401",
          'message' 		=> "apikey is not valid",
          'data' 			  => ""
        ];
      }
    }else{
      $arrOutput = [
        'success' 		=> "",
        'error_code' 	=> "401",
        'message' 		=> "apikey is not defined",
        'data' 			  => ""
      ];
    }
    echo output_api($arrOutput,"json");
  }
}
