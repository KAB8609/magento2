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
 * @method \Magento\Sales\Model\Resource\Order\Invoice\Item _getResource()
 * @method \Magento\Sales\Model\Resource\Order\Invoice\Item getResource()
 * @method int getParentId()
 * @method \Magento\Sales\Model\Order\Invoice\Item setParentId(int $value)
 * @method float getBasePrice()
 * @method \Magento\Sales\Model\Order\Invoice\Item setBasePrice(float $value)
 * @method float getBaseWeeeTaxRowDisposition()
 * @method \Magento\Sales\Model\Order\Invoice\Item setBaseWeeeTaxRowDisposition(float $value)
 * @method float getWeeeTaxAppliedRowAmount()
 * @method \Magento\Sales\Model\Order\Invoice\Item setWeeeTaxAppliedRowAmount(float $value)
 * @method float getBaseWeeeTaxAppliedAmount()
 * @method \Magento\Sales\Model\Order\Invoice\Item setBaseWeeeTaxAppliedAmount(float $value)
 * @method float getTaxAmount()
 * @method \Magento\Sales\Model\Order\Invoice\Item setTaxAmount(float $value)
 * @method float getBaseRowTotal()
 * @method \Magento\Sales\Model\Order\Invoice\Item setBaseRowTotal(float $value)
 * @method float getDiscountAmount()
 * @method \Magento\Sales\Model\Order\Invoice\Item setDiscountAmount(float $value)
 * @method float getRowTotal()
 * @method \Magento\Sales\Model\Order\Invoice\Item setRowTotal(float $value)
 * @method float getWeeeTaxRowDisposition()
 * @method \Magento\Sales\Model\Order\Invoice\Item setWeeeTaxRowDisposition(float $value)
 * @method float getBaseDiscountAmount()
 * @method \Magento\Sales\Model\Order\Invoice\Item setBaseDiscountAmount(float $value)
 * @method float getBaseWeeeTaxDisposition()
 * @method \Magento\Sales\Model\Order\Invoice\Item setBaseWeeeTaxDisposition(float $value)
 * @method float getPriceInclTax()
 * @method \Magento\Sales\Model\Order\Invoice\Item setPriceInclTax(float $value)
 * @method float getWeeeTaxAppliedAmount()
 * @method \Magento\Sales\Model\Order\Invoice\Item setWeeeTaxAppliedAmount(float $value)
 * @method float getBaseTaxAmount()
 * @method \Magento\Sales\Model\Order\Invoice\Item setBaseTaxAmount(float $value)
 * @method float getBasePriceInclTax()
 * @method \Magento\Sales\Model\Order\Invoice\Item setBasePriceInclTax(float $value)
 * @method float getQty()
 * @method float getWeeeTaxDisposition()
 * @method \Magento\Sales\Model\Order\Invoice\Item setWeeeTaxDisposition(float $value)
 * @method float getBaseCost()
 * @method \Magento\Sales\Model\Order\Invoice\Item setBaseCost(float $value)
 * @method float getBaseWeeeTaxAppliedRowAmnt()
 * @method \Magento\Sales\Model\Order\Invoice\Item setBaseWeeeTaxAppliedRowAmnt(float $value)
 * @method float getPrice()
 * @method \Magento\Sales\Model\Order\Invoice\Item setPrice(float $value)
 * @method float getBaseRowTotalInclTax()
 * @method \Magento\Sales\Model\Order\Invoice\Item setBaseRowTotalInclTax(float $value)
 * @method float getRowTotalInclTax()
 * @method \Magento\Sales\Model\Order\Invoice\Item setRowTotalInclTax(float $value)
 * @method int getProductId()
 * @method \Magento\Sales\Model\Order\Invoice\Item setProductId(int $value)
 * @method int getOrderItemId()
 * @method \Magento\Sales\Model\Order\Invoice\Item setOrderItemId(int $value)
 * @method string getAdditionalData()
 * @method \Magento\Sales\Model\Order\Invoice\Item setAdditionalData(string $value)
 * @method string getDescription()
 * @method \Magento\Sales\Model\Order\Invoice\Item setDescription(string $value)
 * @method string getWeeeTaxApplied()
 * @method \Magento\Sales\Model\Order\Invoice\Item setWeeeTaxApplied(string $value)
 * @method string getSku()
 * @method \Magento\Sales\Model\Order\Invoice\Item setSku(string $value)
 * @method string getName()
 * @method \Magento\Sales\Model\Order\Invoice\Item setName(string $value)
 * @method float getHiddenTaxAmount()
 * @method \Magento\Sales\Model\Order\Invoice\Item setHiddenTaxAmount(float $value)
 * @method float getBaseHiddenTaxAmount()
 * @method \Magento\Sales\Model\Order\Invoice\Item setBaseHiddenTaxAmount(float $value)
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Order\Invoice;

class Item extends \Magento\Core\Model\AbstractModel
{
    protected $_eventPrefix = 'sales_invoice_item';
    protected $_eventObject = 'invoice_item';

    protected $_invoice = null;
    protected $_orderItem = null;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('\Magento\Sales\Model\Resource\Order\Invoice\Item');
    }

    /**
     * Declare invoice instance
     *
     * @param   \Magento\Sales\Model\Order\Invoice $invoice
     * @return  \Magento\Sales\Model\Order\Invoice\Item
     */
    public function setInvoice(\Magento\Sales\Model\Order\Invoice $invoice)
    {
        $this->_invoice = $invoice;
        return $this;
    }

    /**
     * Retrieve invoice instance
     *
     * @return \Magento\Sales\Model\Order\Invoice
     */
    public function getInvoice()
    {
        return $this->_invoice;
    }

    /**
     * Declare order item instance
     *
     * @param   \Magento\Sales\Model\Order\Item $item
     * @return  \Magento\Sales\Model\Order\Invoice\Item
     */
    public function setOrderItem(\Magento\Sales\Model\Order\Item $item)
    {
        $this->_orderItem = $item;
        $this->setOrderItemId($item->getId());
        return $this;
    }

    /**
     * Retrieve order item instance
     *
     * @return \Magento\Sales\Model\Order\Item
     */
    public function getOrderItem()
    {
        if (is_null($this->_orderItem)) {
            if ($this->getInvoice()) {
                $this->_orderItem = $this->getInvoice()->getOrder()->getItemById($this->getOrderItemId());
            }
            else {
                $this->_orderItem = \Mage::getModel('Magento\Sales\Model\Order\Item')
                    ->load($this->getOrderItemId());
            }
        }
        return $this->_orderItem;
    }

    /**
     * Declare qty
     *
     * @param   float $qty
     * @return  \Magento\Sales\Model\Order\Invoice\Item
     */
    public function setQty($qty)
    {
        if ($this->getOrderItem()->getIsQtyDecimal()) {
            $qty = (float) $qty;
        }
        else {
            $qty = (int) $qty;
        }
        $qty = $qty > 0 ? $qty : 0;
        /**
         * Check qty availability
         */
        $qtyToInvoice = sprintf("%F", $this->getOrderItem()->getQtyToInvoice());
        $qty = sprintf("%F", $qty);
        if ($qty <= $qtyToInvoice || $this->getOrderItem()->isDummy()) {
            $this->setData('qty', $qty);
        }
        else {
            \Mage::throwException(
                __('We found an invalid quantity to invoice item "%1".', $this->getName())
            );
        }
        return $this;
    }

    /**
     * Applying qty to order item
     *
     * @return \Magento\Sales\Model\Order\Invoice\Item
     */
    public function register()
    {
        $orderItem = $this->getOrderItem();
        $orderItem->setQtyInvoiced($orderItem->getQtyInvoiced()+$this->getQty());

        $orderItem->setTaxInvoiced($orderItem->getTaxInvoiced()+$this->getTaxAmount());
        $orderItem->setBaseTaxInvoiced($orderItem->getBaseTaxInvoiced()+$this->getBaseTaxAmount());
        $orderItem->setHiddenTaxInvoiced($orderItem->getHiddenTaxInvoiced()+$this->getHiddenTaxAmount());
        $orderItem->setBaseHiddenTaxInvoiced($orderItem->getBaseHiddenTaxInvoiced()+$this->getBaseHiddenTaxAmount());

        $orderItem->setDiscountInvoiced($orderItem->getDiscountInvoiced()+$this->getDiscountAmount());
        $orderItem->setBaseDiscountInvoiced($orderItem->getBaseDiscountInvoiced()+$this->getBaseDiscountAmount());

        $orderItem->setRowInvoiced($orderItem->getRowInvoiced()+$this->getRowTotal());
        $orderItem->setBaseRowInvoiced($orderItem->getBaseRowInvoiced()+$this->getBaseRowTotal());
        return $this;
    }

    /**
     * Cancelling invoice item
     *
     * @return \Magento\Sales\Model\Order\Invoice\Item
     */
    public function cancel()
    {
        $orderItem = $this->getOrderItem();
        $orderItem->setQtyInvoiced($orderItem->getQtyInvoiced()-$this->getQty());

        $orderItem->setTaxInvoiced($orderItem->getTaxInvoiced()-$this->getTaxAmount());
        $orderItem->setBaseTaxInvoiced($orderItem->getBaseTaxInvoiced()-$this->getBaseTaxAmount());
        $orderItem->setHiddenTaxInvoiced($orderItem->getHiddenTaxInvoiced()-$this->getHiddenTaxAmount());
        $orderItem->setBaseHiddenTaxInvoiced($orderItem->getBaseHiddenTaxInvoiced()-$this->getBaseHiddenTaxAmount());


        $orderItem->setDiscountInvoiced($orderItem->getDiscountInvoiced()-$this->getDiscountAmount());
        $orderItem->setBaseDiscountInvoiced($orderItem->getBaseDiscountInvoiced()-$this->getBaseDiscountAmount());

        $orderItem->setRowInvoiced($orderItem->getRowInvoiced()-$this->getRowTotal());
        $orderItem->setBaseRowInvoiced($orderItem->getBaseRowInvoiced()-$this->getBaseRowTotal());
        return $this;
    }

    /**
     * Invoice item row total calculation
     *
     * @return \Magento\Sales\Model\Order\Invoice\Item
     */
    public function calcRowTotal()
    {
        $invoice        = $this->getInvoice();
        $orderItem      = $this->getOrderItem();
        $orderItemQty   = $orderItem->getQtyOrdered();

        $rowTotal            = $orderItem->getRowTotal() - $orderItem->getRowInvoiced();
        $baseRowTotal        = $orderItem->getBaseRowTotal() - $orderItem->getBaseRowInvoiced();
        $rowTotalInclTax     = $orderItem->getRowTotalInclTax();
        $baseRowTotalInclTax = $orderItem->getBaseRowTotalInclTax();

        if (!$this->isLast()) {
            $availableQty = $orderItemQty - $orderItem->getQtyInvoiced();
            $rowTotal = $invoice->roundPrice($rowTotal / $availableQty * $this->getQty());
            $baseRowTotal = $invoice->roundPrice($baseRowTotal / $availableQty * $this->getQty(), 'base');
        }

        $this->setRowTotal($rowTotal);
        $this->setBaseRowTotal($baseRowTotal);

        if ($rowTotalInclTax && $baseRowTotalInclTax) {
            $this->setRowTotalInclTax($invoice->roundPrice($rowTotalInclTax / $orderItemQty * $this->getQty(), 'including'));
            $this->setBaseRowTotalInclTax($invoice->roundPrice($baseRowTotalInclTax / $orderItemQty * $this->getQty(), 'including_base'));
        }
        return $this;
    }

    /**
     * Checking if the item is last
     *
     * @return bool
     */
    public function isLast()
    {
        if ((string)(float)$this->getQty() == (string)(float)$this->getOrderItem()->getQtyToInvoice()) {
            return true;
        }
        return false;
    }

    /**
     * Before object save
     *
     * @return \Magento\Sales\Model\Order\Invoice\Item
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getInvoice()) {
            $this->setParentId($this->getInvoice()->getId());
        }

        return $this;
    }

    /**
     * After object save
     *
     * @return \Magento\Sales\Model\Order\Invoice\Item
     */
    protected function _afterSave()
    {
        if (null ==! $this->_orderItem) {
            $this->_orderItem->save();
        }

        parent::_afterSave();
        return $this;
    }
}
