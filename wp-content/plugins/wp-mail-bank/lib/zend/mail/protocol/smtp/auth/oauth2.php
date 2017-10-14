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
 * @subpackage Protocol
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd	  New BSD License
 * @version $Id: Login.php 24593 2012-01-05 20:35:02Z matthew $
 */
/**
 * @see mail_bank_Zend_Mail_Protocol_Smtp
 */
/**
 * Performs Oauth2 authentication
 *
 * @category Zend
 * @package mail_bank_Zend_Mail
 * @subpackage Protocol
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd	  New BSD License
 */
if (!defined("ABSPATH")) {
   exit;
} // Exit if accessed directly
class mail_bank_Zend_Mail_Protocol_Smtp_Auth_Oauth2 extends mail_bank_Zend_Mail_Protocol_Smtp {
   //LOGIN xoauth2 request
   protected $_xoauth2_request;
   //Constructor.
   public function __construct($host = "127.0.0.1", $port = null, $config = null) {
      if (is_array($config)) {
         if (isset($config["xoauth2_request"])) {
            $this->_xoauth2_request = $config["xoauth2_request"];
         }
      }
      parent::__construct($host, $port, $config);
   }
   //Perform LOGIN authentication with supplied credentials
   public function auth() {
      // Ensure AUTH has not already been initiated.
      parent::auth();

      $this->_send("AUTH XOAUTH2 " . $this->_xoauth2_request);
      $this->_expect(235);
      $this->_auth = true;
   }
}