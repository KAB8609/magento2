<?php
/**
 * Factory of web API action controllers (services).
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Controller_Action_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(Magento_ObjectManager $objectManager)
    {
        $this->_objectManager = $objectManager;
    }

    /**
     * Create front controller instance.
     *
     * @param string $className
     * @param Mage_Webapi_Controller_Request $request
     * @return Mage_Webapi_Controller_ActionAbstract
     * @throws InvalidArgumentException
     */
    public function createActionController($className, $request)
    {
        $actionController = $this->_objectManager->create($className, array('request' => $request));
        if (!$actionController instanceof Mage_Webapi_Controller_ActionAbstract) {
            throw new InvalidArgumentException(
                'The specified class is not a valid API action controller.');
        }
        return $actionController;
    }
}
