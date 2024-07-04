<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>InAct | Forgot Password</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.7 -->
  <link rel="shortcut icon" href="<?= base_url('asset/images/logo-no-desc-miniview.png') ?>">
  <link rel="stylesheet" href="<?= base_url('asset/template/bower_components/bootstrap/dist/css/bootstrap.min.css') ?>">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="<?= base_url('asset/template/bower_components/font-awesome/css/font-awesome.min.css') ?>">
  <!-- Ionicons -->
  <link rel="stylesheet" href="<?= base_url('asset/template/bower_components/Ionicons/css/ionicons.min.css') ?>">
  <!-- Theme style -->
  <link rel="stylesheet" href="<?= base_url('asset/template/dist/css/AdminLTE.css') ?>">
  <link rel="stylesheet" href="<?= base_url('asset/css/custom_style.css?v=0.2') ?>">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?= base_url('asset/template/plugins/iCheck/square/blue.css') ?>">
  <!-- validation engine -->
  <link rel="stylesheet" href="<?= base_url('asset/plugins/validation-engine/validationEngine.jquery.css') ?>">
  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
  .has-bg{
    background-image: url('<?= base_url("asset/images/remember-bg.jpg") ?>');
    background-size: cover;
  }

  </style>
</head>
<body class="hold-transition register-page has-bg">
<div class="register-box" style="margin-top:10%">
  <div class="register-logo">
    <a href="<?= base_url() ?>"><img src="<?= base_url("asset/images/logo.png") ?>" width="200px"></a>
  </div>
  <div class="register-box-body">
    <p class="login-box-msg">Forgot Password</p>
    <?= $msg; ?>
    <?= form_open("",["id"=>"form-validation"]); ?>
      <div class="form-group has-feedback">
        <input data-validation-engine="validate[required,custom[email]]" name="email" type="text" class="form-control" placeholder="Enter Your Email">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="row">
        <div class="col-xs-12">
          <button type="submit" name="submit" value="submit" class="btn btn-primary btn-block btn-flat">SUBMIT EMAIL</button>
        </div>
        <div class="col-xs-12 text-center" style="padding-top:20px">
          <a href="<?= base_url("login") ?>" ><i class="fa fa-long-arrow-left"></i> Back To Login</a>
        </div>
        <!-- /.col -->
      </div>
    </form>
  </div>
  <div class="footer-logo">
    <div class="footer-developed-by" style="color:#000;font-weight:bold">Developed By</div>
    <a class="footer-logo" href="https://interactive.co.id" title="interactive.co.id" target="_blank"><img src="<?= base_url('asset/images/interactive.png') ?>"></a>
  </div>
  <!-- /.form-box -->
</div>
<!-- /.register-box -->

<!-- jQuery 3 -->
<script src="<?= base_url('asset/template/bower_components/jquery/dist/jquery.min.js') ?>"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?= base_url('asset/template/bower_components/bootstrap/dist/js/bootstrap.min.js') ?>"></script>
<!-- iCheck -->
<script src="<?= base_url('asset/template/plugins/iCheck/icheck.min.js') ?>"></script>

<!-- validation engine -->
<script src="<?= base_url('asset/plugins/validation-engine/jquery.validationEngine.js') ?>"></script>
<script src="<?= base_url('asset/plugins/validation-engine/jquery.validationEngine-id.js') ?>"></script>
<script type="text/javascript">
$("#form-validation").validationEngine();
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });
</script>
</body>
</html>
