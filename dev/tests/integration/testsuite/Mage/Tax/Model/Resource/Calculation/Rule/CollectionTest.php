<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Tax
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Tax_Model_Resource_Calculation_Rule_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test setClassTypeFilter with correct Class Type
     *
     * @param $classType
     * @param $id
     * @param $expected
     *
     * @dataProvider setClassTypeFilterDataProvider
     */
    public function testSetClassTypeFilter($classType, $id, $expected)
    {
        $collection = new Mage_Tax_Model_Resource_Calculation_Rule_Collection();
        $collection->setClassTypeFilter($classType, $id);
        $this->assertRegExp($expected, (string)$collection->getSelect());
    }

    public function setClassTypeFilterDataProvider()
    {
        return array(
            array(Mage_Tax_Model_Class::TAX_CLASS_TYPE_PRODUCT, 1, '/cd\.product_tax_class_id = [\S]{0,1}1[\S]{0,1}/'),
            array(Mage_Tax_Model_Class::TAX_CLASS_TYPE_CUSTOMER, 1, '/cd\.customer_tax_class_id = [\S]{0,1}1[\S]{0,1}/')
        );
}

    /**
     * Test setClassTypeFilter with wrong Class Type
     *
     * @expectedException Mage_Core_Exception
     */
    public function testSetClassTypeFilterWithWrongType()
    {
        $collection = new Mage_Tax_Model_Resource_Calculation_Rule_Collection();
        $collection->setClassTypeFilter('WrongType', 1);
    }
}
