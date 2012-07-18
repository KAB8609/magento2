<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for abstract export model V2
 *
 * @group module:Mage_ImportExport
 */
class Mage_ImportExport_Model_Export_Entity_V2_Eav_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Skipped attribute codes
     *
     * @var array
     */
    protected static $_skippedAttributes = array('confirmation', 'lastname');

    /**
     * @var Mage_ImportExport_Model_Export_Entity_V2_Eav_Abstract
     */
    protected $_model;

    /**
     * Entity code
     *
     * @var string
     */
    protected $_entityCode = 'customer';

    protected function setUp()
    {
        $customerAttributes = new Mage_Customer_Model_Resource_Attribute_Collection();

        $this->_model = $this->getMockForAbstractClass('Mage_ImportExport_Model_Export_Entity_V2_Eav_Abstract', array(),
            '', false);
        $this->_model->expects($this->any())
            ->method('getEntityTypeCode')
            ->will($this->returnValue($this->_entityCode));
        $this->_model->expects($this->any())
            ->method('getAttributeCollection')
            ->will($this->returnValue($customerAttributes));
        $this->_model->__construct();
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * Test for method getEntityTypeId()
     */
    public function testGetEntityTypeId()
    {
        $entityCode = 'customer';
        $entityId = Mage::getSingleton('Mage_Eav_Model_Config')
            ->getEntityType($entityCode)
            ->getEntityTypeId();

        $this->assertEquals($entityId, $this->_model->getEntityTypeId());
    }

    /**
     * Test for method _getExportAttrCodes()
     *
     * @covers Mage_ImportExport_Model_Export_Entity_V2_Eav_Abstract::_getExportAttrCodes
     */
    public function testGetExportAttrCodes()
    {
        $this->_checkReflectionMethodSetAccessibleExists();

        $this->_model->setParameters($this->_getSkippedAttributes());
        $method = new ReflectionMethod($this->_model, '_getExportAttributeCodes');
        $method->setAccessible(true);
        $attributes = $method->invoke($this->_model);
        foreach (self::$_skippedAttributes as $code) {
            $this->assertNotContains($code, $attributes);
        }
    }

    /**
     * Test for method getAttributeOptions()
     */
    public function testGetAttributeOptions()
    {
        /** @var $attributeCollection Mage_Customer_Model_Resource_Attribute_Collection */
        $attributeCollection = Mage::getResourceModel('Mage_Customer_Model_Resource_Attribute_Collection');
        $attributeCollection->addFieldToFilter('attribute_code', 'gender');
        /** @var $attribute Mage_Customer_Model_Attribute */
        $attribute = $attributeCollection->getFirstItem();

        $expectedOptions = array();
        foreach ($attribute->getSource()->getAllOptions(false) as $option) {
            $expectedOptions[$option['value']] = $option['label'];
        }

        $actualOptions = $this->_model->getAttributeOptions($attribute);
        $this->assertEquals($expectedOptions, $actualOptions);
    }

    /**
     * Retrieve list of skipped attributes
     *
     * @return array
     */
    protected function _getSkippedAttributes()
    {
        /** @var $attributeCollection Mage_Customer_Model_Resource_Attribute_Collection */
        $attributeCollection = Mage::getResourceModel('Mage_Customer_Model_Resource_Attribute_Collection');
        $attributeCollection->addFieldToFilter('attribute_code', array('in' => self::$_skippedAttributes));
        $skippedAttributes = array();
        /** @var $attribute  Mage_Customer_Model_Attribute */
        foreach ($attributeCollection as $attribute) {
            $skippedAttributes[$attribute->getAttributeCode()] = $attribute->getId();
        }

        return array(
            Mage_ImportExport_Model_Export::FILTER_ELEMENT_SKIP => $skippedAttributes
        );
    }

    /**
     * Check that method ReflectionMethod::setAccessible exists
     */
    protected function _checkReflectionMethodSetAccessibleExists()
    {
        if (!method_exists('ReflectionMethod', 'setAccessible')) {
            $this->markTestSkipped('Test requires ReflectionMethod::setAccessible (PHP 5 >= 5.3.2).');
        }
    }
}
