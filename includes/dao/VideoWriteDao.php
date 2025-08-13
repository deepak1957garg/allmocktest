<?php
include_once dirname(__FILE__) . '/../db/DBWrapper.php';

class VideoWriteDao{

	function __construct(){
	}

	public function saveLocation($uid,$data){
		$id = 0;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$params = array('location'=>json_encode($data));
			$q = sprintf("insert into jw_user_location (uid,location,created_on,updated_on) values (%d,'{location}',now(),now())",$uid);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$params);
			if($success && $num_affected_rows>0)	$id = DBWrapper::getMysqlLastInsertId($dbinfo);
		}
		catch(Exception $ex){ }
		return $id;
	}

	public function addEvent($vid,$uid,$event,$value){
		$id=0;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$params = array('event'=>$event,'data'=>$value);
			$q = sprintf("insert into jw_events (vid,uid,event,data,created_on,updated_on) values (%d,%d,'{event}','{data}',now(),now())",$vid,$uid);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$params);
			if($success && $num_affected_rows>0)	$id=DBWrapper::getMysqlLastInsertId($dbinfo);
		}
		catch(Exception $ex){ }
		return $id;
	}

	public function addUserEvent($uid,$event,$value){
		$id=0;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$params = array('event'=>$event,'data'=>$value);
			$q = sprintf("insert into jw_user_events (uid,event,data,created_on,updated_on) values (%d,'{event}','{data}',now(),now())",$uid);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$params);
			if($success && $num_affected_rows>0)	$id=DBWrapper::getMysqlLastInsertId($dbinfo);
		}
		catch(Exception $ex){ }
		return $id;
	}

	public function addUserNotifications($nid,$uids){
		$id=0;
		try{
			$str = "";
			$dbinfo =  DBWrapper::getDBInfoObject('notification');
			foreach ($uids as $uid) {
				$str.="(" . $nid . "," . $uid . "),";
			}
			$str = trim($str,",");
			$q = sprintf("insert into jw_user_notifications (nid,uid) values %s",$str);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,array());
		}
		catch(Exception $ex){ }
		return $id;
	}	

	public function updateNumTips($vid,$count){
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$params = array();
			$q = sprintf("Update jw_video_stats set num_tips=%d where vid=%d",$count,$vid);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$params);
		}
		catch(Exception $ex){ }
	}

	public function updateSoldStatus($vid,$status){
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$params = array();
			$q = sprintf("Update jw_videos set issold=%d where vid=%d",$status,$vid);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$params);
		}
		catch(Exception $ex){ }
	}

	public function updateRemoveStatus($vid,$reason){
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$params = array();
			$q = sprintf("Update jw_videos set is_active=0,vstatus=%d where vid=%d",$reason,$vid);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$params);
		}
		catch(Exception $ex){ }
	}			

	public function editEvent($vid,$uid,$event,$value){
		$id=0;
		try{
			$params = array('event'=>$event,'data'=>$value);
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("Update jw_events set data='{data}',updated_on=now() where  vid=%d and uid=%d and event='{event}'",$vid,$uid);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$params);
		}
		catch(Exception $ex){ }
		return $id;
	}


	public function createBuy($vid,$uid){
		$id=0;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("insert into jw_buys (vid,uid,created_on,updated_on) values (%d,%d,now(),now())",$vid,$uid);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$tip->getObject());
			if($success && $num_affected_rows>0)	$id=DBWrapper::getMysqlLastInsertId($dbinfo);
		}
		catch(Exception $ex){ }
		return $id;
	}

	public function updateTip($tip){
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("Update jw_tips set is_active=%d where vid=%d and uid=%d,updated_on=now()",
				$tip->getValue('is_active'),
				$tip->getValue('vid'),
				$tip->getValue('uid')
			);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$tip->getObject());
		}
		catch(Exception $ex){ }
		return $tip;
	}	

	public function addVideoStats($stats){
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("insert into jw_video_stats (vid,points,num_tips,points_paid,num_tips_paid,num_user_tips,first_tip,last_tip,created_on,updated_on) values (%d,%d,%d,%d,%d,%d,%d,%d,now(),now())",
				$stats->getValue('vid'),
				$stats->getValue('points'),
				$stats->getValue('num_tips'),
				$stats->getValue('points_paid'),
				$stats->getValue('num_tips_paid'),
				$stats->getValue('num_user_tips'),
				$stats->getValue('first_tip'),
				$stats->getValue('last_tip')
			);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$stats->getObject());
		}
		catch(Exception $ex){ }
		return $stats;
	}

	public function addVideo($video,$iscdn){
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("insert into jw_videos (uid,vname,vdesc,slug,path,old_path,iscdn,size,duration,thumb,firstpic,width,height,vstatus,sale_amount,isold,issold,itype,curr,is_active,created_on,updated_on) values (%d,'{vname}','{vdesc}','{slug}','{path}','{old_path}',%d,%d,%d,'{thumb}','{firstpic}',%d,%d,%d,%d,%d,%d,%d,'%s',%d,now(),now())",
				$video->getValue('uid'),
				$video->getValue('iscdn'),
				$video->getValue('size'),
				$video->getValue('duration'),
				$video->getValue('width'),
				$video->getValue('height'),
				$video->getValue('vstatus'),
				$video->getValue('sale_amount'),
				$video->getValue('isold'),
				$video->getValue('issold'),
				$video->getValue('itype'),
				$video->getValue('curr'),
				$video->getValue('is_active')
			);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$video->getObject());
			if($success && $num_affected_rows>0)	$video->setValue('vid',DBWrapper::getMysqlLastInsertId($dbinfo));
		}
		catch(Exception $ex){ }
		return $video;
	}

	public function updateVideoStats($stats,$changes){
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("Update jw_video_stats set ");
			foreach($changes as $key => $value){
				$q.=sprintf("%s='{%s}',",$key,$key);
			}
			$q = rtrim($q,",");
			$q.=sprintf(" where vid=%d",$stats->getValue('vid'));
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$changes);
		}
		catch(Exception $ex){ }
		return $stats;
	}

	public function updateCdnStatus($vid,$status,$vstatus){
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("Update jw_videos set iscdn=%d,vstatus=%d where vid=%d",$status,$vstatus,$vid);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,array());
		}
		catch(Exception $ex){ }
	}

	public function updateUpicCdnStatus($uid,$status){
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("Update jw_users set iscdn=%d where uid=%d",$status,$uid);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,array());
		}
		catch(Exception $ex){ }
	}	

	public function updateUpicAndThumb($uid,$pic,$thumb,$status){
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("Update jw_users set iscdn=%d,upic='%s',thumb='%s' where uid=%d",$status,$pic,$thumb,$uid);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,array());
		}
		catch(Exception $ex){ }
	}

	public function updateUserVideo($uid,$vid){
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("Update jw_users set vid=%d where uid=%d",$vid,$uid);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,array());
		}
		catch(Exception $ex){ }
	}	

	public function updateGifpath($vid,$path){
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("Update jw_videos set gif='%s' where vid=%d",$path,$vid);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,array());
		}
		catch(Exception $ex){ }
	}

	public function updateImagesPath($vid,$webpath,$thumb,$firstpic){
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("Update jw_videos set webp='%s',thumb='%s',firstpic='%s' where vid=%d",$webpath,$thumb,$firstpic,$vid);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,array());
		}
		catch(Exception $ex){ }
	}

	public function addVideoInfo($video_info){
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("insert into jw_videos_info (vid,vname,vurl,vactors,vmessage,vdesc,vgenre,created_on,updated_on) values (%d,'{vname}',
				'{vurl}','{vactors}','{vmessage}','{vdesc}','{vgenre}',now(),now())",
				$video_info->getValue('vid')
			);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$video_info->getObject());
		}
		catch(Exception $ex){ }
		return $video_info;
	}		

}