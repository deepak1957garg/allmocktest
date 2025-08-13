<?php 
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class UserInfo extends DataModel{

	public function __construct(){
		$this->datamodel['uid']=0;
		$this->datamodel['name']='';
		$this->datamodel['uname']='';
		$this->datamodel['cc']='';
		$this->datamodel['mobile']='';
		$this->datamodel['email']='';
		$this->datamodel['everified']=0;
		$this->datamodel['mverified']=0;
		$this->datamodel['password']='';
		$this->datamodel['pic']='';
		$this->datamodel['phone']='';
		$this->datamodel['fcm_token']='';
		$this->datamodel['uId']='';
		$this->datamodel['profile_pic']='';
		$this->datamodel['profile_pic_uri']='';
		$this->datamodel['total_lend']='';
		$this->datamodel['score']='';
		$this->datamodel['avg_borrowed']='';
		$this->datamodel['total_borrow']='';
		$this->datamodel['fb_id']='';
	}

	// public function getShortInfo(){
	// 	$user = array();
	// 	$user['uid'] = $this->datamodel['uid'];
	// 	$user['name'] = $this->datamodel['name'];
	// 	if($this->datamodel['pic']!="")	$user['pic'] = 'https://jalwa-app.s3.ap-south-1.amazonaws.com/' . $this->datamodel['pic'];
	// 	else $user['pic'] = '';
	// 	return $user;
	// }

	// public function getInfo(){
	// 	if($this->datamodel['pic']!="")	$this->datamodel['pic'] = 'https://jalwa-app.s3.ap-south-1.amazonaws.com/' . $this->datamodel['pic'];
	// 	return $this->getObject();
	// }

	// public function getUserPic(){
	// 	if($this->datamodel['pic']!='')	return 'https://jalwa-app.s3.ap-south-1.amazonaws.com/' . $this->datamodel['pic'];
	// 	else return '';
	// }

	// public function getShortInfo2(){
	// 	$user = array();
	// 	$user['uid'] = $this->datamodel['uid'];
	// 	$user['name'] = $this->datamodel['name'];
	// 	$user['email'] = $this->datamodel['email'];
	// 	$user['one_word'] = $this->datamodel['designation'];
	// 	$user['one_line'] = $this->datamodel['aboutme'];
	// 	if($this->datamodel['pic']!="")	$user['pic'] = 'https://jalwa-app.s3.ap-south-1.amazonaws.com/' . $this->datamodel['pic'];
	// 	else $user['pic'] = '';
	// 	return $user;
	// }	

}