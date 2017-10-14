<?php
if (!defined("ABSPATH")) {
   exit;
} // Exit if accessed directly
if (!class_exists("mail_bank_manage_email")) {
   if (file_exists(MAIL_BANK_DIR_PATH . "includes/mail-bank-manage-email-address.php")) {
      require_once MAIL_BANK_DIR_PATH . "includes/mail-bank-manage-email-address.php";
   }
   class mail_bank_manage_email {
      const EOL = "\r\n";
      public
          $from,
          $reply_to,
          $to_recipients,
          $cc_recipients,
          $bcc_recipients,
          $subject,
          $body,
          $body_textpart,
          $body_htmlpart,
          $headers,
          $attachments,
          $date,
          $message_id,
          $content_type,
          $charset,
          $boundary;
      // Constructor
      function __construct() {
         $this->headers = array();
         $this->to_recipients = array();
         $this->cc_recipients = array();
         $this->bcc_recipients = array();
      }
      // public static function getinstance()
      // {
      // 	static $inst = null;
      // 	if($inst == null)
      // 	{
      // 		$inst = new mail_bank_manage_email();
      // 	}
      // 	return $inst;
      // }

      public function check_email_body_parts_mail_bank() {
         return empty($this->body_textpart) && empty($this->body_htmlpart);
      }
      public function validate_email_contents_mail_bank($transport) {
         $this->validate_email_headers_mail_bank();
      }
      // This function create body parts based on content type
      public function createBodyParts() {
         if (false !== stripos($this->content_type, "multipart") && !empty($this->boundary)) {
            $this->content_type = sprintf("%s;\r\n\t boundary=\"%s\"", $this->content_type, $this->mb_get_boundary());
         }

         $body = $this->mb_get_body();
         $content_type = $this->mb_get_content_type();
         if ($content_type == "") {
            $content_type = apply_filters("wp_mail_content_type", $content_type);
         }
         if (substr($content_type, 0, 9) === "text/html") {
            $this->mb_set_body_htmlPart($body);
         } else if (substr($content_type, 0, 10) === "text/plain") {
            $this->mb_set_body_textpart($body);
         } else if (substr($content_type, 0, 21) === "multipart/alternative") {
            $arr = explode(PHP_EOL, $body);
            $textBody = "";
            $htmlBody = "";
            $mode = "";
            foreach ($arr as $s) {
               if (substr($s, 0, 25) === "Content-Type: text/plain;") {
                  $mode = "foundText";
               } else if (substr($s, 0, 24) === "Content-Type: text/html;") {
                  $mode = "foundHtml";
               } else if ($mode == "textReading") {
                  $textBody .= $s;
               } else if ($mode == "htmlReading") {
                  $htmlBody .= $s;
               } else if ($mode == "foundText") {
                  $trim = trim($s);
                  if (empty($trim)) {
                     $mode = "textReading";
                  }
               } else if ($mode == "foundHtml") {
                  $trim = trim($s);
                  if (empty($trim)) {
                     $mode = "htmlReading";
                  }
               }
            }
            $this->mb_set_body_htmlPart($htmlBody);
            $this->mb_set_body_textpart($textBody);
         } else {
            $this->mb_set_body_textpart($body);
         }
      }
      // This function validate email headers
      public function validate_email_headers_mail_bank() {
         if (isset($this->reply_to)) {
            $this->mb_get_reply_to()->validate_email_contents_mail_bank("Reply-To");
         }

         // validate the from address
         $this->get_email_address_mail_bank()->validate_email_contents_mail_bank("From");

         // validate the to recipients
         foreach ((array) $this->mb_get_to_recipients() as $to_address) {
            $to_address->validate_email_contents_mail_bank("To");
         }

         // validate the cc recipients
         foreach ((array) $this->mb_get_cc_recipients() as $cc_address) {
            $cc_address->validate_email_contents_mail_bank("Cc");
         }

         // validate the bcc recipients
         foreach ((array) $this->mb_get_bcc_recipients() as $bcc_address) {
            $bcc_address->validate_email_contents_mail_bank("Bcc");
         }
      }
      public function get_email_address_mail_bank() {
         return $this->from;
      }
      // Get the charset.
      public function mb_get_charset() {
         return $this->charset;
      }
      // Set the charset
      public function mb_set_charset($charset) {
         $this->charset = $charset;
      }
      // Get the content type
      public function mb_get_content_type() {
         return $this->content_type;
      }
      public function mb_set_content_type($content_type) {
         $this->content_type = $content_type;
      }
      public function mb_addto($to) {
         $this->mb_add_recipients($this->to_recipients, $to);
      }
      public function mb_add_cc($cc) {
         $this->mb_add_recipients($this->cc_recipients, $cc);
      }
      public function mb_add_bcc($bcc) {
         $this->mb_add_recipients($this->bcc_recipients, $bcc);
      }
      public function mb_add_recipients(&$all_recipients, $recipients) {
         if (!empty($recipients)) {
            $recipients = mail_bank_manage_email_address::convert_string_to_array_mail_bank($recipients);
            foreach ($recipients as $recipient) {
               if (!empty($recipient)) {
                  array_push($all_recipients, new mail_bank_manage_email_address($recipient));
               }
            }
         }
      }
      // This function add headers
      public function mb_add_headers($headers) {
         if (!is_array($headers)) {
            $headers = explode("\n", str_replace("\r\n", "\n", $headers));
         }
         foreach ($headers as $header) {
            if (!empty($header)) {
               if (strpos($header, ":") === false) {
                  if (false !== stripos($header, "boundary=")) {
                     $parts = preg_split("/boundary=/i", trim($header));
                     $this->boundary = trim(str_replace(array(
                         "'",
                         '"'
                             ), '', $parts [1]));
                  }
                  continue;
               }
               list($name, $content) = explode(":", trim($header), 2);
               $this->mb_process_header($name, $content);
            }
         }
      }
      // This function process headers
      public function mb_process_header($name, $content) {
         $name = trim($name);
         $content = trim($content);
         switch (strtolower($name)) {
            case "content-type" :
               if (strpos($content, ";") !== false) {
                  list($type, $charset) = explode(";", $content);
                  $this->mb_set_content_type(trim($type));
                  if (false !== stripos($charset, "charset=")) {
                     $charset = trim(str_replace(array(
                         'charset=',
                         '"'
                             ), '', $charset));
                  } elseif (false !== stripos($charset, "boundary=")) {
                     $this->boundary = trim(str_replace(array(
                         'BOUNDARY=',
                         'boundary=',
                         '"'
                             ), '', $charset));
                     $charset = '';
                  }
                  if (!empty($charset)) {
                     $this->mb_set_charset($charset);
                  }
               } else {
                  $this->mb_set_content_type(trim($content));
               }
               break;
            case "to" :
               $this->mb_addto($content);
               break;
            case "cc" :
               $this->mb_add_cc($content);
               break;
            case "bcc" :
               $this->mb_add_bcc($content);
               break;
            case "from" :
               $this->mb_set_from($content);
               break;
            case "subject" :
               $this->mb_set_subject($content);
               break;
            case "reply-to" :
               $this->mb_set_replyto($content);
               break;
            case "sender" :
               break;
            case "return-path" :
               break;
            case "date" :
               $this->mb_set_date($content);
               break;
            case "message-id" :
               $this->mb_set_messageid($content);
               break;
            default :
               array_push($this->headers, array(
                   "name" => $name,
                   "content" => $content
               ));
               break;
         }
      }
      // Add attachments to the message
      public function mb_add_attachments_to_mail(mail_bank_Zend_Mail $mail) {
         $attachments = $this->attachments;
         if (!is_array($attachments)) {
            $attributes_array = explode(PHP_EOL, $attachments);
         } else {
            $attributes_array = $attachments;
         }
         foreach ($attributes_array as $file) {
            if (file_exists($file)) {
               $at = new mail_bank_Zend_Mime_Part(file_get_contents($file));
               $at->disposition = mail_bank_Zend_Mime::DISPOSITION_ATTACHMENT;
               $at->encoding = mail_bank_Zend_Mime::ENCODING_BASE64;
               $at->filename = basename($file);
               $mail->addAttachment($at);
            }
         }
      }
      function mb_set_body($body) {
         $this->body = $body;
      }
      function mb_set_body_textpart($body_textpart) {
         $this->body_textpart = $body_textpart;
      }
      function mb_set_body_htmlPart($body_htmlpart) {
         $this->body_htmlpart = $body_htmlpart;
      }
      function mb_set_subject($subject) {
         $this->subject = $subject;
      }
      function mb_set_attachments($attachments) {
         $this->attachments = $attachments;
      }
      function mb_set_from($email, $name = null) {
         if (!empty($email)) {
            $this->from = new mail_bank_manage_email_address($email, $name);
         }
      }
      function mb_set_replyto($reply_to) {
         if (!empty($reply_to)) {
            $this->reply_to = new mail_bank_manage_email_address($reply_to);
         }
      }
      function mb_set_messageid($message_id) {
         $this->message_id = $message_id;
      }
      function mb_set_date($date) {
         $this->date = $date;
      }
      public function mb_get_headers() {
         return $this->headers;
      }
      public function mb_get_boundary() {
         return $this->boundary;
      }
      public function mb_get_to_recipients() {
         return $this->to_recipients;
      }
      public function mb_get_cc_recipients() {
         return $this->cc_recipients;
      }
      public function mb_get_bcc_recipients() {
         return $this->bcc_recipients;
      }
      public function mb_get_reply_to() {
         return $this->reply_to;
      }
      public function mb_get_date() {
         return $this->date;
      }
      public function mb_get_message_id() {
         return $this->message_id;
      }
      public function mb_get_subject() {
         return $this->subject;
      }
      public function mb_get_body() {
         return $this->body;
      }
      public function mb_get_body_textPart() {
         return $this->body_textpart;
      }
      public function mb_get_body_html_part() {
         return $this->body_htmlpart;
      }
   }
}