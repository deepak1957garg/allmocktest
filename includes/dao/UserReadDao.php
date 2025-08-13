<?php
include_once dirname(__FILE__) . '/../db/DBWrapper.php';
include_once dirname(__FILE__) . '/../models/UserModel.php';
include_once dirname(__FILE__) . '/../models/UserVideoStats.php';
include_once dirname(__FILE__) . '/../models/Notif.php';
// include_once dirname(__FILE__) . '/../models/UserClientModel.php';
// include_once dirname(__FILE__) . '/../models/UserSummaryModel.php';

class UserReadDao{

	function __construct(){
	}

	public function getAdminUsers(){
		$user_arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select * from jw_admin_users");
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
			if($error==''){
				foreach($arr as $row)	$user_arr[$row['uid']]=$row;
			}
		}
		catch(Exception $ex){ }
		return $user_arr;
	}

	public function getUser($params=array()){
		$user = new UserModel();
		try{
			$paramstr= '';
			if(isset($params['mobile']))	$paramstr.=" and mobile='" . DBWrapper::mysqlRealEscape($params['mobile'])  . "'";
			if(isset($params['uid']))	$paramstr.=" and uid=" . DBWrapper::mysqlRealEscape($params['uid']);
			$paramstr = ltrim($paramstr,' and');
			if($paramstr!='')	$paramstr='where ' . $paramstr;

			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select uid,name,uname,cc,mobile,mverified,isactive,joined_on,updated_on,pic,bio from jw_users %s",$paramstr);
			list($row,$error,$error_no) = DBWrapper::getSingleRow($dbinfo,$q,'getUser');
			if($error==''){
				$user->setValue('uid',$row[0]);
				$user->setValue('name',$row[1]);
				$user->setValue('uname',$row[2]);
				$user->setValue('cc',$row[3]);
				$user->setValue('mobile',$row[4]);
				$user->setValue('mverified',$row[5]);
				$user->setValue('isactive',$row[6]);
				$user->setValue('joined_on',$row[7]);
				$user->setValue('updated_on',$row[8]);
				$user->setValue('pic',$row[9]);
				$user->setValue('bio',$row[10]);
			}
		}
		catch(Exception $e){ }
		return $user;
	}

	public function getUserStats($stats_model){
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select uid,points,points_paid,num_tips,num_tips_paid,free_tips,points_earned,num_tips_earned,created_on,updated_on from jw_user_stats where uid=%d",
				$stats_model->getValue('uid')
			);
			list($row,$error,$error_no) = DBWrapper::getSingleRow($dbinfo,$q,__FUNCTION__);
			if($error==''){
				$stats_model->setValue('uid',$row[0]);
				$stats_model->setValue('points',$row[1]);
				$stats_model->setValue('points_paid',$row[2]);
				$stats_model->setValue('num_tips',$row[3]);
				$stats_model->setValue('num_tips_paid',$row[4]);
				$stats_model->setValue('free_tips',$row[5]);
				$stats_model->setValue('points_earned',$row[6]);
				$stats_model->setValue('num_tips_earned',$row[7]);
				$stats_model->setValue('created_on',$row[8]);
				$stats_model->setValue('updated_on',$row[9]);
			}
			else{
				$stats_model->setValue('uid',0);
			}
		}
		catch(Exception $ex){ }
		return $stats_model;
	}

	public function getUserVideoStats($stats_model){
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select uid,vid,points_spent,num_tips_spent,points_earned,num_tips_earned,created_on,updated_on from jw_user_video_stats where uid=%d and vid=%d",
				$stats_model->getValue('uid'),
				$stats_model->getValue('vid')
			);
			list($row,$error,$error_no) = DBWrapper::getSingleRow($dbinfo,$q,__FUNCTION__);
			if($error==''){
				$stats_model->setValue('uid',$row[0]);
				$stats_model->setValue('vid',$row[1]);
				$stats_model->setValue('points_spent',$row[2]);
				$stats_model->setValue('num_tips_spent',$row[3]);
				$stats_model->setValue('points_earned',$row[4]);
				$stats_model->setValue('num_tips_earned',$row[5]);
				$stats_model->setValue('created_on',$row[6]);
				$stats_model->setValue('updated_on',$row[7]);
			}
			else{
				$stats_model->setValue('uid',0);
			}
		}
		catch(Exception $ex){ }
		return $stats_model;
	}	

	public function getUserVideoStatsList($uid,$vids){
		$stats = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select uid,vid,points_spent,num_tips_spent,points_earned,num_tips_earned,created_on,updated_on from jw_user_video_stats where uid=%d and vid in (%s)",$uid,implode(",",$vids));
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
			if($error==''){
				foreach($arr as $row){
					$stats_model = new UserVideoStats();
					$stats_model->setObject($row);
					$stats[$stats_model->getvalue('vid')] = $stats_model;
				}
			}
		}
		catch(Exception $ex){ }
		return $stats;
	}

	public function getUserVideoSpentList($uid,$vids){
		$stats = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select uid,vid,sum(points) as points_spent,count(id) as num_tips_spent,created_on,updated_on from jw_tips where uid=%d and vid in (%s) group by vid",$uid,implode(",",$vids));
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
			if($error==''){
				foreach($arr as $row){
					$stats_model = new UserVideoStats();
					$stats_model->setObject($row);
					$stats[$stats_model->getvalue('vid')] = $stats_model;
				}
			}
		}
		catch(Exception $ex){ }
		return $stats;
	}		

	public function getUnpaidTipAmount($uid){
		$amount = 0;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select sum(points) from jw_tips where uid=%d and is_paid=0",$uid);
			list($row,$error,$error_no) = DBWrapper::getSingleRow($dbinfo,$q,'getUnpaidTipAmount');
			if($error==''){
				$amount=$row[0];
			}
		}
		catch(Exception $ex){ }
		return $amount;
	}

	public function getEarnedTipAmount($uid){
		$amount = 0;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select sum(points_earned) from jw_user_video_stats where uid=%d",$uid);
			list($row,$error,$error_no) = DBWrapper::getSingleRow($dbinfo,$q,'getEarnedTipAmount');
			if($error==''){
				if(!is_null($row[0]))	$amount=$row[0];
			}
		}
		catch(Exception $ex){ }
		return $amount;
	}

	public function getOtps($mobile){
		$final_arr = array();
		try{
			$params = array('mobile'=>$mobile);
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select otp from jw_user_otp where mobile='{mobile}'");
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,'getOtps',$params);
			if($error==''){
				foreach($arr as $row)	array_push($final_arr,$row['otp']);
			}
		}
		catch(Exception $ex){ }
		return $final_arr;
	}

	public function getNotifToken($uid){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select uid,token,is_active from jw_notif_token where uid=%d",$uid);
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

	public function getUnsentNotifs(){
		$final_arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('notification');
			$q = sprintf("select * from jw_notification where is_processed=0");
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
			if($error==''){
				foreach($arr as $row){
					$notif =  new Notif();
					$notif->setObject($row);
					array_push($final_arr,$notif);
				}
			}
		}
		catch(Exception $ex){ }
		return $final_arr;
	}

}