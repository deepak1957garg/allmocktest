<?php
include_once dirname(__FILE__) . '/../dao/VideoReadDao.php';
include_once dirname(__FILE__) . '/../dao/VideoWriteDao.php';
include_once dirname(__FILE__) . '/../dao/UserReadDao.php';
include_once dirname(__FILE__) . '/../models/HVideo.php';
include_once dirname(__FILE__) . '/../models/HVideoInfo.php';
include_once dirname(__FILE__) . '/../models/HVideoUser.php';
include_once dirname(__FILE__) . '/../models/VideoModel.php';

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
			
		// $video_obj->setVideoInfo($video_info_obj);

		// $video =  $vlist[$i];
		// 	//print_r($video);
			$video_obj = new HVideo();
			$video_obj->setObject($video);

		$video_obj->setValue('vid',$video['vid']);
		if($video['iscdn']==0)	$video_obj->setValue('path', 'https://firebasestorage.googleapis.com/v0/b/sonder-app-01.firebasestorage.app/o/' . $video['path']."?alt=media");
		else 	$video_obj->setValue('path', 'https://static.thingsapp.co' . $video['path']);
		if($video['thumb']!="")		$video_obj->setValue('pic','https://static.thingsapp.co' . $video['thumb']);
		if($video['firstpic']!="")	$video_obj->setValue('fpic','https://static.thingsapp.co' . $video['firstpic']);
		if($video['gif']!="")	$video_obj->setValue('gif','https://static.thingsapp.co' . $video['gif']);
			$video_obj->setValue('status',1);
			$video_obj->setValue('duration',30);
			$video_obj->setValue('num_tips',$video['num_tips']);
			$video_obj->setValue('coll',0);
			//$video_obj->setValue('sale_status',$video['sstatus']);
			$video_obj->setValue('sale_amt',$video['sale_amount']);
			$video_obj->setValue('spath', '');




			list($vids,$uids,$endorselist) = $this->getVidAndTipIds(array($video_obj->getObject()));


		if($video['uid']!=0 && isset($users[$video['uid']])){
				$video_obj->setvalue('uname',$users[$video['uid']]['name']);
				$video_obj->setvalue('upic',Constants::$VIDEO_NON_CDN_PATH . $users[$video['uid']]['pic']."?alt=media");
				$video_obj->setvalue('umobile',"+".$users[$video['uid']]['cc'].$users[$video['uid']]['mobile']);
				// $user_obj = $this->getVUser($video['vid'],$users[$video['uid']],"owner","0",'owner');
				// $video_obj->setVUser($user_obj);
			}

			if(isset($endorselist[$video['vid']])){
				foreach($endorselist[$video['vid']] as $endorse_uid){
					if(isset($users[$endorse_uid])){
						$user_obj = $this->getVUser($video['vid'],$users[$endorse_uid],"endorser","0",'endorser');
						$video_obj->setVUser($user_obj);
					}
				}
			}


		return $video_obj->getObject();
	}

	private function getVidAndTipIds($vlist){
		$vreadobj = new VideoReadDao();
		$vids = array();
		$uids = array();
		$tip_ids = array();
		$endorselist = array();
		foreach($vlist as $video){
			array_push($vids,$video['vid']);
			array_push($uids,$video['uid']);
			// array_push($uids,$video['owner']);
			// if($video['first_tip']!=0)	array_push($tip_ids,$video['first_tip']);
			// if($video['last_tip']!=0)	array_push($tip_ids,$video['last_tip']);
			// if($video['top_tip']!=0)	array_push($tip_ids,$video['top_tip']);
		}
		if(count($vids)>0){
			$endorses = $vreadobj->getEventList($vids,'endorse',1);
			foreach($endorses as $endorse){
				array_push($uids,$endorse['uid']);
				if(!isset($endorselist[$endorse['vid']]))	$endorselist[$endorse['vid']] = array();
				if(count($endorselist[$endorse['vid']])<3)	array_push($endorselist[$endorse['vid']],$endorse['uid']);
				// array_push($uids,$video['owner']);
				// if($video['first_tip']!=0)	array_push($tip_ids,$video['first_tip']);
				// if($video['last_tip']!=0)	array_push($tip_ids,$video['last_tip']);
				// if($video['top_tip']!=0)	array_push($tip_ids,$video['top_tip']);
			}
		}
		//$tip_ids = array_values(array_unique($tip_ids));
		$uids = array_values(array_unique($uids));
		return array($vids,$uids,$endorselist);
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
		$video_info_obj->setValue('itype',$video['itype']);
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

	public function getNonCdnPicList(){
		$arr = $this->vreadobj->getNonCdnPicList();
		return $arr;
	}	

	public function updateCdnStatus($vid,$status){
		$this->vwriteobj->updateCdnStatus($vid,$status,1);
	}

	public function updateUpicCdnStatus($uid,$status){
		$this->vwriteobj->updateUpicCdnStatus($uid,$status);
	}

	public function updateUpicAndThumb($uid,$pic,$thumb,$status){
		$this->vwriteobj->updateUpicAndThumb($uid,$pic,$thumb,$status);
	}	

	public function updateUserVideo($uid,$vid){
		$this->vwriteobj->updateUserVideo($uid,$vid);
	}	

	public function updateGifPath($vid,$path){
		$this->vwriteobj->updateGifpath($vid,$path);
	}

	public function updateImagesPath($vid,$webpath,$thumb,$firstpic){
		$this->vwriteobj->updateImagesPath($vid,$webpath,$thumb,$firstpic);
	}
}
?>