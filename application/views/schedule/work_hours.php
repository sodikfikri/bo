<style>
  .form-rounded {
    border-radius: 6px;
  }
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?= $this->gtrans->line('Working Hours') ?>
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
              <!-- <?= !empty($notif) ? $notif : "" ?> -->
              <a href="<?= base_url("schedule-work-hours-submit") ?>" class="btn btn-primary"><i class="fa fa-clock-o" style="padding-right: 5px"></i> <?= $this->gtrans->line('Add Data') ?></a>

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
        <!-- <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Update Data</h4>
        </div> -->
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
                        <hr style="margin: 10px 0px 0px 0px">
                          <form action="" method="post" style="margin-top: 12px;">
                              <div class="row">
                                  <div class="col-md-12">
                                      <div class="row">
                                          <div class="col-md-6">
                                              <label for="name" class="form-label">Name</label>
                                              <input type="text" class="form-control form-rounded" name="name" id="name" required>
                                          </div>
                                          <div class="col-md-6">
                                              <label for="location" class="form-label">Location</label>
                                              <select class="form-control form-rounded" name="location" id="location">
                                                  <option value="">Pos A</option>
                                                  <option value="">Pos B</option>
                                                  <option value="">Pos C</option>
                                                  <option value="">Pos D</option>
                                              </select>
                                          </div>
                                          <div class="col-md-6" style="margin-top: 15px;">
                                              <label for="start_work" class="form-label">Start Work Time</label>
                                              <input type="time" class="form-control form-rounded" name="start_work" id="start_work" required>
                                          </div>
                                          <div class="col-md-6" style="margin-top: 15px;">
                                              <label for="end_work" class="form-label">End Work Time</label>
                                              <input type="time" class="form-control form-rounded" name="end_work" id="end_work" required>
                                          </div>
                                          <div class="col-md-6" style="margin-top: 15px;">
                                              <label for="es_work" class="form-label">Earliest Start Work Time</label>
                                              <input type="time" class="form-control form-rounded" name="es_work" id="es_work" required>
                                          </div>
                                          <div class="col-md-6" style="margin-top: 15px;">
                                              <label for="en_work" class="form-label">Latest Start Work Time</label>
                                              <input type="time" class="form-control form-rounded" name="en_work" id="en_work" required>
                                          </div>
                                          <div class="col-md-6" style="margin-top: 15px;">
                                              <label for="en_work" class="form-label">Earliest End Work Time</label>
                                              <input type="time" class="form-control form-rounded" name="en_work" id="en_work" required>
                                          </div>
                                          <div class="col-md-6" style="margin-top: 15px;">
                                              <label for="ln_work" class="form-label">Latest End Work Time</label>
                                              <input type="time" class="form-control form-rounded" name="ln_work" id="ln_work" required>
                                          </div>
                                          <div class="col-md-12" style="margin-top: 15px;">
                                              <label for="" class="form-label">Break Time</label><br>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="1" checked>
                                                  <label class="form-check-label" for="exampleRadios1" style="color: grey; font-weight: 400;">
                                                      Break based on duration
                                                  </label>
                                              </div>
                                              <div class="row" id="opt_break_by_duration">
                                                  <div class="col-md-6">
                                                      <label for="break_duration" class="form-label">Break time start</label>
                                                      <!-- <input type="time" class="form-control form-rounded" name="break_duration" id="break_duration" required> -->
                                                      <select class="form-control form-rounded" name="break_duration" id="break_duration">
                                                          <option value="30"> 30 minutes</option>
                                                          <option value="60"> 1 hours</option>
                                                      </select>
                                                  </div>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="2">
                                                  <label class="form-check-label" for="exampleRadios2" style="color: grey; font-weight: 400;">
                                                      Break time based hours
                                                  </label>
                                              </div>
                                              <div class="row" id="opt_break_by_hour" style="display: none;">
                                                  <div class="col-md-6">
                                                      <label for="break_hour_start" class="form-label">Break time start</label>
                                                      <input type="time" class="form-control form-rounded" name="break_hour_start" id="break_hour_start" required>
                                                  </div>
                                                  <div class="col-md-6">
                                                      <label for="break_hour_end" class="form-label">Break time end</label>
                                                      <input type="time" class="form-control form-rounded" name="break_hour_end" id="break_hour_end" required>
                                                  </div>
                                              </div>
                                              <div class="form-check">
                                                  <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios3" value="0">
                                                  <label class="form-check-label" for="exampleRadios3" style="color: grey; font-weight: 400;">
                                                      Without rest
                                                  </label>
                                              </div>
                                          </div>
                                          <div class="col-md-6" style="margin-top: 15px;">
                                              <label for="late_tolerance" class="form-label">Late Tolerance (minutes)</label>
                                              <input type="number" class="form-control form-rounded" name="late_tolerance" id="late_tolerance" required>
                                          </div>
                                          <div class="col-md-6" style="margin-top: 15px;">
                                              <label for="early_leave_tolerance" class="form-label">Early Leave Tolerance (minutes)</label>
                                              <input type="number" class="form-control form-rounded" name="early_leave_tolerance" id="early_leave_tolerance" required>
                                          </div>
                                          <div class="col-md-6" style="margin-top: 15px;">
                                              <label for="unit_day" class="form-label">Unit Day</label>
                                              <input type="number" class="form-control form-rounded" name="unit_day" id="unit_day" required>
                                          </div>
                                      </div>
                                      <button class="btn btn-primary" style="float: right; margin-left: 8px;">Save</button>
                                      <a href="<?= base_url('schedule-work-hours'); ?>" class="btn btn-danger" style="float: right">Cancel</a>
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

    $("#datatable").DataTable();

    $("#datatable tbody").on('click', '.btn-detail', function() {
      $('#exampleModal').modal('show')
    })

    $('input[name="exampleRadios"]').on('click', function() {
        if ($('input[name="exampleRadios"]:checked').val() == '1') {
            $('#opt_break_by_hour').css('display', 'none')
            $('#opt_break_by_duration').css('display', '')
        } else if ($('input[name="exampleRadios"]:checked').val() == '2') {
            $('#opt_break_by_duration').css('display', 'none')
            $('#opt_break_by_hour').css('display', '')
        } else {
            $('#opt_break_by_duration').css('display', 'none')
            $('#opt_break_by_hour').css('display', 'none')
        }
    })
  })
</script>
