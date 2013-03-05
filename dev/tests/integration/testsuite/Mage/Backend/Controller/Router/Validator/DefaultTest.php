<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class Mage_Backend_Controller_Router_Default
 */
class Mage_Backend_Controller_Router_Validator_DefaultTest extends Mage_Backend_Area_TestCase
{
    /**
     * @magentoConfigFixture global/areas/adminhtml/frontName 0
     * @expectedException InvalidArgumentException
     * @magentoAppIsolation enabled
     */
    public function testConstructWithEmptyAreaFrontName()
    {
        $options = array(
            'areaCode' => Mage::helper('Mage_Backend_Helper_Data')->getAreaCode(),
            'baseController' => 'Mage_Backend_Controller_ActionAbstract',
            'frontName' => 'backend'
        );
        Mage::getModel('Mage_Backend_Controller_Router_Default', $options);
    }

    /**
     * @magentoConfigFixture global/areas/adminhtml/frontName backend
     * @magentoAppIsolation enabled
     */
    public function testConstructWithNotEmptyAreaFrontName()
    {
        $options = array(
            'areaCode'       => Mage::helper('Mage_Backend_Helper_Data')->getAreaCode(),
            'baseController' => 'Mage_Backend_Controller_ActionAbstract',
        );
        Mage::getModel('Mage_Backend_Controller_Router_Default', $options);
    }
}
