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

    //============================ Shift Kerja ============================//
    function listShift($appid) {

        $sql = "SELECT * FROM tbnumrun WHERE appid = '$appid' AND is_delete = 0";
        $response = $this->db->query($sql);

        return $response->result();
    }

    function saveShift($numrun, $numrun_deil) {
        
        $this->db->trans_begin();

        $this->db->insert('tbnumrun', $numrun);
        $insert_id = $this->db->insert_id();
        foreach($numrun_deil['schclass'] as $items) {
            $detail_hour = $this->getDetailHour($items);
            foreach($numrun_deil['day'] as $deil) {
                $ins_deil = [
                    'num_run_id' => $insert_id,
                    'start_time' => $detail_hour[0]->start_time,
                    'end_time' => $detail_hour[0]->end_time,
                    'sdays' => $deil,
                    'edays' => $deil,
                    'schclass_id' => $items,
                    'created_at' => (new DateTime())->format('Y-m-d H:i:s')
                ];

                $this->db->insert('tbnumrundeil', $ins_deil);
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return $numrun_deil['schclass'];
        }
    }

    function deleteShift($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('tbnumrun', $data);
    }

    function getNumRunDeilByNumRunID($numrun_id) {
        $sql = "select * from tbnumrundeil where num_run_id = $numrun_id";

        $response = $this->db->query($sql);

        return $response->result();
    }

    function getDateByDayNumber($numrun_id) {
        $sql = "select tbnumrundeil.*, tbschclass.name as schname, tbschclass.color from tbnumrundeil join tbschclass on tbnumrundeil.schclass_id = tbschclass.id where tbnumrundeil.num_run_id = $numrun_id";

        $response = $this->db->query($sql);

        return $response->result();
    }

    function getNumRunById($numrun_id) {
        $sql = "SELECT * FROM tbnumrun WHERE id = $numrun_id";

        $response = $this->db->query($sql);

        return $response->result();
    }

    function countNumRunDeilByNumRunId($numrun_id) {
        $sql = "SELECT schclass_id, count(schclass_id) as ct FROM tbnumrundeil WHERE num_run_id = $numrun_id GROUP BY schclass_id";
        $response = $this->db->query($sql);

        $schclass_id = [];
        foreach($response->result() as $item) {
            array_push($schclass_id, $item->schclass_id);
        }

        $sql_hr = "SELECT * FROM tbschclass WHERE id IN (".implode(', ', $schclass_id).")";

        $response_hr = $this->db->query($sql_hr);
        return $response_hr->result();
    }

    function updateDataShift($params) {
        $this->db->trans_begin();

        $numrun_id = $params->shift->id;
        $schclass_id = $params->detail->schclass;

        $data_numrun = [
            'name' => $params->shift->name,
            'start_date' => $params->shift->effective_start_date,
            'end_date' => $params->shift->effective_end_date,
            'cyle' => $params->shift->rotation_number,
            'unit' => $params->shift->rotation_unit,
            'national_holiday' => $params->shift->national_holiday,
            'updated_at' => (new DateTime())->format('Y-m-d H:i:s')
        ];

        $this->db->where('id', $numrun_id);
        $upt = $this->db->update('tbnumrun', $data_numrun);

        $del_numrundeil = "delete from tbnumrundeil where num_run_id = $numrun_id";
        $this->db->query($del_numrundeil);

        foreach($schclass_id as $items) {
            $detail_hour = $this->getDetailHour($items);
            foreach($params->detail->day as $deil) {
                $ins_deil = [
                    'num_run_id' => $numrun_id,
                    'start_time' => $detail_hour[0]->start_time,
                    'end_time' => $detail_hour[0]->end_time,
                    'sdays' => $deil,
                    'edays' => $deil,
                    'schclass_id' => $items,
                    'created_at' => (new DateTime())->format('Y-m-d H:i:s')
                ];

                $this->db->insert('tbnumrundeil', $ins_deil);
            }
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return $upt;
        }

    }

    function dayNumRunDeil($numrun_id) {
        $sql = "SELECT * FROM tbnumrundeil WHERE num_run_id = $numrun_id GROUP BY sdays";
        $response = $this->db->query($sql);

        return $response->result();
    }

    //============================ Hari Libur ============================//
    function SaveDataHoliday($data) {
        $ins = $this->db->insert_batch('tbholidays', $data);

        return $ins;
    }

    function UpdateHoliday($id, $data) {

        $this->db->where('id', $id);
        
        return $this->db->update('tbholidays', $data);
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

    function DetailHoliday($id) {
        $sql = "SELECT * FROM tbholidays WHERE id = $id";

        $response = $this->db->query($sql);

        return $response->result();
    }

}