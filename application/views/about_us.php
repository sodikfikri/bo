<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
<style type="text/css">
  .font-lato{
    font-family: 'Lato', sans-serif;
  }
  .title-about{
    text-align: left;
    font-size: 20pt;
  }
  .developed-by{
    font-size: 8pt;
    margin-top: 20px;
  }
  .allrights-reserved{
    font-size: 8pt;
    padding: 22px 0px 22px 0px;
  }
  @media only screen and (min-width: 800px){
    .allrights-reserved{
      padding: 22px 200px 22px 200px;
    }
  }
  .logo-interactive{
    margin-bottom: 22px;
  }
  .link-interactive{
    cursor: pointer;
    padding: 3px 30px 5px 30px;
    border-radius: 20px;
    border: 1px solid #757575;
    display: inline;
  }
  .policy{
    font-color:#00cbce;
  }
  .version-text{
    margin-top: 20px;
    font-weight: bold;
  }
  .revision-description{
    background-color: #b0bec5;
    border: 1px solid #4b636e;
    text-align: left;
    padding: 10px;
  }

</style>
<section class="content">
<div class="row">
  <div class="col-md-12">
    <div class="box box-inact">
      <div class="box-body">
        <div class="row">
          <div class="col-md-12" style="text-align: center;margin-bottom: 20px;margin-top:100px">
            <img src="<?= base_url('asset/images/logo.jpg') ?>" width="150px">
            <p class="version-text">Versions <?= APP_VERSION ?></p>
            <a href="#" onclick="showDetailVersion('<?= APP_VERSION ?>')"><?= $this->gtrans->line("What's new on this version") ?>?</a>

            <p class="developed-by font-lato">Developed By</p>
            <div class="logo-interactive" >
              <img src="<?= base_url('asset/images/interactive.png') ?>">
            </div>
            <div class="link-interactive" onclick="gotoInteractive()">
              interactive.co.id
            </div>
            <div class="allrights-reserved font-lato">
              @Allrights reserved. InterActive website are registered trademarks of InterActive Technologies Corp in Indonesia and other countries. The name of mentioned here may be the trademarks of their respective Owners.
            </div>
            <p >
              <a  href="#"><font class="policy font-lato">Term and Condition</font></a>
              -
              <a href="#"><font class="policy font-lato">Privacy Policy</font></a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="modal-default">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><?= $this->gtrans->line("What`s New On This Versions") ?></h4>
      </div>
      <div class="modal-body">
        <?= $detailVersion ?>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
</section>
<script>
function showDetailVersion(version){
  $("#modal-default").modal('show');
}
</script>
