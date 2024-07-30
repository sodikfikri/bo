<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>InAct | Login</title>
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
  <link rel="stylesheet" href="<?= base_url('asset/css/custom_style.css?v=0.3') ?>">
  <!-- iCheck -->
  <link rel="stylesheet" href="<?= base_url('asset/template/plugins/iCheck/square/blue.css') ?>">
  <!-- validation engine -->
  <link rel="stylesheet" href="<?= base_url('asset/plugins/validation-engine/validationEngine.jquery.css') ?>">

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
      background-image: url('<?= base_url("asset/images/login-bg.jpg") ?>');
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
<body class="hold-transition register-page has-bg" >
<div class="register-box" style="margin-top:7%">
  <div class="register-logo" >
    <a href="<?= base_url() ?>"><img src="<?= base_url("asset/images/logo.png") ?>" width="200px"></a>
  </div>
  <div class="register-box-body">
    <?= !empty($msg) ? $msg : '' ?>
    <div class="callout callout-info" id="divMayus" style="display:none">
      <h4><i class="fa fa-lock"></i> Capslock Is On</h4>
    </div>
    <?= form_open("",["id"=>"form-validation"]); ?>
      <div class="form-group has-feedback">
        <input value="<?= !empty($this->input->post("username")) ? $this->input->post("username") : '' ?>" onkeypress="capLock(event)" autofocus data-validation-engine="validate[required, custom[email]]" name="username" type="text" class="form-control" placeholder="Email">
        <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
      </div>
      <div class="form-group has-feedback field-loginform-password required">
        <input onkeypress="capLock(event)" data-validation-engine="validate[required]" name="password" type="password" id="password" class="form-control" placeholder="Password">
        <i class="fa fa-eye form-control-feedback"></i>
      </div>
      <div class="row">
        <div class="col-xs-8">
          <div class="checkbox icheck">
            <label>
              <input type="checkbox" > Remember Meeeee
            </label>
          </div>
        </div>
        <!-- /.col -->
        <div class="col-xs-4">
          <button type="submit" name="submit" value="submit" class="btn btn-primary btn-block btn-flat">LOGIN</button>
        </div>
      </div>
    </form>
  <div class="row" style="margin-top:15px">
    <div class="col-md-6">
      <a href="<?= base_url("forgot-password") ?>" class="text-center" style="font-weight:bold;color:#0d47a1">FORGOT PASSWORD</a><br>
    </div>
    <div class="col-md-6 text-right">
      <a href="<?= base_url("register") ?>" class="text-center" style="font-weight:bold;color:#0d47a1" >REGISTER</a>
    </div>
  </div>
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
<script>
  function capLock(e){
    var kc = e.keyCode ? e.keyCode : e.which;
    var sk = e.shiftKey ? e.shiftKey : kc === 16;
    var display = ((kc >= 65 && kc <= 90) && !sk) ||
        ((kc >= 97 && kc <= 122) && sk) ? 'block' : 'none';
        document.getElementById('divMayus').style.display = display
  }
  $("#form-validation").validationEngine();
  $(function () {
    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });
  var defaultShowPassword = "show";
  function togglePassword(passwordID,labelToggleID){
    if(defaultShowPassword=="hide"){
      defaultShowPassword ="show";
      $("#"+passwordID).attr("type","text");
      $("#"+labelToggleID).html("HIDE");
    }else{
      defaultShowPassword ="hide";
      $("#"+passwordID).attr("type","password");
      $("#"+labelToggleID).html("SHOW");
    }
  }
  togglePassword('password','label-toggle-password');
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
