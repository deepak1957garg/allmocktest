<?php

class DataModel{

	protected $datamodel = array();

	public $RES_FORMAT_ARRAY	= 'array';
	public $RES_FORMAT_JSON		= 'json';
	public $RES_FORMAT_STDOBJ 	= 'stdobject';
	public $RES_FORMAT_OBJ 		= 'object';
	public $RES_FORMAT_XML 		= 'xml';

	function __construct(){
	}

	public function getValue($field){
		if(isset($this->datamodel[$field]))	return $this->datamodel[$field];
		else return '';	
	}

	public function setValue($field,$value){
		if(isset($this->datamodel[$field]) &&  !is_null($value))	$this->datamodel[$field]=$value;
	}

	public function getObject($format=''){
		if($format==$this->RES_FORMAT_JSON) return json_encode($this->datamodel);
		else if($format==$this->RES_FORMAT_STDOBJ) return (object) $this->datamodel;
		else if($format==$this->RES_FORMAT_OBJ) return json_decode(json_encode($this->datamodel));
		else return $this->datamodel;
	}

	public function setObject($object){
		foreach($object as $field=>$value){
			if(isset($this->datamodel[$field])){
				$this->setValue($field,$value);
			}
		}
	}

}