<?php
$dirname = dirname(__FILE__);
$dirname = str_replace("/cron", "",$dirname);
require_once $dirname . '/includes/classes/VideoManager.php';
require_once $dirname . '/includes/classes/TipManager.php';
require_once $dirname . '/vendor/autoload.php';
//use Google\Cloud\Messaging\CloudMessage;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

$vmanager = new VideoManager();
$tipmanager = new TipManager();




$factory = (new Factory)->withServiceAccount($dirname . "/includes/config/things-app-d3310-firebase-adminsdk-lo8as-22523efa70.json");

$things_config = [
    'keyFilePath' => $dirname . "/includes/config/things-app-d3310-firebase-adminsdk-lo8as-22523efa70.json",
    'projectId' => 'things-app-d3310',
];

//$things_messaging = new CloudMessage($things_config);
$messaging = $factory->createMessaging();

$title = 'My Notification Title';
$body = 'My Notification Body';
$imageUrl = 'https://picsum.photos/400/200';

$notification = Notification::fromArray([
    'title' => $title,
    'body' => $body,
    'image' => $imageUrl,
]);

$deviceToken = "dAJPD9r3T8uNG6UaALVcfK:APA91bEqfKuu-Q4oiPnt3FO_72asH6EmpCe3d4KM0M6lnqgJcmt0lAxOh_qFH-6vxHw7GZiXB0IZzxdeqM7HZzwEscLSObzY02hfrB8hZ4JlKXtKl4pUUhus1jsJL7JaWmFmymyzTe54";

$message = CloudMessage::fromArray([
    'token' => $deviceToken,
    'notification' => $notification, // optional
    //'data' => [/* data array */], // optional
]);

$message = CloudMessage::fromArray([
    'topic' => "NOTIFY_CHANNEL_01",
    'notification' => $notification, // optional
    //'data' => [/* data array */], // optional
]);

// $message = CloudMessage::withTarget('topic', "NOTIFY_CHANNEL_01")
//     ->withNotification($notification);

$messaging->send($message);

// $headers = [
// 			    'Authorization: key=AAAAnnGcNmI:APA91bE3Op6Hmc_DgIHSzCd9beMrwcxjp2znjDa3qCOuJenlu-lfuZH4jcn0QNFcYkYToZaUVrN6pxNjjag-qdWNQDgfk58Jq4S5q4P1OdVOjqNR4p3Uo_7u0hKwwEn3OJHaA2blMzgP',
// 			    'Content-Type:"application/json'
// 			];

// 			$headers = array( 
// 							'Authorization: key=AAAAnnGcNmI:APA91bE3Op6Hmc_DgIHSzCd9beMrwcxjp2znjDa3qCOuJenlu-lfuZH4jcn0QNFcYkYToZaUVrN6pxNjjag-qdWNQDgfk58Jq4S5q4P1OdVOjqNR4p3Uo_7u0hKwwEn3OJHaA2blMzgP',
// 							'Content-Type: application/json'
// 						);


// 			if(count($arr['registration_ids'])>0){
// 				$sccurlobj = new SCCURL("https://fcm.googleapis.com/fcm/send");
// 				$sccurlobj->setHeaders($headers);
// 				$result = $sccurlobj->postBody($vars);
// 				if(!$result) {
// 					$error=$sccurlobj->fetchError();
// 					error_log('Curl error : fn sendAndroidNotificationByCurl class AndroidPushManager : error :' . print_r($error,1) . ' postdata : ' . print_r($postdata,1));
// 				}
// 				else{
// 					// print_r($result);
// 				}
// 				$notif->setValue('is_sent',1);
// 			}
// 			else 	$notif->setValue('is_sent',2);

//     // # Make an authenticated API request (listing storage buckets)
//     // foreach ($storage->buckets() as $bucket) {
//     //     printf('Bucket: %s' . PHP_EOL, $bucket->name());
//     // }

// // $storage = new StorageClient(['projectId'=>'udhaarplease']);
// // //$bucket = $storage->bucket("gs:things-app-d3310.appspot.com");



// $object = $bucket->object("v1/realtime_handshake/Users/11LoXch7YWMUKswwtgeLGHJ5tDQ2/Narayan Nagar_profile_img-1689749356913.jpg");
// $object->downloadToFile('/var/www/html/up/uploads/12.jpg');
?>