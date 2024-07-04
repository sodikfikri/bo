<?php

if($type=="pdf"){
  ob_start();
}

if ($type=="excel") {
  header('Content-Type: application/vnd.ms-excel');
  header('Content-Disposition: attachment;filename="'.str_replace(" ","-",$title).'.xls"');
  header('Cache-Control: max-age=0');
}

?>

<!DOCTYPE HTML>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="shortcut icon" href="<?php echo base_url('asset/image/favicon64.png') ?>" media="all"/>
    <link rel="shortcut icon" href="<?php echo base_url('asset/template/bower_components/font-awesome/css/font-awesome.min.css') ?>" media="all"/>
    
      <title>Laporan</title>

      <style type="text/css">
        body {
          /*background: rgb(204,204,204); */
        }

        page[size="A4"] {
          background: white;
          width: 290mm;
          height: 210mm;
          display: block;
          margin: 0 auto;
          margin-bottom: 0.5cm;

          /*box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);*/
        }

        @media print {
          body, page[size="A4"] {
            margin: 0;
            box-shadow: 0;
          }
          #btnPrint{
            display: none;
          }
        }

        .print,.print tr,.print td,.print th{
          border-collapse: collapse;
          border: 1px solid black;
          font-size: 12pt;
          width: 100%;
          table-layout: fixed;
        }
        .table-print,.table-print tr,.table-print td{
          border-collapse: collapse;
          border: 1px solid black;
          font-size: 12pt;
          width: 100%;
          table-layout: fixed;
        }
        .table-print th{
          border: 1px solid black;
        }
        td{
          padding: 3px;
        }
        .table-content{
              font-size: 10px;
        }
      </style>
    </head>
    <body>
      <h2><?= !empty($title) ? $title : "" ?></h2>
      <p><?= !empty($subtitle) ? $subtitle : "" ?></p>
      <?= $tabel ?>
    </body>
    <script type="text/javascript">
      <?= ($type=="print") ? 'window.print()' : '' ?>
    </script>
</html>

<?php
if($type=="pdf"){

  $html = ob_get_clean();
  $this->pdf->setHtml($html);
  $this->pdf->generate_stream(str_replace(" ","-",$title));

}
?>
