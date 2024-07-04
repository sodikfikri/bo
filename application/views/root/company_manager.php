<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
<style type="text/css">
  .pointer{
    cursor:pointer;
  }
</style>
<section class="content-header">
    <h1>
      Company Manager
    </h1>
</section>
<section class="content">
<div class="row">
  <div class="col-md-12">
    <div class="box box-inact" style="padding-bottom:50px">
      <div class="box-body">
        <div class="row">
          <div class="col-md-12">
            <?= !empty($msg) ? $msg : '' ?>
            <?= !empty($dataPerusahaan) ? $dataPerusahaan : '' ?>
          </div>
        </div>
        
      </div>
    </div>
  </div>
</div>
</section>
<script type="text/javascript">
  var url = "<?= base_url() ?>";
  function switchCompanyType(id,addr){
    let isreal = $("#cs"+addr).attr("value");
    $.ajax({
      method : "POST",
      url    : url + "root/company_manager/switchCompanyType",
      data   : {id:id,isreal:isreal},
      success: function(){
        if(isreal=="yes"){
          $("#cs"+addr).attr("value","no");
          $("#cs"+addr).removeClass("text-green");
          $("#cs"+addr).addClass("text-red");
          $("#cs"+addr).html('<i class="fa fa-link"></i> Internal Account');
        }else if(isreal=="no"){
          $("#cs"+addr).attr("value","yes");
          $("#cs"+addr).removeClass("text-red");
          $("#cs"+addr).addClass("text-green");
          $("#cs"+addr).html('<i class="fa fa-unlink"></i> Real Company');
        }    
      }
    });
    
    
  }

</script>
