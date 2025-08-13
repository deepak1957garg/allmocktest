<?php
include_once dirname(__FILE__) . '/../../general/classes/Utils.php';
include_once dirname(__FILE__) . '/../../api/classes/ApiRequestReader.php';
include_once dirname(__FILE__) . '/../../../includes/common/CookieManager.php';
include_once dirname(__FILE__) . '/../../general/classes/Utils.php';
include_once dirname(__FILE__) . '/../../../includes/common/ImageOperations.php';

class UserApiRequestReader extends ApiRequestReader{
	protected $paramsValidator;

	function __construct(){
		parent::__construct();
	}

	public function sendOtp(){
		$this->validateAndReadPostData();
		$this->params['cc'] = isset($this->postData['cc']) ? $this->paramsValidator->validateCC($this->postData['cc']) : (isset($this->postParams['cc']) ? $this->paramsValidator->validateCC($this->postParams['cc']) : "");
		$this->params['mobile'] = isset($this->postData['mobile']) ? $this->paramsValidator->validateMobile($this->postData['mobile']) : (isset($this->postParams['mobile']) ? $this->paramsValidator->validateMobile($this->postParams['mobile']) : "");
		if($this->params['cc']!=91 || $this->params['mobile']==""){
			$this->error = "Either Mobile no or country code is not valid";
		}
		return $this->params;
	}

	public function login(){
		$this->validateAndReadPostData();
		$this->params['token'] = isset($this->postData['token']) ? $this->postData['token'] : (isset($this->postParams['token']) ? $this->postParams['token'] : "");
		return $this->params;
	}	

	public function verifyOtp(){
		$this->validateAndReadPostData();

		$this->params['cc'] = isset($this->postData['cc']) ? $this->paramsValidator->validateCC($this->postData['cc']) : (isset($this->postParams['cc']) ? $this->paramsValidator->validateCC($this->postParams['cc']) : "");
		$this->params['mobile'] = isset($this->postData['mobile']) ? $this->paramsValidator->validateMobile($this->postData['mobile']) : (isset($this->postParams['mobile']) ? $this->paramsValidator->validateMobile($this->postParams['mobile']) : "");
		$this->params['otp'] = isset($this->postData['otp']) ? $this->paramsValidator->validateOtp($this->postData['otp']) : (isset($this->postParams['otp']) ? $this->paramsValidator->validateOtp($this->postParams['otp']) : "");

		if($this->params['cc']!=91 || $this->params['mobile']==""){
			$this->error = "Either Mobile no or country code is not valid";
		}
		else if($this->params['otp']==""){
			$this->error = "Otp did not match";;
		}
		return $this->params;
	}

	public function updateUser(){
		$this->validateAndReadPostData();

		$uid = CookieManager::getUid();
		$this->params['uid'] = $this->paramsValidator->validateUid($uid);
		$this->params['cc'] = isset($this->postData['cc']) ? $this->paramsValidator->validateCC($this->postData['cc']) : (isset($this->postParams['cc']) ? $this->paramsValidator->validateCC($this->postParams['cc']) : "");
		$this->params['mobile'] = isset($this->postData['mobile']) ? $this->paramsValidator->validateMobile($this->postData['mobile']) : (isset($this->postParams['mobile']) ? $this->paramsValidator->validateMobile($this->postParams['mobile']) : "");
		// $this->params['uid'] = isset($this->postData['uid']) ? $this->paramsValidator->validateUid($this->postData['uid']) : (isset($this->postParams['uid']) ? $this->paramsValidator->validateUid($this->postParams['uid']) : 0);
		$this->params['name'] = isset($this->postData['name']) ? $this->postData['name'] : (isset($this->postParams['name']) ? $this->postParams['name'] : "");
		$this->params['email'] = isset($this->postData['email']) ? $this->paramsValidator->validateEmail($this->postData['email']) : (isset($this->postParams['email']) ? $this->paramsValidator->validateEmail($this->postParams['email']) : "");
		$this->params['pic'] = isset($this->postData['pic']) ? $this->postData['pic'] : (isset($this->postParams['pic']) ? $this->postParams['pic'] : "");
		$this->params['bio'] = isset($this->postData['bio']) ? $this->postData['bio'] : (isset($this->postParams['bio']) ? $this->postParams['bio'] : "");
		$this->params['upi'] = isset($this->postData['upi']) ? $this->postData['upi'] : (isset($this->postParams['upi']) ? $this->postParams['upi'] : "");
		$this->params['aboutme'] = isset($this->postData['aboutme']) ? $this->postData['aboutme'] : (isset($this->postParams['aboutme']) ? $this->postParams['aboutme'] : "");
		$this->params['designation'] = isset($this->postData['designation']) ? $this->postData['designation'] : (isset($this->postParams['designation']) ? $this->postParams['designation'] : "");
		$this->params['login_complete'] = isset($this->postData['login_complete']) ? $this->postData['login_complete'] : (isset($this->postParams['login_complete']) ? $this->postParams['login_complete'] : 0);

		
		// $this->params['pic'] = isset($this->postData['uid']) ? $this->paramsValidator->validateEmail($this->postData['uid']) : (isset($this->postParams['uid']) ? $this->paramsValidator->validateEmail($this->postParams['uid']) : "");

		if(!($this->params['cc']==91 && $this->params['mobile']!="") && $this->params['uid']==0){
			if($this->params['uid']==0)	$this->error = "Invalid User";
			else $this->error = "Either Mobile no or country code is not valid";
		}
		return $this->params;
	}

	public function updateUserDetails(){
		$this->validateAndReadPostData();
		//$postParams = array();
		//if($this->postParams['data'])	$postParams = json_decode($this->postParams['data'],true);
		//$uid = 
		$uid = CookieManager::getUid();
		$this->params['uid'] = $this->paramsValidator->validateUid($uid);
		$this->params['cc'] = isset($this->postParams['cc']) ? $this->paramsValidator->validateCC($this->postParams['cc']) : (isset($postData['cc']) ? $this->paramsValidator->validateCC($postData['cc']) : "");
		$this->params['mobile'] = isset($this->postParams['mobile']) ? $this->paramsValidator->validateMobile($this->postParams['mobile']) : (isset($postData['mobile']) ? $this->paramsValidator->validateMobile($postData['mobile']) : "");
		//$this->params['uid'] = isset($this->postParams['uid']) ? $this->paramsValidator->validateUid($this->postParams['uid']) : (isset($postData['uid']) ? $this->paramsValidator->validateUid($postData['uid']) : 0);
		$this->params['name'] = isset($this->postParams['name']) ? $this->postParams['name'] : (isset($postData['name']) ? $postData['name'] : "");
		$this->params['email'] = isset($this->postParams['email']) ? $this->paramsValidator->validateEmail($this->postParams['email']) : (isset($postData['email']) ? $this->paramsValidator->validateEmail($postData['email']) : "");
		$this->params['bio'] = isset($this->postParams['bio']) ? $this->postParams['bio'] : (isset($postData['bio']) ? $postData['bio'] : "");
		$this->params['aboutme'] = isset($this->postData['aboutme']) ? $this->postData['aboutme'] : (isset($this->postParams['aboutme']) ? $this->postParams['aboutme'] : "");
		$this->params['designation'] = isset($this->postData['designation']) ? $this->postData['designation'] : (isset($this->postParams['designation']) ? $this->postParams['designation'] : "");
		$this->params['profile_pic_temp'] = $this->saveTempPic();
		$this->params['login_complete'] = isset($this->postData['login_complete']) ? $this->postData['login_complete'] : (isset($this->postParams['login_complete']) ? $this->postParams['login_complete'] : 0);
		// $this->params['pic'] = isset($this->postData['uid']) ? $this->paramsValidator->validateEmail($this->postData['uid']) : (isset($this->postParams['uid']) ? $this->paramsValidator->validateEmail($this->postParams['uid']) : "");

		// if(!($this->params['cc']==91 && $this->params['mobile']!="") && $this->params['uid']==0){
		// 	if($this->params['uid']==0)	$this->error = "Invalid User";
		// 	else $this->error = "Either Mobile no or country code is not valid";
		// }
		return $this->params;
	}

	private function saveTempPic(){
		$temp_dir = '/tmp/phantom/profile/';
		if(!file_exists($temp_dir))	mkdir($temp_dir,0777,true);
		$file_path = '';
		$error='';
		try{
			if(isset($_FILES['pic'])){
				$file=$_FILES['pic'];
			}
			else{
				$error = 'file not found';
			}
			$file_path='';
			if($error==''){
				$short_file_path = Utils::generateUID();
				$temp = explode(".",basename( $_FILES['pic']['name']));
				$short_file_path = $short_file_path ."." . $temp[(count($temp)-1)];
				$file_path = $temp_dir;
					
				$file_path = $file_path .$short_file_path;
				if(move_uploaded_file($_FILES['pic']['tmp_name'], $file_path)) {
					$params['pic'] = $short_file_path;
				}
				else{
					$error = 'fail to save file';
				}
			}
		}
		catch(Exception $ex){ }
		return $file_path;
	}

	public function saveTempPicFromUrl($imgurl){
		$temp_dir = '/tmp/phantom/profile/';
		$imgopobj = new ImageOperations($temp_dir);
		$tempname = Utils::generateUID();
		$filepath = '';

		if(copy(str_replace(' ', "%20", $imgurl),$temp_dir.$tempname)){

			$imageextension=$imgopobj->getImageExtension($temp_dir.$tempname);
			if($imageextension){
				$imagename=$tempname . $imageextension;
				rename('' . $temp_dir.$tempname,'' . $temp_dir.$imagename);	
				$filepath = $temp_dir.$imagename;;
			}
		}
		return $filepath;
	}



}