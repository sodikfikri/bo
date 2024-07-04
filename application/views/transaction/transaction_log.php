<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?= $this->gtrans->line('Transaction Log') ?>
  </h1>
</section>
<!-- Main content -->
<section class="content">
<!-- Info boxes -->
<?= !empty($addonsAlert) ? $addonsAlert : '' ?>
<div class="row">
  <div class="col-md-12">
    <div class="box box-inact">
      <div class="box-header with-border">
      </div>
      <!-- /.box-header -->
      <div class="box-body">
        <div class="row">
          <div class="col-md-12">
            <div class="row" style="margin-bottom:10px">
              <div class="col-md-4 text-center">
                <input type="text" placeholder="Date" name="selected-date" id="daterangepicker" class="form-control ">
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div id="transaction-log"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</section>
<script type="text/javascript">
var url = "<?= base_url() ?>";
$('#daterangepicker').daterangepicker({
  "maxSpan": {
      "days": 15
  },
  "maxDate": moment()
});
$(document).ready(function(){
  $("#daterangepicker").change(function(){
    loadTransactionLog();
  });
  loadTransactionLog();
});

function loadTransactionLog(){
  var selRange= $("#daterangepicker").val();
  $.ajax({
    method : "POST",
    url    : url + "transaction-log-load-transaction",
    data   : {selRange:selRange},
    success: function(res){
      var obj = $.parseJSON(res);
      $("#transaction-log").html(obj);
    }
  });
}

function loadsBranch(area,target,selectedBranch=''){
  $(target).html("");
  $(target).append('<option value="" ><?= $this->gtrans->line('Branch') ?></option>');
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


</script>
