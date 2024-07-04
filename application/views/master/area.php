<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?= $this->gtrans->line("Master Area") ?>
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
            <?= !empty($notif) ? $notif : "" ?>
            <button type="button" class="btn btn-primary" data-toggle="modal" onclick="addNew()"><i class="fa fa-pencil"></i> <?= $this->gtrans->line("New Area") ?></button>
          </div>
          <div class="col-md-12" style="padding-top:10px">
            <?= !empty($areaTable) ? $areaTable : "" ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</section>
<div class="modal fade" id="frmArea">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><div id="frm-text"></div></h4>
        </div>
        <?= form_open("save-area",["id"=>"form-validation"]); ?>
        <div class="modal-body">
          <input type="hidden" name="id" id="id">
          <div class="form-group">
            <label for=""><?= $this->gtrans->line("Area Code") ?> <span class="text-red">*</span></label>
            <input onchange="checkExists('areacode','msg-code','check-area-code-exists','<?= $this->gtrans->line('Code was used by deleted or existing data') ?>','<?= $this->gtrans->line('Area Code Is Available') ?>',$('#id').val())" id="areacode" data-validation-engine="validate[required,custom[onlyLetterNumberSemiSpesial],maxSize[50]]" type="text" name="areacode" class="form-control" id="" placeholder="">
            <div id="msg-code"></div>
          </div>
          <div class="form-group">
            <label for=""><?= $this->gtrans->line("Area Name") ?> <span class="text-red">*</span></label>
            <input onchange="checkExists('areaname','msg-name','check-area-name-exists','<?= $this->gtrans->line('Area name was used by existing data') ?>','<?= $this->gtrans->line('Area Name Is Available') ?>',$('#id').val())" id="areaname" data-validation-engine="validate[required,custom[onlyLetterNumberSemiSpesial],maxSize[100]]" type="text" name="areaname" class="form-control" placeholder="">
            <div id="msg-name"></div>
          </div>
          <div class="form-group">
            <label for=""><?= $this->gtrans->line("Description") ?></label>
            <textarea id="areadesc" data-validation-engine="validate[custom[onlyLetterNumberSemiSpesial]]" name="areadesc" class="form-control"></textarea>
          </div>
		  <div class="form-group">
                <label>Presence Method</label>
                <div>
                  <div class="checkbox">
                      <label><input type="checkbox" name="method[]" value="1" id="pin"> PIN</label>
                    </div>
                    <div class="checkbox">
                      <label><input type="checkbox" name="method[]" value="2" id="finger"> Finger Print</label>
                    </div>
                    <div class="checkbox">
                      <label><input type="checkbox" name="method[]" value="3" id="face"> Face Id</label>
                    </div>
					<div class="checkbox">
                      <label><input type="checkbox" name="method[]" value="4" id="pic"> Take Picture</label>
                    </div>
                </div>
              </div>
			<div class="form-group">
              <label for=""><?= $this->gtrans->line("Presence Mode") ?></label>
              <div class="radio">
                <label><input type="radio" name="presence_mode" value="online" id="presence_mode_online"> Online</label>
              </div>
              <div class="radio">
                <label><input type="radio" name="presence_mode" value="online-offline" id="presence_mode_onlineoffline"> Online-Offline</label>
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
  function edit(id,code,name,description,method,mode){
    $("#txtBtnSave").html('<i class="fa fa-check-circle"></i> <?= $this->gtrans->line('Save Changes') ?>');
    $("#frm-text").html('<?= $this->gtrans->line("Edit Area") ?>');
    $("#id").val(id);
    $("#areacode").val(atob(code));
    $("#areaname").val(atob(name));
    $("#areadesc").val(atob(description));
	var strMethod = atob(method);
	var myMethod = strMethod.split("|");
	if(myMethod.includes("1")){$("#pin").prop("checked", true);} else {$("#pin").prop("checked", false);}
	if(myMethod.includes("2")){$("#finger").prop("checked", true);} else {$("#finger").prop("checked", false);}
	if(myMethod.includes("3")){$("#face").prop("checked", true);} else {$("#face").prop("checked", false);}
	if(myMethod.includes("4")){$("#pic").prop("checked", true);} else {$("#pic").prop("checked", false);}
	if(atob(mode)=='online'){$("#presence_mode_online").prop("checked", true);} else {$("#presence_mode_online").prop("checked", false);}
	if(atob(mode)=='online-offline'){$("#presence_mode_onlineoffline").prop("checked", true);} else {$("#presence_mode_onlineoffline").prop("checked", false);}
    $("#frmArea").modal('show');
  }
  function addNew(){
    $("#txtBtnSave").html('<i class="fa fa-check-circle"></i> <?= $this->gtrans->line('Save') ?>');
    $("#frm-text").html('<?= $this->gtrans->line("Add New Area") ?>');
    $("#id").val("");
    $("#areacode").val("");
    $("#areaname").val("");
    $("#areadesc").val("");
	$("#pin").prop("checked", false);
	$("#finger").prop("checked", false);
	$("#face").prop("checked", false);
	$("#pic").prop("checked", false);
	$("#presence_mode_online").prop("checked", false);
	$("#presence_mode_onlineoffline").prop("checked", false);
    $("#frmArea").modal('show');
  }
  function delArea(idarea,branchCount){
    if(branchCount==0){
      Swal.fire({
        title: '<?= $this->gtrans->line("Are you sure") ?>?',
        text: "<?= $this->gtrans->line("You won`t be able to revert this") ?>!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '<?= $this->gtrans->line("Yes, delete it!") ?>'
      }).then((result) => {
        if (result.value) {
          window.open(url + 'delete-area/' + idarea,'_self');
        }
      });
    }else{
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Area is used by several branch!'
        // footer: '<a href>Why do I have this issue?</a>'
      });
    }

  }
  $("#datatable").DataTable();
</script>
