<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?= $this->gtrans->line("Employee Report") ?>
  </h1>
</section>
<!-- Main content -->
<section class="content">
<!-- Info boxes -->
<?= !empty($addonsAlert) ? $addonsAlert : '' ?>
<div class="row" style="margin-bottom:10px">

  <div class="col-md-4 text-center">
    <label>Area</label>
    <select name="" class="form-control" name="sArea" id="sArea">
      <option selected value=""/><?= $this->gtrans->line("All") ?>
      <?php
        foreach ($dataArea as $row) {
          echo '<option value="'.$row->area_id.'">'.ucfirst($row->area_name).'</option>';
        }
      ?>
    </select>
  </div>
  <div class="col-md-4 text-center">
    <label><?= $this->gtrans->line("Branch") ?></label>
    <select name="" class="form-control" name="sCabang" id="sCabang">
      <option selected disabled value=""/><?= $this->gtrans->line("All") ?>
    </select>
  </div>
</div>
<div class="row">
  <div class="col-md-12">
    <div class="box box-inact">
      <div class="box-header with-border text-right">
        <span class="label label-info"><?= $this->gtrans->line("Showing Active Employee Only") ?></span>
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <div class="row">
          <div class="col-md-12">
            <table class="table table-hover" width="100%" id="employee-list">
              <thead>
                <th width="10%" class="text-center">No</th>
                <th class="text-center"><?= $this->gtrans->line("Account No") ?></th>
                <th class="text-center"><?= $this->gtrans->line("Name") ?></th>
                <th class="text-center"><?= $this->gtrans->line("Active Date") ?></th>
                <th class="text-center"><?= $this->gtrans->line("Active Location") ?></th>
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
  DTcostumized = $('#employee-list').DataTable({
    ordering  : true,
    processing: true,
    serverSide: true,
    processing: true,
    scrollX   : true,
    scrollCollapse: true,
    "language": {
      "search": "<?= $this->gtrans->line("Filter Name") ?>:"
    },
    ajax: {
       url : url + "ajaxDtGetEmployeeReport",
       type: 'POST',
       data: function ( data ) {
                data.sArea = $('#sArea').val();
                data.sCabang= $('#sCabang').val();
       }
    }
  });

});
function draw_dt(){
  DTcostumized.ajax.reload();
}

function printReport(){
  var area  = ($('#sArea').val()!=null) ? $('#sArea').val() : "";
  var cabang = ($('#sCabang').val()!=null) ? $('#sCabang').val() : "";
  window.open("<?= base_url('report-employee/print?') ?>"+"area="+area+"&cabang="+cabang);

}

</script>
