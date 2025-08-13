<?php
include_once dirname(__FILE__) . '/../dao/UserReadDao.php';
include_once dirname(__FILE__) . '/../dao/UserWriteDao.php';
include_once dirname(__FILE__) . '/../../../libs/GoogleApiClient.php';

class UserDataExtractor{
	private $cread;
	private $cwrite;
	private $client;

	public function __construct(){
		$this->cread = new UserReadDao();
		$this->cwrite = new UserWriteDao();
		$this->client = new GoogleApiClient();
	}

	public function extractGPlusData($token){
		$data = $this->client->getLoggedInUserData($token);
		return $data;
	}

}
?>