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
        
        $branchData = $this->cabang_model->getById($headOffice,$appid);
        
        $premium = "";
        $lite = "";
        
        $arrActiveAddons    = $this->external_model->myBillingGetActiveAddons($appid);
        $activeTrialAddons  = $this->external_model->myBillingGetTrialAddons($appid);
        $intraxLitePaid = array_key_exists("intraxlicenselite", $arrActiveAddons);
        $intraxPremiumPaid = array_key_exists("intraxlicensepremium", $arrActiveAddons);
        if ($intraxLitePaid==true || $intraxPremiumPaid==true) {
            $licenses = $this->session->userdata("infoAddons");

            foreach($licenses as $licenseCode => $licenseData){
                if($licenseCode=="intraxlicensepremium"){
                    $premium = $licenseData["qty"];
                }
                if($licenseCode=="intraxlicenselite"){
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
            if($intraxLitePaid==true || $intraxPremiumPaid==true){
                $packageType = 2;
                $expired = $infoAddons["intraxlicensepremium"]["expired"];
            }else{
                if(!empty($activeTrialAddons["intraxlicensepremium"])){
                    // paket premium trial
                    $packageType = 3;
                    $expired = $activeTrialAddons["intraxlicensepremium"]["expired"];
                }else{
                    // paket premium
                    $packageType = 2;
                    $expired = $infoAddons["intraxlicensepremium"]["expired"];
                }
            }
            
        }else{
            $licenseQty = $lite;
            $packageType = 1;
            $expired = $infoAddons["intraxlicenselite"]["expired"];
        }
        
        $tanggal1 = new DateTime($expired);
        $tanggal2 = new DateTime();
        
        $perbedaan = $tanggal2->diff($tanggal1)->format("%a");
        
        //echo $perbedaan;
        
        // request registration
        $curl = curl_init();
        $dataCompany = array(
            'nama_lengkap' => $fullname,
            'email' => $email,
            'password' => $password,
            'nama_perusahaan' => $companyName,
            'telp' => $phone,
            'timezone' => str_replace("UTC","",$branchData->cabang_utc),
            'alamat' => $address,
            'employee_count' => $licenseQty,
            'packagetype' => $packageType,
            'active_day' => $perbedaan
        );
        
        // echo "<pre>";
        // print_r($dataCompany);
        // echo "</pre>";exit;

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
            'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiY2U3NjZkZmM5ZWZiMjRmZjlmY2M1Njk1Y2ZkNTBkYTFhMmZhZTBiOTY1YzVlNGU2NjhhY2FmZGE5MDRjYjQ3ZmJiOGFhZmY3NmU4ZjQzZjAiLCJpYXQiOjE2MjY4NDk4MTEsIm5iZiI6MTYyNjg0OTgxMSwiZXhwIjoxNjU4Mzg1ODExLCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.NjfS5VuzX2edr10S72H4PgRcOPtQ1EFqVHLpBz5B-t4c39X1W6d-hw5Y-n0PtXAfjV-Fp3m6NrF31b_HHEESOBOijsU0Qp7prbeS1iR80xbR3Si9SXYgKXx-V-g2t_7TMZcUPezGM4weUEhv-vB3yZuO0_75ShtIP5QSPsgbXawu3gbM_z6hJ6y_k1VsVosn7raAGszsba_VY6rhjfsBY1W31x31526nVD19L947EFD48GreU3FVeJzjIlGhZJXE5rIlTXS3bUHwFBeDu4eI_jWYu2dF8lvLkCIJWsKCKRU7A1i6BTqZTNDIaUkNv8DDCWCJy6Nctb5KhJNV37aoXnJu4x3moIsGStWJmwwmUkOYIBKZ6IUm9uQMR0vWYJiAO3v3FeA2yyo5kdChKTuScTyA9cDTA59Cqv5gqeTkuGwSPTCSWocVw9FUjjVywv4j7PTVJnuz8TBI1a-Jhyyx3-azEyqwQscpVUFAPBsxlc-bBQlkh8CXkHkaf0Df5PXN6w329oRgRQm1gSVTrWCluJwXNV_5pBe5nhL4Ng8x8BvaDG32RHDjsIkXr37oLGj7tZGxBtlne045sEXWEkqSceGMKqGm_S8_hvchU-VO8dvVBQ7Klie2RALIjHwBH_8c3Q0EII1qT7lZkphln1qCRzhXjbE__favOqUE8N3FubQ',
            'Accept: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $arrResponse = json_decode($response);
        
        //print_r($arrResponse);
        if($arrResponse->result==true){
            $intraxCompanyID = $arrResponse->data;
            $this->cabang_model->setHeadOffice($headOffice,$appid);
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
            redirect("addons-placement/".$paramCode);
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
        $this->intermailer->initialize();
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