<?php 
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';
include_once dirname(__FILE__) . '/../../../includes/config/Config.php';

class Memes extends DataModel{

	public function __construct(){
		$this->table_name = 'memes';
		$this->primary_key = 'id';

		$this->datamodel['id']=0;
		$this->datamodel['img']='';
		$this->datamodel['name']='';
	}

	public function setUrl($img_name){
		$this->datamodel['img'] = Config::$SERVER_URL . '/up/memes/' . $img_name;
	}

}