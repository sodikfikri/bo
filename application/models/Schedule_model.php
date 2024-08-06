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

    //============================ Assign Shift Kerja ============================//

    function getDetaprtementByAppid($appid) {
        $sql = "SELECT * FROM tbdepartements WHERE appid = '$appid'";

        $response = $this->db->query($sql);

        return $response->result();
    }

    function getEmpByDept($dept_id) {
        $sql = "SELECT employee_id, employee_full_name FROM tbemployee WHERE departement_id = $dept_id";

        $response = $this->db->query($sql);

        return $response->result();
    }

    function getAllEmp($appid, $search, $limit, $offset) {

        $sql = "SELECT * FROM tbemployee WHERE appid = '$appid' AND departement_id IS NOT NULL AND employee_full_name LIKE '%$search%' LIMIT $limit OFFSET $offset";

        $response = $this->db->query($sql);

        return $response->result();
    }

    function countEmpByName($appid, $name) {
        $sql = "SELECT count(*) total FROM tbemployee where appid = '$appid' AND departement_id IS NOT NULL AND employee_full_name LIKE '%$name%'";

        $response = $this->db->query($sql);

        return $response->result();
    }

    function SaveWorkScheduled($appid, $data) {
        $this->db->trans_begin();

        $params_merge = [];
        $batch = uniqid();

        if ($data->shift) {
            $params_scheduled = [];
            foreach($data->employee as $val) {
                $obj = [
                    'appid' => $appid,
                    'user_id' => $val,
                    'num_of_run_id' => $data->shift,
                    'created_at' => (new DateTime())->format('Y-m-d H:i:s')
                ];
                array_push($params_scheduled, $obj);

                $push_merge = [
                    'appid' => $appid,
                    'user_id' => $val,
                    'departement_id' => $data->departement_id,
                    'numrun_id' => $data->shift,
                    'schclass_id' => null,
                    'cyle' => $data->cyle,
                    'unit' => $data->unit,
                    'batch' => $batch,
                    'created_at' => (new DateTime())->format('Y-m-d H:i:s')
                ];
                array_push($params_merge, $push_merge);
            }
            $ins = $this->db->insert_batch('tbuserofrun', $params_scheduled);
        } 
        if(count($data->work) != 0) {
            $params_auto = [];
            foreach($data->work as $wrk) {
                foreach($data->employee as $val) {
                    $dt = [
                        'appid' => $appid,
                        'user_id' => $val,
                        'schclass_id' => $wrk,
                    ];
                    array_push($params_auto, $dt);
    
                    $push_merge = [
                        'appid' => $appid,
                        'user_id' => $val,
                        'departement_id' => $data->departement_id,
                        'numrun_id' => null,
                        'schclass_id' => $wrk,
                        'cyle' => null,
                        'unit' => null,
                        'batch' => $batch,
                        'created_at' => (new DateTime())->format('Y-m-d H:i:s')
                    ];
                    array_push($params_merge, $push_merge);
                }
            }
            $ins = $this->db->insert_batch('tbuserusedclasses', $params_auto);
        }

        $mergedData = [];

        foreach ($params_merge as $entry) {
            $userId = $entry['user_id'];

            if (!isset($mergedData[$userId])) {
                $mergedData[$userId] = $entry;
            } else {
                $mergedData[$userId] = array_merge($mergedData[$userId], array_filter($entry));
            }
        }
        
        $this->db->insert_batch('tbassignsch', array_values($mergedData));

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return $ins;
        }
    }

    function getEmpById($emp_id) {
        $sql = "SELECT * FROM tbemployee WHERE employee_id = $emp_id";

        $response = $this->db->query($sql);

        return $response->result();
    }

    function getDataUserOfRun() {
        $sql = "SELECT * FROM tbuserofrun";

        $response = $this->db->query($sql);

        return $response->result();
    }

    function getDataUserUsedClass() {
        $sql = "SELECT * FROM tbuserusedclasses";

        $response = $this->db->query($sql);

        return $response->result();
    }

    function listAssign($appid) {
        $sql = "SELECT 
                    count(asg.user_id) count_user, 
                    dpt.name as departement_name, 
                    asg.numrun_id, 
                    asg.schclass_id, 
                    asg.cyle, 
                    asg.unit,
                    asg.batch,
                CASE
                    WHEN asg.numrun_id IS NOT NULL AND asg.schclass_id IS NOT NULL THEN 'Scheduled and Automatic'
                    WHEN asg.numrun_id IS NOT NULL AND asg.schclass_id IS NULL THEN 'Scheduled'
                    WHEN asg.numrun_id IS NULL AND asg.schclass_id IS NOT NULL THEN 'Automatic'
                END AS type_schedule
                FROM 
                    tbassignsch asg
                LEFT JOIN 
                    tbdepartements dpt on asg.departement_id = dpt.id
                WHERE asg.appid = '$appid'
                GROUP BY asg.departement_id,asg.batch";

        $response = $this->db->query($sql);

        return $response->result();
    }

    function getDetailEmpOnSch($batch) {
        $sql = "SELECT 
                    tbassignsch.user_id, 
                    tbassignsch.departement_id,
                    tbassignsch.batch,
                    tbemployee.employee_full_name,
                    tbdepartements.name as departement_name
                FROM 
                    tbassignsch
                JOIN 
                    tbemployee ON tbassignsch.user_id = tbemployee.employee_id
                LEFT JOIN
                    tbdepartements ON tbassignsch.departement_id = tbdepartements.id
                WHERE 
                    batch = '$batch'";

        $response = $this->db->query($sql);

        return $response->result();
    }

    function deleteScheduleEmployee($appid, $userId, $dept_id, $batch) {
        $this->db->trans_begin();

        $usr_of_sch = "delete from tbuserofrun where appid = '$appid' and user_id = $userId";
        $this->db->query($usr_of_sch);

        $usr_used_sch = "delete from tbuserusedclasses where appid = '$appid' and user_id = $userId";
        $this->db->query($usr_used_sch);

        $usr_assign = "delete from tbassignsch where appid = '$appid' and user_id = $userId and departement_id = $dept_id and batch = '$batch'";
        $this->db->query($usr_assign);

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return 'success';
        }
    }

    //============================ Jadwal sementara ============================//

    function getListSchTemp($appid) {
        $sql = "SELECT 
                    temp.id, COUNT(temp.user_id) count_user, temp.start_date, 
                    temp.end_date, temp.batch, temp.departement_id, dpt.name AS departement_name,
                    sch.name, sch.start_time, sch.end_time
                FROM 
                    tbusertempsch AS temp
                JOIN 
                    tbdepartements AS dpt ON temp.departement_id = dpt.id
                JOIN 
                    tbschclass AS sch ON temp.schclass_id = sch.id
                WHERE 
                    temp.appid = '$appid'
                GROUP BY temp.batch ORDER BY temp.id";

        $response = $this->db->query($sql);

        return $response->result();
    }

    function insBatchSchTemp($data) {
        $ins = $this->db->insert_batch('tbusertempsch', $data);

        return $ins;
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