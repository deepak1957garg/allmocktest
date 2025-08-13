<?php
include_once dirname(__FILE__) . '/../../modules/general/models/DataModel.php';

class VideoModel extends DataModel{

	public function __construct(){
		$this->datamodel['vid']=0;
		$this->datamodel['uid']=0;
		$this->datamodel['vname']='';
		$this->datamodel['vdesc']='';
		$this->datamodel['slug']='';
		$this->datamodel['path']='';
		$this->datamodel['old_path']='';
		$this->datamodel['iscdn']=0;
		$this->datamodel['size']=0;
		$this->datamodel['duration']=0;
		$this->datamodel['thumb']="";
		$this->datamodel['firstpic']="";
		$this->datamodel['width']=0;
		$this->datamodel['height']=0;
		$this->datamodel['vstatus']=0;  //'0' COMMENT '0 - aprroval pending, 1 - approved, 2 - partially approved, 3 - rejected,4 - removed by user, 5 - removed due to copyright or violation',
		$this->datamodel['sale_amount']=0;
		$this->datamodel['curr']="";
		$this->datamodel['isold']=0;
		$this->datamodel['issold']=0;
		$this->datamodel['itype']=1;
		$this->datamodel['is_active']=1;
		$this->datamodel['created_on']='';
		$this->datamodel['updated_on']='';
	}

}
?>