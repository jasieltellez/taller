<?php
if (!defined("ABSPATH")) {
   exit;
} // Exit if accessed directly
if (file_exists(MAIL_BANK_DIR_PATH . "includes/token-manager-mail-bank.php")) {
   require_once MAIL_BANK_DIR_PATH . "includes/token-manager-mail-bank.php";
}

if (!class_exists("google_authentication_mail_bank")) {

   class google_authentication_mail_bank extends token_manager_mail_bank {
      public
          $client_id,
          $client_secret,
          $callback_uri,
          $sender_email,
          $token_url;
      // Constructor
      public function __construct($client_id, $client_secret, mail_bank_manage_token $authorization_token, $callback_uri, $sender_email) {
         $this->sender_email = $sender_email;
         $this->client_id = $client_id;
         $this->client_secret = $client_secret;
         $this->callback_uri = $callback_uri;
         $this->token_url = "https://www.googleapis.com/oauth2/v3/token";
         parent::__construct($client_id, $client_secret, $authorization_token, $callback_uri);
      }
      // This function request the token code
      public function get_token_code($state_id) {
         $configurations = array(
             "response_type" => "code",
             "redirect_uri" => urlencode($this->callback_uri),
             "client_id" => $this->client_id,
             "scope" => urlencode("https://mail.google.com/"),
             "access_type" => "offline",
             "approval_prompt" => "force",
             "state" => $state_id,
             "login_hint" => $this->sender_email
         );

         echo $oauth_url = "https://accounts.google.com/o/oauth2/auth?" . build_query($configurations);
      }
      // This function process the token code
      public function process_token_Code($state_id) {
         if (isset($_REQUEST["access_token"])) {
            $code = esc_attr($_REQUEST["access_token"]);
            $configurations = array(
                "client_id" => $this->client_id,
                "client_secret" => $this->client_secret,
                "grant_type" => "authorization_code",
                "redirect_uri" => $this->callback_uri,
                "code" => $code
            );
            $response = mail_bank_zend_mail_helper::retrieve_body_from_response_mail_bank($this->token_url, $configurations);
            $test_error = $this->process_response($response);
            if (isset($test_error->error)) {
               return $test_error;
            } else {
               $this->get_authorization_token()->set_vendorname_mail_bank("google");
               return "1";
            }
         } else {
            return false;
         }
      }
   }
}