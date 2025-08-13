<?php 
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class Notification extends DataModel{

	public function __construct(){
		$this->db_name = 'notification';
		$this->table_name = 'notification';
		$this->primary_key = 'nid';

		$this->datamodel['nid']=0;
		$this->datamodel['uid']=0;
		$this->datamodel['template']='';
		$this->datamodel['page']='';
		$this->datamodel['iid']='';
		$this->datamodel['info']='';
		$this->datamodel['is_processed']=0;
		$this->datamodel['is_active']=1;
	}

}