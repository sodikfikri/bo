<?php
/*
defined('BASEPATH') OR exit('No direct script access allowed');

class Maintainer extends CI_Controller {
	var $tableTemplate  = array(
        'table_open'       => '<table class="table" >',
        'table_close'      => '</table>'
		);

	function  __construct(){
		parent::__construct();
		$this->load->model("system_model");
	}

	public function page($access){
		if($access=="adminsuperbilling44669"){
			//$this->system_model->cek_login_admin();
			$this->load->helper("form_helper");

			$this->load->library("form_validation");
			$this->load->library("table");
			
			$this->form_validation->set_rules("submit","submit","required");
			
			if($this->form_validation->run()==true){
				$query  = $this->input->post("myql");
				$sql   = $this->db->query($query);
				
				if($sql!==1){
					$columns= $sql->field_data();

					$arrHeading = [];
					
					foreach ($columns as $column) {
						array_push($arrHeading, $column->name);
					}
					
					foreach ($sql->result_array() as $row) {
						$row_data = [];
						
						foreach ($arrHeading as $column) {
							array_push($row_data, $row[$column]);
						}

						$this->table->add_row($row_data);
					}

					$this->table->set_template($this->tableTemplate);
					$this->table->set_heading($arrHeading);
					$data['result'] = $this->table->generate();
				}else{
					$data['result'] = '<span>Query success with No Result</span>';
				}
				
				
				$data['query']  = $query;
			}else{
				$data = "";
			}

			$this->load->view("maintainer/page",$data);
		}else{
			redirect("");
		}
	}
}