<!-- Content Header (Page header) -->
<style>
.detail-content{

}

.detail-label{
  margin: 0px;
  font-size: 8pt;
  font-weight: bold;
}

.icon-gold{
  position: relative;
  top:7px;
  left:5px;
  color:#ffc400;
  cursor:pointer;
}
</style>

<section class="content-header">
  <h1>
    <?= $this->gtrans->line("Register Prospective Employees") ?>
  </h1>
</section>
<!-- Main content -->
<section class="content">
<!-- Info boxes -->
<?= !empty($addonsAlert) ? $addonsAlert : '' ?>

<div class="row">
  <div class="col-md-12">
    <div class="box box-inact">
      <!-- /.box-header -->
      <div class="box-body">
        <div class="row">
          <div class="col-md-12">
          <div class="callout callout-warning">
            <h3>WARNING !</h3>
            <p>Mohon periksa kembali NIP karyawan yang akan didaftarkan untuk memastikan keakuratan data.</p>
          </div>
            <?php //!empty($licenseInfo) ? $licenseInfo : '' ?>
            <?= !empty($notif) ? $notif : "" ?>
            <div id="main-msg"></div>
            <?= '<button data-toggle="tooltip" data-placement="top" title="New Employee" type="button" class="btn btn-primary" data-toggle="modal" onclick="addNew()"><i class="fa fa-pencil"></i> '.$this->gtrans->line("New Employee").'</button>' ?>
            <?= '<button data-toggle="tooltip" data-placement="top" title="Import Employee" type="button" class="btn btn-primary" data-toggle="modal" onclick="importNew()"><i class="fa fa-file-excel-o"></i> '.$this->gtrans->line("Import Employee").'</button>' ?>
            <?= '<button data-toggle="tooltip" data-placement="top" title="Pay" type="button" class="btn btn-primary" data-toggle="modal" onclick="buyAddons(\''.$this->uri->segment(2).'\')"><i class="fa fa-money"></i> '.$this->gtrans->line("Pay").'</button>' ?>
            <br><br>
            <div class="row">
              <div class="pull-right col-md-9">
                <div class="row">
                  <div class="col-md-6">
                    <select onchange="draw_dt()" name="sCabang" id="sCabang" class="form-control">
                      <option value="" ><?= $this->gtrans->line("All Institution") ?></option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <input onchange="draw_dt()" type="text" id="strCari" name="strCari" class="form-control" placeholder="<?= $this->gtrans->line("Name (type and press enter)") ?>">
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <form id="frm-push" method="post">
                  <table class="table table-hover" width="100%" id="employee-list">
                    <thead>
                      <th width="10%"><input type="checkbox" id="checkAll" onclick="if(this.checked) {$(':checkbox').each(function() {this.checked = true;});}else{$(':checkbox').each(function() {this.checked = false;});}"> All</th>
                      <th class="text-center"><?= $this->gtrans->line("NIP") ?></th>
                      <th class="text-center"><?= $this->gtrans->line("Name") ?></th>
                      <th class="text-center"><?= $this->gtrans->line("Gender") ?></th>
                      <th class="text-center"><?= $this->gtrans->line("Position") ?></th>
                      <th class="text-center"><?= $this->gtrans->line("PIN Intrax") ?></th>
                      <th class="text-center"><?= $this->gtrans->line("Join Date") ?></th>
                      <th class="text-center"><?= $this->gtrans->line("Data Simpeg") ?></th>
                      <th class="text-center"><?= $this->gtrans->line("Option") ?></th>
                    </thead>
                    <tbody>

                    </tbody>
                  </table>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</section>
<div class="modal fade" id="frmEmployee">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><div id="frm-text"></div></h4>
        </div>
        
        <div class="modal-body">
			  
		<?= form_open("save-employee",["id"=>"form-validation","class"=>"form-horizontal"]); ?>
          <div class="row">
            <div class="col-md-12">
              <input type="hidden" name="id" id="id" value="">
              <div class="form-group">
                <label for="accountno" class="col-sm-3 control-label"><?= $this->gtrans->line("NIP") ?> <span class="text-red">*</span></label>
                <div class="col-sm-9">
                  <input type="text" name="accountno" data-validation-engine="validate[required,custom[onlyLetterNumber],maxSize[20]]" onchange="checkAccountNo()" id="accountno" class="form-control">
                  <div id="msg"></div>
                </div>
              </div>
              <div class="form-group">
                <label for="fullname" class="col-sm-3 control-label"><?= $this->gtrans->line("Full Name") ?> <span class="text-red">*</span></label>
                <div class="col-sm-9">
                  <input type="text" name="fullname" data-validation-engine="validate[required,custom[onlyLetterNumberSemiSpesial],maxSize[100]]" id="fullname" class="form-control">
                </div>
              </div>
              <div class="form-group">
                <label for="position" class="col-sm-3 control-label"><?= $this->gtrans->line("Position") ?> <span class="text-red">*</span></label>
                <div class="col-sm-9">
                  <input name="position" type="text" data-validation-engine="validate[required,custom[onlyLetterNumberSemiSpesial],maxSize[100]]" class="form-control" id="position" >
                </div>
              </div>
              <div class="form-group">
                <label for="gender" class="col-sm-3 control-label"><?= $this->gtrans->line("Gender") ?> <span class="text-red">*</span></label>
                <div class="col-sm-9">
                  <select name="gender" data-validation-engine="validate[required]" class="form-control" id="gender" >
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label for="phone-number" class="col-sm-3 control-label"><?= $this->gtrans->line("Phone Number") ?> <span class="text-red">*</span></label>
                <div class="col-sm-9">
                  <input type="text" name="phone-number" data-validation-engine="validate[required]" class="form-control" id="phone-number" >
                </div>
              </div>
              <div class="form-group">
                <label for="email" class="col-sm-3 control-label"><?= $this->gtrans->line("Email Active") ?> <span class="text-red">*</span></label>
                <div class="col-sm-9">
                  <input type="text" name="email" data-validation-engine="validate[required,minSize[5],maxSize[100],custom[email]]" maxlength="100" onchange="checkEmail()" class="form-control" id="email" >
                  <div id="email-msg"></div>
                </div>
              </div>
              <div class="form-group">
                <label for="intrax-pin" class="col-sm-3 control-label"><?= $this->gtrans->line("Intrax PIN") ?> <span class="text-red">*</span></label>
                <div class="col-sm-9">
                  <input type="number" name="intrax-pin" min="0" maxlength="6" class="form-control" id="intrax-pin" placeholder="123456">
                  <!-- <input type="number" name="intrax-pin" data-validation-engine="validate[required,custom[integer],pinSize[6]]" min="0" maxlength="6" class="form-control" id="intrax-pin" placeholder="123456"> -->
                </div>
              </div>
            </div>
          </div>
        </div>
		

        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-long-arrow-left"></i> <?= $this->gtrans->line("Cancel") ?></button>
          <button type="submit" class="btn btn-primary"><div id="txtBtnSave"></div></button>
        </div>
		<!-- </form> -->
        <?= form_close() ?>
      </div>
      <!-- /.modal-content -->
    </div>
  <!-- /.modal-dialog -->
  </div>
 <div class="modal fade" id="frmImportEmployee">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><div id="frm-text-import"></div></h4>
        </div>
        
        <div class="modal-body">
			  
			<form class="" id="import-prospective-employee" enctype="multipart/form-data" action="" method="post">
			  <ol style="line-height:300%;">
				<li>
				  <?= $this->gtrans->line("Use the Excel template file that we provide") ?>
				  <br>
				  <a href="<?= base_url("sys_upload/template-import-master-karyawan-intrax.xls") ?>" class="download-button" ><?= $this->gtrans->line("Download Template Excel") ?></a>
				</li>
				<li>
				  <?= $this->gtrans->line("Enter your Employee data into the excel template file, make sure it is in the right format, and save it") ?>.
				</li>
				<li>
				  <?= $this->gtrans->line("Select the excel template that has been filled in") ?>. <span class="text-red text-bold">(File type .xls, maximum size 2 MiB)</span>
				  <br>
				  <div class="form-group">
					<input class="download-button" required type="file" accept=".xls" id="file-employee" name="file">
				  </div>
				  <div class="col-md-12">
					<div class="progress-bar" id="employee-progress" style="width:0%"><span id="employee-progress-text">0%</span></div>
				  </div>
				</li>
				<li>
				  <?= $this->gtrans->line("Press the import button below") ?>.
				</li>
			  </ol>
        </div>
		

        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-long-arrow-left"></i> <?= $this->gtrans->line("Cancel") ?></button>
          <button type="submit" class="btn btn-primary"><div id="txtBtnImport"></div></button>
        </div>
		<!-- </form> -->
		</form>
      </div>
      <!-- /.modal-content -->
    </div>
  <!-- /.modal-dialog -->
  </div>
  <div class="modal fade" id="frmResign">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"></h4>
          </div>
          <form action="" id="form-resign-validation" method="post">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="control-label"><?= $this->gtrans->line("Set Resign Date") ?> <span class="text-red">*</span></label>
                  <input name="dateresign" type="text" data-validation-engine="validate[required]" class="form-control datepicker" id="dateresign" >
                </div>
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-long-arrow-left"></i> <?= $this->gtrans->line("Cancel") ?></button>
            <button type="submit" class="btn btn-primary"><?= $this->gtrans->line("Submit Resign") ?> </button>
          </div>
        </form>
        </div>
        <!-- /.modal-content -->
      </div>
    <!-- /.modal-dialog -->
    </div>
    <div class="modal fade" id="mdDetail">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title"><?= $this->gtrans->line("Detail") ?></h4>
          </div>
            <div class="modal-body">
              <div class="row">
                <div id="detail-content"></div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" onclick="redistribute()" class="btn btn-primary"><i class="fa  fa-long-arrow-up"></i> <?= $this->gtrans->line("Push User To Device") ?></button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="modal-msg">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <div class="text-blue info-title"><i class="fa fa-info"></i> <?= $this->gtrans->line("Import Result") ?></div>
        <div class="row" style="margin-top:50px">
          <div class="col-md-3 text-blue text-center" style="font-size:18pt">
            <i class="fa fa-dot-circle-o"></i><br><span class="text-skipped"></span> Skipped
          </div>
          <div class="col-md-3 text-green text-center" style="font-size:18pt">
            <i class="fa  fa-check-circle-o"></i><br><span class="text-inserted"></span> Inserted
          </div>
          <div class="col-md-3 text-yellow text-center" style="font-size:18pt">
            <i class="fa  fa-refresh"></i><br><span class="text-updated"></span> Updated
          </div>
          <div class="col-md-3 text-red text-center" style="font-size:18pt">
            <i class="fa  fa-close"></i><br><span class="text-error"></span> Error
          </div>
        </div>
        <div class="row" style="margin-top:10px">
          <div class="col-md-12">
            <div id="error-list"></div>
          </div>
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal"><?= $this->gtrans->line("Close") ?></button>
      </div>
    </div>
  </div>
</div>

<form id="orderPage" target="_blank" method="POST" action="">
  <input type="hidden" name="cEmail" value="<?= $this->session->userdata("ses_email") ?>">
  <input type="hidden" name="cPassw" value="<?= $this->session->userdata("ses_encpassword") ?>">
  <input type="hidden" name="cPosisi" value="owner">
</form>

<script type="text/javascript">
  var url = "<?= base_url() ?>";
  var tempID = "";
  var mybillingurl = "<?= MYBILLING_LINK ?>";
  var appid = "<?= $this->encryption_org->encode($this->session->userdata("ses_appid")); ?>";
  <?php
    $arrArea = '[';
    foreach ($dataArea as $row) {
      $arrArea .= $row->area_id.",";
    }
    $arrArea .= ']';
    echo 'var lsArea = '.$arrArea.';';
  ?>
  
  function buyAddons(pluginsid){
	let employeeid = []; 
    $('input[name="employee-id[]"]:checked').each(function() { 
		employeeid.push($(this).val()); 
    });
    // return console.log(employeeid);
    var buyingCount = document.querySelectorAll('input[name="employee-id[]"]:checked').length;
	if(buyingCount==0){
	  alert("<?= $this->gtrans->line('Please select at least one employee') ?>");
	}else{
	  $.ajax({
	  method : "POST",
	  url    : url + "addonsPrepareDataForCheckOut",
	  data   : {pluginsid:pluginsid,employeeid:employeeid,buyingCount:buyingCount},
	  success : function(res){
		  window.location.href = url+"checkout-cart/"+res+"/"+appid;
		}, 
    error: function(e) {
      console.log('error: ', e);
    }
	  });
	}
  }
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })
  $('.datepicker').datepicker({
    autoclose: true
  })
  
  $('#import-prospective-employee').submit(function(e){
	  e.preventDefault();
	  $("#employee-progress").css("width","0%");
	  $("#employee-progress-text").html("0%");

	  $.ajax({
		url:url +'import-employee-intrax/<?= $this->uri->segment(2); ?>', //URL submit
		type:"post",
		data:new FormData(this),
		processData:false,
		contentType:false,
		beforeSend : function(){
		  $("#employee-progress").css("width","10%");
		  $("#employee-progress-text").html("10%");
		},
		cache:false,
		//async:false,
		success: function(data){
		  var result = jQuery.parseJSON(data);
		  var error  = arraylength(result.error);
		  if(result.file_error==""){
			for (var i = 0; i <= 100; i++) {
			  $("#employee-progress").css("width",i+"%");
			  $("#employee-progress-text").html(i+"%");
			  if(i==100){
				if(error==0){
				  $("#file-employee").val("");
				}
				var strError = '';
				result.error.forEach(function(row){
				  strError += '<p class="text-red"> '+row+'</p>';
				});

				$(".text-skipped").html(result.skipped);
				$(".text-inserted").html(result.inserted);
				$(".text-updated").html(result.updated);
				$(".text-error").html(error);
				$("#error-list").html(strError);
				$("#modal-msg").modal('show');
			  }
			}
			DTcostumized.ajax.reload();
		  }else{
			Swal.fire({
			  type: 'error',
			  title: 'Oops...',
			  text: result.file_error //,
			  //footer: ''
			});
			$("#employee-progress").css("width","0%");
			$("#employee-progress-text").html("0%");
		  }
		}
	  });
	});
	
	function dataCorrection(id,fullname){
		$.ajax({
		  method : "POST",
		  url    : url + "accept-prospective-employees/<?= $this->uri->segment(2); ?>",
		  data   : {id:id,fullname:fullname},
		  success: function(res){
			var response = jQuery.parseJSON(res);
			if(response.response=="ok"){
			  draw_dt();
			  $("#main-msg").html('<div class="callout callout-'+response.msg.type+'">'+
							   '<h4>'+response.msg.header+'!</h4>'+
							 '<p>'+response.msg.msg+'</p>'+
							 '</div>');
			}else if (response.response=="error") {
			  Swal.fire({
				type : 'error',
				icon: 'error',
				title: 'Oops...',
				text: response.msg
			  });
			  $("#accountno").val("");
			}
		  }
		});
	}
	
	$('#modal-msg').on('hidden.bs.modal', function () {
	  $("#employee-progress").css("width","0%");
	  $("#employee-progress-text").html("0%");
	})


  function addNew(){
	
    lsArea.forEach(function(row){
      $("#chkarea"+row).prop('checked',false);
    });
    $("#txtBtnSave").html('<i class="fa fa-check-circle"></i> <?= $this->gtrans->line("Save") ?>');
    $("#frm-text").html('<?= $this->gtrans->line("Add New Employee") ?>');
    $("#form-photo").hide();
    $("#id").val("");
    $("#accountno").val("");
    $("#fullname").val("");
    $("#position").val("");
    $("#phone-number").val("");
    $("#email").val("");
    $("#intrax-pin").val("");
    $("#frmEmployee").modal('show');
    $("#level").val("user");
    $("#status_user").prop("checked",true);
  }
  
  function importNew(){
    $("#txtBtnImport").html('<i class="fa fa-check-circle"></i> <?= $this->gtrans->line("Import") ?>');
    $("#frm-text-import").html('<?= $this->gtrans->line("Import New Employee") ?>');
    $("#frmImportEmployee").modal('show');
  }
  
  function cekValid(){
	$('#employee-list').DataTable({
      ordering  : false,
      processing: true,
      serverSide: true,
      processing: true,
      scrollX   : true,
      scrollCollapse: true,
      searching :false,
      ajax: {
         url : url + "ajax-get-employee-prospective/<?= $this->uri->segment(2); ?>",
         type: 'POST',
         data: function ( data ) {
                  data.sCabang = $("#sCabang").val()
                  data.strCari = $("#strCari").val();
         }
      }
    });
  }

  function edit(id){
    // return console.log('masuk ke sini');
    $("#txtBtnSave").html('<i class="fa fa-check-circle"></i> <?= $this->gtrans->line("Save Changes") ?>');
    $("#frm-text").html('<?= $this->gtrans->line("Edit Employee") ?>');
    $("#id").val(id);
    $.ajax({
      method : "POST",
      url    : url + "get-prospective_employees-edit",
      data   : {id:id},
      success: function(res){
        var obj = jQuery.parseJSON(res);
        console.log(obj);
        $("#id").val(id);
        $("#id_image").val(id);
        $("#accountno").val(obj.accountno);
        $("#fullname").val(obj.fullname);
        $("#position").val(obj.position);
        $("#gender").val(obj.gender);
        $("#phone-number").val(obj.phone_number);
        $("#email").val(obj.email);
        $("#intrax-pin").val(obj.intrax_pin);
		
        $("#frmEmployee").modal('show');
      }
    });
  }

  function loadTableEmployee(){

    var sArea   = $("#sArea").val();
    var sCabang = $("#sCabang").val();
    var strCari = $("#strCari").val();
    /*
    $.ajax({
      type : "POST",
      url  : url + "load-table-employee",
      data : {sArea:sArea,sCabang:sCabang,strCari:strCari},
      success : function(res){
        var obj = jQuery.parseJSON(res);
        $("#table-employee").html(obj);
      }
    });
    */
  }
  //loadTableEmployee();
  function checkEmail(){
    $("#loader").fadeIn(1);
    var email = $("#email").val();
    $.ajax({
      method : "POST",
      url : url + "checkEmailEmployees",
      data : {email_addr:email},
      success : function(res){
        if(res=="yes"){
          $("#email-msg").html('<p class="text-red"><?= $this->gtrans->line("Email was used by deleted or existing data") ?></p>');
          $("#email").val("");
        }else if(res=="no"){
          $("#email-msg").html('<p class="text-green"><?= $this->gtrans->line("Available") ?></p>');
        }
        $("#loader").fadeOut(1);
      }
    });
  }
  function checkAccountNo(){
    $("#loader").fadeIn(1);
    var accountno = $("#accountno").val();
    $.ajax({
      method : "POST",
      url : url + "checkAccountNoExist",
      data : {no_account:accountno},
      success : function(res){
        if(res=="yes"){
          $("#msg").html('<p class="text-red"><?= $this->gtrans->line("Code was used by deleted or existing data") ?></p>');
          $("#accountno").val("");
        }else if(res=="no"){
          $("#msg").html('<p class="text-green"><?= $this->gtrans->line("Available") ?></p>');
        }
        $("#loader").fadeOut(1);
      }
    });
  }

  function showDetail(id){
    $("#loader").fadeIn(1);
    tempID = id;
    $.ajax({
      method : "POST",
      url    : url + "employee-get-detail",
      data   : {id:id},
      success: function(res){
        var obj = $.parseJSON(res);
        $("#detail-content").html(obj);
        $("#mdDetail").modal('show');
        $("#loader").fadeOut(1);
      }
    });
  }

  $(function () {
	
    DTcostumized = $('#employee-list').DataTable({
      ordering  : false,
      processing: true,
      serverSide: true,
      processing: true,
      scrollX   : true,
      scrollCollapse: true,
      searching :false,
      ajax: {
         url : url + "ajax-get-employee-prospective/<?= $this->uri->segment(2); ?>",
         type: 'POST',
         data: function ( data ) {
                  data.sCabang = $("#sCabang").val()
                  data.strCari = $("#strCari").val();
         }
      }
    });
  });
  function draw_dt(){
    DTcostumized.ajax.reload();
  }
  $("#form-resign-validation").validationEngine();
  stat = 0;
  jQuery("#form-resign-validation").validationEngine('attach', {
    onValidationComplete: function(form, status){
      if(status==true){
        stat = stat + 1;
        if(stat%2==0){
          submitResign();
          return false;
        }
      }
    }
  });
  stat1 = 0;
  jQuery("#form-validation").validationEngine('attach', {
    onValidationComplete: function(form, status){
      if(status==true){
        stat1 = stat1 + 1;
        if(stat1%2==0){
          //submitResign();
          var objArr = $("#form-validation").serializeArray();
            $.ajax({
              method : "POST",
              url    : url + "save-prospective-employees-temp/<?= $this->uri->segment(2); ?>",
              data   : $("#form-validation").serialize(),
              success: function(res){
                var response = jQuery.parseJSON(res);
                if(response.response=="ok"){
                  $("#fullname").val("");
                  $("#position").val("");
                  $("#phone-number").val("");
                  $("#email").val("");
                  $("#intrax-pin").val("");
                  $("#msg").html('');
                  $("#email-msg").html('');
                  $("#frmEmployee").modal('hide');
                  draw_dt();
                  $("#main-msg").html('<div class="callout callout-'+response.msg.type+'">'+
                            	   '<h4>'+response.msg.header+'!</h4>'+
                                 '<p>'+response.msg.msg+'</p>'+
                                 '</div>');
                }else if (response.response=="error") {
                  Swal.fire({
                    type : 'error',
                    icon: 'error',
                    title: 'Oops...',
                    text: response.msg
                  });
                  $("#accountno").val("");
                }
              },
              error: function(e) {
                console.log(e);
              }
            });
          return false;
        }
      }
    }
  });
  

  function delEmployee(idemployee){
    Swal.fire({
      title: '<?= $this->gtrans->line("Are you sure") ?>?',
      text: "<?= $this->gtrans->line("You won't be able to use employee code from this employee again.") ?> <?= $this->gtrans->line("If you want to change data, use edit button.") ?>",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: '<?= $this->gtrans->line("Yes, delete it") ?>!'
    }).then((result) => {
      if (result.value) {
        window.open(url + 'delete-employee-intrax/' + idemployee + '/<?= $this->uri->segment(2); ?>','_self');
      }
    })
  }
  function setPassRequired(){
    $("#password").attr("data-validation-engine","validate[required,custom[onlyNumber]]");
    $("#pwd-msg").html('<p style="color:red">Password required when user set as Super Admin!</p>');
    $("#level").val("admin");
  }
  function removePassRequired(){
    $("#password").attr("data-validation-engine","validate[custom[onlyNumber]]");
    $("#pwd-msg").html('');
    $("#level").val("user");
  }

  function showPushpanel(){
    searchEmployeePush();
    $("#txtSearchEmployee").val("");
    $("#mdPushEmployee").modal('show');
  }
  
  function searchEmployeePush(patern=''){

    $.ajax({
      method  : "POST",
      data    : {strCari:patern.value},
      url     : url + "load-table-employee",
      success : function(res){
        var obj = jQuery.parseJSON(res);
        $("#ls-employee").html(obj);
      }
    });
  }

  function redistributeAll(){
    var frmdata = $("#frm-push").serialize();
    $("#loader").fadeIn(1);
    $.ajax({
      method : "POST",
      url    : url + "employee-redistribute-all",
      data   : frmdata,
      success: function(res){
        if(res=="finish"){
          Swal.fire({
            position: 'center',
            icon: 'success',
            type: 'success',
            title: 'Your request is processed!',
            showConfirmButton: false,
            timer: 1500
          });
          $("#loader").fadeOut(1);
          $(':checkbox').checked = false;
        }else{
          Swal.fire({
            type: 'error',
            title: 'Oops...',
            text: '<?= $this->gtrans->line("You must set at least one employee") ?>!' //,
            //footer: ''
          });
          $("#loader").fadeOut(1);
        }
      }
    });
  }
  
  function deleteTempAll(){
    var frmdata = $("#frm-push").serialize();
    $("#loader").fadeIn(1);
    $.ajax({
      method : "POST",
      url    : url + "employee-delete-all",
      data   : frmdata,
      success: function(res){
        if(res=="finish"){
          Swal.fire({
            position: 'center',
            icon: 'success',
            type: 'success',
            title: 'Your request is processed!',
            showConfirmButton: false,
            timer: 1500
          });
          $("#loader").fadeOut(1);
          $(':checkbox').checked = false;
        }else{
          Swal.fire({
            type: 'error',
            title: 'Oops...',
            text: '<?= $this->gtrans->line("You must set at least one employee") ?>!' //,
            //footer: ''
          });
          $("#loader").fadeOut(1);
        }
      }
    });
  }

  $(document).ready(function(){
    $("#frm-push").submit(function(){
      return false;
    })
  });
  var countButton = 1;
  $(document).ready(function(){
    $("#strCari").keyup(function(e){
      if(e.which == 13) {
        countButton += 1;
        if(countButton%2==1){
          draw_dt();
        }
      }
    });
  });
</script>
