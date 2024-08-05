<?php
/**
 *
 */
class Error_log extends CI_Controller
{
  private $key = "iaDevErrorAccess";

  function __construct()
  {
    parent::__construct();
  }

  function download($key,$date=""){
    if ($key===$this->key) {
      if($date==""){
        $date = date("Y-m-d");
      }

      $this->load->library("intermailer");
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
                        Hello Developer,</p>
                        <p style="font-family: Roboto;
                        font-size: 15px;
                        font-weight: normal;
                        font-style: normal;
                        font-stretch: normal;
                        line-height: 1.67;
                        letter-spacing: normal;
                        text-align: left;
                        color: rgba(0, 0, 0, 0.7);
                        ">Jumpa Lagi<b></b></p>
                        <p style="font-family: Roboto;
                        font-size: 15px;
                        font-weight: normal;
                        font-style: normal;
                        font-stretch: normal;
                        line-height: 1.67;
                        letter-spacing: normal;
                        text-align: left;
                        color: rgba(0, 0, 0, 0.7);
                        ">Ini adalah file error log yang ditemukan pada tanggal '.$date.'
                        .</p>

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
                        color: rgba(0, 0, 0, 0.7);">InAct Robot System,</p>
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
      $fileName = "log-".$date.".php";
      $logPath  = FCPATH.DIRECTORY_SEPARATOR."application".DIRECTORY_SEPARATOR."logs".DIRECTORY_SEPARATOR.$fileName;
      if(file_exists($logPath)){
        $this->load->library("intermailer");
        $this->intermailer->initialize();
        // $this->intermailer->initialize_allin();

        $this->intermailer->to([
            "ronyelkahfidev@gmail.com"=>"Rony Dev",
            "iwan@interactive.co.id"=>"Iwan Sasmiko",
            "deddy.ddr@interactive.co.id" => "Deddy Ddr"
        ]);

        $this->intermailer->set_content("Inact Error Log",$body_msg,"");
        $this->intermailer->set_attachment($logPath,"Error-log-".$date);
        $res = $this->intermailer->send();
        if($res==true){
          echo "Email sent";
        }else{
          echo "SMTP Error";
        }
      }else{
        echo "Error log file was not found on ".$date;
      }
    }else{
      echo "invalid key!";
    }
  }
}
