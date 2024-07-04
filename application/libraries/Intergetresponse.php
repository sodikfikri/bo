<?php
require_once(APPPATH.'third_party/GetResponseAPI3.class.php');
class Intergetresponse{
  var $getresponse;
  var $CI;
  private $api_key_get = "98ffc7511b2f89aaaffe09c794d3297b";

  // wnucvyqfwkykegu23d8s806c77e9llfj  >> key demo
  // 98ffc7511b2f89aaaffe09c794d3297b  >> key live


  function __construct(){
    $this->getresponse = new GetResponse($this->api_key_get);
    $this->CI =& get_instance();
  }

  function sendResponse($member_id,$Cust_Name,$Cust_CompanyName,$Cust_EmailAddr,$Cust_MobilePhone,$Cust_ProductName,$in_campaignid,$Cust_BussinessSegmented){
    //$in_campaignid = "ytncP"; // dev
    // email marketing interactive "98ffc7511b2f89aaaffe09c794d3297b";
	  $hasil             = $this->getresponse->getContactsByCampaignId($Cust_EmailAddr,$in_campaignid);
	  $array             = json_decode(json_encode($hasil), True);

    if(!isset($array[0]['contactId'])){
      // ini untuk get custom field
      $customField = [];

      if(!empty($Cust_CompanyName)){
        $customField[] = [
          'customFieldId' => "HUrq8",
          'value'         => [$Cust_CompanyName]
        ];

        // demo account >> VBbAmK
        // live account >> HUrq8
      }

      if(!empty($Cust_ProductName)){
        $customField[] = [
          'customFieldId' => "HUrMe",
          'value'         => [$Cust_ProductName]
        ];
        // demo account >> VBbA2N
        // live account >> HUrMe
      }
      /*
      if(!empty($Cust_MobilePhone)){
        $mobilePhone = addCountryCode($Cust_MobilePhone);
        $customField[] = [
          'customFieldId' => "ixKcW",
          'value'         => [$mobilePhone]
        ];
        // demo account >> VBbUWZ
        // live account >> ixKcW

        $customField[] = [
          'customFieldId' => "VcASBd",
          'value'         => [$mobilePhone]
        ];
        // demo account >> VBbADP
        // live account >> VcASBd
      }
      */
      if(!empty($Cust_BussinessSegmented)){
        $customField[] = [
          'customFieldId' => "VcOdPB",
          'value'         => [$Cust_BussinessSegmented]
        ];
        // demo account >> VBbvon
        // live account >> VcOdPB
      }

      $response = $this->getresponse->addContact(
        array(
		       'name'              => $Cust_Name,
		       'email'             => $Cust_EmailAddr,
		       'dayOfCycle'        => 1,
		       'campaign'          => array('campaignId' => $in_campaignid),
          'customFieldValues' => $customField
        )
      );
      //print_r($response);
      $arrResponse = (array) $response;
      $error = false;
      if(!empty($arrResponse['code'])){
        $error = true;
      }

      if($error==false){
        $data_insert = [
          "member_id" => $member_id,
          "listCode"  => $in_campaignid
        ];
        $this->CI->db->select("count(id) as total");
        $sql     = $this->CI->db->get_where("getresponse_record",$data_insert);
        $records = $sql->row()->total;
        if($records==0){
          $this->CI->db->insert("getresponse_record",$data_insert);
        }
      }
    }
  }

  function getTags(){
    $result = $this->getresponse->getTags();
    $result_processed = json_decode(json_encode($result), True);
    return $result_processed;
  }

  function getCampaigns(){
    $result = $this->getresponse->getCampaigns();
    $result_processed = json_decode(json_encode($result), True);
    return $result_processed;
  }

  function getContactsByCampaignId($Cust_EmailAddr,$in_campaignid){
    $hasil             = $this->getresponse->getContactsByCampaignId($Cust_EmailAddr,$in_campaignid);
    $array             = json_decode(json_encode($hasil), True);
    return $array;
  }

  function getContact($contact_id){
    $hasil             = $this->getresponse->getContact($contact_id);
    $array             = json_decode(json_encode($hasil), True);
    return $array;
  }

  function updateContact($contact_id, $params){
    return $this->getresponse->updateContact($contact_id, $params);
  }

  function getCustomFields(){
    $res = $this->getresponse->getCustomFields();
    return $res;
  }
}
