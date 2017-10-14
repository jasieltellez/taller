<?php
/*
 * Adds beta/opt-on options
*/

add_filter("wplc_filter_setting_tabs","wplc_beta_settings_tab_heading");
/**
 * Adds 'Beta Features' tab to settings area
*/
function wplc_beta_settings_tab_heading($tab_array) {
    $tab_array['beta'] = array(
      "href" => "#tabs-beta",
      "icon" => 'fa fa-bug',
      "label" => __("Beta Features","wplivechat")
    );
    return $tab_array;
}


add_action("wplc_hook_settings_page_more_tabs","wplc_beta_settings_tab_content");
/**
 * Adds 'Beta Features' content to settings area
*/
function wplc_beta_settings_tab_content() {
  $wplc_settings = get_option("WPLC_SETTINGS"); 

  if(isset($_GET['wplc_action']) && $_GET['wplc_action'] === "node_server_new_token"){
     if(function_exists("wplc_node_server_token_regenerate")){
        wplc_node_server_token_regenerate();
     }
  }

  $wplc_node_token = get_option("wplc_node_server_secret_token");

  if(!$wplc_node_token){
    $wplc_node_token = __("No token found", "wplivechat") . "...";
  }

  ?>
    <div id="tabs-beta">
      <h4><?php _e("Node Server", "wplivechat") ?></h4>
      <?php if (function_exists("wplc_cloud_load_updates")) { echo "<p><span class='update-nag'>".__('The node server cannot be activated while using the Cloud extension as they are not compatible. Please deactivate the cloud extension to make use of the new Node server.','wplivechat')."</span></p>"; } ?>
      <table class="wp-list-table widefat fixed striped pages">
        <tbody>
          <tr>
            <td width="250" valign="top">
              <label for="wplc_use_node_server"><?php _e("Use our Node Server (beta)","wplivechat"); ?> <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e('Opt-in to our Node Server (beta) program, and experience realtime chats with minimal impact on your site resources. Please note this is experimental.', 'wplivechat'); ?>"></i></label>
            </td>
            <td valign="top">
              <input type="checkbox" value="1" name="wplc_use_node_server" <?php if (function_exists("wplc_cloud_load_updates")) { echo 'disabled="disabled" readonly="readonly"'; } ?> <?php if (isset($wplc_settings['wplc_use_node_server']) && $wplc_settings['wplc_use_node_server'] == '1') { echo "checked"; } ?>> 
            </td>
          </tr>

          <tr>
            <td width="250" valign="top">
              <label for="wplc_use_node_server"><?php _e("Node Server Token (beta)","wplivechat"); ?> <i class="fa fa-question-circle wplc_light_grey wplc_settings_tooltip" title="<?php _e('Security token for accessing chats on the node server. Changing this will remove current chats', 'wplivechat'); ?>"></i></label>
            </td>
            <td valign="top">
              <input type="text" value="<?php echo $wplc_node_token; ?>" readonly> <a class="button button-secondary" href="?page=wplivechat-menu-settings&wplc_action=node_server_new_token"><?php _e("Generate New", "wplivechat"); ?></a>
            </td>
          </tr>

        </tbody>
      </table>
  <?php

  do_action("wplc_hook_beta_options_content");

  ?>
  </div>
  <?php
}

add_filter("wplc_settings_save_filter_hook", "wplc_beta_settings_save_hooked", 10, 1);
/**
 * Save 'Beta Features' settings
*/
function wplc_beta_settings_save_hooked($wplc_data){
  if( function_exists( 'wplc_cloud_load_updates' ) ){
    /** Cloud Server is active - this must not be enabled at all */
    $wplc_data['wplc_use_node_server'] = 0;
  } else {
    if (isset($_POST['wplc_use_node_server'])) { $wplc_data['wplc_use_node_server'] = esc_attr($_POST['wplc_use_node_server']); }
  }  
  return $wplc_data;
}