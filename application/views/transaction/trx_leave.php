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
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="f-start-date">Start Date</label>
                        <input type="date" class="form-control" id="f-start-date" name="f-start-date" placeholder="2024-01-01">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <label for="f-end-date">End Date</label>
                        <input type="date" class="form-control" id="f-end-date" placeholder="2024-01-31">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <!-- <div class="form-group"> -->
                        
                        <span class="btn btn-primary" id="btn-arrow-down" style="margin-top: 24px">
                          <i class="fa fa-arrow-down"></i>
                        </span>
                        <button class="btn btn-primary" id="btn-search" style="margin-top: 24px">Search</button>
                      <!-- </div> -->
                    </div>
                  </div>
                  <div class="row" id="another-filter" style="display: none">
                    <div class="col-md-2">
                      <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" id="btn-f-cats" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                          <span id="fc-name">Filter Category</span>
                          <span class="caret"></span>
                        </button>
                          <ul class="dropdown-menu" id="dropdown-menu-cats" aria-labelledby="dropdownMenu1">
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
                          <ul class="dropdown-menu" id="dropdown-menu-bulk-act" aria-labelledby="dropdownMenu1">
                            <li><a href="#" class="dropdown-item" data-value="0">Reset Filter</a></li>
                            <li><a href="#" class="dropdown-item" data-value="1">Delete</a></li>
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

<div class="modal fade" id="showFile" tabindex="-1" aria-labelledby="showFileLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Detail File</h4>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <input type="hidden" id="file-id">
            <img src="" id="show-file-image" alt="show-file" style="width:100%">
            <p id="show-file-document" style="display: none">6683638a6c1bc.pdf</p>
            <button class="btn btn-primary" id="btn-download-file" style="width: 100%; margin-top: 20px;">Download</button>
          </div>
        </div>
        <!-- <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Save</button>
        </div> -->
      </div>
    </div>
  </div>

<form id="filter-form" action="<?= base_url('employee-leave') ?>" method="post" style="display: none;">
</form>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
$(document).ready(function() {
  $("#datatable").DataTable();
  var BASE_URL = "<?php echo base_url(); ?>";
  let state = {
    cats: 0
  } 

  $('#btn-export').on('click', function() {
    let cats = $('input[name="category"]').val()
    // return console.log(cats);
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
              let start_time = val.category_id == 1 || val.category_id == 3 ? val.start_date : `${moment(val.start_date).format('YYYY-MM-DD')} ${val.start_time}`
              let end_time = val.category_id == 1 || val.category_id == 3 ? val.end_date : `${moment(val.end_date).format('YYYY-MM-DD')} ${val.end_time}`
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

  $('#datatable tbody').on('click', '.btn-show-files', function() {
    let id = $(this).data('id')
    let doc_name = $(this).data('name')

    if (!doc_name) {
      Swal.fire({
        type: "warning",
        title: "Oops...",
        text: "Document not found!",
      });

      return false;
    }

    let ext = doc_name.split('.')
    $('#file-id').val(id)
    if (ext[1] == 'jpg' || ext[1] == 'png' || ext == 'jpeg') {
      $('#show-file-image').attr('src', BASE_URL + 'sys_upload/leave/doc/' +doc_name)
      $('#show-file-image').css('display', '')
      $('#show-file-document').css('display', 'none')
    } else {
      $('#show-file-document').val(doc_name)
      $('#show-file-document').css('display', '')
      $('#show-file-image').css('display', 'none')
    }

    $('#showFile').modal('show')
  })

  $('#btn-download-file').on('click', function() {
    let idx = $('#file-id').val()
    
    Swal.fire({
      title: 'Are you sure?',
      text: "You will download this file!",
      type: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Yes, Continue!'
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

  $('#dropdown-menu-cats').on('click', '.dropdown-item', function(event) {
    event.preventDefault();
    var selectedText = $(this).text();
    var selectedValue = $(this).data('value');
    
    $('#btn-f-cats').html(
      '<span>'+selectedText+'</span>' +
      '<span class="caret"></span>'
    )

    state.cats = selectedValue
  });

  $('#btn-search').on('click', function() {
    let start_date = $('#f-start-date').val()
    let end_date = $('#f-end-date').val()

    if (start_date == "" && end_date != "" || end_date == "" && start_date != "") {
      Swal.fire({
        type: "error",
        title: "Oops...",
        text: "Complete the date correctly!",
      });

      return false;
    }

    $('#filter-form').append(
      '<input name="start_date" value="'+start_date+'">' +
      '<input name="end_date" value="'+end_date+'">' +
      '<input name="category" value="'+state.cats+'">'
    )

    $('#filter-form').submit()
  })

  $('#dropdown-menu-bulk-act').on('click', '.dropdown-item', function(event) {
    event.preventDefault();
    var selectedText = $(this).text();
    var selectedValue = $(this).data('value');
    
    if (selectedValue == 0) {
      $('#filter-form').append(
        '<input name="category" value="0">' +
        '<input name="start_date" value="">'
      )
    
      $('#filter-form').submit()
    } else if (selectedValue == 1) {
      let idx = []; 
      $('.checkid').each(function() { 
        if ($(this).is(":checked")) {
          idx.push($(this).val())
        }
      });

      delete_data(idx)
    } 

  });

  $('#head-check').on('click', function() {
    if ($(this).is(':checked')) {
      $('.checkid').prop('checked', true)
    } else {
      $('.checkid').prop('checked', false)
    }
  })

  $(document).on('click', '#btn-arrow-down', function() {
    $('#another-filter').css('display', '')

    $(this).attr('id', 'btn-arrow-up')
    $(this).html('<i class="fa fa-arrow-up"></i>')
  })
  $(document).on('click', '#btn-arrow-up', function() {
    $('#another-filter').css('display', 'none')

    $(this).attr('id', 'btn-arrow-down')
    $(this).html('<i class="fa fa-arrow-down"></i>')
  })

})
</script>