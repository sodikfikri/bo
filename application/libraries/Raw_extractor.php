<?php 
/**
 * 
 */
class Raw_extractor
{
	
	function __construct()
	{
		

	}

	function lineToArray($rowData){
		$output = [];
		$arrColumn = preg_split("/[\t]/", $rowData);
		foreach ($arrColumn as $column) {
			$arrColumnUnit = explode("=", $column,2);
			$columnName = !empty($arrColumnUnit[0]) ? $arrColumnUnit[0] : "";
			$columnVal  = !empty($arrColumnUnit[1]) ? $arrColumnUnit[1] : "";
			if($columnName!=""){
				$output[strtolower($columnName)] = $columnVal;
			}
		}
		return $output;
	}

	function lineToArrayLowerIndex($rowData){
		$output = [];
		$arrColumn = preg_split("/[\t]/", $rowData);
		foreach ($arrColumn as $column) {
			$arrColumnUnit = explode("=", $column,2);
			$columnName = !empty($arrColumnUnit[0]) ? $arrColumnUnit[0] : "";
			$columnVal  = !empty($arrColumnUnit[1]) ? $arrColumnUnit[1] : "";
			if($columnName!=""){
				$output[strtolower($columnName)] = $columnVal;
			}
		}
		return $output;
	}
	
	function rawToRow($rawData){
		$arrRow = preg_split("/[\r\n]/",$rawData);
		$arrRow = array_filter($arrRow,function($row){
			return !empty($row);
		});
		return $arrRow;
	}
}