<?php
/**
 *
 */
class Otp_model extends CI_Model
{
  var $now;
  var $tableName = "tbotp";
  var $tableId   = "otp_id";

  function __construct()
  {
    parent::__construct();
    $this->now = date("Y-m-d H:i:s");
  }

  function generate($userID,$OTPType,$OTPPlatform)
  {
    $this->load->library("string_manipulation");
    generateOTP:
    $OTPNumber = $this->string_manipulation->generateRandomNumber(6);

    $this->db->select("count(otp_id) as totalSameOTP");
    $this->db->where("expired_date <= ",$this->now);
    $this->db->where("otp",$OTPNumber);
    $sqlCek = $this->db->get($this->tableName);
    $totalSameOTP = $sqlCek->row()->totalSameOTP;
    if($totalSameOTP==0){
      $createDate = $this->now;
      $expiredDate= date("Y-m-d H:i:s", strtotime($createDate."+5 minutes"));

      $dataInsert = [
        "user_id"     => $userID,
        "otp"         => $OTPNumber,
        "create_date" => $createDate,
        "expired_date"=> $expiredDate,
        "type"        => $OTPType,
        "platform"    => $OTPPlatform
      ];

      $this->insert($dataInsert);
      return $OTPNumber;
    }else{
      goto generateOTP;
    }
  }

  function insert($dataInsert)
  {
    $this->db->insert($this->tableName,$dataInsert);
  }

  function resendOTP($userID,$tipe,$OTPPlatform)
  {
    // stop OTP lama
    $this->stopOTP($userID,$tipe,$OTPPlatform);
    // generate OTP baru
    $newOTP = $this->generate($userID,$tipe,$OTPPlatform);
    return $newOTP;
  }

  function stopOTP($userID,$tipe,$OTPPlatform)
  {
    $dataUpdate = [
      "status"=>"failed"
    ];
    $this->db->where("type",$tipe);
    $this->db->where("user_id",$userID);
    $this->db->where("expired_date >=",$this->now);
    $this->db->where("platform",$OTPPlatform);
    $res = $this->db->update($this->tableName,$dataUpdate);
    if($res){
      return true;
    }else{
      return false;
    }
  }

  function getActiveOTP($userID,$tipe,$OTPPlatform)
  {
    $this->db->where("type",$tipe);
    $this->db->where("user_id",$userID);
    $this->db->where("expired_date >=",$this->now);
    $this->db->where("status ","");
    $this->db->where("platform",$OTPPlatform);

    $sql = $this->db->get($this->tableName);

    if($sql->num_rows()>0){
      return $sql->row();
    }else{
      return false;
    }
  }
  
  function getActiveIntraxOTP($userID,$tipe,$OTPPlatform)
  {
    $this->db->where("type",$tipe);
    $this->db->where("user_id",$userID);
    $this->db->where("expired_date >=",$this->now);
    $this->db->where("platform",$OTPPlatform);
    $this->db->limit(1);
	$this->db->order_by("otp_id", "desc");

    $sql = $this->db->get($this->tableName);

    if($sql->num_rows()>0){
      return $sql->row();
    }else{
      return false;
    }
  }

  function setSuccess($otpID){
    $dataUpdate = [
      "status" => "success"
    ];
    $this->db->where($this->tableId,$otpID);
    $res = $this->db->update($this->tableName,$dataUpdate);
    if($res){
      return true;
    }else{
      return false;
    }
  }

}
