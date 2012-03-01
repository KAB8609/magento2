<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Accordion grid for products in wishlist
 *
 * @category    Enterprise
 * @package     Enterprise_Wishlist
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Wishlist_Block_Adminhtml_Manage_Accordion_Wishlist
    extends Enterprise_Checkout_Block_Adminhtml_Manage_Accordion_Wishlist
{
    /**
     * Return items collection
     *
     * @return Mage_Wishlist_Model_Resource_Item_Collection
     */
    protected function _createItemsCollection()
    {
        return Mage::getModel('Enterprise_Wishlist_Model_Item')->getCollection();
    }

    /**
     * Prepare Grid columns
     *
     * @return Enterprise_Wishlist_Block_Adminhtml_Manage_Accordion_Wishlist
     */
    protected function _prepareColumns()
    {
        $this->addColumn('wishlist_name', array(
            'header'    => Mage::helper('Enterprise_Wishlist_Helper_Data')->__('Wishlist name'),
            'index'     => 'wishlist_name',
            'sortable'  => false
        ));

        return parent::_prepareColumns();
    }

}
