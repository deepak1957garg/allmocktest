<?php 
include_once dirname(__FILE__) . '/../../modules/general/models/DataModel.php';

class ServiceProvider extends DataModel{

	public function __construct(){
		$this->table_name = 'service_providers';
		$this->primary_key = 'puid';

		$this->datamodel['puid']=0;   //provider id   
		$this->datamodel['name']="";
		$this->datamodel['type']="";  //doctors/health coach
		$this->datamodel['speciality']="";
		$this->datamodel['category']="";
		$this->datamodel['pic']="";
		$this->datamodel['education']="";
		$this->datamodel['languages']="";
		$this->datamodel['reg_no']="";
		$this->datamodel['rating']=0;
		$this->datamodel['cc']='';
		$this->datamodel['mobile']='';
		$this->datamodel['email']='';
		$this->datamodel['whatsapp']='';
		$this->datamodel['num_patient']="0";
		$this->datamodel['num_consultations']="";
		$this->datamodel['experience']="";
		$this->datamodel['fees']=0;
		$this->datamodel['about']="";
		$this->datamodel['vchat_url'] = "";
		$this->datamodel['timings']=array();
		$this->datamodel['timings']['days']=array();
		$this->datamodel['timings']['time']="";
		$this->datamodel['available_slots']=array();
	}

	public function addTimingsInfo($key,$values){
		$this->datamodel['timings'][$key] = $values;
	}

	public function getMinObject($format=""){
		unset($this->datamodel['available_slots']);
		unset($this->datamodel['mobile']);
		unset($this->datamodel['cc']);
		unset($this->datamodel['whatsapp']);
		return $this->getObject($format);
	}

	public function getMinHealthCoachObject($format=""){
		unset($this->datamodel['available_slots']);
		unset($this->datamodel['timings']);
		return $this->getObject($format);
	}

	public function getfullObject($format=""){
		unset($this->datamodel['mobile']);
		unset($this->datamodel['cc']);
		unset($this->datamodel['whatsapp']);
		return $this->getObject($format);
	}	

}