<?php
	$ipvisitor = $_SERVER['REMOTE_ADDR'];
	$cekspambot = file_get_contents("http://www.stopforumspam.com/api?ip=".$ipvisitor);
	if(@eregi('&lt;appears&gt;yes&lt;/appears&gt;',$cekspambot)){die("");}
	header("location:../index.php");
?>