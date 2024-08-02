<section class="content-header">
    <h1>
        <?= $this->gtrans->line('Priview Calendar') ?>
    </h1>
    <small><?= $this->gtrans->line('Shift schedule in calendar form') ?> </small>

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
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.14/index.global.min.js'></script>
<script>
$(document).ready(function() {

    let data = <?php echo $dataCalendar; ?>

    let RenderFullCalendar = (DataEvent) => {
        var calendarEl = document.getElementById('calendar');
        let calendar = new FullCalendar.Calendar(calendarEl, {
            headerToolbar: {
                left: 'today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'  // dayGridMonth,timeGridWeek,listWeek
            },
            initialView: 'dayGridMonth',
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
              console.log(info.event.id);
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

    setTimeout(() => {
        console.log(data);
        RenderFullCalendar(data)
    }, 500);
})
</script>