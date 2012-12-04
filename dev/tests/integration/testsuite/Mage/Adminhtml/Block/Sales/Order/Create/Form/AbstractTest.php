<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Adminhtml_Block_Sales_Order_Create_Form_Abstract
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Form_AbstractTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Mage/Core/_files/init_adminhtml_design.php
     * @magentoAppIsolation enabled
     */
    public function testAddAttributesToForm()
    {
        $arguments = array(
            Mage::getObjectManager()->get('Mage_Core_Controller_Request_Http'),
            Mage::getObjectManager()->get('Mage_Core_Model_Layout'),
            Mage::getObjectManager()->get('Mage_Core_Model_Event_Manager'),
            Mage::getObjectManager()->get('Mage_Backend_Model_Url'),
            Mage::getObjectManager()->get('Mage_Core_Model_Translate'),
            Mage::getObjectManager()->get('Mage_Core_Model_Cache'),
            Mage::getObjectManager()->get('Mage_Core_Model_Design_Package'),
            Mage::getObjectManager()->get('Mage_Core_Model_Session'),
            Mage::getObjectManager()->get('Mage_Core_Model_Store_Config'),
            Mage::getObjectManager()->get('Mage_Core_Controller_Varien_Front'),
            Mage::getObjectManager()->get('Mage_Core_Model_Factory_Helper')
        );
        /** @var $block Mage_Adminhtml_Block_Sales_Order_Create_Form_Abstract */
        $block = $this->getMockForAbstractClass('Mage_Adminhtml_Block_Sales_Order_Create_Form_Abstract', $arguments);
        $block->setLayout(Mage::getObjectManager()->create('Mage_Core_Model_Layout'));

        $method = new ReflectionMethod(
            'Mage_Adminhtml_Block_Sales_Order_Create_Form_Abstract', '_addAttributesToForm');
        $method->setAccessible(true);

        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('test_fieldset', array());
        $arguments = array(
            'data' => array(
                'attribute_code' => 'date',
                'backend_type' => 'datetime',
                'frontend_input' => 'date',
                'frontend_label' => 'Date',
            )
        );
        $dateAttribute = Mage::getObjectManager()->create('Mage_Customer_Model_Attribute', $arguments);
        $attributes = array('date' => $dateAttribute);
        $method->invoke($block, $attributes, $fieldset);

        $element = $form->getElement('date');
        $this->assertNotNull($element);
        $this->assertNotEmpty($element->getDateFormat());
    }
}
