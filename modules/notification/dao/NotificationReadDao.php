<?php
include_once dirname(__FILE__) . '/../../general/dao/GeneralReadDao.php';

class NotificationReadDao extends GeneralReadDao{

	function __construct(){
	}

	public function getNotificationUids($nid){
		$uids = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('notification');
			$q = sprintf("Select uid from jw_user_notifications where nid=%d and is_sent=0",$nid);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,'__FUNCTION__',array(),1);
			if($error==''){
				foreach($arr as $row)	array_push($uids,$row['uid']);
			}
			$uids = array_values(array_unique($uids));
		}
		catch(Exception $ex){ }
		return $uids;
	}

	public function getUserDeviceTokens($uids){
		$tokens = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("Select token from jw_notif_token where uid in (%s)",implode(",",$uids));
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,'__FUNCTION__',array(),1);
			if($error==''){
				foreach($arr as $row)	array_push($tokens,$row['token']);
			}
		}
		catch(Exception $ex){ }
		return $tokens;
	}

	public function getNotificationList($uid){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('notification');
			$q = sprintf("select b.id,a.nid,a.template,a.page,a.iid,a.info,b.is_seen,b.is_sent,b.created_on from jw_notification a,`jw_user_notifications` b where a.nid=b.nid and b.uid=%d and b.is_seen=0 order by b.updated_on desc",$uid);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getSeenNotificationList($uid){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('notification');
			$q = sprintf("select b.id,a.nid,a.template,a.page,a.iid,a.info,b.is_seen,b.is_sent,b.created_on from jw_notification a,`jw_user_notifications` b where a.nid=b.nid and b.uid=%d and b.is_seen=1 order by b.updated_on desc limit 0,10",$uid);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getUsers($uids){
		$users = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select a.uid,a.upic,b.path,b.webp,b.thumb,a.cafes,a.availability,a.name,a.isCdn,a.thumb from jw_users a,jw_videos b where a.vid=b.vid and a.uid in (%s)",implode(",",$uids));
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,'__FUNCTION__',array(),1);
			if($error==''){
				foreach($arr as $row)	$users[$row['uid']] = $row;
			}
		}
		catch(Exception $ex){ }
		return $users;
	}	

	public function getUserInfo($uid){
		$user = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select a.uid,a.upic,b.path,b.gif,b.thumb,a.cafes,a.availability from jw_users a,jw_videos b where a.vid=b.vid and a.uid=%d",$uid);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
			if($error==''){
				if(count($arr)>0)	$user = $arr[0];
			}
		}
		catch(Exception $ex){ }
		return $user;
	}	

}