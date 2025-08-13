<?php
include_once dirname(__FILE__) . '/../db/DBWrapper.php';
include_once dirname(__FILE__) . '/../models/Tip.php';

class VideoReadDao{

	function __construct(){
	}

	function getFeeds(){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select a.uid,b.path,b.thumb as pic,b.firstpic as first,b.webp,a.upic,a.isCdn,a.name,a.cafes,a.availability,a.uname from jw_users a, jw_videos b where a.vid=b.vid and b.is_active=1 and b.isCdn=1 and a.isCdn=1 order by rand() limit 0,50");
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,'getFeeds',array(),1);
		}
		catch(Exception $ex){ }
		return $arr;		
	}


	public function getEvent($vid,$uid,$event){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$params = array('event'=>$event);
			$q = sprintf("select * from  jw_events where  vid=%d and uid=%d and event='{event}' limit 0,1",$vid,$uid);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,$params,1);
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getEventList($vids,$event,$data){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$params = array('event'=>$event,'vids'=>implode(",",$vids));
			$q = sprintf("select * from jw_events where event='{event}' and vid in ({vids}) and data=%d",$data);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,$params,1);
		}
		catch(Exception $ex){ }
		return $arr;
	}	

	public function getEventCount($vid,$event,$value){
		$count = 0;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$params = array('event'=>$event,'value'=>$value);
			$q = sprintf("select count(id) from  jw_events where vid=%d and event='{event}' and data='{value}'",$vid);
			list($row,$error,$error_no) = DBWrapper::getSingleRow($dbinfo,$q,'getEventCount',$params,1);
			if($error==''){
				$count = $row[0];
			}
		}
		catch(Exception $ex){ }
		return $count;
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

	public function getVideoList($params=array()){
		$arr = array();
		try{
			$str = '';
			// if(isset($params['type']) && $params['type']=="me" && isset($params['uid']))	$str .= ' and uid={uid}';
			// if(isset($params['type']) && $params['type']=="me" && isset($params['uid']))	$str .= ' and uid={uid}';
			if(isset($params['vids']))	$str .= sprintf(' and a.vid in ({vids})');
			if(isset($params['excludes']) && $params['excludes']!="")	$str .= sprintf(' and a.vid not in ({excludes})');
			if(isset($params['active']) && $params['active']==1)	$str .= sprintf(' and a.vstatus in (1)');
			//else if(isset($params['active']) && $params['active']==0)	$str .= sprintf(' and a.vstatus in (2)');
			else if(isset($params['active']) && $params['active']==2)	$str .= sprintf(' and a.vstatus in (1,2)');
			else $str .= sprintf(' and a.vstatus in (0,1,2,3)');


			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select a.vid,a.uid,a.path,a.thumb,a.firstpic,a.is_active,a.size,a.duration,a.created_on,b.num_tips,a.vname,a.vdesc,a.vstatus,a.sale_amount,a.iscdn,a.old_path,a.isold,a.issold,a.itype,a.gif,a.curr from jw_videos a,jw_video_stats b where a.vid=b.vid $str order by vid desc limit 0,40",$str);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,'getVideoList',$params,1);
		}
		catch(Exception $ex){ }
		return $arr;
	}

		public function getVideoList2($params=array()){
		$arr = array();
		try{
			$str = '';
			// if(isset($params['type']) && $params['type']=="me" && isset($params['uid']))	$str .= ' and uid={uid}';
			// if(isset($params['type']) && $params['type']=="me" && isset($params['uid']))	$str .= ' and uid={uid}';
			if(isset($params['vids']))	$str .= sprintf(' and a.vid in ({vids})');
			if(isset($params['excludes']) && $params['excludes']!="")	$str .= sprintf(' and a.vid not in ({excludes})');
			if(isset($params['active']) && $params['active']==1)	$str .= sprintf(' and a.vstatus in (1)');
			//else if(isset($params['active']) && $params['active']==0)	$str .= sprintf(' and a.vstatus in (2)');
			else if(isset($params['active']) && $params['active']==2)	$str .= sprintf(' and a.vstatus in (1,2)');
			else $str .= sprintf(' and a.vstatus in (0,1,2,3)');


			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select a.vid,a.uid,a.path,a.thumb,a.firstpic,a.is_active,a.size,a.duration,a.created_on,a.vname,a.vdesc,a.vstatus,a.sale_amount,a.iscdn,a.old_path,a.isold,a.issold,a.itype,a.gif,a.curr from jw_videos a where $str order by vid desc limit 0,40",$str);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,'getVideoList',$params,1);
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getVideo($vid){
		$video = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select a.vid,a.uid,a.path,a.thumb,a.firstpic,a.is_active,a.size,a.duration,a.created_on,b.num_tips,a.vname,a.vdesc,a.vstatus,a.sale_amount,a.iscdn,a.old_path,a.isold,a.issold,a.itype from jw_videos a,jw_video_stats b where a.vid=b.vid and a.vid=%d",$vid);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array());
			if($error==''){
				if(count($arr)>0)	$video = $arr[0];
			}
		}
		catch(Exception $ex){ }
		return $video;
	}

	public function getNonCdnVideoList(){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select a.vid,a.path,a.thumb,a.firstpic,a.is_active,a.uid,a.duration from jw_videos a where a.iscdn=0 and is_active=1");
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,'getNonCdnVideoList',array(),1);
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getNonCdnPicList(){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$q = sprintf("select a.uid,a.upic from jw_users a where a.isCdn=0 and isactive=1 and upic!=''");
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,'getNonCdnPicList',array(),1);
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