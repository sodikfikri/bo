<section class="content-header">
    <h1>
        <?= $this->gtrans->line('Shift Schedule') ?>
    </h1>
    <small><?= $this->gtrans->line('Create and manage your employees work shift schedules') ?> </small>

    <style>
        .form-rounded {
            border-radius: 6px;
        }
    </style>
</section>

<section class="content">
    <div class="box box-inact">
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <?= !empty($notif) ? $notif : "" ?>
                    <button type="button" class="btn btn-primary" id="show_modal_add" style="float: right;">
                        Add Data
                    </button>
                </div>
                    <div class="col-md-12" style="margin-top:10px">
                    <?= !empty($dataTable) ? $dataTable : "" ?>
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
            <span style="font-size: 20px; color: black"><?= $this->gtrans->line('Manage Shift Schedule') ?> <i class="fa fa-info-circle" aria-hidden="true" style="color: #039BE5; font-size: 16px;"></i>
            </span><br>
            <small><?= $this->gtrans->line('Create and manage your employees work shifts') ?></small>
            <hr style="margin: 10px 0px 0px 0px; border: 0.5px solid #DCDCDC;">
                <form action="#" method="post" style="margin-top: 12px;">
                    <div class="row">
                        <div class="col-md-12">
                            <span style="font-size: 16px; font-weight: bold;"><?= $this->gtrans->line('Work Schedule Name') ?></span>
                            <div class="row">
                                <div class="col-md-12" style="margin-top: 10px;">
                                    <label for="name" class="form-label" style="color: grey; font-weight: 500;"><?= $this->gtrans->line('Name') ?></label>
                                    <input type="text" class="form-control form-rounded" name="name" id="name" >
                                </div>
                            </div>

                            <hr style="margin: 15px 0px 10px 0px; border: 0.5px solid #DCDCDC;">
                            <span style="font-size: 16px; font-weight: bold;"><?= $this->gtrans->line('Work Schedule Rotation Number') ?> </span>
                            <div class="row">
                                <div class="col-md-6" style="margin-top: 10px;">
                                    <label for="rotation_number" class="form-label" style="color: grey; font-weight: 500;"><?= $this->gtrans->line('Rotations Number') ?></label>
                                    <input type="number" class="form-control form-rounded" name="rotation_number" id="rotation_number" >
                                </div>
                                <div class="col-md-6" style="margin-top: 10px;">
                                    <label for="rotation_unit" class="form-label" style="color: grey; font-weight: 500;"><?= $this->gtrans->line('Rotations Unit') ?></label>
                                    <!-- <input type="text" class="form-control form-rounded" name="rotation_unit" id="rotation_unit" > -->
                                     <select class="form-control form-rounded" name="rotation_unit" id="rotation_unit" >
                                        <option value="0">Hari</option>
                                        <option value="1" selected>Minggu</option>
                                        <option value="2">Bulan</option>
                                     </select>
                                </div>
                            </div>

                            <hr style="margin: 15px 0px 10px 0px; border: 0.5px solid #DCDCDC;">
                            <span style="font-size: 16px; font-weight: bold;"><?= $this->gtrans->line('Working Hourse') ?></span>
                            <div class="row">
                                <div class="col-md-12" style="margin-top: 10px;">
                                    <label for="work_hours" class="form-label" style="color: grey; font-weight: 500;"><?= $this->gtrans->line('Working Hourse') ?></label>
                                    <select class="form-control form-rounded" name="work_hours" id="work_hours">
                                        <option value="1">Jam pagi</option>
                                        <option value="2">Jam Malam</option>
                                    </select>
                                </div>
                                <div class="col-md-10" style="margin-top: 10px;">
                                    <input type="text" class="form-control form-rounded" value="Jam Pagi" disabled>
                                </div>
                                <div class="col-md-2" style="margin-top: 10px;">
                                    <button class="btn btn-primary set-day">Set Day</button>
                                </div>
                            </div>

                            <hr style="margin: 15px 0px 10px 0px; border: 0.5px solid #DCDCDC;">
                            <span style="font-size: 16px; font-weight: bold;"><?= $this->gtrans->line('National Holiday') ?></span>
                            <div class="row">
                                <div class="col-md-12" style="margin-top: 10px;">
                                    <label for="" class="form-label" style="color: grey; font-weight: 500;"><?= $this->gtrans->line('National Holiday (Optional)') ?></label><br>
                                    <small><?= $this->gtrans->line('Check if you want your work schedule to be unaffected by national holidays') ?></small>
                                    <div class="form-check" style="margin-top: 10px;">
                                        <input class="form-check-input" type="checkbox" name="break_type" id="national_holiday" value="0">
                                        <label class="form-check-label" for="national_holiday" style="color: grey; font-weight: 400; margin-right: 10px;">
                                            <?= $this->gtrans->line('Ignore national holiday') ?>
                                        </label>
                                    </div>
                                    <input type="text" class="form-control form-rounded" value="<?= $this->gtrans->line('employees will still come in on national holidays') ?>" disabled>
                                </div>
                            </div>

                            <hr style="margin: 15px 0px 10px 0px; border: 0.5px solid #DCDCDC;">
                            <span style="font-size: 16px; font-weight: bold;"><?= $this->gtrans->line('Effective Date') ?> </span>
                            <div class="row">
                                <div class="col-md-6" style="margin-top: 10px;">
                                    <label for="effective_start_date" class="form-label" style="color: grey; font-weight: 500;"><?= $this->gtrans->line('Start date') ?></label>
                                    <input type="date" class="form-control form-rounded" name="effective_start_date" id="effective_start_date" >
                                </div>
                                <div class="col-md-6" style="margin-top: 10px;">
                                    <label for="effective_end_date" class="form-label" style="color: grey; font-weight: 500;"><?= $this->gtrans->line('End date (optional)') ?></label>
                                    <input type="date" class="form-control form-rounded" name="effective_end_date" id="effective_end_date" >
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" style="float: right; margin-left: 8px; margin-top: 10px;" id="btn-submit-data">Save</button>
                            <button class="btn btn-danger" style="float: right; margin-top: 10px;">Close</button>
                        </div>
                    </div>
                </form>
              </div>
          </div>
        </div>
      </div>
    </div>
</div>
<div class="modal fade" id="modalSetDate" tabindex="-1" aria-labelledby="modalSetDateLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
      <div class="modal-content" style="border-radius: 10px;">
        <div class="modal-body">
            <div class="row">
              <div class="col-md-12">
            <span style="font-size: 20px; color: black"><?= $this->gtrans->line('Setting day of work') ?> <i class="fa fa-info-circle" aria-hidden="true" style="color: #039BE5; font-size: 16px;"></i>
            </span><br>
            <small><?= $this->gtrans->line('apply work days to your schedule') ?></small>
            <hr style="margin: 10px 0px 0px 0px; border: 0.5px solid #DCDCDC;">
                <form action="#" method="post" style="margin-top: 12px;">
                    <div class="row">
                        <div class="col-md-12">
                            <span style="font-size: 16px; font-weight: bold;"><?= $this->gtrans->line('Select workdays for this schedule') ?></span>
                            <div class="form-check" style="margin-top: 10px;">
                                <div class="row" id="day_of_week" >
                                    <div class="col-md-6">
                                        <input class="form-check-input" type="checkbox" name="set_day_of_week" id="monday" value="1">
                                        <label class="form-check-label" for="monday" style="color: grey; font-weight: 400; margin-right: 10px;">
                                            <?= $this->gtrans->line('monday') ?>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-check-input" type="checkbox" name="set_day_of_week" id="tuesday" value="2">
                                        <label class="form-check-label" for="tuesday" style="color: grey; font-weight: 400; margin-right: 10px;">
                                            <?= $this->gtrans->line('tuesday') ?>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-check-input" type="checkbox" name="set_day_of_week" id="wednesday" value="3">
                                        <label class="form-check-label" for="wednesday" style="color: grey; font-weight: 400; margin-right: 10px;">
                                            <?= $this->gtrans->line('wednesday') ?>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-check-input" type="checkbox" name="set_day_of_week" id="thursday" value="4">
                                        <label class="form-check-label" for="thursday" style="color: grey; font-weight: 400; margin-right: 10px;">
                                            <?= $this->gtrans->line('thursday') ?>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-check-input" type="checkbox" name="set_day_of_week" id="friday" value="5">
                                        <label class="form-check-label" for="friday" style="color: grey; font-weight: 400; margin-right: 10px;">
                                            <?= $this->gtrans->line('friday') ?>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-check-input" type="checkbox" name="set_day_of_week" id="saturday" value="6">
                                        <label class="form-check-label" for="saturday" style="color: grey; font-weight: 400; margin-right: 10px;">
                                            <?= $this->gtrans->line('saturday') ?>
                                        </label>
                                    </div>
                                    <div class="col-md-6">
                                        <input class="form-check-input" type="checkbox" name="set_day_of_week" id="sunday" value="7">
                                        <label class="form-check-label" for="sunday" style="color: grey; font-weight: 400; margin-right: 10px;">
                                            <?= $this->gtrans->line('sunday') ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="row" id="day_of_month" style="display: none;">
                                    <?php for($n = 1; $n <= 31; $n++): ?>
                                        <div class="col-md-4">
                                            <input class="form-check-input" type="checkbox" name="set_day_of_month" id="<?= $n ?>" value="<?= $n ?>">
                                            <label class="form-check-label" for="<?= $n ?>" style="color: grey; font-weight: 400; margin-right: 10px;">
                                                <?= $n .' hari' ?>
                                            </label>
                                        </div>
                                    <?php endfor ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <button class="btn btn-primary btn-modal-set-day" style="margin-top: 10px; width: 100%;" data-dismiss="modal">Save</button>
                                </div>
                                <div class="col-md-6">
                                    <button class="btn btn-danger btn-modal-set-day" style="margin-top: 10px; width: 100%;" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
              </div>
          </div>
        </div>
      </div>
    </div>
</div>

<script>
$(document).ready(function() {
    var url = "<?= base_url() ?>";

    $("#datatable").DataTable();

    $('#show_modal_add').on('click', function() {
        $('#exampleModal').modal('show')
    })

    $('.set-day').on('click', function(e) {
        e.preventDefault()
        $('#modalSetDate').modal('show')
    })

    $('.btn-modal-set-day').on('click', function(e) {
        e.preventDefault()
        $('#modalSetDate').modal('hide')
    })

    $('.modal').on('hidden.bs.modal', function () {
        if ($('.modal.in').length) {
            $('body').addClass('modal-open');
        } else {
            $('body').removeClass('modal-open');
        }
    });

    // $('.modal').on('hidden.bs.modal', function (e) {
    //     e.preventDefault()
    //     if ($('.modal.show').length > 0) {
    //         $('body').addClass('modal-open');
    //     }
    // });

    $('#rotation_unit').on('change', function() {
        if ($(this).val() == '1') {
            $('#day_of_week').css('display', '')
            $('#day_of_month').css('display', 'none')
        } else if ($(this).val() == '2') {
            $('#day_of_week').css('display', 'none')
            $('#day_of_month').css('display', '')
        }
    })

    $('#btn-submit-data').on('click', function() {
 
        let national_holiday = $('#national_holiday').is(':checked') ? 1 : 0
        let data = {
            name: $('#name').val(),
            rotation_number: $('#rotation_number').val(),
            rotation_unit: $('#rotation_unit').val(),
            national_holiday: national_holiday,
            effective_start_date: $('#effective_start_date').val(),
            effective_end_date: $('#effective_end_date').val(),
        }

        if (!$('input[name="set_day_of_week"]').is(':checked') && !$('input[name="set_day_of_month"]').is(':checked')) {
            Swal.fire({
                title: 'Warning',
                text: 'Please set day of this schedule!',
                type: 'warning'
            });

            return false;
        }

        let DOW = $('input[name="set_day_of_week"]').map(function() {
            if ($(this).is(':checked')) return [$(this).val()]
        }).get()

        let detail = {
            schclass: $('#work_hours').val(),
            day: DOW
        }

        let params = {
            shift: data,
            detail: detail
        }

       $.ajax({
        url: url + 'schedule-shift-submit',
        method: 'POST',
        data: {
            data: JSON.stringify(params)
        },
        success: function(res) {
            console.log('response: ', JSON.parse(res));
        }
       })
    })

})
</script>