<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category Zend
 * @package mail_bank_Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd	  New BSD License
 * @version $Id$
 */
if (!defined("ABSPATH")) {
   exit;
} // Exit if accessed directly
/**
 * @see mail_bank_Zend_Mime
 */
if (file_exists(MAIL_BANK_DIR_PATH . 'lib/zend/mime.php'))
   require_once MAIL_BANK_DIR_PATH . 'lib/zend/mime.php';

/**
 * @see mail_bank_Zend_Mail_Protocol_Smtp
 */
if (file_exists(MAIL_BANK_DIR_PATH . 'lib/zend/mail/protocol/smtp.php'))
   require_once MAIL_BANK_DIR_PATH . 'lib/zend/mail/protocol/smtp.php';

/**
 * @see mail_bank_Zend_Mail_Transport_Abstract
 */
if (file_exists(MAIL_BANK_DIR_PATH . 'lib/zend/mail/transport/abstract.php'))
   require_once MAIL_BANK_DIR_PATH . 'lib/zend/mail/transport/abstract.php';
/**
 * SMTP connection object
 *
 * Loads an instance of mail_bank_Zend_Mail_Protocol_Smtp and forwards smtp transactions
 *
 * @category Zend
 * @package  mail_bank_Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd	  New BSD License
 */
class mail_bank_Zend_Mail_Transport_Smtp extends mail_bank_Zend_Mail_Transport_Abstract {
   /**
    * EOL character string used by transport
    * @var string
    * @access public
    */
   public $EOL = "\n";
   /**
    * Remote smtp hostname or i.p.
    *
    * @var string
    */
   protected $_host;
   /**
    * Port number
    *
    * @var integer|null
    */
   protected $_port;
   /**
    * Local client hostname or i.p.
    *
    * @var string
    */
   protected $_name = 'localhost';
   /**
    * Authentication type OPTIONAL
    *
    * @var string
    */
   protected $_auth;
   /**
    * Config options for authentication
    *
    * @var array
    */
   protected $_config;
   /**
    * Instance of mail_bank_Zend_Mail_Protocol_Smtp
    *
    * @var mail_bank_Zend_Mail_Protocol_Smtp
    */
   protected $_connection;
   /**
    * Constructor.
    *
    * @param  string $host OPTIONAL (Default: 127.0.0.1)
    * @param  array|null $config OPTIONAL (Default: null)
    * @return void
    *
    * @todo Someone please make this compatible
    * with the SendMail transport class.
    */
   public function __construct($host = '127.0.0.1', Array $config = array()) {
      if (isset($config['name'])) {
         $this->_name = $config['name'];
      }
      if (isset($config['port'])) {
         $this->_port = $config['port'];
      }
      if (isset($config['auth'])) {
         $this->_auth = $config['auth'];
      }

      $this->_host = $host;
      $this->_config = $config;
   }
   /**
    * Class destructor to ensure all open connections are closed
    *
    * @return void
    */
   public function __destruct() {
      if ($this->_connection instanceof mail_bank_Zend_Mail_Protocol_Smtp) {
         try {
            $this->_connection->quit();
         } catch (mail_bank_Zend_Mail_Protocol_Exception $e) {
            // ignore
         }
         $this->_connection->disconnect();
      }
   }
   /**
    * Sets the connection protocol instance
    *
    * @param mail_bank_Zend_Mail_Protocol_Abstract $client
    *
    * @return void
    */
   public function setConnection(mail_bank_Zend_Mail_Protocol_Abstract $connection) {
      $this->_connection = $connection;
   }
   /**
    * Gets the connection protocol instance
    *
    * @return mail_bank_Zend_Mail_Protocol|null
    */
   public function getConnection() {
      return $this->_connection;
   }
   /**
    * Send an email via the SMTP connection protocol
    *
    * The connection via the protocol adapter is made just-in-time to allow a
    * developer to add a custom adapter if required before mail is sent.
    *
    * @return void
    * @todo Rename this to sendMail, it's a public method...
    */
   public function _sendMail() {
      // If sending multiple messages per session use existing adapter
      if (!($this->_connection instanceof mail_bank_Zend_Mail_Protocol_Smtp)) {
         // Check if authentication is required and determine required class
         $connectionClass = 'mail_bank_Zend_Mail_Protocol_Smtp';
         if ($this->_auth != "none") {
            $connectionClass .= '_Auth_' . ucwords($this->_auth);
         }
         if (!class_exists($connectionClass)) {
            if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/mail/protocol/smtp/auth/plain.php")) {
               include_once MAIL_BANK_DIR_PATH . "lib/zend/mail/protocol/smtp/auth/plain.php";
            }
            if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/mail/protocol/smtp/auth/crammd5.php")) {
               include_once MAIL_BANK_DIR_PATH . "lib/zend/mail/protocol/smtp/auth/crammd5.php";
            }
            if (file_exists(MAIL_BANK_DIR_PATH . "lib/zend/mail/protocol/smtp/auth/login.php")) {
               include_once MAIL_BANK_DIR_PATH . "lib/zend/mail/protocol/smtp/auth/login.php";
            }
            // require_once MAIL_BANK_DIR_PATH.'lib/zend/loader.php';
            // mail_bank_Zend_Loader::loadClass($connectionClass);
         }
         $this->setConnection(new $connectionClass($this->_host, $this->_port, $this->_config));
         $this->_connection->connect();
         $this->_connection->helo($this->_name);
      } else {
         // Reset connection to ensure reliable transaction
         $this->_connection->rset();
      }

      // Set sender email address
      $this->_connection->mail($this->_mail->getReturnPath());

      // Set recipient forward paths
      foreach ($this->_mail->getRecipients() as $recipient) {
         $this->_connection->rcpt($recipient);
      }

      // Issue DATA command to client
      $this->_connection->data($this->header . mail_bank_Zend_Mime::LINEEND . $this->body);
   }
   /**
    * Format and fix headers
    *
    * Some SMTP servers do not strip BCC headers. Most clients do it themselves as do we.
    *
    * @access  protected
    * @param	array $headers
    * @return  void
    * @throws  mail_bank_Zend_Transport_Exception
    */
   protected function _prepareHeaders($headers) {
      if (!$this->_mail) {
         /**
          * @see mail_bank_Zend_Mail_Transport_Exception
          */
         if (file_exists(MAIL_BANK_DIR_PATH . 'lib/zend/mail/transport/exception.php'))
            require_once MAIL_BANK_DIR_PATH . 'lib/zend/mail/transport/exception.php';

         throw new mail_bank_Zend_Mail_Transport_Exception('_prepareHeaders requires a registered mail_bank_Zend_Mail object');
      }

      unset($headers['Bcc']);

      // Prepare headers
      parent::_prepareHeaders($headers);
   }
}