<?php
include_once dirname(__FILE__) . '/../../general/dao/GeneralWriteDao.php';

class UserWriteDao extends GeneralWriteDao{

	function __construct(){
	}



	public function updateBookingStatus($bid,$uid,$status){
		$retarr = array();
		$retarr['success'] = false;
		$retarr['count'] = 0;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("Update jw_bookings set status=%d where id=%d",$status,$bid);
			list($retarr['success'],$retarr['count'],$error,$error_no) = DBWrapper::writeData($dbinfo,$q,'__FUNCTION__',array(),1);
		}
		catch(Exception $ex){ }
		return $retarr;
	}

	public function addSlots($uid,$slots){
		$retarr = array();
		try{
			$str = "";
			foreach($slots as $slot){
				$str .= sprintf("(%d,'%s',%d),",$uid,$slot["time"],$slot["dayno"]);
			}
			$str = trim($str,",");
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("Insert into jw_provider_slots (puid,slot,day) values %s",$str);
			list($retarr['success'],$retarr['count'],$error,$error_no) = DBWrapper::writeData($dbinfo,$q,'__FUNCTION__',array(),1);
		}
		catch(Exception $ex){ }
		return $retarr;
	}

	public function removeSlots($uid,$slotsIds){
		$retarr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("Delete from jw_provider_slots where id in (%s)",implode(",",$slotsIds));
			list($retarr['success'],$retarr['count'],$error,$error_no) = DBWrapper::writeData($dbinfo,$q,'__FUNCTION__',array(),1);
		}
		catch(Exception $ex){ }
		return $retarr;
	}

	public function addPlaces($uid,$places){
		$retarr = array();
		try{
			$str = "";
			foreach($places as $place){
				$str .= sprintf("(%d,'%s','%s','%s','%s',%d),",$uid,$place["address"],$place["latitude"],$place["longitude"],$place["name"],$place["isSel"]);
			}
			$str = trim($str,",");
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("Insert into jw_places (uid,address,latitude,longitude,name,issel) values %s",$str);
			list($retarr['success'],$retarr['count'],$error,$error_no) = DBWrapper::writeData($dbinfo,$q,'__FUNCTION__',array(),1);
		}
		catch(Exception $ex){ }
		return $retarr;
	}


	// public function addNotifToken($uid,$token,$isactive=1){
	// 	$success = false;
	// 	try{
	// 		$dbinfo =  DBWrapper::getDBInfoObject('phantom');
	// 		$params = array('token'=>$token);
	// 		$q = sprintf("insert into jw_notif_token (uid,token,is_active,created_on,updated_on) values (%d,'{token}',%d,now(),now())",
	// 			$uid,$isactive);
	// 		list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,'createUser',$params);
	// 	}
	// 	catch(Exception $ex){ }
	// 	return $success;
	// }

	// public function updateNotifToken($uid,$token,$isactive=1){
	// 	$success = false;
	// 	try{
	// 		$dbinfo =  DBWrapper::getDBInfoObject('phantom');
	// 		$params = array('token'=>$token);
	// 		$q = sprintf("Update jw_notif_token set token='{token}',updated_on=now(),is_active=%d where uid=%d",$isactive,$uid);
	// 		list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$params);
	// 	}
	// 	catch(Exception $ex){ }
	// 	return $success;
	// }

	// public function updateNotif($notif){
	// 	$success = false;
	// 	try{
	// 		$dbinfo =  DBWrapper::getDBInfoObject('phantom');
	// 		$q = sprintf("Update jw_notifs set is_sent=%d,req='{req}',res='{res}',updated_on=now() where id=%d",
	// 			$notif->getValue('is_sent'),
	// 			$notif->getValue('id')
	// 		);
	// 		list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$notif->getObject());
	// 	}
	// 	catch(Exception $ex){ }
	// 	return $success;
	// }

	// public function addUserData($uid,$path,$type,$is_processed=0){
	// 	$success = false;
	// 	try{
	// 		$dbinfo =  DBWrapper::getDBInfoObject('phantom');
	// 		$params = array("path"=>$path,"type"=>$type);
	// 		$q = sprintf("insert into up_user_data (uid,path,type,is_processed,created_on,updated_on) values (%d,'{path}','{type}',%d,now(),now())",
	// 			$uid,$is_processed
	// 		);
	// 		list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$params);
	// 	}
	// 	catch(Exception $ex){ }
	// 	return $success;
	// }

}