<?php 

class Location{
	private $location_id;
	private $lat;
	private $long;
	private $location_text;
	private $distance_from;

	public function __construct(){
		$this->location_id = 0;
		$this->lat="";
		$this->long="";
		$this->location_text="";
		$this->distance_from="";
	}

	public function setLocation($id,$lat,$long,$text,$distance_from){
		$this->location_id = $id;
		$this->lat = $lat;
		$this->long = $long;
		$this->text = $text;
		$this->distance_from = $distance_from;
	}

	public function toJson(){ //,"ip_id":"'.$this->ip_id.'"
		return '{"location_id":"'.$this->location_id.'","lat":"'.$this->lat.'","long":"'.$this->long.'","location_text":"'.$this->location_text.'","distance_from":"'.$this->distance_from.'"}';
	}

}
// $loc = new Location();
// $loc->setLocation(1,"19.03493","28.43","Chayos Hiranandani powai","2 KM");
// print_r($loc);
// print_r($loc->toJson());