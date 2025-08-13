<?php
include_once dirname(__FILE__) . '/../../../includes/common/Constants.php';
include_once dirname(__FILE__) . '/../../general/dao/GeneralReadDao.php';

class UserReadDao extends GeneralReadDao{

	function __construct(){
	}

	public function getUsersInfo($uids){
		$users = array();
		try{
			$params = array('users' => implode(",",$uids));
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$table_name = Constants::$TABLE_PREFIX . 'user_otps';
			$q = sprintf("select jw_users.*,jw_videos.webp from jw_users left join jw_videos on jw_users.vid = jw_videos.vid where jw_users.uid in ({users})");
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,$params);
			if($error==''){
				foreach($arr as $row)	$users[$row['uid']] = $row;
			}
		}
		catch(Exception $ex){ }
		return $users;
	}

	public function getLatestOtps($to,$cc){
		$otps = array();
		try{
			$params = array('mobile' => $to,'cc'=>$cc);
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$table_name = Constants::$TABLE_PREFIX . 'user_otps';
			$q = sprintf("select * from %s where mobile='{mobile}' and cc='{cc}' order by id desc limit 0,5",$table_name);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,$params);
			if($error==''){
				foreach($arr as $row)	array_push($otps,$row['otp']);
			}
		}
		catch(Exception $ex){ }
		return $otps;
	}

	public function getUsernames($title){
		$arr = array();
		try{
			//$params = array('mobile' => $cc.$to);
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select uname from jw_users where uname like '%s%%'",$title);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array());
			// if($error==''){
			// 	foreach($arr as $row)	array_push($otps,$row['otp']);
			// }
			//print_r($arr);
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getBookingsToBeStarted(){
		$arr = array();
		try{
			//$params = array('mobile' => $cc.$to);
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$current_date = date("Y-m-d H:00:00",strtotime('now')+19600);
			$q = sprintf("select * from jw_bookings where status=1 and suggestedAvailability='%s' order by suggestedAvailability",$current_date);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getBookingsBySeller($uid){
		$arr = array();
		try{
			//$params = array('mobile' => $cc.$to);
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$current_date = date("Y-m-d 00:00:00",strtotime('now')+19600);
			$q = sprintf("select * from jw_bookings where seller=%d and suggestedAvailability>'%s' order by suggestedAvailability",$uid,$current_date);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getBookingsByCustomer($uid){
		$arr = array();
		try{
			//$params = array('mobile' => $cc.$to);
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$current_date = date("Y-m-d 00:00:00",strtotime('now')+19600);
			$q = sprintf("select * from jw_bookings where customer=%d and suggestedAvailability>'%s' order by suggestedAvailability",$uid,$current_date);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getBookingsByUsers($seller,$customer){
		$arr = array();
		try{
			//$params = array('mobile' => $cc.$to);
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$current_date = date("Y-m-d 00:00:00",strtotime('now')+19600);
			$q = sprintf("select * from jw_bookings where seller=%d and customer=%d and suggestedAvailability>'%s' order by suggestedAvailability",$seller,$customer,$current_date);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getRandomUsers($limit=10,$uids_to_exclude=array()){
		$arr = array();
		try{
			$str = '';
			if(count($uids_to_exclude)>0)	$str = sprintf('where c.uid not in (%s)',implode(",",$uids_to_exclude));
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select c.uid,c.name,c.uname,c.pic,c.bio,c.aboutme,c.designation,0 as status from pm_users c %s  order by rand() limit 0,%d",$str,$limit);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array());
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getNotifToken($uid){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select uid,token,is_active from jw_notif_token where uid=%d and is_active=1 order by id desc limit 0,1",$uid);
			list($row,$error,$error_no) = DBWrapper::getSingleRow($dbinfo,$q,__FUNCTION__);
			if($error==''){
				$arr['uid'] = $row[0];
				$arr['token'] = $row[1];
				$arr['is_active'] = $row[2];
			}
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getUserCoordinates($uid){
		$loc = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select location from jw_user_location where uid=%d order by id desc limit 0,1",$uid);
			list($row,$error,$error_no) = DBWrapper::getSingleRow($dbinfo,$q,__FUNCTION__);
			if($error==''){
				$loc = json_decode($row[0],true);
			}
		}
		catch(Exception $ex){ }
		return $loc;
	}

		public function getUserSlots($uid){
		$final_arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select id,day,slot from jw_provider_slots where puid=%d order by id desc",$uid);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
			if($error==''){
				foreach($arr as $row){
					$final_arr[$row['day']."_".$row['slot']] = $row['id'];
				}
			}
		}
		catch(Exception $ex){ }
		return $final_arr;
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