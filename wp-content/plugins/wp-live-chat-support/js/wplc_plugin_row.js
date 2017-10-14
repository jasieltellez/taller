jQuery(document).ready(function(){

    jQuery("body").on("click", "#wplc_signup_newsletter_btn", function() {

        var a_email = jQuery("#wplc_signup_newsletter").val();
        jQuery("#wplc_signup_newsletter").hide('slow');
        jQuery("#wplc_signup_newsletter_btn").hide('slow');
        jQuery("#wplc_signup_newsletter_hide").hide('slow'); 
        jQuery("#wplc_subscribe_div").html("Thank you!");
        var data = {
            action: 'wplc_subscribe',
            prod: 'wplcs',
            a_email: a_email
            
        };
        jQuery.post('//ccplugins.co/newsletter-subscription/index.php', data, function(response) {
            returned_data = JSON.parse(response);
            
        });

        var data = {
            action: 'wplc_subscribe_hide',
            security: wplc_sub_nonce
            
        };
        jQuery.post(ajaxurl, data, function(response) {
            
        });


    });

    jQuery("body").on("click", "#wplc_signup_newsletter_hide", function() {

        jQuery(".wplc_sub_div").hide("fast");
        jQuery("#wplc_signup_newsletter").hide('slow');
        jQuery("#wplc_signup_newsletter_btn").hide('slow');
        jQuery("#wplc_signup_newsletter_hide").hide('slow');

        var data = {
            action: 'wplc_subscribe_hide',
            security: wplc_sub_nonce
        };
        
        jQuery.post(ajaxurl, data, function(response) {

        });

    });    
    
});
