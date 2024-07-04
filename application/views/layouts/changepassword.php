<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>InAct | Change Password</title>
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
  <!-- Custom style -->
  <link rel="stylesheet" href="<?= base_url('asset/css/custom_style.css?v=0.2') ?>">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?= base_url('asset/template/plugins/iCheck/square/blue.css') ?>">

  <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
  <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->

  <!-- Google Font -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
  <style>
    .show-password-container{
      text-align: right;
    }
    .show-password{
      cursor: pointer;
    }
  .has-bg{
    background-image: url('<?= base_url("asset/images/remember-bg.jpg") ?>');
    background-size: cover;
  }
  .has-feedback {
    position:relative;
  }

  .has-feedback .form-control {
    padding-right:42.5px;
  }

  .form-control-feedback {
    position:absolute;
    top:0px;
    right:2px;
    z-index:2;
    display:block;
    width:34px;
    height:34px;
    line-height:34px;
    text-align:center;
    cursor:pointer;
    pointer-events:initial;
  }
  </style>
</head>
<body class="hold-transition register-page has-bg">
<div class="register-box" style="margin-top:8%">
  <div class="register-logo">
    <a href="<?= base_url() ?>"><img src="<?= base_url("asset/images/logo.png") ?>" width="150px"></a>
  </div>

  <div class="register-box-body">
    <?= !empty($msg) ? $msg : ''; ?>
    <div class="callout callout-info" id="divMayus" style="display:none">
      <h4><i class="fa fa-lock"></i> Capslock Is On</h4>
    </div>
    <?= form_open(""); ?>
    <div class="form-group has-feedback field-loginform-password required">
      <input onkeypress="capLock(event)" name="password1" id="password1" type="password" class="form-control" placeholder="Enter New Password">
      <span class="fa fa-eye form-control-feedback"></span>
    </div>
    <div class="form-group has-feedback field-loginform-password required">
      <input onkeypress="capLock(event)" name="password2" id="password2" type="password" class="form-control" placeholder="Enter Password Again">
      <span class="fa fa-eye form-control-feedback"></span>
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
<script>
  function capLock(e){
    var kc = e.keyCode ? e.keyCode : e.which;
    var sk = e.shiftKey ? e.shiftKey : kc === 16;
    var display = ((kc >= 65 && kc <= 90) && !sk) ||
        ((kc >= 97 && kc <= 122) && sk) ? 'block' : 'none';
        document.getElementById('divMayus').style.display = display
  }

  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });
  function togglePassword(passwordID,labelToggleID,temp){

    if($("#"+temp).val()=="hide"){
      $("#"+temp).val("show");
      $("#"+passwordID).attr("type","text");
      $("#"+labelToggleID).html("HIDE");
    }else{
      $("#"+temp).val("hide");
      $("#"+passwordID).attr("type","password");
      $("#"+labelToggleID).html("SHOW");
    }
  }
  togglePassword('password','label-toggle-password','tempToggle1')
  togglePassword('repassword','re-label-toggle-password','tempToggle2')
  $(document).ready(function(){
    $('.form-control-feedback').click(function() {
      var attr = $(this).siblings('input').attr('type');
      if (attr == 'password') {
        $(this).siblings('input').attr('type', 'text');
        $(this).removeClass('far fa-eye');
        $(this).addClass('far fa-eye-slash');
      } else {
        $(this).siblings('input').attr('type', 'password');
        $(this).removeClass('far fa-eye-slash');
        $(this).addClass('far fa-eye');
      }
    });
  });
</script>
</body>
</html>
