<?php
include_once dirname(__FILE__) . '/../../../includes/common/Constants.php';
include_once dirname(__FILE__) . '/../../general/dao/GeneralReadDao.php';

class MockTestReadDao extends GeneralReadDao{

	function __construct(){
	}

	public function getUserTestExamIdsList($uid){
		$ids = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select exam_id from ht_user_test where uid = %d and exam_id!=0",$uid);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
			foreach($arr as $row){
				array_push($ids,$row['exam_id']);
			}
		}
		catch(Exception $ex){ }
		return $ids;
	}

	public function getTopicWiseStats($subject_id,$count,$total){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select * from (select (count(a.question_id)*100)/%d as perc,d.topic_id,d.topic,(count(a.question_id)*%d)/%d as num_ques1,ROUND((count(a.question_id)*%d)/%d) as num_ques,count(a.question_id) as cnt,ROUND(count(CASE WHEN b.difficulty = 'easy' THEN 1 END)/count(a.question_id)*ROUND((count(a.question_id)*%d)/%d)) as easy_count,ROUND(count(CASE WHEN b.difficulty = 'medium' THEN 1 END)/count(a.question_id)*ROUND((count(a.question_id)*%d)/%d)) as medium_count,ROUND(count(CASE WHEN b.difficulty = 'hard' THEN 1 END)/count(a.question_id)*ROUND((count(a.question_id)*%d)/%d)) as hard_count,count(CASE WHEN b.difficulty = 'easy' THEN 1 END)/count(a.question_id)*100 as easy_count1,count(CASE WHEN b.difficulty = 'medium' THEN 1 END)/count(a.question_id)*100 as medium_count1,count(CASE WHEN b.difficulty = 'hard' THEN 1 END)/count(a.question_id)*100 as hard_count1 from ht_questions a,ht_question_metadata b,ht_question_topic_mapping c,ht_question_topics d where a.question_id=b.question_id and a.question_id=c.question_id and c.topic_id=d.topic_id and a.is_active=1 and subject_id=%d group by d.topic_id) as ss order by ss.cnt",$total,$count,$total,$count,$total,$count,$total,$count,$total,$count,$total,$subject_id);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getQuestionData($qids){
		$final_arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select a.question_id,a.question_text,a.question_type,a.group_id,b.group_type,b.paragraph,b.pic,a.correct_answer,a.correct_answer_option,c.solution from ht_questions a left join ht_question_groups b on a.group_id=b.group_id left join ht_question_solutions c on a.question_id=c.question_id where a.question_id in (%s)",implode(",",$qids));
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
			error_log($q);
			foreach($arr as $row){
				$row['options'] = array();
				$final_arr[$row['question_id']] = $row;
			}
		}
		catch(Exception $ex){ }
		return $final_arr;
	}

	public function getQuestionTopics($qids){
		$final_arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select a.question_id,b.topic_id,b.topic as topic,b.subject from ht_question_topic_mapping a,ht_question_topics b where a.topic_id = b.topic_id and a.question_id in  (%s)",implode(",",$qids));
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
			//error_log($q);
			foreach($arr as $row){
				$final_arr[$row['question_id']] = $row;
			}
		}
		catch(Exception $ex){ }
		return $final_arr;
	}

	public function getQuestionOptions($qids){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select question_id,option_id,option_text,option_number from ht_question_options where question_id in (%s)",implode(",",$qids));
			error_log($q);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getTopicDifficultyQuestions($topic_id,$difficulty){
		$qids = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			//$q = sprintf("select distinct b.question_id from ht_question_topics a,ht_question_topic_mapping b,ht_question_solutions c,ht_question_metadata d,ht_questions e,ht_question_options f where a.topic_id = b.topic_id and b.question_id=c.question_id and c.question_id=d.question_id and c.question_id=e.question_id and c.question_id=f.question_id and a.parent_topic_id!=0 and c.answer=c.predicted_answer and e.question_type!='Non-MCQ' and a.topic_id=%d and d.difficulty='HARD'",$topic_id,$difficulty);

			$q = sprintf("select a.qid from ht_questions_temp a,ht_question_metadata d where a.qid = d.question_id and a.topic_id=%d and d.difficulty='%s'",$topic_id,$difficulty);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
			foreach($arr as $row){
				//array_push($qids,$row['question_id']);
				array_push($qids,$row['qid']);
			}
		}
		catch(Exception $ex){ }
		return $qids;
	}

	public function getQuestionsGroupTempIds($qids){
		$final_arr = array();
		try{
			//print_r($qids);
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select a.group_id from ht_questions_temp a,ht_question_metadata d where a.qid = d.question_id and a.qid in (%s);",implode(",",$qids));
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
			foreach($arr as $row){
				//array_push($qids,$row['question_id']);
				array_push($final_arr,$row['group_id']);
			}
		}
		catch(Exception $ex){ }
		//print_r($final_arr);
		return $final_arr;
	}

	public function getQuestionsGroupTemp($group_ids){
		$arr = array();
		try{
			//print_r($group_ids);
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select count(*) as cnt,a.group_id,group_concat(a.qid) as qidstr from ht_questions_temp a,ht_question_metadata d where a.qid = d.question_id and a.group_id in (%s) group by a.group_id;",implode(",",$group_ids));
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
		}
		catch(Exception $ex){ }
		return $arr;
	}		

	public function getTestSections($exam_id){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select a.exam_id,a.test_name,d.section_id,d.section_name,d.num_questions,d.num_time,b.questions,c.template_name,c.num_time as total_time,c.num_section from ht_exam a,ht_exam_sections b,ht_exam_template c,ht_exam_template_section d where a.exam_id=b.exam_id and a.template_id=c.template_id and b.section_id=d.section_id and a.exam_id=%d order by d.section_order asc",$exam_id);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getTestSectionsQuestions($exam_ids){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select a.exam_id,a.test_name,d.section_id,d.section_name,d.num_questions,d.num_time,b.questions,c.template_name,c.num_time as total_time,c.num_section from ht_exam a,ht_exam_sections b,ht_exam_template c,ht_exam_template_section d where a.exam_id=b.exam_id and a.template_id=c.template_id and b.section_id=d.section_id and a.exam_id in (%s) order by d.section_order asc",implode(",",$exam_ids));
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
		}
		catch(Exception $ex){ }
		return $arr;
	}	

	public function getLastCompleteNockTests($uid){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf(" select a.*,b.test_name,c.template_name,c.num_questions,c.num_time,c.num_section,c.max_marks,a.updated_on from ht_user_test a,ht_exam b,ht_exam_template c where a.exam_id=b.exam_id and b.template_id = c.template_id and a.uid=%d and a.is_completed=1 order by a.updated_on desc",$uid);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getCompleteNockTests($test_id){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select a.*,b.test_name,c.template_name,c.template_id,c.num_questions,c.num_time,c.num_section,c.max_marks,a.updated_on from ht_user_test a,ht_exam b,ht_exam_template c where a.exam_id=b.exam_id and b.template_id = c.template_id and a.id=%d and a.is_completed=1 order by a.updated_on desc",$test_id);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getSectionsList(){
		$list = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select * from ht_exam_template_section");
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
			foreach($arr as $row){
				$list[$row['section_id']] = $row;
			}
		}
		catch(Exception $ex){ }
		return $list;
	}

	public function getQuestionStatsByTests($uid,$test_ids){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("Select *  from ht_test_attempt where test_id in (%s) order by test_id desc,question_no asc",implode(",",$test_ids));
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
			// foreach ($arr as $row) {
			// 	$final_arr[$row['exam_id']."_".$row['qid']] = $row;
			// }
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getAttemptedQuestions($test_id){
		$final_arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("Select *  from ht_test_attempt where test_id=%d order by test_id desc,question_no asc",$test_id);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
			foreach ($arr as $row) {
				$final_arr[$row['question_id']] = $row;
			}
		}
		catch(Exception $ex){ }
		return $final_arr;
	}	

	public function getQuestionData2($qids){
		$list = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select a.question_id,a.group_id,a.question_type,b.difficulty from ht_questions a,ht_question_metadata b where a.question_id = b.question_id and a.question_id in (%s)",implode(",",$qids));
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
			foreach($arr as $row){
				$list[$row['question_id']] = $row;
			}
		}
		catch(Exception $ex){ }
		return $list;
	}




	// public function getUserCompletedTestList($uid){
	// 	$arr = array();
	// 	try{
	// 		$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
	// 		$q = sprintf(" select a.*,b.test_name,c.template_name,c.template_id,c.num_questions,c.num_time,c.num_section,c.max_marks,a.updated_on from ht_user_test a,ht_exam b,ht_exam_template c where a.exam_id=b.exam_id and b.template_id = c.template_id and a.id=%d and a.is_completed=1 order by a.updated_on desc",$test_id);
	// 		list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
	// 	}
	// 	catch(Exception $ex){ }
	// 	return $arr;
	// }		

	public function getTestAttemptQuestionData($test_id){
		$final_arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select a.question_no,a.id,a.test_id,a.question_id,a.status,a.marks,a.time_taken,b.difficulty,b.subject_id,c.question_type,a.is_correct from ht_test_attempt a,ht_question_metadata b,ht_questions c where a.question_id=b.question_id and a.question_id=c.question_id and a.test_id=%d order by a.question_no",$test_id);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
			foreach($arr as $row){
				$final_arr[$row['question_id']] = $row;
			}
		}
		catch(Exception $ex){ }
		return $final_arr;
	}

	public function getTestAttemptQuestionData2($test_ids){
		$final_arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select a.question_no,a.id,a.test_id,a.question_id,a.status,a.marks,a.time_taken,b.difficulty,b.subject_id,c.question_type,a.is_correct,a.section_id from ht_test_attempt a,ht_question_metadata b,ht_questions c where a.question_id=b.question_id and a.question_id=c.question_id and a.test_id in (%s)",implode(",",$test_ids));
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__);
			foreach($arr as $row){
				$final_arr[$row['question_id']] = $row;
			}
		}
		catch(Exception $ex){ }
		return $final_arr;
	}

	public function getUserTestSectionQuestionString($test_id,$section_id){
		$qids_string = "";
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select b.questions from ht_user_test a,ht_exam_sections b where a.exam_id=b.exam_id and a.id=%d and b.section_id=%d",$test_id,$section_id);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
			if($error==''){
				$qids_string = $arr[0]['questions'];
			}
		}
		catch(Exception $ex){ }
		return $qids_string;
	}

	public function getUserTestQuestionsCumulativeMarks($test_id,$qids){
		$final_arr = array();
		$final_arr['marks'] = 0;
		$final_arr['timetaken'] = 0;
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select sum(marks) as marks,sum(time_taken) as timetaken from ht_test_attempt where test_id=%d and question_id in (%s)",$test_id,implode(",",$qids));
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
			if($error==''){
				$final_arr['marks'] = $arr[0]['marks'];
				$final_arr['timetaken'] = $arr[0]['timetaken'];
			}
		}
		catch(Exception $ex){ }
		return $final_arr;
	}

	public function getAllUsers(){
		$final_arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select distinct customer_id from ht_orders");
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
			foreach($arr as $row){
				array_push($final_arr,$row['customer_id']);
			}
		}
		catch(Exception $ex){ }
		return $final_arr;
	}

	public function getTemplateInfo($exam_id){
		$final_arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("SELECT a.exam_id,b.template_name from ht_exam a,ht_exam_template b where a.template_id = b.template_id and a.exam_id=%d",$exam_id);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
			if($error==''){
				if(count($arr)>0){
					$final_arr = $arr[0];
				}
			}
		}
		catch(Exception $ex){ }
		return $final_arr;
	}

	public function getUserSubjectWiseData($uid){
		$list = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select a.user_id as uid,b.subject_id,a.difficulty,count(a.id) as total_questions,sum(marks) as marks,sum(time_taken) as time_taken,ROUND(avg(time_taken)) as avg_time,sum(swot_marks) as swot_marks,ROUND(avg(swot_marks),2) as swot_avg from ht_test_attempt a,ht_exam_template_section b where a.section_id=b.section_id and a.user_id=%d group by b.subject_id,a.difficulty",$uid);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
			foreach($arr as $row){
				$list[$row['subject_id'] . "_" . $row['difficulty']] = $row;
			}
		}
		catch(Exception $ex){ }
		return $list;
	}


	public function getUserSubjectWiseDataCorrectlyAttempted($uid){
		$list = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select a.user_id as uid,b.subject_id,a.difficulty,count(a.id) as correct_question_attempted,sum(marks) as correct_marks,sum(time_taken) as correct_time_taken,ROUND(avg(time_taken)) as correct_avg_time from ht_test_attempt a,ht_exam_template_section b where a.section_id=b.section_id and a.user_id=%d and is_correct=1 group by b.subject_id,a.difficulty",$uid);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
			foreach($arr as $row){
				$list[$row['subject_id'] . "_" . $row['difficulty']] = $row;
			}
		}
		catch(Exception $ex){ }
		return $list;
	}

	public function getUserSubjectWiseDataWronglyAttempted($uid){
		$list = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select a.user_id as uid,b.subject_id,a.difficulty,count(a.id) as wrong_question_attempted,sum(marks) as wrong_marks,sum(time_taken) as wrong_time_taken,ROUND(avg(time_taken)) as wrong_avg_time from ht_test_attempt a,ht_exam_template_section b where a.section_id=b.section_id and a.user_id=%d and is_correct=0 and status!='' and status!='not_visited' group by b.subject_id,a.difficulty",$uid);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
			foreach($arr as $row){
				$list[$row['subject_id'] . "_" . $row['difficulty']] = $row;
			}
		}
		catch(Exception $ex){ }
		return $list;
	}

	public function getTopicWiseUserStats($uid){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("Select a.user_id as uid,b.topic_id,c.topic,c.subject,a.difficulty,count(a.id) as total_questions,sum(marks) as marks,sum(time_taken) as time_taken,ROUND(avg(time_taken)) as avg_time,sum(swot_marks) as swot_marks,ROUND(avg(swot_marks),2) as swot_avg,count(CASE WHEN a.is_correct = '1' THEN 1 END) as correct_question_attempted,SUM(CASE WHEN a.is_correct = '1' THEN marks ELSE 0 END) as correct_marks,SUM(CASE WHEN a.is_correct = '1' THEN time_taken ELSE 0 END) as correct_time_taken,ROUND(avg(CASE WHEN a.is_correct = '1' THEN time_taken END)) as correct_avg_time,count(CASE WHEN a.is_correct = '0' and status!='' and status!='not_visited' THEN 1 END) as wrong_question_attempted,SUM(CASE WHEN a.is_correct = '0' and status!='' and status!='not_visited' THEN marks ELSE 0 END) as wrong_marks,SUM(CASE WHEN a.is_correct = '0' and status!='' and status!='not_visited' THEN time_taken ELSE 0 END) as wrong_time_taken,ROUND(avg(CASE WHEN a.is_correct = '0' and status!='' and status!='not_visited' THEN time_taken END)) as wrong_avg_time from ht_test_attempt a,ht_question_topic_mapping b,ht_question_topics c where a.question_id=b.question_id and b.topic_id = c.topic_id and a.user_id=%d group by b.topic_id,a.difficulty",$uid);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
		}
		catch(Exception $ex){ }
		return $arr;
	}


	public function getTestQuestionAnalysis($uid,$subject_id,$diff=""){
		$arr = array();
		try{
			$str = "";
			if($diff!="")	$str = " and a.difficulty='" .strtoupper($diff) . "'";
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select a.* from ht_test_attempt a,ht_exam_template_section b where a.section_id=b.section_id and b.subject_id=%d and a.user_id='%d' %s order by a.test_id desc,a.updated_on desc;",$subject_id,$uid,$str);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
		}
		catch(Exception $ex){ }
		return $arr;
	}
	
	public function getTestTopicQuestionAnalysis($uid,$topic_id,$diff=""){
		$arr = array();
		try{
			$str = "";
			if($diff!="")	$str = " and a.difficulty='" .strtoupper($diff) . "'";
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select a.* from ht_test_attempt a,ht_question_topic_mapping b,ht_question_topics c where a.question_id=b.question_id  and b.topic_id=c.topic_id and b.topic_id=%d and a.user_id='%d' %s order by a.test_id desc,a.updated_on desc;",$topic_id,$uid,$str);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getTopicDetails($topic_id){
		$topic = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select * from ht_question_topics where topic_id=%d",$topic_id);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
			if(count($arr)>0){
				$topic = $arr[0];
			}
		}
		catch(Exception $ex){ }
		return $topic;
	}

	public function getExamTemplate($exam_id){
		$obj = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select * from ht_exam_template a, ht_exam b where a.template_id=b.template_id and b.exam_id=%d",$exam_id);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
			if(count($arr)>0){
				$obj = $arr[0];
			}
		}
		catch(Exception $ex){ }
		return $obj;
	}

	public function getTestTemplateList(){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('question_bank');
			$q = sprintf("select * from ht_exam_template a, ht_exam b where a.template_id=b.template_id and b.is_active=1 and b.uid=0 and shopify_variant_id!=0;");
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array(),1);
		}
		catch(Exception $ex){ }
		return $arr;
	}	

}