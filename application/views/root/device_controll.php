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
</style>
<section class="content-header">
    <h1>
      Device Control
    </h1>
</section>
<section class="content">
<div class="row">
  <div class="col-md-12">
    <div class="box box-inact" style="padding-bottom:50px">
      <div class="box-body">
        <div class="row">
          <div class="col-md-7">
            <div class="row">
              <div class="col-md-3">
                <select name="company_type" id="company_type" class="form-control" onchange="loadData();filterCompany()">
                  <option value="" >All</option>
                  <option value="yes" >Real Company</option>
                  <option value="no" >Developer</option>
                </select>
              </div>
              <div class="col-md-3">
                <select name="company" id="company" class="form-control" onchange="loadData()">
                  <option value="" >Company</option>
                </select>
              </div>
              <div class="col-md-3">
                <input type="text" onchange="loadData()" name="sn" id="sn" class="form-control" placeholder="Serial Number" >
              </div>
            </div>
          </div>
          <div class="col-md-5 text-right">
            <button class="btn btn-success" onclick="reboot()">REBOOT</button>
            <button class="btn btn-primary" onclick="showResponseCodePanel()">Change Response Version</button>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div id="data-table"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</section>
<div class="modal fade" id="panel-response-code">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Default Modal</h4>
      </div>
      <div class="modal-body">
        <select name="codeId" id="codeId" class="form-control">
          <option value="">Response Code Version</option>
          <?php 
            foreach ($responseCode->result() as $row) {
              echo '<option value="'.$row->id.'">'.$row->code_name.'</option>';
            }
          ?>
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" onclick="setResponse()">Set Response Code</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $("#datatable").DataTable();
  var url = "<?= base_url() ?>";
  var selected = [];
  function selectItem(target){
    //console.log(target.value);
    if(target.checked==true){
      selected.push(target.value);
    }else{
      removeItem(target.value);
    }
    //console.log(selected);
  }
  
  function removeItem(arrayRemove){
    const index = selected.indexOf(arrayRemove);
    if (index > -1) {
      selected.splice(index, 1);
    }
  }

  function showResponseCodePanel(){
    $("#panel-response-code").modal('show');
  }

  function filterCompany(){
    var company_type = $("#company_type").val();
    $("#company").html('<option value="" >Company</option>');

    $.ajax({
      method : "POST",
      url    : url + "root/device_control/getCompanyList",
      data   : {company_type:company_type},
      success: function(res){
        var obj = jQuery.parseJSON(res);
        obj.forEach(function(row,index){
          $("#company").append('<option value="'+row.id+'" >'+row.name+'</option>');          
        });
      }
    });
  }

  function suspendSN(deviceid){
    Swal.fire({
      title: 'Are you sure want to suspend the device?',
      text: "",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, suspend it!'
    }).then((result) => {
      if (result.value) {
        $.ajax({
          type : "POST",
          url  : url + "suspendDevice",
          data : {deviceid:deviceid},
          success : function(res){
            if (res=="OK") {
              Swal.fire({
                //position: 'top-end',
                type : 'success',
                icon : 'success',
                title: 'The device has been successfuly suspended!',
                showConfirmButton: false,
                timer: 1500
              });
              loadData();
            }
          }
        });
      }
    })
  }

  function undoSuspendSN(deviceid){
    Swal.fire({
      title: 'Are you sure want to unlock the suspended device?',
      text: "",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, unlock it!'
    }).then((result) => {
      if (result.value) {
        $.ajax({
          type : "POST",
          url  : url + "unlockSuspendedDevice",
          data : {deviceid:deviceid},
          success : function(res){
            if (res=="OK") {
              Swal.fire({
                //position: 'top-end',
                type : 'success',
                icon : 'success',
                title: 'Unlock Device Successful!',
                showConfirmButton: false,
                timer: 1500
              });
              loadData();
            }else if(res=="unavailable"){
              Swal.fire({
                type  : 'error',
                icon  : 'error',
                title : 'Oops...',
                text  : 'Unlock Unavailable!'
                //footer: '<a href>Why do I have this issue?</a>'
              });
            }
          }
        });
      }
    })
  }

  function loadData(){
    var company_type = $("#company_type").val();
    var company      = $("#company").val();
    var sn           = $("#sn").val();
    $.ajax({
      method : "POST",
      url    : url + "root/device_control/loadData",
      data   : {company_type:company_type,company:company,sn:sn},
      success: function(res){
        $("#data-table").html(jQuery.parseJSON(res));
      }
    });
  }
  loadData();

  function setResponse(){
    var codeId = $("#codeId").val();
    $.ajax({
      method  : "POST",
      url     : url + "root/device_control/setResponse",
      data    : {deviceId:selected,codeId:codeId},
      success : function(res){
        if(res=="OK"){
          loadData();
          selected = [];
          $("#panel-response-code").modal('hide');
        }
      }
    });
  }
  
  function reboot(){
    $.ajax({
      method  : "POST",
      url     : url + "root/device_control/setReboot",
      data    : {deviceId:selected},
      success : function(res){
        if(res=="OK"){
          loadData();
          selected = [];
          Swal.fire({
            position: 'center',
            icon: 'success',
            type: 'success',
            title: 'Your request is processed!',
            showConfirmButton: false,
            timer: 1500
          });
        }
      }
    });
  }

  function getDeviceLog(deviceID){
    $.ajax({
      method  : "POST",
      url     : url + "root/device_control/cmdPullLog",
      data    : {deviceID:deviceID},
      success : function(res){
        if(res=="OK"){
          loadData();
          $("#panel-response-code").modal('hide');
        }
      }
    });
  }
</script>