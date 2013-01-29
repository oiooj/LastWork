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
	
	if(isset($_POST["action"]) && ("changename" == Str_filter($_POST['action'])) ){
		List_Changename();
		exit(0);
	}
	
	if(isset($_POST["action"]) && ("get" == Str_filter($_POST['action'])) ){
		List_Get();
		exit(0);
	}
	
	echo Return_Error(true,1000,"fuck u~");
	
	//For List New
	function List_New(){
		if(($list_name = Str_filter($_POST['list_name'])) && ($token = Str_filter($_POST['token']))){
			if($username = AccessToken_Getter($token)){
				$list_id = Create_Uid($list_name);
				$list =  array("list_name" => $list_name,"list_id" => $list_id,"event_total" => 0,"list_class" => 0,"list_created_time" =>  Now(),"last_datetime" => Now());
				if($user_lists = Mongodb_Reader("relation_user_list",array("user_id" => md5($username)),1)){
					$lists_id = $user_lists["lists_id"];
					$lists_index = $user_lists["lists_index"] + 1;
					array_push($lists_id,$list_id);
					Mongodb_Updater("relation_user_list",array("user_id" => md5($username)),array("lists_id" => $lists_id,"lists_index" => $lists_index));
				}else{
					$user_lists_new = array("user_id" => md5($username),"lists_id" => array("1" => $list_id),"lists_index" =>0);
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
	
    //For Get Lists
	function List_Get(){
		if($token = Str_filter($_POST['token'])){
			if($username = AccessToken_Getter($token)){
				if($user_lists = Mongodb_Reader("relation_user_list",array("user_id" => md5($username)),1)){
					$return_list = Array();
					$num_list = 0;
					foreach($user_lists["lists_id"] as$key=> $list_id){
						if($list = Mongodb_Reader("todo_lists",array("list_id" => $list_id),1)){
							$return_list[$num_list]['list_id'] = $list['list_id'];
							$return_list[$num_list]['list_name'] = $list['list_name'];
							$return_list[$num_list]['event_total'] = $list['event_total'];
							$num_list++;
						}else{
							$res = Return_Error(true,10,"列表不存在");
						}
					}
					$res = Return_Lists(false,0,"获取成功",$return_list);
				}else{
					$res = Return_Error(false,0,"该用户无列表",array()); //这个不算是错误
				}
			}else{
				$res = Return_Error(true,7,"token无效或登录超时");
			}			
		}else{
			$res = Return_Error(true,4,"提交的数据为空");
		}
		echo $res;
	}
	
	//For List	Change Name
	function List_Changename(){
		if( ($token = Str_filter($_POST['token'])) && ($list_name_new = Str_filter($_POST['list_name_new'])) && ($list_id = Str_filter($_POST['list_id'])) ){
			if($username = AccessToken_Getter($token)){
				if($list = Mongodb_Reader("todo_lists",array("list_id" => $list_id),1)){				
						Mongodb_Updater("todo_lists",array("list_id" => $list_id),array("list_name" => $list_name_new));
						$res = Return_Error(false,0,"修改成功");
				}else{
					$res = Return_Error(true,10,"该列表不存在");
				}
			}else{
				$res = Return_Error(true,7,"token无效或登录超时");
			}
		}else{
			$res = Return_Error(true,4,"提交的数据为空");
		}
		echo $res;
	}
	
?>
