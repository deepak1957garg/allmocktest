<?php 
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class UserNotification extends DataModel{

	public function __construct(){
		$this->db_name = 'notification';
		$this->table_name = 'user_notifications';
		$this->primary_key = 'id';

		$this->datamodel['id']=0;
		$this->datamodel['nid']=0;
		$this->datamodel['uid']=0;
		$this->datamodel['is_seen']=0;
		$this->datamodel['is_sent']=0;
	}

}