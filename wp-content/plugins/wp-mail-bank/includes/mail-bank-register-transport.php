<?php
if (!defined("ABSPATH")) {
   exit;
} // Exit if accessed directly
if (file_exists(MAIL_BANK_DIR_PATH . "includes/mail-bank-configure-transport.php")) {
   require_once MAIL_BANK_DIR_PATH . "includes/mail-bank-configure-transport.php";
}
class mail_bank_register_transport {
   public static $transport;
   public function listing_transport_mail_bank($instance) {
      self::$transport = $instance;
   }
   // This function is used to get the transport
   public function retrieve_mailertype_mail_bank() {
      return self::$transport;
   }
}