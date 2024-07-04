<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
<style type="text/css">
  .help-title{
    text-align: center;
    font-size: 30px;
    color: #707070;
  }
  .component-title{
    text-align: center;
    font-size: 25px;
    font-weight: bold;
    color: #707070;
  }
  .component-description{
    text-align: center;
    font-size: 18px;
    color: #707070;
    padding: 0px 65px 0px 65px;
  }
  .image-icons{
    height: 110px;
  }
</style>
<section class="content-header">
    <h1>
      Device Monitor
    </h1>
</section>
<section class="content">
  <div class="row">
    <?= !empty($msg) ? '<div class="col-md-12"><div class="callout callout-success">'.$msg.'</div></div>' : '' ?>
    <div class="col-md-12">
      <div class="nav-tabs-custom">
        <ul class="nav nav-tabs">
          <li<?= $mode=="device-monitor"?' class="active"':'' ?> ><a href="<?= base_url("rootaccess/device-monitor") ?>" >Device</a></li>
          <li<?= $mode=="server-monitor"?' class="active"':'' ?>><a href="<?= base_url("rootaccess/device-monitor/server-monitor") ?>" >Server</a></li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane active" id="tab_1">
            <div class="row">
              <div class="col-md-12">

                <div class="" style="padding-bottom:50px">
                  <div class="box-body">
                    <?php if($mode=="device-monitor"){ ?>
                    <div class="row">
                      <div class="col-lg-4 col-xs-12">
                        <!-- small box -->
                        <div class="small-box bg-green">
                          <div class="inner">
                            <h3><?= $temporaryInfo["processed"] ?></h3>

                            <p>Temporary Processed</p>
                          </div>
                          <div class="icon">
                            <i class="ion ion-checkmark-circled"></i>
                          </div>
                          <a href="#" class="small-box-footer">&nbsp;</a>
                        </div>
                      </div>
                      <div class="col-lg-4 col-xs-12">
                        <div class="small-box bg-blue">
                          <div class="inner">
                            <h3><?= $temporaryInfo["unprocessed"] ?></h3>

                            <p>Temporary Unprocessed</p>
                          </div>
                          <div class="icon">
                            <i class="ion ion-ios-timer-outline"></i>
                          </div>
                          <a href="#" class="small-box-footer">&nbsp;</a>
                        </div>
                      </div>
                      <div class="col-lg-4 col-xs-12">
                        <div class="small-box bg-red">
                          <div class="inner">
                            <h3><?= $temporaryInfo["broken"] ?></h3>

                            <p>Temporary Broken</p>
                          </div>
                          <div class="icon">
                            <i class="ion ion-alert-circled"></i>
                          </div>
                          <a href="#" class="small-box-footer">More Info</a>
                        </div>
                      </div>
                      <div class="col-md-12">
                        <div class="row">
                          <div class="col-md-6">
                            <h3>Reduce Processed Data</h3>
                            <form action="<?= base_url("root/device_monitor/reduceProcessedData") ?>" method="POST">
                              <div class="form-group">
                                <label>How many data reduce</label>
                                <input type="number" value="1" name="delete-limit" id="delete-limit" class="form-control">
                                <input type="checkbox" id="chk-all-data"> All Data
                              </div>
                              <div class="form-group">
                                <button type="submit" name="delete" onclick="return confirm('Are You sure to to reduce the successfull data temporary?')" value="delete" class="btn btn-primary">Reduce</button>
                              </div>
                            </form>
                          </div>
                          <div class="col-md-6">
                            <h3>Setting how much data processed on one time</h3>
                            <form action="<?= base_url("root/device_monitor/saveSetting") ?>" method="POST">
                              <div class="form-group">
                                <label>Set number of process on one time</label>
                                <input type="number" value="<?= $processCount ?>" name="data-processed-count" class="form-control">
                              </div>
                              <div class="form-group">
                                <button type="submit" name="save-setting" value="save-setting" class="btn btn-success">Save Setting</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                      <!--
                      <div class="col-md-12">
                        <div class="pull-right">
                          <i class="fa fa-search" onclick="searchOpen()" id="search-btn" style="cursor:pointer"></i>
                          <i class="fa fa-gear" id="setting-btn" style="cursor:pointer;"></i>
                          <div style="display:none" id="setting">
                            <label>Clear Data From</label>
                            <div class="form-group">
                              <input type="text" name="enddate" class="form-control" id="datepicker">
                            </div>
                            <div class="form-group">
                              <button class="btn btn-primary" id="clear-btn">Clear</button>
                              <button class="btn btn-danger" id="cancel-btn">Cancel</button>
                            </div>
                          </div>
                        </div>
                      </div>
                      -->
                      
                    </div>
                    <div class="row">
                      <div class="col-md-12">
                        <!--
                        <table class="table table-bordered table-hover">
                          <thead>
                            <tr>
                              <th>datetime</th>
                              <th>SN</th>
                              <th>Appid</th>
                              <th>Endpoint</th>
                              <th>Method</th>
                              <th>Data Received</th>
                            </tr>
                          </thead>
                          <tbody id="table-data">
                            
                          </tbody>
                        </table>
                      -->
                      </div>
                    </div>
                  <?php }elseif ($mode=="server-monitor") { ?>
                  <?php 
                  if($firewallStatus=="on"){
                    echo '<div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4><i class="icon fa fa-check"></i> Firewall Is On!</h4>
                            The system device firewall is on, it will keep server cool. <a onclick="return confirm(\'Are you sure?\')" href="'.base_url("root/device_monitor/changeFirewall/off").'" >Click Here</a> to TURN OFF the firewall
                          </div>';
                  }elseif ($firewallStatus=="off") {
                    echo '<div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4><i class="icon fa fa-ban"></i> Firewall Is Off!</h4>
                            This condition make Inact server consume more resource. <a onclick="return confirm(\'Are you sure?\')" href="'.base_url("root/device_monitor/changeFirewall/on").'" >Click Here</a> to TURN ON the firewall
                          </div>';
                  }
                  ?>
                  <?php } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="tab-pane" id="tab_2">
            Tab 2
          </div>
        </div>
      </div>
    </div>
  </div>

</section>

<script type="text/javascript">
  $("#datepicker").datepicker();
  var url = "<?= base_url() ?>";
  var lastID = "";
  var processed = <?= $temporaryInfo["processed"] ?>;
  <?php if($mode=="device-monitor"){ ?>
  function getDataShipment(){
    $.ajax({
      method : "POST",
      url    : url + "root-getDeviceActivity",
      data   : {lastID:lastID},
      success: function(result){
        
        var obj = jQuery.parseJSON(result);
        if(lastID =="" || obj.newLastID > lastID){
          lastID  = obj.newLastID;
          obj.dataMonitor.forEach(function(row, index){
            $("#table-data").prepend('<tr>'+
              '<td>'+row.datetime+'</td>'+
              '<td>'+row.SN+'</td>'+
              '<td>'+row.appid+'</td>'+
              '<td>'+row.endpoint+'</td>'+
              '<td>'+row.method+'</td>'+
              '<td>'+row.data+'</td>'
            );
          });
        }
      }
    });
  }
  

  function stopRealtime(){
    clearInterval(realtimeMonitor);
    $("#switch-btn").html("Start");
    $("#switch-btn").removeClass("btn-danger");
    $("#switch-btn").addClass("btn-success");
    $("#switch-btn").removeAttr("onclick");
    $("#switch-btn").attr("onclick","startInterval()");
  }

  function startInterval(){
    $("#switch-btn").html("Stop");
    $("#switch-btn").removeClass("btn-success");
    $("#switch-btn").addClass("btn-danger");
    $("#switch-btn").removeAttr("onclick");
    $("#switch-btn").attr("onclick","stopRealtime()");
    realtimeMonitor = setInterval(function(){
    //$("#table-data").html("");
    getDataShipment();
    },1000);
  }

  $(document).ready(function(){
    $("#setting-btn").click(function(){
      $("#setting").show(500);
      $("#setting-btn").hide(500);
      $("#search-btn").hide(500);
    });
    
    $("#cancel-btn").click(function(){
      $("#setting").hide(500);
      $("#setting-btn").show(500);
      $("#search-btn").show(500);
      $("#search-btn").show(500);
    });

    $("#clear-btn").click(function(){
      $(".loading").fadeIn(1);
      let datefinish = $("#datepicker").val();
      if(datefinish!=""){
        $.ajax({
          method  : "POST",
          url     : url + "root/device_monitor/clearShipment",
          data    : {datefinish:datefinish},
          success : function(result){
            if(result=="OK"){
              Swal.fire({
                position: 'top-end',
                icon: 'success',
                type: 'success',
                title: 'Data was deleted!',
                showConfirmButton: false,
                timer: 1500
              });
              $("#setting").hide(500);
              $("#setting-btn").show(500);
              $("#search-btn").show(500);
              $(".loading").fadeOut(1);
            }
          }
        });
      }else{
        Swal.fire({
          icon: 'error',
          type: 'error',
          title: 'Oops...',
          text: 'You must set end of deleted data!'
        })
      }
    });
  });
  function searchOpen(){
    window.open(url + "root/device_monitor/searchData","_self");
  }

  <?php }elseif ($mode=="server-monitor") { ?>

  <?php } ?>
  $(document).ready(function(){
    $("#chk-all-data").click(function(){
      if($(this)[0].checked){
        $("#delete-limit").val(processed);
      }else{
        $("#delete-limit").val(1);
      }
    });
  });
</script>