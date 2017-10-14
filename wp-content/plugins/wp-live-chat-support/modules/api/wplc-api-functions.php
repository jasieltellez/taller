<?php 



/* Handles all functions related to the WP Live Chat Support API */

/*
 * Accepts a chat within the WP Live Chat Support Dashboard
 * Required GET/POST variables:
 * - Token 
 * - Chat ID
 * - Agent ID 
*/
function wplc_api_accept_chat(WP_REST_Request $request){
	$return_array = array();
	if(isset($request)){
		if(isset($request['token'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['token'] === $check_token){
				if(isset($request['chat_id'])){
					if(isset($request['agent_id'])){
						if(wplc_change_chat_status(intval($request['chat_id']), 3, intval($request['agent_id']))){
							$return_array['response'] = "Chat accepted successfully";
							$return_array['code'] = "200";
							$return_array['data'] = array("chat_id" => intval($request['chat_id']),
														  "agent_id" => intval($request['agent_id']));
						} else {
							$return_array['response'] = "Status could not be changed";
							$return_array['code'] = "404";
						}
				 	} else {
						$return_array['response'] = "No 'agent_id' found";
						$return_array['code'] = "401";
						$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
													      "chat_id"   => "Chat ID",
													      "agent_id"   => "Agent ID");
					}
			 	} else {
					$return_array['response'] = "No 'chat_id' found";
					$return_array['code'] = "401";
					$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
												      "chat_id"   => "Chat ID",
												      "agent_id"   => "Agent ID");
				}
		 	} else {
				$return_array['response'] = "Secret token is invalid";
				$return_array['code'] = "401";
			}
		}else{
			$return_array['response'] = "No secret 'token' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
										      "chat_id"   => "Chat ID",
										      "agent_id"   => "Agent ID");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
										  "chat_id"   => "Chat ID",
										  "agent_id"   => "Agent ID");
	}
	
	return $return_array;
}

/*
 * Ends a chat within the WP Live Chat Support Dashboard
 * Required GET/POST variables:
 * - Token 
 * - Chat ID
 * - Agent ID 
*/
function wplc_api_end_chat(WP_REST_Request $request){
	$return_array = array();
	if(isset($request)){
		if(isset($request['token'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['token'] === $check_token){
				if(isset($request['chat_id'])){
					if(isset($request['agent_id'])){
						if(wplc_change_chat_status(intval($request['chat_id']), 1, intval($request['agent_id']))){
							$return_array['response'] = "Chat ended successfully";
							$return_array['code'] = "200";
							$return_array['data'] = array("chat_id" => intval($request['chat_id']),
														  "agent_id" => intval($request['agent_id']));
						} else {
							$return_array['response'] = "Status could not be changed";
							$return_array['code'] = "404";
						}
				 	} else {
						$return_array['response'] = "No 'agent_id' found";
						$return_array['code'] = "401";
						$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
													      "chat_id"   => "Chat ID",
													      "agent_id"   => "Agent ID");
					}
			 	} else {
					$return_array['response'] = "No 'chat_id' found";
					$return_array['code'] = "401";
					$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
												      "chat_id"   => "Chat ID",
												      "agent_id"   => "Agent ID");
				}
		 	} else {
				$return_array['response'] = "Secret token is invalid";
				$return_array['code'] = "401";
			}
		}else{
			$return_array['response'] = "No secret 'token' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
										      "chat_id"   => "Chat ID",
										      "agent_id"   => "Agent ID");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
										  "chat_id"   => "Chat ID",
										  "agent_id"   => "Agent ID");
	}
	
	return $return_array;
}

/*
 * Send a message to a chat within the WP Live Chat Support Dashboard
 * Required GET/POST variables:
 * - Token 
 * - Chat ID
 * - Message
*/
function wplc_api_send_message(WP_REST_Request $request){
	$return_array = array();
	if(isset($request)){
		if(isset($request['token'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['token'] === $check_token){
				if(isset($request['chat_id'])){
					if(isset($request['message'])){
			            $chat_msg = sanitize_text_field($request['message']);
			            $wplc_rec_msg = wplc_api_record_admin_message(intval($request['chat_id']),$chat_msg);
			            if ($wplc_rec_msg) {
			                $return_array['response'] = "Message sent successfully";
							$return_array['code'] = "200";
							$return_array['data'] = array("chat_id" => intval($request['chat_id']),
														  "agent_id" => intval($request['agent_id']));
			            } else {
			                $return_array['response'] = "Message not sent";
							$return_array['code'] = "404";
			            }
					} else {
						$return_array['response'] = "No 'message' found";
						$return_array['code'] = "401";
						$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
													      	  "chat_id"   => "Chat ID",
													      	  "message" => "Message");
					}
			 	} else {
					$return_array['response'] = "No 'chat_id' found";
					$return_array['code'] = "401";
					$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
												      	  "chat_id"   => "Chat ID",
												      	  "message" => "Message");
				}
		 	} else {
				$return_array['response'] = "Secret token is invalid";
				$return_array['code'] = "401";
			}
		}else{
			$return_array['response'] = "No secret 'token' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
										      	  "chat_id"   => "Chat ID",
										      	  "message" => "Message");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
									      	  "chat_id"   => "Chat ID",
									      	  "message" => "Message");
	}
	
	return $return_array;
}

/*
 * Fetch a chat status within the WP Live Chat Support Dashboard
 * Required GET/POST variables:
 * - Token 
 * - Chat ID
*/
function wplc_api_get_status(WP_REST_Request $request){
	$return_array = array();
	if(isset($request)){
		if(isset($request['token'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['token'] === $check_token){
				if(isset($request['chat_id'])){
					$status = wplc_return_chat_status(intval($request['chat_id']));
					if($status){
						$return_array['response'] = "Chat status found";
						$return_array['code'] = "200";
						$return_array['data'] = array("chat_id" => intval($request['chat_id']),
													  "status" => $status);
					} else {
						$return_array['response'] = "Chat status not found";
						$return_array['code'] = "404";
						$return_array['data'] = array("chat_id" => intval($request['chat_id']));
					}
					

			 	} else {
					$return_array['response'] = "No 'chat_id' found";
					$return_array['code'] = "401";
					$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
												      	  "chat_id"   => "Chat ID");
				}
		 	} else {
				$return_array['response'] = "Secret token is invalid";
				$return_array['code'] = "401";
			}
		}else{
			$return_array['response'] = "No secret 'token' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
										      	  "chat_id"   => "Chat ID");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
									      	  "chat_id"   => "Chat ID");
	}
	
	return $return_array;
}

/*
 * Fetch a chat status within the WP Live Chat Support Dashboard
 * Required GET/POST variables:
 * - Token 
 * - Chat ID
 * Optional:
 * - Limit (Defaults to 50/Max Limit of 50)
 * - Offset (Defaults to 0)
*/
function wplc_api_get_messages(WP_REST_Request $request){
	$return_array = array();
	if(isset($request)){
		if(isset($request['token'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['token'] === $check_token){
				if(isset($request['chat_id'])){
					$limit = 50;
					$offset = 0;
					if(isset($request['limit'])){
						$limit = intval($request['limit']);
					}
					if(isset($request['offset'])){
						$offset = intval($request['offset']);
					}

					$message_data = wplc_api_return_messages($request['chat_id'], $limit, $offset);
					
					if($message_data){
						$return_array['response'] = "Message data returned";
						$return_array['code'] = "200";
						$return_array['data'] = array("messages" => $message_data);
					} else {
						$return_array['response'] = "Messages not found";
						$return_array['code'] = "404";
						$return_array['data'] = array("chat_id" => intval($request['chat_id']));
					}
			 	} else {
					$return_array['response'] = "No 'chat_id' found";
					$return_array['code'] = "401";
					$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
												      	  "chat_id"   => "Chat ID");
				}
		 	} else {
				$return_array['response'] = "Secret token is invalid";
				$return_array['code'] = "401";
			}
		}else{
			$return_array['response'] = "No secret 'token' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
										      	  "chat_id"   => "Chat ID");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
									      	  "chat_id"   => "Chat ID");
	}
	
	return $return_array;
}

/*
 * Fetch a chat sessions within the WP Live Chat Support Dashboard
 * Required GET/POST variables:
 * - Token 
*/
function wplc_api_get_sessions(WP_REST_Request $request){
	$return_array = array();
	if(isset($request)){
		if(isset($request['token'])){
			$check_token = get_option('wplc_api_secret_token');
			if($check_token !== false && $request['token'] === $check_token){
				$session_data = wplc_api_return_sessions();
				if($session_data){
					$return_array['response'] = "Sessions found";
					$return_array['code'] = "200";
					$return_array['data'] = array("sessions" => $session_data);
				} else {
					$return_array['response'] = "No sessions found";
					$return_array['code'] = "404";
				}
		 	} else {
				$return_array['response'] = "Secret token is invalid";
				$return_array['code'] = "401";
			}
		}else{
			$return_array['response'] = "No secret 'token' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
										      	  "chat_id"   => "Chat ID");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array("token" => "YOUR_SECRET_TOKEN",
									      	  "chat_id"   => "Chat ID");
	}
	
	return $return_array;
}

/*
 * Records an admin message via the API
*/
function wplc_api_record_admin_message($cid, $msg){
	global $wpdb;
    global $wplc_tblname_msgs;

    $fromname = apply_filters("wplc_filter_admin_name","Admin");
    $orig = '1';
    
    $msg = apply_filters("wplc_filter_message_control",$msg);

    $wpdb->insert( 
	$wplc_tblname_msgs, 
	array( 
            'chat_sess_id' => $cid, 
            'timestamp' => current_time('mysql'),
            'msgfrom' => $fromname,
            'msg' => $msg,
            'status' => 0,
            'originates' => $orig
	), 
	array( 
            '%s', 
            '%s',
            '%s',
            '%s',
            '%d',
            '%s'
	) 
    );
    
    wplc_update_active_timestamp(sanitize_text_field($cid));
    wplc_change_chat_status(sanitize_text_field($cid),3);
    
    return true;
}

/*
 * Returns messages from server
*/
function wplc_api_return_messages($cid, $limit, $offset){
	global $wpdb;
    global $wplc_tblname_msgs;
    
    $cid = intval($cid);
    $limit = intval($limit);
    $offset = intval($offset);

    $cdata = wplc_get_chat_data($cid);
    $wplc_settings = get_option("WPLC_SETTINGS");


    if($limit > 50){
    	$limit = 50;
    }

	$results = $wpdb->get_results(
        "
        SELECT * 
        FROM $wplc_tblname_msgs 
        WHERE `chat_sess_id` = '$cid' 
        ORDER BY `timestamp` ASC 
        LIMIT $limit OFFSET $offset 
        "
    );

    $messages = array(); //The message array
    if ($results) {
	    foreach ($results as $result) {
	    	$messages[$result->id] = array(); //Message object
			
			$messages[$result->id]['from'] = $result->msgfrom;
	        $messages[$result->id]['message'] = stripslashes($result->msg);
	        $messages[$result->id]['timestamp'] = strtotime($result->timestamp);
	        $messages[$result->id]['is_admin'] = $result->originates == 1 ? true : false;
	        
	        $gravatar = "";        
	        if($messages[$result->id]['is_admin']){
	            if (isset($cdata->other)) {
	                $other = maybe_unserialize($cdata->other);
	                if (isset($other['aid'])) {
	                    $user_info = get_userdata(intval($other['aid']));
	                    $gravatar = "//www.gravatar.com/avatar/".md5($user_info->user_email)."?s=30";    
	                }
	            } 
	        } else {
		    	if (isset($cdata->email)) {
		                $gravatar = "//www.gravatar.com/avatar/".md5($cdata->email)."?s=30";    
		        } 
	        }
	        
	        if(function_exists('wplc_decrypt_msg')){
	            $messages[$result->id]['message'] = wplc_decrypt_msg($messages[$result->id]['message']);
	        }

			$messages[$result->id]['message'] = stripslashes($messages[$result->id]['message']);
	    }
	}
    return $messages;
}


function wplc_api_return_sessions() {
    global $wpdb;
    global $wplc_tblname_chats;
    
    $results = $wpdb->get_results("SELECT * FROM $wplc_tblname_chats WHERE `status` = 3 OR `status` = 2 OR `status` = 10 OR `status` = 5 or `status` = 8 or `status` = 9 ORDER BY `timestamp` ASC");
    
    $session_array = array();
            
    if ($results) {
        foreach ($results as $result) {
            global $wplc_basic_plugin_url;
            $ip_info = maybe_unserialize($result->ip);
            $user_ip = $ip_info['ip'];
            if($user_ip == ""){
                $user_ip = __('IP Address not recorded', 'wplivechat');
            }

            $browser = wplc_return_browser_string($user_data['user_agent']);
            $browser_image = wplc_return_browser_image($browser,"16");
         
            
           	$session_array[$result->id] = array();
           
           	$session_array[$result->id]['name'] = $result->name;
           	$session_array[$result->id]['email'] = $result->email;
           
           	$session_array[$result->id]['status'] = $result->status;
           	$session_array[$result->id]['timestamp'] = wplc_time_ago($result->timestamp);

           if ((current_time('timestamp') - strtotime($result->timestamp)) < 3600) {
               $session_array[$result->id]['type'] = __("New","wplivechat");
           } else {
               $session_array[$result->id]['type'] = __("Returning","wplivechat");
           }
           
           $session_array[$result->id]['image'] = "//www.gravatar.com/avatar/".md5($result->email)."?s=30";
           $session_array[$result->id]['data']['browsing'] = $result->url;
           $path = parse_url($result->url, PHP_URL_PATH);
           
           if (strlen($path) > 20) {
                $session_array[$result->id]['data']['browsing_nice_url'] = substr($path,0,20).'...';
           } else { 
               $session_array[$result->id]['data']['browsing_nice_url'] = $path;
           }
           
           $session_array[$result->id]['data']['browser'] = $wplc_basic_plugin_url . "/images/$browser_image";
           $session_array[$result->id]['data']['ip'] = $user_ip;
        }
    }
    
    return $session_array;
}



/**
 * EXPIRIMENTAL - Works when changing the ajaxurl to the relevat endpoint
 */
function wplc_api_call_to_server_visitor(WP_REST_Request $request){
	$return_array = array();
	if(isset($request)){
		@ob_start();
		if(isset($request['security'])){
			$check = check_ajax_referer( 'wplc', 'security' );
			if ($check) {



		        $wplc_advanced_settings = get_option("wplc_advanced_settings");
		        if (!$wplc_advanced_settings) {
		            $wplc_delay_between_updates = 500000;
		            $wplc_delay_between_loops = 500000;
		            $wplc_iterations = 55;
		        } else {
		            if (isset($wplc_advanced_settings['wplc_delay_between_updates'])) { $wplc_delay_between_updates = intval($wplc_advanced_settings['wplc_delay_between_updates']); } else { $wplc_delay_between_updates = 500000; }
		            if (isset($wplc_advanced_settings['wplc_delay_between_loops'])) { $wplc_delay_between_loops = intval($wplc_advanced_settings['wplc_delay_between_loops']); } else { $wplc_delay_between_loops = 500000; }
		            if (isset($wplc_advanced_settings['wplc_iterations'])) { $wplc_iterations = intval($wplc_advanced_settings['wplc_iterations']); } else { $wplc_iterations = 55; }

		            if ($wplc_iterations < 10) { $wplc_iterations = 10; }
		            if ($wplc_iterations > 200) { $wplc_iterations = 200; }

		            if ($wplc_delay_between_updates < 250000) { $wplc_delay_between_updates = 250000; }
		            if ($wplc_delay_between_updates > 1000000) { $wplc_delay_between_updates = 1000000; }

		            if ($wplc_delay_between_loops < 250000) { $wplc_delay_between_loops = 250000; }
		            if ($wplc_delay_between_loops > 1000000) { $wplc_delay_between_loops = 1000000; }

		        }


		        $iterations = $wplc_iterations;



		        /* time in microseconds between updating the user on the page within the DB  (lower number = higher resource usage) */
		        define('WPLC_DELAY_BETWEEN_UPDATES', $wplc_delay_between_updates);
		        /* time in microseconds between long poll loop (lower number = higher resource usage) */
		        define('WPLC_DELAY_BETWEEN_LOOPS', $wplc_delay_between_loops);
		        /* this needs to take into account the previous constants so that we dont run out of time, which in turn returns a 503 error */
		        define('WPLC_TIMEOUT', (((WPLC_DELAY_BETWEEN_UPDATES + WPLC_DELAY_BETWEEN_LOOPS)) * $iterations) / 1000000);



		        global $wpdb;
		        global $wplc_tblname_chats;
		        global $wplc_tblname_msgs;				

				


		        /* we're using PHP 'sleep' which may lock other requests until our script wakes up. Call this function to ensure that other requests can run without waiting for us to finish */
		        session_write_close();



	            $wplc_settings = get_option("WPLC_SETTINGS");

	            
	            if (defined('WPLC_TIMEOUT')) { @set_time_limit(WPLC_TIMEOUT); } else { @set_time_limit(120); }
	            $i = 1;
	            $array = array("check" => false);
	            $array['debug'] = "";

	            $cdata = false;
	            if($request['cid'] == null || $request['cid'] == "" || $request['cid'] == "null" || $request['cid'] == 0){ } else {
	                /* get agent ID */

	                $cdata = wplc_get_chat_data(sanitize_text_field(intval($request['cid'])),__LINE__);
	                $from = __("Admin","wplivechat"); /* set default */

	                $array['aname'] = apply_filters("wplc_filter_admin_from", $from, $request['cid'],$cdata);
	            
	            }
	            
	            while($i <= $iterations) {
	                

	                if($request['cid'] == null || $request['cid'] == "" || $request['cid'] == "null" || $request['cid'] == 0){
	    //                echo 1;
	                    
	                    if( isset( $request['wplc_name'] ) && $request['wplc_name'] !== '' ){
	                        $user = sanitize_text_field($request['wplc_name']);                        
	                    } else {
	                        $user = "Guest";    
	                    }
	                    
	                    if( isset( $request['wplc_email'] ) && $request['wplc_email'] !== '' ){
	                        $email = sanitize_text_field($request['wplc_email']);    
	                    } else {
	                        $email = "no email set";    
	                    }

	                    if(isset($request['wplc_is_mobile']) && ($request['wplc_is_mobile'] === 'true' || $request['wplc_is_mobile'] === true)){
	                        $is_mobile = true;
	                    } else {
	                        $is_mobile = false;
	                    }

	                    $cid = wplc_log_user_on_page($user,$email,sanitize_text_field($request['wplcsession']), $is_mobile);
	                    $array['cid'] = $cid;

	                    $array['status'] = wplc_return_chat_status($cid);
	                    $array['wplc_name'] = $user;
	                    $array['wplc_email'] = $email;
	                    $array['check'] = true;        

	                } else {
	    //                echo 2;
	                    



	                    $new_status = wplc_return_chat_status(sanitize_text_field($request['cid']));
	                    $array['wplc_name'] = sanitize_text_field($request['wplc_name']);
	                    $array['wplc_email'] = sanitize_text_field($request['wplc_email']);
	                    $array['cid'] = sanitize_text_field($request['cid']);
	                    $array['aid'] = sanitize_text_field($request['cid']);

	                    $array = apply_filters("wplc_filter_user_long_poll_chat_loop_iteration",$array,$request,$i,$cdata);
	                    
	                    if($new_status == $request['status']){ // if status matches do the following
	                        if($request['status'] != 2){
	                            /* check if session_variable is different? if yes then stop this script completely. */
	                            if (isset($request['wplcsession']) && $request['wplcsession'] != '' && $i > 1) {
	                                $wplc_session_variable = sanitize_text_field($request['wplcsession']);
	                                $current_session_variable = wplc_return_chat_session_variable(sanitize_text_field($request['cid']));
	                                if ($current_session_variable != "" && $current_session_variable != $wplc_session_variable) {
	                                    /* stop this script */
	                                    $array['status'] = 11;
	                                    echo json_encode($array);
	                                    die();
	                                }
	                            }


	                            if ($i == 1) {
	                                wplc_update_user_on_page(sanitize_text_field($request['cid']), sanitize_text_field($request['status']), sanitize_text_field($request['wplcsession']));
	                            }
	                        }
	                        if (intval($request['status']) == 0 || intval($request['status']) == 12){ // browsing - user tried to chat but admin didn't answer so turn back to browsing
	                            //wplc_update_user_on_page(sanitize_text_field($request['cid']), 0, sanitize_text_field($request['wplcsession']));
	                            //$array['status'] = 5;
	                            wplc_update_user_on_page(sanitize_text_field($request['cid']), 12, sanitize_text_field($request['wplcsession']));
	                            $array['status'] = 12;
	                            //$array['check'] = true;
	                            
	                        } 
	                        else if($request['status'] == 3){
	                            //wplc_update_user_on_page(sanitize_text_field($request['cid']), 3);
	                            $messages = wplc_return_user_chat_messages(sanitize_text_field($request['cid']),$wplc_settings,$cdata);
	                            if ($messages){
	                                wplc_mark_as_read_user_chat_messages(sanitize_text_field($request['cid']));
	                                $array['status'] = 3;
	                                $array['data'] = $messages;
	                                $array['check'] = true;
	                            }
	                        } 
	                        else if(intval($request['status']) == 2){
	                            //wplc_update_user_on_page(sanitize_text_field($request['cid']), 3);
	                            $messages = wplc_return_user_chat_messages(sanitize_text_field($request['cid']),$wplc_settings,$cdata);
	                            $array['debug'] = "we are here ".__LINE__;
	                            if ($messages){
	                                wplc_mark_as_read_user_chat_messages(sanitize_text_field($request['cid']));
	                                $array['status'] = 2;
	                                $array['data'] = $messages;
	                                $array['check'] = true;
	                            }
	                        } 

	                        /* check if this is part of the first run */
	                        if (isset($request['first_run']) && sanitize_text_field($request['first_run']) == 1) {
	                            /* if yes, then send data now and dont wait for all iterations to complete */
	                            if (!isset($array['status'])) { $array['status'] = $new_status; }
	                            $array['check'] = true;
	                        } 
	                        else if (isset($request['short_poll']) && sanitize_text_field($request['short_poll']) == "true") {
	                            /* if yes, then send data now and dont wait for all iterations to complete */
	                            if (!isset($array['status'])) { $array['status'] = $new_status; }
	                            $array['check'] = true;
	                        }
	                    } else { // statuses do not match
	                        $array['debug'] = $array['debug']. " ". "Doesnt match $new_status ".$request['status'];
	                        $array['status'] = $new_status;
	                        if($new_status == 1){ // completed
	                            wplc_update_user_on_page(sanitize_text_field($request['cid']), 8, sanitize_text_field($request['wplcsession']));
	                            $array['check'] = true;
	                            $array['status'] = 8;
	                            $array['data'] =  __("Admin has closed and ended the chat","wplivechat");
	                        }
	                        else if(intval($new_status == 2)) { // pending
	                            $array['debug'] = "we are here ".__LINE__;
	                            $array['check'] = true;
	                            $array['wplc_name'] = wplc_return_chat_name(sanitize_text_field($request['cid']));
	                            $array['wplc_email'] = wplc_return_chat_email(sanitize_text_field($request['cid']));
	                            $messages = wplc_return_chat_messages(sanitize_text_field($request['cid']),false,true,$wplc_settings,$cdata,'array');
	                            if ($messages){
	                                $array['data'] = $messages;
	                            }
	                        }
	                        else if($new_status == 3){ // active
	                            $array['data'] = null;
	                            $array['check'] = true;
	                            if($request['status'] == 5){
	                                $messages = wplc_return_chat_messages(sanitize_text_field($request['cid']),false,true,$wplc_settings,$cdata,'array');
	                                if ($messages){
	                                    $array['data'] = $messages;
	                                }
	                            }
	                        }
	                        else if($new_status == 7){ // timed out
	                            wplc_update_user_on_page(sanitize_text_field($request['cid']), 5, sanitize_text_field($request['wplcsession']));
	                        }
	                        else if($new_status == 9){ // user closed chat without inputting or starting a chat
	                            $array['check'] = true;
	                        } 
	                        else if($new_status == 12){ // no answer from admin
	                            $array['data'] = wplc_return_no_answer_string(sanitize_text_field($request['cid']));
	                            $array['check'] = true;
	                            @do_action("wplc_hook_missed_chat",array("cid" => $request['cid'],"name" => $request['wplc_name'],"email" => $request['wplc_email']));
	                        } 
	                        else if($new_status == 10){ // minimized active chat
	                            $array['check'] = true;
	                            if($request['status'] == 5){
	                                $messages = wplc_return_chat_messages(sanitize_text_field($request['cid']),false,true,$wplc_settings,$cdata,'array');
	                                if ($messages){
	                                    $array['data'] = $messages;
	                                }
	                            }
	                        }
	                        /* check if this is part of the first run */
	                        if (isset($request['first_run']) && sanitize_text_field($request['first_run']) == "1") {
	                            /* if yes, then send data now and dont wait for all iterations to complete */
	                            if (!isset($array['status'])) { $array['status'] = $new_status; }
	                            $array['check'] = true;
	                        } 
	                        else if (isset($request['short_poll']) && sanitize_text_field($request['short_poll']) == "true") {
	                            /* if yes, then send data now and dont wait for all iterations to complete */
	                            if (!isset($array['status'])) { $array['status'] = $new_status; }
	                            $array['check'] = true;
	                        }                         
	                        $array = apply_filters("wplc_filter_wplc_call_to_server_visitor_new_status_check",$array);  
	                        
	                    }
	                }
	                if($array['check'] == true){
	                    echo json_encode($array);
	                    break;
	                }
	                $i++;
	                
	                if (defined('WPLC_DELAY_BETWEEN_LOOPS')) { usleep(WPLC_DELAY_BETWEEN_LOOPS); } else { usleep(500000); }

	                @ob_end_flush();

	            }
	            die();







		 	} else {
				$return_array['response'] = "Nonce is invalid";
				$return_array['code'] = "401";
			}
		} else{
			$return_array['response'] = "No 'security' found";
			$return_array['code'] = "401";
			$return_array['requirements'] = array("security" => "YOUR_SECRET_TOKEN",
										      "cid"   => "Chat ID",
										      "user"   => "User type",
										      'type' => "TYPE");
		}
	}else{
		$return_array['response'] = "No request data found";
		$return_array['code'] = "400";
		$return_array['requirements'] = array("security" => "YOUR_SECRET_TOKEN",
									      "cid"   => "Chat ID",
									      "user"   => "User type",
									      'type' => "TYPE");
	}
	
	return $return_array;
}