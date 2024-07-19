<?php
/**
 *
 */
class Schedule_model extends CI_Model
{

    //============================ Jam Kerja ============================//
    function listHour($appid) {
        $sql = "SELECT * FROM tbschclass WHERE appid = '$appid' AND is_delete = 0";
        $response = $this->db->query($sql);

        return $response->result();
    }

    function insHour($data) {
        $ins = $this->db->insert('tbschclass', $data);

        return $ins;
    }

    function uptHour($id, $data) {
        $this->db->where('id', $id);
        
        return $this->db->update('tbschclass', $data);
    }

    function getDetailHour($id) {
        $sql = "SELECT * FROM tbschclass WHERE id = $id";

        $response = $this->db->query($sql);

        return $response->result();
    }

    function getBranch($appid) {
        $sql = "SELECT * FROM tbcabang WHERE appid = '$appid' AND is_del = 0";
        $response = $this->db->query($sql);

        return $response->result();
    }

    //============================ Hari Libur ============================//
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