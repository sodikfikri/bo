<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?= $this->gtrans->line("Unamed Log") ?>
  </h1>
</section>
<!-- Main content -->
<section class="content">
<!-- Info boxes -->
<?= !empty($addonsAlert) ? $addonsAlert : '' ?>
<div class="row" style="margin-bottom:10px">
  <div class="col-md-3 text-center">
    <label><?= $this->gtrans->line("Periode") ?></label>
    <input type="text" id="reservation" class="form-control" placeholder="Periode" value="<?= date("m/d/Y",strtotime(date("Y-m-d")." -10 days")).' - '.date("m/d/Y") ?>">
  </div>
  <div class="col-md-2 text-center">
    <label>Area</label>
    <select  name="" class="form-control" name="sArea" id="sArea">
      <option selected value="" /><?= $this->gtrans->line("Select One Area") ?>
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
    <label><?= $this->gtrans->line("Use Masker") ?></label>
    <select  name="" class="form-control" name="use-masker" id="use-masker">
      <option selected value="all" /><?= $this->gtrans->line("All") ?>
      <option value="1" /><?= $this->gtrans->line("Yes") ?>
      <option value="0" /><?= $this->gtrans->line("No") ?>
    </select>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="box box-inact">
      <div class="box-header with-border">
        <h3 class="box-title"></h3>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <table class="table table-hover" width="100%" id="history-log">
          <thead>
            <th class="text-center"></th>
            <th class="text-center">SN</th>
            <th class="text-center"><?= $this->gtrans->line("Datetime") ?></th>
            <th class="text-center"><?= $this->gtrans->line("Absen Code") ?></th>
            <th class="text-center"><?= $this->gtrans->line("Verify Code") ?></th>
            <th class="text-center"><?= $this->gtrans->line("Temperature") ?></th>
            <th class="text-center"><?= $this->gtrans->line("Use Masker") ?></th>
          </thead>
          <tbody>
          </tbody>
        </table>
        <!--<button onclick="printReport()" class="btn btn-success"><i class="fa fa-print"></i> <?= $this->gtrans->line("Print") ?></button>-->
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
    draw_dt();
  });
  $("#sCabang").change(function(){
    draw_dt();
  });
  $("#reservation").change(function(){
    draw_dt();
  });
  $("#use-masker").change(function(){
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
    "searching": false,
    ajax: {
       url : url + "report/unamed_log/loadDataLog",
       type: 'POST',
       data: function ( data ) {
                data.reservation = $('#reservation').val();
                data.sArea = $('#sArea').val();
                data.sCabang= $('#sCabang').val();
                data.sUseMasker = $("#use-masker").val();
       }
    }
  });
  $('#reservation').daterangepicker({
    "maxSpan": {
          "days": 31
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
  var searchTerm = $('.dataTables_filter input').val();
  window.open("<?= base_url('report/unamed_log/print?reservation=') ?>"+reservation+"&area="+area+"&cabang="+cabang+'&term='+searchTerm);
}
</script>
