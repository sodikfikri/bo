<?php
/**
 *
 */
class Datafinger_model extends CI_Model
{
  var $tableName = "data_finger";

  function __construct()
  {
    parent::__construct();
  }

  function insert($data){
    $this->db->insert($this->tableName,$data);
  }
}
