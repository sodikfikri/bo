<style>
.pac-container {
    z-index: 1051 !important;
}
.product-label-1{

    font-size: 18px;

    font-weight: 600;

    font-style: normal;

    font-stretch: normal;

    line-height: 1;

    letter-spacing: normal;

    text-align: center;

    color: #43425d;

  }
.product-label-2{

    font-size: 18px;

    font-weight: normal;

    font-style: normal;

    font-stretch: normal;

    line-height: 1.44;

    letter-spacing: normal;

    color: #43425d;

  }
  .product-label-3{

    font-size: 24px;

    font-weight: 600;

    font-style: normal;

    font-stretch: normal;

    line-height: 36px;

    letter-spacing: normal;

    color: #2684FC;

  }
  
  .product-label-4{

    font-size: 32px;

    font-weight: 600;

    font-style: normal;

    font-stretch: normal;

    line-height: 36px;

    letter-spacing: normal;

    color: #002379;

  }
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    <?= $this->gtrans->line("Checkout License") ?>
  </h1>
</section>
<!-- Main content -->
<section class="content">
<!-- Info boxes -->
<?= !empty($addonsAlert) ? $addonsAlert : '' ?>
<div class="row">
  <div class="col-md-8">
    <div class="box box-inact">
      <!-- /.box-header -->
      <div class="box-body">
        <div class="row">
          <div class="col-md-12">
			<table width="100%">
              <tr>
                <td width="10%" align="center"><img src="<?= base_url() ?>asset/images/users.png" alt=""></td>
                <td width="50%"><font class="product-label-1">Employees INTRAX Licence</font><br><font class="product-label-2"><?= $cabangName; ?> <span style="color:#2684FC">*</span> <?= $ctLicense; ?> license<br>Rp150.000.00/license</font></td>
                <td width="40%" align="right"><font class="product-label-2">Rp. <?= number_format($ctLicense*150000); ?></font></td>
              </tr>
            </table>
			<hr style="border-top: 5px dashed #eee;">
			<table width="100%" style="background-color:rgba(38,132,252,0.1);border-radius:10px;">
              <tr>
                <td width="10%" align="center"></td>
                <td width="55%"><font class="product-label-3"><?= $this->gtrans->line("TOTAL PURCHASE"); ?></font></td>
                <td width="35%" align="center"><font class="product-label-3">Rp. <?= number_format($ctLicense*150000); ?></font></td>
              </tr>
            </table>
          </div>  
        </div>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="box box-inact">
      <!-- /.box-header -->
      <div class="box-body">
        <div class="row">
          <div class="col-md-12">
			<h4><img src="<?= base_url() ?>asset/images/wallet.png" alt=""> Payment Method</h4>
			<div class="callout callout-secondary">
				<font class="product-label-1"><img src="<?= base_url() ?>asset/images/qris.png" alt=""> QRIS</font><br>
				<font class="product-label-2"><i>Fee Rp. 0</i></font>
			</div>
			<hr style="border-top: 5px dashed #eee;">
			<h4><font class="product-label-1">Payment Details</font></h4>
            <table width="100%">
              <tr>
                <td width="50%"><font class="product-label-2">Subtotal</font></td>
                <td width="50%" align="right"><font class="product-label-2">Rp. <?= number_format($ctLicense*150000); ?></font></td>
              </tr>
              <tr>
                <td width="50%"><font class="product-label-2">Payment received</font></td>
                <td width="50%" align="right"><font class="product-label-2">Rp. 0</font></td>
              </tr>
              <tr>
                <td width="50%"><font class="product-label-2">Tax</font></td>
                <td width="50%" align="right"><font class="product-label-2">Rp. 0</font></td>
              </tr>
			  <tr>
                <td width="50%"><font class="product-label-2">Global discount</font></td>
                <td width="50%" align="right"><font class="product-label-2">Rp. 0</font></td>
              </tr>
			  <tr>
                <td width="50%"><font class="product-label-2">Unique code</font></td>
                <td width="50%" align="right"><font class="product-label-2">Rp. 0</font></td>
              </tr>
			  <tr>
                <td width="50%"><font class="product-label-1"><b>Payment Total</b></font></td>
                <td width="50%" align="right"><font class="product-label-1">Rp. <?= number_format($ctLicense*150000); ?></font></td>
              </tr>
            </table>
			<hr style="border-top: 5px dashed #eee;">
			<?= '<button data-toggle="tooltip" data-placement="top" title="'.$this->gtrans->line("Continue Paying").'" type="button" class="btn btn-primary btn-block" style="border-radius:100px" data-toggle="modal" onclick="nextPay(\''.$this->encryption_org->encode($lastID).'\')"><i class="fa fa-money"></i> '.$this->gtrans->line("Continue Paying").'</button>' ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="frmPayingLicense">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title"><div id="frm-text-pay"></div></h4>
        </div>
        <div class="modal-body">
			<center>
				<img src="<?= base_url() ?>asset/images/qris-big.png" alt="" width="200px">
				<div id="qrcode"></div>
				<font class="product-label-1">NMID: ID2024321836569</font><br>
				<font class="product-label-4">Rp. <?= number_format($ctLicense*150000); ?></font><br>
				<font class="product-label-2" id="txt-timeout"></font>
			</center>
        </div>
        <div class="modal-footer">
        </div>
      </div>
      <!-- /.modal-content -->
    </div>
  <!-- /.modal-dialog -->
  </div>
</div>
</section>
<script src="<?= base_url('asset/plugins/qrcodejs/qrcode.js') ?>"></script>
<script type="text/javascript">
  var url = "<?= base_url() ?>";
  var existingTimezone = '';
  var stat = 0;

  $('.select2').select2({
    dropdownParent: $("#frm-text")
  });
 
  $("#datatable").DataTable({
    responsive: true
  });
  
  function nextPay(codeLicense){
    $("#frm-text-pay").html("QRIS");
    $("#loader").fadeIn(1);
	$.ajax({
      url: url + "generateQris",
      type: "POST",
      data: {subscription:codeLicense,price:<?= $ctLicense*150000; ?>},
      success: function (res) {
        result = JSON.parse(res);
        $("#qrcode").html('');
		$("#qrcode").html('<img src="https://quickchart.io/qr?size=300&text='+result.qris_content+'" title="" style="margin:0 auto;" id="qr-code" />');
		$("#txt-timeout").html('<font class="product-label-2" id="txt-timeout">Segera lakukan pembayaran sebelum tanggal '+result.qris_date_expired+' WIB</font>');
        qris_invoiceid = result.qris_invoiceid;
        qris_refno = result.qris_refno;
		check();
      },
    });
	$("#loader").fadeOut(1);
	$("#frmPayingLicense").modal("show");
  }
  
  function check() {
    $.ajax({
	  url: url + "checkQris",
	  type: "POST",
	  data: {invoiceId:qris_invoiceid,refNo:qris_refno},
	  success: function (res) {
		result = JSON.parse(res);
		statusCheck = result.statusCheck;
		if(result.statusCheck == 'success'){
			$("#frmPayingLicense").modal("hide");
			Swal.fire({
              position: 'center',
              icon: 'success',
              type: 'success',
              title: 'Your payment has been successfully received!',
              showConfirmButton: false,
              timer: 1700
            });
			setTimeout(function() {
				window.close();
			}, 2000);
		} else {
			check();
		}
	  },
	});
  }
	
	
	$(document).ready(function() {
	  $(window).keydown(function(event){
		if(event.keyCode == 13) {
		  event.preventDefault();
		  return false;
		}
	  });
	});
  
  
</script>