<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Frontend model for DHL shipping methods for documentation
 *
 * @category   Mage
 * @package    Mage_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Usa_Block_Adminhtml_Dhl_Unitofmeasure extends Mage_Backend_Block_System_Config_Form_Field
{

    /**
     * Define params and variables
     *
     * @return void
     */
    public function _construct()
    {
        parent::_construct();

        $carrierModel = Mage::getSingleton('Mage_Usa_Model_Shipping_Carrier_Dhl_International');

        $this->setInch($this->jsQuoteEscape($carrierModel->getCode('unit_of_dimension_cut', 'I')));
        $this->setCm($this->jsQuoteEscape($carrierModel->getCode('unit_of_dimension_cut', 'C')));

        $this->setHeight($this->jsQuoteEscape($carrierModel->getCode('dimensions', 'height')));
        $this->setDepth($this->jsQuoteEscape($carrierModel->getCode('dimensions', 'depth')));
        $this->setWidth($this->jsQuoteEscape($carrierModel->getCode('dimensions', 'width')));

        $kgWeight = 70;

        $this->setDivideOrderWeightNoteKg(
            $this->jsQuoteEscape(__('This allows breaking total order weight into smaller pieces if it exceeds %s %s to ensure accurate calculation of shipping charges.', $kgWeight, 'kg'))
        );

        $weight = round(
            Mage::helper('Mage_Usa_Helper_Data')->convertMeasureWeight(
                $kgWeight, Zend_Measure_Weight::KILOGRAM, Zend_Measure_Weight::POUND), 3);

        $this->setDivideOrderWeightNoteLbp(
            $this->jsQuoteEscape(__('This allows breaking total order weight into smaller pieces if it exceeds %s %s to ensure accurate calculation of shipping charges.', $weight, 'pounds'))
        );

        $this->setTemplate('dhl/unitofmeasure.phtml');
    }

    /**
     * Retrieve Element HTML fragment
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return parent::_getElementHtml($element) . $this->_toHtml();
    }
}
