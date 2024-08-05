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
                        <i class="fa fa-calendar" style="padding-right: 5px;"></i> Add Data
                    </button>
                </div>
                    <div class="col-md-12" style="margin-top:10px">
                    <?= !empty($dataTable) ? $dataTable : "" ?>
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
                                        <input type="hidden" name="id-hide" id="id-hide" value="0">
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
                                            <option value="">Select data ...</option>
                                            <?php foreach($listHour as $hr): ?>
                                                <option value="<?= $hr->id ?>" data-stime="<?= $hr->start_time ?>" data-etime="<?= $hr->end_time ?>"><?= $hr->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="parent-hour">
                                        
                                    </div>
                                </div>
    
                                <hr style="margin: 15px 0px 10px 0px; border: 0.5px solid #DCDCDC;">
                                <span style="font-size: 16px; font-weight: bold;"><?= $this->gtrans->line('National Holiday') ?></span>
                                <div class="row">
                                    <div class="col-md-12" style="margin-top: 10px;">
                                        <label for="" class="form-label" style="color: grey; font-weight: 500;"><?= $this->gtrans->line('National Holiday (Optional)') ?></label><br>
                                        <small><?= $this->gtrans->line('Check if you want your work schedule to be unaffected by national holidays') ?></small>
                                        <div class="form-check" style="margin-top: 10px;">
                                            <input class="form-check-input" type="checkbox" name="national_holiday" id="national_holiday" value="0">
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
                                <button class="btn btn-primary modal-act" style="float: right; margin-left: 8px; margin-top: 10px;" id="btn-submit-data">Save</button>
                                <button class="btn btn-danger" style="float: right; margin-top: 10px;" data-dismiss="modal">Close</button>
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
                                        <div class="col-md-12">
                                            <input class="form-check-input" type="checkbox" name="day_of_week_all" id="day_of_week_all">
                                            <label class="form-check-label" for="day_of_week_all" style="color: balck; font-weight: bold; margin-right: 10px;">
                                                Select All Data
                                            </label>
                                        </div>
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
                                        <div class="col-md-12">
                                            <input class="form-check-input" type="checkbox" name="day_of_month_all" id="day_of_month_all">
                                            <label class="form-check-label" for="day_of_month_all" style="color: balck; font-weight: bold; margin-right: 10px;">
                                                Select All Data
                                            </label>
                                        </div>
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
                                    <div class="col-md-12">
                                        <button class="btn btn-primary btn-modal-set-day" style="margin-top: 10px; width: 100%;" data-dismiss="modal">Save</button>
                                    </div>
                                    <!-- <div class="col-md-6">
                                        <button class="btn btn-danger btn-modal-set-day" style="margin-top: 10px; width: 100%;" data-dismiss="modal">Close</button>
                                    </div> -->
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
</section>



<script>
$(document).ready(function() {
    var url = "<?= base_url() ?>";
    let arr_hr = []

    let state = {
        detail_rotation_unit: null
    }

    $("#datatable").DataTable();

    $('#show_modal_add').on('click', function() {
        $('.modal-act').attr('id', 'btn-submit-data')
        $('#name').val('')
        $('#rotation_number').val('')
        $('#rotation_unit').val('')
        $('#national_holiday').prop('checked', false)
        $('#effective_start_date').val('')
        $('#effective_end_date').val('')
        $('.parent-hour').empty()
        $('#exampleModal').modal('show')
    })

    $(document).on('click', '.set-day', function(e) {
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

    $('#rotation_unit').on('change', function() {
        if ($(this).val() == '1') {
            $('#day_of_week').css('display', '')
            $('#day_of_month').css('display', 'none')
        } else if ($(this).val() == '2') {
            $('#day_of_week').css('display', 'none')
            $('#day_of_month').css('display', '')
        }
    })

    $(document).on('click', '#day_of_month_all', function() {
        if ($(this).is(':checked')) {
            $('input[name="set_day_of_month"]').prop('checked', true)
        } else {
            $('input[name="set_day_of_month"]').prop('checked', false)
        }
    })
    $(document).on('click', '#day_of_week_all', function() {
        if ($(this).is(':checked')) {
            $('input[name="set_day_of_week"]').prop('checked', true)
        } else {
            $('input[name="set_day_of_week"]').prop('checked', false)
        }
    })

    function isTimeInRange(time, range) {
        const [startHour, startMinute, startSecond] = range.start_time.split(':').map(Number);
        const [endHour, endMinute, endSecond] = range.end_time.split(':').map(Number);
        const [checkHour, checkMinute, checkSecond] = time.split(':').map(Number);

        const startTime = new Date();
        startTime.setHours(startHour, startMinute, startSecond, 0);

        const endTime = new Date();
        endTime.setHours(endHour, endMinute, endSecond, 0);

        const checkTime = new Date();
        checkTime.setHours(checkHour, checkMinute, checkSecond, 0);

        return checkTime >= startTime && checkTime <= endTime;
    }

    $('#work_hours').on('change', function() {
        let thisval = $(this).val()
        let stime = $(this).find(':selected').attr('data-stime')
        let etime = $(this).find(':selected').attr('data-etime')

        if (arr_hr.length == 0) {
            arr_hr.push({
                start_time: stime,
                end_time: etime
            })
            $('.parent-hour').append(
                '<div class="child-hour">' +
                    '<div class="col-md-9" style="margin-top: 10px;">' +
                        '<input type="text" class="form-control form-rounded" value="'+$("#work_hours option:selected").text()+'" disabled>' +
                        '<input type="hidden" class="form-control form-rounded schid" value="'+thisval+'" disabled>' +
                    '</div>' +
                    '<div class="col-md-3" style="margin-top: 10px;">' +
                        '<button class="btn btn-primary set-day" style="margin-right: 5px">Set Day</button>' +
                        '<button class="btn btn-danger delete-child-hour">' +
                            '<i class="fa fa-trash fa-lg"></i>' +
                        '</button>' +
                    '</div>' +
                '</div>'
            )
        } else {
            $.each(arr_hr, function(key, val) {
                let ck = isTimeInRange(stime, val)
                if (ck) {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Hours must not overlap',
                        type: 'warning'
                    });
                    return false;
                } else {
                    const newData = {
                        start_time: stime,
                        end_time: etime
                    }
                    const exists = arr_hr.some(item => 
                        item.start_time === newData.start_time && item.end_time === newData.end_time
                    );
                    if (exists) {
                        Swal.fire({
                            title: 'Warning',
                            text: 'Hours must not overlap',
                            type: 'warning'
                        });
                        return false;
                    } else {
                        arr_hr.push(newData)
                        $('.parent-hour').append(
                            '<div class="child-hour">' +
                                '<div class="col-md-9" style="margin-top: 10px;">' +
                                    '<input type="text" class="form-control form-rounded" value="'+$("#work_hours option:selected").text()+'" disabled>' +
                                    '<input type="hidden" class="form-control form-rounded schid" value="'+thisval+'" disabled>' +
                                '</div>' +
                                '<div class="col-md-3" style="margin-top: 10px;">' +
                                    '<button class="btn btn-primary set-day" style="margin-right: 5px">Set Day</button>' +
                                    '<button class="btn btn-danger delete-child-hour">' +
                                        '<i class="fa fa-trash fa-lg"></i>' +
                                    '</button>' +
                                '</div>' +
                            '</div>'
                        )
                    }
                }
            })
        }
        console.log(arr_hr);
        
    })

    $( document).on('click', '.delete-child-hour', function(e) {
        e.preventDefault()
        $(this).closest('.child-hour').remove()
    })

    function showAlertWarning() {
        Swal.fire({
            title: 'Warning',
            text: 'Please complete the data!',
            type: 'warning'
        });
    }

    $(document).on('click', '#btn-submit-data', function(e) {
        e.preventDefault()

        let schid = $('.schid').map(function() {
                    return $(this).val();
                }).get();

        if($.trim($('input[name="name"]').val()) == '') {
            showAlertWarning()
            return;
        }
        if($.trim($('input[name="rotation_number"]').val()) == '' || $.trim($('input[name="rotation_number"]').val()) == 0) {
            showAlertWarning()
            return;
        }
        if($('#rotation_unit').val() == '' || $('#rotation_unit').val() == 0) {
            showAlertWarning()
            return;
        }
        if(!schid || schid.length == 0) {
            showAlertWarning()
            return;
        }
        if($.trim($('input[name="effective_start_date"]').val()) == '') {
            showAlertWarning()
            return;
        }

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

        let DOW;
        if ($('#rotation_unit').val() == '1') {
            DOW = $('input[name="set_day_of_week"]').map(function() {
                if ($(this).is(':checked')) return [$(this).val()]
            }).get()
        } else if($('#rotation_unit').val() == '2') {
            DOW = $('input[name="set_day_of_month"]').map(function() {
                if ($(this).is(':checked')) return [$(this).val()]
            }).get()
        }

        let detail = {
            schclass: schid,
            day: DOW
        }

        let params = {
            shift: data,
            detail: detail
        }

        let thisX = $(this)
        $.ajax({
            url: url + 'schedule-shift-submit',
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
                    thisX.html('<i class="fa fa-edit fa-lg"></i>')
                    setTimeout(() => {
                        location.reload()
                    }, 1000);
                }
            }
        })
    })

    $('#datatable tbody').on('click', '.btn-del', function() {
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
                window.open(url + 'schedule-shift-delete/' + idx,'_self');
            }
        })
    })

    $('#datatable tbody').on('click', '.priview-calendar', function() {
        let idx = $(this).data('id')
        window.open(url + 'schedule-shift-priview/' + idx,'_self');
    })

    $('#datatable tbody').on('click', '.btn-detail', function() {
        let idx = $(this).data('id')
        let thisX = $(this)
        $.ajax({
            url: url + 'schedule-shift-detail',
            method: 'GET',
            data: {
                id: idx
            },
            beforeSend: function() {
                thisX.html('<i class="fa fa-circle-o-notch fa-spin"></i>')
            },
            success: function(res) {
                let response = JSON.parse(res)
                console.log(response);
                if (response.meta.code != '200') {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Data not found',
                        type: 'warning'
                    });
                    return false;
                }
                
                $('#id-hide').val(response.data.id)
                $('#name').val(response.data.name)
                $('#rotation_number').val(response.data.cyle)
                $('#rotation_unit').val(response.data.unit).change()
                state.detail_rotation_unit = response.data.unit

                if (response.data.national_holiday == '1') {
                    $('#national_holiday').prop('checked', true)
                } else {
                    $('#national_holiday').prop('checked', false)
                }

                $('#effective_start_date').val(response.data.start_date)
                
                if (response.data.end_date) {
                    $('#effective_end_date').val(response.data.end_date)
                } else {
                    $('#effective_end_date').val('')
                }

                $('input[name="set_day_of_week"]').prop('checked', false)
                $('.parent-hour').empty()
                $.each(response.deil, function(key, val) {
                    $('.parent-hour').append(
                        '<div class="child-hour">' +
                            '<div class="col-md-9" style="margin-top: 10px;">' +
                                '<input type="text" class="form-control form-rounded" value="'+val.name+'" disabled>' +
                                '<input type="hidden" class="form-control form-rounded schid" value="'+val.id+'" disabled>' +
                            '</div>' +
                            '<div class="col-md-3" style="margin-top: 10px;">' +
                                '<button class="btn btn-primary set-day" style="margin-right: 5px">Set Day</button>' +
                                '<button class="btn btn-danger delete-child-hour">' +
                                    '<i class="fa fa-trash fa-lg"></i>' +
                                '</button>' +
                            '</div>' +
                        '</div>'
                    )
                })
                
                if (response.data.unit == 1) {
                    $.each(response.day_deil, function(key, val) {
                        $('input[name="set_day_of_week"][value="'+val.sdays+'"]').prop('checked', true)
                    })
                } else if (response.data.unit == 2) {
                    $.each(response.day_deil, function(key, val) {
                        $('input[name="set_day_of_month"][value="'+val.sdays+'"]').prop('checked', true)
                    })
                }

            },
            complete: function() {
                $('#exampleModal').modal('show')
                $('.modal-act').attr('id', 'btn-update-data')
                thisX.html('<i class="fa fa-edit fa-lg"></i>')
            }
        })
    })

    $(document).on('click', '#btn-update-data', function(e) {
        e.preventDefault()

        let schid = $('.schid').map(function() {
                    return $(this).val();
                }).get();

        if($.trim($('input[name="name"]').val()) == '') {
            showAlertWarning()
            return;
        }
        if($.trim($('input[name="rotation_number"]').val()) == '' || $.trim($('input[name="rotation_number"]').val()) == 0) {
            showAlertWarning()
            return;
        }
        if($('#rotation_unit').val() == '' || $('#rotation_unit').val() == 0) {
            showAlertWarning()
            return;
        }
        if(!schid || schid.length == 0) {
            showAlertWarning()
            return;
        }
        if($.trim($('input[name="effective_start_date"]').val()) == '') {
            showAlertWarning()
            return;
        }

        let national_holiday = $('#national_holiday').is(':checked') ? 1 : 0
        let data = {
            id: $('#id-hide').val(),
            name: $('#name').val(),
            rotation_number: $('#rotation_number').val(),
            rotation_unit: $('#rotation_unit').val(),
            national_holiday: national_holiday,
            effective_start_date: $('#effective_start_date').val(),
            effective_end_date: $('#effective_end_date').val(),
        }

        let DOW;

        if ($('#rotation_unit').val() != state.detail_rotation_unit) {
            if ($('#rotation_unit').val() == '1') {
                let ckw = $('input[name="set_day_of_week"]').map(function() {
                    if ($(this).is(':checked')) return [$(this).val()]
                }).get()

                if (ckw.length == 0) {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Please setting day of schedule',
                        type: 'warning'
                    });
                    return false;
                }
            }
            if ($('#rotation_unit').val() == '2') {
                let ckm = $('input[name="set_day_of_month"]').map(function() {
                    if ($(this).is(':checked')) return [$(this).val()]
                }).get()

                if (ckm.length == 0) {
                    Swal.fire({
                        title: 'Warning',
                        text: 'Please setting day of schedule',
                        type: 'warning'
                    });
                    return false;
                }
            }
        }

        if ($('#rotation_unit').val() == '1') {
            DOW = $('input[name="set_day_of_week"]').map(function() {
                if ($(this).is(':checked')) return [$(this).val()]
            }).get()
        } else if($('#rotation_unit').val() == '2') {
            DOW = $('input[name="set_day_of_month"]').map(function() {
                if ($(this).is(':checked')) return [$(this).val()]
            }).get()
        }

        let detail = {
            schclass: schid,
            day: DOW
        }

        let params = {
            shift: data,
            detail: detail
        }
        
        let thisX = $(this)

        $.ajax({
            url: url + 'schedule-shift-update',
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
                    thisX.html('<i class="fa fa-edit fa-lg"></i>')
                    setTimeout(() => {
                        location.reload()
                    }, 1000);
                }
            }
        })
    })
})
</script>