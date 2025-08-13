<?php 
include_once dirname(__FILE__) . '/../config/Config.php';
include_once dirname(__FILE__) . '/../common/SimpleEmailService.php';
include_once  dirname(__FILE__) . '/SCCURL.php';

class Utils{

	public static function generateUID(){
	    $my_time=gettimeofday();
		$micro = $my_time['usec'];
		while(strlen($micro) < 6)	$micro = '0'.$micro;	
		$micro = $micro.rand(10,99);
		$timeInMicroSecs = $my_time['sec'].$micro;
        return $timeInMicroSecs;
	}

	public static function generateVerificationCode(){
		return rand(1000, 9999);
	}

	public static function isJson($string) {
	   json_decode($string);
	   return json_last_error() === JSON_ERROR_NONE;
	}

	public static function sendEmail($to='manish@thesnug.app',$sub,$message,$cc=""){
		$ses = new SimpleEmailService(Config::$AWS_SES_KEY, Config::$AWS_SES_SECRET,Config::$AWS_SES_ENDPOINT);
		$m = new SimpleEmailServiceMessage();
		//note that from and to emails must be verified using AWS SES dashboard.  Can remove limitations here https://aws-portal.amazon.com/gp/aws/html-forms-controller/contactus/SESProductionAccess2011Q3.
		$m->addTo($to);
		if($cc !=""){
			$ccemails = explode(",",$cc);
			foreach($ccemails as $ccmail){
				$m->addCC($ccmail);
			}
		} 
		//$m->addCC($cc);
		$m->setFrom('Snug<manish@sweetcouch.com>');
		$m->setSubject($sub);
		$m->setMessageFromString($message);
		$response = $ses->sendEmail($m);
	}

	public static function sendCronMail($sub,$message){
		Utils::sendEmail(Config::$SNUG_EMAIL_DEV,$sub,$message);
	}

	public static function addTOAddressbook($name,$mobile){
		error_log("adding to addressbook - name=".$name." mobile=".$mobile);
		$curl = new SCCURL(Config::$ZAPIER_ADD_USER_HOOK,1);
		//$opt = $curl->getHeaders();
		//$opt = $curl->getBody();
		$opt = $curl->postBody('{"name":"'.$name.'","mobile":"'.$mobile.'"}');
		if($opt == '') error_log($curl->fetchError());
		error_log($opt);
	}

}

//test
//echo Utils::generateUID();
//Utils::sendCronEmail("test","test");
//Utils::sendEmail('manish@thesnug.app',"test email","testing","manish.deora@gmail.com,manish@sweetcouch.com")
//Utils::addTOAddressbook("test user","2222")
?>