<?php
include_once dirname(__FILE__) . '/../../../includes/config/Config.php';
include_once dirname(__FILE__) . '/../dao/EduCardReadDao.php';
include_once dirname(__FILE__) . '/../dao/EduCardWriteDao.php';
include_once dirname(__FILE__) . '/../cache/EduCardCachingDao.php';
include_once dirname(__FILE__) . '/../models/UserEduCard.php';
include_once dirname(__FILE__) . '/../models/EduCard.php';
include_once dirname(__FILE__) . '/../models/MyCard.php';
include_once dirname(__FILE__) . '/../../general/classes/Utils.php';

class EduCardManager{
	private $cread;
	private $cwrite;
	private $ccache;

	public function __construct(){
		$this->cread = new EduCardReadDao();
		$this->cwrite = new EduCardWriteDao();
		$this->ccache = new EduCardCachingDao();
	}

	public function getAndCreateUserCards($uid){
		$type_factors=array('concept'=>3,'application'=>2,'optimization'=>1);
		$object = new UserEduCard();
		$updateList = array();
		$addList = array();
		$oldlist = array();

		$topics_swot = $this->cread->getUserTopicWithSwot($uid);
		$tids = array_keys($topics_swot);
		$cards = $this->cread->getTopicCards($tids);
		$newlist = $cards;

		$list = $this->cread->getList($object,array('uid'=>$uid));
		foreach($list as $obj){
			$key = $obj['card_id'];
			$oldlist[$key] = $obj;
		}
		foreach($newlist as $obj){
			$key = $obj['card_id'];
			if(isset($oldlist[$key])){
				$obj['id'] = $oldlist[$key]['id'];
				$ischanged = 0;
				if(isset($topics_swot[$obj['topic_id']])){
					$swot = 3 - round($topics_swot[$obj['topic_id']]);
					if($swot!=$oldlist[$key]['swot_factor']){
						$oldlist[$key]['swot_factor'] = $swot;
						$ischanged = 1;
					}
				}
				$importance_factor = $obj['percentile']>66.66 ? 5 : ($obj['percentile']>33.33 ? 3 : 1);
				if($oldlist[$key]['importance_factor']!=$importance_factor){
					$oldlist[$key]['importance_factor']=$importance_factor;
					$ischanged = 1;
				}
				$type_factor = 0;
				if(isset($type_factors[$obj['type']]))	$type_factor = $type_factors[$obj['type']];
				if($oldlist[$key]['type_factor']!=$type_factor){
					$oldlist[$key]['type_factor']=$type_factor;
					$ischanged = 1;
				}

				if($ischanged==1){
					array_push($updateList,$oldlist[$key]);
				}
			}
			else{
				$object1 = new UserEduCard();
				$object1->setObject($obj);
				$object1->setValue('uid',$uid);
				$importance_factor = $obj['percentile']>66.66 ? 5 : ($obj['percentile']>33.33 ? 3 : 1); 
				$object1->setValue('importance_factor',$importance_factor);
				if(isset($topics_swot[$obj['topic_id']])){
					$swot = 3 - round($topics_swot[$obj['topic_id']]);
					$object1->setValue('swot_factor',$swot);
				}
				array_push($addList,$object1);
			}
		}
		//print_r(count($addList));
		if(count($addList)>0)	$this->cwrite->createMultipleObject($addList);

		foreach($updateList as $update){
			$object1 = new UserEduCard();
			$object1->setObject($update);
			$changes = array();
			$changes['importance_factor'] = $update['importance_factor'];
			$changes['swot_factor'] = $update['swot_factor'];
			$changes['type_factor'] = $update['type_factor'];
			$this->cwrite->updateObject($object1,$changes);
		}
	}

	public function getUserCards($uid,$topic_id=0){
		$object = new UserEduCard();
		$list = $this->cread->getUserCardsToBeShown($uid);
		$total_cards = $this->cread->getCountUserCardsToBeShown($uid);
		$arr = array();
		foreach($list as $obj){
			$card = new MyCard();
			$card->setObject($obj);
			array_push($arr,$card->getInfo());
		}
		return array($arr,$total_cards);
	}

	public function saveResponse($id,$action){
		$object = new UserEduCard();
		try{
			$object = $this->cread->getObject($object,array('id'=>$id));
			if($object->getValue('id')!="0"){
				$object = $this->setCarDAnkiData($object,$action);
				$changes = array();
				$changes['ease_factor'] = $object->getValue('ease_factor');
				$changes['repetition'] = $object->getValue('repetition');
				$changes['repetition_date'] = $object->getValue('repetition_date');
				$changes['show_interval'] = $object->getValue('show_interval');
				$changes['last_chosen_option'] = $action;
				$this->cwrite->updateObject($object,$changes);
			}

		}
		catch(Exception $ex){ }
		return $object;
	}

	public function setCarDAnkiData($object,$action){
		$quality = 1;
		if($action==1)	$quality = 5;
		else if ($action==2)	$quality = 3;

		if($quality<=3){
			$object->setValue('repetition',0);
			$object->setValue('show_interval',1);
		}
		else{
			if($object->getValue('repetition') == 0){
				$object->setValue('show_interval',1);
			}
			else if($object->getValue('repetition') == 1){
				$object->setValue('show_interval',6);
			}
			else{
				$interval = ceil($object->getValue('show_interval') *  $object->getValue('ease_factor'));
				$object->setValue('show_interval',$interval);
			}
		}
		$ease_factor = $object->getValue('ease_factor') + (0.1 - (5 - $quality) * (0.08 + (5 - $quality) * 0.02));
		$ease_factor = $ease_factor > 1.3 ? $ease_factor : 1.3;
		$repetition = intval($object->getValue('repetition')) + 1;

		$object->setValue('ease_factor',$ease_factor);
		$object->setValue('repetition',$repetition);
		$object->setValue('repetition_date',date("Y-m-d 00:00:00",(strtotime('now') + ($object->getValue('show_interval')*86400))));
		return $object;
	}

}