<?php 
include_once dirname(__FILE__) . '/../../general/models/DataModel.php';

class BookingInfo extends DataModel{

	public function __construct(){
		$this->datamodel['id']="0";
		$this->datamodel['seller']="0";
		$this->datamodel['sellerName']="";
		$this->datamodel['sellerPic']="";
		$this->datamodel['sellerWebp']="";
		$this->datamodel['customer']="0";
		$this->datamodel['customerName']="";
		$this->datamodel['customerPic']="";
		$this->datamodel['customerWebp']="";
		$this->datamodel['availability']="";
		$this->datamodel['locations']="";
		$this->datamodel['suggestedAvailability']="";
		$this->datamodel['suggestedLocations']="";
		$this->datamodel['status']="";
		$this->datamodel['code']="";
		$this->datamodel['meeting_date']="";
	}

}