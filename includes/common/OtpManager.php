<?php
include_once dirname(__FILE__) . '/../utils/Utils.php';
include_once dirname(__FILE__) . '/../utils/SCCURL.php';
include_once dirname(__FILE__) . '/../config/Config.php';

//include_once dirname(__FILE__).'/../../libs/Twilio/autoload.php';
//use Twilio\Rest\Client;

class OTPManager{

	public function __construct(){

	}

	public function sendTwilioSMS($mobile,$cc,$code){
		/*
			curl -X POST -d "Body=Hi there, your new phone number is working." \
    -d "MessagingServiceSid=MGdcbe9ba29aaafe68441d224c48e8a7aa" -d "To=+919820227334" \
    "https://api.twilio.com/2010-04-01/Accounts/AC82ba0f145c8c30f94f9e0ecc2ef96c6d/Messages" \
    -u "AC82ba0f145c8c30f94f9e0ecc2ef96c6d:76c0150685b98773df5931e2991f3ac7" 
		*/

		$success = false;
		$message = "<#> Your Snug mobile verification code is $code.

hxChnxsT82n";
		$to = "+".$cc.$mobile;
		$body ="Body=".$message."&MessagingServiceSid=".Config::$TWILIO_MESSAGE_SERVICE_ID."&To=".$to;
    	$url = "https://api.twilio.com/2010-04-01/Accounts/".Config::$TWILIO_SID."/Messages.json";
    	error_log($url);
    	error_log($body);
    	list($response,$returncode) = $this->sendRequest($url,$body,Config::$TWILIO_SID,Config::$TWILIO_AUTH_CODE);
    	$success = $this->processResponse($response,$returncode,$to,$code);
    	return $success;
	}

	public function sendPromoSMS($to,$metadata){
		$success = false;
		try{
			$key=Config::$APP_HASH_KEY_FOR_OTP_READING;
			$url = Config::$TWOFACTOR_TSMS_URL;
			$body = "From=SNUGAP&To=".$to."&TemplateName=".Config::$TWOFACTOR_PROMO_TSMS_TEMPLATE."&VAR1=".$metadata[0]."&VAR2=".$metadata[1];
			list($response,$returncode) = $this->sendRequest($url,$body);
			error_log("returncode=".$returncode);
			if(empty($response)){
				$errormsg='Response is blank<br /><br />Response return code : ' . print_r($returncode,1) . '<br />mobile no. :' . $mobile . '<br /> code : ' . $code . ' <br /><br />';
				Utils::sendCronMail('Snug PROMO SMS API Failed for '.$to,$errormsg);
				error_log('Snug PROMO SMS API Failed for - ' . $errormsg);
			}
			else{
				$result = json_decode($response,true);
				if($result['Status']=='Success'){
					error_log("Snug PROMO SMS API Request for ".$to." success. Response=".$response);
					$success=true;
				}
				else{
					error_log("Snug PROMO SMS API Request for ".$to." failed. Response=".$response);
					$errormsg='Response : ' . print_r($response,1) . '<br />Response return code : ' . print_r($returncode,1) . '<br /><br />';
					Utils::sendCronMail("Snug PROMO SMS API Failed for ".$to,$errormsg);
				}
			}	
		}catch(Exception $e){
			$success = false;
			error_log($e);
		}
		return $success;
	}

	public function sendOTP($to,$code,$key=""){
		$success = false;
		try{
			$key=Config::$APP_HASH_KEY_FOR_OTP_READING;
			$url = Config::$TWOFACTOR_TSMS_URL;
			$body = "From=HOUSFL&To=".$to."&TemplateName=".Config::$TWOFACTOR_SMS_TEMPLATE_NAME."&VAR1=".$code."&VAR2=".$key;
			//echo $body;
			list($response,$returncode) = $this->sendRequest($url,$body);
			$success = $this->processResponse($response,$returncode,$to,$code);

			// //error_log(print_r($response,1));
			// error_log("returncode=".$returncode);
			// //echo $response;
			// //echo $returncode;
			// if(empty($response)){
			// 	$errormsg='Response is blank<br /><br />Response return code : ' . print_r($returncode,1) . '<br />mobile no. :' . $mobile . '<br /> code : ' . $code . ' <br /><br />';
			// 	Utils::sendCronMail('Snug OTP API Failed for '.$to,$errormsg);
			// 	error_log('Snug OTP request failed - ' . $errormsg);
			// }
			// else{
			// 	$result = json_decode($response,true);
			// 	if($result['Status']=='Success'){
			// 		error_log("OTP Request for ".$to." success. Response=".$response);
			// 		$success=true;
			// 	}
			// 	else{
			// 		error_log("OTP Request for ".$to." failed. Response=".$response);
			// 		$errormsg='Response : ' . print_r($response,1) . '<br />Response return code : ' . print_r($returncode,1) . '<br /><br />';
			// 		Utils::sendCronMail("Snug OTP Request Failed for ".$to,$errormsg);
			// 	}
			// }
		}catch(Exception $e){
			$success = false;
			error_log($e);
		}
		return $success;
	}

	private function processResponse($response,$returncode,$to,$code){
		$success = false;
		error_log("to=".$to." returncode=".$returncode);
		if(empty($response)){
				$errormsg='Response is blank<br /><br />Response return code : ' . print_r($returncode,1) . '<br />mobile no. :' . $to . '<br /> code : ' . $code . ' <br /><br />';
				Utils::sendCronMail('Snug OTP API Failed for '.$to,$errormsg);
				error_log('Snug OTP request failed - ' . $errormsg);
		}
		else{
			$result = json_decode($response,true);
			if(isset($result['Status']) && $result['Status']=='Success'){  //2factor
				error_log("OTP Request for ".$to." success. Response=".$response);
				$success=true;
			}
			else if($result['status']=='accepted'){ //twilio
				error_log("OTP Request for ".$to." success. Response=".print_r($result,1));
				$success=true;
			}
			else{
				error_log("OTP Request for ".$to." failed. Response=".$response);
				$errormsg='Response : ' . print_r($response,1) . '<br />Response return code : ' . print_r($returncode,1) . '<br /><br />';
				Utils::sendCronMail("Snug OTP Request Failed for ".$to,$errormsg);
			}
		}
		return $success;
	}

	public function sendCallMeMessage($mobile,$code){
		$success = false;
		try{
			$url = Config::$TWOFACTOR_CALLME_URL;
			$url = str_replace('<PhoneNumber>',$mobile,$url);
			$url = str_replace('<OTP>',$code,$url);
			list($response,$returncode) = $this->sendRequest($url);
			if(empty($response)){
				$errormsg='Response is blank<br /><br />Response return code : ' . print_r($returncode,1) . '<br />mobile no. :' . $mobile . '<br /> code : ' . $code . ' <br /><br />';
				Utils::sendCronMail('call me api failed',$errormsg);
				error_log('call me api failed - ' . $errormsg);
			}
			else{
				$result = json_decode($response,true);
				//echo $result;
				if($result['Status']=='Success'){
					// if($this->uwritedaoobj->saveCallmeRequest($mobile,$code,$result['Status'],$result['Details'])){}
					// else{
					// 	$errormsg='Saving in db failed<br /><br />';
					// 	Utils::sendCronMail('call me api failed',$errormsg);
					// 	error_log('call me api failed - ' . $errormsg);
					// }
				}
				else{
					$errormsg='Response : ' . print_r($response,1) . '<br />Response return code : ' . print_r($returncode,1) . '<br /><br />';
					//$this->uwritedaoobj->saveCallmeRequest($mobile,$code,$result['Status'],'',$result['Details']);
					Utils::sendCronMail('call me api failed',$errormsg);
					error_log('call me api failed - ' . $errormsg);
				}
		    }
		}catch(Exception $ex){$success = false;}
		return $success;
	}

	private function sendRequest($url,$body="",$basic_auth="",$basic_auth_pass=""){
		$response = '';
		try{
			$scurl = new SCCURL($url);

			if($basic_auth !=""){
				$scurl->setBasicAuth($basic_auth,$basic_auth_pass);
			}
			if($body!=""){
				$response = $scurl->postBody($body);
			}else{
				$response = $scurl->getBody();	
			}
			$returncode = $scurl->fetchReturnCode();
		}catch(Exception $ex){}
		return array($response,$returncode);
	}

}

//Test
//$twof = new OTPManager();
//$twof->sendTwilioSMS('982022734','91','3232');
//echo $twof->sendPromoSMS('9820227334',array('Manish Deora','bit.ly/snug-meeting'))
//echo $twof->sendCallMeMessage('9820227334','1212');
//echo $twof->sendOTP('9987045279','1234');

?>