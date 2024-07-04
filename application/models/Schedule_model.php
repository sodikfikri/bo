<?php
/**
 *
 */
class Schedule_model extends CI_Model
{

    function SaveDataHoliday($data) {
        $ins = $this->db->insert_batch('tbholidays', $data);

        return $ins;
    }

    function HolidayList() {
        $sql = "SELECT * FROM tbholidays";
        $response = $this->db->query($sql);

        return $response->result();
    }

}