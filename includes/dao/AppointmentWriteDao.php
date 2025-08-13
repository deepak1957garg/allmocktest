<?php
include_once dirname(__FILE__) . '/../db/DBWrapper.php';

class AppointmentWriteDao{

	function __construct(){
	}

	public function createAppointment($obj){
		$id = -1;
		try{
			$params = array('puid'=>$obj->getValue('puid'),'uid'=>$obj->getValue('uid'),'booking_date'=>$obj->getValue('booking_date'),'slot'=>$obj->getValue('slot'),'booking_status'=>$obj->getValue('booking_status'));
			$dbinfo =  DBWrapper::getDBInfoObject('sgf');
			$q=sprintf("insert into sgf_appointments (puid,uid,booking_date,slot,booking_status) values ({puid},{uid},'{booking_date}','{slot}',{booking_status})");
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$params);
			$id = DBWrapper::getMysqlLastInsertId($dbinfo);
		}
		catch(Exception $ex){ }
		return $id;
	}

	public function changeAppointmentStatus($aid,$status,$new_aid,$reason=""){
		$success = false;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('sgf');
			$q = sprintf("Update sgf_appointments set booking_status=%d,new_aid=%d,cancel_reason='%s',updated_on=now() where aid=%d",$status,$new_aid,$reason,$aid);
			//print_r($q);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__);
		}
		catch(Exception $ex){ }
		return $success;
	}

	public function addProviderUnavailability($slot_arr){
		$status = false;
		try{
			$str = '';
			foreach($slot_arr as $slot){
				$str.="(" . $slot->getValue('puid') . ",'" . $slot->getValue('date') . "','" . $slot->getValue('slot') . "'),"; 
			}
			$str = trim($str,",");

			$dbinfo =  DBWrapper::getDBInfoObject('sgf');
			$q=sprintf("insert ignore into sgf_slot_unavailable (puid,date,slot) values %s",$str);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__);
			//$id = DBWrapper::getMysqlLastInsertId($dbinfo);
			if($error==""){
				$status = true;
			}
		}
		catch(Exception $ex){ }
		return $status;
	}

	public function removeProviderModule($puid){
		$status = false;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('sgf');
			$q=sprintf("delete from sgf_provider_module where puid=%d",$puid);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__);
			//$id = DBWrapper::getMysqlLastInsertId($dbinfo);
			if($error==""){
				$status = true;
			}
		}
		catch(Exception $ex){ }
		return $status;
	}	


	public function addProviderModule($puid,$module){
		$status = false;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('sgf');
			$q=sprintf("insert ignore into sgf_provider_module (puid,module) values (%d,%d)",$puid,$module);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__);
			//$id = DBWrapper::getMysqlLastInsertId($dbinfo);
			if($error==""){
				$status = true;
			}
		}
		catch(Exception $ex){ }
		return $status;
	}	

	public function cancelUnavailability($puid,$date,$slot){
		$status = false;
		try{
			$str = '';
			if($slot!="0")	$str = "and slot='" . $slot . "'";
			$dbinfo =  DBWrapper::getDBInfoObject('sgf');
			$q=sprintf("update sgf_slot_unavailable set is_active=0 where puid=%d and date='%s' %s",$puid,$date,$str);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__);
			//$id = DBWrapper::getMysqlLastInsertId($dbinfo);
			if($error==""){
				$status = true;
			}
		}
		catch(Exception $ex){ }
		return $status;
	}

	public function deleteProviderSlot($puid,$day,$module=2){
		$status = false;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('sgf');
			$q=sprintf("delete from sgf_provider_slots where puid=%d and day=%d and module=%d",$puid,$day,$module);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__);
			if($error==""){
				$status = true;
			}
		}
		catch(Exception $ex){ }
		return $status;
	}	

	public function addProviderSlots($puid,$day,$slots,$module=2){
		$status = false;
		try{
			$str = '';
			foreach($slots as $slot){
				$str.="(" . $puid . "," . $day . ",'" . $slot . "'," . $module . "),"; 
			}
			$str = trim($str,",");

			$dbinfo =  DBWrapper::getDBInfoObject('sgf');
			$q=sprintf("insert ignore into sgf_provider_slots (puid,day,slot,module) values %s",$str);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__);
		}
		catch(Exception $ex){ }
		return $status;
	}

	public function updateProviderTimings($puid,$timings){
		$status = false;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('sgf');
			$q=sprintf("update sgf_service_providers set timings='%s' where puid=%d",$timings,$puid);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__);
			//$id = DBWrapper::getMysqlLastInsertId($dbinfo);
			if($error==""){
				$status = true;
			}
		}
		catch(Exception $ex){ }
		return $status;
	}

	public function deleteBookedSlot($aid){
		$status = false;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('sgf');
			$q=sprintf("delete from  sgf_provider_booked_slot where aid=%d",$aid);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__);
			//$id = DBWrapper::getMysqlLastInsertId($dbinfo);
			if($error==""){
				$status = true;
			}
		}
		catch(Exception $ex){ }
		return $status;
	}

}