<?php 
                  $txtActionIntrax = "Get";
              //     if($needUpgradeInTraxCompanyId==true){
              //       $txtActionIntrax  = "Update";
              //       $msgAddons = '<div class="callout callout-danger">
              //   <h4>InTrax Upgrade Detected!</h4>

              //   <p>You must re register to get paid feature from InTrax.</p>
              // </div>';
              //     }
                  ?>
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
      <?= !empty($msgAddons) ? $msgAddons : '' ?>
      <div class="box box-inact">
        <div class="box-body">
          <div class="row">
            <div class="col-md-12">
              <div class="row">
                <div class="col-md-8">
                  <?php 
                  switch ($subscriptionData->intrax_plan_code) {
                    case '1':
                      $identitasPaket = '<span class="label label-info">Lite</span>';
                      break;
                    case '2':
                      $identitasPaket = '<span class="label label-success">Premium</span>';
                      break;
                    case '3':
                      $identitasPaket = '<span class="label label-danger">Trial</span>';
                      break;
                    default:
                      $identitasPaket = '';
                      break;
                  }
                    
                  ?>
                  
                  <p><b>InTrax Company ID : <?= !empty($intraxCompanyID) ? "".$intraxCompanyID.' '.$identitasPaket : "<span class='text-red'>Undefined</span></span>" ?></b></p>
                  <?= ($intraxPanelRegister==true) ? "<button id='btn-get-intrax-company-id' class='btn btn-primary btn-sm'>".$txtActionIntrax." InTrax Company ID</button>" : ""  ?>
                  
                </div>
                <div class="col-md-4">
                  <form action="">
                    <div class="row">
                      <div class="col-md-12">
                        <div class="text-green" style="text-align:right">
                          License : <span id="license-count"></span>
                        </div>
                      </div>
                    </div>
                    <div class="row" >
                      <div class="col-md-12">
                        <div class="row">
                          <div class="col-md-8">
                            <input type="text" name="search-box" value="<?= !empty($_GET["search-box"]) ? $_GET["search-box"] : "" ?>" placeholder="Search Name" class="form-control">
                          </div>
                          <div class="col-md-4">
                            <button type="submit" name="submit" value="submit" class="btn btn-primary">Search</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            
            <div class="col-md-12" style="margin-top:5px">
              <form action="" id="frm-license">
                <input type="hidden" name="str-employee-id-displayed" value="<?= $strEmployeeIdDisplayed ?>">
                <?= !empty($tableList) ? $tableList : "" ?>
                <?= $this->pagination->create_links(); ?>
                <input type="hidden" name="system-addons-code" value="<?= $systemAddonsCode ?>" >
                <input type="hidden" name="date_expired" value="<?= $date_expired ?>" >
                <div class="box-footer text-right" >
                  <a href="<?= base_url("addons") ?>" class="btn btn-default">Back</a>
                  <button type="button" class="btn btn-primary" onclick="saveSetting()">Save Setting</button>
                </div>
              </form>
            </div>
            
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php if($intraxPanelRegister==true){ ?>
  <div class="modal" id="modal-intrax-registration">
    <div class="modal-dialog">
      <div class="modal-content">
      <form action="<?= base_url('intrax/submitRegistration'); ?>" method="POST">
        <input type="hidden" name="paramCode" value="<?= !empty($paramCode) ? $paramCode : "" ?>">
        <input type="hidden" name="subscription_id" value="<?= !empty($subscription_id) ? $subscription_id : "" ?>">
        <div class="modal-header">
          <h4 class="modal-title">InTrax Registration</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
          <div class="form-horizontal col-md-12">
            <div class="form-group row">
              <label class="col-sm-3 col-form-label">Full Name</label>
              <div class="col-sm-9">
              <input type="text" value="<?= $this->session->userdata("ses_username") ?>" readonly name="fullname" class="form-control">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-3 col-form-label">Company Name</label>
              <div class="col-sm-9">
              <input type="text" readonly value="<?= $subscriptionData->company_name ?>" name="company-name" class="form-control">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-3 col-form-label">Phone</label>
              <div class="col-sm-9">
              <input type="text" required name="phone" value="<?= $subscriptionData->company_telp ?>" class="form-control">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-3 col-form-label">Address</label>
              <div class="col-sm-9">
              <textarea required name="address" class="form-control"
              ><?= $subscriptionData->company_addr ?></textarea>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-3 col-form-label">Head Office</label>
              <div class="col-sm-9">
                <div class="row">
                  <div class="col-md-8">
                    <select name="head-office" class="form-control" required>
                      <option></option>
                      <?php 
                      foreach($companyInfo["branch"] as $branchInfo){
                        $infoBranchSelected = $branchInfo->is_head_office=='yes' ? 'selected' : '';
                        echo '<option '.$infoBranchSelected.' value="'.$branchInfo->cabang_id.'">'.$branchInfo->cabang_name." ".$branchInfo->area_name.'</option>';
                      }
                      ?>
                    </select>
                  </div>
                  <div class="col-md-4">
                      <a href="<?= base_url("master-area") ?>" class="btn btn-success btn-block"><?= $this->gtrans->line("Setting Master") ?></a>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-3 col-form-label">Email</label>
              <div class="col-sm-9">
              <input type="text" readonly name="email" value="<?= $subscriptionData->company_email ?>" class="form-control">
              </div>
            </div>
            <div class="form-group row">
              <label class="col-sm-3 col-form-label">InTrax Password</label>
              <div class="col-sm-9">
                <div class="row">
                  <div class="col-md-6"><input type="password" id="password" required name="password" class="form-control"></div>
                  <div class="col-md-6">
                    <div class="checkbox">
                      <label>
                        <input required type="checkbox" onclick="if(this.checked==true){$('#password').val('**************************')}else{ $('#password').val('') }" name="use-inact-password" value="1"> Use InAct Password
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button onclick="return confirm('Are you sure regis inTrax with this data?')" type="submit" class="btn btn-primary">Submit Registration</button>
        </div>
        </form>
      </div>
    <!-- /.modal-content -->
  </div>
<!-- /.modal-dialog -->
</div>
<?php } ?>
<script>

var license  = <?= $addonsQty ?>;
var selected = <?= $checkedCount ?>;
var url      = "<?= base_url() ?>";

$(document).ready(function(){
  $("#btn-get-intrax-company-id").click(function(){
    $("#modal-intrax-registration").modal("show");
  });
});
function reDrawLicense(){
  $("#license-count").html(license - selected);
}

reDrawLicense();

function selectEmployee(componen){
  if(componen.checked){
    if(license == selected){
      $("#"+componen.id).prop("checked",false);
      Swal.fire({
        type : 'error',
        title: 'Oops...',
        text: 'Your intrax license is out!'
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
      url     : url + "save-intrax-allocation",
      data    : data,
      success : function(res){
        $("#loader").fadeOut(1);
        if(res=="OK"){
          Swal.fire({
            type    : 'success',
            position: 'center',
            icon    : 'success',
            title   : 'Allocation saved',
            showConfirmButton: false,
            timer: 1500
          });
        }else{
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
      text: 'You must select at least one device!'
      //footer: '<a href>Why do I have this issue?</a>'
    });
  }
}
</script>
