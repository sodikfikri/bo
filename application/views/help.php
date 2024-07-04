<link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
<style type="text/css">
  .help-title{
    text-align: center;
    font-size: 30px;
    color: #707070;
  }
  .component-title{
    text-align: center;
    font-size: 25px;
    font-weight: bold;
    color: #707070;
  }
  .component-description{
    text-align: center;
    font-size: 18px;
    color: #707070;
    padding: 0px 65px 0px 65px;
  }
  .image-icons{
    height: 110px;
  }
</style>
<section class="content">
<div class="row">
  <div class="col-md-12">
    <div class="box box-inact" style="padding-bottom:50px">
      <div class="box-body">
        <div class="row">
          <div class="col-md-12">
            <p class="help-title"><?= $this->gtrans->line("Help") ?> InAct Cloud</p>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6" style="text-align:center">
            <img class="image-icons" src="<?= base_url('asset/images/knowledge.png') ?>" >
            <p class="component-title">
              Knowledge Base
            </p>
            <p class="component-description">
              <?= $this->gtrans->line("The fastest way to get support is finding the answer throught Knowledge Base System") ?>
            </p>
            <a href="https://interactive.co.id/kb/" target="_blank" class="btn btn-primary ">Knowledge Base <i class="fa  fa-long-arrow-right"></i></a>
          </div>
          <div class="col-md-6" style="text-align:center">
            <img class="image-icons" src="<?= base_url('asset/images/support.png') ?>" >
            <p class="component-title">
              Ticket Support
            </p>
            <p class="component-description">
              <?= $this->gtrans->line("You can also easily access the ticket support to answer about InterActive Product & Service") ?>
            </p>
            <a href="https://interactive.co.id/kb/Komplain" target="_blank" class="btn btn-primary">Ticket Support <i class="fa  fa-long-arrow-right"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
</section>
