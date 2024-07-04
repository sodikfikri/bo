<!-- Content Header (Page header) -->
<style>
.has-feedback {
  position:relative;
}

.has-feedback .form-control {
  padding-right:42.5px;
}

.form-control-feedback {
  position:absolute;
  top:0px;
  right:2px;
  z-index:2;
  display:block;
  width:34px;
  height:34px;
  line-height:34px;
  text-align:center;
  cursor:pointer;
  pointer-events:initial;
}

.form-header{
  border-bottom: 1px solid #90a4ae;
  margin-bottom: 20px;
}

</style>
<section class="content-header">
  <h1>
    <?= $this->gtrans->line('Add User') ?>
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
        <?php echo validation_errors(); ?>
        <form class="form-horizontal" id="form-validation" method="post" action="">
          <div class="row">
              <div class="col-md-6">
                <div class="form-header">
                  <h4> <small> <?= $this->gtrans->line('General Info') ?></small></h4>
                </div>
                <div class="form-group">
                  <label for="name" class="col-sm-4 control-label"> <?= $this->gtrans->line('Full Name') ?><span class="text-red">*</span></label>
                  <div class="col-sm-8">
                    <input data-validation-engine="validate[required,maxSize[100],custom[onlyLetterNumberSemiSpesial]]" value="<?= !empty($data_edit) ? $data_edit->user_fullname : set_value('username') ?>" name="name" type="text" class="form-control" id="name" >
                  </div>
                </div>
                <div class="form-group">
                  <label for="email" class="col-sm-4 control-label">Email <span class="text-red">*</span></label>
                  <div class="col-sm-8">
                    <input onchange="checkEmail('email','notif-email','<?= !empty($this->uri->segment(2)) ? $this->uri->segment(2) :"0"?>')" data-validation-engine="validate[required,maxSize[100],custom[email]]" value="<?= !empty($data_edit) ? $data_edit->user_emailaddr : set_value('email') ?>" name="email" type="text" class="form-control" id="email" >
                    <div id="notif-email"></div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="phone" class="col-sm-4 control-label"> <?= $this->gtrans->line('Phone Number') ?></label>
                  <div class="col-sm-8">
                    <input onchange="checkPhone('phone','notif-phone','<?= !empty($this->uri->segment(2)) ? $this->uri->segment(2) :"0"?>')" data-validation-engine="validate[custom[phone],maxSize[20]]" value="<?= !empty($data_edit) ? $data_edit->user_phone : set_value('phone') ?>" name="phone" type="text" class="form-control" id="phone" >
                    <div id="notif-phone"></div>
                  </div>
                </div>
                <?php
                if(empty($data_edit)){
                ?>
                <div class="form-group has-feedback field-loginform-password required">
                  <label for="password" class="col-sm-4 control-label"> <?= $this->gtrans->line('Password') ?><span class="text-red">*</span></label>
                  <div class="col-sm-8">
                    <input data-validation-engine="validate[required,minSize[8],maxSize[50]]" name="password" type="password" class="form-control" id="password" >
                    <i class="fa fa-eye form-control-feedback"></i>
                  </div>
                </div>

                <div class="form-group has-feedback field-loginform-password required">
                  <label for="confirmpassword" class="col-sm-4 control-label"> <?= $this->gtrans->line('Confirm Password') ?><span class="text-red">*</span></label>
                  <div class="col-sm-8">
                    <input data-validation-engine="validate[required,minSize[8],maxSize[50]]" name="confirmpassword" type="password" class="form-control" id="confirmpassword" >
                    <i class="fa fa-eye form-control-feedback"></i>
                  </div>
                </div>
                <div id="msg-password" style="text-align:center"></div>
                <?php
                }
                ?>
				<div class="form-group">
                  <label for="email" class="col-sm-4 control-label">Status User<span class="text-red">*</span></label>
                  <div class="col-sm-8">
                    <label for="super_admin"><input type="radio" name="user_status" onclick="removeAccess()" id="super_admin" checked value="super_admin">  Super Admin</label>
					<label for="admin_area"><input type="radio" name="user_status" onclick="setAccess()" id="admin_area" value="admin_area"> Admin (Employee Area)</label>
					<input type="hidden"  name="level" id="level" value="super_admin">
					<div id="sts-msg"></div>
                  </div>
                </div>
				<div class="form-group" id="setArea">
                  <label for="email" class="col-sm-4 control-label">Setting Area</label>
                  <div class="col-sm-8">
                    <?php
						if(!empty($data_edit)){
							$arrArea = explode("|",$data_edit->iauser_area_id);
						} else {
							$arrArea = "";
						}
                      foreach ($dataArea as $row) {
						$chkArea = "";
						if(in_array($row->area_id, $arrArea)){
							$chkArea = "checked";
						}
                        echo '<label for="chkarea'.$row->area_id.'"><input id="chkarea'.$row->area_id.'" type="checkbox" name="area[]" value="'.$row->area_id.'" '.$chkArea.'> '.strtoupper($row->area_name).'</label><br>';
                      }
                    ?>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-header">
                  <h4> <small> <?= $this->gtrans->line('Access') ?></small></h4>
                </div>
				<div class="col-sm-12" id="setAccessSuperAdmin">
                <?= !empty($str_list_menu) ? $str_list_menu : "" ?>
				</div>
				<div class="col-sm-12" id="setAccessAdminArea">
                <?= !empty($str_list_menu) ? $str_list_menu_area : "" ?>
				</div>
              </div>
          </div>
          <div class="row">
            <div class="box-footer text-center" >
              <a href="<?= base_url("master-user") ?>" class="btn btn-danger"><i class="fa fa-long-arrow-left"></i> <?= $this->gtrans->line('Back') ?></a>
              <button class="btn btn-primary" type="submit" name="submit" value="submit"><i class="fa  fa-check-circle"></i> <?= !empty($data_edit) ? $this->gtrans->line('Save Changes') : $this->gtrans->line('Save') ?></button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
</section>
<script type="text/javascript">
var url = "<?= base_url() ?>";
var userID = "<?= !empty($this->uri->segment(2)) ? $this->uri->segment(2) : "" ?>";
<?php
    $arrArea = '[';
    foreach ($dataArea as $row) {
      $arrArea .= $row->area_id.",";
    }
    $arrArea .= ']';
    echo 'var lsArea = '.$arrArea.';';
  ?>
function checkall(){
  var checkboxes = $('input.check');
  checkboxes.iCheck('check');
  //$("#menu1").prop('checked', true);
}

stat = 0;
jQuery("#form-validation").validationEngine('attach', {
  onValidationComplete: function(form, status){
    if(status==true){
      stat = stat + 1;
      if(stat%2==0){
        var obj = $("#form-validation").serialize();
        if(userID!=""){
          saveUser(obj,userID);
        }else{
          if(pass!=""&& confirmpass!=""){
            if(comparePassword(pass,confirmpass)==true){
              saveUser(obj,userID);
            }else{
              Swal.fire({
                type: 'error',
                title: 'Oops...',
                text: '<?= $this->gtrans->line('Your password is not match') ?>!' //,
                //footer: ''
              });
            }
          }else{
            Swal.fire({
              type: 'error',
              title: 'Oops...',
              text: '<?= $this->gtrans->line('Password is not set') ?>!' //,
              //footer: ''
            });
          }
        }
        return false;
      }
    }
  }
});

function saveUser(obj,userID){
  var formObject = $("#form-validation").serializeArray();
  var selectedMenu = 0;
  formObject.forEach(function(val,index){
    if(val.name=="menu[]"){
      selectedMenu += 1;
    }
  });
  if(selectedMenu==0){
    Swal.fire({
      type: 'error',
      title: 'Oops...',
      text: '<?= $this->gtrans->line('You must set at least one menu access') ?>' //,
      //footer: ''
    });
  }else{
    $.ajax({
      method : "POST",
      url  : url + "save-user",
      data  : obj+"&userID="+userID,
      success : function (res){
        if(res=="ok"){
          window.open(url + "master-user","_self");
        }else{
          Swal.fire({
            type: 'error',
            title: 'Oops...',
            text: 'Interval Server Error!' //,
            //footer: ''
          });
        }
      }
    });
  }
}

var pass="";
var confirmpass="";
var maxMenu = <?= $maxMenu ?>;
$(document).ready(function(){
  $("#password").keyup(function(){
    pass = $(this).val();
    if(pass!=""&& confirmpass!=""){
      if(comparePassword(pass,confirmpass)==true){
        $("#msg-password").html('<span class="text-green"><?= $this->gtrans->line('Password confirmed') ?>!</p>');
      }else{
        $("#msg-password").html('<span class="text-red"><?= $this->gtrans->line('Password not match') ?>!</p>');
      }
    }
  });

  $("#confirmpassword").keyup(function(){
    confirmpass = $(this).val();
    if(pass!=""&& confirmpass!=""){
      if(comparePassword(pass,confirmpass)==true){
        $("#msg-password").html('<span class="text-green"><?= $this->gtrans->line('Password confirmed') ?>!</p>');
      }else{
        $("#msg-password").html('<span class="text-red"><?= $this->gtrans->line('Password not match') ?>!</p>');
      }
    }
  });
});

function comparePassword(inputpass,inputconfirm){
  if(inputpass==inputconfirm){
    return true;
  }else{
    return false;
  }
}
$('.form-control-feedback').click(function() {
  var attr = $(this).siblings('input').attr('type');
  if (attr == 'password') {
    $(this).siblings('input').attr('type', 'text');
    $(this).removeClass('far fa-eye');
    $(this).addClass('far fa-eye-slash');
  } else {
    $(this).siblings('input').attr('type', 'password');
    $(this).removeClass('far fa-eye-slash');
    $(this).addClass('far fa-eye');
  }
});


// Remove the checked state from "All" if any checkbox is unchecked
$('#checkAll').on('ifUnchecked', function (event) {
  for (i = 1; i <= maxMenu; i++) {
    $("#menu"+i).iCheck('uncheck');
  }
});

// Make "All" checked if all checkboxes are checked
$('#checkAll').on('ifChecked', function (event) {
  for (i = 1; i <= maxMenu; i++) {
    $("#menu"+i).iCheck('check');
  }
});
<?php
if (!empty($data_edit) AND $data_edit->status_user=='admin_area'){
?>
  $("#setArea").show();
  $("#sts-msg").html('<p style="color:red">Setting Area required when user set as Admin Area!</p>');
  $("#level").val("admin_area");
  $("#admin_area").prop("checked",true);
  $("#setAccessAdminArea").show();
  $("#setAccessSuperAdmin").hide();
<?php
} else {
?>
  $("#setArea").hide();
  $("#sts-msg").html('');
  $("#level").val("super_admin");
  $("#super_admin").prop("checked",true);
  $("#setAccessAdminArea").hide();
  $("#setAccessSuperAdmin").show();
<?php
}
?>

function setAccess(){
  $("#setArea").show();
  $("#sts-msg").html('<p style="color:red">Setting Area required when user set as Admin Area!</p>');
  $("#level").val("admin_area");
  $("#setAccessAdminArea").show();
  $("#setAccessSuperAdmin").hide();
}
function removeAccess(){
  $("#setArea").hide();
  $("#sts-msg").html('');
  $("#level").val("super_admin");
  $("#setAccessAdminArea").hide();
  $("#setAccessSuperAdmin").show();
}

function selectAll(){
  $('#menu18').iCheck('check');
}

</script>
