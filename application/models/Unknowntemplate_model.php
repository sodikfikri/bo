<?php 

class Unknowntemplate_model extends CI_Model{
    var $tableName = "unknown_template";
    function __Construct(){
        parent::__Construct();

    }


    function insert($data){
        return $this->db->insert($this->tableName,$data);
    }

}