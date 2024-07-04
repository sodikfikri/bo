<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
<section class="content-header">
    <h1>
      Admin Manager
    </h1>
</section>
<section class="content">
<div class="row">
  <div class="col-md-12">
    <div class="box box-inact" style="padding-bottom:50px">
      <div class="box-body">
        <div class="row">
          <div class="col-md-12">
            <?= anchor('rootaccess/admin-manager/add-new','<i class="fa fa-user-plus"></i> New Admin'); ?>
            <?= !empty($msg) ? $msg : '' ?>
            <?= $tableData ?>
          </div>
        </div>
        
      </div>
    </div>
  </div>
</div>
</section>
