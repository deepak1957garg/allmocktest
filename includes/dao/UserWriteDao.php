<?php
include_once dirname(__FILE__) . '/../db/DBWrapper.php';

class UserWriteDao{

	function __construct(){
	}

	public function createUser($user){
		$success = false;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("insert into jw_users (uid,uname,name,email,cc,mobile,everified,mverified,password,isactive,joined_on,updated_on) values (%d,'{uname}','{name}','{email}','{cc}','{mobile}',%d,%d,'{password}',%d,now(),now())",
				$user->getValue('uid'),
				$user->getValue('everified'),
				$user->getValue('mverified'),
				$user->getValue('isactive'),
				$user->getValue('ispublic')
			);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,'createUser',$user->getObject());
		}
		catch(Exception $ex){ }
		return $success;
	}

	public function updateUser($user,$changes){
		$success = false;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("Update jw_users set ");
			foreach($changes as $key => $value){
				$q.=sprintf("%s='{%s}',",$key,$key);
			}
			$q = rtrim($q,",");
			$q.=sprintf(" where uid=%d",$user->getValue('uid'));
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$changes);
		}
		catch(Exception $ex){ }
		return $success;
	}

	public function updateProfilePic($user,$pic_path){
		$success = false;
		try{
			$params = array('pic'=>$pic_path);
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("update jw_users set pic='{pic}' where uid=%d",$user->getValue('uid'));
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,'createUser',$params);
		}
		catch(Exception $ex){ }
		return $success;
	}		

	public function createUserDetails($user){
		$success = false;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("insert into jw_user_details (uid) values (%d)",$user->getValue('uid'));
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,'createUserDetails',$user->getObject());
		}
		catch(Exception $ex){ }
		return $success;
	}

	public function saveOtp($mobile,$otp){
		$success = false;
		try{
			$params = array('mobile'=>$mobile,'otp'=>$otp);
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("insert into jw_user_otp(mobile,otp) values('{mobile}',{otp})");
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,'createUser',$params);
		}
		catch(Exception $ex){ }
		return $success;
	}

	public function addUserStats($stats){
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("insert into jw_user_stats (uid,points,num_tips,points_paid,num_tips_paid,points_earned,num_tips_earned,created_on,updated_on) values (%d,%d,%d,%d,%d,%d,%d,now(),now())",
				$stats->getValue('uid'),
				$stats->getValue('points'),
				$stats->getValue('num_tips'),
				$stats->getValue('points_paid'),
				$stats->getValue('num_tips_paid'),
				$stats->getValue('points_earned'),
				$stats->getValue('num_tips_earned')
			);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$stats->getObject());
		}
		catch(Exception $ex){ }
		return $stats;
	}

	public function addUserVideoStats($stats){
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("insert into jw_user_video_stats (uid,vid,points_spent,num_tips_spent,points_earned,num_tips_earned,created_on,updated_on) values (%d,%d,%d,%d,%d,%d,now(),now())",
				$stats->getValue('uid'),
				$stats->getValue('vid'),
				$stats->getValue('points_spent'),
				$stats->getValue('num_tips_spent'),
				$stats->getValue('points_earned'),
				$stats->getValue('num_tips_earned')
			);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$stats->getObject());
		}
		catch(Exception $ex){ }
		return $stats;
	}	

	public function updateUserStats($stats,$changes){
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("Update jw_user_stats set ");
			foreach($changes as $key => $value){
				$q.=sprintf("%s='{%s}',",$key,$key);
			}
			$q = rtrim($q,",");
			$q.=sprintf(" where uid=%d",$stats->getValue('uid'));
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$changes);
		}
		catch(Exception $ex){ }
		return $stats;
	}

	public function updateUserVideoStats($stats,$changes){
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("Update jw_user_video_stats set ");
			foreach($changes as $key => $value){
				$q.=sprintf("%s='{%s}',",$key,$key);
			}
			$q = rtrim($q,",");
			$q.=sprintf(" where uid=%d",$stats->getValue('uid'));
			$q.=sprintf(" and vid=%d",$stats->getValue('vid'));
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$changes);
		}
		catch(Exception $ex){ }
		return $stats;
	}

	public function addNotif($notif){
		$success = false;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			//$params = array('token'=>$token);
			$q = sprintf("insert into jw_notifs (uid,vid,ntype,is_sent,req,res,created_on,updated_on) values (%d,%d,'{ntype}',%d,'{req}','{res}',now(),now())",
				$notif->getValue('uid'),
				$notif->getValue('vid'),
				$notif->getValue('is_sent')
			);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$notif->getObject());
		}
		catch(Exception $ex){ }
		return $success;
	}	

	public function addNotifToken($uid,$token,$isactive=1){
		$success = false;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$params = array('token'=>$token);
			$q = sprintf("insert into jw_notif_token (uid,token,is_active,created_on,updated_on) values (%d,'{token}',%d,now(),now())",
				$uid,$isactive);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,'createUser',$params);
		}
		catch(Exception $ex){ }
		return $success;
	}

	public function updateNotifToken($uid,$token,$isactive=1){
		$success = false;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$params = array('token'=>$token);
			$q = sprintf("Update jw_notif_token set token='{token}',updated_on=now(),is_active=%d where uid=%d",$isactive,$uid);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$params);
		}
		catch(Exception $ex){ }
		return $success;
	}

	public function updateNotif($notif){
		$success = false;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("Update jw_notifs set is_sent=%d,req='{req}',res='{res}',updated_on=now() where id=%d",
				$notif->getValue('is_sent'),
				$notif->getValue('id')
			);
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$notif->getObject());
		}
		catch(Exception $ex){ }
		return $success;
	}	

}