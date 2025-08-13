<?php
include_once dirname(__FILE__) . '/../dao/VideoReadDao.php';
include_once dirname(__FILE__) . '/../models/Feed.php';
include_once dirname(__FILE__) . '/../../modules/user/dao/UserReadDao.php';

class VideoListManager{
	private $vreadobj;

	public function __construct(){
		$this->vreadobj = new VideoReadDao();
	}

	public function getFeeds($params){
		$final_arr = array();
		$vlist = $this->vreadobj->getFeeds();
		$uids = $this->getAllActiveBookingUsers($params['uid']);
		$uids_list = array_flip($uids);
		for($i=0;$i<count($vlist);$i++){
			$video =  $vlist[$i];
			$video_obj = new Feed();
			$video_obj->setObject($video);
			if(isset($params['uid']) && $params['uid']==$video_obj->getValue('uid')){ }
			else if(isset($uids_list[$video_obj->getValue('uid')])){ }
			else array_push($final_arr,$video_obj->getInfo());
		}
		if(count($final_arr)<2){
			$final_arr = array();
			for($i=0;$i<count($vlist);$i++){
				$video =  $vlist[$i];
				$video_obj = new Feed();
				$video_obj->setObject($video);
				if(isset($params['uid']) && $params['uid']==$video_obj->getValue('uid')){ }
				else array_push($final_arr,$video_obj->getInfo());
			}
		}
		return $final_arr;
	}

	public function getAllActiveBookingUsers($uid){
		$uids = array();
		$uread = new UserReadDao();
		$list1 = $uread->getBookingsBySeller($uid);
		$list2 = $uread->getBookingsByCustomer($uid);
		$list = array_values(array_merge($list1,$list2));
		foreach($list as $booking){
			if(in_array($booking['status'],array(0,1,4,5,6))){
				array_push($uids,$booking['seller']);
				array_push($uids,$booking['customer']);
			}
		}
		$uids = array_values(array_unique($uids));
		return $uids;
	}

}
?>