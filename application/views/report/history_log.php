<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?= $this->gtrans->line("History Log") ?>
  </h1>
</section>
<!-- Main content -->
<section class="content">
<!-- Info boxes -->
<?= !empty($addonsAlert) ? $addonsAlert : '' ?>
<div class="row" style="margin-bottom:10px">
  <div class="col-md-3 text-center">
    <label><?= $this->gtrans->line("Periode") ?></label>
    <input type="text" id="reservation" class="form-control" placeholder="Periode" value="<?= date("m/d/Y",strtotime(date("Y-m-d")." -30 days")).' - '.date("m/d/Y") ?>">
  </div>
  <div class="col-md-2 text-center">
    <label>Area</label>
    <select  name="" class="form-control" name="sArea" id="sArea">
      <option selected value="" /><?= $this->gtrans->line("All") ?>
      <?php
        foreach ($dataArea as $row) {
          echo '<option value="'.$row->area_id.'">'.ucfirst($row->area_name).'</option>';
        }
      ?>
    </select>
  </div>
  <div class="col-md-2 text-center">
    <label><?= $this->gtrans->line("Branch") ?></label>
    <select  name="" class="form-control" name="sCabang" id="sCabang">
      <option selected value="" /><?= $this->gtrans->line("All") ?>
    </select>
  </div>
  <div class="col-md-2 text-center">
    <label><?= $this->gtrans->line("Employee Name") ?></label>
    <input type="text"  class="form-control" name="sName" id="sName">
  </div>
  <div class="col-md-2 text-center">
    <label><?= $this->gtrans->line("temperature") ?></label>
    <select type="text"  class="form-control" name="temperature" id="temperature">
      <option value="">All temperature</option>
      <option value="<38">&lt;38</option>
      <option value=">=38">&ge;38</option>
    </select>
  </div>
  <div class="col-md-1" style="padding-top:25px">
    <button class="btn btn-primary" id="btn-search"><?= $this->gtrans->line("Search") ?></button>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="box box-inact">
      <div class="box-header with-border">
        <!-- <div class="alert alert-success alert-dismissible">
          <p><i class="fa fa-info"></i> Kami hanya menampilkan data absensi mulai kamis 16 Desember 2021. Tenang, Data tidak hilang. Kami hanya sedang melakukan pengoptimalan sistem. Terima kasih!</p>
        </div> -->
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <table class="table table-hover" width="100%" id="history-log">
          <thead>
            <th class="text-center"></th>
            <th class="text-center"><?= $this->gtrans->line('Location') ?> / SN</th>
            <th class="text-center"><?= $this->gtrans->line("Account No") ?></th>
            <th class="text-center"><?= $this->gtrans->line("Name") ?></th>
            <th class="text-center"><?= $this->gtrans->line("Datetime") ?></th>
            <th class="text-center"><?= $this->gtrans->line("Absen Code") ?></th>
            <th class="text-center"><?= $this->gtrans->line("Verify Code") ?></th>
            <th class="text-center"><?= $this->gtrans->line("Temperature") ?></th>
            <th class="text-center"><?= $this->gtrans->line("Use Masker") ?></th>
          </thead>
          <tbody>
          </tbody>
        </table>
        <button onclick="printReport()" class="btn btn-success"><i class="fa fa-print"></i> <?= $this->gtrans->line("Print") ?></button>
      </div>

    </div>
  </div>
</div>
</section>
<script type="text/javascript">
var url = "<?= base_url() ?>";
$(document).ready(function(){
  $("#sArea").change(function(){
    var sArea = $(this).val();
    loadsBranch(sArea,"#sCabang");
  });
  $("#btn-search").click(function(){
    draw_dt();
  });
});

function loadsBranch(area,target,selectedBranch=''){
$(target).html("");
$(target).append('<option value="" ><?= $this->gtrans->line("All") ?></option>');
$.ajax({
  method : 'POST',
  url    : url + "load-cabang",
  data   : {area,area},
  success: function(res){
    var arrObj = jQuery.parseJSON(res);
    arrObj.branchs.forEach(function(row,index){
      if(selectedBranch!=''){
        var selected = 'selected';
      }else{
        var selected = '';
      }
      $(target).append('<option '+selected+' value="'+row.id+'" >'+row.name+'</option>');
    });
  }
});
}

$(function () {
  DTcostumized = $('#history-log').DataTable({
    ordering  : true,
    processing: true,
    serverSide: true,
    processing: true,
    scrollX   : true,
    scrollCollapse: true,
    searching :false,
    //"language": {
    //  "search": "<?= $this->gtrans->line("Filter Name or Account No") ?>:"
    //},
    ajax: {
       //url : url + "ajaxDtGetHistoryLog",
       url : url + "report/history_log/loadDataFromFinalTable",
       type: 'POST',
       data: function ( data ) {
                data.reservation = $('#reservation').val();
                data.sArea  = $('#sArea').val();
                data.sCabang= $('#sCabang').val();
                data.sName  = $('#sName').val();
                data.temperature  = $('#temperature').val();

       },
       timeout: 120000
    }
  });

  $('#reservation').daterangepicker({
    "maxSpan": {
          "days": 30
      },
      "maxDate": moment()
  });
});

function draw_dt(){
  DTcostumized.ajax.reload();
}

function printReport(){
  var reservation  = $('#reservation').val();
  var area  = $('#sArea').val();
  var cabang = $('#sCabang').val();
  var temperature = $('#temperature').val();
  if(temperature!=""){
    temperature = btoa(temperature);
  }
  var searchTerm = $('#sName').val();;//$('.dataTables_filter input').val();
  window.open("<?= base_url('report/history_log/reportPrintFromFinalTable?reservation=') ?>"+reservation+"&area="+area+"&cabang="+cabang+'&temperature='+temperature+'&term='+searchTerm);
}

</script>
