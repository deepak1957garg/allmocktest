<?php
include_once dirname(__FILE__) . '/../../modules/general/models/DataModel.php';

class HVideoUser extends DataModel{

	public function __construct(){
		$this->datamodel['uid']='';
		$this->datamodel['type']='';  //creator, owner, first tipper,latest tipper, top tipper
		$this->datamodel['name']='';
		$this->datamodel['pic']='';
		$this->datamodel['uname']='';
		$this->datamodel['tip_amt']=0;  //tip amount
		$this->datamodel['caption']='';  // caption for type
	}

}
?>