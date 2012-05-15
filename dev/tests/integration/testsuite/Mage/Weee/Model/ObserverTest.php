<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Weee
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Weee_Model_ObserverTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Weee_Model_Observer
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Mage_Weee_Model_Observer();
    }

    /**
     * @magentoConfigFixture current_store tax/weee/enable 1
     * @magentoDataFixture Mage/Weee/_files/product_with_fpt.php
     */
    public function testUpdateConfigurableProductOptions()
    {
        Mage::unregister('current_product');
        $eventObserver = $this->_createEventObserverForUpdateConfigurableProductOptions();
        $this->_model->updateConfigurableProductOptions($eventObserver);
        $this->assertEquals(array(), $eventObserver->getEvent()->getResponseObject()->getAdditionalOptions());

        $product = new Mage_Catalog_Model_Product();
        Mage::register('current_product', $product->load(1));

        foreach (array(Mage_Weee_Model_Tax::DISPLAY_INCL, Mage_Weee_Model_Tax::DISPLAY_INCL_DESCR) as $mode) {
            Mage::app()->getStore()->setConfig('tax/weee/display', $mode);
            $eventObserver = $this->_createEventObserverForUpdateConfigurableProductOptions();
            $this->_model->updateConfigurableProductOptions($eventObserver);
            $this->assertEquals(
                array('oldPlusDisposition' => 0.07, 'plusDisposition' => 0.07),
                $eventObserver->getEvent()->getResponseObject()->getAdditionalOptions()
            );
        }

        foreach (array(Mage_Weee_Model_Tax::DISPLAY_EXCL, Mage_Weee_Model_Tax::DISPLAY_EXCL_DESCR_INCL) as $mode) {
            Mage::app()->getStore()->setConfig('tax/weee/display', $mode);
            $eventObserver = $this->_createEventObserverForUpdateConfigurableProductOptions();
            $this->_model->updateConfigurableProductOptions($eventObserver);
            $this->assertEquals(
                array('oldPlusDisposition' => 0.07, 'plusDisposition' => 0.07, 'exclDisposition' => true),
                $eventObserver->getEvent()->getResponseObject()->getAdditionalOptions()
            );
        }
    }

    /**
     * @return Varien_Event_Observer
     */
    protected function _createEventObserverForUpdateConfigurableProductOptions()
    {
        $response = new Varien_Object(array('additional_options' => array()));
        $event = new Varien_Event(array('response_object' => $response));
        return new Varien_Event_Observer(array('event' => $event));
    }
}
