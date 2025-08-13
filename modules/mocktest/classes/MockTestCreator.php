<?php
include_once dirname(__FILE__) . '/../../../includes/config/Config.php';
include_once dirname(__FILE__) . '/../dao/MockTestReadDao.php';
include_once dirname(__FILE__) . '/../dao/MockTestWriteDao.php';
include_once dirname(__FILE__) . '/../cache/MockTestCachingDao.php';
include_once dirname(__FILE__) . '/../models/ClusterStats.php';
include_once dirname(__FILE__) . '/../models/TopicStats.php';
include_once dirname(__FILE__) . '/../models/MockTest.php';
include_once dirname(__FILE__) . '/../../general/classes/Utils.php';

class MockTestCreator{
	private $cread;
	private $cwrite;
	private $ccache;

	public function __construct(){
		$this->cread = new MockTestReadDao();
		$this->cwrite = new MockTestWriteDao();
		$this->ccache = new MockTestCachingDao();
	}

/*
select d.topic_id,d.topic,
(count(a.question_id)*100)/953 as perc,(count(a.question_id)*22)/953 as num_ques1,
ROUND((count(a.question_id)*22)/953) as num_ques,count(a.question_id) as cnt,
ROUND(count(CASE WHEN b.difficulty = 'easy' THEN 1 END)/count(a.question_id)*ROUND((count(a.question_id)*22)/953)) as easy_count1,
ROUND(count(CASE WHEN b.difficulty = 'medium' THEN 1 END)/count(a.question_id)*ROUND((count(a.question_id)*22)/953)) as medium_count1,
ROUND(count(CASE WHEN b.difficulty = 'hard' THEN 1 END)/count(a.question_id)*ROUND((count(a.question_id)*22)/953)) as hard_count1,
count(CASE WHEN b.difficulty = 'easy' THEN 1 END)/count(a.question_id)*100 as easy_count,
count(CASE WHEN b.difficulty = 'medium' THEN 1 END)/count(a.question_id)*100 as medium_count,
count(CASE WHEN b.difficulty = 'hard' THEN 1 END)/count(a.question_id)*100 as hard_count
from ht_questions a,ht_question_metadata b,ht_question_topic_mapping c,
ht_question_topics d where a.question_id=b.question_id and a.question_id=c.question_id
and c.topic_id=d.topic_id
and a.is_active=1 and subject_id=1 
group by d.topic_id
*/

	public function getTopicWiseStats($subject_id,$count,$total){
		$stats = array();
		$stats = $this->cread->getTopicWiseStats($subject_id,$count,$total);
		$total_ques = 0;
		// foreach($temp_stats as $stat){
		// 	if(!isset($stats[$stat['topic_id']])){
		// 		$stats[$stat['topic_id']] = array();
		// 		$stats[$stat['topic_id']]['num_ques'] = 0;
		// 	}
		// 	if(!isset($stats[$stat['topic_id']]['difficulty'])) $stats[$stat['topic_id']]['difficulty'] = array();
		// 	if(!isset($stats[$stat['topic_id']]['difficulty'][$stat['difficulty']])){
		// 		$stats[$stat['topic_id']]['difficulty'][$stat['difficulty']] = $stat['num_ques'];
		// 		$stats[$stat['topic_id']]['num_ques'] += $stat['num_ques'];
		// 		$total_ques += $stat['num_ques'];
		// 	}	
		// }
		// uasort($stats, function ($item1, $item2) {
		// 	if ($item1['num_ques'] == $item2['num_ques']) {
		//         return 0;
		//     }
		//     return ($item1['num_ques'] > $item2['num_ques']) ? -1 : 1;
		//     //return $item1['num_ques'] <=> $item2['num_ques'];
		// });
		// //print_r($stats);
		return $stats;//array($stats,$total_ques);
	}

	public function distributeQuestionsByTopics($stats,$total_ques,$num_ques){
		$req_ques = 0;
		$avg_num_ques = $total_ques/count($stats)*0.2;
		$required_stats = array();
		foreach($stats as $key=>$stat){
			if($stat['num_ques']>$avg_num_ques){
				$stats[$key]['req_ques'] = 1;
				$req_ques++;
			}
			else{
				$stats[$key]['req_ques'] = 0;
			}
		}
		
		//$num_ques = $num_ques - $req_ques;
		for($i=0;$i<20;$i++){
			if($req_ques>=$num_ques){
				break;
			}
			$avg_num_ques = 2*$avg_num_ques;
			foreach($stats as $key=>$stat){
				if($stat['num_ques']>$avg_num_ques){
					$stats[$key]['req_ques']++;
					$req_ques++;
					if($req_ques>=$num_ques){
						break;
					}
				}
			}
		}

		foreach($stats as $key=>$stat){
			if($stat['req_ques']==2){
				$min = 0;
				$min_type = "";
				if(!isset($stat['difficulty']['HARD']))	$stat['difficulty']['HARD']=0;
				if(!isset($stat['difficulty']['MEDIUM']))	$stat['difficulty']['MEDIUM']=0;
				if(!isset($stat['difficulty']['EASY']))	$stat['difficulty']['EASY']=0;
				if($stat['difficulty']['HARD']>$stat['difficulty']['MEDIUM']){
					$obj = array();
					$obj['topic_id'] = $key;
					$obj['difficulty'] = "HARD";
					$obj['num_ques'] = 1;
					$min = $stat['difficulty']['MEDIUM'];
					$min_type = "MEDIUM";
					array_push($required_stats,$obj);
				}
				else{
					$obj = array();
					$obj['topic_id'] = $key;
					$obj['difficulty'] = "MEDIUM";
					$obj['num_ques'] = 1;
					$min = $stat['difficulty']['HARD'];
					$min_type = "HARD";
					array_push($required_stats,$obj);
				}
				
				if($min>$stat['difficulty']['EASY']){
					$obj = array();
					$obj['topic_id'] = $key;
					$obj['difficulty'] = $min_type;
					$obj['num_ques'] = 1;
					array_push($required_stats,$obj);
				}
				else{
					$obj = array();
					$obj['topic_id'] = $key;
					$obj['difficulty'] = "EASY";
					$obj['num_ques'] = 1;
					array_push($required_stats,$obj);
				}
			}
			else if($stat['req_ques']==1){
				$min = 0;
				$min_type = "";
				if(!isset($stat['difficulty']['HARD']))	$stat['difficulty']['HARD']=0;
				if(!isset($stat['difficulty']['MEDIUM']))	$stat['difficulty']['MEDIUM']=0;
				if(!isset($stat['difficulty']['EASY']))	$stat['difficulty']['EASY']=0;
				if($stat['difficulty']['HARD']>$stat['difficulty']['MEDIUM']){
					$min = $stat['difficulty']['MEDIUM'];
					$min_type = "MEDIUM";
				}
				else{
					$min = $stat['difficulty']['HARD'];
					$min_type = "HARD";
				}
				
				if($min>$stat['difficulty']['EASY']){
					$obj = array();
					$obj['topic_id'] = $key;
					$obj['difficulty'] = $min_type;
					$obj['num_ques'] = 1;
					array_push($required_stats,$obj);
				}
				else{
					$obj = array();
					$obj['topic_id'] = $key;
					$obj['difficulty'] = "EASY";
					$obj['num_ques'] = 1;
					array_push($required_stats,$obj);
				}
			}
		}
		return $required_stats;
	}

	public function createTests($stats,$num_tests){
		$tests = array();
		for($j=0;$j<$num_tests;$j++){
			$tests[$j] = array();
			foreach($stats as $obj){
				if($obj['num_ques']!=0){
					if($obj['num_ques']>($obj['easy_count'] + $obj['medium_count'] + $obj['hard_count'])){
						$delta = array();
						$delta['easy'] = (($obj['easy_count1'] * $obj['cnt'])/100) - $obj['easy_count'];
						$delta['medium'] = (($obj['medium_count1'] * $obj['cnt'])/100) - $obj['medium_count'];
						$delta['hard'] = (($obj['hard_count1'] * $obj['cnt'])/100) - $obj['hard_count'];
						if($delta['easy']<-1)	$delta['easy']=$delta['easy']*-1;
						if($delta['medium']<-1)	$delta['medium']=$delta['medium']*-1;
						if($delta['hard']<-1)	$delta['hard']=$delta['hard']*-1;
						if($delta['easy']>$delta['medium'] && $delta['easy']>$delta['hard']){
							$obj['easy_count']--;
						}
						else if($delta['medium']>=$delta['hard']){
							$obj['medium_count']--;
						}
						else{
							$obj['hard_count']--;
						}
					}
					else if($obj['num_ques']<($obj['easy_count'] + $obj['medium_count'] + $obj['hard_count'])){
						$delta = array();
						$delta['easy'] = (($obj['easy_count1'] * $obj['cnt'])/100) - $obj['easy_count'];
						$delta['medium'] = (($obj['medium_count1'] * $obj['cnt'])/100) - $obj['medium_count'];
						$delta['hard'] = (($obj['hard_count1'] * $obj['cnt'])/100) - $obj['hard_count'];
						if($delta['easy']<-1)	$delta['easy']=$delta['easy']*-1;
						if($delta['medium']<-1)	$delta['medium']=$delta['medium']*-1;
						if($delta['hard']<-1)	$delta['hard']=$delta['hard']*-1;
						if($delta['easy']>$delta['medium'] && $delta['easy']>$delta['hard']){
							$obj['easy_count']++;
						}
						else if($delta['medium']>=$delta['hard']){
							$obj['medium_count']++;
						}
						else{
							$obj['hard_count']++;
						}
					}
					//print_r($obj);
					$obj_qids = array();
					if($obj['easy_count']!=0){
						$qids = $this->cread->getTopicDifficultyQuestions($obj['topic_id'],'easy');
						shuffle($qids);
						$qids_used = array();
						for($i=0;$i<$num_tests;$i++){
							if($obj['easy_count']<=count($qids)){
								$obj_qids = array_merge($obj_qids,array_slice($qids, 0,$obj['easy_count']));
								//$tests[$i] = array_merge($tests[$i],array_slice($qids, 0,$obj['easy_count']));
								//$qids_used = array_merge($qids_used,array_slice($qids, 0,$obj['easy_count']));
							}
						}
					}
					if($obj['medium_count']!=0){
						$qids = $this->cread->getTopicDifficultyQuestions($obj['topic_id'],'medium');
						shuffle($qids);
						$qids_used = array();
						for($i=0;$i<$num_tests;$i++){
							if($obj['medium_count']<=count($qids)){
								$obj_qids = array_merge($obj_qids,array_slice($qids, 0,$obj['medium_count']));
								//$tests[$i] = array_merge($tests[$i],array_slice($qids, 0,$obj['medium_count']));
								//$qids_used = array_merge($qids_used,array_slice($qids, 0,$obj['easy_count']));
							}
						}

					}
					if($obj['hard_count']!=0){
						$qids = $this->cread->getTopicDifficultyQuestions($obj['topic_id'],'hard');
						shuffle($qids);
						$qids_used = array();
						for($i=0;$i<$num_tests;$i++){
							if($obj['hard_count']<=count($qids)){
								$obj_qids = array_merge($obj_qids,array_slice($qids, 0,$obj['hard_count']));
								//$tests[$i] = array_merge($tests[$i],array_slice($qids, 0,$obj['hard_count']));
								//$qids_used = array_merge($qids_used,array_slice($qids, 0,$obj['easy_count']));
							}
						}
					}
					if(count($obj_qids)>0){
						//print_r($obj_qids);
						$ques_arr = array();
						$num_questions = $obj['num_ques'];//count($obj_qids);
						$group_ids  = $this->cread->getQuestionsGroupTempIds($obj_qids);
						$group_arr = $this->cread->getQuestionsGroupTemp($group_ids);
						shuffle($group_arr);
						foreach($group_arr as $group){
							if(count($ques_arr)<$num_questions){
								$ques_arr = array_merge($ques_arr,explode(",",$group['qidstr']));
							}
						}
						print_r($obj);
						print_r($ques_arr);
						print_r($obj['topic_id'] . "   " . $obj['cnt'] . "   ");
						$tests[$j] = array_merge($tests[$j],$ques_arr);
					}
				}
			}
		}
		for($j=0;$j<$num_tests;$j++){
			//print_r($tests[$j]);
			$object = new MockTest();
			//shuffle($tests[$j]);
			$object->setValue('test_name',"QA-MT" . ($j + 1));
			$object->setValue('questions',implode(",",$tests[$j]));
			$object->setValue('total_ques',count($tests[$j]));
			$object->setValue('total_time','3600');
			$object = $this->cwrite->createObject($object);
		}
		return $tests;
	}

}