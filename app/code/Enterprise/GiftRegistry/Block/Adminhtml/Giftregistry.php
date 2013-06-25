<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift Registry Adminhtml Block
 */
class Enterprise_GiftRegistry_Block_Adminhtml_Giftregistry extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Initialize gift registry manage page
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_giftregistry';
        $this->_blockGroup = 'Enterprise_GiftRegistry';
        $this->_headerText = Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Gift Registry Types');
        $this->_addButtonLabel = Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Add Gift Registry Type');
        parent::_construct();
    }
}
