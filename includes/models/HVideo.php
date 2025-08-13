<?php
include_once dirname(__FILE__) . '/../../modules/general/models/DataModel.php';
include_once dirname(__FILE__) . '/HVideoInfo.php';

class HVideo extends DataModel{

	public function __construct(){
		$this->datamodel['vid']=0;
		$this->datamodel['path']='';
		$this->datamodel['spath']='';
		$this->datamodel['pic']='';
		$this->datamodel['fpic']='';
		$this->datamodel['gif']='';
		$this->datamodel['status']=0;         // 0 - aprroval pending, 1 - approved, 2 - partially approved, 3 - rejected 
		$this->datamodel['vname']="";
		$this->datamodel['vdesc']="";
		$this->datamodel['sale_amount']=0;
		$this->datamodel['price']=0;
		$this->datamodel['isold']=0;
		$this->datamodel['issold']=0;
		$this->datamodel['itype']=1;
		$this->datamodel['uid']=0;
		$this->datamodel['uname']="";
		$this->datamodel['umobile']="";
		$this->datamodel['upic']="";
		$this->datamodel['duration']=0;       //in secs
		$this->datamodel['coll']=0;           //collection
		$this->datamodel['num_tips']=0;       //number of tips
		$this->datamodel['tags']=array();
		$this->datamodel['sale_status']=0;    //sale status -> 0 - no sale, 1 - for sale
		$this->datamodel['sale_amt']=0;       //amount for sale 
		$this->datamodel['user_tip']=0;       //user tip on video
		$this->datamodel['vtype']=0;  		  // 0 for video, 1 for stream
		$this->datamodel['isEndorsed']=0;
		$this->datamodel['isBought']=0;
		$this->datamodel['time']=0;
		$this->datamodel['vinfo']=array();    //name, actors, owner message, urlt, desc, tags
		$this->datamodel['cat']=array();      //cid,cname,curl
		$this->datamodel['users']=array();
		$this->datamodel['curr']='₹';   
	}

	public function setVideoInfo($vinfo){
		$this->datamodel['vinfo'] = $vinfo->getObjectPartially();
	}

	public function setTag($tid,$tname,$turl){
		$tag = array();
		$tag['tid'] = $tid;
		$tag['name'] = '#' . $tname;
		$tag['url'] = 'https://www.thesnug.app/jwtags/' . $turl;
		array_push($this->datamodel['tags'],$tag);
	}

	public function setCategory($cid,$cname,$curl){
		$cat = array();
		$cat['cid'] = $cid;
		$cat['cname'] = '#' . $cname;
		$cat['curl'] = 'https://www.thesnug.app/' . $curl;
		$this->datamodel['cat']=$cat;
	}

	public function setVUser($vuser){
		array_push($this->datamodel['users'],$vuser->getObject());
	}

}
?>