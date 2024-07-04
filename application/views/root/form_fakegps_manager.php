<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
<section class="content-header">
    <h1>
      <?= $title ?>
    </h1>
</section>
<section class="content">
<div class="row">
  <div class="col-md-12">
    <div class="box box-inact" style="padding-bottom:50px">
      <div class="box-body">
        <div class="row">
          <div class="col-md-12">
            <form class="" action="<?= base_url("rootaccess/save-fakegps") ?>" method="POST">
              <div class="row">
                <div class="col-md-6">
                  
                  <input type="hidden" name="id" value="<?= !empty($id) ? $id : '' ?>">

                  <div class="form-group">
                    <label>Code</label>
                    <input type="text" required name="fakegps_code" class="form-control" value="<?= !empty($edit->fakegps_code) ? $edit->fakegps_code : '' ?>">
                  </div>
                  <div class="form-group">
                    <label>Application Name</label>
                    <input type="text" required name="fakegps_name" class="form-control" value="<?= !empty($edit->fakegps_name) ? $edit->fakegps_name : '' ?>">
                  </div>
                  <div class="form-group">
                    <label>Description</label>
                    <input type="text" required name="fakegps_keterangan" autocomplete="off" class="form-control" value="<?= !empty($edit->fakegps_keterangan) ? $edit->fakegps_keterangan : '' ?>">
                  </div>
                </div>
                <div class="col-md-12">
                  <?= anchor('rootaccess/fakegps-manager','Cancel',['class' => 'btn btn-danger']); ?>
                  <button type="submit" name="submit" value="submit" class="btn btn-primary">Save Fake GPS</button>
                </div>
              </div>
              
            </form>
          </div>
        </div>
        
      </div>
    </div>
  </div>
</div>
</section>
