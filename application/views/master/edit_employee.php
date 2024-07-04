<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Update Employee
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
            <form action="" method="post" id="form-validation">
              <div class="form-group">
                <label>Account No <span class="text-red">*</span></label>
                <div >
					<input type="hidden" name="id" id="id" value="<?= !empty($detailEmployee) ? ($this->encryption_org->encode($detailEmployee->employee_id)): "" ?>">
                  <input data-validation-engine="validate[required]" type="text" class="form-control" value="<?= !empty($detailEmployee) ? ($detailEmployee->employee_account_no): "" ?>" readonly>
                </div>
              </div>
              <div class="form-group">
                <label>Full Name <span class="text-red">*</span></label>
                <div>
                  <input data-validation-engine="validate[required]" name="fullname" type="text" class="form-control" value="<?= !empty($detailEmployee) ? ($detailEmployee->employee_full_name): "" ?>" >
                </div>
              </div>
              <div class="form-group">
                <label>Nick Name <span class="text-red">*</span></label>
                <div>
                  <input data-validation-engine="validate[required]" name="nickname" type="text" class="form-control" value="<?= !empty($detailEmployee) ? ($detailEmployee->employee_nick_name): "" ?>">
                </div>
              </div>
              <div class="form-group">
                <label>Gender <span class="text-red">*</span></label>
                <div>
                  <select name="gender" data-validation-engine="validate[required]" class="form-control" id="gender" >
                    <option <?= $detailEmployee->gender=="male"?"selected":"" ?> value="male">Male</option>
                    <option <?= $detailEmployee->gender=="female"?"selected":"" ?> value="female">Female</option>
                  </select>
                </div>
              </div>
              <div class="form-group">
                <label>Birthday <span class="text-red">*</span></label>
                <div>
                  <input type="text" name="birthday" data-validation-engine="validate[required]" class="form-control datepicker" id="birthday" value="<?= $detailEmployee->birthday ?>" autocomplete="off">
                </div>
              </div>
              <div class="form-group">
                <label>Phone Number <span class="text-red">*</span></label>
                <div>
                  <input type="text" name="phone-number" data-validation-engine="validate[required]" class="form-control" id="phone-number" value="<?= $detailEmployee->phone_number ?>">
                </div>
              </div>
              <div class="form-group">
                <label>Email <span class="text-red">*</span></label>
                <div>
                  <input type="text" name="email" data-validation-engine="validate[required,minSize[5],maxSize[100],custom[email]]" class="form-control" maxlength="100" id="email" value="<?= $detailEmployee->email ?>">
                  <div id="email-msg"></div>
                </div>
              </div>
              <div class="form-group">
                <label>Address <span class="text-red">*</span></label>
                <div>
                  <textarea name="address" data-validation-engine="validate[required]" class="form-control" id="address"><?= $detailEmployee->address ?></textarea>
                </div>
              </div>
              <div class="form-group">
                <label>Pin <?= $detailEmployee->intrax_license=='active'?'<span class="text-red">*</span>':'' ?></label>
                <div>
                  <input type="number" name="intrax-pin" data-validation-engine="validate[custom[integer],pinSize[6]]" min="0" maxlength="6" class="form-control" id="intrax-pin" value="<?= $detailEmployee->intrax_pin ?>" placeholder="123456" <?= $detailEmployee->intrax_license=='active'?"required":"" ?>>
                </div>
              </div>
              <div class="form-group">
                <label for="" class="control-label">Device access user</label>
                <div>
                  <input type="radio" name="level" onclick="removePassRequired()" id="status_user" checked value="user"> User
                  &nbsp;&nbsp;
                  <input <?= (!empty($detailEmployee->employee_level) && $detailEmployee->employee_level=="14") ? "checked" : "" ?> type="radio" name="level" onclick="setPassRequired()" id="status_admin" value="admin"> Super Admin
                </div>
              </div>
              <div class="form-group">
                <label>Password</label>
                <div>
                  <input value="<?= !empty($detailEmployee->employee_password) ? $detailEmployee->employee_password : "" ?>" name="password" type="text" data-validation-engine="validate[custom[onlyNumber],maxSize[9]]" class="form-control" id="password" >
                  <div id="pwd-msg"></div>
                </div>
              </div>
              <div class="form-group" <?= $detailEmployee->appid!='IA01M82337F20230627732'?'style="display:none;"':"" ?>>
                <label>Presence Method</label>
                <div>
                  <div class="checkbox"><?php $methodPres = explode("|", $detailEmployee->presence_method); ?>
                      <label><input type="checkbox" name="method[]" value="1" <?= (!empty($methodPres) && in_array(1, $methodPres)) ? 'checked' : '' ?>> PIN</label>
                    </div>
                    <div class="checkbox">
                      <label><input type="checkbox" name="method[]" value="2" <?= (!empty($methodPres) && in_array(2, $methodPres)) ? 'checked' : '' ?>> Finger Print</label>
                    </div>
                    <div class="checkbox">
                      <label><input type="checkbox" name="method[]" value="3" <?= (!empty($methodPres) && in_array(3, $methodPres)) ? 'checked' : '' ?>> Face Id</label>
                    </div>
					<div class="checkbox">
                      <label><input type="checkbox" name="method[]" value="4" <?= (!empty($methodPres) && in_array(4, $methodPres)) ? 'checked' : '' ?>> Take Picture</label>
                    </div>
                </div>
              </div>
			  <div class="form-group" <?= $detailEmployee->appid!='IA01M82337F20230627732'?'style="display:none;"':"" ?>>
                <label>Presence Mode <span class="text-red">*</span></label>
                <div>
                  <select name="presence_mode" data-validation-engine="validate[required]" class="form-control" id="presence_mode" >
                    <option <?= $detailEmployee->presence_mode=="online"?"selected":"" ?> value="online">Online</option>
                    <option <?= $detailEmployee->presence_mode=="online-offline"?"selected":"" ?> value="online-offline">Online-Offline</option>
                  </select>
                </div>
              </div>
              <div>
                <a href="<?= base_url("master-employee") ?>" class="btn btn-danger" >Cancel</a>
                <button name="submit" value="submit" type="submit" class="btn btn-primary">Save Changes</button>
              </div>
            </form>
            <?php

            ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</section>
<script>
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
function setPassRequired(){
  $("#password").attr("data-validation-engine","validate[required,custom[onlyNumber]]");
  $("#pwd-msg").html('<p style="color:red">Password required when user set as Super Admin!</p>');
}
function removePassRequired(){
  $("#password").attr("data-validation-engine","validate[custom[onlyNumber]]");
  $("#pwd-msg").html('');
}
</script>
