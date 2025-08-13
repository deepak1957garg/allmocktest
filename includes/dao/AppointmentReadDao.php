<?php
include_once dirname(__FILE__) . '/../db/DBWrapper.php';
include_once dirname(__FILE__) . '/../models/ServiceProvider.php';
include_once dirname(__FILE__) . '/../models/Appointment.php';
include_once dirname(__FILE__) . '/../models/Appointment.php';
include_once dirname(__FILE__) . '/../models/BookedSlot.php';

class AppointmentReadDao{

	function __construct(){
	}

	public function getProviderIds($module="diabezone"){
		$final_arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select puid from jw_provider_module where module='%s'",$module);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
			if($error==''){
				foreach($arr as $row){
					array_push($final_arr,$row['puid']);
				}
			}
		}
		catch(Exception $ex){ }
		return $final_arr;
	}

	public function getProviderModules($puid){
		$final_arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select module from jw_provider_module where puid=%d",$puid);
			//print_r($q);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
			if($error==''){
				foreach($arr as $row){
					$final_arr[$row['module']] = $row['module'];
				}
			}
		}
		catch(Exception $ex){ }
		return $final_arr;
	}	

	public function getServiceProvidersByPuids($puids,$is_active=1){
		$final_arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select * from jw_service_providers where puid in (%s) and is_active=%d order by ordering",implode(",",$puids),$is_active);
			//print_r($q);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
			if($error==''){
				foreach($arr as $row){
					$provider =  new ServiceProvider();
					foreach($row as $key=>$value){
						if($key!='timings'){
							$provider->setValue($key,$value);
						}
					}
					if($row['timings']!=''){
						$timings = json_decode($row['timings'],true);
						foreach($timings as $key=>$value){
							$provider->addTimingsInfo($key,$value);
						}
					}
					array_push($final_arr,$provider);
				}
			}
		}
		catch(Exception $ex){ }
		return $final_arr;
	}	

	public function getServiceProviders($is_active=1){
		$final_arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select * from jw_service_providers where is_active=%d order by ordering",$is_active);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
			if($error==''){
				foreach($arr as $row){
					$provider =  new ServiceProvider();
					foreach($row as $key=>$value){
						if($key!='timings'){
							$provider->setValue($key,$value);
						}
					}
					if($row['timings']!=''){
						$timings = json_decode($row['timings'],true);
						foreach($timings as $key=>$value){
							$provider->addTimingsInfo($key,$value);
						}
					}
					array_push($final_arr,$provider);
				}
			}
		}
		catch(Exception $ex){ }
		return $final_arr;
	}

	public function getProvidersListAdmin(){
		$final_arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select * from jw_service_providers order by puid");
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
			if($error==''){
				foreach($arr as $row){
					$provider =  new ServiceProvider();
					$provider->setObject($row);
					$provider->addValue('module1',0);
					$provider->addValue('module2',0);
					
					$final_arr[$row['puid']]=$provider;
				}
			}

			$q = sprintf("select puid,module from jw_provider_module");
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
			if($error==''){
				foreach($arr as $row){
					if(isset($final_arr[$row['puid']])){
						$final_arr[$row['puid']]->setValue('module'.$row['module'],1);
					}
				}
			}
		}
		catch(Exception $ex){ }
		return $final_arr;
	}	

	public function getAppointments($params){
		$final_arr = array();
		try{
			$str="";
			if(isset($params['uid']))	$str.=sprintf(" and uid=%d",$params['uid']);
			if(isset($params['puid']))	$str.=sprintf(" and puid=%d",$params['puid']);
			if(isset($params['status']) && $params['status']=='blocked'){
				if(isset($params['for_upcoming']))	$str.=sprintf(" and booking_date>'%s'",$params['for_upcoming']);
				$str.=sprintf(" and booking_status in (1,4)");
			}
			else if(isset($params['for_upcoming']))	$str.=sprintf(" and booking_date>'%s' and booking_status=1",$params['for_upcoming']);
			if(isset($params['for_history']))	$str.=sprintf(" and booking_date<'%s' and booking_status=1",$params['for_history']);
			if(isset($params['module']))	$str.=sprintf(" and module=%d",$params['module']);

			if($str!="")	$str=sprintf("where %s",trim(trim(trim($str),'and')));

			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select * from jw_appointments %s",$str);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
			if($error==''){
				foreach($arr as $row){
					$appointment = new Appointment();
					$appointment->setObject($row);
					array_push($final_arr,$appointment);
				}
			}
		}
		catch(Exception $ex){ }
		return $final_arr;
	}

	public function getUpcomingBookedSlots($puid){
		$final_arr = array();
		try{
			$date = date("Y-m-d 00:00:00",strtotime('now'));
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select * from jw_provider_booked_slot where puid=%d and booking_date>='%s'",$puid,$date);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
			if($error==''){
				foreach($arr as $row){
					$slot = new BookedSlot();
					$slot->setObject($row);
					array_push($final_arr,$slot);
				}
			}
		}
		catch(Exception $ex){ }
		return $final_arr;
	}	

	public function getAllUpcomingAppointments($module=1){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select a.*,b.name,b.mobile,c.name as pname from jw_appointments a,jw_users b,jw_service_providers c where a.uid=b.uid and a.puid=c.puid and booking_date>now() and booking_status=1 and module=%d order by a.booking_date",$module);
			//print_r($q);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
		}
		catch(Exception $ex){ }
		return $arr;
	}	

	public function getUnavailableSlots($puid){
		$arr = array();
		try{
			$date=date("Y-m-d 00:00:00",strtotime('now'));

			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select * from jw_slot_unavailable where puid=%d and is_active=1 and date>='%s' order by date,slot;",$puid,$date);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
		}
		catch(Exception $ex){ }
		return $arr;
	}	

	public function getServiceProvider($puid){
		$provider =  new ServiceProvider();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select * from jw_service_providers where puid=%d",$puid);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
			if($error==''){
				foreach($arr as $row){
					foreach($row as $key=>$value){
						if($key!='timings'){
							$provider->setValue($key,$value);
						}
					}
					if($row['timings']!=''){
						$timings = json_decode($row['timings'],true);
						foreach($timings as $key=>$value){
							$provider->addTimingsInfo($key,$value);
						}
					}
					break;
				}
			}
		}
		catch(Exception $ex){ }
		return $provider;
	}


	public function getMySlots($uid){
		$final_arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select * from jw_provider_slots where puid=%d",$uid);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
			if($error==''){
				foreach($arr as $row){
					$final_arr[$row['day']."_".$row['slot']] = $row;
				}
			}
		}
		catch(Exception $ex){ }
		return $final_arr;
	}

	public function getPuidByUid($uid,$module){
		$puid =  0;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select puid from jw_provider_module where uid=%d and module=%d",$uid,$module);
			list($row,$error,$error_no) = DBWrapper::getSingleRow($dbinfo,$q,__FUNCTION__);
			if($error==''){
				$puid = $row[0];
			}
		}
		catch(Exception $ex){ }
		return $puid;
	}	

	public function getUserHealthCoach($uid){
		$id = 0;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select health_coach from jw_program_subscriptions where uid=%d and end_date>now() and health_coach!=0",$uid);
			list($row,$error,$error_no) = DBWrapper::getSingleRow($dbinfo,$q,__FUNCTION__);
			if($error==''){
				$id = $row[0];
			}
		}
		catch(Exception $ex){ }
		return $id;
	}

	public function getAppointmentList($params){
		$arr = array();
		try{
			$str="";
			if(isset($params['uid']))	$str.=sprintf(" and a.uid=%d",$params['uid']);
			if(isset($params['puid']))	$str.=sprintf(" and a.puid=%d",$params['puid']);
			if(isset($params['for_upcoming']))	$str.=sprintf(" and a.booking_date>'%s' and a.booking_status=1",$params['for_upcoming']);
			if(isset($params['for_history']))	$str.=sprintf(" and a.booking_date<'%s'",$params['for_history']);
			if(isset($params['module']))	$str.=sprintf(" and a.module=%d",$params['module']);

			//if($str!="")	$str=sprintf("where %s",trim(trim(trim($str),'and')));

			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select a.booking_date,a.aid,a.uid,a.puid,a.slot,a.booking_status,a.booking_type,a.cancel_reason,b.name,b.mobile,b.email,c.name as pname,a.module from jw_appointments a,jw_users b,jw_service_providers c where a.uid=b.uid and a.puid=c.puid %s order by a.booking_date desc limit 0,300",$str);
			//print_r($q);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getBookedSlotAvailability($puid,$slots){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select * from jw_provider_booked_slot where booking_date in ('%s')",implode("','",$slots));
			//print_r($q);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
		}
		catch(Exception $ex){ }
		return $arr;
	}		

}