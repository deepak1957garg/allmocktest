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
include_once dirname(__FILE__) . '/../models/QuestionSwotSummary.php';
include_once dirname(__FILE__) . '/../models/QuestionSwot.php';
include_once dirname(__FILE__) . '/../models/TopicByStats.php';
include_once dirname(__FILE__) . '/../../question/models/Questions.php';
include_once dirname(__FILE__) . '/../../general/classes/Utils.php';

class MockTestManager{
	private $cread;
	private $cwrite;
	private $ccache;

	public function __construct(){
		$this->cread = new MockTestReadDao();
		$this->cwrite = new MockTestWriteDao();
		$this->ccache = new MockTestCachingDao();
	}

	// public function getIncompleteNockTests($uid){
	// 	$object = new UserTest();
	// 	try{
	// 		$object = $this->cread->getObject($object,array('uid'=>$uid,'exam_id'=>"0"));
	// 		$changes['exam_id'] = "13";
	// 		$this->cwrite->updateObject($object,$changes);
	// 		$object->setValue('exam_id',13);
	// 	}
	// 	catch(Exception $ex){ }
	// 	return $object;
	// }

	public function getTestStats($test_id){
		$stats = new TestSummary();
		$testStatsObj = new TestStats();
		$testStatsObj = $this->cread->getObject($testStatsObj,array('test_id'=>$test_id));
		$stats->setObject($testStatsObj->getObject());

		$object = new UserTest();
		$object = $this->cread->getObject($object,array('id'=>$test_id));



		$sections = $this->getTestSectionsSummary2($object->getObject());
		$stats->setValue('sections',$sections);
		return $stats->getInfo();
	}

	public function getTemplateInfo($exam_id){
		$template = $this->cread->getTemplateInfo($exam_id);
		return $template;
	}

	public function getUserCompletedTestList($uid){
		$arr = array();
		$testStatsObj = new TestStats();
		$list = $this->cread->getList($testStatsObj,array('uid'=>$uid));
		$testList = $this->getUserTestList($uid);
		rsort($list);
		$test_ids = array();
		foreach($list as $statsObj){
			$obj = new TestSummary();
			$obj->setObject($statsObj);
			$info = $obj->getInfo();
			if(isset($testList[$statsObj['test_id']])){
				$sections = $this->getTestSectionsSummary($testList[$statsObj['test_id']]);
				$obj->setValue('sections',$sections);
			}
			array_push($test_ids,$statsObj['test_id']);
			$arr[$statsObj['test_id']] = $obj->getInfo();
		}
		$test_ids = array_values(array_unique($test_ids));
		$test_questions_list = $this->getQuestionStatsByTests($uid,$test_ids);
		foreach($list as $statsObj){
			if(isset($test_questions_list[$statsObj['test_id']])){
				$arr[$statsObj['test_id']]['questions'] = $test_questions_list[$statsObj['test_id']];
			}
		}
		return array_values($arr);
	}

	private function getTestSectionsSummary2($userTest){
		$arr = array();
		$sections = $this->cread->getSectionsList();
		for($i=1;$i<=3;$i++){
			if(isset($sections[$userTest['section' . $i . '_id']])){
				$obj = array();
				$obj['name'] = $sections[$userTest['section' . $i . '_id']]['section_name'];
				$obj['marks'] = $userTest['section' . $i . '_marks'];
				$obj['max_marks'] = $sections[$userTest['section' . $i . '_id']]['max_marks'];
				array_push($arr,$obj);
			}
		}
		return $arr;
	}

	private function getTestSectionsSummary($userTest){
		$arr = array();
		$sections = $this->cread->getSectionsList();
		for($i=1;$i<=3;$i++){
			if(isset($sections[$userTest['section' . $i . '_id']])){
				$obj = array();
				$obj['name'] = $sections[$userTest['section' . $i . '_id']]['section_name'];
				$obj['marks'] = $userTest['section' . $i . '_marks'];
				array_push($arr,$obj);
			}
		}
		return $arr;
	}

	private function getUserTestList($uid){
		$arr = array();
		$object = new UserTest();
		$list = $this->cread->getList($object,array('uid'=>$uid,'is_completed'=>'1'));
		foreach($list as $obj){
			$arr[$obj['id']] = $obj;
		}
		return $arr;
	}

	private function getExamsQuestions($exam_ids){
		$qids_arr = array();
		$qids_sec = array();
		$section_arr = $this->cread->getTestSectionsQuestions($exam_ids);
		foreach($section_arr as $section){
			if(!isset($qids_arr[$section['exam_id']]))	$qids_arr[$section['exam_id']] = array();
			$qids = explode(",",$section['questions']);
			$qids_arr[$section['exam_id']] = array_merge($qids_arr[$section['exam_id']],$qids);
			foreach($qids as $qid){
				$qids_sec[$qid] = $section['section_id'];
			}
		}
		return array($qids_arr,$qids_sec);
	}

	public function getQuestionStatsByTests($uid,$test_ids){
		$test_arr = array();
		try{
			foreach($test_ids as $test_id){
				$test_arr[$test_id] = array();
			}
			$list = $this->cread->getQuestionStatsByTests($uid,$test_ids);
			foreach($list as $ques){
				$obj = new QuestionSwotSummary();
				$obj->setObject($ques);
				$obj->setValue('no',$ques['question_no']."");
				$obj->setValue('qid',$ques['question_id']."");
				array_push($test_arr[$ques['test_id']],$obj->getInfo());
			}
		}
		catch(Exception $ex){ }
		return $test_arr;
	}

	public function getLastCompleteNockTests($uid){
		$test_arr = array();
		try{
			$section_list = array("1"=>"QA","2"=>"VARC","3"=>"DILR");
			$sections = array();
			$list = $this->cread->getLastCompleteNockTests($uid);
			if(count($list)>0){
				$test = $list[0];
				// $stats = new TestStats();
				// $stats = $this->cwrite->getObject($stats,array('test_id'=>$test["id"]));
				//$this->saveTestStats($test["id"]);
				$avg_time = $test['num_time']/$test['num_questions'];
				$fast_time = $avg_time*0.6;
				$slow_time = $avg_time*1.4;
				$exam_id = $list[0]['exam_id'];
				$section_arr = $this->cread->getTestSections($exam_id);
				$qids = array();
				foreach($section_arr as $section){
					$qids = array_merge($qids,explode(",",$section['questions']));
					$sections[$section['section_name']] = 0;
				}
				$qids = array_values($qids);
				$attempted_questions = $this->cread->getTestAttemptQuestionData($list[0]['id']);
				$total_marks = 0;
				$total_time = 0;
				$total_time_correct = 0;
				$total_ques_correct = 0;
				$total_time_wrong = 0;
				$total_ques_wrong = 0;
				$ques_arr = array();
				for($i=0;$i<count($qids);$i++){
					$obj = array();
					$obj['no'] = ($i + 1)."";
					$obj['qid'] = $qids[$i]."";
					if(isset($attempted_questions[$qids[$i]])){
						$attempt = $attempted_questions[$qids[$i]];
						$obj['status'] = $attempt['status'];
						$obj['marks'] = $attempt['marks']."";
						$obj['time_taken'] = $attempt['time_taken']."";
						$obj['difficulty'] = ucfirst(strtolower($attempt['difficulty']));
						$obj['swot'] = "";
						$obj['speed'] = "Medium";
						$obj['color'] = "#ffffff";
						$obj['status_text'] = "";
						$total_marks += $attempt['marks'];
						$total_time += $attempt['time_taken'];
						$sections[$section_list[$attempt['subject_id']]] += $attempt['marks'];
						if($obj['time_taken']<=$fast_time)	$obj['speed'] = "Fast";
						else if($obj['time_taken']>=$slow_time)	$obj['speed'] = "Slow";
						if($obj['marks']>0 && $obj['speed']=="Fast"){
							$obj['swot'] = "S";
							$obj['color'] = "#47f20c";
						}
						else if($obj['marks']>0 && $obj['speed']!="Fast"){
							$obj['swot'] = "O";
							$obj['color'] = "#fce202";
						}
						else if($obj['marks']<=0 && $obj['speed']=="Fast"){
							$obj['swot'] = "W";
							$obj['color'] = "#ef8544";
						}
						else if($obj['marks']<=0 && $obj['speed']!="Fast"){
							$obj['swot'] = "T";
							$obj['color'] = "#ff2524";
						}
						if($obj['status']=="review" || $obj['status']=="skipped"){
							$obj['swot'] = "";
							$obj['color'] = "#ffffff";
							$obj['status'] = "skipped";
							$obj['marks'] = "";
							$obj['speed'] = "";
							$obj['difficulty'] = "";
							$obj['status_text'] = "";
							//$obj['status_text'] = "Skipped";
						}
						else{
							if($obj['marks']>0){
								$total_time_correct += $attempt['time_taken'];
								$total_ques_correct++;
							}
							else{
								$total_time_wrong += $attempt['time_taken'];
								$total_ques_wrong++;
							}
						}
					}
					else{
						$obj['status'] = "";
						$obj['marks'] = "";
						$obj['time_taken'] = "0";
						$obj['difficulty'] = "";
						$obj['swot'] = "";
						$obj['speed'] = "";
						$obj['color'] = "#f3f3f3";
						$obj['status_text'] = "";
						//$obj['status_text'] = "Unattempted";
					}
					array_push($ques_arr,$obj);
					//$test = $list[0];
				}
				//print_r($sections);
				$section_data = array();
				foreach($sections as $key=>$value){
					$temp = array();
					$temp['name'] = $key;
					$temp['marks'] = $value."";
					array_push($section_data,$temp);
				}
				$test['marks_earned'] = $total_marks;
				$test['time_taken'] = $total_time;
				$test['date'] = date("d M Y, H:i A",(strtotime($test['updated_on'])+19800));
				$test['total_time_correct'] = $total_time_correct;
				$test['total_ques_correct'] = $total_ques_correct;
				$test['total_time_wrong'] = $total_time_wrong;
				$test['total_ques_wrong'] = $total_ques_wrong;
				// $test['avg_time_correct'] = ceil($total_time_correct/$total_ques_correct);
				// $test['avg_time_wrong'] = ceil($total_time_wrong/$total_ques_wrong);




				$test['sections'] = $section_data;
				$test['questions'] = $ques_arr;

				//27 July 2025, 11:32 AM
				array_push($test_arr,$test);
			}
		}
		catch(Exception $ex){ }
		return $test_arr;
	}

	// public getTestSwotsData($test){
	// 	try{
	// 		$section_list = array("1"=>"QA","2"=>"VARC","3"=>"DILR");
	// 		$sections = array();
	// 			$test = $list[0];
	// 			$avg_time = $test['num_time']/$test['num_questions'];
	// 			$fast_time = $avg_time*0.6;
	// 			$slow_time = $avg_time*1.4;
	// 			$exam_id = $list[0]['exam_id'];
	// 			$section_arr = $this->cread->getTestSections($exam_id);
	// 			$qids = array();
	// 			foreach($section_arr as $section){
	// 				$qids = array_merge($qids,explode(",",$section['questions']));
	// 				$sections[$section['section_name']] = 0;
	// 			}
	// 			$qids = array_values($qids);
	// 			$attempted_questions = $this->cread->getTestAttemptQuestionData($list[0]['id']);
	// 			$total_marks = 0;
	// 			$total_time = 0;
	// 			$total_time_correct = 0;
	// 			$total_ques_correct = 0;
	// 			$total_time_wrong = 0;
	// 			$total_ques_wrong = 0;
	// 			$ques_arr = array();
	// 			for($i=0;$i<count($qids);$i++){
	// 				$obj = array();
	// 				$obj['no'] = ($i + 1)."";
	// 				$obj['qid'] = $qids[$i]."";
	// 				if(isset($attempted_questions[$qids[$i]])){
	// 					$attempt = $attempted_questions[$qids[$i]];
	// 					$obj['status'] = $attempt['status'];
	// 					$obj['marks'] = $attempt['marks']."";
	// 					$obj['time_taken'] = $attempt['time_taken']."";
	// 					$obj['difficulty'] = ucfirst(strtolower($attempt['difficulty']));
	// 					$obj['swot'] = "";
	// 					$obj['speed'] = "Medium";
	// 					$obj['color'] = "#ffffff";
	// 					$obj['status_text'] = "";
	// 					$total_marks += $attempt['marks'];
	// 					$total_time += $attempt['time_taken'];
	// 					$sections[$section_list[$attempt['subject_id']]] += $attempt['marks'];
	// 					if($obj['time_taken']<=$fast_time)	$obj['speed'] = "Fast";
	// 					else if($obj['time_taken']>=$slow_time)	$obj['speed'] = "Slow";
	// 					if($obj['marks']>0 && $obj['speed']=="Fast"){
	// 						$obj['swot'] = "S";
	// 						$obj['color'] = "#47f20c";
	// 					}
	// 					else if($obj['marks']>0 && $obj['speed']!="Fast"){
	// 						$obj['swot'] = "O";
	// 						$obj['color'] = "#fce202";
	// 					}
	// 					else if($obj['marks']<=0 && $obj['speed']=="Fast"){
	// 						$obj['swot'] = "W";
	// 						$obj['color'] = "#ef8544";
	// 					}
	// 					else if($obj['marks']<=0 && $obj['speed']!="Fast"){
	// 						$obj['swot'] = "T";
	// 						$obj['color'] = "#ff2524";
	// 					}
	// 					if($obj['status']=="review" || $obj['status']=="skipped"){
	// 						$obj['swot'] = "";
	// 						$obj['color'] = "#ffffff";
	// 						$obj['status'] = "skipped";
	// 						$obj['marks'] = "";
	// 						$obj['speed'] = "";
	// 						$obj['difficulty'] = "";
	// 						$obj['status_text'] = "";
	// 						//$obj['status_text'] = "Skipped";
	// 					}
	// 					else{
	// 						if($obj['marks']>0){
	// 							$total_time_correct += $attempt['time_taken'];
	// 							$total_ques_correct++;
	// 						}
	// 						else{
	// 							$total_time_wrong += $attempt['time_taken'];
	// 							$total_ques_wrong++;
	// 						}
	// 					}
	// 				}
	// 				else{
	// 					$obj['status'] = "";
	// 					$obj['marks'] = "";
	// 					$obj['time_taken'] = "0";
	// 					$obj['difficulty'] = "";
	// 					$obj['swot'] = "";
	// 					$obj['speed'] = "";
	// 					$obj['color'] = "#f3f3f3";
	// 					$obj['status_text'] = "";
	// 					//$obj['status_text'] = "Unattempted";
	// 				}
	// 				array_push($ques_arr,$obj);
	// 				//$test = $list[0];
	// 			}
	// 			//print_r($sections);
	// 			$section_data = array();
	// 			foreach($sections as $key=>$value){
	// 				$temp = array();
	// 				$temp['name'] = $key;
	// 				$temp['marks'] = $value."";
	// 				array_push($section_data,$temp);
	// 			}
	// 			$test['marks_earned'] = $total_marks;
	// 			$test['time_taken'] = $total_time;
	// 			$test['date'] = date("d M Y, H:i A",(strtotime($test['updated_on'])+19800));
	// 			$test['total_time_correct'] = $total_time_correct;
	// 			$test['total_ques_correct'] = $total_ques_correct;
	// 			$test['total_time_wrong'] = $total_time_wrong;
	// 			$test['total_ques_wrong'] = $total_ques_wrong;
	// 			// $test['avg_time_correct'] = ceil($total_time_correct/$total_ques_correct);
	// 			// $test['avg_time_wrong'] = ceil($total_time_wrong/$total_ques_wrong);




	// 			$test['sections'] = $section_data;
	// 			$test['questions'] = $ques_arr;

	// 	}
	// 	catch(Exception $ex){ }
	// 	return $test;
	// }

	public function saveTestStats($test_id){
		$stats = new TestStats();

		$test = array();
		$tests = $this->cread->getCompleteNockTests($test_id);
		if(count($tests)>0){
			$test = $tests[0];
			$arr = $this->getTestQuestionStats($test['id']);

			$stats->setValue('test_id',$test['id']);
			$stats->setValue('order_id',$test['order_id']);
			$stats->setValue('exam_id',$test['exam_id']);
			$stats->setValue('uid',$test['uid']);
			$stats->setValue('test_name',$test['test_name']);
			$stats->setValue('total_questions',$test['num_questions']);
			$stats->setValue('total_time',$test['num_time']);
			$stats->setValue('total_section',$test['num_section']);
			$stats->setValue('max_marks',$test['max_marks']);
			$stats->setValue('exam_on',$test['started_on']);
			$stats->setValue('template_name',$test['template_name']);
			$stats->setValue('template_id',$test['template_id']);

			$stats->setValue('marks',"" . $arr['total_marks']);
			$stats->setValue('question_attempted',"" . $arr['total_questions']);
			$stats->setValue('time_taken',"" . $arr['total_time']);
			$stats->setValue('avg_time',"" . $arr['avg_time']);
			$stats->setValue('correct_marks',"" . $arr['total_marks_correct']);
			$stats->setValue('correct_question_attempted',"" . $arr['total_ques_correct']);
			$stats->setValue('correct_time_taken',"" . $arr['total_time_correct']);
			$stats->setValue('correct_avg_time',"" . $arr['avg_time_correct']);
			$stats->setValue('wrong_marks',"" . $arr['total_marks_wrong']);
			$stats->setValue('wrong_question_attempted',"" . $arr['total_ques_wrong']);
			$stats->setValue('wrong_time_taken',"" . $arr['total_time_wrong']);
			$stats->setValue('wrong_avg_time',"" . $arr['avg_time_wrong']);

			$stats->setValue('swot_marks',"0");
			$stats->setValue('swot_type',"");

			$stats = $this->cwrite->createObject($stats,true);
		}
	}

	private function getTestQuestionStats($test_id){
		$arr = array();
		$arr['total_marks'] = 0;
		$arr['total_time'] = 0;
		$arr['total_questions'] = 0;
		$arr['avg_time'] = 0;
		$arr['total_marks_correct'] = 0;
		$arr['total_time_correct'] = 0;
		$arr['total_ques_correct'] = 0;
		$arr['avg_time_correct'] = 0;
		$arr['total_marks_wrong'] = 0;
		$arr['total_time_wrong'] = 0;
		$arr['total_ques_wrong'] = 0;
		$arr['avg_time_wrong'] = 0;

		$attempted_questions = $this->cread->getTestAttemptQuestionData($test_id);
		foreach($attempted_questions as $ques){
			$arr['total_marks']+= $ques['marks'];
			$arr['total_time']+= $ques['time_taken'];
			$arr['total_questions']++;
			if($ques['is_correct']=="1"){
				$arr['total_marks_correct']+= $ques['marks'];
				$arr['total_time_correct']+= $ques['time_taken'];
				$arr['total_ques_correct']++;
			}
			else{
				$arr['total_marks_wrong']+= $ques['marks'];
				$arr['total_time_wrong']+= $ques['time_taken'];
				$arr['total_ques_wrong']++;
			}
		}

		if($arr['total_questions']>0)	$arr['avg_time'] = ceil($arr['total_time']/$arr['total_questions']);
		if($arr['total_ques_correct']>0)	$arr['avg_time_correct'] = ceil($arr['total_time_correct']/$arr['total_ques_correct']);
		if($arr['total_ques_wrong']>0)	$arr['avg_time_wrong'] = ceil($arr['total_time_wrong']/$arr['total_ques_wrong']);

		return $arr;
	}

	public function getRecommendedTest($uid){
		$template = array();
		$list = $this->cread->getTestTemplateList();
		$list3 = $list;
		$object = new UserTest();
		$list2 = $this->cread->getList($object,array('uid'=>$uid));
		$arr = array();
		foreach($list2 as $obj){
			$arr[$obj['exam_id']] = $obj; 
		}
		foreach($list as $key=>$obj){
			if(isset($arr[$obj['exam_id']]))	unset($list[$key]);
		}
		$list = array_values($list);
		if(count($list)>0){
			$template = $list[0];
		}
		else{
			$template = $list3[0];
		}
		return $template;
	}

	public function getIncompleteNockTests($uid){
		$object = new UserTest();
		try{
			$object = $this->cread->getObject($object,array('uid'=>$uid,'exam_id'=>"0"));
			if($object->getValue('id')!="0"){
				$exam_id = $this->getExamIdForOrder($object);
				$changes['exam_id'] = $exam_id;
				$this->cwrite->updateObject($object,$changes);
			}
			$object = $this->cread->getObject($object,array('uid'=>$uid,'is_started'=>"0"));
			if($object->getValue('id')=="0"){
				$object = $this->cread->getObject($object,array('uid'=>$uid,'is_completed'=>"0"));
			}
		}
		catch(Exception $ex){ }
		return $object;
	}

	public function getIncompleteMockTestById($test_id){
		$object = new UserTest();
		try{
			$object = $this->cread->getObject($object,array('id'=>$test_id,'is_completed'=>"0"));
		}
		catch(Exception $ex){ }
		return $object;
	}	

	public function getIncompleteNockTestsStatus($uid){
		$arr = array();
		$object = new UserTest();
		try{
			$object = $this->cread->getObject($object,array('uid'=>$uid,'exam_id'=>"0"));
			if($object->getValue('id')!="0"){
				$exam_id = $this->getExamIdForOrder($object);
				$changes['exam_id'] = $exam_id;
				$this->cwrite->updateObject($object,$changes);
			}
			$object = $this->cread->getObject($object,array('uid'=>$uid,'is_started'=>"0"));
			if($object->getValue('id')=="0"){
				$object = $this->cread->getObject($object,array('uid'=>$uid,'is_completed'=>"0"));
			}
			$arr = $object->getObject();
			if($object->getValue('id')!="0"){
				$template_obj = $this->cread->getExamTemplate($object->getValue('exam_id'));
				if(isset($template_obj['template_name'])){
					$arr['template'] = $template_obj['template_name'];
				}
				else{
					$arr['template'] = "Mock Test";
				}
			}
			else{
				$arr['template'] = "Mock Test";
			}
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getAllIncompletedTestList($uid){
		$arr = array();
		$object = new UserTest();
		try{
			$list = $this->cread->getList($object,array('uid'=>$uid,'exam_id'=>"0"));
			foreach($list as $obj){
				$object = new UserTest();
				$object->setObject($obj);
				if($object->getValue('id')!="0"){
					$exam_id = $this->getExamIdForOrder($object);
					$changes['exam_id'] = $exam_id;
					$this->cwrite->updateObject($object,$changes);
				}
			}

			$list = $this->cread->getList($object,array('uid'=>$uid,'is_completed'=>"0"));
			foreach($list as $obj){
				$object = array();
				$object["action"] = "/pages/mocktest?tst=" . $obj['id'] . "&customer=" . $uid . "&ref=my-dashboard";
				$template_obj = $this->cread->getExamTemplate($obj['exam_id']);
				if(isset($template_obj['template_name'])){
					$object["name"] = $template_obj['template_name'];
				}
				else{
					$object["name"] = "Mock Test";
				}
				//$object["name"] = $obj['template'];
				if($obj['is_started']==0){
					$object["status"] = "start";
				}
				else{
					$object["status"] = "resume";
				}
				array_push($arr,$object);
			}
		}
		catch(Exception $ex){ }
		return $arr;
	}		

	private function getExamIdForOrder($userTest){
		$exam_id = 0;
		$order = new Order();
		$template = new ExamTemplate();
		$exam = new Exam();
		$order_id = $userTest->getValue('order_id');
		$order = $this->cread->getObject($order,array('id'=>$order_id));
		$template = $this->cread->getObject($template,array('shopify_variant_id'=>$order->getValue('variant_id')));
		$exams = $this->cread->getList($exam,array('template_id'=>$template->getValue('template_id'),'uid'=>'0'));
		//shuffle($exams);
		$exam_ids = $this->getUserTestExamIdsList($userTest->getValue('uid'));
		foreach($exams as $exam){
			if(!isset($exam_ids[$exam['exam_id']])){
				$exam_id = $exam['exam_id'];
				break;
			}
		}
		return $exam_id;
	}

	private function getUserTestExamIdsList($uid){
		$exam_ids = $this->cread->getUserTestExamIdsList($uid);
		$exam_ids = array_flip($exam_ids);
		return $exam_ids;
	}

	public function getCompletedTests($uid){
		$object = new UserTest();
		try{
			$object = $this->cread->getObject($object,array('uid'=>$uid));
		}
		catch(Exception $ex){ }
		return $object;
	}	

	public function saveOrder($params){
		$object = new Order();
		try{
			$object->setObject($params);
			$object->setValue('customer_id',$params['customer']['id']);
			$object->setValue('customer_first_name',$params['customer']['first_name']);
			$object->setValue('customer_last_name',$params['customer']['last_name']);
			$object->setValue('customer_email',$params['customer']['email']);
			$object->setValue('product_name',$params['line_items'][0]['name']);
			$object->setValue('product_id',$params['line_items'][0]['id']);
			$object->setValue('variant_id',$params['line_items'][0]['variant_id']);
			$object->setValue('variant_title',$params['line_items'][0]['variant_title']);
			$object->setValue('title',$params['line_items'][0]['title']);
			$object->setValue('order_number',$params['order_number']);
			$object = $this->cwrite->createObject($object);

			$this->createUserTestOrder($params['customer']['id'],$object->getValue('id'));
		}
		catch(Exception $ex){ }
		return $object;
	}

	public function getMockTestOrder($order_id){
		$object = new Order();
		$object = $this->cread->getObject($object,array('id'=>$order_id));
		return $object->getObject();
	}

	public function createUserTestOrder($uid,$order_id){
		$object = new UserTest();
		$object->setValue('uid',$uid);
		$object->setValue('order_id',$order_id);
		$object = $this->cwrite->createObject($object);
		return $object;
	}

	public function getExamQuestions($exam_id){
		$object = new MockTest();
		//$exam_id = 13;
		//$userTest = $this->createUserTest($uid,$exam_id);
		$section_arr = $this->cread->getTestSections($exam_id);
		$qids = array();
		foreach($section_arr as $section){
			$qids = array_merge($qids,explode(",",$section['questions']));
		}
		$qids = array_values($qids);
		$questions = $this->cread->getQuestionData($qids);
		$options = $this->cread->getQuestionOptions($qids);

		foreach($options as $key=>$option){
			if(isset($questions[$option['question_id']])){
				array_push($questions[$option['question_id']]['options'],$option);
			}
		}
		return array($section_arr,$questions);
	}	

	public function getMockTest($test_id){
		$object = new MockTest();
		$object = $this->cread->getObject($object,array('test_id'=>$test_id));
		$questions = $this->cread->getQuestionData(explode(",",$object->getValue('questions')));
		$options = $this->cread->getQuestionOptions(explode(",",$object->getValue('questions')));

		foreach($options as $key=>$option){
			if(isset($questions[$option['question_id']])){
				array_push($questions[$option['question_id']]['options'],$option);
			}
		}
		return array($object,explode(",",$object->getValue('questions')),$questions);
	}

	// public function getMockTest($test_id){
	// 	$object = new MockTest();
	// 	$object = $this->cread->getObject($object,array('test_id'=>$test_id));
	// 	$questions = $this->cread->getQuestionData(explode(",",$object->getValue('questions')));
	// 	$options = $this->cread->getQuestionOptions(explode(",",$object->getValue('questions')));

	// 	foreach($options as $key=>$option){
	// 		if(isset($questions[$option['question_id']])){
	// 			array_push($questions[$option['question_id']]['options'],$option);
	// 		}
	// 	}
	// 	return array($object,explode(",",$object->getValue('questions')),$questions);
	// }

	public function getSwotInfo($difficulty,$time_taken,$status,$iscorrect){
		$medium_time = 100;
		$hard_time = 140;
		$easy_time = 60;

		$arr = array();
		$arr['speed'] = 'none';
		$arr['swot'] = '';
		$arr['swot_marks'] = 0;

		if($status!='' && $status!='not_visited'){
			if($difficulty=="HARD" && $time_taken>$hard_time)	$arr['speed']="Slow";
			else if($difficulty=="HARD" && $time_taken<=$hard_time)	$arr['speed']="Fast";
			else if($difficulty=="MEDIUM" && $time_taken>$medium_time)	$arr['speed']="Slow";
			else if($difficulty=="MEDIUM" && $time_taken<=$medium_time)	$arr['speed']="Fast";
			else if($difficulty=="EASY" && $time_taken>$easy_time)	$arr['speed']="Slow";
			else if($difficulty=="EASY" && $time_taken<=$easy_time)	$arr['speed']="Fast";
		}

		if($arr['speed']!="" || $arr['speed']!='none'){
			if($status=="skipped" || $status=="review"){
				if($arr['speed']=="Fast"){
					$arr['swot']="W";
					$arr['swot_marks']=0;
				}
				else{
					$arr['swot']="T";
					$arr['swot_marks']=-1;
				}
			}
			else if($status=="answered" || $status=="review_answer"){
				if($iscorrect=="1" && $arr['speed']=="Fast"){
					$arr['swot']="S";
					$arr['swot_marks']=2;
				}
				else if($iscorrect=="1" && $arr['speed']!="Fast"){
					$arr['swot']="O";
					$arr['swot_marks']=1;
				}
				else if($iscorrect=="0" && $arr['speed']=="Fast"){
					$arr['swot']="W";
					$arr['swot_marks']=-1;
				}
				else if($iscorrect=="0" && $arr['speed']!="Fast"){
					$arr['swot']="T";
					$arr['swot_marks']=-1;
				}
			}
			else{
				$arr['swot']="O";
				$arr['swot_marks']=0;
			}
		}
		else{
			$arr['swot']="O";
			$arr['swot_marks']=0;
		}
		return $arr;
	}

	public function saveResponse($params){
		$object = new MockTestAttempt();
		try{
			$object = $this->cread->getObject($object,array('test_id'=>$params['test_id'],'question_id'=>$params['qid'],'user_id'=>$params['uid']));
			if($object->getValue('id')!="0"){
				$changes = array();
				$changes['answer']=$params['answer'];
				$changes['time_taken']=$params['time'];
				$changes['answer_option']=$params['answer_option'];
				$changes['status']=$params['status'];
				$changes['is_correct']="0";
				$changes['marks']="0";
				if($params['status']=="answered" || $params['status']=="review_answer"){
					$question_object =  $this->getQuestionAnswer($params['qid']);
					if($question_object->getValue('question_id')!=0){
						if($question_object->getValue('correct_answer')==$params['answer']){
							$changes['is_correct']="1";
							$changes['marks']="3";
						}
						else{
							if($question_object->getValue('question_type')=="MCQ"){
								$changes['is_correct']="0";
								$changes['marks']="-1";
							}
							else{
								$changes['is_correct']="2";
								$changes['marks']="0";
							}
						}
					}
				}
				$arr = $this->getSwotInfo($object->getValue('difficulty'),$changes['time_taken'],$changes['status'],$changes['is_correct']);
				$changes['speed']=$arr['speed'];
				$changes['swot']=$arr['swot'];
				$changes['swot_marks']=$arr['swot_marks'];
				$this->cwrite->updateObject($object,$changes);
			}
			else{
				$object->setValue('test_id',$params['test_id']);
				$object->setValue('question_id',$params['qid']);
				$object->setValue('user_id',$params['uid']);
				$object->setValue('answer',$params['answer']);
				$object->setValue('time_taken',$params['time']);
				$object->setValue('answer_option',$params['answer_option']);
				$object->setValue('status',$params['status']);
				$object->setValue('question_no',$params['qno']);
				$object->setValue('is_correct',"0");
				$object->setValue('marks',"0");
				if($params['status']=="answered" || $params['status']=="review_answer"){
					$question_object =  $this->getQuestionAnswer($params['qid']);
					if($question_object->getValue('question_id')!=0){
						if($question_object->getValue('correct_answer')==$params['answer']){
							$object->setValue('is_correct',"1");
							$object->setValue('marks',"3");
						}
						else{
							if($question_object->getValue('question_type')=="MCQ"){
								$object->setValue('is_correct',"0");
								$object->setValue('marks',"-1");
							}
							else{
								$object->setValue('is_correct',"2");
								$object->setValue('marks',"0");
							}
						}
					}
				}
				$arr = $this->getSwotInfo("MEDIUM",$params['time'],$params['status'],$object->getValue('is_correct'));
				$object->setValue('speed',$arr['speed']);
				$object->setValue('swot',$arr['swot']);
				$object->setValue('swot_marks',$arr['swot_marks']);
				$object = $this->cwrite->createObject($object);
			}
		}
		catch(Exception $ex){ }
		return $object;
	}

	public function getQuestionAnswer($qid){
		$object = new Questions();
		try{
			$object = $this->cread->getObject($object,array('question_id'=>$qid));
		}
		catch(Exception $ex){ }
		return $object;
	}

	public function createUserTest($uid,$exam_id){
		$object = new UserTest();
		$object->setValue('uid',$uid);
		$object->setValue('exam_id',$exam_id);
		$object = $this->cwrite->createObject($object);
		return $object;
	}

	public function startTest($test_id){
		$object = new UserTest();
		try{
			$object = $this->cread->getObject($object,array('id'=>$test_id));
			if($object->getValue('id')!="0"){
				$changes = array();
				$changes['is_started']="1";
				$changes['started_on']=date("Y-m-d H:i:s",strtotime('now'));
				$this->cwrite->updateObject($object,$changes);
				list($qids,$qids_sec) = $this->getTestExamQuestions($object->getValue('exam_id'));
				print_r($qids_sec);
				$arr = $this->saveTestAttemptQuestions($object,$qids,$qids_sec);
				//echo json_encode($arr);
			}
		}
		catch(Exception $ex){ }
		return $object;
	}

	private function saveTestAttemptQuestions($user_test,$qids,$qids_sec){
		$arr = array();
		$data = $this->cread->getQuestionData2($qids);
		$attempted_questions = $this->cread->getAttemptedQuestions($user_test->getValue('id'));
		for($i=0;$i<count($qids);$i++){
			if(!isset($attempted_questions[$qids[$i]])){
				$obj = new QuestionSwot();
				if(isset($data[$qids[$i]])){
					$obj->setObject($data[$qids[$i]]);
				}
				else{
					$obj->setValue('question_id',$qids[$i]);
				}
				$obj->setValue('test_id',$user_test->getValue('id'));
				$obj->setValue('exam_id',$user_test->getValue('exam_id'));
				$obj->setValue('question_no',($i + 1));
				$obj->setValue('user_id',$user_test->getValue('uid'));
				if(isset($qids_sec[$qids[$i]])){
					$obj->setValue('section_id',$qids_sec[$qids[$i]]);
				}
				array_push($arr,$obj);
			}
		}
		if(count($arr)>0)	$this->cwrite->createMultipleObject($arr);
		return $arr;
	}

	function getSubjectLevelSwot($uid){
		$object = new SubjectStats();
		$list = $this->cread->getList($object,array('uid'=>$uid));
		$subjects = array();
		$difficulty_fast_time = array('EASY'=>60,'MEDIUM'=>100,'HARD'=>140);
		foreach($list as $obj){
			if(!isset($subjects[$obj['subject_id']])){
				$subjects[$obj['subject_id']] = array();
				$subjects[$obj['subject_id']]['subject_id'] = $obj['subject_id'];
				$subjects[$obj['subject_id']]['subject'] = $obj['subject'];
				$subjects[$obj['subject_id']]['EASY'] = "";
				$subjects[$obj['subject_id']]['MEDIUM'] = "";
				$subjects[$obj['subject_id']]['HARD'] = "";
				$subjects[$obj['subject_id']]['marks'] = 0;
				$subjects[$obj['subject_id']]['correct_time'] = 0;
				$subjects[$obj['subject_id']]['correct_fast_time'] = 0;
				$subjects[$obj['subject_id']]['wrong_time'] = 0;
				$subjects[$obj['subject_id']]['wrong_fast_time'] = 0;
				$subjects[$obj['subject_id']]['correct_speed'] = "";
				$subjects[$obj['subject_id']]['wrong_speed'] = "";
			}
			$subjects[$obj['subject_id']][$obj['difficulty']] = $obj['swot_type'];
			$subjects[$obj['subject_id']]['marks'] += $obj['marks'];
			$subjects[$obj['subject_id']]['correct_time'] += $obj['correct_time_taken'];
			$subjects[$obj['subject_id']]['correct_fast_time'] += $difficulty_fast_time[$obj['difficulty']]*$obj['wrong_question_attempted'];
			$subjects[$obj['subject_id']]['wrong_time'] += $obj['correct_time_taken'];
			$subjects[$obj['subject_id']]['wrong_fast_time'] += $difficulty_fast_time[$obj['difficulty']]*$obj['wrong_question_attempted'];
		}
		foreach($subjects as $key=>$obj){
			if($obj['correct_time']<=$obj['correct_fast_time']){
				$subjects[$key]['correct_speed'] = 'Fast';
			}
			else{
				$subjects[$key]['correct_speed'] = 'Slow';
			}

			if($obj['wrong_time']<=$obj['wrong_fast_time']){
				$subjects[$key]['wrong_speed'] = 'Fast';
			}
			else{
				$subjects[$key]['wrong_speed'] = 'Slow';
			}
			unset($subjects[$key]['correct_time']);
			unset($subjects[$key]['correct_fast_time']);
			unset($subjects[$key]['wrong_time']);
			unset($subjects[$key]['wrong_fast_time']);
		}
		return array_values($subjects);
	}

	function getTopicLevelSwot($uid){
		$final_arr = array();
		$object = new SubjectStats();
		$list = $this->getTopicSDifficultySwotAnalysis($uid);
		$topics = array();
		$difficulty_fast_time = array('EASY'=>60,'MEDIUM'=>100,'HARD'=>140,''=>100,'none'=>100);
		foreach($list as $obj){
			if(!isset($topics[$obj['topic_id']])){
				$topics[$obj['topic_id']] = array();
				$topics[$obj['topic_id']]['topic_id'] = $obj['topic_id'];
				$topics[$obj['topic_id']]['topic'] = $obj['topic'];
				$topics[$obj['topic_id']]['subject_id'] = $obj['subject_id'];
				$topics[$obj['topic_id']]['subject'] = $obj['subject'];
				$topics[$obj['topic_id']]['EASY'] = "";
				$topics[$obj['topic_id']]['MEDIUM'] = "";
				$topics[$obj['topic_id']]['HARD'] = "";
				$topics[$obj['topic_id']]['marks'] = 0;
				$topics[$obj['topic_id']]['correct_time'] = 0;
				$topics[$obj['topic_id']]['correct_fast_time'] = 0;
				$topics[$obj['topic_id']]['wrong_time'] = 0;
				$topics[$obj['topic_id']]['wrong_fast_time'] = 0;
				$topics[$obj['topic_id']]['correct_speed'] = "";
				$topics[$obj['topic_id']]['wrong_speed'] = "";
			}
			$topics[$obj['topic_id']][$obj['difficulty']] = $obj['swot_type'];
			$topics[$obj['topic_id']]['marks'] += $obj['marks'];
			$topics[$obj['topic_id']]['correct_time'] += $obj['correct_time_taken'];
			$topics[$obj['topic_id']]['correct_fast_time'] += $difficulty_fast_time[$obj['difficulty']]*$obj['correct_question_attempted'];
			$topics[$obj['topic_id']]['wrong_time'] += $obj['correct_time_taken'];
			$topics[$obj['topic_id']]['wrong_fast_time'] += $difficulty_fast_time[$obj['difficulty']]*$obj['wrong_question_attempted'];
		}
		foreach($topics as $key=>$obj){
			if($obj['correct_time']<=$obj['correct_fast_time']){
				$topics[$key]['correct_speed'] = 'Fast';
			}
			else{
				$topics[$key]['correct_speed'] = 'Slow';
			}

			if($obj['wrong_time']<=$obj['wrong_fast_time']){
				$topics[$key]['wrong_speed'] = 'Fast';
			}
			else{
				$topics[$key]['wrong_speed'] = 'Slow';
			}
			unset($topics[$key]['correct_time']);
			unset($topics[$key]['correct_fast_time']);
			unset($topics[$key]['wrong_time']);
			unset($topics[$key]['wrong_fast_time']);
			if(!isset($final_arr[$obj['subject']])){
				$final_arr[$obj['subject']] = array();
				$final_arr[$obj['subject']]['name'] = $obj['subject'];
				$final_arr[$obj['subject']]['id'] = $obj['subject_id'];
				$final_arr[$obj['subject']]['topics'] = array();
			}
			array_push($final_arr[$obj['subject']]['topics'],$topics[$key]);
		}
		//$topic_arr = array_values($topics);
		$subject_arr = array('VARC','DILR','QA');
		$new_arr = array();
		foreach($subject_arr as $subject){
			if(isset($final_arr[$subject]))	array_push($new_arr,$final_arr[$subject]);
		}
		//print_r($final_arr);
		return $new_arr;
	}

	private function getTestExamQuestions($exam_id){
		list($qids_arr,$qids_sec) = $this->getExamsQuestions(array($exam_id));
		if(isset($qids_arr[$exam_id]))	return array($qids_arr[$exam_id],$qids_sec);
		return array();
	}

	// private function getExamsQuestions($exam_ids){
	// 	$qids_arr = array();
	// 	$section_arr = $this->cread->getTestSectionsQuestions($exam_ids);
	// 	foreach($section_arr as $section){
	// 		if(!isset($qids_arr[$section['exam_id']]))	$qids_arr[$section['exam_id']] = array();
	// 		$qids = explode(",",$section['questions']);
	// 		$qids_arr[$section['exam_id']] = array_merge($qids_arr[$section['exam_id']],$qids);
	// 	}
	// 	return $qids_arr;
	// }

	public function endTest($test_id,$section_no=-1,$id=0,$time=null){
		$object = new UserTest();
		try{
			$object = $this->cread->getObject($object,array('id'=>$test_id));
			if($object->getValue('id')!="0"){
				$changes = array();
				$changes['is_completed']="1";
				$changes['completed_on']=date("Y-m-d H:i:s",strtotime('now'));
				if($section_no!=-1){
					$changes['is_section' . ($section_no + 1) . '_completed']="1";
					$changes['section' . ($section_no + 1) . '_time']=$time;
					$changes['section' . ($section_no + 1) . '_id']=$id;
					$arr = $this->getUserTestSectionCumulativeMarks($test_id,$id);
					$changes['section' . ($section_no + 1) . '_marks']=$arr['marks'];
				}
				$this->cwrite->updateObject($object,$changes);
				$this->adjustTimeAsPerGroupId($test_id,$id);
			}
			$this->saveTestStats($object->getValue('id'));
			$this->saveSubjectDifficultySwotAnalysis($object->getValue('uid'));
		}
		catch(Exception $ex){ }
		return $object;
	}

	private function adjustTimeAsPerGroupId($test_id,$section_id){
		$object = new QuestionSwot();
		$groups = array();
		$list = $this->cread->getList($object,array('test_id'=>$test_id,'section_id'=>$section_id));
		foreach($list as $ques){
			if(!isset($groups[$ques['group_id']])){
				$groups[$ques['group_id']] = array();
				$groups[$ques['group_id']]['num'] = 0;
				$groups[$ques['group_id']]['time'] = 0;
				$groups[$ques['group_id']]['avg_time'] = 0;
			}	
			$groups[$ques['group_id']]['time']+=$ques['time_taken'];
			$groups[$ques['group_id']]['num']++;
		}
		foreach($groups as $key=>$group){
			$groups[$key]['avg_time'] = round($group['time']/$group['num']);
		}
		foreach($list as $ques){
			if(isset($groups[$ques['group_id']])){
				if($ques['time_taken']!=$groups[$ques['group_id']]['avg_time']){
					$changes = array();
					$object = new QuestionSwot();
					$object->setObject($ques);
					$changes['time_taken']=$groups[$ques['group_id']]['avg_time'];
					$arr = $this->getSwotInfo($object->getValue('difficulty'),$changes['time_taken'],$object->getValue('status'),$object->getValue('is_correct'));
					$changes['speed']=$arr['speed'];
					$changes['swot']=$arr['swot'];
					$changes['swot_marks']=$arr['swot_marks'];
					$this->cwrite->updateObject($object,$changes);
				}
			}
		}

	}

	public function endSection($test_id,$section_no,$id,$time){
		$object = new UserTest();
		try{
			$object = $this->cread->getObject($object,array('id'=>$test_id));
			if($object->getValue('id')!="0"){
				$changes = array();
				$changes['is_section' . ($section_no + 1) . '_completed']="1";
				$changes['section' . ($section_no + 1) . '_time']=$time;
				$changes['section' . ($section_no + 1) . '_id']=$id;
				$arr = $this->getUserTestSectionCumulativeMarks($test_id,$id);
				$changes['section' . ($section_no + 1) . '_marks']=$arr['marks'];
				$this->cwrite->updateObject($object,$changes);
				$this->adjustTimeAsPerGroupId($test_id,$id);
			}
		}
		catch(Exception $ex){ }
		return $object;
	}

	private function getUserTestSectionCumulativeMarks($test_id,$section_id){
		$final_arr = array();
		$final_arr['marks'] = 0;
		$final_arr['timetaken'] = 0;
		try{
			$qids_string = $this->cread->getUserTestSectionQuestionString($test_id,$section_id);
			$qids = explode(",",$qids_string);
			if(count($qids)>0){
				$final_arr = $this->cread->getUserTestQuestionsCumulativeMarks($test_id,$qids);
			}
		}
		catch(Exception $ex){ }
		return $final_arr;
	}

	public function getTopicSDifficultySwotAnalysis($uid){
		$subject = array("QA"=>1,"VARC"=>2,"DILR"=>3);
		$final_arr = array();
		$arr = array();
		//$object = new TopicByStats();
		$list = $this->cread->getTopicWiseUserStats($uid);
		foreach($list as $key=>$obj){
			$object = new TopicByStats();
			$object->setObject($obj);
			$arr[$key] = $object;
		}

		foreach($arr as $key=>$obj){
			$obj->setValue('subject_id',$subject[$obj->getValue('subject')]);
			if($obj->getValue('swot_avg')>=1.50){
				$obj->setValue('swot_type',"S");
			}
			else if($obj->getValue('swot_avg')>=0.50){
				$obj->setValue('swot_type',"O");
			}
			else if($obj->getValue('swot_avg')>=-0.50){
				$obj->setValue('swot_type',"W");
			}
			else{
				$obj->setValue('swot_type',"T");
			}
			// if(!isset($final_arr[$obj->getValue('subject')])){
			// 	$final_arr[$obj->getValue('subject')] = array();
			// 	$final_arr[$obj->getValue('subject')]['name'] = $obj->getValue('subject');
			// 	$final_arr[$obj->getValue('subject')]['id'] = $obj->getValue('subject_id');
			// 	$final_arr[$obj->getValue('subject')]['topics'] = array();
			// }
			// array_push($final_arr[$obj->getValue('subject')]['topics'],$obj->getObject());
			$final_arr[$key] = $obj->getObject();
		}
		$final_arr = array_values($final_arr);
		return $final_arr;
	}

	public function getSubjectDifficultySwotAnalysis($uid){
		$subject = array(1=>"QA",2=>"VARC",3=>"DILR");
		$final_arr = array();
		$arr = array();
		$object = new SubjectStats();
		$list1 = $this->cread->getUserSubjectWiseData($uid);
		$list2 = $this->cread->getUserSubjectWiseDataCorrectlyAttempted($uid);
		$list3 = $this->cread->getUserSubjectWiseDataWronglyAttempted($uid);
		foreach($list1 as $key=>$obj){
			$object = new SubjectStats();
			$object->setObject($obj);
			$arr[$key] = $object;
		}

		foreach($list2 as $key=>$obj){
			if(!isset($arr[$key])){
				$object = new SubjectStats();
				$arr[$key] = $object;
			}
			$arr[$key]->setObject($obj);
		}

		foreach($list3 as $key=>$obj){
			if(!isset($arr[$key])){
				$object = new SubjectStats();
				$arr[$key] = $object;
			}
			$arr[$key]->setObject($obj);
		}

		foreach($arr as $key=>$obj){
			$obj->setValue('subject',$subject[$obj->getValue('subject_id')]);
			if($obj->getValue('swot_avg')>=1.50){
				$obj->setValue('swot_type',"S");
			}
			else if($obj->getValue('swot_avg')>=0.50){
				$obj->setValue('swot_type',"O");
			}
			else if($obj->getValue('swot_avg')>=-0.50){
				$obj->setValue('swot_type',"W");
			}
			else{
				$obj->setValue('swot_type',"T");
			}
			$final_arr[$key] = $obj->getObject();
		}
		return $final_arr;
	}

	public function saveSubjectDifficultySwotAnalysis($uid){
		$object = new SubjectStats();
		$updateList = array();
		$addList = array();
		$list = $this->cread->getList($object,array('uid'=>$uid));
		$oldlist = array();
		foreach($list as $obj){
			$key = $obj['subject_id'] . "_" . $obj['difficulty'];
			$oldlist[$key] = $obj;
		}
		$newlist = $this->getSubjectDifficultySwotAnalysis($uid);
		foreach($newlist as $key=>$obj){
			if(isset($oldlist[$key])){
				$obj['id'] = $oldlist[$key]['id'];
				array_push($updateList,$obj);
			}
			else{
				$object1 = new SubjectStats();
				$object1->setObject($obj);
				array_push($addList,$object1);
			}
		}
		foreach($updateList as $key=>$obj){
			$object = new SubjectStats();
			$object->setObject($obj);
			$changes = $obj;
			unset($changes['id']);
			$this->cwrite->updateObject($object,$changes);
		}
		if(count($addList)>0)	$this->cwrite->createMultipleObject($addList);
	}

	function getAllUsers(){
		return $this->cread->getAllUsers();
	}

}
