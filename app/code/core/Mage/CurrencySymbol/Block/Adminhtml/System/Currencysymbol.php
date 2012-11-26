<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CurrencySymbol
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Manage currency symbols block
 *
 * @category   Mage
 * @package    Mage_CurrencySymbol
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_CurrencySymbol_Block_Adminhtml_System_Currencysymbol extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Constructor. Initialization required variables for class instance.
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Mage_CurrencySymbol_System';
        $this->_controller = 'adminhtml_system_currencysymbol';
        parent::_construct();
    }

    /**
     * Custom currency symbol properties
     *
     * @var array
     */
    protected $_symbolsData = array();

    /**
     * Prepares layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * Returns page header
     *
     * @return bool|string
     */
    public function getHeader()
    {
        return Mage::helper('Mage_Adminhtml_Helper_Data')->__('Manage Currency Symbols');
    }

    /**
     * Returns 'Save Currency Symbol' button's HTML code
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        /** @var $block Mage_Core_Block_Abstract */
        $block = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button');
        $block->setData(array(
            'label'     => Mage::helper('Mage_CurrencySymbol_Helper_Data')->__('Save Currency Symbols'),
            'class'     => 'save',
            'data_attr'  => array(
                'widget-button' => array('event' => 'save', 'related' => '#currency-symbols-form')
            )
        ));

        return $block->toHtml();
    }

    /**
     * Returns URL for save action
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('*/*/save');
    }

    /**
     * Returns website id
     *
     * @return int
     */
    public function getWebsiteId()
    {
        return $this->getRequest()->getParam('website');
    }

    /**
     * Returns store id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getRequest()->getParam('store');
    }

    /**
     * Returns Custom currency symbol properties
     *
     * @return array
     */
    public function getCurrencySymbolsData()
    {
        if(!$this->_symbolsData) {
            $this->_symbolsData =  Mage::getModel('Mage_CurrencySymbol_Model_System_Currencysymbol')
                ->getCurrencySymbolsData();
        }
        return $this->_symbolsData;
    }

    /**
     * Returns inheritance text
     *
     * @return string
     */
    public function getInheritText()
    {
        return Mage::helper('Mage_CurrencySymbol_Helper_Data')->__('Use Standard');
    }
}
