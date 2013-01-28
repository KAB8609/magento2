<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Logger model
 */
class Mage_Core_Model_Logger
{
    /**#@+
     * Keys that stand for particular log streams
     */
    const LOGGER_SYSTEM    = 'system';
    const LOGGER_EXCEPTION = 'exception';
    /**#@-*/

    /**
     * @var array
     */
    protected $_loggers = array();

    /**
     * @var Mage_Core_Model_Dir
     */
    protected $_dirs = null;

    public function __construct(Mage_Core_Model_Dir $dirs)
    {
        $this->_dirs = $dirs;
    }

    /**
     * Add a logger by specified key
     *
     * Second argument is a file name (relative to log directory) or a PHP "wrapper"
     *
     * @param string $loggerKey
     * @param string $fileOrWrapper
     * @param string $writerClass
     * @return Mage_Core_Model_Logger
     * @link http://php.net/wrappers
     */
    public function addStreamLog($loggerKey, $fileOrWrapper = '', $writerClass = '')
    {
        $file = $fileOrWrapper ?: "{$loggerKey}.log";
        if (!preg_match('#^[a-z][a-z0-9+.-]*\://#i', $file)) {
            $logDir = $this->_dirs->getDir(Mage_Core_Model_Dir::LOG);
            $file = $logDir . DIRECTORY_SEPARATOR . $file;
        }
        if (!$writerClass || !is_subclass_of($writerClass, 'Zend_Log_Writer_Stream')) {
            $writerClass = 'Zend_Log_Writer_Stream';
        }
        /** @var $writer Zend_Log_Writer_Stream */
        $writer = $writerClass::factory(array('stream' => $file));
        $writer->setFormatter(
            new Zend_Log_Formatter_Simple('%timestamp% %priorityName% (%priority%): %message%' . PHP_EOL)
        );
        $this->_loggers[$loggerKey] = new Zend_Log($writer);
        return $this;
    }

    /**
     * Reset all loggers and initialize them according to store configuration
     *
     * @param Mage_Core_Model_Store $store
     * @param Mage_Core_Model_Config $config
     */
    public function initForStore(Mage_Core_Model_Store $store, Mage_Core_Model_Config $config)
    {
        $this->_loggers = array();
        if ($store->getConfig('dev/log/active')) {
            $writer = (string)$config->getNode('global/log/core/writer_model');
            $this->addStreamLog(self::LOGGER_SYSTEM, $store->getConfig('dev/log/file'), $writer);
            $this->addStreamLog(self::LOGGER_EXCEPTION, $store->getConfig('dev/log/exception_file'), $writer);
        }
    }

    /**
     * Add a logger if store configuration allows
     *
     * @param string $loggerKey
     * @param Mage_Core_Model_Store $store
     */
    public function addStoreLog($loggerKey, Mage_Core_Model_Store $store)
    {
        if ($store->getConfig('dev/log/active')) {
            $this->addStreamLog($loggerKey);
        }
    }

    /**
     * Check whether a logger exists by specified key
     *
     * @param string $key
     * @return bool
     */
    public function hasLog($key)
    {
        return isset($this->_loggers[$key]);
    }

    /**
     * Log a message
     *
     * @param string $message
     * @param int $level
     * @param string $loggerKey
     */
    public function log($message, $level = Zend_Log::DEBUG, $loggerKey = self::LOGGER_SYSTEM)
    {
        if (!isset($this->_loggers[$loggerKey])) {
            return;
        }
        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }
        /** @var $logger Zend_Log */
        $logger = $this->_loggers[$loggerKey];
        $logger->log($message, $level);
    }

    /**
     * Log a message with "debug" level
     *
     * @param string $message
     * @param string $loggerKey
     */
    public function logDebug($message, $loggerKey = self::LOGGER_SYSTEM)
    {
        $this->log($message, Zend_Log::DEBUG, $loggerKey);
    }

    /**
     * Log an exception
     *
     * @param Exception $e
     */
    public function logException(Exception $e)
    {
        $this->log("\n" . $e->__toString(), Zend_Log::ERR, self::LOGGER_EXCEPTION);
    }
}
