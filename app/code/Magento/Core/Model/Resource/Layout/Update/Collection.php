<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout update collection model
 */
class Magento_Core_Model_Resource_Layout_Update_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
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
        $this->_init('Magento_Core_Model_Layout_Update', 'Magento_Core_Model_Resource_Layout_Update');
    }

    /**
     * Add filter by theme id
     *
     * @param int $themeId
     * @return Magento_Core_Model_Resource_Layout_Update_Collection
     */
    public function addThemeFilter($themeId)
    {
        $this->_joinWithLink();
        $this->getSelect()
            ->where('link.theme_id = ?', $themeId);

        return $this;
    }

    /**
     * Add filter by store id
     *
     * @param int $storeId
     * @return Magento_Core_Model_Resource_Layout_Update_Collection
     */
    public function addStoreFilter($storeId)
    {
        $this->_joinWithLink();
        $this->getSelect()
            ->where('link.store_id = ?', $storeId);

        return $this;
    }

    /**
     * Join with layout link table
     *
     * @return Magento_Core_Model_Resource_Layout_Update_Collection
     */
    protected function _joinWithLink()
    {
        $flagName = 'joined_with_link_table';
        if (!$this->getFlag($flagName)) {
            $this->getSelect()
                ->join(
                    array('link' => $this->getTable('core_layout_link')),
                    'link.layout_update_id = main_table.layout_update_id',
                    array('store_id', 'theme_id')
                );

            $this->setFlag($flagName, true);
        }

        return $this;
    }

    /**
     * Left Join with layout link table
     *
     * @param array $fields
     * @return Magento_Core_Model_Resource_Layout_Update_Collection
     */
    protected function _joinLeftWithLink($fields = array())
    {
        $flagName = 'joined_left_with_link_table';
        if (!$this->getFlag($flagName)) {
            $this->getSelect()
                ->joinLeft(
                    array('link' => $this->getTable('core_layout_link')),
                    'link.layout_update_id = main_table.layout_update_id',
                    array($fields)
                );
            $this->setFlag($flagName, true);
        }

        return $this;
    }

    /**
     * Get layouts that are older then specified number of days
     *
     * @param $days
     * @return Magento_Core_Model_Resource_Layout_Update_Collection
     */
    public function addUpdatedDaysBeforeFilter($days)
    {
        $datetime = new DateTime();
        $storeInterval = new DateInterval('P' . $days . 'D');
        $datetime->sub($storeInterval);
        $formattedDate = $this->formatDate($datetime->getTimestamp());

        $this->addFieldToFilter('main_table.updated_at', array('notnull' => true))
            ->addFieldToFilter('main_table.updated_at', array('lt' => $formattedDate));

        return $this;
    }

    /**
     * Get layouts without links
     *
     * @return Magento_Core_Model_Resource_Layout_Update_Collection
     */
    public function addNoLinksFilter()
    {
        $this->_joinLeftWithLink();
        $this->addFieldToFilter('link.layout_update_id', array('null' => true));

        return $this;
    }

    /**
     * Delete updates in collection
     *
     * @return $this
     */
    public function delete()
    {
        /** @var $update Magento_Core_Model_Layout_Update */
        foreach ($this->getItems() as $update) {
            $update->delete();
        }
        return $this;
    }
}