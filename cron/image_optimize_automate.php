<?php
$dirname = dirname(__FILE__);
$dirname = str_replace("/cron", "",$dirname);
$running_process = shell_exec("ps auxwww|grep \"image_optimize.php\"|grep -v grep");
if(empty($running_process)) 	exec("php ".$dirname."/cron/image_optimize.php >/dev/null 2>/dev/null &");