<?php
include_once dirname(__FILE__) . '/../../general/classes/Utils.php';
include_once dirname(__FILE__) . '/../../api/classes/ApiRequestReader.php';

class UserMapApiRequestReader extends ApiRequestReader{
	protected $paramsValidator;

	function __construct(){
		parent::__construct();
	}

	public function saveUserMap(){
		$this->validateAndReadPostData();
		$this->params['tuid'] = isset($this->postData['tuid']) ? $this->paramsValidator->validateUid($this->postData['tuid']) : (isset($this->postParams['tuid']) ? $this->paramsValidator->validateUid($this->postParams['tuid']) : 0);
		$this->params['uid'] = isset($this->postData['uid']) ? $this->paramsValidator->validateUid($this->postData['uid']) : (isset($this->postParams['uid']) ? $this->paramsValidator->validateUid($this->postParams['uid']) : 0);
		$this->params['cc'] = isset($this->postData['cc']) ? $this->paramsValidator->validateCC($this->postData['cc']) : (isset($this->postParams['cc']) ? $this->paramsValidator->validateCC($this->postParams['cc']) : "");
		$this->params['mobile'] = isset($this->postData['mobile']) ? $this->paramsValidator->validateMobile($this->postData['mobile']) : (isset($this->postParams['mobile']) ? $this->paramsValidator->validateMobile($this->postParams['mobile']) : "");
		$this->params['fb_id'] = isset($this->postData['fb_id']) ? $this->postData['fb_id'] : (isset($this->postParams['fb_id']) ? $this->postParams['fb_id'] : "");
		$this->params['name'] = isset($this->postData['name']) ? $this->postData['name'] : (isset($this->postParams['name']) ? $this->postParams['name'] : "");
		$this->params['email'] = isset($this->postData['email']) ? $this->paramsValidator->validateEmail($this->postData['email']) : (isset($this->postParams['email']) ? $this->paramsValidator->validateEmail($this->postParams['email']) : "");
		$this->params['pic'] = isset($this->postData['pic']) ? $this->postData['pic'] : (isset($this->postParams['pic']) ? $this->postParams['pic'] : "");

		if($this->params['cc']=='' || $this->params['mobile']==""){
			$this->error = "Either Mobile no or country code is not valid";
		}
		return $this->params;
	}

}