<?php
	//Just for User Module~	
	//session_start();
	require_once('../include/functions.php');

	if( isset($_GET["action"]) &&  ("logout" == Str_filter($_GET['action'])) ){
	User_Logout();
	exit(0);
	}
	
	if(isset($_POST["action"]) &&  ("signup" == Str_filter($_POST['action'])) ){
		User_Signup();
	}
	
	if(isset($_POST["action"]) && ("logon" == Str_filter($_POST['action'])) ){
		User_Logon();
	}
	
	
	//For User signup
	function User_Signup(){
		if(($username = Str_filter($_POST['username'])) && ($password = Str_filter($_POST['password']))){
			if(null == Mongodb_Reader("todo_users",array("username" => $username),1)){
				$user =  array("username" => $username,"password" => md5($password),"user_id" => md5($username),"user_class" => 0,"signup_datetime" =>  date("Y-m-d h:m:s"),"last_datetime" => "");
				try{
					Mongodb_Writter("todo_users",$user);
					$res = Return_Error(false,0,"注册成功");
				} catch(MongoException $e) {
					$res = Return_Error(true,2,"注册失败");
				}
			}else{
				$res = Return_Error(true,3,"用户名已经占用");
			}
		}else{
			$res = Return_Error(true,4,"提交的数据为空");
		}
		echo $res;
	}
	
	//For User logon
	function User_logon(){
		if(($username = Str_filter($_POST['username'])) && ($password = Str_filter($_POST['password'])) ){
			if($user = Mongodb_Reader("todo_users",array("username" => $username),1)){
				if(md5($password) == $user['password']){
					$accesstoken = AccessToken_Setter($username);
					$res = Return_Error(false,0,"登陆成功",array("token" => $accesstoken));
				}else{
					$res = Return_Error(true,6,"密码不正确");
				}
			}else{
				$res = Return_Error(true,5,"该用户不存在");
			}
		}else{
			$res = Return_Error(true,4,"提交的数据为空");
		}
		echo $res;
	}
	
	//For User logout
	function User_logout(){
		if($token = Str_filter($_GET['token'])){
			$res = AccessToken_Remover($token);
			if($res == 16){
				$res = Return_Error(true,8,"注销失败");
			}
			if($res == true){
				$res = Return_Error(false,0,"注销成功");
			}
			if($res == false){
				$res = Return_Error(true,7,"token无效或登录超时");
			}
		}else{
			$res = Return_Error(true,4,"提交的数据为空");
		}
		echo $res;
	}
	
	
	// $data = array("name" => "li","email" => "baidu2@qq.com","age" => 23);
	// Mongodb_Writter("foo",$data);

	// $res = Mongodb_Reader("foo",array("name" => "li"),1);
	// echo $res['name'];
	// Mongodb_Updater("foo",array("name" => "last"),array("name" => "poji"));
	// echo "<br/><br/><br/>";
	// var_dump( Memcached_Getter(Create_Key("foo".array("name" => "last"))));


?>
