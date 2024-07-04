<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?= $this->gtrans->line("Notifications") ?>
  </h1>
</section>
<!-- Main content -->
<section class="content">

<div class="row">
  <div class="col-md-12">
    <div class="box box-inact">
      <!-- /.box-header -->
      <div class="box-body">
        <div class="row">
          <div class="col-md-12" style="padding-top:10px">
            <?= !empty($tableDate) ? $tableDate : "" ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</section>
