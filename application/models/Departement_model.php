<?php
/**
 *
 */
class Departement_model extends CI_Model
{
    function list_departement($appid, $column = null, $value = null) {
        $this->db->select('id, name, parent, label');
        $this->db->from('tbhierarchydepartements');
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
                        'label' =>$item->label,
                        'borderColor' => '#039be5',
                        // 'nodeWidth' => 150,
                    ],
                    'parent' => $item->parent
                ];

                array_push($data, $obj);
            }
        }

        return $data;
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
        $this->db->select('*');
        $this->db->from('tbdepartements');
        $this->db->where('appid', $appid);
        $this->db->where('is_delete', '0');
        
        $query = $this->db->get();

        return $query->result();
    }
    
}