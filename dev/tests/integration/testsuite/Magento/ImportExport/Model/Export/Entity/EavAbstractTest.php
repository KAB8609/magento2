<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for eav abstract export model
 */
class Magento_ImportExport_Model_Export_Entity_EavAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Skipped attribute codes
     *
     * @var array
     */
    protected static $_skippedAttributes = array('confirmation', 'lastname');

    /**
     * @var \Magento\ImportExport\Model\Export\Entity\EavAbstract
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
        $customerAttributes = Mage::getResourceModel('\Magento\Customer\Model\Resource\Attribute\Collection');

        $this->_model = $this->getMockForAbstractClass('\Magento\ImportExport\Model\Export\Entity\EavAbstract', array(),
            '', false);
        $this->_model->expects($this->any())
            ->method('getEntityTypeCode')
            ->will($this->returnValue($this->_entityCode));
        $this->_model->expects($this->any())
            ->method('getAttributeCollection')
            ->will($this->returnValue($customerAttributes));
        $this->_model->__construct();
    }

    /**
     * Test for method getEntityTypeId()
     */
    public function testGetEntityTypeId()
    {
        $entityCode = 'customer';
        $entityId = Mage::getSingleton('Magento\Eav\Model\Config')
            ->getEntityType($entityCode)
            ->getEntityTypeId();

        $this->assertEquals($entityId, $this->_model->getEntityTypeId());
    }

    /**
     * Test for method _getExportAttrCodes()
     *
     * @covers \Magento\ImportExport\Model\Export\Entity\EavAbstract::_getExportAttrCodes
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
        /** @var $attributeCollection \Magento\Customer\Model\Resource\Attribute\Collection */
        $attributeCollection = Mage::getResourceModel('\Magento\Customer\Model\Resource\Attribute\Collection');
        $attributeCollection->addFieldToFilter('attribute_code', 'gender');
        /** @var $attribute \Magento\Customer\Model\Attribute */
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
        /** @var $attributeCollection \Magento\Customer\Model\Resource\Attribute\Collection */
        $attributeCollection = Mage::getResourceModel('\Magento\Customer\Model\Resource\Attribute\Collection');
        $attributeCollection->addFieldToFilter('attribute_code', array('in' => self::$_skippedAttributes));
        $skippedAttributes = array();
        /** @var $attribute  \Magento\Customer\Model\Attribute */
        foreach ($attributeCollection as $attribute) {
            $skippedAttributes[$attribute->getAttributeCode()] = $attribute->getId();
        }

        return array(
            \Magento\ImportExport\Model\Export::FILTER_ELEMENT_SKIP => $skippedAttributes
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
