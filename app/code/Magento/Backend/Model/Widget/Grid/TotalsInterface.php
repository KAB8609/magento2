<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Magento_Backend_Model_Widget_Grid_TotalsInterface
{
    /**
     * Return object contains totals for all items in collection
     *
     * @abstract
     * @param Magento_Data_Collection $collection
     * @return Magento_Object
     */
    public function countTotals($collection);
}