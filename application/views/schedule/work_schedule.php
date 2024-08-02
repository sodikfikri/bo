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
          <div id="frm-text"></div>
          <div class="row">
            <div class="col-md-12">
              <span style="font-size: 20px; color: black"><?= $this->gtrans->line('Employee Work Schedule') ?> <i class="fa fa-info-circle" aria-hidden="true" style="color: #039BE5; font-size: 16px;"></i>
              </span><br>
              <small><?= $this->gtrans->line('Implementation of employee work schedule') ?> </small>
    
              <hr style="margin: 10px 0px 0px 0px; border: 0.5px solid #DCDCDC;">
              
              <form action="#" method="post" style="margin-top: 12px;">
                <div class="row">
                  <div class="col-md-2 col-sm-6">
                    <button class="btn btn-primary btn-cats" role="1" data-type="1"><?= $this->gtrans->line('Scheduled') ?> </button>
                  </div>
                  <div class="col-md-2 col-sm-6">
                    <button class="btn btn-default btn-cats" role="2" data-type="2"><?= $this->gtrans->line('Automatic') ?> </button>
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
                            <select class="form-control form-rounded" name="employee_scheduled" id="employee_scheduled" style="width: 100%;">
                               
                            </select>
                          </div>
                        </div>
                    </div>
                    <div class="row" id="opt-automatic" style="display: none; margin-top: 10px;">
                        <div class="col-md-12">
                          <div class="boxed-row">
                            <label style="color: grey; font-weight: 500;"><?= $this->gtrans->line('Select departement') ?></label> <br>
                            <label for="departement_automatic" class="form-label" style="color: grey; font-weight: 500;"><?= $this->gtrans->line('Departement') ?></label>
                            <select class="form-control form-rounded" name="departement_automatic" id="departement_automatic" style="width: 100%;">
                                <option value="" selected>Select departement ...</option>
                                <?php foreach($departement as $item): ?>
                                  <option value="<?= $item->id ?>"><?= $item->name ?></option>
                                <?php endforeach; ?>
                            </select>

                            <div class="emp-list" style="margin-top: 12px; display: none;">
                              <label style="color: grey; font-weight: 500;"><?= $this->gtrans->line('Select the employees who will be assigned this work schedule') ?></label> <br>
                              <label for="break_duration" class="form-label" style="color: grey; font-weight: 500;"><?= $this->gtrans->line('Employee List') ?></label>

                              <div class="">
                                  <input class="form-check-input" type="checkbox" name="all_employee" id="all_employee">
                                  <label class="form-check-label" for="all_employee" style="color: balck; font-weight: bold; margin-right: 10px;">
                                      Select All Data
                                  </label>
                              </div>
                              <div class="row">
                                
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                  </div>
                </div>

                <hr style="margin: 15px 0px 10px 0px; border: 0.5px solid #DCDCDC;">
                <span style="font-size: 16px; font-weight: bold;"><?= $this->gtrans->line('Select department or employee') ?></span>
                <div class="row schedule-shift">
                  <div class="col-md-12">
                    <label for="" class="form-label" style="color: grey; font-weight: 500;"><?= $this->gtrans->line('select the work shift to be assigned to this schedule') ?></label><br>
                    <div class="form-check">
                      <?php foreach($shift as $key => $shf): 
                          $unt = '';
                          if ($shf->unit == '1') {
                            $unt = 'Week pattern';
                          } elseif ($shf->unit == '1') {
                            $unt = 'Month pattern';
                          } else {
                            $unt = 'Day pattern';
                          }
                        ?>
                        <input class="form-check-input" type="radio" name="shift_schedule" data-cyle="<?= $shf->cyle ?>" data-unit="<?= $shf->unit ?>" id="exampleradios<?= $key ?>" value="<?= $shf->id ?>">
                        <label class="form-check-label" for="exampleradios<?= $key ?>" style="color: grey; font-weight: 400; margin-right: 10px;">
                          <?= "$shf->name (Effective date $shf->start_date / $shf->cyle " . $unt . ")"?>
                        </label>
                        <br>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
                <div class="row schedule-hour" style="display: none;">
                  <div class="col-md-12">
                    <label for="" class="form-label" style="color: grey; font-weight: 500;"><?= $this->gtrans->line('select the work shift to be assigned to this schedule') ?></label><br>
                    <div class="form-check">
                      <?php foreach($hour as $keyhr => $hr): ?>
                        <input class="form-check-input" type="checkbox" name="work_schedule" id="exampleRHour<?= $keyhr ?>" value="<?= $hr->id ?>">
                        <label class="form-check-label" for="exampleRHour<?= $keyhr ?>" style="color: grey; font-weight: 400; margin-right: 10px;">
                          <?= "$hr->name ($hr->start_time - $hr->end_time)" ?>
                        </label>
                        <br>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
                <button class="btn btn-primary" style="float: right; margin-left: 8px; margin-top: 10px;" id="btn-submit-data">Save</button>
                <button class="btn btn-danger" style="float: right; margin-top: 10px;" data-dismiss="modal">Cancel</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalEmp" tabindex="-1" aria-labelledby="modalEmpLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content" style="border-radius: 10px;">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <span style="font-size: 20px; color: black"><?= $this->gtrans->line('Employee List') ?> <i class="fa fa-info-circle" aria-hidden="true" style="color: #039BE5; font-size: 16px;"></i></span><br>
              <small><?= $this->gtrans->line('Employees listed on this schedule') ?> </small>
    
              <hr style="margin: 10px 0px 0px 0px; border: 0.5px solid #DCDCDC;">

              <div class="table-responsive" style="margin-top: 20px;">
                <table class="table" id="table-employee">
                  <thead>
                    <tr>
                      <th>#</th>
                      <th><?= $this->gtrans->line('Employee') ?></th>
                      <th><?= $this->gtrans->line('Departement') ?></th>
                      <th class="text-center"><?= $this->gtrans->line('Action') ?></th>
                    </tr>
                  </thead>
                  <tbody>

                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</section>

<script>
$(document).ready(function() {
  const BASE_URL = "<?= base_url() ?>";
  let notif = '<?php echo json_encode($notif); ?>';

  let State = {
    employee_departement: null
  }
  
  $("#datatable").DataTable();
  

  $('#btn-add').on('click', function() {
    $('#exampleModal').modal('show')
  })

  $('.btn-cats').on('click', function(e) {
    e.preventDefault()
    $('.btn-cats').removeClass('btn-primary').addClass('btn-default');
    $(this).addClass('btn-primary')
    if ($(this).attr('role') == '1') {
      $('.schedule-shift').css('display', '')
      $('.schedule-hour').css('display', 'none')
    } else {
      $('.schedule-shift').css('display', 'none')
      $('.schedule-hour').css('display', '')
    }
  })

  $('#employee_scheduled').select2({
    dropdownParent: $("#frm-text"),
    ajax: {
        url: BASE_URL + 'schedule-work/employee-all',
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                q: params.term, // search term
                page: params.page || 1
            };
        },
        processResults: function (data, params) {
            params.page = params.page || 1;

            return {
                results: data.items,
                pagination: {
                    more: (params.page * 10) < data.total_count
                }
            };
        },
        cache: true
    },
  })

  $('#employee_scheduled').on('change', function() {
    let idx = $(this).val()

    $.ajax({
      url: BASE_URL + 'schedule-work/employee-detail',
      method: 'POST',
      data: {
        id: idx
      },
      success: function(res) {
        const response = JSON.parse(res)

        State.employee_departement = response.data[0].departement_id
      }
    })
  })

  $('#departement_automatic').select2({
    dropdownParent: $("#frm-text")
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

  $('#departement_automatic').on('change', function() {
    let dept_id = $(this).val()

    $.ajax({
      url: BASE_URL + 'schedule-work/employee-departement',
      method: 'POST',
      data: {
        departement_id: dept_id
      },
      beforeSend: function() {
        $('.emp-list').find('.row').empty()
        $('.emp-list').find('.row').append(
          '<div class="text-center">' +
            '<i class="fa fa-circle-o-notch fa-spin"></i>' +
          '</div>'
        )
      },
      success: function(res) {
        const response = JSON.parse(res)
        if (response.meta.code != '200') {
          Swal.fire({
            title: 'Warning',
            text: 'Employee not found',
            type: 'warning'
          });
          return false;
        }
        
        $.each(response.data, function(key, val) {
          $('.emp-list').find('.row').append(
            '<div class="col-md-6 col-sm-12">' +
                '<input class="form-check-input" type="checkbox" name="employee_autimatic" id="employee_autimatic'+key+'" value="'+val.employee_id+'">' +
                '<label class="form-check-label" for="employee_autimatic'+key+'" style="color: grey; font-weight: 400; margin-right: 10px;">' +
                    val.employee_full_name +
                '</label>' +
            '</div>'
          )
        })
      },
      complete: function() {
        $('.emp-list').find('.row').find('.text-center').remove()
      }
    })
  })

  $('#all_employee').on('click', function() {
    if ($(this).is(':checked')) {
      $('input[name="employee_autimatic"]').prop('checked', true)
    } else {
      $('input[name="employee_autimatic"]').prop('checked', false)
    }
  })

  $('#datatable tbody').on('click', '.priview-calendar', function() {
      let idx = $(this).data('id')
      window.open(url + 'schedule-shift-priview/' + idx,'_self');
  })

  $('#btn-submit-data').on('click', function(e) {
    e.preventDefault()

    let type = null;
    let employee = [];

    let emp_auto = $('input[name="employee_autimatic"]').map(function() {
      if ($(this).is(':checked')) return [$(this).val()]
    }).get()

    if (emp_auto.length != 0 && $('input[name="assign_type"]:checked').val() == '2') {
      employee = emp_auto
    }

    if ($('#employee_scheduled').val() && $('input[name="assign_type"]:checked').val() == '1') {
      employee.push($('#employee_scheduled').val())
    }

    if (employee.length == 0) {
      Swal.fire({
        title: 'Warning',
        text: 'Please select employee first',
        type: 'warning'
      });
      return false;
    }

    if (employee.length != 0 && !$('input[name="shift_schedule"]:checked').val() && !$('input[name="work_schedule"]:checked').val()) {
      Swal.fire({
        title: 'Warning',
        text: 'Fill in the data correctly',
        type: 'warning'
      });
      return false;
    }

    const dpt_idx = $('input[name="assign_type"]:checked').val() == '1' ? State.employee_departement : $('#departement_automatic').val()

    let work_batch = $('input[name="work_schedule"]').map(function() {
      if ($(this).is(':checked')) return [$(this).val()]
    }).get()
    
    let params = {
      employee: employee,
      shift: $('input[name="shift_schedule"]:checked').val() ? $('input[name="shift_schedule"]:checked').val() : '',
      cyle: $('input[name="shift_schedule"]:checked').attr('data-cyle'),
      unit: $('input[name="shift_schedule"]:checked').attr('data-unit'),
      work: $('input[name="work_schedule"]:checked').val() ? work_batch : [],
      departement_id: dpt_idx
    }
    
    let thisX = $(this)

    $.ajax({
      url: BASE_URL + 'schedule-work-assign',
      method: 'POST',
      data: {
        data: JSON.stringify(params)
      },
      beforeSend: function() {
        thisX.html('<i class="fa fa-circle-o-notch fa-spin"></i>')
      },
      success: function(res) {
        let response = JSON.parse(res)
        if (response.meta.code == '200') {
          Swal.fire({
              title: 'Success',
              text: 'Success update data',
              type: 'success'
          });
          thisX.html('Save')
          setTimeout(() => {
              location.reload()
          }, 1000);
        }
      }
    })
  })

  $('#datatable tbody').on('click', '.btn-detail', function() {
    let params = $(this).data('batch')
    let thisX = $(this)

    $.ajax({
      url: BASE_URL + 'schedule-work/employee-in-schedule',
      method: 'GET',
      data: {
        batch: params
      },
      beforeSend: function() {
        thisX.html('<i class="fa fa-circle-o-notch fa-spin"></i>')
      },
      success: function(res) {
        let response = JSON.parse(res)
        $("#table-employee tbody").empty()
        $.each(response.data, function(key, val) {
          let number = parseInt(key) + parseInt(1)
          $("#table-employee tbody").append(
            '<tr>' +
              '<td>'+number+'</td>' +
              '<td>'+val.employee_full_name+'</td>' +
              '<td>'+val.departement_name+'</td>' +
              '<td class="text-center">' +
                '<span style="cursor:pointer" data-empid="'+val.user_id+'" data-dpt="'+val.departement_id+'" data-batch="'+val.batch+'" class="text-danger btn-del-emp-sch"><i class="fa fa-trash"></i></span>' +
              '</td>' +
            '</tr>'
          )
        })
      },
      complete: function() {
        thisX.html('<i class="fa fa-list"></i>')
        $("#table-employee").DataTable();
        $('#modalEmp').modal('show')
      }
    })
  })

  $('#table-employee tbody').on('click', '.btn-del-emp-sch', function() {
    let datauser = $(this).data('empid')
    let datadpt = $(this).data('dpt')
    let databatch = $(this).data('batch')
    let thisX = $(this)
    
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
        $.ajax({
          url: BASE_URL + 'schedule-work/delete-employee-in-schedule',
          method: 'POST',
          data: {
            user_id: datauser,
            departement_id: datadpt,
            batch: databatch
          },
          beforeSend: function() {
            thisX.html('<i class="fa fa-circle-o-notch fa-spin"></i>')
          },
          success: function(res) {
            let response = JSON.parse(res)
            if (response.meta.code == '200') {
              Swal.fire({
                  title: 'Success',
                  text: 'Success delete data',
                  type: 'success'
              });
              thisX.html('<i class="fa fa-trash"></i>')
              setTimeout(() => {
                  location.reload()
              }, 1000);
            }
          }
        })
      }
    })
  })
})
</script>