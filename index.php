<?php
	//Just for test here~


	require_once(dirname(__FILE__) .'/include/functions.php');

	// $data = array("name" => "li","email" => "baidu2@qq.com","age" => 23);
	// Mongodb_Writter("foo",$data);

	$res = Mongodb_Reader("foo",array("name" => "li"),1);
	echo $res['name'];
	//Mongodb_Updater("foo",array("name" => "last"),array("name" => "poji"));
	echo "<br/><br/><br/>";
	var_dump( Memcached_Getter(Create_Key("foo".array("name" => "last"))));


?>
