<?php
/**
 * This file is used for managing data in database.
 *
 * @author  Tech Banker
 * @package wp-mail-bank/lib
 * @version 2.0.0
 */
if (!defined("ABSPATH")) {
   exit;
}// Exit if accessed directly
if (!is_user_logged_in()) {
   return;
} else {
   $access_granted = false;
   foreach ($user_role_permission as $permission) {
      if (current_user_can($permission)) {
         $access_granted = true;
         break;
      }
   }
   if (!$access_granted) {
      return;
   } else {

      if (isset($_REQUEST["param"])) {
         $obj_dbHelper_mail_bank = new dbHelper_mail_bank();
         switch (esc_attr($_REQUEST["param"])) {
            case "wizard_wp_mail_bank":
               if (wp_verify_nonce(isset($_REQUEST["_wp_nonce"]) ? $_REQUEST["_wp_nonce"] : "", "wp_mail_bank_check_status")) {
                  $type = isset($_REQUEST["type"]) ? sanitize_text_field($_REQUEST["type"]) : "";
                  update_option("mail-bank-welcome-page", $type);
                  if ($type == "opt_in") {
                     $plugin_info_wp_mail_bank = new plugin_info_wp_mail_bank();

                     global $wp_version;

                     $url = tech_banker_stats_url . "/wp-admin/admin-ajax.php";

                     $theme_details = array();
                     if ($wp_version >= 3.4) {
                        $active_theme = wp_get_theme();
                        $theme_details["theme_name"] = strip_tags($active_theme->Name);
                        $theme_details["theme_version"] = strip_tags($active_theme->Version);
                        $theme_details["author_url"] = strip_tags($active_theme->{"Author URI"});
                     }

                     $plugin_stat_data = array();
                     $plugin_stat_data["plugin_slug"] = "wp-mail-bank";
                     $plugin_stat_data["type"] = "standard_edition";
                     $plugin_stat_data["version_number"] = mail_bank_version_number;
                     $plugin_stat_data["status"] = $type;
                     $plugin_stat_data["event"] = "activate";
                     $plugin_stat_data["domain_url"] = site_url();
                     $plugin_stat_data["wp_language"] = defined("WPLANG") && WPLANG ? WPLANG : get_locale();
                     $plugin_stat_data["email"] = get_option("admin_email");
                     $plugin_stat_data["wp_version"] = $wp_version;
                     $plugin_stat_data["php_version"] = esc_html(phpversion());
                     $plugin_stat_data["mysql_version"] = $wpdb->db_version();
                     $plugin_stat_data["max_input_vars"] = ini_get("max_input_vars");
                     $plugin_stat_data["operating_system"] = PHP_OS . "  (" . PHP_INT_SIZE * 8 . ") BIT";
                     $plugin_stat_data["php_memory_limit"] = ini_get("memory_limit") ? ini_get("memory_limit") : "N/A";
                     $plugin_stat_data["extensions"] = get_loaded_extensions();
                     $plugin_stat_data["plugins"] = $plugin_info_wp_mail_bank->get_plugin_info_wp_mail_bank();
                     $plugin_stat_data["themes"] = $theme_details;
                     $response = wp_safe_remote_post($url, array
                         (
                         "method" => "POST",
                         "timeout" => 45,
                         "redirection" => 5,
                         "httpversion" => "1.0",
                         "blocking" => true,
                         "headers" => array(),
                         "body" => array("data" => serialize($plugin_stat_data), "site_id" => get_option("mb_tech_banker_site_id") != "" ? get_option("mb_tech_banker_site_id") : "", "action" => "plugin_analysis_data")
                     ));

                     if (!is_wp_error($response)) {
                        $response["body"] != "" ? update_option("mb_tech_banker_site_id", $response["body"]) : "";
                     }
                  }
               }
               break;

            case "mail_bank_set_hostname_port_module":
               if (wp_verify_nonce(isset($_REQUEST["_wp_nonce"]) ? $_REQUEST["_wp_nonce"] : "", "mail_bank_set_hostname_port")) {
                  $smtp_user = isset($_REQUEST["smtp_user"]) ? sanitize_text_field($_REQUEST["smtp_user"]) : "";
                  $hostname = substr(strrchr($smtp_user, "@"), 1);
                  $obj_mail_bank_discover_host = new mail_bank_discover_host();
                  $hostname_to_set = $obj_mail_bank_discover_host->get_smtp_from_email($hostname);
                  echo $hostname_to_set;
               }
               break;

            case "mail_bank_test_email_configuration_module":
               if (wp_verify_nonce(isset($_REQUEST["_wp_nonce"]) ? $_REQUEST["_wp_nonce"] : "", "mail_bank_test_email_configuration")) {
                  parse_str(isset($_REQUEST["data"]) ? base64_decode($_REQUEST["data"]) : "", $form_data);
                  global $phpmailer;
                  $logs = array();
                  if (!is_object($phpmailer) || !is_a($phpmailer, "PHPMailer")) {
                     if (file_exists(ABSPATH . WPINC . "/class-phpmailer.php"))
                        require_once ABSPATH . WPINC . "/class-phpmailer.php";

                     if (file_exists(ABSPATH . WPINC . "/class-smtp.php"))
                        require_once ABSPATH . WPINC . "/class-smtp.php";

                     $phpmailer = new PHPMailer(true);
                  }
                  $phpmailer->SMTPDebug = true;

                  $to = isset($form_data["ux_txt_email"]) ? sanitize_text_field($form_data["ux_txt_email"]) : "";
                  $subject = stripcslashes(htmlspecialchars_decode($form_data["ux_txt_subject"], ENT_QUOTES));
                  $message = htmlspecialchars_decode(!empty($form_data["ux_email_configuration_text_area"]) ? htmlspecialchars_decode($form_data["ux_email_configuration_text_area"]) : "This is a demo Test Email for Email Setup - Mail Bank");
                  $headers = "Content-Type: text/html; charset= utf-8" . "\r\n";
                  $result = wp_mail($to, $subject, $message, $headers);
                  $mb_email_configuration_data = $wpdb->get_row
                      (
                      $wpdb->prepare
                          (
                          "SELECT meta_value FROM " . mail_bank_meta() .
                          " WHERE meta_key = %s", "email_configuration"
                      )
                  );
                  $unserialized_email_configuration_data = maybe_unserialize($mb_email_configuration_data->meta_value);

                  $settings_data = $wpdb->get_var
                      (
                      $wpdb->prepare
                          (
                          "SELECT meta_value FROM " . mail_bank_meta() .
                          " WHERE meta_key=%s", "settings"
                      )
                  );
                  $settings_data_array = maybe_unserialize($settings_data);
                  $debugging_output = "";
                  $mailer_type_mail_bank = isset($unserialized_email_configuration_data["mailer_type"]) ? sanitize_text_field($unserialized_email_configuration_data["mailer_type"]) : "";

                  if ($mailer_type_mail_bank == "smtp") {
                     $mail_bank_mail_status = get_option("mail_bank_mail_status");
                     $debug_mode_mail_bank = isset($settings_data_array["debug_mode"]) ? sanitize_text_field($settings_data_array["debug_mode"]) : "";
                     if ($debug_mode_mail_bank == "enable") {
                        $debugging_output .= $mb_email_configuration_send_test_email_textarea . "\n";
                        $debugging_output .= $mb_test_email_sending_test_email . " " . $to . "\n";
                        $debugging_output .= $mb_test_email_status . " : ";
                        $debugging_output .= get_option("mail_bank_is_mail_sent") == "Sent" ? $mb_status_sent : $mb_status_not_sent;
                        $debugging_output .= "\n----------------------------------------------------------------------------------------\n";
                        $debugging_output .= $mb_email_logs_debugging_output . " :\n";
                        $debugging_output .= "----------------------------------------------------------------------------------------\n";
                     }
                     $debugging_output .= $mail_bank_mail_status;
                     if ($settings_data_array["debug_mode"] == "enable") {
                        if (get_option("mail_bank_is_mail_sent") != "Sent") {
                           $debugging_output .= "\n\n";
                           $debugging_output .= "Your Web Host provider may have installed a firewall between you and the server.\n Contact the admin of the server and ask if they allow outgoing communication on port 25,465,587.\n It seems like they are blocking certain traffic. Ask them to open the ports.\n";
                           $debugging_output .= "----------------------------------------------------------------------------------------\n";
                        }
                     }
                     echo $debugging_output;
                  } else {
                     $to_address = $phpmailer->getToAddresses();

                     $email_logs_data_array = array();
                     $email_logs_data_array["email_to"] = $to_address[0][0];
                     $monitor_email_logs = isset($settings_data_array["monitor_email_logs"]) ? sanitize_text_field($settings_data_array["monitor_email_logs"]) : "";
                     if ($monitor_email_logs == "enable") {
                        $email_logs_data_array["sender_name"] = isset($unserialized_email_configuration_data["sender_name"]) ? sanitize_text_field($unserialized_email_configuration_data["sender_name"]) : "";
                        $email_logs_data_array["sender_email"] = isset($unserialized_email_configuration_data["sender_email"]) ? sanitize_text_field($unserialized_email_configuration_data["sender_email"]) : "";
                        $email_logs_data_array["cc"] = "";
                        $email_logs_data_array["bcc"] = "";
                        $email_logs_data_array["subject"] = $phpmailer->Subject;
                        $email_logs_data_array["content"] = $phpmailer->Body;
                        $email_logs_data_array["timestamp"] = MAIL_BANK_LOCAL_TIME;

                        if ($result == "true" || $result == "1") {
                           $email_logs_data_array["status"] = "Sent";
                        } else {
                           $email_logs_data_array["status"] = "Not Sent";
                        }
                        $email_logs_data = array();
                        $email_logs_data["email_data"] = serialize($email_logs_data_array);
                        $obj_dbHelper_mail_bank->insertCommand(mail_bank_email_logs(), $email_logs_data);
                     }
                     if ($result != "true" || $result != "1") {
                        $result .= "Your Web Host provider may have blocked the use of mail() function on your server.\n Ask them to enable the mail() function to start sending emails.\n";
                     }
                     echo $result;
                  }
               }
               break;

            case "mail_bank_settings_module":
               if (wp_verify_nonce(isset($_REQUEST["_wp_nonce"]) ? $_REQUEST["_wp_nonce"] : "", "mail_bank_settings")) {
                  parse_str(isset($_REQUEST["data"]) ? base64_decode($_REQUEST["data"]) : "", $settings_array);

                  $settings_data = array();
                  $settings_data["debug_mode"] = sanitize_text_field($settings_array["ux_ddl_debug_mode"]);
                  $settings_data["remove_tables_at_uninstall"] = sanitize_text_field($settings_array["ux_ddl_remove_tables"]);
                  $settings_data["monitor_email_logs"] = sanitize_text_field($settings_array["ux_ddl_monitor_email_logs"]);
                  $where = array();
                  $settings_data_array = array();
                  $where["meta_key"] = "settings";
                  $settings_data_array["meta_value"] = serialize($settings_data);
                  $obj_dbHelper_mail_bank->updateCommand(mail_bank_meta(), $settings_data_array, $where);
               }
               break;

            case "mail_bank_email_configuration_settings_module":
               if (wp_verify_nonce(isset($_REQUEST["_wp_nonce"]) ? $_REQUEST["_wp_nonce"] : "", "mail_bank_email_configuration_settings")) {
                  parse_str(isset($_REQUEST["data"]) ? base64_decode($_REQUEST["data"]) : "", $form_data);
                  $update_email_configuration_array = array();
                  $update_email_configuration_array["email_address"] = sanitize_text_field($form_data["ux_txt_email_address"]);
                  $update_email_configuration_array["reply_to"] = "";
                  $update_email_configuration_array["cc"] = "";
                  $update_email_configuration_array["bcc"] = "";
                  $update_email_configuration_array["mailer_type"] = sanitize_text_field($form_data["ux_ddl_type"]);
                  $update_email_configuration_array["sender_name_configuration"] = sanitize_text_field($form_data["ux_ddl_from_name"]);
                  $update_email_configuration_array["sender_name"] = isset($form_data["ux_txt_mb_from_name"]) ? esc_html($form_data["ux_txt_mb_from_name"]) : "";
                  $update_email_configuration_array["from_email_configuration"] = sanitize_text_field($form_data["ux_ddl_from_email"]);
                  $update_email_configuration_array["sender_email"] = isset($form_data["ux_txt_mb_from_email_configuration"]) ? esc_html($form_data["ux_txt_mb_from_email_configuration"]) : "";
                  $update_email_configuration_array["hostname"] = esc_html($form_data["ux_txt_host"]);
                  $update_email_configuration_array["port"] = intval($form_data["ux_txt_port"]);
                  $update_email_configuration_array["enc_type"] = sanitize_text_field($form_data["ux_ddl_encryption"]);
                  $update_email_configuration_array["auth_type"] = sanitize_text_field($form_data["ux_ddl_mb_authentication"]);
                  $update_email_configuration_array["client_id"] = esc_html(trim($form_data["ux_txt_client_id"]));
                  $update_email_configuration_array["client_secret"] = esc_html(trim($form_data["ux_txt_client_secret"]));
                  $update_email_configuration_array["username"] = esc_html($form_data["ux_txt_username"]);
                  $update_email_configuration_array["automatic_mail"] = isset($form_data["ux_chk_automatic_sent_mail"]) ? esc_html($form_data["ux_chk_automatic_sent_mail"]) : "";

                  if (preg_match('/^\**$/', $form_data["ux_txt_password"])) {
                     $email_configuration_data = $wpdb->get_var
                         (
                         $wpdb->prepare
                             (
                             "SELECT meta_value FROM " . mail_bank_meta() .
                             " WHERE meta_key=%s", "email_configuration"
                         )
                     );
                     $email_configuration_array = maybe_unserialize($email_configuration_data);
                     $update_email_configuration_array["password"] = isset($email_configuration_array["password"]) ? sanitize_text_field($email_configuration_array["password"]) : "";
                  } else {
                     $update_email_configuration_array["password"] = base64_encode(esc_html($form_data["ux_txt_password"]));
                  }

                  $update_email_configuration_array["redirect_uri"] = esc_html($form_data["ux_txt_redirect_uri"]);

                  update_option("update_email_configuration", $update_email_configuration_array);

                  $mail_bank_auth_host = new mail_bank_auth_host($update_email_configuration_array);
                  if (!in_array($form_data["ux_txt_host"], $mail_bank_auth_host->oauth_domains) && $form_data["ux_ddl_mb_authentication"] == "oauth2") {
                     echo "100";
                     die();
                  }

                  if ($update_email_configuration_array["auth_type"] == "oauth2" && $update_email_configuration_array["mailer_type"] == "smtp") {
                     if ($update_email_configuration_array["hostname"] == "smtp.gmail.com") {
                        $mail_bank_auth_host->google_authentication();
                     } elseif ($update_email_configuration_array["hostname"] == "smtp.live.com" && $update_email_configuration_array["mailer_type"] == "smtp") {
                        $mail_bank_auth_host->microsoft_authentication();
                     } elseif (in_array($update_email_configuration_array["hostname"], $mail_bank_auth_host->yahoo_domains)) {
                        $mail_bank_auth_host->yahoo_authentication();
                     }
                  } else {
                     $update_email_configuration_data_array = array();
                     $where = array();
                     $where["meta_key"] = "email_configuration";
                     $update_email_configuration_data_array["meta_value"] = serialize($update_email_configuration_array);
                     $obj_dbHelper_mail_bank->updateCommand(mail_bank_meta(), $update_email_configuration_data_array, $where);
                  }
               }
               break;

            case "mail_bank_connectivity_test":
               if (wp_verify_nonce(isset($_REQUEST["_wp_nonce"]) ? $_REQUEST["_wp_nonce"] : "", "connectivity_test_nonce")) {
                  $host = isset($_REQUEST["smtp_host"]) ? sanitize_text_field($_REQUEST["smtp_host"]) : "";
                  $ports = array(25, 587, 465, 2525, 4065, 25025);
                  $ports_result = array();
                  foreach ($ports as $port) {
                     $connection = @fsockopen($host, $port);
                     if (is_resource($connection)) {
                        $ports_result[$port] = "Open";
                        fclose($connection);
                     } else {
                        $ports_result[$port] = "Close";
                     }
                  }
                  foreach ($ports_result as $results => $val) {
                     ?>
                     <tr>
                        <td>
                           <?php echo $mb_smtp; ?>
                        </td>
                        <td>
                           <?php echo $host . ":" . intval($results); ?>
                        </td>
                        <td>
                           <span style="<?php echo $val == 'Close' ? 'color:red' : ""; ?>"><?php echo $val; ?>
                        </td>
                     </tr>
                     <?php
                  }
               }
               break;
         }
         die();
      }
   }
}