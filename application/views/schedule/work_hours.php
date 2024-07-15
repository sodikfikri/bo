  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      <?= $this->gtrans->line('Working Hours') ?>
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
              <!-- <?= !empty($notif) ? $notif : "" ?> -->
              <a href="<?= base_url("schedule-work-hours-submit") ?>" class="btn btn-primary"><i class="fa fa-clock-o" style="padding-right: 5px"></i> <?= $this->gtrans->line('Add Data') ?></a>

            </div>
            <div class="col-md-12" style="margin-top:10px">
              <?= !empty($dataTable) ? $dataTable : "" ?>
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
