<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tax Event Observer
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Tax\Model;

class Observer
{
    /**
     * Put quote address tax information into order
     *
     * @param \Magento\Event\Observer $observer
     */
    public function salesEventConvertQuoteAddressToOrder(\Magento\Event\Observer $observer)
    {
        $address = $observer->getEvent()->getAddress();
        $order = $observer->getEvent()->getOrder();

        $taxes = $address->getAppliedTaxes();
        if (is_array($taxes)) {
            if (is_array($order->getAppliedTaxes())) {
                $taxes = array_merge($order->getAppliedTaxes(), $taxes);
            }
            $order->setAppliedTaxes($taxes);
            $order->setConvertingFromQuote(true);
        }
    }

    /**
     * Save order tax information
     *
     * @param \Magento\Event\Observer $observer
     */
    public function salesEventOrderAfterSave(\Magento\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        if (!$order->getConvertingFromQuote() || $order->getAppliedTaxIsSaved()) {
            return;
        }

        $getTaxesForItems   = $order->getQuote()->getTaxesForItems();
        $taxes              = $order->getAppliedTaxes();

        $ratesIdQuoteItemId = array();
        if (!is_array($getTaxesForItems)) {
            $getTaxesForItems = array();
        }
        foreach ($getTaxesForItems as $quoteItemId => $taxesArray) {
            foreach ($taxesArray as $rates) {
                if (count($rates['rates']) == 1) {
                    $ratesIdQuoteItemId[$rates['id']][] = array(
                        'id'        => $quoteItemId,
                        'percent'   => $rates['percent'],
                        'code'      => $rates['rates'][0]['code']
                    );
                } else {
                    $percentDelta   = $rates['percent'];
                    $percentSum     = 0;
                    foreach ($rates['rates'] as $rate) {
                        $ratesIdQuoteItemId[$rates['id']][] = array(
                            'id'        => $quoteItemId,
                            'percent'   => $rate['percent'],
                            'code'      => $rate['code']
                        );
                        $percentSum += $rate['percent'];
                    }

                    if ($percentDelta != $percentSum) {
                        $delta = $percentDelta - $percentSum;
                        foreach ($ratesIdQuoteItemId[$rates['id']] as &$rateTax) {
                            if ($rateTax['id'] == $quoteItemId) {
                                $rateTax['percent'] = (($rateTax['percent'] / $percentSum) * $delta)
                                        + $rateTax['percent'];
                            }
                        }
                    }
                }
            }
        }

        foreach ($taxes as $id => $row) {
            foreach ($row['rates'] as $tax) {
                if (is_null($row['percent'])) {
                    $baseRealAmount = $row['base_amount'];
                } else {
                    if ($row['percent'] == 0 || $tax['percent'] == 0) {
                        continue;
                    }
                    $baseRealAmount = $row['base_amount'] / $row['percent'] * $tax['percent'];
                }
                $hidden = (isset($row['hidden']) ? $row['hidden'] : 0);
                $data = array(
                    'order_id'          => $order->getId(),
                    'code'              => $tax['code'],
                    'title'             => $tax['title'],
                    'hidden'            => $hidden,
                    'percent'           => $tax['percent'],
                    'priority'          => $tax['priority'],
                    'position'          => $tax['position'],
                    'amount'            => $row['amount'],
                    'base_amount'       => $row['base_amount'],
                    'process'           => $row['process'],
                    'base_real_amount'  => $baseRealAmount,
                );

                $result = \Mage::getModel('Magento\Tax\Model\Sales\Order\Tax')->setData($data)->save();

                if (isset($ratesIdQuoteItemId[$id])) {
                    foreach ($ratesIdQuoteItemId[$id] as $quoteItemId) {
                        if ($quoteItemId['code'] == $tax['code']) {
                            $item = $order->getItemByQuoteItemId($quoteItemId['id']);
                            if ($item) {
                                $data = array(
                                    'item_id'       => $item->getId(),
                                    'tax_id'        => $result->getTaxId(),
                                    'tax_percent'   => $quoteItemId['percent']
                                );
                                \Mage::getModel('Magento\Tax\Model\Sales\Order\Tax\Item')->setData($data)->save();
                            }
                        }
                    }
                }
            }
        }

        $order->setAppliedTaxIsSaved(true);
    }

    /**
     * Add tax percent values to product collection items
     *
     * @param   \Magento\Event\Observer $observer
     * @return  \Magento\Tax\Model\Observer
     */
    public function addTaxPercentToProductCollection($observer)
    {
        $helper = \Mage::helper('Magento\Tax\Helper\Data');
        $collection = $observer->getEvent()->getCollection();
        $store = $collection->getStoreId();
        if (!$helper->needPriceConversion($store)) {
            return $this;
        }

        if ($collection->requireTaxPercent()) {
            $request = \Mage::getSingleton('Magento\Tax\Model\Calculation')->getRateRequest();
            foreach ($collection as $item) {
                if (null === $item->getTaxClassId()) {
                    $item->setTaxClassId($item->getMinimalTaxClassId());
                }
                if (!isset($classToRate[$item->getTaxClassId()])) {
                    $request->setProductClassId($item->getTaxClassId());
                    $classToRate[$item->getTaxClassId()] = \Mage::getSingleton('Magento\Tax\Model\Calculation')->getRate($request);
                }
                $item->setTaxPercent($classToRate[$item->getTaxClassId()]);
            }

        }
        return $this;
    }

    /**
     * Refresh sales tax report statistics for last day
     *
     * @param \Magento\Cron\Model\Schedule $schedule
     * @return \Magento\Tax\Model\Observer
     */
    public function aggregateSalesReportTaxData($schedule)
    {
        \Mage::app()->getLocale()->emulate(0);
        $currentDate = \Mage::app()->getLocale()->date();
        $date = $currentDate->subHour(25);
        \Mage::getResourceModel('Magento\Tax\Model\Resource\Report\Tax')->aggregate($date);
        \Mage::app()->getLocale()->revert();
        return $this;
    }

    /**
     * Reset extra tax amounts on quote addresses before recollecting totals
     *
     * @param \Magento\Event\Observer $observer
     * @return \Magento\Tax\Model\Observer
     */
    public function quoteCollectTotalsBefore(\Magento\Event\Observer $observer)
    {
        /* @var $quote \Magento\Sales\Model\Quote */
        $quote = $observer->getEvent()->getQuote();
        foreach ($quote->getAllAddresses() as $address) {
            $address->setExtraTaxAmount(0);
            $address->setBaseExtraTaxAmount(0);
        }
        return $this;
    }
}
