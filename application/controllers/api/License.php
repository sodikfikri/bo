<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
/**
 *
 */
class License extends REST_Controller
{
  var $now;
  var $apikey = "IAdev-apikey3fed48151b389b691898cc2a046772bfa040dadb49aac02fe7b7c2f8d891dfc9";

  function __construct()
  {
    parent::__construct();
    $this->load->library("device_caching");
  }

  function renewLicense_post(){
    $apikey = !empty($this->input->post("apikey")) ? $this->input->post("apikey") : "";
    if($apikey!=""){
      if($apikey==$this->apikey){
        load_model([
          "device_model",
          "employee_model",
          "external_model",
          "subscription_model"
        ]);
        $this->load->library("intrax_library");
        $appid = !empty($this->input->post("appid")) ? $this->input->post("appid") : "";
        $type  = !empty($this->input->post("type")) ? $this->input->post("type") : "";
        if($appid!=""){
          // get active addons
          $arrActiveAddons = $this->external_model->myBillingGetActiveAddons($appid);
          // echo "<pre>";
          // print_r($arrActiveAddons);
          // echo "</pre>";
          
          $arrTrialAddons = $this->external_model->myBillingGetTrialAddons($appid);
          $dataSubscription = $this->subscription_model->getByAppId($appid);  
          $sessAddons = [];
          if($arrActiveAddons!=false){
            foreach ($arrActiveAddons as $index => $map) {
              $sessAddons[$index] = $map['qty'];
              // intrax
              if($map["systemAddonsCode"]=="intraxlicensepremium" || $map["systemAddonsCode"]=="intraxlicenselite"){
                switch ($map["systemAddonsCode"]) {
                  case 'intraxlicensepremium':
                    $newPlan = "premium";
                    break;
                  case 'intraxlicenselite':
                    $newPlan = "lite";
                    break;
                }

                
                if($dataSubscription->intrax_plan_code==1 || $dataSubscription->intrax_plan_code==2){
                  // hanya update ketika selain aktifitas stop
                  if($type!="stop"){
                    $this->intrax_library->updateLicense($dataSubscription->intrax_company_id,$dataSubscription->intrax_plan_code,$newPlan,array(
                        "activeDay" => $map['activeday'],
                        "maxLicense"=> $map['qty'],
                        "typeorder" => $map['typeorder']
                      ));
                  }
                  
                }elseif($dataSubscription->intrax_plan_code==3){
                  if($type!="stop"){
                    $this->intrax_library->updateLicense($dataSubscription->intrax_company_id,$dataSubscription->intrax_plan_code,$newPlan,array(
                        "activeDay" => $map['activeday'],
                        "maxLicense"=> $map['qty'],
                        "typeorder" => $map['typeorder']
                      ));
                  }
                }elseif ($dataSubscription->intrax_plan_code==0) {
                  // kondisi jika sebelumnya belum pernah pakai
                }
              }
              // end intrax
            }
          }
          if($arrTrialAddons!=false){
            foreach ($arrTrialAddons as $index => $map) {
              if(array_key_exists($index,$sessAddons)){
                $sessAddons[$index] += $map['qty'];
              }else{
                $sessAddons[$index] = $map['qty'];
              }
            }
          }
          //
          $systemAddons = $this->db->get("systemaddons");
          foreach ($systemAddons->result() as $systemAddon) {
            // jika tidak menemukan addonanya maka dibuat index baru dengan qty 0
            if(empty($sessAddons[$systemAddon->systemaddons_code])){
              $sessAddons[$systemAddon->systemaddons_code] = 0;
            }
          }
          //
          
          foreach ($sessAddons as $addonsSystemCode => $licenseCount) {
            if($addonsSystemCode=="machinelicense"){
              $this->device_model->renewDeviceLicense($sessAddons,$appid);
            }
            // jika ada addons lain maka if-nya ditambahi kondisinya sebagai tautan ke addons yang terkait
          }
          $this->device_caching->cacheSN();
          $arrOutput = [
            'success' 		=> "OK",
            'error_code' 	=> "200",
            'message' 		=> "Operation Finished",
            'data' 			  => ""
          ];
        }else{
          $arrOutput = [
            'success' 		=> "",
            'error_code' 	=> "500",
            'message' 		=> "appid not defined",
            'data' 			  => ""
          ];
        }
      }else{
        $arrOutput = [
          'success' 		=> "",
          'error_code' 	=> "401",
          'message' 		=> "apikey is not valid",
          'data' 			  => ""
        ];
      }
    }else{
      $arrOutput = [
        'success' 		=> "",
        'error_code' 	=> "401",
        'message' 		=> "apikey is not set",
        'data' 			  => ""
      ];
    }
    echo output_api($arrOutput,"json");
  }
  
  // function reRegisterIntrax($appid){
  //   $this->load->model("external_model");
  //   $this->load->model("cabang_model");
  //   $this->load->model("subscription_model");
  //   $this->load->model("user_model");
  //   $subscriptionData = $this->subscription_model->getByAppId($appid);
  //   $ownerData        = $this->user_model->getRootUser($appid);

  //   $fullname     = $ownerData->user_fullname; 
  //   $companyName  = $subscriptionData->company_name; 
  //   $phone        = $subscriptionData->company_telp; 
  //   $address      = $subscriptionData->company_addr; 
  //   $email        = $subscriptionData->company_email; 
  //   $branchData   = $this->cabang_model->getHeadOffice($appid);
    
  //   $premium      = "";
  //   $lite         = "";
  //   $arrActiveAddons = $this->external_model->myBillingGetActiveAddons($result->appid);
  //   $licenses       = [];
  //   $licenseQty     = 0;
  //   $infoAddons     = [];
    
  //   foreach ($arrActiveAddons as $index => $map) {
  //     $licenses[$index] = $map['qty'];
  //     $infoAddons[$index] = [
  //       "name"   => $map["name"],
  //       "expired"=> $map["expired"]
  //     ];
  //   }

  //   foreach($licenses as $licenseCode => $qty){
  //     if($licenseCode=="intraxlicensepremium"){
  //       $premium = $qty;
  //     }
  //     if($licenseCode=="intraxlicenselite"){
  //       $lite = $qty;
  //     }
  //   }
       
  //   if($premium!=""){
  //     $licenseQty = $premium;
  //     // paket premium
  //     $packageType = 2;
  //     $expired = $infoAddons["intraxlicensepremium"]["expired"];
  //   }else{
  //     $licenseQty = $lite;
  //     $packageType = 1;
  //     $expired = $infoAddons["intraxlicenselite"]["expired"];
  //   }
        
  //   $tanggal1 = new DateTime($expired);
  //   $tanggal2 = new DateTime();
        
  //   $perbedaan = $tanggal2->diff($tanggal1)->format("%a");
        
  //   $curl = curl_init();
  //   $dataCompany = array(
  //     'nama_lengkap' => $fullname,
  //     'email' => $email,
  //     'password' => $password,
  //     'nama_perusahaan' => $companyName,
  //     'telp' => $phone,
  //     'timezone' => str_replace("UTC","",$branchData->cabang_utc),
  //     'alamat' => $address,
  //     'employee_count' => $licenseQty,
  //     'packagetype' => $packageType,
  //     'active_day' => $perbedaan
  //   );
    
  //   curl_setopt_array($curl, array(
  //     CURLOPT_URL => 'https://licenses.genesysindonesia.co.id/api/company/registrationApi',
  //     CURLOPT_RETURNTRANSFER => true,
  //     CURLOPT_ENCODING => '',
  //     CURLOPT_MAXREDIRS => 10,
  //     CURLOPT_TIMEOUT => 0,
  //     CURLOPT_FOLLOWLOCATION => true,
  //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  //     CURLOPT_CUSTOMREQUEST => 'POST',
  //     CURLOPT_POSTFIELDS => $dataCompany,
  //     CURLOPT_HTTPHEADER => array(
  //       'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiY2U3NjZkZmM5ZWZiMjRmZjlmY2M1Njk1Y2ZkNTBkYTFhMmZhZTBiOTY1YzVlNGU2NjhhY2FmZGE5MDRjYjQ3ZmJiOGFhZmY3NmU4ZjQzZjAiLCJpYXQiOjE2MjY4NDk4MTEsIm5iZiI6MTYyNjg0OTgxMSwiZXhwIjoxNjU4Mzg1ODExLCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.NjfS5VuzX2edr10S72H4PgRcOPtQ1EFqVHLpBz5B-t4c39X1W6d-hw5Y-n0PtXAfjV-Fp3m6NrF31b_HHEESOBOijsU0Qp7prbeS1iR80xbR3Si9SXYgKXx-V-g2t_7TMZcUPezGM4weUEhv-vB3yZuO0_75ShtIP5QSPsgbXawu3gbM_z6hJ6y_k1VsVosn7raAGszsba_VY6rhjfsBY1W31x31526nVD19L947EFD48GreU3FVeJzjIlGhZJXE5rIlTXS3bUHwFBeDu4eI_jWYu2dF8lvLkCIJWsKCKRU7A1i6BTqZTNDIaUkNv8DDCWCJy6Nctb5KhJNV37aoXnJu4x3moIsGStWJmwwmUkOYIBKZ6IUm9uQMR0vWYJiAO3v3FeA2yyo5kdChKTuScTyA9cDTA59Cqv5gqeTkuGwSPTCSWocVw9FUjjVywv4j7PTVJnuz8TBI1a-Jhyyx3-azEyqwQscpVUFAPBsxlc-bBQlkh8CXkHkaf0Df5PXN6w329oRgRQm1gSVTrWCluJwXNV_5pBe5nhL4Ng8x8BvaDG32RHDjsIkXr37oLGj7tZGxBtlne045sEXWEkqSceGMKqGm_S8_hvchU-VO8dvVBQ7Klie2RALIjHwBH_8c3Q0EII1qT7lZkphln1qCRzhXjbE__favOqUE8N3FubQ',
  //       'Accept: application/json'
  //     ),
  //   ));

  //   $response = curl_exec($curl);

  //   curl_close($curl);
  //   $arrResponse = json_decode($response);
        
  //   if($arrResponse->result==true){
  //     $intraxCompanyID = $arrResponse->data;
  //     $this->cabang_model->setHeadOffice($headOffice,$appid);
  //     $this->subscription_model->updateByAppId([
  //       "intrax_company_id" => $intraxCompanyID,
  //       "intrax_plan_code" => $packageType
  //     ],$appid);
  //     $this->sendEmailAnnouncement($arrResponse->mail_data);
  //     //redirect("addons-placement/".$paramCode);
  //   }else{
  //     //var_dump($response);
  //   }
  // }

  // function sendEmailAnnouncement($mailData)
  //   {
  //     // kirim email
  //     $body_msg = '
  //       <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
  //       <html>
  //       <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
  //       <head>
  //           <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  //           <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  //       </head>
  //       <style type="text/css" data-hse-inline-css="true">
  //           @media only screen and (max-width: 385px) {
  //           .main-page{
  //           background-color:#dfdfdf;
  //           padding:5px;"
  //           }
  //           @media only screen and (min-width: 386px) {
  //           .main-page{
  //           background-color:#dfdfdf;
  //           padding:40px;"
  //           }
  //       </style>
  //       <body style="font-family: \'Roboto\', sans-serif;">
  //           <div class="main-page"  >
  //           <img src="https://interactive.co.id/product/images/assets/intrax/logo.png" style="width: 200px;">
  //           <div style="max-width:653px; background-color:#ffffff;margin-left:auto; margin-right:auto; margin-top:40px; margin-bottom:40px;" >
  //               <div style="vertical-align: middle; padding:30px 30px 30px 30px;">
  //               <center style="padding-bottom:30px"></center>
  //                   <hr style="height: 1px;color: #dee0e3;background-color: #dee0e3;border: none;">
  //                   <p style="font-family: Roboto;
  //                       font-size: 24px;
  //                       font-weight: bold;
  //                       font-style: normal;
  //                       font-stretch: normal;
  //                       line-height: 1.17;
  //                       letter-spacing: normal;
  //                       text-align: left;
  //                       color: rgba(0, 0, 0, 0.7);
  //                       ">
  //                       Hello '.$mailData->user.',</p>
  //                       <p style="font-family: Roboto;
  //                       font-size: 15px;
  //                       font-weight: normal;
  //                       font-style: normal;
  //                       font-stretch: normal;
  //                       line-height: 1.67;
  //                       letter-spacing: normal;
  //                       text-align: left;
  //                       color: rgba(0, 0, 0, 0.7);
  //                       ">
  //                       Thank you for choosing to use InTrax. For reference, here is your company information on InTrax:</p>
  //                       <p style="font-family: Roboto;
  //                       font-size: 15px;
  //                       font-weight: normal;
  //                       font-style: normal;
  //                       font-stretch: normal;
  //                       line-height: 1.67;
  //                       letter-spacing: normal;
  //                       text-align: left;
  //                       color: rgba(0, 0, 0, 0.7);
  //                       ">
  //                       Company ID: '.$mailData->company_id.'<br>
  //                       Company Name: '.$mailData->company_name.'<br>
  //                       Number of Employees: '.(!empty($mailData->employee_count)?$mailData->employee_count:0).'<br>
  //                       Email PIC: '.$mailData->pic_email.'<br>
                        
  //                       </p>
  //                       <p style="font-family: Roboto;
  //                       font-size: 15px;
  //                       font-weight: normal;
  //                       font-style: normal;
  //                       font-stretch: normal;
  //                       line-height: 1.67;
  //                       letter-spacing: normal;
  //                       text-align: left;
  //                       color: rgba(0, 0, 0, 0.7);
  //                       ">
  //                       Subscription start date: '.$mailData->start.' Subscription end date: '.$mailData->end.'
  //                       </p>
  //                       <p>You can access InTrax dashboard from the the link bellow</p>
  //                       <br>
  //                       <center>
  //                       <a href="https://intrax.interactive.co.id" style="border-radius:3px;padding:15px 20px;font-size:15px;text-decoration:none;font-weight:600;border-radius:100px;background-color:#00cbce;text-align:center;color:#ffffff">Visit InTrax Dashboard</a>
  //                       </center>
  //                       <br>
  //                       <p style="font-family: Roboto;
  //                       font-size: 15px;
  //                       font-weight: normal;
  //                       font-style: normal;
  //                       font-stretch: normal;
  //                       line-height: 1.67;
  //                       letter-spacing: normal;
  //                       text-align: left;
  //                       color: rgba(0, 0, 0, 0.7);">
  //                           If you experience problems using InterActive Products, please contact our Customer Support team through  <font style="font-weight: bold;color: #00cbce;">(031) 535 5353</font> <b>Ext. 34</b>
  //                       </p>
  //                       <br>
  //                       <p style="font-family: Roboto;
  //                       font-size: 15px;
  //                       font-weight: normal;
  //                       font-style: normal;
  //                       font-stretch: normal;
  //                       line-height: 1.67;
  //                       letter-spacing: normal;
  //                       text-align: left;
  //                       color: rgba(0, 0, 0, 0.7);">Greetings,</p>
  //                       <p style="font-family: Roboto;
  //                       font-size: 15px;
  //                       font-weight: normal;
  //                       font-style: normal;
  //                       font-stretch: normal;
  //                       line-height: 1.67;
  //                       letter-spacing: normal;
  //                       text-align: left;
  //                       color: rgba(0, 0, 0, 0.7);">Interactive Team,</p>
  //                   </div>
  //               </div>
  //               <center>
  //                   <img src="https://cloud.interactive.co.id/mybilling/asset/img/interactive.png" height="15px" />
  //                   <p style="font-family: Roboto;
  //                   font-size: 12px;
  //                   font-weight: 500;
  //                   font-style: normal;
  //                   font-stretch: normal;
  //                   line-height: 1.67;
  //                   letter-spacing: normal;
  //                   text-align: center;
  //                   color: rgba(0, 0, 0, 0.38);">Jl. Ambengan No. 85, Surabaya 60136, Indonesia <br>
  //                   @ '.date('Y').', InterActive Technologies Corp. All rights reserved.</p>
  //                   <p style="font-family: Roboto;
  //                   font-size: 12px;
  //                   font-weight: 500;
  //                   font-style: normal;
  //                   font-stretch: normal;
  //                   line-height: 1.83;
  //                   letter-spacing: normal;
  //                   text-align: center;
  //                   color: rgba(0, 0, 0, 0.38);"><a href="https://www.youtube.com/user/interactivecorp">Youtube</a> - <a href="https://www.instagram.com/interactive_tech/">Instagram</a> -  <a href="https://www.facebook.com/InteractiveTec/">Facebook</a> - <a href="https://www.interactive.co.id">Website</a></p>
  //               </center>
  //           </div>
  //       </body>
  //       </html>';
  //       $this->load->library("intermailer");
  //       $this->intermailer->initialize();
  //       $this->intermailer->to([$mailData->pic_email=>$mailData->pic_email]);
  //       $this->intermailer->set_content("InTrax Activation Information",$body_msg,"Alt Body tes");
  //       if($this->intermailer->send())
  //       {
  //       return true;
  //       }else{
  //       return false;
  //       }
  //   }
}
