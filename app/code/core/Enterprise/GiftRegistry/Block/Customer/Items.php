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
 * Customer gift registry view items block
 */
class Enterprise_GiftRegistry_Block_Customer_Items extends Mage_Catalog_Block_Product_Abstract
{

    /**
     * Return gift registry form header
     */
    public function getFormHeader()
    {
        return Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('View Gift Registry %s', $this->getEntity()->getTitle());
    }

    /**
     * Return list of gift registries
     *
     * @return Enterprise_GiftRegistry_Model_Resource_Item_Collection
     */
    public function getItemCollection()
    {
         if (!$this->hasItemCollection()) {
             $attributes = Mage::getSingleton('Mage_Catalog_Model_Config')->getProductAttributes();
             $collection = Mage::getModel('Enterprise_GiftRegistry_Model_Item')->getCollection()
                ->addRegistryFilter($this->getEntity()->getId());
            $this->setData('item_collection', $collection);
        }
        return $this->_getData('item_collection');
    }

    /**
     * Retrieve item formatted date
     *
     * @param Enterprise_GiftRegistry_Model_Item $item
     * @return string
     */
    public function getFormattedDate($item)
    {
        return $this->formatDate($item->getAddedAt(), Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
    }

    /**
     * Retrieve escaped item note
     *
     * @param Enterprise_GiftRegistry_Model_Item $item
     * @return string
     */
    public function getEscapedNote($item)
    {
        return $this->escapeHtml($item->getData('note'));
    }

    /**
     * Retrieve item qty
     *
     * @param Enterprise_GiftRegistry_Model_Item $item
     * @return string
     */
    public function getItemQty($item)
    {
        return $item->getQty()*1;
    }

    /**
     * Retrieve item fulfilled qty
     *
     * @param Enterprise_GiftRegistry_Model_Item $item
     * @return string
     */
    public function getItemQtyFulfilled($item)
    {
        return $item->getQtyFulfilled()*1;
    }

    /**
     * Return action form url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('*/*/updateItems', array('_current' => true));
    }

    /**
     * Return back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('giftregistry');
    }

    /**
     * Returns product price
     *
     * @param Enterprise_GiftRegistry_Model_Item $item
     * @return mixed
     */
    public function getPrice($item)
    {
        $product = $item->getProduct();
        $product->setCustomOptions($item->getOptionsByCode());
        return Mage::helper('Mage_Core_Helper_Data')->currency($product->getFinalPrice(),true,true);
    }
}
