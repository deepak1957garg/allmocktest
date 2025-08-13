<?php
include_once dirname(__FILE__) . '/../../../includes/common/Constants.php';
include_once dirname(__FILE__) . '/../../../includes/db/DBWrapper.php';

class GeneralMemcachedDao{

	function __construct(){
	}

	public function createObject($obj){
		$id = 0;
		try{
			
			$dbname = Constants::$DEFAULT_DB;
			$primary_key = $obj->getPrimarykey();
			if($obj->getDBName()!="")	$dbname = $obj->getDBName();
			if($obj->getTableName()!=""){
				$attrs = array();
				$values = array();

				foreach($obj->getObject() as $attr=>$value){
					if($attr!=$primary_key){
						array_push($attrs,$attr);
						if(is_int($value))	array_push($values,"{" . $attr  . "}");
						else if($value=='0000-00-00')	array_push($values,"now()");
						else	array_push($values,"'{" . $attr  . "}'");
					}
				}

				$dbinfo =  DBWrapper::getDBInfoObject($obj->getDBName());
				$q=sprintf("insert into %s (%s) values (%s)",$obj->getTableName(),implode(",",$attrs),implode(",",$values));
				list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$obj->getObject());
				$id = DBWrapper::getMysqlLastInsertId($dbinfo);
				if($obj->getPrimarykey()!='')	$obj->setValue($obj->getPrimarykey(),$id."");
			}
			else{
				error_log('createObject tablename missing');
			}
		}
		catch(Exception $ex){ }
		return $obj;
	}

	public function updateObject($obj,$changes){
		$success = false;
		try{
			$dbname = Constants::$DEFAULT_DB;
			$primary_key = $obj->getPrimarykey();
			if($obj->getDBName()!="")	$dbname = $obj->getDBName();
			if($obj->getTableName()!=""){
				$dbinfo =  DBWrapper::getDBInfoObject($obj->getDBName());
				$q = sprintf("Update %s set ",$obj->getTableName());
				foreach($changes as $key => $value){
					$q.=sprintf("%s='{%s}',",$key,$key);
				}
				$q = rtrim($q,",");
				$q.=sprintf(" where %s=%d",$primary_key,$obj->getValue($primary_key));
				list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$changes);
			}
		}
		catch(Exception $ex){ }
		return $success;
	}	


	public function getObject($obj,$params=array()){
		try{
			$dbname = Constants::$DEFAULT_DB;
			if($obj->getDBName()!="")	$dbname = $obj->getDBName();
			if($obj->getTableName()!=""){
				$str ='';
				foreach($params as $attr=>$value){
					$str.=sprintf(" and %s='{%s}'",$attr,$attr);
				}
				$str = trim($str);
				$str = trim($str,'and');
				$str = trim($str);
				if($str=="")	$str=sprintf("%s=%d",$obj->getPrimarykey(),$obj->getValue($obj->getPrimarykey()));

				$dbinfo =  DBWrapper::getDBInfoObject($dbname);
				$q=sprintf("Select * from %s where %s",$obj->getTableName(),$str);
				list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,$params,1);
				if($error==''){
					if(count($arr)!=0){
						foreach($arr as $row){
							$obj->setObject($row);
							break;
						}
					}
					else{
						$obj->setValue($obj->getPrimarykey(),0);
					}
				}
			}
			else{
				error_log('getObject tablename missing');
			}
		}
		catch(Exception $ex){ }
		return $obj;
	}

	public function getFormObject($form_name=''){
		$final_arr = array();
		try{
			$params = array('form_name'=>$form_name);
			$dbinfo =  DBWrapper::getDBInfoObject('sgf');
			$q=sprintf("select * from `form_structure` where form_type='{form_name}' order by parent_id");
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,$params,1);
			if($error==''){
				foreach($arr as $row){
					$obj = $row;
					if($row['parent_id']!=0){
						if(!isset($final_arr[$row['parent_id']]['children']))	$final_arr[$row['parent_id']]['children'] = array();
						array_push($final_arr[$row['parent_id']]['children'],$row);
					}
					else $final_arr[$row['id']] = $row;
				}
			}
			else{
				error_log('getObject tablename missing');
			}
			$final_arr = array_values($final_arr);
		}
		catch(Exception $ex){ }
		return $final_arr;
	}

	public function getList($obj,$params=array()){
		try{
			$dbname = Constants::$DEFAULT_DB;
			if($obj->getDBName()!="")	$dbname = $obj->getDBName();
			if($obj->getTableName()!=""){
				$str ='';
				foreach($params as $attr=>$value){
					$str.=sprintf(" and %s='{%s}'",$attr,$attr);
				}
				$str = trim($str);
				$str = trim($str,'and');
				$str = trim($str);
				if($str=="")	$str=sprintf("%s=%d",$obj->getPrimarykey(),$obj->getValue($obj->getPrimarykey()));

				$dbinfo =  DBWrapper::getDBInfoObject($dbname);
				$q=sprintf("Select * from %s where %s",$obj->getTableName(),$str);
				list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,$params,1);
			}
			else{
				error_log('getObject tablename missing');
			}
		}
		catch(Exception $ex){ }
		return $arr;
	}
	

}
// // $model = new Program();
// // //$model->setValue('pname','test');
// $daobj = new GeneralDao();
// $arr = $daobj->getFormObject('team');
// $json = json_encode($arr);
// print_r($json);

// // print_r($id);

// // $model->setValue('pid',2);
// // $obj = $daobj->getObject($model,array('slug'=>'acde'));
// // print_r($obj->getObject());
