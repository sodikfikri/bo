<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?= $this->gtrans->line('Departement') ?>
  </h1>
  <style>
        .apextree-node {
            cursor: pointer;
        }
    </style>
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
                Add Departement
              </button>
            </div>
            <div class="col-md-12" style="margin-top:10px">
                <div id="hierarchy-departement"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Departement</h4>
            </div>
            <div class="modal-body">
                <form action="<?= base_url('departement-submit') ?>" method="post">
                    <input type="hidden" class="form-control" name="id" id="id" value="0">
                    <div class="mb-3">
                        <label for="icon" class="form-label">Name</label>
                        <input type="text" class="form-control" name="name" id="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="icon" class="form-label">Label</label>
                        <select class="form-control" name="label" id="label">
                            <option value="">Select Label</option>
                            <option value="Divisi">Divisi</option>
                            <option value="Role">Role</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="name" class="form-label">Parent</label>
                         <select class="form-control" name="parent" id="parent" style="width: 100%">
                            <option value="">Select parent ...</option>
                         </select>
                    </div>
                    <div style="margin-top: 17px;">
                        <button type="submit" class="btn btn-danger btn-delete" style="display:none;">Delete</button>
                        <button type="button" class="btn btn-danger dismis-close" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/apextree"></script>
<script>
    $(document).ready(function() {
        
        let departement = <?php echo json_encode($departement); ?>

        const Hierarchy = (datax) => {

            const options = {
                contentKey: 'data',
                width: 1000,
                height: 600,
                nodeWidth: 200,
                nodeHeight: 70,
                childrenSpacing: 70,
                siblingSpacing: 30,
                direction: 'top',
                nodeTemplate: (content) => {
                    return `<div style="display: flex; flex-direction: column; height: 100%;">
                                <div style='display: flex;flex-direction: row;justify-content: center;align-items: center;height: 100%; box-shadow: 1px 2px 4px #ccc; padding: 0 7px;'>
                                    <div>
                                        <span style="font-weight: bold; font-family: Arial; font-size: 14px;">${content.name}</span><br>
                                        <span style="font-family: Arial; font-size: 11px;display: flex;flex-direction: row;justify-content: center;align-items: center;">${content.label ?? ''}</span>
                                    </div>
                                </div>
                                <div style='margin-top: auto; border-bottom: 10px solid ${content.borderColor}'></div>
                            </div>`;
                },
                enableToolbar: true,
            };
            const tree = new ApexTree(document.getElementById('hierarchy-departement'), options);
            tree.render(datax[0]);
        }

        setTimeout(() => {
            Hierarchy(departement)
        }, 100);

        $('#show_modal_add').on('click', function() {
            $('.modal-title').html('Add Departement')
            $('#exampleModal').modal('show')
            $('.btn-delete').css('display', 'none')
            $('.dismis-close').css('display', '')
            $('#id').val('0')
        })
    })

    var BASE_URL = "<?php echo base_url(); ?>";

    const getParent = () => {
        $.ajax({
            url: BASE_URL + 'departement-parent',
            method: 'GET',
            success: function(res) {
                let response = JSON.parse(res)
                if (response.meta.code == 200) {
                    $.each(response.data, function(key, val) {
                        $('#parent').append(
                            '<option value="'+val.id+'">'+val.data.name+'</option>' 
                        )
                    })
                }
            },
            error: function(e) {
                console.log('error: ', error);
            }
        })
    }

    $('#parent').select2()

    getParent()

    const detailData = (idx) => {
        $.ajax({
            url: BASE_URL + 'departement-detail',
            method: 'GET',
            data: {
                id: idx
            },
            success: function(res) {
                let response = JSON.parse(res)
                $('#id').val(response.data[0].id)
                $('#name').val(response.data[0].name)
                $('#label').val(response.data[0].label).change()
                $('#parent').val(response.data[0].parent).change()
            },
            complete: function() {
                $('.modal-title').html('Detail Departement')
                $('#exampleModal').modal('show')
                $('.btn-delete').css('display', '')
                $('.dismis-close').css('display', 'none')
            }
        })
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('hierarchy-departement').addEventListener('click', (event) => {
            const node = event.target.closest('.apextree-node');
            if (node) {
                const parentG = node.closest('g');
                if (parentG) {
                    const dataSelf = parentG.getAttribute('data-self'); // get id
                    detailData(dataSelf)
                }
            }
        });
    });

    $('.btn-delete').on('click', function(e) {
        e.preventDefault()
        let id = $('#id').val()
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
                // console.log('masuk');
                window.open(url + 'departement-delete/' + $('#id').val(),'_self');
                // $.ajax({
                //     url: BASE_URL + 'departement-delete',
                //     method: 'post',
                //     data: {
                //         id: id
                //     },
                //     success: function(res) {
                //         console.log(res);
                //     }
                // })
            }
        })
    })
</script>