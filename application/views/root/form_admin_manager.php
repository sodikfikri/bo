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
            <form class="" action="<?= base_url("rootaccess/save-admin") ?>" method="POST">
              <div class="row">
                <div class="col-md-6">
                  
                  <input type="hidden" name="id" value="<?= !empty($id) ? $id : '' ?>">

                  <div class="form-group">
                    <label>Admin Name</label>
                    <input type="text" required name="fullname" class="form-control" value="<?= !empty($edit->fullname) ? $edit->fullname : '' ?>">
                  </div>
                  <div class="form-group">
                    <label>Username</label>
                    <input type="text" required name="username" class="form-control" value="<?= !empty($edit->username) ? $edit->username : '' ?>">
                  </div>
                  <div class="form-group">
                    <label>Email</label>
                    <input type="email" required name="email" autocomplete="off" class="form-control" value="<?= !empty($edit->email) ? $edit->email : '' ?>">
                  </div>
                  <div class="form-group">
                    <label>Password</label>
                    <input <?= ($type=="add") ? "required" : '' ?> type="password" name="password" class="form-control">
                    <div class="text-red">keep empty if you wat to make no change</div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <div class="checkbox">
                      <label><input type="checkbox" name="access[]" value="2" <?= (!empty($access) && in_array(2, $access)) ? 'checked' : '' ?> > List Device</label>
                    </div>
                    <div class="checkbox">
                      <label><input type="checkbox" name="access[]" value="3" <?= (!empty($access) && in_array(3, $access)) ? 'checked' : '' ?>> Menu Manager</label>
                    </div>
                    <div class="checkbox">
                      <label><input type="checkbox" name="access[]" value="4" <?= (!empty($access) && in_array(4, $access)) ? 'checked' : '' ?>> Admin Manager</label>
                    </div>
                    <div class="checkbox">
                      <label><input type="checkbox" name="access[]" value="5" <?= (!empty($access) && in_array(5, $access)) ? 'checked' : '' ?>> Error Log</label>
                    </div>
                    <div class="checkbox">
                      <label><input type="checkbox" name="access[]" value="6" <?= (!empty($access) && in_array(6, $access)) ? 'checked' : '' ?>> Company Manager</label>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <?= anchor('rootaccess/admin-manager','Cancel',['class' => 'btn btn-danger']); ?>
                  <button type="submit" name="submit" value="submit" class="btn btn-primary">Save Admin</button>
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
