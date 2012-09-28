<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout update collection model
 */
class Mage_Core_Model_Resource_Layout_Update_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Name prefix of events that are dispatched by model
     *
     * @var string
     */
    protected $_eventPrefix = 'layout_update_collection';

    /**
     * Name of event parameter
     *
     * @var string
     */
    protected $_eventObject = 'layout_update_collection';

    /**
     * Define resource model
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Mage_Core_Model_Layout_Update', 'Mage_Core_Model_Resource_Layout_Update');
    }
}
