<style>
.pac-container {
    z-index: 1051 !important;
}
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?= $this->gtrans->line("Request Register") ?>
  </h1>
</section>
<?php $new_arr[]= unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$_SERVER['REMOTE_ADDR'])); ?>
<!-- Main content -->
<section class="content">
<!-- Info boxes -->
<?= !empty($addonsAlert) ? $addonsAlert : '' ?>
<div class="row">
  <div class="col-md-12">
    <div class="box box-inact">
      <!-- /.box-header -->
	  <?php
		$appid = $this->session->userdata("ses_appid"); 
		if($appid=='IA01M6858F20210906256'){ ?>
		
	  <?php } ?>
      <div class="box-body">
        <div class="row">
          <div class="col-md-12">
          <div class="callout callout-warning">
            <h3><?= $this->gtrans->line("ATTENTION !") ?></h3>
            <p><?= $this->gtrans->line("Please review all data requests in accordance with the IBOSS System. ").$this->gtrans->line(" Remember to categorize each institution into relevant areas.") ?></p>
          </div>
		  <?= '<button data-toggle="tooltip" data-placement="top" title="'.$this->gtrans->line("Approve Employess").'" type="button" class="btn btn-primary" data-toggle="modal" onclick="approveAll()"><i class="fa fa-check-circle"></i> '.$this->gtrans->line("Approve Employess").'</button>' ?>
		  <div class="table table-responsive" width="100%" style="padding-top:10px">
            <?= !empty($institutionTable) ? $institutionTable : "" ?>
          </div>
          </div>
          
        </div>
      </div>
    </div>
  </div>
</div>
</section>
<div class="modal fade" id="showDetailEmployee">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><div id="show-detail-employee"></div></h4>
        </div>
        <div class="modal-body">
          <div id="list-detail-employee"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-long-arrow-left"></i> <?= $this->gtrans->line("Close") ?></button>
        </div>
        <?= form_close() ?>
      </div>
      <!-- /.modal-content -->
    </div>
  <!-- /.modal-dialog -->
  </div>
<div class="modal fade" id="frmCabang">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><b><div id="frm-text"></div></b></h4>
          <p><div id="frm-desc"></div></p>
        </div>
        <?= form_open("save-request-register",["id"=>"form-validation","class"=>"form-horizontal formInstitution"]); ?>
        <input type="hidden" name="reboot" id="reboot">
        <div class="modal-body">
          <input type="hidden" name="id" id="id">
          <div class="form-group">
            <label for="area" class="col-sm-3 control-label"><?= $this->gtrans->line("Name Area") ?> <span class="text-red">*</span></label>
            <div class="col-sm-9">
              <?= $cmbArea ?>
            </div>
          </div>
          <div class="form-group">
            <label for="branchname" class="col-sm-3 control-label"><?= $this->gtrans->line("Branch Name") ?> <span class="text-red">*</span></label>
            <div class="col-sm-9">
              <select data-validation-engine="validate[required]" name="branchname" id="branchname" class="form-control">
              </select>
            </div>
          </div>
          <div class="form-group">
            <label for="branch_institution" class="col-sm-3 control-label"><?= $this->gtrans->line("Branch Institution") ?> <span class="text-red">*</span></label>
            <div class="col-sm-9">
            <input onchange="" readonly name="branch_institution" type="text" class="form-control" id="branch_institution" placeholder="<?= $this->gtrans->line("Branch Intitution") ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="institutioncode" class="col-sm-3 control-label"><?= $this->gtrans->line("Institution Code") ?> <span class="text-red">*</span></label>
            <div class="col-sm-9">
              <input onchange="" readonly name="institutioncode" data-validation-engine="validate[required,maxSize[50],custom[onlyLetterNumberSemiSpesial]]" type="text" class="form-control" id="institutioncode" placeholder="<?= $this->gtrans->line("Institution Code") ?>">
              <div id="msg-code"></div>
            </div>
          </div>
          <div class="form-group">
            <label for="institutionname" class="col-sm-3 control-label"><?= $this->gtrans->line("Institution Name") ?> <span class="text-red">*</span></label>
            <div class="col-sm-9">
              <input onchange="" readonly name="institutionname" type="text" data-validation-engine="validate[required,maxSize[100],custom[onlyLetterNumberSemiSpesial]]" class="form-control" id="institutionname" placeholder="<?= $this->gtrans->line("Institution Name") ?>">
              <div id="msg-name"></div>
            </div>
          </div>
          <div class="form-group">
            <label for="address" class="col-sm-3 control-label"><?= $this->gtrans->line("Address") ?> <span class="text-red">*</span></label>
            <div class="col-sm-9">
              <input name="address" readonly type="text" data-validation-engine="validate[required]" class="form-control" id="address" placeholder="<?= $this->gtrans->line("Address") ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="contactnumber" class="col-sm-3 control-label"><?= $this->gtrans->line("NPWP") ?> <span class="text-red">*</span></label>
            <div class="col-sm-9">
              <input data-validation-engine="validate[custom[phone],maxSize[20]]" name="contactnumber" type="text"  class="form-control" id="contactnumber" placeholder="<?= $this->gtrans->line("NPWP") ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="description" class="col-sm-3 control-label"><?= $this->gtrans->line("Description") ?></label>
            <div class="col-sm-9">
              <textarea data-validation-engine="validate[custom[onlyLetterNumberSemiSpesial]]" readonly name="description" rows="4" cols="80" id="description" class="form-control" placeholder="<?= $this->gtrans->line("Description") ?>"></textarea>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-long-arrow-left"></i> <?= $this->gtrans->line("Cancel") ?></button>
          <button type="submit" class="btn btn-primary"><div id="txtBtnSave"></div></button>
        </div>
        <?= form_close() ?>
      </div>
      <!-- /.modal-content -->
    </div>
  <!-- /.modal-dialog -->
  </div>
<script type="text/javascript">
  var url = "<?= base_url() ?>";
  var existingTimezone = '';
  var existingDeviceBranch = '';
  stat = 0;
  function approveAll(){
	let order = []; 
    $('input[name="order-id[]"]:checked').each(function() { 
		order.push($(this).val()); 
    });
    var buyingCount = document.querySelectorAll('input[name="order-id[]"]:checked').length;
	if(buyingCount==0){
	  alert("<?= $this->gtrans->line('Please select at least one branch or institution') ?>");
	}else{
	  $.ajax({
	  method : "POST",
	  url    : url + "approveAllBranch",
	  data   : {order:order},
	  success : function(res){
		  location.reload();
		}
	  });
	}
  }
  function loadBranch(area,selectedBranch=''){
    $("#branchname").html("");
    $.ajax({
      method : 'POST',
      url    : url + "load-cabang",
      data   : {area,area},
      success: function(res){
        $("#branchname").html('<option value=""></option>');

        var arrObj = jQuery.parseJSON(res);
        arrObj.branchs.forEach(function(row,index){

          var selected = '';
          if(selectedBranch==row.id){
            selected = 'selected';
          }
          

          $("#branchname").append('<option '+selected+' value="'+row.id+'" >'+row.name+'</option>');
        });
      }
    });
  }
  function detail(encID){
    $("#loader").fadeIn(1);
	$("#show-detail-employee").html('<?= $this->gtrans->line("Show New Employee Details at The Branch") ?>');
	var formData = {
		orderId: encID
	};
	$.ajax({
        method : "POST",
        url    : url + "show-employee-request-register",
        data   : formData,
        success: function (res){
			var obj = $.parseJSON(res);
				//$("#txtBtnApprove").html('<i class="fa fa-check-circle"></i> <?= $this->gtrans->line("Approve Employees") ?>');
				//$("#txtBtnApprove").html('');
				$("#list-detail-employee").html(obj);
				$("#showDetailEmployee").modal('show');
				$("#loader").fadeOut(1);
			}
    });
  }
  function edit(id,area,cabang,code,name,address,contact,description,branchIntitution){
	var deArea   = atob(area);
    var deBranch = atob(cabang);
    existingDeviceBranch = deBranch;
    $("#area").val(deArea);

    loadBranch(deArea,deBranch);

    $("#txtBtnSave").html('<i class="fa fa-check-circle"></i> <?= $this->gtrans->line("Save Changes") ?>');
    $("#frm-text").html('<?= $this->gtrans->line("Edit Institution") ?>');
    $("#id").val(id);
    $("#institutioncode").val(atob(code));
    $("#institutionname").val(atob(name));
    $("#timezone").select2('destroy');
    $(".select2").select2({
      dropdownParent: $("#frm-text"),
      });
    $("#address").val(atob(address));
    $("#contactnumber").val(atob(contact));
    $("#description").val(atob(description));
    $("#branch_institution").val(atob(branchIntitution));
    $("#frmCabang").modal('show');
  }
  
	$(document).ready(function(){
	  $("#btn-search").click(function(){
		  //alert("clicked");
			draw_dt();
	  });
	  $("#area").change(function(){
      var area = $(this).val();
        loadBranch(area);
      });
	});
	
	function draw_dt(){
		var formData = {
		  areaid: $("#sArea").val()
		};
        $.ajax({
            method : "POST",
            url    : url + "filter-institution",
            data   : formData,
            success: function (){
				location.reload();
            }
        });
	}

  $('.select2').select2({
    dropdownParent: $("#frm-text")
  });
  
  $("#datatable").DataTable({
    responsive: true
  });
	
	$(document).ready(function() {
	  $(window).keydown(function(event){
		if(event.keyCode == 13) {
		  event.preventDefault();
		  return false;
		}
	  });
	});
</script>