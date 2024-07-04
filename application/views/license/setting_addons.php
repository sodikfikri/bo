<style>
.addons-price{
  color: #039be5;
  font-weight: bold;
  font-size: 14pt;
  line-height: 10pt;
}
.addons-uom{
  color:#90a4ae;
  line-height: 10pt;
}
.addons-name{
  font-size: 14pt;
  line-height: 15pt;
}
.addons-description{
  min-height: 50px;
}
.text-green {
  font-size: 18pt;
}
</style>
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <?= $title ?>
    </h1>
  </section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-12">
      <div class="box box-inact">
        <div class="box-body">
          <div class="pull-right text-green">
            License : <span id="license-count"></span>
          </div>
          <form action="" id="frm-license">
            <?= !empty($tableList) ? $tableList : "" ?>
            <div class="box-footer text-right" >
              <a href="<?= base_url("addons") ?>" class="btn btn-default">Back</a>
              <button type="button" class="btn btn-primary" onclick="saveSetting()">Save Setting</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
<script>
var license  = <?= $addonsQty ?>;
var selected = <?= $checkedCount ?>;
var url      = "<?= base_url() ?>";

function reDrawLicense(){
  $("#license-count").html(license - selected);
}

reDrawLicense();

function selectDevice(componen){
  if(componen.checked){
    if(license == selected){
      $("#"+componen.id).prop("checked",false);
      Swal.fire({
        type : 'error',
        title: 'Oops...',
        text: 'Your device license is out!'
        //footer: '<a href>Why do I have this issue?</a>'
      });
    }else{
      selected += 1;
    }
  }else{
    selected -= 1;
  }
  reDrawLicense();
}

function saveSetting(){
  $("#loader").fadeIn(1);
  var data = $("#frm-license").serialize();
  if(selected>0){
    $.ajax({
      type    : "POST",
      url     : url + "save-addons-allocation",
      data    : data,
      success : function(res){
        if(res=="OK"){
          $("#loader").fadeOut(1);
          Swal.fire({
            type    : 'success',
            position: 'center',
            icon    : 'success',
            title   : 'Allocation saved',
            showConfirmButton: false,
            timer: 1500
          });
        }else{
		  $("#loader").fadeOut(1);
          Swal.fire({
            type    : 'error',
            position: 'center',
            icon    : 'success',
            title   : 'Error save setting',
            showConfirmButton: false,
            timer: 1500
          });
        }
      }
    });
  }else{
    $("#loader").fadeOut(1);
    Swal.fire({
      type : 'error',
      title: 'Oops...',
      text: 'You must select at least one!'
      //footer: '<a href>Why do I have this issue?</a>'
    });
  }
}
</script>
