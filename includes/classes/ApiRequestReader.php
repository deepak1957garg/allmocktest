<?php
include_once dirname(__FILE__) . '/ParamsValidator.php';
// include_once dirname(__FILE__) . '/../user/UserInfoManager.php';
// include_once dirname(__FILE__) . '/../user/UserModuleManager.php';

class ApiRequestReader{
	private $paramsValidator;
	private $error = "";
	private $postData = array();
	private $postParams = array();
	private $params = array();
	private $validationStatus = false;
	private $by_access_token = false;
	private $module=1;

	function __construct($by_access_token = false){
		$this->paramsValidator = new ParamsValidator();
		$this->by_access_token = $by_access_token;
	}

	private function validateApi(){
		$this->validationStatus = true;
	}

	private function readPostData(){
		$entityBody = file_get_contents('php://input');
		$this->postData = json_decode($entityBody,TRUE);
	}

	private function readPostParams(){
		$this->postParams = $_POST;
	}

	private function validateAndReadPostData(){
		$this->validateApi();
		$this->readPostData();
		$this->readPostParams();
	}

	public function setModule(){
		$this->module = $module;
	}

	public function checkMwHeaderAuthorization(){
		$error = '';
		$token = $this->getBearerToken();
		if($token!='a03c19213a7b618f77c7f0e17b9e85b56f666b9e'){
			$error = "Authorization Failed";
		}
		return $error;
	}

	private function getAuthorizationHeader(){
        $headers = null;
        if (isset($_SERVER['Authorization'])) {
            $headers = trim($_SERVER["Authorization"]);
        }
        else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
            $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
            $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
            //print_r($requestHeaders);
            if (isset($requestHeaders['Authorization'])) {
                $headers = trim($requestHeaders['Authorization']);
            }
        }
        return $headers;
    }
	/**
	 * get access token from header
	 * */
	private function getBearerToken() {
	    $headers = $this->getAuthorizationHeader();
	    // HEADER: Get the access token from the header
	    if (!empty($headers)) {
	        if (preg_match('/Bearer\s(\S+)/', $headers, $matches)) {
	            return $matches[1];
	        }
	    }
	    return null;
	}

	public function sendOtp(){
		$this->validateAndReadPostData();

		$this->params['cc'] = isset($this->postData['cc']) ? $this->paramsValidator->validateCC($this->postData['cc']) : (isset($this->postParams['cc']) ? $this->paramsValidator->validateCC($this->postParams['cc']) : "");
		$this->params['mobile'] = isset($this->postData['mobile']) ? $this->paramsValidator->validateMobile($this->postData['mobile']) : (isset($this->postParams['mobile']) ? $this->paramsValidator->validateMobile($this->postParams['mobile']) : "");
		$this->params['email'] = isset($this->postData['email']) ? $this->paramsValidator->validateEmail($this->postData['email']) : (isset($postParams['email']) ? $this->paramsValidator->validateEmail($postParams['email']) : "");
		if(($this->params['cc']!=91 || $this->params['mobile']=="") && $this->params['email']==""){
			$this->error = "Either Mobile no or country code or email is not valid";
		}
		return $this->params;
	}

	public function verifyOtp(){
		$this->validateAndReadPostData();

		$this->params['cc'] = isset($this->postData['cc']) ? $this->paramsValidator->validateCC($this->postData['cc']) : (isset($this->postParams['cc']) ? $this->paramsValidator->validateCC($this->postParams['cc']) : "");
		$this->params['mobile'] = isset($this->postData['mobile']) ? $this->paramsValidator->validateMobile($this->postData['mobile']) : (isset($this->postParams['mobile']) ? $this->paramsValidator->validateMobile($this->postParams['mobile']) : "");
		$this->params['otp'] = isset($this->postData['otp']) ? $this->paramsValidator->validateOtp($this->postData['otp']) : (isset($this->postParams['otp']) ? $this->paramsValidator->validateOtp($this->postParams['otp']) : "");
		$this->params['email'] = isset($this->postData['email']) ? $this->paramsValidator->validateEmail($this->postData['email']) : (isset($postParams['email']) ? $this->paramsValidator->validateEmail($postParams['email']) : "");
		$this->params['username'] = isset($this->postData['username']) ? $this->postData['username'] : (isset($this->postParams['username']) ? $this->postParams['username'] : "");

		if(($this->params['cc']!=91 || $this->params['mobile']=="") && $this->params['email']==""){
			$this->error = "Either Mobile no or country code or email is not valid";
		}
		else if($this->params['otp']==""){
			$this->error = "Otp did not match";;
		}
		return $this->params;
	}

	public function updateUser(){
		$this->validateAndReadPostData();

		$this->params['cc'] = isset($this->postData['cc']) ? $this->paramsValidator->validateCC($this->postData['cc']) : (isset($this->postParams['cc']) ? $this->paramsValidator->validateCC($this->postParams['cc']) : "");
		$this->params['mobile'] = isset($this->postData['mobile']) ? $this->paramsValidator->validateMobile($this->postData['mobile']) : (isset($this->postParams['mobile']) ? $this->paramsValidator->validateMobile($this->postParams['mobile']) : "");
		$this->params['uid'] = isset($this->postData['uid']) ? $this->paramsValidator->validateUid($this->postData['uid']) : (isset($this->postParams['uid']) ? $this->paramsValidator->validateUid($this->postParams['uid']) : 0);
		$this->params['name'] = isset($this->postData['name']) ? $this->postData['name'] : (isset($this->postParams['name']) ? $this->postParams['name'] : "");
		$this->params['email'] = isset($this->postData['email']) ? $this->paramsValidator->validateEmail($this->postData['email']) : (isset($this->postParams['email']) ? $this->paramsValidator->validateEmail($this->postParams['email']) : "");
		$this->params['remove_pic'] = isset($this->postData['remove_pic']) ? $this->postData['remove_pic'] : (isset($this->postParams['remove_pic']) ? $this->postParams['remove_pic'] : false);
		$this->params['new_password'] = isset($this->postData['password']) ? $this->postData['password'] : (isset($this->postParams['password']) ? $this->postParams['password'] : '');
		// $this->params['pic'] = isset($this->postData['uid']) ? $this->paramsValidator->validateEmail($this->postData['uid']) : (isset($this->postParams['uid']) ? $this->paramsValidator->validateEmail($this->postParams['uid']) : "");

		if(!($this->params['cc']==91 && $this->params['mobile']!="") && $this->params['uid']==0){
			if($this->params['uid']==0)	$this->error = "Invalid User";
			else $this->error = "Either Mobile no or country code is not valid";
		}
		return $this->params;
	}

	public function updateUserDetails(){
		$this->validateAndReadPostData();
		$postParams = array();
		if($this->postParams['data'])	$this->postParams = json_decode($this->postParams['data'],true);

		$this->params['cc'] = isset($this->postData['cc']) ? $this->paramsValidator->validateCC($this->postData['cc']) : (isset($this->postParams['cc']) ? $this->paramsValidator->validateCC($this->postParams['cc']) : "");
		$this->params['mobile'] = isset($this->postData['mobile']) ? $this->paramsValidator->validateMobile($this->postData['mobile']) : (isset($this->postParams['mobile']) ? $this->paramsValidator->validateMobile($this->postParams['mobile']) : "");
		$this->params['uid'] = isset($this->postData['uid']) ? $this->paramsValidator->validateUid($this->postData['uid']) : (isset($this->postParams['uid']) ? $this->paramsValidator->validateUid($this->postParams['uid']) : 0);
		$this->params['name'] = isset($this->postData['name']) ? $this->postData['name'] : (isset($this->postParams['name']) ? $this->postParams['name'] : "");
		$this->params['email'] = isset($this->postData['email']) ? $this->paramsValidator->validateEmail($this->postData['email']) : (isset($this->postParams['email']) ? $this->paramsValidator->validateEmail($this->postParams['email']) : "");
		$this->params['remove_pic'] = isset($this->postData['remove_pic']) ? $this->postData['remove_pic'] : (isset($this->postParams['remove_pic']) ? $this->postParams['remove_pic'] : false);
		$this->params['new_password'] = isset($this->postData['password']) ? $this->postData['password'] : (isset($this->postParams['password']) ? $this->postParams['password'] : '');
		// $this->params['pic'] = isset($this->postData['uid']) ? $this->paramsValidator->validateEmail($this->postData['uid']) : (isset($this->postParams['uid']) ? $this->paramsValidator->validateEmail($this->postParams['uid']) : "");

		// if(!($this->params['cc']==91 && $this->params['mobile']!="") && $this->params['uid']==0){
		// 	if($this->params['uid']==0)	$this->error = "Invalid User";
		// 	else $this->error = "Either Mobile no or country code is not valid";
		// }
		return $this->params;
	}

	public function saveMedicalRecords(){
		$this->validateAndReadPostData();
		$postParams = array();
		if($this->postParams['data'])	$postParams = json_decode($this->postParams['data'],true);

		$this->params['report_date'] = isset($this->postData['report_date']) ? $this->postData['report_date'] : (isset($postParams['report_date']) ? $postParams['report_date'] : date("Y-m-d H:i:s"));
		$this->params['report_type'] = isset($this->postData['report_type']) ? $this->postData['report_type'] : (isset($postParams['report_type']) ? $postParams['report_type'] : "");
		$this->params['comment'] = isset($this->postData['comment']) ? $this->postData['comment'] : (isset($postParams['comment']) ? $postParams['comment'] : '');
		$this->params['id'] = isset($this->postData['id']) ? $this->paramsValidator->validateIsNumber($this->postData['id']) : (isset($postParams['id']) ? $this->paramsValidator->validateIsNumber($postParams['id']) : 0);
		$this->params['uid'] = isset($this->postData['uid']) ? $this->paramsValidator->validateUid($this->postData['uid']) : (isset($postParams['uid']) ? $this->paramsValidator->validateUid($postParams['uid']) : 0);
		return $this->params;
	}

	public function deleteMedicalRecords(){
		$this->validateAndReadPostData();

		$this->params['uid'] = isset($this->postData['uid']) ? $this->paramsValidator->validateUid($this->postData['uid']) : (isset($this->postParams['uid']) ? $this->paramsValidator->validateUid($this->postParams['uid']) : 0);
		$this->params['id'] = isset($this->postData['id']) ? $this->paramsValidator->validateIsNumber($this->postData['id']) : (isset($this->postParams['id']) ? $this->paramsValidator->validateIsNumber($this->postParams['id']) : '');
		return $this->params;
	}			

	public function generalApi(){
		$this->validateAndReadPostData();
		$uinfomanager = new UserInfoManager();
		$ummanager = new UserModuleManager();

		$token = '';
		if($this->by_access_token){
			$token = $this->getBearerToken();
			if($token=='')	$error = "Authorization Failed";
			else{
				$user = $uinfomanager->getUserByAccesstoken($token);
				$this->params['uid'] = $user->getValue('uid');
				if($user->getValue('uid')!=0){
					$this->params['user'] = $user;
				}
				else{
					$this->error = "Invalid User";
				}
			}
		}
		else{
			$this->params['uid'] = isset($this->postData['uid']) ? $this->paramsValidator->validateUid($this->postData['uid']) : (isset($this->postParams['uid']) ? $this->paramsValidator->validateUid($this->postParams['uid']) : 0);

			if($this->params['uid']==0){
				$this->error = "Invalid User";
			}
			else{
				$user = $uinfomanager->getUser($this->params['uid']);
				$this->params['uid'] = $user->getValue('uid');
				if($user->getValue('uid')!=0){
					$this->params['user'] = $user;
					// $this->user_module = $ummanager->getUserModule($this->params['uid'],$this->module);
					// if($this->user_module->getValue('uid')!=0 && $this->user_module->getValue('isactive')==1){

					// }
					// else{
					// 	$this->error = "Invalid User";
					// }
				}
				else{
					$this->error = "Invalid User";
				}
			}

			// if($this->error!=''){
			// 	header("403 Un")
			// }	

		}
		return $this->params;
	}

	public function provider_slots(){
		$this->validateAndReadPostData();

		$this->params['puid'] = isset($this->postData['puid']) ? $this->paramsValidator->validateIsNumber($this->postData['puid']) : (isset($this->postParams['puid']) ? $this->paramsValidator->validateIsNumber($this->postParams['puid']) : 0);
		$this->params['is_reshedule'] = isset($this->postData['is_reshedule']) ? $this->postData['is_reshedule'] : (isset($this->postParams['is_reshedule']) ? $this->postParams['is_reshedule'] : false);
		$this->params['module'] = isset($this->postData['module']) ? $this->postData['module'] : (isset($this->postParams['module']) ? $this->postParams['module'] : false);

		if($this->params['puid']==0){
			$this->error = "Provider missing";
		}
		return $this->params;
	}

	public function createAppointment(){
		$this->generalApi();

		$this->params['puid'] = isset($this->postData['puid']) ? $this->paramsValidator->validateIsNumber($this->postData['puid']) : (isset($this->postParams['puid']) ? $this->paramsValidator->validateIsNumber($this->postParams['puid']) : 0);
		$this->params['sel_date'] = isset($this->postData['sel_date']) ? $this->paramsValidator->validateDate($this->postData['sel_date']) : (isset($this->postParams['sel_date']) ? $this->paramsValidator->validateDate($this->postParams['sel_date']) : 0);
		$this->params['slot'] = isset($this->postData['slot']) ? $this->paramsValidator->validateDate($this->postData['slot']) : (isset($this->postParams['slot']) ? $this->paramsValidator->validateDate($this->postParams['slot']) : 0);
		$this->params['mode'] = isset($this->postData['mode']) ? $this->postData['mode'] : (isset($this->postParams['mode']) ? $this->postParams['mode'] : "");
		$this->params['duration'] = isset($this->postData['slot_duration']) ? $this->postData['slot_duration'] : (isset($this->postParams['slot_duration']) ? $this->postParams['slot_duration'] : 30);

		if($this->params['puid']==0){
			$this->error = "Provider missing";
		}
		else if($this->params['sel_date']==""){
			$this->error = "Date missing";
		}
		else if($this->params['slot']==""){
			$this->error = "Slot missing";
		}
		return $this->params;
	}

	public function confirmAppointment(){
		$this->generalApi();

		$this->params['puid'] = isset($this->postData['puid']) ? $this->paramsValidator->validateIsNumber($this->postData['puid']) : (isset($this->postParams['puid']) ? $this->paramsValidator->validateIsNumber($this->postParams['puid']) : 0);
		$this->params['sel_date'] = isset($this->postData['sel_date']) ? $this->paramsValidator->validateDate($this->postData['sel_date']) : (isset($this->postParams['sel_date']) ? $this->paramsValidator->validateDate($this->postParams['sel_date']) : 0);
		$this->params['slot'] = isset($this->postData['slot']) ? $this->paramsValidator->validateDate($this->postData['slot']) : (isset($this->postParams['slot']) ? $this->paramsValidator->validateDate($this->postParams['slot']) : 0);
		$this->params['payment_response'] = isset($this->postData['payment_response']) ? $this->postData['payment_response'] : (isset($this->postParams['payment_response']) ? $this->postParams['slot'] : array());
		$this->params['duration'] = isset($this->postData['slot_duration']) ? $this->postData['slot_duration'] : (isset($this->postParams['slot_duration']) ? $this->postParams['slot_duration'] : 30);

		if($this->params['puid']==0){
			$this->error = "Provider missing";
		}
		else if($this->params['sel_date']==""){
			$this->error = "Date missing";
		}
		else if($this->params['slot']==""){
			$this->error = "Slot missing";
		}
		return $this->params;
	}

	public function subscribeProgram(){
		$this->generalApi();

		$this->params['pid'] = isset($this->postData['pid']) ? $this->paramsValidator->validateIsNumber($this->postData['pid']) : (isset($this->postParams['pid']) ? $this->paramsValidator->validateIsNumber($this->postParams['pid']) : 0);
		$this->params['payment_response'] = isset($this->postData['payment_response']) ? $this->postData['payment_response'] : (isset($this->postParams['payment_response']) ? $this->postParams['slot'] : array());

		if($this->params['pid']==0){
			$this->error = "Package missing";
		}
		return $this->params;
	}	

	public function resheduleAppointment(){
		$this->createAppointment();

		$this->params['aid'] = isset($this->postData['aid']) ? $this->paramsValidator->validateIsNumber($this->postData['aid']) : (isset($this->postParams['aid']) ? $this->paramsValidator->validateIsNumber($this->postParams['aid']) : 0);

		if($this->params['aid']==0){
			$this->error = "appointment missing";
		}
		return $this->params;
	}

	public function addDiabetesLog(){
		$this->generalApi();

		$this->params['measure_date'] = isset($this->postData['measure_date']) ? $this->paramsValidator->validateDate($this->postData['measure_date']) : (isset($this->postParams['measure_date']) ? $this->paramsValidator->validateDate($this->postParams['measure_date']) : 0);
		$this->params['period'] = isset($this->postData['period']) ? $this->postData['period'] : (isset($this->postParams['period']) ? $this->postParams['period'] : '');
		$this->params['log_value'] = isset($this->postData['log_value']) ? $this->paramsValidator->validateIsNumber($this->postData['log_value']) : (isset($this->postParams['log_value']) ? $this->paramsValidator->validateIsNumber($this->postParams['log_value']) : 0);
		$this->params['lid'] = isset($this->postData['lid']) ? $this->paramsValidator->validateIsNumber($this->postData['lid']) : (isset($this->postParams['lid']) ? $this->paramsValidator->validateIsNumber($this->postParams['lid']) : 0);

		if($this->params['measure_date']==""){
			$this->error = "Date missing";
		}
		else if($this->params['period']==""){
			$this->error = "Period missing";
		}
		else if($this->params['log_value']==0){
			$this->error = "Value missing";
		}
		return $this->params;
	}

	public function removeDiabetesLog(){
		$this->generalApi();

		$this->params['lid'] = isset($this->postData['lid']) ? $this->paramsValidator->validateIsNumber($this->postData['lid']) : (isset($this->postParams['lid']) ? $this->paramsValidator->validateIsNumber($this->postParams['lid']) : 0);

		if($this->params['lid']==""){
			$this->error = "log id missing";
		}
		return $this->params;
	}

	public function payOrder(){
		$this->generalApi();

		$this->params['amount'] = isset($this->postData['amount']) ? $this->paramsValidator->validateIsNumber($this->postData['amount']) : (isset($this->postParams['amount']) ? $this->paramsValidator->validateIsNumber($this->postParams['amount']) : 0);
		$this->params['puid'] = isset($this->postData['puid']) ? $this->paramsValidator->validateIsNumber($this->postData['puid']) : (isset($this->postParams['puid']) ? $this->paramsValidator->validateIsNumber($this->postParams['puid']) : 0);
		$this->params['pid'] = isset($this->postData['pid']) ? $this->paramsValidator->validateIsNumber($this->postData['pid']) : (isset($this->postParams['pid']) ? $this->paramsValidator->validateIsNumber($this->postParams['pid']) : 0);
		$this->params['mode'] = isset($this->postData['mode']) ? $this->paramsValidator->validatePaymentMode($this->postData['mode']) : (isset($this->postParams['mode']) ? $this->paramsValidator->validatePaymentMode($this->postParams['mode']) : "");
		$this->params['type'] = isset($this->postData['type']) ? $this->paramsValidator->validatePaymentFor($this->postData['type']) : (isset($this->postParams['type']) ? $this->paramsValidator->validatePaymentFor($this->postParams['type']) : "");


		if($this->params['amount']==""){
			$this->error = "Amount missing";
		}
		else if($this->params['puid']==0 && $this->params['pid']==0){
			$this->error = "provider or package is missing";
		}
		else if($this->params['type']==""){
			$this->error = "type missing";
		}
		return $this->params;
	}

	public function payFailure(){
		$this->generalApi();

		$this->params['amount'] = isset($this->postData['amount']) ? $this->paramsValidator->validateIsNumber($this->postData['amount']) : (isset($this->postParams['amount']) ? $this->paramsValidator->validateIsNumber($this->postParams['amount']) : 0);
		$this->params['puid'] = isset($this->postData['puid']) ? $this->paramsValidator->validateIsNumber($this->postData['puid']) : (isset($this->postParams['puid']) ? $this->paramsValidator->validateIsNumber($this->postParams['puid']) : 0);
		$this->params['pid'] = isset($this->postData['pid']) ? $this->paramsValidator->validateIsNumber($this->postData['pid']) : (isset($this->postParams['pid']) ? $this->paramsValidator->validateIsNumber($this->postParams['pid']) : 0);
		$this->params['sel_date'] = isset($this->postData['sel_date']) ? $this->postData['sel_date'] : (isset($this->postParams['sel_date']) ? $this->postParams['sel_date'] : "");
		$this->params['slot'] = isset($this->postData['slot']) ? $this->postData['slot'] : (isset($this->postParams['slot']) ? $this->postParams['slot'] : "");
		$this->params['type'] = isset($this->postData['type']) ? $this->paramsValidator->validatePaymentFor($this->postData['type']) : (isset($this->postParams['type']) ? $this->paramsValidator->validatePaymentFor($this->postParams['type']) : "");
		$this->params['error'] = isset($this->postData['error']) ? $this->postData['error'] : (isset($this->postParams['error']) ? $this->postParams['error'] : "");
		$this->params['order_id'] = isset($this->postData['order_id']) ? $this->postData['order_id'] : (isset($this->postParams['order_id']) ? $this->postParams['order_id'] : "");
		error_log("payment failure : " . print_r($this->postData,1));


		// if($this->params['amount']==""){
		// 	$this->error = "Amount missing";
		// }
		// else if($this->params['puid']==0 && $this->params['pid']==0){
		// 	$this->error = "provider or package is missing";
		// }
		// else if($this->params['type']==""){
		// 	$this->error = "type missing";
		// }
		return $this->params;
	}

	public function paySuccess(){
		$this->generalApi();

		$this->params['amount'] = isset($this->postData['amount']) ? $this->paramsValidator->validateIsNumber($this->postData['amount']) : (isset($this->postParams['amount']) ? $this->paramsValidator->validateIsNumber($this->postParams['amount']) : 0);
		$this->params['puid'] = isset($this->postData['puid']) ? $this->paramsValidator->validateIsNumber($this->postData['puid']) : (isset($this->postParams['puid']) ? $this->paramsValidator->validateIsNumber($this->postParams['puid']) : 0);
		$this->params['pid'] = isset($this->postData['pid']) ? $this->paramsValidator->validateIsNumber($this->postData['pid']) : (isset($this->postParams['pid']) ? $this->paramsValidator->validateIsNumber($this->postParams['pid']) : 0);
		$this->params['sel_date'] = isset($this->postData['sel_date']) ? $this->postData['sel_date'] : (isset($this->postParams['sel_date']) ? $this->postParams['sel_date'] : "");
		$this->params['slot'] = isset($this->postData['slot']) ? $this->postData['slot'] : (isset($this->postParams['slot']) ? $this->postParams['slot'] : "");
		$this->params['type'] = isset($this->postData['type']) ? $this->paramsValidator->validatePaymentFor($this->postData['type']) : (isset($this->postParams['type']) ? $this->paramsValidator->validatePaymentFor($this->postParams['type']) : "");
		$this->params['payment_info'] = isset($this->postData['payment_response']) ? json_encode($this->postData['payment_response']) : (isset($this->postParams['payment_response']) ? json_encode($this->postParams['payment_response']) : "");
		$this->params['pay_id'] = isset($this->postData['razorPayKey']) ? $this->postData['razorPayKey'] : (isset($this->postParams['razorPayKey']) ? $this->postParams['razorPayKey'] : "");
		if(empty(json_decode($this->params['payment_info'],true)) && $this->params['pay_id']!=""){
			$this->params['payment_info'] = json_encode(array('pay_id'=>$this->params['pay_id']));
		}
		error_log("payment success : " . print_r($this->postData,1));
		error_log("payment success params  : " . print_r($this->postParams,1));

		// if($this->params['amount']==""){
		// 	$this->error = "Amount missing";
		// }
		// else if($this->params['puid']==0 && $this->params['pid']==0){
		// 	$this->error = "provider or package is missing";
		// }
		// else if($this->params['type']==""){
		// 	$this->error = "type missing";
		// }
		return $this->params;
	}

	public function init(){
		$this->validateAndReadPostData();

		$this->params['token'] = isset($this->postData['token']) ? $this->postData['token'] : (isset($this->postParams['token']) ? $this->postParams['token'] : "");
		$this->params['version'] = isset($this->postData['version']) ? $this->postData['version'] : (isset($this->postParams['version']) ? $this->postParams['version'] : "");
		$this->params['platform'] = isset($this->postData['platform']) ? $this->postData['platform'] : (isset($this->postParams['platform']) ? $this->postParams['platform'] : "");
		return $this->params;
	}

	public function getError(){
		return $this->error;
	}

	public function assignCoach(){
		$this->generalApi();

		$this->params['sid'] = isset($this->postData['sid']) ? $this->paramsValidator->validateIsNumber($this->postData['sid']) : (isset($this->postParams['sid']) ? $this->paramsValidator->validateIsNumber($this->postParams['sid']) : 0);
		$this->params['puid'] = isset($this->postData['puid']) ? $this->paramsValidator->validateIsNumber($this->postData['puid']) : (isset($this->postParams['puid']) ? $this->paramsValidator->validateIsNumber($this->postParams['puid']) : 0);
		return $this->params;
	}

	public function updateMember(){
		$this->validateAndReadPostData();
		

		$this->params['cc'] = isset($this->postParams['cc']) ? $this->paramsValidator->validateCC($this->postParams['cc']) : (isset($postData['cc']) ? $this->paramsValidator->validateCC($postData['cc']) : "");
		$this->params['mobile'] = isset($this->postParams['mobile']) ? $this->paramsValidator->validateMobile($this->postParams['mobile']) : (isset($postData['mobile']) ? $this->paramsValidator->validateMobile($postData['mobile']) : "");
		$this->params['uid'] = isset($this->postParams['uid']) ? $this->paramsValidator->validateUid($this->postParams['uid']) : (isset($postData['uid']) ? $this->paramsValidator->validateUid($postData['uid']) : 0);
		$this->params['name'] = isset($this->postParams['name']) ? $this->postParams['name'] : (isset($postData['name']) ? $postData['name'] : "");
		$this->params['email'] = isset($this->postParams['email']) ? $this->paramsValidator->validateEmail($this->postParams['email']) : (isset($postData['email']) ? $this->paramsValidator->validateEmail($postData['email']) : "");
		$this->params['salutation'] = isset($this->postParams['salutation']) ? $this->postParams['salutation'] : (isset($postData['salutation']) ? $postData['salutation'] : "");
		$this->params['dob'] = isset($this->postParams['dob']) ? $this->postParams['dob'] : (isset($postData['dob']) ? $postData['dob'] : "");
		$this->params['age'] = isset($this->postParams['age']) ? $this->paramsValidator->validateIsNumber($this->postParams['age']) : (isset($this->postData['age']) ? $this->paramsValidator->validateIsNumber($this->postData['age']) : 0);
		$this->params['gender'] = isset($this->postParams['gender']) ? $this->postParams['gender'] : (isset($postData['gender']) ? $postData['gender'] : "");
		$this->params['blood_group'] = isset($this->postParams['blood_group']) ? $this->postParams['blood_group'] : (isset($postData['blood_group']) ? $postData['blood_group'] : "");
		$this->params['aadhaar_no'] = isset($this->postParams['aadhaar_no']) ? $this->postParams['aadhaar_no'] : (isset($postData['aadhaar_no']) ? $postData['aadhaar_no'] : "");
		$this->params['occupation'] = isset($this->postParams['occupation']) ? $this->postParams['occupation'] : (isset($postData['occupation']) ? $postData['occupation'] : "");
		$this->params['address'] = isset($this->postParams['address']) ? $this->postParams['address'] : (isset($postData['address']) ? $postData['address'] : "");
		$this->params['pincode'] = isset($this->postParams['pincode']) ? $this->postParams['pincode'] : (isset($postData['pincode']) ? $postData['pincode'] : "");

		// if(!($this->params['cc']==91 && $this->params['mobile']!="") && $this->params['uid']==0){
		// 	if($this->params['uid']==0)	$this->error = "Invalid User";
		// 	else $this->error = "Either Mobile no or country code is not valid";
		// }
		return $this->params;
	}

	public function login(){
		$this->validateAndReadPostData();

		//print_r($_REQUEST)

		$this->params['cc'] = isset($this->postData['cc']) ? $this->paramsValidator->validateCC($this->postData['cc']) : (isset($this->postParams['cc']) ? $this->paramsValidator->validateCC($this->postParams['cc']) : "");
		$this->params['mobile'] = isset($this->postData['mobile']) ? $this->paramsValidator->validateMobile($this->postData['mobile']) : (isset($this->postParams['mobile']) ? $this->paramsValidator->validateMobile($this->postParams['mobile']) : "");
		$this->params['email'] = isset($this->postData['email']) ? $this->paramsValidator->validateEmail($this->postData['email']) : (isset($this->postParams['email']) ? $this->paramsValidator->validateEmail($this->postParams['email']) : "");
		$this->params['username'] = isset($this->postData['username']) ? $this->postData['username'] : (isset($this->postParams['username']) ? $this->postParams['username'] : "");
		$this->params['password'] = isset($this->postData['password']) ? $this->postData['password'] : (isset($this->postParams['password']) ? $this->postParams['password'] : "");

		$username = $this->paramsValidator->validateIsNumber($this->params['username']);
		if($username==0){
			$username = $this->paramsValidator->validateEmail($this->params['username']);
			if($this->params['email']=='') $this->params['email'] = $username;
		}
		else{
			if($this->params['mobile']=='')	$this->params['mobile'] = $username;
		}

		if($this->params['mobile']=="" && $this->params['email']==""){
			$this->error = "Either mobile or email is not valid";
		}
		return $this->params;
	}

	public function resetPassword(){
		$this->validateAndReadPostData();

		$this->params['email'] = isset($this->postData['email']) ? $this->paramsValidator->validateEmail($this->postData['email']) : (isset($postParams['email']) ? $this->paramsValidator->validateEmail($postParams['email']) : "");
		$this->params['password'] = isset($this->postData['password']) ? $this->postData['password'] : (isset($this->postParams['password']) ? $this->postParams['password'] : "");
		$this->params['new_password'] = isset($this->postData['new_password']) ? $this->postData['new_password'] : (isset($this->postParams['new_password']) ? $this->postParams['new_password'] : "");

		if($this->params['email']==""){
			$this->error = "Email is not valid";
		}
		return $this->params;
	}

	public function verifyNReset(){
		$this->validateAndReadPostData();

		$this->params['email'] = isset($this->postData['email']) ? $this->paramsValidator->validateEmail($this->postData['email']) : (isset($this->postParams['email']) ? $this->paramsValidator->validateEmail($this->postParams['email']) : "");
		$this->params['new_password'] = isset($this->postData['password']) ? $this->postData['password'] : (isset($this->postParams['password']) ? $this->postParams['password'] : "");
		$this->params['otp'] = isset($this->postData['otp']) ? $this->paramsValidator->validateOtp($this->postData['otp']) : (isset($this->postParams['otp']) ? $this->paramsValidator->validateOtp($this->postParams['otp']) : "");

		if($this->params['email']==""){
			$this->error = "Email is not valid";
		}
		return $this->params;
	}

	public function saveEnquiry(){
		$this->validateAndReadPostData();

		$this->params['email'] = isset($this->postData['email']) ? $this->paramsValidator->validateEmail($this->postData['email']) : (isset($this->postParams['email']) ? $this->paramsValidator->validateEmail($this->postParams['email']) : "");
		$this->params['mobile'] = isset($this->postData['mobile']) ? $this->paramsValidator->validateIsNumber($this->postData['mobile']) : (isset($this->postParams['mobile']) ? $this->paramsValidator->validateIsNumber($this->postParams['mobile']) : "");
		$this->params['name'] = isset($this->postData['name']) ? $this->postData['name'] : (isset($this->postParams['name']) ? $this->postParams['name'] : "");
		$this->params['cc'] = isset($this->postData['cc']) ? $this->postData['cc'] : (isset($this->postParams['cc']) ? $this->postParams['cc'] : "");
		$this->params['consent'] = isset($this->postData['consent']) ? $this->paramsValidator->validateIsNumber($this->postData['consent']) : (isset($this->postParams['consent']) ? $this->paramsValidator->validateIsNumber($this->postParams['consent']) : 0);
		$this->params['referrer'] = isset($this->postData['referrer']) ? urldecode($this->postData['referrer']) : (isset($this->postParams['referrer']) ? urldecode($this->postParams['referrer']) : "");

		if($this->params['email']=="" || $this->params['mobile']=="" || $this->params['name']==""){
			$this->error = "Insufficient Info";
		}
		return $this->params;
	}

	public function createDiaryPage(){
		$this->generalApi();

		$this->params['aid'] = isset($this->postData['aid']) ? $this->paramsValidator->validateIsNumber($this->postData['aid']) : (isset($this->postParams['aid']) ? $this->paramsValidator->validateIsNumber($this->postParams['aid']) : 0);

		$this->params['prid'] = isset($this->postData['prid']) ? $this->paramsValidator->validateIsNumber($this->postData['prid']) : (isset($this->postParams['prid']) ? $this->paramsValidator->validateIsNumber($this->postParams['prid']) : 0);
		$this->params['tid'] = isset($this->postData['tid']) ? $this->paramsValidator->validateIsNumber($this->postData['tid']) : (isset($this->postParams['tid']) ? $this->paramsValidator->validateIsNumber($this->postParams['tid']) : 0);
		$this->params['aid'] = isset($this->postData['aid']) ? $this->paramsValidator->validateIsNumber($this->postData['aid']) : (isset($this->postParams['aid']) ? $this->paramsValidator->validateIsNumber($this->postParams['aid']) : 0);
		$this->params['title'] = isset($this->postData['title']) ? $this->postData['title'] : (isset($this->postParams['title']) ? $this->postParams['title'] : '');
		$this->params['body'] = isset($this->postData['body']) ? $this->postData['body'] : (isset($this->postParams['body']) ? $this->postParams['body'] : '');
		return $this->params;
	}					

}
