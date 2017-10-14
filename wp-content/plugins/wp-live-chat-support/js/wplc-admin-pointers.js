var wp_button_pointer_array = new Array();
wp_button_pointer_array[1] = {
    'element' : 'wplc_initiate_chat',
    'options' : {
        'content': pointer_localize_strings["initiate"], 
        'position': {'edge': 'right', 'align': 'middle'} 
    } 
}; 
wp_button_pointer_array[2] = {
    'element' : 'wplc-agent-info',
    'options' : {
        'content': pointer_localize_strings["agent_info"], 
        'position': {'edge': 'right', 'align': 'middle'} 
    } 
}; 
wp_button_pointer_array[3] = {
    'element' : 'wplc_second_chat_request',
    'options' : {
        'content': pointer_localize_strings["chats"], 
        'position': {'edge': 'right', 'align': 'middle'} 
    } 
}; 


jQuery(document).ready( function($) {

    jQuery('body').on("click", ".wplc_initiate_chat", function (e) {
        e.preventDefault();	
        if(typeof(jQuery().pointer) != 'undefined') { // make sure the pointer class exists

            if(jQuery('.wp-pointer').is(":visible")) { // if a pointer is already open...
                var openid = jQuery('.wp-pointer:visible').attr("id").replace('wp-pointer-',''); // ... note its id
                jQuery('#' + wp_button_pointer_array[2].element).pointer('close'); // ... and close it
                jQuery('#' + wp_button_pointer_array[3].element).pointer('close'); // ... and close it
                var pointerid = parseInt(openid) + 1;
            } else {
                var pointerid = 1; // ... otherwise we want to open the first pointer
            }

            if(wp_button_pointer_array[pointerid] != undefined) { // check if next pointer exists
                jQuery('#' + wp_button_pointer_array[1].element).pointer(wp_button_pointer_array[1].options).pointer('open');
                var nextid = pointerid + 1;
            }
        }
    });	
    
    jQuery('body').on("click", ".wplc-agent-info", function (e) {
        e.preventDefault();	
        if(typeof(jQuery().pointer) != 'undefined') { // make sure the pointer class exists

            if(jQuery('.wp-pointer').is(":visible")) { // if a pointer is already open...
                var openid = jQuery('.wp-pointer:visible').attr("id").replace('wp-pointer-',''); // ... note its id
                jQuery('#' + wp_button_pointer_array[1].element).pointer('close'); // ... and close it
                jQuery('#' + wp_button_pointer_array[3].element).pointer('close'); // ... and close it
                var pointerid = parseInt(openid) + 1;
            } else {
                var pointerid = 1; // ... otherwise we want to open the first pointer
            }

            if(wp_button_pointer_array[pointerid] != undefined) { // check if next pointer exists
                jQuery('#' + wp_button_pointer_array[2].element).pointer(wp_button_pointer_array[2].options).pointer('open');
                var nextid = pointerid + 1;
            }
        }
    });

    jQuery('body').on("click", ".wplc_second_chat_request", function (e) {
        e.preventDefault(); 
        if(typeof(jQuery().pointer) != 'undefined') { // make sure the pointer class exists

            if(jQuery('.wp-pointer').is(":visible")) { // if a pointer is already open...
                var openid = jQuery('.wp-pointer:visible').attr("id").replace('wp-pointer-',''); // ... note its id
                jQuery('#' + wp_button_pointer_array[2].element).pointer('close'); // ... and close it
                jQuery('#' + wp_button_pointer_array[1].element).pointer('close'); // ... and close it
                var pointerid = parseInt(openid) + 1;
            } else {
                var pointerid = 1; // ... otherwise we want to open the first pointer
            }

            if(wp_button_pointer_array[pointerid] != undefined) { // check if next pointer exists
                jQuery('.' + wp_button_pointer_array[3].element).pointer(wp_button_pointer_array[3].options).pointer('open');
                var nextid = pointerid + 1;
            }
        }
    }); 

    
});