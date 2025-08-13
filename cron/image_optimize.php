<?php
$dirname = dirname(__FILE__);
$dirname = str_replace("/cron", "",$dirname);
require_once $dirname . '/includes/classes/VideoManager.php';
require_once $dirname . '/includes/classes/TipManager.php';
require_once $dirname . '/includes/common/ImageOperations.php';
$dirname2 = str_replace("/sonder", "/things-api",$dirname);
//$dirname2 = str_replace("/sonder-web", "/things-web",$dirname);
require_once $dirname2 . '/vendor/autoload.php';
use Google\Cloud\Storage\StorageClient;


$vmanager = new VideoManager();
$tipmanager = new TipManager();
$imageOperations = new ImageOperations('',90);

$things_config = [
    'keyFilePath' => $dirname . "/includes/config/things-app-d3310-firebase-adminsdk-lo8as-22523efa70.json",
    'projectId' => 'things-app-d3310',
];

$things_storage = new StorageClient($things_config);
$config = [
        'keyFilePath' => $dirname . "/includes/config/sonder-app-01-firebase-adminsdk-7hrcu-851f825ef8.json",
        'projectId' => 'udhaarplease',
];
$storage = new StorageClient($config);


$bucket_things = $storage->bucket("sonder-app-01.firebasestorage.app");
//$bucket = $storage->bucket("humantales");
$bucket = $things_storage->bucket("thingsapp");

while(1){
	$arr = $vmanager->getNonCdnPicList();
	if(count($arr)>0){
		foreach($arr as $obj){
			$del_arr = array();
			$vname_arr = explode("/",$obj['upic']);
			$vname = $vname_arr[count($vname_arr)-1];
			$object = $bucket_things->object(trim($obj['upic'],"/"));
			$oldpath = $dirname . '/uploads/' . $vname;
			$object->downloadToFile($oldpath);

			$ext = $imageOperations->getImageExtension($oldpath);
			$newpath = $dirname . '/uploads/pic-' . $vname;
			$thumbpath = $dirname . '/uploads/thumb-' . $vname;


			$retval = $imageOperations->resize($dirname . '/uploads/' . $vname,str_replace($ext,"",$newpath),0,720);
			$retval = $imageOperations->resize($dirname . '/uploads/' . $vname,str_replace($ext,"",$thumbpath),0,200);


			$obj['upic2'] = str_replace("/profile/","/suthumb/",$obj['upic']);
			$obj['upic'] = str_replace("/profile/","/supic/",$obj['upic']);

			$bucket->upload(
			        fopen($newpath,"r"),["name"=>trim($obj['upic'],"/")]
			);
			$bucket->upload(
			        fopen($thumbpath,"r"),["name"=>trim($obj['upic2'],"/")]
			);
			array_push($del_arr,$oldpath);
			array_push($del_arr,$newpath);
			array_push($del_arr,$thumbpath);

			
			$vmanager->updateUpicAndThumb($obj['uid'],$obj['upic'],$obj['upic2'],1);

			//print_r($del_arr);
			foreach($del_arr as $tmp_path){
				unlink($tmp_path);
			}
		}
	}
	else{
		break;
	}
}
?>