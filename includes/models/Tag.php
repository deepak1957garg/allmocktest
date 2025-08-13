<?php
class Tag{
	private $id;
	private $name;
	private $small_desc;
	private $is_active;
	private $ips;

	public function __construct(){
		$this->id=0;
		$this->name="";
		$this->small_desc="";
		$this->is_active=true;
		$this->ips = "";
	}

	public function setDetails($id,$name,$small_desc,$ips=""){
		$this->id = $id;
		$this->name = $name;
		$this->small_desc = $small_desc;
		$this->ips = $ips;
	}
	public function setIPS($ips){
		$this->ips=$ips;
	}

	public function toJson(){
		return '{"id":"'.$this->id.'","name":"'.$this->name.'","small_desc":"'.$this->small_desc.'","is_active":'.$this->is_active.',"ips":"'.$this->ips.'"}';
	}

	private function generateId(){
		$this->id = ++$this->id;
	}

}