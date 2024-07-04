<?php 
class Intrax_library
{
	
	private $CI;
	function __construct()
	{
		$this->CI =& get_instance();
	}

	function updateLicense($companyId,$existingPlan,$newPlan,$options=null){
		switch ($existingPlan) {
			case '1':
				$existingPlan = "lite";		
				break;
			case '2':
				$existingPlan = "premium";
				break;
		}
		
		if($existingPlan!=$newPlan){
			
			/*upgrade plan dan renew*/

			if($options!=null){
				$activeDay = $options["activeDay"];
				$maxLicense= $options["maxLicense"];

				$this->changeLicense($companyId,$newPlan,$maxLicense);
				$this->renewLicense($companyId,$activeDay,$maxLicense);
				$this->CI->load->model("subscription_model");
				switch ($newPlan) {
					case 'lite':
						$newPlanId = "1";		
						break;
					case 'premium':
						$newPlanId = "2";
						break;
				}
				$this->CI->subscription_model->changeIntraxPlan($companyId,$newPlanId);
			}
		}else{
			/* change duedate or change userlimit*/ 
			if($options!=null){
				$maxLicense= $options["maxLicense"];
				$typeorder = $options["typeorder"];
				$activeDay = $options["activeDay"];
				if($typeorder=="old"){
					// add qty license in current periode
					$result = $this->changeLicense($companyId,$newPlan,$maxLicense);
				}else{
					// renew only
					$this->renewLicense($companyId,$activeDay,$maxLicense);	
				}
				
			}
			
		}
	}

	function changeLicense($companyId,$newFeature,$maxLicense){
		$curl = curl_init();
		$param = array(
			'company_id' => $companyId,
			'feature' => $newFeature, // lite /premium
			'maxLicense' => $maxLicense
		);
		// echo "<pre>";
		// print_r($param);
		// echo "</pre>";
		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://licenses.genesysindonesia.co.id/api/company/changeLicense',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => $param,
		  CURLOPT_HTTPHEADER => array(
		    'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiNjkzNTQwOWYzMWIyNDFlNDIyZjYyMjExOTVlMTlhNTNmZmQ3MzJlYjVhOGYxNzM2ZGRhZTEyNzZhMjU1YWM2NTBmNzkxZWYyMWM0NmYyN2YiLCJpYXQiOjE2NTgzODkyNjAsIm5iZiI6MTY1ODM4OTI2MCwiZXhwIjoxNjg5OTI1MjYwLCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.VgO9GK9tFSuu7hHpabE1DWm3RFkA0NfL-Q92EBRz_h1lZZAJH65Qtq7p1_HlPyt3TYE9FncVFsDjV1LUsAIMzNquJMcQYNQ-pYfj3irFTqp0LCAEOcrc1c7Pm5J2YUHPOGOQGeRB3_SZzUpejHcWICKhXMQMIRK1Ss3hOyXXnv4sH4B7uujUsd0_99q6gz4ufySzmbChBelzl0PrcXbHiRHAiQKfcsfowvrU1pe2mYgTUnkjdKyRXIy-XJ5mL7NQ4Bq3ZPZROPQpS9YT0TLfSRxiocfqutXmYgx4jwjl0jh7fqF7fM8Y4VcKSKRCBL-Pqg-bK-tPzLBK4kOrh-6Ngnp3WJoL0pUKLgbGEeVysU9Ehu__REvJCamdM5aipt6ym1B0hZxfHji4DoP0jHVG8Xxp6nn0vMqUi3gdZp-hY1JawJusqtj3KEeejfLfB-oKlZKqNXmXGNWagDeNLh9HTM2ry4PTGMKAFXVvQcafmE2OEovDQvzuB2npbO3cDPvyf3nMPgzMiDcE79051R8ojFkf82SypYzMcMunhCiBhd-2zmZ7w5NVYuG3bgvd76qBh6lG8hV1aMV5Jz_F14rxIfsKzRz4-y9sO2xtRivXIIstsV5xU6Pr3gjl__3cN-2KRFx0hnMhmYICPxOBApdfOuqj2G5ZJ3kNGUk9RESoMCs',
			'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);
		curl_close($curl);

		$arrResponse = json_decode($response);
		if($arrResponse->result==true){
			$this->CI->load->model("subscription_model");
			switch ($newFeature) {
				case 'lite':
					$intraxFeatureCode = 1;
					break;
				case 'premium':
					break;
					$intraxFeatureCode = 2;
			}
			$dataUpdate = [
				"intrax_plan_code" => $intraxFeatureCode
			];
			// ubah plan data subscription
			$updateSubscription= $this->CI->subscription_model->changeIntraxPlan($companyId,$intraxFeatureCode);
			return $arrResponse;
		}
	}

	function renewLicense($companyId,$activeDay,$maxLicense){
		$curl  = curl_init();
		$param = array(
			'company_id' => $companyId,
			'active_day' => $activeDay,
			'maxLicense' => $maxLicense
		);

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://licenses.genesysindonesia.co.id/api/company/renewalIntraxLicense',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => $param,
		  CURLOPT_HTTPHEADER => array(
		    'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiNjkzNTQwOWYzMWIyNDFlNDIyZjYyMjExOTVlMTlhNTNmZmQ3MzJlYjVhOGYxNzM2ZGRhZTEyNzZhMjU1YWM2NTBmNzkxZWYyMWM0NmYyN2YiLCJpYXQiOjE2NTgzODkyNjAsIm5iZiI6MTY1ODM4OTI2MCwiZXhwIjoxNjg5OTI1MjYwLCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.VgO9GK9tFSuu7hHpabE1DWm3RFkA0NfL-Q92EBRz_h1lZZAJH65Qtq7p1_HlPyt3TYE9FncVFsDjV1LUsAIMzNquJMcQYNQ-pYfj3irFTqp0LCAEOcrc1c7Pm5J2YUHPOGOQGeRB3_SZzUpejHcWICKhXMQMIRK1Ss3hOyXXnv4sH4B7uujUsd0_99q6gz4ufySzmbChBelzl0PrcXbHiRHAiQKfcsfowvrU1pe2mYgTUnkjdKyRXIy-XJ5mL7NQ4Bq3ZPZROPQpS9YT0TLfSRxiocfqutXmYgx4jwjl0jh7fqF7fM8Y4VcKSKRCBL-Pqg-bK-tPzLBK4kOrh-6Ngnp3WJoL0pUKLgbGEeVysU9Ehu__REvJCamdM5aipt6ym1B0hZxfHji4DoP0jHVG8Xxp6nn0vMqUi3gdZp-hY1JawJusqtj3KEeejfLfB-oKlZKqNXmXGNWagDeNLh9HTM2ry4PTGMKAFXVvQcafmE2OEovDQvzuB2npbO3cDPvyf3nMPgzMiDcE79051R8ojFkf82SypYzMcMunhCiBhd-2zmZ7w5NVYuG3bgvd76qBh6lG8hV1aMV5Jz_F14rxIfsKzRz4-y9sO2xtRivXIIstsV5xU6Pr3gjl__3cN-2KRFx0hnMhmYICPxOBApdfOuqj2G5ZJ3kNGUk9RESoMCs',
			'Content-Type: application/json'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		$arrResponse = json_decode($response);
		return $arrResponse;
	}
}