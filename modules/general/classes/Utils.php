<?php 
include_once dirname(__FILE__) . '/../../../includes/config/Config.php';
// include_once dirname(__FILE__) . '/../../../includes/common/SimpleEmailService.php';
// include_once dirname(__FILE__) . '/SCCURL.php';
// include_once dirname(__FILE__) . '/../../../libs/SesWrapper.php';

class Utils{

	public static function generateUID(){
	    $my_time=gettimeofday();
		$micro = $my_time['usec'];
		while(strlen($micro) < 6)	$micro = '0'.$micro;	
		$micro = $micro.rand(10,99);
		$timeInMicroSecs = $my_time['sec'].$micro;
        return $timeInMicroSecs;
	}

	public static function removeSpecialChars($text){
		$text = str_replace(array("\n", "\r"), ' ',$text);
        $text=stripslashes($text);
		$text=str_ireplace('"','',$text); 
		$text=str_ireplace("'",'',$text); 
		$text = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , ' ',$text);
		//$text=str_ireplace('_',' ',$text); 
		$text = preg_replace('/\s+/',' ',$text);        // remove whitespaces
		$text = trim($text);
		
		return $text;
	}

	public static function getDomain($purl){
		$parseUrl = parse_url(trim($purl));
		$path_arr = array();
		if(isset($parseUrl['path']))	$path_arr = explode('/', $parseUrl['path'], 2);
		$domain = trim(isset($parseUrl['host']) ? $parseUrl['host'] : array_shift($path_arr));
   		$domain = str_replace('www.','',$domain);
   		return $domain;
	}

	public static function sendEmail($to,$sub,$message,$cc=""){
		$ses = new SesWrapper();
		$response = $ses->sendEmail('Things App Team<contact@thesnug.app>',$to,$sub,$message,$cc);
	}

	public static function sendCronMail($sub,$message,$email,$cc=''){
		Utils::sendEmail($email,$sub,$message,$cc);
	}

	public static function sendCronMail2($sub,$message,$bcc=array()){
		Utils::sendEmail2('contact@thesnug.app',$sub,$message,$bcc);
	}

	public static function sendEmail2($to,$sub,$message,$bcc=array()){
		$ses = new SesWrapper();
		array_push($bcc,'deepak@thesnug.app');
		$bcc = array_unique($bcc);
		$response = $ses->sendEmail('Things App Team<contact@thesnug.app>',$to,$sub,$message,'',implode(",",$bcc));

		// $ses = new SimpleEmailService(Config::$AWS_SES_KEY, Config::$AWS_SES_SECRET,Config::$AWS_SES_ENDPOINT);
		// $m = new SimpleEmailServiceMessage();
		// //note that from and to emails must be verified using AWS SES dashboard.  Can remove limitations here https://aws-portal.amazon.com/gp/aws/html-forms-controller/contactus/SESProductionAccess2011Q3.
		// $m->addTo($to);
		// $m->addBCC('deepak@thesnug.app');
		// if(count($bcc)>0){
		// 	foreach($bcc as $bcc_email){
		// 		$m->addBCC($bcc_email);
		// 	}
		// }
		// $m->setFrom('Humantales Team<contact@thesnug.app>');
		// $m->setSubject($sub);
		// $m->setMessageFromHTMLString($message);
		// //$m->addCustomHeader("'Mime-Version' => '1.0', 'Content-Type' => 'text/html; charset=\"ISO-8859-1\"");
		// $response = $ses->sendEmail($m,true);
	}	

	public static function sendCronMail3($email,$sub,$message){
		//Utils::sendEmail3($email,$sub,$message);
	}

	public static function sendEmail3($to,$sub,$message){
		$ses = new SimpleEmailService(Config::$AWS_SES_KEY, Config::$AWS_SES_SECRET,Config::$AWS_SES_ENDPOINT);
		$m = new SimpleEmailServiceMessage();
		//note that from and to emails must be verified using AWS SES dashboard.  Can remove limitations here https://aws-portal.amazon.com/gp/aws/html-forms-controller/contactus/SESProductionAccess2011Q3.
		$m->addTo($to);
		$m->addBCC('deepak@thesnug.app');
		$m->setFrom('Jayshree N<jayshree@thesnug.app>');
		$m->setSubject($sub);
		$m->setMessageFromHTMLString($message);
		//$m->addCustomHeader("'Mime-Version' => '1.0', 'Content-Type' => 'text/html; charset=\"ISO-8859-1\"");
		$response = $ses->sendEmail($m,true);
		print_r($response);
	}		

	public static function generateVerificationCode(){
		return rand(1000, 9999);
	}

	public static function isJson($string) {
	   json_decode($string);
	   return json_last_error() === JSON_ERROR_NONE;
	}

	// public static function sendEmail($to='manish@thesnug.app',$sub,$message,$cc=""){
	// 	$ses = new SimpleEmailService(Config::$AWS_SES_KEY, Config::$AWS_SES_SECRET,Config::$AWS_SES_ENDPOINT);
	// 	$m = new SimpleEmailServiceMessage();
	// 	//note that from and to emails must be verified using AWS SES dashboard.  Can remove limitations here https://aws-portal.amazon.com/gp/aws/html-forms-controller/contactus/SESProductionAccess2011Q3.
	// 	$m->addTo($to);
	// 	if($cc !=""){
	// 		$ccemails = explode(",",$cc);
	// 		foreach($ccemails as $ccmail){
	// 			$m->addCC($ccmail);
	// 		}
	// 	} 
	// 	//$m->addCC($cc);
	// 	$m->setFrom('Phantom<deepak@sweetcouch.com>');
	// 	$m->setSubject($sub);
	// 	$m->setMessageFromString($message);
	// 	$response = $ses->sendEmail($m);
	// }

	// public static function sendCronMail($sub,$message){
	// 	Utils::sendEmail(Config::$SNUG_EMAIL_DEV,$sub,$message);
	// }

	public static function addTOAddressbook($name,$mobile){
		error_log("adding to addressbook - name=".$name." mobile=".$mobile);
		$curl = new SCCURL(Config::$ZAPIER_ADD_USER_HOOK,1);
		//$opt = $curl->getHeaders();
		//$opt = $curl->getBody();
		$opt = $curl->postBody('{"name":"'.$name.'","mobile":"'.$mobile.'"}');
		if($opt == '') error_log($curl->fetchError());
		error_log($opt);
	}

	public static function getHashFromUid($uid){
		$temp_uid=substr($uid,10,8) . substr($uid,0,10);
		$temp_uid=strrev($temp_uid);
		return $temp_uid;
	}

	public static function getUidFromHash($temp_uid){
		$temp_uid=strrev($temp_uid);
		$uid=substr($temp_uid,8) . substr($temp_uid,0,8);
		return $uid;
	}

	public static function getCandidateQueueForHost($rid){
    	$arr = array();
        $queue = $this->getCandidateQueue($rid);
        $que_len = count($queue);
        foreach($queue as $obj){
        	$obj = array();
        	$obj['tuid'] = self::getHashFromUid($obj['uid']);
            $obj['name'] = strtoupper(substr($obj['name'],0,1));
            $obj['pic'] = $obj['pic'];
            array_push($arr,$obj);
        }
        return $arr;
    }

    public static function getOrdinal($n){
    	$val = $n . 'th';
    	$array = array('1st', '2nd', '3rd');
    	if($n!=0 && $n<=3){
    		$val = $array[($n-1)];
    	}
    	return strtolower($val);
    }

    public static function isbot(){
    	$BOTS_LIST=array('bingbot','googlebot','gabot','ahrefsbot','msnbot-media','slurp','yandexbot','yahoo!','applebot','pinterest','twitterbot','pingdom.com_bot_version_1.4_','Rackspace Monitoring');
		$isbot=0;
		if(isset($_SERVER['HTTP_USER_AGENT'])){
			$useragent=strtolower($_SERVER['HTTP_USER_AGENT']);
			if(!in_array($useragent,$BOTS_LIST)){ }
			else 	$isbot=1;
		}
		return 	$isbot;
	}

	public static function isIos(){
		$isIos = 0;

		//Detect special conditions devices
		$iPod    = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
		$iPhone  = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
		$iPad    = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");

		//do something with this information
		if( $iPod || $iPhone || $iPad){
		   	$isIos = 1;
		}
		return $isIos;
	}

}