<?php 
$licenses = $activeaddons;

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
</style>
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Addons
    </h1>
  </section>
<!-- Main content -->

<section class="content">
  <div class="row">
    <div class="col-md-12">
      <?= !empty($msgAddons) ? $msgAddons : '' ?>
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li class="active"><a href="#tab_1" data-toggle="tab"><?= $this->gtrans->line('ADDONS MARKETPLACE') ?></a></li>
          <li><a href="#tab_2" data-toggle="tab"><?= $this->gtrans->line('MY ADDONS') ?></a></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="tab_1">
            <div class="box-body">
              <div class="row">
                <?php

                foreach ($dataAddons as $addons) {

                  $arrPrice = explode("/",$addons['strprice']);
                  $encPluginsId = $this->encryption_org->encode($addons['pluginsid']);

                  $encImg   = base64_encode($addons['image']);
                  $encTitle = base64_encode($addons['name']);
                  $encDesc  = base64_encode($addons['description']);
                  $encPrice = base64_encode($arrPrice[0]);
                  $encUom   = base64_encode($arrPrice[1]);
                  $encUnitName = base64_encode($arrPrice[2]);
                  $encAddonsCode = $this->encryption_org->encode($addons['addonscode']);
                  $jsParam = "'".$encImg."','".$encTitle."','".$encDesc."','".$encPrice."','".$encUom."','".$encUnitName."','".$encPluginsId."','".$encAddonsCode."'";

                  if($addons['trialinterval']>0){
                    $btnTry = '<a class="btn btn-success btn-block"  href="#" onclick="showTrialDialog('.$jsParam.')">'.$this->gtrans->line('TRY FREE').' '.$addons['trialinterval'].' '.$this->gtrans->line('DAYS').' </a>';
                  }else{
                    $btnTry = '';
                  }
                  $display = true;
                  if($addons["systemaddonscode"]=="intraxlicensepremium" || $addons["systemaddonscode"]=="intraxlicenselite"){
                    $intraxPlanCode = ($subscriptionData->intrax_plan_code==3) ? 2 : $subscriptionData->intrax_plan_code;
                    switch ($addons["systemaddonscode"]) {
                      case 'intraxlicenselite':
                        $addonsIntraxId = 1;
                        break;
                      case 'intraxlicensepremium':
                        $addonsIntraxId = 2;
                        break;
                    }
                    if($addonsIntraxId!=$intraxPlanCode){
                      $display= false;
                    }
                    if($subscriptionData->intrax_plan_code==3 ||$subscriptionData->intrax_plan_code==0){
                      $display= true;
                    }

                    if(!array_key_exists("intraxlicensepremium", $activeaddons) && !array_key_exists("intraxlicenselite", $activeaddons)){
                      $display= true;
                    }
                  }
                  $btOrder = $display==true ? '<div class="row">
                                    <div class="col-md-6" style="text-align:center">
                                      '.$btnTry.'
                                    </div>
                                    
                                    <div class="col-md-6">
                                      <button onclick="buyAddons(\''.$encPluginsId.'\')" class="btn btn-primary btn-block">'.$this->gtrans->line('BUY NOW').'</button>
                                    </div>
                                  </div>' : '<div class="row"><div class="col-md-12 text-red" style="text-align:center;font-weight:bold">You have other InTrax product</div></div>';
                  //if($display==true){
                  echo '<div class="col-md-6">
                          <div class="box">
                            <div class="box-body">
                                
                                <div class="row">
                                  <div class="col-md-4">
                                    <img src="'.$addons['image'].'" width="100%">
                                  </div>
                                  <div class="col-md-8">
                                    <div class="col-md-12">
                                      <p class="addons-name">'.$addons['name'].'</p>
                                      <p class="addons-price">Rp'.$arrPrice['0'].'</p>
                                      <p class="addons-uom">/'.$arrPrice[1].'</p>
                                    </div>
                                    <div class="col-md-12">
                                      <div class="row">
                                        <div class="col-md-6">
                                          <input data-validation-engine="validate[required,maxSize[4],custom[onlyNumber]]" type="number" maxlength="4" min="1" id="orderCount'.$encPluginsId.'" value="1" class="form-control">
                                        </div>
                                        <div class="col-md-6" style="padding-left:0px">
                                          '.$arrPrice[2].'
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>                        
                                  <div class="row">
                                    <div class="col-md-12 addons-description">
                                      '.$addons['description'].'
                                    </div>
                                  </div>
                                  '.$btOrder.'
                                
                              </div>
                            </div>
                          </div>
                          ';
                          //}
                }
                ?>
              </div>
            </div>
          </div>
          <!-- /.tab-pane -->
          <div class="tab-pane" id="tab_2">
            <div class="box-body">
              <div class="row">
                <?= !empty($myAddons) ? $myAddons : "" ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="trial-dialog">
    <div class="modal-dialog modal-sm">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12" style="text-align:center">
              <img src="" id="img-trial" width="100px">
            </div>
            <div class="col-md-12" >
              <p class="addons-name" id="trial-addons-name"></p>
              <p class="addons-description" id="trial-addons-description"></p>
            </div>

            <div class="col-md-12">
                <div class="row">
                  <div class="col-md-6"><input class="form-control" style="text-align:center" type="text" name="trial-qty" readonly value="10"></div>
                  <div class="col-md-6">
                  Slot
                  </div>
                </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button onclick="takeTrial()" type="button" class="btn btn-primary btn-block"><?= $this->gtrans->line('TRY') ?></button>
        </div>
      </div>
    </div>
  </div>
</section>

<form id="orderPage" target="_blank" method="POST" action="">
  <input type="hidden" name="cEmail" value="<?= $this->session->userdata("ses_email") ?>">
  <input type="hidden" name="cPassw" value="<?= $this->session->userdata("ses_encpassword") ?>">
  <input type="hidden" name="cPosisi" value="owner">
</form>

<script type="text/javascript">
var selectedTrial = "";
var selectedAddonsCode = "";
var url = "<?= base_url() ?>";
var mybillingurl = "<?= MYBILLING_LINK ?>";
var appid = "<?= $this->encryption_org->encode($this->session->userdata("ses_appid")); ?>";
function showTrialDialog(encImg,encTitle,encDesc,encPrice,encUom,encUnitName,encPluginsId,encAddonsCode){
  var showImg      = atob(encImg);
  var showTitle    = atob(encTitle);
  var showDesc     = atob(encDesc);
  var showPrice    = atob(encPrice);
  var showUom      = atob(encUom);
  var showUnitName = atob(encUnitName);
  selectedTrial    = encPluginsId;
  selectedAddonsCode = encAddonsCode;
  $("#img-trial").prop("src",showImg);
  $("#trial-addons-name").html(showTitle);
  $("#trial-addons-description").html(showDesc);
  $("#trial-dialog").modal('show');
}

function takeTrial(){
  $.ajax({
    method : "POST",
    url    : url+"addonsTakeTrial",
    data   : {selectedTrial:selectedTrial,selectedAddonsCode:selectedAddonsCode},
    success: function(res){
      if(res=="success");
      alert("Trial Addons Ordered Successfully!");
      location.reload();
    }
  });
}

function buyAddons(pluginsid){
  var buyingCount = $("#orderCount"+pluginsid).val();
  if(buyingCount==0){
    alert("<?= $this->gtrans->line('How many license that you buy') ?>?");
  }else{
	$.ajax({
		method : "POST",
		url    : url + "addonsPrepareDataForBuy",
		data   : {pluginsid:pluginsid,buyingCount:buyingCount},
		success : function(res){
		    $("#orderPage").prop("action",mybillingurl+"member/external-order-gate/"+res+"/"+appid);
		    $("#orderPage").submit();
		}
	});
  }
}
stat = 0;
jQuery("#form-validation").validationEngine('attach', {
  onValidationComplete: function(form, status){
    if(status==true){
      stat = stat + 1;
      if(stat%2==0){
        var form_data = $("#form-validation").serializeArray();
        form_data.forEach(function(row, index){
          if(row.name=="pluginsid"){
            buyAddons(row.value);
          }
        })
        //
        return false;
      }
    }
  }
});
  stat1 = 0;
</script>
