<?php 

/* Handles all routes related to the WP Live Chat Support API */

/*
 * Add the following routes:
 * - '/wp_live_chat_support/v1/accept_chat'  
 */
add_action('rest_api_init', 'wplc_rest_routes_init');

function wplc_rest_routes_init() {
	register_rest_route('wp_live_chat_support/v1','/accept_chat', array(
						'methods' => 'GET, POST',
						'callback' => 'wplc_api_accept_chat'
	));

	register_rest_route('wp_live_chat_support/v1','/end_chat', array(
						'methods' => 'GET, POST',
						'callback' => 'wplc_api_end_chat'
	));

	register_rest_route('wp_live_chat_support/v1','/send_message', array(
						'methods' => 'GET, POST',
						'callback' => 'wplc_api_send_message'
	));

	register_rest_route('wp_live_chat_support/v1','/get_status', array(
						'methods' => 'GET, POST',
						'callback' => 'wplc_api_get_status'
	));

	register_rest_route('wp_live_chat_support/v1','/get_messages', array(
						'methods' => 'GET, POST',
						'callback' => 'wplc_api_get_messages'
	));

	register_rest_route('wp_live_chat_support/v1','/get_sessions', array(
						'methods' => 'GET, POST',
						'callback' => 'wplc_api_get_sessions'
	));

	register_rest_route('wp_live_chat_support/v1','/call_to_server_visitor', array(
						'methods' => 'GET, POST',
						'callback' => 'wplc_api_call_to_server_visitor'
	));


	do_action("wplc_api_route_hook");
}