<?php
/**
 * Test object manager
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Test_ObjectManager extends Mage_Core_Model_ObjectManager
{
    /**
     * Classes with xml properties to explicitly call __destruct() due to https://bugs.php.net/bug.php?id=62468
     *
     * @var array
     */
    protected $_classesToDestruct = array(
        'Mage_Core_Model_Layout',
    );

    /**
     * Clear InstanceManager cache
     *
     * @return Magento_Test_ObjectManager
     */
    public function clearCache()
    {
        foreach ($this->_classesToDestruct as $className) {
            if (isset($this->_sharedInstances[$className])) {
                $this->_sharedInstances[$className]->__destruct();
            }
        }

        if (isset($this->_sharedInstances['Mage_Core_Model_Config_Base'])) {
            $this->_sharedInstances['Mage_Core_Model_Config_Base']->destroy();
        }
        $sharedInstances = array('Magento_ObjectManager' => $this);
        if (isset($this->_sharedInstances['Mage_Core_Model_Resource'])) {
            $sharedInstances['Mage_Core_Model_Resource'] = $this->_sharedInstances['Mage_Core_Model_Resource'];
        }
        $this->_sharedInstances = $sharedInstances;
        $this->_configuration = array();

        return $this;
    }

    public function addSharedInstance($instance, $className)
    {
        $this->_sharedInstances[$className] = $instance;
    }

    public function removeSharedInstance($className)
    {
        unset($this->_sharedInstances[$className]);
    }
}
