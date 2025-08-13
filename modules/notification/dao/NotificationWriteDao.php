<?php
include_once dirname(__FILE__) . '/../../general/dao/GeneralWriteDao.php';

class NotificationWriteDao extends GeneralWriteDao{

	function __construct(){
	}

	public function setNotificationIsSent($nid,$is_sent=1){
		$success = false;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('notification');
			$q = sprintf("Update jw_user_notifications set is_sent=%d where nid=%d",$is_sent,$nid);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__);
		}
		catch(Exception $ex){ }
		return $success;
	}

	public function setNotificationIsProcessed($nid,$is_processed=1){
		$success = false;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('notification');
			$q = sprintf("Update jw_notification set is_processed=%d where nid=%d",$is_processed,$nid);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__);
		}
		catch(Exception $ex){ }
		return $success;
	}

	public function setUserNotificationIsSeen($uid,$is_seen=1){
		$success = false;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('notification');
			$q = sprintf("Update jw_user_notifications set is_seen=%d where uid=%d",$is_seen,$uid);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__);
		}
		catch(Exception $ex){ }
		return $success;
	}

}