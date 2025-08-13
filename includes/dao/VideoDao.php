<?php
include_once dirname(__FILE__) . '/../db/DBWrapper.php';

class VideoDao{

	function __construct(){
	}

	public function saveVideo($uid,$video_name,$tags,$info){
		$success = false;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('jalwa');
			$params = array('uid'=>$uid,'video_name'=>$video_name,'tags'=>$tags,'info'=>$info);
			$q = sprintf("insert into  `videos-master` (uid,img,tagslist,mediainfo) values ({uid},'{video_name}','{tags}','{info}')");
			list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,'saveVideo',$params);
		}
		catch(Exception $ex){ }
		return $success;
	}

}