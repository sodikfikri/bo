  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      Dashboard
    </h1>
  </section>
<!-- Main content -->
<section class="content">
  <!-- Info boxes -->
  <!--
  <div class="callout callout-info">
        <h4>Dear para pengguna InAct,</h4>
        

        <p>Mohon maaf pada tanggal 20 Maret 2021 jam 05:00 WIB ada pemeliharaan pada server InAct yang mengakibatkan data log tidak bisa terupload ke cloud InAct.</p>

        <p>Tetapi jangan khawatir, data tetap tersimpan di lokal device utk sabtu-minggu ini.</p>

        <p>Data - data log di InAct akan dapat terupload kembali pada Senin, 22 Maret 2021 pukul 09.00 WIB, semua data absensi di mesin termasuk sabtu-minggu ini akan masuk ke InAct Cloud secara otomatis</p>

        <p>Demikian informasi ini kami sampaikan dan mohon maaf atas ketidaknyamanannya.</p>
  </div>-->
  <?= !empty($msgAddons) ? $msgAddons : '' ?>
  <?= !empty($addonsAlert) ? $addonsAlert : '' ?>
  <div class="row" style="margin-bottom:10px">
    <div class="col-md-4 text-center">
      <label>Periode</label>
      <input onchange="loadGraph1();loadGraph2();loadGraph3()" type="text" id="reservation" class="form-control" placeholder="Periode" value="<?= date("m/d/Y",strtotime(date("Y-m-d")." -10 days")).' - '.date("m/d/Y") ?>">
      <p style="color:red;text-align:left" ><?= $this->gtrans->line("Maximum") ?></p>
    </div>
    <div class="col-md-4 text-center">
      <label>Area</label>
      <select onchange="loadGraph1();loadGraph2();loadGraph3()" name="" class="form-control" name="sArea" id="sArea">
        <option selected disabled value="" /><?= $this->gtrans->line('Select Area') ?>
        <?php
          foreach ($dataArea as $row) {
            echo '<option value="'.$row->area_id.'">'.ucfirst($row->area_name).'</option>';
          }
        ?>
      </select>
    </div>
    <div class="col-md-4 text-center">
      <label><?= $this->gtrans->line('Branch') ?></label>
      <select onchange="loadGraph1();loadGraph2();loadGraph3()" name="" class="form-control" name="sCabang" id="sCabang">
        <option selected value=""/><?= $this->gtrans->line('All Branch') ?>
      </select>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-inact">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $this->gtrans->line('Resign Chart') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-12">
              <p class="text-center">
                <strong><?= $this->gtrans->line('Resign Chart') ?>: <span id="resign-from"></span> <?= $this->gtrans->line('until') ?> <span id="resign-to"></span></strong>
              </p>
              <div class="chart">
                <div id="grafik-resign"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-6">
      <div class="box box-inact">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $this->gtrans->line('Mutation In') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-12">
              <p class="text-center">
                <strong><?= $this->gtrans->line('Mutation In') ?>: <span id="mutation-in-from"></span> <?= $this->gtrans->line('until') ?> <span id="mutation-in-to"></span></strong>
              </p>
              <div class="chart">
                <div id="grafik-mutation-in"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="box box-inact">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $this->gtrans->line('Mutation Out') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-12">
              <p class="text-center">
                <strong><?= $this->gtrans->line('Mutation Out') ?>: <span id="mutation-out-from"></span> <?= $this->gtrans->line('until') ?> <span id="mutation-out-to"></span></strong>
              </p>
              <div class="chart">
                <div id="grafik-mutation-out"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-md-12">
      <div class="box box-inact">
        <div class="box-header with-border">
          <h3 class="box-title"><?= $this->gtrans->line('All Location Summary') ?></h3>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
          <div class="row">
            <div class="col-md-12">
              <div class="row" style="margin-bottom:10px">
                <div class="col-md-4 text-center">
                  <label><?= $this->gtrans->line('Area') ?></label>
                  <select name="sArea1" id="sArea1" class="form-control">
                    <option selected value="" /><?= $this->gtrans->line('All') ?>
                    <?php
                      foreach ($dataArea as $row) {
                        echo '<option value="'.$row->area_id.'">'.ucfirst($row->area_name).'</option>';
                      }
                    ?>
                  </select>
                </div>
                <div class="col-md-4 text-center">
                  <label><?= $this->gtrans->line('Branch') ?></label>
                  <select name="sCabang1" id="sCabang1" class="form-control">
                    <option selected value="" /><?= $this->gtrans->line('All') ?>
                  </select>
                </div>
              </div>
            </div>
            <div class="col-md-12">
              <div id="location-review"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script type="text/javascript">
var url = "<?= base_url() ?>";
$(document).ready(function(){
  loadGraph1();
  loadGraph2();
  loadGraph3();
  $("#sArea").change(function(){
    var sArea = $(this).val();
    loadsBranch(sArea,"#sCabang");

  });
  $("#sArea1").change(function(){
    var sArea = $(this).val();
    loadsBranch(sArea,"#sCabang1");
    loadLocationSummary();
  });
  $("#sCabang1").change(function(){
    loadLocationSummary();
  });
  loadLocationSummary();
});
function loadsBranch(area,target,selectedBranch=''){
  $(target).html("");
  $(target).append('<option value="" >All Branch</option>');
  $.ajax({
    method : 'POST',
    url    : url + "load-cabang",
    data   : {area,area},
    success: function(res){
      var arrObj = jQuery.parseJSON(res);
      arrObj.branchs.forEach(function(row,index){
        if(selectedBranch!=''){
          var selected = 'selected';
        }else{
          var selected = '';
        }
        $(target).append('<option '+selected+' value="'+row.id+'" >'+row.name+'</option>');
      });
    }
  });
}

<?php if(!empty($msgCommand) && $msgCommand=="yes"){ ?>
Swal.fire({
  title: '<?= $this->gtrans->line("There is command need communicate to device") ?>',
  //showDenyButton: true,
  showCancelButton: true,
  confirmButtonText: 'Open Communication',
  denyButtonText: `Close`,
}).then((result) => {
  /* Read more about isConfirmed, isDenied below */
  if (result.isConfirmed) {
    $("#loader").fadeIn(1);
    $.ajax({
      method : "GET",
      url    : url + "master/device/openFirewall",
      success: function(res){
        if(res=="ok"){
          Swal.fire({
            position: 'top-middle',
            icon: 'success',
            title: 'Device communication was open',
            showConfirmButton: false,
            timer: 1500
          });
          $("#loader").fadeOut(1);
        }
      }
    });
  } else if (result.isDenied) {
    Swal.fire('Changes are not saved', '', 'info')
  }
  
})
<?php } ?>

</script>
