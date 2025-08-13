<?php 
 include_once dirname(__FILE__) . '/../config/Config.php';
 include_once dirname(__FILE__) . '/../../libs/aws/aws-autoloader.php';
 
 class S3Wrapper{
	private $s3obj;

	function __construct(){
		$this->s3obj=null;
		$this->createS3Object();
	}

	private function createS3Object(){
		if($this->s3obj==null){
			$this->s3obj = new Aws\S3\S3Client([
				'version'     => 'latest',
				'region'      => Config::AWS_REGION,
				'credentials' => [
					'key'    => Config::AWS_KEY,
					'secret' => Config::AWS_SECRET
				]
			]);
		}
	}

	public function uploadFile($container_name,$filename,$localfile,$contenttype='',$params=array()){
		$success = false;
		try{
			$arr = array();
			$arr['Bucket'] = str_replace("_","-",$container_name);
			$arr['Key'] = $filename;
			$arr['SourceFile'] = $localfile;
			if($contenttype!='')	$arr['ContentType'] = $contenttype;
			foreach($params as $key=>$value)	$arr[$key] = $value;
			$result=$this->s3obj->putObject($arr);
			$success = true;
		}
		catch(Aws\Exception\S3Exception $e){
			$success = false;
			error_log('S3 upload file Exception : ' . print_r($ex->getAwsErrorCode(),1));
		}
		return $success;
	}

	public function multipartUploadFile($container_name,$filename,$localfile){
		$success = false;
		try{
			$uploader = new MultipartUploader($this->s3obj, $localfile, [
			    'bucket' => str_replace("_","-",$container_name),
			    'key'    => $filename,
			]);
			$success = true;
		}
		catch(Aws\Exception\S3Exception $e){
			$success = false;
			error_log('S3 multi part upload file Exception : ' . print_r($ex->getAwsErrorCode(),1));
		}
		return $success;
	}

	private function deleteFile($container_name,$filename){
		$success = false;
		try{
			$result=$this->s3obj->deleteObject([
				'Bucket' => str_replace("_","-",$container_name),
				'Key'    => $filename,
			]);
			$success = true;
		}
		catch (Aws\Exception\S3Exception $e) {
			$success = false;
			error_log('S3 delete file Exception : ' . print_r($ex->getAwsErrorCode(),1));
		}
		return $success;
	}

	public function copyFile($container_name,$old_container_name,$filename){
		$success = false;
		try{
			$container_name = str_replace("_","-",$container_name);
			$old_container_name = str_replace("_","-",$old_container_name);
			$result = $this->s3obj->copyObject([
			    'Bucket'     => $container_name,
			    'Key'        => $filename,
			    'CopySource' => "$old_container_name/{$filename}",
			]);
			//error_log(print_r($result,1));
			$success = true;
		}
		catch (Aws\Exception\S3Exception $e) {
			$success = false;
			error_log('S3 delete file Exception : ' . print_r($ex->getAwsErrorCode(),1));
		}
		return $success;
	}

	public function getobject($container_name,$filename){
		$retval = null;
		try{
			if($container_name!=''){
				$arr = array();
				$arr['Bucket'] = str_replace("_","-",$container_name);
				$arr['Key'] = $filename;
				$result = $this->s3obj->getObject($arr);
				//header("Content-Type: {$result['ContentType']}");
				if(isset($result['Body']))	$retval=$result['Body'];
				else 	error_log("get object from S3 Fails : ");
			}
		}
		catch(Exception $ex){
			error_log("get object from S3 Exception : " . print_r($ex->getAwsErrorCode(),1));
			$retval = null;
		}
		return $retval;
	}

	public function streamObject($container_name,$filename,$filepath_pointer,$hrds= array()){
		$status = false;
		try{
			$this->s3obj->registerStreamWrapper();
			if ($stream = fopen('s3://' . str_replace("_","-",$container_name)  . '/' . $filename, 'r')) {
				while (!feof($stream)) {
					fwrite($filepath_pointer, fread($stream, 1048576));
				}
				fclose($stream);
				$status = true;
			}
		}catch(Exception $ex){
			error_log("stream object Exception : " . print_r($ex->getAwsErrorCode(),1));
			$status = false;
		}
		return $status;
	}

	
}
?>