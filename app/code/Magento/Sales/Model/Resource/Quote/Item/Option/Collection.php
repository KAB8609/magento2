<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Item option collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Quote\Item\Option;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Array of option ids grouped by item id
     *
     * @var array
     */
    protected $_optionsByItem        = array();

    /**
     * Array of option ids grouped by product id
     *
     * @var array
     */
    protected $_optionsByProduct     = array();

    /**
     * Define resource model for collection
     *
     */
    protected function _construct()
    {
        $this->_init('\Magento\Sales\Model\Quote\Item\Option', '\Magento\Sales\Model\Resource\Quote\Item\Option');
    }

    /**
     * Fill array of options by item and product
     *
     * @return \Magento\Sales\Model\Resource\Quote\Item\Option\Collection
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();

        foreach ($this as $option) {
            $optionId   = $option->getId();
            $itemId     = $option->getItemId();
            $productId  = $option->getProductId();
            if (isset($this->_optionsByItem[$itemId])) {
                $this->_optionsByItem[$itemId][] = $optionId;
            } else {
                $this->_optionsByItem[$itemId] = array($optionId);
            }
            if (isset($this->_optionsByProduct[$productId])) {
                $this->_optionsByProduct[$productId][] = $optionId;
            } else {
                $this->_optionsByProduct[$productId] = array($optionId);
            }
        }

        return $this;
    }

    /**
     * Apply quote item(s) filter to collection
     *
     * @param int | array $item
     * @return \Magento\Sales\Model\Resource\Quote\Item\Option\Collection
     */
    public function addItemFilter($item)
    {
        if (empty($item)) {
            $this->_totalRecords = 0;
            $this->_setIsLoaded(true);
            //$this->addFieldToFilter('item_id', '');
        } elseif (is_array($item)) {
            $this->addFieldToFilter('item_id', array('in' => $item));
        } elseif ($item instanceof \Magento\Sales\Model\Quote\Item) {
            $this->addFieldToFilter('item_id', $item->getId());
        } else {
            $this->addFieldToFilter('item_id', $item);
        }

        return $this;
    }

    /**
     * Get array of all product ids
     *
     * @return array
     */
    public function getProductIds()
    {
        $this->load();
        
        return array_keys($this->_optionsByProduct);
    }

    /**
     * Get all option for item
     *
     * @param mixed $item
     * @return array
     */
    public function getOptionsByItem($item)
    {
        if ($item instanceof \Magento\Sales\Model\Quote\Item) {
            $itemId = $item->getId();
        } else {
            $itemId = $item;
        }

        $this->load();

        $options = array();
        if (isset($this->_optionsByItem[$itemId])) {
            foreach ($this->_optionsByItem[$itemId] as $optionId) {
                $options[] = $this->_items[$optionId];
            }
        }

        return $options;
    }

    /**
     * Get all option for item
     *
     * @param int | \Magento\Catalog\Model\Product $product
     * @return array
     */
    public function getOptionsByProduct($product)
    {
        if ($product instanceof \Magento\Catalog\Model\Product) {
            $productId = $product->getId();
        } else {
            $productId = $product;
        }

        $this->load();

        $options = array();
        if (isset($this->_optionsByProduct[$productId])) {
            foreach ($this->_optionsByProduct[$productId] as $optionId) {
                $options[] = $this->_items[$optionId];
            }
        }

        return $options;
    }
}
