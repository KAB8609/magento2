<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_DesignEditor_Model_Url_Factory implements Magento_ObjectManager_Factory
{
    /**
     * Default url model class name
     */
    const CLASS_NAME = 'Mage_Core_Model_Url';

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
     * Replace name of url model
     *
     * @param string $className
     * @return Mage_DesignEditor_Model_Url_Factory
     */
    public function replaceClassName($className)
    {
        $this->_objectManager->addAlias(self::CLASS_NAME, $className);

        return $this;
    }

    /**
     * Create url model new instance
     *
     * @param array $arguments
     * @return Mage_Core_Model_Url
     */
    public function createFromArray(array $arguments = array())
    {
        return $this->_objectManager->create(self::CLASS_NAME, $arguments, false);
    }
}
