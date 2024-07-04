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
<div class="register-box" style="margin-top:5%">
  <div class="register-logo">
    <a href="<?= base_url() ?>"><img src="<?= base_url("asset/images/logo.png") ?>" width="200px"></a>
  </div>
  <div class="register-box-body">

    <?= form_open("register-auth",["id"=>"form-validation"]); ?>
      <p style="color:#7f0000">We have sent an email containing the OTP code to email <b><?= $email ?></b>. Please enter the OTP code in the column below</p>
      <div id="msg-result-submit" ></div>
      <div class="form-group">
        <input data-validation-engine="validate[required,custom[onlyNumber],minSize[6],maxSize[6]]"  name="otp-code" id="otp-code" type="number" maxlength="6" class="form-control" placeholder="Input OTP" style="font-size:24pt;text-align:center;height:auto">
      </div>
      <div id="countdown"></div>
      <div class="row">
        <div class="col-xs-12">
          <button type="submit" class="btn btn-primary btn-block btn-flat">SUBMIT OTP</button>
        </div>
        <div class="col-xs-12" style="text-align:center;margin-top:10px">
          <a href="#" onclick="resendOTP()" >Do Not Receive Email? Resend OTP</a>
          <p id="msg-otp" style="color:red;text-align:center"></p>
        </div>
      </div>
    <?= form_close() ?>
  </div>
  <!-- /.form-box -->
  <div class="footer-logo">
    <div class="footer-developed-by" style="color:#000;font-weight:bold">Developed By</div>
    <a class="footer-logo" href="https://interactive.co.id" title="interactive.co.id" target="_blank"><img src="<?= base_url('asset/images/interactive.png') ?>"></a>
  </div>
</div>
<!-- /.register-box -->

<!-- jQuery 3 -->
<script src="<?= base_url('asset/template/bower_components/jquery/dist/jquery.min.js') ?>"></script>
<!-- Bootstrap 3.3.7 -->
<script src="<?= base_url('asset/template/bower_components/bootstrap/dist/js/bootstrap.min.js') ?>"></script>
<!-- iCheck -->
<script src="<?= base_url('asset/template/plugins/iCheck/icheck.min.js') ?>"></script>
<script src="<?= base_url('asset/js/common.js') ?>"></script>
<!-- validation engine -->
<script src="<?= base_url('asset/plugins/validation-engine/jquery.validationEngine.js') ?>"></script>
<script src="<?= base_url('asset/plugins/validation-engine/jquery.validationEngine-id.js') ?>"></script>

<script>
  var myInterval;
  $("#form-validation").validationEngine();
  function countDown(expiredDate,pageDOM){
    myInterval = setInterval(function(){
      var countDownDate = new Date(expiredDate).getTime();
      // Get today's date and time
      var now = new Date().getTime();
      // Find the distance between now and the count down date
      var distance = countDownDate - now;
      // Time calculations for days, hours, minutes and seconds
      var days = Math.floor(distance / (1000 * 60 * 60 * 24));
      var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
      var seconds = Math.floor((distance % (1000 * 60)) / 1000);

      // Display the result in the element with id="demo"
      document.getElementById(pageDOM).innerHTML = minutes + " minute " + seconds + " seconds ";

      // If the count down is finished, write some text
      if (distance < 0) {
        clearInterval(myInterval);
        document.getElementById(pageDOM).innerHTML = "EXPIRED";
      }
    }
    , 1000);
  }

  countDown("<?= $expired ?>","countdown");
  $(function () {

    $('input').iCheck({
      checkboxClass: 'icheckbox_square-blue',
      radioClass: 'iradio_square-blue',
      increaseArea: '20%' /* optional */
    });
  });
  var url = "<?= base_url() ?>";
  var id = "<?= $id ?>";
  function resendOTP(){
    $("#msg-otp").html('Processing...');

    $.ajax({
      method : "POST",
      url    : url + "ForgotPasswordResendOTP",
      data   : {id:id},
      success: function(res){
        if(res!="failed"){
          $("#countdown").html("");
          clearInterval(myInterval);
          countDown(res,"countdown");

          $("#msg-otp").html('New OTP was sent');
        }else{
          $("#msg-otp").html('Processing Failed!');
        }
      }
    })
  }
  function submitOTP()
  {
    var otp = $("#otp-code").val();
    $.ajax({
      method : "POST",
      url    : url + "submitOTPForgotPassword",
      data   : {id:id,otp:otp},
      success: function (obj){
        var res = jQuery.parseJSON(obj);
        if(res.status=="found"){
          var authkey = res.data;
          window.open(url+"change-password/"+authkey+"/"+id, "_self");
        }else if(res.status=="notfound"){
          $("#msg-result-submit").html('<div class="callout callout-danger">'+
                                       '<h5>OTP Not Found!</h5>'+
                                       '<p>In May be expired</p>'+
                                       '</div>');
        }else if(res.status=="notmatch"){
          $("#msg-result-submit").html('<div class="callout callout-danger">'+
                                       '<h5>OTP Not Match!</h5>'+
                                       '</div>');
        }
      }
    });
  }
  function validatePassword(){
    var password = $("#password").val();
    var repassword = $("#repassword").val();
    if(password==repassword){
      return true;
    }else{
      $(".registration-msg").html('<div class="col-md-12"><div class="callout callout-danger">'+
                                    '<p>Password Not Match</p>'+
                                  '</div></div>');
      return false;
    }
  }
  stat = 0;
  jQuery("#form-validation").validationEngine('attach', {
    onValidationComplete: function(form, status){
      if(status==true){
        stat = stat + 1;
        if(stat%2==0){
          validatePassword();
          submitOTP();
          return false;
        }
      }
    }
  });
</script>
</body>
</html>
