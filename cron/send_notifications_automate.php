<?php
$dirname = dirname(__FILE__);
$dirname = str_replace("/cron", "",$dirname);
$running_process = shell_exec("ps auxwww|grep \"send_notification.php\"|grep -v grep");
if(empty($running_process)) 	exec("php ".$dirname."/cron/send_notification.php >/dev/null 2>/dev/null &");