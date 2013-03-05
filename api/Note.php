 <?php
	// Just for Note Module~	

	require_once('../include/functions.php');

	
	if(isset($_POST["action"]) &&  ("new" == Str_filter($_POST['action'])) ){
		Note_New();
		exit(0);
	}
	
	// if(isset($_POST["action"]) && ("del" == Str_filter($_POST['action'])) ){
		// Note_Delete();
		// exit(0);
	// }
	
	// if(isset($_POST["action"]) && ("updatecontent" == Str_filter($_POST['action'])) ){
		// Note_Update_Content();
		// exit(0);
	// }
	
	if(isset($_POST["action"]) && ("gettitles" == Str_filter($_POST['action'])) ){
		Note_GetTitles();
		exit(0);
	}
	
	// if(isset($_POST["action"]) && ("changedate" == Str_filter($_POST['action'])) ){
		// Note_changeDate();
		// exit(0);
	// }

	// if(isset($_POST["action"]) && ("changetime" == Str_filter($_POST['action'])) ){
		// Note_changeTime();
		// exit(0);
	// }
	
	// if(isset($_POST["action"]) && ("changeorder" == Str_filter($_POST['action'])) ){
		// Note_Order();
		// exit(0);
	// }
	
	// if(isset($_POST["action"]) && ("completed" == Str_filter($_POST['action'])) ){
		// Note_Completed();
		// exit(0);
	// }
	
	// if(isset($_POST["action"]) && ("starrted" == Str_filter($_POST['action'])) ){
		// Note_Starrted();
		// exit(0);
	// }
	
	
	 echo Return_Error(true,1000,"fuck u~");
	
	
	//For Note New
	function Note_New(){
		if(($event_id = Str_filter($_POST['event_id'])) && ($note_content = Str_filter($_POST['note_content'])) && ($note_title = Str_filter($_POST['note_title'])) && ($token = Str_filter($_POST['token']))){
			if($username = AccessToken_Getter($token)){
				$note_id = Create_Uid($note_title);
				$note =  array("note_content" => $note_content,"note_title" => $note_title,"note_id" => $note_id,"note_class" => 0,"note_created_time" =>  Now(),"note_starred" =>false,"note_completed" =>false);
				Add_relation_event_note($event_id,$note_id);
				try{
					Mongodb_Writter("todo_notes",$note);
					Add_note_total($event_id);
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
	
	//For Note Delete
	// function Note_Delete(){
		// if(($event_id = Str_filter($_POST['event_id'])) && ($list_id = Str_filter($_POST['list_id'])) && ($token = Str_filter($_POST['token']))){
			// if($username = AccessToken_Getter($token)){
				// if(($list = Mongodb_Reader("todo_events",array("event_id" => $event_id),1)) != null){
					// if(null == Mongodb_Reader("relation_event_note",array("event_id" => $event_id),1)){
						// Mongodb_Remover("todo_events",array("event_id" => $event_id));
						// if(Remove_relation_list_event($list_id,$event_id)){
							// Remove_event_total($list_id);
							// $res = Return_Error(false,0,"删除成功");
						// }else{
							// $res = Return_Error(true,9,"数据依赖关系不完整");
						// }
					// }else{
						// $res = Return_Error(true,11,"事务中有数据 请先删除列表中的笔记");
					// }
				// }else{
					// $res = Return_Error(true,14,"事务不存在");
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
	function Note_GetTitles(){
		if(($event_id = Str_filter($_POST['event_id'])) && ($token = Str_filter($_POST['token']))){
			if($username = AccessToken_Getter($token)){
				if($event_notes = Mongodb_Reader("relation_event_note",array("event_id" => $event_id),1)){
					$return_note = Array();
					$num_event = 0;
					foreach($event_notes["notes_id"] as$key=> $note_id){
						if($event = Mongodb_Reader("todo_notes",array("note_id" => $note_id),1)){
							$return_note[$num_event]['note_id'] = $event['note_id'];
							$return_note[$num_event]['note_content'] = $event['note_content'];
							$return_note[$num_event]['note_title'] = $event['note_title'];
							$return_note[$num_event]['note_created_time'] = $event['note_created_time'];
							// $return_note[$num_event]['event_starred'] = $event['event_starred'];
							// $return_note[$num_event]['event_completed'] = $event['event_completed'];
							// $return_note[$num_event]['event_due_date'] = $event['event_due_date'];
							// $return_note[$num_event]['event_due_time'] = $event['event_due_time'];
							
							$num_event++;
						}else{
							$res = Return_Error(true,16,"笔记不存在");
						}
					}
					$res = Return_Notes_Title(false,0,"获取成功",$return_note);
				}else{
					$res = Return_Error(false,0,"该事务无笔记",array()); //这个不算是错误
				}
			}else{
				$res = Return_Error(true,7,"token无效或登录超时");
			}			
		}else{
			$res = Return_Error(true,4,"提交的数据为空");
		}
		echo $res;
	}
	
	//For Note Update
	// function Note_Update_Content(){
		// if( ($token = Str_filter($_POST['token'])) && ($event_id = Str_filter($_POST['event_id'])) && ($event_content = Str_filter($_POST['event_content']))){
			// if($username = AccessToken_Getter($token)){
				// if($event = Mongodb_Reader("todo_events",array("event_id" => $event_id),1)){			
						// Mongodb_Updater("todo_events",array("event_id" => $event_id),array("event_content" => $event_content));
						// $res = Return_Error(false,0,"修改成功");
				// }else{
					// $res = Return_Error(true,14,"该事务不存在");
				// }
			// }else{
				// $res = Return_Error(true,7,"token无效或登录超时");
			// }
		// }else{
			// $res = Return_Error(true,4,"提交的数据为空");
		// }
		// echo $res;
	// }
	
	// function Note_changeTime(){
		// if( ($token = Str_filter($_POST['token'])) && ($event_id = Str_filter($_POST['event_id'])) && ($event_time = Str_filter($_POST['event_time']))){
			// if($username = AccessToken_Getter($token)){
				// if($event = Mongodb_Reader("todo_events",array("event_id" => $event_id),1)){			
						// Mongodb_Updater("todo_events",array("event_id" => $event_id),array("event_due_time" => $event_time));
						// $res = Return_Error(false,0,"修改成功");
				// }else{
					// $res = Return_Error(true,14,"该事务不存在");
				// }
			// }else{
				// $res = Return_Error(true,7,"token无效或登录超时");
			// }
		// }else{
			// $res = Return_Error(true,4,"提交的数据为空");
		// }
		// echo $res;
	// }
	
	// function Note_changeDate(){
		// if( ($token = Str_filter($_POST['token'])) && ($event_id = Str_filter($_POST['event_id'])) && ($event_date = Str_filter($_POST['event_date']))){
			// if($username = AccessToken_Getter($token)){
				// if($event = Mongodb_Reader("todo_events",array("event_id" => $event_id),1)){			
						// Mongodb_Updater("todo_events",array("event_id" => $event_id),array("event_due_date" => $event_date));
						// $res = Return_Error(false,0,"修改成功");
				// }else{
					// $res = Return_Error(true,14,"该事务不存在");
				// }
			// }else{
				// $res = Return_Error(true,7,"token无效或登录超时");
			// }
		// }else{
			// $res = Return_Error(true,4,"提交的数据为空");
		// }
		// echo $res;
	// }
	
	// function Note_Completed(){
		// if( ($token = Str_filter($_POST['token'])) && ($event_id = Str_filter($_POST['event_id'])) ){
			// if($username = AccessToken_Getter($token)){
				// if($event = Mongodb_Reader("todo_events",array("event_id" => $event_id),1)){	
						// if($event['event_completed']){
							// $completed = false;
						// }else{
							// $completed = true;
						// }
						// Mongodb_Updater("todo_events",array("event_id" => $event_id),array("event_completed" => $completed));
						// $res = Return_Error(false,0,"修改成功");
				// }else{
					// $res = Return_Error(true,14,"该事务不存在");
				// }
			// }else{
				// $res = Return_Error(true,7,"token无效或登录超时");
			// }
		// }else{
			// $res = Return_Error(true,4,"提交的数据为空");
		// }
		// echo $res;
	// }
	
	// function Note_Starrted(){
		// if( ($token = Str_filter($_POST['token'])) && ($event_id = Str_filter($_POST['event_id'])) ){
			// if($username = AccessToken_Getter($token)){
				// if($event = Mongodb_Reader("todo_events",array("event_id" => $event_id),1)){	
						// if($event['event_starred']){
							// $starrted = false;
						// }else{
							// $starrted = true;
						// }
						// Mongodb_Updater("todo_events",array("event_id" => $event_id),array("event_starred" => $starrted));
						// $res = Return_Error(false,0,"修改成功");
				// }else{
					// $res = Return_Error(true,14,"该事务不存在");
				// }
			// }else{
				// $res = Return_Error(true,7,"token无效或登录超时");
			// }
		// }else{
			// $res = Return_Error(true,4,"提交的数据为空");
		// }
		// echo $res;
	// }
	
	// function Note_Order(){
		// if(($new_order = Str_filter($_POST['new_order'])) && ($token = Str_filter($_POST['token'])) && ($list_id = Str_filter($_POST['list_id'])) ){
			// if($username = AccessToken_Getter($token)){
				// if($list_events = Mongodb_Reader("relation_list_event",array("list_id" => $list_id),1)){
					// if(($events_id = change_order($new_order,$list_events["events_id"])) != false ){
						// Mongodb_Updater("relation_list_event",array("list_id" => $list_id),array("events_id" => $events_id));
						// $res = Return_Error(true,0,"修改成功");
					// }else{
						// $res = Return_Error(true,15,"事务数量有误");
					// }
				// }else{
					// $res = Return_Error(true,1,"该用户无事务");
				// }
			// }else{
				// $res = Return_Error(true,7,"token无效或登录超时");
			// }
		// }else{
			// $res = Return_Error(true,4,"提交的数据为空");
		// }
		// echo $res;
	// }
	
	function Add_relation_event_note($event_id,$note_id){
		if($list_notes = Mongodb_Reader("relation_event_note",array("event_id" => $event_id),1)){
			$notes_id = $list_notes["notes_id"];
			$notes_index = $list_notes["notes_index"] + 1;
			array_push($notes_id,$note_id);
			Mongodb_Updater("relation_event_note",array("event_id" => $event_id),array("notes_id" => $notes_id,"notes_index" => $notes_index));
		}else{
			$event_notes_new = array("event_id" => $event_id,"notes_id" => array("1" => $note_id),"notes_index" =>0);
			Mongodb_Writter("relation_event_note",$event_notes_new);
		}
	}
	
	function Remove_relation_event_note($event_id,$note_id){
		if($event_notes = Mongodb_Reader("relation_event_note",array("event_id" => $event_id),1)){
			$notes_id = $event_notes["notes_id"];
			if(count($notes_id)  <= 1){
				Mongodb_Remover("relation_event_note",array("event_id" => $event_id));
				return true;
			}else{
				$notes_index = $event_notes["notes_index"] - 1;
				$notes_id = Delete_From_Array($notes_id,$note_id);
				Mongodb_Updater("relation_event_note",array("event_id" => $event_id),array("notes_id" => $notes_id,"notes_index" => $notes_index));
				return true;
			}
		}
		else{
			return false;
		}	
	}
	
	function Add_note_total($event_id){
		if($event = Mongodb_Reader("todo_events",array("event_id" => $event_id),1)){
			$total = $event["note_total"] + 1;
			Mongodb_Updater("todo_events",array("event_id" => $event_id),array("note_total" => $total));
		}
	}
	
	function Remove_note_total($event_id){
		if($event = Mongodb_Reader("todo_events",array("event_id" => $event_id),1)){
			$total = $event["note_total"] - 1;
			Mongodb_Updater("todo_events",array("event_id" => $event_id),array("note_total" => $total));
		}
	}
	
	function Return_Notes_Title($bool,$error_code,$message,$data = array()){
		return json_encode(array("error" => $bool,"error_code" => $error_code,"message" => $message,"notes_title" => $data));
	}
	
 ?>
