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

    function RemoveHoliday($ids) {
        $this->db->where_in('id', $ids);
        $this->db->delete('tbholidays');

        return $this->db->affected_rows();
    }

}