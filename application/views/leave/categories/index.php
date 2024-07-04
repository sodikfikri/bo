<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?= $this->gtrans->line('Leave Categories') ?>
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
              <?= !empty($catsTable) ? $catsTable : "" ?>
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
          <form action="<?= base_url('master-leave-categories/submit') ?>" method="post" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
              <input type="hidden" name="id" id="id" value="0">
              <input type="text" class="form-control" name="name" id="name" required>
            </div>
            <div class="mb-3">
              <label for="icon" class="form-label">Icon</label>
              <input type="file" class="form-control" name="icon" id="icon">
              <input type="hidden" class="form-control" name="icon_name" id="icon_name">
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
    $('#id').val('0')
    $('#name').val('')
    $('#icon').val('')
    $('#icon_name').val('')
    $('#exampleModal').modal('show')
  })

  function delCats(catsID){
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
        window.open(url + 'master-leave-categories/delete/' + catsID,'_self');
      }
    })
  }

  $('#datatable tbody').on('click', '.btn-del', function() {
    let idx = $(this).data('id') 
    delCats(idx)
  })

  $('#datatable tbody').on('click', '.btn-detail', function() {
    let id = $(this).data('id')
    let name = $(this).data('name')
    let imgname = $(this).data('imgname')

    $('#id').val(id)
    $('#name').val(name)
    $('#icon_name').val(imgname)
    $('#exampleModal').modal('show')

  })
});
</script>
