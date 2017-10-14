<?php
/**
 * This file is used for creating dbHelper class.
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
      /*
        Class Name: dbHelper_mail_bank
        Parameters: No
        Description: This Class is used for Insert, Update and Delete operations.
        Created On: 15-06-2016 10:44
        Created By: Tech Banker Team
       */
      class dbHelper_mail_bank {
         /*
           Function Name: insertCommand
           Parameters: Yes($table_name,$data)
           Description: This Function is used for Insert data in database.
           Created On: 15-06-2016 10:43
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
           Description: This function is used for Update data in database.
           Created On: 15-06-2016 10:43
           Created By: Tech Banker Team
          */
         function updateCommand($table_name, $data, $where) {
            global $wpdb;
            $wpdb->update($table_name, $data, $where);
         }
         /*
           Function Name: deleteCommand
           Parameters: Yes($table_name,$where)
           Description: This function is used for delete data from database.
           Created On: 15-06-2016 10:43
           Created By: Tech Banker Team
          */
         function deleteCommand($table_name, $where) {
            global $wpdb;
            $wpdb->delete($table_name, $where);
         }
         /*
           Function Name: file_reader
           Parameters: Yes($filepath)
           Description: This function is used to read file contents
           Created On: 18-01-2017 11:24
           Created By: Tech Banker Team
          */
         public static function file_reader($filepath) {
            $reader = "";
            if (file_exists($filepath)) {
               $reader = file_get_contents($filepath);
            }
            return $reader;
         }
         /*
           Function Name: bulk_deleteCommand
           Parameters: Yes($table_name,$data,$where)
           Decription: This function is being used to delete multiple data from database.
           Created On: 15-06-2016 10:43
           Created By: Tech Banker Team
          */
         function bulk_deleteCommand($table_name, $where, $data) {
            global $wpdb;
            $wpdb->query
                (
                "DELETE FROM $table_name WHERE $where IN ($data)"
            );
         }
      }
      /*
        Class Name: mail_bank_discover_host
        Parameters: No
        Description: This Class is used for Get host and Port.
        Created On: 21-06-2016 11:44
        Created By: Tech Banker Team
       */
      class mail_bank_discover_host {
         public $domain;
         public $email_domains = array(
             "1and1.com" => "smtp.1and1.com",
             "airmail.net" => "smtp.airmail.net",
             "aol.com" => "smtp.aol.com",
             "Bluewin.ch" => "Smtpauths.bluewin.ch",
             "Comcast.net" => "Smtp.comcast.net",
             "Earthlink.net" => "Smtpauth.earthlink.net",
             "gmail.com" => "smtp.gmail.com",
             "Gmx.com" => "mail.gmx.com",
             "Gmx.net" => "mail.gmx.com",
             "Gmx.us" => "mail.gmx.com",
             "hotmail.com" => "smtp.live.com",
             "outlook.com" => "smtp.live.com",
             "icloud.com" => "smtp.mail.me.com",
             "mail.com" => "smtp.mail.com",
             "ntlworld.com" => "smtp.ntlworld.com",
             "rocketmail.com" => "smtp.mail.yahoo.com",
             "rogers.com" => "smtp.broadband.rogers.com",
             "yahoo.ca" => "smtp.mail.yahoo.ca",
             "yahoo.co.id" => "smtp.mail.yahoo.co.id",
             "yahoo.co.in" => "smtp.mail.yahoo.co.in",
             "yahoo.co.kr" => "smtp.mail.yahoo.com",
             "yahoo.com" => "smtp.mail.yahoo.com",
             "yahoo.com.ar" => "smtp.mail.yahoo.com.ar",
             "yahoo.com.au" => "smtp.mail.yahoo.com.au",
             "yahoo.com.br" => "smtp.mail.yahoo.com.br",
             "yahoo.com.cn" => "smtp.mail.yahoo.com.cn",
             "yahoo.com.hk" => "smtp.mail.yahoo.com.hk",
             "yahoo.com.mx" => "smtp.mail.yahoo.com",
             "yahoo.com.my" => "smtp.mail.yahoo.com.my",
             "yahoo.com.ph" => "smtp.mail.yahoo.com.ph",
             "yahoo.com.sg" => "smtp.mail.yahoo.com.sg",
             "yahoo.com.tw" => "smtp.mail.yahoo.com.tw",
             "yahoo.com.vn" => "smtp.mail.yahoo.com.vn",
             "yahoo.co.nz" => "smtp.mail.yahoo.com.au",
             "yahoo.co.th" => "smtp.mail.yahoo.co.th",
             "yahoo.co.uk" => "smtp.mail.yahoo.co.uk",
             "ymail.com" => "smtp.mail.yahoo.com",
             "yahoo.de" => "smtp.mail.yahoo.de",
             "yahoo.es" => "smtp.correo.yahoo.es",
             "yahoo.fr" => "smtp.mail.yahoo.fr",
             "yahoo.ie" => "smtp.mail.yahoo.co.uk",
             "yahoo.it" => "smtp.mail.yahoo.it",
             "zoho.com" => "smtp.zoho.com",
             "ameritech.net" => "outbound.att.net",
             "att.net" => "outbound.att.net",
             "bellsouth.net" => "outbound.att.net",
             "flash.net" => "outbound.att.net",
             "nvbell.net" => "outbound.att.net",
             "pacbell.net" => "outbound.att.net",
             "prodigy.net" => "outbound.att.net",
             "sbcglobal.net" => "outbound.att.net",
             "snet.net" => "outbound.att.net",
             "swbell.net" => "outbound.att.net",
             "wans.net" => "outbound.att.net"
         );
         /*
           Function Name: get_smtp_from_email
           Parameters: Yes($hostname)
           Description: This Function is used for getting hostname.
           Created On: 15-06-2016 10:43
           Created By: Tech Banker Team
          */
         public function get_smtp_from_email($hostname) {
            reset($this->email_domains);
            while (list($domain, $smtp) = each($this->email_domains)) {
               if (strcasecmp($hostname, $domain) == 0) {
                  return $smtp;
               }
            }
            return false;
         }
      }
      class plugin_info_wp_mail_bank {
         /*
           Function Name: get_plugin_info_wp_mail_bank
           Parameters: No
           Decription: This function is used to return the information about plugins.
           Created On: 21-04-2017 09:48
           Created By: Tech Banker Team
          */
         function get_plugin_info_wp_mail_bank() {
            $active_plugins = (array) get_option("active_plugins", array());
            if (is_multisite())
               $active_plugins = array_merge($active_plugins, get_site_option("active_sitewide_plugins", array()));
            $plugins = array();
            if (count($active_plugins) > 0) {
               $get_plugins = array();
               foreach ($active_plugins as $plugin) {
                  $plugin_data = @get_plugin_data(WP_PLUGIN_DIR . "/" . $plugin);

                  $get_plugins["plugin_name"] = strip_tags($plugin_data["Name"]);
                  $get_plugins["plugin_author"] = strip_tags($plugin_data["Author"]);
                  $get_plugins["plugin_version"] = strip_tags($plugin_data["Version"]);
                  array_push($plugins, $get_plugins);
               }
               return $plugins;
            }
         }
      }
   }
}