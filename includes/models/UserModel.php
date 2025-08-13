<?php
include_once dirname(__FILE__) . '/../../modules/general/models/DataModel.php';

class UserModel extends DataModel{

	function __construct(){
		$this->datamodel['uid']=0;
		$this->datamodel['uname']='';
		$this->datamodel['name']='';
		$this->datamodel['email']='';
		$this->datamodel['cc']='';
		$this->datamodel['mobile']='';
		$this->datamodel['everified']=0;
		$this->datamodel['mverified']=0;
		$this->datamodel['password']='';
		$this->datamodel['isactive']=1;
		$this->datamodel['joined_on']='';
		$this->datamodel['updated_on']='';
		$this->datamodel['pic']='';
		$this->datamodel['bio']='';
		$this->datamodel['vlist']=array();
		$this->datamodel['tips']=array();
		$this->datamodel['amount_earned']=0;
		$this->datamodel['amount_earned_paid']=0;
		$this->datamodel['amount_to_pay']=0;
	}

	public function setEarnedAmount($amount){
		$this->datamodel['amount_earned']=$amount;
	}

	public function setUserStats($stats){
		$this->datamodel['amount_earned']=$stats['points'];
		$this->datamodel['amount_earned_paid']=$stats['points_paid'];
		//$this->datamodel['amount_to_pay']=($this->datamodel['amount_earned']-$this->datamodel['amount_earned_paid']);
	}

	public function setUserUnpaidTipAmount($points){
		if($points!=NULL)	$this->datamodel['amount_to_pay']=$points;
	}

	public function setUserVideo($video,$earned=0){
		$obj = array();
		$obj['vid']	= isset($video['vid'])	?	$obj['vid']=$video['vid'] : 0;
		$obj['vname'] = isset($video['vname'])	? $video['vname'] : "";
		$obj['vmsg'] = isset($video['vmessage']) ? $video['vmessage'] : "";
		$obj['path'] = isset($video['path']) ? 'https://jalwa-app.s3.ap-south-1.amazonaws.com' . $video['path'] : "";
		$obj['thumb'] = isset($video['thumb'])	?	'https://jalwa-app.s3.ap-south-1.amazonaws.com' . $video['thumb'] : "";
		$obj['url']	= isset($video['vid'])	 ? 'https://www.thesnug.app/video/' . $obj['vid'] : "";
		$obj['amount'] = $earned;
		$obj['amount_received']	= 0;//isset($video['points_paid']) ? $video['points_paid'] : 0;
		$obj['num_tips'] = isset($video['num_tips']) ? $video['num_tips'] : 0;
		$obj['created_on'] = isset($video['created_on']) ? date("d M Y",strtotime($video['created_on'])) : "";
		$obj['status'] = isset($video['vstatus']) ? $video['vstatus'] : 0;
		array_push($this->datamodel['vlist'],$obj);
	}

	public function setTippedVideo($video,$tip_amount){
		$obj = array();
		$obj['vid'] = (isset($video['vid'])) ? $video['vid'] : "0";
		$obj['vname'] = (isset($video['vname'])) ? $video['vname'] : "";
		$obj['path'] = (isset($video['path'])) ? 'https://jalwa-app.s3.ap-south-1.amazonaws.com' . $video['path'] : "";
		$obj['thumb'] = (isset($video['thumb'])) ? 'https://jalwa-app.s3.ap-south-1.amazonaws.com' . $video['thumb'] : "";
		$obj['url'] = (isset($video['vurl'])) ? 'https://www.thesnug.app/video/' . $obj['vid'] : "";
		$obj['tip_amount'] = (isset($tip_amount))	? $tip_amount : "0";
		array_push($this->datamodel['tips'],$obj);
	}	

	 //{"vid":"16","vname":"rajeshree","path":"https:\/\/jalwa-app.s3.ap-south-1.amazonaws.com\/videos\/rajeshree-2-small.mp4","thumb":"https:\/\/jalwa-app.s3.ap-south-1.amazonaws.com\/front_image\/rajeshree-2.jpg","tip_amount":"10"}


	public function getMyInfo(){
		$user = array();
		$user['uid'] = $this->datamodel['uid'];
		$user['name'] = $this->datamodel['name'];
		$user['mobile'] = $this->datamodel['mobile'];
		$user['cc'] = $this->datamodel['cc'];
		$user['hasupi'] = 0;
		if($this->datamodel['pic']!="")	$user['pic'] = 'https://jalwa-app.s3.ap-south-1.amazonaws.com' . $this->datamodel['pic'];
		else $user['pic'] = '';
		$user['url'] = 'https://www.thesnug.app/profile/' . $user['uid'];
		$user['bio'] = $this->datamodel['bio'];
		$user['amount_earned'] = $this->datamodel['amount_earned'];
		$user['amount_earned_paid'] = $this->datamodel['amount_earned_paid'];
		$user['amount_to_pay'] = $this->datamodel['amount_to_pay'];
		$user['vlist'] = $this->datamodel['vlist'];
		$user['tips'] = $this->datamodel['tips'];
		return $user;
	}

	public function getMyInfoShort(){
		$user = array();
		$user['uid'] = $this->datamodel['uid'];
		$user['name'] = $this->datamodel['name'];
		$user['mobile'] = $this->datamodel['mobile'];
		$user['cc'] = $this->datamodel['cc'];
		$user['hasupi'] = 0;
		if($this->datamodel['pic']!="")	$user['pic'] = 'https://jalwa-app.s3.ap-south-1.amazonaws.com' . $this->datamodel['pic'];
		else $user['pic'] = '';
		$user['url'] = 'https://www.thesnug.app/profile/' . $user['uid'];
		$user['bio'] = $this->datamodel['bio'];
		return $user;
	}

}