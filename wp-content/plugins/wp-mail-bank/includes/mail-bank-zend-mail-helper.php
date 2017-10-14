<?php
if (!defined("ABSPATH")) {
   exit;
} // Exit if accessed directly
if (!class_exists("mail_bank_zend_mail_helper")) {

   class mail_bank_zend_mail_helper {
      public static $validate_email;
      public static function email_domains_mail_bank($hostname, $needle) {
         $length = strlen($needle);
         return(substr($hostname, - $length) === $needle);
      }
      // This function is used to make the outgoing Http requests.
      public static function retrieve_body_from_response_mail_bank($url, $parameters, array $headers = array()) {
         $response = mail_bank_zend_mail_helper::post_request_mail_bank($url, $parameters, $headers);
         if (isset($response["error"])) {
            return json_encode($response);
         }
         $body = wp_remote_retrieve_body($response);
         return $body;
      }
      // This function is used to make outgoing Http requests.
      public static function post_request_mail_bank($url, $parameters = array(), array $headers = array()) {
         $args = array(
             "timeout" => "10000",
             "headers" => $headers,
             "body" => $parameters
         );
         $response = wp_remote_post($url, $args);

         if (is_wp_error($response)) {
            return array("error" => "An error occured", "error_description" => $response->get_error_message());
         } else {
            return $response;
         }
      }
      // This function is used for basic field validation.
      public static function check_field_mail_bank($text) {
         return(!isset($text) || trim($text) === "");
      }
      // This function is used to validate an email-address.
      public static function email_validation_mail_bank($email) {
         require_once MAIL_BANK_DIR_PATH . "lib/zend/exception.php";
         require_once MAIL_BANK_DIR_PATH . "lib/zend/registry.php";
         require_once MAIL_BANK_DIR_PATH . "lib/zend/validate/exception.php";
         require_once MAIL_BANK_DIR_PATH . "lib/zend/validate/interface.php";
         require_once MAIL_BANK_DIR_PATH . "lib/zend/validate/abstract.php";
         require_once MAIL_BANK_DIR_PATH . "lib/zend/validate/ip.php";
         require_once MAIL_BANK_DIR_PATH . "lib/zend/validate/hostname.php";
         require_once MAIL_BANK_DIR_PATH . "lib/zend/validate/emailaddress.php";
         if (!isset(mail_bank_zend_mail_helper::$validate_email)) {
            mail_bank_zend_mail_helper::$validate_email = new mail_bank_Zend_Validate_EmailAddress();
         }
         return mail_bank_zend_mail_helper::$validate_email->isValid($email);
      }
   }
}