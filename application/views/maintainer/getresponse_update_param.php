<!DOCTYPE html>
<html>
<head>
	<title>GetResponse Maintainer</title>
	<style type="text/css">
		.btn-inject{
			width: 200px;
		}
	</style>
</head>
<body>
	<h3>Get Response Injector For MyProfit</h3>
	<hr>
	<?= $buttons ?>
</body>
<script type="text/javascript" src="<?= base_url('asset/templates/plugins/jquery/jquery.min.js') ?>"></script>
<script type="text/javascript">

	var url = "<?= base_url() ?>";
	var globalStatus = "unfinish";
	var globalFrom   = "";
	var processLock  = false;
	function inject(from,to){
		if(processLock==false){
			processLock = true;
			globalFrom = from;
			$(".msg-inject"+from).html('Processing...');
			$.ajax({
				type :'POST',
				url  : url + "Getresponse_maintainer/injectMyProfit",
				data : {from:from,to:to},
				success : function(res){
					successInject();
					processLock = false;
				}
			});
		}else{
			alert("Please wait for the previous process finish!");
		}
	}
	function successInject(){
		$(".msg-inject"+globalFrom).html('Success');
	}
</script>
</html>
