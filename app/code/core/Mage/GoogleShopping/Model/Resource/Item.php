<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Content Item resource model
 *
 * @category   Mage
 * @package    Mage_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleShopping_Model_Resource_Item extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('googleshopping_items', 'item_id');
    }

    /**
     * Load Item model by product
     *
     * @param Mage_GoogleShopping_Model_Item $model
     * @return Mage_GoogleShopping_Model_Resource_Item
     */
    public function loadByProduct($model)
    {
        if (!($model->getProduct() instanceof Varien_Object)) {
            return $this;
        }

        $product = $model->getProduct();
        $productId = $product->getId();
        $storeId = $model->getStoreId() ? $model->getStoreId() : $product->getStoreId();

        $read = $this->_getReadAdapter();
        $select = $read->select();

        if ($productId !== null) {
            $select->from($this->getMainTable())
                ->where("product_id = ?", $productId)
                ->where('store_id = ?', (int)$storeId);

            $data = $read->fetchRow($select);
            $data = is_array($data) ? $data : array();
            $model->addData($data);
        }
        return $this;
    }
}
