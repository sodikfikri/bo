<?php

/**
 *
 */
class Devicelicense_model extends CI_Model
{
  var $tableName= "tbdevicelicense";
  var $tableId  = "id";
  var $now;

  function __construct()
  {
    $this->now = date("Y-m-d H:i:s");
  }

  function get($appid){
    $this->db->where("appid",$appid);
    $sql = $this->db->get($this->tableName);
    return $sql;
  }

  function insert($data){
    $res = $this->db->insert($data);
    return $res;
  }
}
