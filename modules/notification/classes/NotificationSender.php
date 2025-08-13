<?php
$dirname = dirname(__FILE__);
$dirname = str_replace("/modules/notification/classes", "",$dirname);
$dirname2 = str_replace("/sonder", "/things-api",$dirname);
//$dirname2 = str_replace("/sonder-web", "/things-web",$dirname);
//print($dirname2 . '/vendor/autoload.php');
require_once $dirname2 . '/vendor/autoload.php';
//require_once dirname(__FILE__) . '/../../../vendor/autoload.php';
//use Google\Cloud\Messaging\CloudMessage;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
include_once dirname(__FILE__) . '/../dao/NotificationReadDao.php';
include_once dirname(__FILE__) . '/../dao/NotificationWriteDao.php';
include_once dirname(__FILE__) . '/../models/Notification.php';
include_once dirname(__FILE__) . '/../models/UserNotification.php';
include_once dirname(__FILE__) . '/../models/ChatMessage.php';
include_once dirname(__FILE__) . '/../classes/NotificationTemplates.php';
include_once dirname(__FILE__) . '/../../user/classes/UserInfoManager.php';

class NotificationSender{
	private $cread;
	private $cwrite;
	private $messaging;
	private $uinfomanager;

	public function __construct(){
		$this->cread = new NotificationReadDao();
		$this->cwrite = new NotificationWriteDao();
		$this->uinfomanager = new UserInfoManager();

		$root_dir = str_replace("/modules/notification/classes", "",__DIR__);

		$factory = (new Factory)->withServiceAccount($root_dir . "/includes/config/sonder-app-01-firebase-adminsdk-7hrcu-851f825ef8.json");

		$this->messaging = $factory->createMessaging();
	}


	function sentNotification($obj){
		print_r($obj);
		$info = json_decode($obj->getValue('info'),true);
		$template = NotificationTemplates::$TEMPLATES[$obj->getValue('template')];
		//print_r($info);
		// $title = $info['title'];
		$body = $template['text'];
		foreach($info as $key=>$value){
			$body = str_replace('{' . $key . '}',$value,$body);
		}
		print_r(NotificationTemplates::$TEMPLATES[$obj->getValue('template')]);
		print_r($body);
		$pic = "";
		$title = "";
		$name = "";
		$sender="";
		if($template['users']=="seller"){
			$title = $info['customerName'];
			$name = $info['customerName'];
			$pic = $info['customerPic'];
			$sender = $info['customer'];
		}
		else if($template['users']=="customer"){
			$title = $info['sellerName'];
			$name = $info['sellerName'];
			$pic = $info['sellerPic'];
			$sender = $info['seller'];
		}

		$notification = Notification::fromArray([
		    'title' => $title,
		    'body' => $body,
		    'image' => $pic,
		]);

		//print_r($notification);

		$nid = $obj->getValue('nid');
		$device_tokens = array();
		if($nid!=0){
			$uids = $this->cread->getNotificationUids($nid);
			//print_r($uids);
			if(count($uids)>0){
				$device_tokens = $this->cread->getUserDeviceTokens($uids);
			}
		}

		foreach($device_tokens as $device_token){
			try{
				$message = CloudMessage::fromArray([
				    'token' => $device_token,
				    'notification' => $notification, // optional
				    'data' => array('page'=>$obj->getValue('page'),'iid'=>$obj->getValue('iid'),'template'=>$obj->getValue('template'),"name"=>$name,"pic"=>$pic,"sender"=>$sender), // optional
				]);
				//print_r("indidee1");
				$this->messaging->send($message);
			}
			catch(Exception $ex){ 
				//error_log(print_r($ex,1));
			}
		}

		$this->cwrite->setNotificationIsProcessed($nid,1);
		$this->cwrite->setNotificationIsSent($nid,1);
	}

	function sentChatNotification($params){
		$chat_message = new ChatMessage();
		$chat_message->setObject($params);

		$user = $this->uinfomanager->getObject(array('uid'=>$chat_message->getValue('sender')));

		$title = $user->getValue("name");
		if($title=="")	$title = "New message";
		$body = $chat_message->getValue("message");
		if($body==""){
			$body = $chat_message->getValue("vname");
			if($body==""){
				$body = "You have new message from " . $user->getValue("name");
			}
		}
		if(strlen($body)>70)	$body=substr($body,0,70) . "...";
		$pic = Constants::$VIDEO_NON_CDN_PATH . str_replace("profile/","profile%2F",$user->getValue("pic")) ."?alt=media";

		$notification = Notification::fromArray([
		    'title' => $title,
		    'body' => $body
		]);

		$image_arr = array();
		$image_arr["notification"] = array();
		$image_arr["notification"]["image"] = $pic;

		$token_obj = $this->uinfomanager->getNotificationToken($chat_message->getValue('receiver'));
		if(isset($token_obj['token']) && $token_obj['token']!="" && $user->getValue('uid')!=0){
			try{
				$message = CloudMessage::fromArray([
					'token' => $token_obj['token'],
					'notification' => $notification, // optional
//					'android' => $image_arr,
					'data' => array('page'=>'chat','mid'=>$chat_message->getValue("msgId"),'sender'=>$user->getValue('uid'),'pic'=>$user->getValue('pic'),'name'=>$user->getValue('name')), // optional
				]);
				$s1 = $this->messaging->send($message);
			}
			catch(Exception $ex){ 
				error_log(print_r($ex,1));
			}
		}
	}

}
// $notificationSender = new NotificationSender();
// $notificationSender->sentNotification();
?>