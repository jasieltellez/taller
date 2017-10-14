<?php
if (!defined("ABSPATH")) {
   exit;
} // Exit if accessed directly
if (file_exists(MAIL_BANK_DIR_PATH . "includes/google-authentication-mail-bank.php")) {
   require_once MAIL_BANK_DIR_PATH . "includes/google-authentication-mail-bank.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "includes/microsoft-authentication-mail-bank.php")) {
   require_once MAIL_BANK_DIR_PATH . "includes/microsoft-authentication-mail-bank.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "includes/yahoo-authentication-mail-bank.php")) {
   require_once MAIL_BANK_DIR_PATH . "includes/yahoo-authentication-mail-bank.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "includes/mail-bank-register-transport.php")) {
   include_once MAIL_BANK_DIR_PATH . "includes/mail-bank-register-transport.php";
}

if (!class_exists("authentication_manager_mail_bank")) {
   class authentication_manager_mail_bank {
      public function create_authentication_manager() {
         $obj_mail_bank_register_transport = new mail_bank_register_transport();
         $transport = $obj_mail_bank_register_transport->retrieve_mailertype_mail_bank();
         return $this->create_manager($transport);
      }
      public function create_manager(mail_bank_smtp_transport $transport) {
         $obj_mb_config_provider = new mail_bank_configuration_provider();
         $configuration_settings = $obj_mb_config_provider->get_configuration_settings();
         $authorization_token = mail_bank_manage_token::get_instance();
         $hostname = $configuration_settings["hostname"];
         $client_id = $configuration_settings["client_id"];
         $client_secret = $configuration_settings["client_secret"];
         $sender_email = $configuration_settings["sender_email"];
         $redirect_uri = admin_url("admin-ajax.php");
         if ($this->check_google_service_provider_mail_bank($hostname)) {
            $obj_service_provider = new google_authentication_mail_bank($client_id, $client_secret, $authorization_token, $redirect_uri, $sender_email);
         } elseif ($this->check_microsoft_service_provider_mail_bank($hostname)) {
            $obj_service_provider = new microsoft_authentication_mail_bank($client_id, $client_secret, $authorization_token, $redirect_uri);
         } elseif ($this->check_yahoo_service_provider_mail_bank($hostname)) {
            $obj_service_provider = new yahoo_authentication_mail_bank($client_id, $client_secret, $authorization_token, $redirect_uri);
         }
         return $obj_service_provider;
      }
      public function check_google_service_provider_mail_bank($hostname) {
         return mail_bank_zend_mail_helper::email_domains_mail_bank($hostname, "gmail.com") || mail_bank_zend_mail_helper::email_domains_mail_bank($hostname, "googleapis.com");
      }
      public function check_microsoft_service_provider_mail_bank($hostname) {
         return mail_bank_zend_mail_helper::email_domains_mail_bank($hostname, "live.com");
      }
      public function check_yahoo_service_provider_mail_bank($hostname) {
         return strpos($hostname, "yahoo");
      }
   }
}