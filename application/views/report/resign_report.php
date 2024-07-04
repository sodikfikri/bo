<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?= $this->gtrans->line("Employee Resign Report") ?>
  </h1>
</section>
<!-- Main content -->
<section class="content">
<!-- Info boxes -->
<div class="row">
  <div class="col-md-12">
    <div class="box box-inact">
      <div class="box-body">
        <div class="row">
          <div class="col-md-12" style="margin-bottom: 20px">
            <form action="" id="frmSearch" method="GET">
             <div class="row">
               <div class="col-md-3">
                 <input type="text" onchange="draw_dt()" name="periode" class="form-control" id="reservation">
               </div>
               <div class="col-md-3">
                 <input type="text" onchange="draw_dt()" name="search-text" class="form-control" id="search-text" placeholder="<?= $this->gtrans->line("Search By Name") ?>">
               </div>
               <div class="col-md-1">
                 <button type="button" onclick="draw_dt()" class="btn btn-primary"><i class="fa fa-search"></i> <?= $this->gtrans->line("Apply Filter")?></button>
               </div>
             </div>
            </form>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">

            <table class="table table-bordered" width="100%" id="datatable">
              <thead>
                <tr>
                  <th>No</th>
                  <th><?= $this->gtrans->line("Account No") ?></th>
                  <th><?= $this->gtrans->line("Full Name") ?></th>
                  <th><?= $this->gtrans->line("Date Resign") ?></th>
                  <th><?= $this->gtrans->line("Employee Location") ?></th>
                </tr>
              </thead>
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
  DTcostumized = $('#datatable').DataTable({
    ordering  : true,
      processing: true,
      serverSide: true,
      processing: true,
      scrollX   : true,
      scrollCollapse: true,
      searching :false,
      ajax: {
        url : url + "report/employee_resign/getData",
        type: 'POST',
        data: function ( data ) {
          data.periode = $("#reservation").val(),
          data.term    = $("#search-text").val()
        }
      }
  });
});

function draw_dt(){
  DTcostumized.ajax.reload();
}
function printReport(){
  var reservation = $("#reservation").val();
  var term        = $("#search-text").val();
  window.open("<?= base_url('report-employee-resign/print?reservation=') ?>"+reservation+"&term="+term);

}
$('#reservation').daterangepicker({
  "maxSpan": {
        "days": 30
    },
    "maxDate": moment()
});
function search(){
  $('#frmSearch').submit();
}
</script>
