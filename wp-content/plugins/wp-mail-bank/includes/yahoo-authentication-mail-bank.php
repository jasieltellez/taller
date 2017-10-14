<?php
if (!defined("ABSPATH")) {
   exit;
} // Exit if accessed directly
if (file_exists(MAIL_BANK_DIR_PATH . "includes/token-manager-mail-bank.php")) {
   require_once MAIL_BANK_DIR_PATH . "includes/token-manager-mail-bank.php";
}

if (!class_exists("yahoo_authentication_mail_bank")) {

   class yahoo_authentication_mail_bank extends token_manager_mail_bank {
      public
          $client_id,
          $client_secret,
          $callback_uri,
          $token_url;
      // Constructor
      public function __construct($client_id, $client_secret, mail_bank_manage_token $authorization_token, $callback_uri) {
         $this->client_id = $client_id;
         $this->client_secret = $client_secret;
         $this->callback_uri = $callback_uri;
         $this->token_url = "https://api.login.yahoo.com/oauth2/get_token";
         parent::__construct($client_id, $client_secret, $authorization_token, $callback_uri);
      }
      // This function is used to get token code.
      public function get_token_code($transactionId) {
         $configurations = array(
             "response_type" => "code",
             "redirect_uri" => urlencode($this->callback_uri),
             "client_id" => $this->client_id,
             "state" => $transactionId,
             "language" => get_locale()
         );
         echo $authUrl = "https://api.login.yahoo.com/oauth2/request_auth?" . build_query($configurations);
      }
      // This function is used to process token code
      public function process_token_Code($transactionId) {
         if (isset($_REQUEST["access_token"])) {
            $code = esc_attr($_REQUEST["access_token"]);

            $headers = array(
                "Authorization" => sprintf("Basic %s", base64_encode($this->client_id . ':' . $this->client_secret))
            );
            $configurations = array(
                "code" => $code,
                "grant_type" => "authorization_code",
                "redirect_uri" => $this->callback_uri
            );
            $response = mail_bank_zend_mail_helper::retrieve_body_from_response_mail_bank($this->token_url, $configurations, $headers);
            $yahoo_secret_key = $this->process_response($response);
            if (isset($yahoo_secret_key->error)) {
               return $yahoo_secret_key;
            } else {
               $this->get_authorization_token()->set_vendorname_mail_bank("yahoo");
               return "1";
            }
         } else {
            return false;
         }
      }
      //This function is used to get refresh token for new access token
      public function get_refresh_token() {
         $refresh_url = $this->token_url;
         $callback_uri = $this->callback_uri;
         $headers = array(
             "Authorization" => sprintf("Basic %s", base64_encode($this->client_id . ':' . $this->client_secret))
         );

         $configurations = array(
             "redirect_uri" => $callback_uri,
             "grant_type" => "refresh_token",
             "refresh_token" => $this->get_authorization_token()->retrieve_refresh_token_mail_bank()
         );
         $response = mail_bank_zend_mail_helper::retrieve_body_from_response_mail_bank($this->token_url, $configurations, $headers);
         $this->process_response($response);
      }
   }
}