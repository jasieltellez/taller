<?php
if (!defined("ABSPATH")) {
   exit;
} // Exit if accessed directly
if (!class_exists("mail_bank_manage_email_address")) {

   class mail_bank_manage_email_address {
      public
          $name,
          $email;
      public function __construct($email, $name = null) {
         if (preg_match('/(.*)<(.+)>/', $email, $matches)) {
            if (count($matches) == 3) {
               $name = $matches [1];
               $email = $matches [2];
            }
         }
         $this->mb_set_email(trim($email));
         $this->mb_set_name(trim($name));
      }
      public function mb_get_name() {
         return $this->name;
      }
      public function mb_get_email() {
         return $this->email;
      }
      public function mb_email_format() {
         $name = $this->mb_get_name();
         if (!empty($name)) {
            return sprintf("%s <%s>", $this->mb_get_name(), $this->mb_get_email());
         } else {
            return sprintf("%s", $this->mb_get_email());
         }
      }
      public function mb_set_name($name) {
         $this->name = $name;
      }
      public function mb_set_email($email) {
         $this->email = $email;
      }
      // This function validate the email address.
      public function validate_email_contents_mail_bank($description = "") {
         if (!mail_bank_zend_mail_helper::email_validation_mail_bank($this->email)) {
            if (empty($description)) {
               $message = sprintf('Invalid e-mail address "%s"', $this->email);
            } else {
               $message = sprintf('Invalid "%1$s" e-mail address "%2$s"', $description, $this->email);
            }
            throw new Exception($message);
         }
      }
      // This function takes a string or array of addresses and return an array.
      public static function convert_string_to_array_mail_bank($emails) {
         if (!is_array($emails)) {
            $t = explode(",", $emails);
            $emails = array();
            foreach ($t as $k => $v) {
               if (strpos($v, ',') !== false) {
                  $t[$k] = '"' . str_replace(' <', '" <', $v);
               }
               $simplified_email = trim($t [$k]);
               array_push($emails, $simplified_email);
            }
         }
         return $emails;
      }
   }
}