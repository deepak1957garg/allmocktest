<?php
include_once dirname(__FILE__) . '/../dao/UserReadDao.php';
include_once dirname(__FILE__) . '/../dao/UserWriteDao.php';
include_once dirname(__FILE__) . '/../../general/classes/Utils.php';
//include_once dirname(__FILE__) . '/../../notification/classes/OtpManager.php';
include_once dirname(__FILE__) . '/../models/Otp.php';
include_once dirname(__FILE__) . '/../models/Booking.php';
include_once dirname(__FILE__) . '/../models/BookingInfo.php';
include_once dirname(__FILE__) . '/../models/User.php';
include_once dirname(__FILE__) . '/../models/UserInfo2.php';
include_once dirname(__FILE__) . '/../models/UserReferrer.php';
include_once dirname(__FILE__) . '/../models/BasicUserInfo.php';
include_once dirname(__FILE__) . '/../../video/models/Video.php';
include_once dirname(__FILE__) . '/../../../includes/common/Countries.php';
// include_once dirname(__FILE__) . '/../models/UserInfo.php';
// include_once dirname(__FILE__) . '/../models/BlockedUser.php';
// include_once dirname(__FILE__) . '/../../../includes/common/ImageOperations.php';


class BookingManager{
	private $cread;
	private $cwrite;

	public function __construct(){
		$this->cread = new UserReadDao();
		$this->cwrite = new UserWriteDao();
	}

	public function createAndUpdate($params){
		$object = new User();
		$error = "";
		try{
			$object = $this->getObject($params);
			//print_r($object);
			if($object->getValue('uid')==0){
				$object = $this->createObject($params);
			}
			else{
				$this->updateObject($object,$params);
				$object = $this->getObject($params);;
			}
		}
		catch(Exception $ex){ }
		return $object;
	}

	public function getObject($params){
		$user = new User();
		if($params['uid']!=0)	$user = $this->cread->getObject($user,array('uid'=>$params['uid']));
		else if($params['fbid']!="")	$user = $this->cread->getObject($user,array('fbid'=>$params['fbid']));
		else if($params['mobile']!="")	$user = $this->cread->getObject($user,array('mobile'=>$params['mobile'],'cc'=>$params['cc']));
		else if($params['email']!="")	$user = $this->cread->getObject($user,array('email'=>$params['email']));
		return $user;
	}

	public function createObject($params){
		$user = new User();
		$user->setObject($params);
		$user->setValue('uid',Utils::generateUID());
		$user->setValue('info',json_encode($params));
		if($user->getValue('fbid')!="" && $user->getValue('email')!=""){
			$user->setValue('everified',1);
		}
		if($user->getValue('name')!='')	$user->setValue('uname',$this->getUniqueUsername($user->getValue('name')));
		if($user->getValue('curr')==''){
			if($user->getValue('country')!="" && isset(Countries::$COUNTRIES_CURRENCIES[$user->getValue('country')])){
				$user->setValue('curr',Countries::$COUNTRIES_CURRENCIES[$user->getValue('country')]['sym']);
			}
		}
		else{
			if(isset(Countries::$CURRENCIES[$user->getValue('curr')])){
				$user->setValue('curr',Countries::$CURRENCIES[$user->getValue('curr')]);
			}
		}
		$user = $this->cwrite->createObject($user,true);
		$params['uid']=$user->getValue('uid');
		$object = $this->getObject($params);
		return $user;
	}

	public function updateObject($object,$params){
		$changes = array();
		try{

			if(isset($params['mobile']) && $params['mobile']!="" && $params['mobile']!=$object->getValue('mobile'))	$changes['mobile']=$params['mobile'];
			if(isset($params['bio']) && $params['bio']!="" && $params['bio']!=$object->getValue('bio'))	$changes['bio']=$params['bio'];
			if(isset($params['name']) && $params['name']!="" && $params['name']!=$object->getValue('name'))	$changes['name']=$params['name'];
			if(isset($params['cc']) && $params['cc']!="" && $params['cc']!=$object->getValue('cc'))	$changes['cc']=$params['cc'];
			if(isset($params['pic']) && $params['pic']!="" && $params['pic']!=$object->getValue('pic'))	$changes['pic']=$params['pic'];
			if(isset($params['fbid']) && $params['fbid']!="" && $params['fbid']!=$object->getValue('fbid'))	$changes['fbid']=$params['fbid'];
			if(isset($params['mobile']) && $params['mobile']!="" && $params['mobile']!=$object->getValue('mobile'))	$changes['mobile']=$params['mobile'];
			if(isset($params['email']) && $params['email']!="" && $params['email']!=$object->getValue('email'))	$changes['email']=$params['email'];
			if($object->getValue('uname')=="" && $object->getValue('name')!='')	$changes['uname']=$this->getUniqueUsername($object->getValue('name'));
			if(isset($params['country']) && $params['country']!="" && $params['country']!=$object->getValue('country')){
				$changes['country']=$params['country'];
				$changes['curr']=Countries::$COUNTRIES_CURRENCIES[$changes['country']]['sym'];
			}
			if(isset($params['cafes']) && $params['cafes']!="" && $params['cafes']!=$object->getValue('cafes'))	$changes['cafes']=$params['cafes'];
			if(isset($params['availability']) && $params['availability']!="" && $params['availability']!=$object->getValue('availability'))	$changes['availability']=$params['availability'];
			if(isset($params['paypalid']) && $params['paypalid']!="" && $params['paypalid']!=$object->getValue('paypalid'))	$changes['paypalid']=$params['paypalid'];
			if(isset($params['upic']) && $params['upic']!="" && $params['upic']!=$object->getValue('upic')){
				$changes['upic']=$params['upic'];
				$changes['isCdn']=0;
			}
			if(count($changes)){
				$this->cwrite->updateObject($object,$changes);
			}
		}
		catch(Exception $ex){ }
		return $object;
	}

	public function getUserByUid($uid){
		$user = new User();
		$user = $this->cread->getObject($user,array('uid'=>$uid));
		return $user;
	}

	public function getUserInformation($uid){
		$info = new UserInfo2();

		$user = new User();
		$user = $this->cread->getObject($user,array('uid'=>$uid));
		$info->setObject($user->getInfo());
		$vid = $user->getValue('vid');
		//print_r($vid);
		$video = new Video();
		if($vid!=0){
			$video = $this->cread->getObject($video,array('vid'=>$vid));
			$info->setObject($video->getObject());
		}
		return $info->getInfo();
	}

	// public function sendOTP($mobile,$cc){
	// 	$otp = Utils::generateVerificationCode();
	// 	try{
	// 		//if($mobile=="9987045279") $otp = 1111;
	// 		$twof = new OTPManager();
	// 		$otp_model = new Otp();
	// 		$otp_model->setValue('cc',$cc);
	// 		$otp_model->setValue('mobile',$mobile);
	// 		$otp_model->setValue('otp',$otp);

	// 		if($cc == '91') $twof->sendUpcardOTP($mobile,$otp);
	// 		$this->cwrite->createObject($otp_model);
	// 	}
	// 	catch(Exception $ex){ }
	// 	return $otp;
	// }	

	public function getLatestOtps($mobile,$cc){
		$otps = $this->cread->getLatestOtps($mobile,$cc);
		return $otps;
	}

	public function verifyAndCreateUser($mobile,$cc,$otp){
		$isnew = false;
		$error = "";
		$user = new User();
		try{
			$isverified = $this->verifyOTP($mobile,$cc,$otp);
			if($isverified){
				list($user,$isnew,$error) = $this->getAndCreateUser($mobile,$cc,1);
			}
			else{
				$error = "Otp did not match";
			}
		}
		catch(Exception $ex){ }
		return array($user,$isnew,$error);
	}

	public function verifyOTP($mobile,$cc,$otp){
		$isverified = false;
		try{
			$otps = $this->getLatestOtps($mobile,$cc);
			if(in_array($otp,$otps))	$isverified = true;
		}
		catch(Exception $ex){ }
		return $isverified;
	}

	public function getAndCreateUser($mobile,$cc,$ismverified=0){
		$isnew = false;
		$error = "";
		$user = new User();
		try{
			$user = $this->getUserByMobile($mobile,$cc);
			if($user->getValue('uid')==0){
				$user = $this->createUser($user,array('mobile'=>$mobile,'cc'=>$cc,'mverified'=>$ismverified));
				if($user->getValue('uid')!=0){
					$isnew = true;
					$user = $this->getUserByMobile($mobile,$cc);
				}
				else{
					$error = "Create User Failed";
				}
			}
			else{
				$isnew = false;
			}
		}
		catch(Exception $ex){ }
		return array($user,$isnew,$error);
	}

	public function getAndCreateUserInfo($uid,$info,$type){
		$user_info = new UserInfo();
		try{
			$user_info = $this->cread->getObject($user_info,array('uid'=>$uid)); 
			if($user_info->getValue('uid')==0){
				$user_info->setValue('uid',$uid);
				$data = array();
				$data[$type] = $info;
				$user_info->setValue('info',json_encode($data));
				$user_info = $this->cwrite->createObject($user_info,true);
			}
			else{
				$data = array();
				$data_json = $user_info->getValue('info');
				if($data_json!='')	$data = json_decode($data_json,true);
				$data[$type] = $info;
				$user_info->setValue('info',json_encode($data));
				$changes['info'] = $user_info->getValue('info');
				$success = $this->cwrite->updateObject($user_info,$changes);
			}
		}
		catch(Exception $ex){ }
		return $user_info;
	}	

	public function getAndCreateUserByEmail($params){
		$isnew = false;
		$error = "";
		$user = new User();
		try{
			$user = $this->getUserByEmail($params['email']);
			if($user->getValue('uid')==0){
				$user = $this->createUser($user,$params);
				if($user->getValue('uid')!=0){
					$isnew = true;
					$user = $this->getUserByEmail($params['email']);
				}
				else{
					$error = "Create User Failed";
				}
			}
			else{
				$isnew = false;
			}
		}
		catch(Exception $ex){ }
		return array($user,$isnew,$error);
	}

	public function createUser($user,$params){
		$user->setValue('uid',Utils::generateUID());
		if(isset($params['email'])) $user->setValue('email',$params['email']);
		if(isset($params['email_verified'])) $user->setValue('everified',1);
		if(isset($params['name'])) $user->setValue('name',$params['name']);
		else{
			$name='';
			if(isset($params['given_name']))	$name.=$params['given_name'] . ' ';
			if(isset($params['family_name']))	$name.=$params['family_name'] ;
			$name = trim($name);
			$user->setValue('name',$name);
		}

		if($user->getValue('name')!='')	$user->setValue('uname',$this->getUniqueUsername($user->getValue('name')));
		else{
			$temp = explode("@",$user->setValue('email'));
			$name = $temp[0];
			$user->setValue('uname',$this->getUniqueUsername($name));
		}

		if(isset($params['picture'])){
			$pic = $this->saveTempPic($params['picture']);
			$user->setValue('pic',$pic);
		}

		if(isset($params['bio'])) $user->setValue('bio',$params['bio']);
		if(isset($params['aboutme'])) $user->setValue('aboutme',$params['aboutme']);
		if(isset($params['designation'])) $user->setValue('designation',$params['designation']);
		$user = $this->cwrite->createObject($user,true);
		return $user;
	}

	public function getUniqueUsername($title){
		//print_r("#".$title."#");
		$title=Utils::removeSpecialChars($title);
		$title = strtolower($title);
		$title = str_replace(" ","-",$title);
		//print_r("#".$title."#");
		$url_titles = $this->cread->getUsernames($title);
		$url_title_count = count($url_titles);
		if($url_title_count>0){
			for($i=$url_title_count;$i<=($url_title_count+100);$i++){
				$alias = $title . "-" . $i;
				if(!in_array($alias,$url_titles)){
					$title = $alias;
					break;
				}
			}
		}
		//print_r("#".$title."#");
		return $title;
	}

	public function updateUser($params){
		//print_r("hello");
		$user = new User();
		$changes = array();
		$error="";
		if($params['uid']==0){
			$user = $this->getUserByMobile($params['mobile'],$params['cc']);
			$params['uid']=$user->getValue('uid');
		}
		else{
			$user = $this->getUser($params['uid']);
			$params['uid']=$user->getValue('uid');
		}
		//print_r($params);
		if($params['uid']!=0){
			if(isset($params['profile_pic_temp']) && $params['profile_pic_temp']!=""){
				$path = $this->saveProfilePic($params['profile_pic_temp']);
				if($path!=$user->getValue('pic'))	$changes['pic']=$path;
			}

			if(isset($params['cc']) && $params['cc']!="" && $params['cc']!=$user->getValue('cc'))	$changes['cc']=$params['cc'];
			if(isset($params['mobile']) && $params['mobile']!="" && $params['mobile']!=$user->getValue('mobile'))	$changes['mobile']=$params['mobile'];
			if(isset($params['name']) && $params['name']!="" && $params['name']!=$user->getValue('name'))	$changes['name']=$params['name'];
			if(isset($params['email']) && $params['email']!="" && $params['email']!=$user->getValue('email'))	$changes['email']=$params['email'];
			if(isset($params['bio']) && $params['bio']!="" && $params['bio']!=$user->getValue('bio'))	$changes['bio']=$params['bio'];
			if(isset($params['aboutme']) && $params['aboutme']!="" && $params['aboutme']!=$user->getValue('aboutme'))	$changes['aboutme']=$params['aboutme'];
			if(isset($params['designation']) && $params['designation']!="" && $params['designation']!=$user->getValue('designation'))	$changes['designation']=$params['designation'];

			$uname = $user->getValue('uname');
			if($uname==''){
				if(isset($changes['name']))	$uname = $this->getUniqueUsername($changes['name']);
				if($uname=='' && $user->getValue('name')!='')	$uname = $this->getUniqueUsername($user->getValue('name'));
				if($uname!='')	$changes['uname']=$uname;
			}
			//print_r($changes);
			if(count($changes)>0){
				$success = $this->cwrite->updateObject($user,$changes);
			}
		}
		else{
			$error = "Invalid User";
		}
		$user = $this->getUser($params['uid']);
		return array($user,$error);
	}

	// public function getUser($uid){
	// 	$user = new User();
	// 	$user = $this->cread->getObject($user,array('uid'=>$uid));
	// 	return $user;
	// }

	public function getUserByMobile($mobile,$cc){
		$user = new User();
		$user = $this->cread->getObject($user,array('mobile'=>$mobile,'cc'=>$cc)); 
		return $user;
	}

	public function getUserByFbId($fbid){
		$user = new User();
		$user = $this->cread->getObject($user,array('fbid'=>$fbid)); 
		return $user;
	}	

	public function getUserByUsename($uname){
		$user = new User();
		$user = $this->cread->getObject($user,array('uname'=>$uname)); 
		return $user;
	}	

	public function getUserByEmail($email){
		$user = new User();
		$user = $this->cread->getObject($user,array('email'=>$email)); 
		return $user;
	}

	public function getUserInfo($usermodel){
		if($user->getValue('uid')!=0)	$user = $this->getUser($user->getValue('uid'));
		else if($user->getValue('fbid')!="")	$user = $this->getUserByFbId($user->getValue('fbid'));
		else{
			$user = $this->getUserByMobile($user->getValue('mobile'));
		}
		return $user;
	}

	private function getFileName($path){
		$temp = explode("/",$path);
		$filename = $temp[count($temp)-1];
		$temp2 = explode(".",$filename);
		$filename = $temp2[0];
		return $filename;
	}

	public function saveTempPic($url){
		$tmp_dir = '/tmp/phantom/profile/';
		$imgopobj = new ImageOperations($tmp_dir);
		$imagename = $tempname=Utils::generateUID();
		if(copy(str_replace(' ', "%20", $url),$tmp_dir.$imagename));
		$imageextension = $imgopobj->getImageExtension($tmp_dir.$imagename);
		$path = $tmp_dir.$imagename.$imageextension;
		rename('' . $tmp_dir.$tempname,'' . $path);
		$res = chmod($path, 0777);
		$cdnpath = $this->saveProfilePic($path);
		return $cdnpath;
	}	

	public function saveProfilePic($tmp_path){
		$tmp_dir = '/tmp/phantom/profile/';
		$imgopobj = new ImageOperations($tmp_dir);
		$s3obj = new S3Wrapper();
		$cdnpath = '';
		try{
			$filename = $this->getFileName($tmp_path);
			$imageextension=$imgopobj->getImageExtension($tmp_path);
			$tempname=$imgopobj->resize($tmp_path,'profile_' .$filename,500,0);
			if($tempname!="")	unlink($tmp_path);

			$filename = $filename . $imageextension;
			$tmp_path = $tmp_dir . $tempname;

			$args = array('CacheControl'=>'max-age=2592000');
			$cdnpath = 'pm-profile/' . $filename;
			if($s3obj->uploadFile('jalwa-app',$cdnpath,$tmp_path,'',$args)){
				unlink($tmp_path);
			}
			
		}
		catch(Exception $ex){ }
		// print_r('https://jalwa-app.s3.ap-south-1.amazonaws.com/' . $cdnpath);
		return $cdnpath;
	}

	public function getAllUsers(){
		$user = new User();
		$list = $this->cread->getList($user,array('isactive'=>1));
		return $list;
	}

	public function getUsersBasicInfo($uids){
		$users = array();
		$user = new BasicUserInfo();
		$list = $this->cread->getListByIds($user,$uids);
		foreach ($list as $uid => $obj) {
			$user = new BasicUserInfo();
			$user->setObject($obj);
			array_push($users,$user->getInfo());
		}
		return $users;
	}

	public function getNotificationToken($uid){
		return $this->cread->getNotifToken($uid);
	}

	public function createUserReferrer($params){
		$user_referrer = new UserReferrer();
		$user_referrer->setObject($params);
		$user_referrer = $this->cwrite->createObject($user_referrer,true);
		return $user_referrer;
	}

	public function bookNow($params){
		$booking = new Booking();
		$booking->setObject($params);
		$booking = $this->cwrite->createObject($booking,false);
		return $booking;
	}

	public function getBookings($uid){
		$booking = new Booking();
		$list1 = $this->cread->getList($booking,array('provider'=>$uid));
		$list2 = $this->cread->getList($booking,array('taker'=>$uid));
		$list = array_values(array_merge($list1,$list2));
		//print_r($list);
		$uids = array();
		foreach ($list as $booking) {
			array_push($uids,$booking['provider']);
			array_push($uids,$booking['taker']);
		}
		$uids = array_values(array_unique($uids));
		$user = new BasicUserInfo();
		$users = $this->cread->getListByIds($user,$uids);

		//print_r($list);

		$bookings = array();
		foreach ($list as $booking) {
			if(isset($users[$booking['provider']]) && isset($users[$booking['taker']])){
				$bookingInfo = new BookingInfo();
				$bookingInfo->setValue('id',$booking['id']);
				$provider = $users[$booking['provider']];
				$taker = $users[$booking['taker']];

				$bookingInfo->setValue('provider',$provider['uid']);
				$bookingInfo->setValue('pname',$provider['name']);
				//$bookingInfo->setValue('provider_pic',$provider['pic']);

				if(!strpos($provider['upic'],"googleusercontent.com")){
					$bookingInfo->setValue('ppic',Constants::$VIDEO_NON_CDN_PATH . $provider['upic']."?alt=media");
				}
				else{
					$bookingInfo->setValue('ppic',$provider['upic']);
				}

				$bookingInfo->setValue('taker',$taker['uid']);
				$bookingInfo->setValue('tname',$taker['name']);
				//$bookingInfo->setValue('taker_pic',$taker['pic']);

				if(!strpos($taker['upic'],"googleusercontent.com")){
					$bookingInfo->setValue('tpic',Constants::$VIDEO_NON_CDN_PATH . $taker['upic']."?alt=media");
				}
				else{
					$bookingInfo->setValue('tpic',$taker['upic']);
				}

				$bookingInfo->setValue('availability',$booking['availability']);
				$bookingInfo->setValue('locations',$booking['cafes']);

				$bookingInfo->setValue('isConfirm',$booking['provider_confirm']);
				$bookingInfo->setValue('code',$booking['code']);

				$bookingInfo->setValue('booking_date',$booking['created_on']);
				array_push($bookings,$bookingInfo->getObject());
			}
		}
		return $bookings;
	}

}
?>