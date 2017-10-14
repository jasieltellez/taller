<?php
if (!defined("ABSPATH")) {
   exit;
} // Exit if accessed directly
if (!class_exists("token_manager_mail_bank")) {

   class token_manager_mail_bank {
      public
          $client_id,
          $client_secret,
          $authorization_token,
          $callback_uri;
      // Constructor
      public function __construct($client_id, $client_secret, mail_bank_manage_token $authorization_token, $callback_uri) {
         $this->client_id = $client_id;
         $this->client_secret = $client_secret;
         $this->authorization_token = $authorization_token;
         $this->callback_uri = $callback_uri;
      }
      public function get_authorization_token() {
         return $this->authorization_token;
      }
      public function check_access_token() {
         $expiry_time = ($this->authorization_token->retrieve_token_expiry_time_mail_bank() - 60);
         $token_expired = time() > $expiry_time;
         return $token_expired;
      }
      // Decoded the received token
      public function process_response($response) {
         $oauth_token = json_decode(stripslashes($response));
         if ($oauth_token === NULL) {
            throw new Exception($response);
         } elseif (isset($oauth_token->{"error"})) {
            if (isset($oauth_token->{"error_description"})) {
               return $oauth_token;
            } else {
               throw new Exception($oauth_token->{"error"});
            }
         } else {
            $this->receive_decode_authorization_token($oauth_token);
         }
      }
      //This function is used to extracts values(expiry time, accesstoken, refresh token)
      public function receive_decode_authorization_token($new_token) {
         // Update expiry time
         if (empty($new_token->{"expires_in"})) {
            throw new Exception("[expires_in] value is missing from token");
         }
         $changed_expiry_time = time() + $new_token->{"expires_in"};
         $this->get_authorization_token()->set_token_expirytime_mail_bank($changed_expiry_time);

         // Update access token
         if (empty($new_token->{"access_token"})) {
            throw new Exception("[access_token] value is missing from token");
         }
         $new_access_token = $new_token->{"access_token"};
         $this->get_authorization_token()->set_access_token_mail_bank($new_access_token);

         // Update refresh token
         if (isset($new_token->{"refresh_token"})) {
            $new_refresh_token = $new_token->{"refresh_token"};
            $this->get_authorization_token()->set_refresh_token_mail_bank($new_refresh_token);
         }
      }
      // get_refresh_token function is used to give specific URL and redirectUri to refresh the access token.
      public function get_refresh_token() {
         $refresh_uri = $this->token_url;
         $callback_uri = $this->callback_uri;
         $configurations = array(
             "client_id" => $this->client_id,
             "client_secret" => $this->client_secret,
             "redirect_uri" => $callback_uri,
             "grant_type" => "refresh_token",
             "refresh_token" => $this->get_authorization_token()->retrieve_refresh_token_mail_bank()
         );
         $response = mail_bank_zend_mail_helper::retrieve_body_from_response_mail_bank($refresh_uri, $configurations);
         $this->process_response($response);
      }
   }
}