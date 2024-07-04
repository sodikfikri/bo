<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
<style type="text/css">
  .help-title{
    text-align: center;
    font-size: 30px;
    color: #707070;
  }
  .component-title{
    text-align: center;
    font-size: 25px;
    font-weight: bold;
    color: #707070;
  }
  .component-description{
    text-align: center;
    font-size: 18px;
    color: #707070;
    padding: 0px 65px 0px 65px;
  }
  .image-icons{
    height: 110px;
  }
  .vertical-margin{
    margin-top: 5px;
    margin-bottom: 5px; 
  }
</style>
<section class="content-header">
    <h1>
      Device Monitor
    </h1>
</section>
<section class="content">
<div class="row">
  <div class="col-md-12">
    <div class="box box-inact" style="padding-bottom:50px">
      <div class="box-body">
        <div class="row">
          <div class="col-md-12">
            <div class="pull-right">
              <i class="fa fa-bar-chart" onclick="monitorOpen()" id="search-btn" style="cursor:pointer"></i>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3 vertical-margin">
            <input name="periode" class="form-control" id="reservation">
          </div>
          <div class="col-md-2 vertical-margin">
            <select name="company" id="company" class="form-control select2">
              <option value="" />Company
              <?php 
                foreach ($dataCompany->result() as $row) {
                  echo '<option value="'.$row->appid.'" /> '.$row->company_name.' ('.$row->appid.')';
                }
              ?>
            </select>
          </div>
          <div class="col-md-2 vertical-margin">
            <input type="text" name="SN" id="serial-number" class="form-control" placeholder="Serial Number">
          </div>
          <div class="col-md-2 vertical-margin">
            <input type="text" name="pattern" id="pattern" class="form-control" placeholder="Pattern on post data">
          </div>
          <div class="col-md-2 vertical-margin">
            <button onclick="searchShipment()" class="btn btn-primary btn-block">Show</button>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="table-responsive">
              <table class="table table-bordered table-hover" width="100%">
                <thead>
                  <tr>
                    <th>datetime</th>
                    <th>SN</th>
                    <th>Appid</th>
                    <th>Endpoint</th>
                    <th>Method</th>
                    <th>Data Received</th>
                  </tr>
                </thead>
                <tbody id="table-data">
                  
                </tbody>
              </table>
            </div>
          </div>
        </div>
        
      </div>
    </div>
  </div>
</div>
</section>

<script type="text/javascript">
 var url = "<?= base_url() ?>";
 $(function () {
  'use strict';
  // Option
  $('#reservation').daterangepicker({
    "maxSpan": {
        "days": 15
    },
    "maxDate": moment()
  });
  $('.select2').select2();
});

function searchShipment(){
  var reservation = $("#reservation").val();
  var company     = $("#company").val();
  var SN          = $("#serial-number").val();
  var pattern     = $("#pattern").val();
  $("#table-data").html('<tr><td colspan="6" class="text-center">Processing...</td></tr>');
  
  $.ajax({
    method : "POST",
    url    : url + "root/device_monitor/searchShipment",
    data   : {reservation:reservation,company:company,SN:SN,pattern:pattern},
    success: function(res){
      $("#table-data").html('');
      var arrOutput = jQuery.parseJSON(res);

      if(arrOutput.length>0){
        arrOutput.forEach(function(row,index){
          $("#table-data").append('<tr>'+
            '<td>'+row.datetime+'</td>'+
            '<td>'+row.SN+'</td>'+
            '<td>'+row.appid+'</td>'+
            '<td>'+row.endpoint+'</td>'+
            '<td>'+row.method+'</td>'+
            '<td><textarea class="form-control">'+row.post+'</textarea></td>'+
            '</tr>');
        });
      }else{
        $("#table-data").html('<tr><td colspan="6" class="text-center">No Data Found!</td></tr>');
      }
    }
  });
}

function monitorOpen(){
  window.open(url + "root/device_monitor","_self");
}

</script>