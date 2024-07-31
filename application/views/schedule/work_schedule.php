<section class="content-header">
    <h1>
        <?= $this->gtrans->line('Work Schedule') ?>
    </h1>
    <small><?= $this->gtrans->line('Create and manage your employees work schedules') ?> </small>

    <style>
        .form-rounded {
            border-radius: 6px;
        }
        .boxed-row {
            border: 1px solid #ddd9;
            padding: 15px;
            border-radius: 5px;
            /* box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); */
            background-color: #f9f9f900;
        }
  </style>
</section>

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
              <button class="btn btn-primary" id="btn-add" style="float: right;">
                <i class="fa fa-plus-circle" style="padding-right: 5px;"></i> <?= $this->gtrans->line('Add Data') ?>
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

  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content" style="border-radius: 10px;">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <span style="font-size: 20px; color: black"><?= $this->gtrans->line('Employee Work Schedule') ?> <i class="fa fa-info-circle" aria-hidden="true" style="color: #039BE5; font-size: 16px;"></i>
              </span><br>
              <small><?= $this->gtrans->line('Implementation of employee work schedule') ?> </small>
    
              <hr style="margin: 10px 0px 0px 0px; border: 0.5px solid #DCDCDC;">
              
              <form action="#" method="post" style="margin-top: 12px;">
                <div class="row">
                  <div class="col-md-2 col-sm-6">
                    <button class="btn btn-primary btn-cats" role="1"><?= $this->gtrans->line('Scheduled') ?> </button>
                  </div>
                  <div class="col-md-2 col-sm-6">
                    <button class="btn btn-default btn-cats" role="1"><?= $this->gtrans->line('Automatic') ?> </button>
                  </div>
                </div>

                <hr style="margin: 15px 0px 10px 0px; border: 0.5px solid #DCDCDC;">
                <span style="font-size: 16px; font-weight: bold;"><?= $this->gtrans->line('Select department or employee') ?></span>
                <div class="row">
                  <div class="col-md-12">
                    <label for="" class="form-label" style="color: grey; font-weight: 500;"><?= $this->gtrans->line('Select department or employee to place work schedule') ?></label><br>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="assign_type" id="exampleRadios1" value="1" checked>
                        <label class="form-check-label" for="exampleRadios1" style="color: grey; font-weight: 400; margin-right: 10px;">
                          <?= $this->gtrans->line('Scheduled') ?>
                        </label>

                        <input class="form-check-input" type="radio" name="assign_type" id="exampleRadios2" value="2">
                        <label class="form-check-label" for="exampleRadios2" style="color: grey; font-weight: 400; margin-right: 10px;">
                          <?= $this->gtrans->line('Departement') ?>
                        </label>
                    </div>
                    <div class="row" id="opt-schedule" style="margin-top: 10px;">
                        <div class="col-md-12">
                          <div class="boxed-row">
                            <label style="color: grey; font-weight: 500;"><?= $this->gtrans->line('Select the employees who will be assigned this work schedule') ?></label> <br>
                            <label for="employee_scheduled" class="form-label" style="color: grey; font-weight: 500;"><?= $this->gtrans->line('Employee') ?></label>
                            <select class="form-control form-rounded" name="employee_scheduled" id="employee_scheduled">
                                <option value="30"> Ahmad</option>
                                <option value="60"> Ahmed</option>
                            </select>
                          </div>
                        </div>
                    </div>
                    <div class="row" id="opt-automatic" style="display: none; margin-top: 10px;">
                        <div class="col-md-12">
                          <div class="boxed-row">
                            <label style="color: grey; font-weight: 500;"><?= $this->gtrans->line('Select departement') ?></label> <br>
                            <label for="departement_automatic" class="form-label" style="color: grey; font-weight: 500;"><?= $this->gtrans->line('Departement') ?></label>
                            <select class="form-control form-rounded" name="departement_automatic" id="departement_automatic">
                                <option value="" selected>Select departement ...</option>
                                <option value="30"> RnD</option>
                                <option value="60"> Marketing</option>
                            </select>

                            <div class="emp-list" style="margin-top: 12px; display: none;">
                              <label style="color: grey; font-weight: 500;"><?= $this->gtrans->line('Select the employees who will be assigned this work schedule') ?></label> <br>
                              <label for="break_duration" class="form-label" style="color: grey; font-weight: 500;"><?= $this->gtrans->line('Employee List') ?></label>
                              <div class="row">

                                <div class="col-md-6 col-sm-12">
                                    <input class="form-check-input" type="checkbox" name="all_employee" id="all_employee">
                                    <label class="form-check-label" for="all_employee" style="color: balck; font-weight: bold; margin-right: 10px;">
                                        Select All Data
                                    </label>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <input class="form-check-input" type="checkbox" name="employee_autimatic[]" id="employee_autimatic[]" value="1">
                                    <label class="form-check-label" for="employee_autimatic[]" style="color: grey; font-weight: 400; margin-right: 10px;">
                                        Ahmad
                                    </label>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <input class="form-check-input" type="checkbox" name="employee_autimatic[]" id="employee_autimatic[]" value="1">
                                    <label class="form-check-label" for="employee_autimatic[]" style="color: grey; font-weight: 400; margin-right: 10px;">
                                        Ahmad
                                    </label>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <input class="form-check-input" type="checkbox" name="employee_autimatic[]" id="employee_autimatic[]" value="1">
                                    <label class="form-check-label" for="employee_autimatic[]" style="color: grey; font-weight: 400; margin-right: 10px;">
                                        Ahmad
                                    </label>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <input class="form-check-input" type="checkbox" name="employee_autimatic[]" id="employee_autimatic[]" value="1">
                                    <label class="form-check-label" for="employee_autimatic[]" style="color: grey; font-weight: 400; margin-right: 10px;">
                                        Ahmad
                                    </label>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <input class="form-check-input" type="checkbox" name="employee_autimatic[]" id="employee_autimatic[]" value="1">
                                    <label class="form-check-label" for="employee_autimatic[]" style="color: grey; font-weight: 400; margin-right: 10px;">
                                        Ahmad
                                    </label>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                  </div>
                </div>

                <hr style="margin: 15px 0px 10px 0px; border: 0.5px solid #DCDCDC;">
                <span style="font-size: 16px; font-weight: bold;"><?= $this->gtrans->line('Select department or employee') ?></span>
                <div class="row">
                  <div class="col-md-12">
                    <label for="" class="form-label" style="color: grey; font-weight: 500;"><?= $this->gtrans->line('select the work shift to be assigned to this schedule') ?></label><br>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="shift_schedule" id="exampleradios1" value="1" checked>
                        <label class="form-check-label" for="exampleradios1" style="color: grey; font-weight: 400; margin-right: 10px;">
                          <?= $this->gtrans->line('Shift Pagi (Effective date 4 Juni 2024 / 1 month pattern)') ?>
                        </label>
                        <br>
                        <input class="form-check-input" type="radio" name="shift_schedule" id="exampleradios2" value="2">
                        <label class="form-check-label" for="exampleradios2" style="color: grey; font-weight: 400; margin-right: 10px;">
                          <?= $this->gtrans->line('Shift Malam (Effective date 4 Juni 2024 / 1 month pattern)') ?>
                        </label>
                    </div>
                  </div>
                </div>
                <button type="submit" class="btn btn-primary" style="float: right; margin-left: 8px; margin-top: 10px;" id="btn-submit-data">Save</button>
                <button  class="btn btn-danger" style="float: right; margin-top: 10px;" data-dismiss="modal">Cancel</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</section>

<script>
$(document).ready(function() {
  $("#datatable").DataTable();

  $('#btn-add').on('click', function() {
    $('#exampleModal').modal('show')
  })

  $('.btn-cats').on('click', function(e) {
    e.preventDefault()
    // $('.btn-cats').removeClass('btn-primary').addClass('btn-default');
    if ($(this).attr('role') == '1') {
      $(this).removeClass('btn-default').addClass('btn-primary');
      $(this).attr('role', '2')
    } else {
      $(this).removeClass('btn-primary').addClass('btn-default');
      $(this).attr('role', '1')
    }
  })

  $('input[name="assign_type"]').on('click', function() {
    if ($('input[name="assign_type"]:checked').val() == '1') {
        $('#opt-automatic').css('display', 'none')
        $('#opt-schedule').css('display', '')
    } else {
      $('#opt-automatic').css('display', '')
      $('#opt-schedule').css('display', 'none')
    }
  })

  $('#departement_automatic').on('change', function() {
    if ($(this).val()) {
      $('.emp-list').css('display', '')
    } else {
      $('.emp-list').css('display', 'none')
    }
  })
})
</script>