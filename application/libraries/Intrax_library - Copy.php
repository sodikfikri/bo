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
		    'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiY2U3NjZkZmM5ZWZiMjRmZjlmY2M1Njk1Y2ZkNTBkYTFhMmZhZTBiOTY1YzVlNGU2NjhhY2FmZGE5MDRjYjQ3ZmJiOGFhZmY3NmU4ZjQzZjAiLCJpYXQiOjE2MjY4NDk4MTEsIm5iZiI6MTYyNjg0OTgxMSwiZXhwIjoxNjU4Mzg1ODExLCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.NjfS5VuzX2edr10S72H4PgRcOPtQ1EFqVHLpBz5B-t4c39X1W6d-hw5Y-n0PtXAfjV-Fp3m6NrF31b_HHEESOBOijsU0Qp7prbeS1iR80xbR3Si9SXYgKXx-V-g2t_7TMZcUPezGM4weUEhv-vB3yZuO0_75ShtIP5QSPsgbXawu3gbM_z6hJ6y_k1VsVosn7raAGszsba_VY6rhjfsBY1W31x31526nVD19L947EFD48GreU3FVeJzjIlGhZJXE5rIlTXS3bUHwFBeDu4eI_jWYu2dF8lvLkCIJWsKCKRU7A1i6BTqZTNDIaUkNv8DDCWCJy6Nctb5KhJNV37aoXnJu4x3moIsGStWJmwwmUkOYIBKZ6IUm9uQMR0vWYJiAO3v3FeA2yyo5kdChKTuScTyA9cDTA59Cqv5gqeTkuGwSPTCSWocVw9FUjjVywv4j7PTVJnuz8TBI1a-Jhyyx3-azEyqwQscpVUFAPBsxlc-bBQlkh8CXkHkaf0Df5PXN6w329oRgRQm1gSVTrWCluJwXNV_5pBe5nhL4Ng8x8BvaDG32RHDjsIkXr37oLGj7tZGxBtlne045sEXWEkqSceGMKqGm_S8_hvchU-VO8dvVBQ7Klie2RALIjHwBH_8c3Q0EII1qT7lZkphln1qCRzhXjbE__favOqUE8N3FubQ'
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
		    'Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiY2U3NjZkZmM5ZWZiMjRmZjlmY2M1Njk1Y2ZkNTBkYTFhMmZhZTBiOTY1YzVlNGU2NjhhY2FmZGE5MDRjYjQ3ZmJiOGFhZmY3NmU4ZjQzZjAiLCJpYXQiOjE2MjY4NDk4MTEsIm5iZiI6MTYyNjg0OTgxMSwiZXhwIjoxNjU4Mzg1ODExLCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.NjfS5VuzX2edr10S72H4PgRcOPtQ1EFqVHLpBz5B-t4c39X1W6d-hw5Y-n0PtXAfjV-Fp3m6NrF31b_HHEESOBOijsU0Qp7prbeS1iR80xbR3Si9SXYgKXx-V-g2t_7TMZcUPezGM4weUEhv-vB3yZuO0_75ShtIP5QSPsgbXawu3gbM_z6hJ6y_k1VsVosn7raAGszsba_VY6rhjfsBY1W31x31526nVD19L947EFD48GreU3FVeJzjIlGhZJXE5rIlTXS3bUHwFBeDu4eI_jWYu2dF8lvLkCIJWsKCKRU7A1i6BTqZTNDIaUkNv8DDCWCJy6Nctb5KhJNV37aoXnJu4x3moIsGStWJmwwmUkOYIBKZ6IUm9uQMR0vWYJiAO3v3FeA2yyo5kdChKTuScTyA9cDTA59Cqv5gqeTkuGwSPTCSWocVw9FUjjVywv4j7PTVJnuz8TBI1a-Jhyyx3-azEyqwQscpVUFAPBsxlc-bBQlkh8CXkHkaf0Df5PXN6w329oRgRQm1gSVTrWCluJwXNV_5pBe5nhL4Ng8x8BvaDG32RHDjsIkXr37oLGj7tZGxBtlne045sEXWEkqSceGMKqGm_S8_hvchU-VO8dvVBQ7Klie2RALIjHwBH_8c3Q0EII1qT7lZkphln1qCRzhXjbE__favOqUE8N3FubQ'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		$arrResponse = json_decode($response);
		return $arrResponse;
	}
}