<?php
include_once dirname(__FILE__) . '/../../../includes/common/Constants.php';
include_once dirname(__FILE__) . '/../models/AttributionParams.php';
include_once dirname(__FILE__) . '/../../user/classes/UserInfoManager.php';
include_once dirname(__FILE__) . '/../../../includes/dao/VideoReadDao.php';


class ShareManager{

	public function __construct(){
	}

	public function getShareReferrer($attributionParams){
		$referrer = "host=" . $attributionParams->getValue('host') . "&iid=" . $attributionParams->getValue('iid') . "&uname=" . $attributionParams->getValue('uname') . "&utm_medium=" . $attributionParams->getValue('uname') . "&utm_campaign=" . $attributionParams->getValue('uname') . "&utm_source=" . $attributionParams->getValue('uname') . "&ref=" . $attributionParams->getValue('ref');
		return $referrer;
	}

	public function getProfileShareDeeplinkUrl($attributionParams){
		$android_url = "things://profile?host=" . $attributionParams->getValue('host') . "&iid=" . $attributionParams->getValue('iid') . "&uname=" . $attributionParams->getValue('uname') . "&utm_medium=" . $attributionParams->getValue('utm_medium') . "&utm_campaign=" . $attributionParams->getValue('utm_campaign') . "&utm_source=" . $attributionParams->getValue('utm_source') . "&ref=" . $attributionParams->getValue('ref');
		return $android_url;
	}

	public function getVideoShareDeeplinkUrl($attributionParams){
		//print_r($attributionParams->getObject());
		$android_url = "things://" . $attributionParams->getValue('host') . "?host=" . $attributionParams->getValue('host') . "&iid=" . $attributionParams->getValue('iid') . "&utm_medium=" . $attributionParams->getValue('utm_medium') . "&utm_campaign=" . $attributionParams->getValue('utm_campaign') . "&utm_source=" . $attributionParams->getValue('utm_source') . "&ref=" . $attributionParams->getValue('ref');
		return $android_url;
	}


	public function getProfileShareData(){
		$params = new AttributionParams();
		$user = new User();

		$params->setValue("host","profile");
		if(isset($_REQUEST['purl']) && is_numeric($_REQUEST['purl']))	$params->setValue("iid",$_REQUEST['purl']);
		$query_params = $this->extractQueryParams();
		foreach($query_params as $key=>$value){
			$params->setValue($key,$value);
		}

		$uinfomanager = new UserInfoManager();
		if($params->getValue("iid")!=""){
			$user = $uinfomanager->getUserByUid($params->getValue("iid"));
			if($user->getValue('name')!="")	$params->setValue("uname",str_replace(" ","%20",$user->getValue('name')));
		}


		return array($params,$user);
	}

	public function getVideoShareData(){
		$params = new AttributionParams();
		$video = array();

		$params->setValue("host","video");
		if(isset($_REQUEST['vurl']) && is_numeric($_REQUEST['vurl']))	$params->setValue("iid",$_REQUEST['vurl']);
		$query_params = $this->extractQueryParams();
		foreach($query_params as $key=>$value){
			$params->setValue($key,$value);
		}

		$vreadobj = new VideoReadDao();
		if($params->getValue("iid")!=""){
			$video = $vreadobj->getVideo($params->getValue("iid"));
		}


		return array($params,$video);
	}

	private function extractQueryParams(){
		$query_params = array();
		$str = "";
		if(isset($_SERVER['REQUEST_URI']))	$str = $_SERVER['REQUEST_URI'];
		if($str!=""){
			$temp = explode("?",$str);
			if(count($temp)>1){
				$str2 = $temp[1];
				$temp2 = explode("&",$str2);
				foreach($temp2 as $query){
					if($query!=""){
						$query_temp = explode("=",$query);
						if(count($query_temp)==2){
							$query_params[$query_temp[0]]=$query_temp[1];
						}
					}
				}
			}
		}
		return $query_params;
	}

}
?>