/*
 * Cookie Status
 * 
 * 1 - complete - user has left site
 * 2 - pending - user waiting for chat to be answered by admin
 * 3 - active chat - user and admin are chatting
 * 4 - deleted
 * 5 - browsing - no data has been inputted
 * 6 - requesting chat - admin has requested a chat with user
 * 7 - timed out - visitor has timed out 
 * 8 - complete but now browsing again
 * 9 - user closed chat before starting chat
 * 10 - user minimized active chat
 * 11 - user moved on to another page (session variable is different)
 * 12 - user has not been answered after sending chat request and is still active
 * 
 */
var wplc_is_chat_open = false;
var wplc_online = false;
var wplc_agent_name = "";
var msg_history = new Array();
var wplc_is_minimized = false; /* global to hold whether or not the chat box is minimized */

var wplc_retry_interval = null;

var wplc_run = true;

var wplc_server = null;
wplc_server = new WPLCServer();

var wplc_server_last_loop_data = null;

var wplc_shown_welcome = false;

var wplc_current_agent = false;



jQuery(document).ready(function() {        
    var wplc_session_variable = new Date().getTime();
    var wplc_cid;
    var wplc_check_hide_cookie;
    var wplc_chat_status = "";
    var wplc_cookie_name = "";
    var wplc_cookie_email = "";
    var wplc_init_chat_box_check = true;
    var wplc_cid = null;
    
    var initial_data = {};
    var wplc_fist_run = true; 
    var wplc_long_poll_delay = 1500;

    /* preload the images */
    wplc_preload();

    wplc_cid = Cookies.get('wplc_cid');
    
    if(typeof wplc_cid === 'undefined'){
        wplc_cid = null;
    } else {
        wplc_cid = Cookies.get('wplc_cid');
    }
    
    wplc_check_hide_cookie = Cookies.get('wplc_hide');
    wplc_check_minimize_cookie = Cookies.get('wplc_minimize');
    wplc_chat_status = Cookies.get('wplc_chat_status');
    wplc_cookie_name = Cookies.get('wplc_name');
    wplc_cookie_email = Cookies.get('wplc_email');
    // Always start on 5 - ajax will then return chat status if active
    
    Cookies.set('wplc_chat_status', 5, { expires: 1, path: '/' });
    wplc_chat_status = 5;
    //if (typeof wplc_chat_status !== "undefined" && parseInt(wplc_chat_status) === 3) { } else {
        
    //}

    var data = {
        action: 'wplc_get_chat_box',
        security: wplc_nonce,
        cid: wplc_cid
    };

    jQuery.ajax({
        url: wplc_ajaxurl_site,
        data:data,
        type:"POST",
        success: function(response) {
            /* inject html */
            if(response){
                if (response === "0") { if (window.console) { console.log('WP Live Chat Support Return Error'); } wplc_run = false;  return; }
                response = JSON.parse(response);
                 
                 
                jQuery( "body" ).append( response['cbox']);

                wplc_listenForScrollEvent(jQuery("#wplc_chatbox"));

                if( typeof wplc_cookie_name == 'undefined' || typeof wplc_cookie_email == 'undefined' ){

                    var wplc_cookie_name =  jQuery( jQuery.parseHTML( response['cbox'] ) ).find( "#wplc_name" ).val(); 
                    var wplc_cookie_email =  jQuery( jQuery.parseHTML( response['cbox'] ) ).find( "#wplc_email" ).val(); 

                }
                


                /* is an agent online? */
                if (response['online'] === false) {
                    wplc_run = false;
                    wplc_online = false;
                } else {
                    wplc_online = true;
                }

                

                if (wplc_filter_run_override !== "1" || wplc_online === false) { wplc_run = false; } else { /* we can run */ }

                
                /* has this user been assigned an agent? */
                if (typeof response['type'] === "undefined") {
                    wplc_shown_welcome = false;
                } else {
                    if (response['type'] === "returning") {
                        wplc_shown_welcome = true;
                        /* set the agent vars so we can access them on the fly */
                        if (typeof response['agent_data'] !== "undefined") { 
                            wplc_current_agent = response['agent_data'];
                        }
                    } else {
                        wplc_shown_welcome = false;
                    }
                    
                }


                /*Support mobile loggin*/
                var wplc_mobile_check = false;
                if(typeof wplc_is_mobile !== "undefined" && (wplc_is_mobile === "true" || wplc_is_mobile === true)){
                    wplc_mobile_check = true;
                }

                /* start long polling */
                var data = {
                    action: 'wplc_call_to_server_visitor',
                    security: wplc_nonce,
                    cid:wplc_cid,
                    wplc_name: wplc_cookie_name,
                    wplc_email: wplc_cookie_email,
                    status:wplc_chat_status,
                    wplcsession:wplc_session_variable,
                    wplc_is_mobile: wplc_mobile_check,
                    wplc_extra_data:wplc_extra_data
                };

                if(wplc_server.browserIsSocketReady()){
                    data.socket = true;
                }

                initial_data = data;
                // ajax long polling function
                if (wplc_filter_run_override !== "1" || wplc_online === false) { 
                    wplc_call_to_server_chat(data,true,true);
                } else { 
                    wplc_call_to_server_chat(data,true,false);
                }
                
                if(wplc_cid !== null   && wplc_init_chat_box_check == true && wplc_init_chat_box !== false){
                    wplc_init_chat_box(wplc_cid,wplc_chat_status);
                }
             
                
            }

        }

    });

    /**
     * Preload images from the localized variable
     * @return void
     */
    function wplc_preload() {
        var images = [];
        if (typeof wplc_preload_images !== "undefined" && typeof wplc_preload_images === "object" ) {
            var wplc_i = 0;
            for (var key in wplc_preload_images) {
                images[wplc_i] = new Image();
                images[wplc_i].src = wplc_preload_images[key];
                wplc_i++;
            }
        }
    }


    /**
     * Detect if the chatbox is being scrolled.
     *
     * This had to be created as jQuery does not allow the scroll event to bubble up the DOM tree.
     * 
     * Thank you Bikal Basnet (https://stackoverflow.com/questions/16505182/bind-scroll-event-to-dynamic-div)
     * 
     * @param  object el The element in question
     */
    function wplc_listenForScrollEvent(el) {
        el.on("scroll", function(){
            el.trigger("wplc-custom-scroll");
        })

    }

    /**
     * This instantly sends the user the welcome message (i.e. to wait for any agent and start typing so long)
     * 
     * 
     */
    function wplc_send_welcome_message() {
        if(wplc_welcome_msg !== "" && !wplc_shown_welcome){
            message_class = "wplc_system_notification wplc-color-4";
            var concatenated_message = "<span class='" + message_class + "'>";
            concatenated_message += wplc_welcome_msg;
            concatenated_message += "</span>";

            if(typeof niftyFormatParser !== "undefined"){
                jQuery("#wplc_chatbox").append(niftyFormatParser(concatenated_message));
            } else{
                jQuery("#wplc_chatbox").append(concatenated_message);
            }
            wplc_scroll_to_bottom();
            
            wplc_shown_welcome = true;
        } 

    }

    /**
     * Scrolls the chat box to the bottom
     * 
     */
    function wplc_scroll_to_bottom() {
        var height = jQuery('#wplc_chatbox')[0].scrollHeight;
        jQuery('#wplc_chatbox').scrollTop(height);
    }
    function wplc_user_message_receiver(data){
        if(typeof wplc_loop_response_handler !== "undefined" && typeof wplc_loop_response_handler === "function"){
            wplc_loop_response_handler(data, wplc_server_last_loop_data);
            data = JSON.parse(data);
            if(typeof data['status'] !== "undefined"){ 
                delete wplc_server_last_loop_data.status;
            }

            if(data.keep_alive === true){
                setTimeout(function(){
                    wplc_server_last_loop_data.status = wplc_chat_status;
                    wplc_call_to_server_chat(wplc_server_last_loop_data);
                },100);
            }
        }
    }
    
    function wplc_user_retry_handler(data){
        var tstatus = Cookies.get("wplc_chat_status");

        if (tstatus !== "undefined") {
            if(tstatus !== 8 || tstatus !== 1){
                wplc_retry_interval = setTimeout(function(){
                    
                    wplc_server.prepareTransport(function(){
                        //Transport ready...
                        wplc_server_last_loop_data.status = parseInt(tstatus); //Set to existing status
                        wplc_call_to_server_chat(wplc_server_last_loop_data);
                    }, wplc_user_message_receiver, wplc_user_retry_handler, wplc_log_connection_error);


                },500);
            }

        }
    }

    function wplc_call_to_server_chat(data,first_run,short_poll) {


        if (typeof first_run === "undefined") { first_run = false; };
        if (typeof short_poll === "undefined") { short_poll = false; };
        data.first_run = first_run;
        data.short_poll = short_poll;

        if(typeof Cookies.get('wplc_name') !== "undefined"){
            data.msg_from_print = Cookies.get('wplc_name');
        }
  
        wplc_server_last_loop_data = data;

        wplc_server.send(wplc_ajaxurl, data, "POST", 120000,
            function(response) {
                wplc_long_poll_delay = 1500;
                wplc_loop_response_handler(response, data);
            },
            function(jqXHR, exception) {
                wplc_long_poll_delay = 5000;

                if (jqXHR.status == 404) {
                    wplc_log_connection_error('Error: Requested page not found. [404]');
        			wplc_run = false;
                } else if (jqXHR.status == 500) {
                    wplc_log_connection_error('Error: Internal Server Error [500].'); 
                    wplc_log_connection_error('Retrying in 5 seconds...');
		            wplc_run = true;
                } else if (exception === 'parsererror') {
                    wplc_log_connection_error('Error: Requested JSON parse failed.'); 
		            wplc_run = false;
                } else if (exception === 'abort') {
                    wplc_log_connection_error('Error: Ajax request aborted.');
		            wplc_run = false;
                } else {
                    wplc_log_connection_error('Error: Uncaught Error.\n' + jqXHR.responseText); 
                    wplc_log_connection_error('Retrying in 5 seconds...');
		            wplc_run = true;
                }                    
            },
            function(response){
                if (wplc_run) {
                    if(wplc_server.isInSocketMode() === false && wplc_server.isPreparingSocketMode() === false){
                        setTimeout(function() { 
                            wplc_call_to_server_chat(data,false,false); 
                        }, wplc_long_poll_delay);
                    } else if ((wplc_server.isInSocketMode() === false && wplc_server.isPreparingSocketMode() === true) && (typeof wplc_transport_prepared !== "undefined" && wplc_transport_prepared === false)) {
                        /* Allows for initiate chat to work on the node server */
                        if (typeof wplc_use_node_server !== "undefined" && wplc_use_node_server === "true") {
                            /* do not run this if using not the node jedi */
                            setTimeout(function() { 
                                wplc_call_to_server_chat(data,false,true); 
                            }, 7500);
                        }
                    } else {
                        if(typeof response !== "undefined" && typeof response.responseText !== "undefined" && response.responseText !== ""){
                            var response_data = JSON.parse(response.responseText);
                            if (typeof wplc_transport_prepared !== "undefined") {
                                if(wplc_transport_prepared !== true && (parseInt(response_data.status) === 3 || parseInt(response_data.status) === 2)){
                                    //Transport is unprepared and the user has returned to the page with a status 3/2
                                    wplc_server.prepareTransport(function(){
                                        wplc_call_to_server_chat(data,false,false); 
                                    }, wplc_user_message_receiver, wplc_user_retry_handler, wplc_log_connection_error);
                                }
                            }
                        }
                    }
                }
            }           
        );
        
    };  

    function wplc_loop_response_handler(response, data){
        if(response){
            if (response === "0") { if (window.console) { console.log('WP Live Chat Support Return Error'); } wplc_run = false;  return; }
            if (typeof response !== "object") {
                response = JSON.parse(response);    
            }

            data['action_2'] = "";            
            if(typeof response['wplc_name'] !== "undefined"){ data['wplc_name'] = response['wplc_name']; /* Cookies.set('wplc_name', response['wplc_name'], { expires: 1, path: '/' });*/ }
            if(typeof response['wplc_email'] !== "undefined"){ data['wplc_email'] = response['wplc_email']; /* Cookies.set('wplc_email', response['wplc_email'], { expires: 1, path: '/' }); */ }
            if(typeof response['cid'] !== "undefined"){ data['cid'] = response['cid']; Cookies.set('wplc_cid', response['cid'], { expires: 1, path: '/' }); }
            if(typeof response['aname'] !== "undefined") { wplc_agent_name = response['aname']; }
            if(typeof response['cid'] !== "undefined" && wplc_cid !== jQuery.trim(response['cid'])){ wplc_cid = jQuery.trim(response['cid']);  jQuery("#wplc_cid").val(wplc_cid); }
            if(typeof response['status'] !== "undefined" && parseInt(wplc_chat_status) !== parseInt(response['status'])){ 
                wplc_chat_status = response['status']; 
                Cookies.set('wplc_chat_status', null, { path: '/' }); 
                Cookies.set('wplc_chat_status', wplc_chat_status, { expires: 1, path: '/' }); 
            }
            
            /* Trigger for handling responses */
            jQuery.event.trigger({type: "wplc_user_chat_loop",response:response});

            /* Process status changes */
            if(data['status'] == response['status']){
                
                if(data['status'] == 5 && wplc_init_chat_box_check === true && wplc_init_chat_box !== false){ // open chat box on load
                    wplc_init_chat_box(data['cid'], data['status']);
                } 
                if((response['status'] == 3 || response['status'] == 2) && response['data'] != null){ // if active and data is returned      
                    wplc_run = true;
                    var wplc_new_message_sound = false;
                    if (typeof response['data'] === "object") {

                        for (var index in response['data']) {
                            if(typeof response['data'][index] !== "object"){
                                if (typeof msg_history[index] === "undefined") {
                                    //Not from node
                                    /* we dont have this message */
                                    msg_history[index] = true;
                                    
                                    msg_to_parse = response['data'][index].wplcStripSlashes();

                                    if(typeof niftyFormatParser !== "undefined"){
                                        jQuery("#wplc_chatbox").append(niftyFormatParser(msg_to_parse));
                                    } else{

                                        jQuery("#wplc_chatbox").append(response['data'][index].wplcStripSlashes());
                                    }

                                    wplc_new_message_sound = true;
                                
                                } else {
                                    /* we already have this message */
                                }
                            } else {
                                var the_message = response['data'][index];
                                the_message.mid = index;
                                wplc_push_message_to_chatbox(the_message,'u', function() {
                                    wplc_scroll_to_bottom();    
                                });

                                            
                            }
                        }
                    }   
                    else {
                        /* backwards compatibility - response['data'] is a string */
                        if(typeof niftyFormatParser !== "undefined"){
                            jQuery("#wplc_chatbox").append(niftyFormatParser(response['data'].wplcStripSlashes()));
                        } else{
                            jQuery("#wplc_chatbox").append(response['data'].wplcStripSlashes());
                            
                        }
                        wplc_new_message_sound = true;
                    }
                    
                    if(wplc_new_message_sound){
                        wplc_scroll_to_bottom();
                        if (typeof wplc_enable_ding !== 'undefined' && wplc_enable_ding === "1") {
                            new Audio(wplc_plugin_url+'/wp-live-chat-support/ding.mp3').play();                            
                        }
                    } 
                }

            } else {
                data['status'] = wplc_chat_status;
                Cookies.set('wplc_chat_status', wplc_chat_status, { expires: 1, path: '/' });
                if(response['status'] == 0 || response['status'] == 12){ // no answer from admin
                    jQuery("#wp-live-chat-3").hide();
                    if (typeof response['data'] !== "undefined") {
                        jQuery("#wplc_chatbox").append(response['data'].wplcStripSlashes()+"<hr />");
                    }
                    
                }
                else if(response['status'] == 8){ // chat has been ended by admin
                    wplc_run = false;

                    document.getElementById('wplc_chatmsg').disabled = true;
                    wplc_shown_welcome = false;

                    the_message = {};
                    the_message.originates = 0;
                    the_message.msg = wplc_error_messages.chat_ended_by_operator;
                    the_message.other = {};
                    var wplc_d = new Date();
                    the_message.other.datetime = Math.round( wplc_d.getTime() / 1000 );

                    wplc_push_message_to_chatbox(the_message,'u', function() {
                        wplc_scroll_to_bottom();    
                    });
                    

                    /*
                    if(typeof response['data'] === "object") {
                        for (var index in response['data']) {
                            if(typeof response['data'][index] === "object"){

                                var the_message = response['data'][index];
                                if(typeof the_message.originates !== "undefined"){
                                    var message_class = "";
                                    var message_content = "";

                                    if (parseInt(the_message.originates) === 0){
                                        //System Notification
                                        message_class = "wplc_system_notification wplc-color-4";
                                        message_content = the_message.msg;
                                        if(message_content !== ""){
                                            var concatenated_message = "<span class='" + message_class + "'>";
                                            concatenated_message += message_content;
                                            concatenated_message += "</span>";

                                            if(typeof niftyFormatParser !== "undefined"){
                                                jQuery("#wplc_chatbox").append(niftyFormatParser(concatenated_message));
                                            } else{
                                                jQuery("#wplc_chatbox").append(concatenated_message);
                                            }
                                        } 
                                    }
                                }
                            }
                        }
                    } else {
                        //Backwards Compat
                        jQuery("#wplc_chatbox").append("<em>"+response['data']+"</em><br />");
                    }*/


                    


                    jQuery.event.trigger({type: "wplc_end_chat"});
                    
               }
                else if(parseInt(response['status']) == 11){ /* use moved on to another page (perhaps in another tab so close this instance */
                    jQuery("#wp-live-chat").css({ "display" : "none" });
                    wplc_run = false;
                }
                else if(parseInt(response['status']) == 3 || parseInt(response['status']) == 2 || parseInt(response['status']) == 10){ // re-initialize chat
                    wplc_run = true;
                    if(parseInt(response['status']) == 3) { // only if not minimized open aswell
                        /* HERE NODE */

                        if (typeof wplc_use_node_server !== "undefined" && wplc_use_node_server === "true") {
                            /* do not run this if using not the node jedi */

                            if (typeof wplc_transport_prepared !== "undefined" && wplc_transport_prepared === false) {
                               wplc_server.prepareTransport(function(){
                                    wplc_call_to_server_chat(wplc_server_last_loop_data,false,false); 
                                }, wplc_user_message_receiver, wplc_user_retry_handler, wplc_log_connection_error);
                            }
                        }
                        if (!wplc_is_minimized) {
                            if (!wplc_is_chat_open) {
                                wplc_cbox_animation();
                                setTimeout(function() {
                                    open_chat(0);
                                },1500);
                            }
                        }


                        if(jQuery('#wp-live-chat').hasClass('wplc_left') === true || jQuery('#wp-live-chat').hasClass('wplc_right') === true){
                            //jQuery('#wp-live-chat').height("400px");
                        }
                    }
                    if(parseInt(response['status']) == 10) { // only if not minimized open aswell
                        wplc_run = true;
                        open_chat(0);
                        
                    }
                    if(response['data'] != null){ // append messages to chat area
                        if (typeof response['data'] === "object") {

                            for (var index in response['data']) {
                                wplc_new_message_sound = false;
                                if(typeof response['data'][index] !== "object"){

                                    if (typeof msg_history[index] === "undefined") {
                                        /* we dont have this message */
                                        msg_history[index] = true;
                                        if(typeof niftyFormatParser !== "undefined"){
                                            jQuery("#wplc_chatbox").append(niftyFormatParser(response['data'][index].wplcStripSlashes()));
                                        } else{
                                            jQuery("#wplc_chatbox").append(response['data'][index].wplcStripSlashes());
                                        }

                                        wplc_new_message_sound = true;
                                    } else {
                                            /* we already have this message */
                                    }
                                } else {
                                    var the_message = response['data'][index];
                                    the_message.mid = index;
                                    wplc_push_message_to_chatbox(the_message,'u', function() {
                                        wplc_scroll_to_bottom();    
                                    });
                                    
                                          
                                }

                                if(wplc_new_message_sound){
                                    if (response['alert']) {
                                        jQuery('#wplc-chat-alert').addClass('is-active');
                                    }
                                    wplc_scroll_to_bottom();
                                    if (typeof wplc_enable_ding !== 'undefined' && wplc_enable_ding === "1") {
                                        new Audio(wplc_plugin_url+'/wp-live-chat-support/ding.mp3').play();                            
                                    }
                                }
                            }
                        }   
                        else {
                            /* backwards compatibility - response['data'] is a string */
                            if(typeof niftyFormatParser !== "undefined"){
                                jQuery("#wplc_chatbox").append(niftyFormatParser(response['data'].wplcStripSlashes()));
                            } else{
                                jQuery("#wplc_chatbox").append(response['data'].wplcStripSlashes());
                                
                            }
                        }

                        if(response['data']){
                            wplc_scroll_to_bottom();
                            
                        } 
                    }
                }
            }  
        }
    }

    

    function wplc_log_connection_error(error){
        if (window.console) { console.log(error); }

        jQuery("#wplc_chatbox").append("<small>" + error + "</small><br>");
        wplc_scroll_to_bottom();
    }

    function wplc_display_error(error) {

        the_message = {};
        the_message.originates = 2;
        the_message.msg = error;
        the_message.other = {};
        var wplc_d = new Date();
        the_message.other.datetime = Math.round( wplc_d.getTime() / 1000 );
        wplc_push_message_to_chatbox(the_message,'u', function() {
            wplc_scroll_to_bottom();    
        });



    }
    
    function wplc_init_chat_box(cid, status){

        
        if(wplc_chat_status == 9 && wplc_check_hide_cookie == "yes"){
            
        } else if (wplc_chat_status === 3) {
            wplc_cbox_animation();
        } else {
            if(wplc_check_hide_cookie != "yes"){
                wplc_dc = setTimeout(function (){
                    wplc_cbox_animation();
                }, parseInt(window.wplc_delay));
            }
        }
        wplc_init_chat_box = false;
    }

    function wplc_cbox_animation() {
        /*
         * 1- Slide Up 
         * 2- Slide Across (Left/Right) 
         * 3- Slide Down 
         * 4- Fade In 
         */
        

        var wplc_window_id = jQuery("#wp-live-chat");

        var wplc_theme_chosen = jQuery(wplc_window_id).attr('wplc_animation');

        switch(wplc_theme_chosen){
            case 'none':
                jQuery(wplc_window_id).css('display', 'block');
                break;
            case 'animation-1':
                // Slide Up
                jQuery(wplc_window_id).animate({'marginBottom' : '0px'}, 1000);
                break;
            case 'animation-2-bl':
                // Slide Accross from left
                jQuery(wplc_window_id).animate({'left' : '20px'}, 1000);
                break;
            case 'animation-2-br':
                // Slide Accross from right
                jQuery(wplc_window_id).animate({'right' : '20px'}, 1000);
                break;
            case 'animation-2-l':
                // Slide Accross from left
                jQuery(wplc_window_id).animate({"left" : '0px'}, 1000);
                break;
            case 'animation-2-r':

                // Slide Accross from right
                jQuery(wplc_window_id).animate({'right' : '0px'}, 1000);
                break;
            case 'animation-3':
                // Fade In
                jQuery(wplc_window_id).fadeIn('slow');
            case 'animation-4':
                jQuery(wplc_window_id).css('display', 'block');
                break;
            default:
                jQuery(wplc_window_id).css('display', 'block');
                break;
        }
                       
        //jQuery("#wp-live-chat").css({ "display" : "block" });
        if(jQuery("#wp-live-chat").attr('wplc-auto-pop-up') === "1"){

            open_chat(0);
        }

        jQuery.event.trigger({type: "wplc_animation_done"});
    }


    function wplc_sound(source,volume,loop) {
        this.source=source;
        this.volume=volume;
        this.loop=loop;
        var son;
        this.son=son;
        this.finish=false;
        this.stop=function()
        {
            document.body.removeChild(this.son);
        }
        this.start=function()
        {
            if(this.finish)return false;
            this.son=document.createElement("embed");
            this.son.setAttribute("src",this.source);
            this.son.setAttribute("hidden","true");
            this.son.setAttribute("volume",this.volume);
            this.son.setAttribute("autostart","true");
            this.son.setAttribute("loop",this.loop);
            document.body.appendChild(this.son);
        }
        this.remove=function()
        {
            document.body.removeChild(this.son);
            this.finish=true;
        }
        this.init=function(volume,loop)
        {
            this.finish=false;
            this.volume=volume;
            this.loop=loop;
        }
    }
    
    
     
    //placeholder text fix for IE
    jQuery('#wp-live-chat [placeholder]').focus(function() {
        var input = jQuery(this);
        if (input.val() == input.attr('placeholder')) {
            input.val('');
            input.removeClass('placeholder');
        }
    }).blur(function() {
        var input = jQuery(this);
        if (input.val() == '' || input.val() == input.attr('placeholder')) {
            input.addClass('placeholder');
            input.val(input.attr('placeholder'));
        }
    }).blur();
        
   
        /* minimize chat window */
        jQuery("body").on("click", "#wp-live-chat-minimize", function() {
            
            jQuery.event.trigger({type: "wplc_minimize_chat"});
            
            
        });


         /* close chat window */
        jQuery("body").on("click", "#wp-live-chat-close", function() {
            
            jQuery("#wp-live-chat").hide();
            jQuery("#wp-live-chat-1").hide();
            jQuery("#wp-live-chat-2").hide();
            jQuery("#wp-live-chat-3").hide();
            jQuery("#wp-live-chat-4").hide();
            jQuery("#wplc_social_holder").hide();
            jQuery("#nifty_ratings_holder").hide();
            jQuery("#wp-live-chat-react").hide();
            jQuery("#wp-live-chat-minimize").hide();
            if (typeof wplc_hide_chat !== "undefined" && wplc_hide_chat !== "" && wplc_hide_chat !== null) { Cookies.set('wplc_hide', wplc_hide_chat , { expires: 1, path: '/' });  } else {
                var wplc_expire_date = new Date();
                var minutes = 2;
                wplc_expire_date.setTime(wplc_expire_date.getTime() + (minutes * 60 * 1000));
                Cookies.set('wplc_hide', "yes" , { expires: wplc_expire_date , path: '/' });
            }
            var data = {
                action: 'wplc_user_close_chat',
                security: wplc_nonce,
                cid: wplc_cid,
                status: wplc_chat_status
            };
            jQuery.post(wplc_ajaxurl, data, function(response) {


            });            
        });  
        //open chat window function
         
        function open_chat(force){
            jQuery.event.trigger({type: "wplc_open_chat_1"});


            
            wplc_chat_status = Cookies.get('wplc_chat_status');

            /**
             *  double check we have a cookie. If not, set to 5 so that the chat box can atleast restart
             *  https://github.com/CodeCabin/wp-live-chat-support/issues/313
            */
            if( wplc_chat_status == null || wplc_chat_status == 'null' ){
                 Cookies.set('wplc_chat_status', 5, { expires: 1, path: '/' });
                 wplc_chat_status = 5;
            }


            if (parseInt(wplc_chat_status) == 3 || parseInt(wplc_chat_status) == 2 || parseInt(wplc_chat_status) == 0 || parseInt(wplc_chat_status) == 12) {

                jQuery.event.trigger({type: "wplc_open_chat_2", wplc_online: wplc_online});

                Cookies.set('wplc_had_chat', true, { path: '/' });


                wplc_send_welcome_message();

                if (parseInt(wplc_chat_status) == 0 || parseInt( wplc_chat_status ) == 11 || parseInt(wplc_chat_status) == 12) {
                    /* user was a missed chat, now lets change them back to "pending" */
                    wplc_chat_status = 2;
                }
                if(typeof wplc_start_chat_pro_custom_fields_filter !== "undefined" && typeof wplc_start_chat_pro_custom_fields_filter === "function"){
                    wplc_extra_data = wplc_start_chat_pro_custom_fields_filter(wplc_extra_data);
                }
            
                if (wplc_online) {
                    var data = {
                        action: 'wplc_user_maximize_chat',
                        security: wplc_nonce,
                        cid: wplc_cid,
                        chat_status : parseInt(wplc_chat_status),
                        wplc_extra_data:wplc_extra_data
                    };
                    jQuery.post(wplc_ajaxurl, data, function(response) {
                        
                            //log("user maximized chat success");
                    });
                }
                
            }
            else if (parseInt(wplc_chat_status) == 10) {
                    jQuery("#wp-live-chat-minimize").trigger("click");
                
            }
            
            else if (wplc_chat_status == 5 || wplc_chat_status == 9 || wplc_chat_status == 8){                
                if(jQuery("#wp-live-chat-2").is(":visible") === false && jQuery("#wp-live-chat-4").is(":visible") === false){
                    jQuery("#wp-live-chat-2").show();    
                    jQuery("#wp-live-chat-2-inner").show();         
                    var wplc_visitor_name = Cookies.get('wplc_name');           
                    if(Cookies.get('wplc_email') !== "no email set" && typeof wplc_visitor_name !== "undefined"){
                        jQuery("#wplc_name").val(Cookies.get('wplc_name'));
                        jQuery("#wplc_email").val(Cookies.get('wplc_email'));
                    }
                    jQuery("#wp-live-chat-header").addClass("active");
                }
            }
            /*else if (wplc_chat_status == 2){
                jQuery("#wp-live-chat-3").show();
            } */
            else if(wplc_chat_status == 1){
                jQuery("#wp-live-chat-4").show();
                jQuery("#wplc_social_holder").show();
                jQuery("#nifty_ratings_holder").show();
                jQuery.event.trigger({type: "wplc_animation_done"});
                jQuery("#wplc_chatbox").append(wplc_error_messages.chat_ended_by_operator+"<br />");
                wplc_scroll_to_bottom();
                jQuery("#wp-live-chat-minimize").hide();
                document.getElementById('wplc_chatmsg').disabled = true;
            }   

            wplc_is_chat_open = true;

               

            
        }

        //allows for a class to open chat window now
        jQuery("body").on("click", ".wp-live-chat-now", function() {
            open_chat(0);
        });
        
        jQuery(document).on("wplc_minimize_chat", function() {
            wplc_is_minimized = true;

            Cookies.set('wplc_minimize', "yes", { expires: 1, path: '/' });
            wplc_chat_status = Cookies.get('wplc_chat_status');
            
            if(typeof wplc_start_chat_pro_custom_fields_filter !== "undefined" && typeof wplc_start_chat_pro_custom_fields_filter === "function"){
                wplc_extra_data = wplc_start_chat_pro_custom_fields_filter(wplc_extra_data);
            }

            if(wplc_chat_status != 5 && wplc_chat_status != 10 && wplc_chat_status != 9 && wplc_chat_status != 8){
                if (wplc_online) {
                    var data = {
                        action: 'wplc_user_minimize_chat',
                        security: wplc_nonce,
                        cid: wplc_cid,
                        wplc_extra_data:wplc_extra_data
                    };
                    
                    jQuery.post(wplc_ajaxurl, data, function(response) {

                    });
                }
            }

            if (typeof wplc_enable_ga !== "undefined" && wplc_enable_ga === '1' && wplc_online) {
                if (typeof ga !== "undefined") {
                    ga('send', {
                      hitType: 'event',
                      eventCategory: 'WP_Live_Chat_Support',
                      eventAction: 'Event',
                      eventLabel: 'Minimize Chat'
                    });
                }
            }
        });
        jQuery(document).on("wplc_start_chat", function() {
            if (typeof wplc_enable_ga !== "undefined" && wplc_enable_ga === '1') {
                if (typeof ga !== "undefined") {
                    ga('send', {
                      hitType: 'event',
                      eventCategory: 'WP_Live_Chat_Support',
                      eventAction: 'Event',
                      eventLabel: 'Start Chat'
                    });
                }
            }
        });        
        jQuery(document).on("wplc_open_chat_1", function() {
            if (typeof wplc_enable_ga !== "undefined" && wplc_enable_ga === '1') {
                if (typeof ga !== "undefined") {
                    ga('send', {
                      hitType: 'event',
                      eventCategory: 'WP_Live_Chat_Support',
                      eventAction: 'Event',
                      eventLabel: 'Start Chat - Step 1'
                    });
                }
            }
        });        
        jQuery(document).on("wplc_open_chat_2", function() {
            if (typeof wplc_enable_ga !== "undefined" && wplc_enable_ga === '1') {
                if (typeof ga !== "undefined") {
                    ga('send', {
                      hitType: 'event',
                      eventCategory: 'WP_Live_Chat_Support',
                      eventAction: 'Event',
                      eventLabel: 'Start Chat - Step 2'
                    });
                }
            }
        });        

        jQuery(document).on("wplc_agent_joined", function(e) {
            var temail = '';
            var tname = '';
            var taid = '';
            var ta_tagline = '';
            var ta_bio = '';

            if (typeof e.ndata.other.email !== "undefined") { temail = e.ndata.other.email; }
            if (typeof e.ndata.other.name !== "undefined") { tname = e.ndata.other.name; }
            if (typeof e.ndata.other.aid !== "undefined") { taid = e.ndata.other.aid; }
            if (typeof e.ndata.other.agent_tagline !== "undefined") { ta_tagline = e.ndata.other.agent_tagline; }
            if (typeof e.ndata.other.agent_bio !== "undefined") { ta_bio = e.ndata.other.agent_bio; }
            wplc_current_agent = e.ndata.other;

            jQuery(".wplc_no_answer").remove();

            jQuery(".admin_chat_name").html(tname);
            wplc_node_pair_name = tname;
            wplc_agent_name = tname;
        });
       

        jQuery("body").on("click", "#wplc_start_chat_btn", function() {
            var wplc_name = jQuery("#wplc_name").val();
            var wplc_email = jQuery("#wplc_email").val(); 
            
            if (wplc_name.length <= 0) { alert(wplc_error_messages.valid_name); return false; }
            if (wplc_email.length <= 0) { alert(wplc_error_messages.valid_email); return false; }

            if(jQuery("#wplc_email").attr('wplc_hide') !== "1"){
                var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,12}$/i;
                
                //var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,4}$/i;
                if (!testEmail.test(wplc_email)){
                    alert(wplc_error_messages.valid_email); return false;
                }
            }
            document.getElementById('wplc_chatmsg').disabled = false;

            /* start the long polling */
            wplc_run = true;

            wplc_send_welcome_message();
            wplc_scroll_to_bottom();
            
            /*
            if (wplc_filter_run_override === "1" || wplc_online === false) { } else {
                initial_data.status = 2;
                // force the loop to start only now, as we are not using the initiate extension
                wplc_call_to_server_chat(initial_data,false,false);
            }*/

            jQuery.event.trigger({type: "wplc_start_chat"});
           
            
            var date = new Date();
            date.setTime(date.getTime() + (2 * 60 * 1000));
            
            wplc_cid = Cookies.get('wplc_cid');

            if(typeof wplc_start_chat_pro_custom_fields_filter !== "undefined" && typeof wplc_start_chat_pro_custom_fields_filter === "function"){
                wplc_extra_data = wplc_start_chat_pro_custom_fields_filter(wplc_extra_data);
            }
            
            if (typeof wplc_cid !== "undefined" && wplc_cid !== null) { 
                /* we've already recorded a cookie for this person */
                var data = {
                        action: 'wplc_start_chat',
                        security: wplc_nonce,
                        name: wplc_name,
                        email: wplc_email,
                        cid: wplc_cid,
                        wplcsession: wplc_session_variable,
                        wplc_extra_data:wplc_extra_data                        
                };

                if(typeof wplc_start_chat_pro_data !== "undefined" && typeof wplc_start_chat_pro_data === "function"){
                    data = wplc_start_chat_pro_data(data);
                }   
            } else { // no cookie recorded yet for this visitor
                var data = {
                        action: 'wplc_start_chat',
                        security: wplc_nonce,
                        name: wplc_name,
                        email: wplc_email,
                        wplcsession: wplc_session_variable,
                        wplc_extra_data:wplc_extra_data                        
                };
                
                if(typeof wplc_start_chat_pro_data !== "undefined" && typeof wplc_start_chat_pro_data === "function"){
                    data = wplc_start_chat_pro_data(data);
                }   
            }

            Cookies.set('wplc_name', wplc_name, { path: '/' } );
            Cookies.set('wplc_email', wplc_email, { path: '/' } );

            wplc_server.send(wplc_ajaxurl, data, "POST", 120000, 
                function(response){
                    wplc_chat_status = 2;
                    Cookies.set('wplc_chat_status', 2, { expires: date, path: '/' });
                    wplc_cid = jQuery.trim(response);

                    //All sorted, let's check for message transport mode
                    wplc_server.prepareTransport(function(){
                        //Transport ready...
                        wplc_server_last_loop_data.status = 2; //Set to waiting
                        if (wplc_filter_run_override === "1" || wplc_online === false) {  } else {
                            wplc_call_to_server_chat(wplc_server_last_loop_data);
                        }
                    }, wplc_user_message_receiver, wplc_user_retry_handler, wplc_log_connection_error);
                },
                function(){
                    //Fails
                },
                function(response, wplc_send_data){
                    //Complete
                    if (typeof wplc_send_data !== "undefined" && typeof wplc_send_data['action'] !== "undefined" && wplc_send_data['action'] == 'wplc_start_chat') {
                        /* we got here because the short poll  (when disabling the initiate chat feature) comes back on the "complete" callback. This check makes sure we restart the longpoll */
                        wplc_chat_status = 2;
                        Cookies.set('wplc_chat_status', 2, { expires: date, path: '/' });
                        wplc_cid = jQuery.trim(response);

                        //All sorted, let's check for message transport mode
                        wplc_server.prepareTransport(function(){
                            //Transport ready...
                            wplc_server_last_loop_data.status = 2; //Set to waiting
                            if (wplc_filter_run_override === "1" || wplc_online === false) {  } else {
                                wplc_call_to_server_chat(wplc_server_last_loop_data);
                            }
                        }, wplc_user_message_receiver, wplc_user_retry_handler, wplc_log_connection_error);
                    }
                    
                }
            );
        });


        jQuery("body").on("click", "#wplc_na_msg_btn", function() {
            var wplc_name = jQuery("#wplc_name").val();
            var wplc_email = jQuery("#wplc_email").val();
            var wplc_msg = jQuery("#wplc_message").val();
            var wplc_domain = jQuery("#wplc_domain_offline").val();
            var ip_address = jQuery("#wplc_ip_address").val();
            
            if (wplc_name.length <= 0) { alert(wplc_error_messages.valid_name); return false; }
            if (wplc_email.length <= 0) { alert(wplc_error_messages.valid_email); return false; }
            var testEmail = /^[A-Z0-9._%+-]+@([A-Z0-9-]+\.)+[A-Z]{2,12}$/i;
            if (!testEmail.test(wplc_email)){
                alert(wplc_error_messages.valid_email); return false;
            }
            if (wplc_msg.length <= 0) { alert(wplc_error_messages.empty_message); return false; }
            jQuery("#wp-live-chat-2-info").hide();
            jQuery("#wplc_message_div").html(wplc_offline_msg);

            wplc_cid = Cookies.get('wplc_cid');
                            
            var data = {
                    action: 'wplc_user_send_offline_message',
                    security: wplc_nonce,
                    cid: wplc_cid,
                    name: wplc_name,
                    email: wplc_email,
                    msg: wplc_msg,
                    ip: ip_address,
                    domain: wplc_domain,
                    wplc_extra_data:wplc_extra_data
            };
            
            jQuery.post(wplc_ajaxurl_site, data, function(response) {
                jQuery("#wplc_message_div").html(wplc_offline_msg3);
            });
            if (typeof wplc_enable_ga !== "undefined" && wplc_enable_ga === '1') {
                if (typeof ga !== "undefined") {
                    ga('send', {
                      hitType: 'event',
                      eventCategory: 'WP_Live_Chat_Support',
                      eventAction: 'Event',
                      eventLabel: 'User Send Offline Message'
                    });
                }
            }
        });
        

        function wplc_strip(str) {
            str=str.replace(/<br>/gi, "\n");
            str=str.replace(/<p.*>/gi, "\n");
            str=str.replace(/<a.*href="(.*?)".*>(.*?)<\/a>/gi, " $2 ($1) ");
            str=str.replace(/<(?:.|\s)*?>/g, "");

            str=str.replace('iframe', "");    
            str=str.replace('src', "");    
            str=str.replace('href', "");  
            str=str.replace('<', "");  
            str=str.replace('>', "");  

            /* remove the last carriage return from the text area output */
            str=str.replace(/\n$/, "")

            return str;
        }


        jQuery("body").on("keyup","#wplc_chatmsg", function(event){
            if(event.keyCode === 13){
                jQuery("#wplc_send_msg").trigger("click");
            }
        });
        jQuery("body").on("click", "#wplc_send_msg", function() {
            var wplc_cid = jQuery("#wplc_cid").val();
            if (wplc_cid.length < 1) {
                /* failover for wplc_cid */
                var wplc_cid = Cookies.get('wplc_cid');
            }
            var wplc_chat = wplc_strip(document.getElementById('wplc_chatmsg').value);

            if(wplc_chat !== ""){
                var wplc_name = jQuery("#wplc_name").val();
                if (typeof wplc_name == "undefined" || wplc_name == null || wplc_name == "") {
                    wplc_name = Cookies.get('wplc_name');
                }
                if (typeof wplc_name == "undefined") {
                    wplc_name = 'Guest';
                }

                var wplc_email = jQuery("#wplc_email").val();
                if (typeof wplc_email == "undefined" || wplc_email == null || wplc_email == "") {
                    wplc_email = Cookies.get('wplc_email');
                }
                if (typeof wplc_email == "undefined") {
                    wplc_email = '';
                }


                

                /*Nifty format Parse*/
                var wplc_chat_parsed = wplc_chat;
                
                //if(typeof niftyFormatParser !== "undefined"){
                //    wplc_chat_parsed = niftyFormatParser(wplc_chat_parsed);
                //}
            
                if( typeof wplc_display_name !== 'undefined' ){
                    /**
                     * We're still using the old options
                     */
                    if(wplc_display_name == 'display'){
                        if (wplc_gravatar_image.length > 1) {
                            jQuery("#wplc_chatbox").append("<span class='wplc-user-message wplc-color-bg-1 wplc-color-2 wplc-color-border-1'>"+wplc_gravatar_image+" <strong>"+wplc_name+"</strong>: "+wplc_chat_parsed+"</span><br /><div class='wplc-clear-float-message'></div>");
                        } else {
                            jQuery("#wplc_chatbox").append("<span class='wplc-user-message wplc-color-bg-1 wplc-color-2 wplc-color-border-1'><img src='//www.gravatar.com/avatar/"+md5(wplc_email)+"?s=30' class='wplc-user-message-avatar' \/> <strong>"+wplc_name+"</strong>: "+wplc_chat_parsed+"</span><br /><div class='wplc-clear-float-message'></div>");
                        }
                    } else {
                        jQuery("#wplc_chatbox").append("<span class='wplc-user-message wplc-color-bg-1 wplc-color-2 wplc-color-border-1'>"+wplc_chat_parsed+"</span><div class='wplc-clear-float-message'></div>");
                    }
                } else {
                    the_message = {};
                    the_message.originates = 2;
                    the_message.msg = wplc_chat_parsed;
                    the_message.other = {};
                    var wplc_d = new Date();
                    the_message.other.datetime = Math.round( wplc_d.getTime() / 1000 );
                    wplc_push_message_to_chatbox(the_message,'u', function() {
                        wplc_scroll_to_bottom();    
                    });

                   
                }
                
                wplc_scroll_to_bottom();
                

                var data = {
                        action: 'wplc_user_send_msg',
                        security: wplc_nonce,
                        cid: wplc_cid,
                        msg: wplc_chat_parsed,
                        wplc_extra_data:wplc_extra_data
                };
               
                wplc_server.sendMessage(wplc_ajaxurl, data, "POST", 120000, 
                    function(){
                        //Success
                        wplc_server.asyncStorage(wplc_ajaxurl, data, 120000);
                    }, function(){
                        //Fail
                    }, function(){
                        //Complete
                    }
                );
                
                if (typeof wplc_enable_ga !== "undefined" && wplc_enable_ga === '1') {
                    if (typeof ga !== "undefined") {
                        ga('send', {
                          hitType: 'event',
                          eventCategory: 'WP_Live_Chat_Support',
                          eventAction: 'Event',
                          eventLabel: 'User Send Message'
                        });
                    }
                }
            }

            jQuery("#wplc_chatmsg").val('');


        });   

        jQuery(document).on("wplc_open_chat", function (event) {
            /* what is the current status? */
            wplc_chat_status = Cookies.get('wplc_chat_status');            
            if( typeof wplc_chat_status === 'undefined' ){
                Cookies.set('wplc_chat_status', 5, { expires: 1, path: '/' });
            }
            var wplc_tmp_checker = wplc_pre_open_check_status(status, function() {
                open_chat();
            });
        }); 

        jQuery(document).on("wplc_end_chat", function(){
            /* Clear Cookies */
            Cookies.remove('wplc_chat_status');
            Cookies.remove('wplc_cid');
            //Cookies.remove('wplc_name');
            //Cookies.remove('wplc_email');

            /* Close ports if applicable*/
            wplc_server.forceClosePort();

            /* Check if we should redirect */
            if(typeof wplc_redirect_thank_you !== "undefined" && wplc_redirect_thank_you !== null && wplc_redirect_thank_you !== ""){
                window.location = wplc_redirect_thank_you;
            }
        });

        function wplc_pre_open_check_status(status, callback) {
            if (typeof wplc_chat_status !== 'undefined' && ( typeof wplc_chat_status.length !== 'undefined' && wplc_chat_status.length > 0 ) ) {
                if (parseInt(wplc_chat_status) === 10 || parseInt(wplc_chat_status) === 7) {
                    /* it was minimized or timedout, now we need to open it - set status to 3 (back to open chat) */
                    Cookies.set('wplc_chat_status', 3, { expires: 1, path: '/' });

                }
                if (parseInt(wplc_chat_status) === 0 || parseInt(wplc_chat_status) === 12) {
                    /* no answer from agent previously */
                    // Cookies.set('wplc_chat_status', 5, { expires: 1, path: '/' });                    
                }
                if (parseInt(wplc_chat_status) === 8) {
                    /* no answer from agent previously */
                    cnonsole.log("now setting it to 5");
                    Cookies.set('wplc_chat_status', 5, { expires: 1, path: '/' });                    
                }
               
            }
            callback();
        }

        if(typeof wplc_elem_trigger_id !== "undefined" && wplc_elem_trigger_id !== ""){
            var wplc_click_or_hover = 0;
            var wplc_class_or_id = 0;

            if(typeof wplc_elem_trigger_action !== "undefined" && wplc_elem_trigger_action !== ""){ wplc_click_or_hover = parseInt(wplc_elem_trigger_action); }
            if(typeof wplc_elem_trigger_type !== "undefined" && wplc_elem_trigger_type !== ""){ wplc_class_or_id = parseInt(wplc_elem_trigger_type); }
            
            jQuery( (wplc_class_or_id === 1 ? "#" : ".") + wplc_elem_trigger_id).on( (wplc_click_or_hover === 1 ? "mouseenter" : "click"), function(){
                open_chat(0);
            });
        }

    // Fix conflict with Responsive Lighbox plugin
    setTimeout(function () {
        if (jQuery('html').hasClass('nivo-lightbox-notouch') || jQuery('a[rel*="lightbox"]').length) {
            jQuery("body").on("keyup", function (event) {
                if (event.keyCode === 13) {
                    jQuery("#wplc_send_msg").trigger("click");
                }
            });
        }
    }, 5000);


});
