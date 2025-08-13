<?php
class User{
	private $uid;
	private $name;
	private $mobile;
	private $is_verified;
	private $join_date;
	private $pic_url;
	private $otp;
	private $cc;

	public function __construct(){
			$this->uid=0;
			$this->name="";
			$this->mobile="";
			$this->is_verified=1;
			$this->join_date="";
			$this->pic_url="";
			$this->otp = "";
			$this->cc = "91";
	}

	public function setDetails($uid,$name,$mobile,$cc,$verified=1){
		$this->uid = $uid;
		$this->name = $name;
		$this->mobile = $mobile;
		$this->cc = $cc;
		$this->is_verified = $verified;
		$this->join_date = time();
		$this->otp = $this->generateOTP();
	}

	public function setOTP($otp){
		$this->otp = $otp;
	}

	public function setPicUrl($url){
		$this->pic_url = $url;
	}

	public function toJson(){
		return '{"uid":"'.$this->uid.'","name":"'.$this->name.'","mobile":"'.$this->mobile.'","is_verified":'.$this->is_verified.',"join_date":"'.$this->join_date.'","pic_url":"'.$this->pic_url.'","otp":"'.$this->otp.'"}';
	}

	// public function toJsonv2(){
	// 	return '{"uid":"'.$this->uid.'","name":"'.$this->name.'","mobile":"'.$this->mobile.'","is_verified":0'.',"join_date":"'.$this->join_date.'","pic_url":"'.$this->pic_url.'","otp":"'.$this->otp.'"}';
	// }

	private function generateId(){
		$this->uid = ++$this->uid;
	}

	private function generateOTP(){
		return "1233";
	}
}

?>