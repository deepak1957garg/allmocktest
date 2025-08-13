<?php 
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class ChatMessage extends DataModel{

	public function __construct(){
		$this->db_name = 'notification';
		$this->table_name = 'chat_notifications';
		$this->primary_key = 'msgId';

		$this->datamodel['msgId']="";
		$this->datamodel['sender']="";
		$this->datamodel['receiver']="";
		$this->datamodel['convId']="";
		$this->datamodel['time']=0;
		$this->datamodel['message']="";
		$this->datamodel['vid']=0;
		$this->datamodel['vname']="";
		$this->datamodel['price']=0;
		$this->datamodel['status']=0;
		$this->datamodel['pic']="";
		$this->datamodel['gif']="";
	}

}