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

</style>
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <?= $this->gtrans->line("User Profile") ?>
    </h1>
  </section>
<!-- Main content -->
<section class="content">
  <!-- Info boxes -->
  <div class="row">
    <div class="col-md-12">
      <div class="box box-inact">
        <!-- /.box-header -->
        <div class="box-body">
          <?= !empty($msg)?$msg:"" ?>
          <div class="row">
            <div class="pull-right">
              <div class="col-md-4" id="bt-setting">
                <a href="#" onclick="editProfile()">
                  <i class="fa fa-gear fa-lg"></i>
                </a>
              </div>
            </div>
            <div class="col-md-2" style="text-align:center">
              <img src="<?= (!empty($dataUser->user_imgprofile) && file_exists(FCPATH.DIRECTORY_SEPARATOR.('sys_upload'.DIRECTORY_SEPARATOR.'userpic'.DIRECTORY_SEPARATOR.$dataUser->user_imgprofile))) ? base_url('sys_upload/userpic/'.$dataUser->user_imgprofile) : base_url('asset/images/admin-icon.png') ?>" width="150px">
              <button id="btn-change-image" class="btn btn-primary btn-sm btn-block"  ><i class="fa fa-image"></i> Change Image</button>
            </div>

            <div class="col-md-10">
              <div id="profile-show">
                <h3 id="view-full-name" ><strong><?= $dataUser->user_fullname ?></strong></h3>
                <p><?= $dataUser->user_emailaddr ?></p>
                <hr>
                <div class="form-group">
                  <label><?= $this->gtrans->line("Company") ?></label>
                  <p style="font-size:16pt" ><?= $dataCompany->company_name ?></p>
                </div>
                <div class="form-group">
                  <label><?= $this->gtrans->line("Phone Number") ?></label>
                  <p style="font-size:16pt" id="view-phone-number"><?= $dataUser->user_phone ?></p>
                </div>
                <div class="form-group">
                  <label>APP ID</label>
                  <p style="font-size:16pt;font-family: 'Roboto Mono', monospace;" ><?= $appid ?></p>
                </div>
              </div>
              <div id="profile-edit" style="display:none">
                <form class="form-horizontal" action="" id="form-validation" method="post">
                  <div class="col-md-8">
                    <div id="setting-profile-msg"></div>
                    <div class="form-group">
                      <label for="" class="col-sm-3 control-label"><?= $this->gtrans->line("Full Name") ?> <span class="text-red">*</span></label>
                      <div class="col-sm-9">
                        <input data-validation-engine="validate[required,custom[onlyLetterNumber],maxSize[100]]" name="fullname" id="fullname" value="<?= $dataUser->user_fullname ?>" type="text" class="form-control" id="" placeholder="">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="" class="col-sm-3 control-label"><?= $this->gtrans->line("Phone Number") ?> <span class="text-red">*</span></label>
                      <div class="col-sm-9">
                        <input data-validation-engine="validate[required,custom[phone],maxSize[20]]" name="phonenumber" id="phonenumber" value="<?= $dataUser->user_phone ?>" type="text" class="form-control" id="" placeholder="">
                      </div>
                    </div>
                    <div class="form-group">
                      <label for="" class="col-sm-3 control-label"><?= $this->gtrans->line("Email") ?></label>
                      <div class="col-sm-9">
                        <input name="email" readonly value="<?= $dataUser->user_emailaddr ?>" type="text" class="form-control" id="" placeholder="">
                      </div>
                    </div>
                    <div class="form-group  has-feedback field-loginform-password required">
                      <label for="" class="col-sm-3 control-label"><?= $this->gtrans->line("New Password") ?></label>
                      <div class="col-sm-9">
                        <input data-validation-engine="validate[minSize[8],maxSize[50]]" name="password" id="password" type="password" class="form-control" id="" placeholder="">
                        <i class="fa fa-eye form-control-feedback"></i>
                        <p class="help-block"><?= $this->gtrans->line("Set empty when you don`t need change password") ?></p>
                      </div>
                    </div>
                    <div class="form-group  has-feedback field-loginform-password required">
                      <label for="" class="col-sm-3 control-label"><?= $this->gtrans->line("Confirm New Password") ?></label>
                      <div class="col-sm-9">
                        <input data-validation-engine="validate[minSize[8],maxSize[50]]" name="confirmpassword" id="confirmpassword" type="password" class="form-control" id="" placeholder="">
                        <i class="fa fa-eye form-control-feedback"></i>
                        <p class="help-block"><?= $this->gtrans->line("Set empty when you don`t need change password") ?></p>
                      </div>
                    </div>
                    <div id="msg-password" style="text-align:center"></div>
                    <div class="pull-right">
                          <button onclick="back()" type="button" class="btn btn-danger"><i class="fa fa-long-arrow-left"></i> <?= $this->gtrans->line("Back") ?></button>
                          <button type="submit" name="submit" value="submit" class="btn btn-primary"><i class="fa fa-check-circle"></i> <?= $this->gtrans->line("Save Changes") ?></button>
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</section>
<div class="modal fade" id="modal-default">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Change Image</h4>
      </div>
      <form action="<?= base_url("setting_user/saveUserImage") ?>" enctype="multipart/form-data" method="POST">
      <div class="modal-body">
          <input type="file" name="file" >
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function(){
    $("#btn-change-image").click(function(){
      $("#modal-default").modal('show');
    });
  });
function editProfile(){
  $("#profile-show").fadeOut(100);
  $("#profile-edit").fadeIn(100);
  $("#bt-setting").fadeOut(100);
}
function back() {
  $("#profile-show").fadeIn(100);
  $("#profile-edit").fadeOut(100);
  $("#bt-setting").fadeIn(100);
}
function saveChanges(){
  var fullname = $("#fullname").val();
  var phonenumber = $("#phonenumber").val();
  var password = $("#password").val();
  var confirmpassword = $("#confirmpassword").val();

  $.ajax({
    method : "POST",
    url    : url + "save-profile-changes",
    data   : {fullname:fullname,phonenumber:phonenumber,password:password,confirmpassword:confirmpassword},
    success: function(res){
      if(res=="fullupdate"){
        $("#setting-profile-msg").html('<div class="callout callout-success">'+
                  	                   '<h4><?= $this->gtrans->line("Success") ?>!</h4>'+
                                       '<p><?= $this->gtrans->line("Profile and password updated") ?></p>'+
                                       '</div>');
        $("#password").val("");
        $("#confirmpassword").val("");
        window.location.replace(url+"logout");
      }else if(res=="passwordnotmatch"){
        $("#setting-profile-msg").html('<div class="callout callout-danger">'+
                  	                   '<h4><?= $this->gtrans->line("Failed") ?>!</h4>'+
                                       '<p><?= $this->gtrans->line("Password Not Match") ?></p>'+
                                       '</div>');
      }else if(res=="halfupdate"){
        $("#setting-profile-msg").html('<div class="callout callout-success">'+
                  	                   '<h4><?= $this->gtrans->line("Success") ?>!</h4>'+
                                       '<p><?= $this->gtrans->line("Profile updated") ?></p>'+
                                       '</div>');
        $("#view-full-name").html('<strong>'+fullname+'</strong>');
        $("#view-phone-number").html(phonenumber);
        alert("Change was saved!");
        location.reload();

      }
    }
  });
}
$("#form-validation").validationEngine();

stat = 0;
jQuery("#form-validation").validationEngine('attach', {
  onValidationComplete: function(form, status){
    if(status==true){
      stat = stat + 1;
      if(stat%2==0){
        if(pass!=""&& confirmpass!=""){
          if(comparePassword(pass,confirmpass)==true){
            saveChanges();
          }else{
            Swal.fire({
              type: 'error',
              title: 'Oops...',
              text: '<?= $this->gtrans->line("Your password is not match") ?>!' //,
              //footer: ''
            });
          }
        }else{
          saveChanges();
        }
        return false;
      }
    }
  }
});
var pass="";
var confirmpass="";
$(document).ready(function(){
  $("#password").keyup(function(){
    pass = $(this).val();
    if(pass!=""&& confirmpass!=""){
      if(comparePassword(pass,confirmpass)==true){
        $("#msg-password").html('<span class="text-green"><?= $this->gtrans->line("Password confirmed") ?>!</p>');
      }else{
        $("#msg-password").html('<span class="text-red"><?= $this->gtrans->line("Password not match") ?>!</p>');
      }
    }
  });

  $("#confirmpassword").keyup(function(){
    confirmpass = $(this).val();
    if(pass!=""&& confirmpass!=""){
      if(comparePassword(pass,confirmpass)==true){
        $("#msg-password").html('<span class="text-green"><?= $this->gtrans->line("Password confirmed") ?>!</p>');
      }else{
        $("#msg-password").html('<span class="text-red"><?= $this->gtrans->line("Password not match") ?>!</p>');
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
</script>
