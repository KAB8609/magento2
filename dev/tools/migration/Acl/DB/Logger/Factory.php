<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    tools
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Tools_Migration_Acl_Db_Logger_Factory
{
    /**
     * List of allowed logger types
     * @var array
     */
    protected $_allowedLoggerTypes = array();

    public function __construct()
    {
        $this->_allowedLoggerTypes = array(
            'console',
            'file',
        );
    }

    /**
     * @param string $loggerType
     * @param string $filePath
     * @return Tools_Migration_Acl_Db_LoggerAbstract
     * @throws InvalidArgumentException
     */
    public function getLogger($loggerType, $filePath = null)
    {
        $loggerType = empty($loggerType) ? 'console' : $loggerType;
        if (false == in_array($loggerType, $this->_allowedLoggerTypes)) {
            throw new InvalidArgumentException('Invalid logger type: ' . $loggerType);
        }

        $loggerClassName = null;
        switch ($loggerType) {
            case 'file':
                $loggerClassName = 'Tools_Migration_Acl_Db_Logger_File';
                break;
            default:
                $loggerClassName = 'Tools_Migration_Acl_Db_Logger_Console';
                break;
        }

        return new $loggerClassName($filePath);
    }
}
