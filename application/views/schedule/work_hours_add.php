<section class="content-header">
    <h1>
        <?= $this->gtrans->line('Add Working Hours') ?>
    </h1>
    <small>Set the working hours of your employees</small>
</section>

<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="box box-inact">
                <div class="box-header" style="padding-bottom: 0;">
                    <span style="font-size: 20px; color: black">Setting Working Hours</span><br>
                    <small>Create working hours for your employees</small>
                </div>
                <div class="box-body">
                    <form action="" method="post">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" name="name" id="name" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="location" class="form-label">Location</label>
                                        <select class="form-control" name="location" id="location">
                                            <option value="">Pos A</option>
                                            <option value="">Pos B</option>
                                            <option value="">Pos C</option>
                                            <option value="">Pos D</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6" style="margin-top: 15px;">
                                        <label for="start_work" class="form-label">Start Work Time</label>
                                        <input type="time" class="form-control" name="start_work" id="start_work" required>
                                    </div>
                                    <div class="col-md-6" style="margin-top: 15px;">
                                        <label for="end_work" class="form-label">End Work Time</label>
                                        <input type="time" class="form-control" name="end_work" id="end_work" required>
                                    </div>
                                    <div class="col-md-6" style="margin-top: 15px;">
                                        <label for="es_work" class="form-label">Earliest Start Work Time</label>
                                        <input type="time" class="form-control" name="es_work" id="es_work" required>
                                    </div>
                                    <div class="col-md-6" style="margin-top: 15px;">
                                        <label for="en_work" class="form-label">Latest Start Work Time</label>
                                        <input type="time" class="form-control" name="en_work" id="en_work" required>
                                    </div>
                                    <div class="col-md-6" style="margin-top: 15px;">
                                        <label for="en_work" class="form-label">Earliest End Work Time</label>
                                        <input type="time" class="form-control" name="en_work" id="en_work" required>
                                    </div>
                                    <div class="col-md-6" style="margin-top: 15px;">
                                        <label for="ln_work" class="form-label">Latest End Work Time</label>
                                        <input type="time" class="form-control" name="ln_work" id="ln_work" required>
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
                                                <!-- <input type="time" class="form-control" name="break_duration" id="break_duration" required> -->
                                                <select class="form-control" name="break_duration" id="break_duration">
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
                                                <input type="time" class="form-control" name="break_hour_start" id="break_hour_start" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="break_hour_end" class="form-label">Break time end</label>
                                                <input type="time" class="form-control" name="break_hour_end" id="break_hour_end" required>
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
                                        <input type="number" class="form-control" name="late_tolerance" id="late_tolerance" required>
                                    </div>
                                    <div class="col-md-6" style="margin-top: 15px;">
                                        <label for="early_leave_tolerance" class="form-label">Early Leave Tolerance (minutes)</label>
                                        <input type="number" class="form-control" name="early_leave_tolerance" id="early_leave_tolerance" required>
                                    </div>
                                    <div class="col-md-6" style="margin-top: 15px;">
                                        <label for="unit_day" class="form-label">Unit Day</label>
                                        <input type="number" class="form-control" name="unit_day" id="unit_day" required>
                                    </div>
                                </div>
                                <button class="btn btn-primary" style="float: right; margin-left: 8px;">Save</button>
                                <a href="<?= base_url('schedule-work-hours'); ?>" class="btn btn-danger" style="float: right">Cancel</a>
                            </div>
                            <div class="col-md-4">
                                <span style="font-weight: bold; padding-left: 20px;">Langkah Mudah Membuat Jam Kerja</span>
                                <ul>
                                    <li>Isi nama jam kerja</li>
                                    <li>Pilih lokasi kerja yang di inginkan</li>
                                    <li>Isi jam masuk kerja: Waktu pegawai untuk mulai bekerja</li>
                                    <li>Isi jam pulang kerja: Waktu pegawai selesai bekerja</li>
                                    <li>Jam awal masuk: Waktu pegawai untuk dapat absen sebelum waktu dan jam masuk kerja</li>
                                    <li>Jam akhir masuk: Waktu terakhir pegawai dapat absen masuk sebelum jam masuk kerja</li>
                                    <li>Jam awal keluar: Waktu pegawai untuk dapat sebelum waktu dan jam pulang kerja</li>
                                    <li>Jam akhir keluar: Waktu terakhir pegawai dapat melakukan absen pulang kerja</li>
                                    <li>Jam istirahat berdasarkan range jam: Waktu interval jam istirahat yang paten berdasarkan waktu yang di tentukan</li>
                                    <li>Jam istirahat berdasarkan durasi: Waktu interval berdasarkan durasi waktu yang bebas</li>
                                    <li>Tanpa istirahat: -</li>
                                    <li>Toleransi terlambat masuk kerja: Waktu yang di berikan perusahaan untuk menoleransi pegawai yang telambat</li>
                                    <li>Toleransi pulang awal: Waktu yang di berikan perusahaan untuk menoleransi pegawai yang pulang lebih dahulu</li>
                                    <li>Hitungan hari kerja: Hitungan jam kerja</li>
                                    <!-- <li>Label: untuk tampilan pada preview periode</li> -->
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>

$(document).ready(function() {

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
