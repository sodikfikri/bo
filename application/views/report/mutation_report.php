<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?= $this->gtrans->line("Mutation Report") ?>
  </h1>
</section>
<!-- Main content -->
<section class="content">
<!-- Info boxes -->
<?= !empty($addonsAlert) ? $addonsAlert : '' ?>
<div class="row" style="margin-bottom:10px">
  <div class="col-md-4 text-center">
    <label><?= $this->gtrans->line("Effective Date Range") ?></label>
    <input type="text" id="reservation" class="form-control" placeholder="Periode" value="<?= date("m/d/Y",strtotime(date("Y-m-d")." -10 days")).' - '.date("m/d/Y") ?>">
  </div>
  <div class="col-md-4 text-center" style="display:none">
    <select name="" class="form-control" name="sArea" id="sArea">
      <option selected disabled />Area
      <?php
        foreach ($dataArea as $row) {
          echo '<option value="'.$row->area_id.'">'.ucfirst($row->area_name).'</option>';
        }
      ?>
    </select>
  </div>
  <div class="col-md-4 text-center" style="display:none">
    <select name="" class="form-control" name="sCabang" id="sCabang">
      <option selected disabled /><?= $this->gtrans->line("Branch") ?>
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
        <div class="row">
          <div class="col-md-12">
            <table class="table table-hover" width="100%" id="employee-list">
              <thead>
                <th class="text-center">No</th>
                <th class="text-center"><?= $this->gtrans->line("Effective Date") ?></th>
                <th class="text-center"><?= $this->gtrans->line("Account No") ?></th>
                <th class="text-center"><?= $this->gtrans->line("Employee Name") ?></th>
                <th class="text-center"><?= $this->gtrans->line("Location Source") ?></th>
                <th class="text-center"><?= $this->gtrans->line("Location Destination") ?></th>
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
});
function loadsBranch(area,target,selectedBranch=''){
  $(target).html("");
  $(target).append('<option value="" ><?= $this->gtrans->line("Branch") ?></option>');
  $.ajax({
    method : 'POST',
    url    : url + "load-cabang",
    data   : {area,area},
    success: function(res){
      var arrObj = jQuery.parseJSON(res);
      arrObj.forEach(function(row,index){
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
  DTcostumized = $('#employee-list').DataTable({
    ordering  : true,
    processing: true,
    serverSide: true,
    searching : false,
    processing: true,
    scrollX   : true,
    scrollCollapse: true,
    ajax: {
       url : url + "ajaxDtGetMutationReport",
       type: 'POST',
       data: function ( data ) {
                //data.sArea = $('#sArea').val();
                //data.sCabang= $('#sCabang').val();
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
  window.open("<?= base_url('report-mutation/print?') ?>"+"reservation="+reservation);
}

$('#reservation').daterangepicker({
  "maxSpan": {
        "days": 30
    },
    "maxDate": moment()
});
</script>
