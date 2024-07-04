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
    <?= $this->gtrans->line("Master Employee") ?>
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
          <div class="callout callout-info">
            <h3>UPDATE</h3>
            <p>Untuk <b>push employee</b> dari data yang ditampilkan. Centang data yang ingin di-push lalu tekan <b>Push Employee</b> </p>
            <p>Untuk <b>push employee</b> dari data dalam suatu lokasi. Pastikan filter lokasi terisi lalu tekan <b>Push Selected Filter</b> </p>
          </div>
            <?php //!empty($licenseInfo) ? $licenseInfo : '' ?>
            <?= !empty($notif) ? $notif : "" ?>
            <div id="main-msg"></div>
            <?= '<button data-toggle="tooltip" data-placement="top" title="New Employee" type="button" class="btn btn-primary" data-toggle="modal" onclick="addNew()"><i class="fa fa-pencil"></i> '.$this->gtrans->line("New Employee").'</button>' ?>
            <?= '<button data-toggle="tooltip" data-placement="top" title="Push Employee" type="button" class="btn btn-success" onclick="redistributeAll()"><i class="fa fa-long-arrow-up"></i> '.$this->gtrans->line("Push Employee").'</button>' ?>
			<?php if($this->session->userdata("ses_appid")=='IA01M6792F20210901903'){ ?>
				<?= '<button data-toggle="tooltip" data-placement="top" title="Delete Template" type="button" class="btn btn-danger" onclick="deleteTempAll()"><i class="fa fa-trash"></i> '.$this->gtrans->line("Delete Template").'</button>' ?>
			<?php } ?>
            <br><br>
            <div class="row">
              <div class="pull-right col-md-9">
                <div class="row">
                  <div class="col-md-3">
                    <select name="sArea" id="sArea" class="form-control">
                      <option value="" ><?= $this->gtrans->line("All Area") ?></option>
                      <?php
						$lsarrArea = explode("|",$this->session->userdata("ses_area"));
                        foreach ($dataArea as $row) {
						  if($this->session->userdata("ses_status")=="admin_area"){
							if(in_array($row->area_id, $lsarrArea)){
							  echo '<option value="'.$row->area_id.'">'.ucfirst($row->area_name).'</option>';
							}
						  } else {
							echo '<option value="'.$row->area_id.'">'.ucfirst($row->area_name).'</option>';
						  }
                        }
                      ?>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <select onchange="draw_dt()" name="sCabang" id="sCabang" class="form-control">
                      <option value="" ><?= $this->gtrans->line("All Branch") ?></option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <select onchange="draw_dt()" name="haveTemplate" id="haveTemplate" class="form-control">
                      <option value="" ><?= $this->gtrans->line("All Template Condition") ?></option>
                      <option value="1" ><?= $this->gtrans->line("Template Exist") ?></option>
                      <option value="2" ><?= $this->gtrans->line("Template Not Exist") ?></option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <input onchange="draw_dt()" type="text" id="strCari" name="strCari" class="form-control" placeholder="<?= $this->gtrans->line("Name (type and press enter)") ?>">
                  </div>
                  <div class="pull-right" style="padding-top:10px;padding-right:16px">
                  <button type="button" class="btn btn-primary" onclick="pushSelectedFilter()">Push Selected Filter</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <form id="frm-push" method="post">
                  <table class="table table-hover" width="100%" id="employee-list">
                    <thead>
                      <th width="10%"><input type="checkbox" id="checkAll" onclick="if(this.checked) {$(':checkbox').each(function() {this.checked = true;});}else{$(':checkbox').each(function() {this.checked = false;});}"> All Showed</th>
                      <th class="text-center"><?= $this->gtrans->line("Code") ?></th>
                      <th class="text-center"><?= $this->gtrans->line("Pic") ?></th>
                      <th class="text-center"><?= $this->gtrans->line("Name") ?></th>
                      <th class="text-center"><?= $this->gtrans->line("Join Date") ?></th>
                      <th class="text-center"><?= $this->gtrans->line("Template") ?></th>
                      <th class="text-center"><?= $this->gtrans->line("Location") ?></th>
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
			<div id="form-photo">
				<center><img src="" id="srcImg" alt="photoprofile" style="border-radius: 50%;margin:10px" width="100px" height="100px"></center>
			  <form class="" id="import-photoprofile" enctype="multipart/form-data" action="" method="post">
			  <input type="hidden" name="id" id="id_image" value="">
			  <div class="form-group">
                <label for="photoprofile" class="col-sm-3 control-label"><?= $this->gtrans->line("Photo Profile") ?> </label>
                <div class="col-sm-7">
                  <input type="file" name="photoprofile" data-validation-engine="" onchange="" id="photoprofile" class="form-control">
                  <div style="color:red"><?= $this->gtrans->line("Please fill in if you want to change photos.") ?></div>
                </div>
				<div class="col-sm-2">
                  <button type="submit" class="btn btn-primary"><i class="fa fa-refresh"></i></button>
                </div>
              </div>
			  
			  </form>
			</div>
			  
			  <?= form_open("save-employee",["id"=>"form-validation","class"=>"form-horizontal"]); ?>
          <div class="row">
            <div class="col-md-7">
              <input type="hidden" name="id" id="id" value="">
              <div class="form-group">
                <label for="accountno" class="col-sm-3 control-label"><?= $this->gtrans->line("Account No") ?> <span class="text-red">*</span></label>
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
                <label for="nickname" class="col-sm-3 control-label"><?= $this->gtrans->line("Nick Name") ?> <span class="text-red">*</span></label>
                <div class="col-sm-9">
                  <input name="nickname" type="text" data-validation-engine="validate[required,custom[onlyLetterNumberSemiSpesial],maxSize[100]]" class="form-control" id="nickname" >
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
                <label for="birthday" class="col-sm-3 control-label"><?= $this->gtrans->line("Birthday") ?> <span class="text-red">*</span></label>
                <div class="col-sm-9">
                  <input type="text" name="birthday" data-validation-engine="validate[required]" class="form-control datepicker" id="birthday" autocomplete="off">
                </div>
              </div>
              <div class="form-group">
                <label for="phone-number" class="col-sm-3 control-label"><?= $this->gtrans->line("Phone Number") ?> <span class="text-red">*</span></label>
                <div class="col-sm-9">
                  <input type="text" name="phone-number" data-validation-engine="validate[required]" class="form-control" id="phone-number" >
                </div>
              </div>
              <div class="form-group">
                <label for="email" class="col-sm-3 control-label"><?= $this->gtrans->line("Email") ?> <span class="text-red">*</span></label>
                <div class="col-sm-9">
                  <input type="text" name="email" data-validation-engine="validate[required,minSize[5],maxSize[100],custom[email]]" maxlength="100" onchange="checkEmail()" class="form-control" id="email" >
                  <div id="email-msg"></div>
                </div>
              </div>
              <div class="form-group">
                <label for="address" class="col-sm-3 control-label"><?= $this->gtrans->line("Address") ?> <span class="text-red">*</span></label>
                <div class="col-sm-9">
                  <textarea name="address" data-validation-engine="validate[required]" class="form-control" id="address"></textarea>
                </div>
              </div>
              <div class="form-group">
                <label for="intrax-pin" class="col-sm-3 control-label"><?= $this->gtrans->line("Intrax PIN") ?> <span class="text-red"></span></label>
                <div class="col-sm-9">
                  <input type="number" name="intrax-pin" data-validation-engine="validate[custom[integer],pinSize[6]]" min="0" maxlength="6" class="form-control" id="intrax-pin" placeholder="123456">
                </div>
              </div>
              <div class="form-group">
                <label for="joindate" class="col-sm-3 control-label"><?= $this->gtrans->line("Join Date") ?> <span class="text-red">*</span></label>
                <div class="col-sm-9">
                  <input name="joindate" type="text" data-validation-engine="validate[required]" class="form-control datepicker" id="joindate" autocomplete="off">
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-3 control-label">Device access user</label>
                <div class="col-sm-9">
                  <!--<input onclick="if(this.checked){setPassRequired()}else{ removePassRequired()}" name="level" id="level" type="checkbox" value="admin"> Super Admin
                  -->

                  <label for="status_user"><input type="radio" name="employee_status" onclick="removePassRequired()" id="status_user" checked value="user"> User</label>
                  <label for="status_admin"><input type="radio" name="employee_status" onclick="setPassRequired()" id="status_admin" value="admin"> Super Admin</label>
                  <input type="hidden"  name="level" id="level" value="user">
                </div>
              </div>
              <div class="form-group">
                <label for="" class="col-sm-3 control-label"><?= $this->gtrans->line("Password") ?> <span class="text-red"></span></label>
                <div class="col-sm-9">
                  <input name="password" type="text" data-validation-engine="validate[custom[onlyNumber,maxSize[9]]]" class="form-control" id="password" >
                  <div id="pwd-msg"></div>
                </div>
              </div>
			  <div class="form-group">
                <label for="method" class="col-sm-3 control-label"><?= $this->gtrans->line("Presence Method") ?> <span class="text-red"></span></label>
                <div class="col-sm-9">
                  <div class="checkbox">
                      <label><input type="checkbox" name="method[]" id="method_1" value="1"> PIN</label>
                    </div>
                    <div class="checkbox">
                      <label><input type="checkbox" name="method[]" id="method_2" value="2"> Finger Print</label>
                    </div>
                    <div class="checkbox">
                      <label><input type="checkbox" name="method[]" id="method_3" value="3"> Face Id</label>
                    </div>
					<div class="checkbox">
                      <label><input type="checkbox" name="method[]" id="method_4" value="4"> Take Picture</label>
                    </div>
                </div>
              </div>
              <div class="form-group">
                <label for="mode" class="col-sm-3 control-label"><?= $this->gtrans->line("Presence Mode") ?> <span class="text-red"></span></label>
                <div class="col-sm-9">
				  <input type="radio" name="presence_mode" onclick="" id="mode_online" checked value="online"> Online
                  &nbsp;&nbsp;
                  <input type="radio" name="presence_mode" onclick="" id="mode_offline" value="online-offline"> Online-Offline
                </div>
              </div>
			  <div class="form-group">
                <label for="location" class="col-sm-3 control-label"><?= $this->gtrans->line("Presence Location") ?> <span class="text-red"></span></label>
                <div class="col-sm-9">
                  <input type="radio" name="presence_location" onclick="" id="specific_location" checked value="1"> Specific Location
                  &nbsp;&nbsp;
				  <input type="radio" name="presence_location" onclick="" id="all_location" value="0"> All Location
                </div>
              </div>
            </div>
            <div class="col-md-5">
              <div class="form-group">
                <div class="row">
                  <div class="col-md-6">
                    <div>
                      <span class="label label-default"><?= $this->gtrans->line("Set Area") ?></span>
                    </div>
                    <?php
                      foreach ($dataArea as $row) {
						  if($this->session->userdata("ses_status")=="admin_area"){
							if(in_array($row->area_id, $lsarrArea)){
							  echo '<input id="chkarea'.$row->area_id.'" onclick="showBranch('.$row->area_id.',this.checked)" type="checkbox" name="area[]" value="'.$row->area_id.'" > '.strtoupper($row->area_name).'<br>';
							}
						  } else {
							echo '<input id="chkarea'.$row->area_id.'" onclick="showBranch('.$row->area_id.',this.checked)" type="checkbox" name="area[]" value="'.$row->area_id.'" > '.strtoupper($row->area_name).'<br>';
						  }
                        
                      }
                    ?>
                  </div>
                  <div class="col-md-6">
                    <div>
                      <span class="label label-default"><?= $this->gtrans->line("Set Branch") ?></span>
                    </div>
                    <div id="chkCabang"></div>
                  </div>
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

  <div class="modal fade" id="mdPushEmployee">
    <div class="modal-dialog modal-default">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?= $this->gtrans->line("Push Employee") ?></h4>
        </div>
        <div class="modal-body">
          <div class="row">

              <div class="pull-right">
                <div class="col-md-12">
                  <input type="text" placeholder="Search by name" id="txtSearchEmployee" onchange="searchEmployeePush(this)" class="form-control">
                </div>
              </div>

              <div class="table-responsive col-md-12" style="height: 400px;overflow: scroll;">
                <div id="ls-employee">
                </div>
              </div>

          <div class="modal-footer">
            <button type="button" onclick="redistributeAll()" class="btn btn-primary"><i class="fa  fa-long-arrow-up"></i> <?= $this->gtrans->line("Push User To Device") ?></button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  var url = "<?= base_url() ?>";
  var tempID = "";
  <?php
    $arrArea = '[';
    foreach ($dataArea as $row) {
      $arrArea .= $row->area_id.",";
    }
    $arrArea .= ']';
    echo 'var lsArea = '.$arrArea.';';
  ?>
  $(function () {
  $('[data-toggle="tooltip"]').tooltip()
})
  $('.datepicker').datepicker({
    autoclose: true
  })

  function  pushSelectedFilter(){
    var filterTemplate = $("#haveTemplate").val();
    var filterArea   = $("#sArea").val();
    var filterCabang = $("#sCabang").val();
    if(filterArea!="" && filterCabang!=""){
      $("#loader").fadeIn(1);
      $.ajax({
        method : "POST",
        url    : url + "master/employee/pushSelectedFilter",
        data   : {area:filterArea,cabang:filterCabang,filterTemplate:filterTemplate},
        success: function(result){
          if(result=="finish"){
            Swal.fire({
              position: 'center',
              icon: 'success',
              type: 'success',
              title: 'Your request is processed!',
              showConfirmButton: false,
              timer: 1500
            });
            $("#loader").fadeOut(1);
          }
        }
      });
    }else{
      Swal.fire({
        type: 'error',
        title: 'Oops...',
        text: '<?= $this->gtrans->line("You must set area and branch") ?>!' //,
        //footer: ''
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
        var arrObj = jQuery.parseJSON(res);
        arrObj.branchs.forEach(function(row,index){
          if(selectedBranch!=''){
            var selected = 'selected';
          }else{
            var selected = '';
          }
          $("#branchname").append('<option '+selected+' value="'+row.id+'" >'+row.name+'</option>');
        });
      }
    });
  }

  function loadsBranch(area,selectedBranch=''){
    $("#sCabang").html("");
    $("#sCabang").append('<option value="" ><?= $this->gtrans->line("All Branch") ?></option>');
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
          $("#sCabang").append('<option '+selected+' value="'+row.id+'" >'+row.name+'</option>');
        });
        draw_dt();
      }
    });
  }

  $(document).ready(function(){
    $("#area").change(function(){
      var area = $(this).val();
      loadBranch(area);
    });
    $("#sArea").change(function(){
      var sArea = $(this).val();
      loadsBranch(sArea);
    });
  });

  function addNew(){
	
    lsArea.forEach(function(row){
      $("#chkarea"+row).prop('checked',false);
    });
    $("#txtBtnSave").html('<i class="fa fa-check-circle"></i> <?= $this->gtrans->line("Save") ?>');
    $("#frm-text").html('<?= $this->gtrans->line("Add New Employee") ?>');
    $("#form-photo").hide();
    $("#id").val("");
    $("#photoprofile").val("");
    $("#accountno").val("");
    $("#fullname").val("");
    $("#nickname").val("");
    $("#birthday").val("");
    $("#phone-number").val("");
    $("#email").val("");
    $("#address").val("");
    $("#intrax-pin").val("");
    $("#joindate").val("");
    $("#password").val("");
    $("#chkCabang").html("");
    $("#level").prop("checked",false);
    $("#method_1").prop("checked",false);
    $("#method_2").prop("checked",false);
    $("#method_3").prop("checked",false);
    $("#method_4").prop("checked",false);
    $("#frmEmployee").modal('show');
    $("#level").val("user");
    $("#status_user").prop("checked",true);
  }

  function edit(id){
    lsArea.forEach(function(row){
      $("#chkarea"+row).prop('checked',false);
    });
    $("#chkCabang").html("");
    $("#txtBtnSave").html('<i class="fa fa-check-circle"></i> <?= $this->gtrans->line("Save Changes") ?>');
    $("#frm-text").html('<?= $this->gtrans->line("Edit Employee") ?>');
    $("#id").val(id);
    $("#form-photo").show();
    $("#method_1").prop("checked",false);
    $("#method_2").prop("checked",false);
    $("#method_3").prop("checked",false);
    $("#method_4").prop("checked",false);
	$("#mode_online").prop("checked",true);
	$("#mode_offline").prop("checked",false);
	$("#specific_location").prop("checked",true);
	$("#all_location").prop("checked",false);
    $("#level").val("user");
    $("#password").val("");
    $("#status_user").prop("checked",true);
    $.ajax({
      method : "POST",
      url    : url + "get-employee-edit",
      data   : {id:id},
      success: function(res){
        var obj = jQuery.parseJSON(res);
        $("#id").val(id);
        $("#id_image").val(id);
        $("#accountno").val(obj.accountno);
        $("#fullname").val(obj.fullname);
        $("#nickname").val(obj.nickname);
        $("#joindate").val(obj.joindate);
        $("#password").val(obj.password);
        $("#gender").val(obj.gender);
        $("#birthday").val(obj.birthday);
        $("#phone-number").val(obj.phone_number);
        $("#email").val(obj.email);
        $("#address").val(obj.address);
        $("#intrax-pin").val(obj.intrax_pin);
		if(!obj.employee_photo){
			if (obj.gender=='male') {
				$('#srcImg').attr('src', 'https://inact.interactiveholic.net/bo/img_employee/img_avatar_boy.png');
			} else {
				$('#srcImg').attr('src', 'https://inact.interactiveholic.net/bo/img_employee/img_avatar_girl.png');
			}
		} else {
			$('#srcImg').attr('src', 'https://inact.interactiveholic.net/bo/sys_upload/user_profile/'+obj.employee_photo);
		}
		
        var arrMethod = obj.presence_method;
		if(arrMethod!=null){
			var method = arrMethod.split('|');
			for(var i=0; i< method.length; i++){
				if(method[i]==1){
					$("#method_1").prop("checked",true);
				}else if(method[i]==2){
					$("#method_2").prop("checked",true);
				}else if(method[i]==3){
					$("#method_3").prop("checked",true);
				}else if(method[i]==4){
					$("#method_4").prop("checked",true);
				}
			}
		}
        
		if(obj.presence_mode=="online"){
          $("#mode_online").prop("checked",true);
        }else{
          $("#mode_offline").prop("checked",true);
        }
		
		if(obj.presence_location==1){
          $("#specific_location").prop("checked",true);
        }else{
          $("#all_location").prop("checked",true);
        }

        if(obj.level=="admin"){
          $("#level").val("admin");
          $("#status_admin").prop("checked",true);
        }else{
          $("#level").val("user");
          $("#status_user").prop("checked",true);
        }
		
		obj.area.forEach(function(row){
		<?php if($this->session->userdata("ses_status")=="admin_area"){ ?>
		let lsArea = <?php echo json_encode($lsarrArea); ?>;
		if (lsArea.includes(row)){
			$("#chkarea"+row).prop('checked', true);
			//$("#radius"+row).val(obj.radius+row);
			showBranch(row,true,obj.cabang,obj.radius);
		}
		<?php } else { ?>
          $("#chkarea"+row).prop('checked', true);
		  //$("#radius"+row).val(obj.radius+row);
          showBranch(row,true,obj.cabang,obj.radius);
		<?php } ?>
		});
		
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
  function showBranch(areaID,chkChecked,checkedComponens = [],checkedRadius){
	$("#loader").fadeIn(1);
    if(chkChecked==true){
      $.ajax({
        method : 'POST',
        url    : url + "load-cabang",
        data   : {area:areaID},
        success: function(res){
          var arrObj = jQuery.parseJSON(res);
		  var i=0;
		  $("#chkCabang").append('<strong id="area'+areaID+'">'+arrObj.areaname+'</strong>');
				arrObj.branchs.forEach(function(row,index){
				if(checkedComponens.indexOf(row.id)>=0){
				  var checked = 'checked';
				  var radius = checkedRadius[i];
				  i++;
				}else{
				  var checked = '';
				  var radius = 20;
				}
				$("#chkCabang").append('<div id="cabang'+row.id+'"><input '+checked+' id="checkboxcabang'+row.id+'" type="checkbox" name="cabang[]" value="'+areaID+'.'+row.id+'" > '+row.name+' <br><label for="" style="font-weight: normal">radius: <input placeholder="radius" style="width:100px;border: none;border-bottom: 2px solid black;text-align:center" id="fieldradius'+row.id+'" value="'+radius+'" min="20" required type="number" name="radius'+row.id+'"></label> m</div>');
				});
          
			  
          
		  $("#loader").fadeOut(1);
        }
      });
      //console.log('Checked!');
    }else{
      //console.log('NotChecked!');
      $("#area"+areaID).remove();
      $.ajax({
        method : 'POST',
        url    : url + "load-cabang",
        data   : {area:areaID},
        success: function(res){
          var arrObj = jQuery.parseJSON(res);
          arrObj.branchs.forEach(function(row,index){
            $("#cabang"+row.id).remove();

          });
		  $("#loader").fadeOut(1);
        }
      });
    }
	
	
  }

  function switchLicense(checkedStatus,employeeID){
    if(checkedStatus==true){
      var status = 'active';
    }else{
      var status = 'inactive';
    }

    $.ajax({
      method : "POST",
      url    : url + "employee-switch-license",
      data   : {employee:employeeID,status:status},
      success: function(res){
        if(res=="failed"){
          alert('<?= $this->gtrans->line("You have no employee slot") ?>!');
          $('#toggleSwitch'+employeeID).click();
        }
      }
    });
  }
  var selectedResign;
  function setResign(idEmp){
    selectedResign = idEmp;
    $("#frmResign").modal("show");
  }

  function submitResign(){
    var dateresign = $("#dateresign").val();
    $.ajax({
      method : "POST",
      url    : url + "employee-resign",
      data   : {id:selectedResign,dateresign:dateresign},
      success: function(res){
        if(res=="ok"){
          alert("<?= $this->gtrans->line("Resign Employee Complete") ?>");
          draw_dt();
          selectedResign = "";
          $("#dateresign").val("");
          $("#frmResign").modal("hide");
        }
      }
    });
  }
  function checkEmail(){
    $("#loader").fadeIn(1);
    var email = $("#email").val();
    $.ajax({
      method : "POST",
      url : url + "checkEmailNoExist",
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
  function redistribute(type){
    $("#loader").fadeIn(1);
    $.ajax({
      method : "POST",
      url    : url + "employee-redistribute",
      data   : {id:tempID},
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
        }else{
          $("#loader").fadeOut(1);
          alert("Runtime Error!");
        }
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
         url : url + "ajax-get-employee-data",
         type: 'POST',
         data: function ( data ) {
                  data.sArea = $("#sArea").val()
                  data.sCabang = $("#sCabang").val()
                  data.strCari = $("#strCari").val()
                  data.haveTemplate = $("#haveTemplate").val();
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
          var branchCount = 0;
          objArr.forEach(function(row,index){
            if(row.name=="cabang[]"){
              branchCount += 1;
            }
          });
          if(branchCount==0){
            Swal.fire({
              type: 'error',
              title: 'Oops...',
              text: '<?= $this->gtrans->line("Please select at least one branch") ?>!' //,
              //footer: ''
            });
          }else{
            $.ajax({
              method : "POST",
              url    : url + "save-employee",
              data   : $("#form-validation").serialize(),
              success: function(res){
                var response = jQuery.parseJSON(res);
                if(response.response=="ok"){
                  $("#fullname").val("");
                  $("#nickname").val("");
				  $("#birthday").val("");
				  $("#phone-number").val("");
				  $("#email").val("");
				  $("#address").val("");
				  $("#intrax-pin").val("");
                  $("#joindate").val("");
                  $("#chkCabang").html("");
				  $("#msg").html('');
				  $("#email-msg").html('');
                  lsArea.forEach(function(row){
                    $("#chkarea"+row).prop('checked',false);
                  });
				  $("#method_1").prop("checked",false);
				  $("#method_2").prop("checked",false);
				  $("#method_3").prop("checked",false);
				  $("#method_4").prop("checked",false);
				  
				  $("#mode_online").prop("checked",true);
				  $("#mode_offline").prop("checked",false);
				  
				  $("#specific_location").prop("checked",true);
				  $("#all_location").prop("checked",false);
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
              }
            });
          }
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
        window.open(url + 'delete-employee/' + idemployee,'_self');
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
  
  $('#import-photoprofile').submit(function(e){ 
  e.preventDefault();
	  $.ajax({
		url:url +'import-photoprofile', //URL submit
		type:"post",
		data:new FormData(this),
		processData:false,
		contentType:false,
		cache:false,
		//async:false,
		success: function(data){
            $("#photoprofile").val("");
			alert("Success update photo profile.");
			$("#frmEmployee").modal('hide');
		}
	  });
	});

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
