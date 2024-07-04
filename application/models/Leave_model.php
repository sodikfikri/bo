<?php
/**
 *
 */
class Leave_model extends CI_Model
{
    // ======================== Master Categories Leave ======================== // 
    function getcategoryList($appid) {
        $this->db->select('*');
        $this->db->from('tbleavecategories');
        $this->db->where('is_delete', '0');
        $this->db->where('appid', "$appid");

        $query = $this->db->get();
        return $query->result();
    }

    function addCats($params) {
        $ins = $this->db->insert('tbleavecategories', $params);

        return $ins;
    }

    function delCats($idCats) {
        $upt = (new DateTime())->format('Y-m-d H:i:s');
        $sql = "UPDATE tbleavecategories SET is_delete = 1, updated_at = '$upt' WHERE id = $idCats";
        $res = $this->db->query($sql);
        return $res;
    }
    
    function updateCats($id, $data) {
        $upt = (new DateTime())->format('Y-m-d H:i:s');
        $nm = $data['name'];
        
        $file_sql = $data['icon'] ? 'lc.icon = "' . $data['icon'] . '",' : '';

        $sql = "UPDATE tbleavecategories AS lc SET lc.name = '$nm', $file_sql lc.updated_at = '$upt' WHERE lc.id = $id";

        $res = $this->db->query($sql);
        return $res;
    }

    // ======================== Leave Model For Mobile ======================== //
    function addLeave($datax, $appid, $employee_id) {
        $this->db->trans_begin();

        $ins = $this->db->insert('tbleaveclass', $datax);
        // kurangi kuota leave
        $sql = "UPDATE tbemployee SET leave_count = leave_count-1 WHERE employee_id = $employee_id AND appid = '$appid'";
        $this->db->query($sql);

        if ($this->db->trans_status() === FALSE) {
            // Jika ada kesalahan, rollback transaksi
            $this->db->trans_rollback();
            return false;
        } else {
            // Jika tidak ada kesalahan, commit transaksi
            $this->db->trans_commit();
            return $ins;
        }

    }

    function countLeave($appid, $employee_id, $start_date, $end_date) {
        $sql = "SELECT count(*) total FROM tbleaveclass WHERE DATE_FORMAT(created_at, '%Y-%m-%d') BETWEEN '$start_date' AND '$end_date' AND appid = '$appid' AND employee_id = $employee_id";
        $response = $this->db->query($sql);

        return $response->result();
    }

    function leaveListMobile($appid, $employee_id, $leave_id = null, $start_date, $end_date)
    {
        $sql_leave_id = '';
        if ($leave_id) {
            $sql_leave_id = ' AND lv.id = ' . $leave_id;
        }
        $sql = "SELECT 
                    lv.id, emp.employee_full_name, cats.name category_name, lv.category_id, lv.doc_path,
                    lv.start_date, lv.end_date, lv.start_time, lv.end_time, lv.reason, lv.created_at
                FROM 
                    tbleaveclass lv 
                JOIN
                    tbemployee emp ON lv.employee_id = emp.employee_id
                JOIN
                    tbleavecategories cats ON lv.category_id = cats.id
                WHERE 
                    lv.appid = '$appid' 
                    AND emp.employee_id = $employee_id 
                    AND DATE_FORMAT(lv.created_at, '%Y-%m-%d') BETWEEN '$start_date' AND '$end_date'
                    $sql_leave_id 
                ORDER BY lv.id DESC";
        
        $query = $this->db->query($sql);

        return $query->result();
    }

    function activePeriode($appid) {
        $sql = "SELECT * FROM tbperiod WHERE is_delete = 0 AND is_active = 1 AND appid = '$appid'";

        $query = $this->db->query($sql);

        return $query->result();
    }

    // ======================== Transaction Leave ======================== //
    function leaveList($appid) {
        $sql = "SELECT 
                    lv.*, emp.employee_full_name, cats.name category_name FROM tbleaveclass lv 
                JOIN
                    tbemployee emp ON lv.employee_id = emp.employee_id
                JOIN
                    tbleavecategories cats ON lv.category_id = cats.id
                WHERE 
                    lv.appid = '$appid' ORDER BY lv.id DESC";

        $query = $this->db->query($sql);

        return $query->result();
    } 

    function getLeaveClassById($id) {
        $sql = "SELECT * FROM tbleaveclass WHERE id = $id";
        $query = $this->db->query($sql);

        return $query->result();
    }
}