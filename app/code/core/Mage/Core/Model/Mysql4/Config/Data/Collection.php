<?php
/**
 * Config data collection
 *
 * @package    Mage
 * @subpackage Core
 * @author     Moshe Gurvich <moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Model_Mysql4_Config_Data_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract 
{
    protected function _construct() 
    {
        $this->_init('core/config_data');
    }
}