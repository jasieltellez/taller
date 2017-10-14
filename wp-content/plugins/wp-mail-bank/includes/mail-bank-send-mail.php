<?php
if (!defined("ABSPATH")) {
   exit;
} // Exit if accessed directly
if (file_exists(MAIL_BANK_DIR_PATH . "includes/mail-bank-configuration-provider.php")) {
   include_once MAIL_BANK_DIR_PATH . "includes/mail-bank-configuration-provider.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "includes/mail-bank-manage-email.php")) {
   include_once MAIL_BANK_DIR_PATH . "includes/mail-bank-manage-email.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "includes/mail-bank-email-log.php")) {
   include_once MAIL_BANK_DIR_PATH . "includes/mail-bank-email-log.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "includes/authentication-manager-mail-bank.php")) {
   include_once MAIL_BANK_DIR_PATH . "includes/authentication-manager-mail-bank.php";
}

if (!class_exists("mail_bank_send_mail")) {

   class mail_bank_send_mail {
      public
          $exception,
          $configuration_settings,
          $obj_mail_bank_register_transport;
      public function __construct() {
         $obj_mb_config_provider = new mail_bank_configuration_provider();
         $this->obj_mail_bank_register_transport = new mail_bank_register_transport();
         $this->configuration_settings = $obj_mb_config_provider->get_configuration_settings();
      }
      // This function is used to send the message and return the result.
      public function send_email_message_mail_bank($to, $subject, $message, $headers = "", $attachments = array(), $email_configuration_settings) {
         $mail_bank_manage_email = $this->build_message_mail_bank($to, $subject, $message, $headers, $attachments);

         $log = new mail_bank_email_log();
         $log->email_to = $to;
         $log->email_subject = $subject;
         $log->email_message = $message;
         $log->email_headers = $headers;

         return $this->get_message_content_mail_bank($mail_bank_manage_email, $log, $email_configuration_settings);
      }
      // This function is used to build a message based on the wordPress wp_mail parameters.
      public function build_message_mail_bank($to, $subject, $message, $headers, $attachments) {
         if (!is_array($attachments)) {
            $attachments = explode("\n", str_replace("\r\n", "\n", $attachments));
         }

         // Creates the message
         $mail_bank_manage_email = $this->create_message_mail_bank();
         $this->get_entire_message_content_mail_bank($mail_bank_manage_email, $to, $subject, $message, $headers, $attachments);

         // Return the message
         return $mail_bank_manage_email;
      }
      // This function is used to create the instance of mail_bank_manage_email.
      public function create_message_mail_bank() {
         $message = new mail_bank_manage_email();
         $transport = $this->obj_mail_bank_register_transport->retrieve_mailertype_mail_bank();
         $message->mb_set_from($this->configuration_settings["email_address"], html_entity_decode($this->configuration_settings["sender_name"], ENT_QUOTES));
         $message->mb_set_charset(get_bloginfo("charset"));
         return $message;
      }
      // This function is used to get the options and token generated to send the message
      public function get_message_content_mail_bank(mail_bank_manage_email $message, mail_bank_email_log $log, $email_configuration_settings) {
         global $wpdb;
         $mail_bank_settings_data = $wpdb->get_row
             (
             $wpdb->prepare
                 (
                 "SELECT meta_value FROM " . mail_bank_meta() .
                 " WHERE meta_key = %s", "settings"
             )
         );

         $settings_data = maybe_unserialize($mail_bank_settings_data->meta_value);
         $ob_mb_config_provider = new mail_bank_configuration_provider();
         $this->configuration_settings = $ob_mb_config_provider->get_configuration_settings();
         $authorization_token = mail_bank_manage_token::get_instance();

         $transport = $this->obj_mail_bank_register_transport->retrieve_mailertype_mail_bank();
         $engine = $transport->initiate_mail_engine_mail_bank();

         if ($message->check_email_body_parts_mail_bank()) {
            $message->createBodyParts();
         }
         $obj_mail_bank_manage_email = new mail_bank_manage_email();

         try {
            $message->validate_email_contents_mail_bank($transport);

            if ($this->configuration_settings["auth_type"] == "oauth2") {
               $this->check_authtoken_mail_bank($transport, $authorization_token);
            }
            $engine->send_email_mail_bank($message);

            // writes the log on success.
            if ($engine->get_output_mail_bank() != "" && $settings_data["debug_mode"] == "enable") {
               update_option("mail_bank_mail_status", $engine->get_output_mail_bank());
            } else {
               update_option("mail_bank_mail_status", true);
            }
            $obj_mb_log_writter = new mail_bank_email_log_writter();
            update_option("mail_bank_is_mail_sent", "Sent");
            if ($settings_data["monitor_email_logs"] == "enable") {
               $obj_mb_log_writter->mb_success_log($log, $message, $settings_data["debug_mode"], $email_configuration_settings, $obj_mail_bank_manage_email);
            }
            return true;
         } catch (Exception $e) {
            $this->exception = $e;
            // Writes the log on failure
            if ($e->getCode() == 334 && $settings_data["debug_mode"] == "enable") {
               update_option("mail_bank_mail_status", $e->getMessage());
            } elseif ($engine->get_output_mail_bank() != "" && $settings_data["debug_mode"] == "enable") {
               update_option("mail_bank_mail_status", $engine->get_output_mail_bank());
            } elseif ($engine->get_output_mail_bank() == "" && $settings_data["debug_mode"] == "enable") {
               update_option("mail_bank_mail_status", $e->getMessage());
            } else {
               update_option("mail_bank_mail_status", false);
            }
            $obj_mb_log_writter = new mail_bank_email_log_writter();
            update_option("mail_bank_is_mail_sent", "Not Sent");
            if ($settings_data["monitor_email_logs"] == "enable") {
               $obj_mb_log_writter->mb_failure_log($log, $message, $settings_data["debug_mode"], $email_configuration_settings, $obj_mail_bank_manage_email);
            }
            return false;
         }
      }
      // This function is used to ensure the token is updated.
      public function check_authtoken_mail_bank($transport, $authorization_token) {
         $authentication_manager = new authentication_manager_mail_bank();
         $obj_authentication_manager_mail_bank = $authentication_manager->create_authentication_manager();
         if ($obj_authentication_manager_mail_bank->check_access_token()) {
            $obj_authentication_manager_mail_bank->get_refresh_token();
            $authorization_token->save_token_mail_bank();
         }
      }
      // This function is used to set all the content into a message.
      public function get_entire_message_content_mail_bank($message, $to, $subject, $body, $headers, $attachments) {
         $message->mb_add_headers($headers);
         $message->mb_set_body($body);
         $message->mb_set_subject($subject);
         $message->mb_addto($to);
         $message->mb_set_attachments($attachments);
         return $message;
      }
   }
}