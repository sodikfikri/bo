<?php 

class Error_log extends Root_controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->library("session");
		$this->setMenu(5);
		$this->checkPermission();
		$this->load->library("encryption_org");

	}

	function index(){
		$main = $this->getMain();

		$dir = FCPATH.DIRECTORY_SEPARATOR."application".DIRECTORY_SEPARATOR."logs";
		$logFiles = [];
		if (is_dir($dir)) {
		    if ($dh = opendir($dir)) {
		        while (($file = readdir($dh)) !== false) {
		            if($file!="." && $file!=".." && $file!="index.html" && $file !="My_log.php"){
		            	$logFiles[] = $file;
		            }
		        }
		        closedir($dh);
		    }
		}
		rsort($logFiles);
		$data["logFiles"] 	= $logFiles;
		$main["title"] 		= "Error Log";
		$main["content"] 	= "root/error_log";
		$main["viewData"]  	= $data;
		$main["externalJS"] = [
			base_url("asset/template/bower_components/datatables.net/js/jquery.dataTables.min.js"),
        	base_url("asset/template/bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js")
		];

		$main["externalCSS"]= [
			base_url("asset/template/bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css")
		];

		$this->load->view("layouts/main_root",$main);
		
	}

	function download($encFileName){
		$this->load->helper('download');
		$fileName = $this->encryption_org->decode($encFileName);
		force_download(FCPATH."application".DIRECTORY_SEPARATOR."logs".DIRECTORY_SEPARATOR.$fileName,NULL);
	}
}