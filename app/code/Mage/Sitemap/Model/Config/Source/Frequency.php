<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Sitemap_Model_Config_Source_Frequency implements Mage_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'always', 'label'=>__('Always')),
            array('value'=>'hourly', 'label'=>__('Hourly')),
            array('value'=>'daily', 'label'=>__('Daily')),
            array('value'=>'weekly', 'label'=>__('Weekly')),
            array('value'=>'monthly', 'label'=>__('Monthly')),
            array('value'=>'yearly', 'label'=>__('Yearly')),
            array('value'=>'never', 'label'=>__('Never')),
        );
    }
}
