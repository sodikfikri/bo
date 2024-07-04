<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?= $this->gtrans->line("User Activity Report") ?>
  </h1>
</section>
<!-- Main content -->
<section class="content">
<!-- Info boxes -->
<?= !empty($addonsAlert) ? $addonsAlert : '' ?>
<div class="row" style="margin-bottom:10px">
  <div class="col-md-4 text-center">
    <label><?= $this->gtrans->line("Activity Date") ?></label>
    <input type="text" id="reservation" class="form-control" placeholder="Periode" value="<?= date("m/d/Y",strtotime(date("Y-m-d")." -10 days")).' - '.date("m/d/Y") ?>">
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
        <div class="row">
          <div class="col-md-12">
            <table class="table table-hover" width="100%" id="employee-list">
              <thead>
                <th class="text-center">No</th>
                <th class="text-center"><?= $this->gtrans->line("User Name") ?></th>
                <th class="text-center"><?= $this->gtrans->line("Time Activity") ?></th>
                <th class="text-center"><?= $this->gtrans->line("Menu") ?></th>
                <th class="text-center"><?= $this->gtrans->line("Action") ?></th>
              </thead>
              <tbody>

              </tbody>
            </table>
            <button onclick="printReport()" class="btn btn-success"><i class="fa fa-print"></i> <?= $this->gtrans->line("Print") ?></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</section>
<script type="text/javascript">
var url = "<?= base_url() ?>";
$(document).ready(function(){
  $("#reservation").change(function(){
    draw_dt();
  });
});
$(function () {
  DTcostumized = $('#employee-list').DataTable({
    ordering  : true,
    processing: true,
    serverSide: true,
    searching : false,
    processing: true,
    scrollX   : true,
    scrollCollapse: true,
    ajax: {
       url : url + "ajaxDtGetUserActivityReport",
       type: 'POST',
       data: function ( data ) {
                data.reservation = $('#reservation').val();
       }
    }
  });

});
function draw_dt(){
  DTcostumized.ajax.reload();
}

function printReport(){
  var reservation = ($('#reservation').val()!=null) ? $('#reservation').val() : "";
  window.open("<?= base_url('report-user-activity/print?') ?>"+"reservation="+reservation);
}

$('#reservation').daterangepicker({
  "maxSpan": {
        "days": 15
    },
    "maxDate": moment()
});
</script>
