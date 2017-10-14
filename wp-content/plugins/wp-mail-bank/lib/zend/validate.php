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
 * @package mail_bank_Zend_Validate
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 * @version $Id$
 */
if (!defined("ABSPATH")) {
   exit;
} // Exit if accessed directly
/**
 * @see mail_bank_Zend_Validate_Interface
 */
if (file_exists(MAIL_BANK_DIR_PATH . 'lib/zend/validate/interface.php'))
   require_once MAIL_BANK_DIR_PATH . 'lib/zend/validate/interface.php';
/**
 * @category Zend
 * @package mail_bank_Zend_Validate
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license http://framework.zend.com/license/new-bsd New BSD License
 */
class mail_bank_Zend_Validate implements mail_bank_Zend_Validate_Interface {
   /**
    * Validator chain
    *
    * @var array
    */
   protected $_validators = array();
   /**
    * Array of validation failure messages
    *
    * @var array
    */
   protected $_messages = array();
   /**
    * Default Namespaces
    *
    * @var array
    */
   protected static $_defaultNamespaces = array();
   /**
    * Array of validation failure message codes
    *
    * @var array
    * @deprecated Since 1.5.0
    */
   protected $_errors = array();
   /**
    * Adds a validator to the end of the chain
    *
    * If $breakChainOnFailure is true, then if the validator fails, the next validator in the chain,
    * if one exists, will not be executed.
    *
    * @param	mail_bank_Zend_Validate_Interface $validator
    * @param	boolean $breakChainOnFailure
    * @return mail_bank_Zend_Validate Provides a fluent interface
    */
   public function addValidator(mail_bank_Zend_Validate_Interface $validator, $breakChainOnFailure = false) {
      $this->_validators[] = array(
          'instance' => $validator,
          'breakChainOnFailure' => (boolean) $breakChainOnFailure
      );
      return $this;
   }
   /**
    * Returns true if and only if $value passes all validations in the chain
    *
    * Validators are run in the order in which they were added to the chain (FIFO).
    *
    * @param	mixed $value
    * @return boolean
    */
   public function isValid($value) {
      $this->_messages = array();
      $this->_errors = array();
      $result = true;
      foreach ($this->_validators as $element) {
         $validator = $element['instance'];
         if ($validator->isValid($value)) {
            continue;
         }
         $result = false;
         $messages = $validator->getMessages();
         $this->_messages = array_merge($this->_messages, $messages);
         $this->_errors = array_merge($this->_errors, array_keys($messages));
         if ($element['breakChainOnFailure']) {
            break;
         }
      }
      return $result;
   }
   /**
    * Defined by mail_bank_Zend_Validate_Interface
    *
    * Returns array of validation failure messages
    *
    * @return array
    */
   public function getMessages() {
      return $this->_messages;
   }
   /**
    * Defined by mail_bank_Zend_Validate_Interface
    *
    * Returns array of validation failure message codes
    *
    * @return array
    * @deprecated Since 1.5.0
    */
   public function getErrors() {
      return $this->_errors;
   }
   /**
    * Returns the set default namespaces
    *
    * @return array
    */
   public static function getDefaultNamespaces() {
      return self::$_defaultNamespaces;
   }
   /**
    * Sets new default namespaces
    *
    * @param array|string $namespace
    * @return null
    */
   public static function setDefaultNamespaces($namespace) {
      if (!is_array($namespace)) {
         $namespace = array((string) $namespace);
      }

      self::$_defaultNamespaces = $namespace;
   }
   /**
    * Adds a new default namespace
    *
    * @param array|string $namespace
    * @return null
    */
   public static function addDefaultNamespaces($namespace) {
      if (!is_array($namespace)) {
         $namespace = array((string) $namespace);
      }

      self::$_defaultNamespaces = array_unique(array_merge(self::$_defaultNamespaces, $namespace));
   }
   /**
    * Returns true when defaultNamespaces are set
    *
    * @return boolean
    */
   public static function hasDefaultNamespaces() {
      return (!empty(self::$_defaultNamespaces));
   }
   /**
    * @param	mixed $value
    * @param	string $classBaseName
    * @param	array $args OPTIONAL
    * @param	mixed $namespaces OPTIONAL
    * @return boolean
    * @throws mail_bank_Zend_Validate_Exception
    */
   public static function is($value, $classBaseName, array $args = array(), $namespaces = array()) {
      $namespaces = array_merge((array) $namespaces, self::$_defaultNamespaces, array('mail_bank_Zend_Validate'));
      $className = ucfirst($classBaseName);
      try {
         if (!class_exists($className, false)) {
            if (file_exists(MAIL_BANK_DIR_PATH . 'lib/zend/loader.php'))
               require_once MAIL_BANK_DIR_PATH . 'lib/zend/loader.php';

            foreach ($namespaces as $namespace) {
               $class = $namespace . '_' . $className;
               $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
               if (mail_bank_Zend_Loader::isReadable($file)) {
                  mail_bank_Zend_Loader::loadClass($class);
                  $className = $class;
                  break;
               }
            }
         }

         $class = new ReflectionClass($className);
         if ($class->implementsInterface('mail_bank_Zend_Validate_Interface')) {
            if ($class->hasMethod('__construct')) {
               $keys = array_keys($args);
               $numeric = false;
               foreach ($keys as $key) {
                  if (is_numeric($key)) {
                     $numeric = true;
                     break;
                  }
               }

               if ($numeric) {
                  $object = $class->newInstanceArgs($args);
               } else {
                  $object = $class->newInstance($args);
               }
            } else {
               $object = $class->newInstance();
            }

            return $object->isValid($value);
         }
      } catch (mail_bank_Zend_Validate_Exception $ze) {
         // if there is an exception while validating throw it
         throw $ze;
      } catch (Exception $e) {
         // fallthrough and continue for missing validation classes
      }
      if (file_exists(MAIL_BANK_DIR_PATH . 'lib/zend/validate/exception.php'))
         require_once MAIL_BANK_DIR_PATH . 'lib/zend/validate/exception.php';

      throw new mail_bank_Zend_Validate_Exception("Validate class not found from basename '$classBaseName'");
   }
   /**
    * Returns the maximum allowed message length
    *
    * @return integer
    */
   public static function getMessageLength() {
      if (file_exists(MAIL_BANK_DIR_PATH . 'lib/zend/validate/abstract.php'))
         require_once MAIL_BANK_DIR_PATH . 'lib/zend/validate/abstract.php';

      return mail_bank_Zend_Validate_Abstract::getMessageLength();
   }
   /**
    * Sets the maximum allowed message length
    *
    * @param integer $length
    */
   public static function setMessageLength($length = -1) {
      if (file_exists(MAIL_BANK_DIR_PATH . 'lib/zend/validate/abstract.php'))
         require_once MAIL_BANK_DIR_PATH . 'lib/zend/validate/abstract.php';

      mail_bank_Zend_Validate_Abstract::setMessageLength($length);
   }
   /**
    * Returns the default translation object
    *
    * @return mail_bank_Zend_Translate_Adapter|null
    */
   public static function getDefaultTranslator($translator = null) {
      if (file_exists(MAIL_BANK_DIR_PATH . 'lib/zend/validate/abstract.php'))
         require_once MAIL_BANK_DIR_PATH . 'lib/zend/validate/abstract.php';

      return mail_bank_Zend_Validate_Abstract::getDefaultTranslator();
   }
   /**
    * Sets a default translation object for all validation objects
    *
    * @param mail_bank_Zend_Translate|mail_bank_Zend_Translate_Adapter|null $translator
    */
   public static function setDefaultTranslator($translator = null) {
      if (file_exists(MAIL_BANK_DIR_PATH . 'lib/zend/validate/abstract.php'))
         require_once MAIL_BANK_DIR_PATH . 'lib/zend/validate/abstract.php';

      mail_bank_Zend_Validate_Abstract::setDefaultTranslator($translator);
   }
}