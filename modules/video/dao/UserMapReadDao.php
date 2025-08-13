<?php
include_once dirname(__FILE__) . '/../../general/dao/GeneralReadDao.php';

class UserMapReadDao extends GeneralReadDao{

	function __construct(){
	}

	function getMemes(){
		$arr = array();
		try{
			$dbinfo =  DBWrapper::getDBInfoObject('uplease');
			$q = sprintf("select * from up_memes where created_on<'%s' order by created_on desc limit 0,30",date("Y-m-d H:i:s",strtotime('now')));
			//print_r($q);
			list($arr,$error,$error_no) = DBWrapper::getMultiRows($dbinfo,$q,__FUNCTION__,array());
		}
		catch(Exception $ex){ }
		return $arr;
	}

}