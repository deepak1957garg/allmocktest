<?php
include_once dirname(__FILE__) . '/../../../includes/common/Constants.php';
include_once dirname(__FILE__) . '/../../general/dao/GeneralReadDao.php';
// include_once dirname(__FILE__) . '/../models/Tip.php';

class VideoReadDao extends GeneralReadDao{

	function __construct(){
	}

	public function getTippedVideoList($params=array()){
		$arr = array();
		try{
			$paramstr= '';
			if(isset($params['uid']))	$paramstr.=" and uid=" . $params['uid'];
			$paramstr = ltrim($paramstr,' and');
			if($paramstr!='')	$paramstr='where ' . $paramstr;	

			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select vid,points from  jw_tips %s  order by id desc limit 0,20",$paramstr);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,'getTippedVideoList',$params);
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getVideoListAdmin($params=array()){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select a.vid,a.uid,a.path,a.thumb,a.is_active,a.owner,a.created_on,c.vname,c.vurl,c.vactors,c.vmessage,c.vdesc,a.vstatus,b.name as creator_name,b.pic as creator_pic,b.bio as creator_bio,d.name as owner_name,d.bio as owner_bio,d.pic as owner_pic,a.iscdn from jw_videos a,jw_videos_info c,jw_users b,jw_users d where a.vid=c.vid and a.uid=b.uid and a.owner=d.uid and a.vstatus=0 order by vid desc limit 0,20",$str);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,'getVideoListAdmin',$params,1);
		}
		catch(Exception $ex){ }
		return $arr;
	}	

	public function getVidsList($params=array()){
		$vids = array();
		try{
			$str = '';
			// if(isset($params['type']) && $params['type']=="me" && isset($params['uid']))	$str .= ' and uid={uid}';
			// if(isset($params['type']) && $params['type']=="me" && isset($params['uid']))	$str .= ' and uid={uid}';
			if(isset($params['vids']))	$str .= sprintf(' and a.vid in ({vids})');
			if(isset($params['excludes']) && $params['excludes']!="")	$str .= sprintf(' and a.vid not in ({excludes})');
			if(isset($params['active']) && $params['active']==1)	$str .= sprintf(' and a.vstatus in (1)');
			else if(isset($params['active']) && $params['active']==2)	$str .= sprintf(' and a.vstatus in (1,2)');
			else $str .= sprintf(' and a.vstatus in (0,1,2,3)');


			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select a.vid from jw_videos a,jw_video_stats b,jw_videos_info c where a.vid=b.vid and a.vid=c.vid $str order by vid desc limit 0,40",$str);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,'getVidsList',$params,1);
			if($error==''){
				foreach($arr as $row){
					array_push($vids,$row['vid']);
				}
			}
		}
		catch(Exception $ex){ }
		return $vids;
	}

	public function getVideoList($params=array()){
		$arr = array();
		try{
			$str = '';
			// if(isset($params['type']) && $params['type']=="me" && isset($params['uid']))	$str .= ' and uid={uid}';
			// if(isset($params['type']) && $params['type']=="me" && isset($params['uid']))	$str .= ' and uid={uid}';
			if(isset($params['vids']))	$str .= sprintf(' and a.vid in ({vids})');
			if(isset($params['excludes']) && $params['excludes']!="")	$str .= sprintf(' and a.vid not in ({excludes})');
			if(isset($params['active']) && $params['active']==1)	$str .= sprintf(' and a.vstatus in (1)');
			else if(isset($params['active']) && $params['active']==2)	$str .= sprintf(' and a.vstatus in (1,2)');
			else $str .= sprintf(' and a.vstatus in (0,1,2,3)');


			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select a.vid,a.uid,a.cid,a.path,a.thumb,a.firstpic,a.is_active,a.owner,a.size,a.duration,a.created_on,b.num_tips,c.vname,c.vurl,c.vdesc,a.sstatus,a.vstatus,c.sale_amount,a.iscdn,a.hls_path,c.isold from jw_videos a,jw_video_stats b,jw_videos_info c where a.vid=b.vid and a.vid=c.vid  $str order by vid desc limit 0,40",$str);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,'getVideoList',$params,1);
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getNonCdnVideoList(){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select a.vid,a.path,a.thumb,a.firstpic,a.is_active from jw_videos a where a.iscdn=0 and is_active=1");
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,'getNonCdnVideoList',array(),1);
		}
		catch(Exception $ex){ }
		return $arr;
	}	

	public function getTipsWithUid($tip_ids){
		$final_arr = array();
		$uids = array();
		try{
			$params = array('tip_ids'=>implode(",",$tip_ids));
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select * from jw_tips where id in ({tip_ids})");
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,'getTipsWithUid',$params);
			if($error==''){
				foreach($arr as $row){
					$tip = new Tip();
					$tip->setObject($row);
					array_push($uids,$tip->getValue('uid'));
					$final_arr[$tip->getValue('id')] = $tip;
				}
			}
		}
		catch(Exception $ex){ }
		return array($final_arr,$uids);
	}

	public function getUsers($uids){
		$final_arr = array();
		try{
			$params = array('uids'=>implode(",",$uids));
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select * from jw_users where uid in ({uids})");
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,'getUsers',$params);
			if($error==''){
				foreach($arr as $row){
					$final_arr[$row['uid']] = $row;
				}
			}
		}
		catch(Exception $ex){ }
		return $final_arr;
	}

}