<?php
    /**
     * Reward rate collection for customer edit tab history grid
     *
     * {license_notice}
     *
     * @copyright   {copyright}
     * @license     {license_link}
     */
class Enterprise_Reward_Model_Resource_Reward_History_Grid_Collection
    extends Enterprise_Reward_Model_Resource_Reward_History_Collection
{
    /**
     * @var Enterprise_Reward_Helper_Data
     */
    protected $_helper;

    /**
     * @param Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Enterprise_Reward_Helper_Data $helper
     * @param Mage_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Magento_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Enterprise_Reward_Helper_Data $helper,
        Mage_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        $this->_helper = $helper;
        parent::__construct($fetchStrategy, $resource);
    }

    /**
     * @return Enterprise_Reward_Model_Resource_Reward_History_Grid_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        /** @var $collection Enterprise_Reward_Model_Resource_Reward_History_Collection */
        $this->setExpiryConfig($this->_helper->getExpiryConfig())
            ->addExpirationDate()
            ->setOrder('history_id', 'desc');
        $this->setDefaultOrder();
        return $this;
    }

    /**
     * Add column filter to collection
     *
     * @param array|string $field
     * @param null $condition
     * @return Enterprise_Reward_Model_Resource_Reward_History_Grid_Collection
     */
    public  function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'website_id' || $field == 'points_balance') {
            if ($field && isset($condition)) {
                $this->addFieldToFilter('main_table.' . $field, $condition);
            }
        } else {
            parent::addFieldToFilter($field, $condition);
        }

        return $this;
    }
}