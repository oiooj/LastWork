<?php
	//Just for List Module~	

	require_once('../include/functions.php');

	// if( isset($_GET["action"]) &&  ("logout" == Str_filter($_GET['action'])) ){
	// User_Logout();
	// exit(0);
	// }
	
	if(isset($_POST["action"]) &&  ("new" == Str_filter($_POST['action'])) ){
		List_New();
		exit(0);
	}
	
	if(isset($_POST["action"]) && ("del" == Str_filter($_POST['action'])) ){
		List_Delete();
		exit(0);
	}
	
	// if(isset($_POST["action"]) && ("changepass" == Str_filter($_POST['action'])) ){
		// User_Changepass();
	// }
	
	echo Return_Error(true,1000,"fuck u~");
	
	//For List New
	function List_New(){
		if(($list_name = Str_filter($_POST['list_name'])) && ($token = Str_filter($_POST['token']))){
			if($username = AccessToken_Getter($token)){
				$list_id = Create_Uid($list_name);
				$list =  array("list_name" => $list_name,"list_id" => $list_id,"list_total" => 0,"list_class" => 0,"list_created_time" =>  Now(),"last_datetime" => Now());
				if($user_lists = Mongodb_Reader("relation_user_list",array("user_id" => md5($username)),1)){
					$lists_id = $user_lists["lists_id"];
					$lists_index = $user_lists["lists_index"] + 1;
					array_push($lists_id,$list_id);
					Mongodb_Updater("relation_user_list",array("user_id" => md5($username)),array("lists_id" => $lists_id,"lists_index" => $lists_index));
				}else{
					$user_lists_new = array("user_id" => md5($username),"lists_id" => array($list_id),"lists_index" =>0);
					Mongodb_Writter("relation_user_list",$user_lists_new);
				}
				try{
					Mongodb_Writter("todo_lists",$list);
					$res = Return_Error(false,0,"创建成功");
				} catch(MongoException $e) {
					$res = Return_Error(true,2,"创建失败");
				}
			}else{
				$res = Return_Error(true,7,"token无效或登录超时");
			}
		}else{
			$res = Return_Error(true,4,"提交的数据为空");
		}
		echo $res;
	}
	
	//For Delete List
	function List_Delete(){
		if(($list_id = Str_filter($_POST['list_id'])) && ($token = Str_filter($_POST['token']))){
			if($username = AccessToken_Getter($token)){
				if(null != Mongodb_Reader("todo_lists",array("list_id" => $list_id),1)){
					if(null == Mongodb_Reader("relation_list_event",array("list_id" => $list_id),1)){
						Mongodb_Remover("todo_lists",array("list_id" => $list_id));
						if($user_lists = Mongodb_Reader("relation_user_list",array("user_id" => md5($username)),1)){
							$lists_id = $user_lists["lists_id"];
							$lists_index = $user_lists["lists_index"] - 1;
							$lists_id = Delete_From_Array($lists_id,$list_id);
							Mongodb_Updater("relation_user_list",array("user_id" => md5($username)),array("lists_id" => $lists_id,"lists_index" => $lists_index));
							$res = Return_Error(false,0,"删除成功");
						}
						else{
							$res = Return_Error(true,9,"数据依赖关系不完整");
						}
					}else{
						$res = Return_Error(true,11,"列表有数据 请先删除列表中的事务");
					}
				}else{
					$res = Return_Error(true,10,"列表不存在");
				}
			}else{
				$res = Return_Error(true,7,"token无效或登录超时");
			}
		}else{
			$res = Return_Error(true,4,"提交的数据为空");
		}
		echo $res;
	}
	
	//For User logon
	// function User_logon(){
		// if(($username = Str_filter($_POST['username'])) && ($password = Str_filter($_POST['password'])) ){
			// if($user = Mongodb_Reader("todo_users",array("username" => $username),1)){
				// if(md5($password) == $user['password']){
					// $accesstoken = AccessToken_Setter($username);
					// Mongodb_Updater("todo_users",array("username" => $username),array("last_datetime" =>  Now()));
					// $res = Return_Error(false,0,"登陆成功",array("token" => $accesstoken));
				// }else{
					// $res = Return_Error(true,6,"密码不正确");
				// }
			// }else{
				// $res = Return_Error(true,5,"该用户不存在");
			// }
		// }else{
			// $res = Return_Error(true,4,"提交的数据为空");
		// }
		// echo $res;
	// }
	
	//For User logout
	// function User_logout(){
		// if($token = Str_filter($_GET['token'])){
			// $res = AccessToken_Remover($token);
			// if($res == 16){
				// $res = Return_Error(true,8,"注销失败");
			// }
			// if($res == true){
				// $res = Return_Error(false,0,"注销成功");
			// }
			// if($res == false){
				// $res = Return_Error(true,7,"token无效或登录超时");
			// }
		// }else{
			// $res = Return_Error(true,4,"提交的数据为空");
		// }
		// echo $res;
	// }
	
	//For User Change Password
	// function User_Changepass(){
		// if( ($token = Str_filter($_POST['token'])) && ($old_password = Str_filter($_POST['old_password'])) && ($new_password = Str_filter($_POST['new_password'])) ){
			// if($username = AccessToken_Getter($token)){
				// if($user = Mongodb_Reader("todo_users",array("username" => $username),1)){				
					// if(md5($old_password) == $user['password']){
						// Mongodb_Updater("todo_users",array("username" => $username),array("password" => md5($new_password)));
						// $res = Return_Error(false,0,"修改成功");
					// }else{
						// $res = Return_Error(true,6,"密码不正确");
					// }
				// }else{
					// $res = Return_Error(true,5,"该用户不存在");
				// }
			// }else{
				// $res = Return_Error(true,7,"token无效或登录超时");
			// }
		// }else{
			// $res = Return_Error(true,4,"提交的数据为空");
		// }
		// echo $res;
	// }
	
?>
