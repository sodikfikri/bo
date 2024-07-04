
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <?= $this->gtrans->line("Link Registration Settings") ?>
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
            <form action="#" enctype="multipart/form-data" class="form-horizontal" id="registrationLink" method="post">
              <div id="setting-msg"></div>
			  <div class="col-md-12">
				<label><?= $this->gtrans->line("If you want to enable the registration link, please check the box") ?></label>
				<div class="checkbox">
					<label><input type="checkbox" name="enableLink" id="enableLink" value="1" <?= (!empty($companyData)&&$companyData->allow_regis_link=="1") ? "checked" : "" ?>> <?= $this->gtrans->line("Enable Link Registration") ?></label>
                </div>
				<div id="fieldCheckbox">
					<hr>
					<label><?= $this->gtrans->line("Access granted to PIC") ?></label><br>
					<label style="font-weight: normal;"><input type="checkbox" name="checkRegistration" id="checkRegistration" value="1" <?= (!empty($companyData)&&$companyData->allow_regis_link=="1") ? "checked" : "" ?>> <?= $this->gtrans->line("Registration") ?></label>
					<ol>
						<li style="list-style-type: none;"><label style="font-weight: normal;"><input type="checkbox" name="checkRegistrationAdd" id="checkRegistrationAdd" value="1|" <?= (!empty($companyData)&&in_array("1",explode("|",$companyData->access_granted_link))) ? "checked" : "" ?>> <?= $this->gtrans->line("Add Employee") ?></label></li>
						<li style="list-style-type: none;"><label style="font-weight: normal;"><input type="checkbox" name="checkRegistrationImport" id="checkRegistrationImport" value="2|" <?= (!empty($companyData)&&in_array("2",explode("|",$companyData->access_granted_link))) ? "checked" : "" ?>> <?= $this->gtrans->line("Import Excel") ?></label></li>
						<li style="list-style-type: none;"><label style="font-weight: normal;"><input type="checkbox" name="checkRegistrationSimpeg" id="checkRegistrationSimpeg" value="3|" <?= (!empty($companyData)&&in_array("3",explode("|",$companyData->access_granted_link))) ? "checked" : "" ?>> <?= $this->gtrans->line("Simpeg Validation") ?></label></li>
					</ol>
					<div class="col-md-12 text-right">
					  <button type="submit" class="btn btn-primary" name="saveLink"><?= $this->gtrans->line("SAVE") ?></button>
					</div>
                </div>
			  </div>
            
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script type="text/javascript">
  $("#form-validation").validationEngine();
  loadProvince(globalCountryID);
  loadCity(provinceID);
  stat = 0;
  <?php if(!empty($companyData)&&$companyData->allow_regis_link=="1"){ ?>
	document.getElementById("fieldCheckbox").style.display = 'block';
  <?php } else { ?>
	document.getElementById("fieldCheckbox").style.display = 'none';
  <?php } ?>
  
  jQuery("#form-validation").validationEngine('attach', {
    onValidationComplete: function(form, status){
      if(status==true){
        stat = stat + 1;
        if(stat%2==0){
          updateSetting();
          return false;
        }
      }
    }
  });
	let checkbox = document.getElementById("checkRegistration");
      checkbox.addEventListener( "click", () => {
         if ( checkbox.checked ) {
			document.getElementById("checkRegistrationAdd").checked = true;
			document.getElementById("checkRegistrationImport").checked = true;
			document.getElementById("checkRegistrationSimpeg").checked = true;
         } else {
			document.getElementById("checkRegistrationAdd").checked = false;
			document.getElementById("checkRegistrationImport").checked = false;
			document.getElementById("checkRegistrationSimpeg").checked = false;
         }
     });
	 let checkboxEnable = document.getElementById("enableLink");
      checkboxEnable.addEventListener( "click", () => {
         if ( checkboxEnable.checked ) {
			document.getElementById("fieldCheckbox").style.display = 'block';
         } else {
			document.getElementById("fieldCheckbox").style.display = 'none';
         }
     });
	$('#registrationLink').submit(function(e){ 
	e.preventDefault();
	  $.ajax({
		url:url +'update-registrationLink', //URL submit
		type:"post",
		data:new FormData(this),
		processData:false,
		contentType:false,
		cache:false,
		//async:false,
		success: function(response){
			const obj = JSON.parse(response);
			if(obj.code=="200"){
				alert("Success update Registration Link.");
				location.reload(true);
			}else{
				alert("Failed update Registration Link.");
			}
		}
	  });
	});
</script>
