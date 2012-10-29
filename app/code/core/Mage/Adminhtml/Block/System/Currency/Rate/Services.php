<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Manage currency import services block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_System_Currency_Rate_Services extends Mage_Adminhtml_Block_Template
{
    protected $_template = 'system/currency/rate/services.phtml';

    /**
     * Create import services form select element
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $this->setChild('import_services',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Html_Select')
            ->setOptions(Mage::getModel('Mage_Adminhtml_Model_System_Config_Source_Currency_Service')->toOptionArray(0))
            ->setId('rate_services')
            ->setName('rate_services')
            ->setValue(Mage::getSingleton('Mage_Adminhtml_Model_Session')->getCurrencyRateService(true))
            ->setTitle(Mage::helper('Mage_Adminhtml_Helper_Data')->__('Import Service'))
        );

        return parent::_prepareLayout();
    }

}
