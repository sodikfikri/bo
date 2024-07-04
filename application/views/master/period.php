<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?= $this->gtrans->line('Active Period') ?>
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
              <button type="button" class="btn btn-primary" id="show_modal_add">
                Add Data
              </button>
            </div>
            <div class="col-md-12" style="margin-top:10px">
              <?= !empty($datatable) ? $datatable : "" ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- Button trigger modal -->
  

  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">Add Data</h4>
        </div>
        <div class="modal-body">
          <form action="<?= base_url('active-period/submit') ?>" method="post">
            <input type="hidden" name="id" id="id" value="0">
            <div class="mb-3">
              <label for="start_date" class="form-label">Start Date</label>
              <input type="text" class="form-control" name="start_date" id="start_date" required>
            </div>
            <div class="mb-3">
              <label for="end_date" class="form-label">End Date</label>
              <input type="text" class="form-control" name="end_date" id="end_date" required>
            </div>
            <div class="mb-3">
              <label for="status" class="form-label">Status</label>
              <select name="status" id="status" class="form-control" required>
                  <option value="1">Active</option>
                  <option value="0">Inactive</option>
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
</section>
<script>
    $(document).ready(function() {
        $("#datatable").DataTable();

        $('#show_modal_add').on('click', function() {
            $('.modal-title').html('Add Data')
            $('#id').val('0')
            $('#start_date').val('')
            $('#end_date').val('')
            $('#status').val('0').change()
            $('#exampleModal').modal('show')
        })
        
        $('#datatable tbody').on('click', '.btn-detail', function() {
            $('.modal-title').html('Detail Data')
            $('#id').val($(this).data('id'))
            $('#start_date').val($(this).data('sdate'))
            $('#end_date').val($(this).data('edate'))
            $('#status').val($(this).data('status')).change()

            $('#exampleModal').modal('show')
        })

        function delCats(idx){
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
                    window.open(url + 'active-period/delete/' + idx,'_self');
                }
            })
        }

        $('#datatable tbody').on('click', '.btn-del', function() {
            let idx = $(this).data('id') 
            delCats(idx)
        })
    })
</script>