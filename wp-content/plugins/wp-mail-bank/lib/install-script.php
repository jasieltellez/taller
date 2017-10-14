<?php
/**
 * This file is used for creating tables in database on the activation hook.
 *
 * @author  Tech Banker
 * @package wp-mail-bank/lib
 * @version 2.0.0
 */
if (!defined("ABSPATH")) {
   exit;
} // Exit if accessed directly
if (!is_user_logged_in()) {
   return;
} else {
   if (!current_user_can("manage_options")) {
      return;
   } else {
      /*
        Class Name: dbHelper_install_script_mail_bank
        Parameters: No
        Description: This Class is used to Insert, Update operations.
        Created On: 05-02-2016 11:40
        Created By: Tech Banker Team
       */
      if (!class_exists("dbHelper_install_script_mail_bank")) {
         class dbHelper_install_script_mail_bank {
            /*
              Function Name: insertCommand
              Parameters: Yes($table_name,$data)
              Description: This Function is used to Insert data in database.
              Created On: 05-02-2016 11:40
              Created By: Tech Banker Team
             */
            function insertCommand($table_name, $data) {
               global $wpdb;
               $wpdb->insert($table_name, $data);
               return $wpdb->insert_id;
            }
            /*
              Function Name: updateCommand
              Parameters: Yes($table_name,$data,$where)
              Description: This function is used to Update data.
              Created On: 05-02-2016 11:40
              Created By: Tech Banker Team
             */
            function updateCommand($table_name, $data, $where) {
               global $wpdb;
               $wpdb->update($table_name, $data, $where);
            }
         }
      }

      if (file_exists(ABSPATH . "wp-admin/includes/upgrade.php"))
         require_once ABSPATH . "wp-admin/includes/upgrade.php";

      $mail_bank_version_number = get_option("mail-bank-version-number");
      if (!function_exists("mail_bank_table")) {

         function mail_bank_table() {
            $sql = "CREATE TABLE IF NOT EXISTS " . mail_bank() . "
                        (
                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                `type` varchar(100) NOT NULL,
                                `parent_id` int(11) NOT NULL,
                                PRIMARY KEY (`id`)
                        )
                        ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
            dbDelta($sql);

            $data = "INSERT INTO " . mail_bank() . " (`type`, `parent_id`) VALUES
                        ('email_configuration', 0),
                        ('email_logs', 0),
                        ('settings', 0),
                        ('roles_and_capabilities', 0)";
            dbDelta($data);
         }
      }
      if (!function_exists("mail_bank_email_logs_table")) {

         function mail_bank_email_logs_table() {
            $obj_dbHelper_install_script_mail_bank = new dbHelper_install_script_mail_bank();
            $sql = "CREATE TABLE IF NOT EXISTS " . mail_bank_email_logs() . "
				(
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`email_data` longtext NOT NULL,
					PRIMARY KEY (`id`),
                                        FULLTEXT (`email_data`)
				)
				ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
            dbDelta($sql);
         }
      }

      if (!function_exists("mail_bank_meta_table")) {

         function mail_bank_meta_table() {
            $obj_dbHelper_install_script_mail_bank = new dbHelper_install_script_mail_bank();
            global $wpdb;
            $sql = "CREATE TABLE IF NOT EXISTS " . mail_bank_meta() . "
                        (
                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                `meta_id` int(11) NOT NULL,
                                `meta_key` varchar(255) NOT NULL,
                                `meta_value` longtext NOT NULL,
                                PRIMARY KEY (`id`)
                        )
                        ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
            dbDelta($sql);

            $admin_email = get_option("admin_email");
            $admin_name = get_option("blogname");

            $mail_bank_table_data = $wpdb->get_results
                (
                "SELECT * FROM " . mail_bank()
            );

            foreach ($mail_bank_table_data as $row) {
               switch ($row->type) {
                  case "email_configuration":
                     $email_configuration_array = array();
                     $email_configuration_array["email_address"] = $admin_email;
                     $email_configuration_array["reply_to"] = "";
                     $email_configuration_array["cc"] = "";
                     $email_configuration_array["bcc"] = "";
                     $email_configuration_array["mailer_type"] = "smtp";
                     $email_configuration_array["sender_name"] = $admin_name;
                     $email_configuration_array["sender_name_configuration"] = "override";
                     $email_configuration_array["hostname"] = "";
                     $email_configuration_array["port"] = "587";
                     $email_configuration_array["client_id"] = "";
                     $email_configuration_array["client_secret"] = "";
                     $email_configuration_array["redirect_uri"] = "";
                     $email_configuration_array["sender_email"] = $admin_email;
                     $email_configuration_array["from_email_configuration"] = "override";
                     $email_configuration_array["auth_type"] = "none";
                     $email_configuration_array["username"] = $admin_email;
                     $email_configuration_array["password"] = "";
                     $email_configuration_array["enc_type"] = "tls";

                     $email_configuration_array_data = array();
                     $email_configuration_array_data["meta_id"] = $row->id;
                     $email_configuration_array_data["meta_key"] = "email_configuration";
                     $email_configuration_array_data["meta_value"] = serialize($email_configuration_array);
                     $obj_dbHelper_install_script_mail_bank->insertCommand(mail_bank_meta(), $email_configuration_array_data);
                     break;

                  case "settings":
                     $settings_data_array = array();
                     $settings_data_array["debug_mode"] = "enable";
                     $settings_data_array["remove_tables_at_uninstall"] = "enable";
                     $settings_data_array["monitor_email_logs"] = "enable";

                     $settings_array = array();
                     $settings_array["meta_id"] = $row->id;
                     $settings_array["meta_key"] = "settings";
                     $settings_array["meta_value"] = serialize($settings_data_array);
                     $obj_dbHelper_install_script_mail_bank->insertCommand(mail_bank_meta(), $settings_array);
                     break;

                  case "roles_and_capabilities":
                     $roles_capabilities_data_array = array();
                     $roles_capabilities_data_array["roles_and_capabilities"] = "1,1,1,0,0,0";
                     $roles_capabilities_data_array["show_mail_bank_top_bar_menu"] = "enable";
                     $roles_capabilities_data_array["others_full_control_capability"] = "0";
                     $roles_capabilities_data_array["administrator_privileges"] = "1,1,1,1,1,1,1,1,1,1";
                     $roles_capabilities_data_array["author_privileges"] = "0,0,1,0,0,0,0,0,0,0";
                     $roles_capabilities_data_array["editor_privileges"] = "0,0,1,0,0,0,1,0,0,0";
                     $roles_capabilities_data_array["contributor_privileges"] = "0,0,0,0,0,0,1,0,0,0";
                     $roles_capabilities_data_array["subscriber_privileges"] = "0,0,0,0,0,0,0,0,0,0";
                     $roles_capabilities_data_array["other_roles_privileges"] = "0,0,0,0,0,0,0,0,0,0";
                     $user_capabilities = get_others_capabilities_mail_bank();
                     $other_roles_array = array();
                     $other_roles_access_array = array(
                         "manage_options",
                         "edit_plugins",
                         "edit_posts",
                         "publish_posts",
                         "publish_pages",
                         "edit_pages",
                         "read"
                     );
                     foreach ($other_roles_access_array as $role) {
                        if (in_array($role, $user_capabilities)) {
                           array_push($other_roles_array, $role);
                        }
                     }
                     $roles_capabilities_data_array["capabilities"] = $other_roles_array;

                     $roles_data_array = array();
                     $roles_data_array["meta_id"] = $row->id;
                     $roles_data_array["meta_key"] = "roles_and_capabilities";
                     $roles_data_array["meta_value"] = serialize($roles_capabilities_data_array);
                     $obj_dbHelper_install_script_mail_bank->insertCommand(mail_bank_meta(), $roles_data_array);
                     break;
               }
            }
         }
      }



      $obj_dbHelper_install_script_mail_bank = new dbHelper_install_script_mail_bank();
      switch ($mail_bank_version_number) {
         case "":
            if (count($wpdb->get_var("SHOW TABLES LIKE '" . $wpdb->prefix . "mail_bank'")) != 0) {
               $mail_bank_data = $wpdb->get_row
                   (
                   "SELECT * FROM " . $wpdb->prefix . "mail_bank"
               );

               $wpdb->query("DROP TABLE IF EXISTS " . $wpdb->prefix . "mail_bank");
               mail_bank_table();
               mail_bank_meta_table();
               mail_bank_email_logs_table();

               $get_from_name = get_option("show_from_name_in_email");
               $get_from_email = get_option("show_from_email_in_email");

               if (count($mail_bank_data) > 0) {
                  $update_mail_bank_data = array();
                  $update_mail_bank_data["email_address"] = get_option("admin_email");
                  $update_mail_bank_data["reply_to"] = "";
                  $update_mail_bank_data["cc"] = "";
                  $update_mail_bank_data["bcc"] = "";
                  $update_mail_bank_data["mailer_type"] = isset($mail_bank_data->mailer_type) && $mail_bank_data->mailer_type == 1 ? "php_mail_function" : "smtp";
                  $update_mail_bank_data["sender_name_configuration"] = isset($get_from_name) && $get_from_name == 1 ? "override" : "dont_override";
                  $update_mail_bank_data["sender_name"] = isset($mail_bank_data->from_name) ? esc_html($mail_bank_data->from_name) : esc_html(get_option("blogname"));
                  $update_mail_bank_data["from_email_configuration"] = isset($get_from_email) && $get_from_email == 1 ? "override" : "dont_override";
                  $update_mail_bank_data["sender_email"] = isset($mail_bank_data->from_email) ? esc_attr($mail_bank_data->from_email) : get_option("admin_email");
                  $update_mail_bank_data["hostname"] = isset($mail_bank_data->smtp_host) ? esc_attr($mail_bank_data->smtp_host) : "";
                  $update_mail_bank_data["port"] = isset($mail_bank_data->smtp_port) ? intval($mail_bank_data->smtp_port) : 0;
                  $update_mail_bank_data["enc_type"] = isset($mail_bank_data->encryption) && (($mail_bank_data->encryption) == 0) ? "none" : ((($mail_bank_data->encryption) == 1) ? "ssl" : "tls");
                  $update_mail_bank_data["auth_type"] = "login";
                  $update_mail_bank_data["client_id"] = "";
                  $update_mail_bank_data["client_secret"] = "";
                  $update_mail_bank_data["redirect_uri"] = "";
                  $update_mail_bank_data["username"] = isset($mail_bank_data->smtp_username) ? esc_attr($mail_bank_data->smtp_username) : "";
                  $update_mail_bank_data["password"] = isset($mail_bank_data->smtp_password) ? base64_encode($mail_bank_data->smtp_password) : "";
                  $update_mail_bank_data["automatic_mail"] = "1";

                  $update_mail_bank_data_serialize = array();
                  $where = array();
                  $where["meta_id"] = $mail_bank_data->id;
                  $where["meta_key"] = "email_configuration";
                  $update_mail_bank_data_serialize["meta_value"] = serialize($update_mail_bank_data);
                  $obj_dbHelper_install_script_mail_bank->updateCommand(mail_bank_meta(), $update_mail_bank_data_serialize, $where);
               }
               $plugin_settings_data = $wpdb->get_var
                   (
                   $wpdb->prepare
                       (
                       "SELECT meta_value FROM " . mail_bank_meta() . "
							WHERE meta_key = %s", "settings"
                   )
               );
               $plugin_settings_data_unserialize = maybe_unserialize($plugin_settings_data);

               $update_plugin_data = array();
               $update_plugin_data["debug_mode"] = isset($plugin_settings_data_unserialize["debug_mode"]) ? esc_attr($plugin_settings_data_unserialize["debug_mode"]) : "enable";
               $update_plugin_data["remove_tables_at_uninstall"] = isset($plugin_settings_data_unserialize["remove_tables_at_uninstall"]) ? esc_attr($plugin_settings_data_unserialize["remove_tables_at_uninstall"]) : "disable";
               $update_plugin_data["monitor_email_logs"] = isset($plugin_settings_data_unserialize["monitor_email_logs"]) ? esc_attr($plugin_settings_data_unserialize["monitor_email_logs"]) : "enable";

               $update_plugin_settings_data_serialize = array();
               $where = array();
               $where["meta_key"] = "settings";
               $update_plugin_settings_data_serialize["meta_value"] = serialize($update_plugin_data);
               $obj_dbHelper_install_script_mail_bank->updateCommand(mail_bank_meta(), $update_plugin_settings_data_serialize, $where);
            } else {
               mail_bank_table();
               mail_bank_meta_table();
               mail_bank_email_logs_table();
            }
            break;

         default:
            if (count($wpdb->get_var("SHOW TABLES LIKE '" . mail_bank() . "'")) != 0 && count($wpdb->get_var("SHOW TABLES LIKE '" . mail_bank_meta() . "'")) != 0) {
               $settings_data = $wpdb->get_var
                   (
                   $wpdb->prepare
                       (
                       "SELECT meta_value FROM " . mail_bank_meta() .
                       " WHERE meta_key=%s", "settings"
                   )
               );

               $settings_data_array = maybe_unserialize($settings_data);
               if (!array_key_exists("monitor_email_logs", $settings_data_array)) {
                  $settings_data_array["monitor_email_logs"] = "enable";
               }
               $where = array();
               $settings_array = array();
               $where["meta_key"] = "settings";
               $settings_array["meta_value"] = serialize($settings_data_array);
               $obj_dbHelper_install_script_mail_bank->updateCommand(mail_bank_meta(), $settings_array, $where);

               $get_roles_settings_data = $wpdb->get_var
                   (
                   $wpdb->prepare
                       (
                       "SELECT meta_value FROM " . mail_bank_meta() .
                       " WHERE meta_key=%s", "roles_and_capabilities"
                   )
               );

               $get_roles_settings_data_array = maybe_unserialize($get_roles_settings_data);

               if (array_key_exists("roles_and_capabilities", $get_roles_settings_data_array)) {
                  $roles_and_capabilities_data = isset($get_roles_settings_data_array["roles_and_capabilities"]) ? explode(",", $get_roles_settings_data_array["roles_and_capabilities"]) : "1,1,1,0,0,0";
                  $administrator_privileges_data = isset($get_roles_settings_data_array["administrator_privileges"]) ? explode(",", $get_roles_settings_data_array["administrator_privileges"]) : "1,1,1,1,1,1,1,1,1,1";
                  $author_privileges_data = isset($get_roles_settings_data_array["author_privileges"]) ? explode(",", $get_roles_settings_data_array["author_privileges"]) : "0,0,1,0,0,0,0,0,0,0";
                  $editor_privileges_data = isset($get_roles_settings_data_array["editor_privileges"]) ? explode(",", $get_roles_settings_data_array["editor_privileges"]) : "0,0,1,0,0,0,1,0,0,0";
                  $contributor_privileges_data = isset($get_roles_settings_data_array["contributor_privileges"]) ? explode(",", $get_roles_settings_data_array["contributor_privileges"]) : "0,0,0,0,0,0,1,0,0,0";
                  $subscriber_privileges_data = isset($get_roles_settings_data_array["subscriber_privileges"]) ? explode(",", $get_roles_settings_data_array["subscriber_privileges"]) : "0,0,0,0,0,0,0,0,0,0";
                  $other_privileges_data = isset($get_roles_settings_data_array["other_roles_privileges"]) ? explode(",", $get_roles_settings_data_array["other_roles_privileges"]) : "0,0,0,0,0,0,0,0,0,0";

                  if (count($roles_and_capabilities_data) == 5) {
                     array_push($roles_and_capabilities_data, 0);
                  }

                  if (count($administrator_privileges_data) == 8) {
                     array_splice($administrator_privileges_data, 3, 0, 1);
                     array_splice($administrator_privileges_data, 8, 0, 1);
                  } elseif (count($administrator_privileges_data) == 9) {
                     array_splice($administrator_privileges_data, 3, 0, 1);
                  }

                  if (count($author_privileges_data) == 8) {
                     array_splice($author_privileges_data, 3, 0, 0);
                     array_splice($author_privileges_data, 8, 0, 0);
                  } elseif (count($author_privileges_data) == 9) {
                     array_splice($author_privileges_data, 3, 0, 0);
                  }

                  if (count($editor_privileges_data) == 8) {
                     array_splice($editor_privileges_data, 3, 0, 0);
                     array_splice($editor_privileges_data, 8, 0, 0);
                  } elseif (count($editor_privileges_data) == 9) {
                     array_splice($editor_privileges_data, 3, 0, 0);
                  }

                  if (count($contributor_privileges_data) == 8) {
                     array_splice($contributor_privileges_data, 3, 0, 0);
                     array_splice($contributor_privileges_data, 8, 0, 0);
                  } elseif (count($contributor_privileges_data) == 9) {
                     array_splice($editor_privileges_data, 3, 0, 0);
                  }

                  if (count($subscriber_privileges_data) == 8) {
                     array_splice($subscriber_privileges_data, 3, 0, 0);
                     array_splice($subscriber_privileges_data, 8, 0, 0);
                  } elseif (count($subscriber_privileges_data) == 9) {
                     array_splice($subscriber_privileges_data, 3, 0, 0);
                  }

                  if (count($other_privileges_data) == 8) {
                     array_splice($other_privileges_data, 3, 0, 0);
                     array_splice($other_privileges_data, 8, 0, 0);
                  } elseif (count($other_privileges_data) == 9) {
                     array_splice($other_privileges_data, 3, 0, 0);
                  }

                  if (!array_key_exists("others_full_control_capability", $get_roles_settings_data_array)) {
                     $get_roles_settings_data_array["others_full_control_capability"] = "0";
                  }

                  if (!array_key_exists("capabilities", $get_roles_settings_data_array)) {
                     $user_capabilities = get_others_capabilities_mail_bank();
                     $other_roles_array = array();
                     $other_roles_access_array = array(
                         "manage_options",
                         "edit_plugins",
                         "edit_posts",
                         "publish_posts",
                         "publish_pages",
                         "edit_pages",
                         "read"
                     );
                     foreach ($other_roles_access_array as $role) {
                        if (in_array($role, $user_capabilities)) {
                           array_push($other_roles_array, $role);
                        }
                     }
                     $get_roles_settings_data_array["capabilities"] = $other_roles_array;
                  }
                  $get_roles_settings_data_array["roles_and_capabilities"] = implode(",", $roles_and_capabilities_data);
                  $get_roles_settings_data_array["administrator_privileges"] = implode(",", $administrator_privileges_data);
                  $get_roles_settings_data_array["author_privileges"] = implode(",", $author_privileges_data);
                  $get_roles_settings_data_array["editor_privileges"] = implode(",", $editor_privileges_data);
                  $get_roles_settings_data_array["contributor_privileges"] = implode(",", $contributor_privileges_data);
                  $get_roles_settings_data_array["subscriber_privileges"] = implode(",", $subscriber_privileges_data);
                  $get_roles_settings_data_array["other_roles_privileges"] = implode(",", $other_privileges_data);
                  $where = array();
                  $roles_capabilities_array = array();
                  $where["meta_key"] = "roles_and_capabilities";
                  $roles_capabilities_array["meta_value"] = serialize($get_roles_settings_data_array);
                  $obj_dbHelper_install_script_mail_bank->updateCommand(mail_bank_meta(), $roles_capabilities_array, $where);
               }
            }
            if ($mail_bank_version_number < "3.0.1" && count($wpdb->get_var("SHOW TABLES LIKE '" . mail_bank_email_logs() . "'")) == 0) {
               mail_bank_email_logs_table();
               $wpdb->query
                   (
                   $wpdb->prepare
                       (
                       "INSERT INTO " . mail_bank_email_logs() . " (email_data)
                            SELECT  meta_value FROM " . mail_bank_meta() . " WHERE meta_key=%s", "email_logs"
                   )
               );
               $wpdb->query
                   (
                   $wpdb->prepare
                       (
                       "DELETE FROM " . mail_bank_meta() . " WHERE meta_key = %s", "email_logs"
                   )
               );
            }
      }
      update_option("mail-bank-version-number", "3.0.1");
   }
}