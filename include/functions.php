<?php
	
	require_once("config.php");
	require_once("memcached-client.php");
	require_once("phpmailer/class.phpmailer.php");
	
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
				Cache_Timemap(true);
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
		Cache_Timemap(false);
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
	function Get_Memcached($server = "DATA_SERVER"){
		global $SYS_CONFIG;
		if($server == "DATA_SERVER"){
			if($SYS_CONFIG["USE_MEMCACHE"]){
				return new memcached($SYS_CONFIG["MEMCACHE_SERVERS"]);
			}
		}
		if($server == "AccessToken_SERVER"){
			return new memcached($SYS_CONFIG["ACCESSTOKEN_SERVERS"]);
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


	
	///////////////////////////////////Memcached AccessToken/////////////////////////
	
	function Create_AccessToken($username){
		return strtoupper(md5(uniqid(rand(-2147483640, 2147483640), true).time().microtime().$username));
	}
	
	function AccessToken_Setter($username,$accesstoken = null){	
		global $SYS_CONFIG;	
		if($m = Get_Memcached("AccessToken_SERVER")){
			if(null == $accesstoken){
				$accesstoken = Create_AccessToken($username);
			}
			$m -> set ($accesstoken,$username,$SYS_CONFIG["ACCESS_TOKEN_TIME"]); //设置过期时间
			Close_Memcached($m);
			return $accesstoken;
		}
	}
	
	/*
	function AccessToken_Updater($accesstoken,$username){	
		global $SYS_CONFIG;
		if($m = Get_Memcached("AccessToken_SERVER")){
			$m -> set ($accesstoken,$username,$SYS_CONFIG["ACCESS_TOKEN_TIME"]); 
			Close_Memcached($m);
		}
	}
	*/

	function AccessToken_Getter($accesstoken){		
		if($m = Get_Memcached("AccessToken_SERVER")){
			$res = $m -> get ($accesstoken);
			Close_Memcached($m);
			if($res == false || $res == 16){
				return false;
			}else{
				AccessToken_Setter($res,$accesstoken);
			}
			return $res;
		}
	}

	function AccessToken_Remover($accesstoken){
		if($m = Get_Memcached("AccessToken_SERVER")){
			$res = $m -> delete($accesstoken);
			Close_Memcached($m);
			return $res;
		}else{
			return false;
		}
	}
	
	
	
	///////////////////////////////////Time Utils////////////////////////////////////

	function Microtime_Float(){
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
	
	function GetExecuteTime($time_start) {
		return round((Microtime_Float() - $time_start) * 1000);
	}
	
	function Cache_Timemap($is_cached){
		global $SYS_CONFIG;
		if($is_cached){
			$status = "<!-- 已缓存，耗时：".GetExecuteTime($SYS_CONFIG["TIME"]["START"])." 毫秒，更新时间：".Now().", IP:".$_SERVER['REMOTE_ADDR']."-->";
		}else{
			$status = "<!-- 未缓存，耗时：".GetExecuteTime($SYS_CONFIG["TIME"]["START"])." 毫秒，更新时间：".Now().", IP:".$_SERVER['REMOTE_ADDR']."-->";
		}
		echo $status;
	}
	
	function Now(){
		return date("Y-m-d H:i:s");
	}
	
	///////////////////////////////////Utils/////////////////////////////////////////	
	
	//Create Key
	function Create_Key($str){
		return md5(trim($str));
	}
	
	//String Filter
	function Str_filter($str){
		$str = trim($str);
		if($str == ""){
			return false;
		}
		return $str;
	}
	
	//Return json error result
	function Return_Error($bool,$error_code,$message,$data = array()){
		return json_encode(array_merge(array("error" => $bool,"error_code" => $error_code,"message" => $message),$data));
	}
	
	function Create_Uid($username){
		return strtoupper(md5(uniqid(rand(-2147483640, 2147483640), true).time().microtime().$username));
	}
	
	//Send Email
	function Email_Sender($email,$title,$content,$bak_content = ""){
		global $SYS_CONFIG;
		$mail = new PHPMailer();
		$mail->IsSMTP();                                                                        // 启用SMTP
		$mail->Host          =     $SYS_CONFIG["EMAIL"]["HOST"];			                    // SMTP服务器
		$mail->SMTPAuth      =     true;					                                    // 开启SMTP认证
		$mail->Username      =     $SYS_CONFIG["EMAIL"]["USER"];			                    // SMTP用户名
		$mail->Password      =     $SYS_CONFIG["EMAIL"]["PASS"];	                            // SMTP密码
		$mail->From          =     $SYS_CONFIG["EMAIL"]["USER"];		                        // 发件人地址
		$mail->FromName      =     $SYS_CONFIG["EMAIL"]["TEAM"];				                // 发件人
		$mail->AddAddress($email);                                                              // 添加收件人
		$mail->AddReplyTo($SYS_CONFIG["EMAIL"]["USER"], "RE:".$SYS_CONFIG["EMAIL"]["TEAM"]);	// 回复地址
		$mail->WordWrap = 50;					                                                // 设置每行字符长度
		/** 附件设置
		$mail->AddAttachment("/var/tmp/file.tar.gz");		                                    // 添加附件
		$mail->AddAttachment("/tmp/image.jpg", "new.jpg");                                  	// 添加附件,并指定名称
		*/
		$mail->IsHTML(true);					                                                // 是否HTML格式邮件
		$mail->Subject = $title;			                                                    // 邮件主题
		$mail->Body    = $content;		                                                        // 邮件内容
		$mail->AltBody = $bak_content;	                                                        // 邮件正文不支持HTML的备用显示

		if(!$mail->Send())
		{
		   // echo "Message could not be sent. <p>";
		   // echo "Mailer Error: " . $mail->ErrorInfo;
		   return false;		   
		}
		else
		{
			// echo "Message has been sent";
			return true;
		}
	}

?>