<?php
/**
 *
 */
class Departement_model extends CI_Model
{
    function getDataTree($appid, $column = null, $value = null) {
        $this->db->select('id, name, parent');
        $this->db->from('tbdepartements');
        $this->db->where('appid', $appid);
        $this->db->where('is_delete', '0');

        if ($column) {
            $this->db->where($column, $value);
        }

        $data = [];
        $query = $this->db->get();

        if (count($query->result_array()) != 0) {
            foreach($query->result() as $item) {
                $obj = [
                    'id' => $item->id,
                    'data' => [
                        'name' =>$item->name,
                        'borderColor' => '#039be5',
                    ],
                    'parent' => $item->parent
                ];

                array_push($data, $obj);
            }
        }

        return $data;
    }

    function getParent($appid) {
        $sql = "SELECT id, name FROM tbdepartements WHERE is_delete = 0 AND appid = '$appid'";

        $response = $this->db->query($sql);

        return $response->result();
    }

    function saveData($params) {
        $ins = $this->db->insert('tbdepartements', $params);

        return $ins;
    }

    function getDetail($id) {
        $result = $this->db->select('*')->from('tbdepartements')->where('id', $id)->get();

        return $result->result();
    }

    function updateData($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('tbdepartements', $data);
    }

    function validateDelete($id) {
        $sql = 'select * from tbhierarchydepartements where parent = '.$id;

        $response = $this->db->query($sql);

        return $response->result();
    }

    function listTable($appid) {

        $sql = "select *, (
                    select IFNULL(COUNT(*), 0) from db_inact.tbemployee where departement_id = tbdepartements.id
                ) as total_emp
                from db_inact.tbdepartements 
                where appid = '$appid' and is_delete = 0";

        $response = $this->db->query($sql);

        return $response->result();
    }

    function getCompany($appid) {
        $sql = "select * from iasubscription where appid = '$appid'";

        $response = $this->db->query($sql);

        return $response->result();
    }
    
}