
<section class="content-header">
  <h1>
    <?= $this->gtrans->line('Holidays') ?>
  </h1>
  <small><?= $this->gtrans->line('Create and manage your companys holiday schedule') ?> </small>

  <style>
      .form-rounded {
          border-radius: 6px;
      }
  </style>
</section>
<!-- Main content -->
<section class="content">
  <!-- Info boxes -->
  <?= !empty($addonsAlert) ? $addonsAlert : '' ?>
  <div class="row">
    <div class="col-md-12">
      <!-- <div class="box box-inact">
        <div class="box-body"> -->
          <!-- <div class="row">
            <div class="col-md-12"> -->
              <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab"><?= $this->gtrans->line('CALENDAR') ?></a></li>
                    <li><a href="#tab_2" data-toggle="tab"><?= $this->gtrans->line('HOLIDAY LIST') ?></a></li>
                    <button class="btn btn-primary" id="btn-add-data" style="float: right; margin: 5px;"> Add data</button>
                </ul>
                <div class="tab-content">
                  <div class="tab-pane active" id="tab_1">
                    <div id="calendar"></div>
                  </div>
                  <div class="tab-pane" id="tab_2">
                    <?= !empty($dataTable) ? $dataTable : "" ?>
                  </div>
                </div>
              </div>
            <!-- </div>
          </div> -->
        <!-- </div>
      </div> -->
    </div>
  </div>

  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content" style="border-radius: 10px;">
        <!-- <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" style="font-weight: bold">Add Data Holidays</h4>
        </div> -->
        <div class="modal-body">
          <span style="font-size: 20px; color: black">Add Holiday <i class="fa fa-info-circle" aria-hidden="true" style="color: #039BE5; font-size: 16px;"></i>
          </span><br>
          <small>Add your company holidays</small>

          <hr style="margin: 10px 0px 0px 0px; border: 0.5px solid #DCDCDC;">
          <form action="<?= base_url('schedule-holidays/submit') ?>" method="post">
            <input type="hidden" name="id" id="id" value="0">
            <span style="font-size: 16px; font-weight: bold;"><?= $this->gtrans->line('Holiday Date') ?></span>
            <div class="row" style="margin-top: 10px;">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="start_date" class="form-label" style="color: grey; font-weight: 500;"><?= $this->gtrans->line('Start Date') ?></label>
                  <input type="date" class="form-control form-rounded" name="start_date" id="start_date" required>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="end_date" class="form-label" style="color: grey; font-weight: 500;"><?= $this->gtrans->line('End Date') ?></label>
                  <input type="date" class="form-control form-rounded" name="end_date" id="end_date" required>
                </div>
              </div>
            </div>

            <hr style="margin: 15px 0px 10px 0px; border: 0.5px solid #DCDCDC;">
            <span style="font-size: 16px; font-weight: bold;"><?= $this->gtrans->line('Holiday Name') ?></span>
            <div class="row" style="margin-top: 10px;">
              <div class="col-md-12">
                <div class="mb-3">
                  <label for="holiday_name" class="form-label" style="color: grey; font-weight: 500;">Name</label>
                  <input type="text" class="form-control form-rounded" name="holiday_name" id="holiday_name" required>
                </div>
              </div>
            </div>
            <hr style="margin: 15px 0px 10px 0px; border: 0.5px solid #DCDCDC;">
            <!-- <span style="font-size: 16px; font-weight: bold;"><?= $this->gtrans->line('Holiday Name') ?></span> -->
            <div class="row" style="margin-top: 10px;">
              <div class="col-md-12">
                <div class="mb-3">
                  <label for="notes" class="form-label" style="color: grey; font-weight: 500;"><?= $this->gtrans->line('Notes') ?></label>
                  <!-- <input type="text" class="form-control form-rounded" name="notes" id="notes" required> -->
                   <textarea class="form-control form-rounded" rows="4" name="notes" id="notes"></textarea>
                </div>
              </div>
            </div>
            <div class="mb-3" style="margin-top: 10px;">
              <label for="colour" class="form-label" style="color: grey; font-weight: 500;">Colour</label>
              <select class="form-control form-rounded" name="colour" id="colour">
                <option value="#DC143C">Red</option>
                <option value="#FF6347">Orange</option>
                <option value="#00a65a">Green</option>
                <option value="#039be5">Blue</option>
              </select>
            </div>
            <div style="margin-top: 17px;">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary">Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <form action="<?= base_url('schedule-holidays/delete') ?>" method="post" style="display: none;" id="form-delete">

  </form>
</section>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/@fullcalendar/multimonth@6.1.14/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.30.1/moment.min.js"></script>
<script>
$(document).ready(function() {
    $("#datatable").DataTable();

    let notif = '<?php echo json_encode($notif); ?>';
    let data_holiday = <?php echo $holidays; ?>

    const delete_data = (idx) => {
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
            // let arr_id = idx.split(',')
            
            $.each(idx, function(key, val) {
              $('#form-delete').append(
                `<input type="text" name="id_delete[]" value="${val}">`
              )
            })

            $('#form-delete').submit()
          }
      })
    }

    let RenderFullCalendar = (DataEvent) => {
        var calendarEl = document.getElementById('calendar');
        let calendar = new FullCalendar.Calendar(calendarEl, {
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'multiMonthYear,dayGridMonth,timeGridWeek,timeGridDay,list'  // dayGridMonth,timeGridWeek,listWeek
            },
            initialView: 'multiMonthYear',
            // initialDate: '2017-06-01',
            eventDidMount: function(info) {
                $(info.el).tooltip({ 
                    title: info.event._def.title,
                    placement: "top",
                    trigger: "hover",
                    container: "body"
                });
            },
            events: DataEvent,
            eventClick: function(info) {
              // console.log(info.event.id);
                delete_data([info.event.id]);
            },
            navLinks: true, // can click day/week names to navigate views
            businessHours: true, // display business hours
            editable: true,
            selectable: false,
            eventTimeFormat: { // like '14:30:00'
                hour: '2-digit',
                minute: '2-digit',
                // second: '2-digit',
                meridiem: false,
                hour12: false
            }
        });
        calendar.render();
    }

    // const rescder_fullcalendar = () => {

    //     const groupedData = data_holiday.map(event => {
    //         let endDate = new Date(event.start_time);
    //         endDate.setDate(endDate.getDate());
    //         endDate = endDate.toISOString().split('T')[0];
    
    //         return {
    //             title: event.name,
    //             start: event.start_time,
    //             end: endDate,
    //             id: [event.id],
    //             color: event.color
    //         };
    //     });
    //     // Merge events with the same title
    //     const mergedData = [];
    //     groupedData.forEach(event => {
    //         const existingEvent = mergedData.find(e => e.title === event.title);
    //         if (existingEvent) {
    //             existingEvent.end = event.end;
    //             existingEvent.id = existingEvent.id.concat(event.id);
    //         } else {
    //             mergedData.push(event);
    //         }
    //     });
    
    //     $.each(mergedData, function(key, val) {
    //         let edate = moment(val.end)
    //             edate = edate.add(1, 'days')
    //         let sdate = moment(val.start)
    //         // console.log(sdate);
    //         val.start = sdate.format('YYYY-MM-DD')
    //         val.end = edate.format('YYYY-MM-DD')
    //     })
    //     console.log(mergedData);
    //     RenderFullCalendar(mergedData)
    // }

    setTimeout(() => {
        // rescder_fullcalendar()
        RenderFullCalendar(data_holiday)
        // console.log(data_holiday);
    }, 300);

    $('#btn-add-data').on('click', function() {
        $('#exampleModal').modal('show')
    })

    $('#datatable tbody').on('click', '.btn-del', function() {
      let id = $(this).data('id')
      delete_data([id])
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
