<?php
include_once dirname(__FILE__) . '/../../../includes/common/Constants.php';
include_once dirname(__FILE__) . '/../../../includes/db/DBWrapper.php';

class GeneralWriteDao{

	function __construct(){
	}

	public function createObject($obj,$add_primary_key=false,$show_query=1){
		$id = 0;
		try{
			$dbname = Constants::$DEFAULT_DB;
			$primary_key = $obj->getPrimarykey();
			if($obj->getDBName()!="")	$dbname = $obj->getDBName();
			if($obj->getTableName()!=""){
				$table_name = Constants::$TABLE_PREFIX . $obj->getTableName();
				$attrs = array();
				$values = array();

				foreach($obj->getObject() as $attr=>$value){
					if($attr!=$primary_key){
						array_push($attrs,$attr);
						if(is_int($value))	array_push($values,"{" . $attr  . "}");
						else if($value=='0000-00-00')	array_push($values,"now()");
						//else if($value=='NULL')	array_push($values,NULL);
						else	array_push($values,"'{" . $attr  . "}'");
					}
					else if($attr==$primary_key && $add_primary_key){
						array_push($attrs,$attr);
						if(is_int($value))	array_push($values,"{" . $attr  . "}");
						else if($value=='0000-00-00')	array_push($values,"now()");
						else	array_push($values,"'{" . $attr  . "}'");
					}
				}

				$dbinfo =  DBWrapper::getDBInfoObject($dbname);
				$q=sprintf("insert into %s (%s) values (%s)",$table_name,implode(",",$attrs),implode(",",$values));
				list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$obj->getObject(),$show_query);
				if(!$add_primary_key){
					$id = DBWrapper::getMysqlLastInsertId($dbinfo);
					if($obj->getPrimarykey()!='')	$obj->setValue($obj->getPrimarykey(),$id."");
				}
			}
			else{
				error_log('createObject tablename missing');
			}
		}
		catch(Exception $ex){ }
		return $obj;
	}

	public function updateObject($obj,$changes,$show_query=1){
		$success = false;
		try{
			$dbname = Constants::$DEFAULT_DB;
			$primary_key = $obj->getPrimarykey();
			if($obj->getDBName()!="")	$dbname = $obj->getDBName();
			if($obj->getTableName()!=""){
				$table_name = Constants::$TABLE_PREFIX . $obj->getTableName();
				$dbinfo =  DBWrapper::getDBInfoObject($dbname);
				$q = sprintf("Update %s set ",$table_name);
				foreach($changes as $key => $value){
					$q.=sprintf("%s='{%s}',",$key,$key);
				}
				$q = rtrim($q,",");
				$q.=sprintf(" where %s=%d",$primary_key,$obj->getValue($primary_key));
				list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$changes,$show_query);
			}
		}
		catch(Exception $ex){ }
		return $success;
	}

	public function createMultipleObject($objs,$add_primary_key=false,$show_query=1){
		$id = 0;
		try{
			$obj = $objs[0];
			$dbname = Constants::$DEFAULT_DB;
			$primary_key = $obj->getPrimarykey();
			if($obj->getDBName()!="")	$dbname = $obj->getDBName();
			if($obj->getTableName()!=""){
				$table_name = Constants::$TABLE_PREFIX . $obj->getTableName();
				$attrs = array();
				$values = array();

				foreach($objs as $obj){
					foreach($obj->getObject() as $attr=>$value){
						if($attr!=$primary_key){
							array_push($attrs,$attr);
						}
						else if($attr==$primary_key && $add_primary_key){
							array_push($attrs,$attr);
						}
					}
					break;
				}

				foreach($objs as $obj){
					$str = '(';
					foreach($obj->getObject() as $attr=>$value){
						if($attr!=$primary_key){
							$str.=sprintf('"%s",',$value);
						}
						else if($attr==$primary_key && $add_primary_key){
							$str.=sprintf('"%s",',$value);
						}
					}
					$str = rtrim ( $str, "," );
					$str .= ')';
					array_push($values,$str);
				}

				$dbinfo =  DBWrapper::getDBInfoObject($dbname);
				$q=sprintf("insert into %s (%s) values %s",$table_name,implode(",",$attrs),implode(",",$values));
				list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$obj->getObject(),$show_query);
				if(!$add_primary_key){
					$id = DBWrapper::getMysqlLastInsertId($dbinfo);
					if($obj->getPrimarykey()!='')	$obj->setValue($obj->getPrimarykey(),$id."");
				}
			}
			else{
				error_log('createObject tablename missing');
			}
		}
		catch(Exception $ex){ }
		return $obj;
	}

	public function deleteObject($obj,$params,$show_query=1){
		$success = false;
		try{
			$dbname = Constants::$DEFAULT_DB;
			$primary_key = $obj->getPrimarykey();
			if($obj->getDBName()!="")	$dbname = $obj->getDBName();
			if($obj->getTableName()!=""){
				$table_name = Constants::$TABLE_PREFIX . $obj->getTableName();
				$dbinfo =  DBWrapper::getDBInfoObject($dbname);
				$q = sprintf("Delete from %s where ",$table_name);
				foreach($params as $key => $value){
					$q.=sprintf("%s='{%s}' and ",$key,$key);
				}
				$q = rtrim($q," and ");
				list($success,$num_affected_rows,$error,$error_no) = DBWrapper::writeData($dbinfo,$q,__FUNCTION__,$params,$show_query);
			}
		}
		catch(Exception $ex){ }
		return $success;
	}

}