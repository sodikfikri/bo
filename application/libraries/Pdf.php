<?php defined("BASEPATH") OR exit("No direct script access allowed");

require_once "./vendor/dompdf/dompdf/src/Autoloader.php";
use Dompdf\Dompdf;

Class Pdf{
  private $paperSize;
  private $orientation;
  private $html;

  public function setSizeOrientation($paperSize,$orientation){
    $this->paperSize   = $paperSize;
    $this->orientation = $orientation;
  }

  public function setHtml($html){
    $this->html = $html;
  }

  public function generate_stream($fileName){
    $dompdf = new Dompdf();
    $dompdf->loadHtml($this->html);
    $dompdf->setpaper($this->paperSize,$this->orientation);
    $dompdf->render();

    $dompdf->stream($fileName,array('Attachment'=>0));
  }

  public function generate(){
    $dompdf = new Dompdf();
    $dompdf->loadHtml($this->html);
    $dompdf->setpaper($this->paperSize,$this->orientation);
    $dompdf->render();

    return $dompdf->output();
  }

}
