<?php
if (!defined("ABSPATH")) {
   exit;
} // Exit if accessed directly
if (!class_exists("mail_bank_manage_token")) {

   class mail_bank_manage_token {
      public
          $vendor_name,
          $access_token,
          $refresh_token,
          $expiry_time;
      public function __construct() {
         $this->get_token_mail_bank();
      }
      public static function get_instance() {
         static $instance = null;
         if ($instance === null) {
            $instance = new mail_bank_manage_token();
         }
         return $instance;
      }
      public function isValid() {
         $access_token = $this->retrieve_access_token_mail_bank();
         $refresh_token = $this->retrieve_refresh_token_mail_bank();
         return !(empty($access_token) || empty($refresh_token));
      }
      public function get_token_mail_bank() {
         $oauth_token = get_option("mail_bank_auth");
         $this->set_access_token_mail_bank($oauth_token["access_token"]);
         $this->set_refresh_token_mail_bank($oauth_token["refresh_token"]);
         $this->set_token_expirytime_mail_bank($oauth_token["auth_token_expires"]);
         $this->set_vendorname_mail_bank($oauth_token["vendor_name"]);
      }
      // Save the mail bank oauth token properties to the database
      public function save_token_mail_bank() {
         $oauth_token["access_token"] = $this->retrieve_access_token_mail_bank();
         $oauth_token["refresh_token"] = $this->retrieve_refresh_token_mail_bank();
         $oauth_token["auth_token_expires"] = $this->retrieve_token_expiry_time_mail_bank();
         $oauth_token["vendor_name"] = $this->get_vendor_mail_bank();
         update_option("mail_bank_auth", $oauth_token);
      }
      public function get_vendor_mail_bank() {
         return $this->vendor_name;
      }
      public function retrieve_token_expiry_time_mail_bank() {
         return $this->expiry_time;
      }
      public function retrieve_access_token_mail_bank() {
         return $this->access_token;
      }
      public function retrieve_refresh_token_mail_bank() {
         return $this->refresh_token;
      }
      public function set_vendorname_mail_bank($name) {
         $this->vendor_name = esc_html($name);
      }
      public function set_token_expirytime_mail_bank($time) {
         $this->expiry_time = esc_html($time);
      }
      public function set_access_token_mail_bank($token) {
         $this->access_token = esc_html($token);
      }
      public function set_refresh_token_mail_bank($token) {
         $this->refresh_token = esc_html($token);
      }
   }
}