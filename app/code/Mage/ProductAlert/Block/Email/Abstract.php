<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Product Alert Abstract Email Block
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_ProductAlert_Block_Email_Abstract extends Mage_Core_Block_Template
{
    /**
     * Product collection array
     *
     * @var array
     */
    protected $_products = array();

    /**
     * Current Store scope object
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    /**
     * Set Store scope
     *
     * @param int|string|Mage_Core_Model_Website|Mage_Core_Model_Store $store
     * @return Mage_ProductAlert_Block_Email_Abstract
     */
    public function setStore($store)
    {
        if ($store instanceof Mage_Core_Model_Website) {
            $store = $store->getDefaultStore();
        }
        if (!$store instanceof Mage_Core_Model_Store) {
            $store = Mage::app()->getStore($store);
        }

        $this->_store = $store;

        return $this;
    }

    /**
     * Retrieve current store object
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            $this->_store = Mage::app()->getStore();
        }
        return $this->_store;
    }

    /**
     * Convert price from default currency to current currency
     *
     * @param double $price
     * @param boolean $format             Format price to currency format
     * @param boolean $includeContainer   Enclose into <span class="price"><span>
     * @return double
     */
    public function formatPrice($price, $format = true, $includeContainer = true)
    {
        return $this->getStore()->convertPrice($price, $format, $includeContainer);
    }

    /**
     * Reset product collection
     *
     */
    public function reset()
    {
        $this->_products = array();
    }

    /**
     * Add product to collection
     *
     * @param Mage_Catalog_Model_Product $product
     */
    public function addProduct(Mage_Catalog_Model_Product $product)
    {
        $this->_products[$product->getId()] = $product;
    }

    /**
     * Retrieve product collection array
     *
     * @return array
     */
    public function getProducts()
    {
        return $this->_products;
    }

    /**
     * Get store url params
     *
     * @return string
     */
    protected function _getUrlParams()
    {
        return array(
            '_store'        => $this->getStore(),
            '_store_to_url' => true
        );
    }
}
