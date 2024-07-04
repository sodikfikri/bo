<?php
/**
 *
 */
class Region_model extends CI_model
{
  var $countryTable = "m_countries";
  var $countryKey   = "id";

  var $provinceTable= "m_provinces";
  var $provinceKey  = "id";
  var $provinceCountryKey = "country_id";

  var $cityTable    = "m_regencies";
  var $cityKey      = "id";
  var $cityProvinceKey = "province_id";


  function __construct()
  {
    parent::__construct();
  }

  function getProvinceByCountry($countryID){
    $this->db->where($this->provinceCountryKey,$countryID);
    $sql = $this->db->get($this->provinceTable);
    if($sql->num_rows()>0){
      return $sql->result();
    }else{
      return false;
    }
  }

  function getCityByProvince($provinceID){
    $this->db->where($this->cityProvinceKey,$provinceID);
    $sql = $this->db->get($this->cityTable);
    if($sql->num_rows()>0){
      return $sql->result();
    }else{
      return false;
    }
  }
  function getCountryById($id){
    $this->db->where($this->countryKey,$id);
    $sql = $this->db->get($this->countryTable);
    if($sql->num_rows()>0){
      return $sql->row();
    }else{
      return false;
    }
  }

  function getProvinceById($id){
    $this->db->where($this->provinceKey,$id);
    $sql = $this->db->get($this->provinceTable);
    if($sql->num_rows()>0){
      return $sql->row();
    }else{
      return false;
    }
  }

  function getCityById($id){
    $this->db->where($this->cityKey,$id);
    $sql = $this->db->get($this->cityTable);
    if($sql->num_rows()>0){
      return $sql->row();
    }else{
      return false;
    }
  }
  function getCountry(){
    $sql = $this->db->get($this->countryTable);
    return $sql->result();
  }
}
