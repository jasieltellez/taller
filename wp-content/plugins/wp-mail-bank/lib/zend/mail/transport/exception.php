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
 * @category   Zend
 * @package    mail_bank_Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
if (!defined("ABSPATH")) {
   exit;
} // Exit if accessed directly

/**
 * @see mail_bank_Zend_Mail_Exception
 */
if (file_exists(MAIL_BANK_DIR_PATH . 'lib/zend/mail/exception.php'))
   require_once MAIL_BANK_DIR_PATH . 'lib/zend/mail/exception.php';
/**
 * @category   Zend
 * @package    mail_bank_Zend_Mail
 * @subpackage Transport
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class mail_bank_Zend_Mail_Transport_Exception extends mail_bank_Zend_Mail_Exception {
   
}