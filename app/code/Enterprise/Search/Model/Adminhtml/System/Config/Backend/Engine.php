<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog search backend model
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Model_Adminhtml_System_Config_Backend_Engine extends Mage_Core_Model_Config_Value
{
    /**
     * After save call
     * Invalidate catalog search index if engine was changed
     *
     * @return Enterprise_Search_Model_Adminhtml_System_Config_Backend_Engine
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        if ($this->isValueChanged()) {
            Mage::getSingleton('Mage_Index_Model_Indexer')->getProcessByCode('catalogsearch_fulltext')
                ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }

        return $this;
    }
}
