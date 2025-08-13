<?php
include_once dirname(__FILE__) . '/../../../includes/common/Constants.php';
include_once dirname(__FILE__) . '/../../general/dao/GeneralWriteDao.php';

class QuestionWriteDao extends GeneralWriteDao{

	function __construct(){
	}

	public function addEvent($vid,$uid,$event){
		$id=0;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("insert into jw_events (vid,uid,event,created_on,updated_on) values (%d,%d,'%s',now(),now())",$vid,$uid,$event);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$tip->getObject());
			if($success && $num_affected_rows>0)	$id=DBWrapper::getMysqlLastInsertId($dbinfo);
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
			$q = sprintf("insert into jw_videos (uid,owner,cid,vname,urlt,path,hls_path,thumb,firstpic,duration,iscdn,created_on,updated_on) values (%d,%d,%d,'{vname}',
				'{urlt}','{path}','{hls_path}','{thumb}','{firstpic}',%d,%d,now(),now())",
				$video->getValue('uid'),
				$video->getValue('uid'),
				$video->getValue('cid'),
				$video->getValue('dur'),
				$iscdn
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