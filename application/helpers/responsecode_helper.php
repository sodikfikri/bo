<?php 
	function getResponseCode($id,$SN,$deviceTZ){
		switch ($id) {
			case '1':
				$response = "GET OPTION FROM: ".$SN."\r\n".
							"Stamp=0\r\n".
							"OpStamp=0\r\n".
							"PhotStamp=0\r\n".
							"ErrorDelay=60\r\n".
							"Delay=30\r\n".
							"TransTime=00:00;14:05\r\n".
							"TransInterval=1\r\n".
							"TransFlag=111111111111\r\n".
							"TimeZone=".$deviceTZ."\r\n".
							"RealTime=0\r\n".
							"Encrypt=0";
				break;
			case '2':
				$response = "GET OPTION FROM: ".$SN."\r\n".
			                "Stamp=0\r\n".
			                "OpStamp=0\r\n".
			                "PhotoStamp=0\r\n".
			                "ErrorDelay=60\r\n".
			                "Delay=10\r\n".
			                "ServerVer=2.4.1\r\n".
			                "PushProtVer=2.4.1\r\n".
			                "EncryptFlag=1000000000\r\n".
			                "PushOptionsFlag=1\r\n".
			                "supportPing=1\r\n".
			                "PushOptions=UserCount,TransactionCount,FingerFunOn,FPVersion,FPCount,FaceFunOn,FaceVersion,FaceCount,FvFunOn,FvVersion,FvCount,PvFunOn,PvVersion,PvCount,BioPhotoFun,BioDataFun,PhotoFunOn,~LockFunOn\r\n".
			                "TransTimes=00:00;14:05\r\n".
			                "TransInterval=1\r\n".
			                "TransFlag=TransData\tAttLog\tOpLog\tAttPhoto\tEnrollFP\tEnrollUser\tFPImag\tChgUser\tChgFP\tFACE\tUserPic\tFVEIN\tBioPhoto\r\n".
			                "Realtime=0\r\n".
			                "TimeZone=".$deviceTZ."\r\n".
			                "Encrypt=0";
				break;
			case '4':
				$response = "GET OPTION FROM: ".$SN."\r\n".
			                "Stamp=0\r\n".
			                "OpStamp=0\r\n".
			                "PhotoStamp=0\r\n".
			                "ErrorDelay=60\r\n".
			                "Delay=10\r\n".
			                "ServerVer=2.4.1\r\n".
			                "PushProtVer=2.4.1\r\n".
			                "EncryptFlag=1000000000\r\n".
			                "PushOptionsFlag=1\r\n".
			                "supportPing=1\r\n".
			                "PushOptions=UserCount,TransactionCount,FingerFunOn,FPVersion,FPCount,FaceFunOn,FaceVersion,FaceCount,FvFunOn,FvVersion,FvCount,PvFunOn,PvVersion,PvCount,BioPhotoFun,BioDataFun,PhotoFunOn,~LockFunOn\r\n".
			                "TransTimes=00:00;14:05\r\n".
			                "TransInterval=1\r\n".
			                "TransFlag=TransData\tAttLog\tOpLog\tAttPhoto\tEnrollFP\tEnrollUser\tFPImag\tChgUser\tChgFP\tFACE\tUserPic\tFVEIN\tBioPhoto\r\n".
			                "Realtime=0\r\n".
			                "TimeZone=".$deviceTZ."\r\n".
			                "Encrypt=0";
				break;

			default:
				$response = "";
				break;
		}
		return $response;
	}

	function getResponseCodeThermoAttendance($deviceID,$SN,$deviceTZ){
		$registryCode = hash('crc32', $deviceID);
      	$CI =& get_instance();
      	$response = "registry=ok\n".
	                "RegistryCode=".$registryCode."\n".
	                "ServerVersion=2.4.1\n".
	                "ServerName=InAct\n".
	                "PushProtVer=2.4.1\n".
	                "ErrorDelay=60\n".
	                "RequestDelay=30\n".
	                "TransTimes=00:00 14:00\n".
	                "TransInterval=2\n".
	                "TransTables=User Transaction\n".
	                "Realtime=0\n".
	                "SessionID=\n".
	                "TimeoutSec=10";
	                //"TimeZone=".$deviceTZ;

	    return $response;
	}