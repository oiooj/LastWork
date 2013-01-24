<?php
	
	require_once("config.php");
	require_once( "memcached-client.php");
	
	
	///////////////////////////////////MongoDB//////////////////////////////////////////
	
	//Connect to MongoDB
	function Get_Mongodb($server="mongodb://192.168.139.100:27017"){
		return new Mongo($server); 
	}
	//Close MongoDB
	function Close_Mongodb($mogo){
		$mogo->close(); 
	}	
	
	//MongoDB Writter
	function Mongodb_Writter($collection_name,$data){
		global $SYS_CONFIG;
		$mogo = Get_Mongodb($SYS_CONFIG["DB"]["MASTER"]);
		$db = $mogo -> $SYS_CONFIG["DB"]["DATABASE"];
		$collection = $db ->$collection_name;
		$collection->insert($data);
		Close_Mongodb($mogo);
	}
	
	//MongDB Reader
	//return N records (todo)
	function Mongodb_Reader($collection_name,$condition,$type=0){
		global $SYS_CONFIG;
		try{
			$mogo = Get_Mongodb($SYS_CONFIG["DB"]["SLAVE"]);
		} catch(MongoConnectionException $e) {
			$mogo = Get_Mongodb($SYS_CONFIG["DB"]["MASTER"]);
		}
		$db = $mogo -> $SYS_CONFIG["DB"]["DATABASE"];
		$collection = $db ->$collection_name;
		if($type == 1){
			$key = Create_Key($collection_name.$condition);
			if($data = Memcached_Getter($key)){
				return $data;
			}
			$data = $collection->findOne($condition);
			Memcached_Setter($key,$data);
		}
		else if ($type == 0){
			$data = $collection->find($condition);
		}
		else{

		}
		Close_Mongodb($mogo);
		return $data;
	}
	
	//MongoDB Remover
	function Mongodb_Remover($collection_name,$condition,$type=0){
		global $SYS_CONFIG;
		$mogo = Get_Mongodb($SYS_CONFIG["DB"]["MASTER"]);
		$db = $mogo -> $SYS_CONFIG["DB"]["DATABASE"];
		$collection = $db ->$collection_name;
		if($type == 1){
			$res = $collection->remove($condition,array("justOne" => true));
		}
		else{
			$res = $collection->remove($condition);
		}
		Close_Mongodb($mogo);
		$key = Create_Key($collection_name.$condition);
		Memcached_Remover($key);
		return $res;
	}

	//MongoDB Updater
	function Mongodb_Updater($collection_name,$condition,$data){
		global $SYS_CONFIG;
		$mogo = Get_Mongodb($SYS_CONFIG["DB"]["MASTER"]);
		$db = $mogo -> $SYS_CONFIG["DB"]["DATABASE"];
		$collection = $db ->$collection_name;
		$collection->update($condition,array('$set' => $data));
		Close_Mongodb($mogo);
		$key = Create_Key($collection_name.$condition);
		Memcached_Remover($key);
	}
	
	
	
	
	///////////////////////////////////Memcached/////////////////////////////////////////
	
	//Connect to Memcached Servers
	function Get_Memcached(){
		global $SYS_CONFIG;
		if($SYS_CONFIG["USE_MEMCACHE"]){
			return new memcached($SYS_CONFIG["MEMCACHE_SERVERS"]);
		}
		return false;
	}

	//Close Memcached
	function Close_Memcached($m){
		$m->close();
	}
	
	//Memcached Setter
	function Memcached_Setter($key,$value){
		if($m = Get_Memcached()){
			$m -> set ($key,$value);
			Close_Memcached($m);
		}
	}
	
	//Memcached Getter
	function Memcached_Getter($key){
		if($m = Get_Memcached()){
			$res = $m -> get ($key);
			Close_Memcached($m);
			if($res == false || $res == 16){
				return false;
			}
			return $res;
		}
	}
	
	//Memcached Rrmover
	function Memcached_Remover($key){
		if($m = Get_Memcached()){
			$m -> delete($key);
			Close_Memcached($m);
		}
	}
	
	///////////////////////////////////Utils/////////////////////////////////////////	
	
	function Create_Key($str){
		return md5(trim($str));
	}
	
	
	
?>