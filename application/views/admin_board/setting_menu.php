
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Setting Menu
  </h1>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-md-4 col-xs-12">
      <div class="box">
        <div class="box-header">
          <h3 class="box-title">Menu Baru</h3>
        </div>
        <div class="box-body">
          <form method="post" id="form-validate" action="">
          <div class="">
            <input type="hidden" name="kd" value="<?php echo !empty($data_edit->menuid) ? $data_edit->menuid : ''; ?>">
            <div class="form-group">
              <label>Parent Menu</label>
                <?php echo $combo_parent; ?>
            </div>
            <div class="form-group">
              <label>Lokasi Menu</label>
              <div>
              <input type="number" name="urut" class="form-control col-md-4" value="<?php echo !empty($data_edit->urut) ? $data_edit->urut : ''; ?>" id="urut">
              <font color="red">Jika dikosongkan menu akan diletakkan paling bawah</font>
            </div>
            </div>
            <div class="form-group">
              <label>Caption</label>
              <input type="text" name="caption" class="form-control" value="<?php echo !empty($data_edit->menucaption) ? $data_edit->menucaption : ''; ?>" required>
            </div>

            <div class="form-group">
              <label>Class Icon</label>
                <input type="text" name="class_icon" class="form-control" value="<?php echo !empty($data_edit->class_icon) ? $data_edit->class_icon : ''; ?>" >
            </div>
            <div class="form-group">
              <label>Link</label>
                <input type="text" name="link" class="form-control" value="<?php echo !empty($data_edit->link) ? $data_edit->link : ''; ?>" required>
            </div>
            <div class="form-group">

                <input type="checkbox" <?php echo (!empty($data_edit->newTab)) ? 'checked' : ''; ?> name="newtab" value="1" > <label>Buka di tab baru</label>
            </div>
            <div class="form-group">
              <label></label>
                <div class="btn-group">
                <button type="submit" name="submit" value="submit" class="btn btn-primary"><?php echo !empty($data_edit->menuid) ? 'Simpan perubahan' : 'Tambahkan';   ?></button>
                <?php echo !empty($data_edit->menuid) ? '<a href="'.$mainUrl.'" class="btn btn-danger">Batal</a>' : ''; ?>
                </div>
            </div>
          </div>
        </form>
        </div>
      </div>
    </div>
    <div class="col-md-8 col-xs-12">
      <div class="box">
        <div class="box-header">
          <div class="row">
            <div class="col-md-6"><h3 class="box-title">Daftar Menu</h3></div>
            <div class="col-md-6 text-right"><button class="btn btn-primary" onclick="spreadMenu()"><i class="fa fa-refresh"></i> Spread new menu</button></div>
          </div>
          
        </div>
        <div class="box-body">
           <div style="height: 500px; overflow-y: scroll;">
          <?php echo $tabel_data; ?>
          </div>
        </div>
      </div>
    </div>

  <!-- /.col -->
</div>
<!-- /.row -->
</section>
<!-- /.content -->
<script type="text/javascript">
  $(document).ready(function(){
    $("#form-validate").submit(function(){
      var urut = $("#urut").val();
      if(urut!==''&&urut=='0'){
        alert("Lokasi menu tidak boleh nol (\"0\")!");
        return false;
      }
    });
  });

  <?php
  if(!empty($err)){
    echo !empty($err) ? $err : '';
  }
  ?>
  function set_parent(id){
    count_parent(id)
    $("#parent").val(id);
  }
  function count_parent(id_menu){

    $.ajax({
      type : 'POST',
      url  : '<?php echo base_url("count-parent"); ?>',
      data : {menu:id_menu},
      success: function(res){

        if(res>2){
          alert("Child menu melebihi yang diijinkan!");
          $("#parent").val('');
        }
      }
    });
  }
  function spreadMenu(){
    $.ajax({
      type : 'POST',
      url  : '<?php echo base_url("root/menu_manager/spreadmenu"); ?>',
      success : function(res){
        if(res=="OK"){
          alert("Menu was spread successfully!");
        }
      }
    })
  }
</script>
