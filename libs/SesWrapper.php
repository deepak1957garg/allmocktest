<?php
include_once dirname(__FILE__) . '/../includes/config/Config.php';
require_once dirname(__FILE__) . '/../vendor/autoload.php';

use Aws\Ses\SesClient;
use Aws\Exception\AwsException;
use Aws\Credentials\Credentials;

class SesWrapper{
	private $sesClient;

	function __construct(){
		$this->sesClient=null;
		$this->createSesObject();
	}

	private function createSesObject(){
		$credentials = new Credentials(Config::$AWS_SES_KEY,Config::$AWS_SES_SECRET);
		$this->sesClient = SesClient::factory(array(
			'credentials' => $credentials,
			'region'      => Config::AWS_REGION,
			'version'     => '2010-12-01'
		));
	}
	
	public function sendEmail($sender,$to,$sub,$message,$cc='',$bcc=''){
		$messageId = '';
		try {
			$char_set = 'UTF-8';
			$destination = array();
			$destination['ToAddresses'] = explode(",",$to);
			if($cc!='')		$destination['CcAddresses'] = explode(",",$cc);
			if($bcc!='')	$destination['BccAddresses'] = explode(",",$bcc);

			$result = $this->sesClient->sendEmail([
				'Destination' => $destination,
				'ReplyToAddresses' => [$sender],
				'Source' => $sender,
				'Message' => [
					'Body' => [
						'Html' => [
							'Charset' => $char_set,
							'Data' => $message,
						],
					],
					'Subject' => [
						'Charset' => $char_set,
						'Data' => $sub,
					],
				],
			]);
			    
			$messageId = $result['MessageId'];
		}
		catch (AwsException $e) {
			error_log("Ses mail send fail exception : " . $e->getMessage());
			error_log("Ses mail send fail exception error msg : " . $e->getAwsErrorMessage());
		}
	}

}
?>