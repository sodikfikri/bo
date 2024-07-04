<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function setActivity($menu,$activityType){
  // add , edit, delete
  $CI =& get_instance();
  $userid = !empty($CI->session->userdata("ses_userid")) ? $CI->session->userdata("ses_userid") : "";
  $appid  = !empty($CI->session->userdata("ses_appid")) ? $CI->session->userdata("ses_appid") : "";
  $now    = date("Y-m-d H:i:s");
  if($userid!=""){
    $dataInsert = [
      "appid"  => $appid,
      "userid" => $userid,
      "activity_timestamp" => $now,
      "menu"   => $menu,
      "activity_type" => $activityType
    ];
    $res = $CI->db->insert("user_activity",$dataInsert);
    return $res;
  }
}
