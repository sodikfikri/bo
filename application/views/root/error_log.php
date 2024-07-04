<style type="text/css">
  .file-error{
    margin-bottom: 20px;
  }
  .download-file{
    cursor:pointer;
  }
</style>
<section class="content-header">
    <h1>
      Error Log
    </h1>
</section>
<section class="content">
<div class="row">
  <div class="col-md-12">
    <div class="box box-inact" style="padding-bottom:50px">
      <div class="box-body">
        <div class="row">
          <div class="col-md-6">
            <table class="table table-bordered dataTable" id="datatable">
              <thead>
                <th>File</th>
                <th class="text-center"></th>
              </thead>
              <tbody>
                <?php 
                foreach ($logFiles as $file) {
                  echo '<tr>
                          <td>
                            <i class="fa fa-file-text-o"></i> '.$file.'
                          </td>
                          <td class="text-center">
                            <span class="download-file" onclick="download(\''.$this->encryption_org->encode($file).'\')">
                              <i class="fa fa-cloud-download  text-blue" ></i> Download
                            </span>
                          </td>
                        </tr>';
                  }
              ?>
              </tbody>
            </table>           
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</section>
<script type="text/javascript">
  var url = "<?= base_url() ?>";
  function download(file){
    window.open(url+"rootaccess/error/download/"+file,"_self");
  }
  $(function () {
    $('#datatable').DataTable();
  });
</script>