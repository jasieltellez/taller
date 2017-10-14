<?php
/**
 * This file is used for authenticating and sending Emails.
 *
 * @author  Tech Banker
 * @package wp-mail-bank/includes
 * @version 2.0.0
 */
if (!defined("ABSPATH")) {
   exit;
} // Exit if accessed directly
if (!class_exists("mail_bank_auth_host")) {

   class mail_bank_auth_host {
      public $from_name;
      public $smtp_host;
      public $smtp_port;
      public $client_id;
      public $client_secret;
      public $redirect_uri;
      public $api_key;
      public $authorization_token;
      public $oauth_domains = array(
          "hotmail.com" => "smtp.live.com",
          "outlook.com" => "smtp.live.com",
          "yahoo.ca" => "smtp.mail.yahoo.ca",
          "yahoo.co.id" => "smtp.mail.yahoo.co.id",
          "yahoo.co.in" => "smtp.mail.yahoo.co.in",
          "yahoo.co.kr" => "smtp.mail.yahoo.com",
          "yahoo.com" => "smtp.mail.yahoo.com",
          "ymail.com" => "smtp.mail.yahoo.com",
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
          "yahoo.de" => "smtp.mail.yahoo.de",
          "yahoo.es" => "smtp.correo.yahoo.es",
          "yahoo.fr" => "smtp.mail.yahoo.fr",
          "yahoo.ie" => "smtp.mail.yahoo.co.uk",
          "yahoo.it" => "smtp.mail.yahoo.it",
          "gmail.com" => "smtp.gmail.com",
      );
      public $yahoo_domains = array(
          "smtp.mail.yahoo.ca",
          "smtp.mail.yahoo.co.id",
          "smtp.mail.yahoo.co.in",
          "smtp.mail.yahoo.com",
          "smtp.mail.yahoo.com",
          "smtp.mail.yahoo.com.ar",
          "smtp.mail.yahoo.com.au",
          "smtp.mail.yahoo.com.br",
          "smtp.mail.yahoo.com.cn",
          "smtp.mail.yahoo.com.hk",
          "smtp.mail.yahoo.com",
          "smtp.mail.yahoo.com.my",
          "smtp.mail.yahoo.com.ph",
          "smtp.mail.yahoo.com.sg",
          "smtp.mail.yahoo.com.tw",
          "smtp.mail.yahoo.com.vn",
          "smtp.mail.yahoo.com.au",
          "smtp.mail.yahoo.co.th",
          "smtp.mail.yahoo.co.uk",
          "smtp.mail.yahoo.de",
          "smtp.correo.yahoo.es",
          "smtp.mail.yahoo.fr",
          "smtp.mail.yahoo.co.uk",
          "smtp.mail.yahoo.it"
      );
      public function __construct($settings_array) {
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/mail-bank-manage-token.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/mail-bank-manage-token.php";
         }
         $this->authorization_token = mail_bank_manage_token::get_instance();
         $this->from_name = $settings_array["sender_name"];
         $this->from_email = $settings_array["sender_email"];
         $this->smtp_host = $settings_array["hostname"];
         $this->smtp_port = $settings_array["port"];
         $this->client_id = $settings_array["client_id"];
         $this->client_secret = $settings_array["client_secret"];
         $this->redirect_uri = $settings_array["redirect_uri"];
         $this->sender_email = $settings_array["email_address"];
      }
      /*
        Function Name: send_test_mail_bank
        Parameters: Yes($to,$subject,$message,$headers,$attachments,$email_configuration_settings)
        Description: This Function is used for sending Email.
        Created On: 15-06-2016 10:43
        Created By: Tech Banker Team
       */
      public function send_test_mail_bank($to, $subject, $message, $headers = "", $attachments = "", $email_configuration_settings) {
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/mail-bank-send-mail.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/mail-bank-send-mail.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/mail-bank-manage-token.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/mail-bank-manage-token.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/mail-bank-zend-mail-helper.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/mail-bank-zend-mail-helper.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/mail-bank-smtp-transport.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/mail-bank-smtp-transport.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/mail-bank-register-transport.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/mail-bank-register-transport.php";
         }
         $obj_transport_registry = new mail_bank_register_transport();
         $obj_transport_registry->listing_transport_mail_bank(new mail_bank_smtp_transport($email_configuration_settings));
         $obj_wp_mail = new mail_bank_send_mail();
         return $obj_wp_mail->send_email_message_mail_bank($to, $subject, $message, $headers, $attachments, $email_configuration_settings);
      }
      /*
        Function Name: microsoft_authentication
        Parameters: No
        Description: This Function is used for Authentication in case of Microsoft.
        Created On: 15-06-2016 10:43
        Created By: Tech Banker Team
       */
      public function microsoft_authentication() {
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/microsoft-authentication-mail-bank.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/microsoft-authentication-mail-bank.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/authentication-manager-mail-bank.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/authentication-manager-mail-bank.php";
         }
         $obj_microsoft_authentication_mail_bank = new microsoft_authentication_mail_bank($this->client_id, $this->client_secret, $this->authorization_token, $this->redirect_uri);

         $obj_microsoft_authentication_mail_bank->get_token_code(md5(rand()));
      }
      /*
        Function Name: google_authentication
        Parameters: No
        Description: This Function is used for Authentication in case of Google.
        Created On: 15-06-2016 10:43
        Created By: Tech Banker Team
       */
      public function google_authentication() {
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/google-authentication-mail-bank.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/google-authentication-mail-bank.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/authentication-manager-mail-bank.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/authentication-manager-mail-bank.php";
         }
         $obj_google_authentication_mail_bank = new google_authentication_mail_bank($this->client_id, $this->client_secret, $this->authorization_token, $this->redirect_uri, $this->sender_email);

         $obj_google_authentication_mail_bank->get_token_code(md5(rand()));
      }
      /*
        Function Name: microsoft_authentication_token
        Parameters: Yes($code)
        Description: This Function is used for Saving token of Microsoft.
        Created On: 15-06-2016 10:43
        Created By: Tech Banker Team
       */
      public function microsoft_authentication_token($code) {
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/mail-bank-zend-mail-helper.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/mail-bank-zend-mail-helper.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/microsoft-authentication-mail-bank.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/microsoft-authentication-mail-bank.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/authentication-manager-mail-bank.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/authentication-manager-mail-bank.php";
         }
         $obj_microsoft_authentication_mail_bank = new microsoft_authentication_mail_bank($this->client_id, $this->client_secret, $this->authorization_token, $this->redirect_uri);

         $test_error = $obj_microsoft_authentication_mail_bank->process_token_Code(md5(rand()));
         if (isset($test_error->error)) {
            return $test_error;
         }
         $this->authorization_token->save_token_mail_bank();
      }
      /*
        Function Name: google_authentication_token
        Parameters: Yes($code)
        Description: This Function is used for Saving token of Google.
        Created On: 15-06-2016 10:43
        Created By: Tech Banker Team
       */
      public function google_authentication_token($code) {
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/mail-bank-zend-mail-helper.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/mail-bank-zend-mail-helper.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/google-authentication-mail-bank.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/google-authentication-mail-bank.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/authentication-manager-mail-bank.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/authentication-manager-mail-bank.php";
         }
         $obj_google_authentication_mail_bank = new google_authentication_mail_bank($this->client_id, $this->client_secret, $this->authorization_token, $this->redirect_uri, $this->sender_email);

         $test_error1 = $obj_google_authentication_mail_bank->process_token_Code(md5(rand()));
         if (isset($test_error1->error)) {
            return $test_error1;
         }

         $this->authorization_token->save_token_mail_bank();
      }
      /*
        Function Name: yahoo_authentication
        Parameters: No
        Description: This Function is used for Yahoo Authentication.
        Created On: 15-06-2016 10:43
        Created By: Tech Banker Team
       */
      public function yahoo_authentication() {
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/mail-bank-zend-mail-helper.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/mail-bank-zend-mail-helper.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/yahoo-authentication-mail-bank.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/yahoo-authentication-mail-bank.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/authentication-manager-mail-bank.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/authentication-manager-mail-bank.php";
         }

         $obj_yahoo_authentication_mail_bank = new yahoo_authentication_mail_bank($this->client_id, $this->client_secret, $this->authorization_token, $this->redirect_uri);
         $obj_yahoo_authentication_mail_bank->get_token_code(md5(rand()));
      }
      /*
        Function Name: yahoo_authentication_token
        Parameters: Yes($code)
        Description: This Function is used for saving token of Yahoo.
        Created On: 15-06-2016 10:43
        Created By: Tech Banker Team
       */
      public function yahoo_authentication_token($code) {
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/mail-bank-zend-mail-helper.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/mail-bank-zend-mail-helper.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/yahoo-authentication-mail-bank.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/yahoo-authentication-mail-bank.php";
         }
         if (file_exists(MAIL_BANK_DIR_PATH . "includes/authentication-manager-mail-bank.php")) {
            include_once MAIL_BANK_DIR_PATH . "includes/authentication-manager-mail-bank.php";
         }
         $obj_yahoo_authentication_mail_bank = new yahoo_authentication_mail_bank($this->client_id, $this->client_secret, $this->authorization_token, $this->redirect_uri, $this->sender_email);

         $test_error1 = $obj_yahoo_authentication_mail_bank->process_token_Code(md5(rand()));
         if (isset($test_error1->error)) {
            return $test_error1;
         }
         $this->authorization_token->save_token_mail_bank();
      }
      /*
        Function Name: override_wp_mail_function
        Parameters: No
        Description: This Function is used for overriding wp_mail function.
        Created On: 15-06-2016 10:43
        Created By: Tech Banker Team
       */
      public static function override_wp_mail_function() {
         global $wpdb, $email_configuration_settings;
         $mail_bank_version_number = get_option("mail-bank-version-number");
         if ($mail_bank_version_number != "") {
            $email_configuration_data = $wpdb->get_var
                (
                $wpdb->prepare
                    (
                    "SELECT meta_value FROM " . mail_bank_meta() . "
						WHERE meta_key = %s", "email_configuration"
                )
            );
            $email_configuration_settings = maybe_unserialize($email_configuration_data);
            if ($email_configuration_settings["mailer_type"] == "smtp") {
               if (!function_exists("wp_mail")) {
                  function wp_mail($to, $subject, $message, $headers = "", $attachments = "") {
                     global $wpdb, $email_configuration_settings;
                     $obj_send_test_mail = new mail_bank_auth_host($email_configuration_settings);
                     $result = $obj_send_test_mail->send_test_mail_bank($to, $subject, $message, $headers, $attachments, $email_configuration_settings);
                     return $result;
                  }
               }
            }
         }
      }
   }
}