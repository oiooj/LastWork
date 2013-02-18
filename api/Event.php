<?php
	//Just for List Module~	

	require_once('../include/functions.php');

	
	if(isset($_POST["action"]) &&  ("new" == Str_filter($_POST['action'])) ){
		Event_New();
		exit(0);
	}
	
	if(isset($_POST["action"]) && ("del" == Str_filter($_POST['action'])) ){
		Event_Delete();
		exit(0);
	}
	
	if(isset($_POST["action"]) && ("updatecontent" == Str_filter($_POST['action'])) ){
		Event_Update_Content();
		exit(0);
	}
	
	if(isset($_POST["action"]) && ("get" == Str_filter($_POST['action'])) ){
		Event_Get();
		exit(0);
	}
	
	if(isset($_POST["action"]) && ("changedate" == Str_filter($_POST['action'])) ){
		Event_changeDate();
		exit(0);
	}

	if(isset($_POST["action"]) && ("changetime" == Str_filter($_POST['action'])) ){
		Event_changeTime();
		exit(0);
	}
	
	if(isset($_POST["action"]) && ("changeorder" == Str_filter($_POST['action'])) ){
		Event_Order();
		exit(0);
	}
	
	if(isset($_POST["action"]) && ("completed" == Str_filter($_POST['action'])) ){
		Event_Completed();
		exit(0);
	}
	
	if(isset($_POST["action"]) && ("starrted" == Str_filter($_POST['action'])) ){
		Event_Starrted();
		exit(0);
	}
	
	
	echo Return_Error(true,1000,"fuck u~");
	
	
	//For Event New
	function Event_New(){
		if(($list_id = Str_filter($_POST['list_id'])) && ($event_content = Str_filter($_POST['event_content'])) && ($token = Str_filter($_POST['token']))){
			if($username = AccessToken_Getter($token)){
				$event_id = Create_Uid($event_content);
				$event =  array("event_content" => $event_content,"event_id" => $event_id,"note_total" => 0,"event_class" => 0,"event_created_time" =>  Now(),"event_starred" =>false,"event_completed" =>false,"event_due_date" =>"","event_due_time" =>"");
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
	
	//For Event Delete
	function Event_Delete(){
		if(($event_id = Str_filter($_POST['event_id'])) && ($list_id = Str_filter($_POST['list_id'])) && ($token = Str_filter($_POST['token']))){
			if($username = AccessToken_Getter($token)){
				if(($list = Mongodb_Reader("todo_events",array("event_id" => $event_id),1)) != null){
					if(null == Mongodb_Reader("relation_event_note",array("event_id" => $event_id),1)){
						Mongodb_Remover("todo_events",array("event_id" => $event_id));
						if(Remove_relation_list_event($list_id,$event_id)){
							Remove_event_total($list_id);
							$res = Return_Error(false,0,"删除成功");
						}else{
							$res = Return_Error(true,9,"数据依赖关系不完整");
						}
					}else{
						$res = Return_Error(true,11,"事务中有数据 请先删除列表中的笔记");
					}
				}else{
					$res = Return_Error(true,14,"事务不存在");
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
	function Event_Get(){
		if(($list_id = Str_filter($_POST['list_id'])) && ($token = Str_filter($_POST['token']))){
			if($username = AccessToken_Getter($token)){
				if($list_events = Mongodb_Reader("relation_list_event",array("list_id" => $list_id),1)){
					$return_event = Array();
					$num_event = 0;
					foreach($list_events["events_id"] as$key=> $event_id){
						if($event = Mongodb_Reader("todo_events",array("event_id" => $event_id),1)){
							$return_event[$num_event]['event_id'] = $event['event_id'];
							$return_event[$num_event]['event_content'] = $event['event_content'];
							$return_event[$num_event]['note_total'] = $event['note_total'];
							$return_event[$num_event]['event_created_time'] = $event['event_created_time'];
							$return_event[$num_event]['event_starred'] = $event['event_starred'];
							$return_event[$num_event]['event_completed'] = $event['event_completed'];
							$return_event[$num_event]['event_due_date'] = $event['event_due_date'];
							$return_event[$num_event]['event_due_time'] = $event['event_due_time'];
							
							$num_event++;
						}else{
							$res = Return_Error(true,14,"事务不存在");
						}
					}
					$res = Return_Events(false,0,"获取成功",$return_event);
				}else{
					$res = Return_Error(false,0,"该列表无事务",array()); //这个不算是错误
				}
			}else{
				$res = Return_Error(true,7,"token无效或登录超时");
			}			
		}else{
			$res = Return_Error(true,4,"提交的数据为空");
		}
		echo $res;
	}
	
	//For Event Update
	function Event_Update_Content(){
		if( ($token = Str_filter($_POST['token'])) && ($event_id = Str_filter($_POST['event_id'])) && ($event_content = Str_filter($_POST['event_content']))){
			if($username = AccessToken_Getter($token)){
				if($event = Mongodb_Reader("todo_events",array("event_id" => $event_id),1)){			
						Mongodb_Updater("todo_events",array("event_id" => $event_id),array("event_content" => $event_content));
						$res = Return_Error(false,0,"修改成功");
				}else{
					$res = Return_Error(true,14,"该事务不存在");
				}
			}else{
				$res = Return_Error(true,7,"token无效或登录超时");
			}
		}else{
			$res = Return_Error(true,4,"提交的数据为空");
		}
		echo $res;
	}
	
	function Event_changeTime(){
		if( ($token = Str_filter($_POST['token'])) && ($event_id = Str_filter($_POST['event_id'])) && ($event_time = Str_filter($_POST['event_time']))){
			if($username = AccessToken_Getter($token)){
				if($event = Mongodb_Reader("todo_events",array("event_id" => $event_id),1)){			
						Mongodb_Updater("todo_events",array("event_id" => $event_id),array("event_due_time" => $event_time));
						$res = Return_Error(false,0,"修改成功");
				}else{
					$res = Return_Error(true,14,"该事务不存在");
				}
			}else{
				$res = Return_Error(true,7,"token无效或登录超时");
			}
		}else{
			$res = Return_Error(true,4,"提交的数据为空");
		}
		echo $res;
	}
	
	function Event_changeDate(){
		if( ($token = Str_filter($_POST['token'])) && ($event_id = Str_filter($_POST['event_id'])) && ($event_date = Str_filter($_POST['event_date']))){
			if($username = AccessToken_Getter($token)){
				if($event = Mongodb_Reader("todo_events",array("event_id" => $event_id),1)){			
						Mongodb_Updater("todo_events",array("event_id" => $event_id),array("event_due_date" => $event_date));
						$res = Return_Error(false,0,"修改成功");
				}else{
					$res = Return_Error(true,14,"该事务不存在");
				}
			}else{
				$res = Return_Error(true,7,"token无效或登录超时");
			}
		}else{
			$res = Return_Error(true,4,"提交的数据为空");
		}
		echo $res;
	}
	
	function Event_Completed(){
		if( ($token = Str_filter($_POST['token'])) && ($event_id = Str_filter($_POST['event_id'])) ){
			if($username = AccessToken_Getter($token)){
				if($event = Mongodb_Reader("todo_events",array("event_id" => $event_id),1)){	
						if($event['event_completed']){
							$completed = false;
						}else{
							$completed = true;
						}
						Mongodb_Updater("todo_events",array("event_id" => $event_id),array("event_completed" => $completed));
						$res = Return_Error(false,0,"修改成功");
				}else{
					$res = Return_Error(true,14,"该事务不存在");
				}
			}else{
				$res = Return_Error(true,7,"token无效或登录超时");
			}
		}else{
			$res = Return_Error(true,4,"提交的数据为空");
		}
		echo $res;
	}
	
	function Event_Starrted(){
		if( ($token = Str_filter($_POST['token'])) && ($event_id = Str_filter($_POST['event_id'])) ){
			if($username = AccessToken_Getter($token)){
				if($event = Mongodb_Reader("todo_events",array("event_id" => $event_id),1)){	
						if($event['event_starred']){
							$starrted = false;
						}else{
							$starrted = true;
						}
						Mongodb_Updater("todo_events",array("event_id" => $event_id),array("event_starred" => $starrted));
						$res = Return_Error(false,0,"修改成功");
				}else{
					$res = Return_Error(true,14,"该事务不存在");
				}
			}else{
				$res = Return_Error(true,7,"token无效或登录超时");
			}
		}else{
			$res = Return_Error(true,4,"提交的数据为空");
		}
		echo $res;
	}
	
	function Event_Order(){
		if(($new_order = Str_filter($_POST['new_order'])) && ($token = Str_filter($_POST['token'])) && ($list_id = Str_filter($_POST['list_id'])) ){
			if($username = AccessToken_Getter($token)){
				if($list_events = Mongodb_Reader("relation_list_event",array("list_id" => $list_id),1)){
					if(($events_id = change_order($new_order,$list_events["events_id"])) != false ){
						Mongodb_Updater("relation_list_event",array("list_id" => $list_id),array("events_id" => $events_id));
						$res = Return_Error(true,0,"修改成功");
					}else{
						$res = Return_Error(true,15,"事务数量有误");
					}
				}else{
					$res = Return_Error(true,1,"该用户无事务");
				}
			}else{
				$res = Return_Error(true,7,"token无效或登录超时");
			}
		}else{
			$res = Return_Error(true,4,"提交的数据为空");
		}
		echo $res;
	}
	
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
	
	function Return_Events($bool,$error_code,$message,$data = array()){
		return json_encode(array("error" => $bool,"error_code" => $error_code,"message" => $message,"events" => $data));
	}
	
?>
