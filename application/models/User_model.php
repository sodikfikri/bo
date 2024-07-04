<?php

/**
 *
 */
class User_model extends CI_Model
{
  var $tableName = "iauser";
  var $tableId   = "userid";
  var $now;
  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
  }

  function insert($dataInsert)
  {
    $res = $this->db->insert($this->tableName,$dataInsert);
    if($res){
      return true;
    }else {
      return false;
    }
  }

  function update($data,$id){
    $this->db->where("userid",$id);
    $res = $this->db->update($this->tableName,$data);
    return $res;
  }

  function getById($id){
    $this->db->where($this->tableId,$id);
    $sql  = $this->db->get($this->tableName);
    $data = $sql->row();
    return $data;
  }

  function activate($userID){
    $dataUpdate = [
      "user_dateactive"=> $this->now,
      "user_isactive"  => "1"
    ];

    $this->db->where($this->tableId,$userID);
    $this->db->update($this->tableName,$dataUpdate);
  }

  function getDataUser($username,$password){
    $this->db->where("user_emailaddr",$username);
    $this->db->where("user_passw",$password);
    $this->db->order_by("userid","DESC");
    $this->db->limit(1);
    $sql = $this->db->get($this->tableName);

    if($sql->num_rows()>0){
      return $sql->row();
    }else{
      return false;
    }
  }

  function getDataByEmail($email){
    $this->db->where("user_emailaddr",$email);
    $sql = $this->db->get($this->tableName);
    if($sql->num_rows()>0){
      return $sql->row();
    }else{
      return false;
    }
  }

  function setAuthKey($authkey,$userid){
    $dataUpdate = [
      "authkey" => $authkey
    ];
    $this->db->where($this->tableId,$userid);
    $update = $this->db->update($this->tableName,$dataUpdate);
    if($update){
      return true;
    }else{
      return false;
    }
  }

  function getAuthenticateUser($userID,$authkey){
    $where = [
      "userid" => $userID,
      "authkey"=> $authkey
    ];

    $sql = $this->db->get_where($this->tableName,$where);

    if($sql->num_rows()>0){
      return $sql->row();
    }else{
      return false;
    }
  }

  function setNewPassword($newPassword,$userID,$authkey){
    $dataUpdate = [
      "user_passw" => $newPassword,
      "authkey" => ""
    ];

    $this->db->where("userid",$userID);
    $this->db->where("authkey",$authkey);
    $res = $this->db->update($this->tableName,$dataUpdate);
    if($res){
      return true;
    }else{
      return false;
    }
  }

  function getData($appid){
    $this->db->where("appid",$appid);
    $this->db->where("user_isdel !=","1");
    $sql = $this->db->get($this->tableName);
    return $sql->result();
  }
  function getRootID($appid){
    $this->db->select("userid");
    $this->db->where("appid",$appid);
    $this->db->where("user_parent","0");
    $sql = $this->db->get($this->tableName);
    if($sql->num_rows()>0){
      return $sql->row()->userid;
    }else{
      return false;
    }
  }

  function getRootUser($appid){
    $this->db->where("appid",$appid);
    $this->db->where("user_parent","0");
    $sql = $this->db->get($this->tableName);
    if($sql->num_rows()>0){
      return $sql->row();
    }else{
      return false;
    }
  }

  function setDeleted($userid){
    $dataUpdate = [
      "user_isdel"=>"1"
    ];
    $this->db->where($this->tableId,$userid);
    $res = $this->db->update($this->tableName,$dataUpdate);
    return $res;
  }
  public function getActivity($datestart,$dateend,$start,$length,$appid)
  {
    $this->db->select([
      "A.*",
      "B.user_fullname"
    ]);
    $this->db->from("user_activity A");
    $this->db->join("iauser B","B.userid = A.userid","left");
    $this->db->where("A.appid",$appid);
    $this->db->where("A.activity_timestamp >=",$datestart);
    $this->db->where("A.activity_timestamp <=",$dateend);
    if($start!="" || $length!="")
    {
      $this->db->limit($length,$start);
    }
    $sql = $this->db->get();
    return $sql;
  }
  function countAllActivity($appid)
  {
    $this->db->select("count(activity_id) as total");
    $this->db->where("appid",$appid);
    $sql = $this->db->get("user_activity");
    return $sql->row()->total;
  }

  function isEmailExists($email,$user_id){
    $this->db->where("user_emailaddr",$email);
    $sql = $this->db->get($this->tableName);
    if($sql->num_rows()>0){
      $data = $sql->row();
      if($data->userid==$user_id){
        return false;
      }else{
        return true;
      }
    }else{
      return false;
    }
  }
  function isPhoneExists($phone,$user_id){
    $this->db->where("user_phone",$phone);
    $sql = $this->db->get($this->tableName);
    if($sql->num_rows()>0){
      $data = $sql->row();
      if($data->userid==$user_id){
        return false;
      }else{
        return true;
      }
    }else{
      return false;
    }
  }

  function setRootUserAccess($aceess){
    $dataUpdate = [
      "user_access" => $aceess
    ];
    $this->db->where("user_isdel","0");
    $this->db->where("user_parent","0");
    return $this->db->update($this->tableName,$dataUpdate);
  }
}
