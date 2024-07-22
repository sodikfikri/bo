<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?= $this->gtrans->line('Departement') ?>
  </h1>
  <small>Create and manage your company departments or divisions</small>
  <style>
        .apextree-node {
            cursor: pointer;
        }

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
      <!-- <div class="box box-inact"> -->
        <!-- /.box-header -->
        <!-- <div class="box-body">
          <div class="row"> -->
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab"><?= $this->gtrans->line('LIST DEPARTEMENT') ?></a></li>
                    <li><a href="#tab_2" data-toggle="tab"><?= $this->gtrans->line('TREE') ?></a></li>
                </ul>  
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                        <!-- <div class="col-md-12"> -->
                            <?= !empty($notif) ? $notif : "" ?>
                            <button type="button" class="btn btn-primary" id="show_modal_add" style="margin-bottom: 10px;">
                                Add Departement
                            </button>
                        <!-- </div> -->
                        <!-- <div class="col-md-12" style="margin-top:10px"> -->
                            <?= !empty($listTable) ? $listTable : "" ?>
                        <!-- </div> -->
                    </div>
                    <div class="tab-pane" id="tab_2">
                        <div id="hierarchy-departement"></div>
                    </div>
                </div>
            </div>

          </div>
        <!-- </div>
      </div> -->
    <!-- </div> -->
</section>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-body">
                <span style="font-size: 20px; color: black">Departements <i class="fa fa-info-circle" aria-hidden="true" style="color: #039BE5; font-size: 16px;"></i>
                </span><br>
                <small>Define departments or divisions in your company</small>
                <hr style="margin: 10px 0px 0px 0px; border: 0.5px solid #DCDCDC;">
                <form action="<?= base_url('departement-submit'); ?>"  method="post" style="margin-top: 12px;">
                    <div class="row">
                        <div class="col-md-12">
                            <span style="font-size: 16px; font-weight: bold;">Departement information</span>
                            <div class="row" style="margin-top: 10px;">
                                <div class="col-md-12">
                                    <input type="hidden" id="id" name="id" value="0">
                                    <label for="name" class="form-label" style="color: grey; font-weight: 500;">Departement Name</label>
                                    <input type="text" class="form-control form-rounded" name="name" id="name" >
                                </div>
                                <div class="col-md-12" style="margin-top: 8px;">
                                    <label for="name" class="form-label" style="color: grey; font-weight: 500;">Telepon</label>
                                    <input type="text" class="form-control form-rounded" name="telepon" id="telepon" >
                                </div>
                            </div>

                            <hr style="margin: 15px 0px 10px 0px; border: 0.5px solid #DCDCDC;">
                            <span style="font-size: 16px; font-weight: bold;">PIC Name</span>
                            <div class="row" style="margin-top: 10px;">
                                <div class="col-md-12">
                                    <label for="name" class="form-label" style="color: grey; font-weight: 500;">Manager name</label>
                                    <input type="text" class="form-control form-rounded" name="manager_name" id="manager_name" >
                                </div>
                            </div>

                            <hr style="margin: 15px 0px 10px 0px; border: 0.5px solid #DCDCDC;">
                            <span style="font-size: 16px; font-weight: bold;">Parent</span>
                            <div class="row" style="margin-top: 10px;">
                                <div class="col-md-12">
                                    <label for="name" class="form-label" style="color: grey; font-weight: 500;">Parent</label>
                                    <select name="parent" id="parent" class="form-control" style="width: 100%;">
                                        <option value="">Select Parent</option>
                                        <?php foreach($parentData as $item): ?>
                                            <option value="<?= $item->id ?>"><?= $item->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary" style="float: right; margin-left: 8px; margin-top: 10px;" id="btn-submit-data">Save</button>
                            <button type="button" class="btn btn-danger" style="float: right; margin-top: 10px;" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/apextree"></script>
<script>
    $(document).ready(function() {

        $("#datatable").DataTable();

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
                                    </div>
                                </div>
                                <div style='margin-top: auto; border-bottom: 10px solid ${content.borderColor}'></div>
                            </div>`;
                },
                enableToolbar: true,
            };
            const tree = new ApexTree(document.getElementById('hierarchy-departement'), options);
            tree.render(datax);
        }

        $('#show_modal_add').on('click', function() {
            $('.modal-title').html('Add Departement')
            $('#exampleModal').modal('show')
            $('#id').val('0')
            $('#name').val('')
            $('#telepon').val('')
            $('#manager_name').val('')
            $('#parent').val('').change()
        })
    
        var BASE_URL = "<?php echo base_url(); ?>";
    
        const getTree = () => {
            $.ajax({
                url: BASE_URL + 'departement-tree',
                method: 'GET',
                success: function(res) {
                    let response = JSON.parse(res)
                    
                    if (response.meta.code == '200') {
                        Hierarchy(response.data) 
                    }
                },
                error: function(e) {
                    console.log('error: ', error);
                }
            })
        }
        getTree()
    
        $('#parent').select2({
            theme: 'bootstrap4'
        })
    
        const detailData = (idx, thisX) => {
            $.ajax({
                url: BASE_URL + 'departement-detail',
                method: 'GET',
                data: {
                    id: idx
                },
                beforeSend: function() {
                    thisX.html('<i class="fa fa-circle-o-notch fa-spin"></i>')
                },
                success: function(res) {
                    let response = JSON.parse(res)
                    $('#id').val(response.data[0].id)
                    $('#name').val(response.data[0].name)
                    $('#telepon').val(response.data[0].telepon)
                    $('#manager_name').val(response.data[0].pic)
                    
                    if(response.data[0].parent != 0) {
                        $('#parent').val(response.data[0].parent).change()
                    } else {
                        $('#parent').val('').change()
                    }
    
                    $('#exampleModal').modal('show')
                },
                complete: function() {
                    thisX.html('<i class="fa fa-edit fa-lg"></i>')
                    $('.modal-title').html('Detail Departement')
                    $('.btn-delete').css('display', '')
                    $('.dismis-close').css('display', 'none')
                }
            })
        }
    
        $('#datatable tbody').on('click', '.btn-detail', function() {
            let thisX = $(this)
            detailData($(this).data('id'), thisX)
        })
    
        $('#datatable tbody').on('click', '.btn-del', function(e) {
            let id = $(this).data('id')
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
                    window.open(url + 'departement-delete/' + id,'_self');
                }
            })
        })
    })
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('hierarchy-departement').addEventListener('click', (event) => {
            const node = event.target.closest('.apextree-node');
            if (node) {
                const parentG = node.closest('g');
                if (parentG) {
                    const dataSelf = parentG.getAttribute('data-self'); // get id
                    console.log(dataSelf);
                    // detailData(dataSelf)
                }
            }
        });
    });
</script>