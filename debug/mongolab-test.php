<?php
	//Just for test here~

//这里采用默认连接本机的27017端口，当然你也可以连接远程主机如192.168.0.4:27017,如果端口是27017，端口可以省略

$m = new Mongo("mongodb://oiooj:123@ds043977.mongolab.com:43977/web");

// 选择comedy数据库，如果以前没该数据库会自动创建，也可以用$m->selectDB("comedy");

$db = $m->web;

//选择comedy里面的collection集合，相当于RDBMS里面的表，也-可以使用

$collection = $db->collection;

$db->selectCollection("foo");

//添加一个元素

$obj = array( "title" => "Calvin and Hobbes-".date('i:s'), "author" => "Bill Watterson" );

//将$obj 添加到$collection 集合中

$collection->insert($obj);

//添加另一个元素

$obj = array( "title" => "XKCD-".date('i:s'), "online" => true );

$collection->insert($obj);

//查询所有的记录

$cursor = $collection->find();

//遍历所有集合中的文档

foreach ($cursor as $obj)

{

echo $obj["title"] . "\n";

}

// 删除所有数据

// $collection->remove();

// 删除 name 为hm

// $collection->remove(array('name'=>'hm'));

//断开MongoDB连接

$m->close();

?>

?>
