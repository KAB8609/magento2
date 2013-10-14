<?php
/**
 * Controller exception that can fork different actions, cause forward or redirect
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\App\Action;

class Exception extends \Exception
{
    const RESULT_FORWARD  = '_forward';
    const RESULT_REDIRECT = '_redirect';

    protected $_resultCallback       = null;
    protected $_resultCallbackParams = array();
    protected $_defaultActionName    = 'noroute';
    protected $_flags                = array();

    /**
     * Prepare data for forwarding action
     *
     * @param string $actionName
     * @param string $controllerName
     * @param string $moduleName
     * @param array $params
     * @return \Magento\App\Action\Exception
     */
    public function prepareForward($actionName = null, $controllerName = null, $moduleName = null, array $params = array())
    {
        $this->_resultCallback = self::RESULT_FORWARD;
        if (null === $actionName) {
            $actionName = $this->_defaultActionName;
        }
        $this->_resultCallbackParams = array($actionName, $controllerName, $moduleName, $params);
        return $this;
    }

    /**
     * Prepare data for running a custom action
     *
     * @param string $actionName
     * @return \Magento\App\Action\Exception
     */
    public function prepareFork($actionName = null)
    {
        if (null === $actionName) {
            $actionName = $this->_defaultActionName;
        }
        $this->_resultCallback = $actionName;
        return $this;
    }

    /**
     * Prepare a flag data
     *
     * @param string $action
     * @param string $flag
     * @param bool $value
     * @return \Magento\App\Action\Exception
     */
    public function prepareFlag($action, $flag, $value)
    {
        $this->_flags[] = array($action, $flag, $value);
        return $this;
    }

    /**
     * Return all set flags
     *
     * @return array
     */
    public function getResultFlags()
    {
        return $this->_flags;
    }

    /**
     * Return results as callback for a controller
     *
     * @return array
     */
    public function getResultCallback()
    {
        if (null === $this->_resultCallback) {
            $this->prepareFork();
        }
        return array($this->_resultCallback, $this->_resultCallbackParams);
    }
}
