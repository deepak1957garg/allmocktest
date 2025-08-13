<?php

class ParamsValidator{

	function __construct(){
	}

	public function validateCC($cc){
		if(strlen($cc)==2 && is_numeric($cc))	return $cc;
		else 	return "";
	}

	public function validateMobile($mobile){
		if(strlen($mobile)==10 && is_numeric($mobile))	return $mobile;
		else 	return "";
	}

	public function validateOtp($otp){
		if(strlen($otp)==4 && is_numeric($otp))	return $otp;
		else 	return "";
	}

	public function validateUid($uid){
		return $this->validateNumber($uid,18);
	}	

	public function validateNumber($num,$len){
		if(strlen($num)==$len && is_numeric($num))	return $num;
		else 	return 0;
	}

	public function validateIsNumber($num){
		if(is_numeric($num))	return $num;
		else 	return 0;
	}

	public function validateEmail($email){
		if(filter_var($email, FILTER_VALIDATE_EMAIL)) 	return $email;
		else return "";
	}

	public function validatePolicyType($type){
		if(in_array($type,array('lender','borrower')))	return $type;
		else return "";
	}

}