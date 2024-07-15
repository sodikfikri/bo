<?php 
class Intrax extends CI_Controller{
    
    function __construct(){
        parent::__construct();
    }

    function submitRegistration(){
        $this->load->library("session");
        $this->load->library("encryption_org");
        $this->load->model("external_model");
        $this->load->model("system_model");
        $appid = $this->session->userdata("ses_appid");
        $this->system_model->renewLicenseSession($appid);
		$url_subscription = $this->uri->segment(3);
        $this->load->model("cabang_model");
        $fullname = $this->input->post("fullname"); 
        $companyName = $this->input->post("company-name"); 
        $phone = $this->input->post("phone"); 
        $address = $this->input->post("address"); 
        $headOffice = $this->input->post("head-office"); 
        $email = $this->input->post("email"); 
        if(!empty($this->input->post('use-inact-password')) && $this->input->post('use-inact-password')=="1"){
            $encPassword = $this->session->userdata("ses_encpassword");
            $password = $this->encryption_org->decode($encPassword);
        }else{
            $password = $this->input->post("password");
        }
        
        $paramCode = $this->input->post("paramCode");
        $subscription_id = $this->encryption_org->decode($this->input->post("subscription_id"));
        
        $branchData = $this->cabang_model->getById($headOffice,$appid);
        
        $premium = "";
        $lite = "";
        
        $arrActiveAddons    = $this->external_model->myBillingGetActiveAddons($appid);
        $activeTrialAddons  = $this->external_model->myBillingGetTrialAddons($appid);
        $intraxLitePaid = array_key_exists("intraxlicenselite", $arrActiveAddons);
        $intraxPremiumPaid = array_key_exists("intraxlicensepremium", $arrActiveAddons);
        $intraxSubscriptionPaid = array_key_exists($subscription_id, $arrActiveAddons);
        if ($intraxLitePaid==true || $intraxPremiumPaid==true || $intraxSubscriptionPaid==true) {
            $licenses = $this->session->userdata("infoAddons");
            foreach($licenses as $licenseCode => $licenseData){
                if($licenseCode=="intraxlicensepremium" || $licenseData["name"]=="InTrax Mobile Premium"){
					$license['intraxlicensepremium'] = $licenseData;
                    $premium = $licenseData["qty"];
                }
                if($licenseCode=="intraxlicenselite" || $licenseData["name"]=="InTrax Mobile Lite"){
					$license['intraxlicenselite'] = $licenseData;
                    $lite = $licenseData["qty"];
                }
            }
        }else{
            $licenses = $this->session->userdata("activeaddons");
            foreach($licenses as $licenseCode => $qty){
                if($licenseCode=="intraxlicensepremium"){
                    $premium = $qty;
                }
                if($licenseCode=="intraxlicenselite"){
                    $lite = $qty;
                }
				if($licenseCode==$subscription_id){
                    $subscription = $qty;
                }
            }
        }
        
        $licenseQty = 0;
        $infoAddons      = [];
        foreach ($arrActiveAddons as $index => $map) {
            $sessAddons[$index] = $map['qty'];
              
            $infoAddons[$index] = [
                "name"   => $map["name"],
                "expired"=> $map["expired"]
            ];

        }
        
        //$activeTrialAddons = !empty($this->session->userdata("activeTrialAddons")) ? $this->session->userdata("activeTrialAddons") : [];
        
        //print_r($this->session->userdata()); exit;
        
        if($premium!=""){
            $licenseQty = $premium;
            if($intraxLitePaid==true || $intraxPremiumPaid==true || $intraxSubscriptionPaid==true){
                $packageType = 2;
                $expired = !empty($license["intraxlicensepremium"]["expired"]) ? $license["intraxlicensepremium"]["expired"] : $license[$subscription_id]["expired"];
            }else{
                if(!empty($activeTrialAddons["intraxlicensepremium"])){
                    // paket premium trial
                    $packageType = 3;
                    $expired = $activeTrialAddons["intraxlicensepremium"]["expired"];
                }else{
                    // paket premium
                    $packageType = 2;
                    $expired = $license["intraxlicensepremium"]["expired"];
                }
            }
            
        }else{
            $licenseQty = $lite;
            $packageType = 1;
            $expired = $license["intraxlicenselite"]["expired"];
        }
        
        $tanggal1 = new DateTime($expired);
        $tanggal2 = new DateTime();
        
        $perbedaan = $tanggal2->diff($tanggal1)->format("%a");
        
        // request registration
		$time_zonenya = str_replace("UTC","",$branchData->cabang_utc);
        $curl = curl_init();
        $dataCompany = array(
            'nama_lengkap' => $fullname,
            'email' => $email,
            'password' => $password,
            'nama_perusahaan' => $companyName,
            'telp' => $phone,
            'timezone' => $time_zonenya,
            'alamat' => $address,
            'employee_count' => $licenseQty+$subscription,
            'packagetype' => $packageType,
            'active_day' => $perbedaan
        );
		if (!file_exists("application/controllers/api/intrax/logs/log-register-companyid-".date("Y-m-d").".txt")) {
			$myfile = fopen("application/controllers/api/intrax/logs/log-register-companyid-".date("Y-m-d").".txt", "a");
		} else {
			$myfile = fopen("application/controllers/api/intrax/logs/log-register-companyid-".date("Y-m-d").".txt", "a");
		}
		$txt = "-------------------------------------------------------------\n";
		fwrite($myfile, $txt);
		$txt = "REQUEST-".date("Ymd-His")."->".json_encode($dataCompany)."\n";
		fwrite($myfile, $txt);
		$txt = "-------------------------------------------------------------\n";
		fwrite($myfile, $txt);
		
		//print_r($dataCompany);
        
        //echo json_encode($dataCompany);
		//exit;
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://licenses.genesysindonesia.co.id/api/company/registrationApi',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $dataCompany,
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiNTg5NWQ3NzJlZjUwYjJiNDIxNzhlNTk3ZGIyZDEyYTFmMzJiMDUwYTg5YmE3NzE2MDQxYjEwZDliNjUxMjM3N2Y4ZjIwYWRlNmNhNjg1Y2UiLCJpYXQiOjE2ODk5MjUzMzYsIm5iZiI6MTY4OTkyNTMzNiwiZXhwIjoxNzIxNTQ3NzM2LCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.fEvQlX2xWE1tT-u89V4jSRUon4bMlr3nPD_zR0pBLGBNEQcnFWrXB63FVRGMCkduDKZ6C1LMJABhzj9OnhAevuaege9QUp2XjafUUDXA_UW4-E10qrIY3WEaCLfMFGaJDknl0_Cao2kQRxs440ftdsWJ_PMzPXOjwS0ywYxqIOrt0DoLzcB3FObxTMyR_QxsN9gTZdD--hk2wQU1iSE7I16432mAgXbWTCwEW1UeHlPMBaiSEivbzqhI6DByaFPLYa4YZMtqqCzJ4e2rOYMGnmQL4FP_WbONGyUKw5iyliUzWzD4Uljev-ZzBfde6LSpcUZWrF6M6liqZz93py-czB3Wpa5-CvfGqDQrF-8Dl5BbrvQjFs0wKd5dMncNjqjfvzNYuTqwq_dDQ5hghFybK9m7OXj4WlCtc0YZWXQwQJhka9EFeiGQMauFg7V4m44Kmdk36Nfqz_L5YWy6dVcgavNr9QqHv00u28aA0q3_Au4ju7poWI3KRheBGF1Iyd4KZ1T-g1MvTxGOYHZn5P8H2qCgSsPDoWvMcg2iMecPVLKAXLudhSvBp2HF50LJjxEBA4a3nago1PCmDcvOhC5TAIjOha2j-GT_Y4Or-CKaIrMF4s-h1vY2Jxy664FlAwBxt_al0MPvqT__gQyJMfLKSgHhnlcIPOwLIdA_KITXrnQ'
        ),
        ));

        $response = curl_exec($curl);
		$txt = "RESPON-".date("Ymd-His")."->".$response."\n";
		fwrite($myfile, $txt);
		$txt = "-------------------------------------------------------------\n";
		fwrite($myfile, $txt);
		fclose($myfile);
        curl_close($curl);
        $arrResponse = json_decode($response);
        
        //print_r($dataCompany);
        if($arrResponse->result==true){
            $intraxCompanyID = $arrResponse->data;
            //$this->cabang_model->setHeadOffice($headOffice,$appid);
            $this->load->model("subscription_model");
            $this->subscription_model->updateByAppId([
                "intrax_company_id" => $intraxCompanyID,
                "company_addr" => $address,
                "intrax_plan_code" => $packageType
            ],$appid);
            $this->sendEmailAnnouncement($arrResponse->mail_data);
            $this->session->set_userdata("msg",'<div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-check"></i> Success!</h5>
            InTrax Registration was success.
            </div>');
            redirect("addons-placement/".$paramCode."/".$url_subscription);
        }else{
            var_dump($response);
        }
    }
    
    function sendEmailAnnouncement($mailData)
    {
        /*
        $jsonData = '{
            "result": true,
            "message": "Success register data",
            "data": "TL0010",
            "mail_data": {
                "company_id": "TL0010",
                "company_name": "PT. Multi Saranatama",
                "user": "TL0010",
                "password": "12345678",
                "pic_email": "lodehmboksemah@gmail.com",
                "start": "31 August 2021",
                "end": "14 September 2021",
                "active_day": "14",
                "employee_count" : "10"
            }
        }';
        
        $objectmailData = json_decode($jsonData);
        $mailData = $objectmailData->mail_data;
        */
        // kirim email
        $body_msg = '
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
        <html>
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        </head>
        <style type="text/css" data-hse-inline-css="true">
            @media only screen and (max-width: 385px) {
            .main-page{
            background-color:#dfdfdf;
            padding:5px;"
            }
            @media only screen and (min-width: 386px) {
            .main-page{
            background-color:#dfdfdf;
            padding:40px;"
            }
        </style>
        <body style="font-family: \'Roboto\', sans-serif;">
            <div class="main-page"  >
            <img src="https://interactive.co.id/product/images/assets/intrax/logo.png" style="width: 200px;">
            <div style="max-width:653px; background-color:#ffffff;margin-left:auto; margin-right:auto; margin-top:40px; margin-bottom:40px;" >
                <div style="vertical-align: middle; padding:30px 30px 30px 30px;">
                <center style="padding-bottom:30px"></center>
                    <hr style="height: 1px;color: #dee0e3;background-color: #dee0e3;border: none;">
                    <p style="font-family: Roboto;
                        font-size: 24px;
                        font-weight: bold;
                        font-style: normal;
                        font-stretch: normal;
                        line-height: 1.17;
                        letter-spacing: normal;
                        text-align: left;
                        color: rgba(0, 0, 0, 0.7);
                        ">
                        Hello '.$mailData->user.',</p>
                        <p style="font-family: Roboto;
                        font-size: 15px;
                        font-weight: normal;
                        font-style: normal;
                        font-stretch: normal;
                        line-height: 1.67;
                        letter-spacing: normal;
                        text-align: left;
                        color: rgba(0, 0, 0, 0.7);
                        ">
                        Thank you for choosing to use InTrax. For reference, here is your company information on InTrax:</p>
                        <p style="font-family: Roboto;
                        font-size: 15px;
                        font-weight: normal;
                        font-style: normal;
                        font-stretch: normal;
                        line-height: 1.67;
                        letter-spacing: normal;
                        text-align: left;
                        color: rgba(0, 0, 0, 0.7);
                        ">
                        Company ID: '.$mailData->company_id.'<br>
                        Company Name: '.$mailData->company_name.'<br>
                        Number of Employees: '.(!empty($mailData->employee_count)?$mailData->employee_count:0).'<br>
                        Email PIC: '.$mailData->pic_email.'<br>
                        
                        </p>
                        <p style="font-family: Roboto;
                        font-size: 15px;
                        font-weight: normal;
                        font-style: normal;
                        font-stretch: normal;
                        line-height: 1.67;
                        letter-spacing: normal;
                        text-align: left;
                        color: rgba(0, 0, 0, 0.7);
                        ">
                        Subscription start date: '.$mailData->start.' Subscription end date: '.$mailData->end.'
                        </p>
                        <p>You can access InTrax dashboard from the the link bellow</p>
                        <br>
                        <center>
                        <a href="https://intrax.interactive.co.id" style="border-radius:3px;padding:15px 20px;font-size:15px;text-decoration:none;font-weight:600;border-radius:100px;background-color:#00cbce;text-align:center;color:#ffffff">Visit InTrax Dashboard</a>
                        </center>
                        <br>
                        <p style="font-family: Roboto;
                        font-size: 15px;
                        font-weight: normal;
                        font-style: normal;
                        font-stretch: normal;
                        line-height: 1.67;
                        letter-spacing: normal;
                        text-align: left;
                        color: rgba(0, 0, 0, 0.7);">
                            If you experience problems using InterActive Products, please contact our Customer Support team through  <font style="font-weight: bold;color: #00cbce;">(031) 535 5353</font> <b>Ext. 34</b>
                        </p>
                        <br>
                        <p style="font-family: Roboto;
                        font-size: 15px;
                        font-weight: normal;
                        font-style: normal;
                        font-stretch: normal;
                        line-height: 1.67;
                        letter-spacing: normal;
                        text-align: left;
                        color: rgba(0, 0, 0, 0.7);">Greetings,</p>
                        <p style="font-family: Roboto;
                        font-size: 15px;
                        font-weight: normal;
                        font-style: normal;
                        font-stretch: normal;
                        line-height: 1.67;
                        letter-spacing: normal;
                        text-align: left;
                        color: rgba(0, 0, 0, 0.7);">Interactive Team,</p>
                    </div>
                </div>
                <center>
                    <img src="https://cloud.interactive.co.id/mybilling/asset/img/interactive.png" height="15px" />
                    <p style="font-family: Roboto;
                    font-size: 12px;
                    font-weight: 500;
                    font-style: normal;
                    font-stretch: normal;
                    line-height: 1.67;
                    letter-spacing: normal;
                    text-align: center;
                    color: rgba(0, 0, 0, 0.38);">Jl. Ambengan No. 85, Surabaya 60136, Indonesia <br>
                    @ '.date('Y').', InterActive Technologies Corp. All rights reserved.</p>
                    <p style="font-family: Roboto;
                    font-size: 12px;
                    font-weight: 500;
                    font-style: normal;
                    font-stretch: normal;
                    line-height: 1.83;
                    letter-spacing: normal;
                    text-align: center;
                    color: rgba(0, 0, 0, 0.38);"><a href="https://www.youtube.com/user/interactivecorp">Youtube</a> - <a href="https://www.instagram.com/interactive_tech/">Instagram</a> -  <a href="https://www.facebook.com/InteractiveTec/">Facebook</a> - <a href="https://www.interactive.co.id">Website</a></p>
                </center>
            </div>
        </body>
        </html>';
        $this->load->library("intermailer");
        // $this->intermailer->initialize();
        $this->intermailer->initialize_allin();
        $this->intermailer->to([$mailData->pic_email=>$mailData->pic_email]);
        $this->intermailer->set_content("InTrax Activation Information",$body_msg,"Alt Body tes");
        if($this->intermailer->send())
        {
        return true;
        }else{
        return false;
        }
    }
}