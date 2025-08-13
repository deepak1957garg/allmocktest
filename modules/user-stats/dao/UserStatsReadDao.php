<?php
include_once dirname(__FILE__) . '/../../general/dao/GeneralReadDao.php';

class UserStatsReadDao extends GeneralReadDao{

	function __construct(){
	}

	public function getUserStats($uid){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			//$table_name = Constants::$TABLE_PREFIX . 'events';
			$q = sprintf("select count(a.id) as cnt,a.event,a.data from jw_events a,jw_videos b where a.vid=b.vid and a.uid=%d and b.is_active=1 and b.vstatus=1 group by a.event,a.data",$uid);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getUserVideoCount($uid){
		$count = 0;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$table_name = Constants::$TABLE_PREFIX . 'videos';
			$q = sprintf("select count(vid) as cnt from %s where uid=%d and is_active=1 and vstatus=1 group by uid",$table_name,$uid);
			list($row,$error,$error_no) = DBWrapper::getSingleRow($dbinfo,$q,__FUNCTION__,array(),1);
			if($error==''){
				$count=$row[0];
			}
		}
		catch(Exception $ex){ }
		return $count;
	}

	public function getUserVideoCountByType($uid,$type){
		$count = 0;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$table_name = Constants::$TABLE_PREFIX . 'videos';
			$q = sprintf("select count(vid) as cnt from %s where uid=%d and itype=%d and is_active=1 and vstatus=1 group by uid",$table_name,$uid,$type);
			list($row,$error,$error_no) = DBWrapper::getSingleRow($dbinfo,$q,__FUNCTION__,array(),1);
			if($error==''){
				$count=$row[0];
			}
		}
		catch(Exception $ex){ }
		return $count;
	}

	public function getUserMessageCount($uid){
		$count = 0;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('notification');
			$table_name = Constants::$TABLE_PREFIX . 'videos';
			$q = sprintf("select count(b.id) as cnt from jw_notification a,`jw_user_notifications` b where a.nid=b.nid and b.uid=%d and b.is_seen=0",$uid);
			list($row,$error,$error_no) = DBWrapper::getSingleRow($dbinfo,$q,__FUNCTION__,array(),1);
			if($error==''){
				$count=$row[0];
			}
		}
		catch(Exception $ex){ }
		return $count;
	}	

}