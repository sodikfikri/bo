<?php
/**
 *
 */
class Historidownload_model extends CI_Model
{
  var $tableName  = "tbhistorydownloadcheckinout";
  var $tableId    = "historydownloadcheckinout_id";

  function __construct()
  {
    parent::__construct();
  }

  function getByDate($from,$to,$appid){
    $this->db->where("DATE(historydownloadcheckinout_date_create) >=",$from);
    $this->db->where("DATE(historydownloadcheckinout_date_create) <=",$to);
    $this->db->where("appid",$appid);
    $sql = $this->db->get($this->tableName);
    return $sql->result();
  }

}
