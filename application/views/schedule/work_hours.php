<style>
  .form-rounded {
    border-radius: 6px;
  }
  .modal.right .modal-dialog {
      position: absolute;
      right: 0;
      margin: 0;
      padding-right: 20px;
      /* width: 30%; */
  }
  .modal.right .modal-content {
      height: 100vh;
      border: 0;
  }
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?= $this->gtrans->line('Working Hours') ?>
  </h1>
  <small>Create and manage your employee's working hours</small>
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
              <!-- <?= !empty($notif) ? $notif : "" ?> -->
              <!-- <a href="<?= base_url("schedule-work-hours-submit") ?>" class="btn btn-primary"><i class="fa fa-clock-o" style="padding-right: 5px"></i> <?= $this->gtrans->line('Add Data') ?></a> -->
              <button class="btn btn-primary" id="btn-add" style="float: right;">
                <i class="fa fa-clock-o" style="padding-right: 5px;"></i> <?= $this->gtrans->line('Add Data') ?>
              </button>

            </div>
            <div class="col-md-12" style="margin-top:10px">
              <?= !empty($dataTable) ? $dataTable : "" ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content" style="border-radius: 10px;">
        <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
                  <!-- <div class=""> -->
                      <!-- <div class="" style="padding-bottom: 0;"> -->
                          <span style="font-size: 20px; color: black">Setting Working Hours <i class="fa fa-info-circle" aria-hidden="true" style="color: #039BE5; font-size: 16px;"></i>
                          </span><br>
                          <small>Create working hours for your employees</small>
                      <!-- </div> -->
                      <!-- <div class=""> -->
                        <hr style="margin: 10px 0px 0px 0px; border: 0.5px solid #DCDCDC;">
                          <form action="<?= base_url('schedule-work-hours-submit'); ?>" method="post" style="margin-top: 12px;">
                              <div class="row">
                                  <div class="col-md-12">
                                    <span style="font-size: 16px; font-weight: bold;">Name and location</span>
                                      <div class="row" style="margin-top: 10px;">
                                          <div class="col-md-6">
                                            <input type="hidden" id="id_hidden" name="id_hidden" value="0">
                                              <label for="name" class="form-label" style="color: grey; font-weight: 500;">Name</label>
                                              <input type="text" class="form-control form-rounded" name="name" id="name" >
                                          </div>
                                          <div class="col-md-6">
                                              <label for="location" class="form-label" style="color: grey; font-weight: 500;">Location</label>
                                              <select class="form-control form-rounded" multiple="multiple" name="location[]" id="location" style="width: 100%;">
                                                  <?php foreach($branchData as $branch): ?>
                                                    <option value="<?= $branch->cabang_id ?>"><?= $branch->cabang_name ?></option>
                                                  <?php endforeach; ?>
                                              </select>
                                          </div>
                                      </div>
                                      <hr style="margin: 15px 0px 10px 0px; border: 0.5px solid #DCDCDC;">
                                      <span style="font-size: 16px; font-weight: bold;">Working hours</span>
                                      <div class="row">
                                          <div class="col-md-6" style="margin-top: 10px;">
                                              <label for="start_work" class="form-label" style="color: grey; font-weight: 500;">Start Work Time</label>
                                              <input type="time" class="form-control form-rounded" name="start_work" id="start_work" >
                                          </div>
                                          <div class="col-md-6" style="margin-top: 10px;">
                                              <label for="end_work" class="form-label" style="color: grey; font-weight: 500;">End Work Time</label>
                                              <input type="time" class="form-control form-rounded" name="end_work" id="end_work" >
                                          </div>
                                          <div class="col-md-6" style="margin-top: 10px;">
                                              <label for="start_checkin_time" class="form-label" style="color: grey; font-weight: 500;">Earliest Start Work Time</label>
                                              <input type="time" class="form-control form-rounded" name="start_checkin_time" id="start_checkin_time" >
                                          </div>
                                          <div class="col-md-6" style="margin-top: 10px;">
                                              <label for="end_checkin_time" class="form-label" style="color: grey; font-weight: 500;">Latest Start Work Time</label>
                                              <input type="time" class="form-control form-rounded" name="end_checkin_time" id="end_checkin_time" >
                                          </div>
                                          <div class="col-md-6" style="margin-top: 10px;">
                                              <label for="start_checkout_time" class="form-label" style="color: grey; font-weight: 500;">Earliest End Work Time</label>
                                              <input type="time" class="form-control form-rounded" name="start_checkout_time" id="start_checkout_time" >
                                          </div>
                                          <div class="col-md-6" style="margin-top: 10px;">
                                              <label for="end_checkout_time" class="form-label" style="color: grey; font-weight: 500;">Latest End Work Time</label>
                                              <input type="time" class="form-control form-rounded" name="end_checkout_time" id="end_checkout_time" >
                                          </div>
                                      </div>
                                      <hr style="margin: 15px 0px 10px 0px; border: 0.5px solid #DCDCDC;">
                                      <span style="font-size: 16px; font-weight: bold;">Late tolerance</span>
                                      <div class="row">
                                          <div class="col-md-6" style="margin-top: 10px;">
                                              <label for="late_tolerance" class="form-label" style="color: grey; font-weight: 500;">Late Tolerance (minutes)</label>
                                              <input type="number" class="form-control form-rounded" name="late_tolerance" id="late_tolerance" >
                                          </div>
                                          <div class="col-md-6" style="margin-top: 10px;">
                                              <label for="early_leave_tolerance" class="form-label" style="color: grey; font-weight: 500;">Early Leave Tolerance (minutes)</label>
                                              <input type="number" class="form-control form-rounded" name="early_leave_tolerance" id="early_leave_tolerance" >
                                          </div>
                                      </div>
                                      <hr style="margin: 15px 0px 10px 0px; border: 0.5px solid #DCDCDC;">
                                      <span style="font-size: 16px; font-weight: bold;">Working days count</span>
                                      <div class="row">
                                          <div class="col-md-12" style="margin-top: 10px;">
                                              <label for="workday" class="form-label" style="color: grey; font-weight: 500;">Count</label>
                                              <input type="number" class="form-control form-rounded" step="0.5" min="0" max="100" name="workday" id="workday" >
                                          </div>
                                      </div>
                                      <hr style="margin: 15px 0px 10px 0px; border: 0.5px solid #DCDCDC;">
                                      <span style="font-size: 16px; font-weight: bold;">Break Time</span>
                                      <div class="row">
                                          <div class="col-md-12" style="margin-top: 10px;">
                                              <label for="" class="form-label" style="color: grey; font-weight: 500;">Break Time</label><br>
                                              <div class="form-check">
                                                
                                                  <input class="form-check-input" type="radio" name="break_type" id="exampleRadios1" value="1" checked>
                                                  <label class="form-check-label" for="exampleRadios1" style="color: grey; font-weight: 400; margin-right: 10px;">
                                                      Break based on duration
                                                  </label>

                                                  <input class="form-check-input" type="radio" name="break_type" id="exampleRadios2" value="2">
                                                  <label class="form-check-label" for="exampleRadios2" style="color: grey; font-weight: 400; margin-right: 10px;">
                                                      Break time based hours
                                                  </label>

                                                  <input class="form-check-input" type="radio" name="break_type" id="exampleRadios3" value="0">
                                                  <label class="form-check-label" for="exampleRadios3" style="color: grey; font-weight: 400; margin-right: 10px;">
                                                      Without rest
                                                  </label>
                                              </div>
                                              <div class="row" id="opt_break_by_duration">
                                                  <div class="col-md-6">
                                                      <label for="break_duration" class="form-label" style="color: grey; font-weight: 500;">Break time start</label>
                                                      <!-- <input type="time" class="form-control form-rounded" name="break_duration" id="break_duration" > -->
                                                      <select class="form-control form-rounded" name="break_duration" id="break_duration">
                                                          <option value="30"> 30 minutes</option>
                                                          <option value="60"> 1 hours</option>
                                                      </select>
                                                  </div>
                                              </div>
                                              <div class="row" id="opt_break_by_hour" style="display: none;">
                                                  <div class="col-md-6">
                                                      <label for="break_hour_start" class="form-label" style="color: grey; font-weight: 500;">Break time start</label>
                                                      <input type="time" class="form-control form-rounded" name="break_hour_start" id="break_hour_start" >
                                                  </div>
                                                  <div class="col-md-6">
                                                      <label for="break_hour_end" class="form-label" style="color: grey; font-weight: 500;">Break time end</label>
                                                      <input type="time" class="form-control form-rounded" name="break_hour_end" id="break_hour_end" >
                                                  </div>
                                              </div>
                                          </div>
                                      </div>
                                      <hr style="margin: 15px 0px 10px 0px; border: 0.5px solid #DCDCDC;">
                                      <span style="font-size: 16px; font-weight: bold;">Setting label</span>
                                      <div class="row">
                                          <div class="col-md-6" style="margin-top: 10px;">
                                              <label for="colour" class="form-label" style="color: grey; font-weight: 500;">Work schedule background color</label>
                                              <input type="text" class="form-control form-rounded" name="colour" id="colour" >
                                          </div>
                                      </div>
                                      <button type="submit" class="btn btn-primary" style="float: right; margin-left: 8px; margin-top: 10px;" id="btn-submit-data">Save</button>
                                      <button  class="btn btn-danger" style="float: right; margin-top: 10px;" data-dismiss="modal">Cancel</button>
                                  </div>
                              </div>
                          </form>
                      <!-- </div> -->
                  <!-- </div> -->
              </div>
          </div>
        </div>
      </div>
    </div>
</div>

<script>
  $(document).ready(function() {
    var url = "<?= base_url() ?>";
    var notif = '<?php echo json_encode($notif); ?>';
    $("#datatable").DataTable();

    $('#btn-add').on('click', function() {
      $('#id_hidden').val('0')
      $('#name').val('')
      $('#location').val('').change()
      $('#start_work').val('')
      $('#end_work').val('')
      $('#start_checkin_time').val('')
      $('#end_checkin_time').val('')
      $('#start_checkout_time').val('')
      $('#end_checkout_time').val('')
      $('#late_tolerance').val('')
      $('#early_leave_tolerance').val('')
      $('#colour').val('')
      $('#workday').val('')
      $('#opt_break_by_hour').css('display', 'none')
      $('#opt_break_by_duration').css('display', '')
      $('input[name="break_type"][value="1"]').prop('checked', true)
      $('#exampleModal').modal('show')
    })

    $("#datatable tbody").on('click', '.btn-del', function() {
      let idx = $(this).data('id')
      Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
        if (result.value) {
          window.open(url + 'schedule-work-hours-delete/' + idx,'_self');
        }
      })
    })

    $("#datatable tbody").on('click', '.btn-detail', function() {
      let idx = $(this).data('id')
      let thisX = $(this)
      $.ajax({
        url: url + 'schedule-work-hours-detail',
        method: 'GET',
        data: {
          id: idx
        },
        beforeSend: function() {
          thisX.html('<i class="fa fa-circle-o-notch fa-spin"></i>')
        },
        success: function(res) {
          let response = JSON.parse(res)
          let data = response.data
          console.log(data);

          $('#id_hidden').val(data.id)
          $('#name').val(data.name)
          $('#location').val(JSON.parse(data.location)).change()
          $('#start_work').val(data.start_time)
          $('#end_work').val(data.end_time)
          $('#start_checkin_time').val(data.start_checkin_time)
          $('#end_checkin_time').val(data.end_checkin_time)
          $('#start_checkout_time').val(data.start_checkout_time)
          $('#end_checkout_time').val(data.end_checkout_time)
          $('#late_tolerance').val(data.late_minutes)
          $('#early_leave_tolerance').val(data.early_minutes)
          $('#colour').val(data.color)
          $('#workday').val(data.workday)

          $('input[name="break_type"][value="'+data.break_type+'"]').prop('checked', true)

          if (data.break_type == '1') {
            $('#opt_break_by_hour').css('display', 'none')
            $('#opt_break_by_duration').css('display', '')

            $('#break_duration').val(data.break_duration).change()
          } else if (data.break_type == '2') {
              $('#opt_break_by_duration').css('display', 'none')
              $('#opt_break_by_hour').css('display', '')

              $('#break_hour_start').val(data.break_in)
              $('#break_hour_end').val(data.break_out)
          } else {
              $('#opt_break_by_duration').css('display', 'none')
              $('#opt_break_by_hour').css('display', 'none')
          }
        },
        complete: function(){
          $('#exampleModal').modal('show')
          thisX.html('<i class="fa fa-edit fa-lg"></i>')
        }
      })
    })

    $('#location').select2({
        theme: 'bootstrap4'
    })

    $('input[name="break_type"]').on('click', function() {
        if ($('input[name="break_type"]:checked').val() == '1') {
            $('#opt_break_by_hour').css('display', 'none')
            $('#opt_break_by_duration').css('display', '')
        } else if ($('input[name="break_type"]:checked').val() == '2') {
            $('#opt_break_by_duration').css('display', 'none')
            $('#opt_break_by_hour').css('display', '')
        } else {
            $('#opt_break_by_duration').css('display', 'none')
            $('#opt_break_by_hour').css('display', 'none')
        }
    })

    if (notif != 'null') {
      let data_notif = JSON.parse(notif)
      Swal.fire({
        title: data_notif.title,
        text: data_notif.msg,
        type: data_notif.type
      });
    }

  })
</script>
