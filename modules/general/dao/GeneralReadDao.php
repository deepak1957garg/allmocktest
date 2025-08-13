<?php
include_once dirname(__FILE__) . '/../../../includes/common/Constants.php';
include_once dirname(__FILE__) . '/../../../includes/db/DBWrapper.php';

class GeneralReadDao{

	function __construct(){
	}

	public function getObject($obj,$params=array(),$show_query=1){
		try{
			$dbname = Constants::$DEFAULT_DB;
			if($obj->getDBName()!="")	$dbname = $obj->getDBName();
			if($obj->getTableName()!=""){
				$table_name = Constants::$TABLE_PREFIX . $obj->getTableName();
				$str ='';
				foreach($params as $attr=>$value){
					$str.=sprintf(" and %s='{%s}'",$attr,$attr);
				}
				$str = trim($str);
				$str = trim($str,'and');
				$str = trim($str);
				if($str=="")	$str=sprintf("%s=%d",$obj->getPrimarykey(),$obj->getValue($obj->getPrimarykey()));

				$dbinfo =  DBWrapper::getDBInfoObject($dbname);
				$q=sprintf("Select * from %s where %s",$table_name,$str);
				list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,$params,$show_query);
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

	public function getObjectCount($obj,$params=array(),$show_query=1){
		$count = 0;
		try{
			$dbname = Constants::$DEFAULT_DB;
			if($obj->getDBName()!="")	$dbname = $obj->getDBName();
			if($obj->getTableName()!=""){
				$table_name = Constants::$TABLE_PREFIX . $obj->getTableName();
				$str ='';
				foreach($params as $attr=>$value){
					$str.=sprintf(" and %s='{%s}'",$attr,$attr);
				}
				$str = trim($str);
				$str = trim($str,'and');
				$str = trim($str);
				if($str=="")	$str=sprintf("%s=%d",$obj->getPrimarykey(),$obj->getValue($obj->getPrimarykey()));

				$dbinfo =  DBWrapper::getDBInfoObject($dbname);
				$q=sprintf("Select count(*) as count from %s where %s",$table_name,$str);
				list($arr,$error,$error_no) = DBWrapper::getSingleRow($dbinfo,$q,__FUNCTION__,$params,$show_query);
				if($error==''){
					$count = $row[0];
				}
			}
			else{
				error_log('getObject tablename missing');
			}
		}
		catch(Exception $ex){ }
		return $obj;
	}	

	public function getList($obj,$params=array(),$show_query=1){
		try{
			$dbname = Constants::$DEFAULT_DB;
			if($obj->getDBName()!="")	$dbname = $obj->getDBName();
			if($obj->getTableName()!=""){
				$table_name = Constants::$TABLE_PREFIX . $obj->getTableName();
				$str ='';
				foreach($params as $attr=>$value){
					$str.=sprintf(" and %s='{%s}'",$attr,$attr);
				}
				$str = trim($str);
				$str = trim($str,'and');
				$str = trim($str);
				if($str=="")	$str=sprintf("%s=%d",$obj->getPrimarykey(),$obj->getValue($obj->getPrimarykey()));

				$dbinfo =  DBWrapper::getDBInfoObject($dbname);
				$q=sprintf("Select * from %s where %s",$table_name,$str);
				list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,$params,$show_query);
			}
			else{
				error_log('getObject tablename missing');
			}
		}
		catch(Exception $ex){ }
		return $arr;
	}

	public function getListByIds($obj,$ids,$params=array(),$show_query=1){
		$final_arr = array();
		try{
			$dbname = Constants::$DEFAULT_DB;
			if($obj->getDBName()!="")	$dbname = $obj->getDBName();
			if($obj->getTableName()!=""){
				$table_name = Constants::$TABLE_PREFIX . $obj->getTableName();
				$str ='';
				foreach($params as $attr=>$value){
					$str.=sprintf(" and %s='{%s}'",$attr,$attr);
				}
				$str .= sprintf(' and %s in (%s)',$obj->getPrimarykey(),implode(",",$ids));
				$str = trim($str);
				$str = trim($str,'and');
				$str = trim($str);
				if($str=="")	$str=sprintf("%s=%d",$obj->getPrimarykey(),$obj->getValue($obj->getPrimarykey()));

				$dbinfo =  DBWrapper::getDBInfoObject($dbname);
				$q=sprintf("Select * from %s where %s",$table_name,$str);
				list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,$params,$show_query);
				foreach($arr as $object){
					$final_arr[$object[$obj->getPrimarykey()]] = $object;
				}
			}
			else{
				error_log('getObject tablename missing');
			}
		}
		catch(Exception $ex){ }
		return $final_arr;
	}

}