  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <?= $this->gtrans->line('Master User') ?>
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
              <a href="<?= base_url("add-user") ?>" class="btn btn-primary"><i class="fa  fa-user-plus"></i> <?= $this->gtrans->line('New User') ?></a>

            </div>
            <div class="col-md-12" style="margin-top:10px">
              <?= !empty($userTable) ? $userTable : "" ?>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script>
$("#datatable").DataTable();
</script>
