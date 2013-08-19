<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Search_Model_Adminhtml_System_Config_Source_EngineTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Search_Model_Adminhtml_System_Config_Source_Engine
     */
    protected $_model;

    protected function setUp()
    {
        $mockHelper = $this->getMock('Magento_Search_Helper_Data', array(), array(), '', false, false);
        $mockHelper->expects($this->any())->method('__')->will($this->returnValue('Search_Translation'));
        $this->_model= new Magento_Search_Model_Adminhtml_System_Config_Source_Engine(array(
            'helper' => $mockHelper,
        ));
    }

    /**
     * Check if Magento_Search_Model_Adminhtml_System_Config_Source_Engine has method toOptionArray
     */
    public function testToOptionArrayExistence()
    {
        $this->assertTrue(method_exists($this->_model, 'toOptionArray'), 'Required method toOptionArray not exists');
    }

    /**
     * Check output format
     * @depends testToOptionArrayExistence
     */
    public function testToOptionArrayFormat()
    {
        $options = $this->_model->toOptionArray();
        $this->assertNotEmpty($options);
        foreach ($options as $option) {
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('value', $option);
            $this->assertEquals($option['label'], 'Search_Translation');
            $this->assertTrue(class_exists($option['value']));
        }
    }
}
