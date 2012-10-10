<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Config_ElementTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Config_Element
     */
    protected $_model;

    protected function setUp()
    {
        $xml = <<<XML
<?xml version="1.0"?>
<root>
    <is_test>
        <value_key>value</value_key>
        <value_sensitive_key>vaLue</value_sensitive_key>
        <false_key>false</false_key>
        <off_key>off</off_key>
        <regular_cdata><![CDATA[value]]></regular_cdata>
        <empty_cdata><![CDATA[]]></empty_cdata>
        <empty_text></empty_text>
    </is_test>
    <class_test>
        <class>Mage_Catalog_Model_Observer</class>
    </class_test>
    <model_test>
        <model>Mage_Catalog_Model_Observer</model>
    </model_test>
    <no_classname_test>
        <none/>
    </no_classname_test>
</root>
XML;
        $this->_model = Mage::getModel('Mage_Core_Model_Config_Element', array('data' => $xml));
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testIs()
    {
        $element = $this->_model->is_test;
        $this->assertTrue($element->is('value_key', 'value'));
        $this->assertTrue($element->is('value_sensitive_key', 'value'));
        $this->assertTrue($element->is('regular_cdata', 'value'));
        $this->assertFalse($element->is('false_key'));
        $this->assertFalse($element->is('empty_cdata'));
        $this->assertFalse($element->is('empty_text'));
    }

    public function testGetClassName()
    {
        $this->assertEquals('Mage_Catalog_Model_Observer', $this->_model->class_test->getClassName());
        $this->assertEquals('Mage_Catalog_Model_Observer', $this->_model->model_test->getClassName());
        $this->assertFalse($this->_model->no_classname_test->getClassName());
    }
}
