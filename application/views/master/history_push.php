<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Histori Push Template
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
            <?= !empty($table)?$table:'' ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</section>
