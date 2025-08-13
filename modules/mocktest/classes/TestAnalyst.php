<?php
include_once dirname(__FILE__) . '/../../../includes/config/Config.php';
include_once dirname(__FILE__) . '/../dao/MockTestReadDao.php';
include_once dirname(__FILE__) . '/../dao/MockTestWriteDao.php';
include_once dirname(__FILE__) . '/../cache/MockTestCachingDao.php';
include_once dirname(__FILE__) . '/../models/MockTestAttempt.php';
include_once dirname(__FILE__) . '/../models/TopicStats.php';
include_once dirname(__FILE__) . '/../models/MockTest.php';
include_once dirname(__FILE__) . '/../models/Order.php';
include_once dirname(__FILE__) . '/../models/ExamTemplate.php';
include_once dirname(__FILE__) . '/../models/Exam.php';
include_once dirname(__FILE__) . '/../models/UserTest.php';
include_once dirname(__FILE__) . '/../models/TestStats.php';
include_once dirname(__FILE__) . '/../models/TestSectionStats.php';
include_once dirname(__FILE__) . '/../models/SubjectStats.php';
include_once dirname(__FILE__) . '/../models/TestSummary.php';
include_once dirname(__FILE__) . '/../models/QuestionSwotDetails.php';
include_once dirname(__FILE__) . '/../models/QuestionSwot.php';
include_once dirname(__FILE__) . '/../../question/models/Questions.php';
include_once dirname(__FILE__) . '/../../general/classes/Utils.php';

class TestAnalyst{
	private $cread;
	private $cwrite;
	private $ccache;

	public function __construct(){
		$this->cread = new MockTestReadDao();
		$this->cwrite = new MockTestWriteDao();
		$this->ccache = new MockTestCachingDao();
	}


	public function getQuestionsList($uid,$test_id="0",$sub,$diff="",$topic_id="0"){
		$arr = array();
		$questions = array();
		try{
			$list = array();
			$qids = array();
			if($test_id!="0"){
				$list = $this->getTestQuestionsSwot($uid,$test_id);
			}
			else if($topic_id!="0"){
				$list = $this->getTopicQuestionsSwot($uid,$topic_id,$diff);
			}
			else{
				$list = $this->getSubjectQuestionsSwot($uid,$sub,$diff);
			}
			foreach($list as $obj){
				array_push($qids,$obj['question_id']);
			}
			if(count($qids)>0){
				$questions = $this->cread->getQuestionData($qids);
				$options = $this->cread->getQuestionOptions($qids);
				$topics = $this->cread->getQuestionTopics($qids);
				foreach($options as $key=>$option){
					if(isset($questions[$option['question_id']])){
						array_push($questions[$option['question_id']]['options'],$option);
					}
				}
			}

			foreach($questions as $question){
				$details = new QuestionSwotDetails();
				$details->setObject($question);
				if(isset($list[$question['question_id']])){
					$details->setObject($list[$question['question_id']]);
					$details->setValue('no',$list[$question['question_id']]['question_no']);
					$details->setValue('qid',$question['question_id']);
				}
				if(isset($topics[$question['question_id']])){
					$details->setObject($topics[$question['question_id']]);
				}
				array_push($arr,$details->getInfo());
			}
			sort($arr);
			//print_r($list);
		}
		catch(Exception $ex){ }
		return $arr;
	}

	private function getTestQuestionsSwot($uid,$test_id="0"){
		$arr = array();
		$object = new QuestionSwot();
		$list = $this->cread->getList($object,array('test_id'=>$test_id,'user_id'=>$uid));
		foreach($list as $obj){
			$arr[$obj['question_id']] = $obj;
		}
		return $arr;
	}

	private function getSubjectQuestionsSwot($uid,$sub,$diff=""){
		$arr = array();
		$object = new QuestionSwot();
		$section_list = array("QA"=>"1","VARC"=>"2","DILR"=>"3");
		$subject_id = 0;
		if(isset($section_list[strtoupper($sub)]))	$subject_id = $section_list[strtoupper($sub)];
		$list = $this->cread->getTestQuestionAnalysis($uid,$subject_id,$diff);
		for($i=0;$i<count($list);$i++){
			$list[$i]['question_no'] = ($i + 1)."";
			$arr[$list[$i]['question_id']] = $list[$i];
		}	
		return $arr;
	}

	private function getTopicQuestionsSwot($uid,$topic_id,$diff=""){
		$arr = array();
		$object = new QuestionSwot();
		// $section_list = array("QA"=>"1","VARC"=>"2","DILR"=>"3");
		// $subject_id = 0;
		// if(isset($section_list[strtoupper($sub)]))	$subject_id = $section_list[strtoupper($sub)];
		$list = $this->cread->getTestTopicQuestionAnalysis($uid,$topic_id,$diff);
		for($i=0;$i<count($list);$i++){
			$list[$i]['question_no'] = ($i + 1)."";
			$arr[$list[$i]['question_id']] = $list[$i];
		}	
		return $arr;
	}

	public function getSubjectStats($sub,$diff,$qlist){
		$stats = new TestSummary();
		$difficulty_name = array('EASY'=>'Easy','MEDIUM'=>'Moderate','HARD'=>'Tough','none'=>'');
		$name = strtoupper($sub);
		if($diff!="" && isset($difficulty_name[strtoupper($diff)])) $name.= " - " . $difficulty_name[strtoupper($diff)];
		$stats->setValue("test_name",$name);
		$stats->setValue("total_questions",count($qlist));
		$stats->setValue("max_marks",count($qlist)*3);
		$marks = 0;
		foreach($qlist as $question){
			$marks += $question['marks'];
		}
		$stats->setValue("marks",$marks);
		return $stats->getObject();
	}

	public function getTopicStats($topic_id,$diff,$qlist){
		$stats = new TestSummary();
		$difficulty_name = array('EASY'=>'Easy','MEDIUM'=>'Moderate','HARD'=>'Tough','none'=>'');
		$topic = $this->cread->getTopicDetails($topic_id);
		$name = strtoupper($topic['subject']) .  " - " . strtoupper($topic['topic']);
		if($diff!="" && isset($difficulty_name[strtoupper($diff)])) $name.= " - " . $difficulty_name[strtoupper($diff)];
		$stats->setValue("test_name",$name);
		$stats->setValue("total_questions",count($qlist));
		$stats->setValue("max_marks",count($qlist)*3);
		$marks = 0;
		foreach($qlist as $question){
			$marks += $question['marks'];
		}
		$stats->setValue("marks",$marks);
		return $stats->getObject();
	}
}
