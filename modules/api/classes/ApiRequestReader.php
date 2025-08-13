<?php
include_once dirname(__FILE__) . '/ParamsValidator.php';
//include_once dirname(__FILE__) . '/../../user/classes/UserInfoManager.php';

class ApiRequestReader{
	protected $paramsValidator;
	protected $error = "";
	protected $postData = array();
	protected $postParams = array();
	protected $params = array();
	protected $validationStatus = false;

	function __construct(){
		$this->paramsValidator = new ParamsValidator();
	}

	protected function validateApi(){
		$this->validationStatus = true;
	}

	protected function readPostData(){
		// $entityBody = file_get_contents('php://input');
		// $this->postData = json_decode($entityBody,TRUE);

		$entityBody = file_get_contents('php://input');
		$data = json_decode($entityBody,TRUE);

		$this->postData = isset($_REQUEST['data']) ? json_decode($_REQUEST['data'],true) : $data;
		error_log(print_r($this->postData,1));

	}

	protected function readPostParams(){
		$this->postParams = $_POST;
		error_log(print_r($this->postParams,1));
	}

	protected function validateAndReadPostData(){
		$this->validateApi();
		$this->readPostData();
		$this->readPostParams();
	}

	// public function sendOtp(){
	// 	$this->validateAndReadPostData();

	// 	$this->params['cc'] = isset($this->postData['cc']) ? $this->paramsValidator->validateCC($this->postData['cc']) : (isset($this->postParams['cc']) ? $this->paramsValidator->validateCC($this->postParams['cc']) : "");
	// 	$this->params['mobile'] = isset($this->postData['mobile']) ? $this->paramsValidator->validateMobile($this->postData['mobile']) : (isset($this->postParams['mobile']) ? $this->paramsValidator->validateMobile($this->postParams['mobile']) : "");
	// 	if($this->params['cc']!=91 || $this->params['mobile']==""){
	// 		$this->error = "Either Mobile no or country code is not valid";
	// 	}
	// 	return $this->params;
	// }

	// public function verifyOtp(){
	// 	$this->validateAndReadPostData();

	// 	$this->params['cc'] = isset($this->postData['cc']) ? $this->paramsValidator->validateCC($this->postData['cc']) : (isset($this->postParams['cc']) ? $this->paramsValidator->validateCC($this->postParams['cc']) : "");
	// 	$this->params['mobile'] = isset($this->postData['mobile']) ? $this->paramsValidator->validateMobile($this->postData['mobile']) : (isset($this->postParams['mobile']) ? $this->paramsValidator->validateMobile($this->postParams['mobile']) : "");
	// 	$this->params['otp'] = isset($this->postData['otp']) ? $this->paramsValidator->validateOtp($this->postData['otp']) : (isset($this->postParams['otp']) ? $this->paramsValidator->validateOtp($this->postParams['otp']) : "");

	// 	if($this->params['cc']!=91 || $this->params['mobile']==""){
	// 		$this->error = "Either Mobile no or country code is not valid";
	// 	}
	// 	else if($this->params['otp']==""){
	// 		$this->error = "Otp did not match";;
	// 	}
	// 	return $this->params;
	// }

	// public function updateUser(){
	// 	$this->validateAndReadPostData();

	// 	$this->params['cc'] = isset($this->postData['cc']) ? $this->paramsValidator->validateCC($this->postData['cc']) : (isset($this->postParams['cc']) ? $this->paramsValidator->validateCC($this->postParams['cc']) : "");
	// 	$this->params['mobile'] = isset($this->postData['mobile']) ? $this->paramsValidator->validateMobile($this->postData['mobile']) : (isset($this->postParams['mobile']) ? $this->paramsValidator->validateMobile($this->postParams['mobile']) : "");
	// 	$this->params['uid'] = isset($this->postData['uid']) ? $this->paramsValidator->validateUid($this->postData['uid']) : (isset($this->postParams['uid']) ? $this->paramsValidator->validateUid($this->postParams['uid']) : 0);
	// 	$this->params['name'] = isset($this->postData['name']) ? $this->postData['name'] : (isset($this->postParams['name']) ? $this->postParams['name'] : "");
	// 	$this->params['email'] = isset($this->postData['email']) ? $this->paramsValidator->validateEmail($this->postData['email']) : (isset($this->postParams['email']) ? $this->paramsValidator->validateEmail($this->postParams['email']) : "");
	// 	$this->params['pic'] = isset($this->postData['pic']) ? $this->postData['pic'] : (isset($this->postParams['pic']) ? $this->postParams['pic'] : "");
	// 	$this->params['bio'] = isset($this->postData['bio']) ? $this->postData['bio'] : (isset($this->postParams['bio']) ? $this->postParams['bio'] : "");
	// 	$this->params['upi'] = isset($this->postData['upi']) ? $this->postData['upi'] : (isset($this->postParams['upi']) ? $this->postParams['upi'] : "");
	// 	// $this->params['pic'] = isset($this->postData['uid']) ? $this->paramsValidator->validateEmail($this->postData['uid']) : (isset($this->postParams['uid']) ? $this->paramsValidator->validateEmail($this->postParams['uid']) : "");

	// 	if(!($this->params['cc']==91 && $this->params['mobile']!="") && $this->params['uid']==0){
	// 		if($this->params['uid']==0)	$this->error = "Invalid User";
	// 		else $this->error = "Either Mobile no or country code is not valid";
	// 	}
	// 	return $this->params;
	// }

	// public function updateUserDetails(){
	// 	$this->validateAndReadPostData();
	// 	$postParams = array();
	// 	if($this->postParams['data'])	$postParams = json_decode($this->postParams['data'],true);

	// 	$this->params['cc'] = isset($this->postData['cc']) ? $this->paramsValidator->validateCC($this->postData['cc']) : (isset($postParams['cc']) ? $this->paramsValidator->validateCC($postParams['cc']) : "");
	// 	$this->params['mobile'] = isset($this->postData['mobile']) ? $this->paramsValidator->validateMobile($this->postData['mobile']) : (isset($postParams['mobile']) ? $this->paramsValidator->validateMobile($postParams['mobile']) : "");
	// 	$this->params['uid'] = isset($this->postData['uid']) ? $this->paramsValidator->validateUid($this->postData['uid']) : (isset($postParams['uid']) ? $this->paramsValidator->validateUid($postParams['uid']) : 0);
	// 	$this->params['name'] = isset($this->postData['name']) ? $this->postData['name'] : (isset($postParams['name']) ? $postParams['name'] : "");
	// 	$this->params['email'] = isset($this->postData['email']) ? $this->paramsValidator->validateEmail($this->postData['email']) : (isset($postParams['email']) ? $this->paramsValidator->validateEmail($postParams['email']) : "");
	// 	// $this->params['pic'] = isset($this->postData['uid']) ? $this->paramsValidator->validateEmail($this->postData['uid']) : (isset($this->postParams['uid']) ? $this->paramsValidator->validateEmail($this->postParams['uid']) : "");

	// 	// if(!($this->params['cc']==91 && $this->params['mobile']!="") && $this->params['uid']==0){
	// 	// 	if($this->params['uid']==0)	$this->error = "Invalid User";
	// 	// 	else $this->error = "Either Mobile no or country code is not valid";
	// 	// }
	// 	return $this->params;
	// }

	public function generalApi(){
		$this->validateAndReadPostData();
		// $uinfomanager = new UserInfoManager();

		// $this->params['uid'] = isset($this->postData['uid']) ? $this->paramsValidator->validateUid($this->postData['uid']) : (isset($this->postParams['uid']) ? $this->paramsValidator->validateUid($this->postParams['uid']) : 0);

		// if($this->params['uid']==0){
		// 	$this->error = "Invalid User";
		// 	//header($_SERVER["SERVER_PROTOCOL"] . " 401 Unauthorized");
		// }
		// else{
		// 	$user = $uinfomanager->getUser($this->params['uid']);
		// 	$this->params['uid'] = $user->getValue('uid');
		// 	if($user->getValue('uid')!=0){
		// 		$this->params['user'] = $user;
		// 	}
		// 	else{
		// 		$this->error = "Invalid User";
		// 	}
		// }
		return $this->params;
	}

	public function getError(){
		return $this->error;
	}

	// public function getPolicy(){
	// 	$this->validateAndReadPostData();

	// 	$this->params['type'] = isset($this->postData['type']) ? $this->paramsValidator->validatePolicyType($this->postData['type']) : (isset($this->postParams['type']) ? $this->paramsValidator->validatePolicyType($this->postParams['type']) : "");
	// 	$this->params['cc'] = isset($this->postData['cc']) ? $this->paramsValidator->validateCC($this->postData['cc']) : (isset($this->postParams['cc']) ? $this->paramsValidator->validateCC($this->postParams['cc']) : "");
	// 	$this->params['mobile'] = isset($this->postData['mobile']) ? $this->paramsValidator->validateMobile($this->postData['mobile']) : (isset($this->postParams['mobile']) ? $this->paramsValidator->validateMobile($this->postParams['mobile']) : "");
	// 	$this->params['uid'] = isset($this->postData['uid']) ? $this->paramsValidator->validateUid($this->postData['uid']) : (isset($this->postParams['uid']) ? $this->paramsValidator->validateUid($this->postParams['uid']) : 0);
	// 	$this->params['qcode'] = isset($this->postData['qcode']) ? $this->postData['qcode'] : (isset($this->postParams['qcode']) ? $this->postParams['qcode'] : "");

	// 	if($this->params['uid']==0){
	// 		$this->error = "User credentials missing";
	// 	}
	// 	return $this->params;
	// }

	// public function sendBorrowRequest(){
	// 	$this->validateAndReadPostData();
	// 	$postParams = array();
	// 	if($this->postParams['data'])	$postParams = json_decode($this->postParams['data'],true);

	// 	$this->params['buid'] = isset($this->postData['buid']) ? $this->paramsValidator->validateUid($this->postData['buid']) : (isset($this->postParams['buid']) ? $this->paramsValidator->validateUid($this->postParams['buid']) : 0);
	// 	$this->params['cc'] = isset($this->postData['cc']) ? $this->paramsValidator->validateCC($this->postData['cc']) : (isset($this->postParams['cc']) ? $this->paramsValidator->validateCC($this->postParams['cc']) : "");
	// 	$this->params['mobile'] = isset($this->postData['mobile']) ? $this->paramsValidator->validateMobile($this->postData['mobile']) : (isset($this->postParams['mobile']) ? $this->paramsValidator->validateMobile($this->postParams['mobile']) : "");
	// 	$this->params['amount'] = isset($this->postData['amount']) ? $this->paramsValidator->validateIsNumber($this->postData['amount']) : (isset($this->postParams['amount']) ? $this->paramsValidator->validateIsNumber($this->postParams['amount']) : 0);
	// 	$this->params['time'] = isset($this->postData['time']) ? $this->paramsValidator->validateIsNumber($this->postData['time']) : (isset($this->postParams['time']) ? $this->paramsValidator->validateIsNumber($this->postParams['time']) : 0);
	// 	$this->params['for_reason'] = isset($this->postData['for_reason']) ? $this->postData['for_reason'] : (isset($this->postParams['for_reason']) ? $this->postParams['for_reason'] : '');
	// 	$this->params['lender_policy'] = isset($this->postData['lender_policy']) ? $this->paramsValidator->validateIsNumber($this->postData['lender_policy']) : (isset($this->postParams['lender_policy']) ? $this->paramsValidator->validateIsNumber($this->postParams['lender_policy']) : 0);

	// 	if($this->params['buid']==0){
	// 		$this->error = "User credentials missing";
	// 	}
	// 	return $this->params;
	// }

	// public function createLoan(){
	// 	$this->validateAndReadPostData();
	// 	$postParams = array();
	// 	if($this->postParams['data'])	$postParams = json_decode($this->postParams['data'],true);

	// 	$this->params['luid'] = isset($this->postData['luid']) ? $this->paramsValidator->validateUid($this->postData['luid']) : (isset($this->postParams['luid']) ? $this->paramsValidator->validateUid($this->postParams['luid']) : 0);
	// 	$this->params['cc'] = isset($this->postData['cc']) ? $this->paramsValidator->validateCC($this->postData['cc']) : (isset($this->postParams['cc']) ? $this->paramsValidator->validateCC($this->postParams['cc']) : "");
	// 	$this->params['mobile'] = isset($this->postData['mobile']) ? $this->paramsValidator->validateMobile($this->postData['mobile']) : (isset($this->postParams['mobile']) ? $this->paramsValidator->validateMobile($this->postParams['mobile']) : "");
	// 	$this->params['amount'] = isset($this->postData['amount']) ? $this->paramsValidator->validateIsNumber($this->postData['amount']) : (isset($this->postParams['amount']) ? $this->paramsValidator->validateIsNumber($this->postParams['amount']) : 0);
	// 	$this->params['period'] = isset($this->postData['period']) ? $this->paramsValidator->validateIsNumber($this->postData['period']) : (isset($this->postParams['period']) ? $this->paramsValidator->validateIsNumber($this->postParams['period']) : 0);
	// 	$this->params['for_reason'] = isset($this->postData['for_reason']) ? $this->postData['for_reason'] : (isset($this->postParams['for_reason']) ? $this->postParams['for_reason'] : '');
	// 	$this->params['pid'] = isset($this->postData['lender_policy']) ? $this->paramsValidator->validateIsNumber($this->postData['lender_policy']) : (isset($this->postParams['lender_policy']) ? $this->paramsValidator->validateIsNumber($this->postParams['lender_policy']) : 0);
	// 	$this->params['interest_free_period'] = isset($this->postData['interest_free_period']) ? $this->postData['interest_free_period'] : (isset($this->postParams['interest_free_period']) ? $this->postParams['interest_free_period'] : '');
	// 	$this->params['interest_rate'] = isset($this->postData['interest_rate']) ? $this->postData['interest_rate'] : (isset($this->postParams['interest_rate']) ? $this->postParams['interest_rate'] : '');
	// 	$this->params['for_reason'] = isset($this->postData['for_reason']) ? $this->postData['for_reason'] : (isset($this->postParams['for_reason']) ? $this->postParams['for_reason'] : '');

	// 	if($this->params['luid']==0){
	// 		$this->error = "User credentials missing";
	// 	}
	// 	return $this->params;
	// }	

	// public function lenderDetails(){
	// 	$this->generalApi();

	// 	$this->params['buid'] = isset($this->postData['buid']) ? $this->paramsValidator->validateUid($this->postData['buid']) : (isset($this->postParams['buid']) ? $this->paramsValidator->validateUid($this->postParams['buid']) : 0);
	// 	// $this->params['luid'] = isset($this->postData['uid']) ? $this->paramsValidator->validateUid($this->postData['luid']) : (isset($this->postParams['luid']) ? $this->paramsValidator->validateUid($this->postParams['luid']) : 0);
	// 	$this->params['cc'] = isset($this->postData['cc']) ? $this->paramsValidator->validateCC($this->postData['cc']) : (isset($this->postParams['cc']) ? $this->paramsValidator->validateCC($this->postParams['cc']) : "");
	// 	$this->params['mobile'] = isset($this->postData['mobile']) ? $this->paramsValidator->validateMobile($this->postData['mobile']) : (isset($this->postParams['mobile']) ? $this->paramsValidator->validateMobile($this->postParams['mobile']) : "");
	// 	if($this->params['uid']==0){
	// 		$this->error = "User credentials missing";
	// 	}
	// 	return $this->params;
	// }

	// public function saveLenderPolicy(){
	// 	$this->generalApi();

	// 	$this->params['uid'] = isset($this->postData['uid']) ? $this->paramsValidator->validateUid($this->postData['uid']) : (isset($this->postParams['uid']) ? $this->paramsValidator->validateUid($this->postParams['uid']) : 0);
	// 	$this->params['interestRate'] = isset($this->postData['interestRate']) ? $this->postData['interestRate'] : (isset($this->postParams['interestRate']) ? $this->postParams['interestRate'] : 0);
	// 	$this->params['interestFreePeriod'] = isset($this->postData['interestFreePeriod']) ? $this->paramsValidator->validateIsNumber($this->postData['interestFreePeriod']) : (isset($this->postParams['interestFreePeriod']) ? $this->paramsValidator->validateIsNumber($this->postParams['interestFreePeriod']) : 0);
	// 	$this->params['duration'] = isset($this->postData['duration']) ? $this->paramsValidator->validateIsNumber($this->postData['duration']) : (isset($this->postParams['duration']) ? $this->paramsValidator->validateIsNumber($this->postParams['duration']) : 0);

	// 	$this->params['lendingLimitBorrower'] = isset($this->postData['lendingLimitBorrower']) ? $this->paramsValidator->validateIsNumber($this->postData['lendingLimitBorrower']) : (isset($this->postParams['lendingLimitBorrower']) ? $this->paramsValidator->validateIsNumber($this->postParams['lendingLimitBorrower']) : 0);
	// 	$this->params['outstandingLimit'] = isset($this->postData['outstandingLimit']) ? $this->paramsValidator->validateIsNumber($this->postData['outstandingLimit']) : (isset($this->postParams['outstandingLimit']) ? $this->paramsValidator->validateIsNumber($this->postParams['outstandingLimit']) : 0);
	// 	$this->params['chargeInterest'] = isset($this->postData['chargeInterest']) ? $this->postData['chargeInterest'] : (isset($this->postParams['chargeInterest']) ? $this->postParams['chargeInterest'] : false);
	// 	if($this->params['uid']==0){
	// 		$this->error = "User credentials missing";
	// 	}
	// 	return $this->params;
	// }

	// public function getLenderPolicy(){
	// 	$this->generalApi();

	// 	$this->params['luid'] = isset($this->postData['luid']) ? $this->paramsValidator->validateUid($this->postData['luid']) : (isset($this->postParams['luid']) ? $this->paramsValidator->validateUid($this->postParams['luid']) : 0);
	// 	if($this->params['uid']==0){
	// 		$this->error = "User credentials missing";
	// 	}
	// 	return $this->params;
	// }

}