<?php
include_once dirname(__FILE__) . '/../../../includes/config/Config.php';
include_once dirname(__FILE__) . '/../dao/EduCardReadDao.php';
include_once dirname(__FILE__) . '/../dao/EduCardWriteDao.php';
include_once dirname(__FILE__) . '/../cache/EduCardCachingDao.php';
include_once dirname(__FILE__) . '/../models/UserEduCard.php';
include_once dirname(__FILE__) . '/../models/EduCard.php';
include_once dirname(__FILE__) . '/../../question/models/QuestionTopics.php';
include_once dirname(__FILE__) . '/../../general/classes/Utils.php';

class EduCardCreator{
	private $cread;
	private $cwrite;
	private $ccache;
	private $topics = array();

	public function __construct(){
		$this->cread = new EduCardReadDao();
		$this->cwrite = new EduCardWriteDao();
		$this->ccache = new EduCardCachingDao();
	}

	public function getTopics($subject){
		$object = new QuestionTopics();
		$list = $this->cread->getList($object,array('subject'=>$subject));
		foreach($list as $obj){
			$this->topics[strtolower($obj['topic_name'])]	= $obj;
		}
	}

	function saveData($arr){
		$subject = array("QA"=>1,"VARC"=>2,"DILR"=>3);
		$object = new EduCard();
		$object->setObject($arr);
		$arr['topic'] = str_replace('__','_&_',$arr['topic']);
		if(!isset($this->topics[strtolower($arr['topic'])])){
			$topic = new QuestionTopics();
			$topic->setValue('topic_name',strtolower($arr['topic']));
			$topic->setValue('subject',strtoupper($arr['category']));
			$topic = $this->cwrite->createObject($topic);
			$this->topics[strtolower($topic->getvalue('topic_name'))] = $topic->getObject();
			// print_r($topic->getObject());
			// print_r($arr);
			// print_r(strtolower($topic->getvalue('topic_name')));
			// exit();
		}
		$object->setValue('topic_id',$this->topics[strtolower($arr['topic'])]['topic_id']);
		$object->setValue('subject_id',$subject[strtoupper($arr['category'])]);
		$object->setValue('type',$arr['type_of_card']);
		$object = $this->cwrite->createObject($object,true);
	}

	public function putDataInTempTable($arr){


		// $arr = $this->saveQuestionGroup($arr);
		// $arr = $this->saveQuestion($arr);
		// $arr = $this->saveQuestionTopics($arr);
		// //$arr = $this->saveQuestionTopics($arr);
		// // $arr = $this->saveQuestionSubTopics($arr);
		// $arr = $this->saveQuestionMetaData($arr);
		// $arr = $this->saveQuestionSolutions($arr);
		// $arr = $this->saveQuestionVerification($arr);


		// // $ques_temp = new QuestionTempData();
		// // $ques_temp->setObject($arr);
		// // $ques_temp = $this->cwrite->createObject($ques_temp,true);
	}

	public function saveQuestionGroup($arr){
		$group =  new QuestionGroups();
		if(isset($arr['uuid']) && $arr['uuid']!=""){
			$group->setValue('group_name',$arr['uuid']);
			if(!isset($group_arr[$arr['uuid']])){
				if(isset($arr['passage']) && $arr['passage']!=""){
					$group->setValue('paragraph',$arr['passage']);
					$group->setValue('group_type','para');
				}
				if(isset($arr['image_url']) && $arr['image_url']!=""){
					if(!isset($image_arr[$group->getValue('group_name')])){
						if(strpos($arr['image_url'], "q")){
							$temp = explode("?",$arr['image_url']);
							$arr['image_url'] = $temp[0];
						}

						$localcontainer = str_replace("/modules/question/classes", "/uploads/pics/",dirname(__FILE__));
						error_log(print_r(dirname(__FILE__),1));
						error_log(print_r($localcontainer,1));
						$guid=Utils::generateUID();
						$ext_arr = explode(".",$arr['image_url']);
						$ext = $ext_arr[count($ext_arr) - 1];
						$tempname=$guid . '.' . $ext;
						error_log(print_r(str_replace(' ', "%20", $arr['image_url']),1));
						error_log(print_r($localcontainer.$tempname,1));
						copy(str_replace(' ', "%20", $arr['image_url']),$localcontainer.$tempname);
						$group->setValue('pic',$tempname);
						if($group->getValue('group_type')=='para'){
							$group->setValue('group_type','both');
						}
						else{
							$group->setValue('group_type','image');
						}
					}
				}
				$group = $this->cread->getObject($group,array('group_name'=>$group->getValue('group_name')));
				if($group->getValue('group_id')=="0"){
					$group = $this->cwrite->createObject($group);
				}
				$group_arr[$arr['uuid']] = $group->getValue('group_id');
			}
			else{
				$group->setValue('group_id',$group_arr[$arr['uuid']]);
			}
		}
		print_r("######" . $group->getValue('group_id') . "######");
		$arr['group_id'] = $group->getValue('group_id');
		return $arr;
	}

	public function saveQuestion($arr){
		$object =  new Questions();
		if(isset($arr['question']) && $arr['question']!=""){
			$object->setValue('question_text',$arr['question']);
		}
		else if(isset($arr['question_body']) && $arr['question_body']!=""){
			$object->setValue('question_text',$arr['question_body']);
		}
		if(isset($arr['options']) && is_array($arr['options']) && count($arr['options'])==0){
			$object->setValue('question_type','TITA');
		}
		else if(isset($arr['options']) && is_array($arr['options']) && count($arr['options'])>=2){
			$object->setValue('question_type','MCQ');
		}
		else{
			$object->setValue('question_type','Non-MCQ');
		}
		if(isset($arr['actual_answer']) && $arr['actual_answer']!=""){
			$object->setValue('correct_answer',$arr['actual_answer']);
		}
		else if(isset($arr['answer']) && $arr['answer']!=""){
			$object->setValue('correct_answer',$arr['answer']);
			$arr['actual_answer'] = $arr['answer'];
		}
		if(isset($arr['group_id']) && $arr['group_id']!=""){
			$object->setValue('group_id',$arr['group_id']);
		}
		$object = $this->cwrite->createObject($object);

		$arr['question_id'] = $object->getValue('question_id');
		if($object->getValue('question_type')=="MCQ"){
			$this->saveQuestionOptions($object->getValue('question_id'),$arr['options'],$arr['actual_answer']);
		}
		return $arr;
	}

	public function saveQuestionMetaData($arr){
		$object =  new QuestionMetadata();
		if(isset($arr['question_id']) && $arr['question_id']!=""){
			$object->setValue('question_id',$arr['question_id']);
		}
		$object->setValue('course_id',"1");
		$object->setValue('exam_id',"1");
		$object->setValue('subject_id',"1");
		if(isset($arr['category']) && $arr['category']=='dilr'){
			$object->setValue('subject_id',"3");
		}
		else if(isset($arr['category']) && ($arr['category']=='verbal' || $arr['category']=='varc')){
			$object->setValue('subject_id',"2");
		}
		if(isset($arr['subject_id']) && $arr['subject_id']!="0"){
			$object->setValue('subject_id',$arr['subject_id']);
		}
		if(isset($arr['topic_id']) && $arr['topic_id']!=""){
			$object->setValue('topic_id',$arr['topic_id']);
		}
		if(isset($arr['difficulty']) && $arr['difficulty']!=""){
			$object->setValue('difficulty',$arr['difficulty']);
		}
		if(isset($arr['source']) && $arr['source']!=""){
			$object->setValue('source',$arr['source']);
		}
		if(isset($arr['year']) && $arr['year']!=""){
			$object->setValue('year',$arr['year']);
		}
		if(isset($arr['slot']) && $arr['slot']!=""){
			$object->setValue('slot',$arr['slot']);
		}
		$object = $this->cwrite->createObject($object,true);
		return $arr;
	}

	public function saveQuestionOptions($qid,$options,$actual_answer){
		$objects = array();

		for($i=0;$i<count($options);$i++){
			$object =  new QuestionOptions();
			$object->setValue('option_text',$options[$i]);
			$object->setValue('option_number',($i + 1));
			$object->setValue('question_id',$qid);
			if($options[$i]==$actual_answer){
				$object->setValue('is_correct',1);
			}
			array_push($objects,$object);
		}
		$this->cwrite->createMultipleObject($objects);
	}

	public function saveQuestionSolutions($arr){
		$object =  new QuestionSolutions();
		if(isset($arr['question_id']) && $arr['question_id']!=""){
			$object->setValue('question_id',$arr['question_id']);
		}
		if(isset($arr['generated_solution']) && $arr['generated_solution']!=""){
			$object->setValue('solution',$arr['generated_solution']);
		}
		if(isset($arr['actual_answer']) && $arr['actual_answer']!=""){
			$object->setValue('answer',$arr['actual_answer']);
		}
		else if(isset($arr['answer']) && $arr['answer']!=""){
			$object->setValue('answer',$arr['answer']);
		}
		if(isset($arr['predicted_answer']) && $arr['predicted_answer']!=""){
			$object->setValue('predicted_answer',$arr['predicted_answer']);
		}
		if(isset($arr['algorithm']) && $arr['algorithm']!=""){
			$object->setValue('algo',$arr['algorithm']);
		}
		if(isset($arr['solution_image_link']) && $arr['solution_image_link']!=""){
			if(strpos($arr['solution_image_link'], "?")){
				$temp = explode("?",$arr['solution_image_link']);
				$arr['solution_image_link'] = $temp[0];
			}

			$localcontainer = str_replace("/modules/question/classes", "/uploads/pics/",dirname(__FILE__));
			error_log(print_r(dirname(__FILE__),1));
			error_log(print_r($localcontainer,1));
			$guid=Utils::generateUID();
			$ext_arr = explode(".",$arr['solution_image_link']);
			$ext = $ext_arr[count($ext_arr) - 1];
			$tempname=$guid . '.' . $ext;
			error_log(print_r(str_replace(' ', "%20", $arr['solution_image_link']),1));
			error_log(print_r($localcontainer.$tempname,1));
			copy(str_replace(' ', "%20", $arr['solution_image_link']),$localcontainer.$tempname);
			$object->setValue('pic',$tempname);
		}
		$object = $this->cwrite->createObject($object);
		return $arr;
	}

	public function saveQuestionVerification($arr){
		$object =  new QuestionVerification();
		if(isset($arr['question_id']) && $arr['question_id']!=""){
			$object->setValue('question_id',$arr['question_id']);
		}
		if(isset($arr['accuracy']) && $arr['accuracy']==1){
			$object->setValue('is_answer_verified',1);
		}


		$object = $this->cwrite->createObject($object);
		return $arr;
	}

	public function saveQuestionTopics($arr){
		$object =  new QuestionTopics();
		if(isset($arr['topic']) && $arr['topic']!=""){
			if(!isset($topics_mapping[$arr['topic']])){
				$object->setValue('topic_name',$arr['topic']);
				$object->setValue('subject',$this->subject_name);
				$object = $this->cread->getObject($object,array('topic_name'=>$object->getValue('topic_name')));
				if($object->getValue('topic_id')=="0"){
					$object = $this->cwrite->createObject($object);
				}
				$topics_mapping[$object->getValue('topic_name')] = $object;
				$arr['topic_id'] = $object->getValue('topic_id');
			}
			else{
				$object = $topics_mapping[$arr['topic']];
				$arr['topic_id'] = $object->getValue('topic_id');
			}
			if($arr['topic_id']!="0"){
				$this->saveQuestionTopicMapping($arr['question_id'],$arr['topic_id']);
				if(!isset($arr['sub_topic']) || $arr['sub_topic']==""){
					if(isset($arr['cluster']) && count($arr['cluster'])>0){
						$this->saveQuestionClusterMapping($arr['question_id'],$arr['topic_id'],$arr['cluster']);
					}
				}
			}
		}
		return $arr;
	}

	public function saveQuestionSubTopics($arr){
		$object =  new QuestionTopics();
		if(isset($arr['sub_topic']) && $arr['sub_topic']!=""){
			if(!isset($sub_topics_mapping[$arr['sub_topic']])){
				$object->setValue('topic_name',$arr['sub_topic']);
				$object->setValue('subject',$this->subject_name);
				if(isset($arr['category']) && $arr['category']=='dilr'){
					$object->setValue('subject','Data Interpretation & Logical Reasoning');
				}
				else if(isset($arr['category']) && $arr['category']=='verbal'){
					$object->setValue('subject','Verbal Ability & Reading Comprehension');
				}

				if($object->getValue('subject')=='Verbal Ability & Reading Comprehension'){
					$object->setValue('subject_id',"2");
				}
				else if($object->getValue('subject')=='Data Interpretation & Logical Reasoning'){
					$object->setValue('subject_id',"3");
				}
				else if($object->getValue('subject')=='Quantitative Aptitude'){
					$object->setValue('subject_id',"1");
				}


				if(isset($arr['topic_id'])){
					$object->setValue('parent_topic_id',$arr['topic_id']);
				}
				else{
					$object->setValue('parent_topic_id',"0");
				}
				$object = $this->cread->getObject($object,array('topic_name'=>$object->getValue('topic_name'),'parent_topic_id'=>$object->getValue('parent_topic_id')));
				if($object->getValue('topic_id')=="0"){
					$object = $this->cwrite->createObject($object);
				}
				$sub_topics_mapping[$object->getValue('topic_name')] = $object;
				$arr['sub_topic_id'] = $object->getValue('topic_id');
			}
			else{
				$object = $sub_topics_mapping[$arr['topic']];
				$arr['sub_topic_id'] = $object->getValue('topic_id');
			}
			if($arr['sub_topic_id']!="0"){
				$this->saveQuestionTopicMapping($arr['question_id'],$arr['sub_topic_id']);
				if(isset($arr['cluster']) && count($arr['cluster'])>0){
					$this->saveQuestionClusterMapping($arr['question_id'],$arr['sub_topic_id'],$arr['cluster']);
				}
			}
		}
		return $arr;
	}

	public function saveQuestionTopicMapping($qid,$topic_id){
		$object =  new QuestionTopicMapping();
		$object->setValue('question_id',$qid);
		$object->setValue('topic_id',$topic_id);
		$object = $this->cwrite->createObject($object);
	}
	
	public function saveQuestionClusterMapping($qid,$topic_id,$clusters){
		$objects = array();

		for($i=0;$i<count($clusters);$i++){
			$object =  new QuestionClusterMapping();
			$object->setValue('question_id',$qid);
			$object->setValue('topic_id',$topic_id);
			$object->setValue('cluster_id',$clusters[$i]);
			array_push($objects,$object);
		}
		if(count($objects)>0){
			$this->cwrite->createMultipleObject($objects);
		}
	}
}