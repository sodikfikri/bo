<?php 
class Migrator extends CI_Controller{
    function __construct(){
        parent::__construct();
    } 

    function migrateToFinalAttendance(){
        $this->load->library("dbconnection");

        $this->db->limit(5);
        // $this->db->order_by("checkinout_id","ASC");
        $this->db->where("checkinout_id >",5910460); // TRAD
        $this->db->where("toFinalTable", "n");
        $this->db->where("temperature <>", "0");
        // $this->db->where("appid", "IA01M6859F20210906613"); // TRAD
        // $this->db->like("checkinout_datetime ", "2022-02","after"); //periode
        
        $CIsql = $this->db->get("tbcheckinout");
        $conn = $this->dbconnection->connect();
        foreach($CIsql->result() as $data){
            // echo "<pre>";
            // print_r($data);
            // echo "</pre>";
            $query = "update tbfinal_checkinout set temperature='".$data->temperature."', use_masker='".$data->mask_flag."'
                      where
                      sn = '".$data->checkinout_SN."'
                      and employee_id = '".$data->checkinout_employee_id."'
                      and datetime = '".$data->checkinout_datetime."'
                      ";
            $sql = $conn->prepare($query);
            $sql->execute();

            $this->db->where("checkinout_id",$data->checkinout_id);
            $this->db->update("tbcheckinout",["toFinalTable" => "y"]);
        }
        echo $CIsql->num_rows()." data processed";
    }
}