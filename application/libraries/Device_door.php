<?php 
/**
 * 
 */
class Device_door
{
	function isOpenPermission($SN){
		return true;
		/*
		$openHours = "23:00";
		$closeHours= "09:00";

		$nowHours  = date("H:i");
		
		$timeOpen  = strtotime($openHours);
		$timeClose = strtotime($closeHours);
		$timeNow   = strtotime($nowHours);
		
		if($timeNow>$timeClose && $timeNow<$timeOpen){
			//check whiltelist
			$whiteList = $this->getWhiteList();
			if(in_array($SN, $whiteList)){
				return true;
			}else{
				return false;
			}
		}else{
			return true;
		}
		*/
	}

	function getWhiteList(){
		return [
			"CC5B194960267"
		];
	}
	
}