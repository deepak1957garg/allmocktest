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

	public function validatePaymentMode($mode){
		if(in_array($mode,array('live','test')))	return $mode;
		else 	return "";
	}

	public function validatePaymentFor($type){
		if(in_array($type,array('appointment','package')))	return $type;
		else 	return "";
	}		

	public function validateDate($date){
		if($date=="")	return "";
		else return $date;
		$temp = expolde(" ",$date);
		$temp2 = explode("-",$temp[0]);
		if(count($temp2)!=3)	return "";

		return $date;
		// else{
		// 	if($temp2[0]>2000 && $temp2[0]<2025)	return "";
		// 	else if($temp2[1]2000 && $temp2[1]<2025)	return "";
		// } 	

		if(is_numeric($num))	return $num;
		else 	return 0;
	}


	public function validateEmail($email){
		if(filter_var($email, FILTER_VALIDATE_EMAIL)) 	return $email;
		else return "";
	}

}