<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Rma_Model_ShippingTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rma\Model\Shipping
     */
    protected $_model;

    protected function setUp()
    {
        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('\Magento\Rma\Model\Shipping');
    }

    /**
     * @dataProvider isCustomDataProvider
     * @param bool $expectedResult
     * @param string $carrierCodeToSet
     */
    public function testIsCustom($expectedResult, $carrierCodeToSet)
    {
        $this->_model->setCarrierCode($carrierCodeToSet);
        $this->assertEquals($expectedResult, $this->_model->isCustom());
    }

    /**
     * @return array
     */
    public static function isCustomDataProvider()
    {
        return array(
            array(true, \Magento\Sales\Model\Order\Shipment\Track::CUSTOM_CARRIER_CODE),
            array(false, 'ups'),
        );
    }
}
