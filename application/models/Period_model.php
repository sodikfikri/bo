<?php
/**
 *
 */
class Period_model extends CI_Model
{

    function list($appid) {
        $sql = "SELECT * FROM tbperiod WHERE appid = '$appid' AND is_delete = 0";
        $response = $this->db->query($sql);

        return $response->result();
    }

    function saveData($params) {
        $ins = $this->db->insert('tbperiod', $params);

        return $ins;
    }

    function updateData($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('tbperiod', $data);
    }
    
    function deleteData($id) {
        $this->db->where('id', $id);
        return $this->db->update('tbperiod', ['is_delete' => 1]);
    }

    function validateStoreData($appid, $id = null) {
        $parmas_update = '';
        if ($id) {
            $parmas_update = ' AND id <> ' . $id;
        } 
        $sql = "select * from db_inact.tbperiod where appid = '$appid' and is_active = 1 and is_delete = 0 $parmas_update";
        $response = $this->db->query($sql);

        return $response->result();
    }

}