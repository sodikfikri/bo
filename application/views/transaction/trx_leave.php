<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?= $this->gtrans->line('Transaction Leave') ?>
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
              <?= !empty($notif) ? $notif : "" ?>
              <button class="btn btn-primary" id="btn-export">Export To Excel</button>
            </div>
            <div class="col-md-12" style="margin-top:10px">
              <?= !empty($datatable) ? $datatable : "" ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script>
$(document).ready(function() {
  $("#datatable").DataTable();
  var BASE_URL = "<?php echo base_url(); ?>";

  $('#btn-export').on('click', function() {
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
          url: `${BASE_URL}employee-leave/export`,
          method: 'GET',
          success: function(res) {
            let response = JSON.parse(res)

            let data = [];

            $.each(response, function(key, val) {
              let start_time = val.category_id == 1 || val.category_id == 3 ? val.start_date : `${moment(val.created_at).format('YYYY-MM-DD')} ${val.start_time}`
              let end_time = val.category_id == 1 || val.category_id == 3 ? val.end_date : `${moment(val.created_at).format('YYYY-MM-DD')} ${val.end_time}`
              let obj = {
                'Employee Name': val.employee_full_name,
                'Category': val.category_name,
                'Start Time': start_time,
                'End Time': end_time,
                'Reason': val.reason
              }
              data.push(obj)
            })

            let ws = XLSX.utils.json_to_sheet(
                data
            , {
                header: ['Employee Name', 'Category', 'Start Time', 'End Time', 'Reason']
            });
            let wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, 'Rekap');
            XLSX.writeFile(wb, 'Vacation Recap.xlsx');
          }
        })
      }
    })
  })


  $('#datatable tbody').on('click', '.btn-download', function() {
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
        window.open(url + 'employee-leave-file/'+idx,'_self');
      }
    })
  })
})
</script>