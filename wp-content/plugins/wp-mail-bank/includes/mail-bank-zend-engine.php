<?php
if (!defined("ABSPATH")) {
   exit;
} // Exit if accessed directly
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/loader.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/loader.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/registry.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/registry.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/mime/message.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/mime/message.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/mime/part.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/mime/part.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/mime.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/mime.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/validate/interface.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/validate/interface.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/validate/abstract.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/validate/abstract.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/validate.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/validate.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/validate/ip.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/validate/ip.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/validate/hostname.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/validate/hostname.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/mail.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/mail.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/exception.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/exception.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/mail/exception.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/mail/exception.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/mail/transport/exception.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/mail/transport/exception.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/mail/transport/abstract.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/mail/transport/abstract.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/mail/transport/smtp.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/mail/transport/smtp.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/mail/transport/sendmail.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/mail/transport/sendmail.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/mail/protocol/abstract.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/mail/protocol/abstract.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/mail/protocol/exception.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/mail/protocol/exception.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/mail/protocol/smtp.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/mail/protocol/smtp.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/mail/protocol/smtp/auth/oauth2.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/mail/protocol/smtp/auth/oauth2.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/mail/protocol/smtp/auth/plain.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/mail/protocol/smtp/auth/plain.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/mail/protocol/smtp/auth/crammd5.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/mail/protocol/smtp/auth/crammd5.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/mail/protocol/smtp/auth/login.php")) {
   include_once MAIL_BANK_DIR_PATH . "lib/zend/mail/protocol/smtp/auth/login.php";
}
if (file_exists(MAIL_BANK_DIR_PATH . "includes/mail-bank-configuration-provider.php")) {
   include_once MAIL_BANK_DIR_PATH . "includes/mail-bank-configuration-provider.php";
}

if (!class_exists("mail_bank_zend_engine")) {

   class mail_bank_zend_engine {
      public
          $transcript,
          $mail_bank_options,
          $transport;
      function __construct($transport) {
         $this->transport = $transport;
         $mb_config_provider_obj = new mail_bank_configuration_provider();
         $this->mail_bank_options = $mb_config_provider_obj->get_configuration_settings();
      }
      // This Function is used to send Email.
      public function send_email_mail_bank(mail_bank_manage_email $message) {
         $envelope_from = new mail_bank_manage_email_address($this->mail_bank_options["email_address"]);
         $envelope_from->validate_email_contents_mail_bank("Envelope From");
         $charset = $message->mb_get_charset();
         $mail = new mail_bank_Zend_Mail($charset);

         // Add headers
         foreach ((array) $message->mb_get_headers() as $header) {
            $mail->addHeader($header["name"], $header["content"], true);
         }

         $content_type = $message->mb_get_content_type();
         if (!empty($content_type)) {
            $mail->addHeader("Content-Type", $content_type, false);
         }

         // Add the from header
         $fromHeader = $this->get_sender_from_email_mail_bank($message, $mail);
         $mail->addHeader("Sender", $this->mail_bank_options["email_address"], false);

         // Add to recipients
         foreach ((array) $message->mb_get_to_recipients() as $recipient) {
            $mail->addTo($recipient->mb_get_email(), $recipient->mb_get_name());
         }

         // Add cc recipients
         if ($this->mail_bank_options["cc"] == "") {
            foreach ((array) $message->mb_get_cc_recipients() as $recipient) {
               $mail->addCc($recipient->mb_get_email(), $recipient->mb_get_name());
            }
         } else {
            $cc_address_array = explode(",", $this->mail_bank_options["cc"]);
            foreach ($cc_address_array as $cc_address) {
               $mail->addCc($cc_address);
            }
         }


         // Add bcc recepients
         if ($this->mail_bank_options["bcc"] == "") {
            foreach ((array) $message->mb_get_bcc_recipients() as $recipient) {
               $mail->addBcc($recipient->mb_get_email(), $recipient->mb_get_name());
            }
         } else {
            $bcc_address_array = explode(",", $this->mail_bank_options["bcc"]);
            foreach ($bcc_address_array as $bcc_address) {
               $mail->addBcc($bcc_address);
            }
         }

         // Add reply to
         $reply_to = $message->mb_get_reply_to();
         if ($this->mail_bank_options["reply_to"] != "") {
            $mail->setReplyTo($this->mail_bank_options["reply_to"]);
         } elseif (isset($reply_to)) {
            $mail->setReplyTo($reply_to->mb_get_email());
         }

         // Add date
         $date = $message->mb_get_date();
         if (!empty($date)) {
            $mail->setDate($date);
         }

         // Add message id
         $message_id = $message->mb_get_message_id();
         if (!empty($message_id)) {
            $mail->setMessageId($message_id);
         }

         // Add subject of the email
         if (null !== $message->mb_get_subject()) {
            $mail->setSubject($message->mb_get_subject());
         }

         // Add message content of the email
         //{
         $text_part = $message->mb_get_body_textPart();
         if (!empty($text_part)) {
            $mail->setBodyText($text_part);
         }
         $html_part = $message->mb_get_body_html_part();
         if (!empty($html_part)) {
            $mail->setBodyHtml($html_part);
         }
         //}
         // Add attachments to the email
         $message->mb_add_attachments_to_mail($mail);

         // Create the SMTP transport
         $zend_transport = $this->transport->initiate_zendmail_transport_mail_bank($this->mail_bank_options["hostname"], array());
         try {
            // Send the message
            $mail->send($zend_transport);
            if ($zend_transport->getConnection() && !mail_bank_zend_mail_helper::check_field_mail_bank($zend_transport->getConnection()->getLog())) {
               $this->transcript = $zend_transport->getConnection()->getLog();
            } else if (method_exists($zend_transport, "get_output_mail_bank") && !mail_bank_zend_mail_helper::check_field_mail_bank($zend_transport->get_output_mail_bank())) {
               // use the API response
               $this->transcript = $zend_transport->get_output_mail_bank();
            } else if (method_exists($zend_transport, "getMessage") && !mail_bank_zend_mail_helper::check_field_mail_bank($zend_transport->getMessage())) {
               //use the raw message as the transcript
               $this->transcript = $zend_transport->getMessage();
            }
         } catch (Exception $e) {
            // In case of Error
            if ($zend_transport->getConnection() && !mail_bank_zend_mail_helper::check_field_mail_bank($zend_transport->getConnection()->getLog())) {
               $this->transcript = $zend_transport->getConnection()->getLog();
            } else if (method_exists($zend_transport, "get_output_mail_bank") && !mail_bank_zend_mail_helper::check_field_mail_bank($zend_transport->get_output_mail_bank())) {
               // Use API response
               $this->transcript = $zend_transport->get_output_mail_bank();
            } else if (method_exists($zend_transport, "getMessage") && !mail_bank_zend_mail_helper::check_field_mail_bank($zend_transport->getMessage())) {
               // Use message as the transcript
               $this->transcript = $zend_transport->getMessage();
            }

            // Get the current exception message
            $message = $e->getMessage();
            if ($e->getCode() == 334) {
               $message = "From Email should be of same account used to create the Client Id.";
            }
            $exception = new Exception($message, $e->getCode());
            // Throws the new exception
            throw $exception;
         }
      }
      // This function is used to get the sender from mail_bank_manage_email and add it to the mail_bank_Zend_Mail object
      public function get_sender_from_email_mail_bank($message, $mail) {
         $sender = $message->get_email_address_mail_bank();
         $sender_email = $sender->mb_get_email();
         $sender_name = $sender->mb_get_name();
         if (($this->mail_bank_options["sender_name_configuration"] == "override") && ($this->mail_bank_options["from_email_configuration"] == "override")) {
            $mail->setFrom($this->mail_bank_options["sender_email"], html_entity_decode($this->mail_bank_options["sender_name"], ENT_QUOTES));
         } elseif (($this->mail_bank_options["sender_name_configuration"] == "dont_override") && ($this->mail_bank_options["from_email_configuration"] == "dont_override")) {
            $mail->setFrom($sender_email, $sender_name);
         } elseif (($this->mail_bank_options["sender_name_configuration"] == "override") && ($this->mail_bank_options["from_email_configuration"] == "dont_override")) {
            $mail->setFrom($sender_email, html_entity_decode($this->mail_bank_options["sender_name"], ENT_QUOTES));
         } else {
            $mail->setFrom($this->mail_bank_options["sender_email"], $sender_name);
         }
         return $sender;
      }
      // This funtion is used to return SMTP session Transcript.
      public function get_output_mail_bank() {
         return $this->transcript;
      }
   }
}