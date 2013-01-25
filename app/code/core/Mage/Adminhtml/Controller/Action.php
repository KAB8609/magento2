<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Generic backend controller
 */
class Mage_Adminhtml_Controller_Action extends Mage_Backend_Controller_ActionAbstract
{
    /**
     * Used module name in current adminhtml controller
     */
    protected $_usedModuleName = 'adminhtml';

    /**
     * Currently used area
     *
     * @var string
     */
    protected $_currentArea = 'adminhtml';

    /**
     * @var Mage_Core_Model_Translate
     */
    protected $_translator;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Layout_Factory $layoutFactory
     * @param string $areaCode
     * @param array $invokeArgs
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Response_Http $response,
        Magento_ObjectManager $objectManager,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Layout_Factory $layoutFactory,
        $areaCode = null,
        array $invokeArgs = array()
    ) {
        parent::__construct($request, $response, $objectManager, $frontController, $layoutFactory, $areaCode,
            $invokeArgs
        );

        $this->_translator = isset($invokeArgs['translator']) ? $invokeArgs['translator'] : $this->_getTranslator();
    }

    /**
     * Translate a phrase
     *
     * @return string
     */
    public function __()
    {
        $args = func_get_args();
        $expr = new Mage_Core_Model_Translate_Expr(array_shift($args), $this->getUsedModuleName());
        array_unshift($args, $expr);
        return $this->_getTranslator()->translate($args);
    }

    /**
     * Get translator model
     *
     * @return Mage_Core_Model_Translate
     */
    protected function _getTranslator()
    {
        if (null === $this->_translator) {
            $this->_translator = $this->_objectManager->get('Mage_Core_Model_Translate');
        }
        return $this->_translator;
    }

    /**
     * Retrieve currently used module name
     *
     * @return string
     */
    public function getUsedModuleName()
    {
        return $this->_usedModuleName;
    }

    /**
     * Set currently used module name
     *
     * @param string $moduleName
     * @return Mage_Adminhtml_Controller_Action
     */
    public function setUsedModuleName($moduleName)
    {
        $this->_usedModuleName = $moduleName;
        return $this;
    }
}
