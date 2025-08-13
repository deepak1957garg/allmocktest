<?php
include_once dirname(__FILE__) . '/../dao/VideoReadDao.php';
include_once dirname(__FILE__) . '/../dao/VideoWriteDao.php';
//include_once dirname(__FILE__) . '/../dao/UserReadDao.php';
include_once dirname(__FILE__) . '/../models/HVideo.php';
include_once dirname(__FILE__) . '/../models/HVideoInfo.php';
include_once dirname(__FILE__) . '/../models/HVideoUser.php';
include_once dirname(__FILE__) . '/../models/Video.php';

class VideoManager{
	private $vreadobj;
	private $vwriteobj;
	private $ureadobj;

	public function __construct(){
		$this->vreadobj = new VideoReadDao();
	 	$this->vwriteobj = new VideoWriteDao();
	 	$this->ureadobj = new UserReadDao();
	}



	public function getVideoData($vid,$uid=0){
		$vlist = $this->vreadobj->getVideoList(array('vids'=>"$vid",'active'=>0));
		$video =  $vlist[0];
		$video_obj = new HVideo();
		$video_info_obj = $this->getVideoInfo($video);
			
		$video_obj->setVideoInfo($video_info_obj);

		$video_obj->setValue('vid',$video['vid']);
		if($video['iscdn']==0)	$video_obj->setValue('path', 'https://firebasestorage.googleapis.com/v0/b/sonder-app-01.firebasestorage.app/o/' . $video['path']);
		else 	$video_obj->setValue('path', 'https://static.thingsapp.co' . $video['path']);
		if($video['thumb']!="")		$video_obj->setValue('pic','https://static.thingsapp.co' . $video['thumb']);
		if($video['firstpic']!="")	$video_obj->setValue('fpic','https://static.thingsapp.co' . $video['firstpic']);
		$video_obj->setValue('status',$video['vstatus']);
		$video_obj->setValue('duration',$video['duration']);
		$video_obj->setValue('num_tips',$video['num_tips']);
		$video_obj->setValue('coll',0);
		$video_obj->setValue('sale_status',1);
		$video_obj->setValue('sale_amt',$video['sale_amount']);

		$user_video_stats = $this->ureadobj->getUserVideoSpentList($uid,array($vid));
		if(isset($user_video_stats[$video['vid']])) $video_obj->setValue('user_tip',$user_video_stats[$video['vid']]->getValue('points_spent'));
		else $video_obj->setValue('user_tip',0);

		$uids = array();
		$tip_ids = array();
		array_push($uids,$video['uid']);
		array_push($uids,$video['owner']);
		// if($video['first_tip']!=0)	array_push($tip_ids,$video['first_tip']);
		// if($video['last_tip']!=0)	array_push($tip_ids,$video['last_tip']);
		// if($video['top_tip']!=0)	array_push($tip_ids,$video['top_tip']);
		
		$tip_ids = array_values(array_unique($tip_ids));
		list($tips,$tips_uids) = $this->vreadobj->getTipsWithUid($tip_ids);
		$uids = array_merge($uids,$tips_uids);
		$uids = array_values(array_unique($uids));
		$users = $this->vreadobj->getUsers($uids);


		if($video['owner']!=0 && isset($users[$video['owner']])){
			$user_obj = $this->getVUser($video['vid'],$users[$video['owner']],"owner","0",'Owner');
			$video_obj->setVUser($user_obj);
		}

		if($video['uid']!=0 && isset($users[$video['uid']])){
			$user_obj = $this->getVUser($video['vid'],$users[$video['uid']],"director","0",'Director');
			$video_obj->setVUser($user_obj);
		}

		// if($video['first_tip']!=0){
		// 	if(isset($tips[$video['first_tip']])){
		// 		$uid = $tips[$video['first_tip']]->getValue('uid');
		// 		if($uid!=0 && isset($users[$uid])){
		// 			$user_obj = $this->getVUser($video['vid'],$users[$uid],"first_tipper",$tips[$video['first_tip']]->getValue('points'),'1st');
		// 			$video_obj->setVUser($user_obj);
		// 		}
		// 	}
		// }

		// if($video['last_tip']!=0){
		// 	if(isset($tips[$video['last_tip']])){
		// 		$uid = $tips[$video['last_tip']]->getValue('uid');
		// 		if($uid!=0 && isset($users[$uid])){
		// 			$user_obj = $this->getVUser($video['vid'],$users[$uid],"latest_tipper",$tips[$video['last_tip']]->getValue('points'),'Now');
		// 			$video_obj->setVUser($user_obj);
		// 		}
		// 	}
		// }

		// if($video['top_tip']!=0){
		// 	if(isset($tips[$video['top_tip']])){
		// 		$uid = $tips[$video['top_tip']]->getValue('uid');
		// 		if($uid!=0 && isset($users[$uid])){
		// 			$user_obj = $this->getVUser($video['vid'],$users[$uid],"top_tipper",$video['top_tip_amount'],'Top');
		// 			$video_obj->setVUser($user_obj);
		// 		}
		// 	}
		// }
		$video_obj->setCategory(2,'Comedy','comedy');
		return $video_obj->getObject();
	}

	private function getVideoInfo($video){
		$video_info_obj = new HVideoInfo();
		$video_info_obj->setValue('vid',$video['vid']);
		$video_info_obj->setValue('vname',$video['vname']);
		//$video_info_obj->setValue('vurl',$video['vurl']);
		$video_info_obj->setValue('vurl',$video['vid']);
		//$video_info_obj->setValue('vactors',$video['vactors']);
		//$video_info_obj->setValue('vmessage',$video['vmessage']);
		$video_info_obj->setValue('vdesc',$video['vdesc']);
		$video_info_obj->setValue('isold',$video['isold']);
		$video_info_obj->setValue('issold',$video['issold']);
		$video_info_obj->setValue('sale_amount',$video['sale_amount']);
		return $video_info_obj;
	}

	private function getVUser($vid,$user,$type,$tip_amt,$caption){
		$vuser = new HVideoUser();
		$vuser->setValue('uid',$user['uid']);
		$vuser->setValue('name',$user['name']);
		$vuser->setValue('pic','https://jalwa-app.s3.ap-south-1.amazonaws.com' . $user['pic']);
		$vuser->setValue('uname',$user['uname']);
		$vuser->setValue('type',$type);
		$vuser->setValue('caption',$caption);
		$vuser->setValue('tip_amt',$tip_amt);
		return $vuser;
	}

	public function getNonCdnVideoList(){
		$list = array();
		$arr = $this->vreadobj->getNonCdnVideoList();
		foreach($arr as $obj){
			$video = new VideoModel();
			$video->setObject($obj);
			array_push($list,$video);
		}
		return $list;
	}

	public function updateCdnStatus($vid,$status){
		$this->vwriteobj->updateCdnStatus($vid,$status,1);
	}

}
?>