<?php
/*
 * Node Code -> Insert pun here
*/

add_action("wplc_activate_hook", "wplc_node_server_token_check", 10);
add_action("wplc_update_hook", "wplc_node_server_token_check", 10);
/**
 * Checks if a secret key has been created. 
 * If not create one for use in the API
 *
 * @return void
*/
function wplc_node_server_token_check(){
	if (!get_option("wplc_node_server_secret_token")) {
		$user_token = wplc_node_server_token_create();
        add_option("wplc_node_server_secret_token", $user_token);
    }
}

/**
 * Generates a new Secret Token
 *
 * @return string
*/
function wplc_node_server_token_create(){
	$the_code = rand(0, 1000) . rand(0, 1000) . rand(0, 1000) . rand(0, 1000) . rand(0, 1000);
	$the_time = time();
	$token = md5($the_code . $the_time);
	return $token;
}

/**
 * Re-Generates the token
 * @return void
*/
function wplc_node_server_token_regenerate(){
	$wplc_node_token_new = wplc_node_server_token_create();
    update_option("wplc_node_server_secret_token", $wplc_node_token_new);
}

/**
 * Post to the NODE server - WHAHWHAH
 * 
 * @param string $route Route you would like to use for the node server
 * @param string $form_data data to send
 * @return string (or false on fail)
*/
function wplc_node_server_post($route, $form_data){
	$url = "https://wp-livechat.us-2.evennode.com" . "/" . $route;
	//$url = "http://" . "34.193.164.98:6086" . "/" . $route;
	if(!isset($form_data['server_token'])){
		$wplc_node_token = get_option("wplc_node_server_secret_token");
    	if(!$wplc_node_token){
	    	if(function_exists("wplc_node_server_token_regenerate")){
		        wplc_node_server_token_regenerate();
		        $wplc_node_token = get_option("wplc_node_server_secret_token");
		    }
		}

		$form_data['server_token'] = $wplc_node_token; //Add the security token
	}

	if(!isset($form_data['origin_url'])){
		$ajax_url = admin_url('admin-ajax.php');
		$origin_url = str_replace("/wp-admin/admin-ajax.php", "", $ajax_url);
		$form_data['origin_url'] = $origin_url; //Add the security token
	}

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($form_data)
        )
    );
    $context  = @stream_context_create($options);
    $result = @file_get_contents($url, false, $context);
    
    if ($result === FALSE) { 
        return false;
    } else {
        return $result;
    }
}

add_action("wplc_hook_chat_notification","wplc_filter_notification_hook_node",20,3);
/**
 * Send a system notification to the node server
 *
 * @return void
*/
function wplc_filter_notification_hook_node($type,$cid,$data){
    $wplc_settings = get_option("WPLC_SETTINGS");
    if(isset($wplc_settings['wplc_use_node_server']) && intval($wplc_settings['wplc_use_node_server']) == 1) { 
	
		$msg = false;
		$msg_admin = false;


		$other = false;
		switch($type){
			case "user_loaded":
				$msg_admin = sprintf( __("User is browsing <small><a href='%s' target='_BLANK'>%s</a></small>","wplivechat") , $data['uri'] , wplc_shortenurl($data['uri']) );
				break;
			case "await_agent":
				/**
				 * Removed this as it is duplicated on the second loop to the server
				 */
				$msg = $data['msg'];
				break;
			case "joined":
				$user_info = get_userdata(intval($data['aid']));
	        	$agent = $user_info->display_name;
	       		$msg = $agent . " ". __("has joined the chat.","wplivechat");	

		        $agent_tagline = '';
		        $agent_bio = '';
			  	$a_twitter = '';
			  	$a_linkedin = '';
			  	$a_facebook = '';
			  	$a_website = '';
			  	$social_links = '';

		        $tagline = get_user_meta( intval($data['aid']), 'wplc_user_tagline', true );
		        if( $tagline !== "" ){
		            $agent_tagline = $tagline;
		            $agent_tagline = '<span class="wplc_agent_infosection wplc_agent_tagline wplc-color-2">'.$agent_tagline.'</span>';
		        }
		        $bio = get_user_meta( intval($data['aid']), 'wplc_user_bio', true );
		        if( $bio !== "" ){
		            $agent_bio = $bio;
		            $agent_bio = '<span class="wplc_agent_infosection wplc_agent_bio wplc-color-2">'.$agent_bio.'</span>';
		        }      

			 	$a_twitter = get_user_meta( intval($data['aid']), 'wplc_user_twitter', true );
			 	$a_linkedin = get_user_meta( intval($data['aid']), 'wplc_user_linkedin', true );
			 	$a_facebook = get_user_meta( intval($data['aid']), 'wplc_user_facebook', true );
			 	$a_website = get_user_meta( intval($data['aid']), 'wplc_user_website', true );

			    if ($a_twitter === '' && $a_linkedin === '' && $a_facebook === '' && $a_website === '') { 
			    	$social_links = '';
			    } else {
			    	$social_links = '<span class="wplc_agent_infosection wplc_agent_social_links wplc-color-2">';
			    	if ($a_twitter !== '') {
			    		$social_links .= '<a href="'.$a_twitter.'" title="'.$agent.' - Twitter" border="0" rel="nofollow" target="_BLANK"><img src="'.plugins_url('/images/social/twitter.png',__FILE__).'" title="'.$agent.' - Twitter" border="0" /></a> &nbsp; ';
			    	}
			    	if ($a_linkedin !== '') {
			    		$social_links .= '<a href="'.$a_linkedin.'" title="'.$agent.' - Twitter" border="0" rel="nofollow" target="_BLANK"><img src="'.plugins_url('/images/social/linkedin.png',__FILE__).'" title="'.$agent.' - LinkedIn" border="0" /></a> &nbsp; ';
			    	}
			    	if ($a_facebook !== '') {
			    		$social_links .= '<a href="'.$a_facebook.'" title="'.$agent.' - Twitter" border="0" rel="nofollow" target="_BLANK"><img src="'.plugins_url('/images/social/facebook.png',__FILE__).'" title="'.$agent.' - Facebook" border="0" /></a> &nbsp; ';
			    	}
			    	if ($a_website !== '') {
			    		$social_links .= '<a href="'.$a_website.'" title="'.$agent.' - Twitter" border="0" rel="nofollow" target="_BLANK"><img src="'.plugins_url('/images/social/website.png',__FILE__).'" title="'.$agent.' - Website" border="0" /></a> &nbsp; ';
			    	}
			    	$social_links .= '</span>';
			    }

	       		$other = array(
	                        'ntype' => 'joined',
	                        'email' => md5($user_info->user_email),
	                        'name' => $agent,
	                        'aid' => $user_info->ID,
	                        'agent_tagline' => $agent_tagline,
	                        'agent_bio' => $agent_bio,
	                        'social_links' => $social_links
	                    );
				break;
			case "doc_suggestion":
				$msg = $data['formatted_msg'];
	        	$msg_admin = $data['formatted_msg_admin'];
				break;
			case "system_dep_transfer":
				if(function_exists("wplc_filter_control_chat_notification_auto_department_transfer")){
					$from_department = null; 
		        	$to_department = null;

			        if(isset($data['to_dep_id']) && isset($data['from_dep_id'])){
			        	if(function_exists("wplc_get_department")){
			        		$from_department = wplc_get_department(intval($data['from_dep_id']));
			        		$to_department = wplc_get_department(intval($data['to_dep_id']));
			        	}
			        }
		
			        //User
			        $msg = __("No agents available in","wplivechat") . " ";
			        if($from_department === null){
			        	$msg .= __("selected department", "wplivechat");
			        } else {
			        	$msg .= $from_department[0]->name;
			        }
			        $msg .= ", " . __("automatically transferring you to", "wplivechat") . " ";
			        if($to_department === null){
			        	$msg .= __("the next available department", "wplivechat");
			        } else {
			        	$msg .= $to_department[0]->name;
			        }
			        $msg .= ".";

			        //Admin
			        $msg_admin = __("User has been transfered from ","wplivechat") . " ";
			        if($from_department === null){
			        	$msg_admin .= __("department", "wplivechat");
			        } else {
			        	$msg_admin .= $from_department[0]->name;
			        }

			        if($to_department !== null){
			        	$msg_admin .= __(" to ", "wplivechat") . " " . $to_department[0]->name;
			        }
			        $msg_admin .= " " . __("as there were no agents online") .  ".";
			    }
				break;
			case "transfer": 

				$user_info = get_userdata(intval($data['aid']));
				if( $user_info ){
		        	$agent = $user_info->display_name;
		        } else {
		        	$agent = "";
		        }

		        if(isset($data["auto_transfer"]) && $data["auto_transfer"] == true){
		        	if(intval($data['aid']) === 0){
		        		//Came from a department originally
		        		$msg =  __("Department took too long to respond, we are transferring this chat to the next available agent.","wplivechat");
		        	} else {
						$msg = $agent . " " . __("took too long to respond, we are transferring this chat to the next available agent.","wplivechat");
					}
		        } else {
		        	$msg = $agent . " ". __("has transferred the chat.","wplivechat");
		        }

		        $msg_admin = "<strong>" . __("User received this message", "wplivechat") . ":</strong> " . $msg;

				break;
			default:
				break;
		}

		if(isset($cid)){
			$cid = intval($cid);
			if($msg !== false){
	            $user_message = array( 
	                    'cid' => $cid, 
	                    'timestamp' => current_time('mysql'),
	                    'msgfrom' => __('System notification',"wplivechat"),
	                    'msg' => $msg,
	                    'status' => 0,
	                    'originates' => '0',
	                    'other' => $other
	            ); 

	            $user_request = wplc_node_server_post("system_message", $user_message);

	            if($user_request === false){
	            	//Something is wrong
	            } else {

	            }
			}

			if($msg_admin !== false){
				$agent_message = array( 
	                    'cid' => $cid, 
	                    'timestamp' => current_time('mysql'),
	                    'msgfrom' => __('System notification',"wplivechat"),
	                    'msg' => $msg_admin,
	                    'status' => 0,
	                    'originates' => '3'
	            );

	            $agent_request = wplc_node_server_post("system_message", $agent_message);
	            if($agent_request === false){
					//Something is wrong
	            } else {

	            }
			}

		}
	}

	return;
}

add_action("wplc_api_route_hook", "wplc_api_node_routes");
/**
 * Add a REST API routes for the node server
 *
 * @return void
*/
function wplc_api_node_routes(){
	register_rest_route('wp_live_chat_support/v1','/async_storage', array(
						'methods' => 'POST',
						'callback' => 'wplc_node_async_storage_rest'
	));
}

/**
 * Handles Async storage REST -> Params are processed within the request
 * Required POST variables:
 * - Chat ID
 * - Security Key
 * - Message (JSON encoded array)
 * - Action 
 *
 * @param WP_REST_Request $request Request Data
 * @return void
*/
function wplc_node_async_storage_rest(WP_REST_Request $request){
	$return_array = array();
	$return_array['request_status'] = false; //Default to be returned if something goes wrong
	if(isset($request)){
		if(isset($request['security'])){
			$stored_token = get_option("wplc_node_server_secret_token");
			$check = $_POST['server_token'] == $stored_token ? true : false;
			if ($check) {
				if(isset($request['chat_id'])){
					if(isset($request['messages'])){
						if(isset($request['relay_action'])){
							$chat_id = sanitize_text_field($request['chat_id']);
							$message_data = json_decode($request['messages']);
							$chat_session = wplc_return_chat_session_variable($chat_id);
							$action = $request['relay_action'];
							if($message_data !== NULL){
								if($action == "wplc_user_send_msg"){
									foreach ($message_data as $message) {
										$message = sanitize_text_field($message);
										wplc_record_chat_msg("1", $chat_id, $message);
										//wplc_update_user_on_page($chat_id, "3", $chat_session); //Keep timestamp active
										wplc_update_active_timestamp($chat_id);
									}

									$return_array['request_status'] = true;
									$return_array['request_information'] = __("Success", "wplivechat");
								} else if ($action == "wplc_admin_send_msg"){
									foreach ($message_data as $message) {
										$message = sanitize_text_field($message);
										wplc_record_chat_msg("2", $chat_id, $message, true);
										//wplc_update_user_on_page($chat_id, "3", $chat_session); //Keep timestamp active
										wplc_update_active_timestamp($chat_id);
									}

									$return_array['request_status'] = true;
									$return_array['request_information'] = __("Success", "wplivechat");
								}
							} else {
								$return_array['request_information'] = __("Message data is corrupt", "wplivechat");
							}
						} else {
							$return_array['request_information'] = __("Action not set", "wplivechat");
						}
					} else {
						$return_array['request_information'] = __("Message data array not set", "wplivechat");
					}
				} else {
					$return_array['request_information'] = __("Chat ID is not set", "wplivechat");
				}
			}
		} else {
			$return_array['request_information'] = __("No security nonce found", "wplivechat");
		}
	}

	return $return_array;
          
}

add_action("wp_ajax_wplc_node_async_storage_ajax", "wplc_node_async_storage_ajax");
add_action("wp_ajax_nopriv_wplc_node_async_storage_ajax", "wplc_node_async_storage_ajax");
/**
 * Handles Async storage AJAX (Fallback for if REST is not present) -> Params are processed within the request
 * Required POST variables:
 * - Chat ID
 * - Security Key
 * - Message (JSON encoded array)
 * - Action 
 *
 * @return void
*/
function wplc_node_async_storage_ajax(){
	$return_array = array();
	$return_array['request_status'] = false; //Default to be returned if something goes wrong
	if(isset($_POST)){
		if(isset($_POST['server_token'])){
			$stored_token = get_option("wplc_node_server_secret_token");
			$check = $_POST['server_token'] == $stored_token ? true : false;
			if ($check) {
				if(isset($_POST['chat_id'])){
					if(isset($_POST['messages'])){
						if(isset($_POST['relay_action'])){
							$chat_id = sanitize_text_field($_POST['chat_id']);
							$message_data = json_decode($_POST['messages']);
							$chat_session = wplc_return_chat_session_variable($chat_id);
							$action = $_POST['relay_action'];
							if($message_data !== NULL){
								if($action == "wplc_user_send_msg"){
									foreach ($message_data as $message) {
										$message = sanitize_text_field($message);
										wplc_record_chat_msg("1", $chat_id, $message);
										//wplc_update_user_on_page($chat_id, "3", $chat_session); //Keep timestamp active
										wplc_update_active_timestamp($chat_id);

									}

									$return_array['request_status'] = true;
									$return_array['request_information'] = __("Success", "wplivechat");
								} else if ($action == "wplc_admin_send_msg"){
									foreach ($message_data as $message) {
										$message = sanitize_text_field($message);
										wplc_record_chat_msg("2", $chat_id, $message);
										//wplc_update_user_on_page($chat_id, "3", $chat_session); //Keep timestamp active
										wplc_update_active_timestamp($chat_id);
									}

									$return_array['request_status'] = true;
									$return_array['request_information'] = __("Success", "wplivechat");
								}
							} else {
								$return_array['request_information'] = __("Message data is corrupt", "wplivechat");
							}
						} else {
							$return_array['request_information'] = __("Action not set", "wplivechat");
						}
					} else {
						$return_array['request_information'] = __("Message data array not set", "wplivechat");
					}
				} else {
					$return_array['request_information'] = __("Chat ID is not set", "wplivechat");
				}
			}
		} else {
			$return_array['request_information'] = __("No security nonce found", "wplivechat");
		}
	}

	return $return_array;
          
}

add_action("wplc_change_chat_status_hook", "wplc_node_notify_server_of_status_change", 10, 2);
/**
 * Handles notifying the node server that this chat status has been changed by the php server
 *
 * @return void
*/
function wplc_node_notify_server_of_status_change($cid, $status){
    $wplc_settings = get_option("WPLC_SETTINGS");

    if(isset($wplc_settings['wplc_use_node_server']) && $wplc_settings['wplc_use_node_server'] == 1){
        $cid = intval($cid);
        $status = intval($status);
        if($status === 1){
            //End - This is most commonly done when the user has not sent a message in a long time. 

            //Notify the user and agent of this occurring 
            $msg = __("Chat has been ended.", "wplivechat");

            $system_message = array( 
                    'cid' => $cid, 
                    'timestamp' => current_time('mysql'),
                    'msgfrom' => __('System notification',"wplivechat"),
                    'system_notice' => $msg,
                    'status' => 0,
                    'originates' => '0',
                    'tripswitch' => true
            );

            $node_request = wplc_node_server_post("end_chat", $system_message);
            if($node_request === false){ } else { }
        } else if ($status === 0){
            //Notify the user and agent of this occurring 
            $system_message = array( 
                    'cid' => $cid, 
                    'timestamp' => current_time('mysql'),
                    'msgfrom' => __('System notification',"wplivechat"),
                    'msg' => wplc_return_no_answer_string($cid),
                    'status' => 0,
                    'originates' => '0'
            );
              

            $node_request = wplc_node_server_post("system_message", $system_message);
            if($node_request === false){ } else { }

    

        }
    } 
}
add_filter("wplc_log_user_on_page_insert_other_data_filter", "wplc_is_client_socket_enabled", 10, 1);
/**
 * Checks if the user is socket enabled (ready to chat via a socket connection)
 * If so add this to the other_data column of the session
 * 
 * @return void
*/
function wplc_is_client_socket_enabled($other_data){
	if(isset($_POST['socket'])){
		$other_data['socket'] = true;
	} else {
		$other_data['socket'] = false;
	}
	return $other_data;
}

add_action('wp_ajax_wplc_node_switch_to_ajax', 'wplc_node_switch_to_ajax');
add_action('wp_ajax_nopriv_wplc_node_switch_to_ajax', 'wplc_node_switch_to_ajax');
/**
 * Updates the 'other_data' of the session to reflect the socket fail over
 *
 * @return void
*/
function wplc_node_switch_to_ajax(){
	global $wpdb;
    global $wplc_tblname_chats;

	$check = check_ajax_referer( 'wplc', 'security' );
	if ($check == 1) {
		if ($_POST['action'] == "wplc_node_switch_to_ajax") {
           $cid = intval($_POST['cid']);
           if($cid){
           		if(function_exists("wplc_get_chat_data")){
           			$cdata = wplc_get_chat_data($cid);
           			if($cdata){
           				$other = maybe_unserialize($cdata->other);
           				$other['socket'] = false;

           				$query = $wpdb->update( 
					        $wplc_tblname_chats, 
					        array( 
					            'other' => maybe_serialize($other),
					        ), 
					        array('id' => $cid)
					    );
					    echo "1";
           				die();
           			}
           		}
           }
        }
	}
	echo "0";
	die();
}