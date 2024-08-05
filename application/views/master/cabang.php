<style>
.pac-container {
    z-index: 1051 !important;
}
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?= $this->gtrans->line("Master Branch") ?>
  </h1>
</section>
<?php $new_arr[]= unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip=182.253.50.128')); ?>
<!-- Main content -->
<section class="content">
<!-- Info boxes -->
<?= !empty($addonsAlert) ? $addonsAlert : '' ?>
<div class="row">
  <div class="col-md-12">
    <div class="box box-inact">
      <!-- /.box-header -->
	  <?php
		$appid = $this->session->userdata("ses_appid"); 
		if($appid=='IA01M6858F20210906256'){ ?>
		
	  <?php } ?>
      <div class="box-body">
        <div class="row">
          <div class="col-md-12">
            <?= !empty($notif) ? $notif : "" ?>
            <button type="button" class="btn btn-primary" data-toggle="modal" onclick="addNew()"><i class="fa fa-pencil"></i> <?= $this->gtrans->line("New Branch") ?></button><br><br>
			
			<div class="table table-responsive" width="100%" style="padding-top:10px">
            <?= !empty($branchTable) ? $branchTable : "" ?>
          </div>
          </div>
          
        </div>
      </div>
    </div>
  </div>
</div>
</section>
<div class="modal fade" id="frmCabang">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><div id="frm-text"></div></h4>
        </div>
        <?= form_open("save-branch",["id"=>"form-validation","class"=>"form-horizontal formBranch"]); ?>
        <input type="hidden" name="reboot" id="reboot">
        <div class="modal-body">
          <input type="hidden" name="id" id="id">
          <div class="form-group">
            <label for="area" class="col-sm-3 control-label"><?= $this->gtrans->line("Area Name") ?> <span class="text-red">*</span></label>
            <div class="col-sm-9">
              <?= $cmbArea ?>
            </div>
          </div>
          <div class="form-group">
            <label for="branchcode" class="col-sm-3 control-label"><?= $this->gtrans->line("Branch Code") ?> <span class="text-red">*</span></label>
            <div class="col-sm-9">
              <input onchange="checkExists('branchcode','msg-code','check-cabang-code-exists','<?= $this->gtrans->line("Code was used by deleted or existing data") ?>','<?= $this->gtrans->line("Branch Code Is Available") ?>',$('#id').val())" name="branchcode" data-validation-engine="validate[required,maxSize[50],custom[onlyLetterNumberSemiSpesial]]" type="text" class="form-control" id="branchcode" placeholder="<?= $this->gtrans->line("Branch Code") ?>">
              <div id="msg-code"></div>
            </div>
          </div>
          <div class="form-group">
            <label for="branchname" class="col-sm-3 control-label"><?= $this->gtrans->line("Branch Name") ?> <span class="text-red">*</span></label>
            <div class="col-sm-9">
              <input onchange="checkExists('branchname','msg-name','check-cabang-name-exists','<?= $this->gtrans->line("Branch Name was used by existing data") ?>','<?= $this->gtrans->line("Branch Name Is Available") ?>',$('#id').val())" name="branchname" type="text" data-validation-engine="validate[required,maxSize[100],custom[onlyLetterNumberSemiSpesial]]" class="form-control" id="branchname" placeholder="<?= $this->gtrans->line("Branch Name") ?>">
              <div id="msg-name"></div>
            </div>
          </div>
          <div class="form-group">
            <label for="timezone" class="col-sm-3 control-label"><?= $this->gtrans->line("TimeZone") ?> <span class="text-red">*</span></label>
            <div class="col-sm-9">
              <?= $cmbTimezone ?>
            </div>
          </div>
          <div class="form-group">
            <label for="address" class="col-sm-3 control-label"><?= $this->gtrans->line("Address") ?> <span class="text-red">*</span></label>
            <div class="col-sm-9">
              <input name="address" type="text" data-validation-engine="validate[required]" class="form-control" id="address" placeholder="<?= $this->gtrans->line("Address") ?>">
			  <label style="color:#FF9800">nb: memilih rekomendasi lokasi dengan menggunakan arah panah, selanjutnya tekan tab untuk lokasi yang dipilih.</label>
            </div>
          </div>
		  <div class="form-group">
            <label for="longitude" class="col-sm-3 control-label"><?= $this->gtrans->line("Longitude") ?> </label>
            <div class="col-sm-9">
              <input name="longitude" type="number" step=".00000000000000001" data-validation-engine="validate[maxSize[100]]" class="form-control longitude" id="longitude" placeholder="<?= $this->gtrans->line("Longitude") ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="latitude" class="col-sm-3 control-label"><?= $this->gtrans->line("Latitude") ?> </label>
            <div class="col-sm-9">
              <input name="latitude" type="number" step=".00000000000000001" data-validation-engine="validate[maxSize[100]]" class="form-control latitude" id="latitude" placeholder="<?= $this->gtrans->line("Latitude") ?>">
            </div>
          </div>
		  <div class="form-group">
			<label for="maps" class="col-sm-3 control-label"><?= $this->gtrans->line("Maps") ?> </label>
			<div class="col-sm-9">
				<div id="map-canvas" style="height: 400px;width: 100%;"></div>
				<input type="hidden" class="city" placeholder="City">
			</div>
		  </div>
          <div class="form-group">
            <label for="contactnumber" class="col-sm-3 control-label"><?= $this->gtrans->line("Contact Number") ?></label>
            <div class="col-sm-9">
              <input data-validation-engine="validate[custom[phone],maxSize[20]]" name="contactnumber" type="text"  class="form-control" id="contactnumber" placeholder="<?= $this->gtrans->line("Contact Number") ?>">
            </div>
          </div>
          <div class="form-group">
            <label for="description" class="col-sm-3 control-label"><?= $this->gtrans->line("Description") ?></label>
            <div class="col-sm-9">
              <textarea data-validation-engine="validate[custom[onlyLetterNumberSemiSpesial]]" name="description" rows="4" cols="80" id="description" class="form-control" placeholder="<?= $this->gtrans->line("Description") ?>"></textarea>
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
  <div class="modal fade" id="showDetailEmployee">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><div id="show-detail-employee"></div></h4>
        </div>
        <div class="modal-body">
          <div id="list-detail-employee"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-long-arrow-left"></i> <?= $this->gtrans->line("Close") ?></button>
        </div>
        <?= form_close() ?>
      </div>
      <!-- /.modal-content -->
    </div>
  <!-- /.modal-dialog -->
  </div>
  <div class="modal fade" id="frmCabangMethod">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><div id="frm-text-method"></div></h4>
        </div>
        <?= form_open("save-branch-method",["id"=>"form-validation","class"=>"form-horizontal formBranchMethod"]); ?>
        <input type="hidden" name="reboot" id="reboot">
        <div class="modal-body">
          <input type="hidden" name="idmethod" id="idmethod">
          <div class="form-group">
            <label for="method" class="col-sm-3 control-label"><?= $this->gtrans->line("Presence Method") ?> <span class="text-red">*</span></label>
            <div class="col-sm-9">
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
          </div>
		  <div class="form-group">
            <label for="mode" class="col-sm-3 control-label"><?= $this->gtrans->line("Presence Mode") ?> <span class="text-red">*</span></label>
            <div class="col-sm-9">
                <div>
                    <div class="radio">
                      <label><input type="radio" name="presence_mode" value="online" id="presence_mode_online"> Online</label>
                    </div>
                    <div class="radio">
                      <label><input type="radio" name="presence_mode" value="online-offline" id="presence_mode_onlineoffline"> Online-Offline</label>
                    </div>
                </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-left" data-dismiss="modal"><i class="fa fa-long-arrow-left"></i> <?= $this->gtrans->line("Cancel") ?></button>
          <button type="submit" class="btn btn-primary"><div id="txtBtnSaveMethod"></div></button>
        </div>
        <?= form_close() ?>
      </div>
      <!-- /.modal-content -->
    </div>
  <!-- /.modal-dialog -->
  </div>
  <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBkLKNQj9zEMKoCDj9lZKmG2CDi9ZVp8p0&libraries=places&callback=initialize"></script>
<script type="text/javascript">
  var url = "<?= base_url() ?>";
  var existingTimezone = '';
  stat = 0;
  jQuery("#form-validation").validationEngine('attach', {
    onValidationComplete: function(form, status){
      if(status==true){
        stat = stat + 1;
        if(stat%2==0){
          
          if(existingTimezone!=$("#timezone").val()){
            Swal.fire({
              title: 'Reboot Device?',
              text: "This action will reboot all device below branch!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#3085d6',
              cancelButtonColor: '#d33',
              confirmButtonText: 'Yes, Reboot!'
            }).then((result) => {
              $("#reboot").val("yes");
              if(result.value==true){

                var formdata = $('#form-validation').serialize();
                $.ajax({
                  method : "POST",
                  url    : url + "save-branch",
                  data   : formdata,
                  success: function (){
                    location.reload();
                  }
                });
              }
              
            });
          }else{
            $("#reboot").val("");
            var formdata = $('#form-validation').serialize();
            $.ajax({
              method : "POST",
              url    : url + "save-branch",
              data   : formdata,
              success: function (){
                location.reload();
              }
            });
          }
          
        }
        return false;
      }
    }
  });
  
	$(document).ready(function(){
	  $("#btn-search").click(function(){
		  //alert("clicked");
			draw_dt();
	  });
	});
	
	function draw_dt(){
		var formData = {
		  areaid: $("#sArea").val()
		};
        $.ajax({
            method : "POST",
            url    : url + "filter-branch",
            data   : formData,
            success: function (){
				location.reload();
            }
        });
	}

  $('.select2').select2({
    dropdownParent: $("#frm-text")
  });
  function addNew(){
    $("#timezone").select2('destroy');
    $("#timezone").val('QXNpYS9KYWthcnRhfFVUQyswNzowMA==');
    $(".select2").select2({
      dropdownParent: $("#frm-text"),
    });

    $("#txtBtnSave").html('<i class="fa fa-check-circle"></i> Save');
    $("#frm-text").html('<?= $this->gtrans->line("Add New Branch") ?>');
    $("#id").val("");
    $("#area").val("");
    $("#branchcode").val("");
    $("#branchname").val("");
    $("#address").val("");
    $("#longitude").val("");
    $("#latitude").val("");
    $("#contactnumber").val("");
    $("#description").val("");
    $("#frmCabang").modal('show');
    
    // mengosongkan existing timezone
    existingTimezone = '';

  }

  function edit(id,area,code,name,timezone,address,longitude,latitude,contact,description){
    existingTimezone = atob(timezone);

    $("#txtBtnSave").html('<i class="fa fa-check-circle"></i> <?= $this->gtrans->line("Save Changes") ?>');
    $("#frm-text").html('<?= $this->gtrans->line("Edit Branch") ?>');
    $("#id").val(id);
    $("#area").val(atob(area));
    $("#branchcode").val(atob(code));
    $("#branchname").val(atob(name));
    $("#timezone").select2('destroy');
    $("#timezone").val(atob(timezone));
    $(".select2").select2({
      dropdownParent: $("#frm-text"),
      });
    $("#address").val(atob(address));
    $("#longitude").val(atob(longitude));
    $("#latitude").val(atob(latitude));
    $("#contactnumber").val(atob(contact));
    $("#description").val(atob(description));
    // mengosongkan existing timezone
    
    $("#frmCabang").modal('show');
  }
  function editMethod(id,method,mode){
    $("#txtBtnSaveMethod").html('<i class="fa fa-check-circle"></i> <?= $this->gtrans->line("Save Changes") ?>');
    $("#frm-text-method").html('<?= $this->gtrans->line("Edit Branch Setting") ?>');
    $("#idmethod").val(id);
	var strMethod = atob(method);
	var myMethod = strMethod.split("|");
	if(myMethod.includes("1")){$("#pin").prop("checked", true);} else {$("#pin").prop("checked", false);}
	if(myMethod.includes("2")){$("#finger").prop("checked", true);} else {$("#finger").prop("checked", false);}
	if(myMethod.includes("3")){$("#face").prop("checked", true);} else {$("#face").prop("checked", false);}
	if(myMethod.includes("4")){$("#pic").prop("checked", true);} else {$("#pic").prop("checked", false);}
	if(atob(mode)=='online'){$("#presence_mode_online").prop("checked", true);} else {$("#presence_mode_online").prop("checked", false);}
	if(atob(mode)=='online-offline'){$("#presence_mode_onlineoffline").prop("checked", true);} else {$("#presence_mode_onlineoffline").prop("checked", false);}
    
    $("#frmCabangMethod").modal('show');
  }
  function detail(encID){
    $("#loader").fadeIn(1);
	$("#show-detail-employee").html('<?= $this->gtrans->line("Show Employee Details at The Branch") ?>');
	var formData = {
		cabangId: encID
	};
	$.ajax({
        method : "POST",
        url    : url + "show-employee",
        data   : formData,
        success: function (res){
			var obj = $.parseJSON(res);
				$("#list-detail-employee").html(obj);
				$("#showDetailEmployee").modal('show');
				$("#loader").fadeOut(1);
			}
    });
  }
  function delBranch(idcabang,totalDevice,totalEmployee){
    if(totalDevice == 0 && totalEmployee== 0){
      Swal.fire({
        title: '<?= $this->gtrans->line("Are you sure") ?>?',
        text: "<?= $this->gtrans->line("You won't be able to revert this") ?>!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '<?= $this->gtrans->line("Yes, delete it") ?>!'
      }).then((result) => {
        if (result.value) {
          window.open(url + 'delete-branch/' + idcabang,'_self');
        }
      });
    }else{
      Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: 'Branch is used by several device or employee!'
        // footer: '<a href>Why do I have this issue?</a>'
      });
    }

  }
  $("#datatable").DataTable({
    responsive: true
});
	
	function initialize() {

		var mapOptions, map, marker, searchBox, city,
			infoWindow = '',
			addressEl = document.querySelector( '#address' ),
			latEl = document.querySelector( '.latitude' ),
			longEl = document.querySelector( '.longitude' ),
			element = document.getElementById( 'map-canvas' );
			city = document.querySelector( '.city' );

		mapOptions = {
			zoom: 12,
			center: new google.maps.LatLng( <?php echo $new_arr[0]['geoplugin_latitude']; ?>, <?php echo $new_arr[0]['geoplugin_longitude']; ?> ),
			disableDefaultUI: false, 
			scrollWheel: true, 
			draggable: true,

		};

		map = new google.maps.Map( element, mapOptions ); 
		marker = new google.maps.Marker({
			position: mapOptions.center,
			map: map,
			// icon: 'http://pngimages.net/sites/default/files/google-maps-png-image-70164.png',
			draggable: true
		});

		searchBox = new google.maps.places.SearchBox( addressEl );

		google.maps.event.addListener( searchBox, 'places_changed', function () {
			var places = searchBox.getPlaces(),
				bounds = new google.maps.LatLngBounds(),
				i, place, lat, long, resultArray,
				addresss = places[0].formatted_address;

			for( i = 0; place = places[i]; i++ ) {
				bounds.extend( place.geometry.location );
				marker.setPosition( place.geometry.location );  
			}

			map.fitBounds( bounds );
			map.setZoom( 12 );

			lat = marker.getPosition().lat();
			long = marker.getPosition().lng();
			latEl.value = lat;
			longEl.value = long;

			resultArray =  places[0].address_components;

			for( var i = 0; i < resultArray.length; i++ ) {
				if ( resultArray[ i ].types[0] && 'administrative_area_level_2' === resultArray[ i ].types[0] ) {
					citi = resultArray[ i ].long_name;
					city.value = citi;
				}
			}

			if ( infoWindow ) {
				infoWindow.close();
			}
			infoWindow = new google.maps.InfoWindow({
				content: addresss
			});

			infoWindow.open( map, marker );
		} );

		google.maps.event.addListener( marker, "dragend", function ( event ) {
			var lat, long, address, resultArray, citi;

			console.log( 'i am dragged' );
			lat = marker.getPosition().lat();
			long = marker.getPosition().lng();

			var geocoder = new google.maps.Geocoder();
			geocoder.geocode( { latLng: marker.getPosition() }, function ( result, status ) {
				if ( 'OK' === status ) {  
					address = result[0].formatted_address;
					resultArray =  result[0].address_components;

					for( var i = 0; i < resultArray.length; i++ ) {
						if ( resultArray[ i ].types[0] && 'administrative_area_level_2' === resultArray[ i ].types[0] ) {
							citi = resultArray[ i ].long_name;
							console.log( citi );
							city.value = citi;
						}
					}
					addressEl.value = address;
					latEl.value = lat;
					longEl.value = long;

				} else {
					console.log( 'Geocode was not successful for the following reason: ' + status );
				}

				if ( infoWindow ) {
					infoWindow.close();
				}

				infoWindow = new google.maps.InfoWindow({
					content: address
				});

				infoWindow.open( map, marker );
			} );
		});
	}
	
	$(document).ready(function() {
	  $(window).keydown(function(event){
		if(event.keyCode == 13) {
		  event.preventDefault();
		  return false;
		}
	  });
	});
</script>
