<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping cart api for product
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Checkout_Model_Cart_Product_Api extends Magento_Checkout_Model_Api_Resource_Product
{
    /**
     * Base preparation of product data
     *
     * @param mixed $data
     * @return null|array
     */
    protected function _prepareProductsData($data)
    {
        return is_array($data) ? $data : null;
    }

    /**
     * @param  $quoteId
     * @param  $productsData
     * @param  $store
     * @return bool
     */
    public function add($quoteId, $productsData, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);
        if (empty($store)) {
            $store = $quote->getStoreId();
        }

        $productsData = $this->_prepareProductsData($productsData);
        if (empty($productsData)) {
            $this->_fault('invalid_product_data');
        }

        $errors = array();
        foreach ($productsData as $productItem) {
            if (isset($productItem['product_id'])) {
                $productByItem = $this->_getProduct($productItem['product_id'], $store, "id");
            } else if (isset($productItem['sku'])) {
                $productByItem = $this->_getProduct($productItem['sku'], $store, "sku");
            } else {
                $errors[] = __("One item of products do not have identifier or sku");
                continue;
            }
            /** 'configurable_options' array items are expected in the following format 'attribute ID' => 'option ID'*/
            if (isset($productItem['configurable_options']) and is_array($productItem['configurable_options'])) {
                $productItem['super_attribute'] = $productItem['configurable_options'];
                unset($productItem['configurable_options']);
            }

            $productRequest = $this->_getProductRequest($productItem);
            try {
                $result = $quote->addProduct($productByItem, $productRequest);
                if (is_string($result)) {
                    Mage::throwException($result);
                }
            } catch (Magento_Core_Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (!empty($errors)) {
            $this->_fault("add_product_fault", implode(PHP_EOL, $errors));
        }

        try {
            $quote->collectTotals()->save();
        } catch (Exception $e) {
            $this->_fault("add_product_quote_save_fault", $e->getMessage());
        }

        return true;
    }

    /**
     * @param  $quoteId
     * @param  $productsData
     * @param  $store
     * @return bool
     */
    public function update($quoteId, $productsData, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);
        if (empty($store)) {
            $store = $quote->getStoreId();
        }

        $productsData = $this->_prepareProductsData($productsData);
        if (empty($productsData)) {
            $this->_fault('invalid_product_data');
        }

        $errors = array();
        foreach ($productsData as $productItem) {
            if (isset($productItem['product_id'])) {
                $productByItem = $this->_getProduct($productItem['product_id'], $store, "id");
            } else if (isset($productItem['sku'])) {
                $productByItem = $this->_getProduct($productItem['sku'], $store, "sku");
            } else {
                $errors[] = __("One item of products do not have identifier or sku");
                continue;
            }

            /** @var $quoteItem Magento_Sales_Model_Quote_Item */
            $quoteItem = $this->_getQuoteItemByProduct(
                $quote,
                $productByItem,
                $this->_getProductRequest($productItem)
            );
            if (is_null($quoteItem->getId())) {
                $errors[] = __("One item of products is not belong any of quote item");
                continue;
            }

            if ($productItem['qty'] > 0) {
                $quoteItem->setQty($productItem['qty']);
            }
        }

        if (!empty($errors)) {
            $this->_fault("update_product_fault", implode(PHP_EOL, $errors));
        }

        try {
            $quote->collectTotals()->save();
        } catch (Exception $e) {
            $this->_fault("update_product_quote_save_fault", $e->getMessage());
        }

        return true;
    }

    /**
     * @param  $quoteId
     * @param  $productsData
     * @param  $store
     * @return bool
     */
    public function remove($quoteId, $productsData, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);
        if (empty($store)) {
            $store = $quote->getStoreId();
        }

        $productsData = $this->_prepareProductsData($productsData);
        if (empty($productsData)) {
            $this->_fault('invalid_product_data');
        }

        $errors = array();
        foreach ($productsData as $productItem) {
            if (isset($productItem['product_id'])) {
                $productByItem = $this->_getProduct($productItem['product_id'], $store, "id");
            } else if (isset($productItem['sku'])) {
                $productByItem = $this->_getProduct($productItem['sku'], $store, "sku");
            } else {
                $errors[] = __("One item of products do not have identifier or sku");
                continue;
            }

            try {
                /** @var $quoteItem Magento_Sales_Model_Quote_Item */
                $quoteItem = $this->_getQuoteItemByProduct(
                    $quote,
                    $productByItem,
                    $this->_getProductRequest($productItem)
                );
                if (is_null($quoteItem->getId())) {
                    $errors[] = __("One item of products is not belong any of quote item");
                    continue;
                }
                $quote->removeItem($quoteItem->getId());
            } catch (Magento_Core_Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (!empty($errors)) {
            $this->_fault("remove_product_fault", implode(PHP_EOL, $errors));
        }

        try {
            $quote->collectTotals()->save();
        } catch (Exception $e) {
            $this->_fault("remove_product_quote_save_fault", $e->getMessage());
        }

        return true;
    }

    /**
     * @param  $quoteId
     * @param  $store
     * @return array
     */
    public function items($quoteId, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);
        if (empty($store)) {
            $store = $quote->getStoreId();
        }

        if (!$quote->getItemsCount()) {
            return array();
        }

        $productsResult = array();
        foreach ($quote->getAllItems() as $item) {
            /** @var $item Magento_Sales_Model_Quote_Item */
            $product = $item->getProduct();
            $productsResult[] = array( // Basic product data
                'product_id' => $product->getId(),
                'sku' => $product->getSku(),
                'name' => $product->getName(),
                'set' => $product->getAttributeSetId(),
                'type' => $product->getTypeId(),
                'category_ids' => $product->getCategoryIds(),
                'website_ids' => $product->getWebsiteIds()
            );
        }

        return $productsResult;
    }

    /**
     * @param  $quoteId
     * @param  $productsData
     * @param  $store
     * @return bool
     */
    public function moveToCustomerQuote($quoteId, $productsData, $store = null)
    {
        $quote = $this->_getQuote($quoteId, $store);

        if (empty($store)) {
            $store = $quote->getStoreId();
        }

        $customer = $quote->getCustomer();
        if (is_null($customer->getId())) {
            $this->_fault('customer_not_set_for_quote');
        }

        /** @var $customerQuote Magento_Sales_Model_Quote */
        $customerQuote = Mage::getModel('Magento_Sales_Model_Quote')
            ->setStoreId($store)
            ->loadByCustomer($customer);

        if (is_null($customerQuote->getId())) {
            $this->_fault('customer_quote_not_exist');
        }

        if ($customerQuote->getId() == $quote->getId()) {
            $this->_fault('quotes_are_similar');
        }

        $productsData = $this->_prepareProductsData($productsData);
        if (empty($productsData)) {
            $this->_fault('invalid_product_data');
        }

        $errors = array();
        foreach ($productsData as $key => $productItem) {
            if (isset($productItem['product_id'])) {
                $productByItem = $this->_getProduct($productItem['product_id'], $store, "id");
            } else if (isset($productItem['sku'])) {
                $productByItem = $this->_getProduct($productItem['sku'], $store, "sku");
            } else {
                $errors[] = __("One item of products do not have identifier or sku");
                continue;
            }

            try {
                /** @var $quoteItem Magento_Sales_Model_Quote_Item */
                $quoteItem = $this->_getQuoteItemByProduct(
                    $quote,
                    $productByItem,
                    $this->_getProductRequest($productItem)
                );
                if ($quoteItem && $quoteItem->getId()) {
                    $newQuoteItem = clone $quoteItem;
                    $newQuoteItem->setId(null);
                    $customerQuote->addItem($newQuoteItem);
                    $quote->removeItem($quoteItem->getId());
                    unset($productsData[$key]);
                } else {
                    $errors[] = __("One item of products is not belong any of quote item");
                }
            } catch (Magento_Core_Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (count($productsData) || !empty($errors)) {
            $this->_fault('unable_to_move_all_products', implode(PHP_EOL, $errors));
        }

        try {
            $customerQuote->collectTotals()->save();
            $quote->collectTotals()->save();
        } catch (Exception $e) {
            $this->_fault("product_move_quote_save_fault", $e->getMessage());
        }

        return true;
    }
}