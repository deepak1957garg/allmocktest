<?php
include_once dirname(__FILE__) . '/../models/ServiceProvider.php';
include_once dirname(__FILE__) . '/../models/ProviderSlot.php';
include_once dirname(__FILE__) . '/../models/ProviderSlotUnavailable.php';
include_once dirname(__FILE__) . '/../models/ProviderSlotFees.php';
include_once dirname(__FILE__) . '/../models/BookedSlot.php';
include_once dirname(__FILE__) . '/../models/Slot.php';
include_once dirname(__FILE__) . '/../dao/AppointmentReadDao.php';
include_once dirname(__FILE__) . '/../dao/AppointmentWriteDao.php';
//include_once dirname(__FILE__) . '/../dao/GeneralDao.php';
include_once dirname(__FILE__) . '/../../modules/general/dao/GeneralReadDao.php';

class ServiceProviderManager{
	private $areadobj;
	private $awriteobj;
	private $generaldaobj;

	public function __construct(){
		$this->generaldaobj = new GeneralReadDao();
		$this->areadobj = new AppointmentReadDao();
		$this->awriteobj = new AppointmentWriteDao();
	}

	public function getServiceProvidersMin(){
		$arr = $this->areadobj->getServiceProviders();
		return $arr;
	}	

	public function getProviderslist($uid,$module=1,$is_member=0){
		$list = array();
		$puids = $this->areadobj->getProviderIds($module);
		$arr = $this->areadobj->getServiceProvidersByPuids($puids);
		foreach($arr as $obj){
			$obj->setValue("available_slots",$this->getAvailableSlots($obj->getValue('puid'),$uid));
			$obj->addValue("is_free",false);
			// if($is_member==1)	$obj->addValue("is_free",true);
			// else $obj->addValue("is_free",false);
			array_push($list,$obj->getfullObject());
		}
		return $list;
	}

	public function getProviderslist2($uid,$module=1,$is_member=0){
		$list = array();
		$puids = $this->areadobj->getProviderIds($module);
		$arr = $this->areadobj->getServiceProvidersByPuids($puids);
		$fees_arr = $this->getSlotFees($module);
		foreach($arr as $obj){
			$obj->addValue("is_free",false);
			if(isset($fees_arr[$obj->getValue('puid')]))	$obj->addValue("slot_fees",$fees_arr[$obj->getValue('puid')]);
			else 	$obj->addValue("slot_fees",array());	
			array_push($list,$obj->getfullObject());
		}
		return $list;
	}

	public function getProviderslistAdmin(){
		$list = $this->areadobj->getProvidersListAdmin();
		return $list;
	}	

	public function getSlotFees($module=1){
		$final_arr = array();
		$fees = new ProviderSlotFees();
		$list = $this->generaldaobj->getList($fees,array('module'=>$module));
		foreach ($list as $value) {
			if(!isset($final_arr[$value['puid']]))	$final_arr[$value['puid']] = array();
			// $fees_val = $value;
			// unset($fees_val['module']);
			$fees = new ProviderSlotFees();
			$fees->setObject($value);
			$fees->removeValue('module');
			$fees->removeValue('puid');
			$fees->removeValue('id');
			//$final_arr[$value['puid']] = $fees->getObject();
			array_push($final_arr[$value['puid']],$fees->getObject());
		}
		return $final_arr;
	}

	public function getProviderSlotFees($puid,$module=1){
		$final_arr = array();
		$fees = new ProviderSlotFees();
		$list = $this->generaldaobj->getList($fees,array('module'=>$module,'puid'=>$puid));
		foreach ($list as $value) {
			$fees = new ProviderSlotFees();
			$fees->setObject($value);
			//$fees->removeValue('module');
			//$fees->removeValue('puid');
			//$fees->removeValue('id');
			//$final_arr[$value['puid']] = $fees->getObject();
			array_push($final_arr,$fees->getObject());
		}
		return $final_arr;
	}	

	public function getSlotByDay($puid,$module=1){
		//print_r($module);
		$day_by_slots = array();
		$provider_slot = new ProviderSlot();
		$slots_list = $this->generaldaobj->getList($provider_slot,array('puid'=>$puid,'is_active'=>1,'module'=>$module));
		foreach($slots_list as $obj){
			if(!isset($day_by_slots[$obj['day']]))	$day_by_slots[$obj['day']] = array();
			array_push($day_by_slots[$obj['day']],$obj['slot']);
		}

		for($i=1;$i<=7;$i++){
			if(!isset($day_by_slots[$i]))	$day_by_slots[$i] = array();
			else{
				sort($day_by_slots[$i]);
			} 
		}
		//print_r($day_by_slots);
		return $day_by_slots;
	}

	public function getProviderHolidays($puid){
		$leaves_arr = $this->areadobj->getUnavailableSlots($puid);
		return $leaves_arr;
	}

	private function getUnavailableSlots($puid,$uid,$module=1){
		$unavailable_slots = array();
		//$params = array('puid'=>$puid,'for_upcoming'=>date("Y-m-d h:i:s",strtotime('now')),'is_active'=>1,'status'=>'blocked');
		$arr = $this->areadobj->getUpcomingBookedSlots($puid);

		$params = array('puid'=>$puid,'for_upcoming'=>date("Y-m-d h:i:s",strtotime('now')),'is_active'=>1,'status'=>'blocked');
		$arr2 = $this->areadobj->getUpcomingBookedSlots($params);

		$slot_unavailable = new ProviderSlotUnavailable();
		$params2 = array('puid'=>$puid,'is_active'=>1);
		$leaves_arr = $this->areadobj->getUnavailableSlots($puid);

		//user appointments 
		$params = array('uid'=>$uid,'for_upcoming'=>date("Y-m-d h:i:s",strtotime('now')),'is_active'=>1);
		$user_appointments = $this->areadobj->getAppointments($params);

		
		foreach($arr as $booking_slot){
			$mon = strtolower(date('M',strtotime($booking_slot->getValue('booking_date'))));
			$date = date('d',strtotime($booking_slot->getValue('booking_date')));
			$key = $mon . "_" . $date . "_" . $booking_slot->getValue('slot');
			$unavailable_slots[$key]=1;
		}

		foreach($arr2 as $appointment){
			$mon = strtolower(date('M',strtotime($appointment->getValue('booking_date'))));
			$date = date('d',strtotime($appointment->getValue('booking_date')));
			$key = $mon . "_" . $date . "_" . $appointment->getValue('slot');
			$unavailable_slots[$key]=1;
		}		

		foreach($user_appointments as $appointment){
			$mon = strtolower(date('M',strtotime($appointment->getValue('booking_date'))));
			$date = date('d',strtotime($appointment->getValue('booking_date')));
			$key = $mon . "_" . $date . "_" . $appointment->getValue('slot');
			$unavailable_slots[$key]=1;
		}

		$days = array("1"=>"mon","2"=>"tue","3"=>"wed","4"=>"thu","5"=>"fri","6"=>"sat","7"=>"sun");

		$day_by_slots = $this->getSlotByDay($puid,$module);

		foreach($leaves_arr as $obj){
			if($obj['slot']!=0){
				$mon = strtolower(date('M',strtotime($obj['date'])));
				$date = date('d',strtotime($obj['date']));
				$key = $mon . "_" . $date . "_" . $obj['slot'];
				$unavailable_slots[$key]=1;
			}
			else{
				$j = date("N",strtotime($obj['date']));
				$all_slots =  isset($day_by_slots[$j]) ? $day_by_slots[$j] : array();
				$mon = strtolower(date('M',strtotime($obj['date'])));
				$date = date('d',strtotime($obj['date']));
				foreach($all_slots as $slot){
					$key = $mon . "_" . $date . "_" . $slot;
					$unavailable_slots[$key]=1;
				}
			}
		}
		return $unavailable_slots;
	}

	function getAvailableSlots($puid,$uid,$is_reshedule=false,$module=1){
		$slots = array();
		$days = array("1"=>"mon","2"=>"tue","3"=>"wed","4"=>"thu","5"=>"fri","6"=>"sat","7"=>"sun");
		$timngs = array("09:00"=>"9AM-12PM","12:00"=>"12-3PM","15:00"=>"3-6PM");

		$day_by_slots = $this->getSlotByDay($puid,$module);

		$now = new DateTime();
		$current_month = strtolower($now->format('F'));
		$current_month_no = strtolower($now->format('m'));
		$current_year = strtolower($now->format('Y'));
		$current_month_days = cal_days_in_month(CAL_GREGORIAN, $now->format('m'), $now->format('Y')); // 31
		$current_day = $now->format('d'); // 31
		$start_day = $current_day + 1;
		$unavailable_slots = $this->getUnavailableSlots($puid,$uid,$module);

		$j = date("N",(strtotime("now")+86400));
		$current_time = date("H:i",strtotime("now")+900);
		if($is_reshedule)	$current_time = date("H:i",strtotime("now")+1800);
		$last_day = $current_month_days;
		if(($current_month_days - $start_day) > 7){
			$last_day = $start_day + 6;
		}
		for($i=$start_day;$i<=$last_day;$i++){
			$day = array();
			$day["day"] = ($i<10) ? '0' . $i : '' . $i;
			$day["date"] = $current_year . '-' . $current_month_no. '-' . $day["day"];
			$day["dayname"] = $days[$j];
			$day['month'] = $current_month;
			$day['year'] = $current_year;
			$day["slots"] = array();
			$day['slots'] = $day_by_slots[$j];
			//print_r($day['slots']);
			foreach($day['slots'] as $key1 => $val){
				$key = strtolower($current_month) . "_" . $day["date"] . "_" . $val;
				if(isset($unavailable_slots[$key])){
					unset($day['slots'][$key1]);
				}	
			}
			sort($day['slots']);
			foreach($day['slots'] as $day_slot){
				$slot = new Slot();
				$slot->setObject($day);
				if(isset($timngs[$day_slot])){
					$slot->setValue('timing',$timngs[$day_slot]);
					$slot->setValue('date',$day["date"] . ' ' . $day_slot . ':00');
					array_push($slots,$slot->getObject());
				}
			}
			//array_push($slot["days"],$day);
			$j++;
			if($j>7)	$j=1;
		}
		//array_push($slots,$slot);

		$num_days = $current_month_days - $current_day;
		if($num_days<7){
			$num_days_remaining = 7 - $num_days;
			$next_month_first_day = (strtotime("now")+($current_month_days-$current_day+1)*86400);
			$next_month = strtolower(date("F",$next_month_first_day));
			$next_month_no = strtolower(date("m",$next_month_first_day));
			$next_month_year = strtolower(date("Y",$next_month_first_day));
			$next_month_first_day = (strtotime("now")+($current_month_days-$current_day+1)*86400);
			$next_month_same_day = (strtotime("now")+30*86400);
			$next_month_day = date("d",$next_month_same_day);

			$next_month_days = cal_days_in_month(CAL_GREGORIAN, date("m",$next_month_first_day), date("Y",$next_month_first_day)); // 31

			// $slot = array();
			// $slot["month"] = $next_month;
			// $slot["days"] = array();


			$j = date("N",$next_month_first_day);
			for($i=1;$i<=$num_days_remaining;$i++){
				$day = array();
				$day["day"] = ($i<10) ? '0' . $i : '' . $i;
				$day["date"] = $next_month_year . '-' . $next_month_no. '-' . $day["day"];
				$day["dayname"] = $days[$j];
				$day['month'] = $next_month;
				$day['year'] = $next_month_year;
				$day["slots"] = array();

				//if($i<=$next_month_day){
				$day['slots'] = $day_by_slots[$j];
				foreach($day['slots'] as $key1 => $val){
					$key = strtolower($next_month) . "_" . $day["date"] . "_" . $val;
					//print_r($key);
					if(isset($unavailable_slots[$key])){
						//print_r($key);
						unset($day['slots'][$key1]);
					}	
				}
				sort($day['slots']);
				foreach($day['slots'] as $day_slot){
					$slot = new Slot();
					$slot->setObject($day);
					//$slot->setValue('timing',$day_slot);
					if(isset($timngs[$day_slot])){
						$slot->setValue('timing',$timngs[$day_slot]);
						$slot->setValue('date',$day["date"] . ' ' . $day_slot . ':00');
						array_push($slots,$slot->getObject());
					}
				}
				//}
				//array_push($slot["days"],$day);
				$j++;
				if($j>7)	$j=1;

			}
			//array_push($slots,$slot);
		}
		return $slots;
	}

	function getAvailableSlots2($puid,$uid,$is_reshedule=false,$module=1){
		$slots = array();
		$final_slots = array();
		$days = array("1"=>"mon","2"=>"tue","3"=>"wed","4"=>"thu","5"=>"fri","6"=>"sat","7"=>"sun");

		$day_by_slots = $this->getSlotByDay($puid,$module);
		//print_r($day_by_slots);

		$now = new DateTime();
		$current_month = strtolower($now->format('M'));
		$current_month_days = cal_days_in_month(CAL_GREGORIAN, $now->format('m'), $now->format('Y')); // 31
		$current_day = $now->format('d'); // 31

		$unavailable_slots = $this->getUnavailableSlots($puid,$uid,$module);

		$slot = array();
		$slot["month"] = $current_month;
		$slot["days"] = array();
		$j = date("N",strtotime("now")-(86400*($current_day-1)));
		//$current_time = date("H:i",strtotime("now")+19800);
		$current_time = date("H:i",strtotime("now")+900);
		if($is_reshedule)	$current_time = date("H:i",strtotime("now")+1800);
		for($i=1;$i<=$current_month_days;$i++){
			$day = array();
			$day["date"] = ($i<10) ? '0' . $i : '' . $i;
			$day["day"] = $days[$j];
			$day["slots"] = array();
			//$day["date1"] = date("Y") 
			if($i>$current_day){
				$day['slots'] = $day_by_slots[$j];
				foreach($day['slots'] as $key1 => $val){
					$key = strtolower($slot["month"]) . "_" . $day["date"] . "_" . $val;
					//print_r($key);
					if(isset($unavailable_slots[$key])){
						unset($day['slots'][$key1]);
					}	
				}
				sort($day['slots']);
			}
			else if($i==$current_day){
				$day['slots'] = $day_by_slots[$j];
				foreach($day['slots'] as $key1 => $val){
					$key = strtolower($slot["month"]) . "_" . $day["date"] . "_" . $val;
					if(isset($unavailable_slots[$key])){
						unset($day['slots'][$key1]);
					}
					else if($val<$current_time){
						unset($day['slots'][$key1]);
					}
				}
				sort($day['slots']);
			}
			$day["date1"] = date("Y-m-".$day["date"]);
			//$slot[$day["date1"]] = $day["slots"];
			array_push($slot["days"],$day);
			$final_slots[$day["date1"]] = $day["slots"];
			$j++;
			if($j>7)	$j=1;
		}
		array_push($slots,$slot);

		$next_month_first_day = (strtotime("now")+($current_month_days-$current_day+1)*86400);
		$next_month = strtolower(date("M",$next_month_first_day));
		$next_month_first_day = (strtotime("now")+($current_month_days-$current_day+1)*86400);
		$next_month_same_day = (strtotime("now")+30*86400);
		$next_month_day = date("d",$next_month_same_day);
		$next_month_num = strtolower(date("m",$next_month_first_day));
		$next_month_year = strtolower(date("Y",$next_month_first_day));

		$next_month_days = cal_days_in_month(CAL_GREGORIAN, date("m",$next_month_first_day), date("Y",$next_month_first_day)); // 31

		$slot = array();
		$slot["month"] = $next_month;
		$slot["days"] = array();

		$j = date("N",$next_month_first_day);
		for($i=1;$i<=$next_month_days;$i++){
			$day = array();
			$day["date"] = ($i<10) ? '0' . $i : '' . $i;
			$day["day"] = $days[$j];
			$day["slots"] = array();

			if($i<=$next_month_day){
				$day['slots'] = $day_by_slots[$j];
				foreach($day['slots'] as $key1 => $val){
					$key = strtolower($slot["month"]) . "_" . $day["date"] . "_" . $val;
					//print_r($key);
					if(isset($unavailable_slots[$key])){
						//print_r($key);
						unset($day['slots'][$key1]);
					}	
				}
				sort($day['slots']);
			}
			$day["date1"] = date($next_month_year . "-" . $next_month_num . "-" . $day["date"]);
			$final_slots[$day["date1"]] = $day["slots"];
			array_push($slot["days"],$day);
			$j++;
			if($j>7)	$j=1;

		}
		array_push($slots,$slot);
		//print_r($final_slots);
		return $final_slots;
	}	

	public function getHealthCoach($uid){
		$list = array();
		$coach_id = $this->getUserHealthCoach($uid);
		$serviceProvider = new ServiceProvider();
		if($coach_id!=0){
			$serviceProvider = $this->areadobj->getServiceProvider($coach_id);
			$serviceProvider->addValue("is_free",true);
			$serviceProvider->setValue("available_slots",$this->getAvailableSlots($coach_id,$uid));
		}
		return $serviceProvider->getObject();
	}

	public function getServiceProvider($puid){
		$serviceProvider = $this->areadobj->getServiceProvider($puid);
		return $serviceProvider;
	}

	public function getPuidByUid($uid,$module){
		$puid = $this->areadobj->getPuidByUid($uid,$module);
		return $puid;
	}	

	public function getUserHealthCoach($uid){
		$coach_id = $this->areadobj->getUserHealthCoach($uid);
		return $coach_id;
	}	

	public function getHealthCoachList(){
		$list = array();
		$arr = $this->areadobj->getServiceProviders();
		$excludes = array(1,2,3,4);
		foreach($arr as $obj){
			if(!in_array($obj->getValue('puid'),$excludes)){
				array_push($list,$obj);
			}
		}
		return $list;
	}

	public function addProviderUnavailability($puid,$dates,$slots=array()){
		$provider_slots = array();
		foreach($dates as $date){
			if(count($slots)==0){
				$provider_slot = new ProviderSlotUnavailable();
				$provider_slot->setValue("puid",$puid);
				$provider_slot->setValue("date",$date);
				$provider_slot->setValue("slot",0);
				array_push($provider_slots,$provider_slot);
			}
			else{
				foreach($slots as $slot){
					$provider_slot = new ProviderSlotUnavailable();
					$provider_slot->setValue("puid",$puid);
					$provider_slot->setValue("date",$date);
					$provider_slot->setValue("slot",$slot);
					array_push($provider_slots,$provider_slot);
				}
			}
		}
		$this->awriteobj->addProviderUnavailability($provider_slots);
		//print_r($provider_slots);
	}

	public function addProviderModule($puid,$modules=array()){
		$this->awriteobj->removeProviderModule($puid);
		foreach($modules as $module){
			$this->awriteobj->addProviderModule($puid,$module);
		}
	}	

	public function cancelUnavailability($puid,$date,$slot){
		if($date!='' && $slot!=''){
			$unavailable_slot = new ProviderSlotUnavailable();
			$this->awriteobj->cancelUnavailability($puid,$date,$slot);
		}
	}

	public function getProviderModules($puid){
		$arr = $this->areadobj->getProviderModules($puid);
		return $arr;
	}	

	public function updateProviderInfo($puid,$params){
		$changes = array();
		$provider = new ServiceProvider();
		$provider = $this->getServiceProvider($puid);

		foreach($params as $key=>$param){
			if($param!="" && $param!=$provider->getValue($key))	$changes[$key]=$params[$key];
		}
		if(count($changes)>0){
			$success = $this->generaldaobj->updateObject($provider,$changes);
		}
	}

	public function createProviderInfo($params){
		$changes = array();
		$provider = new ServiceProvider();
		$provider->setObject($params);
		// $provider = $this->getServiceProvider($puid);

		// foreach($params as $key=>$param){
		// 	if($param!="" && $param!=$provider->getValue($key))	$changes[$key]=$params[$key];
		// }
		$provider->removeValue('available_slots');
		$provider->removeValue('timings');
		$id = $this->generaldaobj->createObject($provider);
		$provider->setValue("puid",$id."");
		return $provider;
		//$success = $this->generaldaobj->updateObject($provider,$changes);
	}	

	public function updateProviderSlots($puid,$day,$slots,$new_timing,$module=2){
		$days = array("1"=>"mon","2"=>"tue","3"=>"wed","4"=>"thu","5"=>"fri","6"=>"sat","7"=>"sun");
		$this->awriteobj->deleteProviderSlot($puid,$day,$module);
		$this->awriteobj->addProviderSlots($puid,$day,$slots,$module);
		$provider = $this->getServiceProvider($puid);
		$timings = $provider->getValue('timings');
		if(!isset($timings['days']))	$timings['days'] = array();
		$timings['days'][$days[$day]] = $new_timing;
		$this->awriteobj->updateProviderTimings($puid,json_encode($timings));
	}

	function getMySlots($uid){
		$slots = array();
		$days = array("1"=>"mon","2"=>"tue","3"=>"wed","4"=>"thu","5"=>"fri","6"=>"sat","7"=>"sun");
		$timngs = array("09:00"=>"9AM-12PM","12:00"=>"12-3PM","15:00"=>"3-6PM");

		$myslots = $this->areadobj->getMySlots($uid);

		$slots = array();
		$i=1;
		foreach($days as $day_no => $day){
			foreach($timngs as $key => $value){
				$slot = array();
				$slot['id'] = $i."";
				$slot['day'] = $day;
				$slot['dayno'] = $day_no."";
				$slot['time'] = $key;
				$slot['timing'] = $value;
				$slot['issel'] = "0";
				if(isset($myslots[$day_no."_".$key]))	$slot['issel'] = "1";
				//if(!isset($slots[$day]))	$slots[$day] = array();
				array_push($slots,$slot);
				$i++;
			}
		}
		return $slots;
	}	


}
?>