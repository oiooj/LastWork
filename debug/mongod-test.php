<?php
	//Just for test here~


	require_once('../include/functions.php');

	// $data = array("name" => "li","email" => "baidu2@qq.com","age" => 23);
	// Mongodb_Writter("foo",$data);

	//echo AccessToken_Getter("943520D48D28580E55DC7475F087FD0A");
	
	$res = Mongodb_Reader("todo_users",array("username" => "oiooj@qq.com"),1);
	echo $res['password'];
	//Mongodb_Updater("foo",array("name" => "last"),array("name" => "poji"));
	// echo "<br/><br/><br/>";
	// var_dump( Memcached_Getter(Create_Key("foo".array("name" => "last"))));


?>
