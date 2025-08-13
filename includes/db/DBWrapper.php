<?php 
include_once dirname(__FILE__) . '/../config/DbConnectionList.php';

class DBWrapper {
	private static $db_connections = array();
	private static $read_db_connections = array();
	private static $migrate_db_connections = array();
	private static $LOST_CONNECTION_ERROR = 2006;
	private static $TOO_MANY_CONNECTIONS_ERROR = 1040;
	public static $CONNECTION_TYPE_MIGRATE = 'migrate';
	public static $DB_ERRORS = array();

	function __construct(){ }

	public static function getConnection($dbtype,$type='all',$shard=''){
		$conn = null;
		try{
			$dbname='';
			if(isset(DbConnectionList::$DB_TYPES[$dbtype])){
				$dbname=DbConnectionList::$DB_TYPES[$dbtype];
				if($shard!='')	$dbname.=$shard;
			}
			if($dbname!=''){
				if($type=='read')	$conn=self::getReadDbConnection($dbname);
				else if($type=='migrate')	$conn=self::getMigrateDbConnection($dbname);
				else 	$conn=self::getWriteDbConnection($dbname);
			}
		}
		catch(Exception $e){
			$conn = null;
			error_log('mysql ' . $dbname . ' connection fail ' . $e->getMessage());
		}
		return $conn;
	}

	private static function getReadDbConnection($dbname){
		$conn = null;
		try{
			if(!array_key_exists($dbname,self::$read_db_connections)){
				$conn_info = DbConnectionList::$READ_CONNECTION_INFO[$dbname];
				$conn = new mysqli($conn_info['host'], $conn_info['user'], $conn_info['pass'], $dbname);
				if($conn->connect_error){
					if($conn->connect_errno==self::$TOO_MANY_CONNECTIONS_ERROR){
						self::closeDBConnections();
						$conn = new mysqli($conn_info['host'], $conn_info['user'], $conn_info['pass'], $dbname);
					}
				}

				if($conn->connect_error){
					error_log('mysql ' . $dbname . ' on host ' . $conn_info['host'] . ' connection fail. Connection Error : ' . print_r($conn->connect_error,1));
				}
				else{
					$conn->set_charset("utf8");
					self::$read_db_connections[$dbname]=$conn;						
				}
			}
			else $conn=self::$read_db_connections[$dbname];
		}
		catch(Exception $e){
			$conn = null;
			error_log('mysql read ' . $dbname . ' connection fail ' . $e->getMessage());
		}
		return $conn;
	}

	private static function getWriteDbConnection($dbname){
		$conn = null;
		try{
			if(!array_key_exists($dbname,self::$db_connections)){
				$conn_info = DbConnectionList::$CONNECTION_INFO[$dbname];
				$conn = new mysqli($conn_info['host'], $conn_info['user'], $conn_info['pass'], $dbname);
				// error_log('mysql ' . $dbname);
				if($conn->connect_error){
					if($conn->connect_errno==self::$TOO_MANY_CONNECTIONS_ERROR){
						self::closeDBConnections();
						$conn = new mysqli($conn_info['host'], $conn_info['user'], $conn_info['pass'], $dbname);
					}
				}

				if ($conn->connect_error) {
					error_log('mysql ' . $dbname . ' on host ' . $conn_info['host'] . ' connection fail. Connection Error : ' . print_r($conn->connect_error,1));
				}
				else{
					$conn->set_charset("utf8");
					self::$db_connections[$dbname]=$conn;						
				}
			}
			else $conn=self::$db_connections[$dbname];
		}
		catch(Exception $e){
			$conn = null;
			error_log('mysql write ' . $dbname . ' connection fail ' . $e->getMessage());
		}
		return $conn;
	}

	private static function getMigrateDbConnection($dbname){
		$conn = null;
		try{
			if(!array_key_exists($dbname,self::$migrate_db_connections)){
				$conn_info = DbConnectionList::$MIGRATE_CONNECTION_INFO[$dbname];
				$conn = new mysqli($conn_info['host'], $conn_info['user'], $conn_info['pass'], $dbname);
				//error_log('mysql migrate ' . $dbname);
				if($conn->connect_error){
					if($conn->connect_errno==self::$TOO_MANY_CONNECTIONS_ERROR){
						self::closeDBConnections();
						$conn = new mysqli($conn_info['host'], $conn_info['user'], $conn_info['pass'], $dbname);
					}
				}

				if ($conn->connect_error) {
					error_log('mysql ' . $dbname . ' on host ' . $conn_info['host'] . ' connection fail. Connection Error : ' . print_r($conn->connect_error,1));
				}
				else{
					$conn->set_charset("utf8");
					self::$migrate_db_connections[$dbname]=$conn;
				}
			}
			else $conn=self::$migrate_db_connections[$dbname];
		}
		catch(Exception $e){
			$conn = null;
			error_log('mysql read ' . $dbname . ' connection fail ' . $e->getMessage());
		}
		return $conn;
	}

	public static function getDBInfoObject($dbtype,$permtype='all',$shard=''){
		return array('dbtype'=>$dbtype,'permtype'=>$permtype,'shard'=>$shard);
	}

	public static function getMultiRows($dbinfo,$q,$fn='',$params=array(),$show_query=0){
		$arr = array();
		$error = '';
		$error_no = 0;
		try{
			$conn = self::getConnection($dbinfo['dbtype'],$dbinfo['permtype'],$dbinfo['shard']);
			if(isset($conn) && !$conn->connect_error){
				if(count($params)>0){
					foreach($params as $key=>$value)	$q = str_replace('{' . $key . '}',$conn->real_escape_string($value),$q);
				}
				if($show_query==1)	error_log($q);
				//print_r($q);
				$r = $conn->query($q);
				if($r){
					if (function_exists('mysqli_fetch_all'))	$arr = $r->fetch_all(MYSQLI_ASSOC);
					else{
						while($row = $r->fetch_array(MYSQLI_ASSOC))		$arr[] = $row;
					}
					$r->free_result();
				}
				else{
					$error_no = $conn->errno;
					if($error_no==self::$LOST_CONNECTION_ERROR){
						self::closeDBConnections();
						$conn = self::getConnection($dbinfo['dbtype'],$dbinfo['permtype'],$dbinfo['shard']);
						if(isset($conn)){
							$r = $conn->query($q);
							if($r){
								if (function_exists('mysqli_fetch_all'))	$arr = $r->fetch_all(MYSQLI_ASSOC);
								else{
									while($row = $r->fetch_array(MYSQLI_ASSOC))		$arr[] = $row;
								}
								$r->free_result();
							}
							else{	$error = $conn->error;	$error_no = $conn->errno;	}
						}
						else{	$error = 'connection failure';	$error_no = -1;	}
					}
					else	$error = $conn->error;
				}
			}
			else{
				if(isset($conn)){	$error = $conn->connect_error;	$error_no = $conn->connect_errno; }
				else{ $error = 'connection failure';  $error_no = -1; }
				if(count($params)>0){
					foreach($params as $key=>$value)	$q = str_replace('{' . $key . '}',self::mysqlRealEscape($value),$q);
				}
				array_push(self::$DB_ERRORS,'Mysql error fn ' . $fn . ' : error_no : ' . $error_no . ', error : ' . $error);
			}

			if($error_no!= 0){
				error_log($q);
				error_log('Mysql error fn ' . $fn . ' : error_no : ' . $error_no . ', error : ' . $error);
			}
		}
		catch(Exception $e){ }
		return array($arr,$error,$error_no);
	}

	public static function getSingleRow($dbinfo,$q,$fn='',$params=array(),$show_query=0){
		$arr = array();
		$error = '';
		$error_no = 0;
		try{
			$conn = self::getConnection($dbinfo['dbtype'],$dbinfo['permtype'],$dbinfo['shard']);
			if(isset($conn) && !$conn->connect_error){
				if(count($params)>0){
					foreach($params as $key=>$value)	$q = str_replace('{' . $key . '}',$conn->real_escape_string($value),$q);
				}
				if($show_query==1)	error_log($q);
				$r = $conn->query($q);
				if($r){
					if($r->num_rows>0)	$arr = $r->fetch_row();
					else{	$error = 'No row found';	$error_no = 0;	}
					$r->free_result();
				}
				else{
					$error_no = $conn->errno;
					if($error_no==self::$LOST_CONNECTION_ERROR){
						self::closeDBConnections();
						$conn = self::getConnection($dbinfo['dbtype'],$dbinfo['permtype'],$dbinfo['shard']);
						if(isset($conn)){
							$r = $conn->query($q);
							if($r){
								if($r->num_rows>0)	$arr = $r->fetch_row();
								else{	$error = 'No row found';	$error_no = 0;	}
								$r->free_result();
							}
							else{	$error = $conn->error;	$error_no = $conn->errno;	}
						}
						else{	$error = 'connection failure';	$error_no = -1;	}
					}
					else	$error = $conn->error;
				}
			}
			else{
				if(isset($conn)){	$error = $conn->connect_error;	$error_no = $conn->connect_errno; }
				else{ $error = 'connection failure';  $error_no = -1; }
				if(count($params)>0){
					foreach($params as $key=>$value)	$q = str_replace('{' . $key . '}',self::mysqlRealEscape($value),$q);
				}
				array_push(self::$DB_ERRORS,'Mysql error fn ' . $fn . ' : error_no : ' . $error_no . ', error : ' . $error);
			}

			if($error_no!= 0){
				error_log($q);
				error_log('Mysql error fn ' . $fn . ' : error_no : ' . $error_no . ', error : ' . $error);
			}
		}
		catch(Exception $e){ }
		return array($arr,$error,$error_no);
	}

	public static function writeData($dbinfo,$q,$fn='',$params=array(),$show_query=1){
		$status = false;
		$num_affected_rows = 0;
		$error = '';
		$error_no = 0;
		try{
			$conn = self::getConnection($dbinfo['dbtype'],$dbinfo['permtype'],$dbinfo['shard']);
			if(isset($conn) && !$conn->connect_error){
				if(count($params)>0){
					foreach($params as $key=>$value){
						if(!is_array($value))	$q = str_replace('{' . $key . '}',$conn->real_escape_string($value),$q);
					}	
				}
				if($show_query==1)	error_log($q);
				//print_r($params);
				$r = $conn->query($q);
				if($r){
					$status = true;
					$num_affected_rows = $conn->affected_rows;
				}
				else{
					$error_no = $conn->errno;
					if($error_no==self::$LOST_CONNECTION_ERROR){
						self::closeDBConnections();
						$conn = self::getConnection($dbinfo['dbtype'],$dbinfo['permtype'],$dbinfo['shard']);
						if(isset($conn)){
							$r = $conn->query($q);
							if($r){
								$status = true;
								$num_affected_rows = $conn->affected_rows;
							}
							else{	$error = $conn->error;	$error_no = $conn->errno;	}
						}
						else{	$error = 'connection failure';	$error_no = -1;	}
					}
					else	$error = $conn->error;
				}
			}
			else{
				if(isset($conn)){	$error = $conn->connect_error;	$error_no = $conn->connect_errno; }
				else { $error = 'connection failure';  $error_no = -1; }
				if(count($params)>0){
					foreach($params as $key=>$value)	$q = str_replace('{' . $key . '}',self::mysqlRealEscape($value),$q);
				}
				array_push(self::$DB_ERRORS,'Mysql error fn ' . $fn . ' : error_no : ' . $error_no . ', error : ' . $error);
			}

			if($error_no!= 0){
				error_log($q);
				error_log('Mysql error fn ' . $fn . ' : error_no : ' . $error_no . ', error : ' . $error);
			}
		}
		catch(Exception $e){ }
		return array($status,$num_affected_rows,$error,$error_no);
	}

	public static function getMysqlLastQueryInfo($dbinfo){
		$info = array();
		try{
			$conn = self::getConnection($dbinfo['dbtype'],$dbinfo['permtype'],$dbinfo['shard']);
			$info_str = $conn->info;

			preg_match("/Records: ([0-9]*)/", $info_str, $records);
			preg_match("/Duplicates: ([0-9]*)/", $info_str, $dupes);
			preg_match("/Warnings: ([0-9]*)/", $info_str, $warnings);
			preg_match("/Deleted: ([0-9]*)/", $info_str, $deleted);
			preg_match("/Skipped: ([0-9]*)/", $info_str, $skipped);
			preg_match("/Rows matched: ([0-9]*)/", $info_str, $rows_matched);
			preg_match("/Changed: ([0-9]*)/", $info_str, $changed);

			if(isset($records[1]))	$info['records'] = $records[1];
			if(isset($dupes[1]))	$info['duplicates'] = $dupes[1];
			if(isset($warnings[1]))	$info['warnings'] = $warnings[1];
			if(isset($deleted[1]))	$info['deleted'] = $deleted[1];
			if(isset($skipped[1]))	$info['skipped'] = $skipped[1];
			if(isset($rows_matched[1]))	$info['rows_matched'] = $rows_matched[1];
			if(isset($changed[1]))	$info['changed'] = $changed[1];
		}
		catch(Exception $e){ }
		return $info;
	}

	public static function getMysqlLastInsertId($dbinfo){
		$id = 0;
		try{
			$conn = self::getConnection($dbinfo['dbtype'],$dbinfo['permtype'],$dbinfo['shard']);
			$id = $conn->insert_id;
		}
		catch(Exception $e){ }
		return $id;
	}

	public static function closeDBConnections(){
		foreach(self::$db_connections as $key=>$conn){
			$conn->close();
		}
		self::$db_connections = array();
 	}

	public static function closeReadDBConnections(){
		foreach(self::$read_db_connections as $key=>$conn){
			$conn->close();
		}
		self::$read_db_connections = array();
	}

	public static function mysqlRealEscape($value){
		$search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
		$replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
		return str_replace($search, $replace, $value);
	}

	public static function getErrors(){
		return self::$DB_ERRORS;
	}

	public static function resetErrors(){
		self::$DB_ERRORS = array();
	}

}
?>