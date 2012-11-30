<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Model_Widget_Grid_SubTotals extends Mage_Backend_Model_Widget_Grid_TotalsAbstract
{
    /**
     * Count collection column sum based on column index
     *
     * @param string $index
     * @param Varien_Data_Collection $collection
     * @return float|int
     */
    protected function _countSum($index, $collection)
    {
        $sum = 0;
        foreach ($collection as $item) {
            $sum += $item[$index];
        }
        return $sum;
    }

    /**
     * Count collection column average based on column index
     *
     * @param string $index
     * @param Varien_Data_Collection $collection
     * @return float|int
     */
    protected function _countAverage($index, $collection)
    {
        $itemsCount = count($collection);
        return $itemsCount ? $this->_countSum($index, $collection) / $itemsCount : $itemsCount;
    }
}
