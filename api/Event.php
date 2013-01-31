<?php
	//Just for List Module~	

	require_once('../include/functions.php');

	
	if(isset($_POST["action"]) &&  ("new" == Str_filter($_POST['action'])) ){
		Event_New();
		exit(0);
	}
	
	// if(isset($_POST["action"]) && ("del" == Str_filter($_POST['action'])) ){
		// List_Delete();
		// exit(0);
	// }
	
	// if(isset($_POST["action"]) && ("changename" == Str_filter($_POST['action'])) ){
		// List_Changename();
		// exit(0);
	// }
	
	// if(isset($_POST["action"]) && ("get" == Str_filter($_POST['action'])) ){
		// List_Get();
		// exit(0);
	// }
	
	// if(isset($_POST["action"]) && ("share" == Str_filter($_POST['action'])) ){
		// List_Share();
		// exit(0);
	// }
	
	// if(isset($_POST["action"]) && ("changeorder" == Str_filter($_POST['action'])) ){
		// List_Order();
		// exit(0);
	// }

	echo Return_Error(true,1000,"fuck u~");
	
	
	//For Event New
	function Event_New(){
		if(($list_id = Str_filter($_POST['list_id'])) && ($event_content = Str_filter($_POST['event_content'])) && ($token = Str_filter($_POST['token']))){
			if($username = AccessToken_Getter($token)){
				$event_id = Create_Uid($event_content);
				$event =  array("event_content" => $event_content,"event_id" => $event_id,"note_total" => 0,"event_class" => 0,"event_created_time" =>  Now(),"event_starred" =>0,"event_completed" =>0,"event_due_date" =>"","event_due_time" =>"");
				Add_relation_list_event($list_id,$event_id);
				try{
					Mongodb_Writter("todo_events",$event);
					Add_event_total($list_id);
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
	// function List_Delete(){
		// if(($list_id = Str_filter($_POST['list_id'])) && ($token = Str_filter($_POST['token']))){
			// if($username = AccessToken_Getter($token)){
				// if(($list = Mongodb_Reader("todo_lists",array("list_id" => $list_id),1)) != null){
					// if($list["list_class"] == 0){
						// if(null == Mongodb_Reader("relation_list_event",array("list_id" => $list_id),1)){
							// Mongodb_Remover("todo_lists",array("list_id" => $list_id));
							// if(Remove_relation_user_list($username,$list_id)){
								// $res = Return_Error(false,0,"删除成功");
							// }else{
								// $res = Return_Error(true,9,"数据依赖关系不完整");
							// }
						// }else{
							// $res = Return_Error(true,11,"列表有数据 请先删除列表中的事务");
						// }
					// }
					// if($list["list_class"] == 1){
						// if(($list_users = Mongodb_Reader("share_list_user",array("list_id" => $list_id),1)) != null){
							// if(count($list_users["users_id"])  <= 1){
								// Mongodb_Remover("share_list_user",array("list_id" => $list_id));
								// Mongodb_Remover("todo_lists",array("list_id" => $list_id));
							// }else{
								// Remove_share_list_user($username,$list_id);
							// }
							// if(Remove_relation_user_list($username,$list_id)){
								// $res = Return_Error(false,0,"删除成功");
							// }else{
								// $res = Return_Error(true,9,"数据依赖关系不完整");
							// }
						// }else{
							// $res = Return_Error(true,11,"列表有数据 请先删除列表中的事务");
						// }
					// }										
				// }else{
					// $res = Return_Error(true,10,"列表不存在");
				// }
			// }else{
				// $res = Return_Error(true,7,"token无效或登录超时");
			// }
		// }else{
			// $res = Return_Error(true,4,"提交的数据为空");
		// }
		// echo $res;
	// }
	
    //For Get Lists
	// function List_Get(){
		// if($token = Str_filter($_POST['token'])){
			// if($username = AccessToken_Getter($token)){
				// if($user_lists = Mongodb_Reader("relation_user_list",array("user_id" => md5($username)),1)){
					// $return_list = Array();
					// $num_list = 0;
					// foreach($user_lists["lists_id"] as$key=> $list_id){
						// if($list = Mongodb_Reader("todo_lists",array("list_id" => $list_id),1)){
							// $return_list[$num_list]['list_id'] = $list['list_id'];
							// $return_list[$num_list]['list_name'] = $list['list_name'];
							// $return_list[$num_list]['event_total'] = $list['event_total'];
							// if($list['list_class'] == 1){
								// $return_list[$num_list]['shared'] = true;
								// if(($list_users = Mongodb_Reader("share_list_user",array("list_id" => $list_id),1)) != null){
									// $return_list[$num_list]['shared_users'] = $list_users['users_id'];
								// }						
							// }else{
								// $return_list[$num_list]['shared'] = false;
							// }
							
							// $num_list++;
						// }else{
							// $res = Return_Error(true,10,"列表不存在");
						// }
					// }
					// $res = Return_Lists(false,0,"获取成功",$return_list);
				// }else{
					// $res = Return_Error(false,0,"该用户无列表",array()); //这个不算是错误
				// }
			// }else{
				// $res = Return_Error(true,7,"token无效或登录超时");
			// }			
		// }else{
			// $res = Return_Error(true,4,"提交的数据为空");
		// }
		// echo $res;
	// }
	
	//For List	Change Name
	// function List_Changename(){
		// if( ($token = Str_filter($_POST['token'])) && ($list_name_new = Str_filter($_POST['list_name_new'])) && ($list_id = Str_filter($_POST['list_id'])) ){
			// if($username = AccessToken_Getter($token)){
				// if($list = Mongodb_Reader("todo_lists",array("list_id" => $list_id),1)){				
						// Mongodb_Updater("todo_lists",array("list_id" => $list_id),array("list_name" => $list_name_new));
						// $res = Return_Error(false,0,"修改成功");
				// }else{
					// $res = Return_Error(true,10,"该列表不存在");
				// }
			// }else{
				// $res = Return_Error(true,7,"token无效或登录超时");
			// }
		// }else{
			// $res = Return_Error(true,4,"提交的数据为空");
		// }
		// echo $res;
	// }
	
	// function List_Share(){
		// if( ($token = Str_filter($_POST['token'])) && ($to_username = Str_filter($_POST['to_username'])) && ($list_id = Str_filter($_POST['list_id'])) ){
			// if($username = AccessToken_Getter($token)){
				// if($username != $to_username){
					// if($list = Mongodb_Reader("todo_lists",array("list_id" => $list_id),1)){				
							// Mongodb_Updater("todo_lists",array("list_id" => $list_id),array("list_class" => 1));
							// Add_relation_user_list($to_username,$list_id);
							// Add_share_list_user($username,$list_id);
							// Add_share_list_user($to_username,$list_id);
							// $res = Return_Error(false,0,"共享成功");
					// }else{
						// $res = Return_Error(true,10,"该列表不存在");
					// }
				// }else{
					// $res = Return_Error(true,12,"列表不能共享给自己");
				// }
			// }else{
				// $res = Return_Error(true,7,"token无效或登录超时");
			// }
		// }else{
			// $res = Return_Error(true,4,"提交的数据为空");
		// }
		// echo $res;
	// }
	
	// function List_Order(){
		// if(($new_order = Str_filter($_POST['new_order'])) && ($token = Str_filter($_POST['token']))){
			// if($username = AccessToken_Getter($token)){
				// if($user_lists = Mongodb_Reader("relation_user_list",array("user_id" => md5($username)),1)){
					// if(($lists_id = change_order($new_order,$user_lists["lists_id"])) != false ){
						// Mongodb_Updater("relation_user_list",array("user_id" => md5($username)),array("lists_id" => $lists_id));
						// $res = Return_Error(true,0,"修改成功");
					// }else{
						// $res = Return_Error(true,13,"列表数量有误");
					// }
				// }else{
					// $res = Return_Error(true,1,"该用户无列表");
				// }
			// }else{
				// $res = Return_Error(true,7,"token无效或登录超时");
			// }
		// }else{
			// $res = Return_Error(true,4,"提交的数据为空");
		// }
		// echo $res;
	// }
	
	function Add_relation_list_event($list_id,$event_id){
		if($list_events = Mongodb_Reader("relation_list_event",array("list_id" => $list_id),1)){
			$events_id = $list_events["events_id"];
			$events_index = $list_events["events_index"] + 1;
			array_push($events_id,$event_id);
			Mongodb_Updater("relation_list_event",array("list_id" => $list_id),array("events_id" => $events_id,"events_index" => $events_index));
		}else{
			$list_events_new = array("list_id" => $list_id,"events_id" => array("1" => $event_id),"events_index" =>0);
			Mongodb_Writter("relation_list_event",$list_events_new);
		}
	}
	
	function Remove_relation_list_event($list_id,$event_id){
		if($list_events = Mongodb_Reader("relation_list_event",array("list_id" => $list_id),1)){
			$events_id = $list_events["events_id"];
			if(count($events_id)  <= 1){
				Mongodb_Remover("relation_list_event",array("list_id" => $list_id));
				return true;
			}else{
				$events_index = $list_events["events_index"] - 1;
				$events_id = Delete_From_Array($events_id,$event_id);
				Mongodb_Updater("relation_list_event",array("list_id" => $list_id),array("events_id" => $events_id,"events_index" => $events_index));
				return true;
			}
		}
		else{
			return false;
		}	
	}
	
	function Add_event_total($list_id){
		if($list = Mongodb_Reader("todo_lists",array("list_id" => $list_id),1)){
			$total = $list["event_total"] + 1;
			Mongodb_Updater("todo_lists",array("list_id" => $list_id),array("event_total" => $total));
		}
	}
	
	function Remove_event_total($list_id){
		if($list = Mongodb_Reader("todo_lists",array("list_id" => $list_id),1)){
			$total = $list["event_total"] - 1;
			Mongodb_Updater("todo_lists",array("list_id" => $list_id),array("event_total" => $total));
		}
	}
	
?>
