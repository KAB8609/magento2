<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Config category field backend
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Config_Backend_Datashare extends Mage_Core_Model_Config_Data
{
    protected function _afterSave()
    {
#echo "<pre>".print_r($configData,1)."</pre>"; die;
    }
}
