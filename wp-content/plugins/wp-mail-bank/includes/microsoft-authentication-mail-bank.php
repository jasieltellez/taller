<?php
if (!defined("ABSPATH")) {
   exit;
} // Exit if accessed directly
if (file_exists(MAIL_BANK_DIR_PATH . "includes/token-manager-mail-bank.php")) {
   require_once MAIL_BANK_DIR_PATH . "includes/token-manager-mail-bank.php";
}

if (!class_exists("microsoft_authentication_mail_bank")) {

   class microsoft_authentication_mail_bank extends token_manager_mail_bank {
      public
          $client_id,
          $client_secret,
          $callback_uri,
          $token_url;
      //Constructor
      public function __construct($client_id, $client_secret, mail_bank_manage_token $authorization_token, $callback_uri) {
         $this->client_id = $client_id;
         $this->client_secret = $client_secret;
         $this->callback_uri = $callback_uri;
         $this->token_url = "https://login.live.com/oauth20_token.srf";
         parent::__construct($client_id, $client_secret, $authorization_token, $callback_uri);
      }
      // This function return the verification code after successfull authentication
      public function get_token_code($transactionId) {
         $configurations = array(
             "response_type" => "code",
             "redirect_uri" => urlencode($this->callback_uri),
             "client_id" => $this->client_id,
             "client_secret" => $this->client_secret,
             "scope" => urlencode("wl.imap,wl.offline_access"),
             "access_type" => "offline",
             "approval_prompt" => "force"
         );
         $oauth_url = "https://login.live.com/oauth20_authorize.srf?" . build_query($configurations);
         echo $oauth_url;
      }
      // This function proccess the grant code
      public function process_token_Code($transactionId) {
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
            $microsoft_secret_key = $this->process_response($response);
            if (isset($microsoft_secret_key->error)) {
               return $microsoft_secret_key;
            } else {
               $this->get_authorization_token()->set_vendorname_mail_bank("microsoft");
               return "1";
            }
         } else {
            return false;
         }
      }
   }
}