jQuery(document).ready(function () {

    jQuery("body").on("click", ".wplc_delete_message", function(e){

        var message_id = jQuery(this).attr('mid');

        var data = {
            action: 'delete_offline_message',
            security: wplc_ajax_nonce,
            mid: message_id
        }

        jQuery.post( wplc_ajaxurl, data, function( response ){

            if( response ){

                jQuery('#record_'+message_id).fadeOut(700);

            }


        });

    });

});