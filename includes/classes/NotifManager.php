<?php
include_once dirname(__FILE__) . '/../config/Config.php';
include_once dirname(__FILE__) . '/../dao/UserReadDao.php';
include_once dirname(__FILE__) . '/../dao/VideoReadDao.php';
include_once dirname(__FILE__) . '/../dao/UserWriteDao.php';
include_once dirname(__FILE__) . '/../models/Notif.php';
include_once dirname(__FILE__) . '/../utils/SCCURL.php';

class NotifManager{
	private $vreadobj;
	private $ureadobj;
	private $uwriteobj;

	public function __construct(){
		$this->ureadobj = new UserReadDao();
		$this->uwriteobj = new UserWriteDao();
		$this->vreadobj = new VideoReadDao();
	}

	public function addNotif($uid,$vid,$type){
		$notif = new Notif();

		$notif->setValue('uid',$uid);
		$notif->setValue('vid',$vid);
		$notif->setValue('ntype',$type);

		$this->uwriteobj->addNotif($notif);
		$running_process = shell_exec("ps auxwww|grep \"send_notification.php\"|grep -v grep");
		if(empty($running_process)) 	exec("php ".Config::$ROOT_DIR."/cron/send_notification.php >/dev/null 2>/dev/null &");

		return $notif->getObject();
	}

	function getUnsentNotifs(){
		$notifs = $this->ureadobj->getUnsentNotifs();
		return $notifs;
	}

	function sentNotification($notif){
		$token_obj = $this->ureadobj->getNotifToken($notif->getValue('uid'));
		$vlist = $this->vreadobj->getVideoList(array('vids'=>$notif->getValue("vid")));
		if(count($vlist)>0){
			$video = $vlist[0];

			$arr = array();
			$arr['registration_ids'] = array();
			$arr['data'] = array();
			if($token_obj['token']!=null or $token_obj['token']!="")	array_push($arr['registration_ids'],$token_obj['token']);
			if($notif->getValue('ntype')=='add_tip'){
				$arr['data']['title'] = "Your Video got a tip";
				$arr['data']['body'] = "Your Video " . $video['vname'] . " got a tip";
			}
			else if($notif->getValue('ntype')=='approved'){
				$arr['data']['title'] = "Your video is approved";
				$arr['data']['body'] = "Your Video " . $video['vname'] . " is approved";
			}
			else if($notif->getValue('ntype')=='rejected'){
				$arr['data']['title'] = "Your video is rejected";
				$arr['data']['body'] = "Your Video " . $video['vname'] . " is rejected";
			}
			else if($notif->getValue('ntype')=='sold'){
				$arr['data']['title'] = "Your video is sold";
				$arr['data']['body'] = "Your Video " . $video['vname'] . " is sold";
			}
			else if($notif->getValue('ntype')=='latest_replaced'){
				$arr['data']['title'] = "You have been replaced as the latest tipper";
				$arr['data']['body'] = "You have been replaced as the latest tipper for Video " . $video['vname'];
			}
			else if($notif->getValue('ntype')=='top_replaced'){
				$arr['data']['title'] = "You have been replaced as the top tipper";
				$arr['data']['body'] = "You have been replaced as the top tipper for Video " . $video['vname'];
			}
			$arr['data']['vid'] = $video['vid'];
			$arr['data']['page'] = 'video';

			$vars = json_encode($arr);
			$headers = [
			    'Authorization: key=AAAAnnGcNmI:APA91bE3Op6Hmc_DgIHSzCd9beMrwcxjp2znjDa3qCOuJenlu-lfuZH4jcn0QNFcYkYToZaUVrN6pxNjjag-qdWNQDgfk58Jq4S5q4P1OdVOjqNR4p3Uo_7u0hKwwEn3OJHaA2blMzgP',
			    'Content-Type:"application/json'
			];

			$headers = array( 
							'Authorization: key=AAAAnnGcNmI:APA91bE3Op6Hmc_DgIHSzCd9beMrwcxjp2znjDa3qCOuJenlu-lfuZH4jcn0QNFcYkYToZaUVrN6pxNjjag-qdWNQDgfk58Jq4S5q4P1OdVOjqNR4p3Uo_7u0hKwwEn3OJHaA2blMzgP',
							'Content-Type: application/json'
						);


			if(count($arr['registration_ids'])>0){
				$sccurlobj = new SCCURL("https://fcm.googleapis.com/fcm/send");
				$sccurlobj->setHeaders($headers);
				$result = $sccurlobj->postBody($vars);
				if(!$result) {
					$error=$sccurlobj->fetchError();
					error_log('Curl error : fn sendAndroidNotificationByCurl class AndroidPushManager : error :' . print_r($error,1) . ' postdata : ' . print_r($postdata,1));
				}
				else{
					// print_r($result);
				}
				$notif->setValue('is_sent',1);
			}
			else 	$notif->setValue('is_sent',2);


			$notif->setValue('req',$vars);
			$notif->setValue('res',$result);
			
			$this->uwriteobj->updateNotif($notif);
			
		}
	}

}
?>