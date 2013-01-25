<?php
error_reporting(E_ALL);

$SYS_CONFIG["DB"]["TYPE"]       = "mongodb";		  
$SYS_CONFIG["DB"]["MASTER"]     = "mongodb://192.168.139.100:27017";  
$SYS_CONFIG["DB"]["SLAVE"]      = "mongodb://192.168.139.101:27017"; 
$SYS_CONFIG["DB"]["DATABASE"]   = "Web_Design_Todo";
$SYS_CONFIG["DB"]["CHARSET"]    = "utf8";

$SYS_CONFIG["FLASH_EXTENSIONS"]	= "swf";
$SYS_CONFIG["IMAGE_EXTENSIONS"]	= "gif|jpg|jpeg|png";
$SYS_CONFIG["FILES_EXTENSIONS"]	= "txt|zip|tar|rar|chm|htm|html|doc|xml|xls|ppt|bmp";
$SYS_CONFIG["OS"] 				= "CentOS"; 

$SYS_CONFIG["USE_MEMCACHE"]        = false;
$SYS_CONFIG["ACCESSTOKEN_SERVERS"] = array(  'servers' => array('192.168.139.103:11211'),'debug'   => false,'compress_threshold' => 10240,'persistant' => true);
$SYS_CONFIG["MEMCACHE_SERVERS"]    = array(  'servers' => array('192.168.139.102:11211'),'debug'   => false,'compress_threshold' => 10240,'persistant' => true);

$SYS_CONFIG["EMAIL"]["HOST"]       = "smtp.126.com";
$SYS_CONFIG["EMAIL"]["USER"]       = "nototon@126.com";
$SYS_CONFIG["EMAIL"]["PASS"]       = "wwwww";
$SYS_CONFIG["EMAIL"]["TEAM"]       = "TEAM S13";

/***************************************
if (!isset($SYS_CONFIG['NO_SESSION'])) {
	session_start();
}
**************************************/

$SYS_CONFIG["CONFIG_PATH"] = dirname(__FILE__);

//header("Content-type: text/html; charset=utf-8"); 


?>