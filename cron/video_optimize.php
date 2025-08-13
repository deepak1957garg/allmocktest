<?php
$dirname = dirname(__FILE__);
$dirname = str_replace("/cron", "",$dirname);
require_once $dirname . '/includes/classes/VideoManager.php';
require_once $dirname . '/includes/classes/TipManager.php';
$dirname2 = str_replace("/sonder", "/things-api",$dirname);
require_once $dirname2 . '/vendor/autoload.php';

use Google\Cloud\Storage\StorageClient;


$vmanager = new VideoManager();
$tipmanager = new TipManager();


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
	$arr = $vmanager->getNonCdnVideoList();
	if(count($arr)>0){
		foreach($arr as $video){
			$del_arr = array();
			$vname_arr = explode("/",$video->getValue('path'));
			print_r($vname_arr);
			$vname = $vname_arr[count($vname_arr)-1];
			$object = $bucket_things->object(trim($video->getValue('path'),"/"));
			$object->downloadToFile($dirname . '/uploads/' . $vname);
			print_r($dirname . '/uploads/' . $vname);

			array_push($del_arr,$dirname . "/uploads/" . $vname);


			echo "Starting ffmpeg....\n\n\n";
			echo shell_exec("ffmpeg -i " . $dirname . "/uploads/" . $vname . " -c:v libx264 -crf 32 -deadline best -movflags faststart -vf scale=720:-2,unsharp=5:5:1.0 " . $dirname . "/uploads/tmp" . $vname . "");
			echo "Completed ffmpeg....\n\n\n";


			// $giffilename = str_replace(".mp4",".gif", $vname);

			// if($video->getValue('duration')>3){
			// 	echo "Starting ffmpeg....\n\n\n";
			// 	echo shell_exec("ffmpeg -ss 3 -to 8 -i " . $dirname . "/uploads/" . $vname . " -filter_complex \"fps=10, scale=240:-1\" " . $dirname . "/uploads/tmp" . $giffilename . "");
			// 	echo "Completed ffmpeg....\n\n\n";
			// }
			// else{
			// 	echo "Starting ffmpeg....\n\n\n";
			// 	echo shell_exec("ffmpeg -ss 0 -to 5 -i " . $dirname . "/uploads/" . $vname . " -filter_complex \"fps=10, scale=240:-1\" " . $dirname . "/uploads/tmp" . $giffilename . "");
			// 	echo "Completed ffmpeg....\n\n\n";
			// }


			$bucket->upload(
			        fopen($dirname . "/uploads/tmp" . $vname . "","r"),["name"=>trim($video->getValue('path'),"/")]
			);
			array_push($del_arr,$dirname . "/uploads/tmp" . $vname);

			// $bucket->upload(
			//         fopen($dirname . "/uploads/tmp" . $giffilename . "","r"),["name"=>"sgif/" . $giffilename]
			// );
			// array_push($del_arr,$dirname . "/uploads/tmp" . $giffilename);



			//$vmanager->updateGifPath($video->getValue('vid'),"/gif/" . $giffilename);

			$webpfilename = str_replace(".mp4",".webp", $vname);

			if($video->getValue('duration')>3){
				echo "Starting ffmpeg....\n\n\n";
				echo shell_exec("ffmpeg -ss 3 -to 6 -i " . $dirname . "/uploads/" . $vname . " -vf \"fps=12,scale=240:-1:flags=lanczos\" -loop 0 -quality 85 -compression_level 6 -y " . $dirname . "/uploads/tmp" .  $webpfilename . "");
				echo "Completed ffmpeg....\n\n\n";
			}
			else{
				echo "Starting ffmpeg....\n\n\n";
				echo shell_exec("ffmpeg -ss 0 -to 3 -i " . $dirname . "/uploads/" . $vname . " -vf \"fps=12,scale=240:-1:flags=lanczos\" -loop 0 -quality 85 -compression_level 6 -y " . $dirname . "/uploads/tmp" . $webpfilename . "");
				echo "Completed ffmpeg....\n\n\n";
			}

			$bucket->upload(
			        fopen($dirname . "/uploads/tmp" . $webpfilename . "","r"),["name"=>"swebp/" . $webpfilename]
			);
			array_push($del_arr,$dirname . "/uploads/tmp" . $webpfilename);



			$vname1 = $vname;
			// if($video->getValue('thumb')!=""){
			// 	$vname_arr = explode("/",$video->getValue('thumb'));
			// 	$vname1 = $vname_arr[count($vname_arr)-1];
			// 	$object = $bucket_things->object(trim($video->getValue('thumb'),"/"));
			// 	$object->downloadToFile($dirname . '/uploads/tmp' . $vname1);
			// }
			// else{
				$dur = "00:00:03";
				if($video->getValue('duration')==0){
					$dur = "00:00:00";
				}
				else if ($video->getValue('duration')<=3){
					$dur = "00:00:0" . ($video->getValue('duration') - 1);
				}
				$thumbfilename = str_replace(".mp4","_thumb.jpg", $vname1);
				echo "Starting ffmpeg....\n\n\n";
				echo shell_exec("ffmpeg -ss " . $dur . " -i " . $dirname . "/uploads/" . $vname1 . " -frames:v 1  -q:v 20 -vf scale=-1:240 " . $dirname . "/uploads/tmp" . $thumbfilename . "");
				$vname1 = $thumbfilename;
				echo "Completed ffmpeg....\n\n\n";
				$video->setValue('thumb',"/sthumb/" . str_replace("_thumb","",$thumbfilename) . "");
//			}

			$bucket->upload(
			        fopen($dirname . "/uploads/tmp" . $vname1 . "","r"),["name"=>trim($video->getValue('thumb'),"/")]
			);

			array_push($del_arr,$dirname . "/uploads/tmp" . $vname1);


			$vname2 = $vname;
			// if($video->getValue('firstpic')!=""){
			// 	$vname_arr = explode("/",$video->getValue('firstpic'));
			// 	$vname2 = $vname_arr[count($vname_arr)-1];
			// 	$object = $bucket_things->object(trim($video->getValue('firstpic'),"/"));
			// 	$object->downloadToFile($dirname . '/uploads/tmp' . $vname2);
			// }
			// else{
				$firstfilename = str_replace(".mp4","_first.jpg", $vname2);
				echo "Starting ffmpeg....\n\n\n";
				echo shell_exec("ffmpeg -ss 00:00:00 -i " . $dirname . "/uploads/" . $vname2 . " -frames:v 1  -q:v 20 -vf scale=-1:720 " . $dirname . "/uploads/tmp" . $firstfilename . "");
				$vname2 = $firstfilename;
				echo "Completed ffmpeg....\n\n\n";
				$video->setValue('firstpic',"/sfirst/" . $firstfilename . "");
			//}


			$bucket->upload(
			        fopen($dirname . "/uploads/tmp" . $vname2 . "","r"),["name"=>trim($video->getValue('firstpic'),"/")]
			);
			array_push($del_arr,$dirname . "/uploads/tmp" . $vname2);

			$vmanager->updateImagesPath($video->getValue('vid'),"/swebp/" . $webpfilename,$video->getValue('thumb'),$video->getValue('firstpic'));

			//print_r($del_arr);

			$vmanager->updateCdnStatus($video->getValue('vid'),1);
			$vmanager->updateUserVideo($video->getValue('uid'),$video->getValue('vid'));
			$tipmanager->addNotification($video->getValue('vid'),$video->getValue('uid'),"sell_ready");

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