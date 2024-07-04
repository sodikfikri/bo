<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?= $this->gtrans->line("Master Fake GPS") ?>
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
            <button type="button" class="btn btn-primary" data-toggle="modal" onclick="addNew()"><i class="fa fa-pencil"></i> <?= $this->gtrans->line("New Fake GPS") ?></button>
          </div>
          <div class="col-md-12" style="padding-top:10px">
            <?= !empty($fakegpsTable) ? $fakegpsTable : "" ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</section>
<div class="modal fade" id="frmFakegps">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><div id="frm-text"></div></h4>
        </div>
        <?= form_open("save-fakegps",["id"=>"form-validation"]); ?>
        <div class="modal-body">
          <input type="hidden" name="id" id="id">
          <div class="form-group">
            <label for=""><?= $this->gtrans->line("Fake GPS Code") ?> <span class="text-red">*</span></label>
            <input onchange="checkExists('fakegpscode','msg-code','check-fakegps-code-exists','<?= $this->gtrans->line('Code was used by deleted or existing data') ?>','<?= $this->gtrans->line('Fake GPS Code Is Available') ?>',$('#id').val())" id="fakegpscode" data-validation-engine="validate[required,custom[onlyLetterNumberSemiSpesial],maxSize[50]]" type="text" name="fakegpscode" class="form-control" id="" placeholder="">
            <div id="msg-code"></div>
          </div>
          <div class="form-group">
            <label for=""><?= $this->gtrans->line("Fake GPS Name") ?> <span class="text-red">*</span></label>
            <input onchange="checkExists('fakegpsname','msg-name','check-fakegps-name-exists','<?= $this->gtrans->line('Fake GPS name was used by existing data') ?>','<?= $this->gtrans->line('Fake GPS Name Is Available') ?>',$('#id').val())" id="fakegpsname" data-validation-engine="validate[required,custom[onlyLetterNumberSemiSpesial],maxSize[100]]" type="text" name="fakegpsname" class="form-control" placeholder="">
            <div id="msg-name"></div>
          </div>
          <div class="form-group">
            <label for=""><?= $this->gtrans->line("Description") ?></label>
            <textarea id="fakegpsdesc" data-validation-engine="validate[custom[onlyLetterNumberSemiSpesial]]" name="fakegpsdesc" class="form-control"></textarea>
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
  function edit(id,code,name,description){
    $("#txtBtnSave").html('<i class="fa fa-check-circle"></i> <?= $this->gtrans->line('Save Changes') ?>');
    $("#frm-text").html('<?= $this->gtrans->line("Edit Fake GPS") ?>');
    $("#id").val(id);
    $("#fakegpscode").val(atob(code));
    $("#fakegpsname").val(atob(name));
    $("#fakegpsdesc").val(atob(description));
    $("#frmFakegps").modal('show');
  }
  function addNew(){
    $("#txtBtnSave").html('<i class="fa fa-check-circle"></i> <?= $this->gtrans->line('Save') ?>');
    $("#frm-text").html('<?= $this->gtrans->line("Add New Fake GPS") ?>');
    $("#id").val("");
    $("#fakegpscode").val("");
    $("#fakegpsname").val("");
    $("#fakegpsdesc").val("");
    $("#frmFakegps").modal('show');
  }
  function delFakegps(idfakegps){
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
          window.open(url + 'delete-fakegps/' + idfakegps,'_self');
        }
      });

  }
  $("#datatable").DataTable();
</script>
