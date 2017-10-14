<?php
if (!defined("ABSPATH")) {
   exit;
} // Exit if accessed directly
if (!class_exists("mail_bank_configuration_provider")) {

   class mail_bank_configuration_provider {
      public function get_configuration_settings() {
         global $wpdb;
         $email_configuration_data = $wpdb->get_var
             (
             $wpdb->prepare
                 (
                 "SELECT meta_value FROM " . mail_bank_meta() .
                 " WHERE meta_key=%s", "email_configuration"
             )
         );
         $email_configuration_array = maybe_unserialize($email_configuration_data);
         return $email_configuration_array;
      }
   }
}