<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?= $this->gtrans->line("Mutation") ?>
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
            <?php //!empty($licenseInfo) ? $licenseInfo : '' ?>
            <?= !empty($notif) ? $notif : "" ?>
            <div class="pull-right col-md-12" style="margin-bottom:10px">
              <div class="row">
                <div class="col-md-2">
                  <label><?= $this->gtrans->line("Area") ?></label>
                  <select  name="sArea" id="sArea" class="form-control">
                    <option value="-" ><?= $this->gtrans->line("Select One Area") ?></option>
                    <?php
                      foreach ($dataArea as $row) {
                        echo '<option value="'.$row->area_id.'">'.ucfirst($row->area_name).'</option>';
                      }
                    ?>
                  </select>
                </div>
                <div class="col-md-2">
                  <label><?= $this->gtrans->line("Branch") ?></label>
                  <select  name="sCabang" id="sCabang" class="form-control">
                    <option value="-" ><?= $this->gtrans->line("Select One Branch") ?></option>
                  </select>
                </div>
                <div class="col-md-2">
                  <label>Status</label>
                  <select  name="sStatus" id="sStatus" class="form-control">
                    <option value="new" >New</option>
                    <option value="pending" >Pending</option>
                    <option value="success" >Success</option>
                  </select>
                </div>
                <div class="col-md-2">
                  <label><?= $this->gtrans->line("Name") ?></label>
                  <input type="text" name="name" id="name" class="form-control">
                </div>
                <div class="col-md-2" style="padding-top: 25px">

                  <button onclick="draw_dt()" class="btn btn-primary btn-block"><?= $this->gtrans->line("View") ?></button>
                  <!--<button class="btn btn-success">Set Selected To Mutation</button>-->
                </div>
                <div class="col-md-2" style="padding-top: 25px">
                  <button id="btnSetMutation" class="btn btn-primary btn-block" onclick="showGroupMutationPanel()"><?= $this->gtrans->line("Set Mutation") ?></button>
                  <button id="btnRemoveMutation" class="btn btn-danger btn-block" style="display:none;margin-top:0px" onclick="cancelGroupMutation()"><?= $this->gtrans->line("Cancel Mutation") ?></button>
                </div>
              </div>
            </div>
            <br>
            <!--<div id="table-mutation"></div>-->
            <div class="col-md-12">
            <div id="msg"></div>
            <table class="table table-bordered" id="tb-mutation" width="100%">
              <thead>
                <th class="text-center"><input onclick="checkAll(this)" type="checkbox" name="checkall" id="checkall"></th>
                
                <th class="text-center">ID</th>
                <th class="text-center"><?= $this->gtrans->line("Full Name") ?></th>
                <th class="text-center"><?= $this->gtrans->line("Active Location") ?></th>
                <th class="text-center"><?= $this->gtrans->line("Destination Branch") ?></th>
                
              </thead>
            </table>
          </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</section>

  <div class="modal fade" id="frmGroupMutation">
  <div class="modal-dialog modal-default modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><?= $this->gtrans->line("New Mutation") ?></h4>
        </div>
        <?= form_open("save-mutation",["id"=>"form-validation","class"=>"form-horizontal"]); ?>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div id="selected-employee" style="overflow-y: scroll;max-height: 300px"></div>
            </div>
            <div class="col-md-6">
              <input type="hidden" name="id" id="id" value="">
              <p class="text-red" style="text-align:center;margin-bottom:20px"><strong><?= $this->gtrans->line("Caution : Employee will remove from all location source") ?></strong></p>
              <!--
              <div class="form-group">
                <label for="" class="col-sm-4 control-label"><?= $this->gtrans->line("Remove from this location") ?></label>
                <div class="col-sm-8">
                  <div id="location-source"></div>
                </div>
              </div>
              -->
              <div class="form-group">
                <label for="accountno" class="col-sm-4 control-label"><?= $this->gtrans->line("New Location") ?> <span class="text-red">*</span></label>
                <div class="col-sm-8">
                  <div class="row">
                    <div class="col-md-6">
                      <div>
                        <span class="label label-default"><?= $this->gtrans->line("Set Area") ?></span>
                      </div>
                      <?php
                        $arrAreaID = [];
                        foreach ($dataArea as $row) {
                          echo '<input name="area[]" data-validation-engine="validate[required]" id="chkarea'.$row->area_id.'" onclick="showBranch('.$row->area_id.',this.checked)" type="checkbox" value="'.$row->area_id.'" > '.strtoupper($row->area_name).'<br>';
                          $arrAreaID[] = $row->area_id;
                        }
                        $strAreaID = implode(",",$arrAreaID);
                      ?>
                    </div>
                    <div class="col-md-6">
                      <div>
                        <span class="label label-default"><?= $this->gtrans->line("Set Branch") ?></span>
                      </div>
                        <div id="chkCabang"></div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label class="col-sm-4 control-label"><?= $this->gtrans->line("Effective Date") ?> <span class="text-red">*</span></label>
                <div class="col-sm-8">
                  <input name="date-effective" type="text" data-validation-engine="validate[required]" class="form-control datepicker" id="dateresign" >
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-long-arrow-left"></i> <?= $this->gtrans->line("Cancel") ?></button>
          <button type="button" onclick="saveMutation()" class="btn btn-primary"><?= $this->gtrans->line("Save Mutation") ?></button>
        </div>
        <?= form_close() ?>
      </div>
      <!-- /.modal-content -->
    </div>
  <!-- /.modal-dialog -->
  </div>

  <div class="modal fade" id="destination-modal">
    <div class="modal-dialog modal-default">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?= $this->gtrans->line("Destination") ?></h4>
          </div>
          <div class="modal-body">
            <div class="row">
              <div id="data-mutation-location"></div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-long-arrow-left"></i> <?= $this->gtrans->line("Close") ?></button>
          </div>
        </div>
      </div>
    </div>
<script type="text/javascript">
  $(document).ready(function(){
    $("#name").keypress(function(e) {
      if(e.which == 13) {
          loadTableEmployee();
      }
    });
  });

  var url = "<?= base_url() ?>";
  var locationSource = [];
  var arrArea = [<?= $strAreaID ?>];
  <?php
    $arrArea = '[';
    foreach ($dataArea as $row) {
      $arrArea .= $row->area_id.",";
    }
    $arrArea .= ']';
    echo 'var lsArea = '.$arrArea.';';
  ?>
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  });

  $('.datepicker').datepicker({
    autoclose: true
  });
  function loadBranch(area,selectedBranch=''){
    $("#branchname").html("");
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
          $("#branchname").append('<option '+selected+' value="'+row.id+'" >'+row.name+'</option>');
        });
      }
    });
  }

  function loadsBranch(area,selectedBranch=''){
    $("#sCabang").html("");
    $("#sCabang").append('<option value="-" ><?= $this->gtrans->line("Select One Branch") ?></option>');
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
          $("#sCabang").append('<option '+selected+' value="'+row.id+'" >'+row.name+'</option>');
        });
      }
    });
  }

  function loadSource(employeeid,dontShowthis=''){
    /*
    $.ajax({
      method : "POST",
      url    : url + "load-employee-source",
      data   : {employeeid:employeeid,dontShowthis:dontShowthis},
      success: function(res){
        var obj = $.parseJSON(res);
        locationSource = obj.allBranchSource;
        $("#location-source").html(obj.tableSource);
      }
    }); */
  }

  $(document).ready(function(){
    $("#area").change(function(){
      var area = $(this).val();

      loadBranch(area);
    });
    $("#sArea").change(function(){
      var sArea = $(this).val();
      loadsBranch(sArea);
    });

    /*
    $("#sStatus").change(function(){
      let status = $(this).val();
      
      if(status=="new"){
        $("#btnSetMutation").show(100);
        $("#btnRemoveMutation").hide(100);
      }else if (status=="pending") {
        $("#btnSetMutation").hide(100);
        $("#btnRemoveMutation").show(100);
      }else if (status=="success") {
        $("#btnSetMutation").hide(100);
        $("#btnRemoveMutation").hide(100);
      }
    });
    */

  });

  function newMutation(locId,encName,employeeid){
    loadSource(employeeid,locId);
    $("#id").val(locId);
    $("#frmMutation").modal("show");

    $("#employeename").val(atob(encName));
    $("#chkCabang").html("");
    lsArea.forEach(function(row){
      $("#chkarea"+row).prop('checked',false);
    });
  }

  function cancelMutation(MutationId,encName){
    Swal.fire({
      title: "<?= $this->gtrans->line("Are you sure want to cancel") ?> "+atob(encName)+" \`s mutation?",
      text: "<?= $this->gtrans->line("You won`t be able to revert this") ?>!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: '<?= $this->gtrans->line("Yes, delete it") ?>!',
      cancelButtonText: '<?= $this->gtrans->line("Cancel") ?>!',
    }).then((result) => {
      if (result.value) {
        $.ajax({
          method : "POST",
          url : url + "cancel-mutation",
          data : {mutation_id : MutationId},
          success : function(res){
            if(res=="OK"){

              Swal.fire(
                '<?= $this->gtrans->line("Deleted") ?>!',
                '<?= $this->gtrans->line("The mutation queue have been deleted") ?>.',
                'success'
              )
              draw_dt();
            }
          }
        });
      }
    })
  }

  function cancelGroupMutation(){
    if(selectedEmployee.length>0){
      Swal.fire({
        title: "<?= $this->gtrans->line("Are you sure want to cancel mutation from selected employee") ?> ?",
        text: "<?= $this->gtrans->line("You won`t be able to revert this") ?>!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '<?= $this->gtrans->line("Yes, delete it") ?>!',
        cancelButtonText: '<?= $this->gtrans->line("Cancel") ?>!',
      }).then((result) => {
        if (result.value) {
          $(".loading").fadeIn(1);
          $.ajax({
            method : "POST",
            url    : url + "cancel-group-mutation",
            data   : {selectedEmployee : selectedEmployee},
            success: function(res){
              if(res=="OK"){
                Swal.fire(
                  '<?= $this->gtrans->line("Deleted") ?>!',
                  '<?= $this->gtrans->line("The mutation queue have been deleted") ?>.',
                  'success'
                )
                draw_dt();
                $(".loading").fadeOut(1);
              }
            }
          });
        }
      })
    }else{
      Swal.fire({
        type: 'error',
        title: 'Oops...',
        text: '<?= $this->gtrans->line("You must select at least one employee") ?>!' //,
        //footer: ''
      });
    }
  }
  /*
  function loadTableEmployee(){

    var sArea   = $("#sArea").val();
    var sCabang = $("#sCabang").val();
    var sStatus = $("#sStatus").val();
    var name    = $("#name").val();
    if(sArea!="" && sCabang!="" && sStatus!=""){
      $.ajax({
        type : "POST",
        url  : url + "load-table-mutation",
        data : {sArea:sArea,sCabang:sCabang,sStatus:sStatus,name:name},
        success : function(res){
          var obj = jQuery.parseJSON(res);
          $("#table-mutation").html(obj);
        }
      });
    }
  }
  */
  function showBranch(areaID,chkChecked,checkedComponens = []){
    if(chkChecked==true){

      $.ajax({
        method : 'POST',
        url    : url + "load-cabang",
        data   : {area:areaID},
        success: function(res){
          var arrObj = jQuery.parseJSON(res);
          $("#chkCabang").append('<strong id="area'+areaID+'">'+arrObj.areaname+'</strong>');
          arrObj.branchs.forEach(function(row,index){
            if(checkedComponens.indexOf(row.id)>=0){
              var checked = 'checked';
            }else{
              var checked = '';
            }
            if(locationSource.indexOf(row.id)>=0){
              $("#chkCabang").append('<div id="cabang'+row.id+'" style="color:red"><i class="fa fa-times-circle-o"></i> '+row.name+'<div>');
            }else{
              $("#chkCabang").append('<div id="cabang'+row.id+'"><input  '+checked+' id="checkboxcabang'+row.id+'" type="checkbox" name="cabang[]" value="'+areaID+'.'+row.id+'" > '+row.name+'<div>');
            }

          });
        }
      });
      //console.log('Checked!');
    }else{
      //console.log('NotChecked!');
      $.ajax({
        method : 'POST',
        url    : url + "load-cabang",
        data   : {area:areaID},
        success: function(res){
          var arrObj = jQuery.parseJSON(res);
          $("#area"+areaID).remove();
          arrObj.branchs.forEach(function(row,index){
            $("#cabang"+row.id).remove();

          });
        }
      });
    }
  }
  /*
  function switchLicense(checkedStatus,employeeID){
    if(checkedStatus==true){
      var status = 'active';
    }else{
      var status = 'inactive';
    }

    $.ajax({
      method : "POST",
      url    : url + "employee-switch-license",
      data   : {employee:employeeID,status:status},
      success: function(res){
        if(res=="failed"){
          alert('<?= $this->gtrans->line("You have no employee slot") ?>!');
          $('#toggleSwitch'+employeeID).click();
        }
      }
    });
  }
  */
  var selectedResign;
  function setResign(idEmp){
    selectedResign = idEmp;
    $("#frmResign").modal("show");
  }
  /*
  function submitResign(){
    var dateresign = $("#dateresign").val();
    $.ajax({
      method : "POST",
      url    : url + "employee-resign",
      data   : {id:selectedResign,dateresign:dateresign},
      success: function(res){
        if(res=="ok"){
          alert("Resign Employee Complete");
          loadTableEmployee();
          selectedResign = "";
          $("#dateresign").val("");
          $("#frmResign").modal("hide");
        }
      }
    });
  }
  */
  function checkAccountNo(){
    var accountno = $("#accountno").val();
    $.ajax({
      method : "POST",
      url : url + "checkAccountNoExist",
      data : {no_account:accountno},
      success : function(res){
        if(res=="yes"){
          $("#msg").html('<p class="text-red"><?= $this->gtrans->line("Not Available") ?></p>');
          $("#accountno").val("");
        }else if(res=="no"){
          $("#msg").html('<p class="text-green"><?= $this->gtrans->line("Available") ?></p>');
        }
      }
    });
  }
  function showDestination(mutationid){
    $("#destination-modal").modal('show');
    $.ajax({
      method : "POST",
      url    : url + "get-mutation-destination",
      data   : {mutationid:mutationid},
      success: function(res){
        var obj = jQuery.parseJSON(res);
        $("#data-mutation-location").html(obj);
      }
    });
  }
  $(function () {

  });
  var txtArea = $("#sArea").val();
  var DTcostumized = $('#tb-mutation').DataTable({
    ordering  : false,
    processing: true,
    serverSide: true,
    processing: true,
    scrollX   : true,
    scrollCollapse: true,
    searching :false,
    "language": {
      "zeroRecords": ""
    },
    ajax: {
       url : url + "ajax-get-mutation-data",
       type: 'POST',
       data: function ( data ) {
                data.sArea = $("#sArea").val()
                data.sCabang = $("#sCabang").val()
                data.sStatus = $("#sStatus").val()
                data.name = $("#name").val()
       },
       complete: function (data) {
         $(".loading").fadeOut(1);
         if(data.responseJSON.emptyMessage!="none"){
           $("#msg").html(data.responseJSON.emptyMessage);
         }else{
           $("#msg").html("");
         }
       }
    }
  });

  function draw_dt(){
    $(".loading").fadeIn(1);
    DTcostumized.ajax.reload();
    selectedEmployee = [];
    selectedEmployeeName = [];
    
    $("#checkall").prop("checked", false);
    
    let status = $("#sStatus").val();
      
    if(status=="new"){
      $("#btnSetMutation").show(100);
      $("#btnRemoveMutation").hide(100);
    }else if (status=="pending") {
      $("#btnSetMutation").hide(100);
      $("#btnRemoveMutation").show(100);
    }else if (status=="success") {
      $("#btnSetMutation").hide(100);
      $("#btnRemoveMutation").hide(100);
    }
  }

  function saveMutation(){
    var obj    = $("#form-validation").serialize();
    var objArr = $("#form-validation").serializeArray();
    var branchCount = 0;
    objArr.forEach(function(row,index){
      if(row.name=="cabang[]"){
        branchCount += 1;
      }
    });
    if($("#dateresign").val()!=""){
      if(branchCount==0){
        Swal.fire({
          type: 'error',
          title: 'Oops...',
          text: '<?= $this->gtrans->line("You must set at least one branch") ?>!' //,
          //footer: ''
        });
      }else{
        $(".loading").fadeIn(1);
        $.ajax({
          method : "POST",
          url    : url + "save-mutation",
          data   : obj+"&selectedEmployee="+selectedEmployee,
          success: function(res){
            if(res=="ok"){
              $("#frmGroupMutation").modal('hide');
              // $("#chkCabang").html("");
              
              resetPanel();
              
              /*
              arrArea.forEach(function(row,index){
                $("#chkarea"+row).removeAttr("checked");
              });
              $("#dateresign").val("");
              */
              draw_dt();
              $(".loading").fadeOut(1);
            }
          }
        });
      }
    }else{
      Swal.fire({
        type: 'error',
        title: 'Oops...',
        text: '<?= $this->gtrans->line("You must Effective date") ?>!' //,
      });
    }
  }
  function checkAll(komponen){
    var inputComponen = document.getElementsByTagName("input");

    for(i=0;i<inputComponen.length;i++){
      if(inputComponen[i].type==="checkbox" && inputComponen[i].id==="employee-to-mutate"){
        var chkValue = inputComponen[i].value;
        
        var arrValue = chkValue.split("|");
        if(komponen.checked){
          inputComponen[i].checked = true;

          if(selectedEmployee.includes(arrValue[0])!=true){
            selectedEmployee.push(arrValue[0]);
          }
          if(selectedEmployeeName.includes(arrValue[1])!=true){
            selectedEmployeeName.push(arrValue[1]);
          }
        }else{
          inputComponen[i].checked = false;
          selectedEmployee = removeArrayElement(arrValue[0],selectedEmployee);
          selectedEmployeeName = removeArrayElement(arrValue[1],selectedEmployeeName);
        }
      }
    }
    //console.log(selectedEmployeeName);
  }
  
  var selectedEmployee = [];
  var selectedEmployeeName = [];  
  
  function showGroupMutationPanel(){
    if(selectedEmployee.length>0){
      $.ajax({
        method : "POST",
        url    : url + "transaction/mutation/getDataEmployee",
        data   : {employees:selectedEmployee},
        success: function(res){
          $("#selected-employee").html(jQuery.parseJSON(res));
          $("#frmGroupMutation").modal('show');
        }
      });
    }else{
      Swal.fire({
        type: 'error',
        title: 'Oops...',
        text: '<?= $this->gtrans->line("You must select at least one employee") ?>!' //,
        //footer: ''
      });
    }
  }

  function selectEmployee(empID, empName, komponen){
    if (komponen.checked) {
      if(selectedEmployee.includes(empID)!=true){
        selectedEmployee.push(empID);
      }
      if(selectedEmployeeName.includes(empName)!=true){
        selectedEmployeeName.push(empName);
      }
    }else{
      selectedEmployee = removeArrayElement(empID,selectedEmployee);
      selectedEmployeeName = removeArrayElement(empName,selectedEmployeeName);
    }
    console.log(selectedEmployeeName);
  }

  function resetPanel(){
    $("#dateresign").val("");
    
    arrArea.forEach(function(row,index){
      $("#chkarea"+row).prop("checked",false);
    });
    $("#chkCabang").html("");
  }

  $('#frmGroupMutation').on('hidden.bs.modal', function () {
    resetPanel();
  });
</script>
