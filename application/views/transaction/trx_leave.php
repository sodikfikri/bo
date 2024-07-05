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
              <div class="card" style="width: 60%;">
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="f-start-date">Start Date</label>
                        <input type="email" class="form-control" id="f-start-date" name="f-start-date" placeholder="2024-01-01">
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="form-group">
                        <label for="f-end-date">End Date</label>
                        <input type="email" class="form-control" id="f-end-date" placeholder="2024-01-31">
                      </div>
                    </div>
                    <div class="col-md-2">
                      <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                          Filter Category
                          <span class="caret"></span>
                        </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                            <li><a href="#" class="dropdown-item" data-value="0">Select Category</a></li>
                            <?php foreach($listCategory as $item): ?>
                              <li><a href="#" class="dropdown-item" data-value="<?= $item->id ?>"><?= $item->name ?></a></li>
                            <?php endforeach; ?>
                          </ul>
                      </div>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                          Bulk Action
                          <span class="caret"></span>
                        </button>
                          <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                          <li><a href="#">Delete</a></li>
                        </ul>
                    </div>
                    <div class="col-md-2">
                      <button class="btn btn-primary" id="btn-export" style="float: right">Export To Excel</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-12" style="margin-top:30px">
              <?= !empty($datatable) ? $datatable : "" ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<form id="filter-form" action="<?= base_url('employee-leave') ?>" method="post" style="display: none;">
</form>

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

  const delete_data = (idx) => {
    $.ajax({
      url: BASE_URL + 'employee-leave/delete',
      method: 'POST',
      data: {
        id: idx
      },
      success: function(res) {
        let data = JSON.parse(res)

        if (data.meta.code == 200) {
          location.reload();
        }
      }
    })
  }

  $('#datatable tbody').on('click', '.btn-del', function() {
    let data_id = $(this).data('id')
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
        delete_data([data_id])
      }
    })
  })

  $('.dropdown-menu').on('click', '.dropdown-item', function(event) {
    event.preventDefault();
    var selectedText = $(this).text();
    var selectedValue = $(this).data('value');

    $('#filter-form').append(
      `<input name="category" value="${selectedValue}">`
    )

    $('#filter-form').submit()
  });

})
</script>