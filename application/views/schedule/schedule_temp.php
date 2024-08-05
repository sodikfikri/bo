<section class="content-header">
    <h1>
        <?= $this->gtrans->line('Temporary Schedule') ?>
    </h1>
    <small><?= $this->gtrans->line('Create and manage your employees temporary shifts') ?> </small>

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
                    <div id="frm-text"></div>
                    <div class="row">
                        <div class="col-md-12">
                            <span style="font-size: 20px; color: black"><?= $this->gtrans->line('Temporary Schedule') ?> <i class="fa fa-info-circle" aria-hidden="true" style="color: #039BE5; font-size: 16px;"></i>
                            </span><br>
                            <small><?= $this->gtrans->line('Implementation of temporary employee schedules') ?> </small>
                    
                            <hr style="margin: 10px 0px 0px 0px; border: 0.5px solid #DCDCDC;">

                            <form action="#" method="post" style="margin-top: 12px;">
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
                                <span style="font-size: 16px; font-weight: bold;"><?= $this->gtrans->line('Select schedule') ?></span>
                                <div class="row schedule-hour">
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
    const BASE_URL = "<?= base_url() ?>";
    let notif = '<?php echo json_encode($notif); ?>';

    $("#datatable").DataTable();

    $('#show_modal_add').on('click', function() {
        $('#exampleModal').modal('show')
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
})
</script>