<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Layout_UpdateTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Layout_Update
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento_Core_Model_Layout_Update');
    }

    public function testConstructor()
    {
        $this->_model = Mage::getModel('Magento_Core_Model_Layout_Update');
        $this->assertInstanceOf('Magento_Core_Model_Resource_Layout_Update', $this->_model->getResource());
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testCrud()
    {
        $this->_model->setData(array(
            'handle' => 'default',
            'xml' => '<layout/>',
            'sort_order' => 123,
        ));
        $entityHelper = new Magento_Test_Entity($this->_model, array(
            'handle' => 'custom',
            'xml' => '<layout version="0.1.0"/>',
            'sort_order' => 456,
        ));
        $entityHelper->testCrud();
    }
}