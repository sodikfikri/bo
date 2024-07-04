<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . 'libraries/REST_Controller.php';

class Location extends REST_Controller
{
  var $now;
  var $apikey = "InterActive-fa040d-adb49aa-c02fe7-b7c2f8d-891dfc9";

  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
  }
  
  function getActiveLocation_get($apikey){
    $apikey = str_replace("_", "-", $apikey);
    if($apikey!=""){
      if($apikey==$this->apikey){
        $appid  = !empty($_GET["app_id"]) ? $_GET["app_id"] : "";
        if ($appid!="") {
          load_model([
            "area_model",
            "cabang_model",
            "subscription_model"
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
                $branchs[] = [
                  "branch_code" => $rowBranch->cabang_code,
                  "branch_name" => $rowBranch->cabang_name
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
              'message' 		=> "appid not found",
              'data' 			  => ""
            ];
          }

        }else{
          $arrOutput = [
            'success' 		=> "",
            'error_code' 	=> "500",
            'message' 		=> "appid not defined",
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

  function getNotActiveLocation_get($apikey){
    $apikey = str_replace("_", "-", $apikey);
    if($apikey!=""){
      if($apikey==$this->apikey){
        $appid  = !empty($_GET["app_id"]) ? $_GET["app_id"] : "";
        if ($appid!="") {
          load_model([
            "area_model",
            "cabang_model",
            "subscription_model"
          ]);
          $company = $this->subscription_model->getByAppId($appid);
          if($company!=false){
            $to   = date("Y-m-d");
            $from = date("Y-m-d",strtotime($to."-60 days"));
            $sqlArea   = $this->area_model->getNotActiveArea($from,$to,$appid);
            $sqlCabang = $this->cabang_model->getNotActiveCabang($from,$to,$appid);

            $arrArea   = [];
            $arrCabang = [];
            foreach ($sqlArea->result() as $row) {
              $arrArea[] = [
                "area_code" => $row->area_code,
                "area_name" => $row->area_name
              ];
            }
            foreach ($sqlCabang->result() as $row) {
              $arrCabang[] = [
                "cabang_code" => $row->cabang_code,
                "cabang_name" => $row->cabang_name
              ];
            }

            $result  = [
              "appid" => $appid,
              "company_name" => $company->company_name,
              "area" => $arrArea,
              "branch" => $arrCabang
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
              'message' 		=> "appid not found",
              'data' 			  => ""
            ];
          }
        }else{
          $arrOutput = [
            'success' 		=> "",
            'error_code' 	=> "500",
            'message' 		=> "appid not defined",
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
